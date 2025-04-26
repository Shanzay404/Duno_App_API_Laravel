<?php

use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\forgotPasswordController;
use App\Http\Controllers\Budget\BudgetController;
use App\Http\Controllers\Expense\ExpenseController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Laravel\Sanctum\Sanctum;

// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');


Route::post('signup', [AuthController::class, 'signup']);
Route::post('login', [AuthController::class, 'login']);
Route::post('logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');
Route::post('edit-profile/{id}', [AuthController::class, 'Edit'])->middleware('auth:sanctum');

Route::post('forgot-password', [forgotPasswordController::class, 'forgotPassword']);
Route::post('verify-otp', [forgotPasswordController::class, 'verifyOtp']);
Route::post('resend-otp', [forgotPasswordController::class, 'forgotPassword']);
// Route::post('reset-password', [forgotPasswordController::class, 'submitResetPasswordForm']);
Route::post('change-password/{id}', [forgotPasswordController::class, 'changePassword']);

Route::post('create-budget', [BudgetController::class, 'create'])->middleware('auth:sanctum');
Route::post('view-budget-report/{id}', [BudgetController::class, 'viewReport'])->middleware('auth:sanctum');
Route::post('create-expense', [ExpenseController::class, 'create'])->middleware('auth:sanctum');
