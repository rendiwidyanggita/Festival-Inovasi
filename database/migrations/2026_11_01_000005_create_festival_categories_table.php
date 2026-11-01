<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('festival_categories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('festival_id')
                ->constrained('festivals')->restrictOnDelete();
            $table->string('name');
            $table->string('assessment_type', 20); // innovation | institution
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->timestamps();

            $table->unique(['festival_id', 'name'], 'festival_categories_festival_name_unique');
            // Required as a referenced key for same-festival composite foreign keys.
            $table->unique(['festival_id', 'id'], 'festival_categories_festival_id_unique');
            $table->index(['festival_id', 'assessment_type'], 'festival_categories_type_index');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('festival_categories');
    }
};
