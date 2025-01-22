<?php

namespace App\Http\Controllers;

use App\Models\Member;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class MembersController extends Controller
{
    public function index(Request $request)
    {
        $Member = Member::where('id', '!=', '1')->get();
        
        if (Auth::user()->type === 'admin') {
            return view('admin.member', compact('Member'));
        } elseif (Auth::user()->type === 'manager') {
            return view('manager.member', compact('Member'));
        }
        return view('home');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'phone' => 'required',
        ]);

        Member::create($request->all());
        return redirect()->back()->with('success', 'เพิ่มข้อมูล สมาชิก เรียบร้อยแล้ว.');
    }

    public function update(Request $request, $id)
    {
        // Validation rules
        $validated = $request->validate([
            'name' => 'required',
            'phone' => 'required',
        ]);

        // ดึงข้อมูล User จากฐานข้อมูล
        $Member = Member::findOrFail($id);

        // เตรียมข้อมูลสำหรับการอัปเดต
        $updateData = [
            'name' => $validated['name'],
            'phone' => $validated['phone'],
        ];


        // อัปเดตข้อมูล User
        $Member->update($updateData);

        return redirect()->back()->with('success', 'อัพเดตข้อมูลสมาชิกเรียบร้อยแล้ว.');
    }

    public function destroy($id)
    {
        try {
            $Member = Member::findOrFail($id); // ค้นหาสินค้าโดย ID

            $Member->delete(); // ลบสินค้า

            // คืนค่าการแจ้งเตือนหรือกลับไปยังหน้าก่อนหน้า
            return redirect()->back()->with('success', 'ลบข้อมูล สมาชิก เรียบร้อยแล้ว.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to delete User.');
        }
    }
}
