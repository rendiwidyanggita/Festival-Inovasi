<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('institutions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('festival_id');
            $table->unsignedBigInteger('award_category_id');
            $table->string('name');
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['festival_id', 'id'], 'institutions_festival_id_unique');
            $table->unique(
                ['festival_id', 'award_category_id', 'id'],
                'institutions_festival_category_id_unique'
            );
            $table->index(['festival_id', 'award_category_id', 'name'], 'institutions_category_name_index');

            $table->foreign(['festival_id', 'award_category_id'], 'institutions_award_category_fk')
                ->references(['festival_id', 'id'])
                ->on('award_categories')
                ->restrictOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('institutions');
    }
};
