<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('festival_settings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('festival_id')->unique()
                ->constrained('festivals')->restrictOnDelete();
            $table->string('organizer_name');
            $table->string('organizer_logo_path')->nullable();
            $table->string('report_title')->nullable();
            $table->string('report_footer')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('festival_settings');
    }
};
