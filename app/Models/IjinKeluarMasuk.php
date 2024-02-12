<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IjinKeluarMasuk extends Model
{
    use HasFactory;
    protected $connection= 'absensi';
    protected $table = 'ijin_keluar_masuk';
}
