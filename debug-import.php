<?php

require_once 'vendor/autoload.php';

use Illuminate\Foundation\Application;
use Illuminate\Http\Request;
use App\Http\Requests\ImportCoursesRequest;

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "=== Testing Import Course Request Validation ===\n\n";

// Test case 1: Missing file
echo "1. Testing with missing file:\n";
$request1 = Request::create('/api/v1/admin/import/courses', 'POST', [
    'semester' => 'Fall 2025'
]);

$importRequest1 = new ImportCoursesRequest();
$importRequest1->merge($request1->all());
$importRequest1->files = $request1->files;

try {
    $rules = $importRequest1->rules();
    $validator = \Illuminate\Support\Facades\Validator::make($request1->all(), $rules);

    if ($validator->fails()) {
        echo "Validation failed as expected:\n";
        foreach ($validator->errors()->all() as $error) {
            echo "  - $error\n";
        }
    } else {
        echo "Validation passed (unexpected)\n";
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}

echo "\n";

// Test case 2: Missing semester
echo "2. Testing with missing semester:\n";
$request2 = Request::create('/api/v1/admin/import/courses', 'POST', []);

// Simulate file upload
$uploadedFile = new \Illuminate\Http\UploadedFile(
    __DIR__ . '/test-file.csv',
    'test-file.csv',
    'text/csv',
    null,
    true
);

$request2->files->set('file', $uploadedFile);

try {
    $rules = $importRequest1->rules();
    $validator = \Illuminate\Support\Facades\Validator::make($request2->all(), $rules, $importRequest1->messages());

    if ($validator->fails()) {
        echo "Validation failed as expected:\n";
        foreach ($validator->errors()->all() as $error) {
            echo "  - $error\n";
        }
    } else {
        echo "Validation passed (unexpected)\n";
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}

echo "\n";

// Test case 3: Invalid semester format
echo "3. Testing with invalid semester format:\n";
$request3 = Request::create('/api/v1/admin/import/courses', 'POST', [
    'semester' => 'Fall@2025!' // Invalid characters
]);
$request3->files->set('file', $uploadedFile);

try {
    $validator = \Illuminate\Support\Facades\Validator::make($request3->all(), $importRequest1->rules(), $importRequest1->messages());

    if ($validator->fails()) {
        echo "Validation failed as expected:\n";
        foreach ($validator->errors()->all() as $error) {
            echo "  - $error\n";
        }
    } else {
        echo "Validation passed (unexpected)\n";
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}

echo "\n=== Import Request Validation Rules ===\n";
print_r($importRequest1->rules());

echo "\n=== Import Request Messages ===\n";
print_r($importRequest1->messages());
