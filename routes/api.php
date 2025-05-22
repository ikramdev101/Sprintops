// routes/api.php
<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\TeamController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\NotificationController;



// Routes publiques
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// Routes protégées
Route::middleware('auth:sanctum')->group(function () {
    // Auth
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', [AuthController::class, 'user']);
    Route::put('/user/theme', [AuthController::class, 'updateTheme']);

    // Users
    Route::apiResource('users', UserController::class);
    Route::get('/users/{user}/projects', [UserController::class, 'projects']);
    Route::get('/users/{user}/teams', [UserController::class, 'teams']);
    Route::get('/users/{user}/tasks', [UserController::class, 'tasks']);

    // Projects
    Route::apiResource('projects', ProjectController::class);
    Route::get('/projects/{project}/members', [ProjectController::class, 'members']);
    Route::post('/projects/{project}/members', [ProjectController::class, 'addMember']);
    Route::delete('/projects/{project}/members', [ProjectController::class, 'removeMember']);
    Route::get('/projects/{project}/tasks', [ProjectController::class, 'tasks']);
    Route::get('/projects/{project}/statistics', [ProjectController::class, 'statistics']);

    // Tasks
    Route::apiResource('tasks', TaskController::class);
    Route::get('/tasks/{task}/comments', [TaskController::class, 'comments']);
    Route::post('/tasks/{task}/comments', [TaskController::class, 'addComment']);

    // Teams
    Route::apiResource('teams', TeamController::class);
    Route::get('/teams/{team}/members', [TeamController::class, 'members']);
    Route::post('/teams/{team}/members', [TeamController::class, 'addMember']);
    Route::delete('/teams/{team}/members', [TeamController::class, 'removeMember']);
    Route::get('/teams/{team}/projects', [TeamController::class, 'projects']);
    Route::post('/teams/{team}/projects', [TeamController::class, 'addProject']);
    Route::delete('/teams/{team}/projects', [TeamController::class, 'removeProject']);

    // Notifications
    Route::get('/notifications', [NotificationController::class, 'index']);
    Route::get('/notifications/{notification}', [NotificationController::class, 'show']);
    Route::put('/notifications/{notification}/read', [NotificationController::class, 'markAsRead']);
    Route::put('/notifications/read-all', [NotificationController::class, 'markAllAsRead']);
    Route::delete('/notifications/{notification}', [NotificationController::class, 'destroy']);
});