<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('employees', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->unique()->constrained()->nullOnDelete();
            $table->string('matricule')->unique();
            $table->string('prenom');
            $table->string('nom');
            $table->string('genre')->default('M');
            $table->date('date_naissance')->nullable();
            $table->string('email')->nullable();
            $table->string('telephone')->nullable();
            $table->string('adresse')->nullable();
            $table->string('ville')->nullable();
            $table->string('nationalite')->nullable();
            $table->string('cin')->nullable();
            $table->date('date_embauche');
            $table->string('statut')->default('actif');
            $table->foreignId('poste_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('departement_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('responsable_id')->nullable();
            $table->decimal('salaire_base', 10, 2)->default(0);
            $table->string('photo')->nullable();
            $table->timestamps();

            $table->foreign('responsable_id')->references('id')->on('employees')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('employees');
    }
};
