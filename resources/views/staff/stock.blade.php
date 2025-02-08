@extends('layouts.main') 
@php
    $manu = 'จัดการสต็อกสินค้า';
@endphp

@section('content')
<div class="container mx-auto px-4 py-6">
    <h1 class="text-2xl font-bold mb-6">การจัดการสต็อกสินค้า</h1>

    @if(session('success'))
        <div class="bg-green-100 text-green-700 p-4 rounded-lg mb-4">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="bg-red-100 text-red-700 p-4 rounded-lg mb-4">
            {{ session('error') }}
        </div>
    @endif

    <!-- การ์ดสินค้า -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        @foreach($products as $product)
        <div class="bg-white shadow-md rounded-lg p-4 flex flex-col justify-between">
            <div>
                <h2 class="text-lg font-bold text-gray-700">{{ $product->name }}</h2>
                <p class="text-gray-600">จำนวนสต็อก: <span class="font-semibold">{{ $product->stock_quantity }}</span></p>
                <p class="text-sm text-gray-500 mt-1">
                    ผู้ดำเนินการล่าสุด: 
                    <span class="font-semibold">
                        {{ $product->latestStockMovement->user->name ?? 'ไม่มีข้อมูล' }}
                    </span>
                </p>
            </div>
            <div class="mt-4 flex justify-between">
                <button 
                    type="button" 
                    onclick="openStockModal({{ $product->id }}, '{{ $product->name }}')"
                    class="bg-blue-500 text-white px-4 py-2 rounded-md hover:bg-blue-600 text-sm">
                    ปรับสต็อก
                </button>
            </div>
        </div>
        @endforeach
    </div>
</div>

<!-- Modal -->
<div id="stockModal" class="fixed inset-0 p-2 flex items-center justify-center bg-black bg-opacity-50 hidden">
    <div class="bg-white rounded shadow-lg max-w-md w-full p-4 relative">
        <!-- ปุ่มปิด -->
        <button 
            type="button" 
            class="absolute top-2 right-2 text-gray-500 hover:text-red-500" 
            onclick="closeStockModal()">
            ✕
        </button>
        <h2 class="text-xl font-bold mb-4">ปรับสต็อก</h2>

        <!-- ฟอร์มปรับสต็อก -->
        <form id="stockForm" method="POST">
            @csrf
            <input type="hidden" id="stockProductId" />

            <!-- ชื่อสินค้า -->
            <div class="mb-2">
                <label class="block text-sm font-semibold mb-1">สินค้า:</label>
                <p id="stockProductName" class="bg-gray-100 p-2 rounded"></p>
            </div>

            <!-- เลือกการกระทำ -->
            <div class="mb-2">
                <label class="block text-sm font-semibold mb-1">เลือกการกระทำ:</label>
                <select id="stockOperation" class="border rounded p-2 w-full">
                    <option value="add">เพิ่มสต็อก</option>
                    <option value="reduce">ลดสต็อก</option>
                </select>
            </div>

            <!-- จำนวน -->
            <div class="mb-2">
                <label class="block text-sm font-semibold mb-1">จำนวน:</label>
                <input type="number" id="stockQuantity" class="border rounded p-2 w-full" placeholder="กรอกจำนวน" value="1" />
            </div>

            <div class="mb-2">
                <label class="block text-sm font-semibold mb-1">ราคาต่อหน่วย:</label>
                <input type="number" id="cost_price" class="border rounded p-2 w-full" placeholder="กรอกจำนวนเงิน" value="1" step="0.01"/>
            </div>

            <!-- หมายเหตุ -->
            <div class="mb-2">
                <label class="block text-sm font-semibold mb-1">หมายเหตุ:</label>
                <textarea id="stockNote" rows="2" class="border rounded p-2 w-full" placeholder="ระบุรายละเอียด"></textarea>
            </div>

            <!-- ปุ่ม -->
            <div class="text-right pt-2">
                <button type="button" onclick="closeStockModal()" class="px-4 py-2 bg-gray-300 text-gray-700 rounded mr-2">ยกเลิก</button>
                <button type="button" onclick="submitStockForm()" class="px-4 py-2 bg-blue-500 hover:bg-blue-600 text-white rounded">บันทึก</button>
            </div>
        </form>
    </div>
</div>

<script>
function openStockModal(productId, productName) {
    document.getElementById('stockProductId').value = productId;
    document.getElementById('stockProductName').textContent = productName;

    document.getElementById('stockQuantity').value = 1;
    document.getElementById('cost_price').value = 1;
    document.getElementById('stockNote').value = '';

    document.getElementById('stockModal').classList.remove('hidden');
}

function closeStockModal() {
    document.getElementById('stockModal').classList.add('hidden');
}

function submitStockForm() {
    const productId = document.getElementById('stockProductId').value;
    const operation = document.getElementById('stockOperation').value;
    const quantity = document.getElementById('stockQuantity').value || 0;
    const costprice = document.getElementById('cost_price').value || 0;
    const note = document.getElementById('stockNote').value || '';

    const form = document.getElementById('stockForm');

    if (operation === 'add') {
        form.action = "{{ route('pendingStockAdd', ':id') }}".replace(':id', productId);
    } else {
        form.action = "{{ route('pendingStockReduce', ':id') }}".replace(':id', productId);
    }

    form.innerHTML += `<input type="hidden" name="quantity" value="${quantity}" />
                       <input type="hidden" name="cost_price" value="${costprice}" />
                       <input type="hidden" name="note" value="${note}" />`;

    form.submit();
}
</script>
@endsection
