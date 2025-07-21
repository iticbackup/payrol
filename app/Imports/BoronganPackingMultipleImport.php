<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithStartRow;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

use App\Models\NewDataPengerjaan;
use \Carbon\Carbon;

class BoronganPackingMultipleImport implements 
WithMultipleSheets
{
    /**
    * @param Collection $collection
    */
    // public function collection(Collection $collection)
    // {
    //     //
    // }

    function __construct(
        $kodePengerjaan,
        $kodePayrol
    ){
        $this->kode_pengerjaan = $kodePengerjaan;
        $this->kode_payrol = $kodePayrol;
    }

    public function sheets(): array
    {
        $kodePengerjaan = $this->kode_pengerjaan;
        $newDataPengerjaan = NewDataPengerjaan::where('kode_pengerjaan',$kodePengerjaan)->first();

        $import = [];
        foreach (array_filter(explode('#',$newDataPengerjaan->tanggal)) as $key => $value) {
            $tanggal = Carbon::create($value)->format('d-m-Y');
            $import[] = new BoronganImport($tanggal,$kodePengerjaan,$this->kode_payrol);
        }

        return $import;
    }
}
