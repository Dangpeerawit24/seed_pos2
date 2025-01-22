<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\StockMovement;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        // คำนวณยอดขายรวมเฉพาะคำสั่งซื้อที่สถานะเป็น 'completed'
        $totalSales = Order::where('status', 'completed')
            ->whereYear('created_at', now()->year)
            ->whereMonth('created_at', now()->month)
            ->sum('total_amount');

        // นับจำนวนคำสั่งซื้อที่สถานะเป็น 'completed'
        $totalOrders = Order::where('status', '=', 'pending')->count();

        $borrowOrders = Order::where('status', '=', 'borrow')->count();

        // คำนวณยอดขายเฉพาะวันนี้
        $salesToday = Order::where('status', 'completed')
            ->whereDate('updated_at', today())
            ->sum('total_amount');

        // สินค้าที่ใกล้หมดสต็อก
        $lowStockProducts = Product::whereColumn('stock_quantity', '<', 'restock_level')->get();

        // การอัปเดตสต็อกล่าสุด
        $recentStockMovements = StockMovement::with('user', 'product')->latest()->take(5)->get();

        $soldProducts = DB::table('order_items')
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->join('orders', 'order_items.order_id', '=', 'orders.id') // เข้าร่วมกับ orders
            ->where('orders.status', '!=', 'cancelled') // กรองบิลที่ไม่ถูกยกเลิก
            ->select('products.name', DB::raw('SUM(order_items.quantity) as total_quantity'))
            ->groupBy('products.name')
            ->orderByDesc('total_quantity')
            ->get();

        // คำสั่งซื้อที่ถูกยกเลิก
        // $cancelledOrders = Order::where('status', 'cancelled')->orderBy('created_at', 'desc')->get();

        // ส่งข้อมูลไปยัง View
        return view('admin.dashboard', compact('totalSales', 'totalOrders', 'salesToday', 'lowStockProducts', 'recentStockMovements', 'soldProducts', 'borrowOrders'));
    }

    public function todayProductSales()
    {
        // ดึงเฉพาะ items ของ Order ที่ 'completed' วันนี้
        // หรือจะ join กับ orders ก็ได้ แต่ที่ง่ายสุด สมมติ OrderItem มี timestamp = created_at ของ order
        // ถ้ายังไม่แน่ใจ ให้ join กับ Order => whereDate(orders.created_at, Carbon::today()) + orders.status = completed
        // ตัวอย่างนี้ สมมติ OrderItem มี created_at/updated_at ตรงกับ Order
        $todayItems = OrderItem::select(
            'product_id',
            DB::raw('SUM(quantity) as total_qty'),
            DB::raw('SUM(quantity * price) as total_revenue')
        )
            ->join('orders', 'order_items.order_id', '=', 'orders.id') // ตัวเชื่อมต่อกับตาราง orders
            ->whereDate('orders.updated_at', today()) // ใช้วันที่จากตาราง orders
            ->where('orders.status', 'completed') // ใช้สถานะจากตาราง orders
            ->groupBy('product_id')
            ->with('product') // ต้องมั่นใจว่าการเชื่อมโยงข้อมูลผลิตภัณฑ์มีการตั้งค่าอย่างถูกต้อง
            ->get();


        // แยกเป็น label (ชื่อสินค้า), dataQty (ยอดจำนวน), dataRevenue (ยอดเงิน)
        // จะใช้ chart “จำนวน” หรือ “ยอดเงิน” ก็ได้
        $labels = [];
        $dataQty = [];
        $dataRevenue = [];

        foreach ($todayItems as $item) {
            $labels[] = $item->product->name ?? 'N/A';
            $dataQty[] = $item->total_qty;
            $dataRevenue[] = $item->total_revenue;
        }

        // รวมยอดเงินวันนี้ (ทุกสินค้า) => sum total_revenue
        $todaySum = array_sum($dataRevenue);

        return view('admin.today-product-sales', [
            'todayItems'  => $todayItems,
            'labels'      => $labels,
            'dataQty'     => $dataQty,
            'dataRevenue' => $dataRevenue,
            'todaySum'    => $todaySum,
        ]);
    }

    public function monthProductSales()
    {
        // ดึงเฉพาะ items ของ Order ที่ 'completed' วันนี้
        // หรือจะ join กับ orders ก็ได้ แต่ที่ง่ายสุด สมมติ OrderItem มี timestamp = created_at ของ order
        // ถ้ายังไม่แน่ใจ ให้ join กับ Order => whereDate(orders.created_at, Carbon::today()) + orders.status = completed
        // ตัวอย่างนี้ สมมติ OrderItem มี created_at/updated_at ตรงกับ Order
        $todayItems = OrderItem::select(
            'product_id',
            DB::raw('SUM(quantity) as total_qty'),
            DB::raw('SUM(quantity * price) as total_revenue')
        )
        ->join('orders', 'order_items.order_id', '=', 'orders.id')  // ตัวเชื่อมต่อกับตาราง orders
        ->whereMonth('orders.updated_at', Carbon::now()->month)     // กรองข้อมูลตามเดือนปัจจุบัน
        ->whereYear('orders.updated_at', Carbon::now()->year)       // กรองข้อมูลตามปีปัจจุบัน
        ->where('orders.status', 'completed')                       // ใช้สถานะจากตาราง orders
        ->groupBy('product_id')
        ->with('product')                                           // โหลดข้อมูลผลิตภัณฑ์ที่เกี่ยวข้อง
        ->get();


        // แยกเป็น label (ชื่อสินค้า), dataQty (ยอดจำนวน), dataRevenue (ยอดเงิน)
        // จะใช้ chart “จำนวน” หรือ “ยอดเงิน” ก็ได้
        $labels = [];
        $dataQty = [];
        $dataRevenue = [];

        foreach ($todayItems as $item) {
            $labels[] = $item->product->name ?? 'N/A';
            $dataQty[] = $item->total_qty;
            $dataRevenue[] = $item->total_revenue;
        }

        // รวมยอดเงินวันนี้ (ทุกสินค้า) => sum total_revenue
        $todaySum = array_sum($dataRevenue);

        return view('admin.month-product-sales', [
            'todayItems'  => $todayItems,
            'labels'      => $labels,
            'dataQty'     => $dataQty,
            'dataRevenue' => $dataRevenue,
            'todaySum'    => $todaySum,
        ]);
    }

    public function filterSales(Request $request)
    {
        $dateFilter = $request->query('date', 'today'); // ค่าเริ่มต้นคือ 'today'

        $query = DB::table('order_items')
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->select('products.name', DB::raw('SUM(order_items.quantity) as total_quantity'))
            ->groupBy('products.name')
            ->orderByDesc('total_quantity');

        if ($dateFilter === 'today') {
            $query->whereDate('order_items.created_at', now()->format('Y-m-d'));
        }

        $salesData = $query->get();

        return response()->json($salesData);
    }
}
