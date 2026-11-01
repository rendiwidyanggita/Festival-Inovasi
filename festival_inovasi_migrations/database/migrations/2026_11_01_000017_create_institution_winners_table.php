<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('institution_winners', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('festival_id');
            $table->unsignedBigInteger('award_category_id');
            $table->unsignedBigInteger('institution_id');
            $table->unsignedSmallInteger('position');
            $table->foreignId('appointed_by')->constrained('users')->restrictOnDelete();
            $table->timestamp('appointed_at');
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->unique(
                ['festival_id', 'award_category_id', 'position'],
                'institution_winners_category_position_unique'
            );
            $table->unique(
                ['festival_id', 'award_category_id', 'institution_id'],
                'institution_winners_category_institution_unique'
            );

            $table->foreign(
                ['festival_id', 'award_category_id', 'institution_id'],
                'institution_winners_institution_fk'
            )->references(['festival_id', 'award_category_id', 'id'])
                ->on('institutions')
                ->restrictOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('institution_winners');
    }
};
