<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('institution_assessment_details', function (Blueprint $table) {
            $table->id();
            // Repeated context columns allow the database to enforce same-festival/type relations.
            $table->unsignedBigInteger('festival_id');
            $table->string('assessment_type', 20)->default('institution');
            $table->unsignedBigInteger('institution_assessment_id');
            $table->unsignedBigInteger('assessment_criterion_id');
            $table->unsignedTinyInteger('score'); // Application validation: integer 6..10
            $table->timestamps();

            $table->unique(
                ['institution_assessment_id', 'assessment_criterion_id'],
                'institution_details_assessment_criterion_unique'
            );

            $table->foreign(['festival_id', 'institution_assessment_id'], 'institution_details_assessment_fk')
                ->references(['festival_id', 'id'])
                ->on('institution_assessments')
                ->cascadeOnDelete();

            $table->foreign(
                ['festival_id', 'assessment_type', 'assessment_criterion_id'],
                'institution_details_criterion_fk'
            )->references(['festival_id', 'assessment_type', 'id'])
                ->on('assessment_criteria')
                ->restrictOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('institution_assessment_details');
    }
};
