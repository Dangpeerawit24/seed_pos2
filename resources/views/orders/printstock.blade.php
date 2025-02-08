<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="utf-8">
    <title>เอกสารปรับรายการ #{{ $stock->id }}</title>
    <style>
        /* จัดรูปแบบสไตล์สำหรับการพิมพ์ */
        @media print {

            /* ซ่อนปุ่มหรือส่วนที่ไม่ต้องการให้พิมพ์ */
            .no-print {
                display: none;
            }
        }

        body {
            font-family: 'Tahoma', sans-serif;
            margin: 20px;
            font-size: 16px;
            line-height: 1.4;
        }

        h2 {
            margin: 0;
            padding: 0;
        }

        /* ตารางหลัก */
        table {
            width: 100%;
            border-collapse: collapse;
        }

        table th,
        table td {
            border: 1px solid #000000;
            padding: 8px;
            vertical-align: top;
        }

        .text-center {
            text-align: center;
        }

        .text-right {
            text-align: right;
        }

        /* ตารางสำหรับลายเซ็น */
        .signature-table td {
            border: 0;
            /* ไม่ต้องมีเส้นในตารางลายเซ็น */
            padding: 20px;
        }
    </style>
</head>
{{-- @dd($stock) --}}

<body onload="initPrint()">
    <!-- ปุ่มสั่งพิมพ์ซ้ำ (จะถูกซ่อนตอนสั่งพิมพ์) -->
    <div class="no-print" style="text-align: right; margin-bottom: 10px;">
        <button onclick="window.print()">พิมพ์อีกครั้ง</button>
    </div>

    <!-- หัวเอกสาร -->
    <h1 style="text-align: center;">เอกสารปรับรายการ</h1>

    <!-- ข้อมูลลูกค้า / ใบสั่ง -->
    <table style="margin-bottom: 10px;">
        <tr>
            <td style="width: 20%;">ผู้ทำรายการ :</td>
            <td>{{ $stock->user->name ?? 'ไม่มีข้อมูล' }}
            </td>
        </tr>
        <tr>
            <td>วันที่ :</td>
            <td>{{ $stock->created_at->format('d/m/') . ($stock->created_at->format('Y') + 543) . $stock->created_at->format(' H:i') }}
            </td>
        </tr>
    </table>
    <div>
        <h2 style="text-align: center; margin: 10px;">รายการ</h2>
    </div>
    <!-- ตารางรายการสินค้า -->
    <table>
        <thead>
            <tr>
                <th style="width: 45%;">รายการ</th>
                <th style="width: 10%;">ประเภท</th>
                <th style="width: 10%;">จำนวน</th>
                <th style="width: 20%;">ราคา/หน่วย</th>
                <th style="width: 20%;">จำนวนเงิน</th>
            </tr>
        </thead>
        <tbody>
            @php
                $rowTotal = $stock->cost_price * $stock->quantity;
            @endphp
            <tr>
                <td>
                    {{ $stock->product->name }}
                </td>
                <td class="text-center">
                    @if ($stock->type == 'in')
                        เพิ่ม
                    @else
                        ลด
                    @endif
                </td>
                <td class="text-center">{{ abs($stock->quantity) }}</td>
                <td class="text-right">{{ number_format($stock->cost_price, 2) }}</td>
                <td class="text-right">{{ number_format($rowTotal, 2) }}</td>
            </tr>
        </tbody>
        <tfoot>
            <tr>
                <!-- รวมเงินท้ายตาราง -->
                <td colspan="4" class="text-right"><strong>รวมเงิน</strong></td>
                <td class="text-right">
                    {{ number_format($rowTotal, 2) }}
                </td>
            </tr>
        </tfoot>
    </table>
    

    @if (empty($stock->note))
    @else
        <h3 class=" mt-10">หมายเหตุ : {{ $stock->note }}</h3>
    @endif

    <table class="signature-table" style="margin-top: 30px; width: 100%;">
        <tr>
            <td style="width: 33%; text-align: center;">
                <p style="margin-bottom: 16px;">ผู้ทำรายการ</p>
                (...............................................) <br>
                <p style="margin-top: 10px;">( ...... / ...... / ...... )</p>
            </td>
            
        </tr>
    </table>

    <script>
        function initPrint() {
            // สั่งพิมพ์ทันทีเมื่อหน้าโหลด
            window.print();
        }
        // หลังพิมพ์เสร็จ ให้ปิดหน้าต่างอัตโนมัติ (ต้องเป็น window.open)
        window.onafterprint = function() {
            window.close();
        }
    </script>
</body>

</html>
