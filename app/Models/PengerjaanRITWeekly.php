<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PengerjaanRITWeekly extends Model
{
    use HasFactory, SoftDeletes;
    public $table = 'pengerjaan_supir_rit_weekly';
    protected $primaryKey = 'id';
    protected $dates = ['deleted_at'];
    public $incrementing = false;
    public $fillable = [
        'id',
        'kode_pengerjaan',
        'kode_payrol',
        'karyawan_supir_rit_id',
        'total_hasil',
        'tunjangan_kerja',
        'tunjangan_kehadiran',
        'uang_makan',
        'plus_1',
        'plus_2',
        'plus_3',
        'minus_1',
        'minus_2',
        'lembur',
        'jht',
        'bpjs_kesehatan',
        'pensiun',
    ];

    public function operator_supir_rit()
    {
        return $this->belongsTo(\App\Models\RitKaryawan::class, 'karyawan_supir_rit_id');
    }
}
