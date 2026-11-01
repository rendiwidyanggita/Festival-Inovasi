<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('innovations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('festival_id');
            $table->unsignedBigInteger('award_category_id');
            $table->unsignedBigInteger('institution_id');
            $table->string('name');
            $table->string('innovator_name');
            $table->timestamps();
            $table->softDeletes();

            $table->unique(
                ['festival_id', 'award_category_id', 'id'],
                'innovations_festival_category_id_unique'
            );
            $table->index(['festival_id', 'award_category_id', 'name'], 'innovations_category_name_index');
            $table->index(['festival_id', 'institution_id'], 'innovations_institution_index');

            $table->foreign(['festival_id', 'award_category_id'], 'innovations_award_category_fk')
                ->references(['festival_id', 'id'])
                ->on('award_categories')
                ->restrictOnDelete();

            $table->foreign(['festival_id', 'institution_id'], 'innovations_institution_fk')
                ->references(['festival_id', 'id'])
                ->on('institutions')
                ->restrictOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('innovations');
    }
};
