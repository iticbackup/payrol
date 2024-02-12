<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class RitUMK extends Model
{
    use HasFactory, SoftDeletes;
    public $table = 'rit_umk';
    protected $dates = ['deleted_at'];
    public $incrementing = false;

    public $fillable = [
        'id',
        'kategori_upah',
        'rit_posisi_id',
        'rit_kendaraan_id',
        'rit_tujuan_id',
        'tarif',
        'tahun_aktif',
        'status',
    ];

    public function rit_posisi()
    {
        return $this->belongsTo(\App\Models\RitPosisi::class, 'rit_posisi_id');
    }

    public function rit_tujuan()
    {
        return $this->belongsTo(\App\Models\RitTujuan::class, 'rit_tujuan_id');
    }

    public function rit_kendaraan()
    {
        return $this->belongsTo(\App\Models\RitKendaraan::class, 'rit_kendaraan_id');
    }
}
