<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BPJSKesehatan extends Model
{
    use HasFactory, SoftDeletes;
    public $table = 'bpjs_kesehatan';
    protected $dates = ['deleted_at'];
    public $incrementing = false;
    
    public $fillable = [
        'id',
        'keterangan',
        'nominal',
        'tahun',
        'status',
    ];
}
