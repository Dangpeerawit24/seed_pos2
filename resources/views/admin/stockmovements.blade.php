@extends('layouts.main')
@php
    $manu = 'จัดการสต็อกสินค้า';
@endphp

@section('content')
    <div class="container mx-auto px-4 py-6">
        <h1 class="text-2xl font-bold mb-6 text-center sm:text-left">การเคลื่อนไหวของสต็อกสินค้า</h1>

        <!-- รายละเอียดสินค้า -->
        <div class="bg-white shadow-md rounded-lg p-4 mb-6">
            <h2 class="text-lg sm:text-xl font-semibold text-center sm:text-left">สินค้า: {{ $product->name }}</h2>
            <div class="text-gray-600 text-center sm:text-left mt-2">
                <p>จำนวนสต็อกปัจจุบัน: <span class="font-bold">{{ $product->stock_quantity }}</span></p>
                <p>ประเภทสินค้า: <span class="font-bold">{{ $product->category ?? 'ไม่ระบุ' }}</span></p>
            </div>
        </div>

        <!-- ตารางแสดงการเคลื่อนไหว -->
        <div class="hidden md:block bg-white shadow-md rounded-lg p-4">
            <div class="overflow-x-auto">
                <table class="table-auto w-full border-collapse border border-gray-200 text-sm sm:text-base">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="border border-gray-300 px-3 py-2 text-left">วันที่</th>
                            <th class="border border-gray-300 px-3 py-2 text-left">ประเภท</th>
                            <th class="border border-gray-300 px-3 py-2 text-right">จำนวน</th>
                            <th class="border border-gray-300 px-3 py-2 text-left">หมายเหตุ</th>
                            <th class="border border-gray-300 px-3 py-2 text-left">ผู้ดำเนินการ</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($stockMovements as $movement)
                            <tr class="hover:bg-gray-50">
                                <td class="border border-gray-300 px-3 py-2">
                                    {{ $movement->created_at->format('d/m/') . ($movement->created_at->format('Y') + 543) . $movement->created_at->format(' H:i') }}</td>
                                <td class="border border-gray-300 px-3 py-2">
                                    @if ($movement->type === 'in')
                                        <span class="text-green-500 font-bold">เพิ่ม</span>
                                    @elseif($movement->type === 'out')
                                        <span class="text-red-500 font-bold">ลด</span>
                                    @endif
                                </td>
                                <td class="border border-gray-300 px-3 py-2 text-right">{{ $movement->quantity }}</td>
                                <td class="border border-gray-300 px-3 py-2">{{ $movement->note ?? '-' }}</td>
                                <td class="border border-gray-300 px-3 py-2">{{ $movement->user->name ?? 'ไม่ระบุ' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="border border-gray-300 px-3 py-2 text-center text-gray-500">
                                    ไม่มีข้อมูลการเคลื่อนไหวของสต็อก
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <div class="md:hidden space-y-4">
            @forelse($stockMovements as $movement)
                <!-- กล่องแต่ละคำสั่งซื้อ -->
                <div class="bg-white shadow-md p-4 rounded border border-gray-200">
                    <div class="flex items-center justify-between mb-2">
                        <div>
                            <span
                                class="font-semibold text-blue-600">{{ $movement->created_at->format('d/m/') . ($movement->created_at->format('Y') + 543) . $movement->created_at->format(' H:i') }}</span>
                            @if ($movement->type === 'in')
                                <span class="text-green-500 font-bold">เพิ่ม</span>
                            @elseif($movement->type === 'out')
                                <span class="text-red-500 font-bold">ลด</span>
                            @endif
                        </div>
                    </div>

                    <!-- แสดงข้อมูลอื่น -->
                    <div class="text-gray-700 text-sm">
                        <p>จำนวน : {{ $movement->quantity }}</p>
                        <p class=" text-wrap">หมายเหตุ : {{ $movement->note ?? '-' }}</p>
                        <p>ผู้ดำเนินการ : {{ $movement->user->name ?? 'ไม่ระบุ' }} </p>
                    </div>
                </div>
            @empty
                <p class="text-center text-gray-500">ไม่มีข้อมูลคำสั่งซื้อ</p>
            @endforelse
        </div>
    </div>
@endsection
