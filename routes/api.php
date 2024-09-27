<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\AdminController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('/admin/add-task', [AdminController::class, 'create']);
    Route::post('/admin/add-task', [AdminController::class, 'store'])->name('admin.add-task');
    Route::get('/admin/tasks', [AdminController::class, 'index'])->name('admin.tasks.index');
    Route::get('/admin/tasks/create', [AdminController::class, 'create'])->name('admin.tasks.create');
    Route::get('/admin/tasks/{task}/edit', [AdminController::class, 'edit'])->name('admin.tasks.edit');
    Route::put('/admin/tasks/{task}', [AdminController::class, 'update'])->name('admin.tasks.update');
    Route::delete('/admin/tasks/{task}', [AdminController::class, 'destroy'])->name('admin.tasks.destroy');
});
