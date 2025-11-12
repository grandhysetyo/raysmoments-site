<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - {{ config('app.name', 'Laravel') }}</title>
    @vite('resources/css/app.css')
</head>
<body class="bg-gray-100 font-sans">

    <div class="flex min-h-screen items-center justify-center py-12">
        <div class="w-full max-w-md p-8 space-y-8 bg-white rounded-2xl shadow-xl">
            
            <div class="text-center">
                <h1 class="text-3xl font-bold text-gray-900">
                    Buat Akun Baru
                </h1>
                <p class="mt-2 text-sm text-gray-600">
                    Isi data di bawah untuk mendaftar.
                </p>
            </div>

            <form method="POST" action="{{ route('register') }}" class="space-y-6">
                @csrf

                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700">
                        Nama Lengkap
                    </label>
                    <div class="mt-1">
                        <input id="name" name="name" type="text" autocomplete="name" required
                               value="{{ old('name') }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm 
                                      placeholder-gray-400 focus:outline-none focus:ring-blue-500 
                                      focus:border-blue-500 @error('name') border-red-500 @enderror">
                        
                        @error('name')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700">
                        Alamat Email
                    </label>
                    <div class="mt-1">
                        <input id="email" name="email" type="email" autocomplete="email" required
                               value="{{ old('email') }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm 
                                      placeholder-gray-400 focus:outline-none focus:ring-blue-500 
                                      focus:border-blue-500 @error('email') border-red-500 @enderror">
                        
                        @error('email')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700">
                        Password
                    </label>
                    <div class="mt-1">
                        <input id="password" name="password" type="password" required
                               class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm 
                                      placeholder-gray-400 focus:outline-none focus:ring-blue-500 
                                      focus:border-blue-500 @error('password') border-red-500 @enderror">
                        
                        @error('password')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
                
                <div>
                    <label for="password_confirmation" class="block text-sm font-medium text-gray-700">
                        Konfirmasi Password
                    </label>
                    <div class="mt-1">
                        <input id="password_confirmation" name="password_confirmation" type="password" required
                               class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm 
                                      placeholder-gray-400 focus:outline-none focus:ring-blue-500 
                                      focus:border-blue-500">
                    </div>
                </div>

                <div>
                    <button type="submit"
                            class="w-full flex justify-center py-2.5 px-4 border border-transparent 
                                   rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 
                                   hover:bg-blue-700 focus:outline-none focus:ring-2 
                                   focus:ring-offset-2 focus:ring-blue-500 transition duration-150">
                        Daftar
                    </button>
                </div>
            </form>

            <p class="text-center text-sm text-gray-600">
                Sudah punya akun?
                <a href="{{ route('login') }}" class="font-medium text-blue-600 hover:text-blue-500">
                    Masuk di sini
                </a>
            </p>
            
        </div>
    </div>

</body>
</html>