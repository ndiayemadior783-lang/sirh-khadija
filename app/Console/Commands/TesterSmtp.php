<?php

namespace App\Console\Commands;

use App\Exceptions\SmtpException;
use App\Mail\TestSmtpMail;
use App\Services\MailConfig;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class TesterSmtp extends Command
{
    protected $signature = 'smtp:tester {destinataire : Adresse qui recevra l e-mail de test}
                            {--verifier : Verifier la configuration sans envoyer d e-mail}';
    protected $description = 'Verifier la configuration SMTP enregistree et envoyer un e-mail de test (aucun secret affiche)';

    public function handle()
    {
        MailConfig::refresh();

        if (!MailConfig::estConfigure()) {
            $this->error('SMTP non active : renseignez et enregistrez la configuration dans Parametres > Messagerie (SMTP).');
            return self::FAILURE;
        }

        $smtp = config('mail.mailers.smtp');
        $motDePasse = (string) ($smtp['password'] ?? '');
        $gmail = MailConfig::estGmail($smtp['host'] ?? '');

        $this->table(['Element', 'Valeur'], [
            ['Serveur', $smtp['host'] . ':' . $smtp['port']],
            ['Securite', ($smtp['scheme'] ?? '') === 'smtps' ? 'SSL (smtps)' : (($smtp['auto_tls'] ?? true) ? 'STARTTLS' : 'aucune')],
            ['Utilisateur', $this->masquerAdresse((string) ($smtp['username'] ?? ''))],
            ['Expediteur', $this->masquerAdresse((string) config('mail.from.address'))],
            ['Mot de passe', $motDePasse === '' ? 'ABSENT' : 'present (' . strlen($motDePasse) . ' caracteres, non affiche)'],
        ]);

        $ok = true;
        if ($motDePasse === '') {
            $this->error('Aucun mot de passe enregistre (ou APP_KEY modifiee) : ressaisissez-le dans Parametres.');
            $ok = false;
        }
        if ($gmail && $motDePasse !== '' && strlen($motDePasse) !== 16) {
            $this->warn('Gmail attend un mot de passe d application de 16 caracteres : celui enregistre n a pas ce format.');
            $ok = false;
        }
        if ($gmail && (int) $smtp['port'] === 587 && ($smtp['scheme'] ?? '') === 'smtps') {
            $this->warn('Port 587 avec SSL : pour Gmail, choisissez TLS sur le port 587 (ou SSL sur le port 465).');
            $ok = false;
        }

        if ($this->option('verifier')) {
            $ok ? $this->info('Configuration coherente.') : $this->warn('Configuration a corriger (voir ci-dessus).');
            return $ok ? self::SUCCESS : self::FAILURE;
        }

        $destinataire = (string) $this->argument('destinataire');
        if (!filter_var($destinataire, FILTER_VALIDATE_EMAIL)) {
            $this->error('Adresse du destinataire invalide.');
            return self::FAILURE;
        }

        try {
            Mail::to($destinataire)->send(new TestSmtpMail());
        } catch (\Throwable $e) {
            $this->error('Echec de l envoi : ' . ($e instanceof SmtpException ? $e->getMessage() : MailConfig::messageErreur($e)));
            return self::FAILURE;
        }

        $this->info('E-mail de test envoye a ' . $this->masquerAdresse($destinataire) . '. Verifiez la boite de reception (et les spams).');
        return self::SUCCESS;
    }

    private function masquerAdresse(string $adresse): string
    {
        if ($adresse === '') {
            return '(vide)';
        }
        [$nom, $domaine] = array_pad(explode('@', $adresse, 2), 2, '');

        return substr($nom, 0, 2) . '***' . ($domaine !== '' ? '@' . $domaine : '');
    }
}
