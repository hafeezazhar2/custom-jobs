<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class ExampleJob implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        //
    }

    public function execute($param1 = 'default1', $param2 = 'default2')
    {
        // Perform some task
        \Log::info("Executing ExampleJob with parameters: {$param1}, {$param2}");
        
        // Simulate job processing
        sleep(7);  // Just for demonstration
        
        \Log::info("ExampleJob completed with parameters: {$param1}, {$param2}");
    }
}
