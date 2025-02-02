@extends('layouts.main')

@php
    $manu = 'สมาชิก';
@endphp

@section('content')
<div class="container mx-auto px-4 py-6">
    <h1 class="text-2xl font-bold mb-6">ประวัติการซื้อของ : {{ request('name') }}</h1>
    <div class="overflow-auto">
        <div class="hidden md:block">
            <table class="table-auto w-full border-collapse border border-gray-200 bg-white shadow-md">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="border border-gray-300 px-4 py-2 text-left">หมายเลขบิล</th>
                        <th class="border border-gray-300 px-4 py-2 text-left">วันที่</th>
                        <th class="border border-gray-300 px-4 py-2 text-left">รายการสินค้า</th>
                        <th class="border border-gray-300 px-4 py-2 text-right">ราคารวม (฿)</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($orders as $order)
                    <tr>
                        <td class="border border-gray-300 px-4 py-2">{{ $order->order_number }}</td>
                        <td class="border border-gray-300 px-4 py-2">{{ $order->created_at->format('d/m/') . ($order->created_at->format('Y') + 543) . $order->created_at->format(' H:i') }}</td>
                        <td class="border border-gray-300 px-4 py-2">
                            @foreach ($order->items as $item)
                            <div>{{ $item->product->name }} ({{ $item->quantity }} ชิ้น)</div>
                            @endforeach
                        </td>
                        <td class="border border-gray-300 px-4 py-2 text-right">{{ number_format($order->total_amount, 2) }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="text-center text-gray-500 py-4">ไม่มีข้อมูลการซื้อ</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="md:hidden space-y-4">
            @forelse($orders as $order)
            <div class="bg-white shadow-md p-4 rounded border border-gray-200">
                <div class="flex items-center justify-between mb-2">
                    <div>
                        <span class="font-semibold text-blue-600">#{{ $order->order_number }}</span>
                        <span class="text-sm text-gray-500 ml-2">{{ $order->created_at->format('d/m/') . ($order->created_at->format('Y') + 543) . $order->created_at->format(' H:i') }}</span>
                    </div>
                </div>
                <div class="text-gray-700 text-sm">
                    @foreach ($order->items as $item)
                    <p>{{ $item->product->name }}: {{ number_format($item->product->price, 2) }} ฿ x {{ $item->quantity }}</p>
                    @endforeach
                </div>
                <div class="text-right text-sm font-semibold mt-2">
                    รวม: {{ number_format($order->total_amount, 2) }} ฿
                </div>
            </div>
            @empty
            <p class="text-center text-gray-500">ไม่มีข้อมูลการซื้อ</p>
            @endforelse
        </div>
    </div>
    <div class="mt-6">
        {{ $orders->appends(['name' => request('name')])->links() }}
    </div>
</div>
@endsection
