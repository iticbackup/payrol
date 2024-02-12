<?php

namespace App\Exports;

// use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

use App\Models\PengerjaanWeekly;
use App\Models\JenisOperatorDetailPengerjaan;

class BoronganMultiSheets implements WithMultipleSheets
{
    // /**
    // * @return \Illuminate\Support\Collection
    // */
    // public function collection()
    // {
    //     //
    // }
    use Exportable;

    public function  __construct($id,$kode_pengerjaan) {
        $this->id= $id;
        $this->kode_pengerjaan= $kode_pengerjaan;
    }

    public function sheets(): array
    {
        $sheets = [];

        if($this->id == 1){
            $kode_jenis_operator_detail = 'L';
        }
        elseif($this->id == 2){
            $kode_jenis_operator_detail = 'E';
        }
        elseif($this->id == 3){
            $kode_jenis_operator_detail = 'A';
        }
        
        $kode_jenis_operator_detail_pekerjaans = JenisOperatorDetailPengerjaan::where('jenis_operator_detail_id',$this->id)->get();
        foreach ($kode_jenis_operator_detail_pekerjaans as $key => $kode_jenis_operator_detail_pekerjaan) {
            $sheets[] = new LaporanBoronganLokalExport($this->id,$this->kode_pengerjaan);
            // $sheets = [
            //     $data['pengerjaan_borongan_weeklys'],
            //     new LaporanBoronganLokalExport($this->id,$this->kode_pengerjaan,$kode_jenis_operator_detail_pekerjaan->jenis_posisi_pekerjaan)
            // ];
            // $sheets[] = new LaporanBoronganLokalExport($this->id,$this->kode_pengerjaan,$kode_jenis_operator_detail_pekerjaan->jenis_posisi_pekerjaan);
        }
        return $sheets;
    }
}
