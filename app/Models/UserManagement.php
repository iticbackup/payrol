<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserManagement extends Model
{
    use HasFactory, SoftDeletes;
    public $table = 'user_management';
    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $dates = ['deleted_at'];
    
    public $fillable = [
        'id',
        'user_id',
        'c',
        'r',
        'u',
        'd',
    ];

    public function user()
    {
        return $this->belongsTo(\App\Models\User::class, 'user_id');
    }

}
