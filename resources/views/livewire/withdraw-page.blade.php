<div class="mx-auto w-full max-w-lg space-y-6">
    <div class="rounded-xl border border-neutral-200 bg-white p-6 shadow-sm dark:border-neutral-800 dark:bg-neutral-900">
        <div class="space-y-2">
            <h1 class="text-2xl font-semibold text-neutral-900 dark:text-neutral-100">{{ $account->name }}</h1>
            <p class="text-sm text-neutral-500 dark:text-neutral-400">
                {{ $account->is_safe ? 'Fluxo seguro com protecao contra concorrencia.' : 'Fluxo inseguro, vulneravel a race conditions.' }}
            </p>
        </div>

        <div class="mt-6 space-y-4">
            <label class="block">
                <span class="text-sm text-neutral-600 dark:text-neutral-300">Seu nome</span>
                <input
                    class="mt-1 w-full rounded-md border border-neutral-300 bg-white px-3 py-2 text-sm text-neutral-900 shadow-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500 dark:border-neutral-700 dark:bg-neutral-950 dark:text-neutral-100"
                    type="text"
                    wire:model.defer="name"
                    placeholder="Ex: Ana"
                    @if ($nameLocked) disabled @endif
                />
            </label>

            <button
                class="w-full rounded-md border border-neutral-300 px-4 py-2 text-sm font-semibold text-neutral-700 hover:border-neutral-400 dark:border-neutral-700 dark:text-neutral-200"
                type="button"
                wire:click="confirmName"
                @if ($nameLocked) disabled @endif
            >
                {{ $nameLocked ? 'Nome confirmado' : 'Confirmar nome' }}
            </button>
        </div>

        <form class="mt-4 space-y-4" wire:submit="submit">
            <button
                class="w-full rounded-md bg-blue-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-blue-500 disabled:cursor-not-allowed disabled:opacity-60"
                type="submit"
                @if (! $nameLocked) disabled @endif
            >
                Sacar R$ {{ number_format($account->withdrawal_amount / 100, 2, ',', '.') }}
            </button>
        </form>

        @if ($statusMessage)
            <div class="mt-4 rounded-md border px-3 py-2 text-sm {{ $success ? 'border-emerald-300 bg-emerald-50 text-emerald-700 dark:border-emerald-700 dark:bg-emerald-950 dark:text-emerald-200' : 'border-red-300 bg-red-50 text-red-700 dark:border-red-700 dark:bg-red-950 dark:text-red-200' }}">
                {{ $statusMessage }}
            </div>
        @endif

        <p class="mt-4 text-xs text-neutral-500 dark:text-neutral-400">
            Dica: abra varias abas e tente sacar ao mesmo tempo para ver a diferenca.
        </p>
    </div>
</div>
