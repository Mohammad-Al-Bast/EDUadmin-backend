<?php


use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\UserController;
use App\Http\Controllers\AdminUserController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\CoursesChangeGradeFormController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\ChangeGradeFormController;
use App\Http\Controllers\API\AuthenticationController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\Auth\ChangePasswordController;
use App\Http\Controllers\Auth\VerificationController;


Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// Protected Resource Routes - Require Authentication
Route::middleware('auth:sanctum')->group(function () {
    Route::apiResource('users', UserController::class);
    Route::apiResource('admin-users', AdminUserController::class);
    Route::apiResource('courses', CourseController::class);
    Route::apiResource('courses-change-grade-forms', CoursesChangeGradeFormController::class);
    Route::apiResource('students', StudentController::class);
    Route::apiResource('change-grade-forms', ChangeGradeFormController::class);
});

Route::group(['namespace' => 'App\\Http\\Controllers\\API'], function () {
    // ------------- Register and Login -------------//
    Route::post('register', [AuthenticationController::class, 'register'])
        ->middleware('throttle:6,1')
        ->name('register');
    Route::post('login', [AuthenticationController::class, 'login'])
        ->middleware('throttle:6,1')
        ->name('login');

    // ------------- Password Reset (Guest) -------------//
    Route::post('forgot-password', [ForgotPasswordController::class, 'sendResetLinkEmail'])
        ->middleware('guest')
        ->middleware('throttle:6,1')
        ->name('password.email');
    
    Route::post('reset-password', [ResetPasswordController::class, 'reset'])
        ->middleware('guest')
        ->middleware('throttle:6,1')
        ->name('password.update');

    // ------------- Protected Routes -------------//
    Route::middleware('auth:sanctum')->group(function () {
        // User Info and Logout
        Route::get('get-user', [AuthenticationController::class, 'userInfo'])->name('get-user');
        Route::post('logout', [AuthenticationController::class, 'logOut'])->name('logout');
        
        // Profile Management
        Route::put('profile', [AuthenticationController::class, 'updateProfile'])->name('profile.update');
        
        // Password Change
        Route::post('change-password', [ChangePasswordController::class, 'changePassword'])
            ->middleware('throttle:6,1')
            ->name('password.change');
        
        // Email Verification
        Route::post('email/verify', [VerificationController::class, 'send'])->name('verification.send');
        Route::post('email/resend', [VerificationController::class, 'resend'])
            ->middleware('throttle:6,1')
            ->name('verification.resend');
    });
    
    // Email Verification Link (Signed Route)
    Route::get('email/verify/{id}/{hash}', [VerificationController::class, 'verify'])
        ->middleware(['auth:sanctum', 'signed', 'throttle:6,1'])
        ->name('verification.verify');
});