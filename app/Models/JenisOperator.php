<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class JenisOperator extends Model
{
    use HasFactory, SoftDeletes;
    public $table = 'jenis_operator';
    protected $dates = ['deleted_at'];
    public $fillable = [
        'kode_operator',
        'jenis_operator',
        'status',
    ];

    public function jenisOperatorDetails()
    {
        return $this->hasMany(\App\Models\JenisOperatorDetail::class, 'jenis_operator_id','id');
    }
}
