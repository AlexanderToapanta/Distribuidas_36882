<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
class Transferencia extends Model
{
    protected $table = 'transferencias';
    protected $fillable = ['muestra_id','origen_investigador_id','destino_investigador_id','motivo'];
    public function muestra(): BelongsTo { return $this->belongsTo(Muestra::class); }
    public function origen(): BelongsTo { return $this->belongsTo(Investigador::class, 'origen_investigador_id'); }
    public function destino(): BelongsTo { return $this->belongsTo(Investigador::class, 'destino_investigador_id'); }
}
