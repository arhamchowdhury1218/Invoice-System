<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\ClientController;
use App\Models\Invoice;
// use App\Http\Controllers\Auth\AuthenticatedSessionController;

/*
|--------------------------------------------------------------------------
| Guest Routes (Not Logged In)
|--------------------------------------------------------------------------
*/

Route::middleware('guest')->group(function () {
    Route::get('/', [RegisterController::class, 'create'])->name('register');
    Route::post('/', [RegisterController::class, 'store']);
    // Route::get('/', [AuthenticatedSessionController::class, 'create']);
});

/*
|--------------------------------------------------------------------------
| Authenticated Routes (Logged In Users)
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {

    /*
    | Dashboard
    */
    Route::get('/dashboard', function () {
        $stats = [
            'total_invoices' => Invoice::where('user_id', auth()->id())->count(),

            'total_revenue' => Invoice::where('user_id', auth()->id())
                ->where('status', 'paid')
                ->sum('total'),

            'outstanding' => Invoice::where('user_id', auth()->id())
                ->whereIn('status', ['draft', 'sent'])
                ->sum('total'),

            'overdue' => Invoice::where('user_id', auth()->id())
                ->where('status', '!=', 'paid')
                ->where('due_date', '<', now())
                ->count(),
        ];

        $recentInvoices = Invoice::with('client')
            ->where('user_id', auth()->id())
            ->latest()
            ->take(5)
            ->get();

        return view('dashboard', compact('stats', 'recentInvoices'));
    })->name('dashboard');

    /*
    | Profile Routes
    */
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    /*
    | Clients Resource
    */
    Route::resource('clients', ClientController::class);

    /*
    | Invoices Resource
    */
    Route::resource('invoices', InvoiceController::class);

    /*
    | Extra Invoice Actions
    */
    Route::patch('invoices/{invoice}/paid', [InvoiceController::class, 'markPaid'])
        ->name('invoices.markPaid');

    Route::get('invoices/{invoice}/pdf', [InvoiceController::class, 'downloadPdf'])
        ->name('invoices.pdf');
});

/*
|--------------------------------------------------------------------------
| Auth System (Login, Logout, etc.)
|--------------------------------------------------------------------------
*/
require __DIR__ . '/auth.php';
