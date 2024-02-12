<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PengerjaanRITHarian extends Model
{
    use HasFactory, SoftDeletes;
    public $table = 'pengerjaan_supir_rit';
    protected $primaryKey = 'id';
    protected $dates = ['deleted_at'];
    public $incrementing = false;
    public $fillable = [
        'id',
        'kode_pengerjaan',
        'kode_payrol',
        'karyawan_supir_rit_id',
        'tanggal_pengerjaan',
        'hasil_kerja_1',
        'hasil_kerja_2',
        'dpb',
        'upah_dasar'
    ];
}
