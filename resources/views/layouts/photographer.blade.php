<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Photographer Dashboard') - {{ config('app.name', 'Laravel') }}</title>
    @vite('resources/css/app.css') 
</head>
<body class="bg-gray-100 font-sans">

    <div class="flex h-screen">
        <aside class="w-64 bg-gray-900 text-gray-200 flex flex-col">
            
            <div class="p-6 text-center">
                <a href="{{ route('photographer.dashboard') }}" class="text-white text-2xl font-bold">
                    Photographer
                </a>
            </div>

            <nav class="flex-1 px-4 py-2 space-y-2">
                
                <a href="{{ route('photographer.dashboard') }}" 
                   class="flex items-center px-4 py-2.5 rounded-lg transition duration-200 
                          {{ request()->routeIs('photographer.dashboard') ? 'bg-gray-800 text-white' : 'hover:bg-gray-700 hover:text-white' }}">
                    <svg class="h-5 w-5 mr-3" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1V10a1 1 0 00-1-1H7a1 1 0 00-1 1v10a1 1 0 001 1h3z" /></svg>
                    Dashboard
                </a>

                <a href="{{ route('photographer.schedule') }}" 
                   class="flex items-center px-4 py-2.5 rounded-lg transition duration-200 
                          {{ request()->routeIs('photographer.schedule*') ? 'bg-gray-800 text-white' : 'hover:bg-gray-700 hover:text-white' }}">
                    <svg class="h-5 w-5 mr-3" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                    Jadwal Saya
                </a>

                <a href="{{ route('photographer.profile') }}" 
                   class="flex items-center px-4 py-2.5 rounded-lg transition duration-200 
                          {{ request()->routeIs('photographer.profile*') ? 'bg-gray-800 text-white' : 'hover:bg-gray-700 hover:text-white' }}">
                    <svg class="h-5 w-5 mr-3" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" /></svg>
                    Profil Saya
                </a>
                
                <a href="{{ route('photographer.rates') }}" 
                   class="flex items-center px-4 py-2.5 rounded-lg transition duration-200 
                          {{ request()->routeIs('photographer.rates*') ? 'bg-gray-800 text-white' : 'hover:bg-gray-700 hover:text-white' }}">
                    <svg class="h-5 w-5 mr-3" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3-.895 3-2-1.343-2-3-2zM12 8c1.657 0 3 .895 3 2s-1.343 2-3 2-3-.895-3-2 1.343-2 3-2zm0 0v6m0 0c-1.657 0-3 .895-3 2s1.343 2 3 2 3-.895 3-2-1.343-2-3-2zm0 0v6" /></svg>
                    Tarif Saya
                </a>

            </nav>

            <div class="p-4 mt-auto">
                 <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" 
                       class="w-full flex items-center px-4 py-2.5 rounded-lg transition duration-200 text-red-400 hover:bg-red-500 hover:text-white">
                        <svg class="h-5 w-5 mr-3" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" /></svg>
                        Logout
                    </button>
                 </form>
            </div>
        </aside>

        <main class="flex-1 flex flex-col overflow-y-auto">
            <header class="bg-white shadow-md p-6">
                <div class="flex justify-between items-center">
                    <h1 class="text-2xl font-semibold text-gray-800">
                        @yield('page-title', 'Dashboard')
                    </h1>
                    <span class="text-gray-600">Selamat datang, {{ Auth::user()->name }}</span>
                </div>
            </header>
            <div class="p-6 md:p-10">
                @yield('content')
            </div>
        </main>
    </div>

</body>
</html>