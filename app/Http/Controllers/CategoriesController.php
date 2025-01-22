<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CategoriesController extends Controller
{
    public function index(Request $request)
    {
        $categories = Category::all();

        if (Auth::user()->type === 'admin') {
            return view('admin.categorys', compact('categories'));
        } elseif (Auth::user()->type === 'manager') {
            return view('manager.categorys', compact('categories'));
        }
        return view('home');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $data = $request->all();

        // บันทึกข้อมูลสินค้า
        Category::create($data);

        return back()->with('success', 'เพิ่มสินค้าเรียบร้อยแล้ว!');
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $Category = Category::findOrFail($id);

        // อัปเดตข้อมูลที่ไม่ใช่รูปภาพ
        $Category->update([
            'name' => $validated['name'],
        ]);

        return redirect()->back()->with('success', 'อัพเดตประเภทสินค้าเรียบร้อย!');
    }

    public function destroy($id)
    {
        try {
            $Category = Category::findOrFail($id); // ค้นหาสินค้าโดย ID
            $Category->delete(); // ลบสินค้า

            // คืนค่าการแจ้งเตือนหรือกลับไปยังหน้าก่อนหน้า
            return redirect()->back()->with('success', 'สินค้าถูกลบแล้ว.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to delete Category.');
        }
    }
}
