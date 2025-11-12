<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - {{ config('app.name', 'Laravel') }}</title>
    @vite('resources/css/app.css')
</head>
<body class="bg-gray-100 font-sans">

    <div class="flex min-h-screen items-center justify-center">
        <div class="w-full max-w-md p-8 space-y-8 bg-white rounded-2xl shadow-xl">
            
            <div class="text-center">
                <h1 class="text-3xl font-bold text-gray-900">
                    Selamat Datang
                </h1>
                <p class="mt-2 text-sm text-gray-600">
                    Silakan masuk ke akun staf Anda.
                </p>
            </div>

            <form method="POST" action="{{ route('login') }}" class="space-y-6">
                @csrf

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
                        <input id="password" name="password" type="password" autocomplete="current-password" required
                               class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm 
                                      placeholder-gray-400 focus:outline-none focus:ring-blue-500 
                                      focus:border-blue-500 @error('password') border-red-500 @enderror">
                    </div>
                </div>

                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <input id="remember" name="remember" type="checkbox"
                               class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                        <label for="remember" class="ml-2 block text-sm text-gray-900">
                            Ingat Saya
                        </label>
                    </div>

                    <div class="text-sm">
                        <a href="#" class="font-medium text-blue-600 hover:text-blue-500">
                            Lupa password?
                        </a>
                    </div>
                </div>

                <div>
                    <button type="submit"
                            class="w-full flex justify-center py-2.5 px-4 border border-transparent 
                                   rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 
                                   hover:bg-blue-700 focus:outline-none focus:ring-2 
                                   focus:ring-offset-2 focus:ring-blue-500 transition duration-150">
                        Masuk
                    </button>
                </div>
            </form>
            
        </div>
    </div>

</body>
</html>