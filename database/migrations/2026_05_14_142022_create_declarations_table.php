<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('declarations', function (Blueprint $table) {
            $table->id();
            $table->string('numero_dcl')->unique(); // Ex: LUB/1023/2026
            $table->foreignId('customs_office_id')->constrained();
            $table->string('importateur');
            $table->string('code_sh'); // Pour vérification contre la table Exemptions
            $table->decimal('montant_cif', 15, 2);
            $table->decimal('taxe_due', 15, 2); // 2% du CIF ou 0 si exempté
            $table->enum('statut', ['conforme', 'alerte', 'fraude_suspectée'])->default('conforme');
            $table->boolean('gps_validated')->default(false);
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();
            $table->foreignId('agent_id')->constrained('users'); // L'agent qui a traité
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('declarations');
    }
};