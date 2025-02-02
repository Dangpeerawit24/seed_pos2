@extends('layouts.main')
@php
    $manu = 'แดชบอร์ด';
@endphp
@section('content')
    <div class="container mx-auto p-6">
        <h1 class="text-3xl font-bold mb-6">Dashboard</h1>

        <!-- สถิติรวม -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div class="p-4 bg-white shadow rounded-lg">
                <h2 class="text-lg font-semibold">ยอดขายวันนี้</h2>
                <div class="flex flex-col ">
                    <p class="text-2xl font-bold text-green-500">฿{{ number_format($salesToday, 2) }}</p>
                    <a class=" text-blue-600 hover:underline text-sm"
                        href="/admin/today-product-sales">ดูรายละเอียด</a>
                </div>
            </div>
            <div class="p-4 bg-white shadow rounded-lg">
                <h2 class="text-lg font-semibold">ยอดขายรวม(เดือนนี้)</h2>
                <div class="flex flex-col ">
                    <p class="text-2xl font-bold text-green-500">฿{{ number_format($totalSales, 2) }}</p>
                    <a class=" text-blue-600 hover:underline text-sm"
                        href="/admin/month-product-sales">ดูรายละเอียด</a>
                </div>
            </div>
            <div class="p-4 bg-white shadow rounded-lg">
                <h2 class="text-lg font-semibold">รอการตรวจสอบ</h2>
                <div class="flex flex-col ">
                    <p class="text-2xl font-bold">{{ $totalOrders }}</p>
                    <a class=" text-blue-600 hover:underline text-sm"
                        href="/admin/sales-history?status=pending">ดูรายละเอียด</a>
                </div>
            </div>
            <div class="p-4 bg-white shadow rounded-lg">
                <h2 class="text-lg font-semibold">สินค้ารอคืน</h2>
                <div class="flex flex-col ">
                    <p class="text-2xl font-bold text-red-500">{{ $borrowOrders }}</p>
                    <a class=" text-blue-600 hover:underline text-sm"
                        href="/admin/sales-history?status=borrow">ดูรายละเอียด</a>
                </div>
            </div>
            <div class="p-4 bg-white shadow rounded-lg">
                <h2 class="text-lg font-semibold">รายการขอปรับสต็อก</h2>
                <div class="flex flex-col ">
                    <p class="text-2xl font-bold text-red-500">{{ $pendingStock->count() }}</p>
                    <a class=" text-blue-600 hover:underline text-sm"
                        href="/admin/stock_review">ดูรายละเอียด</a>
                </div>
            </div>
            <div class="p-4 bg-white shadow rounded-lg">
                <h2 class="text-lg font-semibold">สินค้าใกล้หมด</h2>
                <div class="flex flex-col ">
                    <p class="text-2xl font-bold text-red-500">{{ $lowStockProducts->count() }}</p>
                    <a class=" text-blue-600 hover:underline text-sm"
                        href="/admin/stock?restock=1">ดูรายละเอียด</a>
                </div>
            </div>
        </div>
        <div class="mt-6">
            <h2 class="text-xl font-bold mb-4">ยอดขายสินค้ารวม</h2>
            <div class="mb-2">
                <label for="filterDate" class="block text-sm font-medium text-gray-700">เลือกช่วงเวลา:</label>
                <select id="filterDate" class="w-full px-4 py-2 border rounded-lg" onchange="filterSales()">
                    <option value="today" selected>วันนี้</option>
                    <option value="all">ทั้งหมด</option>
                </select>
            </div>
            <div id="salesResult">
                <!-- แสดงผลยอดขายที่นี่ -->
            </div>
        </div>
        <!-- สินค้าใกล้หมดสต็อก -->
        <div class="mt-6">
            <h2 class="text-xl font-bold mb-4">สินค้าใกล้หมดสต็อก</h2>
            <div class="overflow-x-auto">
                <table class="table-auto w-full border-collapse border border-gray-200 bg-white shadow-md">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="border border-gray-300 px-4 py-2 text-left">ชื่อสินค้า</th>
                            <th class="border border-gray-300 px-4 py-2 text-right">จำนวนคงเหลือ</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($lowStockProducts as $product)
                            <tr>
                                <td class="border border-gray-300 px-4 py-2">{{ $product->name }}</td>
                                <td class="border border-gray-300 px-4 py-2 text-right">{{ $product->stock_quantity }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="2" class="border border-gray-300 px-4 py-2 text-center">
                                    ไม่มีสินค้าใกล้หมดสต็อก</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- การอัปเดตสต็อกล่าสุด -->
        <div class="mt-6">
            <h2 class="text-xl font-bold mb-4">การอัปเดตสต็อกล่าสุด</h2>
            <div class="overflow-x-auto">
                <table class="table-auto w-full border-collapse border border-gray-200 bg-white shadow-md">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="border text-nowrap border-gray-300 px-4 py-2 text-left">สินค้า</th>
                            <th class="border text-nowrap border-gray-300 px-4 py-2 text-left">การดำเนินการ</th>
                            <th class="border text-nowrap border-gray-300 px-4 py-2 text-right">จำนวน</th>
                            <th class="border text-nowrap border-gray-300 px-4 py-2 text-left">ผู้ดำเนินการ</th>
                            <th class="border text-nowrap border-gray-300 px-4 py-2 text-left">เวลา</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recentStockMovements as $movement)
                            <tr>
                                <td class="border text-nowrap border-gray-300 px-4 py-2">{{ $movement->product->name }}</td>
                                <td class="border text-nowrap border-gray-300 px-4 py-2">
                                    {{ $movement->type === 'in' ? 'เพิ่ม' : 'ลด' }}</td>
                                <td class="border text-nowrap border-gray-300 px-4 py-2 text-right">
                                    {{ $movement->quantity }}</td>
                                <td class="border text-nowrap border-gray-300 px-4 py-2">
                                    {{ $movement->user->name ?? 'ไม่ระบุ' }}</td>
                                <td class="border text-nowrap border-gray-300 px-4 py-2">
                                    {{ $movement->created_at->format('d/m/Y H:i') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="border border-gray-300 px-4 py-2 text-center">
                                    ไม่มีการอัปเดตสต็อกล่าสุด</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script>
        window.onload = function() {
            filterSales();
        };


        function filterSales() {
            const filterDate = document.getElementById("filterDate").value;

            fetch(`/sales/filter?date=${filterDate}`, {
                    method: 'GET',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    }
                })
                .then(response => response.json())
                .then(data => {
                    const salesResult = document.getElementById("salesResult");
                    salesResult.innerHTML = `
                <table class="table-auto w-full border-collapse border border-gray-200 bg-white shadow-md">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="border px-4 py-2">ชื่อสินค้า</th>
                            <th class="border px-4 py-2 text-right">จำนวนที่ขาย</th>
                        </tr>
                    </thead>
                    <tbody>
                        ${data.map(item => `
                                        <tr>
                                            <td class="border px-4 py-2">${item.name}</td>
                                            <td class="border px-4 py-2 text-right">${item.total_quantity}</td>
                                        </tr>
                                    `).join('')}
                    </tbody>
                </table>
            `;
                })
                .catch(error => console.error("Error fetching sales data:", error));
        }
    </script>
@endsection
