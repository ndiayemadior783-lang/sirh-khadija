<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('conges', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained()->cascadeOnDelete();
            $table->string('type')->default('paye');
            $table->date('date_debut');
            $table->date('date_fin');
            $table->integer('jours');
            $table->text('motif')->nullable();
            $table->string('statut')->default('en_attente');
            $table->foreignId('valide_par')->nullable();
            $table->timestamp('date_validation')->nullable();
            $table->timestamps();

            $table->foreign('valide_par')->references('id')->on('users')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('conges');
    }
};
