<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BPJSJHT extends Model
{
    use HasFactory, SoftDeletes;
    public $table = 'bpjs_jht';
    protected $dates = ['deleted_at'];
    public $incrementing = false;
    
    public $fillable = [
        'id',
        'urutan',
        'keterangan',
        'nominal',
        'masa_kerja',
        'tahun',
        'status',
    ];
}
