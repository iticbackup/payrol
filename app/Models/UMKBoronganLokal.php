<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UMKBoronganLokal extends Model
{
    use HasFactory, SoftDeletes;
    public $table = 'borongan_umk_lokal';
    public $incrementing = false;

    protected $dates = ['deleted_at'];

    public $fillable = [
        'id',
        'jenis_produk',
        'umk_packing',
        'umk_bandrol',
        'umk_inner',
        'umk_outer',
        'target_packing',
        'target_bandrol',
        'target_inner',
        'target_outer',
        'tahun_aktif',
        'status',
    ];
}
