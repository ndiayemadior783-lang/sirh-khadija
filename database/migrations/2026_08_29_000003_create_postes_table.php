<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('postes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('departement_id')->nullable()->constrained()->nullOnDelete();
            $table->string('libelle');
            $table->text('description')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('postes');
    }
};
