<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\StockMovement;
use Illuminate\Support\Facades\Auth;

class StockController extends Controller
{
    // แสดงรายการสินค้าและสต็อก
    public function index(Request $request)
    {

        $restock = $request->restock;

        if ($restock) {
            $products = Product::with('latestStockMovement')
                ->whereColumn('stock_quantity', '<', 'restock_level') // เปรียบเทียบคอลัมน์ stock_quantity กับ restock_level
                ->orderBy('created_at', 'desc')
                ->get();
        } else {
            $products = Product::with('latestStockMovement.user')->get();
        }

        if (Auth::user()->type === 'admin') {
            return view('admin.stock', compact('products'));
        } elseif (Auth::user()->type === 'staff') {
            return view('staff.stock', compact('products'));
        }
        return view('home');
    }


    // เพิ่มสินค้าในสต็อก
    public function addStock(Request $request, $id)
    {
        $request->validate([
            'quantity' => 'required|integer|min:1',
            'note' => 'nullable|string',
        ]);

        $product = Product::findOrFail($id);
        $product->increment('stock_quantity', $request->quantity);

        StockMovement::create([
            'product_id' => $product->id,
            'user_id' => auth()->id(), // บันทึก ID ของผู้ใช้งาน
            'quantity' => $request->quantity,
            'type' => 'in',
            'status' => 'approved',
            'note' => $request->note,
        ]);

        return redirect()->back()->with('success', 'เพิ่มสินค้าในสต็อกเรียบร้อย');
    }


    // ลดสินค้าในสต็อก
    public function reduceStock(Request $request, $id)
    {
        $request->validate([
            'quantity' => 'required|integer|min:1',
            'note' => 'nullable|string',
        ]);

        $product = Product::findOrFail($id);

        if ($product->stock_quantity < $request->quantity) {
            return redirect()->back()->with('error', 'สต็อกสินค้าไม่เพียงพอ');
        }

        $product->decrement('stock_quantity', $request->quantity);

        StockMovement::create([
            'product_id' => $product->id,
            'user_id' => auth()->id(), // บันทึก ID ของผู้ใช้งาน
            'quantity' => $request->quantity,
            'type' => 'out',
            'status' => 'approved',
            'note' => $request->note,
        ]);

        return redirect()->back()->with('success', 'ลดสินค้าในสต็อกเรียบร้อย');
    }

    public function showStockMovements($productId)
    {
        $product = Product::findOrFail($productId);
        $stockMovements = $product->stockMovements()
            ->with('user')
            ->where('status', '!=', 'pending')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('admin.stockmovements', compact('product', 'stockMovements'));
    }

    public function pendingStockAdd(Request $request, $id)
    {
        $request->validate([
            'quantity' => 'required|integer',
            'note' => 'nullable|string'
        ]);
        $operation = 'add';

        StockMovement::create([
            'product_id' => $id,
            'user_id' => Auth::id(),
            'quantity' => ($operation == 'add' ? 1 : -1) * abs($request->quantity),
            'operation' => $operation,
            'status' => 'pending', // Initial status is always pending
            'note' => $request->note
        ]);

        return back()->with('success', 'ส่งคำร้องขอปรับสต็อกเรียบร้อยโปรดรอการตรวจสอบ');
    }

    public function pendingStockReduce(Request $request, $id)
    {
        $request->validate([
            'quantity' => 'required|integer',
            'note' => 'nullable|string'
        ]);
        $operation = 'reduce';

        StockMovement::create([
            'product_id' => $id,
            'user_id' => Auth::id(),
            'quantity' => ($operation == 'add' ? 1 : -1) * abs($request->quantity),
            'operation' => $operation,
            'type' => 'out',
            'status' => 'pending', // Initial status is always pending
            'note' => $request->note
        ]);

        return back()->with('success', 'ส่งคำร้องขอปรับสต็อกเรียบร้อยโปรดรอการตรวจสอบ');
    }

    public function reviewStockMovements()
    {
        $movements = StockMovement::where('status', 'pending')->get();
        return view('admin.stock_review', compact('movements'));
    }

    public function approveStockMovement($id)
    {
        $movement = StockMovement::findOrFail($id);
        $product = Product::findOrFail($movement->product_id);
        $product->stock_quantity += $movement->quantity;
        $product->save();

        $movement->update(['status' => 'approved']);
        return back()->with('success', 'ยืนยันการขอปรับสต็อก.');
    }

    public function rejectStockMovement($id)
    {
        $movement = StockMovement::findOrFail($id);
        $movement->update(['status' => 'rejected']);
        return back()->with('error', 'ปฏิเสธการขอปรับสต็อก.');
    }
}
