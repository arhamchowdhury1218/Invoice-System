@extends('layouts.app')

@section('content')

<div class="flex justify-between items-center mb-6">
    <div>
        <h1 class="text-2xl font-semibold text-gray-800">
            {{ $invoice->invoice_number }}
        </h1>
        <p class="text-gray-500 text-sm mt-1">{{ $invoice->client->name }}</p>
    </div>
    <div class="flex gap-3">
        <a href="{{ route('invoices.pdf', $invoice) }}"
           class="border px-4 py-2 rounded-lg text-sm text-gray-600 hover:bg-gray-50">
            Download PDF
        </a>
        @if($invoice->status !== 'paid')
        <form method="POST" action="{{ route('invoices.markPaid', $invoice) }}">
            @csrf @method('PATCH')
            <button class="bg-green-600 text-white px-4 py-2 rounded-lg text-sm">
                Mark as paid
            </button>
        </form>
        @endif
    </div>
</div>

<div class="grid grid-cols-1 md:grid-cols-3 gap-6">

  <div class="md:col-span-2">

    <!-- Invoice info -->
    <div class="bg-white rounded-xl border p-6 mb-4">
      <div class="grid grid-cols-3 gap-4 text-sm">
        <div>
          <p class="text-gray-500">Status</p>
          <div class="mt-1">
            @include('invoices._status_badge', ['status' => $invoice->status])
          </div>
        </div>
        <div>
          <p class="text-gray-500">Issue date</p>
          <p class="font-medium mt-1">{{ $invoice->issue_date->format('d M Y') }}</p>
        </div>
        <div>
          <p class="text-gray-500">Due date</p>
          <p class="font-medium mt-1 {{ $invoice->status === 'overdue' ? 'text-red-600' : '' }}">
            {{ $invoice->due_date->format('d M Y') }}
          </p>
        </div>
      </div>
    </div>

    <!-- Line items table -->
    <div class="bg-white rounded-xl border overflow-hidden">
      <table class="w-full text-sm">
        <thead class="bg-gray-50 text-gray-500 text-xs uppercase">
          <tr>
            <th class="px-6 py-3 text-left">Description</th>
            <th class="px-6 py-3 text-right">Qty</th>
            <th class="px-6 py-3 text-right">Unit price</th>
            <th class="px-6 py-3 text-right">Total</th>
          </tr>
        </thead>
        <tbody class="divide-y">
          @foreach($invoice->items as $item)
          <tr>
            <td class="px-6 py-4">{{ $item->description }}</td>
            <td class="px-6 py-4 text-right text-gray-500">{{ $item->quantity }}</td>
            <td class="px-6 py-4 text-right text-gray-500">
              ৳{{ number_format($item->unit_price, 2) }}
            </td>
            <td class="px-6 py-4 text-right font-medium">
              ৳{{ number_format($item->total, 2) }}
            </td>
          </tr>
          @endforeach
        </tbody>
      </table>

      <!-- Totals -->
      <div class="px-6 py-4 border-t bg-gray-50">
        <div class="flex justify-end">
          <div class="w-60 space-y-1 text-sm">
            <div class="flex justify-between">
              <span class="text-gray-500">Subtotal</span>
              <span>৳{{ number_format($invoice->subtotal, 2) }}</span>
            </div>
            @if($invoice->discount_percent > 0)
            <div class="flex justify-between text-red-600">
              <span>Discount ({{ $invoice->discount_percent }}%)</span>
              <span>-৳{{ number_format($invoice->subtotal * $invoice->discount_percent / 100, 2) }}</span>
            </div>
            @endif
            @if($invoice->tax_percent > 0)
            <div class="flex justify-between text-gray-600">
              <span>Tax ({{ $invoice->tax_percent }}%)</span>
              <span>+৳{{ number_format($invoice->total - $invoice->subtotal * (1 - $invoice->discount_percent/100), 2) }}</span>
            </div>
            @endif
            <div class="flex justify-between font-semibold text-base pt-2 border-t">
              <span>Total</span>
              <span>৳{{ number_format($invoice->total, 2) }}</span>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Client info sidebar -->
  <div class="bg-white rounded-xl border p-6 h-fit">
    <h3 class="font-semibold text-gray-700 mb-3">Bill to</h3>
    <p class="font-medium text-gray-800">{{ $invoice->client->name }}</p>
    <p class="text-sm text-gray-500 mt-1">{{ $invoice->client->email }}</p>
    <p class="text-sm text-gray-500">{{ $invoice->client->phone }}</p>
    <p class="text-sm text-gray-500 mt-2">{{ $invoice->client->address }}</p>
    @if($invoice->notes)
    <div class="mt-4 pt-4 border-t">
      <p class="text-xs text-gray-500 font-medium">Notes</p>
      <p class="text-sm text-gray-600 mt-1">{{ $invoice->notes }}</p>
    </div>
    @endif
  </div>

</div>

@endsection