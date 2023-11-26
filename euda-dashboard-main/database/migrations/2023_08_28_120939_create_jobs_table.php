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
        Schema::create('jobs', function (Blueprint $table) {
            $table->id();
            $table->string('job_id')->nullable();
            $table->string('thing_type')->nullable();
            $table->string('status')->default('Queued');
            $table->string('plantId')->constrained('plants', 'plant_id')->nullable();
            $table->foreignId('owner_id')->nullable()->constrained('users')->nullable();
            $table->string('version_number')->nullable();
            $table->timestamps();
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('jobs');
    }
};
