<?php

namespace imbalance\Jobs;

use imbalance\Jobs\Job;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use imbalance\Models\Project;
use imbalance\Models\ProjectDeploymentHistory;
use imbalance\Models\ProjectPackage;
use imbalance\Models\ProjectPackageCommand;
use imbalance\Models\Server;
use imbalance\Models\User;

class DeployProject extends Job implements ShouldQueue {

    use InteractsWithQueue, SerializesModels;

    private $_project, $_server, $_user, $_history, $_firstRun;

    /**
     * Create a new job instance.
     * @param Project $project
     * @param Server $server
     * @param User $user
     * @param ProjectDeploymentHistory $history
     * @param bool $firstRun
     */
    public function __construct($project, $server, $user, $history, $firstRun) {
        $this->_project = $project;
        $this->_server = $server;
        $this->_user = $user;
        $this->_history = $history;
        $this->_firstRun = $firstRun;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle() {

        $output = null;

        /** @var ProjectPackage $package */
        foreach ($this->_project->packages as $package) {

            $outputTemp = null;

            if ($this->_firstRun) {
                $outputTemp = $this->runEnvoy(
                    'install --repo=' . $package->repository .
                    ' --deployLocation=' . $package->deploy_location .
                    ' --server=envoy@' . $this->_server->address
                );
            } else {
                $outputTemp = $this->runEnvoy(
                    'gitPull --deployLocation=' . $package->deploy_location.
                    ' --server=envoy@' . $this->_server->address
                );
            }

            if (sizeof($output) < 1) {
                $output = $outputTemp;
            } else {
                array_merge($output['message'], $outputTemp['message']);
            }

            /** @var ProjectPackageCommand $command */
            foreach ($package->projectPackageCommands as $command) {

                $runCommand = false;

                if ($this->_firstRun && $command->run_on == 'install') {
                    $runCommand = true;
                } elseif (!$this->_firstRun && $command->run_on == 'update') {
                    $runCommand = true;
                }

                if ($runCommand) {
                    $outputTemp = $this->runEnvoy(
                        "runCommand --command='" . $command->command . "'".
                        " --deployLocation=" . $package->deploy_location .
                        " --server=envoy@" . $this->_server->address
                    );
                }

                if (isset($output['message'])) {
                    if (isset($outputTemp['message'])) {
                        array_merge($output['message'], $outputTemp['message']);
                    } else {
                        array_push($output['message'], $outputTemp);
                    }
                } else {
                    $output = $outputTemp;
                }
            }

            if (sizeof($output['message']) < 1) {
                $output['completed'] = true;
            }
        }

        if ($output['completed']) {
            $outputMessage = "Project [" . $this->_project->key . "]" . $this->_project->name . " deployed <br>";
        } else {
            $outputMessage = "Project [" . $this->_project->key . "]" . $this->_project->name . " failed to deploy <br>";
        }

        if (isset($output['message'])) {
            foreach ($output['message'] as $outputMessageTest) {
                $outputMessage .= $outputMessageTest."<br>";
            }
        }

        $this->_history->job_output = $outputMessage;

        if ($output['completed']) {
            $this->_history->status = 'Finished';
            $this->_history->save();
            if ($this->_firstRun) {
                $this->_project->servers()->updateExistingPivot($this->_server->id, ['first_run'=>false]);
            }
        } else {
            $this->_history->status = 'Failed';
            $this->_history->save();
        }

    }
}
