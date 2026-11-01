<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('festivals', function (Blueprint $table) {
            $table->id();
            $table->unsignedSmallInteger('year')->unique();
            $table->string('name');
            $table->string('status', 20)->default('preparation'); // preparation | ongoing | completed
            $table->timestamp('results_published_at')->nullable();
            $table->foreignId('results_published_by')->nullable()
                ->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index('status', 'festivals_status_index');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('festivals');
    }
};
