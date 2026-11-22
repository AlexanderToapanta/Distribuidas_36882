<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
class Muestra extends Model
{
    protected $table = 'muestras';
    protected $fillable = ['codigo','tipo','origen','lugar_recoleccion','fecha_recoleccion','observaciones','responsable_id'];
    protected $casts = ['fecha_recoleccion' => 'date'];
    public function responsable(): BelongsTo { return $this->belongsTo(Investigador::class, 'responsable_id'); }
    public function transferencias(): HasMany { return $this->hasMany(Transferencia::class)->latest(); }
    public function analisis(): HasOne { return $this->hasOne(Analisis::class); }
}
