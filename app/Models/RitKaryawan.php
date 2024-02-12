<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class RitKaryawan extends Model
{
    use HasFactory, SoftDeletes;
    public $table = 'operator_supir_rit_karyawan';
    protected $dates = ['deleted_at'];
    public $incrementing = false;

    public $fillable = [
        'id',
        'nik',
        'rit_posisi_id',
        'tunjangan_kerja_id',
        'upah_dasar',
        'jht',
        'bpjs',
        'status',
    ];

    public function tunjangan_kerja()
    {
        return $this->belongsTo(\App\Models\TunjanganKerja::class, 'tunjangan_kerja_id');
    }

    public function rit_posisi()
    {
        return $this->belongsTo(\App\Models\RitPosisi::class, 'rit_posisi_id');
    }
}
