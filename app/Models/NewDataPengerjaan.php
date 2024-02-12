<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class NewDataPengerjaan extends Model
{
    use HasFactory, SoftDeletes;
    protected $primaryKey = 'id';
    public $table = 'new_data_pengerjaan';
    public $incrementing = false;
    protected $dates = ['deleted_at'];

    public $fillable = [
        'id',
        'kode_pengerjaan',
        'date',
        'tanggal',
        'akhir_bulan',
        'status',
    ];
}
