<div class="w-full mx-0 px-4 sm:px-4 lg:px-8">
    <div class="flex justify-between h-16 items-center">
        <!-- Logo -->
        <a href="/{{ Auth::user()->type }}/pos" class="text-lg flex flex-row gap-2 items-center font-semibold "><img
                src="{{ asset('img/logo.png') }}" width="50px" alt=""> ระบบขายเมล็ดพันธุ์</a>
        <!-- Menu Button -->
        <div class="xl:hidden">
            <label for="menu-toggle" class="cursor-pointer">
                <svg class="w-6 h-6 " fill="none" stroke="currentColor" viewBox="0 0 24 24"
                    xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16m-7 6h7">
                    </path>
                </svg>
            </label>
            <input type="checkbox" id="menu-toggle" class="hidden">
        </div>
        <!-- Desktop Links -->
        <div class="hidden xl:flex items-center justify-between gap-4	border-sky-100	">
            <div class=" items-center gap-1">
                <h2 class="text-xl">{{ Auth::user()->name }}</h2>
                @if (Auth::user()->type === 'admin')
                    <h2 class="text-md">สิทธิ์การใช้งาน : {{ Auth::user()->type }}</h2>
                @elseif (Auth::user()->type === 'manager')
                    <h2 class="text-md">สิทธิ์การใช้งาน : ผู้จัดการ</h2>
                @else
                    <h2 class="text-md">สิทธิ์การใช้งาน : พนักงาน</h2>
                @endif
            </div>
            <form id="logout-form" method="POST" action="{{ route('logout') }}">
                @csrf
                <a href="#" id="logout-btn"
                    class="flex items-center gap-2 p-2 rounded bg-sky-600 text-white hover:bg-sky-700">
                    Logout
                </a>
            </form>
        </div>
    </div>
