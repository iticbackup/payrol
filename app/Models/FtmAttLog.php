<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FtmAttLog extends Model
{
    use HasFactory;
    protected $connection= 'ftm';
    protected $table = 'att_log';
}
