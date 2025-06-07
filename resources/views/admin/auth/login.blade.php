<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Dashboard Admin - TRENDZ</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet" />
</head>

<body class="bg-gray-100 text-gray-800 h-screen overflow-hidden">
    <div class="min-h-screen flex items-center justify-center bg-gray-50">
        <div class="w-full max-w-md p-6 bg-white rounded-lg shadow-lg border border-gray-200">
            <h2 class="text-center text-3xl font-bold text-[#2c3c84] mb-6">Admin Login</h2>
            @if (session('status'))
                <div class="alert alert-success">
                    {{ session('status') }}
                </div>
            @endif

            <!-- Login Form -->
            <form method="POST" action="{{ route('admin.login.submit') }}">
                @csrf

                <!-- Email -->
                <div class="mb-4">
                    <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                    <input type="email" name="email" id="email" value="{{ old('email') }}"
                        class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md @error('email') border-red-500 @enderror"
                        required autofocus>
                    @error('email')
                        <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Password -->
                <div class="mb-6">
                    <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
                    <input type="password" name="password" id="password"
                        class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md @error('password') border-red-500 @enderror"
                        required>
                    @error('password')
                        <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Remember Me Checkbox -->
                <div class="mb-6 flex items-center">
                    <input type="checkbox" name="remember" id="remember" class="mr-2">
                    <label for="remember" class="text-sm text-gray-600">Remember me</label>
                </div>

                <!-- Login Button -->
                <div class="mb-4">
                    <button type="submit"
                        class="w-full py-2 bg-[#5979f5] text-white rounded-md hover:bg-[#2c3c84] transition duration-300">
                        Login
                    </button>
                </div>
            </form>

            <!-- Registration Link -->
            <div class="text-center">
                <a href="{{ route('admin.register.form') }}" class="text-sm text-[#5979f5] hover:text-[#2c3c84]">Don't
                    have
                    an account? Register</a>
            </div>
        </div>
    </div>

</body>
