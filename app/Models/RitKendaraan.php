<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class RitKendaraan extends Model
{
    use HasFactory, SoftDeletes;
    public $table = 'rit_kendaraan';
    protected $dates = ['deleted_at'];
    public $incrementing = false;

    public $fillable = [
        'id',
        'jenis_kendaraan',
    ];
}
