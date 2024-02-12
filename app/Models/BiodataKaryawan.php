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
        'nama',
        'alamat',
        'id_posisi',
        'id_jabatan',
        'satuan_kerja',
        'rekening',
        'credit',
        'jenis_kelamin',
        'status_klg',
        'npwp',
        'pin',
        'status_karyawan',
        'tempat_lahir',
        'kewarganegaraan',
        'agama',
        'status_kontrak',
        'tanggal_resign',
    ];

    public function operator_karyawan()
    {
        return $this->belongsTo(\App\Models\KaryawanOperator::class, 'nik');
    }
}
