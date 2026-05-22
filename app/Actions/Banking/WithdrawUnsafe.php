<?php

namespace App\Actions\Banking;

use App\Models\BankAccount;

class WithdrawUnsafe
{
    public function handle(BankAccount $account, string $personName): bool
    {
        $startingBalance = BankAccount::query()
            ->whereKey($account->id)
            ->value('balance');

        $amount = $account->withdrawal_amount;

        if ($startingBalance < $amount) {
            return false;
        }

        usleep((int) config('banking.unsafe_delay_ms', 200) * 1000);

        $freshAccount = BankAccount::query()->find($account->id);
        $freshAccount->balance = $freshAccount->balance - $amount;
        $freshAccount->save();

        $freshAccount->withdrawals()->create([
            'person_name' => $personName,
            'amount' => $amount,
        ]);

        return true;
    }
}
