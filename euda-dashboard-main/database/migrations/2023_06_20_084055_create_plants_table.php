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
        Schema::create('plants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('assigned_to')->constrained('users')->required();
            $table->foreignId('owner_id')->constrained('users')->required();
            $table->string('plant_id')->required()->index();
            $table->string('name')->required();
            $table->string('state')->required();
            $table->string('city')->required();
            $table->string('cap')->required();
            $table->string('address')->required();
            $table->string('country_code')->required();
            $table->date('schedule_date')->nullable();
            $table->boolean('status')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('plants');
    }
};
