<?php

namespace imbalance\Jobs;

use imbalance\Http\Controllers\Controller;
use imbalance\Jobs\Job;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use imbalance\Models\Project;
use imbalance\Models\ProjectPackage;
use imbalance\Models\ProjectPackageCommand;
use imbalance\Models\User;

class DeployDevProject extends Job implements ShouldQueue {

    use InteractsWithQueue, SerializesModels;

    private $_project, $_user, $_deleteProject;

    /**
     * Create a new job instance.
     * @param Project $project
     * @param User $user
     * @param bool $deleteProject
     */
    public function __construct($project, $user, $deleteProject) {
        $this->_project = $project;
        $this->_user = $user;
        $this->_deleteProject = $deleteProject;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle() {

        $output = null;

        if ($this->_deleteProject) {
            $output = $this->runEnvoy(
                "removeForDev ".
                " --deployLocation=" . $this->_project->packages[0]->deploy_location .
                " --server=envoy@".Controller::DEV_SERVER .
                " --user=".strtolower(substr($this->_user->forename, 0, 1)).strtolower($this->_user->surname)
            );
        } else {
            /** @var ProjectPackage $package */
            foreach ($this->_project->packages as $package) {

                $outputTemp = null;

                $outputTemp = $this->runEnvoy(
                    'installForDev --repo=' . $package->repository .
                    ' --deployLocation=' . $package->deploy_location .
                    ' --server=envoy@'.Controller::DEV_SERVER .
                    " --user=".strtolower(substr($this->_user->forename, 0, 1)).strtolower($this->_user->surname)
                );

                if (sizeof($output) < 1) {
                    $output = $outputTemp;
                } else {
                    array_merge($output['message'], $outputTemp['message']);
                }

                /** @var ProjectPackageCommand $command */
                foreach ($package->projectPackageCommands as $command) {

                    $runCommand = false;

                    if ($command->run_on == 'install') {
                        $runCommand = true;
                    }

                    if ($runCommand) {
                        $outputTemp = $this->runEnvoy(
                            "runCommandForDev --command='" . $command->command . "'".
                            " --deployLocation=" . $package->deploy_location .
                            " --server=envoy@".Controller::DEV_SERVER .
                            " --user=".strtolower(substr($this->_user->forename, 0, 1)).strtolower($this->_user->surname)
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
        }

    }
}
