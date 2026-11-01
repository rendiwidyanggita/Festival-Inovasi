<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('innovation_winners', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('festival_id');
            $table->unsignedBigInteger('award_category_id');
            $table->unsignedBigInteger('innovation_id');
            $table->unsignedSmallInteger('position');
            $table->foreignId('appointed_by')->constrained('users')->restrictOnDelete();
            $table->timestamp('appointed_at');
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->unique(
                ['festival_id', 'award_category_id', 'position'],
                'innovation_winners_category_position_unique'
            );
            $table->unique(
                ['festival_id', 'award_category_id', 'innovation_id'],
                'innovation_winners_category_innovation_unique'
            );

            $table->foreign(
                ['festival_id', 'award_category_id', 'innovation_id'],
                'innovation_winners_innovation_fk'
            )->references(['festival_id', 'award_category_id', 'id'])
                ->on('innovations')
                ->restrictOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('innovation_winners');
    }
};
