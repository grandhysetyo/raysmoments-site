<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Admin Dashboard') - {{ config('app.name', 'Laravel') }}</title>
    
    @vite('resources/css/app.css') 
    
    <script src="//unpkg.com/alpinejs" defer></script>

    <style>
        /* Opsi: Sembunyikan scrollbar untuk sidebar di Chrome/Safari */
        .sidebar-scroll::-webkit-scrollbar {
            display: none;
        }
        /* Opsi: Sembunyikan scrollbar untuk Firefox */
        .sidebar-scroll {
            -ms-overflow-style: none;  /* IE dan Edge */
            scrollbar-width: none;  /* Firefox */
        }
    </style>
</head>
<body class="bg-gray-100 font-sans">

    <div class="flex h-screen">
        <main class="flex-1 flex flex-col overflow-y-auto">
            
            <header class="bg-white shadow-md p-6">
                <div class="flex justify-between items-center">
                    <h1 class="text-2xl font-semibold text-gray-800">
                        @yield('page-title', 'New Books')
                    </h1>
                    <div class="flex items-center space-x-4">
                        <span class="text-gray-600">Selamat datang, **Admin User**</span>
                        </div>
                </div>
            </header>

            <div class="p-6 md:p-10">
                @yield('content')
            </div>

        </main>
    </div>

</body>
</html>