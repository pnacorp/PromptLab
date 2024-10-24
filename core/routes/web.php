<?php

use App\Models\Deposit;
use Illuminate\Support\Facades\Route;

Route::get('/clear', function(){
    \Illuminate\Support\Facades\Artisan::call('optimize:clear');
});

// User Support Ticket
Route::controller('TicketController')->prefix('ticket')->name('ticket.')->group(function () {
    Route::get('/', 'supportTicket')->name('index');
    Route::get('new', 'openSupportTicket')->name('open');
    Route::post('create', 'storeSupportTicket')->name('store');
    Route::get('view/{ticket}', 'viewTicket')->name('view');
    Route::post('reply/{id}', 'replyTicket')->name('reply');
    Route::post('close/{id}', 'closeTicket')->name('close');
    Route::get('download/{attachment_id}', 'ticketDownload')->name('download');
});

Route::get('app/deposit/confirm/{hash}', 'Gateway\PaymentController@appDepositConfirm')->name('deposit.app.confirm');

Route::controller('SiteController')->group(function () {
    Route::post('/subscribe', 'addSubscriber')->name('subscribe');
    Route::get('/contact', 'contact')->name('contact');
    Route::post('/contact', 'contactSubmit');
    Route::get('/change/{lang?}', 'changeLanguage')->name('lang');

    Route::get('cookie-policy', 'cookiePolicy')->name('cookie.policy');

    Route::get('/cookie/accept', 'cookieAccept')->name('cookie.accept');

    Route::get('blog', 'blogs')->name('blog');
    Route::get('blog/{slug}', 'blogDetails')->name('blog.details');

    Route::get('policy/{slug}', 'policyPages')->name('policy.pages');

    Route::get('placeholder-image/{size}', 'placeholderImage')->withoutMiddleware('maintenance')->name('placeholder.image');
    Route::get('maintenance-mode','maintenance')->withoutMiddleware('maintenance')->name('maintenance');

    Route::get('pages/{slug}', 'pages')->name('pages');
    Route::get('/', 'index')->name('home');

    Route::get('top-sellers', 'topSeller')->name('top.seller');

    Route::get('profile/{username}', 'profile')->name('seller.profile');
});

Route::controller('PromptController')->name('prompt.')->group(function () {
    Route::get('prompts', 'index')->name('all');
    Route::get('prompts-by-category/{slug}', 'categoryWise')->name('categories');
    Route::get('prompts-by-ai-tool/{slug}', 'toolWise')->name('tools');
    Route::get('prompts/filter', 'promptFilter')->name('filter');
    Route::get('prompts/featured', 'featured')->name('featured');
    Route::get('prompts/trending', 'trending')->name('trending');
    Route::get('similar-prompts/{id}', 'similar')->name('similar');
    Route::get('prompt-details/{slug}', 'details')->name('details');
    Route::post('add-to-favorite', 'addFavorite')->name('favorite');

});

Route::controller('CartController')->name('cart.')->group(function () {
    Route::post('add-to-cart', 'addToCart')->name('add');
    Route::get('cart-items-count', 'cartCount')->name('count');
    Route::get('my-cart', 'viewCart')->name('view');
    Route::post('my-cart/clear', 'clearCart')->name('clear');
    Route::post('my-cart/clear/{id}', 'deleteCart')->name('delete');
});
