<?php

use Illuminate\Http\Request;
use App\Http\Controllers\SuperAdmin\RoleController;
use App\Http\Controllers\SuperAdmin\SuperAdminController;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::get('roles', [RoleController::class, 'index']);
Route::post('roles', [RoleController::class, 'store']);
Route::put('roles/edit/{id}', [RoleController::class, 'edit']);
Route::delete('roles/delete/{id}', [RoleController::class, 'destroy']);

Route::get('super-admins', [SuperAdminController::class, 'index']);
Route::post('super-admins', [SuperAdminController::class, 'store']);
Route::put('super-admins/edit/{id}', [SuperAdminController::class, 'edit']);
Route::delete('super-admins/delete/{id}', [SuperAdminController::class, 'destroy']);
