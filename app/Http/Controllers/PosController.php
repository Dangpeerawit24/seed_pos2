<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Member;


class PosController extends Controller
{
    public function index(Request $request)
    {
        $products = \App\Models\Product::where('stock_quantity', '>', 0)->get();
        $categories = \App\Models\Category::all();
        $members = Member::where('id', '!=', '1')->get();

        if (Auth::user()->type === 'admin') {
            return view('admin.pos', compact('products', 'categories', 'members'));
        }elseif (Auth::user()->type === 'manager') {
            return view('manager.pos', compact('products', 'categories', 'members'));
        }elseif (Auth::user()->type === 'staff') {
            return view('staff.pos', compact('products', 'categories', 'members'));
        }
    }

    public function addToCart(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
        ]);

        $product = \App\Models\Product::find($request->product_id);

        // ดึงตะกร้าปัจจุบัน
        $cart = session()->get('cart', []);

        // เพิ่มหรืออัปเดตรายการสินค้าในตะกร้า
        if (isset($cart[$product->id])) {
            $cart[$product->id]['quantity'] += $request->quantity;
        } else {
            $cart[$product->id] = [
                'name' => $product->name,
                'price' => $product->price,
                'quantity' => $request->quantity,
            ];
        }

        // บันทึกตะกร้าใน session
        session()->put('cart', $cart);

        return response()->json(['success' => 'Product added to cart!', 'cart' => $cart]);
    }

    public function calculateTotal()
    {
        $cart = session()->get('cart', []);
        $total = 0;

        foreach ($cart as $item) {
            $total += $item['price'] * $item['quantity'];
        }

        return response()->json(['total' => $total]);
    }

    public function checkout(Request $request)
    {
        $cart = session()->get('cart', []);

        if (empty($cart)) {
            return response()->json(['error' => 'Cart is empty!'], 400);
        }

        $order = \App\Models\Order::create([
            'total_amount' => array_sum(array_map(function ($item) {
                return $item['price'] * $item['quantity'];
            }, $cart)),
            'status' => 'Completed',
        ]);

        foreach ($cart as $productId => $item) {
            \App\Models\OrderItem::create([
                'order_id' => $order->id,
                'product_id' => $productId,
                'quantity' => $item['quantity'],
                'price' => $item['price'],
            ]);

            // ลดจำนวนสินค้าในสต็อก
            $product = \App\Models\Product::find($productId);
            $product->decrement('stock_quantity', $item['quantity']);
        }

        // ล้างตะกร้าหลังบันทึกสำเร็จ
        session()->forget('cart');

        return response()->json(['success' => 'Order completed!', 'order_id' => $order->id]);
    }
}
