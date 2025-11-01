<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CutOff extends Model
{
    use HasFactory, SoftDeletes;
    public $table = 'cutoff';
    // public $incrementing = false;

    protected $dates = ['deleted_at'];

    protected $guarded = [
        
    ];
}
