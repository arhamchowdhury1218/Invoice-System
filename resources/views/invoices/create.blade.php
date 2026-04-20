@extends('layouts.app')

@section('content')

<h1 class="text-2xl font-semibold text-gray-800 mb-6">Create Invoice</h1>

<form method="POST" action="{{ route('invoices.store') }}" id="invoice-form">
@csrf

<div class="grid grid-cols-1 md:grid-cols-3 gap-6">

  <!-- Left: Invoice details -->
  <div class="md:col-span-2 space-y-4">

    <div class="bg-white rounded-xl border p-6">
      <h2 class="font-semibold text-gray-700 mb-4">Invoice details</h2>

      <div class="grid grid-cols-2 gap-4">
        <div>
          <label class="text-sm font-medium text-gray-700">Client *</label>
          <select name="client_id"
                  class="w-full border rounded-lg px-3 py-2 mt-1 text-sm">
            <option value="">Select client...</option>
            @foreach($clients as $client)
              <option value="{{ $client->id }}"
                {{ old('client_id') == $client->id ? 'selected' : '' }}>
                {{ $client->name }}
              </option>
            @endforeach
          </select>
          @error('client_id')
            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
          @enderror
        </div>

        <div>
          <label class="text-sm font-medium text-gray-700">Issue date *</label>
          <input type="date" name="issue_date"
                 value="{{ old('issue_date', date('Y-m-d')) }}"
                 class="w-full border rounded-lg px-3 py-2 mt-1 text-sm">
        </div>

        <div>
          <label class="text-sm font-medium text-gray-700">Due date *</label>
          <input type="date" name="due_date"
                 value="{{ old('due_date', date('Y-m-d', strtotime('+30 days'))) }}"
                 class="w-full border rounded-lg px-3 py-2 mt-1 text-sm">
        </div>

        <div>
          <label class="text-sm font-medium text-gray-700">Discount %</label>
          <input type="number" name="discount_percent"
                 value="{{ old('discount_percent', 0) }}"
                 min="0" max="100" step="0.01"
                 oninput="recalculate()"
                 class="w-full border rounded-lg px-3 py-2 mt-1 text-sm">
        </div>

        <div>
          <label class="text-sm font-medium text-gray-700">Tax %</label>
          <input type="number" name="tax_percent"
                 value="{{ old('tax_percent', 0) }}"
                 min="0" max="100" step="0.01"
                 oninput="recalculate()"
                 class="w-full border rounded-lg px-3 py-2 mt-1 text-sm">
        </div>
      </div>

      <div class="mt-4">
        <label class="text-sm font-medium text-gray-700">Notes</label>
        <textarea name="notes" rows="2"
                  class="w-full border rounded-lg px-3 py-2 mt-1 text-sm"
                  placeholder="Payment terms, bank details...">{{ old('notes') }}</textarea>
      </div>
    </div>

    <!-- Line items -->
    <div class="bg-white rounded-xl border p-6">
      <h2 class="font-semibold text-gray-700 mb-4">Line items</h2>

      <div id="items-container">
        <!-- Items added dynamically by JS -->
      </div>

      <button type="button" onclick="addItem()"
              class="mt-3 text-blue-600 text-sm hover:underline">
        + Add item
      </button>

      @error('items')
        <p class="text-red-500 text-xs mt-2">{{ $message }}</p>
      @enderror
    </div>

  </div>

  <!-- Right: Summary -->
  <div>
    <div class="bg-white rounded-xl border p-6 sticky top-4">
      <h2 class="font-semibold text-gray-700 mb-4">Summary</h2>
      <div class="space-y-2 text-sm">
        <div class="flex justify-between">
          <span class="text-gray-500">Subtotal</span>
          <span id="subtotal">৳0.00</span>
        </div>
        <div class="flex justify-between">
          <span class="text-gray-500">Discount</span>
          <span id="discount-amt" class="text-red-500">-৳0.00</span>
        </div>
        <div class="flex justify-between">
          <span class="text-gray-500">Tax</span>
          <span id="tax-amt">+৳0.00</span>
        </div>
        <div class="flex justify-between font-semibold text-base pt-2 border-t">
          <span>Total</span>
          <span id="total-amt">৳0.00</span>
        </div>
      </div>
      <button type="submit"
              class="w-full mt-6 bg-blue-600 text-white py-2 rounded-lg text-sm">
        Create Invoice
      </button>
    </div>
  </div>

</div>
</form>

<script>
let itemCount = 0;

function addItem() {
    const i = itemCount++;
    const div = document.createElement('div');
    div.className = 'flex gap-2 mb-3 items-start';
    div.id = 'item-' + i;
    div.innerHTML = `
        <div class="flex-1">
            <input type="text" name="items[${i}][description]"
                   placeholder="Description e.g. Web design"
                   class="w-full border rounded-lg px-3 py-2 text-sm"
                   required>
        </div>
        <div class="w-20">
            <input type="number" name="items[${i}][quantity]"
                   placeholder="Qty" value="1" min="0.01" step="0.01"
                   class="w-full border rounded-lg px-3 py-2 text-sm"
                   oninput="recalculate()" required>
        </div>
        <div class="w-28">
            <input type="number" name="items[${i}][unit_price]"
                   placeholder="Price" min="0" step="0.01"
                   class="w-full border rounded-lg px-3 py-2 text-sm"
                   oninput="recalculate()" required>
        </div>
        <button type="button" onclick="removeItem(${i})"
                class="text-red-400 hover:text-red-600 mt-2 text-lg">×</button>
    `;
    document.getElementById('items-container').appendChild(div);
    recalculate();
}

function removeItem(i) {
    document.getElementById('item-' + i)?.remove();
    recalculate();
}

function recalculate() {
    let subtotal = 0;
    document.querySelectorAll('[name*="[quantity]"]').forEach((qtyInput, idx) => {
        const priceInput = document.querySelectorAll('[name*="[unit_price]"]')[idx];
        const qty   = parseFloat(qtyInput.value) || 0;
        const price = parseFloat(priceInput?.value) || 0;
        subtotal += qty * price;
    });
    const disc = parseFloat(document.querySelector('[name="discount_percent"]')?.value) || 0;
    const tax  = parseFloat(document.querySelector('[name="tax_percent"]')?.value) || 0;
    const afterDisc = subtotal * (1 - disc / 100);
    const total = afterDisc * (1 + tax / 100);

    document.getElementById('subtotal').textContent    = '৳' + subtotal.toFixed(2);
    document.getElementById('discount-amt').textContent = '-৳' + (subtotal - afterDisc).toFixed(2);
    document.getElementById('tax-amt').textContent     = '+৳' + (total - afterDisc).toFixed(2);
    document.getElementById('total-amt').textContent   = '৳' + total.toFixed(2);
}

addItem(); // Start with one empty item
</script>

@endsection