<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('innovation_assessments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('festival_id');
            $table->unsignedBigInteger('award_category_id');
            $table->unsignedBigInteger('jury_subcategory_participation_id');
            $table->unsignedBigInteger('innovation_id');
            $table->string('status', 30)->default('draft'); // draft | submitted | locked | revision_requested
            $table->timestamp('submitted_at')->nullable();
            $table->timestamp('locked_at')->nullable();
            $table->timestamps();

            $table->unique(
                ['jury_subcategory_participation_id', 'innovation_id'],
                'innovation_assessments_participation_innovation_unique'
            );
            $table->unique(['festival_id', 'id'], 'innovation_assessments_festival_id_unique');

            $table->foreign(
                ['festival_id', 'award_category_id', 'jury_subcategory_participation_id'],
                'innovation_assessments_participation_fk'
            )->references(['festival_id', 'award_category_id', 'id'])
                ->on('jury_subcategory_participations')
                ->restrictOnDelete();

            $table->foreign(
                ['festival_id', 'award_category_id', 'innovation_id'],
                'innovation_assessments_innovation_fk'
            )->references(['festival_id', 'award_category_id', 'id'])
                ->on('innovations')
                ->restrictOnDelete();

            $table->index(['award_category_id', 'status'], 'innovation_assessments_category_status_index');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('innovation_assessments');
    }
};
