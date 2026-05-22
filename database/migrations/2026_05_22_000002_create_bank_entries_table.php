<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bank_entries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bank_account_id')->constrained()->cascadeOnDelete();
            $table->string('person_name');
            $table->timestamps();

            $table->index(['bank_account_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bank_entries');
    }
};

