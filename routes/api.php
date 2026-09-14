<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\TaskController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\NotificationController;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->name('api.')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    Route::apiResource('projects', ProjectController::class);

    Route::get('/projects/{projectId}/tasks', [TaskController::class, 'index']);
    Route::get('/tasks/assigned', [TaskController::class, 'assigned']);
    Route::post('/tasks', [TaskController::class, 'store']);
    Route::patch('/tasks/{task}/status', [TaskController::class, 'updateStatus']);
    Route::delete('/tasks/{task}', [TaskController::class, 'destroy']);
    Route::post('/tasks/{task}/assign', [TaskController::class, 'assign']);
    Route::post('/tasks/{task}/set-due-date', [TaskController::class, 'setDueDate']);

    Route::get('/tasks/{task}/comments', [CommentController::class, 'index']);
    Route::post('/tasks/{task}/comments', [CommentController::class, 'store']);

    Route::get('/notifications', [NotificationController::class, 'index']);
    Route::patch('/notifications/{id}/read', [NotificationController::class, 'markAsRead']);
    Route::post('/notifications/read-all', [NotificationController::class, 'markAllAsRead']);
});
