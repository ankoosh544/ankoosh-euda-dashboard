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
        Schema::create('debugs', function (Blueprint $table) {
            $table->id();
            $table->string('plantId')->constrained('plants', 'plant_id');
            $table->integer('fwMajor')->default(0);
            $table->integer('fwMinor')->default(0);
            $table->integer('fwPatch')->default(0);
            $table->integer('wifiReset')->default(0);
            $table->integer('rssi')->nullable();
            $table->integer('otaRetry')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('debugs');
    }
};
