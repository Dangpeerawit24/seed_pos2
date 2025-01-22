<?php

namespace App\Http\Controllers;

use App\Models\CashDrawer;
use App\Models\CashMovement;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class CashDrawerController extends Controller
{
    public function index()
    {
        $cashDrawer = CashDrawer::firstOrCreate(['id' => 1], ['current_balance' => '0.00']);

        // ใช้ paginate สำหรับ movements
        $movements = $cashDrawer->movements()->orderBy('created_at', 'desc')->paginate(10);

        if (Auth::user()->type === 'admin') {
            return view('admin.cashdrawer', compact('cashDrawer', 'movements'));
        } elseif (Auth::user()->type === 'manager') {
            return view('manager.cashdrawer', compact('cashDrawer', 'movements'));
        }
        return view('home');
    }


    public function addFunds(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:0.01',
            'note' => 'nullable|string|max:255',
        ]);

        $cashDrawer = CashDrawer::find(1);
        $cashDrawer->adjustBalance($request->amount, 'add', $request->note);

        return redirect()->back()->with('success', 'เพิ่มเงินสำเร็จ');
    }

    public function subtractFunds(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:0.01',
            'note' => 'nullable|string|max:255',
        ]);

        $cashDrawer = CashDrawer::find(1);

        if ($cashDrawer->current_balance < $request->amount) {
            return redirect()->back()->with('error', 'ยอดเงินไม่เพียงพอ');
        }

        $cashDrawer->adjustBalance($request->amount, 'subtract', $request->note);

        return redirect()->back()->with('success', 'ถอนเงินสำเร็จ');
    }
}
