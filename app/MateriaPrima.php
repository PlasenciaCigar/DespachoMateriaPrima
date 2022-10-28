<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class MateriaPrima extends Model
{
    protected $fillable=["Codigo","Descripcion","Libras", "created_at", "updated_at"];

    protected $primaryKey = 'Codigo';
    protected $keyType = 'string';
}
