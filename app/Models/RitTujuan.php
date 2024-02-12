<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class RitTujuan extends Model
{
    use HasFactory, SoftDeletes;
    public $table = 'rit_tujuan';
    protected $dates = ['deleted_at'];
    public $incrementing = false;

    public $fillable = [
        'id',
        'kode_tujuan',
        'tujuan',
    ];
}
