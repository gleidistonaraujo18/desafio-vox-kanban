<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\BoardController;
use App\Http\Controllers\ColumnController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/', [AuthController::class, 'index'])->name('index');

Route::prefix('users')->group(function () {
    Route::get('/create', [UserController::class, 'create'])->name('register');
    Route::post('/', [UserController::class, 'store'])->name('register.store');
});


Route::prefix('auth')->group(function () {
    Route::post('/login', [AuthController::class, 'loginAttempt'])->name('login');
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
});

Route::middleware('auth')->group(function () {
    Route::prefix('boards')->group(function () {
        Route::get('/', [BoardController::class, 'index'])->name('boards.index');
        Route::get('/create', [BoardController::class, 'create'])->name('boards.create');
        Route::post('/', [BoardController::class, 'store'])->name('boards.store');
    });

    Route::prefix('api')->group(function () {
        Route::get('/boards', [BoardController::class, 'indexJson'])->name('api.boards.index');
        Route::post('/boards', [BoardController::class, 'storeJson'])->name('api.boards.store');
        Route::patch('/boards/{board}', [BoardController::class, 'update'])->name('api.boards.update');
        Route::put('/boards/{board}', [BoardController::class, 'update']);
        Route::delete('/boards/{board}', [BoardController::class, 'destroy'])->name('api.boards.destroy');

        Route::get('/boards/{board}/columns', [ColumnController::class, 'index'])->name('api.columns.index');
        Route::post('/boards/{board}/columns', [ColumnController::class, 'store'])->name('api.columns.store');
        Route::get('/boards/{board}/columns-with-tasks', [ColumnController::class, 'indexWithTasks'])->name('api.columns.index-with-tasks');
        Route::patch('/columns/{column}', [ColumnController::class, 'update'])->name('api.columns.update');
        Route::put('/columns/{column}', [ColumnController::class, 'update']);
        Route::delete('/columns/{column}', [ColumnController::class, 'destroy'])->name('api.columns.destroy');

        Route::get('/columns/{column}/tasks', [TaskController::class, 'index'])->name('api.tasks.index');
        Route::post('/columns/{column}/tasks', [TaskController::class, 'store'])->name('api.tasks.store');
        Route::patch('/tasks/{task}', [TaskController::class, 'update'])->name('api.tasks.update');
        Route::put('/tasks/{task}', [TaskController::class, 'update']);
        Route::delete('/tasks/{task}', [TaskController::class, 'destroy'])->name('api.tasks.destroy');
        Route::patch('/tasks/{task}/move', [TaskController::class, 'move'])->name('api.tasks.move');
        Route::patch('/tasks/reorder', [TaskController::class, 'reorder'])->name('api.tasks.reorder');
    });
});
