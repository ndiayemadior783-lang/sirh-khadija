<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('parametres', function (Blueprint $table) {
            $table->boolean('mail_actif')->default(false);
            $table->string('mail_host')->nullable();
            $table->unsignedInteger('mail_port')->nullable();
            $table->string('mail_encryption')->nullable(); // tls, ssl ou null
            $table->string('mail_username')->nullable();
            $table->text('mail_password')->nullable(); // chiffre
            $table->string('mail_from_address')->nullable();
            $table->string('mail_from_name')->nullable();
            $table->boolean('mail_notif_conges')->default(true);
        });
    }

    public function down(): void
    {
        Schema::table('parametres', function (Blueprint $table) {
            $table->dropColumn([
                'mail_actif', 'mail_host', 'mail_port', 'mail_encryption', 'mail_username',
                'mail_password', 'mail_from_address', 'mail_from_name', 'mail_notif_conges',
            ]);
        });
    }
};
