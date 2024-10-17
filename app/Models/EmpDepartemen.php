<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EmpDepartemen extends Model
{
    use HasFactory, SoftDeletes;
    protected $connection= 'emp';
    public $table = 'departemen';
    protected $dates = ['deleted_at'];

    public $fillable = [
        'id',
        'nama_departemen'
    ];
}
