<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\FromView;

use App\Models\NewDataPengerjaan;
use App\Models\Pengerjaan;
use App\Models\PengerjaanWeekly;

class TestingTemplateBoronganLokalExport implements 
FromView,
ShouldAutoSize,
WithTitle
{
    /**
    * @return \Illuminate\Support\Collection
    */

    use Exportable;

    function __construct(
        $tanggal,
        $kodePengerjaan,
        $kodePayrol,
        $jenis_operator_detail_id,
        $jenis_operator_detail_pekerjaan_id
    )
    {
        $this->tanggal = $tanggal;
        $this->kode_pengerjaan = $kodePengerjaan;
        $this->kode_payrol = $kodePayrol;
        $this->jenis_operator_detail_id = $jenis_operator_detail_id;
        $this->jenis_operator_detail_pekerjaan_id = $jenis_operator_detail_pekerjaan_id;
    }

    public function title(): string
    {
        return $this->tanggal;
    }

    public function view(): View
    {
        $data['tanggal'] = $this->tanggal;
        $data['pengerjaanBorongans'] = PengerjaanWeekly::where('kode_payrol',$this->kode_payrol)
                                                                ->whereHas('operator_karyawan', function($query){
                                                                    $query->where('jenis_operator_id',1)
                                                                        ->where('jenis_operator_detail_id',$this->jenis_operator_detail_id)
                                                                        ->where('jenis_operator_detail_pekerjaan_id',$this->jenis_operator_detail_pekerjaan_id);
                                                                })
                                                                ->get();
        // dd($this->jenis_operator_detail_id);
        switch ($this->jenis_operator_detail_id) {
            case '1':
                $data['nama_file'] = 'Packing';
                switch ($this->jenis_operator_detail_pekerjaan_id) {
                    case '1':
                        $data['kategoriPekerjaan'] = 'Packing Lokal';
                        break;
                    case '2':
                        $data['kategoriPekerjaan'] = 'Bandrol Lokal';
                        break;
                    case '3':
                        $data['kategoriPekerjaan'] = 'Inner Lokal';
                        break;
                    case '4':
                        $data['kategoriPekerjaan'] = 'Outer Lokal';
                        break;
                    case '25':
                        $data['kategoriPekerjaan'] = 'Stempel Lokal';
                        break;
                    default:
                        # code...
                        break;
                }
                break;
            case '2':
                $data['nama_file'] = 'Ekspor';
                switch ($this->jenis_operator_detail_pekerjaan_id) {
                    case '5':
                        $data['kategoriPekerjaan'] = 'Packing Ekspor';
                        break;
                    case '6':
                        $data['kategoriPekerjaan'] = 'Kemas Ekspor';
                        break;
                    case '7':
                        $data['kategoriPekerjaan'] = 'Gagang Ekspor';
                        break;
                    default:
                        # code...
                        break;
                }
                break;
            case '3':
                $data['nama_file'] = 'Ambri';
                switch ($this->jenis_operator_detail_pekerjaan_id) {
                    case '8':
                        $data['kategoriPekerjaan'] = 'Isi Etiket';
                        break;
                    case '9':
                        $data['kategoriPekerjaan'] = 'Las Tepi';
                        break;
                    case '10':
                        $data['kategoriPekerjaan'] = 'Las Pojok';
                        break;
                    case '11':
                        $data['kategoriPekerjaan'] = 'Isi Ambri';
                        break;
                    default:
                        # code...
                        break;
                }
                break;
            
            default:
                # code...
                break;
        }
        return view('backend.testing.templateBoronganLokal',$data);
    }

}
