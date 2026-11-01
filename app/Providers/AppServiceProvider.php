<?php

namespace App\Providers;

use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Alias stabil untuk audit_logs.auditable_type — jangan bergantung pada
        // nama class PHP yang bisa berubah saat refactor (BUSINESS_RULES §11).
        Relation::enforceMorphMap([
            'user' => \App\Models\User::class,
            'festival' => \App\Models\Festival::class,
            'festival_setting' => \App\Models\FestivalSetting::class,
            'festival_category' => \App\Models\FestivalCategory::class,
            'award_category' => \App\Models\AwardCategory::class,
            'institution' => \App\Models\Institution::class,
            'innovation' => \App\Models\Innovation::class,
            'festival_jury' => \App\Models\FestivalJury::class,
            'jury_participation' => \App\Models\JurySubcategoryParticipation::class,
            'assessment_criterion' => \App\Models\AssessmentCriterion::class,
            'innovation_assessment' => \App\Models\InnovationAssessment::class,
            'institution_assessment' => \App\Models\InstitutionAssessment::class,
            'innovation_winner' => \App\Models\InnovationWinner::class,
            'institution_winner' => \App\Models\InstitutionWinner::class,
        ]);
    }
}
