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
use App\Http\Controllers\API\DashboardController;


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

    // -----------------------------------------------------------------------------
    // Course Management (Read Access for All Authenticated Users)
    // -----------------------------------------------------------------------------
    Route::group(['prefix' => 'courses'], function () {
        Route::get('/', [CourseController::class, 'index'])->name('courses.index');
        Route::get('{course}', [CourseController::class, 'show'])->name('courses.show');
    });

    // -----------------------------------------------------------------------------
    // Dashboard Statistics (All Authenticated Users)
    // -----------------------------------------------------------------------------
    Route::group(['prefix' => 'dashboard'], function () {
        Route::get('summary', [DashboardController::class, 'summary'])->name('dashboard.summary');
        Route::get('stats', [DashboardController::class, 'detailedStats'])->name('dashboard.stats');
    });

    // -----------------------------------------------------------------------------
    // User Management - Moved to Admin Section
    // -----------------------------------------------------------------------------
    // All user management operations moved to /admin/users/ prefix for security

    // -----------------------------------------------------------------------------
    // Student Management (Limited Access)
    // -----------------------------------------------------------------------------
    Route::group(['prefix' => 'students'], function () {
        Route::get('/', [StudentController::class, 'index'])->middleware('admin')->name('students.index');
        Route::get('{student}', [StudentController::class, 'show'])->name('students.show'); // Own data or admin
        // PUT operation moved to admin section
    });

    // -----------------------------------------------------------------------------
    // Grade Change Forms (All Authenticated Users)
    // -----------------------------------------------------------------------------
    Route::group(['prefix' => 'change-grade-forms'], function () {
        Route::get('/', [ChangeGradeFormController::class, 'index'])->name('change-grade-forms.index');
        Route::post('/', [ChangeGradeFormController::class, 'store'])->name('change-grade-forms.store');
        Route::get('{change_grade_form}', [ChangeGradeFormController::class, 'show'])->name('change-grade-forms.show');
        Route::put('{change_grade_form}', [ChangeGradeFormController::class, 'update'])->name('change-grade-forms.update');
        Route::patch('{change_grade_form}', [ChangeGradeFormController::class, 'update'])->name('change-grade-forms.update');
        Route::delete('{change_grade_form}', [ChangeGradeFormController::class, 'destroy'])->name('change-grade-forms.destroy');
    });

    Route::group(['prefix' => 'courses-change-grade-forms'], function () {
        Route::get('/', [CoursesChangeGradeFormController::class, 'index'])->name('courses-change-grade-forms.index');
        Route::post('/', [CoursesChangeGradeFormController::class, 'store'])->name('courses-change-grade-forms.store');
        Route::get('{courses_change_grade_form}', [CoursesChangeGradeFormController::class, 'show'])->name('courses-change-grade-forms.show');
        Route::put('{courses_change_grade_form}', [CoursesChangeGradeFormController::class, 'update'])->name('courses-change-grade-forms.update');
        Route::patch('{courses_change_grade_form}', [CoursesChangeGradeFormController::class, 'update'])->name('courses-change-grade-forms.update');
        Route::delete('{courses_change_grade_form}', [CoursesChangeGradeFormController::class, 'destroy'])->name('courses-change-grade-forms.destroy');
    });
});

// =============================================================================
// ADMIN ROUTES (Require Admin Privileges)
// =============================================================================

Route::middleware(['auth:sanctum', 'admin'])->group(function () {

    // -----------------------------------------------------------------------------
    // User Administration
    // -----------------------------------------------------------------------------
    Route::group(['prefix' => 'admin/users'], function () {
        // User CRUD operations
        Route::get('/', [UserController::class, 'index'])->name('admin.users.index');
        Route::get('{user}', [UserController::class, 'show'])->name('admin.users.show');
        Route::put('{user}', [UserController::class, 'update'])->name('admin.users.update');

        // User admin actions
        Route::post('{id}/verify', [UserController::class, 'verifyUser'])->name('admin.users.verify');
        Route::post('{id}/block', [UserController::class, 'blockUser'])->name('admin.users.block');
        Route::match(['post', 'put'], '{id}/reset-password', [UserController::class, 'resetPassword'])->name('admin.users.reset-password');
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

    // -----------------------------------------------------------------------------
    // Student Administration (Admin Only)
    // -----------------------------------------------------------------------------
    Route::group(['prefix' => 'admin/students'], function () {
        Route::put('{student}', [StudentController::class, 'update'])->name('admin.students.update');
        Route::delete('/', [StudentController::class, 'destroyAll'])->name('admin.students.destroy-all');
        Route::delete('{student}', [StudentController::class, 'destroy'])->name('admin.students.destroy');
    });
});

// =============================================================================
// VERIFIED ADMIN ROUTES (Require Admin + Verification)
// =============================================================================

Route::middleware(['auth:sanctum', 'admin.verified'])->group(function () {

    // -----------------------------------------------------------------------------
    // User Management (Create) - Added to Admin Users Section
    // -----------------------------------------------------------------------------
    Route::group(['prefix' => 'admin/users'], function () {
        Route::post('/', [UserController::class, 'store'])->name('admin.users.store');
    });

    // -----------------------------------------------------------------------------
    // Course Management (Full CRUD) - Moved to Admin Section
    // -----------------------------------------------------------------------------
    Route::group(['prefix' => 'admin/courses'], function () {
        Route::post('/', [CourseController::class, 'store'])->name('admin.courses.store');
        Route::put('{course}', [CourseController::class, 'update'])->name('admin.courses.update');
    });

    // -----------------------------------------------------------------------------
    // Student Management (Full CRUD) - Moved to Admin Section
    // -----------------------------------------------------------------------------
    Route::group(['prefix' => 'admin/students'], function () {
        Route::post('/', [StudentController::class, 'store'])->name('admin.students.store');
        // DELETE operations already in admin section above
    });

    // -----------------------------------------------------------------------------
    // Import Management (Verified Admin Only)
    // -----------------------------------------------------------------------------
    Route::group(['prefix' => 'admin/import'], function () {
        // Import Data
        Route::post('students', [ImportController::class, 'importStudents'])->name('admin.import.students');
        Route::post('courses', [ImportController::class, 'importCourses'])->name('admin.import.courses');

        // Template Information and Downloads
        Route::get('template-info', [ImportController::class, 'getTemplateInfo'])->name('admin.import.template-info');
        Route::get('template/students', [ImportController::class, 'downloadStudentTemplate'])->name('admin.import.template.students');
        Route::get('template/courses', [ImportController::class, 'downloadCourseTemplate'])->name('admin.import.template.courses');
    });
});
