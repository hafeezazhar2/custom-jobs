<?php
use App\Models\BackgroundJob;

if (!function_exists('runBackgroundJob')) {
    if (!function_exists('runBackgroundJob')) {
        function runBackgroundJob($class, $method, $parameters = [], $priority = 1, $maxRetries = 3, $scheduledAt = null, $status='pending') {
            $approvedClasses = [
                \App\Jobs\ExampleJob::class, // Add approved classes here
            ];
    
            if (!in_array($class, $approvedClasses) || !method_exists($class, $method)) {
                Log::channel('background_jobs_errors')->error("Unauthorized job class or method: $class::$method");
                return false;
            }
    
            return BackgroundJob::create([
                'class' => $class,
                'method' => $method,
                'parameters' => json_encode($parameters),
                'priority' => $priority,
                'max_retries' => $maxRetries,
                'scheduled_at' => $scheduledAt,
                'status' => $status,              
            ]);
        }
    }    
}