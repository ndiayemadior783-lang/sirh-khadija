<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Colonnes SMTP et leur definition. Migration additive : seules les colonnes
     * absentes sont ajoutees, aucune colonne ni donnee existante n'est modifiee.
     */
    private function colonnes(): array
    {
        return [
            'mail_actif' => fn (Blueprint $t) => $t->boolean('mail_actif')->default(false),
            'mail_host' => fn (Blueprint $t) => $t->string('mail_host')->nullable(),
            'mail_port' => fn (Blueprint $t) => $t->unsignedInteger('mail_port')->nullable(),
            'mail_encryption' => fn (Blueprint $t) => $t->string('mail_encryption')->nullable(), // tls, ssl ou null
            'mail_username' => fn (Blueprint $t) => $t->string('mail_username')->nullable(),
            'mail_password' => fn (Blueprint $t) => $t->text('mail_password')->nullable(), // chiffre
            'mail_from_address' => fn (Blueprint $t) => $t->string('mail_from_address')->nullable(),
            'mail_from_name' => fn (Blueprint $t) => $t->string('mail_from_name')->nullable(),
            'mail_notif_conges' => fn (Blueprint $t) => $t->boolean('mail_notif_conges')->default(true),
        ];
    }

    public function up(): void
    {
        $manquantes = array_filter(
            $this->colonnes(),
            fn ($nom) => ! Schema::hasColumn('parametres', $nom),
            ARRAY_FILTER_USE_KEY
        );

        if ($manquantes === []) {
            return;
        }

        Schema::table('parametres', function (Blueprint $table) use ($manquantes) {
            foreach ($manquantes as $definir) {
                $definir($table);
            }
        });
    }

    public function down(): void
    {
        $presentes = array_values(array_filter(
            array_keys($this->colonnes()),
            fn ($nom) => Schema::hasColumn('parametres', $nom)
        ));

        if ($presentes !== []) {
            Schema::table('parametres', fn (Blueprint $table) => $table->dropColumn($presentes));
        }
    }
};
