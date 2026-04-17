<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ThrDetail extends Model
{
    use HasFactory, SoftDeletes;
    public $table = 'thr_detail';
    protected $dates = ['deleted_at'];
    
    public $guarded = [
    ];

    public function thr()
    {
        return $this->belongsTo(\App\Models\Thr::class, 'thr_id', 'id');
    }
}