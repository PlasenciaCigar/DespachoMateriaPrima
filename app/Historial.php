<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Historial extends Model
{
    protected $table = 'historial';
    protected $fillable = ["id","fk_inventario_norma","fecha_salida","cantidad"];
    public $timestamps = false;
}
