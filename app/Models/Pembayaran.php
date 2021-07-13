<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pembayaran extends Model
{
    protected $table  = 'pembayaran';
    protected $primaryKey = 'ID';
    protected $guarded = ['ID'];
    public $timestamps = false;
}
