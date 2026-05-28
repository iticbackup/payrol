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
        'jenis_operator_id',
        'jenis_operator_detail_id',
        'jenis_operator_detail_pekerjaan_id',
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

    public function jenis_operator()
    {
        return $this->belongsTo(\App\Models\JenisOperator::class, 'jenis_operator_id','id');
    }

    public function jenis_operator_detail()
    {
        return $this->belongsTo(\App\Models\JenisOperatorDetail::class, 'jenis_operator_detail_id','id');
    }

    public function jenis_operator_detail_pengerjaan()
    {
        return $this->belongsTo(\App\Models\JenisOperatorDetailPengerjaan::class, 'jenis_operator_detail_pekerjaan_id','id');
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

    public function biodata_karyawan()
    {
        return $this->belongsTo(\App\Models\BiodataKaryawan::class, 'nik', 'nik');
    }

    public function new_data_pengerjaan()
    {
        return $this->belongsTo(\App\Models\NewDataPengerjaan::class, 'kode_pengerjaan', 'kode_pengerjaan');
    }
}
