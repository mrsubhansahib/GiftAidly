<?php

use App\Http\Controllers\RoutingController;
use App\Http\Controllers\SubscriptionController;
use Illuminate\Support\Facades\Route;

require __DIR__ . '/auth.php';


Route::group(['prefix' => '/', 'middleware' => ['auth', 'verified']], function () {

    Route::post('donate/daily-weekly-monthly',[SubscriptionController::class,'donateDailyWeeklyMonthly'])->name('donation.daily_weekly_monthly');
    Route::post('donate/friday',[SubscriptionController::class,'donateFriday'])->name('donation.friday');
    Route::post('donate/special',[SubscriptionController::class,'donateSpecial'])->name('donation.special');
    // User detail only for admin
    Route::get('/admin/donor/{id}', function ($id) {
        return view('admin.donors.detail', ['id' => $id]);
    })->name('admin.donor.detail');

    // Admin Subscription Detail (can view any user's subscription)
    Route::get('/admin/donation/{id}', function ($id) {
        return view('admin.donations.detail', ['id' => $id]);
    })->name('admin.donations.detail');

    // User subscription detail
    Route::get('/user/donation/{id}', function ($id) {
        return view('user.donations.detail', ['id' => $id]);
    })->name('user.donations.detail');

    Route::get('', [RoutingController::class, 'index'])->name('root');
    Route::get('{first}/{second}/{third}', [RoutingController::class, 'thirdLevel'])->name('third');
    Route::get('{first}/{second}', [RoutingController::class, 'secondLevel'])->name('second');
    Route::get('{any}', [RoutingController::class, 'root'])->name('any');
});
