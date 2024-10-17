<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EmpDepartemenBagian extends Model
{
    use HasFactory, SoftDeletes;

    protected $connection= 'emp';
    public $table = 'departemen_bagian';
    protected $dates = ['deleted_at'];

    public $fillable = [
        'id',
        'id_departemen',
        'nama_bagian'
    ];
}
