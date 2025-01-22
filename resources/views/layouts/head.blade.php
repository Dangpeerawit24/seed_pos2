<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>ระบบขายเมล็ดพันธุ์</title>
<link rel="icon" type="" href="{{ asset('img/logo.png') }}" />
<script src="{{ asset('style.js') }}"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
<link rel="stylesheet" href="{{ asset('sweetalert2.min.css') }}">
<script src="{{asset ('sweetalert2@11.js') }}"></script>
<script src="{{ asset('xlsx.full.min.js') }}"></script>
<meta name="csrf-token" content="{{ csrf_token() }}">
<style>
    h1.line-clamp-2 {
        display: -webkit-box;
        -webkit-line-clamp: 1;
        -webkit-box-orient: vertical;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: normal;
    }

    /* ซ่อน scrollbar */
    .scrollbar-hide {
        -ms-overflow-style: none;
        /* IE and Edge */
        scrollbar-width: none;
        /* Firefox */
    }

    .scrollbar-hide::-webkit-scrollbar {
        display: none;
        /* Chrome, Safari, Opera */
    }
</style>
