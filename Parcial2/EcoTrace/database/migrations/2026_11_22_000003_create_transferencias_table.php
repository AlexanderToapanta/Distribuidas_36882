<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
    public function up(): void {
        Schema::create('transferencias', function (Blueprint $table) {
            $table->id();
            $table->foreignId('muestra_id')->constrained('muestras')->cascadeOnDelete();
            $table->foreignId('origen_investigador_id')->constrained('investigadores')->restrictOnDelete();
            $table->foreignId('destino_investigador_id')->constrained('investigadores')->restrictOnDelete();
            $table->string('motivo', 200);
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('transferencias'); }
};
