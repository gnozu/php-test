<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TaskController;

// standard routes
Route::get('/tasks', [TaskController::class, 'index'])->name('tasks.index')->middleware('log');
Route::get('/tasks/{id}', [TaskController::class, 'show'])->name('task.show')->middleware('log');
Route::post('/tasks', [TaskController::class, 'store'])->name('task.store')->middleware('log');

// protected routes
Route::put('/tasks/{id}', [TaskController::class, 'update'])->name('task.update')->middleware('signed')->middleware('log');
Route::delete('/tasks/{id}', [TaskController::class, 'delete'])->name('task.delete')->middleware('signed')->middleware('log');
