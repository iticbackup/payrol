<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class KirimGaji extends Model
{
    use HasFactory, SoftDeletes;
    public $table = 'kirim_slip_gaji';
    protected $dates = ['deleted_at'];

    public $fillable = [
        'id',
        'kode_pengerjaan',
        'kode_payrol',
        'pengerjaan_id',
        'nik',
        'nama_karyawan',
        'nominal_gaji',
        'status'
    ];

    public function karyawan_operator()
    {
        return $this->belongsTo(\App\Models\KaryawanOperator::class, 'nik', 'nik');
    }
    
    public function karyawan_operator_harian()
    {
        return $this->belongsTo(\App\Models\KaryawanOperatorHarian::class, 'nik', 'nik');
    }

    public function karyawan_operator_supir_rit()
    {
        return $this->belongsTo(\App\Models\RitKaryawan::class, 'nik', 'nik');
    }

    public function pengerjaan_weekly()
    {
        return $this->belongsTo(\App\Models\PengerjaanWeekly::class, 'pengerjaan_id', 'id');
    }

    public function pengerjaan_harian()
    {
        return $this->belongsTo(\App\Models\PengerjaanHarian::class, 'pengerjaan_id', 'id');
    }

    public function pengerjaan_supir_rit()
    {
        return $this->belongsTo(\App\Models\PengerjaanRITWeekly::class, 'pengerjaan_id', 'id');
    }
}
