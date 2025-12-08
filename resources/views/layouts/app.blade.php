<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
    <meta http-equiv="Pragma" content="no-cache">
    <meta http-equiv="Expires" content="0">
    <title>{{ $title ?? 'BooGSG Unila' }}</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap');
        body { font-family: 'Inter', sans-serif; }
        [x-cloak] { display: none !important; }
    </style>
</head>
<body class="bg-gray-50 flex flex-col min-h-screen">
    @include('layouts.navbar')
    
    <main class="flex-grow">
        @yield('content')
    </main>
    
    @include('layouts.footer')
    
    <!-- Flash toast for public pages -->
    <div id="flash-toast" class="fixed top-6 right-6 z-50 hidden">
        <div id="flash-inner" class="max-w-sm bg-green-600 text-white px-4 py-3 rounded-lg shadow-lg"></div>
    </div>

    <div id="flash-data" data-success="{{ e(session('success')) }}" data-error="{{ e(session('error')) }}"></div>

    <script>
        (function(){
            window.onpageshow = function(event) {
                if (event.persisted) {
                    window.location.reload();
                }
            };

            var data = document.getElementById('flash-data');
            if (!data) return;
            var msg = data.dataset.success || data.dataset.error || '';
            var isError = !!data.dataset.error && !data.dataset.success;
            if (!msg) return;
            var toast = document.getElementById('flash-toast');
            var inner = document.getElementById('flash-inner');
            inner.textContent = msg;
            if (isError) {
                inner.classList.remove('bg-green-600');
                inner.classList.add('bg-red-600');
            }
            toast.classList.remove('hidden');
            setTimeout(function(){ toast.classList.add('opacity-100'); }, 10);
            setTimeout(function(){ toast.classList.add('hidden'); }, 4000);
        })();
    </script>
</body>
</html>
