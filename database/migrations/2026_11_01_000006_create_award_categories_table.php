<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('award_categories', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('festival_id');
            $table->unsignedBigInteger('festival_category_id');
            $table->string('name');
            $table->unsignedSmallInteger('winner_count')->default(1);
            $table->string('scoring_status', 20)->default('preparation'); // preparation | open | closed
            $table->timestamp('opened_at')->nullable();
            $table->foreignId('opened_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('closed_at')->nullable();
            $table->foreignId('closed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->timestamps();

            $table->unique(['festival_category_id', 'name'], 'award_categories_parent_name_unique');
            $table->unique(['festival_id', 'id'], 'award_categories_festival_id_unique');
            $table->unique(
                ['festival_id', 'festival_category_id', 'id'],
                'award_categories_festival_parent_id_unique'
            );

            $table->foreign(['festival_id', 'festival_category_id'], 'award_categories_parent_fk')
                ->references(['festival_id', 'id'])
                ->on('festival_categories')
                ->restrictOnDelete();

            $table->index(['festival_id', 'scoring_status'], 'award_categories_status_index');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('award_categories');
    }
};
