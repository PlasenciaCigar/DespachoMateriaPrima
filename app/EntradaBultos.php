<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class EntradaBultos extends Model
{
    protected $fillable = [
        'bultos', 'peso', 'marca', 'vitola', 'created_at',
    ];
}
