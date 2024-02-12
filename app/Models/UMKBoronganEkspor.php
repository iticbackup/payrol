<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UMKBoronganEkspor extends Model
{
    use HasFactory, SoftDeletes;
    public $table = 'borongan_umk_ekspor';
    public $incrementing = false;

    protected $dates = ['deleted_at'];

    public $fillable = [
        'id',
        'jenis_produk',
        'umk_packing',
        'umk_kemas',
        'umk_pilih_gagang',
        'tahun_aktif',
        'status',
    ];
}
