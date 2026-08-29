<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bulletins_paie', function (Blueprint $table) {
            $table->id();
            $table->string('numero')->unique();
            $table->foreignId('employee_id')->constrained()->cascadeOnDelete();
            $table->string('period'); // 2026-08
            $table->decimal('salaire_base', 10, 2)->default(0);
            $table->decimal('primes', 10, 2)->default(0);
            $table->decimal('heures_sup', 10, 2)->default(0);
            $table->decimal('indemnites', 10, 2)->default(0);
            $table->decimal('salaire_brut', 10, 2)->default(0);
            $table->decimal('cotisations_sociales', 10, 2)->default(0);
            $table->decimal('impots', 10, 2)->default(0);
            $table->decimal('salaire_net', 10, 2)->default(0);
            $table->string('statut')->default('en_attente');
            $table->date('date_paiement')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->unique(['employee_id', 'period']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bulletins_paie');
    }
};
