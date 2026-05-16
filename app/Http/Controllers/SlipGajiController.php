<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SlipGajiController extends Controller
{
    public function listSlipGajiKaryawan($nik)
    {
        $listFiles = \File::files(public_path('itic/pdf/slip/'.$nik));

        $allMedia = [];

        foreach ($listFiles as $path) {
            $files = pathinfo($path);
            // $allMedia[] = $files['basename'];
            $allMedia[] = [
                'nik' => $nik,
                'file' => asset('public/itic/pdf/slip/'.$nik.'/'.$files['basename']),
                'size' => filesize(public_path('itic/pdf/slip/'.$nik.'/'.$files['basename']))
            ];
        }

        return $allMedia;
    }
}
