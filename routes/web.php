<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Admin\OrderPrintController;
use Filament\Http\Middleware\Authenticate as FilamentAuthenticate;

Route::post('/customer/logout', function (Request $request) {
    Auth::guard('customer')->logout();

    $request->session()->forget('customer_id');
    $request->session()->invalidate();
    $request->session()->regenerateToken();

    return redirect()->back();
});

Route::post('/webhooks/stripe', [\App\Http\Controllers\Api\StripeWebhookController::class, 'handle']);

Route::get('/clear-cache', function () {
    Artisan::call('optimize:clear');
    return response()->json([
        'status' => 'success',
        'message' => 'Cache cleared successfully!'
    ]);
});

// Printable admin invoice
Route::get('/admin/orders/{order}/print', [OrderPrintController::class, 'show'])
    ->name('admin.orders.print')
    ->middleware(FilamentAuthenticate::class);

