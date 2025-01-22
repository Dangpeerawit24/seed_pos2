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
        } elseif (Auth::user()->type === 'manager') {
            return view('manager.stock', compact('products'));
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
            'note' => $request->note,
        ]);

        return redirect()->back()->with('success', 'ลดสินค้าในสต็อกเรียบร้อย');
    }

    public function showStockMovements($productId)
    {
        $product = Product::findOrFail($productId);
        $stockMovements = $product->stockMovements()
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('admin.stockmovements', compact('product', 'stockMovements'));
    }
}
