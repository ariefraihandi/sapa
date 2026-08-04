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
        Schema::create('users', function (Blueprint $table) {
            $table->uuid('id')->primary();

            // Relasi ke Satker & Role
            $table->uuid('satker_id');
            $table->foreign('satker_id')->references('id')->on('satkers')->onDelete('cascade');

            $table->uuid('role_id');
            $table->foreign('role_id')->references('id')->on('roles')->onDelete('cascade');

            // Data Utama Pegawai
            $table->string('nip')->unique()->nullable(); // Bikin nullable jika ada user non-NIP (misal dev/vendor)
            $table->string('name');
            $table->string('jabatan')->nullable();
            $table->string('email')->unique();
            $table->string('phone')->nullable();
            
            // Otentikasi & Keamanan
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->boolean('is_active')->default(true);
            $table->string('avatar')->nullable();

            $table->rememberToken();
            $table->timestamps();
        });

        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            // Diganti ke foreignUuid karena primary key users menggunakan UUID
            $table->foreignUuid('user_id')->nullable()->index()->constrained('users')->onDelete('cascade');
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('sessions');
    }
};