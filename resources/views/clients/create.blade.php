@extends('layouts.app')

@section('content')

<div class="max-w-xl">
    <h1 class="text-2xl font-semibold text-gray-800 mb-6">Add New Client</h1>

    <div class="bg-white rounded-xl border p-6">
        <form method="POST" action="{{ route('clients.store') }}">
            @csrf

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    Client name *
                </label>
                <input type="text" name="name"
                       value="{{ old('name') }}"
                       class="w-full border rounded-lg px-3 py-2 text-sm
                              @error('name') border-red-500 @enderror"
                       placeholder="ABC Company Ltd">
                @error('name')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                <input type="email" name="email"
                       value="{{ old('email') }}"
                       class="w-full border rounded-lg px-3 py-2 text-sm"
                       placeholder="client@email.com">
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Phone</label>
                <input type="text" name="phone"
                       value="{{ old('phone') }}"
                       class="w-full border rounded-lg px-3 py-2 text-sm"
                       placeholder="01700000000">
            </div>

            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-1">Address</label>
                <textarea name="address" rows="3"
                          class="w-full border rounded-lg px-3 py-2 text-sm"
                          placeholder="Dhaka, Bangladesh">{{ old('address') }}</textarea>
            </div>

            <div class="flex gap-3">
                <button type="submit"
                        class="bg-blue-600 text-white px-6 py-2 rounded-lg text-sm">
                    Save Client
                </button>
                <a href="{{ route('clients.index') }}"
                   class="px-6 py-2 border rounded-lg text-sm text-gray-600">
                    Cancel
                </a>
            </div>
        </form>
    </div>
</div>

@endsection