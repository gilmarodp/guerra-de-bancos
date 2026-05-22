<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bank_accounts', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique();
            $table->string('name');
            $table->boolean('is_safe')->default(false);
            $table->boolean('show_qr')->default(true);
            $table->unsignedInteger('initial_balance');
            $table->unsignedInteger('balance');
            $table->unsignedInteger('withdrawal_amount');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bank_accounts');
    }
};
