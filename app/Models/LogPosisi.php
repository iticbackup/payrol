<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LogPosisi extends Model
{
    use HasFactory;
    protected $connection= 'emp';
    protected $table = 'log_posisi';
}
