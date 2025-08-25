<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Authentication Controllers
use App\Http\Controllers\API\AuthenticationController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\Auth\ChangePasswordController;

// Resource Controllers
use App\Http\Controllers\UserController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\ChangeGradeFormController;
use App\Http\Controllers\CoursesChangeGradeFormController;
use App\Http\Controllers\ImportController;


/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// =============================================================================
// PUBLIC ROUTES (No Authentication Required)
// =============================================================================

// Authentication Routes
Route::group(['prefix' => 'auth', 'namespace' => 'App\\Http\\Controllers\\API'], function () {
    // Registration and Login
    Route::post('register', [AuthenticationController::class, 'register'])
        ->middleware('throttle:auth')
        ->name('auth.register');

    Route::post('login', [AuthenticationController::class, 'login'])
        ->middleware('throttle:auth')
        ->name('auth.login');

    // Password Reset (Guest Only)
    Route::post('forgot-password', [ForgotPasswordController::class, 'sendResetLinkEmail'])
        ->middleware(['guest', 'throttle:password'])
        ->name('password.email');

    Route::post('reset-password', [ResetPasswordController::class, 'reset'])
        ->middleware(['guest', 'throttle:password'])
        ->name('password.update');
});

// =============================================================================
// AUTHENTICATED ROUTES (Require Authentication)
// =============================================================================

Route::middleware('auth:sanctum')->group(function () {

    // -----------------------------------------------------------------------------
    // User Profile & Authentication Management
    // -----------------------------------------------------------------------------
    Route::group(['prefix' => 'auth'], function () {
        Route::get('user', [AuthenticationController::class, 'userInfo'])->name('auth.user');
        Route::post('logout', [AuthenticationController::class, 'logOut'])->name('auth.logout');
        Route::put('profile', [AuthenticationController::class, 'updateProfile'])->name('auth.profile.update');
        Route::post('change-password', [ChangePasswordController::class, 'changePassword'])
            ->middleware('throttle:password')
            ->name('auth.password.change');
    });

    // Legacy user endpoint (for backward compatibility)
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    // -----------------------------------------------------------------------------
    // Course Management (Read Access for All Authenticated Users)
    // -----------------------------------------------------------------------------
    Route::group(['prefix' => 'courses'], function () {
        Route::get('/', [CourseController::class, 'index'])->name('courses.index');
        Route::get('{course}', [CourseController::class, 'show'])->name('courses.show');
    });

    // -----------------------------------------------------------------------------
    // User Management (Limited Access)
    // -----------------------------------------------------------------------------
    Route::group(['prefix' => 'users'], function () {
        Route::get('/', [UserController::class, 'index'])->middleware('admin')->name('users.index');
        Route::get('{user}', [UserController::class, 'show'])->name('users.show'); // Own data or admin
        Route::put('{user}', [UserController::class, 'update'])->name('users.update'); // Own data or admin
    });

    // -----------------------------------------------------------------------------
    // Student Management (Limited Access)
    // -----------------------------------------------------------------------------
    Route::group(['prefix' => 'students'], function () {
        Route::get('/', [StudentController::class, 'index'])->middleware('admin')->name('students.index');
        Route::get('{student}', [StudentController::class, 'show'])->name('students.show'); // Own data or admin
        Route::put('{student}', [StudentController::class, 'update'])->name('students.update'); // Own data or admin
    });

    // -----------------------------------------------------------------------------
    // Grade Change Forms (All Authenticated Users)
    // -----------------------------------------------------------------------------
    Route::apiResource('change-grade-forms', ChangeGradeFormController::class);
    Route::apiResource('courses-change-grade-forms', CoursesChangeGradeFormController::class);
});

// =============================================================================
// ADMIN ROUTES (Require Admin Privileges)
// =============================================================================

Route::middleware(['auth:sanctum', 'admin'])->group(function () {

    // -----------------------------------------------------------------------------
    // User Administration
    // -----------------------------------------------------------------------------
    Route::group(['prefix' => 'admin/users'], function () {
        Route::post('{id}/verify', [UserController::class, 'verifyUser'])->name('admin.users.verify');
        Route::post('{id}/block', [UserController::class, 'blockUser'])->name('admin.users.block');
        Route::post('{id}/reset-password', [UserController::class, 'resetPassword'])->name('admin.users.reset-password');
        Route::delete('{id}/delete', [UserController::class, 'deleteUser'])->name('admin.users.delete');
        Route::delete('users/{user}', [UserController::class, 'destroy'])->name('admin.users.destroy');
    });

    // -----------------------------------------------------------------------------
    // Course Administration (Admin Only)
    // -----------------------------------------------------------------------------
    Route::group(['prefix' => 'admin/courses'], function () {
        Route::delete('/', [CourseController::class, 'destroyAll'])->name('admin.courses.destroy-all');
        Route::delete('{course}', [CourseController::class, 'destroy'])->name('admin.courses.destroy');
    });
});

// =============================================================================
// VERIFIED ADMIN ROUTES (Require Admin + Verification)
// =============================================================================

Route::middleware(['auth:sanctum', 'admin.verified'])->group(function () {

    // -----------------------------------------------------------------------------
    // User Management (Create)
    // -----------------------------------------------------------------------------
    Route::post('users', [UserController::class, 'store'])->name('admin.users.store');

    // -----------------------------------------------------------------------------
    // Course Management (Full CRUD)
    // -----------------------------------------------------------------------------
    Route::group(['prefix' => 'courses'], function () {
        Route::post('/', [CourseController::class, 'store'])->name('admin.courses.store');
        Route::put('{course}', [CourseController::class, 'update'])->name('admin.courses.update');
    });

    // -----------------------------------------------------------------------------
    // Student Management (Full CRUD)
    // -----------------------------------------------------------------------------
    Route::group(['prefix' => 'students'], function () {
        Route::post('/', [StudentController::class, 'store'])->name('admin.students.store');
        Route::delete('/', [StudentController::class, 'destroyAll'])->name('admin.students.destroy-all');
        Route::delete('{student}', [StudentController::class, 'destroy'])->name('admin.students.destroy');
    });

    // -----------------------------------------------------------------------------
    // Import Management (Verified Admin Only)
    // -----------------------------------------------------------------------------
    Route::group(['prefix' => 'import'], function () {
        // Import Data
        Route::post('students', [ImportController::class, 'importStudents'])->name('admin.import.students');
        Route::post('courses', [ImportController::class, 'importCourses'])->name('admin.import.courses');

        // Template Information and Downloads
        Route::get('template-info', [ImportController::class, 'getTemplateInfo'])->name('admin.import.template-info');
        Route::get('template/students', [ImportController::class, 'downloadStudentTemplate'])->name('admin.import.template.students');
        Route::get('template/courses', [ImportController::class, 'downloadCourseTemplate'])->name('admin.import.template.courses');
    });
});
