<?php

namespace App\Http\Controllers;

use App\Models\Client;
use Illuminate\Http\Request;

class ClientController extends Controller
{
    // Show all clients
    public function index()
    {
        $clients = Client::withCount('invoices')
            ->where('user_id', auth()->id())
            ->latest()
            ->get();

        return view('clients.index', compact('clients'));
    }

    // Show create form
    public function create()
    {
        return view('clients.create');
    }

    // Save new client
    public function store(Request $request)
    {
        $request->validate([
            'name'    => 'required|string|max:255',
            'email'   => 'nullable|email|max:255',
            'phone'   => 'nullable|string|max:20',
            'address' => 'nullable|string',
        ]);

        Client::create([
            'user_id' => auth()->id(),
            'name'    => $request->name,
            'email'   => $request->email,
            'phone'   => $request->phone,
            'address' => $request->address,
        ]);

        return redirect()->route('clients.index')
            ->with('success', 'Client added successfully!');
    }

    // Show edit form
    public function edit(Client $client)
    {
        // Make sure user owns this client
        if ($client->user_id !== auth()->id()) {
            abort(403);
        }

        return view('clients.edit', compact('client'));
    }

    // Update client
    public function update(Request $request, Client $client)
    {
        if ($client->user_id !== auth()->id()) {
            abort(403);
        }

        $request->validate([
            'name'    => 'required|string|max:255',
            'email'   => 'nullable|email|max:255',
            'phone'   => 'nullable|string|max:20',
            'address' => 'nullable|string',
        ]);

        $client->update($request->only('name', 'email', 'phone', 'address'));

        return redirect()->route('clients.index')
            ->with('success', 'Client updated successfully!');
    }

    // Delete client
    public function destroy(Client $client)
    {
        if ($client->user_id !== auth()->id()) {
            abort(403);
        }

        $client->delete();

        return redirect()->route('clients.index')
            ->with('success', 'Client deleted.');
    }

    // We don't need show() for clients
    // but Laravel resource needs it — just redirect
    public function show(Client $client)
    {
        return redirect()->route('clients.index');
    }
}
