<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\RecommendationController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SuperAdmin\SuperAdminDashboardController;
use App\Http\Controllers\SuperAdmin\LocationController as SuperAdminLocationController;
use App\Http\Controllers\SuperAdmin\TouristSpotController as SuperAdminTouristSpotController;
use App\Http\Controllers\User\TouristSpotController as UserTouristSpotController;
use App\Http\Controllers\User\LocationController as UserLocationController;
use App\Http\Controllers\SuperAdmin\CriteriaController as SuperAdminCriteriaController;
use App\Http\Controllers\SuperAdmin\CriteriaTypeController as SuperAdminCriteriaTypeController;
use App\Http\Controllers\ImportDataController;



Route::get('/', function () {
    return view('welcome');
})->name('welcome');

// User Authentication Routes
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.submit');

Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
Route::post('/register', [AuthController::class, 'register'])->name('register.submit');

Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Super Administrator Authentication Routes
Route::get('/super-admin/login', [AuthController::class, 'showSuperAdminLoginForm'])->name('super-admin.login');
Route::post('/super-admin/login', [AuthController::class, 'superAdminLogin'])->name('super-admin.login.submit');
Route::post('/super-admin/logout', [AuthController::class, 'superAdminLogout'])->name('super-admin.logout');

// Admin Authentication Routes
Route::get('/admin/login', [AuthController::class, 'showAdminLoginForm'])->name('admin.login');
Route::post('/admin/login', [AuthController::class, 'adminLogin'])->name('admin.login.submit');
Route::post('/admin/logout', [AuthController::class, 'adminLogout'])->name('admin.logout');

// Protected Routes
Route::get('/dashboard', [RecommendationController::class, 'dashboard'])->name('user.dashboard');

// user view tourist spot
Route::get('/viewTouristSpot', [\App\Http\Controllers\ViewTouristSpot::class, 'index'])->name('viewTouristSpot');
Route::get('/viewLocation', [UserLocationController::class, 'index'])->name('viewLocation');
Route::get('/view-spots', [UserTouristSpotController::class, 'index'])->name('user.viewTouristSpot');

