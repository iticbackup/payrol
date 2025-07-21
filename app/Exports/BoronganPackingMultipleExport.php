<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use App\Models\NewDataPengerjaan;
use \Carbon\Carbon;

class BoronganPackingMultipleExport implements 
WithMultipleSheets
{
    /**
    * @return \Illuminate\Support\Collection
    */
    // public function collection()
    // {
    //     //
    // }

    use Exportable;

    function __construct(
        $kodePengerjaan,
        $kodePayrol,
        $jenis_operator_detail_id,
        $jenis_operator_detail_pekerjaan_id
    )
    {
        $this->kode_pengerjaan = $kodePengerjaan;
        $this->kode_payrol = $kodePayrol;
        $this->jenis_operator_detail_id = $jenis_operator_detail_id;
        $this->jenis_operator_detail_pekerjaan_id = $jenis_operator_detail_pekerjaan_id;
    }

    public function sheets(): array
    {
        $kodePengerjaan = $this->kode_pengerjaan;
        $newDataPengerjaan = NewDataPengerjaan::where('kode_pengerjaan',$kodePengerjaan)->first();

        $export = [];
        foreach (array_filter(explode('#',$newDataPengerjaan->tanggal)) as $key => $value) {
            $tanggal = Carbon::create($value)->format('d-m-Y');
            $export[] = new TestingTemplateBoronganLokalExport(
                $tanggal,
                $kodePengerjaan,
                $this->kode_payrol,
                $this->jenis_operator_detail_id,
                $this->jenis_operator_detail_pekerjaan_id
            );
        }

        return $export;
    }

}
