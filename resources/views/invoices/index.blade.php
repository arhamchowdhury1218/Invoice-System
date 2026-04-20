@extends('layouts.app')

@section('content')

<div class="flex justify-between items-center mb-6">
    <h1 class="text-2xl font-semibold text-gray-800">Invoices</h1>
    <a href="{{ route('invoices.create') }}"
       class="bg-blue-600 text-white px-4 py-2 rounded-lg text-sm">
        + New Invoice
    </a>
</div>

<!-- Filter tabs -->
<div class="flex gap-2 mb-4">
    @foreach(['all','draft','sent','paid','overdue'] as $filter)
    <a href="{{ route('invoices.index', ['status' => $filter]) }}"
       class="px-3 py-1 rounded-full text-xs font-medium border
              {{ request('status', 'all') === $filter
                 ? 'bg-blue-600 text-white border-blue-600'
                 : 'text-gray-600 hover:bg-gray-100' }}">
        {{ ucfirst($filter) }}
    </a>
    @endforeach
</div>

<div class="bg-white rounded-xl border overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-gray-50 text-gray-500 text-xs uppercase">
            <tr>
                <th class="px-6 py-3 text-left">Invoice #</th>
                <th class="px-6 py-3 text-left">Client</th>
                <th class="px-6 py-3 text-left">Issued</th>
                <th class="px-6 py-3 text-left">Due</th>
                <th class="px-6 py-3 text-right">Total</th>
                <th class="px-6 py-3 text-left">Status</th>
                <th class="px-6 py-3 text-left">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y">
            @forelse($invoices as $invoice)
            <tr class="hover:bg-gray-50">
                <td class="px-6 py-4">
                    <a href="{{ route('invoices.show', $invoice) }}"
                       class="font-medium text-blue-600 hover:underline">
                        {{ $invoice->invoice_number }}
                    </a>
                </td>
                <td class="px-6 py-4 text-gray-700">{{ $invoice->client->name }}</td>
                <td class="px-6 py-4 text-gray-500">
                    {{ $invoice->issue_date->format('d M Y') }}
                </td>
                <td class="px-6 py-4 text-gray-500">
                    {{ $invoice->due_date->format('d M Y') }}
                </td>
                <td class="px-6 py-4 text-right font-medium">
                    ৳{{ number_format($invoice->total, 2) }}
                </td>
                <td class="px-6 py-4">
                    @include('invoices._status_badge', ['status' => $invoice->status])
                </td>
                <td class="px-6 py-4 flex gap-2">
                    <a href="{{ route('invoices.show', $invoice) }}"
                       class="text-blue-600 text-xs hover:underline">View</a>
                    <a href="{{ route('invoices.pdf', $invoice) }}"
                       class="text-gray-600 text-xs hover:underline">PDF</a>
                    @if($invoice->status !== 'paid')
                    <form method="POST"
                          action="{{ route('invoices.markPaid', $invoice) }}">
                        @csrf @method('PATCH')
                        <button class="text-green-600 text-xs hover:underline">
                            Mark paid
                        </button>
                    </form>
                    @endif
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="7" class="px-6 py-10 text-center text-gray-400">
                    No invoices found.
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
    <div class="px-6 py-4 border-t">
        {{ $invoices->links() }}
    </div>
</div>

@endsection