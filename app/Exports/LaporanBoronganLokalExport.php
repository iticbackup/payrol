<?php

namespace App\Exports;

use App\Models\NewDataPengerjaan;
use App\Models\Pengerjaan;
use App\Models\PengerjaanWeekly;
use App\Models\JenisOperatorDetailPengerjaan;

// use Maatwebsite\Excel\Concerns\FromCollection;
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

use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;

// class LaporanBoronganLokalExport implements FromCollection
class LaporanBoronganLokalExport implements 
FromView,
ShouldAutoSize,
WithDrawings,
WithCustomCsvSettings
// WithTitle,
// FromQuery,
// WithMultipleSheets
{
    // /**
    // * @return \Illuminate\Support\Collection
    // */

    use Exportable;

    // private $titles;

    private $sheets;

    public function  __construct($id_jenis_pekerjaan,$id,$kode_pengerjaan,$baris_akhir) {
        $this->id_jenis_pekerjaan= $id_jenis_pekerjaan;
        $this->id= $id;
        $this->kode_pengerjaan= $kode_pengerjaan;
        $this->baris_akhir= $baris_akhir;
        // $this->jenis_posisi_pekerjaan = $jenis_posisi_pekerjaan;
    }

    public function getCsvSettings(): array
    {
        return [
            'delimiter' => '.'
        ];
    }

    public function view(): View
    {
        //export adalah file export.blade.php yang ada di folder views
        if($this->id == 1){
            $kode_jenis_operator_detail = 'L';
        }
        elseif($this->id == 2){
            $kode_jenis_operator_detail = 'E';
        }
        elseif($this->id == 3){
            $kode_jenis_operator_detail = 'A';
        }
        // dd(substr($kode_pengerjaan,0,2).$kode_jenis_operator_detail.'_'.substr($kode_pengerjaan,3));
        $data['kode_id'] = $this->id;
        $data['kode_pengerjaan'] = $this->kode_pengerjaan;
        $data['new_data_pengerjaan'] = NewDataPengerjaan::where('kode_pengerjaan',$this->kode_pengerjaan)->first();
        $data['explode_tanggal_pengerjaans'] = explode('#',$data['new_data_pengerjaan']['tanggal']);

        // $data['pengerjaan_borongan_weeklys'] = PengerjaanWeekly::select([
        //                                         'pengerjaan_weekly.kode_payrol as kode_payrol',
        //                                         'operator_karyawan.nik as nik',
        //                                         'biodata_karyawan.nama as nama',
        //                                         'pengerjaan_weekly.operator_karyawan_id as operator_karyawan_id',
        //                                     ])
        //                                     ->leftJoin('operator_karyawan','operator_karyawan.id','=','pengerjaan_weekly.operator_karyawan_id')
        //                                     ->leftJoin('biodata_karyawan','biodata_karyawan.nik','=','operator_karyawan.nik')
        //                                     ->where('pengerjaan_weekly.kode_payrol',substr($this->kode_pengerjaan,0,2).$kode_jenis_operator_detail.'_'.substr($this->kode_pengerjaan,3))
        //                                     // ->where('pengerjaan_weekly.kode_payrol',$kode_pengerjaan)
        //                                     ->orderBy('biodata_karyawan.nama','asc')
        //                                     ->get();
        $data['jenis_operator_detail_pengerjaan'] = JenisOperatorDetailPengerjaan::where('id',$this->id_jenis_pekerjaan)->first();
        $data['pengerjaan_borongan_weeklys'] = PengerjaanWeekly::select([
                                                'pengerjaan_weekly.kode_payrol as kode_payrol',
                                                'operator_karyawan.nik as nik',
                                                'biodata_karyawan.nama as nama',
                                                'pengerjaan_weekly.operator_karyawan_id as operator_karyawan_id',
                                                'pengerjaan_weekly.plus_1 as plus_1',
                                                'pengerjaan_weekly.plus_2 as plus_2',
                                                'pengerjaan_weekly.plus_3 as plus_3',
                                                'pengerjaan_weekly.uang_makan as uang_makan',
                                                'pengerjaan_weekly.tunjangan_kerja as tunjangan_kerja',
                                                'pengerjaan_weekly.tunjangan_kehadiran as tunjangan_kehadiran',
                                                'pengerjaan_weekly.minus_1 as minus_1',
                                                'pengerjaan_weekly.minus_2 as minus_2',
                                                'pengerjaan_weekly.jht as jht',
                                                'pengerjaan_weekly.bpjs_kesehatan as bpjs_kesehatan',
                                            ])
                                            ->leftJoin('operator_karyawan','operator_karyawan.id','=','pengerjaan_weekly.operator_karyawan_id')
                                            ->leftJoin('itic_emp_new.biodata_karyawan','biodata_karyawan.nik','=','operator_karyawan.nik')
                                            // ->where('pengerjaan_weekly.kode_payrol',substr($this->kode_pengerjaan,0,2).$kode_jenis_operator_detail.'_'.substr($this->kode_pengerjaan,3))
                                            // ->where('biodata_karyawan.status_karyawan','!=','R')
                                            ->where('pengerjaan_weekly.kode_pengerjaan',$this->kode_pengerjaan)
                                            ->where('operator_karyawan.jenis_operator_detail_pekerjaan_id',$this->id_jenis_pekerjaan)
                                            ->where('operator_karyawan.status','Y')
                                            ->orderBy('biodata_karyawan.nama','asc')
                                            ->get();
        // dd($data);
        // return $data;
        return view('backend.laporan.borongan.excel_laporan_borongan',$data);
        // return view('backend.laporan.borongan.excel_laporan_borongan', [
        //     //data adalah value yang akan kita gunakan pada blade nanti
        //     //User::all() mengambil seluruh data user dan disimpan pada variabel data
        //     // 'data' => Pengerjaan::where('kode_pengerjaan',$this->kode_pengerjaan)->get()
        // ]);
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

    // public function title(): string
    // {
    //     return $this->jenis_posisi_pekerjaan;
    // }
    

    // public function sheets(): array
    // {
    //     $kode_jenis_operator_detail_pekerjaans = JenisOperatorDetailPengerjaan::where('jenis_operator_detail_id',$this->id)->get();
    //     foreach ($kode_jenis_operator_detail_pekerjaans as $key => $kode_jenis_operator_detail_pekerjaan) {
    //         $sheets[] = $key+1;
    //     }
    //     return $sheets;
    // }

    // public function query(): array
    // {
    //     $kode_jenis_operator_detail_pekerjaans = JenisOperatorDetailPengerjaan::where('jenis_operator_detail_id',$this->id)->get();
    //     foreach ($kode_jenis_operator_detail_pekerjaans as $key => $kode_jenis_operator_detail_pekerjaan) {
    //         $datas[] = $key+1;
    //     }
    //     return $datas;
    // }

    // public function map($row): array
    // {
    //     return [
    //         $row['id']
    //     ];
    // }

    // public function collection()
    // {
    //     return Pengerjaan::where('kode_pengerjaan',$this->kode_pengerjaan)->get();
    // }
}
