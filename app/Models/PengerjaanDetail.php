<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PengerjaanDetail extends Model
{
    use HasFactory, SoftDeletes;
    public $table = 'pengerjaan_detail';
    protected $dates = ['deleted_at'];
    public $fillable = [
        'pengerjaan_id',
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
        'upah_dasar',
        'tunjangan_kerja',
        'tunjangan_kehadiran',
        'uang_makan',
        'lembur',
        'plus_1',
        'plus_2',
        'plus_3',
        'minus_1',
        'minus_2',
        'minus_3',
        'jht',
        'bpjs_kesehatan',
    ];
}
