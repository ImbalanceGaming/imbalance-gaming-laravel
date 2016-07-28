<?php

namespace imbalance\Jobs;

use Illuminate\Bus\Queueable;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;

abstract class Job
{
    /*
    |--------------------------------------------------------------------------
    | Queueable Jobs
    |--------------------------------------------------------------------------
    |
    | This job base class provides a central location to place any logic that
    | is shared across all of your jobs. The trait included with the class
    | provides access to the "queueOn" and "delay" queue helper methods.
    |
    */

    use Queueable;

    /**
     * Run envoy command via SSH
     *
     * @param string $task
     * @return array
     */
    protected function runEnvoy($task) {

        $output = [
            'completed' => false,
            'message' => []
        ];

        $directory = base_path();
        $process = new Process("/var/www/.config/composer/vendor/bin/envoy run $task");
        $process->setTimeout(3600);
        $process->setIdleTimeout(300);
        $process->setWorkingDirectory($directory);

        try {
            $process->mustRun(function ($type, $buffer) use (&$output) {
                if (Process::ERR === $type) {
                    $output['completed'] = false;
                } else {
                    $output['completed'] = true;
                }
                array_push($output['message'], $buffer);
            });
            return $output;
        } catch (ProcessFailedException $e) {
            $output = [
                'completed' => false,
                'message' => [$e->getMessage()]
            ];
            return $output;
        }
    }

}
