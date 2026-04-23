<?php

use App\Http\Controllers\Admin;
use App\Http\Controllers\Admin\NotificationSettingController;
use App\Http\Controllers\Admin\PaymentSettingController;
use App\Http\Controllers\Admin\ShippingSettingController;
use App\Http\Controllers\Admin\StoreSettingController;
use App\Http\Controllers\Admin\ProductSettingController;
use App\Http\Controllers\Admin\AdminUserController;
use App\Http\Controllers\Admin\AdminSessionController;
use App\Http\Controllers\Admin\AdminNotificationController;
use App\Http\Controllers\Admin\SecurityController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ShopController;
use App\Http\Controllers\UserDashboardController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\ProfileController as AdminProfileController;
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/tentang', [HomeController::class, 'about'])->name('about');
Route::get('/kontak', [HomeController::class, 'contact'])->name('contact');

Route::get('/produk', [ShopController::class, 'index'])->name('shop');
Route::get('/produk/{product:slug}', [ShopController::class, 'show'])->name('product.show');

require __DIR__ . '/auth.php';

Route::get('/dashboard', function () {
    if (auth()->user()->isAdmin()) {
        return redirect()->route('admin.dashboard');
    }
    return redirect()->route('user.profile');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware(['auth', 'verified'])->group(function () {

    Route::get('/keranjang', [CartController::class, 'index'])->name('cart.index');
    Route::post('/keranjang/tambah', [CartController::class, 'add'])->name('cart.add');
    Route::patch('/keranjang/{cartItem}', [CartController::class, 'update'])->name('cart.update');
    Route::patch('/keranjang/{cartItem}/toggle-select', [CartController::class, 'toggleSelect'])->name('cart.toggle-select');
    Route::post('/keranjang/toggle-select-all', [CartController::class, 'toggleSelectAll'])->name('cart.toggle-select-all');
    Route::delete('/keranjang/{cartItem}', [CartController::class, 'remove'])->name('cart.remove');
    Route::post('/keranjang/hapus-semua', [CartController::class, 'clearAll'])->name('cart.clear-all');

    Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout.index');
    Route::post('/checkout', [CheckoutController::class, 'store'])->name('checkout.store');

    Route::get('/pesanan', [OrderController::class, 'index'])->name('orders.index');
    Route::get('/pesanan/{order}', [OrderController::class, 'show'])->name('orders.show');
    Route::post('/pesanan/{order}/batal', [OrderController::class, 'cancel'])->name('orders.cancel');
    Route::post('/pesanan/{order}/item/{itemId}/batal', [OrderController::class, 'cancelItem'])->name('orders.cancel-item');
    Route::post('/pesanan/{order}/bayar', [OrderController::class, 'payNow'])->name('orders.pay-now');
    Route::post('/pesanan/{order}/beli-lagi', [OrderController::class, 'buyAgain'])->name('orders.buy-again');
    Route::post('/pesanan/{order}/beli-lagi-item', [OrderController::class, 'buyAgainItem'])->name('orders.buy-again-item');
    Route::post('/pesanan/{order}/ulasan', [OrderController::class, 'storeReview'])->name('orders.review');
    Route::delete('/ulasan/{review}/hapus', [OrderController::class, 'deleteReview'])->name('review.delete');

    Route::prefix('akun')->name('user.')->group(function () {
        Route::get('/profil', [UserDashboardController::class, 'profile'])->name('profile');
        Route::put('/profil', [UserDashboardController::class, 'updateProfile'])->name('profile.update');
        Route::put('/password', [UserDashboardController::class, 'changePassword'])->name('password.update');
        Route::get('/alamat', [UserDashboardController::class, 'addresses'])->name('addresses');
        Route::post('/alamat', [UserDashboardController::class, 'storeAddress'])->name('addresses.store');
        Route::delete('/alamat/{address}', [UserDashboardController::class, 'deleteAddress'])->name('addresses.destroy');
        Route::patch('/alamat/{address}/default', [UserDashboardController::class, 'setDefaultAddress'])->name('addresses.default');
    });
});

Route::middleware(['auth', 'admin', 'update_session_activity'])->prefix('admin')->name('admin.')->group(function () {

    Route::get('/', [Admin\DashboardController::class, 'index'])->name('dashboard');

    Route::resource('products', Admin\ProductController::class)->names([
        'index'   => 'products.index',
        'create'  => 'products.create',
        'store'   => 'products.store',
        'edit'    => 'products.edit',
        'update'  => 'products.update',
        'destroy' => 'products.destroy',
    ]);

    Route::get('categories', [Admin\CategoryController::class, 'index'])->name('categories.index');
    Route::post('categories', [Admin\CategoryController::class, 'store'])->name('categories.store');
    Route::put('categories/{category}', [Admin\CategoryController::class, 'update'])->name('categories.update');
    Route::delete('categories/{category}', [Admin\CategoryController::class, 'destroy'])->name('categories.destroy');

    Route::get('orders', [Admin\OrderController::class, 'index'])->name('orders.index');
    Route::get('orders/export', [Admin\OrderController::class, 'export'])->name('orders.export');
    Route::get('orders/{order}', [Admin\OrderController::class, 'show'])->name('orders.show');
    Route::patch('orders/{order}/status', [Admin\OrderController::class, 'updateStatus'])->name('orders.update-status');

    Route::get('customers', [Admin\CustomerController::class, 'index'])->name('customers.index');
    Route::get('customers/{user}', [Admin\CustomerController::class, 'show'])->name('customers.show');

    Route::get('reports', [Admin\ReportController::class, 'index'])->name('reports.index');
    Route::get('reports/export', [Admin\ReportController::class, 'export'])->name('reports.export');

    Route::get('settings/store', [StoreSettingController::class, 'index'])->name('settings.store');
    Route::post('settings/store', [StoreSettingController::class, 'update'])->name('settings.store.update');

    Route::get('settings/payment',  [PaymentSettingController::class, 'index'])->name('settings.payment');
    Route::post('settings/payment', [PaymentSettingController::class, 'update'])->name('settings.payment.update');
    Route::post('settings/payment/bank', [PaymentSettingController::class, 'storeBankAccount'])->name('settings.bank.store');
    Route::put('settings/payment/bank/{bankAccount}', [PaymentSettingController::class, 'updateBankAccount'])->name('settings.bank.update');
    Route::delete('settings/payment/bank/{bankAccount}', [PaymentSettingController::class, 'destroyBankAccount'])->name('settings.bank.destroy');

    Route::get('settings/shipping',  [ShippingSettingController::class, 'index'])->name('settings.shipping');
    Route::post('settings/shipping', [ShippingSettingController::class, 'update'])->name('settings.shipping.update');

    Route::get('settings/notification',  [NotificationSettingController::class, 'index'])->name('settings.notification');
    Route::post('settings/notification', [NotificationSettingController::class, 'update'])->name('settings.notification.update');
    Route::post('settings/notification/test-email',     [NotificationSettingController::class, 'testEmail'])->name('settings.notification.test-email');
    Route::post('settings/notification/test-whatsapp',  [NotificationSettingController::class, 'testWhatsapp'])->name('settings.notification.test-whatsapp');

    Route::get('/profile', [AdminProfileController::class, 'index'])->name('profile');
    Route::post('/profile/update', [AdminProfileController::class, 'update'])->name('profile.update');
    Route::post('/profile/password', [AdminProfileController::class, 'updatePassword'])->name('profile.password');

    Route::patch('products/{product}/stock', [Admin\ProductController::class, 'updateStock'])->name('products.update-stock');

    Route::get('settings/product',       [ProductSettingController::class, 'index'])->name('settings.product');
    Route::post('settings/product',      [ProductSettingController::class, 'update'])->name('settings.product.update');
    Route::post('settings/product/reset', [ProductSettingController::class, 'reset'])->name('settings.product.reset');

    Route::middleware('super_admin')->group(function () {
        Route::get('admins',             [AdminUserController::class, 'index'])->name('admins.index');
        Route::get('admins/create',      [AdminUserController::class, 'create'])->name('admins.create');
        Route::post('admins',            [AdminUserController::class, 'store'])->name('admins.store');
        Route::get('admins/{user}/edit', [AdminUserController::class, 'edit'])->name('admins.edit');
        Route::put('admins/{user}',      [AdminUserController::class, 'update'])->name('admins.update');
        Route::delete('admins/{user}',   [AdminUserController::class, 'destroy'])->name('admins.destroy');

        Route::get('security',           [SecurityController::class, 'index'])->name('security.index');
    });

    Route::get('sessions',              [AdminSessionController::class, 'index'])->name('sessions.index');
    Route::delete('sessions/all',       [AdminSessionController::class, 'destroyAll'])->name('sessions.destroy-all');
    Route::delete('sessions/{adminSession}', [AdminSessionController::class, 'destroy'])->name('sessions.destroy');

    Route::get('notifications',                       [AdminNotificationController::class, 'index'])->name('notifications.index');
    Route::get('notifications/unread-count',          [AdminNotificationController::class, 'unreadCount'])->name('notifications.unread-count');
    Route::get('notifications/latest',                [AdminNotificationController::class, 'latest'])->name('notifications.latest');
    Route::post('notifications/{notification}/read',  [AdminNotificationController::class, 'markRead'])->name('notifications.read');
    Route::post('notifications/read-all',             [AdminNotificationController::class, 'markAllRead'])->name('notifications.read-all');
    Route::delete('notifications/{notification}',     [AdminNotificationController::class, 'destroy'])->name('notifications.destroy');
});
