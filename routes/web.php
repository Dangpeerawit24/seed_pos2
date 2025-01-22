<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CategoriesController;
use App\Http\Controllers\PosController;
use App\Http\Controllers\UsersController;
use App\Http\Controllers\MembersController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\StockController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\CashDrawerController;


Route::get('/', function () {
    return view('auth.login');
});


Auth::routes();

/*------------------------------------------
All Normal Users Routes List
--------------------------------------------*/
Route::middleware(['auth', 'user-access:staff'])->group(function () {

    Route::get('/home', [POSController::class, 'index'])->name('home');
    Route::get('/staff/pos', [POSController::class, 'index'])->name('staff.dashboard');
});

/*------------------------------------------
All Admin Routes List
--------------------------------------------*/
Route::middleware(['auth', 'user-access:admin'])->group(function () {

    Route::get('/admin/home', [HomeController::class, 'adminHome'])->name('admin.home');
    Route::view('/admin/index', 'admin.index');
    Route::resource('/admin/products', ProductController::class)->names([
        'store' => 'products.store',
    ]);
    Route::delete('/admin/products/destroy/{id}', [ProductController::class, 'destroy'])->name('products.destroy');
    Route::put('/admin/products/update/{id}', [ProductController::class, 'update'])->name('products.update');
    Route::resource('/admin/categories', CategoriesController::class);
    Route::delete('/admin/categories/destroy/{id}', [CategoriesController::class, 'destroy'])->name('products.destroy');
    Route::put('/admin/categories/update/{id}', [CategoriesController::class, 'update'])->name('products.update');
    Route::resource('/admin/pos', PosController::class);
    Route::resource('/admin/users', UsersController::class);
    Route::put('/admin/users/update/{id}', [UsersController::class, 'update'])->name('users.update');
    Route::delete('/admin/users/destroy/{id}', [UsersController::class, 'destroy'])->name('users.destroy');
    Route::resource('/admin/member', MembersController::class);
    Route::put('/admin/member/update/{id}', [MembersController::class, 'update'])->name('member.update');
    Route::delete('/admin/member/destroy/{id}', [MembersController::class, 'destroy'])->name('member.destroy');
    Route::get('/admin/sales-history', [OrderController::class, 'salesHistory'])->name('sales.history');
    Route::get('/admin/sales-history/{id}', [OrderController::class, 'salesDetail'])->name('sales.detail');
    Route::get('/admin/sales-history2/{orderNumber}', [OrderController::class, 'salesDetail2'])->name('sales.detail2');
    Route::get('/admin/orders/{order}/edit', [OrderController::class, 'edit'])->name('orders.edit');
    Route::put('/admin/orders/{order}', [OrderController::class, 'update'])->name('orders.update');
    Route::patch('/admin/orders/{id}/cancel', [OrderController::class, 'cancelOrder'])->name('orders.cancel');
    Route::patch('/admin/orders/{id}/completed', [OrderController::class, 'completedOrder'])->name('orders.completed');
    Route::patch('/admin/orders/{id}/rebate', [OrderController::class, 'rebateOrder'])->name('orders.rebate');
    Route::patch('/admin/orders/{id}/rebateMoneyOrder', [OrderController::class, 'rebateMoneyOrder'])->name('orders.rebateMoneyOrder');
    Route::get('/admin/stock', [StockController::class, 'index'])->name('admin.stock');
    Route::get('/admin/stock/movements/{product}', [StockController::class, 'showStockMovements'])->name('stock.movements');
    Route::post('/admin/stock/{id}/add', [StockController::class, 'addStock'])->name('stock.add');
    Route::post('/admin/stock/{id}/reduce', [StockController::class, 'reduceStock'])->name('stock.reduce');
    Route::get('/admin/dashboard', [DashboardController::class, 'index'])->name('admin.dashboard');
    Route::get('/admin/today-product-sales', [DashboardController::class, 'todayProductSales'])->name('admin.today-product-sales');
    Route::get('/admin/month-product-sales', [DashboardController::class, 'monthProductSales'])->name('admin.month-product-sales');
    Route::get('/admin/cashdrawer', [CashDrawerController::class, 'index'])->name('cashdrawer.index');
    Route::post('/admin/cashdrawer/add', [CashDrawerController::class, 'addFunds'])->name('cashdrawer.add');
    Route::post('/admin/cashdrawer/subtract', [CashDrawerController::class, 'subtractFunds'])->name('cashdrawer.subtract');
    // POS
    Route::get('/pos', [POSController::class, 'index'])->name('pos.index');
    Route::post('/pos/add-to-cart', [POSController::class, 'addToCart'])->name('pos.addToCart');
    Route::get('/pos/calculate-total', [POSController::class, 'calculateTotal'])->name('pos.calculateTotal');
    Route::post('/pos/checkout', [POSController::class, 'checkout'])->name('pos.checkout');
});

