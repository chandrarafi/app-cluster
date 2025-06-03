<?php

use App\Livewire\Settings\Appearance;
use App\Livewire\Settings\Password;
use App\Livewire\Settings\Profile;
use App\Livewire\UserManagement;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ExcelController;
use App\Http\Controllers\UserController;

Route::redirect('/', '/login')->name('home');

Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');


Route::middleware(['auth'])->group(function () {
    Route::redirect('settings', 'settings/profile');

    Route::get('settings/profile', Profile::class)->name('settings.profile');
    Route::get('settings/password', Password::class)->name('settings.password');
    Route::get('settings/appearance', Appearance::class)->name('settings.appearance');

    Route::get('dataset/siswa', App\Livewire\Dataset\StudentDataset::class)->name('student.dataset');

    Route::get('clustering/elbow', App\Livewire\Clustering\ElbowMethod::class)->name('clustering.elbow');
    Route::get('clustering/kmeans', App\Livewire\Clustering\KMeansClustering::class)->name('clustering.kmeans');
    Route::get('clustering/result', App\Livewire\Clustering\ClusteringResult::class)->name('clustering.result');
    Route::get('clustering/setup', App\Livewire\Clustering\SetupCluster::class)->name('clustering.setup');

    Route::get('/excel/import-form', [ExcelController::class, 'importForm'])->name('excel.import-form');

    Route::post('/excel/import', [ExcelController::class, 'import'])->name('excel.import');
    Route::get('/excel/export', [ExcelController::class, 'export'])->name('excel.export');
    Route::get('/excel/download-template', [ExcelController::class, 'downloadTemplate'])->name('excel.download-template');

    Route::get('users', function () {
        return view('users.index-livewire');
    })->name('users.index');
    Route::resource('users', UserController::class)->except(['index']);
});

require __DIR__ . '/auth.php';
