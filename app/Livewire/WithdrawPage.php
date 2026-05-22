<?php

namespace App\Livewire;

use App\Actions\Banking\WithdrawSafe;
use App\Actions\Banking\WithdrawUnsafe;
use App\Models\BankAccount;
use Livewire\Component;

class WithdrawPage extends Component
{
    public BankAccount $account;

    public string $name = '';

    public bool $nameLocked = false;

    public ?string $statusMessage = null;

    public bool $success = false;

    public function mount(BankAccount $account): void
    {
        $this->account = $account;
        $sessionKey = $this->sessionKey();
        $storedName = session()->get($sessionKey, '');

        if ($storedName !== '') {
            $this->name = $storedName;
            $this->nameLocked = true;
        }
    }

    protected function rules(): array
    {
        return [
            'name' => ['required', 'string', 'min:2', 'max:60'],
        ];
    }

    public function confirmName(): void
    {
        $this->validate();

        $this->nameLocked = true;
        session()->put($this->sessionKey(), $this->name);

        $alreadyCheckedIn = $this->account
            ->entries()
            ->where('person_name', $this->name)
            ->exists();

        if (! $alreadyCheckedIn) {
            $this->account->entries()->create([
                'person_name' => $this->name,
            ]);
        }
    }

    public function submit(): void
    {
        if (! $this->nameLocked) {
            $this->statusMessage = 'Confirme seu nome antes de sacar.';
            $this->success = false;
            return;
        }

        $this->account->refresh();

        if (! $this->account->withdrawals_enabled) {
            $this->statusMessage = 'Saques bloqueados. Aguarde o cadeado ser aberto.';
            $this->success = false;
            return;
        }

        $this->validate();

        $action = $this->account->is_safe
            ? WithdrawSafe::class
            : WithdrawUnsafe::class;
        $ok = app($action)->handle($this->account, $this->name);

        $this->account->refresh();
        $this->success = $ok;
        $this->statusMessage = $ok
            ? 'Saque realizado com sucesso.'
            : 'Saldo insuficiente para este saque.';
    }

    public function render()
    {
        $this->account->refresh();

        return view('livewire.withdraw-page', [
            'account' => $this->account,
        ])->layout('layouts.auth', [
            'title' => $this->account->name,
        ]);
    }

    private function sessionKey(): string
    {
        return 'withdraw_name.account_'.$this->account->id;
    }
}
