@extends('layouts.main')
@php
    $manu = 'แดชบอร์ด';
@endphp
@section('content')
<div class="container mx-auto p-4">
    <h1 class="text-2xl font-bold mb-4">สรุปการขาย "เดือนนี้" (รายสินค้า)</h1>

    <!-- การ์ดยอดรวมวันนี้ -->
    <div class="bg-white shadow p-4 rounded mb-4">
        <h2 class="text-xl font-semibold">ยอดเงินรวม เดือนนี้</h2>
        <p class="text-3xl text-green-600 font-bold mt-2">
            {{ number_format($todaySum, 2) }} ฿
        </p>
    </div>

    <!-- ตารางสรุป (overflow-x-auto รองรับจอเล็ก) -->
    <div class="bg-white shadow rounded p-4 mb-4">
        <h2 class="text-xl font-semibold mb-2">รายการสินค้า (เดือนนี้)</h2>
        
        @if ($todayItems->count() > 0)
            <div class="overflow-x-auto">
                <table class="table-auto w-full border-collapse min-w-max">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="border px-4 py-2 text-left">สินค้า</th>
                            <th class="border px-4 py-2 text-right">จำนวน (ชิ้น)</th>
                            <th class="border px-4 py-2 text-right">ยอดเงิน (฿)</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($todayItems as $item)
                            <tr>
                                <td class="border px-4 py-2 whitespace-nowrap">
                                    {{ $item->product->name ?? 'N/A' }}
                                </td>
                                <td class="border px-4 py-2 text-right">
                                    {{ number_format($item->total_qty, 0) }}
                                </td>
                                <td class="border px-4 py-2 text-right">
                                    {{ number_format($item->total_revenue, 2) }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <p class="text-gray-500 mt-2">ไม่มีรายการสินค้าวันนี้</p>
        @endif
    </div>

    <!-- กราฟ Chart.js -->
    <div class="bg-white shadow rounded p-4">
        <h2 class="text-xl font-semibold mb-2">กราฟยอดขายแยกตามสินค้า</h2>
        <!-- ปุ่มสลับกราฟ (Optional) -->
        <div class="mb-2">
            <button 
                class="bg-blue-500 text-white px-3 py-1 rounded mr-2"
                onclick="showChart('quantity')">
                จำนวน (ชิ้น)
            </button>
            <button 
                class="bg-green-500 text-white px-3 py-1 rounded"
                onclick="showChart('revenue')">
                ยอดเงิน (฿)
            </button>
        </div>

        <!-- Canvas สำหรับกราฟ 2 อัน (หรือจะใช้ 1 อันแล้วสลับ dataset ก็ได้) -->
        <canvas id="chartQty" width="400" height="200"></canvas>
        <canvas id="chartRevenue" width="400" height="200" class="hidden"></canvas>
    </div>
</div>

<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // รับ labels, dataQty, dataRevenue จาก backend
    const labels = {!! json_encode($labels) !!}; 
    const dataQty = {!! json_encode($dataQty) !!};
    const dataRevenue = {!! json_encode($dataRevenue) !!};

    // สร้างกราฟ Quantity
    const ctxQty = document.getElementById('chartQty').getContext('2d');
    const chartQty = new Chart(ctxQty, {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [{
                label: 'จำนวน (ชิ้น)',
                data: dataQty,
                backgroundColor: 'rgba(54, 162, 235, 0.6)'
            }]
        },
        options: {
            responsive: true,
            scales: {
                y: { beginAtZero: true }
            }
        }
    });

    // สร้างกราฟ Revenue
    const ctxRev = document.getElementById('chartRevenue').getContext('2d');
    const chartRev = new Chart(ctxRev, {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [{
                label: 'ยอดเงิน (บาท)',
                data: dataRevenue,
                backgroundColor: 'rgba(75, 192, 192, 0.6)'
            }]
        },
        options: {
            responsive: true,
            scales: {
                y: { beginAtZero: true }
            }
        }
    });

    function showChart(type) {
        const chartQtyCanvas = document.getElementById('chartQty');
        const chartRevCanvas = document.getElementById('chartRevenue');
        if (type === 'quantity') {
            chartQtyCanvas.classList.remove('hidden');
            chartRevCanvas.classList.add('hidden');
        } else {
            chartQtyCanvas.classList.add('hidden');
            chartRevCanvas.classList.remove('hidden');
        }
    }
</script>
@endsection
