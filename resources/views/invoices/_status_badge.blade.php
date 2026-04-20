@php
    $colors = [
        'draft'   => 'bg-gray-100 text-gray-600',
        'sent'    => 'bg-blue-100 text-blue-700',
        'paid'    => 'bg-green-100 text-green-700',
        'overdue' => 'bg-red-100 text-red-700',
    ];
    $color = $colors[$status] ?? 'bg-gray-100 text-gray-600';
@endphp
<span class="px-2 py-1 rounded-full text-xs font-medium {{ $color }}">
    {{ ucfirst($status) }}
</span>