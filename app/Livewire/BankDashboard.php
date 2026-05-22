<?php

namespace App\Livewire;

use App\Models\BankAccount;
use App\Models\Withdrawal;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class BankDashboard extends Component
{
    public array $settings = [];

    public int $pollSeconds = 2;

    public function mount(): void
    {
        $this->pollSeconds = (int) config('banking.poll_seconds', 2);
        $this->primeSettings();
    }

    public function render()
    {
        $accounts = BankAccount::query()
            ->withCount(['withdrawals', 'entries'])
            ->withSum('withdrawals', 'amount')
            ->with([
                'withdrawals' => fn ($query) => $query->latest()->limit(8),
                'entries' => fn ($query) => $query->latest()->limit(8),
            ])
            ->orderBy('id')
            ->get();

        $this->ensureSettings($accounts);

        $totalsByAccount = Withdrawal::query()
            ->select('bank_account_id', 'person_name', DB::raw('sum(amount) as total_amount'), DB::raw('count(*) as total_count'))
            ->groupBy('bank_account_id', 'person_name')
            ->orderByDesc('total_amount')
            ->get()
            ->groupBy('bank_account_id');

        return view('livewire.bank-dashboard', [
            'accounts' => $accounts,
            'totalsByAccount' => $totalsByAccount,
        ])->layout('layouts.app', [
            'title' => __('Dashboard'),
        ]);
    }

    public function saveSettings(int $accountId): void
    {
        $account = BankAccount::query()->findOrFail($accountId);
        $data = $this->settings[$accountId] ?? [];

        $initialBalance = $this->parseMoneyToCents($data['initial_balance'] ?? '0');
        $withdrawalAmount = $this->parseMoneyToCents($data['withdrawal_amount'] ?? '0');
        $showQr = (bool) ($data['show_qr'] ?? false);
        $withdrawalsEnabled = (bool) ($data['withdrawals_enabled'] ?? false);

        $account->update([
            'initial_balance' => $initialBalance,
            'withdrawal_amount' => $withdrawalAmount,
            'show_qr' => $showQr,
            'withdrawals_enabled' => $withdrawalsEnabled,
        ]);

        $this->settings[$accountId] = $this->settingsFromAccount($account->fresh());
    }

    public function resetBalance(int $accountId): void
    {
        $account = BankAccount::query()->findOrFail($accountId);
        $account->balance = $account->initial_balance;
        $account->save();
    }

    public function clearHistory(int $accountId): void
    {
        $account = BankAccount::query()->findOrFail($accountId);
        $account->withdrawals()->delete();
    }

    public function formatMoney(int $cents): string
    {
        return number_format($cents / 100, 2, ',', '.');
    }

    private function parseMoneyToCents(string $value): int
    {
        $hasComma = str_contains($value, ',');
        $normalized = $hasComma ? str_replace('.', '', $value) : $value;
        $normalized = str_replace(',', '.', $normalized);
        $normalized = preg_replace('/[^0-9.]/', '', $normalized) ?? '0';

        return max(0, (int) round(((float) $normalized) * 100));
    }

    private function primeSettings(): void
    {
        $accounts = BankAccount::query()->orderBy('id')->get();
        $this->ensureSettings($accounts, true);
    }

    private function ensureSettings(Collection $accounts, bool $force = false): void
    {
        foreach ($accounts as $account) {
            if ($force || ! array_key_exists($account->id, $this->settings)) {
                $this->settings[$account->id] = $this->settingsFromAccount($account);
            }
        }
    }

    private function settingsFromAccount(BankAccount $account): array
    {
        return [
            'initial_balance' => $this->formatMoney($account->initial_balance),
            'withdrawal_amount' => $this->formatMoney($account->withdrawal_amount),
            'show_qr' => $account->show_qr,
            'withdrawals_enabled' => $account->withdrawals_enabled,
        ];
    }
}
