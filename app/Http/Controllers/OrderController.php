<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Http\Request;
use App\Models\CashDrawer;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    public function store(Request $request)
    {

        // เริ่ม Transaction
        DB::beginTransaction();

        try {
            // ตรวจสอบข้อมูล

            $validatedData = $request->validate([
                'total_amount'    => 'required|numeric|min:0',
                'payment_method'  => 'required|string|in:cash,qr,borrow',
                'cart'            => 'required', // รับได้ทั้ง JSON String หรือ Array
                'received_amount' => 'nullable|numeric|min:0',
                'change'          => 'nullable|numeric|min:0',
                // 'membership_id' => 'nullable|integer|exists:members,id',
                // 'customer_name'   => 'nullable|string|max:255',
            ]);

            if ($request->membership_id == 'null') {
                $membership_id = '1';
            } else {
                $membership_id = $request->membership_id;
            }

            if ($request->customer_name == 'null') {
                $customer_name = 'null';
            } else {
                $customer_name = $request->customer_name;
            }

            if ($request->payment_method === "cash") {
                // โหลด/สร้าง CashDrawer (ลิ้นชักเก็บเงิน)
                $cashDrawer = CashDrawer::firstOrCreate(['id' => 1], ['current_balance' => 0.00]);

                // แปลง cart จาก JSON String เป็น Array (ถ้าจำเป็น)
                $cart = is_string($request->input('cart'))
                    ? json_decode($request->input('cart'), true)
                    : $request->input('cart');

                if (!$cart || !is_array($cart)) {
                    // ยกเลิก Transaction และส่ง error ออกไป
                    DB::rollBack();
                    return response()->json(['error' => 'Invalid cart data'], 400);
                }

                // บันทึกคำสั่งซื้อ (Order)
                $order = Order::create([
                    'order_number'    => uniqid('ORD-'),
                    'total_amount'    => $validatedData['total_amount'],
                    'payment_method'  => $validatedData['payment_method'],
                    'received_amount' => $validatedData['received_amount'] ?? null,
                    'change'          => $validatedData['change'] ?? null,
                    'proof_image'     => $validatedData['proof_image'] ?? null,
                    'membership_id'  => $membership_id,
                    'customer_name'  => $customer_name,
                    'user_id'         => auth()->id(),
                ]);

                // ตรวจสอบสต็อกและบันทึกรายการสินค้า
                foreach ($cart as $item) {
                    // ดึง Product แบบ lockForUpdate() กัน race condition
                    $product = Product::where('id', $item['id'])->lockForUpdate()->first();

                    if (!$product) {
                        // ไม่พบสินค้า ยกเลิก Transaction
                        DB::rollBack();
                        return response()->json(['error' => 'ไม่พบสินค้า ID: ' . $item['id']], 404);
                    }

                    // ตรวจสอบสต็อก
                    if ($product->stock_quantity < $item['quantity']) {
                        // สต็อกไม่พอ ยกเลิก Transaction
                        DB::rollBack();
                        return response()->json([
                            'error' => 'สินค้าไม่เพียงพอ: ' . $product->name . ' (คงเหลือ ' . $product->stock_quantity . ')'
                        ], 400);
                    }

                    // ตัดสต็อก
                    $product->decrement('stock_quantity', $item['quantity']);

                    // บันทึกรายการใน OrderItem
                    OrderItem::create([
                        'order_id'  => $order->id,
                        'product_id' => $item['id'],
                        'price'     => $item['price'],
                        'quantity'  => $item['quantity'],
                    ]);
                }

                // ถ้าชำระด้วย 'cash' ให้บวกยอดลง CashDrawer
                if ($validatedData['payment_method'] === 'cash') {
                    $cashDrawer->adjustBalance(
                        $validatedData['total_amount'],
                        'add',
                        'ยอดขายจากคำสั่งซื้อ #' . $order->order_number
                    );
                }

                // ทุกอย่างสำเร็จ จึง commit
                DB::commit();

                return response()->json([
                    'message' => 'บันทึกคำสั่งซื้อสำเร็จ',
                    'order'   => $order,
                ]);
            } else if ($request->payment_method === "qr") {

                $cart = is_string($request->input('cart'))
                    ? json_decode($request->input('cart'), true)
                    : $request->input('cart');

                if (!$cart || !is_array($cart)) {
                    // ยกเลิก Transaction และส่ง error ออกไป
                    DB::rollBack();
                    return response()->json(['error' => 'Invalid cart data'], 400);
                }

                // บันทึกหลักฐานการโอนเงิน (ถ้ามี)
                if ($request->hasFile('proof_image')) {
                    $file = $request->file('proof_image');
                    $fileName = time() . '.' . $request->proof_image->extension();
                    $file->move(public_path('img/proof_image/'), $fileName);
                    $validatedData['proof_image'] = 'img/proof_image/' . $fileName;
                }

                // บันทึกคำสั่งซื้อ (Order)
                $order = Order::create([
                    'order_number'    => uniqid('ORD-'),
                    'total_amount'    => $validatedData['total_amount'],
                    'payment_method'  => $validatedData['payment_method'],
                    'received_amount' => $validatedData['received_amount'] ?? null,
                    'change'          => $validatedData['change'] ?? null,
                    'proof_image'     => $validatedData['proof_image'] ?? null,
                    'membership_id'  => $membership_id,
                    'customer_name'  => $customer_name,
                    'user_id'         => auth()->id(),
                ]);

                // ตรวจสอบสต็อกและบันทึกรายการสินค้า
                foreach ($cart as $item) {
                    // ดึง Product แบบ lockForUpdate() กัน race condition
                    $product = Product::where('id', $item['id'])->lockForUpdate()->first();

                    if (!$product) {
                        // ไม่พบสินค้า ยกเลิก Transaction
                        DB::rollBack();
                        return response()->json(['error' => 'ไม่พบสินค้า ID: ' . $item['id']], 404);
                    }

                    // ตรวจสอบสต็อก
                    if ($product->stock_quantity < $item['quantity']) {
                        // สต็อกไม่พอ ยกเลิก Transaction
                        DB::rollBack();
                        return response()->json([
                            'error' => 'สินค้าไม่เพียงพอ: ' . $product->name . ' (คงเหลือ ' . $product->stock_quantity . ')'
                        ], 400);
                    }

                    // ตัดสต็อก
                    $product->decrement('stock_quantity', $item['quantity']);

                    // บันทึกรายการใน OrderItem
                    OrderItem::create([
                        'order_id'  => $order->id,
                        'product_id' => $item['id'],
                        'price'     => $item['price'],
                        'quantity'  => $item['quantity'],
                    ]);
                }

                // ทุกอย่างสำเร็จ จึง commit
                DB::commit();

                return response()->json([
                    'message' => 'บันทึกคำสั่งซื้อสำเร็จ',
                    'order'   => $order,
                ]);
            } else if ($request->payment_method === "borrow") {

                // แปลง cart จาก JSON String เป็น Array (ถ้าจำเป็น)
                $cart = is_string($request->input('cart'))
                    ? json_decode($request->input('cart'), true)
                    : $request->input('cart');

                if (!$cart || !is_array($cart)) {
                    // ยกเลิก Transaction และส่ง error ออกไป
                    DB::rollBack();
                    return response()->json(['error' => 'Invalid cart data'], 400);
                }

                // บันทึกคำสั่งซื้อ (Order)
                $order = Order::create([
                    'order_number'    => uniqid('ORD-'),
                    'total_amount'    => $validatedData['total_amount'],
                    'payment_method'  => $validatedData['payment_method'],
                    'membership_id'  => $membership_id,
                    'customer_name'  => $customer_name,
                    'user_id'         => auth()->id(),
                ]);

                // ตรวจสอบสต็อกและบันทึกรายการสินค้า
                foreach ($cart as $item) {
                    // ดึง Product แบบ lockForUpdate() กัน race condition
                    $product = Product::where('id', $item['id'])->lockForUpdate()->first();

                    if (!$product) {
                        // ไม่พบสินค้า ยกเลิก Transaction
                        DB::rollBack();
                        return response()->json(['error' => 'ไม่พบสินค้า ID: ' . $item['id']], 404);
                    }

                    // ตรวจสอบสต็อก
                    if ($product->stock_quantity < $item['quantity']) {
                        // สต็อกไม่พอ ยกเลิก Transaction
                        DB::rollBack();
                        return response()->json([
                            'error' => 'สินค้าไม่เพียงพอ: ' . $product->name . ' (คงเหลือ ' . $product->stock_quantity . ')'
                        ], 400);
                    }

                    // ตัดสต็อก
                    $product->decrement('stock_quantity', $item['quantity']);

                    // บันทึกรายการใน OrderItem
                    OrderItem::create([
                        'order_id'  => $order->id,
                        'product_id' => $item['id'],
                        'price'     => $item['price'],
                        'quantity'  => $item['quantity'],
                    ]);
                }

                // ทุกอย่างสำเร็จ จึง commit
                DB::commit();

                return response()->json([
                    'message' => 'บันทึกคำสั่งซื้อสำเร็จ',
                    'order'   => $order,
                ]);
            }
        } catch (\Exception $e) {
            // ถ้ามี Error ใด ๆ ให้ RollBack และส่งข้อความ error ออกไป
            DB::rollBack();
            return response()->json([
                'error' => 'เกิดข้อผิดพลาด: ' . $e->getMessage()
            ], 500);
        }
    }

    public function print(Order $order)
    {
        // โหลดความสัมพันธ์ (เช่น order items, product) ให้พร้อม
        // สมมติใน Order model มีเมธอด ->items() = hasMany(OrderItem::class)
        $order->load([
            'items.product',
            'member' // <-- สมมติคุณตั้งชื่อเมธอดว่า member()
        ]);

        return view('orders.print', compact('order'));
    }


    public function salesHistory(Request $request)
    {
        $status = $request->status;

        if ($status) {
            $orders = \App\Models\Order::with('items')->where('status', $status)->orderBy('created_at', 'desc')->paginate(100);
        } else if (Auth::user()->type === 'staff') {
            $orders = \App\Models\Order::with('items')
                ->where('user_id', auth()->id()) // Removed the space after 'user_id'
                ->orderBy('created_at', 'desc')
                ->paginate(100);
        } else {
            $orders = \App\Models\Order::with('items')->orderBy('created_at', 'desc')->paginate(100);
        }

        if (Auth::user()->type === 'admin') {
            return view('admin.saleshistory', compact('orders'));
        } elseif (Auth::user()->type === 'staff') {
            return view('staff.saleshistory', compact('orders'));
        }
        return view('home');
    }

    public function salesDetail($id)
    {
        // ดึงคำสั่งซื้อพร้อมรายการสินค้า
        $order = \App\Models\Order::with('items')->findOrFail($id);
        $id = $id;

        // ส่งตัวแปร $order ไปยัง View
        if (Auth::user()->type === 'admin') {
            return view('admin.salesdetail', compact('order', 'id'));
        } elseif (Auth::user()->type === 'staff') {
            return view('staff.salesdetail', compact('order', 'id'));
        }
        return view('home');
    }

    public function salesDetail2($orderNumber)
    {
        // ค้นหาคำสั่งซื้อด้วย order_number
        $order = Order::with('items')->where('order_number', $orderNumber)->firstOrFail();

        if (Auth::user()->type === 'admin') {
            return view('admin.salesdetail', compact('order'));
        } elseif (Auth::user()->type === 'staff') {
            return view('staff.salesdetail', compact('order'));
        }
        return view('home');
    }

    public function edit(Order $order)
    {
        // $order->load('items.product'); // โหลด order + items
        // ต้องโหลด products เพื่อนำไป select
        $products = Product::all();

        return view('orders.edit', compact('order', 'products'));
    }



    public function update(Request $request, Order $order)
    {
        DB::beginTransaction();
        try {
            // 1) คืนสต็อกเก่า
            foreach ($order->items as $oldItem) {
                $product = Product::find($oldItem->product_id);
                if ($product) {
                    $product->stock_quantity += $oldItem->quantity;
                    $product->save();
                }
            }

            // 2) ลบ order items เดิมทั้งหมด
            $order->items()->delete();

            // 3) เพิ่มรายการใหม่
            $total = 0;

            $items = $request->input('items', []);
            foreach ($items as $itemData) {
                // ถ้ามี _remove == 1 ก็ข้าม
                if (!empty($itemData['_remove'])) {
                    continue;
                }

                $product = Product::find($itemData['product_id']);
                // ตัดสต็อก
                $product->stock_quantity -= $itemData['quantity'];
                $product->save();

                $subTotal = $itemData['price'] * $itemData['quantity'];
                $total += $subTotal;

                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $itemData['product_id'],
                    'price' => $itemData['price'],
                    'quantity' => $itemData['quantity'],
                    'sub_total' => $subTotal,
                ]);
            }

            // 4) อัปเดตยอดรวม
            $order->update(['total_amount' => $total]);

            DB::commit();
            return redirect()->back()->with('success', 'แก้ไขบิลเรียบร้อย');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'แก้ไขบิลไม่สำเร็จ');
        }
    }

    public function completedOrder($id)
    {
        $order = \App\Models\Order::with('items')->findOrFail($id);

        if ($order->payment_method !== "borrow") {
            $order->update([
                'status' => 'completed',
            ]);

            return redirect()->back()->with('success', 'บิลถูกยืนยันเรียบร้อยแล้ว');
        } else if ($order->payment_method === "borrow") {
            $order->update([
                'status' => 'borrow',
            ]);
            return redirect()->back()->with('success', 'บิลถูกยืนยันเรียบร้อยแล้ว');
        }

        return redirect()->back()->with('error', 'บิลไม่ถูกยืนยัน');
    }

    public function rebateOrder($id)
    {
        DB::beginTransaction();
        try {
            $order = Order::with('items')->findOrFail($id);

            // เช็คว่าเป็นบิลแบบ "borrow" หรือไม่
            if ($order->payment_method !== 'borrow') {
                // ถ้าไม่ใช่ borrow ก็ยกเลิก Transaction และแจ้ง error
                DB::rollBack();
                return redirect()->back()->with('error', 'บิลไม่ถูกยืนยัน');
            }

            // 1) อัปเดตสถานะเป็น 'rebate'
            $order->update([
                'status' => 'rebate',
            ]);

            // 2) คืนสต็อกทุก item ใน order นี้
            foreach ($order->items as $oldItem) {
                $product = Product::find($oldItem->product_id);
                if ($product) {
                    $product->stock_quantity += $oldItem->quantity; // คืน stock
                    $product->save();
                }
            }

            // 3) ลบ order items ออกจาก DB (เพราะคืนของแล้ว)
            // $order->items()->delete();

            // ทุกอย่างผ่าน -> commit!
            DB::commit();
            return redirect()->back()->with('success', 'การคืนสินค้ายืนยันเรียบร้อยแล้ว');
        } catch (\Exception $e) {
            DB::rollBack();
            // เกิด Error กลางคัน -> ยกเลิกทั้งหมด
            return redirect()->back()->with('error', 'เกิดข้อผิดพลาด: ' . $e->getMessage());
        }
    }

    public function rebateMoneyOrder($id)
    {
        DB::beginTransaction();
        try {
            $order = Order::with('items')->findOrFail($id);

            // เช็คว่าเป็นบิลแบบ "borrow" หรือไม่
            if ($order->payment_method !== 'borrow') {
                // ถ้าไม่ใช่ borrow ก็ยกเลิก Transaction และแจ้ง error
                DB::rollBack();
                return redirect()->back()->with('error', 'บิลไม่ถูกยืนยัน');
            }

            // 1) อัปเดตสถานะเป็น 'rebate'
            $order->update([
                'status' => 'rebateMoney',
            ]);


            // ทุกอย่างผ่าน -> commit!
            DB::commit();
            return redirect()->back()->with('success', 'การคืนสินค้ายืนยันเรียบร้อยแล้ว');
        } catch (\Exception $e) {
            DB::rollBack();
            // เกิด Error กลางคัน -> ยกเลิกทั้งหมด
            return redirect()->back()->with('error', 'เกิดข้อผิดพลาด: ' . $e->getMessage());
        }
    }

    public function cancelOrder($id)
    {
        $order = \App\Models\Order::with('items')->findOrFail($id);

        // ตรวจสอบสถานะปัจจุบันก่อนยกเลิก
        if ($order->status === 'cancelled') {
            return redirect()->back()->with('error', 'บิลนี้ถูกยกเลิกไปแล้ว');
        }

        // ตรวจสอบว่าชำระด้วยเงินสด
        if ($order->payment_method === 'cash') {
            // อัปเดตกล่องเงินสด (คืนเงินกลับเข้าระบบ)
            $cashDrawer = \App\Models\CashDrawer::firstOrCreate(['id' => 1]); // ตรวจสอบหรือสร้างกล่องเงินสด
            $cashDrawer->adjustBalance($order->total_amount, 'refund', "คืนเงินสำหรับคำสั่งซื้อ #{$order->order_number}");
        }

        // อัปเดตสถานะคำสั่งซื้อเป็น 'cancelled'
        $order->update([
            'status' => 'cancelled',
            'cancelled_by' => auth()->id(),
        ]);

        // คืนสินค้าเข้าสต็อก
        foreach ($order->items as $item) {
            $product = \App\Models\Product::find($item->product_id);
            if ($product) {
                $product->increment('stock_quantity', $item->quantity); // เพิ่มสินค้ากลับเข้าสต็อก
            }
        }

        return redirect()->back()->with('success', 'บิลถูกยกเลิกและเงินถูกคืนเรียบร้อยแล้ว');
    }
}
