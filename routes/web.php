<?php

use App\Http\Controllers\API\AdminController;
use App\Http\Controllers\API\TaskController;
use App\Http\Controllers\ProfileController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Dashboard', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');


Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('/admin', [AdminController::class, 'index'])->name('admin.index');
    Route::get('/admin/add-task', [AdminController::class, 'create']);
    Route::post('/admin/add-task', [AdminController::class, 'store'])->name('admin.add-task');
    Route::get('/tasks', [AdminController::class, 'index'])->name('admin.tasks.index');
    Route::put('/tasks/{task}', [AdminController::class, 'update'])->name('admin.update');
    Route::delete('/tasks/{task}', [AdminController::class, 'destroy'])->name('admin.destroy');
});
Route::get('/tasks', [TaskController::class, 'index'])->name('task.index');
Route::post('/tasks-add', [TaskController::class, 'store'])->name('task.store');
Route::delete('/tasks/{task}', [TaskController::class, 'destroy'])->name('task.destroy');
Route::put('/tasks/{task}', [TaskController::class, 'update'])->name('task.update');


Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
