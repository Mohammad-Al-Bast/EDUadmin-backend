#!/usr/bin/env php
<?php

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;

echo "=== EDU Admin Backend API Testing ===\n\n";

// Test 1: Check current users
echo "1. Current Users in Database:\n";
$users = User::all(['id', 'name', 'email', 'is_admin', 'is_verified']);
foreach ($users as $user) {
    $admin = $user->is_admin ? 'Admin' : 'User';
    $verified = $user->is_verified ? 'Verified' : 'Unverified';
    echo "   ID: {$user->id} | {$user->name} ({$user->email}) | {$admin} | {$verified}\n";
}

// Test 2: Create test users
echo "\n2. Creating Test Users:\n";

// Create or update admin user
$admin = User::updateOrCreate(
    ['email' => 'admin@example.com'],
    [
        'name' => 'Admin User',
        'password' => bcrypt('password123'),
        'is_admin' => true,
        'is_verified' => true
    ]
);
echo "   ✓ Admin User: {$admin->email} (ID: {$admin->id})\n";

// Create or update regular verified user
$regular = User::updateOrCreate(
    ['email' => 'regular@example.com'],
    [
        'name' => 'Regular User',
        'password' => bcrypt('password123'),
        'is_admin' => false,
        'is_verified' => true
    ]
);
echo "   ✓ Regular Verified User: {$regular->email} (ID: {$regular->id})\n";

// Create or update unverified user
$unverified = User::updateOrCreate(
    ['email' => 'unverified@example.com'],
    [
        'name' => 'Unverified User',
        'password' => bcrypt('password123'),
        'is_admin' => false,
        'is_verified' => false
    ]
);
echo "   ✓ Unverified User: {$unverified->email} (ID: {$unverified->id})\n";

echo "\n3. Test Users Created Successfully!\n";
echo "   Now you can test the following scenarios:\n";
echo "   - Unverified User: unverified@example.com / password123 (should fail login)\n";
echo "   - Regular User: regular@example.com / password123 (can login, limited access)\n";
echo "   - Admin User: admin@example.com / password123 (can login, full access)\n";

echo "\n=== Test Users Setup Complete ===\n";
