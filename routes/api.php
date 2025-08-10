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
    // User can access their own data and basic read operations
    Route::get('users', [UserController::class, 'index'])->middleware('admin'); // Admin only
    Route::get('users/{user}', [UserController::class, 'show']); // Own data or admin
    Route::put('users/{user}', [UserController::class, 'update']); // Own data or admin
    Route::delete('users/{user}', [UserController::class, 'destroy'])->middleware('admin'); // Admin only
    
    // Students can view courses
    Route::get('courses', [CourseController::class, 'index']);
    Route::get('courses/{course}', [CourseController::class, 'show']);
    
    // Students can view their own data
    Route::get('students', [StudentController::class, 'index'])->middleware('admin'); // Admin only
    Route::get('students/{student}', [StudentController::class, 'show']); // Own data or admin
    Route::put('students/{student}', [StudentController::class, 'update']); // Own data or admin
    
    // Grade change forms
    Route::apiResource('change-grade-forms', ChangeGradeFormController::class);
    Route::apiResource('courses-change-grade-forms', CoursesChangeGradeFormController::class);
});

// Admin-only routes
Route::middleware(['auth:sanctum', 'verified', 'admin'])->group(function () {
    // User management (admin only)
    Route::post('users', [UserController::class, 'store']);
    
    // Admin user management
    Route::apiResource('admin-users', AdminUserController::class);
    
    // Course management (admin only)
    Route::post('courses', [CourseController::class, 'store']);
    Route::put('courses/{course}', [CourseController::class, 'update']);
    Route::delete('courses/{course}', [CourseController::class, 'destroy']);
    
    // Student management (admin only)
    Route::post('students', [StudentController::class, 'store']);
    Route::delete('students/{student}', [StudentController::class, 'destroy']);
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
        ->middleware('throttle:3,1')
        ->name('password.email');
    
    Route::post('reset-password', [ResetPasswordController::class, 'reset'])
        ->middleware('guest')
        ->middleware('throttle:3,1')
        ->name('password.update');

    // ------------- Protected Routes -------------//
    Route::middleware('auth:sanctum')->group(function () {
        // User Info and Logout (no email verification required)
        Route::get('get-user', [AuthenticationController::class, 'userInfo'])->name('get-user');
        Route::post('logout', [AuthenticationController::class, 'logOut'])->name('logout');
        
        // Email Verification routes (no email verification required)
        Route::post('email/verify', [VerificationController::class, 'send'])->name('verification.send');
        Route::post('email/resend', [VerificationController::class, 'resend'])
            ->middleware('throttle:3,1')
            ->name('verification.resend');
    });
    
    // ------------- Sensitive Routes (Require Email Verification) -------------//
    Route::middleware(['auth:sanctum', 'verified'])->group(function () {
        // Profile Management
        Route::put('profile', [AuthenticationController::class, 'updateProfile'])->name('profile.update');
        
        // Password Change
        Route::post('change-password', [ChangePasswordController::class, 'changePassword'])
            ->middleware('throttle:3,1')
            ->name('password.change');
    });
    
    // Email Verification Link (Signed Route)
    Route::get('email/verify/{id}/{hash}', [VerificationController::class, 'verify'])
        ->middleware(['auth:sanctum', 'signed', 'throttle:3,1'])
        ->name('verification.verify');
});