/*------------------------------------------
All Manager Routes List
--------------------------------------------*/
Route::middleware(['auth', 'user-access:manager'])->group(function () {

    Route::get('/manager/home', [HomeController::class, 'managerHome'])->name('manager.home');
    Route::view('/manager/index', 'manager.index');
    Route::resource('/manager/products', ProductController::class)->names([
        'store' => 'manager.products.store',
    ]);
    Route::delete('/manager/products/destroy/{id}', [ProductController::class, 'destroy'])->name('manager.products.destroy');
    Route::put('/manager/products/update/{id}', [ProductController::class, 'update'])->name('manager.products.update');
    Route::resource('/manager/categories', CategoriesController::class)->names([
        'store' => 'manager.categories.store',
    ]);
    Route::delete('/manager/categories/destroy/{id}', [CategoriesController::class, 'destroy'])->name('manager.products.destroy');
    Route::put('/manager/categories/update/{id}', [CategoriesController::class, 'update'])->name('manager.products.update');
    Route::resource('/manager/pos', PosController::class);
    Route::resource('/manager/users', UsersController::class)->names([
        'store' => 'manager.users.store',
    ]);
    Route::put('/manager/users/update/{id}', [UsersController::class, 'update'])->name('manager.users.update');
    Route::delete('/manager/users/destroy/{id}', [UsersController::class, 'destroy'])->name('manager.users.destroy');
    Route::get('/manager/sales-history', [OrderController::class, 'salesHistory'])->name('manager.sales.history');
    Route::get('/manager/sales-history/{id}', [OrderController::class, 'salesDetail'])->name('manager.sales.detail');
    Route::get('/manager/sales-history2/{orderNumber}', [OrderController::class, 'salesDetail2'])->name('manager.sales.detail2');
    Route::patch('/manager/orders/{id}/cancel', [OrderController::class, 'cancelOrder'])->name('manager.orders.cancel');
    Route::get('/manager/stock', [StockController::class, 'index'])->name('manager.manager.stock');
    Route::post('/manager/stock/{id}/add', [StockController::class, 'addStock'])->name('manager.stock.add');
    Route::post('/manager/stock/{id}/reduce', [StockController::class, 'reduceStock'])->name('manager.stock.reduce');
    Route::get('/manager/dashboard', [DashboardController::class, 'index'])->name('manager.dashboard');
    Route::get('/manager/cashdrawer', [CashDrawerController::class, 'index'])->name('manager.cashdrawer.index');
    Route::post('/manager/cashdrawer/add', [CashDrawerController::class, 'addFunds'])->name('manager.cashdrawer.add');
    Route::post('/manager/cashdrawer/subtract', [CashDrawerController::class, 'subtractFunds'])->name('manager.cashdrawer.subtract');
    // POS
    Route::get('/pos', [POSController::class, 'index'])->name('pos.index');
    Route::post('/pos/add-to-cart', [POSController::class, 'addToCart'])->name('pos.addToCart');
    Route::get('/pos/calculate-total', [POSController::class, 'calculateTotal'])->name('pos.calculateTotal');
    Route::post('/pos/checkout', [POSController::class, 'checkout'])->name('pos.checkout');
});


/*------------------------------------------
All API Routes List
--------------------------------------------*/
Route::post('/api/orders', [OrderController::class, 'store']);
Route::get('/sales/filter', [DashboardController::class, 'filterSales']);
Route::get('/orders/{order}/print', [OrderController::class, 'print'])->name('orders.print');
