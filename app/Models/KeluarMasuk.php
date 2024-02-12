<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KeluarMasuk extends Model
{
    use HasFactory;
    protected $connection= 'absensi';
    protected $table = 'ijin_keluar_masuk';
}
