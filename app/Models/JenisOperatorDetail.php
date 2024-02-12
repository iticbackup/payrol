<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class JenisOperatorDetail extends Model
{
    use HasFactory, SoftDeletes;
    public $table = 'jenis_operator_detail';
    protected $dates = ['deleted_at'];
    public $fillable = [
        'jenis_operator_id',
        'jenis_posisi',
        'status',
    ];

    public function jenis_operator()
    {
        return $this->belongsTo(\App\Models\JenisOperator::class, 'jenis_operator_id');
    }

    public function jenis_operator_detail_pekerjaan()
    {
        return $this->belongsTo(\App\Models\JenisOperatorDetailPengerjaan::class, 'jenis_operator_detail');
    }
}
