@extends('layouts.main') 
@php
    $manu = 'ประวัติการขาย';
@endphp

@section('content')
<div class="max-w-4xl mx-auto p-4 bg-white shadow rounded">
    <h3 class="text-xl sm:text-2xl font-bold mb-4">แก้ไขออเดอร์ #{{ $order->order_number }}</h3>

    <form action="{{ route('orders.update', $order->id) }}" method="POST" class="space-y-4">
        @csrf
        @method('PUT')

        <!-- Container สำหรับชุดการ์ด -->
        <div id="itemContainer" class="space-y-4">
            <!-- วนลูปรายการสินค้าเดิม -->
            @foreach ($order->items as $i => $item)
                <div class="bg-gray-50 border border-gray-200 rounded p-4 relative" data-card-index="{{ $i }}">
                    <!-- ปุ่มลบ มุมบนขวา -->
                    <button type="button" onclick="removeCard(this)"
                        class="absolute top-2 right-2 bg-red-500 hover:bg-red-600 text-white px-2 py-1 rounded">
                        ลบ
                    </button>

                    <!-- เลือกสินค้า -->
                    <div class="mb-2">
                        <label class="block font-semibold mb-1">สินค้า</label>
                        <select name="items[{{ $i }}][product_id]"
                            class="productSelect border rounded p-1 w-full"
                            onchange="onProductChange()">
                            @foreach ($products as $product)
                                <option value="{{ $product->id }}"
                                    data-price="{{ $product->price }}"
                                    {{ $item->product_id == $product->id ? 'selected' : '' }}>
                                    {{ $product->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- ราคา/หน่วย + จำนวน -->
                    <div class="mb-2 flex justify-between">
                        <div>
                            <label class="font-semibold mb-1 block">ราคา/หน่วย</label>
                            <input type="number" step="0.01"
                                name="items[{{ $i }}][price]"
                                value="{{ $item->price }}"
                                class="priceInput border rounded p-1 w-full text-right" />
                        </div>
                        <div>
                            <label class="font-semibold mb-1 block">จำนวน</label>
                            <input type="number"
                                name="items[{{ $i }}][quantity]"
                                value="{{ $item->quantity }}"
                                class="border rounded p-1 w-full text-right" />
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
        <div class="flex justify-between">
            <button type="button" onclick="addNewCard()"
            class="bg-green-500 hover:bg-green-600 text-white font-semibold px-4 sm:px-4 py-2 rounded">
            + เพิ่มสินค้า
        </button>

        <!-- ปุ่ม submit อัปเดต -->
        <div class="">
            <button type="submit"
                class="bg-blue-500 hover:bg-blue-600 text-white font-semibold px-4 sm:px-4 py-2 rounded">
                บันทึกการแก้ไข
            </button>
        </div>
        </div>

        <!-- ปุ่มเพิ่มสินค้าใหม่ -->
        
    </form>
</div>

<script>
    // ฟังก์ชันป้องกันเลือกสินค้าซ้ำ
    function onProductChange() {
        const selects = document.querySelectorAll('.productSelect');
        // รวบรวมสินค้าใดถูกเลือกแล้ว
        const chosenSet = new Set();

        selects.forEach(sel => {
            if (sel.value) {
                chosenSet.add(sel.value);
            }
        });

        // วนลูปทุก select -> ทุก option
        selects.forEach(sel => {
            const currentVal = sel.value; // ค่าปัจจุบัน
            for (let opt of sel.options) {
                if (!opt.value) continue; // ข้าม option ว่าง
                if (opt.value === currentVal) {
                    // อนุญาตตัวที่ตัวเองเลือก
                    opt.disabled = false;
                } else {
                    // ถ้ามีคนเลือกแล้ว และไม่ใช่ตัวเอง => disable
                    opt.disabled = chosenSet.has(opt.value);
                }
            }
        });
    }

    // ฟังก์ชันเพิ่มการ์ดใหม่
    function addNewCard() {
        const container = document.getElementById('itemContainer');

        // สร้าง div สำหรับการ์ดใหม่
        const cardIndex = Date.now(); // หรือใช้ตัวนับ
        const cardDiv = document.createElement('div');
        cardDiv.classList.add('bg-gray-50', 'border', 'border-gray-200', 'rounded', 'p-4', 'relative');
        cardDiv.setAttribute('data-card-index', cardIndex);

        // เนื้อหาในการ์ด (ใช้ template string)
        cardDiv.innerHTML = `
            <button type="button" onclick="removeCard(this)"
                class="absolute top-2 right-2 bg-red-500 hover:bg-red-600 text-white px-2 py-1 rounded">
                ลบ
            </button>

            <div class="mb-2">
                <label class="block font-semibold mb-1">สินค้า</label>
                <select name="items[new-${cardIndex}][product_id]"
                        class="productSelect border rounded p-1 w-full"
                        onchange="onProductChange()">
                    <option value="" data-price="0">-- เลือกสินค้า --</option>
                    @foreach($products as $p)
                    <option value="{{ $p->id }}" data-price="{{ $p->price }}">
                        {{ $p->name }}
                    </option>
                    @endforeach
                </select>
            </div>

            <div class="mb-2 flex justify-between">
                <div>
                    <label class="font-semibold mb-1 block">ราคา/หน่วย</label>
                    <input type="number" step="0.01"
                           name="items[new-${cardIndex}][price]"
                           value="0"
                           class="priceInput border rounded p-1 w-full text-right" />
                </div>
                <div>
                    <label class="font-semibold mb-1 block">จำนวน</label>
                    <input type="number"
                           name="items[new-${cardIndex}][quantity]"
                           value="1"
                           class="border rounded p-1 w-full text-right" />
                </div>
            </div>
        `;

        container.appendChild(cardDiv);

        // ผูก event ให้ select เพื่ออัปเดตราคาอัตโนมัติ
        const select = cardDiv.querySelector('.productSelect');
        const priceInput = cardDiv.querySelector('.priceInput');
        select.addEventListener('change', function() {
            const selectedOption = this.selectedOptions[0];
            const defaultPrice = selectedOption.getAttribute('data-price') || 0;
            priceInput.value = defaultPrice;
        });

        // เรียก onProductChange เพื่อ disable ตัวเลือกซ้ำ
        onProductChange();
    }

    // ฟังก์ชันลบการ์ด
    function removeCard(btn) {
        btn.closest('div[data-card-index]').remove();
        // อัปเดต disable/enable ใหม่
        onProductChange();
    }

    // ถ้าอยากให้ราคาอัปเดตทันทีในรายการเก่า => bind event
    window.addEventListener('load', () => {
        // ผูก event กับ select เดิม
        document.querySelectorAll('.productSelect').forEach(sel => {
            sel.addEventListener('change', onProductChange);
        });
        // เรียกครั้งแรก
        onProductChange();
    });
</script>
@endsection
