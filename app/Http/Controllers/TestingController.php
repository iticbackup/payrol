<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DateTime;
use DB;

class TestingController extends Controller
{
    public function testing()
    {
        $log_posisi = DB::connection('emp')->table('log_posisi')->where('nik','2103484')->first();
        // dd($log_posisi);
        $awal  = new DateTime($log_posisi->tanggal);
        $akhir = new DateTime(); // Waktu sekarang
        $diff  = $awal->diff($akhir);

        echo 'Selisih waktu: ';
        echo $diff->y . ' tahun, ';
        echo $diff->m . ' bulan, ';
        echo $diff->d . ' hari, ';
        echo $diff->h . ' jam, ';
        echo $diff->i . ' menit, ';
        echo $diff->s . ' detik, ';
    }
}
