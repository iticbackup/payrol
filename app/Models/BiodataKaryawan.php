<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
// use Illuminate\Database\Eloquent\SoftDeletes;

class BiodataKaryawan extends Model
{
    use HasFactory;
    protected $connection= 'emp';
    public $table = 'biodata_karyawan';
    public $timestamps = false;
    // public $table = 'biodata_karyawan';
    // protected $dates = ['deleted_at'];
    public $fillable = [
        'nik',
        // 'nama',
        // 'tempat_lahir',
        // 'tgl_lahir',
        // 'alamat',
        // 'jenis_kelamin',
        // 'id_posisi',
        // 'id_jabatan',
        // 'id_departemen',
        // 'id_departemen_bagian',
        // 'departemen_dept',
        // 'departemen_bagian',
        // 'departemen_level',
        // 'rekening',
        // 'credit',
        // 'agama',
        // 'status_klg',
        // 'no_npwp',
        // 'no_telp',
        // 'no_bpjs_ketenagakerjaan',
        // 'no_bpjs_kesehatan',
        // 'no_rekening_mandiri',
        // 'no_rekening_bws',
        // 'no_rekening_bca',
        // 'golongan_darah',
        // 'pendidikan',
        // 'email',
        // 'kunci_loker',
        // 'sim_kendaraan',
        // 'pin',
        // 'kewarganegaraan',
        // 'foto_karyawan',
        // 'tanggal_masuk',
    ];

    public function operator_karyawan()
    {
        return $this->belongsTo(\App\Models\KaryawanOperator::class, 'nik');
    }
}
