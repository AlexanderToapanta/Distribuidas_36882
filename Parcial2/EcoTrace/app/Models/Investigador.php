<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
class Investigador extends Model
{
    protected $table = 'investigadores';
    protected $fillable = ['nombre','email'];
    public function muestras(): HasMany { return $this->hasMany(Muestra::class, 'responsable_id'); }
}
