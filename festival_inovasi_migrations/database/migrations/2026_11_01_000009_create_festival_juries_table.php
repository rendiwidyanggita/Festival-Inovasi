<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('festival_juries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('festival_id')->constrained('festivals')->restrictOnDelete();
            $table->foreignId('user_id')->constrained('users')->restrictOnDelete();
            $table->string('status', 20)->default('active'); // active | inactive
            $table->timestamps();

            $table->unique(['festival_id', 'user_id'], 'festival_juries_festival_user_unique');
            $table->unique(['festival_id', 'id'], 'festival_juries_festival_id_unique');
            $table->index(['festival_id', 'status'], 'festival_juries_status_index');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('festival_juries');
    }
};
