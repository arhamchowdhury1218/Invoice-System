<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice System</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-100 min-h-screen">

    <!-- Navbar -->
    <nav class="bg-white shadow-sm border-b">
        <div class="max-w-7xl mx-auto px-4 py-3 flex justify-between items-center">
            <a href="{{ route('dashboard') }}"
               class="text-xl font-semibold text-gray-800">
                InvoiceApp
            </a>
            <div class="flex gap-6 text-sm">
                <a href="{{ route('dashboard') }}"
                   class="text-gray-600 hover:text-gray-900">Dashboard</a>
                <a href="{{ route('clients.index') }}"
                   class="text-gray-600 hover:text-gray-900">Clients</a>
                <a href="{{ route('invoices.index') }}"
                   class="text-gray-600 hover:text-gray-900">Invoices</a>
                <form method="POST" action="{{ route('logout') }}" class="inline">
                    @csrf
                    <button class="text-gray-600 hover:text-red-500">Logout</button>
                </form>
            </div>
        </div>
    </nav>

    <!-- Flash success message -->
    @if(session('success'))
        <div class="max-w-7xl mx-auto px-4 mt-4">
            <div class="bg-green-100 text-green-800 px-4 py-3 rounded-lg text-sm">
                {{ session('success') }}
            </div>
        </div>
    @endif

    <!-- Page content goes here -->
    <main class="max-w-7xl mx-auto px-4 py-8">
        @yield('content')
    </main>

</body>
</html>