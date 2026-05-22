<?php

use App\Actions\Banking\WithdrawSafe;
use App\Actions\Banking\WithdrawUnsafe;
use App\Models\BankAccount;
use Illuminate\Contracts\Console\Kernel;

require __DIR__.'/../vendor/autoload.php';

$app = require __DIR__.'/../bootstrap/app.php';
$app->make(Kernel::class)->bootstrap();

$slug = $argv[1] ?? 'unsafe';
$workers = (int) ($argv[2] ?? 20);
$attemptsPerWorker = (int) ($argv[3] ?? 1);

$account = BankAccount::query()->where('slug', $slug)->firstOrFail();
$actionClass = $account->is_safe ? WithdrawSafe::class : WithdrawUnsafe::class;
$accountId = $account->id;

$pids = [];

for ($worker = 1; $worker <= $workers; $worker++) {
    $pid = pcntl_fork();

    if ($pid === -1) {
        fwrite(STDERR, "Failed to fork worker {$worker}.\n");
        exit(1);
    }

    if ($pid === 0) {
        $action = app($actionClass);
        $localAccount = BankAccount::query()->findOrFail($accountId);

        for ($attempt = 1; $attempt <= $attemptsPerWorker; $attempt++) {
            $action->handle($localAccount, "bot-{$worker}-{$attempt}");
        }

        exit(0);
    }

    $pids[] = $pid;
}

foreach ($pids as $pid) {
    pcntl_waitpid($pid, $status);
}

$account->refresh();

$totalWithdrawals = $account->withdrawals()->count();

fwrite(STDOUT, "Finished.\n");
fwrite(STDOUT, "Account: {$account->name} ({$account->slug})\n");
fwrite(STDOUT, "Balance: {$account->balance} cents\n");
fwrite(STDOUT, "Withdrawals: {$totalWithdrawals}\n");
