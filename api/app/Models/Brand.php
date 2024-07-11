<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Util;

class Brand extends Model
{
    protected $table = 'brand';
    protected $primaryKey = 'id';
    protected $fillable = [ 'name' ];
    public $incrementing = true;
    public $timestamps = false;
}