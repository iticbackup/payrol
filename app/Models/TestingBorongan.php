<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TestingBorongan extends Model
{
    use HasFactory, SoftDeletes;
    public $table = 'testing_borongan';
    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $dates = ['deleted_at'];
    
    public $guarded = [
    ];
}
