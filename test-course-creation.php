#!/usr/bin/env php
<?php

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Course;

echo "=== Testing Course Creation Directly ===\n\n";

try {
    $course = Course::create([
        'course_name' => 'Direct Test Course',
        'course_code' => 'DIRECT101',
        'course_description' => 'Test course created directly',
    ]);

    echo "✅ SUCCESS: Course created directly\n";
    echo "   ID: {$course->course_id}\n";
    echo "   Name: {$course->course_name}\n";
    echo "   Code: {$course->course_code}\n";
} catch (\Exception $e) {
    echo "❌ ERROR: {$e->getMessage()}\n";
    echo "   File: {$e->getFile()}:{$e->getLine()}\n";
}

echo "\n=== Testing Course Validation ===\n\n";

try {
    $validator = \Validator::make([
        'course_name' => 'Validation Test Course',
        'course_code' => 'VALID101',
        'course_description' => 'Test course for validation',
    ], [
        'course_name'        => 'required|string|max:255|unique:courses,course_name,NULL,course_id',
        'course_code'        => 'required|string|max:50|unique:courses,course_code,NULL,course_id',
        'course_description' => 'nullable|string',
    ]);

    if ($validator->fails()) {
        echo "❌ Validation failed:\n";
        foreach ($validator->errors()->all() as $error) {
            echo "   - $error\n";
        }
    } else {
        echo "✅ Validation passed\n";
    }
} catch (\Exception $e) {
    echo "❌ Validation error: {$e->getMessage()}\n";
}

echo "\n=== Done ===\n";
