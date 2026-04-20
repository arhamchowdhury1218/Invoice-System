<!DOCTYPE html>
<html>
<head>
    <title>Register</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-100 min-h-screen flex items-center justify-center">

<div class="bg-white rounded-2xl border p-8 w-full max-w-md">

    <h1 class="text-2xl font-semibold mb-6">Create account</h1>

    <form method="POST" action="{{ route('register') }}">
        @csrf

        <div class="mb-4">
            <label class="block text-sm font-medium mb-1">Full name</label>
            <input type="text" name="name" value="{{ old('name') }}" required
                class="w-full border rounded-lg px-3 py-2 text-sm">
            @error('name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
        </div>

        <div class="mb-4">
            <label class="block text-sm font-medium mb-1">Email</label>
            <input type="email" name="email" value="{{ old('email') }}" required
                class="w-full border rounded-lg px-3 py-2 text-sm">
            @error('email') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
        </div>

        <div class="mb-4">
            <label class="block text-sm font-medium mb-1">Password</label>
            <input type="password" name="password" required placeholder="Min 8 characters"
                class="w-full border rounded-lg px-3 py-2 text-sm">
            @error('password') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
        </div>

        <div class="mb-6">
            <label class="block text-sm font-medium mb-1">Confirm password</label>
            <input type="password" name="password_confirmation" required
                class="w-full border rounded-lg px-3 py-2 text-sm">
        </div>

        <button type="submit"
            class="w-full bg-blue-600 text-white py-2.5 rounded-lg text-sm font-medium">
            Create account
        </button>

        <p class="text-center text-sm text-gray-500 mt-4">
            Already have an account?
            <a href="{{ route('login') }}" class="text-blue-600">Sign in</a>
        </p>

    </form>
</div>
</body>
</html>