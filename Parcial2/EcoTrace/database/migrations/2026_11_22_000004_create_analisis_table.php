<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
    public function up(): void {
        Schema::create('analisis', function (Blueprint $table) {
            $table->id();
            $table->foreignId('muestra_id')->unique()->constrained('muestras')->cascadeOnDelete();
            $table->foreignId('investigador_id')->constrained('investigadores')->restrictOnDelete();
            $table->text('resultado');
            $table->timestamp('fecha_analisis');
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('analisis'); }
};
