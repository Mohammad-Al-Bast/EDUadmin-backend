<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\UserController;
use App\Http\Controllers\AdminUserController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\CoursesChangeGradeFormController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\ChangeGradeFormController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::apiResource('users', UserController::class);
Route::apiResource('admin-users', AdminUserController::class);
Route::apiResource('courses', CourseController::class);
Route::apiResource('courses-change-grade-forms', CoursesChangeGradeFormController::class);
Route::apiResource('students', StudentController::class);
Route::apiResource('change-grade-forms', ChangeGradeFormController::class);
