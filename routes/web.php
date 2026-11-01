<?php

use App\Http\Controllers\Admin;
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
    Route::post('login', [LoginController::class, 'login']);
});

Route::post('logout', [LoginController::class, 'logout'])->middleware('auth')->name('logout');

// Admin routes
Route::middleware(['auth', 'role:superadmin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', Admin\DashboardController::class)->name('dashboard');
    
    // Festival selection
    Route::get('/select-festival', [Admin\FestivalSelectionController::class, 'index'])->name('select-festival.index');
    Route::post('/select-festival/{festival}', [Admin\FestivalSelectionController::class, 'select'])->name('select-festival.select');
    
    // Festivals
    Route::resource('festivals', Admin\FestivalController::class)->except(['destroy']);
    
    // Festival Categories
    Route::resource('festival-categories', Admin\FestivalCategoryController::class)->except(['show']);
    
    // Award Categories (nested under festival categories)
    Route::resource('festival-categories.award-categories', Admin\AwardCategoryController::class)
        ->except(['show'])
        ->shallow();
    
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
});

// Jury routes
Route::middleware(['auth', 'role:jury'])->prefix('jury')->name('jury.')->group(function () {
    Route::get('/', Jury\DashboardController::class)->name('dashboard');
});
