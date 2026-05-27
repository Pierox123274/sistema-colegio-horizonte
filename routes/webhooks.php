<?php

use App\Http\Controllers\IntegrationWebhookController;
use Illuminate\Support\Facades\Route;

Route::prefix('webhooks')->name('webhooks.')->group(function () {
    Route::post('/payments', [IntegrationWebhookController::class, 'payments'])->name('payments');
    Route::post('/mercadopago', [IntegrationWebhookController::class, 'mercadoPago'])->name('mercadopago');
    Route::post('/calendar', [IntegrationWebhookController::class, 'calendar'])->name('calendar');
});
