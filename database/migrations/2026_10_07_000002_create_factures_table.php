<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('factures', function (Blueprint $table) {
            $table->id();
            $table->string('numero')->unique();
            $table->foreignId('client_id')->constrained('clients')->cascadeOnDelete();
            $table->date('date_facture');
            $table->date('date_echeance')->nullable();
            $table->string('statut')->default('brouillon'); // brouillon, emise, payee, annulee
            $table->string('mode_paiement')->nullable(); // virement, cheque, especes, wave, orange_money
            $table->date('date_paiement')->nullable();
            $table->string('reference_paiement')->nullable();
            $table->boolean('appliquer_tva')->default(true);
            $table->decimal('taux_tva', 5, 2)->default(18.00);
            $table->decimal('total_ht', 14, 2)->default(0);
            $table->decimal('montant_tva', 14, 2)->default(0);
            $table->decimal('total_ttc', 14, 2)->default(0);
            $table->text('conditions_reglement')->nullable();
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('factures');
    }
};
