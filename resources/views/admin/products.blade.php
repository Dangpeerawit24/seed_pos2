@extends('layouts.main')
@php
    $manu = 'จัดการข้อมูลสินค้า';
@endphp
@Section('content')
    <div class="flex flex-col md:flex-row gap-x-5">
        <h3 class="text-3xl m-0 md:mb-10">จัดการข้อมูลสินค้า</h3>
        <div>
            <button id="openModal" class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600">
                เพิ่มสินค้า
            </button>
        </div>
    </div>
    <div class=" mx-auto px-4">
        <!-- Search Box -->
        <div class="flex flex-col mt-2 md:mt-0 md:flex-row justify-between items-center mb-4">
            <div>
                <button id="copy-table" class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600">Copy Table</button>
                <button id="export-excel" class="px-4 py-2 bg-green-500 text-white rounded hover:bg-green-600">Export to
                    Excel</button>
            </div>
            <input type="text" id="search" class="mt-5 md:mt-0 px-4 py-2 border rounded"
                placeholder="Search products...">
        </div>

        <!-- Table -->
        <div class="overflow-hidden rounded-lg shadow-lg overflow-x-auto">
            <table class="min-w-full border-collapse bg-white">
                <thead>
                    <tr class="bg-gradient-to-r h-12 from-sky-600 to-sky-500">
                        <th class="px-6 py-3 text-nowrap  text-center text-md font-semibold text-white">#</th>
                        <th class="px-6 py-3 text-nowrap  text-center w-10 text-md font-semibold text-white">รูปสินค้า</th>
                        <th class="px-6 py-3 text-nowrap  text-center text-md font-semibold text-white">ชื่อสินค้า</th>
                        <th class="px-6 py-3 text-nowrap  text-center text-md font-semibold text-white">สต็อก</th>
                        <th class="px-6 py-3 text-nowrap  text-center text-md font-semibold text-white">ประเภท</th>
                        <th class="px-6 py-3 text-nowrap  text-center  w-10 text-md font-semibold text-white">การเปลื่ยนแปลง
                        </th>
                    </tr>
                </thead>
                <tbody id="table-body" class="divide-y divide-gray-200">

                </tbody>
            </table>
        </div>
        <div class="flex justify-center gap-5 items-center my-4">
            <button id="prev" class="px-3 py-1 bg-gray-200 rounded hover:bg-gray-300" disabled>Previous</button>
            <span id="page-info">Page 1 of 1</span>
            <button id="next" class="px-3 py-1 bg-gray-200 rounded hover:bg-gray-300">Next</button>
        </div>
    </div>

    <!-- Modal -->
    <div id="modal" class="fixed inset-0 p-2 bg-gray-900 bg-opacity-50 flex items-center justify-center hidden z-50">
        <div class="bg-white rounded-xl shadow-xl w-full max-w-2xl overflow-hidden">
            <!-- Modal Header -->
            <div class="px-6 py-4 bg-blue-500 text-white flex justify-between items-center">
                <h2 class="text-xl font-semibold">เพิ่มสินค้า</h2>
                <button id="closeModal" class="text-white hover:text-gray-300 text-2xl">
                    &times;
                </button>
            </div>

            <!-- Modal Body -->
            <div class="px-6 py-4">
                <form action="{{ route('products.store') }}" id="productForm" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label for="name" class="block text-sm font-medium text-gray-700 mb-1">ชื่อสินค้า</label>
                            <input type="text" name="name" id="name" value="{{ old('name') }}"
                                class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring focus:ring-blue-200"
                                placeholder="กรอก ชื่อสินค้า" required>
                        </div>
                        <div>
                            <label for="price" class="block text-sm font-medium text-gray-700 mb-1">ราคาขายปกติ</label>
                            <input type="number" name="price" id="price" value="{{ old('price') }}" step="0.01"
                                class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring focus:ring-blue-200"
                                placeholder="กรอก ราคาขาย" required>
                        </div>
                        {{-- <div>
                            <label for="cost_price" class="block text-sm font-medium text-gray-700 mb-1">ราคาต้นทุน</label>
                            <input type="number" name="cost_price" id="cost_price" value="{{ old('cost_price') }}"
                                step="0.01"
                                class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring focus:ring-blue-200"
                                placeholder="กรอก ราคาต้นทุน" required>
                        </div> --}}
                        {{-- <div>
                            <label for="stock_quantity" class="block text-sm font-medium text-gray-700 mb-1">สต็อก</label>
                            <input type="number" name="stock_quantity" id="stock_quantity"
                                value="{{ old('stock_quantity') }}"
                                class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring focus:ring-blue-200"
                                placeholder="กรอก จำนวนสต็อก" required>
                        </div> --}}
                        <div>
                            <label for="restock_level" class="block text-sm font-medium text-gray-700 mb-1">Restock
                                Level</label>
                            <input type="number" name="restock_level" id="restock_level" value="{{ old('restock_level') }}"
                                class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring focus:ring-blue-200"
                                placeholder="Restock Level" required>
                        </div>
                    </div>

                    <div class="mt-4">
                        <label for="description" class="block text-sm font-medium text-gray-700 mb-1">รายละเอียด</label>
                        <textarea name="description" id="description" rows="4"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring focus:ring-blue-200"
                            placeholder="กรอกรายละเอียด">{{ old('description') }}</textarea>
                    </div>

                    <div class="mt-4">
                        <label for="category" class="block text-sm font-medium text-gray-700 mb-1">ประเภทสินค้า</label>
                        <select name="category" id="category"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring focus:ring-blue-200">
                            <option value="">เลือกประเภทสินค้า</option>
                            @foreach ($categories as $category)
                                <option value="{{ $category->name }}"
                                    {{ old('category_id') == $category->name ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mt-4">
                        <label for="image" class="block text-sm font-medium text-gray-700 mb-1">รูปสินค้า</label>
                        <input type="file" name="image" id="image"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring focus:ring-blue-200">
                    </div>
                </form>
            </div>

            <!-- Modal Footer -->
            <div class="px-6 py-4 bg-gray-100 flex justify-end items-center space-x-3">
                <button id="closeModalFooter" class="px-4 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600">
                    Cancel
                </button>
                <button type="submit" form="productForm"
                    class="px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600">
                    Save
                </button>
            </div>
        </div>
    </div>

    <div id="editModal" class="fixed inset-0 p-2 bg-gray-900 bg-opacity-50 flex items-center justify-center hidden z-50">
        <div class="bg-white rounded-xl shadow-xl w-full max-w-2xl overflow-hidden">
            <!-- Modal Header -->
            <div class="px-6 py-4 bg-blue-500 text-white flex justify-between items-center">
                <h2 class="text-xl font-semibold">แก้ไขข้อมูลสินค้า</h2>
                <button id="closeModal2" class="text-white hover:text-gray-300 text-2xl">
                    &times;
                </button>
            </div>

            <!-- Modal Body -->
            <div class="px-6 py-4">
                <form action="{{ route('products.store') }}" id="productForm2" method="POST"
                    enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label for="name" class="block text-sm font-medium text-gray-700 mb-1">ชื่อสินค้า</label>
                            <input type="text" name="name" id="name2" value="{{ old('name') }}"
                                class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring focus:ring-blue-200"
                                placeholder="กรอก ชื่อสินค้า" required>
                        </div>
                        <div>
                            <label for="price" class="block text-sm font-medium text-gray-700 mb-1">ราคาขายปกติ</label>
                            <input type="number" name="price" id="price2" value="{{ old('price') }}"
                                step="0.01"
                                class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring focus:ring-blue-200"
                                placeholder="กรอก ราคาขาย" required>
                        </div>
                        {{-- <div>
                            <label for="cost_price"
                                class="block text-sm font-medium text-gray-700 mb-1">ราคาต้นทุน</label>
                            <input type="number" name="cost_price" id="cost_price2" value="{{ old('cost_price') }}"
                                step="0.01"
                                class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring focus:ring-blue-200"
                                placeholder="กรอก ราคาต้นทุน" required>
                        </div> --}}
                        {{-- <div class=" hidden">
                            <label for="stock_quantity" class="block text-sm font-medium text-gray-700 mb-1">สต็อก</label>
                            <input type="number" name="stock_quantity" id="stock_quantity2"
                                value="{{ old('stock_quantity') }}"
                                class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring focus:ring-blue-200"
                                placeholder="กรอก จำนวนสต็อก" required>
                        </div> --}}
                        <div>
                            <label for="restock_level"
                                class="block text-sm font-medium text-gray-700 mb-1">restock_level</label>
                            <input type="number" name="restock_level" id="restock_level2"
                                value="{{ old('restock_level') }}"
                                class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring focus:ring-blue-200"
                                placeholder="Restock Level" required>
                        </div>
                    </div>

                    <div class="mt-4">
                        <label for="description" class="block text-sm font-medium text-gray-700 mb-1">รายละเอียด</label>
                        <textarea name="description" id="description2" rows="4"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring focus:ring-blue-200"
                            placeholder="กรอกรายละเอียด">{{ old('description') }}</textarea>
                    </div>

                    <div class="mt-4">
                        <label for="category" class="block text-sm font-medium text-gray-700 mb-1">ประเภทสินค้า</label>
                        <select name="category" id="category_id2"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring focus:ring-blue-200">
                            <option value="">เลือกประเภทสินค้า</option>
                            @foreach ($categories as $category)
                                <option value="{{ $category->name }}"
                                    {{ old('category_id') == $category->name ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mt-4">
                        <label for="image" class="block text-sm font-medium text-gray-700 mb-1">รูปสินค้า</label>
                        <input type="file" name="image" id="image2"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring focus:ring-blue-200">
                    </div>
                </form>
            </div>

            <!-- Modal Footer -->
            <div class="px-6 py-4 bg-gray-100 flex justify-end items-center space-x-3">
                <button id="closeModalFooter2" class="px-4 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600">
                    Cancel
                </button>
                <button type="submit" form="productForm2"
                    class="px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600">
                    Save
                </button>
            </div>
        </div>
    </div>

    <div id="imageModal"
        class="fixed inset-0 text-center bg-gray-900 bg-opacity-50 flex items-center justify-center hidden z-50">
        <div class="bg-white text-center rounded-xl shadow-lg max-w-4xl w-auto">
            <!-- Modal Header -->
            <div class="px-6 py-4 bg-blue-500 text-white flex justify-between items-center">
                <h5 class="text-lg font-semibold">รูปสินค้า</h5>
                <button id="closeImageModal" class="text-white hover:text-gray-300 text-2xl">&times;</button>
            </div>
            <!-- Modal Body -->
            <div class="px-6 py-4 text-center">
                <img id="modalImage" src="" class=" max-w-80 md:max-w-xl h-auto rounded-lg" alt="หลักฐานการโอน">
            </div>
        </div>
    </div>
    <script>
        // เปิด Modal
        function openImageModal(imageSrc) {
            const modal = document.getElementById('imageModal');
            const modalImage = document.getElementById('modalImage');
            modalImage.src = imageSrc;
            modal.classList.remove('hidden');
            window.addEventListener('click', (e) => {
                if (e.target === modal) {
                    modal.classList.add('hidden');
                    document.getElementById('loader').classList.add('hidden');
                }
            });
        }

        // ปิด Modal
        document.getElementById('closeImageModal').addEventListener('click', () => {
            const modal = document.getElementById('imageModal');
            modal.classList.add('hidden');
            document.getElementById('loader').classList.add('hidden');
        });
    </script>

    <script>
        const modal = document.getElementById('modal');
        const openModal = document.getElementById('openModal');
        const closeModal = document.getElementById('closeModal');
        const closeModalFooter = document.getElementById('closeModalFooter');

        // เปิด Modal
        openModal.addEventListener('click', () => {
            modal.classList.remove('hidden');
        });

        // ปิด Modal
        closeModal.addEventListener('click', () => {
            modal.classList.add('hidden');
        });

        closeModalFooter.addEventListener('click', () => {
            modal.classList.add('hidden');
        });

        // ปิด Modal เมื่อคลิกนอก Modal
        window.addEventListener('click', (e) => {
            if (e.target === modal) {
                modal.classList.add('hidden');
            }
        });
    </script>
    <script>
        function openEditModal(id, name, price, restock_level, description, category) {
            // เปิด Modal
            const modal = document.getElementById('editModal');
            modal.classList.remove('hidden');

            // เติมข้อมูลในฟอร์ม
            const form = document.getElementById('productForm2');
            form.action = `/admin/products/update/${id}`; // เปลี่ยน action ของฟอร์ม

            document.getElementById('name2').value = name;
            document.getElementById('price2').value = price;
            document.getElementById('restock_level2').value = restock_level;
            document.getElementById('description2').value = description;
            document.getElementById('category_id2').value = category;

            window.addEventListener('click', (e) => {
                if (e.target === modal) {
                    modal.classList.add('hidden');
                }
            });
        }

        // ฟังก์ชันสำหรับปิด Modal
        document.getElementById('closeModal2').addEventListener('click', () => {
            document.getElementById('editModal').classList.add('hidden');
        });

        document.getElementById('closeModalFooter2').addEventListener('click', () => {
            document.getElementById('editModal').classList.add('hidden');
        });
    </script>

    <script>
        const csrfToken = "{{ csrf_token() }}";
    </script>
    <script>
        function confirmDelete(productId) {
            Swal.fire({
                title: 'คุณแน่ใจหรือไม่?',
                text: 'ข้อมูลนี้จะถูกลบและไม่สามารถกู้คืนได้!',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'ใช่, ลบเลย!',
                cancelButtonText: 'ยกเลิก'
            }).then((result) => {
                if (result.isConfirmed) {
                    // ส่งฟอร์มหลังจากได้รับการยืนยัน
                    document.getElementById(`deleteForm-${productId}`).submit();
                }
            });
        }
    </script>
    <script>
        function showImage(src) {
            console.log(src);
            document.getElementById('modalImage').src = src;
        }
    </script>
    <script>
        const products = @json($products); // ดึงข้อมูลจาก Controller
        const rowsPerPage = 300;
        let currentPage = 1;
        let filteredData = products;

        // อ้างอิง DOM
        const tableBody = document.getElementById('table-body');
        const pageInfo = document.getElementById('page-info');
        const prevButton = document.getElementById('prev');
        const nextButton = document.getElementById('next');
        const searchInput = document.getElementById('search');

        // ฟังก์ชันแสดงข้อมูลในตาราง
        function renderTable() {
            const startIndex = (currentPage - 1) * rowsPerPage;
            const endIndex = startIndex + rowsPerPage;
            const currentData = filteredData.slice(startIndex, endIndex);

            tableBody.innerHTML = '';
            currentData.forEach((product, index) => {
                const row = `
                       <tr>
                           <td class="px-6 py-2 text-center text-md text-gray-700">${startIndex + index + 1}</td>
                           <td class="px-6 py-2 text-center text-md text-gray-700">
                               <a href="#" data-toggle="modal" data-target="#imageModal"
                                            onclick="openImageModal('/img/product/${product.image}')">
                                            ${product.image ? `<img src="/img/product/${product.image}" alt="${product.name}" style="max-width: 100px;">` : 'No Image'}
                               </a> 
                           </td>   
                           <td class="px-6 py-2 text-nowrap  text-center text-md text-gray-700">${product.name}</td>
                           <td class="px-6 py-2 text-nowrap  text-center text-md text-gray-700">${product.stock_quantity}</td>
                           <td class="px-6 py-2 text-nowrap  text-center text-md text-gray-700">${product.category}</td>
                           <td class="px-6 py-2 text-nowrap  text-center text-md text-gray-700">
                               <div class="flex justify-center gap-2">
                                   <button 
                                       class="px-4 py-2 bg-yellow-300 text-black rounded hover:bg-yellow-600"
                                       onclick="openEditModal(${product.id}, '${product.name}', ${product.price}, ${product.restock_level}, '${product.description}', '${product.category}')">
                                       Edit
                                   </button>
                                   <form id="deleteForm-${product.id}" action="/admin/products/destroy/${product.id}" method="POST">
                                       <input type="hidden" name="_method" value="DELETE">
                                       <input type="hidden" name="_token" value="${csrfToken}">
                                       <button type="button" onclick="confirmDelete(${product.id})" 
                                           class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-900">
                                           Delete
                                       </button>
                                   </form>
                               </div>
                           </td>
                       </tr>
                   `;
                tableBody.insertAdjacentHTML('beforeend', row);
            });

            // อัปเดต Pagination Info
            const totalPages = Math.ceil(filteredData.length / rowsPerPage);
            pageInfo.textContent = `Page ${currentPage} of ${totalPages}`;
            prevButton.disabled = currentPage === 1;
            nextButton.disabled = currentPage === totalPages;
        }

        // ฟังก์ชันเปลี่ยนหน้า
        function changePage(increment) {
            currentPage += increment;
            renderTable();
        }

        // ฟังก์ชันการค้นหา
        function searchTable(query) {
            filteredData = products.filter((products) =>
                products.name.toLowerCase().includes(query.toLowerCase()) ||
                products.category.toLowerCase().includes(query.toLowerCase()) ||
                products.price.toString().includes(query.toLowerCase())
            );
            currentPage = 1;
            renderTable();
        }

        // Event Listeners
        prevButton.addEventListener('click', () => changePage(-1));
        nextButton.addEventListener('click', () => changePage(1));
        searchInput.addEventListener('input', (e) => searchTable(e.target.value));

        // เริ่มแสดงข้อมูล
        renderTable();

        document.getElementById('export-excel').addEventListener('click', () => {
            const table = document.querySelector('table'); // ดึงข้อมูลจากตาราง
            const workbook = XLSX.utils.table_to_book(table, {
                sheet: "Sheet 1"
            }); // แปลงตารางเป็น workbook
            XLSX.writeFile(workbook, "table_data.xlsx"); // ดาวน์โหลดไฟล์ Excel ชื่อ "table_data.xlsx"
        });

        document.getElementById('copy-table').addEventListener('click', () => {
            const table = document.querySelector('table');
            const rows = Array.from(table.rows);

            const columnsToCopy = [0, 2, 3, 4, 5];

            const text = rows.map(row => {
                return Array.from(row.cells)
                    .filter((_, index) => columnsToCopy.includes(index)) // เลือกเฉพาะคอลัมน์ที่ต้องการ
                    .map(cell => cell.innerText)
                    .join('\t');
            }).join('\n');

            navigator.clipboard.writeText(text).then(() => {

                Swal.fire({
                    title: 'สำเร็จ!',
                    text: 'ข้อมูลในตารางถูกคัดลอกไปยังคลิปบอร์ดแล้ว!',
                    icon: 'success',
                    confirmButtonText: 'ตกลง',
                    timer: 3000,
                    timerProgressBar: true
                });
            }).catch(err => {

                Swal.fire({
                    title: 'เกิดข้อผิดพลาด!',
                    text: 'ไม่สามารถคัดลอกข้อมูลในตารางได้!',
                    icon: 'error',
                    confirmButtonText: 'ลองใหม่'
                });
                console.error('ไม่สามารถคัดลอกข้อมูลในตาราง:', err);
            });
        });
    </script>
@endSection