</div>
<!-- Slide-in Menu -->
<div id="menu"
    class="fixed inset-0 bg-sky-900 text-white z-20 transform -translate-x-full transition-transform duration-300 ease-in-out">
    <div class="flex flex-col h-full">
        <!-- Close Button -->
        <div class="flex justify-between p-4">
            <h1 class="text-3xl">
                เมนูจัดการระบบ
            </h1>
            <button id="close-menu" class="text-white hover:text-gray-900">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                    xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12">
                    </path>
                </svg>
            </button>
        </div>
        <!-- Menu Links -->
        <div class="flex flex-col h-full space-y-6">
            <ul class="flex-1 m-4 p-2 space-y-4">
                @if (Auth::user()->type === 'admin')
                    <li class="">
                        <a href="/admin/pos" class="flex items-center gap-2 p-2 rounded hover:bg-sky-800">
                            <img src="{{ asset('img/submenu/ic_menu_store_normal.svg') }}" width="30px" height="30px"
                                alt="">
                            หน้าขาย
                        </a>
                    </li>
                    <li class="">
                        <a href="/admin/dashboard" class="flex items-center gap-2 p-2 rounded hover:bg-sky-800">
                            <img src="{{ asset('img/submenu/ic_dashboard_normal.svg') }}" width="30px" height="30px"
                                alt="">
                            แดชบอร์ด
                        </a>
                    </li>
                    <li>
                        <a href="/admin/sales-history" class="flex items-center gap-2 p-2 rounded hover:bg-sky-800">
                            <img src="{{ asset('img/submenu/ic_menu_receipt_normal.svg') }}" width="30px"
                                height="30px" alt="">
                            ประวัติการขาย
                        </a>
                    </li>
                    {{-- <li>
                        <a href="/admin/cashdrawer" class="flex items-center gap-2 p-2 rounded hover:bg-sky-800">
                            <img src="{{ asset('img/submenu/ic_menu_expense.svg') }}" width="30px" height="30px"
                                alt="">
                            จัดการกล่องเงินสด

                        </a>
                    </li> --}}
                    <li>
                        <a href="/admin/stock" class="flex items-center gap-2 p-2 rounded hover:bg-sky-800">
                            <img src="{{ asset('img/submenu/ic_menu_history_normal.svg') }}" width="30px"
                                height="30px" alt="">
                            จัดการสต็อกสินค้า
                        </a>
                    </li>
                    <li>
                        <a href="/admin/products" class="flex items-center gap-2 p-2 rounded hover:bg-sky-800">
                            <img src="{{ asset('img/submenu/ic_menu_fullstock_v2_normal.svg') }}" width="30px"
                                height="30px" alt="">
                            จัดการข้อมูลสินค้า
                        </a>
                    </li>
                    {{-- <li>
                        <a href="/admin/categories" class="flex items-center gap-2 p-2 rounded hover:bg-sky-800">
                            <img src="{{ asset('img/submenu/ic_wholesale.svg') }}" width="30px" height="30px"
                                alt="">
                            จัดการประเภทสินค้า
                        </a>
                    </li> --}}
                    <li>
                        <a href="/admin/member" class="flex items-center gap-2 p-2 rounded hover:bg-sky-800">
                            <img src="{{ asset('img/submenu/ic_menu_staff_v2_normal.svg') }}" width="30px"
                                height="30px" alt="">
                            จัดการข้อมูลสมาชิก
                        </a>
                    </li>
                    <li>
                        <a href="/admin/users" class="flex items-center gap-2 p-2 rounded hover:bg-sky-800">
                            <img src="{{ asset('img/submenu/ic_menu_staff_v2_normal.svg') }}" width="30px"
                                height="30px" alt="">
                            จัดการพนักงาน
                        </a>
                    </li>
                @elseif (Auth::user()->type === 'staff')
                    <li class="">
                        <a href="/staff/pos" class="flex items-center gap-2 p-2 rounded hover:bg-sky-800">
                            <img src="{{ asset('img/submenu/ic_menu_store_normal.svg') }}" width="30px"
                                height="30px" alt="">
                            หน้าขาย
                        </a>
                    </li>
                    <li>
                        <a href="/staff/sales-history" class="flex items-center gap-2 p-2 rounded hover:bg-sky-800">
                            <img src="{{ asset('img/submenu/ic_menu_receipt_normal.svg') }}" width="30px"
                                height="30px" alt="">
                            ประวัติการขาย
                        </a>
                    </li>
                    <li>
                        <a href="/staff/stock" class="flex items-center gap-2 p-2 rounded hover:bg-sky-800">
                            <img src="{{ asset('img/submenu/ic_menu_history_normal.svg') }}" width="30px"
                                height="30px" alt="">
                            จัดการสต็อกสินค้า
                        </a>
                    </li>
                    <li>
                        <a href="/staff/member" class="flex items-center gap-2 p-2 rounded hover:bg-sky-800">
                            <img src="{{ asset('img/submenu/ic_menu_staff_v2_normal.svg') }}" width="30px"
                                height="30px" alt="">
                            จัดการข้อมูลสมาชิก
                        </a>
                    </li>
                @else
                    <li class="">
                        <a href="/staff/pos" class="flex items-center gap-2 p-2 rounded hover:bg-sky-800">
                            <img src="{{ asset('img/submenu/ic_menu_store_normal.svg') }}" width="30px"
                                height="30px" alt="">
                            หน้าขาย
                        </a>
                    </li>
                @endif
                <div class="flex items-center justify-between border-t p-2	border-sky-100	">
                    <div class=" items-center gap-1">
                        <h2 class="text-xl">{{ Auth::user()->name }}</h2>
                        @if (Auth::user()->type === 'admin')
                            <h2 class="text-md">สิทธิ์การใช้งาน : {{ Auth::user()->type }}</h2>
                        @elseif (Auth::user()->type === 'manager')
                            <h2 class="text-md">สิทธิ์การใช้งาน : ผู้จัดการ</h2>
                        @else
                            <h2 class="text-md">สิทธิ์การใช้งาน : พนักงาน</h2>
                        @endif
                    </div>
                    <form id="logout-form2" method="POST" action="{{ route('logout') }}">
                        @csrf
                        <a href="#" id="logout-btn2"
                            class="flex items-center gap-2 p-2 rounded bg-sky-600 text-white hover:bg-sky-700">
                            Logout
                        </a>
                    </form>
                </div>
            </ul>
        </div>
    </div>
