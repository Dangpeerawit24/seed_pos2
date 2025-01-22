@extends('layouts.main')

@php
    $manu = 'ประวัติการขาย';
@endphp

@section('content')
    <div class="container mx-auto px-4 py-6">
        <div class=" flex justify-between">
            <h1 class="text-2xl font-bold mb-6">รายละเอียดคำสั่งซื้อ</h1>
        </div>

        <!-- ข้อมูลคำสั่งซื้อ -->
        <div class="bg-white p-6 shadow-md rounded-lg">
            <div class="flex justify-between">
                <p><strong>รหัสคำสั่งซื้อ:</strong> {{ $order->order_number }}</p>
                <div class="hidden md:flex gap-1">
                    <div>
                        <a id="button" href="/admin/orders/{{ $id }}/edit"
                            class="inline-block px-4 py-2 bg-yellow-500 text-white font-semibold rounded hover:bg-yellow-600">
                            แก้ไข
                        </a>
                    </div>
                    <div>
                        @if ($order->payment_method_label === 'ยืมสินค้า')
                            <a id="button" href="/orders/{{ $id }}/print?title=ใบยืมสินค้า" target="_blank"
                                class="inline-block px-4 py-2 bg-blue-500 text-white font-semibold rounded hover:bg-blue-600">
                                พิมพ์บิล
                            </a>
                        @else
                            <a id="button" href="/orders/{{ $id }}/print?title=ใบเสร็จรับเงิน" target="_blank"
                                class="inline-block px-4 py-2 bg-blue-500 text-white font-semibold rounded hover:bg-blue-600">
                                พิมพ์บิล
                            </a>
                        @endif
                    </div>
                    <div>
                        @if ($order->status !== 'cancelled')
                            <form action="{{ route('orders.cancel', $order->id) }}" method="POST"
                                id="orderscancel-{{ $order->id }}">
                                @csrf
                                @method('PATCH')
                                <button type="button"
                                    class="bg-red-500 hover:bg-red-800 text-white font-semibold px-4 py-2 rounded"
                                    onclick="submitCancelForm({{ $order->id }})">
                                    ยกเลิกบิล
                                </button>
                            </form>
                        @else
                            <span class="text-red-500 font-bold">ยกเลิกแล้ว</span><br>
                            <small class="text-gray-500">โดย {{ $order->cancelledBy->name ?? 'ไม่ทราบ' }}</small>
                        @endif
                    </div>
                </div>
            </div>
            <p><strong>วันที่:</strong> {{ $order->formatted_date }}</p>
            <p><strong>ชื่อลูกค้า:</strong>
                @if ($order->membership_id == '1' && $order->customer_name == 'null')
                    -
                @elseif ($order->membership_id == '1' && $order->customer_name !== 'null')
                    {{ $order->customer_name }}
                @elseif ($order->membership_id == '' && $order->customer_name == '')
                    -
                @elseif ($order->membership_id == '' && $order->customer_name !== '')
                    {{ $order->customer_name }}
                @elseif ($order->membership_id && $order->member)
                    {{ $order->member->name }}
                @endif
            </p>
            <p><strong>ยอดรวม:</strong> {{ $order->formatted_total }}</p>
            <p><strong>วิธีการชำระเงิน:</strong> {{ $order->payment_method_label }}</p>
            <p><strong>พนักงานขาย:</strong> {{ $order->user ? $order->user->name : 'ไม่ระบุ' }}</p>
            @if ($order->payment_method_label === 'ออนไลน์')
                <button onclick="openSlipModal('{{ asset($order->proof_image) }}')"
                    class="px-4 py-2 mt-3 bg-blue-500 text-white rounded hover:bg-blue-600">สลิปหลักฐานการโอน</button>
            @endif
            <div class="flex md:hidden space-x-2 mt-3">
                <div>
                    <a id="button" href="/admin/orders/{{ $id }}/edit"
                        class="inline-block px-4 py-2 bg-yellow-500 text-white font-semibold rounded hover:bg-yellow-600">
                        แก้ไข
                    </a>
                </div>
                <div>
                    @if ($order->payment_method_label === 'ยืมสินค้า')
                        <a id="button" href="/orders/{{ $id }}/print?title=ใบยืมสินค้า" target="_blank"
                            class="inline-block px-4 py-2 bg-blue-500 text-white font-semibold rounded hover:bg-blue-600">
                            พิมพ์บิล
                        </a>
                    @else
                        <a id="button" href="/orders/{{ $id }}/print?title=ใบเสร็จรับเงิน" target="_blank"
                            class="inline-block px-4 py-2 bg-blue-500 text-white font-semibold rounded hover:bg-blue-600">
                            พิมพ์บิล
                        </a>
                    @endif
                </div>
                <div>
                    @if ($order->status !== 'cancelled')
                        <form action="{{ route('orders.cancel', $order->id) }}" method="POST"
                            id="orderscancel-{{ $order->id }}">
                            @csrf
                            @method('PATCH')
                            <button type="button"
                                class="bg-red-500 hover:bg-red-800 text-white font-semibold px-4 py-2 rounded"
                                onclick="submitCancelForm({{ $order->id }})">
                                ยกเลิกบิล
                            </button>
                        </form>
                    @else
                        <span class="text-red-500 font-bold">ยกเลิกแล้ว</span><br>
                        <small class="text-gray-500">โดย {{ $order->cancelledBy->name ?? 'ไม่ทราบ' }}</small>
                    @endif
                </div>
            </div>
        </div>

        <!-- รายการสินค้า -->
        <h2 class="text-xl font-bold mt-6 mb-4">รายการสินค้า</h2>
        <div class=" overflow-x-auto">
            <table class="table-auto w-full border-collapse border border-gray-200 bg-white shadow-md">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="border text-nowrap border-gray-300 px-4 py-2 text-left">ชื่อสินค้า</th>
                        @if ($order->payment_method_label !== 'ยืมสินค้า')
                            <th class="border text-nowrap border-gray-300 px-4 py-2 text-right">ราคา (฿)</th>
                        @endif
                        <th class="border text-nowrap border-gray-300 px-4 py-2 text-center">จำนวน</th>
                        @if ($order->payment_method_label !== 'ยืมสินค้า')
                            <th class="border text-nowrap border-gray-300 px-4 py-2 text-right">รวม (฿)</th>
                        @endif
                    </tr>
                </thead>
                <tbody>
                    @foreach ($order->items as $item)
                        <tr>
                            <td class="border text-nowrap border-gray-300 px-4 py-2">
                                {{ $item->product ? $item->product->name : 'สินค้าถูกลบ' }}</td>
                            @if ($order->payment_method_label !== 'ยืมสินค้า')
                                <td class="border text-nowrap border-gray-300 px-4 py-2 text-right">
                                    {{ number_format($item->price, 2) }}</td>
                            @endif
                            <td class="border text-nowrap border-gray-300 px-4 py-2 text-center">{{ $item->quantity }}</td>
                            @if ($order->payment_method_label !== 'ยืมสินค้า')
                                <td class="border text-nowrap border-gray-300 px-4 py-2 text-right">
                                    {{ number_format($item->price * $item->quantity, 2) }}</td>
                            @endif
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- Modal -->
    <div id="slipModal" class="fixed inset-0 px-2 bg-gray-900 bg-opacity-50 flex items-center justify-center hidden">
        <div class="bg-white rounded-lg shadow-lg max-w-md w-full p-4">
            <!-- ปุ่มปิด -->
            <div class="flex justify-end">
                <button onclick="closeSlipModal()" class="text-gray-500 hover:text-gray-700">
                    <i class="fa-solid fa-times"></i> <!-- ไอคอนปิด -->
                </button>
            </div>
            <!-- ภาพสลิป -->
            <div class="mt-2">
                <img id="slipImage" src="" alt="สลิปการโอน" class="rounded-lg w-full">
            </div>
        </div>
    </div>
    <script>
        function openSlipModal(imageUrl) {
            const modal = document.getElementById('slipModal');
            const slipImage = document.getElementById('slipImage');

            // ตั้งค่ารูปภาพใน Modal
            slipImage.src = imageUrl;

            // แสดง Modal
            modal.classList.remove('hidden');
        }

        function closeSlipModal() {
            const modal = document.getElementById('slipModal');

            // ซ่อน Modal
            modal.classList.add('hidden');
        }
    </script>
    <script>
        function submitCancelForm(orderId) {
            Swal.fire({
                title: 'คุณต้องการยกเลิกบิลนี้ใช่หรือไม่?',
                text: "การยกเลิกบิลจะไม่สามารถย้อนกลับได้!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'ใช่, ยกเลิกบิล!',
                cancelButtonText: 'ยกเลิก'
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById(`orderscancel-${orderId}`).submit();
                }
            });
        }
    </script>
@endsection
