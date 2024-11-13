<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\BackgroundJob; 
use App\Enums\JobStatus;

class JobController extends Controller
{
    /**
     * Display a listing of the jobs.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {       
        // Fetch all jobs from the database
        $jobs = BackgroundJob::all()->sortByDesc('id');

        // Return the view with job data
        return view('jobs.index', compact('jobs'));
    }   

    /**
     * Cancel a job.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function cancel($id)
    {
        $job = BackgroundJob::find($id);

        if (!$job || $job->status !== JobStatus::RUNNING->value) {
            return redirect()->back()->with('error', 'Job not found or not running');
        }

        // Mark the job as cancelled
        $job->update(['status' => JobStatus::CANCELED->value]);

        return redirect()->back()->with('success', 'Job cancellation initiated');
    }

    /**
     * Retry a job.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function retry($id)
    {
        // Find the job by ID
        $job = BackgroundJob::find($id);

        if ($job) {
            $job->status = JobStatus::PENDING->value; // Mark the job as queued for retry
            $job->retry_attempts += 1;  // Increment the retry count
            $job->scheduled_at = now()->addMinutes(2); // Schedule the job to run in 2 minutes
            $job->save();

            // Trigger job execution logic here if required
            return redirect()->back()->with('success', 'Job retried successfully');            
        }

        return redirect()->back()->with('error', 'Job not found');         
    }

     /**
     * Add sample jobs for testing.
     *
     * @return String
     */
    public function addSampleJobs()
    {
        $jobs = [
            [
                'class' => \App\Jobs\ExampleJob::class,
                'method' => 'execute',
                'params' => ['param1' => 'value1', 'param2' => 'value2'],
                'priority' => 2,
                'maxRetries' => 5,
                'scheduledAt' => now()->addMinutes(2)
            ],
            [
                'class' => \App\Jobs\ExampleJob::class,
                'method' => 'execute',
                'params' => ['param1' => 'value1', 'param2' => 'value2'],
                'priority' => 1,
                'maxRetries' => 2,
                'status' => JobStatus::FAILED->value
            ],
            [
                'class' => \App\Jobs\ExampleJob::class,
                'method' => 'execute',
                'params' => ['param1' => 'value1', 'param2' => 'value2'],
                'priority' => 1,
                'maxRetries' => 2,
                'status' => JobStatus::FAILED->value
            ],
            [
                'class' => \App\Jobs\ExampleJob::class,
                'method' => 'execute',
                'params' => ['param1' => 'value1', 'param2' => 'value2'],
                'priority' => 5,
                'maxRetries' => 2
            ],
            [
                'class' => \App\Jobs\ExampleJob::class,
                'method' => 'execute',
                'params' => ['param1' => 'value1', 'param2' => 'value2'],
                'priority' => 1,
                'maxRetries' => 2
            ]          
            
        ];

        foreach ($jobs as $job) {
            runBackgroundJob(
                $job['class'],
                $job['method'],
                $job['params'],
                $job['priority'] ?? 1,
                $job['maxRetries'] ?? 1,
                $job['scheduledAt'] ?? null,
                $job['status'] ?? JobStatus::PENDING->value
            );
        }

        // Start the background job process
        $appPath = env('APP_PATH');
        if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
            exec("taskkill /F /IM php.exe"); // Forcefully kill the php.exe process
            exec("cd /d $appPath && start /B php artisan background-jobs:process");
        } else {
            exec("pkill -f 'php artisan background-jobs:process'"); // Kill if the process is already running
            exec("cd $appPath && nohup php artisan background-jobs:process > /dev/null 2>&1 &"); // Start the process
        }

        return 'Sample jobs added successfully, also started worker';
    }
}
