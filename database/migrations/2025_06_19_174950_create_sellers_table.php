<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sellers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('mobile_no')->unique();
            $table->string('country');
            $table->string('state');
            $table->json('skills');
            $table->timestamps();
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('sellers');
    }
};
