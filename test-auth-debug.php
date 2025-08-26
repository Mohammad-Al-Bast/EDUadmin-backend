<?php

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "=== Creating Test Admin and Token ===\n";

// Create/get admin user
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
}

$token = $user->createToken('test-token')->plainTextToken;
echo "Bearer Token: $token\n";

// Test authentication
echo "\n=== Testing Authentication ===\n";
$request = \Illuminate\Http\Request::create('/api/v1/admin/import/courses', 'POST');
$request->headers->set('Authorization', 'Bearer ' . $token);

// Simulate the sanctum middleware
\Laravel\Sanctum\Sanctum::actingAs($user);

echo "User authenticated: " . (auth()->check() ? 'Yes' : 'No') . "\n";
echo "User is admin: " . ($user->isAdmin() ? 'Yes' : 'No') . "\n";
echo "User is verified: " . ($user->isVerified() ? 'Yes' : 'No') . "\n";

// Test all middleware conditions
$adminMiddleware = new \App\Http\Middleware\EnsureUserIsAdmin();
$verifiedMiddleware = new \App\Http\Middleware\EnsureUserIsVerified();
$adminVerifiedMiddleware = new \App\Http\Middleware\EnsureAdminAndVerified();

echo "\n=== Testing Middleware ===\n";

try {
    $adminMiddleware->handle($request, function () {
        return true;
    });
    echo "Admin middleware: PASS\n";
} catch (\Exception $e) {
    echo "Admin middleware: FAIL - " . $e->getMessage() . "\n";
}

try {
    $verifiedMiddleware->handle($request, function () {
        return true;
    });
    echo "Verified middleware: PASS\n";
} catch (\Exception $e) {
    echo "Verified middleware: FAIL - " . $e->getMessage() . "\n";
}

try {
    $adminVerifiedMiddleware->handle($request, function () {
        return true;
    });
    echo "Admin+Verified middleware: PASS\n";
} catch (\Exception $e) {
    echo "Admin+Verified middleware: FAIL - " . $e->getMessage() . "\n";
}
