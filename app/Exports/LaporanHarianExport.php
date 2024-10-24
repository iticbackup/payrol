<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithDrawings;

use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;

use App\Models\NewDataPengerjaan;
use App\Models\JenisOperatorDetailPengerjaan;
use App\Models\PengerjaanHarian;

class LaporanHarianExport implements 
FromView,
ShouldAutoSize,
WithDrawings
{
    /**
    * @return \Illuminate\Support\Collection
    */

    use Exportable;

    public function  __construct($id_jenis_pekerjaan,$id,$kode_pengerjaan,$baris_akhir) {
        $this->id_jenis_pekerjaan= $id_jenis_pekerjaan;
        $this->id= $id;
        $this->kode_pengerjaan= $kode_pengerjaan;
        $this->baris_akhir= $baris_akhir;
    }

    public function view(): View
    {
        $data['kode_id'] = $this->id;
        $data['kode_pengerjaan'] = $this->kode_pengerjaan;
        $data['new_data_pengerjaan'] = NewDataPengerjaan::where('kode_pengerjaan',$this->kode_pengerjaan)->first();
        $data['explode_tanggal_pengerjaans'] = explode('#',$data['new_data_pengerjaan']['tanggal']);
        $data['jenis_operator_detail_pengerjaan'] = JenisOperatorDetailPengerjaan::where('id',$this->id_jenis_pekerjaan)->first();
        $data['pengerjaan_harians'] = PengerjaanHarian::select(
                                                        [
                                                            'pengerjaan_harian.id as id',
                                                            'operator_harian_karyawan.nik as nik',
                                                            'biodata_karyawan.nama as nama',
                                                            'pengerjaan_harian.operator_harian_karyawan_id as operator_harian_karyawan_id',
                                                            'pengerjaan_harian.upah_dasar as upah_dasar',
                                                            'pengerjaan_harian.upah_dasar_weekly as upah_dasar_weekly',
                                                            'pengerjaan_harian.hari_kerja as hari_kerja',
                                                            'pengerjaan_harian.hasil_kerja as hasil_kerja',
                                                            'pengerjaan_harian.tunjangan_kerja as tunjangan_kerja',
                                                            'pengerjaan_harian.tunjangan_kehadiran as tunjangan_kehadiran',
                                                            'pengerjaan_harian.plus_1 as plus_1',
                                                            'pengerjaan_harian.plus_2 as plus_2',
                                                            'pengerjaan_harian.plus_3 as plus_3',
                                                            'pengerjaan_harian.minus_1 as minus_1',
                                                            'pengerjaan_harian.minus_2 as minus_2',
                                                            'pengerjaan_harian.uang_makan as uang_makan',
                                                            'pengerjaan_harian.lembur as lembur',
                                                            'pengerjaan_harian.jht as jht',
                                                            'pengerjaan_harian.bpjs_kesehatan as bpjs_kesehatan',
                                                            'pengerjaan_harian.pensiun as pensiun',
                                                        ]
                                                    )
                                                    ->leftJoin('operator_harian_karyawan','operator_harian_karyawan.id','=','pengerjaan_harian.operator_harian_karyawan_id')
                                                    ->leftJoin('itic_emp_new.biodata_karyawan','biodata_karyawan.nik','=','operator_harian_karyawan.nik')
                                                    ->where('pengerjaan_harian.kode_pengerjaan',$this->kode_pengerjaan)
                                                    ->where('operator_harian_karyawan.jenis_operator_detail_pekerjaan_id',$this->id_jenis_pekerjaan)
                                                    ->orderBy('biodata_karyawan.nama','asc')
                                                    ->get();
        return view('backend.laporan.harian.excel_laporan_harian',$data);
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