// Super Admin protected routes
Route::middleware('super-admin.auth')->group(function () {
    // Super Admin Dashboard
    Route::get('/super-admin/dashboard', [SuperAdminDashboardController::class, 'index'])->name('super-admin.dashboard');
    Route::get('/super-admin/submissions', [SuperAdminDashboardController::class, 'submissions'])->name('super-admin.submissions.index');
    Route::get('/super-admin/sus-submissions', [SuperAdminDashboardController::class, 'susSubmissions'])->name('super-admin.sus_submissions.index');

    // Super Admin Profile
    Route::get('/super-admin/profile', [\App\Http\Controllers\SuperAdmin\SuperAdminProfileController::class, 'show'])->name('super-admin.profile.show');
    Route::get('/super-admin/profile/edit', [\App\Http\Controllers\SuperAdmin\SuperAdminProfileController::class, 'edit'])->name('super-admin.profile.edit');
    Route::put('/super-admin/profile', [\App\Http\Controllers\SuperAdmin\SuperAdminProfileController::class, 'update'])->name('super-admin.profile.update');
    Route::put('/super-admin/profile/password', [\App\Http\Controllers\SuperAdmin\SuperAdminProfileController::class, 'updatePassword'])->name('super-admin.profile.password');

    // Super Admin: Locations
    Route::get('/super-admin/locations', [SuperAdminLocationController::class, 'index'])->name('super-admin.locations.index');
    Route::get('/super-admin/locations/create', [SuperAdminLocationController::class, 'create'])->name('super-admin.locations.create');
    Route::post('/super-admin/locations', [SuperAdminLocationController::class, 'store'])->name('super-admin.locations.store');
    Route::get('/super-admin/locations/{location}/edit', [SuperAdminLocationController::class, 'edit'])->name('super-admin.locations.edit');
    Route::put('/super-admin/locations/{location}', [SuperAdminLocationController::class, 'update'])->name('super-admin.locations.update');
    Route::delete('/super-admin/locations/{location}', [SuperAdminLocationController::class, 'destroy'])->name('super-admin.locations.destroy');

    // Super Admin: Tourist Spots
    Route::get('/super-admin/tourist-spots', [SuperAdminTouristSpotController::class, 'index'])->name('super-admin.tourist_spots.index');
    Route::get('/super-admin/tourist-spots/create', [SuperAdminTouristSpotController::class, 'create'])->name('super-admin.tourist_spots.create');
    Route::post('/super-admin/tourist-spots', [SuperAdminTouristSpotController::class, 'store'])->name('super-admin.tourist_spots.store');
    Route::get('/super-admin/tourist-spots/{tourist_spot}/edit', [SuperAdminTouristSpotController::class, 'edit'])->name('super-admin.tourist_spots.edit');
    Route::put('/super-admin/tourist-spots/{tourist_spot}', [SuperAdminTouristSpotController::class, 'update'])->name('super-admin.tourist_spots.update');
    Route::delete('/super-admin/tourist-spots/{tourist_spot}', [SuperAdminTouristSpotController::class, 'destroy'])->name('super-admin.tourist_spots.destroy');

    // Super Admin: Criteria
    Route::get('/super-admin/criteria', [SuperAdminCriteriaController::class, 'index'])->name('super-admin.criteria.index');
    Route::get('/super-admin/criteria/create', [SuperAdminCriteriaController::class, 'create'])->name('super-admin.criteria.create');
    Route::post('/super-admin/criteria', [SuperAdminCriteriaController::class, 'store'])->name('super-admin.criteria.store');
    Route::get('/super-admin/criteria/{criteria}/edit', [SuperAdminCriteriaController::class, 'edit'])->name('super-admin.criteria.edit');
    Route::put('/super-admin/criteria/{criteria}', [SuperAdminCriteriaController::class, 'update'])->name('super-admin.criteria.update');
    Route::delete('/super-admin/criteria/{criteria}', [SuperAdminCriteriaController::class, 'destroy'])->name('super-admin.criteria.destroy');

    // Super Admin: Criteria Types
    Route::get('/super-admin/criteria-types', [SuperAdminCriteriaTypeController::class, 'index'])->name('super-admin.criteria_types.index');
    Route::get('/super-admin/criteria-types/create', [SuperAdminCriteriaTypeController::class, 'create'])->name('super-admin.criteria_types.create');
    Route::post('/super-admin/criteria-types', [SuperAdminCriteriaTypeController::class, 'store'])->name('super-admin.criteria_types.store');
    Route::get('/super-admin/criteria-types/{criteria_type}/edit', [SuperAdminCriteriaTypeController::class, 'edit'])->name('super-admin.criteria_types.edit');
    Route::put('/super-admin/criteria-types/{criteria_type}', [SuperAdminCriteriaTypeController::class, 'update'])->name('super-admin.criteria_types.update');
    Route::delete('/super-admin/criteria-types/{criteria_type}', [SuperAdminCriteriaTypeController::class, 'destroy'])->name('super-admin.criteria_types.destroy');
});

// Admin protected routes
// Recommendation Routes
Route::get('/recommendations/criteria', [RecommendationController::class, 'directRatingMethod'])->name('recommendations.drm');
Route::get('/recommendations/hundred-dollar', [RecommendationController::class, 'hundredDollarMethod'])->name('recommendations.hdm');
Route::get('/recommendations/kano', [RecommendationController::class, 'kanoMethod'])->name('recommendations.kano');
Route::post('/recommendations/calculate', [RecommendationController::class, 'calculateRecommendations'])->name('recommendations.calculate');
Route::get('/recommendations/results', [RecommendationController::class, 'showResults'])->name('recommendations.results');
Route::get('/recommendations/previous', [RecommendationController::class, 'showPreviousResult'])->name('recommendations.showPrevious');
Route::get('/sus', [RecommendationController::class, 'susPage'])->name('recommendations.sus.index');
Route::post('/recommendations/sus', [RecommendationController::class, 'submitSystemUsabilityScale'])->name('recommendations.sus.submit');
Route::get('/recommendations/compare', [RecommendationController::class, 'compareRecommendations'])->name('recommendations.compare');
Route::post('/recommendations/clear-method', [RecommendationController::class, 'clearMethodResult'])->name('recommendations.clear_method');
Route::post('/recommendations/favorite', [RecommendationController::class, 'saveFavorite'])->name('recommendations.save_favorite');
Route::post('/recommendations/send-to-admin', [RecommendationController::class, 'sendResultsToAdmin'])->name('recommendations.send_admin');

// Profile Routes
Route::get('/profile', [ProfileController::class, 'show'])->name('profile.show');
Route::get('/profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password');
Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

// Import Routes
Route::post('/import-data', [ImportDataController::class, 'import'])->name('import-data');
