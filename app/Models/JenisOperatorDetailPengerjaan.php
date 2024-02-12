<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class JenisOperatorDetailPengerjaan extends Model
{
    use HasFactory, SoftDeletes;
    public $table = 'jenis_operator_detail_pekerjaan';
    protected $dates = ['deleted_at'];
    public $fillable = [
        'jenis_operator_detail_id',
        'jenis_posisi_pekerjaan',
        'link',
        'status',
    ];

    public function jenis_operator_detail()
    {
        return $this->belongsTo(\App\Models\JenisOperatorDetail::class, 'jenis_operator_detail_id');
    }
    
    public function operator_karyawan()
    {
        return $this->belongsTo(\App\Models\KaryawanOperator::class, 'id');
    }
}
