<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MasterCase extends Model
{
    use HasFactory, SoftDeletes;
    
    public $table = 'master_case';
    // public $incrementing = false;

    protected $dates = ['deleted_at'];

    protected $guarded = [
        
    ];
}
