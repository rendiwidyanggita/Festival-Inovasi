<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('jury_subcategory_participations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('festival_id');
            $table->unsignedBigInteger('festival_jury_id');
            $table->unsignedBigInteger('award_category_id');
            $table->string('status', 20)->default('selected'); // selected | in_progress | completed
            $table->timestamp('selected_at')->useCurrent();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();

            $table->unique(
                ['festival_jury_id', 'award_category_id'],
                'jury_participations_jury_category_unique'
            );
            $table->unique(
                ['festival_id', 'award_category_id', 'id'],
                'jury_participations_festival_category_id_unique'
            );

            $table->foreign(['festival_id', 'festival_jury_id'], 'jury_participations_festival_jury_fk')
                ->references(['festival_id', 'id'])
                ->on('festival_juries')
                ->restrictOnDelete();

            $table->foreign(['festival_id', 'award_category_id'], 'jury_participations_award_category_fk')
                ->references(['festival_id', 'id'])
                ->on('award_categories')
                ->restrictOnDelete();

            $table->index(['award_category_id', 'status'], 'jury_participations_category_status_index');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('jury_subcategory_participations');
    }
};
