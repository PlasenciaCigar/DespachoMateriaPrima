<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SalidaMateriaPrima extends Model
{
    protected $table = 'salidas_materia_primas';
    protected $fillable = ["id","id_combinacion","cantidad","created_at", "updated_at"];
}
