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
            <h2 class="text-center text-3xl font-bold text-[#2c3c84] mb-6">Admin Register</h2>

            <!-- Register Form -->
            <form method="POST" action="{{ route('admin.register.submit') }}">
                @csrf

                <!-- Name -->
                <div class="mb-4">
                    <label for="name" class="block text-sm font-medium text-gray-700">Full Name</label>
                    <input type="text" name="name" id="name" value="{{ old('name') }}"
                        class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md @error('name') border-red-500 @enderror"
                        required>
                    @error('name')
                        <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Email -->
                <div class="mb-4">
                    <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                    <input type="email" name="email" id="email" value="{{ old('email') }}"
                        class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md @error('email') border-red-500 @enderror"
                        required>
                    @error('email')
                        <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Password -->
                <div class="mb-4">
                    <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
                    <input type="password" name="password" id="password"
                        class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md @error('password') border-red-500 @enderror"
                        required>
                    @error('password')
                        <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Confirm Password -->
                <div class="mb-6">
                    <label for="password_confirmation" class="block text-sm font-medium text-gray-700">Confirm
                        Password</label>
                    <input type="password" name="password_confirmation" id="password_confirmation"
                        class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md" required>
                </div>

                <!-- Role (Hidden default 'admin') -->
                <input type="hidden" name="role" value="admin">

                <!-- Register Button -->
                <div class="mb-4">
                    <button type="submit"
                        class="w-full py-2 bg-[#5979f5] text-white rounded-md hover:bg-[#2c3c84] transition duration-300">
                        Register
                    </button>
                </div>
            </form>

            <!-- Login Link -->
            <div class="text-center">
                <a href="{{ route('admin.login.form') }}" class="text-sm text-[#5979f5] hover:text-[#2c3c84]">Already
                    have
                    an account? Login</a>
            </div>
        </div>
    </div>
</body>
