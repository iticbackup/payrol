<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TunjanganKerja extends Model
{
    use HasFactory, SoftDeletes;
    public $table = 'tunjangan_kerja';
    protected $dates = ['deleted_at'];
    public $incrementing = false;
    
    public $fillable = [
        'id',
        'golongan',
        'nominal',
    ];
}
