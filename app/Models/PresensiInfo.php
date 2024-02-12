<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PresensiInfo extends Model
{
    use HasFactory;
    protected $connection= 'absensi';
    protected $table = 'att_presensi_info';
}
