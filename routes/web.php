<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\GroupController;
use App\Http\Controllers\LanguageController;
use App\Http\Controllers\MailProcessingController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\WithdrawalRequestController;
use Illuminate\Support\Facades\Route;






Route::get('/', function () {
    return view('welcome');
});

// Add this route for cron job mail processing
Route::any('/process-mails', [MailProcessingController::class, 'processPendingMails'])
    ->name('mails.process');

Route::get('/language/{locale}', [App\Http\Controllers\LanguageController::class, 'changeLanguage'])
    ->name('language.change')
    ->where('locale', 'en|yoruba|igbo|hausa');

Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');


Route::view('wheel', 'wheel');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::patch('/profile/bank-details', [ProfileController::class, 'updateBankDetails'])->name('profile.bank-details.update');
    Route::patch('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password.update');
    Route::delete('/profile/image', [ProfileController::class, 'removeProfileImage'])->name('profile.image.remove');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Group routes
    Route::get('/groups', [GroupController::class, 'index'])->name('groups.index');
    Route::get('/groups/create', [GroupController::class, 'create'])->name('groups.create');
    Route::post('/groups', [GroupController::class, 'store'])->name('groups.store');
    Route::get('/groups/browse', [GroupController::class, 'browse'])->name('groups.browse');

    Route::post('/groups/{group}/join', [GroupController::class, 'join'])->name('groups.join');
    Route::delete('/groups/{group}/leave', [GroupController::class, 'leave'])->name('groups.leave');


    Route::post('/groups/{group}/start-contribution', [GroupController::class, 'startContribution'])->name('groups.start-contribution');
    Route::post('/groups/{group}/update-member-position', [GroupController::class, 'updateMemberPosition'])->name('groups.update-member-position');
    Route::post('/groups/{group}/spin-wheel', [GroupController::class, 'spinWheel'])->name('groups.spin-wheel');
    Route::get('/groups/{group}/available-positions', [GroupController::class, 'getAvailablePositions'])->name('groups.available-positions');

    Route::get('/groups/{group}/settings', [GroupController::class, 'settings'])->name('groups.settings');
    Route::patch('/groups/{group}', [GroupController::class, 'update'])->name('groups.update');
    Route::post('/groups/{group}/approve-member', [GroupController::class, 'approveMember'])->name('groups.approve-member');
    Route::post('/groups/{group}/reject-member', [GroupController::class, 'rejectMember'])->name('groups.reject-member');

    Route::get('/groups/{group}/withdrawal-request', [WithdrawalRequestController::class, 'create'])
        ->name('withdrawal-requests.create');
    Route::post('/groups/{group}/withdrawal-request', [WithdrawalRequestController::class, 'store'])
        ->name('withdrawal-requests.store');


    // Payment routes
    Route::get('/contributions/{contribution}/payment', [PaymentController::class, 'showPaymentOptions'])->name('payments.options');
    Route::get('/groups/{group}/payment/cycle/{cycle}', [PaymentController::class, 'showPaymentOptionsForCycle'])->name('payments.options-cycle');
    Route::post('/contributions/{contribution}/payment/initialize-card', [PaymentController::class, 'initializeCardPayment'])->name('payments.initialize-card');
    Route::post('/contributions/{contribution}/payment/process-card', [PaymentController::class, 'processCardPayment'])->name('payments.process-card');
    Route::any('/contributions/{contribution}/payment/virtual-account', [PaymentController::class, 'generateVirtualAccount'])->name('payments.virtual-account');
    Route::any('/contributions/payment/virtual-account', [PaymentController::class, 'generateVirtualAccount'])->name('payments.virtual-account2');
    Route::get('/contributions/{contribution}/payment/status', [PaymentController::class, 'checkPaymentStatus'])->name('payments.check-status');

    // ALATPay routes
    Route::post('/alat/card/initialize', [PaymentController::class, 'initializeCardPayment'])->name('alat.card.initialize');
    Route::post('/alat/bank/generate', [PaymentController::class, 'generateVirtualAccount'])->name('alat.bank.generate');
    Route::get('/alat/card/form/{group}', [PaymentController::class, 'showCardForm'])->name('alat.card.form');
});

// ALATPay webhook (no auth required)
Route::post('/alatpay/callback', [PaymentController::class, 'handleCallback'])->name('payments.callback');

Route::get('/groups/{group}', [GroupController::class, 'show'])->name('groups.show');
require __DIR__ . '/auth.php';
