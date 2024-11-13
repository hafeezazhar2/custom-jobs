<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\JobController;

 // Route to list all jobs
 Route::get('/', [JobController::class, 'index'])->name('jobs.index');

Route::group(['prefix' => 'jobs/'], function () {    
    // Route to add sample jobs
    Route::get('/add-sample-jobs', [JobController::class, 'addSampleJobs'])->name('jobs.addSampleJobs');   

    // Route to cancel a job by ID
    Route::post('/cancel/{id}', [JobController::class, 'cancel'])->name('jobs.cancel');

    // Route to retry a job by ID
    Route::post('/retry/{id}', [JobController::class, 'retry'])->name('jobs.retry');
});