</div>
</div>
<div class=" row flex flex-row ">
    <div class="w-64 hidden mt-20 xl:flex fixed bg-sky-500 overflow-y-auto ">
        <div class="w-64 h-screen bg-sky-900 text-white flex flex-col ">
            {{-- <div class="p-4 text-center font-bold text-2xl border-b border-sky-100">
            เมนูจัดการระบบ
        </div> --}}
            @if (Auth::user()->type === 'admin')
                <ul class="flex-1 m-4 p-2 space-y-4">
                    <li class="">
                        <a href="/admin/pos"
                            class="flex items-center gap-2 p-2 rounded {{ $manu == 'หน้าขาย' ? ' bg-sky-600 scale-125' : '' }} hover:bg-sky-800 hover:scale-110	">
                            <img src="{{ asset('img/submenu/ic_menu_store_normal.svg') }}" width="30px"
                                height="30px" alt="">
                            หน้าขาย
                        </a>
                    </li>
                    <li class="">
                        <a href="/admin/dashboard"
                            class="flex items-center gap-2 p-2 rounded {{ $manu == 'แดชบอร์ด' ? ' bg-sky-600 scale-125' : '' }} hover:bg-sky-800 hover:scale-110	">
                            <img src="{{ asset('img/submenu/ic_dashboard_normal.svg') }}" width="30px"
                                height="30px" alt="">
                            แดชบอร์ด
                        </a>
                    </li>
                    <li>
                        <a href="/admin/sales-history"
                            class="flex items-center gap-2 p-2 rounded {{ $manu == 'ประวัติการขาย' ? ' bg-sky-600 scale-125' : '' }}  hover:bg-sky-800 hover:scale-110	">
                            <img src="{{ asset('img/submenu/ic_menu_receipt_normal.svg') }}" width="30px"
                                height="30px" alt="">
                            ประวัติการขาย
                        </a>
                    </li>
                    {{-- <li>
                        <a href="/admin/cashdrawer"
                            class="flex items-center gap-2 p-2 rounded {{ $manu == 'จัดการกล่องเงินสด' ? ' bg-sky-600 scale-125' : '' }}  hover:bg-sky-800 hover:scale-110	">
                            <img src="{{ asset('img/submenu/ic_menu_expense.svg') }}" width="30px" height="30px"
                                alt="">
                            จัดการกล่องเงินสด
                        </a>
                    </li> --}}
                    <li>
                        <a href="/admin/stock"
                            class="flex items-center gap-2 p-2 rounded {{ $manu == 'จัดการสต็อกสินค้า' ? ' bg-sky-600 scale-125' : '' }} hover:bg-sky-800 hover:scale-110	">
                            <img src="{{ asset('img/submenu/ic_menu_history_normal.svg') }}" width="30px"
                                height="30px" alt="">
                            จัดการสต็อกสินค้า
                        </a>
                    </li>
                    <li>
                        <a href="/admin/products"
                            class="flex items-center gap-2 p-2 rounded {{ $manu == 'จัดการข้อมูลสินค้า' ? ' bg-sky-600 scale-125' : '' }} hover:bg-sky-800 hover:scale-110	">
                            <img src="{{ asset('img/submenu/ic_menu_fullstock_v2_normal.svg') }}" width="30px"
                                height="30px" alt="">
                            จัดการข้อมูลสินค้า
                        </a>
                    </li>
                    {{-- <li>
                        <a href="/admin/categories"
                            class="flex items-center gap-2 p-2 rounded {{ $manu == 'จัดการประเภทสินค้า' ? ' bg-sky-600 scale-125' : '' }} hover:bg-sky-800 hover:scale-110	">
                            <img src="{{ asset('img/submenu/ic_wholesale.svg') }}" width="30px" height="30px"
                                alt="">
                            จัดการประเภทสินค้า
                        </a>
                    </li> --}}
                    <li>
                        <a href="/admin/member"
                            class="flex items-center gap-2 p-2 rounded {{ $manu == 'สมาชิก' ? ' bg-sky-600 scale-125' : '' }} hover:bg-sky-800 hover:scale-110	">
                            <img src="{{ asset('img/submenu/users-svgrepo-com.svg') }}" width="30px"
                                height="30px" alt="">
                            จัดการข้อมูลสมาชิก
                        </a>
                    </li>
                    <li>
                        <a href="/admin/users"
                            class="flex items-center gap-2 p-2 rounded {{ $manu == 'พนักงาน' ? ' bg-sky-600 scale-125' : '' }} hover:bg-sky-800 hover:scale-110	">
                            <img src="{{ asset('img/submenu/ic_menu_staff_v2_normal.svg') }}" width="30px"
                                height="30px" alt="">
                            จัดการพนักงาน
                        </a>
                    </li>
                </ul>
            @elseif (Auth::user()->type === 'staff')
                <ul class="flex-1 m-4 p-2 space-y-4">
                    <li class="">
                        <a href="/staff/pos"
                            class="flex items-center gap-2 p-2 rounded {{ $manu == 'หน้าขาย' ? ' bg-sky-600 scale-125' : '' }} hover:bg-sky-800 hover:scale-110	">
                            <img src="{{ asset('img/submenu/ic_menu_store_normal.svg') }}" width="30px"
                                height="30px" alt="">
                            หน้าขาย
                        </a>
                    </li>
                    <li>
                        <a href="/staff/sales-history"
                            class="flex items-center gap-2 p-2 rounded {{ $manu == 'ประวัติการขาย' ? ' bg-sky-600 scale-125' : '' }}  hover:bg-sky-800 hover:scale-110	">
                            <img src="{{ asset('img/submenu/ic_menu_receipt_normal.svg') }}" width="30px"
                                height="30px" alt="">
                            ประวัติการขาย
                        </a>
                    </li>
                    <li>
                        <a href="/staff/stock"
                            class="flex items-center gap-2 p-2 rounded {{ $manu == 'จัดการสต็อกสินค้า' ? ' bg-sky-600 scale-125' : '' }} hover:bg-sky-800 hover:scale-110	">
                            <img src="{{ asset('img/submenu/ic_menu_history_normal.svg') }}" width="30px"
                                height="30px" alt="">
                            จัดการสต็อกสินค้า
                        </a>
                    </li>
                    <li>
                        <a href="/staff/member"
                            class="flex items-center gap-2 p-2 rounded {{ $manu == 'สมาชิก' ? ' bg-sky-600 scale-125' : '' }} hover:bg-sky-800 hover:scale-110	">
                            <img src="{{ asset('img/submenu/users-svgrepo-com.svg') }}" width="30px"
                                height="30px" alt="">
                            จัดการข้อมูลสมาชิก
                        </a>
                    </li>
                </ul>
            @else
                <ul class="flex-1 m-4 p-2 space-y-4">
                    <li class="">
                        <a href="/staff/pos"
                            class="flex items-center gap-2 p-2 rounded {{ $manu == 'หน้าขาย' ? ' bg-sky-600 scale-125' : '' }} hover:bg-sky-800 hover:scale-110	">
                            <img src="{{ asset('img/submenu/ic_menu_store_normal.svg') }}" width="30px"
                                height="30px" alt="">
                            หน้าขาย
                        </a>
                    </li>
                </ul>
            @endif
        </div>
    </div>
