<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // First add campus and school fields to users table
        Schema::table('users', function (Blueprint $table) {
            $table->string('campus')->nullable()->after('is_admin');
            $table->string('school')->nullable()->after('campus');
            $table->text('profile')->nullable()->after('school');
        });

        // Migrate data from admin_users to users table
        if (Schema::hasTable('admin_users')) {
            $adminUsers = DB::table('admin_users')->get();
            
            foreach ($adminUsers as $adminUser) {
                // Check if user with this email already exists
                $existingUser = DB::table('users')->where('email', $adminUser->email)->first();
                
                if (!$existingUser) {
                    // Create new user with admin privileges
                    DB::table('users')->insert([
                        'name' => $adminUser->name,
                        'email' => $adminUser->email,
                        'password' => $adminUser->password,
                        'email_verified_at' => now(),
                        'is_verified' => true,
                        'is_admin' => true,
                        'campus' => $adminUser->campus,
                        'school' => $adminUser->school,
                        'profile' => $adminUser->profile,
                        'created_at' => $adminUser->created_at ?? now(),
                        'updated_at' => $adminUser->updated_at ?? now(),
                    ]);
                } else {
                    // Update existing user to be admin
                    DB::table('users')
                        ->where('email', $adminUser->email)
                        ->update([
                            'is_admin' => true,
                            'campus' => $adminUser->campus,
                            'school' => $adminUser->school,
                            'profile' => $adminUser->profile,
                            'updated_at' => now(),
                        ]);
                }
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remove admin users (those with is_admin = true) from users table
        DB::table('users')->where('is_admin', true)->delete();
        
        // Remove the added columns
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['campus', 'school', 'profile']);
        });
    }
};
