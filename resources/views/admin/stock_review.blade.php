@extends('layouts.main')

@php
    $manu = 'จัดการสต็อกสินค้า';
@endphp

@section('content')
    <div class="container mx-auto px-4 py-6">
        <h1 class="text-2xl font-bold mb-6">จัดการคำขอปรับสต็อกสินค้า</h1>

        <!-- 📌 Table for Desktop View -->
        <div class="hidden md:block">
            <table class="table-auto w-full border-collapse border border-gray-200 bg-white shadow-md">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="border border-gray-300 px-4 py-2 text-left">ชื่อสินค้า</th>
                        <th class="border border-gray-300 px-4 py-2 text-left">ส่งคำขอโดย</th>
                        <th class="border border-gray-300 px-4 py-2 text-right">จำนวน</th>
                        <th class="border border-gray-300 px-4 py-2 text-center">ประเภท</th>
                        <th class="border border-gray-300 px-4 py-2 text-center">หมายเหตุ</th>
                        <th class="border border-gray-300 px-4 py-2 text-center">ดำเนินการ</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($movements as $movement)
                        <tr>
                            <td class="border border-gray-300 px-4 py-2">{{ $movement->product->name }}</td>
                            <td class="border border-gray-300 px-4 py-2">{{ $movement->user->name }}</td>
                            <td class="border border-gray-300 px-4 py-2 text-right">{{ $movement->quantity }}</td>
                            <td class="border border-gray-300 px-4 py-2 text-center">{{ $movement->operation == 'add' ? 'เพิ่ม' : 'ลด' }}</td>
                            <td class="border border-gray-300 px-4 py-2 text-center">{{ $movement->note }}</td>
                            <td class="border border-gray-300 px-4 py-2 text-center">
                                <form action="{{ route('stock.approve', $movement->id) }}" method="POST" class="inline">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit"
                                        class="bg-green-500 hover:bg-green-700 text-white font-semibold px-4 py-2 rounded">
                                        ยืนยัน
                                    </button>
                                </form>
                                <form action="{{ route('stock.reject', $movement->id) }}" method="POST" class="inline">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit"
                                        class="bg-red-500 hover:bg-red-700 text-white font-semibold px-4 py-2 rounded">
                                        ปฏิเสธ
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center text-gray-500 py-4">ไม่มีคำขอปรับสต็อก</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- 📌 Card View for Mobile -->
        <div class="md:hidden space-y-4">
            @foreach ($movements as $movement)
                <div class="bg-white shadow-md rounded-lg p-4 border border-gray-200">
        
                    <!-- รายละเอียดสินค้า -->
                    <div class="mt-2 text-gray-700">
                        <p><strong>👤 ส่งคำขอโดย:</strong> {{ $movement->user->name }}</p>
                        <p><strong>📦 สินค้า:</strong> {{ $movement->product->name }}</p>
                        <p><strong>🔢 จำนวน:</strong> {{ $movement->quantity }}</p>
                        <p><strong>🔄 ประเภท:</strong> {{ $movement->operation == 'add' ? 'เพิ่ม' : 'ลด' }}</p>
                        @if ($movement->note)
                            <p><strong>📝 หมายเหตุ:</strong> {{ $movement->note }}</p>
                        @endif
                    </div>
        
                    <!-- ปุ่ม "ยืนยัน" และ "ปฏิเสธ" -->
                    <div class="mt-4 flex items-center space-x-4">
                        <form action="{{ route('stock.approve', $movement->id) }}" method="POST">
                            @csrf
                            @method('PATCH')
                            <button type="submit"
                                class="bg-green-500 hover:bg-green-700 text-white font-semibold py-2 px-4 rounded w-full">
                                ✅ ยืนยัน
                            </button>
                        </form>
                        <form action="{{ route('stock.reject', $movement->id) }}" method="POST">
                            @csrf
                            @method('PATCH')
                            <button type="submit"
                                class="bg-red-500 hover:bg-red-700 text-white font-semibold py-2 px-4 rounded w-full">
                                ❌ ปฏิเสธ
                            </button>
                        </form>
                    </div>
                </div>
            @endforeach
        </div>
        

    </div>
@endsection
