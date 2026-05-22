<?php

use App\Livewire\BankDashboard;
use App\Livewire\WithdrawPage;
use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome')->name('home');

Route::get('/withdraw/{account:slug}', WithdrawPage::class)->name('withdraw.show');

Route::redirect('register', '/login', 301);

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('dashboard', BankDashboard::class)->name('dashboard');
});

require __DIR__.'/settings.php';
