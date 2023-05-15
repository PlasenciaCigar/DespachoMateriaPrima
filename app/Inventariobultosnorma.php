<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Inventariobultosnorma extends Model
{
    protected $table = 'inventariobultosnorma';
    protected $fillable = ["id","combinacion","cantidad","fecha"];
    public $timestamps = false;
}
