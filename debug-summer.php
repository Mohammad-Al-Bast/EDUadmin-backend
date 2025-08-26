<?php

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "=== Testing Excel File Import with 'summer' semester ===\n";

// Create test data
$testData = "Code,Course,Instructor,Section,Credits,Room,Schedule,Days,Time,School\n";
$testData .= "CS101,Summer Programming,Dr. Smith,A,3,Room 101,MWF,Monday Wednesday Friday,9:00-10:00,Engineering\n";
$testData .= "CS102,Summer Algorithms,Dr. Jones,B,4,Room 102,TTh,Tuesday Thursday,10:00-11:30,Engineering\n";

file_put_contents('test-summer.csv', $testData);

// Create uploaded file
$uploadedFile = new \Illuminate\Http\UploadedFile(
    __DIR__ . '/test-summer.csv',
    'test-summer.csv',
    'text/csv',
    null,
    true
);

echo "File: " . $uploadedFile->getClientOriginalName() . "\n";
echo "MIME: " . $uploadedFile->getMimeType() . "\n";
echo "Extension: " . $uploadedFile->getClientOriginalExtension() . "\n";

// Test validation
$request = new \App\Http\Requests\ImportCoursesRequest();
$request->merge(['semester' => 'summer']);  // Lowercase like in error
$request->files->set('file', $uploadedFile);

$validator = \Illuminate\Support\Facades\Validator::make([
    'file' => $uploadedFile,
    'semester' => 'summer'
], $request->rules(), $request->messages());

if ($validator->fails()) {
    echo "\nValidation FAILED:\n";
    foreach ($validator->errors()->all() as $error) {
        echo "  - $error\n";
    }
} else {
    echo "\nValidation PASSED!\n";

    // Test the actual import
    try {
        $controller = new \App\Http\Controllers\ImportController();
        $response = $controller->importCourses($request);
        echo "Import Response: " . $response->getContent() . "\n";
    } catch (\Exception $e) {
        echo "Import Error: " . $e->getMessage() . "\n";
        echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
    }
}

// Clean up
unlink('test-summer.csv');
