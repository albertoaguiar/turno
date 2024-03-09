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
            $table->id();
            $table->string('password')->nullable(false);
            $table->string('name')->nullable(false);
            $table->string('email')->unique()->nullable(false);
            $table->string('account_number')->unique()->nullable(false);
            $table->decimal('balance', 10, 2)->default(0)->nullable(false);
            $table->enum('user_type', ['A', 'C'])->nullable(false); //A = Adm; C = Customer 
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
