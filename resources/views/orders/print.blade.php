<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="utf-8">
    <title>{{ request('title') }} #{{ $order->order_number }}</title>
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

<body onload="initPrint()">
    <!-- ปุ่มสั่งพิมพ์ซ้ำ (จะถูกซ่อนตอนสั่งพิมพ์) -->
    <div class="no-print" style="text-align: right; margin-bottom: 10px;">
        <button onclick="window.print()">พิมพ์อีกครั้ง</button>
    </div>

    <!-- หัวเอกสาร -->
    <h1 style="text-align: center;">{{ request('title') }}</h1>

    <!-- ข้อมูลลูกค้า / ใบสั่ง -->
    <table style="margin-bottom: 10px;">
        <tr>
            {{-- @dd($order) --}}
            <td style="width: 20%;">ชื่อลูกค้า :</td>
            <td style="width: 80%;" colspan="3">
                <!-- สามารถดึงจาก $order->customer_name ได้ตามจริง -->
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
            </td>
        </tr>
        <tr>
            <td>วันที่ :</td>
            <td>{{ $order->created_at->format('d/m/') . ($order->created_at->format('Y') + 543) . $order->created_at->format(' H:i') }}
            </td>
            {{-- <td style="width: 15%;">เลขที่ใบส่ง :</td>
            <td style="width: 25%;">{{ $order->order_number }}</td> --}}
        </tr>
    </table>
    <div>
        <h2 style="text-align: center; margin: 10px;">รายการ</h2>
    </div>
    <!-- ตารางรายการสินค้า -->
    <table>
        <thead>
            <tr>
                <th style="width: 5%;">ลำดับ</th>
                <th style="width: 45%;">รายการ</th>
                <th style="width: 10%;">จำนวน</th>
                <th style="width: 20%;">ราคา/หน่วย</th>
                <th style="width: 20%;">จำนวนเงิน</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($order->items as $index => $item)
                @php
                    $rowTotal = $item->price * $item->quantity;
                @endphp
                <tr>
                    <td class="text-center">{{ $loop->iteration }}</td>
                    <td>
                        {{ $item->product_name ?? ($item->product->name ?? 'N/A') }}
                    </td>
                    <td class="text-center">{{ $item->quantity }}</td>
                    <td class="text-right">{{ number_format($item->price, 2) }}</td>
                    <td class="text-right">{{ number_format($rowTotal, 2) }}</td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <!-- รวมเงินท้ายตาราง -->
                <td colspan="4" class="text-right"><strong>รวมเงิน</strong></td>
                <td class="text-right">
                    {{ number_format($order->total_amount, 2) }}
                </td>
            </tr>
        </tfoot>
    </table>

    @if (request('title') === 'ใบเสร็จรับเงิน')
        @if ($order->payment_method === 'cash')
            <p>ชำระด้วยเงินสด</p>
            @if (!is_null($order->received_amount))
                <p>รับเงินมา: {{ number_format($order->received_amount, 2) }}</p>
                <p>เงินทอน: {{ number_format($order->change, 2) }}</p>
            @endif
        @elseif ($order->payment_method === 'qr')
            <p>ชำระผ่านธนาคาร</p>
            <img src="{{ asset($order->proof_image) }}" alt="" width="150px">
        @endif

    @endif


    <table class="signature-table" style="margin-top: 30px; width: 100%;">
        <tr>
            <td style="width: 33%; text-align: center;">
                <p style="margin-bottom: 16px;">ลูกค้า</p>
                (...............................................) <br>
                <p style="margin-top: 10px;">( ...... / ...... / ...... )</p>
            </td>
            <td style="width: 33%; text-align: center;">
                <p style="margin-bottom: 16px;">ผู้รับเงิน</p>
                (...............................................) <br>
                <p style="margin-top: 10px;">( ...... / ...... / ...... )</p>
            </td>
            <td style="width: 33%; text-align: center;">
                <p style="margin-bottom: 16px;">พนักงาน</p>
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
