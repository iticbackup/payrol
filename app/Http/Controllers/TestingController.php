<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use App\Models\TestingBorongan;
use App\Imports\TestingBoronganImport;
use App\Imports\TestingBoronganPackingMultipleImport;
use App\Exports\TestingTemplateBoronganLokalExport;
use App\Exports\TestingBoronganPackingMultipleExport;
use DateTime;
use DB;
use Excel;

class TestingController extends Controller
{
    public function testing()
    {
        $log_posisi = DB::connection('emp')->table('log_posisi')->where('nik','2103484')->first();
        // dd($log_posisi);
        $awal  = new DateTime($log_posisi->tanggal);
        $akhir = new DateTime(); // Waktu sekarang
        $diff  = $awal->diff($akhir);

        echo 'Selisih waktu: ';
        echo $diff->y . ' tahun, ';
        echo $diff->m . ' bulan, ';
        echo $diff->d . ' hari, ';
        echo $diff->h . ' jam, ';
        echo $diff->i . ' menit, ';
        echo $diff->s . ' detik, ';
    }

    public function test_import_excel_borongan()
    {
        return view('backend.testing.borongan');
    }

    public function test_import_excel_borongan_simpan(Request $request)
    {
        try {
            $file = $request->file('upload_file_pengerjaan_borongan');
            // Excel::import($import = new TestingBoronganPackingMultipleImport, $file);
            $kodePengerjaan = 'PB_2025_0027';
            $kodePayrol = 'PBL_2025_0027';
            // Excel::import($import = new TestingBoronganImport($kodePengerjaan), $file);
            Excel::import($import = new TestingBoronganPackingMultipleImport($kodePengerjaan,$kodePayrol), $file);
            // Menyimpan pesan sukses ke sesi
            // return 'Data berhasil diimpor! '.$import->getRowCount();
            return 'Data berhasil diimpor!';
            // Session::flash('success', 'Data berhasil diimpor!');
        } catch (\Exception $e) {
            // Menyimpan pesan error jika terjadi kesalahan
            return 'Gagal mengimpor data: '.$e->getMessage();
            // Session::flash('error', 'Gagal mengimpor data: ' . $e->getMessage());
        }

        return redirect()->back();
    }

    public function test_export_excel_borongan($kode_pengerjaan,$jenis_operator_detail_id,$jenis_operator_detail_pekerjaan_id)
    {

        if($jenis_operator_detail_id == 1){
            $kode_jenis_operator_detail = 'L';
            $namaFile = 'Packing';
            switch ($jenis_operator_detail_pekerjaan_id) {
                case '1':
                    $kategoriPekerjaan = 'Packing Lokal';
                    break;
                case '2':
                    $kategoriPekerjaan = 'Bandrol Lokal';
                    break;
                case '3':
                    $kategoriPekerjaan = 'Inner Lokal';
                    break;
                case '4':
                    $kategoriPekerjaan = 'Outer Lokal';
                    break;
                case '25':
                    $kategoriPekerjaan = 'Stempel Lokal';
                    break;
                default:
                    # code...
                    break;
            }
        }
        elseif($jenis_operator_detail_id == 2){
            $kode_jenis_operator_detail = 'E';
            $namaFile = 'Ekspor';
            switch ($jenis_operator_detail_pekerjaan_id) {
                case '5':
                    $kategoriPekerjaan = 'Packing Ekspor';
                    break;
                case '6':
                    $kategoriPekerjaan = 'Kemas Ekspor';
                    break;
                case '7':
                    $kategoriPekerjaan = 'Gagang Ekspor';
                    break;
                default:
                    # code...
                    break;
            }
        }
        elseif($jenis_operator_detail_id == 3){
            $kode_jenis_operator_detail = 'A';
            $namaFile = 'Ambri';
            switch ($jenis_operator_detail_pekerjaan_id) {
                case '8':
                    $kategoriPekerjaan = 'Isi Etiket';
                    break;
                case '9':
                    $kategoriPekerjaan = 'Las Tepi';
                    break;
                case '10':
                    $kategoriPekerjaan = 'Las Pojok';
                    break;
                case '11':
                    $kategoriPekerjaan = 'Isi Ambri';
                    break;
                default:
                    # code...
                    break;
            }
        }

        $kodePayrol = substr($kode_pengerjaan,0,2).$kode_jenis_operator_detail.'_'.substr($kode_pengerjaan,3);
        return Excel::download(new TestingBoronganPackingMultipleExport($kode_pengerjaan,$kodePayrol,$jenis_operator_detail_id,$jenis_operator_detail_pekerjaan_id), 'Template Borongan '.$namaFile.' - '.$kategoriPekerjaan.'.xlsx');
        // return Excel::download(new TestingTemplateBoronganLokalExport($kodePengerjaan), 'template_borongan_packing.xlsx');
    }
}
