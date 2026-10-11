<?php

namespace App\Http\Controllers;

use App\Mail\TestSmtpMail;
use App\Models\Parametre;
use App\Services\MailConfig;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class ParametreController extends Controller
{
    public function index()
    {
        $param = Parametre::instance();
        $fileAttente = $this->etatFileAttente();
        return view('parametres.index', compact('param', 'fileAttente'));
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
            'mail_actif' => ['boolean'],
            'mail_host' => ['nullable', 'required_if_accepted:mail_actif', 'string', 'max:255'],
            'mail_port' => ['nullable', 'required_if_accepted:mail_actif', 'integer', 'min:1', 'max:65535'],
            'mail_encryption' => ['nullable', 'in:tls,ssl'],
            'mail_username' => ['nullable', 'string', 'max:255'],
            'mail_password' => ['nullable', 'string', 'max:255'],
            'mail_from_address' => ['nullable', 'required_if_accepted:mail_actif', 'email', 'max:255'],
            'mail_from_name' => ['nullable', 'string', 'max:255'],
            'mail_notif_conges' => ['boolean'],
        ]);

        $data['mail_actif'] = $request->boolean('mail_actif');
        $data['mail_notif_conges'] = $request->boolean('mail_notif_conges');

        // Mot de passe vide = on conserve l'actuel (sauf demande explicite d'effacement)
        if ($request->boolean('mail_password_clear')) {
            $data['mail_password'] = null;
        } elseif (blank($data['mail_password'] ?? null)) {
            unset($data['mail_password']);
        }

        $param->update($data);
        MailConfig::refresh();

        return back()->with('success', 'Parametres mis a jour avec succes.');
    }

    public function testSmtp(Request $request)
    {
        $data = $request->validate([
            'destinataire' => ['required', 'email'],
        ]);

        if (!MailConfig::estConfigure()) {
            return back()->with('error', 'SMTP non active : renseignez et enregistrez la configuration SMTP avant le test.');
        }

        try {
            Mail::to($data['destinataire'])->send(new TestSmtpMail());
        } catch (\Throwable $e) {
            report($e);
            return back()->with('error', 'Echec de l envoi : ' . $e->getMessage());
        }

        return back()->with('success', 'E-mail de test envoye a ' . $data['destinataire'] . '.');
    }

    public function relancerEchecs(Request $request)
    {
        Artisan::call('queue:retry', ['id' => ['all']]);

        activity()->causedBy($request->user())->log('E-mails en echec relances');

        return back()->with('success', 'Les e-mails en echec ont ete remis en file d attente.');
    }

    private function etatFileAttente(): array
    {
        try {
            $exception = DB::table(config('queue.failed.table', 'failed_jobs'))->latest('failed_at')->value('exception');

            return [
                'en_attente' => DB::table(config('queue.connections.database.table', 'jobs'))->count(),
                'echecs' => DB::table(config('queue.failed.table', 'failed_jobs'))->count(),
                'dernier_echec' => $exception ? Str::limit(strtok($exception, "\n"), 250) : null,
                'connexion' => config('queue.default'),
            ];
        } catch (\Throwable $e) {
            return ['en_attente' => 0, 'echecs' => 0, 'dernier_echec' => null, 'connexion' => config('queue.default')];
        }
    }
}
