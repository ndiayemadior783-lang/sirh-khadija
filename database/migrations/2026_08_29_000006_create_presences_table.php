<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('presences', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained()->cascadeOnDelete();
            $table->date('date');
            $table->time('heure_arrivee')->nullable();
            $table->time('heure_depart')->nullable();
            $table->string('statut')->default('present');
            $table->decimal('heures_sup', 4, 2)->default(0);
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->unique(['employee_id', 'date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('presences');
    }
};
