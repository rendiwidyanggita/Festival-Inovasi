<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('innovation_winners', function (Blueprint $table) {
            $table->decimal('total_score', 8, 2)->default(0)->after('appointed_at');
        });

        Schema::table('institution_winners', function (Blueprint $table) {
            $table->decimal('total_score', 8, 2)->default(0)->after('appointed_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('innovation_winners', function (Blueprint $table) {
            $table->dropColumn('total_score');
        });

        Schema::table('institution_winners', function (Blueprint $table) {
            $table->dropColumn('total_score');
        });
    }
};
