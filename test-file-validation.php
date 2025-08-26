<?php

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "=== Testing File Upload Validation ===\n";

// Create a test uploaded file
$testFilePath = __DIR__ . '/test-file.csv';
$uploadedFile = new \Illuminate\Http\UploadedFile(
    $testFilePath,
    'test-file.csv',
    'text/csv',
    null,
    true // test mode
);

echo "Original file: " . $testFilePath . "\n";
echo "Uploaded file name: " . $uploadedFile->getClientOriginalName() . "\n";
echo "Uploaded file MIME: " . $uploadedFile->getMimeType() . "\n";
echo "Uploaded file extension: " . $uploadedFile->getClientOriginalExtension() . "\n";

// Test validation using the actual request class
$request = new \App\Http\Requests\ImportCoursesRequest();
$request->merge(['semester' => 'Fall 2025']);
$request->files->set('file', $uploadedFile);

$validator = \Illuminate\Support\Facades\Validator::make([
    'file' => $uploadedFile,
    'semester' => 'Fall 2025'
], $request->rules(), $request->messages());

if ($validator->fails()) {
    echo "\nValidation failed:\n";
    foreach ($validator->errors()->all() as $error) {
        echo "  - $error\n";
    }
} else {
    echo "\nValidation passed!\n";

    // Test the actual import process
    $controller = new \App\Http\Controllers\ImportController();
    $request = new \App\Http\Requests\ImportCoursesRequest();

    // Mock the request
    $request->merge(['semester' => 'Fall 2025']);
    $request->files->set('file', $uploadedFile);

    echo "\nTesting import process...\n";
    try {
        $response = $controller->importCourses($request);
        echo "Response: " . $response->getContent() . "\n";
    } catch (\Exception $e) {
        echo "Error: " . $e->getMessage() . "\n";
    }
}
