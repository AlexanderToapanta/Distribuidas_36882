<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
class Analisis extends Model
{
    protected $table = 'analisis';
    protected $fillable = ['muestra_id','investigador_id','resultado','fecha_analisis'];
    protected $casts = ['fecha_analisis'=>'datetime'];
    public function muestra(): BelongsTo { return $this->belongsTo(Muestra::class); }
    public function investigador(): BelongsTo { return $this->belongsTo(Investigador::class); }
}
