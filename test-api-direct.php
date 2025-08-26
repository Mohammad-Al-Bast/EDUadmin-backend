<?php

// Direct API test to simulate frontend behavior
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\MultipartStream;

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "=== Testing Direct API Call ===\n";

// Get admin user and token
$user = \App\Models\User::where('email', 'admin@test.com')->first();
$token = $user->createToken('api-test')->plainTextToken;

echo "Using token: $token\n";

// Create test file content
$testData = "Code,Course,Instructor,Section,Credits,Room,Schedule,Days,Time,School\n";
$testData .= "CS301,Summer Course,Dr. Test,A,3,Room 301,MWF,Monday Wednesday Friday,9:00-10:00,Engineering\n";

file_put_contents('api-test.csv', $testData);

// Test with Guzzle HTTP client
$client = new Client([
    'base_uri' => 'http://localhost:8000',
    'timeout' => 30.0,
]);

try {
    echo "\nSending POST request to /api/v1/admin/import/courses\n";

    $response = $client->post('/api/v1/admin/import/courses', [
        'headers' => [
            'Authorization' => 'Bearer ' . $token,
            'Accept' => 'application/json',
        ],
        'multipart' => [
            [
                'name' => 'semester',
                'contents' => 'summer'
            ],
            [
                'name' => 'file',
                'contents' => fopen('api-test.csv', 'r'),
                'filename' => 'api-test.csv',
                'headers' => [
                    'Content-Type' => 'text/csv'
                ]
            ]
        ]
    ]);

    echo "Success! Status: " . $response->getStatusCode() . "\n";
    echo "Response: " . $response->getBody() . "\n";
} catch (\GuzzleHttp\Exception\ClientException $e) {
    echo "Client Error: " . $e->getResponse()->getStatusCode() . "\n";
    echo "Response: " . $e->getResponse()->getBody() . "\n";
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}

// Clean up
unlink('api-test.csv');
