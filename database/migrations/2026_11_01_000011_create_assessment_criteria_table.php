<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('assessment_criteria', function (Blueprint $table) {
            $table->id();
            $table->foreignId('festival_id')->constrained('festivals')->restrictOnDelete();
            $table->string('assessment_type', 20); // innovation | institution
            $table->string('name');
            $table->unsignedTinyInteger('weight');
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->timestamps();

            $table->unique(
                ['festival_id', 'assessment_type', 'name'],
                'assessment_criteria_festival_type_name_unique'
            );
            $table->unique(['festival_id', 'id'], 'assessment_criteria_festival_id_unique');
            $table->unique(
                ['festival_id', 'assessment_type', 'id'],
                'assessment_criteria_festival_type_id_unique'
            );
            $table->index(['festival_id', 'assessment_type'], 'assessment_criteria_type_index');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('assessment_criteria');
    }
};
