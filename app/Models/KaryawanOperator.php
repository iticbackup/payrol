<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class KaryawanOperator extends Model
{
    use HasFactory, SoftDeletes;
    protected $primaryKey = 'id';
    public $table = 'operator_karyawan';
    protected $dates = ['deleted_at'];
    public $incrementing = false;
    public $fillable = [
        'id',
        'nik',
        // 'nama_karyawan',
        'jenis_operator_id',
        'jenis_operator_detail_id',
        'jenis_operator_detail_pekerjaan_id',
        // 'level_id',
        // 'posisi_id',
        'tunjangan_kerja_id',
        'jht',
        'bpjs',
        'training',
        'status',
    ];

    public function jenis_operator()
    {
        return $this->belongsTo(\App\Models\JenisOperator::class, 'jenis_operator_id');
    }

    public function jenis_operator_detail()
    {
        return $this->belongsTo(\App\Models\JenisOperatorDetail::class, 'jenis_operator_detail_id');
    }

    public function jenis_operator_detail_pengerjaan()
    {
        return $this->belongsTo(\App\Models\JenisOperatorDetailPengerjaan::class, 'jenis_operator_detail_pekerjaan_id');
    }

    public function tunjangan_kerja()
    {
        return $this->belongsTo(\App\Models\TunjanganKerja::class, 'tunjangan_kerja_id');
    }

    public function biodata()
    {
        return $this->hasMany(\App\Models\BiodataKaryawan::class,'nik');
    }

    public function biodata_karyawan()
    {
        return $this->belongsTo(\App\Models\BiodataKaryawan::class, 'nik', 'nik');
    }
    
    // public function biodata_karyawan()
    // {
    //     return $this->belongsTo(\App\Models\BiodataKaryawan::class,'nik');
    // }

}
