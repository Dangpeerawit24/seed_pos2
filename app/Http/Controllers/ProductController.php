<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $categories = \App\Models\Category::all();
        $products = Product::all();
        
        if (Auth::user()->type === 'admin') {
            return view('admin.products', compact('products', 'categories'));
        } elseif (Auth::user()->type === 'manager') {
            return view('manager.products', compact('products', 'categories'));
        }
        return view('home');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            // 'cost_price' => 'required|numeric|min:0',
            // 'stock_quantity' => 'required|integer|min:0',
            'restock_level' => 'required|integer|min:0',
            'category' => 'required|string|max:255',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:7048',
        ]);

        $data = $request->all();

        if ($request->hasFile('image')) {
            $fileName = time() . '.' . $request->image->extension();
            $request->image->move(public_path('img/product/'), $fileName);
            $data['image'] = $fileName;
        }

        // บันทึกข้อมูลสินค้า
        Product::create($data);

        return redirect()->back()->with('success', 'เพิ่มสินค้าเรียบร้อยแล้ว!');
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            // 'cost_price' => 'required|numeric|min:0',
            // 'stock_quantity' => 'required|integer|min:0',
            'restock_level' => 'required|integer|min:0',
            'description' => 'nullable|string',
            'category' => 'required|string|max:255',
            'image' => 'nullable|image|max:2048', // ตรวจสอบรูปภาพ
        ]);

        $product = Product::findOrFail($id);

        // อัปเดตข้อมูลที่ไม่ใช่รูปภาพ
        $product->update([
            'name' => $validated['name'],
            'price' => $validated['price'],
            // 'cost_price' => $validated['cost_price'],
            // 'stock_quantity' => $validated['stock_quantity'],
            'restock_level' => $validated['restock_level'],
            'description' => $validated['description'] ?? null,
            'category' => $validated['category'] ?? null,
        ]);

        // อัปโหลดรูปภาพถ้ามี
        if ($request->hasFile('image')) {
            // ลบรูปภาพเก่า (ถ้ามี)
            if ($product->image && file_exists(public_path('img/product/' . $product->image))) {
                unlink(public_path('img/product/' . $product->image));
            }

            // อัปโหลดรูปภาพใหม่
            $fileName = time() . '.' . $request->image->extension();
            $request->image->move(public_path('img/product/'), $fileName);

            // อัปเดตรูปภาพในฐานข้อมูล
            $product->update(['image' => $fileName]);
        }

        return redirect()->back()->with('success', 'Product updated successfully!');
    }

    public function destroy($id)
    {
        try {
            $product = Product::findOrFail($id); // ค้นหาสินค้าโดย ID
            if ($product->image && file_exists(public_path('img/product/' . $product->image))) {
                unlink(public_path('img/product/' . $product->image));
            }
            $product->delete(); // ลบสินค้า

            // คืนค่าการแจ้งเตือนหรือกลับไปยังหน้าก่อนหน้า
            return redirect()->back()->with('success', 'สินค้าถูกลบแล้ว.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to delete product.');
        }
    }

}
