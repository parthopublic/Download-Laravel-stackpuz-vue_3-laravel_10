<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Util;

class Customer extends Model
{
    protected $table = 'customer';
    protected $primaryKey = 'id';
    protected $fillable = [ 'name' ];
    public $incrementing = true;
    public $timestamps = false;
}