<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Create the 'background_jobs' table
        Schema::create('background_jobs', function (Blueprint $table) {
            $table->id(); // Primary key
            $table->string('class'); // Class name of the job
            $table->string('method'); // Method to be called on the class
            $table->text('parameters')->nullable(); // Parameters for the method, nullable
            $table->enum('status', ['pending', 'running', 'completed', 'failed', 'canceled', 'queued'])->default('pending'); // Status of the job
            $table->integer('priority')->default(1); // Priority of the job, default is 1
            $table->integer('retry_attempts')->default(0); // Number of retry attempts, default is 0
            $table->integer('max_retries')->default(3); // Maximum number of retries, default is 3
            $table->timestamp('scheduled_at')->nullable(); // Scheduled time for the job, nullable
            $table->timestamps(); // Timestamps for created_at and updated_at
        });
    }
    
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('background_jobs');
    }
};
