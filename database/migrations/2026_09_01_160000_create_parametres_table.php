<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('parametres', function (Blueprint $table) {
            $table->id();
            $table->string('nom_entreprise')->default('Khadija Consulting');
            $table->string('adresse')->default('Dakar, Senegal');
            $table->string('telephone')->nullable();
            $table->string('email')->nullable();
            $table->string('nine_a')->nullable();
            $table->string('rc_cm')->nullable();
            $table->decimal('ipres_employeur', 6, 4)->default(0.084);
            $table->decimal('ipres_employe', 6, 4)->default(0.056);
            $table->decimal('ipm_employeur', 6, 4)->default(0.0325);
            $table->decimal('ipm_employe', 6, 4)->default(0.0125);
            $table->decimal('cfrp_employeur', 6, 4)->default(0.06);
            $table->decimal('cfrp_employe', 6, 4)->default(0.06);
            $table->decimal('taxe_exceptionnelle', 6, 4)->default(0.03);
            $table->decimal('taux_cotisation', 6, 4)->default(0.06);
            $table->decimal('taux_impot', 6, 4)->default(0.08);
            $table->decimal('taux_horaire', 10, 2)->default(1500);
            $table->integer('jours_conge_paye')->default(30);
            $table->timestamps();
        });

        // Insert les parametres par defaut
        DB::table('parametres')->insert([
            'nom_entreprise' => 'Khadija Consulting',
            'adresse' => 'Dakar, Senegal',
            'ipres_employeur' => 0.084,
            'ipres_employe' => 0.056,
            'ipm_employeur' => 0.0325,
            'ipm_employe' => 0.0125,
            'cfrp_employeur' => 0.06,
            'cfrp_employe' => 0.06,
            'taxe_exceptionnelle' => 0.03,
            'taux_cotisation' => 0.06,
            'taux_impot' => 0.08,
            'taux_horaire' => 1500,
            'jours_conge_paye' => 30,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('parametres');
    }
};