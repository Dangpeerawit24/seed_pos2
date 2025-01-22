@extends('layouts.main')
@php
    $manu = 'หน้าขาย';
@endphp
@Section('content')
    <div class="row flex flex-col lg:flex-row gap-1 h-full fixed overflow-hidden scrollbar-hide">
        <div class="w-full lg:w-3/5 overflow-auto scrollbar-hide  bg-gray-100">
            <div class=" fixed lg:sticky flex flex-col w-full h-auto bg-white overflow-auto scrollbar-hide">
                <h2 class=" text-2xl">เลือกประเภทสินค้า</h2>
                <div class="flex flex-row lg:flex-wrap gap-1 mb-2 overflow-auto scrollbar-hide">
                    <button class="px-4 py-2 bg-blue-500 text-white rounded-lg"
                        onclick="filterProducts('all')">ทั้งหมด</button>
                    @foreach ($categories as $category)
                        <button class="px-4 py-2 bg-blue-500 text-white rounded-lg text-nowrap"
                            onclick="filterProducts('{{ $category->id }}')">
                            {{ $category->name }}
                        </button>
                    @endforeach
                </div>
            </div>
            <div class="grid grid-cols-2 pt-20 mb-56 lg:pt-0 lg:mt-2 lg:mb-48 md:grid-cols-3 xl:grid-cols-4 scrollbar-hide  overflow-x-auto"
                id="productContainer">
                @foreach ($products as $product)
                    <div class="product-card bg-sky-100 w-auto md:w-auto h-auto flex items-end  m-1 p-2 rounded-lg shadow-xl border-2 border-blue-300 lg:hover:scale-105 transition-transform duration-500 ease-in-out	"
                        data-category="{{ $product->category_id }}">
                        <a id="pos" href="#"
                            onclick="addToCart({{ $product->id }}, '{{ $product->name }}', {{ $product->price }})">
                            <img src="{{ asset('img/product/' . $product->image) }}" alt="">
                            <h1 class="text-lg font-bold mt-2 line-clamp-2">{{ $product->name }}</h1>
                            <p class=" text-xl font-bold text-sky-500">฿{{ $product->price }}</p>
                        </a>
                    </div>
                @endforeach
            </div>
        </div>
        <div class=" hidden w-0 lg:flex lg:w-2/5 lg:p-5 bg-white overflow-hidden rounded-lg shadow-lg overflow-y-auto">
            <div class="w-full">
                <!-- แสดงยอดรวมสุทธิ -->
                <div class="p-5 flex items-center justify-between">
                    <h1 class="text-3xl">ยอดรวมสุทธิ</h1>
                    <h1 class="text-3xl" id="totalAmount">0.00 ฿</h1>
                </div>

                <!-- ปุ่มชำระเงิน -->
                <div>
                    <button id="checkoutButton" class="px-4 py-2 h-20 w-full bg-sky-400 text-white rounded hover:bg-sky-600"
                        onclick="checkout()">
                        <h1 class="text-4xl">ชำระเงิน</h1>
                    </button>
                </div>

                <!-- แสดงจำนวนรายการและจำนวนชิ้น -->
                <div class="p-5 flex items-center gap-2 justify-end">
                    <h1 class="text-lg" id="totalItems">0 รายการ</h1>
                    <h1 class="text-lg"> | </h1>
                    <h1 class="text-lg" id="totalQuantity">0 ชิ้น</h1>
                </div>

                <!-- รายการในตะกร้า -->
                <div class="overflow-y-auto h-3/5 px-4" id="cartContainer">
                    <!-- รายการสินค้า -->
                    <!-- สินค้าจะแสดงที่นี่ด้วย JavaScript -->
                </div>
            </div>
        </div>
        <div class=" w-full h-32 p-2 flex fixed bottom-0 lg:hidden z-50 bg-white rounded-t-xl ">
            <div class="flex flex-col w-full p-2">
                <div class="flex flex-row gap-2">
                    <p class="text-md">พนักงาน</p>
                    <p class="text-md">:</p>
                    <p class="text-md">{{ Auth::user()->name }}</p>
                </div>
                <div class="flex flex-row gap-2 mt-2 pr-2 w-full items-center justify-between">
                    <button id="openModal" onclick="checkout()"
                        class="h-14 w-full  bg-sky-400 text-white rounded-lg hover:bg-sky-600">
                        <h1 class=" text-4xl" id="totalAmountSmall">ชำระเงิน</h1>
                    </button>
                    <div class="bg-sky-400 flex items-center h-14 w-auto rounded-lg text-center">
                        <button
                            class="p-4 h-14 flex justify-between items-center text-center gap-2 bg-sky-500 hover:bg-sky-600 rounded-lg"
                            onclick="toggleSidebar()">
                            <i class="fa-solid fa-cart-arrow-down fa-md text-white"></i>
                            <p class="text-2xl text-white" id="totalQuantitySmall">0</p>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div id="cartSidebar" class="fixed top-0 mt-20 right-0 h-full w-0 bg-white shadow-lg  overflow-hidden">
        <div class="w-full h-full p-5">
            <!-- ปุ่มปิด Sidebar -->
            <div class="flex justify-end">
                <button onclick="toggleSidebar()" class="text-xl font-bold text-gray-600 hover:text-gray-800 mb-4">
                    ปิด
                </button>
            </div>

            <!-- แสดงยอดรวมสุทธิ -->
            <div class="p-5 flex items-center justify-between">
                <h1 class="text-3xl">ยอดรวมสุทธิ</h1>
                <h1 class="text-3xl text-sky-500 font-bold" id="totalAmount2">0.00 ฿</h1>
            </div>

            <!-- ปุ่มชำระเงิน -->
            <div class="mt-5">
                <button id="checkoutButton" class="px-4 py-2 h-14 w-full bg-sky-400 text-white rounded-lg hover:bg-sky-600"
                    onclick="checkout()">
                    <h1 class="text-2xl">ชำระเงิน</h1>
                </button>
            </div>

            <!-- แสดงจำนวนรายการและจำนวนชิ้น -->
            <div class="p-5 flex items-center gap-2 justify-end">
                <h1 class="text-lg" id="totalItems2">0 รายการ</h1>
                <h1 class="text-lg"> | </h1>
                <h1 class="text-lg" id="totalQuantity2">0 ชิ้น</h1>
            </div>

            <!-- รายการในตะกร้า -->
            <div class="overflow-y-auto h-3/5 px-4 pb-5" id="cartContainerSmall">
                <!-- รายการสินค้า -->
                <!-- สินค้าจะแสดงที่นี่ด้วย JavaScript -->
            </div>
        </div>
    </div>

    <!-- Modal -->
    <div id="paymentModal" class="fixed inset-0  px-2 flex items-center justify-center bg-gray-900 bg-opacity-50 hidden">
        <div class="bg-white rounded-lg shadow-lg w-full max-w-lg ">
            <!-- Header -->
            <div class="p-4 border-b flex justify-between items-center">
                <h2 class="text-xl font-bold">ชำระเงิน</h2>
                <button class="text-gray-500 hover:text-red-500" onclick="closePaymentModal()">×</button>
            </div>

            <select id="paymentMethod" class=" hidden">
                <option value="cash">เงินสด</option>
                <option value="qr">สแกนคิวอาร์</option>
            </select>

            <div id="cashPayment" class=" hidden">
                <label class="hidden">จำนวนเงินที่ได้รับ:</label>
                <input type="number" id="receivedAmount"
                    class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-sky-400"
                    placeholder="กรอกจำนวนเงิน" />
            </div>

            <!-- Tabs -->
            <div class="flex justify-around h-14 p-0 m-0 bg-gray-100">
                <button class="font-bold w-1/2 h-14 text-white bg-sky-400" onclick="switchTab('cash')"
                    id="cashbtn">เงินสด</button>
                <button class="font-bold w-1/2 h-14 " onclick="switchTab('online')" id="onlinebtn">ออนไลน์</button>
            </div>

            <!-- Content -->
            <div class="p-4 h-[350px]">
                <!-- แสดงยอดเงิน -->
                <div id="cashTab" class="tab-content">
                    <div class="text-right text-3xl mb-4" id="totalAmountadd">0.00</div>

                    <!-- ปุ่มตัวเลข -->
                    <div class="grid grid-cols-4 gap-2 text-center">
                        <button class="p-4 bg-gray-200 rounded-lg text-xl" onclick="enterNumber(7)">7</button>
                        <button class="p-4 bg-gray-200 rounded-lg text-xl" onclick="enterNumber(8)">8</button>
                        <button class="p-4 bg-gray-200 rounded-lg text-xl" onclick="enterNumber(9)">9</button>
                        <button class="p-4 bg-gray-200 rounded-lg text-xl text-blue-500"
                            onclick="enterShortcut(1000)">1,000</button>
                        <button class="p-4 bg-gray-200 rounded-lg text-xl" onclick="enterNumber(4)">4</button>
                        <button class="p-4 bg-gray-200 rounded-lg text-xl" onclick="enterNumber(5)">5</button>
                        <button class="p-4 bg-gray-200 rounded-lg text-xl" onclick="enterNumber(6)">6</button>
                        <button class="p-4 bg-gray-200 rounded-lg text-xl text-blue-500"
                            onclick="enterShortcut(500)">500</button>
                        <button class="p-4 bg-gray-200 rounded-lg text-xl" onclick="enterNumber(1)">1</button>
                        <button class="p-4 bg-gray-200 rounded-lg text-xl" onclick="enterNumber(2)">2</button>
                        <button class="p-4 bg-gray-200 rounded-lg text-xl" onclick="enterNumber(3)">3</button>
                        <button class="p-4 bg-gray-200 rounded-lg text-xl text-blue-500"
                            onclick="enterShortcut(100)">100</button>
                        <button class="p-4 bg-gray-200 rounded-lg text-xl text-red-500" onclick="clearInput()">ลบ</button>
                        <button class="p-4 bg-gray-200 rounded-lg text-xl" onclick="enterNumber(0)">0</button>
                        <button class="p-4 bg-gray-200 rounded-lg text-xl text-green-500"
                            onclick="fillTotal()">เต็ม</button>
                        <button class="p-4 bg-gray-200 rounded-lg text-xl text-blue-500"
                            onclick="enterShortcut(50)">50</button>
                    </div>
                </div>
                <div id="onlineTab" class="h-[350px] hidden flex flex-col tab-content items-center text-center">
                    <div class=" w-full px-16 text-center">
                        <div class="flex justify-between w-full">
                            <h1 class=" text-xl">ยอดรวมสุทธิ</h1>
                            <h1 class=" text-2xl" id="qrTotal"></h1>
                        </div>
                        <div class="flex p-2 justify-center">
                            <img id="qr" src="" width="150px" height="150px" alt="">
                        </div>
                    </div>
                    <div
                        class="relative w-full border rounded-lg p-4 bg-gray-100 hover:bg-gray-200 cursor-pointer text-center">
                        <label for="proofImage" class="block text-lg font-medium text-gray-600 cursor-pointer">
                            <i class="fa-solid fa-upload text-sky-400 text-2xl"></i>
                            <p class="mt-2">อัปโหลดหลักฐานการโอน</p>
                            <span id="fileName" class="block mt-2 text-sm text-gray-500">ยังไม่ได้เลือกไฟล์</span>
                        </label>
                        <input type="file" id="proofImage" accept=""
                            class="absolute top-0 left-0 w-full h-full opacity-0 cursor-pointer"
                            onchange="updateFileName()" />
                    </div>
                </div>
            </div>

            <!-- Footer -->
            <div class="p-4 border-t flex justify-end">
                <button class="px-6 py-2 bg-blue-500 text-white rounded-lg" onclick="submitPayment()">ตกลง</button>
            </div>
        </div>
    </div>

    <script>
        let currentAmount = 0;

        function openPaymentModal() {
            document.getElementById("paymentModal").classList.remove("hidden");
            document.getElementById("totalAmountadd").textContent = '0.00';
        }

        function closePaymentModal() {
            document.getElementById("paymentModal").classList.add("hidden");
            document.getElementById('totalAmountadd').textContent = '0.00';
        }

        function switchTab(tab) {
            // ซ่อน Tab อื่น ๆ และแสดง Tab ที่เลือก
            const tabs = document.querySelectorAll(".tab-content");
            tabs.forEach(tabContent => tabContent.classList.add("hidden"));
            document.getElementById(`${tab}Tab`).classList.remove("hidden");


            if (tab === 'cash') {
                document.getElementById("cashbtn").classList.add("bg-sky-400", "text-white");
                document.getElementById("cashbtn").classList.remove("bg-gray-200", "text-gray-800");

                document.getElementById("onlinebtn").classList.add("bg-gray-200", "text-gray-800");
                document.getElementById("onlinebtn").classList.remove("bg-sky-400", "text-white");
            } else {
                document.getElementById("onlinebtn").classList.add("bg-sky-400", "text-white");
                document.getElementById("onlinebtn").classList.remove("bg-gray-200", "text-gray-800");

                document.getElementById("cashbtn").classList.add("bg-gray-200", "text-gray-800");
                document.getElementById("cashbtn").classList.remove("bg-sky-400", "text-white");
            }

            // เปลี่ยน value ของ paymentMethod
            const paymentMethod = document.getElementById("paymentMethod");
            if (tab === 'cash') {
                paymentMethod.value = 'cash'; // เปลี่ยนค่าเป็น 'cash'
            } else {
                paymentMethod.value = 'qr'; // เปลี่ยนค่าเป็น 'qr'
            }
        }

        function updateFileName() {
            const fileInput = document.getElementById('proofImage');
            const fileNameDisplay = document.getElementById('fileName');

            if (fileInput.files.length > 0) {
                fileNameDisplay.textContent = fileInput.files[0].name;
            } else {
                fileNameDisplay.textContent = "ยังไม่ได้เลือกไฟล์";
            }
        }


        function enterNumber(number) {
            const display = document.getElementById("totalAmountadd");
            const receivedInput = document.getElementById("receivedAmount");

            // คำนวณยอดเงิน
            currentAmount = parseFloat(`${currentAmount}${number}`);

            // อัปเดตยอดใน modal และ input field
            display.textContent = currentAmount.toFixed(2);
            receivedInput.value = currentAmount.toFixed(2);
        }

        function enterShortcut(amount) {
            const display = document.getElementById("totalAmountadd");
            const receivedInput = document.getElementById("receivedAmount");

            // เพิ่มยอด Shortcut
            currentAmount += amount;

            // อัปเดตยอดใน modal และ input field
            display.textContent = currentAmount.toFixed(2);
            receivedInput.value = currentAmount.toFixed(2);
        }

        function clearInput() {
            const display = document.getElementById("totalAmountadd");
            const receivedInput = document.getElementById("receivedAmount");

            // รีเซ็ตยอดเงิน
            currentAmount = 0;

            // อัปเดตยอดใน modal และ input field
            display.textContent = "0.00";
            receivedInput.value = "";
        }

        function fillTotal() {
            const totalElement = document.getElementById("totalAmount");
            const display = document.getElementById("totalAmountadd");
            const receivedInput = document.getElementById("receivedAmount");

            // ดึงยอดรวมจาก totalAmount
            const total = parseFloat(totalElement.textContent.replace('฿', '').trim()) || 0;

            // อัปเดตยอดเงินใน modal และ input field
            currentAmount = total;
            display.textContent = total.toFixed(2);
            receivedInput.value = total.toFixed(2);
        }



        // function submitPayment() {
        //     submitPayment()
        //     clearadd()
        //     closePaymentModal();
        // }
    </script>


    <!-- Modal สำหรับแก้ไขจำนวน -->
    <div id="editModal" class="fixed inset-0 bg-gray-900 bg-opacity-50 flex items-center justify-center hidden">
        <div class="bg-white rounded-lg shadow-lg p-6 w-full max-w-sm">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-2xl font-bold">จำนวนสินค้า</h2>
                <button onclick="closeModal()" class="text-gray-600 hover:text-gray-800">
                    <i class="fa-solid fa-times text-2xl"></i>
                </button>
            </div>

            <!-- Display for Quantity -->
            <div class="text-center text-4xl font-bold mb-4">
                <span id="modalQuantityDisplay">1</span>
            </div>

            <!-- Keypad -->
            <div class="grid grid-cols-3 gap-1 mb-4">
                <button onclick="enterNumber2(7)" class="p-4 bg-gray-200 rounded-lg text-xl">7</button>
                <button onclick="enterNumber2(8)" class="p-4 bg-gray-200 rounded-lg text-xl">8</button>
                <button onclick="enterNumber2(9)" class="p-4 bg-gray-200 rounded-lg text-xl">9</button>
                <button onclick="enterNumber2(4)" class="p-4 bg-gray-200 rounded-lg text-xl">4</button>
                <button onclick="enterNumber2(5)" class="p-4 bg-gray-200 rounded-lg text-xl">5</button>
                <button onclick="enterNumber2(6)" class="p-4 bg-gray-200 rounded-lg text-xl">6</button>
                <button onclick="enterNumber2(1)" class="p-4 bg-gray-200 rounded-lg text-xl">1</button>
                <button onclick="enterNumber2(2)" class="p-4 bg-gray-200 rounded-lg text-xl">2</button>
                <button onclick="enterNumber2(3)" class="p-4 bg-gray-200 rounded-lg text-xl">3</button>
                <button onclick="clearInput2()"
                    class="bg-red-100 text-red-600 rounded-lg text-2xl font-bold py-4">ลบ</button>
                <button onclick="enterNumber2(0)" class="p-4 bg-gray-200 rounded-lg text-xl">0</button>
            </div>

            <!-- Confirm Button -->
            <div>
                <button onclick="saveQuantity()" class="w-full py-3 bg-blue-500 text-white text-2xl font-bold rounded-lg">
                    ตกลง
                </button>
            </div>
        </div>
    </div>

    <script>
        function toggleSidebar() {
            const sidebar = document.getElementById('cartSidebar');

            if (sidebar.style.width === '0px' || sidebar.style.width === '') {
                sidebar.style.width = '100%'; // สำหรับหน้าจอเล็ก (Mobile)
                sidebar.classList.add('lg:hidden'); // สำหรับหน้าจอใหญ่
            } else {
                sidebar.style.width = '0px'; // ปิด Sidebar
                sidebar.classList.remove('lg:w-2/5');
            }
        }
    </script>
    <script>
        let currentProductId = null; // เก็บ ID สินค้าที่กำลังแก้ไข
        let currentQuantity = 1; // เก็บจำนวนสินค้าปัจจุบัน

        // ฟังก์ชันเปิด Modal
        function openModal(productId, productName, quantity) {
            currentProductId = productId;
            currentQuantity = quantity;

            // อัปเดตรายละเอียดใน Modal
            // document.getElementById('modalProductName').textContent = productName;
            document.getElementById('modalQuantityDisplay').textContent = currentQuantity; // แสดงจำนวนสินค้าใน Display

            // แสดง Modal
            document.getElementById('editModal').classList.remove('hidden');
        }

        // ฟังก์ชันปิด Modal
        function closeModal() {
            document.getElementById('editModal').classList.add('hidden');
            currentProductId = null; // รีเซ็ต ID สินค้า
            currentQuantity = 1; // รีเซ็ตจำนวนสินค้า
        }

        // ฟังก์ชันเพิ่มตัวเลขผ่าน Keypad
        function enterNumber2(number) {
            currentQuantity = parseInt(`${currentQuantity}${number}`, 10);
            document.getElementById('modalQuantityDisplay').textContent = currentQuantity; // อัปเดต Display
        }

        // ฟังก์ชันใช้ Shortcut เพิ่มจำนวนสินค้า
        function enterShortcut2(amount) {
            currentQuantity += amount;
            document.getElementById('modalQuantityDisplay').textContent = currentQuantity; // อัปเดต Display
        }

        // ฟังก์ชันล้างจำนวนสินค้า
        function clearInput2() {
            currentQuantity = 0;
            document.getElementById('modalQuantityDisplay').textContent = currentQuantity; // อัปเดต Display
        }

        // ฟังก์ชันบันทึกจำนวนสินค้า
        function saveQuantity() {
            if (isNaN(currentQuantity) || currentQuantity < 1) {
                alert('กรุณากรอกจำนวนที่ถูกต้อง');
                return;
            }

            // อัปเดตจำนวนสินค้าในตะกร้า
            if (cart[currentProductId]) {
                cart[currentProductId].quantity = currentQuantity;
            }

            updateCart(); // อัปเดต UI
            closeModal(); // ปิด Modal
        }
    </script>
    <script>
        let cart = {}; // ตะกร้าสินค้า

        // เพิ่มสินค้าในตะกร้า
        function addToCart(productId, productName, productPrice) {

            // จำลองการโหลดข้อมูล (หรือใช้ AJAX จริง)
            setTimeout(() => {
                // เพิ่มสินค้าในตะกร้า
                if (cart[productId]) {
                    cart[productId].quantity += 1; // เพิ่มจำนวนสินค้าที่มีอยู่แล้ว
                } else {
                    cart[productId] = {
                        name: productName,
                        price: productPrice,
                        quantity: 1,
                    };
                }

                // อัปเดตตะกร้า
                updateCart();

            }, 0); // จำลองการโหลด 500ms
        }

        // อัปเดตตะกร้าสินค้า
        function updateCart() {
            const cartContainer = document.getElementById("cartContainer");
            const cartContainerSmall = document.getElementById("cartContainerSmall");
            const totalAmount = document.getElementById("totalAmount");
            const totalAmount2 = document.getElementById("totalAmount2");
            const totalAmountSmall = document.getElementById("totalAmountSmall");
            const totalItems = document.getElementById("totalItems");
            const totalItems2 = document.getElementById("totalItems2");
            const totalQuantity = document.getElementById("totalQuantity");
            const totalQuantity2 = document.getElementById("totalQuantity2");
            const totalQuantitySmall = document.getElementById("totalQuantitySmall");
            const qrTotal = document.getElementById("qrTotal");
            const qrImage = document.getElementById('qr');

            cartContainer.innerHTML = ""; // ล้างรายการเก่า
            cartContainerSmall.innerHTML = ""; // ล้างรายการเก่า
            let total = 0;
            let itemsCount = 0;
            let totalQty = 0;

            for (const productId in cart) {
                const item = cart[productId];
                const itemTotal = item.price * item.quantity;
                total += itemTotal;
                itemsCount += 1;
                totalQty += item.quantity;

                // เพิ่มสินค้าใน HTML
                cartContainer.innerHTML += `
                    <div class="w-full mt-2 p-5 px-4 bg-sky-50 rounded-xl overflow-y-auto">
                        <div class="flex justify-between">
                            <h1 class="text-xl">${item.name}</h1>
                            <h1 class="text-2xl">${itemTotal.toFixed(2)}</h1>
                        </div>
                        <div class="mt-2 flex justify-between">
                            <button class="px-4 py-2 bg-white rounded hover:bg-red-50" onclick="removeFromCart(${productId})">
                                <i class="fa-solid fa-trash-can" style="color: #ff0000;"></i>
                            </button>
                            <button onclick="openModal(${productId}, '${item.name}', ${item.quantity})">
                                <p class="text-xl bg-white rounded-lg px-4 py-2">x${item.quantity}</p>
                            </button>
                        </div>
                    </div>
                `;
                cartContainerSmall.innerHTML += `
                    <div class="w-full mt-2 p-5 px-4 bg-sky-50 rounded-xl overflow-y-auto">
                        <div class="flex justify-between">
                            <h1 class="text-xl">${item.name}</h1>
                            <h1 class="text-2xl">${itemTotal.toFixed(2)}</h1>
                        </div>
                        <div class="mt-2 flex justify-between">
                            <button class="px-4 py-2 bg-white rounded hover:bg-red-50" onclick="removeFromCart(${productId})">
                                <i class="fa-solid fa-trash-can" style="color: #ff0000;"></i>
                            </button>
                            <button onclick="openModal(${productId}, '${item.name}', ${item.quantity})">
                                <p class="text-xl bg-white rounded-lg px-4 py-2">x${item.quantity}</p>
                            </button>
                        </div>
                    </div>
                `;
            }

            // อัปเดตยอดรวม
            totalAmount.textContent = `${total.toFixed(2)} ฿`;
            qrTotal.textContent = `${total.toFixed(2)} ฿`;
            qrImage.src = `{{ asset('img/QR Kbank.png') }}`;
            totalAmount2.textContent = `${total.toFixed(2)} ฿`;
            totalAmountSmall.textContent = `${total.toFixed(2)} ฿`;
            totalItems.textContent = `${itemsCount} รายการ`;
            totalItems2.textContent = `${itemsCount} รายการ`;
            totalQuantity.textContent = `${totalQty} ชิ้น`;
            totalQuantity2.textContent = `${totalQty} ชิ้น`;
            totalQuantitySmall.textContent = `${totalQty}`;
        }

        // ลบสินค้าออกจากตะกร้า
        function removeFromCart(productId) {
            delete cart[productId]; // ลบสินค้า
            updateCart();
        }

        // ชำระเงิน
        function checkout() {
            const totalAmount = parseFloat(document.getElementById("totalAmount").textContent.replace('฿', '')) || 0;

            if (Object.keys(cart).length === 0) {
                Swal.fire({
                    icon: 'warning',
                    title: 'ไม่มีสินค้าในตะกร้า!',
                    text: 'กรุณาเพิ่มสินค้าเพื่อชำระเงิน',
                    confirmButtonText: 'ตกลง',
                });
                return;
            }

            // เปิด Modal ชำระเงิน
            openPaymentModal(totalAmount);
        }

        function openPaymentModal(totalAmount) {
            // แสดง Modal
            document.getElementById('paymentModal').classList.remove('hidden');

            // กำหนดยอดรวมที่จะแสดงใน Modal
            document.getElementById('totalAmountModal').textContent = `${totalAmount.toFixed(2)} ฿`;

            // รีเซ็ตฟิลด์ต่าง ๆ
            document.getElementById('paymentMethod').value = 'cash'; // ตั้งค่าเริ่มต้นเป็นเงินสด
            document.getElementById('receivedAmount').value = '';
            document.getElementById('proofImage').value = '';
            document.getElementById('changeAmount').textContent = '0.00';
            document.getElementById('qrPayment').classList.add('hidden');
            document.getElementById('cashPayment').classList.remove('hidden');
            document.getElementById('changeDisplay').classList.add('hidden');
        }

        document.getElementById('paymentMethod').addEventListener('change', function() {
            const method = this.value;
            const cashPayment = document.getElementById('cashPayment');
            const qrPayment = document.getElementById('qrPayment');
            const changeDisplay = document.getElementById('changeDisplay');

            if (method === 'cash') {
                cashPayment.classList.remove('hidden');
                qrPayment.classList.add('hidden');
                changeDisplay.classList.add('hidden');
            } else if (method === 'qr') {
                qrPayment.classList.remove('hidden');
                cashPayment.classList.add('hidden');
                changeDisplay.classList.add('hidden');
            }
        });


        function closePaymentModal() {
            document.getElementById('paymentModal').classList.add('hidden');
            document.getElementById('totalAmountadd').textContent = '0.00';
        }

        async function submitPayment() {
            const paymentMethod = document.getElementById('paymentMethod').value;
            const totalAmount = parseFloat(document.getElementById("totalAmount").textContent.replace('฿', '')) || 0;

            const cartItems = Object.keys(cart).map((productId) => ({
                id: productId, // ส่ง ID ของสินค้า
                name: cart[productId].name,
                price: cart[productId].price,
                quantity: cart[productId].quantity,
            }));

            if (paymentMethod === 'cash') {
                const receivedAmount = parseFloat(document.getElementById('receivedAmount').value) || 0;

                if (receivedAmount < totalAmount) {
                    Swal.fire({
                        icon: 'error',
                        title: 'จำนวนเงินไม่เพียงพอ',
                        text: 'กรุณากรอกจำนวนเงินให้มากกว่ายอดรวม',
                        confirmButtonText: 'ตกลง',
                    });
                    return;
                }

                Swal.fire({
                    title: "กำลังประมวลผล",
                    text: "กรุณารอสักครู่...",
                    icon: "info",
                    allowOutsideClick: false, // ป้องกันการคลิกด้านนอก
                    allowEscapeKey: false, // ป้องกันการกด Esc
                    showConfirmButton: false // ไม่แสดงปุ่มใดๆ
                });
                
                const paymentData = {
                    payment_method: 'cash',
                    total_amount: totalAmount,
                    received_amount: receivedAmount,
                    change: receivedAmount - totalAmount,
                    cart: cartItems, // ส่งข้อมูลที่มี ID
                };

                try {
                    const response = await fetch('/api/orders', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        },
                        body: JSON.stringify(paymentData),
                    });

                    if (response.ok) {
                        const data = await response.json(); // ดึงข้อมูลจากการตอบกลับของ Backend
                        const changeAmount = data.order.change || 0; // ยอดเงินทอน (Default = 0)


                        Swal.fire({
                            icon: 'success',
                            html: `<strong class="text-3xl">เงินทอน: ${changeAmount.toFixed(2)} ฿</strong><br><br>บันทึกคำสั่งซื้อเรียบร้อย`,
                            confirmButtonText: 'ตกลง',
                        }).then(() => {
                            closePaymentModal(); // ปิด Modal
                            cart = {}; // ล้างตะกร้า
                            updateCart(); // อัปเดต UI
                        });
                    } else {
                        const error = await response.json();
                        throw new Error(error.message || 'การบันทึกคำสั่งซื้อล้มเหลว');
                    }

                } catch (error) {
                    Swal.fire({
                        icon: 'error',
                        title: 'เกิดข้อผิดพลาด',
                        text: error.message,
                        confirmButtonText: 'ตกลง',
                    });
                }
            } else if (paymentMethod === 'qr') {
                const proofImage = document.getElementById('proofImage').files[0];

                if (!proofImage) {
                    Swal.fire({
                        icon: 'error',
                        title: 'กรุณาอัปโหลดหลักฐานการชำระเงิน',
                        text: 'ไฟล์หลักฐานการโอนจำเป็นสำหรับการชำระเงินด้วย QR Code',
                        confirmButtonText: 'ตกลง',
                    });
                    return;
                }

                Swal.fire({
                    title: "กำลังประมวลผล",
                    text: "กรุณารอสักครู่...",
                    icon: "info",
                    allowOutsideClick: false, // ป้องกันการคลิกด้านนอก
                    allowEscapeKey: false, // ป้องกันการกด Esc
                    showConfirmButton: false // ไม่แสดงปุ่มใดๆ
                });

                const formData = new FormData();
                formData.append('payment_method', 'qr');
                formData.append('total_amount', totalAmount);
                formData.append('proof_image', proofImage);
                formData.append('cart', JSON.stringify(cartItems)); // ส่งข้อมูลที่มี ID

                try {
                    const response = await fetch('/api/orders', {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        },
                        body: formData,
                    });

                    if (response.ok) {
                        Swal.fire({
                            icon: 'success',
                            title: 'ชำระเงินสำเร็จ!',
                            text: 'บันทึกคำสั่งซื้อเรียบร้อย',
                            confirmButtonText: 'ตกลง',
                        }).then(() => {
                            closePaymentModal();
                            cart = {};
                            updateCart();
                            location.reload();
                        });
                    } else {
                        const error = await response.json();
                        throw new Error(error.message || 'การบันทึกคำสั่งซื้อล้มเหลว');
                    }
                } catch (error) {
                    Swal.fire({
                        icon: 'error',
                        title: 'เกิดข้อผิดพลาด',
                        text: error.message,
                        confirmButtonText: 'ตกลง',
                    });
                }
            }
        }

        function filterProducts(category) {
            const productCards = document.querySelectorAll('.product-card');

            productCards.forEach(card => {
                const productCategory = card.getAttribute('data-category');

                if (category === 'all' || productCategory === category) {
                    card.classList.remove('hidden'); // แสดงสินค้าที่ตรงกับประเภท
                } else {
                    card.classList.add('hidden'); // ซ่อนสินค้าที่ไม่ตรงกับประเภท
                }
            });
        }
    </script>
@endSection
