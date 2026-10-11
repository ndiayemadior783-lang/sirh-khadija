<?php

namespace App\Http\Controllers;

use App\Models\Client;
use Illuminate\Http\Request;

class ClientController extends Controller
{
    public function index(Request $request)
    {
        $query = Client::withCount('factures');

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('nom', 'like', "%{$search}%")
                    ->orWhere('contact_nom', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('telephone', 'like', "%{$search}%")
                    ->orWhere('ninea', 'like', "%{$search}%");
            });
        }

        $clients = $query->orderBy('nom')->paginate(12)->withQueryString();

        return view('clients.index', compact('clients'));
    }

    public function create()
    {
        return view('clients.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nom' => ['required', 'string', 'max:200'],
            'contact_nom' => ['nullable', 'string', 'max:150'],
            'email' => ['nullable', 'email', 'max:150'],
            'telephone' => ['nullable', 'string', 'max:50'],
            'adresse' => ['nullable', 'string', 'max:255'],
            'ville' => ['nullable', 'string', 'max:100'],
            'ninea' => ['nullable', 'string', 'max:50'],
            'rc_cm' => ['nullable', 'string', 'max:50'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);

        $client = Client::create($data);

        activity()->performedOn($client)
            ->causedBy($request->user())
            ->withProperties(['nom' => $client->nom])
            ->log('Client créé');

        return redirect()->route('clients.index')->with('success', "Client « {$client->nom} » ajouté avec succès.");
    }

    public function edit(Client $client)
    {
        return view('clients.edit', compact('client'));
    }

    public function update(Request $request, Client $client)
    {
        $data = $request->validate([
            'nom' => ['required', 'string', 'max:200'],
            'contact_nom' => ['nullable', 'string', 'max:150'],
            'email' => ['nullable', 'email', 'max:150'],
            'telephone' => ['nullable', 'string', 'max:50'],
            'adresse' => ['nullable', 'string', 'max:255'],
            'ville' => ['nullable', 'string', 'max:100'],
            'ninea' => ['nullable', 'string', 'max:50'],
            'rc_cm' => ['nullable', 'string', 'max:50'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);

        $client->update($data);

        activity()->performedOn($client)
            ->causedBy($request->user())
            ->log('Client mis à jour');

        return redirect()->route('clients.index')->with('success', "Client « {$client->nom} » mis à jour.");
    }

    public function destroy(Request $request, Client $client)
    {
        if ($client->factures()->count() > 0) {
            return back()->with('error', 'Impossible de supprimer ce client : des factures lui sont déjà associées.');
        }

        activity()->performedOn($client)
            ->causedBy($request->user())
            ->withProperties(['nom' => $client->nom])
            ->log('Client supprimé');

        $client->delete();

        return redirect()->route('clients.index')->with('success', 'Client supprimé avec succès.');
    }
}
