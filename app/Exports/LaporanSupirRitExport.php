<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithDrawings;
use Maatwebsite\Excel\Concerns\Exportable;

use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;

use App\Models\NewDataPengerjaan;
use App\Models\PengerjaanRITWeekly;

class LaporanSupirRitExport implements 
FromView,
ShouldAutoSize,
WithDrawings
{
    /**
    * @return \Illuminate\Support\Collection
    */

    use Exportable;

    public function  __construct($kode_pengerjaan,$baris_akhir) {
        $this->kode_pengerjaan= $kode_pengerjaan;
        $this->baris_akhir= $baris_akhir;
    }

    public function view(): View
    {
        $data['kode_pengerjaan'] = $this->kode_pengerjaan;
        $data['new_data_pengerjaan'] = NewDataPengerjaan::where('kode_pengerjaan',$this->kode_pengerjaan)->first();
        $data['explode_tanggal_pengerjaans'] = explode('#',$data['new_data_pengerjaan']['tanggal']);
        $data['pengerjaan_supir_rits'] = PengerjaanRITWeekly::select([
                                                            'pengerjaan_supir_rit_weekly.id as id',
                                                            'pengerjaan_supir_rit_weekly.karyawan_supir_rit_id as karyawan_supir_rit_id',
                                                            'operator_supir_rit_karyawan.nik as nik',
                                                            'biodata_karyawan.nama as nama',
                                                            'pengerjaan_supir_rit_weekly.plus_1 as plus_1',
                                                            'pengerjaan_supir_rit_weekly.plus_2 as plus_2',
                                                            'pengerjaan_supir_rit_weekly.plus_3 as plus_3',
                                                            'pengerjaan_supir_rit_weekly.uang_makan as uang_makan',
                                                            'pengerjaan_supir_rit_weekly.tunjangan_kerja as tunjangan_kerja',
                                                            'pengerjaan_supir_rit_weekly.tunjangan_kehadiran as tunjangan_kehadiran',
                                                            'pengerjaan_supir_rit_weekly.lembur as lembur',
                                                            'pengerjaan_supir_rit_weekly.jht as jht',
                                                            'pengerjaan_supir_rit_weekly.bpjs_kesehatan as bpjs_kesehatan',
                                                            'pengerjaan_supir_rit_weekly.minus_1 as minus_1',
                                                            'pengerjaan_supir_rit_weekly.minus_2 as minus_2',
                                                            'pengerjaan_supir_rit_weekly.pensiun as pensiun',
                                                        ])
                                                        ->leftJoin('operator_supir_rit_karyawan','operator_supir_rit_karyawan.id','=','pengerjaan_supir_rit_weekly.karyawan_supir_rit_id')
                                                        ->leftJoin('itic_emp.biodata_karyawan','biodata_karyawan.nik','=','operator_supir_rit_karyawan.nik')
                                                        ->where('pengerjaan_supir_rit_weekly.kode_pengerjaan',$this->kode_pengerjaan)
                                                        ->orderBy('biodata_karyawan.nama','asc')
                                                        ->get();
        $baris_akhir = $data['pengerjaan_supir_rits']->count()+10;
        return view('backend.laporan.supir_rit.excel_laporan_supir_rit',$data);
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
