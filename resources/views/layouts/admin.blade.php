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
        <aside class="w-64 bg-gray-900 text-gray-200 flex flex-col sidebar-scroll overflow-y-auto">
            
            <div class="p-6 text-center">
                <a href="{{ route('admin.dashboard') }}" class="text-white text-2xl font-bold">
                    Admin Panel
                </a>
            </div>

            <nav class="flex-1 px-4 py-2 space-y-2">
                
                <a href="{{ route('admin.dashboard') }}" 
                   class="flex items-center px-4 py-2.5 rounded-lg transition duration-200 
                          {{ request()->routeIs('admin.dashboard') ? 'bg-gray-800 text-white' : 'hover:bg-gray-700 hover:text-white' }}">
                    <svg class="h-5 w-5 mr-3" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1V10a1 1 0 00-1-1H7a1 1 0 00-1 1v10a1 1 0 001 1h3z" />
                    </svg>
                    Dashboard
                </a>

                <div x-data="{ open: {{ request()->routeIs('admin.new-books*') || request()->routeIs('admin.upcoming*') || request()->routeIs('admin.projects*') ? 'true' : 'false' }} }">
                    <button @click="open = !open" 
                            class="w-full flex justify-between items-center px-4 py-2.5 rounded-lg transition duration-200 hover:bg-gray-700 hover:text-white">
                        <span class="flex items-center">
                            <svg class="h-5 w-5 mr-3" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                            </svg>
                            Bookings
                        </span>
                        <svg class="h-5 w-5 transform transition-transform duration-200" :class="{ 'rotate-180': open }" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>
                    <div x-show="open" x-transition class="mt-2 space-y-2 pl-8">
                        <a href="{{ route('admin.new-books.index') }}" 
                           class="block px-4 py-2 rounded-lg text-sm 
                                  {{ request()->routeIs('admin.new-books*') ? 'bg-gray-700 text-white' : 'hover:bg-gray-700 hover:text-white' }}">
                            New Bookings
                        </a>
                        <a href="{{ route('admin.upcoming.index') }}"
                           class="block px-4 py-2 rounded-lg text-sm 
                                  {{ request()->routeIs('admin.upcoming.index*') ? 'bg-gray-700 text-white' : 'hover:bg-gray-700 hover:text-white' }}">
                            Upcoming Shooting 
                        </a>
                        <a href="{{ route('admin.projects.index') }}"
                           class="block px-4 py-2 rounded-lg text-sm 
                                  {{ request()->routeIs('admin.projects.index*') ? 'bg-gray-700 text-white' : 'hover:bg-gray-700 hover:text-white' }}">
                            List Project 
                        </a>
                    </div>
                </div>

                {{-- <a href="{{ route('admin.payments') }}"  --}}
                <a href="#"
                   class="flex items-center px-4 py-2.5 rounded-lg transition duration-200 
                          {{ request()->routeIs('admin.payments*') ? 'bg-gray-800 text-white' : 'hover:bg-gray-700 hover:text-white' }}">
                    <svg class="h-5 w-5 mr-3" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                    </svg>
                    Payments
                </a>

                <div x-data="{ open: {{ request()->routeIs('admin.packages*') || request()->routeIs('admin.addons*') ? 'true' : 'false' }} }">
                    <button @click="open = !open" 
                            class="w-full flex justify-between items-center px-4 py-2.5 rounded-lg transition duration-200 hover:bg-gray-700 hover:text-white">
                        <span class="flex items-center">
                            <svg class="h-5 w-5 mr-3" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                            </svg>
                            Master Data
                        </span>
                        <svg class="h-5 w-5 transform transition-transform duration-200" :class="{ 'rotate-180': open }" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>
                    <div x-show="open" x-transition class="mt-2 space-y-2 pl-8">
                        <a href="{{ route('admin.packages.index') }}" 
                           class="block px-4 py-2 rounded-lg text-sm 
                                  {{ request()->routeIs('admin.packages*') ? 'bg-gray-700 text-white' : 'hover:bg-gray-700 hover:text-white' }}">
                            Packages
                        </a>
                        <a href="{{ route('admin.addons.index') }}"
                           class="block px-4 py-2 rounded-lg text-sm 
                                  {{ request()->routeIs('admin.addons*') ? 'bg-gray-700 text-white' : 'hover:bg-gray-700 hover:text-white' }}">
                            Add-ons
                        </a>
                    </div>
                </div>

                <a href="{{ route('admin.photographers.index') }}" 
                   class="flex items-center px-4 py-2.5 rounded-lg transition duration-200 
                          {{ request()->routeIs('admin.photographers*') ? 'bg-gray-800 text-white' : 'hover:bg-gray-700 hover:text-white' }}">
                    <svg class="h-5 w-5 mr-3" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                    </svg>
                    Manajemen Fotografer
                </a>

                <a href="{{ route('admin.users.index') }}" 
                   class="flex items-center px-4 py-2.5 rounded-lg transition duration-200 
                          {{ request()->routeIs('admin.users*') ? 'bg-gray-800 text-white' : 'hover:bg-gray-700 hover:text-white' }}">
                    <svg class="h-5 w-5 mr-3" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                    </svg>
                    Manajemen User
                </a>
            </nav>

            <div class="p-4 mt-auto">
                 <a href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();" 
                   class="flex items-center px-4 py-2.5 rounded-lg transition duration-200 text-red-400 hover:bg-red-500 hover:text-white">
                    <svg class="h-5 w-5 mr-3" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                    </svg>
                    Logout
                </a>
                <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                    @csrf
                </form>
            </div>

        </aside>

        <main class="flex-1 flex flex-col overflow-y-auto">
            
            <header class="bg-white shadow-md p-6">
                <div class="flex justify-between items-center">
                    <h1 class="text-2xl font-semibold text-gray-800">
                        @yield('page-title', 'Dashboard')
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