<?php

namespace App\Services;

use App\Models\Parametre;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Mail;

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
}
