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
    Route::get('/admin/stock_review', [StockController::class, 'reviewStockMovements'])->name('admin.stock');
    Route::patch('/admin/stock/approve/{id}', [StockController::class, 'approveStockMovement'])->name('stock.approve');
    Route::patch('/admin/stock/reject/{id}', [StockController::class, 'rejectStockMovement'])->name('stock.reject');

    // POS
    Route::get('/pos', [POSController::class, 'index'])->name('pos.index');
    Route::post('/pos/add-to-cart', [POSController::class, 'addToCart'])->name('pos.addToCart');
    Route::get('/pos/calculate-total', [POSController::class, 'calculateTotal'])->name('pos.calculateTotal');
    Route::post('/pos/checkout', [POSController::class, 'checkout'])->name('pos.checkout');
    Route::get('/admin/purchase-history/{id}', [MembersController::class, 'purchaseHistory'])->name('admin.purchase_history');
});

/*------------------------------------------
All Staff Routes List
--------------------------------------------*/
Route::middleware(['auth', 'user-access:staff'])->group(function () {
    Route::get('/staff/pos', [POSController::class, 'index'])->name('staff.pos');
    Route::resource('/staff/pos', PosController::class);
    Route::resource('/staff/member', MembersController::class);
    Route::put('/staff/member/update/{id}', [MembersController::class, 'update'])->name('member.update');
    Route::delete('/staff/member/destroy/{id}', [MembersController::class, 'destroy'])->name('member.destroy');
    Route::get('/staff/sales-history', [OrderController::class, 'salesHistory'])->name('staff.sales.history');
    Route::get('/staff/sales-history/{id}', [OrderController::class, 'salesDetail'])->name('staff.sales.detail');
    Route::get('/staff/sales-history2/{orderNumber}', [OrderController::class, 'salesDetail2'])->name('staff.sales.detail2');
    Route::get('/staff/purchase-history/{id}', [MembersController::class, 'purchaseHistory'])->name('staff.purchase_history');
    Route::get('/staff/stock', [StockController::class, 'index'])->name('staff.stock');
    Route::post('/staff/stock/pendingStock/{id}/add', [StockController::class, 'pendingStockAdd'])->name('pendingStockAdd');
    Route::post('/staff/stock/pendingStock/{id}/reduce', [StockController::class, 'pendingStockReduce'])->name('pendingStockReduce');
    
    // POS
    Route::get('/pos', [POSController::class, 'index'])->name('pos.index');
    Route::post('/pos/add-to-cart', [POSController::class, 'addToCart'])->name('pos.addToCart');
    Route::get('/pos/calculate-total', [POSController::class, 'calculateTotal'])->name('pos.calculateTotal');
    Route::post('/pos/checkout', [POSController::class, 'checkout'])->name('pos.checkout');
});

/*------------------------------------------
All Manager Routes List
--------------------------------------------*/
Route::middleware(['auth', 'user-access:manager'])->group(function () {});
/*------------------------------------------
All API Routes List
--------------------------------------------*/
Route::post('/api/orders', [OrderController::class, 'store']);
Route::get('/sales/filter', [DashboardController::class, 'filterSales']);
Route::get('/orders/{order}/print', [OrderController::class, 'print'])->name('orders.print');
