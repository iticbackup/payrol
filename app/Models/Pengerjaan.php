<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Pengerjaan extends Model
{
    use HasFactory, SoftDeletes;
    public $table = 'pengerjaan';
    protected $primaryKey = 'id';
    protected $dates = ['deleted_at'];
    public $incrementing = false;
    public $fillable = [
        'id',
        'kode_pengerjaan',
        'kode_payrol',
        'operator_karyawan_id',
        'tanggal_pengerjaan',
        'hasil_kerja_1',
        'hasil_kerja_2',
        'hasil_kerja_3',
        'hasil_kerja_4',
        'hasil_kerja_5',
        'total_jam_kerja_1',
        'total_jam_kerja_2',
        'total_jam_kerja_3',
        'total_jam_kerja_4',
        'total_jam_kerja_5',
        'uang_lembur',
        'lembur',
        // 'upah_dasar',
        // 'tunjangan_kerja',
        // 'tunjangan_kehadiran',
        // 'uang_makan',
        // 'plus_1',
        // 'plus_2',
        // 'plus_3',
        // 'minus_1',
        // 'minus_2',
        // 'minus_3',
        // 'jht',
        // 'bpjs_kesehatan',
    ];

    public function operator_karyawan()
    {
        return $this->belongsTo(\App\Models\KaryawanOperator::class, 'operator_karyawan_id');
    }
    // public $fillable = [
    //     'kode_pengerjaan',
    //     'kode_hasil_pengerjaan',
    //     // 'operator_karyawan_id',
    //     'tanggal_pengerjaan',
    // ];
}
