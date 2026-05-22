<?php

namespace App\Actions\Banking;

use App\Models\BankAccount;
use Illuminate\Support\Facades\DB;

class WithdrawSafe
{
    /**
     * @throws \Throwable
     */
    public function handle(BankAccount $account, string $personName): bool
    {
        $amount = $account->withdrawal_amount;
        $succeeded = false;

        DB::transaction(function () use ($account, $amount, $personName, &$succeeded) {
            $lockedAccount = BankAccount::query()
                ->whereKey($account->id)
                ->lockForUpdate()
                ->first();

            if ($lockedAccount->balance < $amount) {
                return;
            }

            usleep((int) config('banking.unsafe_delay_ms', 200) * 1000);

            $lockedAccount->balance = $lockedAccount->balance - $amount;
            $lockedAccount->save();

            $lockedAccount->withdrawals()->create([
                'person_name' => $personName,
                'amount' => $amount,
            ]);

            $succeeded = true;
        }, 3);

        return $succeeded;
    }
}
