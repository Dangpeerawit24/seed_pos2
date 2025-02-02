@extends('layouts.main')

@php
    $manu = 'ประวัติการขาย';
@endphp

@section('content')
    <div class="container mx-auto px-4 py-6">
        <h1 class="text-2xl font-bold mb-6">ประวัติการขาย</h1>
        <div class=" overflow-auto">
            <!-- ส่วนตาราง: แสดงเฉพาะหน้าจอ md ขึ้นไป -->
            <div class="hidden md:block">
                <table class="table-auto w-full border-collapse border border-gray-200 bg-white shadow-md">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="border text-nowrap border-gray-300 px-4 py-2 text-left">รหัสคำสั่งซื้อ</th>
                            <th class="border text-nowrap border-gray-300 px-4 py-2 text-left">วันที่</th>
                            <th class="border text-nowrap border-gray-300 px-4 py-2 text-right">ยอดรวม (฿)</th>
                            <th class="border text-nowrap border-gray-300 px-4 py-2 text-center">วิธีการชำระเงิน</th>
                            <th class="border text-nowrap border-gray-300 px-4 py-2 text-center">สถานะ</th>
                            <th class="border text-nowrap border-gray-300 px-4 py-2 text-center">รายละเอียด</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($orders as $order)
                            <tr>
                                <td class="border text-nowrap border-gray-300 px-4 py-2">
                                    {{ $order->order_number }}
                                </td>
                                <td class="border text-nowrap border-gray-300 px-4 py-2">
                                    {{ $order->created_at->format('d/m/') . ($order->created_at->format('Y') + 543) . $order->created_at->format(' H:i') }}
                                </td>
                                <td class="border text-nowrap border-gray-300 px-4 py-2 text-right">
                                    {{ number_format($order->total_amount, 2) }}
                                </td>
                                <td class="border text-nowrap border-gray-300 px-4 py-2 text-center">
                                    @if ($order->payment_method === 'cash')
                                        เงินสด
                                    @elseif ($order->payment_method === 'borrow')
                                        ยืมสินค้า
                                    @else
                                        โอนเข้าบัญชี
                                    @endif
                                </td>
                                <td class="border text-nowrap border-gray-300 px-4 py-2 text-center">
                                    <!-- ตัวอย่างเงื่อนไขสถานะ -->
                                    @if ($order->status === 'pending')
                                        <form action="{{ route('orders.completed', $order->id) }}" method="POST"
                                            id="orderscancel-{{ $order->id }}">
                                            @csrf
                                            @method('PATCH')
                                            <button type="button"
                                                class="bg-yellow-500 hover:bg-yellow-800 text-white font-semibold px-4 py-2 rounded"
                                                onclick="submitCancelForm({{ $order->id }})" disabled>
                                                รอตรวจสอบ
                                            </button>
                                        </form>
                                    @elseif ($order->status === 'borrow')
                                        <form action="" method="POST" id="changeActionForm">
                                            @csrf
                                            @method('PATCH')
                                            <button type="button"
                                                class="bg-blue-500 hover:bg-blue-800 text-white font-semibold px-4 py-2 rounded"
                                                onclick="chooseAction({{ $order->id }})" disabled>
                                                ยืมสินค้า
                                            </button>
                                        </form>
                                    @elseif ($order->status === 'completed')
                                        <button type="button"
                                            class="bg-green-500 text-white font-semibold px-4 py-2 rounded" disabled>
                                            ยืนยันรายการ
                                        </button>
                                    @elseif ($order->status === 'rebate')
                                        <button type="button"
                                            class="bg-green-500 text-white font-semibold px-4 py-2 rounded" disabled>
                                            คืนสินค้าแล้ว
                                        </button>
                                    @elseif ($order->status === 'rebateMoney')
                                        <button type="button"
                                            class="bg-green-500 text-white font-semibold px-4 py-2 rounded" disabled>
                                            คืนสินค้าเป็นเงินแล้ว
                                        </button>
                                    @else
                                        <button type="button" class="bg-red-500 text-white font-semibold px-4 py-2 rounded"
                                            disabled>
                                            ยกเลิกแล้ว
                                        </button>
                                    @endif
                                </td>
                                <td class="border text-nowrap border-gray-300 px-4 py-2 text-center">
                                    <a href="{{ route('staff.sales.detail', $order->id) }}" class="text-blue-500 hover:underline">
                                        ดูรายละเอียด
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center text-gray-500 py-4">
                                    ไม่มีข้อมูลคำสั่งซื้อ
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- ส่วน Card Layout: แสดงเฉพาะบนหน้าจอเล็ก (md:hidden) -->
            <div class="md:hidden space-y-4">
                @forelse($orders as $order)
                    <!-- กล่องแต่ละคำสั่งซื้อ -->
                    <div class="bg-white shadow-md p-4 rounded border border-gray-200">
                        <div class="flex items-center justify-between mb-2">
                            <div>
                                <span class="font-semibold text-blue-600">#{{ $order->order_number }}</span>
                                <span class="text-sm text-gray-500 ml-2">
                                    {{ $order->created_at->format('d/m/') . ($order->created_at->format('Y') + 543) . $order->created_at->format(' H:i') }}
                                </span>
                            </div>
                        </div>

                        <!-- แสดงข้อมูลอื่น -->
                        <div class="text-gray-700 text-sm">
                            @if ($order->payment_method === 'cash')
                                <p>วิธีชำระ: เงินสด</p>
                            @elseif ($order->payment_method === 'borrow')
                                <p>วิธีชำระ: ยืมสินค้า</p>
                            @else
                                <p>วิธีชำระ: โอนเข้าบัญชี</p>
                            @endif
                            <p>ยอดรวม: {{ number_format($order->total_amount, 2) }} ฿</p>
                        </div>

                        <!-- ปุ่ม action -->
                        <div class="mt-2">
                            @if ($order->status === 'pending')
                                <form action="{{ route('orders.completed', $order->id) }}" method="POST"
                                    id="orderscancel-{{ $order->id }}">
                                    @csrf
                                    @method('PATCH')
                                    <button type="button"
                                        class="bg-yellow-500 hover:bg-yellow-800 text-white font-semibold px-3 py-1 rounded"
                                        onclick="submitCancelForm({{ $order->id }})" disabled>
                                        รอตรวจสอบ
                                    </button>
                                </form>
                            @elseif ($order->status === 'borrow')
                                <form action="" method="POST" id="changeActionForm">
                                    @csrf
                                    @method('PATCH')
                                    <button type="button"
                                        class="bg-blue-500 hover:bg-blue-800 text-white font-semibold px-4 py-2 rounded"
                                        onclick="chooseAction({{ $order->id }})" disabled>
                                        คืนสินค้า
                                    </button>
                                </form>
                            @elseif ($order->status === 'completed')
                                <button type="button" class="bg-green-500 text-white font-semibold px-3 py-1 rounded"
                                    disabled>
                                    ยืนยันรายการ
                                </button>
                            @else
                                <button type="button" class="bg-green-500 text-white font-semibold px-3 py-1 rounded"
                                    disabled>
                                    คืนสินค้าแล้ว
                                </button>
                            @endif

                            <!-- ลิงก์รายละเอียด -->
                            <a href="{{ route('staff.sales.detail', $order->id) }}"
                                class="ml-2 text-blue-600 hover:underline text-sm">
                                ดูรายละเอียด
                            </a>
                        </div>
                    </div>
                @empty
                    <p class="text-center text-gray-500">ไม่มีข้อมูลคำสั่งซื้อ</p>
                @endforelse
            </div>

        </div>
        <div class="mt-6">
            {{ $orders->links() }}
        </div>
    </div>
    <script>
        function chooseAction(orderId) {
            Swal.fire({
                title: 'คุณต้องการทำอะไร?',
                text: "เลือกการกระทำที่ต้องการกับบิลนี้",
                icon: 'question',
                showCancelButton: false, // ไม่ต้องการปุ่ม Cancel
                showDenyButton: true, // เปิดปุ่ม Deny
                confirmButtonText: 'คืนสินค้า',
                denyButtonText: 'คืนเป็นเงิน',
                // ตั้งสีปุ่มได้ ถ้าต้องการ
                confirmButtonColor: '#3b82f6', // น้ำเงิน
                denyButtonColor: '#16a34a', // เขียว
            }).then((result) => {
                // isConfirmed = กดปุ่ม "confirmButtonText"
                // isDenied = กดปุ่ม "denyButtonText"
                if (result.isConfirmed) {
                    // เลือก "คืนสินค้า"
                    submitForm(orderId, 'rebate');
                } else if (result.isDenied) {
                    // เลือก "ยืนยันบิล"
                    submitForm(orderId, 'completed');
                }
            });
        }

        // ฟังก์ชันเปลี่ยน action ของฟอร์ม แล้ว submit
        function submitForm(orderId, actionType) {
            const form = document.getElementById('changeActionForm');

            // กำหนด action ตาม actionType
            if (actionType === 'rebate') {
                // สมมติเส้นทาง Route = orders.rebate
                form.action = `/admin/orders/${orderId}/rebate`;
            } else if (actionType === 'completed') {
                // สมมติเส้นทาง Route = orders.completed
                form.action = `/admin/orders/${orderId}/rebateMoneyOrder`;
            }

            // ส่งฟอร์ม
            form.submit();
        }
    </script>
    <script>
        function submitCancelForm(orderId) {
            Swal.fire({
                title: 'คุณต้องการยืนยันบิลนี้ใช่หรือไม่?',
                text: "การยืนยันบิลจะไม่สามารถย้อนกลับได้!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#0b7805',
                cancelButtonColor: '#d33',
                confirmButtonText: 'ใช่, ยืนยันบิล!',
                cancelButtonText: 'ยกเลิก'
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById(`orderscancel-${orderId}`).submit();
                }
            });
        }
    </script>
@endsection
