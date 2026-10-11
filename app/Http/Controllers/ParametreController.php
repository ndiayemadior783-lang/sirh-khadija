<?php

namespace App\Http\Controllers;

use App\Models\Parametre;
use Illuminate\Http\Request;

class ParametreController extends Controller
{
    public function index()
    {
        $param = Parametre::instance();
        return view('parametres.index', compact('param'));
    }

    public function update(Request $request)
    {
        $param = Parametre::instance();

        $data = $request->validate([
            'nom_entreprise' => ['required', 'string', 'max:200'],
            'adresse' => ['nullable', 'string', 'max:300'],
            'telephone' => ['nullable', 'string', 'max:30'],
            'email' => ['nullable', 'email'],
            'nine_a' => ['nullable', 'string', 'max:30'],
            'rc_cm' => ['nullable', 'string', 'max:30'],
            'ipres_employeur' => ['required', 'numeric', 'min:0', 'max:1'],
            'ipres_employe' => ['required', 'numeric', 'min:0', 'max:1'],
            'ipm_employeur' => ['required', 'numeric', 'min:0', 'max:1'],
            'ipm_employe' => ['required', 'numeric', 'min:0', 'max:1'],
            'cfrp_employeur' => ['required', 'numeric', 'min:0', 'max:1'],
            'cfrp_employe' => ['required', 'numeric', 'min:0', 'max:1'],
            'taxe_exceptionnelle' => ['required', 'numeric', 'min:0', 'max:1'],
            'taux_cotisation' => ['required', 'numeric', 'min:0', 'max:1'],
            'taux_impot' => ['required', 'numeric', 'min:0', 'max:1'],
            'taux_horaire' => ['required', 'numeric', 'min:0'],
            'jours_conge_paye' => ['required', 'integer', 'min:0', 'max:365'],
        ]);

        $param->update($data);

        return back()->with('success', 'Parametres mis a jour avec succes.');
    }
}
