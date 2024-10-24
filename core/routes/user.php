<?php

use Illuminate\Support\Facades\Route;

Route::namespace('User\Auth')->name('user.')->middleware('guest')->group(function () {
    Route::controller('LoginController')->group(function () {
        Route::get('/login', 'showLoginForm')->name('login');
        Route::post('/login', 'login');
        Route::get('logout', 'logout')->middleware('auth')->withoutMiddleware('guest')->name('logout');
    });

    Route::controller('RegisterController')->group(function () {
        Route::get('register', 'showRegistrationForm')->name('register');
        Route::post('register', 'register');
        Route::post('check-user', 'checkUser')->name('checkUser')->withoutMiddleware('guest');
    });

    Route::controller('ForgotPasswordController')->prefix('password')->name('password.')->group(function () {
        Route::get('reset', 'showLinkRequestForm')->name('request');
        Route::post('email', 'sendResetCodeEmail')->name('email');
        Route::get('code-verify', 'codeVerify')->name('code.verify');
        Route::post('verify-code', 'verifyCode')->name('verify.code');
    });

    Route::controller('ResetPasswordController')->group(function () {
        Route::post('password/reset', 'reset')->name('password.update');
        Route::get('password/reset/{token}', 'showResetForm')->name('password.reset');
    });

    Route::controller('SocialiteController')->group(function () {
        Route::get('social-login/{provider}', 'socialLogin')->name('social.login');
        Route::get('social-login/callback/{provider}', 'callback')->name('social.login.callback');
    });
});

Route::middleware('auth')->name('user.')->group(function () {

    Route::get('user-data', 'User\UserController@userData')->name('data');
    Route::post('user-data-submit', 'User\UserController@userDataSubmit')->name('data.submit');

    //authorization
    Route::middleware('registration.complete')->namespace('User')->controller('AuthorizationController')->group(function () {
        Route::get('authorization', 'authorizeForm')->name('authorization');
        Route::get('resend-verify/{type}', 'sendVerifyCode')->name('send.verify.code');
        Route::post('verify-email', 'emailVerification')->name('verify.email');
        Route::post('verify-mobile', 'mobileVerification')->name('verify.mobile');
        Route::post('verify-g2fa', 'g2faVerification')->name('2fa.verify');
    });

    Route::middleware(['check.status', 'registration.complete'])->group(function () {

        Route::namespace('User')->group(function () {

            Route::controller('UserController')->group(function () {
                Route::get('dashboard', 'home')->name('home');

                Route::get('sales-data', 'getSalesData')->name('getSalesData');
                Route::get('view-data', 'getPromptViewData')->name('getViewData');

                Route::get('download-attachments/{file_hash}', 'downloadAttachment')->name('download.attachment');

                //2FA
                Route::get('twofactor', 'show2faForm')->name('twofactor');
                Route::post('twofactor/enable', 'create2fa')->name('twofactor.enable');
                Route::post('twofactor/disable', 'disable2fa')->name('twofactor.disable');

                //KYC
                Route::get('kyc-form', 'kycForm')->name('kyc.form');
                Route::get('kyc-data', 'kycData')->name('kyc.data');
                Route::post('kyc-submit', 'kycSubmit')->name('kyc.submit');

                //Report
                Route::any('payment-history', 'depositHistory')->name('deposit.history');
                Route::get('transactions', 'transactions')->name('transactions');
                Route::get('purchase-history', 'purchaseHistory')->name('purchase.history');
                Route::get('purchase/{orderNo}', 'purchaseDetails')->name('purchase.details');
                Route::get('sale-history', 'saleHistory')->name('sale.history');
                Route::post('add-device-token', 'addDeviceToken')->name('add.device.token');
            });

            //Profile setting
            Route::controller('ProfileController')->group(function () {
                Route::get('profile-setting', 'profile')->name('profile.setting');
                Route::post('profile-setting', 'submitProfile');
                Route::get('change-password', 'changePassword')->name('change.password');
                Route::post('change-password', 'submitPassword');
                Route::post('follow', 'follow')->name('follow');
                Route::get('remove-favorite/{id}', 'removefavorite')->name('remove.favorite');
            });


            // Withdraw
            Route::controller('WithdrawController')->name('withdraw')->group(function () {
                Route::middleware('kyc')->prefix('withdraw')->group(function () {
                    Route::get('/', 'withdrawMoney');
                    Route::post('/', 'withdrawStore')->name('.money');
                    Route::get('preview', 'withdrawPreview')->name('.preview');
                    Route::post('preview', 'withdrawSubmit')->name('.submit');
                });
                Route::get('withdrawal-history', 'withdrawLog')->name('.history');
            });

            Route::controller('CheckoutController')->group(function () {
                Route::post('checkout/order', 'order')->name('checkout.order');
                Route::post('coupon/apply', 'applyCoupon')->name('coupon.apply');
                Route::post('remove/coupon', 'removeCoupon')->name('remove.coupon');
            });
        });

        // Payment
        Route::prefix('order')->name('deposit.')->controller('Gateway\PaymentController')->group(function () {
            Route::any('/{hash}', 'deposit')->name('index');
            Route::post('payment/insert', 'depositInsert')->name('insert');
            Route::get('payment/confirm', 'depositConfirm')->name('confirm');
            Route::get('payment/manual', 'manualDepositConfirm')->name('manual.confirm');
            Route::post('payment/manual', 'manualDepositUpdate')->name('manual.update');
        });

        // prompt
        Route::namespace('User')->controller('PromptController')->name('prompt.')->group(function () {
            Route::prefix('prompt')->group(function () {
                Route::get('create', 'create')->name('create');
                Route::get('create/details/{slug?}', 'step2')->name('step2');
                Route::get('edit/details/{slug}', 'step2Edit')->name('step2.edit');
                Route::post('store-details/{slug?}', 'step2Store')->name('step2.store');
                Route::get('prompt-file/{slug?}', 'step3')->name('step3');
                Route::post('store-prompt-file/{slug?}', 'step3Store')->name('step3.store');
                Route::get('publish/{slug?}', 'step4')->name('step4');
                Route::get('my-prompts', 'myPrompt')->name('my.list');
                Route::get('favorite-prompts', 'favoritePrompt')->name('favorite.list');
                Route::get('/download-prompt-file/{id}', 'downloadPromptFile')->name('download');
            });

            Route::get('my-prompts', 'myPrompt')->name('my.list');
            Route::get('favorite-prompts', 'favoritePrompt')->name('favorite.list');
            Route::get('download-prompt-file/{id}', 'downloadPromptFile')->name('download');
            Route::post('review/{slug}', 'review')->name('review');
            Route::post('review-prompt/{slug}', 'review')->name('review');
        });
    });
});
