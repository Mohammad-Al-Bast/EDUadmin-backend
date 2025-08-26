<?php

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "=== Creating Test Admin User ===\n";

$user = \App\Models\User::where('email', 'admin@test.com')->first();
if (!$user) {
    $user = \App\Models\User::create([
        'name' => 'Test Admin',
        'email' => 'admin@test.com',
        'password' => bcrypt('password'),
        'is_admin' => true,
        'is_verified' => true,
        'email_verified_at' => now()
    ]);
    echo "Admin user created\n";
} else {
    // Update existing user to be admin and verified
    $user->update([
        'is_admin' => true,
        'is_verified' => true,
        'email_verified_at' => now()
    ]);
    echo "Admin user updated\n";
}

echo "Email: " . $user->email . ", Admin: " . ($user->is_admin ? 'Yes' : 'No') . ", Verified: " . ($user->is_verified ? 'Yes' : 'No') . "\n";

// Create a token for the user
$token = $user->createToken('test-token')->plainTextToken;
echo "Bearer Token: " . $token . "\n";

echo "\n=== Test API Call Information ===\n";
echo "Endpoint: POST http://localhost:8000/api/v1/admin/import/courses\n";
echo "Headers:\n";
echo "  Authorization: Bearer " . $token . "\n";
echo "  Content-Type: multipart/form-data\n";
echo "Body:\n";
echo "  file: (CSV/Excel file)\n";
echo "  semester: Fall 2025\n";
