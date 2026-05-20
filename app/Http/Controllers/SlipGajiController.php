<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use \Carbon\Carbon;

class SlipGajiController extends Controller
{
    public function formatBytes($bytes, $precision = 2) {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];

        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);

        $bytes /= pow(1024, $pow);

        return round($bytes, $precision) . ' ' . $units[$pow];
    }

    public function listSlipGajiKaryawan($nik)
    {
        $listFiles = \File::files(public_path('itic/pdf/slip/'.$nik));

        $allMedia = [];

        foreach ($listFiles as $path) {
            $files = pathinfo($path);
            $filename = explode('/',asset('public/itic/pdf/slip/'.$nik.'/'.$files['basename']))[9];

            //Filesize
            $units = ['B', 'KB', 'MB', 'GB', 'TB'];

            $bytes = max(filesize(public_path('itic/pdf/slip/'.$nik.'/'.$files['basename'])), 0);
            $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
            $pow = min($pow, count($units) - 1);

            $bytes /= pow(1024, $pow);
            //endFilesize
            
            // return round($bytes, $precision) . ' ' . $units[$pow];
            // filesize(public_path('itic/pdf/slip/'.$nik.'/'.$files['basename']))
            $allMedia[] = [
                'nik' => $nik,
                'file_name' => explode('.',$filename)[0],
                'file' => asset('public/itic/pdf/slip/'.$nik.'/'.$files['basename']),
                'size' => round($bytes, 2) . ' ' . $units[$pow],
                'extension' => explode('.',$filename)[1],
                'modified_date' => date("d-m-Y H:i:s", filemtime($path))
            ];
        }

        return $allMedia;
    }
}
