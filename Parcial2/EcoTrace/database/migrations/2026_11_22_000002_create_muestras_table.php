<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
    public function up(): void {
        Schema::create('muestras', function (Blueprint $table) {
            $table->id();
            $table->string('codigo', 30)->unique();
            $table->string('tipo', 40);
            $table->string('origen', 120);
            $table->string('lugar_recoleccion', 120);
            $table->date('fecha_recoleccion');
            $table->string('estado', 25)->default('recolectada');
            $table->text('observaciones')->nullable();
            $table->foreignId('responsable_id')->constrained('investigadores')->restrictOnDelete();
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('muestras'); }
};
