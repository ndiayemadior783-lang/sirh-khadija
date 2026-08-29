<?php

namespace App\Http\Controllers;

use App\Models\Contrat;
use App\Models\Departement;
use App\Models\Employe;
use App\Models\Poste;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class EmployeController extends Controller
{
    public function index(Request $request)
    {
        $query = Employe::with('poste', 'departement');

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('prenom', 'like', "%{$search}%")
                    ->orWhere('nom', 'like', "%{$search}%")
                    ->orWhere('matricule', 'like', "%{$search}%");
            });
        }

        if ($request->filled('departement_id')) {
            $query->where('departement_id', $request->input('departement_id'));
        }

        if ($request->filled('statut')) {
            $query->where('statut', $request->input('statut'));
        }

        $employes = $query->orderBy('created_at', 'desc')->paginate(12)->withQueryString();
        $departements = Departement::orderBy('nom')->get();

        return view('employes.index', compact('employes', 'departements'));
    }

    public function create()
    {
        $departements = Departement::orderBy('nom')->get();
        $postes = Poste::with('departement')->orderBy('libelle')->get();
        $responsables = Employe::where('statut', 'actif')->orderBy('prenom')->get();

        return view('employes.create', compact('departements', 'postes', 'responsables'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'prenom' => ['required', 'string', 'max:100'],
            'nom' => ['required', 'string', 'max:100'],
            'genre' => ['required', 'in:M,F'],
            'date_naissance' => ['nullable', 'date'],
            'email' => ['nullable', 'email', Rule::unique('employees', 'email')],
            'telephone' => ['nullable', 'string', 'max:30'],
            'adresse' => ['nullable', 'string', 'max:255'],
            'ville' => ['nullable', 'string', 'max:100'],
            'nationalite' => ['nullable', 'string', 'max:100'],
            'cin' => ['nullable', 'string', 'max:50'],
            'date_embauche' => ['required', 'date'],
            'statut' => ['required', 'in:actif,inactif,suspendu,demission'],
            'poste_id' => ['nullable', 'exists:postes,id'],
            'departement_id' => ['nullable', 'exists:departements,id'],
            'responsable_id' => ['nullable', 'exists:employees,id'],
            'salaire_base' => ['required', 'numeric', 'min:0'],
            'creer_compte' => ['nullable', 'boolean'],
            'email_compte' => ['nullable', 'email', Rule::unique('users', 'email')],
        ]);

        $data['matricule'] = 'KC-' . str_pad((string)(Employe::max('id') + 1), 3, '0', STR_PAD_LEFT);

        if (!empty($data['creer_compte']) && !empty($data['email_compte'])) {
            $user = User::create([
                'name' => $data['prenom'] . ' ' . $data['nom'],
                'email' => $data['email_compte'],
                'password' => Hash::make('password'),
                'role' => 'employe',
            ]);
            $data['user_id'] = $user->id;
        }

        unset($data['creer_compte'], $data['email_compte']);

        Employe::create($data);

        return redirect()->route('employes.index')->with('success', 'Employe ajoute avec succes.');
    }

    public function show(Employe $employe)
    {
        $employe->load('poste', 'departement', 'responsable', 'user');
        $employe->loadCount(['presences', 'conges']);

        $dernierContrat = $employe->contrats()->orderByDesc('date_debut')->first();
        $dernierBulletin = $employe->bulletins()->orderByDesc('period')->first();
        $presenceCeMois = $employe->presences()
            ->whereYear('date', now()->year)
            ->whereMonth('date', now()->month)
            ->count();
        $congeValideCeMois = $employe->conges()->where('statut', 'valide')->count();

        return view('employes.show', compact(
            'employe', 'dernierContrat', 'dernierBulletin', 'presenceCeMois', 'congeValideCeMois'
        ));
    }

    public function edit(Employe $employe)
    {
        $departements = Departement::orderBy('nom')->get();
        $postes = Poste::with('departement')->orderBy('libelle')->get();
        $responsables = Employe::where('statut', 'actif')->where('id', '!=', $employe->id)->orderBy('prenom')->get();

        return view('employes.edit', compact('employe', 'departements', 'postes', 'responsables'));
    }

    public function update(Request $request, Employe $employe)
    {
        $data = $request->validate([
            'prenom' => ['required', 'string', 'max:100'],
            'nom' => ['required', 'string', 'max:100'],
            'genre' => ['required', 'in:M,F'],
            'date_naissance' => ['nullable', 'date'],
            'email' => ['nullable', 'email', Rule::unique('employees', 'email')->ignore($employe->id)],
            'telephone' => ['nullable', 'string', 'max:30'],
            'adresse' => ['nullable', 'string', 'max:255'],
            'ville' => ['nullable', 'string', 'max:100'],
            'nationalite' => ['nullable', 'string', 'max:100'],
            'cin' => ['nullable', 'string', 'max:50'],
            'date_embauche' => ['required', 'date'],
            'statut' => ['required', 'in:actif,inactif,suspendu,demission'],
            'poste_id' => ['nullable', 'exists:postes,id'],
            'departement_id' => ['nullable', 'exists:departements,id'],
            'responsable_id' => ['nullable', 'exists:employees,id'],
            'salaire_base' => ['required', 'numeric', 'min:0'],
        ]);

        $employe->update($data);

        return redirect()->route('employes.show', $employe)->with('success', 'Employe mis a jour avec succes.');
    }

    public function destroy(Employe $employe)
    {
        $employe->delete();
        return redirect()->route('employes.index')->with('success', 'Employe supprime.');
    }
}