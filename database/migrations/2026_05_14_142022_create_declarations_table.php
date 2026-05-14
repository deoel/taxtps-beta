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
            $table->string('numero_dcl')->unique();
            $table->foreignId('customs_office_id')->constrained();
            $table->string('importateur');
            $table->string('code_sh');
            $table->decimal('montant_cif', 15, 2);
            $table->decimal('taxe_due', 15, 2);
            $table->integer('priority_score')->default(0);
            $table->enum('statut', ['conforme', 'alerte', 'fraude_suspectée', 'en_attente', 'suspect', 'valide', 'litige'])->default('en_attente');
            $table->boolean('gps_validated')->default(false);
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();
            $table->foreignId('agent_id')->nullable()->constrained('users');
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