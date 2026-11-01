<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('institution_assessments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('festival_id');
            $table->unsignedBigInteger('award_category_id');
            $table->unsignedBigInteger('jury_subcategory_participation_id');
            $table->unsignedBigInteger('institution_id');
            $table->string('status', 30)->default('draft'); // draft | submitted | locked | revision_requested
            $table->timestamp('submitted_at')->nullable();
            $table->timestamp('locked_at')->nullable();
            $table->timestamps();

            $table->unique(
                ['jury_subcategory_participation_id', 'institution_id'],
                'institution_assessments_participation_institution_unique'
            );
            $table->unique(['festival_id', 'id'], 'institution_assessments_festival_id_unique');

            $table->foreign(
                ['festival_id', 'award_category_id', 'jury_subcategory_participation_id'],
                'institution_assessments_participation_fk'
            )->references(['festival_id', 'award_category_id', 'id'])
                ->on('jury_subcategory_participations')
                ->restrictOnDelete();

            $table->foreign(
                ['festival_id', 'award_category_id', 'institution_id'],
                'institution_assessments_institution_fk'
            )->references(['festival_id', 'award_category_id', 'id'])
                ->on('institutions')
                ->restrictOnDelete();

            $table->index(['award_category_id', 'status'], 'institution_assessments_category_status_index');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('institution_assessments');
    }
};
