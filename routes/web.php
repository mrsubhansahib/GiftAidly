<?php

use App\Http\Controllers\NotificationController;
use App\Http\Controllers\RoutingController;
use App\Http\Controllers\SubscriptionController;
use App\Http\Controllers\ZakatController;
use App\Livewire\Zakat;
use Illuminate\Support\Facades\Route;

require __DIR__ . '/auth.php';

require __DIR__ . '/api.php';

// 🟢 Public routes first
Route::get('/', [RoutingController::class, 'index'])->name('root');

// 🔒 Authenticated routes
Route::middleware(['auth'])->group(function () {
    Route::get('admin/dashboard', [RoutingController::class, 'adminDashboard'])->name('admin.dashboard');
    Route::get('admin/donor/{id}', fn($id) => view('admin.donors.detail', ['id' => $id]))->name('admin.donor.detail');
    Route::get('admin/donation/{id}', fn($id) => view('admin.donations.detail', ['id' => $id]))->name('admin.donations.detail');
});

// 📬 routes (no auth)
Route::post('/notifications/{id}/read', [NotificationController::class, 'markAsRead'])->name('notifications.read');
Route::post('/notifications/clear', [NotificationController::class, 'clearAll'])->name('notifications.clear');
Route::get('cancel/donation/{id}', [SubscriptionController::class, 'cancelSubscription'])->name('cancel.donation');


// 🕋 Donation routes (public)
Route::post('donate/daily-weekly-monthly', [SubscriptionController::class, 'donateDailyWeeklyMonthly'])->name('donation.daily_weekly_monthly');
Route::post('donate/friday', [SubscriptionController::class, 'donateFriday'])->name('donation.friday');
Route::post('donate/special', [SubscriptionController::class, 'donateSpecial'])->name('donation.special');

// 💰 Zakat routes
Route::get('/receive-zakat/currency={currency}/amount={amount}', [ZakatController::class, 'index'])->name('zakat.form');
Route::get('/zakat/redirect', [ZakatController::class, 'redirect'])->name('zakat.redirect');

// Reference ID route
Route::get('user/donations/{reference_id}', fn($reference_id) => view('user.donations.index', ['reference_id' => $reference_id]))->name('user.donations');

// 🌍 Dynamic public pages (no auth)
Route::get('{first}/{second}/{third}', [RoutingController::class, 'thirdLevel'])->name('third');
Route::get('{first}/{second}', [RoutingController::class, 'secondLevel'])->name('second');
Route::get('{any}', [RoutingController::class, 'root'])->name('any');


// 🧭 Fallback for true 404s
Route::fallback(function () {
    return response()->view('pages.404', [], 404);
});
