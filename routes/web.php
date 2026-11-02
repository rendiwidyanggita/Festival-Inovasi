<?php

use App\Http\Controllers\Admin;
use App\Http\Controllers\Admin\PublicationController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\WinnerController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Jury;
use App\Http\Controllers\PublicController;
use Illuminate\Support\Facades\Route;

// Public routes
Route::get('/', [PublicController::class, 'index'])->name('home');
Route::get('/festival/{festival}', [PublicController::class, 'festival'])->name('public.festival');
Route::get('/festival/{festival}/kategori/{awardCategory}', [PublicController::class, 'category'])->name('public.category');

// Authentication routes
Route::middleware('guest')->group(function () {
    Route::get('login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('login', [LoginController::class, 'login'])->middleware('throttle:5,1');
});

Route::post('logout', [LoginController::class, 'logout'])->middleware('auth')->name('logout');

// Admin routes
Route::middleware(['auth', 'role:superadmin', 'festival'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', Admin\DashboardController::class)->name('dashboard');
    
    // Festival selection
    Route::get('/select-festival', [Admin\FestivalSelectionController::class, 'index'])->name('select-festival.index');
    Route::post('/select-festival/{festival}', [Admin\FestivalSelectionController::class, 'select'])->name('select-festival.select');
    
    // Festivals
    Route::resource('festivals', Admin\FestivalController::class);
    
    // Festival Categories
    Route::resource('festival-categories', Admin\FestivalCategoryController::class)->except(['show']);
    
    // Award Categories (nested under festival categories)
    Route::resource('festival-categories.award-categories', Admin\AwardCategoryController::class)
        ->except(['show'])
        ->shallow();
    
    // Route aliases for backward compatibility and clean view routing
    Route::get('festival-categories/{festival_category}/subkategori', [Admin\AwardCategoryController::class, 'index'])->name('award-categories.index');
    Route::get('festival-categories/{festival_category}/subkategori/create', [Admin\AwardCategoryController::class, 'create'])->name('award-categories.create');
    Route::post('festival-categories/{festival_category}/subkategori', [Admin\AwardCategoryController::class, 'store'])->name('award-categories.store');
    
    // Institutions
    Route::resource('institutions', Admin\InstitutionController::class)->except(['show']);
    
    // Innovations
    Route::resource('innovations', Admin\InnovationController::class)->except(['show']);
    
    // Juries
    Route::resource('juries', Admin\JuryController::class)->except(['show']);
    Route::post('juries/{id}/restore', [Admin\JuryController::class, 'restore'])->name('juries.restore');
    
    // Festival Juries
    Route::resource('festival-juries', Admin\FestivalJuryController::class)->only(['index', 'create', 'store', 'update', 'destroy']);
    
    // Assessment Criteria
    Route::resource('assessment-criteria', Admin\AssessmentCriterionController::class)->except(['show']);
    
    
    // Winners Management
    Route::get('/winners', [Admin\WinnerController::class, 'index'])->name('winners.index');
    Route::get('/award-categories/{awardCategory}/rankings', [Admin\WinnerController::class, 'rankings'])->name('award-categories.rankings');
    Route::post('/award-categories/{awardCategory}/winners', [Admin\WinnerController::class, 'store'])->name('award-categories.winners.store');
    Route::patch('/winners/{winnerType}/{winnerId}', [Admin\WinnerController::class, 'update'])->name('winners.update');
    Route::delete('/winners/{winnerType}/{winnerId}', [Admin\WinnerController::class, 'destroy'])->name('winners.destroy');
    
    // Reports
    Route::get('/reports', [Admin\ReportController::class, 'index'])->name('reports.index');
    Route::get('/reports/{awardCategory}/preview', [Admin\ReportController::class, 'preview'])->name('reports.preview');
    Route::get('/reports/{awardCategory}/pdf', [Admin\ReportController::class, 'pdf'])->name('reports.pdf');
    
    // Publication
    Route::post('/festivals/{festival}/publish-results', [Admin\PublicationController::class, 'publish'])->name('festivals.publish-results');
    Route::post('/festivals/{festival}/unpublish-results', [Admin\PublicationController::class, 'unpublish'])->name('festivals.unpublish-results');
    Route::post('/award-categories/{awardCategory}/open-scoring', [Admin\ScoringLifecycleController::class, 'openScoring'])->name('award-categories.open-scoring');
    Route::post('/award-categories/{awardCategory}/close-scoring', [Admin\ScoringLifecycleController::class, 'closeScoring'])->name('award-categories.close-scoring');
    Route::post('/assessments/{type}/{id}/lock', [Admin\ScoringLifecycleController::class, 'lockAssessment'])->name('assessments.lock');
    Route::post('/assessments/{type}/{id}/request-revision', [Admin\ScoringLifecycleController::class, 'requestRevision'])->name('assessments.request-revision');
    
    // Scoring Monitoring
    Route::get('/scoring/monitoring', [Admin\ScoringMonitoringController::class, 'index'])->name('scoring.monitoring');
});
// Jury routes
Route::middleware(['auth', 'role:jury'])->prefix('jury')->name('jury.')->group(function () {
    Route::get('/', Jury\DashboardController::class)->name('dashboard');
    
    // Participations (Subcategory Selection)
    Route::get('/festivals/{festival}/participations', [Jury\ParticipationController::class, 'index'])->name('participations.index');
    Route::post('/festivals/{festival}/participations', [Jury\ParticipationController::class, 'store'])->name('participations.store');
    
    // Subcategories
    Route::get('/subcategories/{awardCategory}', [Jury\AssessmentController::class, 'show'])->name('subcategories.show');
    
    // Assessments
    Route::get('/subcategories/{awardCategory}/assess/{type}/{id}', [Jury\AssessmentController::class, 'assess'])->name('assessments.show');
    Route::post('/subcategories/{awardCategory}/assess/{type}/{id}/draft', [Jury\AssessmentController::class, 'draft'])->name('assessments.draft');
    Route::post('/subcategories/{awardCategory}/assess/{type}/{id}/submit', [Jury\AssessmentController::class, 'submit'])->name('assessments.submit');
});
