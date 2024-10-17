<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EmpPosisiJabatan extends Model
{
    use HasFactory, SoftDeletes;

    protected $connection= 'emp';
    public $table = 'posisi';
    protected $dates = ['deleted_at'];

    public $fillable = [
        'id',
        'id_jabatan',
        'nama_posisi',
    ];
}
