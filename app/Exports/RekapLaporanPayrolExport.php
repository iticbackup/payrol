<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Concerns\Exportable;
use App\Exports\Sheets\LpPerMonth;

use App\Models\JenisOperator;
use App\Models\JenisOperatorDetail;
use App\Models\JenisOperatorDetailPengerjaan;

class RekapLaporanPayrolExport implements WithMultipleSheets
{
    // /**
    // * @return \Illuminate\Support\Collection
    // */
    // public function collection()
    // {
    //     //
    // }

    use Exportable;

    protected $jenis_operator_id;

    public function __construct(
        int $jenis_operator_id
    ){
        $this->jenis_operator_id = $jenis_operator_id;
    }

    public function sheets(): array
    {
        $sheets = [];

        $jenis_operator = JenisOperator::find($this->jenis_operator_id);
        $jenis_operator_details = JenisOperatorDetail::where('jenis_operator_id',$jenis_operator->id)->get();
        foreach ($jenis_operator_details as $key_jenis_operator_detail => $jenis_operator_detail) {
            $jenis_operator_detail_pengerjaans = JenisOperatorDetailPengerjaan::where('jenis_operator_detail_id',$jenis_operator_detail->id)->get();
            foreach ($jenis_operator_detail_pengerjaans as $key_jenis_operator_detail_pengerjaan => $jenis_operator_detail_pengerjaan) {
                $sheets[] = new LpPerMonth($jenis_operator_detail->jenis_operator_detail_id);
            }
        }
        // for ($i=8; $i <= 11 ; $i++) { 
        //     $sheets[] = new LpPerMonth($this->year,$i);
        //     // $sheets[] = $i;
        // }

        return $sheets;
        // return [
        //     0 => 'Borongan Lokal',
        //     1 => 'Borongan Bandrol',
        //     // new LPBoronganLokalExport()
        //     // new RekapDataKaryawanAktifExcel($this->tanggal),
        //     // new RekapDataKaryawanNonAktifExcel($this->tanggal),
        // ];
    }
}
