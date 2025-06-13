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
        Schema::create('visitors', function (Blueprint $table) {
            $table->id();
            $table->foreignId('location_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('sensor_id')->nullable()->constrained()->onDelete('set null');
            $table->date('date');
            $table->tinyInteger('hour')->nullable();
            $table->unsignedInteger('in_count')->default(0);
            $table->unsignedInteger('out_count')->default(0);
            $table->unsignedInteger('passby_count')->default(0);
            $table->string('source')->default('sensor');
            $table->timestamps();

            $table->unique(['location_id', 'sensor_id', 'date', 'hour']);
            $table->index(['date', 'hour']);
            $table->index('location_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('visitors');
    }
};
