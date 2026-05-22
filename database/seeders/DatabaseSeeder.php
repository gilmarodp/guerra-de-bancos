<?php

namespace Database\Seeders;

use App\Models\BankAccount;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        BankAccount::query()->updateOrCreate(
            ['slug' => BankAccount::SLUG_UNSAFE],
            [
                'name' => 'Saldo inseguro',
                'is_safe' => false,
                'show_qr' => true,
                'initial_balance' => 10000,
                'balance' => 10000,
                'withdrawal_amount' => 1000,
            ]
        );

        BankAccount::query()->updateOrCreate(
            ['slug' => BankAccount::SLUG_SAFE],
            [
                'name' => 'Saldo seguro',
                'is_safe' => true,
                'show_qr' => true,
                'initial_balance' => 10000,
                'balance' => 10000,
                'withdrawal_amount' => 1000,
            ]
        );
    }
}
