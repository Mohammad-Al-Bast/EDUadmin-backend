<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::dropIfExists('admin_users');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Recreate admin_users table for rollback
        Schema::create('admin_users', function (Blueprint $table) {
            $table->id('admin_id');
            $table->string('email');
            $table->string('name');
            $table->string('password');
            $table->text('profile')->nullable();
            $table->string('campus');
            $table->string('school');
            $table->timestamps();
        });
    }
};
