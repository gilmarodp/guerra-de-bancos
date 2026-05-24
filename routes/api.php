<?php

use App\Http\Controllers\Api\AccessCredentialsController;
use Illuminate\Support\Facades\Route;

Route::post('/access-credentials', AccessCredentialsController::class)
    ->middleware(['access.key']);

