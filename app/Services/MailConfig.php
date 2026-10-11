<?php

namespace App\Services;

use App\Models\Parametre;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class MailConfig
{
    public const CACHE_KEY = 'parametres.smtp';

    /** Config mail d'origine (.env), pour pouvoir y revenir si le SMTP est desactive. */
    private static ?array $origine = null;

    /**
     * Applique la configuration SMTP enregistree dans les parametres
     * (prioritaire sur le .env lorsqu'elle est activee).
     */
    public static function apply(): void
    {
        static::$origine ??= [
            'default' => config('mail.default'),
            'smtp' => config('mail.mailers.smtp'),
            'from' => config('mail.from'),
        ];

        try {
            $smtp = Cache::rememberForever(self::CACHE_KEY, function () {
                $param = Parametre::first();
                if (!$param || !$param->mail_actif || !$param->mail_host) {
                    return null;
                }

                return [
                    'host' => $param->mail_host,
                    'port' => (int) ($param->mail_port ?: 587),
                    'encryption' => $param->mail_encryption,
                    'username' => $param->mail_username,
                    // Valeur chiffree telle qu'en base : jamais de mot de passe en clair dans le cache
                    'password' => $param->getRawOriginal('mail_password'),
                    'from_address' => $param->mail_from_address ?: $param->email,
                    'from_name' => $param->mail_from_name ?: $param->nom_entreprise,
                ];
            });
        } catch (\Throwable $e) {
            // Table absente (installation / migration en cours) : on garde le .env
            return;
        }

        if (!$smtp) {
            static::restaurerOrigine();
            return;
        }

        try {
            $motDePasse = filled($smtp['password']) ? Crypt::decryptString($smtp['password']) : null;
        } catch (\Throwable $e) {
            $motDePasse = null; // APP_KEY changee : le mot de passe doit etre ressaisi
        }

        // Google affiche les mots de passe d'application en 4 blocs ("abcd efgh ijkl mnop") :
        // on retire les espaces colles avec, ils ne font jamais partie du mot de passe.
        if ($motDePasse !== null && static::estGmail($smtp['host'])) {
            $motDePasse = preg_replace('/\s+/', '', $motDePasse);
        }

        config([
            'mail.default' => 'smtp',
            'mail.mailers.smtp.scheme' => $smtp['encryption'] === 'ssl' ? 'smtps' : 'smtp',
            'mail.mailers.smtp.host' => $smtp['host'],
            'mail.mailers.smtp.port' => $smtp['port'],
            'mail.mailers.smtp.username' => $smtp['username'],
            'mail.mailers.smtp.password' => $motDePasse,
            'mail.mailers.smtp.auto_tls' => $smtp['encryption'] !== null,
            'mail.mailers.smtp.timeout' => 15,
        ]);

        if ($smtp['from_address']) {
            config([
                'mail.from.address' => $smtp['from_address'],
                'mail.from.name' => $smtp['from_name'],
            ]);
        }

        // Force la recreation du mailer avec la nouvelle config
        if (app()->resolved('mail.manager')) {
            Mail::purge('smtp');
        }
    }

    private static function restaurerOrigine(): void
    {
        if (config('mail.mailers.smtp') === static::$origine['smtp'] && config('mail.default') === static::$origine['default']) {
            return;
        }

        config([
            'mail.default' => static::$origine['default'],
            'mail.mailers.smtp' => static::$origine['smtp'],
            'mail.from' => static::$origine['from'],
        ]);

        if (app()->resolved('mail.manager')) {
            Mail::purge('smtp');
        }
    }

    public static function refresh(): void
    {
        Cache::forget(self::CACHE_KEY);
        static::apply();
    }

    public static function estConfigure(): bool
    {
        return config('mail.default') === 'smtp' && filled(config('mail.mailers.smtp.host'));
    }

    public static function estGmail(?string $host): bool
    {
        return (bool) preg_match('/(^|\.)(gmail|googlemail)\.com$/i', trim((string) $host));
    }

    /**
     * Message d'erreur SMTP lisible, sans identifiant ni mot de passe,
     * affichable a l'ecran et enregistrable dans les journaux.
     */
    public static function messageErreur(\Throwable $e): string
    {
        $brut = $e->getMessage();
        $code = preg_match('/\b(4\d\d|5\d\d)[ -](\d\.\d\.\d+)/', $brut, $m) ? " ({$m[1]} {$m[2]})" : '';

        if (preg_match('/\b534\b.*5\.7\.9|InvalidSecondFactor|Application-specific password required/is', $brut)) {
            return "Gmail refuse le mot de passe{$code} : ce compte utilise la validation en deux etapes. "
                . "Creez un mot de passe d application (16 caracteres) sur https://myaccount.google.com/apppasswords "
                . "et enregistrez-le dans Parametres a la place du mot de passe du compte.";
        }

        if (preg_match('/\b535\b|Failed to authenticate|Username and Password not accepted|BadCredentials/i', $brut)) {
            return "Identifiants SMTP refuses par le serveur{$code} : verifiez le nom d utilisateur et le mot de passe.";
        }

        if (preg_match('/Connection could not be established|Unable to connect|timed out|getaddrinfo/i', $brut)) {
            return 'Impossible de joindre le serveur SMTP ' . config('mail.mailers.smtp.host') . ':' . config('mail.mailers.smtp.port')
                . ' : verifiez le serveur, le port et le chiffrement (Gmail : smtp.gmail.com, port 587, TLS).';
        }

        return 'Erreur SMTP' . $code . ' : ' . static::masquer($brut);
    }

    /**
     * Retire d'un texte le nom d'utilisateur et le mot de passe SMTP, ainsi que toute adresse e-mail.
     */
    public static function masquer(string $texte): string
    {
        foreach ([config('mail.mailers.smtp.password'), config('mail.mailers.smtp.username')] as $secret) {
            if (is_string($secret) && strlen($secret) >= 3) {
                $texte = str_replace([$secret, base64_encode($secret)], '[masque]', $texte);
            }
        }

        $texte = preg_replace('/[A-Z0-9._%+-]+@[A-Z0-9.-]+\.[A-Z]{2,}/i', '[adresse masquee]', $texte);

        return Str::limit($texte, 300);
    }
}
