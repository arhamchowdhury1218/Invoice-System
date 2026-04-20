<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Invoice;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class InvoiceController extends Controller
{

    public function index(Request $request)
    {
        $query = Invoice::with('client')
            ->where('user_id', auth()->id());

        // Filter by status tab
        if ($request->status && $request->status !== 'all') {
            if ($request->status === 'overdue') {
                $query->where('status', '!=', 'paid')
                    ->where('due_date', '<', now());
            } else {
                $query->where('status', $request->status);
            }
        }

        $invoices = $query->latest()->paginate(15);
        return view('invoices.index', compact('invoices'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'client_id'        => 'required|exists:clients,id',
            'issue_date'       => 'required|date',
            'due_date'         => 'required|date|after:issue_date',
            'discount_percent' => 'nullable|numeric|min:0|max:100',
            'tax_percent'      => 'nullable|numeric|min:0|max:100',
            'items'            => 'required|array|min:1',
            'items.*.description' => 'required|string',
            'items.*.quantity'    => 'required|numeric|min:0.01',
            'items.*.unit_price'  => 'required|numeric|min:0',
        ]);

        DB::transaction(function () use ($request) {
            $subtotal = collect($request->items)->sum(function ($item) {
                return $item['quantity'] * $item['unit_price'];
            });

            $invoice = Invoice::create([
                'user_id'          => auth()->id(),
                'client_id'        => $request->client_id,
                'invoice_number'   => Invoice::generateNumber(),
                'issue_date'       => $request->issue_date,
                'due_date'         => $request->due_date,
                'discount_percent' => $request->discount_percent ?? 0,
                'tax_percent'      => $request->tax_percent ?? 0,
                'subtotal'         => $subtotal,
                'total'            => 0,
                'status'           => 'draft',
                'notes'            => $request->notes,
            ]);

            foreach ($request->items as $item) {
                $invoice->items()->create($item);
            }

            $invoice->update(['total' => $invoice->calculateTotal()]);
        });

        return redirect()->route('invoices.index')
            ->with('success', 'Invoice created!');
    }

    public function markPaid(Invoice $invoice)
    {
        $invoice->update(['status' => 'paid']);
        return back()->with('success', 'Invoice marked as paid.');
    }

    // public function downloadPdf(Invoice $invoice)
    // {
    //     $invoice->load('client', 'items');
    //     $pdf = Pdf::loadView('invoices.pdf', compact('invoice'));
    //     return $pdf->download('invoice-' . $invoice->invoice_number . '.pdf');
    // }
    public function downloadPdf(Invoice $invoice)
    {
        $invoice->load('client', 'items');
        $pdf = Pdf::loadView('invoices.pdf', compact('invoice'));
        return $pdf->download('invoice-' . $invoice->invoice_number . '.pdf');
    }

    public function show(Invoice $invoice)
    {
        // Load relationships to avoid N+1
        $invoice->load('client', 'items');
        return view('invoices.show', compact('invoice'));
    }

    public function create()
    {
        // Pass clients list to the create form dropdown
        $clients = Client::where('user_id', auth()->id())->get();
        return view('invoices.create', compact('clients'));
    }
}
