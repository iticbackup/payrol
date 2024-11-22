<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Illuminate\Contracts\View\View; //Harus diimport untuk men-convert blade menjadi file excel
use Maatwebsite\Excel\Concerns\FromView; //Harus diimport untuk men-convert blade menjadi file excel
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithDrawings;
use Maatwebsite\Excel\Concerns\WithCustomCsvSettings;

class LPBoronganLokalExport implements 
FromView,
ShouldAutoSize,
WithDrawings,
WithCustomCsvSettings
{
    // /**
    // * @return \Illuminate\Support\Collection
    // */
    // public function collection()
    // {
    //     //
    // }
    use Exportable;

    private $sheets;

    public function __construct(

    ){
        $this->id= 1;
        $this->kode_pengerjaan= 'PB_2024_0046';
    }

    public function getCsvSettings(): array
    {
        return [
            'delimiter' => '.'
        ];
    }

    public function view(): View
    {
        if($this->id == 1){
            $kode_jenis_operator_detail = 'L';
        }
        elseif($this->id == 2){
            $kode_jenis_operator_detail = 'E';
        }
        elseif($this->id == 3){
            $kode_jenis_operator_detail = 'A';
        }

        $data['kode_id'] = $this->id;
        $data['kode_pengerjaan'] = $this->kode_pengerjaan;
        $data['new_data_pengerjaan'] = NewDataPengerjaan::where('kode_pengerjaan',$this->kode_pengerjaan)->first();
        $data['explode_tanggal_pengerjaans'] = explode('#',$data['new_data_pengerjaan']['tanggal']);
    }

    public function drawings()
    {
        $drawing = new Drawing();
        $drawing->setName('Logo');
        $drawing->setPath(public_path('itic/Tanda_Tangan_Payroll.jpg'));
        $drawing->setCoordinates('B'.$this->baris_akhir);
        $drawing->setHeight(140);
        return $drawing;
    }
}
