<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UMKBoronganStempel extends Model
{
    use HasFactory, SoftDeletes;
    public $table = 'borongan_umk_stempel';
    public $incrementing = false;

    protected $dates = ['deleted_at'];

    public $fillable = [
        'id',
        'jenis_produk',
        'nominal_umk',
        'tahun_aktif',
        'status',
    ];
}
