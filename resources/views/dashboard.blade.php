@extends('layouts.app')

@section('content')

<div class="flex justify-between items-center mb-6">
    <h1 class="text-2xl font-semibold text-gray-800">Dashboard</h1>
    <a href="{{ route('invoices.create') }}"
       class="bg-blue-600 text-white px-4 py-2 rounded-lg text-sm hover:bg-blue-700">
        + New Invoice
    </a>
</div>

<!-- Stats cards -->
<div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-8">

    <div class="bg-white rounded-xl p-5 border">
        <p class="text-sm text-gray-500">Total invoices</p>
        <p class="text-3xl font-semibold text-gray-800 mt-1">
            {{ $stats['total_invoices'] }}
        </p>
    </div>

    <div class="bg-white rounded-xl p-5 border">
        <p class="text-sm text-gray-500">Total revenue</p>
        <p class="text-3xl font-semibold text-green-600 mt-1">
            ৳{{ number_format($stats['total_revenue'], 2) }}
        </p>
    </div>

    <div class="bg-white rounded-xl p-5 border">
        <p class="text-sm text-gray-500">Outstanding</p>
        <p class="text-3xl font-semibold text-yellow-600 mt-1">
            ৳{{ number_format($stats['outstanding'], 2) }}
        </p>
    </div>

    <div class="bg-white rounded-xl p-5 border">
        <p class="text-sm text-gray-500">Overdue</p>
        <p class="text-3xl font-semibold text-red-600 mt-1">
            {{ $stats['overdue'] }}
        </p>
    </div>

</div>

<!-- Recent invoices table -->
<div class="bg-white rounded-xl border overflow-hidden">
    <div class="px-6 py-4 border-b">
        <h2 class="font-semibold text-gray-700">Recent invoices</h2>
    </div>
    <table class="w-full text-sm">
        <thead class="bg-gray-50 text-gray-500 text-xs uppercase">
            <tr>
                <th class="px-6 py-3 text-left">Invoice #</th>
                <th class="px-6 py-3 text-left">Client</th>
                <th class="px-6 py-3 text-left">Due date</th>
                <th class="px-6 py-3 text-left">Total</th>
                <th class="px-6 py-3 text-left">Status</th>
            </tr>
        </thead>
        <tbody class="divide-y">
            @forelse($recentInvoices as $invoice)
            <tr class="hover:bg-gray-50">
                <td class="px-6 py-4 font-medium">
                    <a href="{{ route('invoices.show', $invoice) }}"
                       class="text-blue-600 hover:underline">
                        {{ $invoice->invoice_number }}
                    </a>
                </td>
                <td class="px-6 py-4 text-gray-600">{{ $invoice->client->name }}</td>
                <td class="px-6 py-4 text-gray-600">{{ $invoice->due_date->format('d M Y') }}</td>
                <td class="px-6 py-4 font-medium">৳{{ number_format($invoice->total, 2) }}</td>
                <td class="px-6 py-4">
                    @include('invoices._status_badge', ['status' => $invoice->status])
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="5" class="px-6 py-8 text-center text-gray-400">
                    No invoices yet. <a href="{{ route('invoices.create') }}"
                    class="text-blue-600">Create your first one</a>
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

@endsection