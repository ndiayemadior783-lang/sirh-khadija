<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('contrats', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained()->cascadeOnDelete();
            $table->string('type')->default('CDI');
            $table->date('date_debut');
            $table->date('date_fin')->nullable();
            $table->decimal('salaire_base', 10, 2)->default(0);
            $table->string('statut')->default('en_cours');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('contrats');
    }
};
