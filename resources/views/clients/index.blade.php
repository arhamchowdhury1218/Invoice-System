@extends('layouts.app')

@section('content')

<div class="flex justify-between items-center mb-6">
    <h1 class="text-2xl font-semibold text-gray-800">Clients</h1>
    <a href="{{ route('clients.create') }}"
       class="bg-blue-600 text-white px-4 py-2 rounded-lg text-sm">
        + Add Client
    </a>
</div>

<div class="bg-white rounded-xl border overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-gray-50 text-gray-500 text-xs uppercase">
            <tr>
                <th class="px-6 py-3 text-left">Name</th>
                <th class="px-6 py-3 text-left">Email</th>
                <th class="px-6 py-3 text-left">Phone</th>
                <th class="px-6 py-3 text-left">Invoices</th>
                <th class="px-6 py-3 text-left">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y">
            @forelse($clients as $client)
            <tr class="hover:bg-gray-50">
                <td class="px-6 py-4 font-medium text-gray-800">{{ $client->name }}</td>
                <td class="px-6 py-4 text-gray-500">{{ $client->email ?? '—' }}</td>
                <td class="px-6 py-4 text-gray-500">{{ $client->phone ?? '—' }}</td>
                <td class="px-6 py-4 text-gray-500">{{ $client->invoices_count }}</td>
                <td class="px-6 py-4 flex gap-3">
                    <a href="{{ route('clients.edit', $client) }}"
                       class="text-blue-600 hover:underline text-xs">Edit</a>
                    <form method="POST"
                          action="{{ route('clients.destroy', $client) }}"
                          onsubmit="return confirm('Delete this client?')">
                        @csrf @method('DELETE')
                        <button class="text-red-500 hover:underline text-xs">Delete</button>
                    </form>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="5" class="px-6 py-8 text-center text-gray-400">
                    No clients yet.
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

@endsection