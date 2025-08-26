<?php

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "=== Testing XLS File Parsing ===\n";

// Let's test with a simple approach - create a test Excel file to see what happens
use Shuchkin\SimpleXLSX;

// First, let's see what SimpleXLSX can tell us about supported formats
echo "SimpleXLSX version and capabilities:\n";
if (class_exists('Shuchkin\SimpleXLSX')) {
    echo "SimpleXLSX class exists\n";

    // Test with a non-existent file to see error handling
    $result = SimpleXLSX::parse('non-existent.xlsx');
    if (!$result) {
        echo "Error for non-existent file: " . SimpleXLSX::parseError() . "\n";
    }

    // Let's create a simple CSV and see if we can convert it to test our logic
    $csvData = "Code,Course,Instructor,Section,Credits,Room,Schedule,Days,Time,School\n";
    $csvData .= "CS101,Test Course,Dr. Test,A,3,Room 101,MWF,Monday Wednesday Friday,9:00-10:00,Engineering\n";

    file_put_contents('test-parse.csv', $csvData);
    echo "\nCreated test CSV file\n";

    // Test our CSV parsing
    $controller = new \App\Http\Controllers\ImportController();
    $reflection = new \ReflectionClass($controller);
    $method = $reflection->getMethod('parseCsv');
    $method->setAccessible(true);

    try {
        $result = $method->invoke($controller, 'test-parse.csv');
        echo "CSV parsing successful. Rows: " . count($result) . "\n";
        echo "First row: " . print_r($result[0], true) . "\n";
    } catch (\Exception $e) {
        echo "CSV parsing failed: " . $e->getMessage() . "\n";
    }

    // Clean up
    unlink('test-parse.csv');
} else {
    echo "SimpleXLSX class not found\n";
}

echo "\n=== Checking MIME type detection ===\n";

// Test MIME type detection with a fake XLS file
$fakeXlsContent = file_get_contents(__FILE__); // Just use this PHP file as fake content
file_put_contents('fake.xls', $fakeXlsContent);

if (function_exists('mime_content_type')) {
    echo "MIME type of fake.xls: " . mime_content_type('fake.xls') . "\n";
} else {
    echo "mime_content_type function not available\n";
}

// Try to detect based on file extension
$finfo = finfo_open(FILEINFO_MIME_TYPE);
if ($finfo) {
    echo "finfo MIME type of fake.xls: " . finfo_file($finfo, 'fake.xls') . "\n";
    finfo_close($finfo);
}

unlink('fake.xls');
