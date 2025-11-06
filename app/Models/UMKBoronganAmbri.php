<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UMKBoronganAmbri extends Model
{
    use HasFactory, SoftDeletes;
    public $table = 'borongan_umk_ambri';
    public $incrementing = false;

    protected $dates = ['deleted_at'];

    public $fillable = [
        'id',
        'jenis_produk',
        'umk_etiket',
        'umk_las_tepi',
        'umk_las_pojok',
        'umk_ambri',
        'target_etiket',
        'target_las_tepi',
        'target_las_pojok',
        'target_ambri',
        'tahun_aktif',
        'status',
    ];
}
