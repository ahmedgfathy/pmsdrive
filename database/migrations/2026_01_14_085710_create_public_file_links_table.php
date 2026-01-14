<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('public_file_links', function (Blueprint $table) {
            $table->id();
            $table->foreignId('file_id')->constrained()->onDelete('cascade');
            $table->foreignId('created_by_user_id')->constrained('users')->onDelete('cascade');
            $table->string('token', 64)->unique();
            $table->string('password')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->integer('download_count')->default(0);
            $table->timestamps();
            
            $table->index('token');
            $table->index('expires_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('public_file_links');
    }
};
