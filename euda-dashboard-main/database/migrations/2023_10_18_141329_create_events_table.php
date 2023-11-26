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
        Schema::create('events', function (Blueprint $table) {
            $table->id();
            $table->string('plantId')->constrained('plants', 'plant_id');
            $table->boolean('OOS')->nullable();
            $table->json('DFD')->nullable();
            $table->boolean('STR')->nullable();
            $table->json('IUPS')->nullable();
            $table->boolean('CLS')->nullable();
            $table->boolean('BAT')->nullable();
            $table->boolean('AC')->nullable();
            $table->integer('sequence');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('events');
    }
};
