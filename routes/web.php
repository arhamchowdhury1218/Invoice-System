<?php

use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\ProfileController;
use App\Models\Invoice;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\ClientController;
use Barryvdh\DomPDF\Facade\Pdf;

// Route::get('/profile', function () {
//     return view('welcome');
// });

Route::middleware('guest')->group(function () {
    Route::get('/', [RegisterController::class, 'create'])->name('register');
    Route::post('/', [RegisterController::class, 'store']);
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});
// In DashboardController or web.php route
Route::get('/dashboard', function () {
    $stats = [
        'total_invoices' => Invoice::where('user_id', auth()->id())->count(),
        'total_revenue'  => Invoice::where('user_id', auth()->id())
            ->where('status', 'paid')->sum('total'),
        'outstanding'    => Invoice::where('user_id', auth()->id())
            ->whereIn('status', ['draft', 'sent'])->sum('total'),
        'overdue'        => Invoice::where('user_id', auth()->id())
            ->where('status', '!=', 'paid')
            ->where('due_date', '<', now())->count(),
    ];
    $recentInvoices = Invoice::with('client')
        ->where('user_id', auth()->id())
        ->latest()->take(5)->get();

    return view('dashboard', compact('stats', 'recentInvoices'));
})->middleware('auth')->name('dashboard');

Route::middleware('auth')->group(function () {

    // Dashboard
    Route::get('/dashboard', function () {
        $stats = [
            'total_invoices' => \App\Models\Invoice::where('user_id', auth()->id())->count(),
            'total_revenue'  => \App\Models\Invoice::where('user_id', auth()->id())->where('status', 'paid')->sum('total'),
            'outstanding'    => \App\Models\Invoice::where('user_id', auth()->id())->whereIn('status', ['draft', 'sent'])->sum('total'),
            'overdue'        => \App\Models\Invoice::where('user_id', auth()->id())->where('status', '!=', 'paid')->where('due_date', '<', now())->count(),
        ];
        $recentInvoices = \App\Models\Invoice::with('client')->where('user_id', auth()->id())->latest()->take(5)->get();
        return view('dashboard', compact('stats', 'recentInvoices'));
    })->name('dashboard');

    // Clients
    Route::resource('clients', ClientController::class);

    // Invoices
    Route::resource('invoices', InvoiceController::class);
    Route::patch(
        'invoices/{invoice}/paid',
        [InvoiceController::class, 'markPaid']
    )->name('invoices.markPaid');
    Route::get(
        'invoices/{invoice}/pdf',
        [InvoiceController::class, 'downloadPdf']
    )->name('invoices.pdf');
});

require __DIR__ . '/auth.php';
