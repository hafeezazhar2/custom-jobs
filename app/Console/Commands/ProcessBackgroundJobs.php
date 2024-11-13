<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\BackgroundJob;
use Illuminate\Support\Facades\Log;
use Throwable;
use Carbon\Carbon;
use App\Enums\JobStatus;

class ProcessBackgroundJobs extends Command
{
    protected $signature = 'background-jobs:process';
    protected $description = 'Processes background jobs from the database queue';

    public function handle()
    {
        while (true) {
            // Fetch the next pending job that is either not scheduled or scheduled to run now
            $job = BackgroundJob::where('status', JobStatus::PENDING->value)
                ->where(function ($query) {
                    $query->whereNull('scheduled_at')->orWhere('scheduled_at', '<=', now());
                })
                ->orderBy('priority', 'asc')
                ->orderBy('created_at', 'asc')
                ->first();

            // If no job is found, wait for 10 seconds before checking again
            if (!$job) {
                sleep(10);
                continue;
            }          
           
            // Mark the job as running
            $job->update(['status' => JobStatus::RUNNING->value]);
           
            try {
                // Dynamically instantiate the job class and call the specified method with parameters
                $class = new $job->class();
                $method = $job->method;
                $parameters = json_decode($job->parameters, true);

                // Long-running jobs should periodically check for cancellation
                //$this->checkForCancellation($job);

                call_user_func_array([$class, $method], $parameters);

                // Mark the job as completed and log success
                $job->update(['status' => JobStatus::COMPLETED->value]);
                Log::info("Job {$job->id} completed successfully.");

            } catch (Throwable $e) {
                // Increment retry attempts and handle job failure
                $job->increment('retry_attempts');

                if ($job->retry_attempts < $job->max_retries) {
                    // Retry the job if it hasn't reached the max retries
                    $job->update(['status' => JobStatus::PENDING->value]);
                } else {
                    // Mark the job as failed and log the error
                    $job->update(['status' => JobStatus::FAILED->value]);
                    Log::channel('background_jobs_errors')->error("Job {$job->id} failed: {$e->getMessage()}");
                }
            }
        }
    }


    /**
     * Periodically check if the job is cancelled during long-running tasks.
     */
    private function checkForCancellation(BackgroundJob $job)
    {
        // Check for cancellation every 10 seconds during a long-running job
        while ($job->status === 'running') {
            // Refresh the job status from the database
            $job->refresh();

            if ($job->status === 'canceled') {
                // If the job is cancelled, log and exit
                Log::channel('background_jobs_errors')->info("Job {$job->id} was cancelled during execution.");
                $job->update(['status' => 'canceled']);

                exec("pkill -f 'php artisan background-jobs:process'"); //Kill if the process is already running
                exec('cd '.env('APP_PATH').' && nohup php artisan background-jobs:process > /dev/null 2>&1 &'); //Start the process
                return;
            }

            // Sleep for a short period before checking again
            sleep(10);
            return;
        }
    }
}
