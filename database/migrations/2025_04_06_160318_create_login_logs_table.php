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
        Schema::create('login_logs', function (Blueprint $table) {
            $table->id();
            // Correct foreign key reference to 'registration_info' instead of 'register_info'
            $table->foreignId('user_id')->nullable()->constrained('registration_info')->onDelete('cascade');
            $table->string('login_method'); // Google, Facebook, Traditional
            $table->boolean('is_successful'); // Indicates if login was successful or failed
            $table->timestamp('login_time')->useCurrent(); // Timestamp of the login attempt
            $table->string('ip_address')->nullable(); // User's IP address for tracking
            $table->text('details')->nullable(); // Extra details (optional)
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('login_logs');
    }
};
