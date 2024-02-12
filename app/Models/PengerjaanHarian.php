<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PengerjaanHarian extends Model
{
    use HasFactory, SoftDeletes;
    protected $primaryKey = 'id';
    public $table = 'pengerjaan_harian';
    protected $dates = ['deleted_at'];
    public $incrementing = false;
    public $fillable = [
        'id',
        'kode_pengerjaan',
        'kode_payrol',
        'operator_harian_karyawan_id',
        'upah_dasar',
        'upah_dasar_weekly',
        'hari_kerja',
        'hasil_kerja',
        'tunjangan_kerja',
        'tunjangan_kehadiran',
        'plus_1',
        'plus_2',
        'plus_3',
        'minus_1',
        'minus_2',
        'minus_3',
        'uang_makan',
        'lembur',
        'jht',
        'bpjs_kesehatan',
        'pensiun',
    ];

    public function operator_karyawan()
    {
        return $this->belongsTo(\App\Models\KaryawanOperator::class, 'operator_karyawan_id');
    }

    // public function pengerjaan_1()
    // {
    //     return $this->belongsTo(\App\Models\Pengerjaan::class, 'operator_karyawan_id');
    // }
    // public $fillable = [
    //     'kode_pengerjaan',
    //     'kode_hasil_pengerjaan',
    //     // 'operator_karyawan_id',
    //     'tanggal_pengerjaan',
    // ];
}
