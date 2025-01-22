@extends('layouts.main')
@php
    $manu = 'จัดการกล่องเงินสด';
@endphp
@section('content')
    <div class="p-6 bg-white shadow rounded-lg">
        <h2 class="text-2xl font-semibold mb-4">กล่องเงินสด</h2>

        <p class="text-lg mb-4">ยอดเงินปัจจุบัน: <span
                class="font-bold text-green-500">฿{{ number_format($cashDrawer->current_balance, 2) }}</span></p>

        <form action="{{ route('cashdrawer.add') }}" method="POST" class="mb-4">
            @csrf
            <input type="number" name="amount" class="border rounded-lg p-2" placeholder="จำนวนเงิน">
            <input type="text" name="note" class="border rounded-lg p-2" placeholder="หมายเหตุ (ถ้ามี)">
            <button type="submit" class="px-4 py-2 bg-green-500 text-white rounded-lg">เพิ่มเงิน</button>
        </form>

        <form action="{{ route('cashdrawer.subtract') }}" method="POST">
            @csrf
            <input type="number" name="amount" class="border rounded-lg p-2" placeholder="จำนวนเงิน">
            <input type="text" name="note" class="border rounded-lg p-2" placeholder="หมายเหตุ (ถ้ามี)">
            <button type="submit" class="px-4 py-2 bg-red-500 text-white rounded-lg">ถอนเงิน</button>
        </form>

        <h3 class="text-lg font-semibold mt-6 mb-4">ประวัติการเคลื่อนไหว</h3>
        <div class="overflow-x-auto">
            <table class="table-auto w-full border-collapse border px-2 overflow-x-auto border-gray-300">
                <thead>
                    <tr>
                        <th class="border text-nowrap px-4 py-2">วันที่</th>
                        <th class="border text-nowrap px-4 py-2">ประเภท</th>
                        <th class="border text-nowrap px-4 py-2">จำนวน</th>
                        <th class="border text-nowrap px-4 py-2">หมายเหตุ</th>
                        <th class="border text-nowrap px-4 py-2">ผู้แก้ไข</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($movements as $movement)
                        <tr>
                            <td class="border text-nowrap px-4 py-2">{{ $movement->created_at }}</td>
                            <td class="border text-nowrap px-4 py-2">{{ $movement->type }}</td>
                            <td class="border text-nowrap px-4 py-2 text-right">฿{{ number_format($movement->amount, 2) }}
                            </td>
                            <td class="border text-nowrap px-4 py-2">
                                @if (strpos($movement->note, '#ORD-') !== false)
                                    @php
                                        $orderNumber = str_replace(
                                            ['ยอดขายจากคำสั่งซื้อ #', 'คืนเงินสำหรับคำสั่งซื้อ #'],
                                            '',
                                            $movement->note,
                                        );
                                        $orderNumber = trim($orderNumber);
                                    @endphp
                                    <a href="{{ route('sales.detail2', ['orderNumber' => $orderNumber]) }}"
                                        class="text-sky-500 hover:underline">
                                        {{ $movement->note }}
                                    </a>
                                @else
                                    {{ $movement->note }}
                                @endif
                            </td>
                            <td class="border text-nowrap px-4 py-2">{{ $movement->user->name ?? 'ไม่ระบุ' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="mt-4">
            {{ $movements->links() }}
        </div>
    </div>
@endsection
