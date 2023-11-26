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
        Schema::create('states', function (Blueprint $table) {
            $table->id();
            $table->string('plantId')->constrained('plants', 'plant_id');
            $table->boolean('OOS')->required();
            $table->integer('OSN')->required();
            $table->json('FCN')->required();
            $table->double('CAM')->required();
            $table->integer('STR')->required();
            $table->json('DFN')->required();
            $table->json('IUPS')->required();
            $table->integer('DON')->required();
            $table->integer('rides')->required();
            $table->boolean('BAT')->required();
            $table->boolean('AC')->required();
            $table->integer('sequence')->required();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('states');
    }
};
