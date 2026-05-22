<div class="space-y-6" wire:poll.{{ $pollSeconds }}s>
    <div class="flex flex-wrap items-center justify-between gap-3">
        <div>
            <h1 class="text-2xl font-semibold text-neutral-900 dark:text-neutral-100">Concorrencia em saques</h1>
            <p class="text-sm text-neutral-500 dark:text-neutral-400">Comparacao entre fluxo inseguro e seguro.</p>
        </div>
        <div class="text-xs text-neutral-500 dark:text-neutral-400">
            Atualizacao automatica a cada {{ $pollSeconds }}s
        </div>
    </div>

    <div class="grid gap-6 lg:grid-cols-1">
        @foreach ($accounts as $account)
            <div class="rounded-xl border border-neutral-200 bg-white p-4 shadow-sm dark:border-neutral-800 dark:bg-neutral-900">
                <div class="flex flex-wrap items-center justify-between gap-2">
                    <div>
                        <h2 class="text-lg font-semibold text-neutral-900 dark:text-neutral-100">{{ $account->name }}</h2>
                        <span class="text-xs uppercase tracking-wide text-neutral-500 dark:text-neutral-400">
                            {{ $account->is_safe ? 'Seguro' : 'Inseguro' }}
                        </span>
                    </div>
                    <div class="text-right">
                        <div class="text-xs text-neutral-500 dark:text-neutral-400">Saldo atual</div>
                        <div class="text-xl font-semibold text-neutral-900 dark:text-neutral-100">
                            R$ {{ $this->formatMoney($account->balance) }}
                        </div>
                    </div>
                </div>

                <div class="mt-4 grid gap-3 md:grid-cols-2">
                    <div class="rounded-lg border border-neutral-200 p-3 dark:border-neutral-800">
                        <div class="text-xs text-neutral-500 dark:text-neutral-400">Saques realizados</div>
                        <div class="text-2xl font-semibold text-neutral-900 dark:text-neutral-100">{{ $account->withdrawals_count }}</div>
                        <div class="text-xs text-neutral-500 dark:text-neutral-400">Saldo inicial: R$ {{ $this->formatMoney($account->initial_balance) }}</div>
                    </div>
                    <div class="rounded-lg border border-neutral-200 p-3 dark:border-neutral-800">
                        <div class="text-xs text-neutral-500 dark:text-neutral-400">Valor do saque</div>
                        <div class="text-2xl font-semibold text-neutral-900 dark:text-neutral-100">R$ {{ $this->formatMoney($account->withdrawal_amount) }}</div>
                        <div class="text-xs text-neutral-500 dark:text-neutral-400">QR code {{ ($settings[$account->id]['show_qr'] ?? $account->show_qr) ? 'visivel' : 'oculto' }}</div>
                    </div>
                    <div class="rounded-lg border border-neutral-200 p-3 dark:border-neutral-800">
                        <div class="text-xs text-neutral-500 dark:text-neutral-400">Total sacado</div>
                        <div class="text-2xl font-semibold text-neutral-900 dark:text-neutral-100">R$ {{ $this->formatMoney((int) ($account->withdrawals_sum_amount ?? 0)) }}</div>
                        <div class="text-xs text-neutral-500 dark:text-neutral-400">Saldo final: R$ {{ $this->formatMoney($account->balance) }}</div>
                    </div>
                    <div class="rounded-lg border border-neutral-200 p-3 dark:border-neutral-800">
                        <div class="text-xs text-neutral-500 dark:text-neutral-400">Pessoas que entraram</div>
                        <div class="text-2xl font-semibold text-neutral-900 dark:text-neutral-100">{{ $account->entries_count }}</div>
                        <div class="text-xs text-neutral-500 dark:text-neutral-400">Ultimas confirmacoes abaixo</div>
                    </div>
                </div>

                <div class="mt-4 grid gap-4 lg:grid-cols-2">
                    <div class="rounded-lg border border-neutral-200 p-3 dark:border-neutral-800">
                        <h3 class="text-sm font-semibold text-neutral-900 dark:text-neutral-100">QR Code</h3>
                        @php($publicUrl = route('withdraw.show', $account->slug))
                        @if ($settings[$account->id]['show_qr'] ?? $account->show_qr)
                            <div class="mt-3 flex flex-wrap items-center gap-4">
                                <div class="rounded-lg bg-white p-2 shadow-sm">
                                    {!! \App\Helpers::qr_svg($publicUrl) !!}
                                </div>
                                <div class="text-xs text-neutral-600 dark:text-neutral-300">
                                    <div class="font-semibold text-neutral-900 dark:text-neutral-100">Link publico</div>
                                    <a class="break-all text-blue-600 hover:underline dark:text-blue-400" href="{{ $publicUrl }}" target="_blank" rel="noopener">{{ $publicUrl }}</a>
                                </div>
                            </div>
                        @else
                            <p class="mt-2 text-xs text-neutral-500 dark:text-neutral-400">QR code oculto no painel administrativo.</p>
                        @endif
                    </div>

                    <div class="rounded-lg border border-neutral-200 p-3 dark:border-neutral-800">
                        <h3 class="text-sm font-semibold text-neutral-900 dark:text-neutral-100">Configuracoes</h3>
                        <div class="mt-3 space-y-3 text-sm text-neutral-700 dark:text-neutral-200">
                            <label class="block">
                                <span class="text-xs text-neutral-500 dark:text-neutral-400">Saldo inicial (R$)</span>
                                <input
                                    class="mt-1 w-full rounded-md border border-neutral-300 bg-white px-3 py-2 text-sm text-neutral-900 shadow-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500 dark:border-neutral-700 dark:bg-neutral-950 dark:text-neutral-100"
                                    type="text"
                                    wire:model.defer="settings.{{ $account->id }}.initial_balance"
                                />
                            </label>
                            <label class="block">
                                <span class="text-xs text-neutral-500 dark:text-neutral-400">Valor do saque (R$)</span>
                                <input
                                    class="mt-1 w-full rounded-md border border-neutral-300 bg-white px-3 py-2 text-sm text-neutral-900 shadow-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500 dark:border-neutral-700 dark:bg-neutral-950 dark:text-neutral-100"
                                    type="text"
                                    wire:model.defer="settings.{{ $account->id }}.withdrawal_amount"
                                />
                            </label>
                            <label class="inline-flex items-center gap-2 text-xs text-neutral-600 dark:text-neutral-300">
                                <input
                                    type="checkbox"
                                    class="rounded border-neutral-300 text-blue-600 focus:ring-blue-500 dark:border-neutral-700"
                                    wire:model="settings.{{ $account->id }}.show_qr"
                                />
                                Mostrar QR code no painel
                            </label>
                        </div>
                        <div class="mt-3 flex flex-wrap gap-2">
                            <button
                                class="rounded-md bg-blue-600 px-3 py-2 text-xs font-semibold text-white shadow-sm hover:bg-blue-500"
                                type="button"
                                wire:click="saveSettings({{ $account->id }})"
                            >
                                Salvar
                            </button>
                            <button
                                class="rounded-md border border-neutral-300 px-3 py-2 text-xs font-semibold text-neutral-700 hover:border-neutral-400 dark:border-neutral-700 dark:text-neutral-200"
                                type="button"
                                wire:click="resetBalance({{ $account->id }})"
                            >
                                Resetar saldo
                            </button>
                            <button
                                class="rounded-md border border-red-300 px-3 py-2 text-xs font-semibold text-red-600 hover:border-red-400 dark:border-red-700 dark:text-red-400"
                                type="button"
                                wire:click="clearHistory({{ $account->id }})"
                            >
                                Limpar historico
                            </button>
                        </div>
                    </div>
                </div>

                <div class="mt-4 rounded-lg border border-neutral-200 p-3 dark:border-neutral-800">
                    <h3 class="text-sm font-semibold text-neutral-900 dark:text-neutral-100">Pessoas que confirmaram nome</h3>
                    <div class="mt-2 space-y-2 text-sm">
                        @forelse ($account->entries as $entry)
                            <div class="flex items-center justify-between gap-2 rounded-md bg-neutral-50 px-3 py-2 text-xs text-neutral-700 dark:bg-neutral-950 dark:text-neutral-200">
                                <span class="font-semibold">{{ $entry->person_name }}</span>
                                <span class="text-neutral-500 dark:text-neutral-400">{{ $entry->created_at->format('H:i:s') }}</span>
                            </div>
                        @empty
                            <p class="text-xs text-neutral-500 dark:text-neutral-400">Nenhuma pessoa confirmou nome ainda.</p>
                        @endforelse
                    </div>
                </div>

                <div class="mt-4 rounded-lg border border-neutral-200 p-3 dark:border-neutral-800">
                    <h3 class="text-sm font-semibold text-neutral-900 dark:text-neutral-100">Totais por usuario</h3>
                    <div class="mt-2 space-y-2 text-sm">
                        @php($totals = $totalsByAccount[$account->id] ?? collect())
                        @forelse ($totals as $total)
                            <div class="flex items-center justify-between gap-2 rounded-md bg-neutral-50 px-3 py-2 text-xs text-neutral-700 dark:bg-neutral-950 dark:text-neutral-200">
                                <span class="font-semibold">{{ $total->person_name }}</span>
                                <span class="text-neutral-500 dark:text-neutral-400">{{ $total->total_count }} saques</span>
                                <span class="font-semibold text-neutral-900 dark:text-neutral-100">R$ {{ $this->formatMoney((int) $total->total_amount) }}</span>
                            </div>
                        @empty
                            <p class="text-xs text-neutral-500 dark:text-neutral-400">Nenhum saque registrado ainda.</p>
                        @endforelse
                    </div>
                </div>

                <div class="mt-4 rounded-lg border border-neutral-200 p-3 dark:border-neutral-800">
                    <h3 class="text-sm font-semibold text-neutral-900 dark:text-neutral-100">Ultimos saques</h3>
                    <div class="mt-2 space-y-2 text-sm">
                        @forelse ($account->withdrawals as $withdrawal)
                            <div class="flex items-center justify-between gap-2 rounded-md bg-neutral-50 px-3 py-2 text-xs text-neutral-700 dark:bg-neutral-950 dark:text-neutral-200">
                                <span class="font-semibold">{{ $withdrawal->person_name }}</span>
                                <span class="text-neutral-500 dark:text-neutral-400">{{ $withdrawal->created_at->format('H:i:s') }}</span>
                                <span class="font-semibold text-neutral-900 dark:text-neutral-100">R$ {{ $this->formatMoney($withdrawal->amount) }}</span>
                            </div>
                        @empty
                            <p class="text-xs text-neutral-500 dark:text-neutral-400">Nenhum saque registrado ainda.</p>
                        @endforelse
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</div>
