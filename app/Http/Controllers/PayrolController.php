<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;

use App\Models\NewDataPengerjaan;
use App\Models\BiodataKaryawan;
use App\Models\KaryawanOperator;
use App\Models\PengerjaanWeekly;
use App\Models\Pengerjaan;
use App\Models\PengerjaanHarian;
use App\Models\PengerjaanRITHarian;
use App\Models\PengerjaanRITWeekly;

use App\Models\RitPosisi;
use App\Models\RitUMK;
use App\Models\RitTujuan;

use App\Models\KirimGaji;

use App\Models\UMKBoronganLokal;
use App\Models\UMKBoronganEkspor;
use App\Models\UMKBoronganAmbri;
use App\Models\UMKBoronganStempel;
use App\Models\JenisOperatorDetailPengerjaan;
use \Carbon\Carbon;
use DataTables;

use Mail;
use Pdf;
use DB;
use Dompdf\Options;
use \Codedge\Fpdf\Fpdf\Fpdf;


class PayrolController extends Controller
{

    function __construct(
        NewDataPengerjaan $newDataPengerjaan,
        BiodataKaryawan $biodataKaryawan,
        KaryawanOperator $karyawanOperator,
        PengerjaanWeekly $pengerjaanWeekly,
        Pengerjaan $pengerjaan,
        PengerjaanHarian $pengerjaanHarian,
        PengerjaanRITHarian $pengerjaanRitHarian,
        PengerjaanRITWeekly $pengerjaanRitWeekly,
        RitPosisi $ritPosisi,
        RitUMK $ritUmk,
        RitTujuan $ritTujuan,
        UMKBoronganLokal $umkBoronganLokal,
        UMKBoronganEkspor $umkBoronganEkspor,
        UMKBoronganAmbri $umkBoronganAmbri,
        UMKBoronganStempel $umkBoronganStempel,
        JenisOperatorDetailPengerjaan $jenisOperatorDetailPengerjaan,
        KirimGaji $kirim_gaji
    ){
        $this->newDataPengerjaan = $newDataPengerjaan;
        $this->biodataKaryawan = $biodataKaryawan;
        $this->karyawanOperator = $karyawanOperator;
        $this->pengerjaanWeekly = $pengerjaanWeekly;
        $this->pengerjaan = $pengerjaan;
        $this->pengerjaanHarian = $pengerjaanHarian;
        $this->pengerjaanRitHarian = $pengerjaanRitHarian;
        $this->pengerjaanRitWeekly = $pengerjaanRitWeekly;
        $this->ritPosisi = $ritPosisi;
        $this->ritUmk = $ritUmk;
        $this->ritTujuan = $ritTujuan;
        $this->umkBoronganLokal = $umkBoronganLokal;
        $this->umkBoronganEkspor = $umkBoronganEkspor;
        $this->umkBoronganAmbri = $umkBoronganAmbri;
        $this->umkBoronganStempel = $umkBoronganStempel;
        $this->jenisOperatorDetailPengerjaan = $jenisOperatorDetailPengerjaan;
        $this->kirim_gaji = $kirim_gaji;

        $this->sendLimit = 50;
    }

    public function borongan(Request $request)
    {
        if ($request->ajax()) {
            $data = $this->newDataPengerjaan->where('kode_pengerjaan','LIKE','%PB%')->get();
            return DataTables::of($data)
                            ->addIndexColumn()
                            ->addColumn('tanggal_penggajian', function($row){
                                $explode_tanggal_pengerjaans = explode("#",$row->tanggal);
                                foreach ($explode_tanggal_pengerjaans as $key => $explode_tanggal_pengerjaan) {
                                    if ($key != 0) {
                                        $hasil_tanggal_pengerjaan[] = Carbon::parse($explode_tanggal_pengerjaan)->isoFormat('D MMMM');
                                    }
                                }
                                return $hasil_tanggal_pengerjaan;
                            })
                            ->addColumn('status', function($row){
                                if ($row->status == 'y') {
                                    return '<span class="badge badge-outline-primary">Progress</span>';
                                }else{
                                    return '<span class="badge badge-outline-success">Selesai</span>';
                                }
                                return '-';
                            })
                            ->addColumn('action', function($row){
                                $btn = '';
                                // $btn .= '<div class="button-items">';
                                // $btn .= '<button class="btn btn-success btn-icon-circle btn-icon-circle-md"><i class="far fa-file-pdf"></i></button>';
                                // $btn .= '</div>';
                                $btn.=  '<div class="btn-group" role="group">';
                                $btn.=      '<a href='.route('payrol.borongan.slip_gaji',['kode_pengerjaan' => $row->kode_pengerjaan]).' class="btn btn-success" target="_blank"><i class="far fa-file-pdf"></i> Slip Gaji<a>';
                                // $btn.=      '<button onclick="slip_gaji_view(`'.$row->kode_pengerjaan.'`)" class="btn btn-success" target="_blank"><i class="far fa-file-pdf"></i> Slip Gaji</button>';
                                // $btn.=      '<a href='.route('payrol.borongan.bank',['kode_pengerjaan' => $row->kode_pengerjaan]).' class="btn btn-primary" target="_blank"><i class="far fa-file-pdf"></i> Bank</a>';
                                // $btn.=      '<a href='.route('payrol.borongan.weekly_report',['kode_pengerjaan' => $row->kode_pengerjaan]).' class="btn btn-danger" target="_blank"><i class="fas fa-book"></i> Weekly Report</a>';
                                if ($row->status != 'y') {
                                    $btn.=      '<a href='.route('payrol.borongan.borongan_detail_kirim_slip_gaji',['kode_pengerjaan' => $row->kode_pengerjaan]).' class="btn btn-primary" target="_blank"><i class="fas fa-envelope"></i> Detail Slip Gaji</a>';
                                }
                                $btn.=  '</div>';
                                return $btn;
                            })
                            ->rawColumns(['action','status'])
                            ->make(true);
        }
        return view('backend.payrol.penggajian.borongan.index');
    }

    public function borongan_slip_gaji_view($kode_pengerjaan)
    {
        return response()->json([
            'success' => true,
            'link' => route('payrol.borongan.slip_gaji',['kode_pengerjaan' => $kode_pengerjaan])
        ]);
    }

    public function borongan_slip_gaji($kode_pengerjaan){
        $data['new_data_pengerjaan'] = $this->newDataPengerjaan->where('kode_pengerjaan',$kode_pengerjaan)->first();
        $explode_tanggal_pengerjaans = explode('#',$data['new_data_pengerjaan']['tanggal']);
        $exp_tanggals = array_filter($explode_tanggal_pengerjaans);
        $a = count($exp_tanggals);
        // dd($exp_tanggals);
        $exp_tgl_awal = explode('-', $exp_tanggals[1]);
        $exp_tgl_akhir = explode('-', $exp_tanggals[$a]);
        $jenis_operator_id = [];
        $jenis_pengerjaan_operator = $this->jenisOperatorDetailPengerjaan->where('id','<=',11)->orderBy('id','asc')->get();
        foreach ($jenis_pengerjaan_operator as $jpo) {
            $jenis_operator_id[] = $jpo->id;
        }
        $first_pengerjaan_weekly_id = $this->pengerjaanWeekly->select([
                                                        'pengerjaan_weekly.id as id',
                                                        'operator_karyawan.nik as nik',
                                                        'operator_karyawan.jenis_operator_detail_pekerjaan_id as jenis_operator_detail_pekerjaan_id'
                                                    ])
                                                    ->leftJoin('operator_karyawan','operator_karyawan.id','=','pengerjaan_weekly.operator_karyawan_id')
                                                    ->leftJoin('itic_emp_new.biodata_karyawan','biodata_karyawan.nik','=','operator_karyawan.nik')
                                                    ->whereIn('operator_karyawan.jenis_operator_detail_pekerjaan_id',$jenis_operator_id)
                                                    ->where('pengerjaan_weekly.kode_pengerjaan',$kode_pengerjaan)
                                                    // ->orderBy('operator_karyawan.jenis_operator_detail_pekerjaan_id','asc')
                                                    // ->orderBy('biodata_karyawan.nama','asc')
                                                    ->first();
        if ($first_pengerjaan_weekly_id->id %2 == 0) {
            $ganjil_genap = 'MOD(pengerjaan_weekly.id, 2) = 0';
            // $ganjil_genap = 'MOD(pengerjaan_weekly.id, 2) <> 0';
        }else{
            $ganjil_genap = 'MOD(pengerjaan_weekly.id, 2) <> 0';
            // $ganjil_genap = 'MOD(pengerjaan_weekly.id, 2) = 0';
        }

        $query_weeklys = $this->pengerjaanWeekly->select([
                        'pengerjaan_weekly.id as id',
                        'pengerjaan_weekly.kode_payrol as kode_payrol',
                        'pengerjaan_weekly.operator_karyawan_id as operator_karyawan_id',
                        'jenis_operator_detail_pekerjaan.jenis_posisi_pekerjaan as jenis_posisi_pekerjaan',
                        'operator_karyawan.nik as nik',
                        'biodata_karyawan.nama as nama',
                        'pengerjaan_weekly.upah_dasar as upah_dasar',
                        'pengerjaan_weekly.tunjangan_kerja as tunjangan_kerja',
                        'pengerjaan_weekly.tunjangan_kehadiran as tunjangan_kehadiran',
                        'pengerjaan_weekly.uang_makan as uang_makan',
                        'pengerjaan_weekly.plus_1 as plus_1',
                        'pengerjaan_weekly.plus_2 as plus_2',
                        'pengerjaan_weekly.plus_3 as plus_3',
                        'pengerjaan_weekly.minus_1 as minus_1',
                        'pengerjaan_weekly.minus_2 as minus_2',
                        'pengerjaan_weekly.jht as jht',
                        'pengerjaan_weekly.bpjs_kesehatan as bpjs_kesehatan',
                        'operator_karyawan.jenis_operator_detail_pekerjaan_id as jenis_operator_detail_pekerjaan_id'
                    ])
                    ->leftJoin('operator_karyawan','operator_karyawan.id','=','pengerjaan_weekly.operator_karyawan_id')
                    ->leftJoin('itic_emp_new.biodata_karyawan','biodata_karyawan.nik','=','operator_karyawan.nik')
                    ->leftJoin('jenis_operator_detail_pekerjaan','jenis_operator_detail_pekerjaan.id','=','operator_karyawan.jenis_operator_detail_pekerjaan_id')
                    ->whereRaw($ganjil_genap)
                    // ->where('pengerjaan_weekly.operator_karyawan_id',26)
                    ->whereIn('operator_karyawan.jenis_operator_detail_pekerjaan_id',$jenis_operator_id)
                    ->where('pengerjaan_weekly.kode_pengerjaan',$kode_pengerjaan)
                    ->orderBy('operator_karyawan.jenis_operator_detail_pekerjaan_id','asc')
                    ->orderBy('biodata_karyawan.nama','asc')
                    ->get();
        // dd(count($query_weeklys));
        $data_array_1 = [];
        $data_array_2 = [];
        $pdf = new Fpdf('P', 'mm', 'A4');
        $pdf->AddPage();
        $pdf->SetFillColor(153,153,153);
        $pdf->SetTextColor(255);
        $pdf->SetDrawColor(0,0,0);
        $pdf->SetLineWidth(.3);
        $pdf->SetFont('Arial','B',8);
        $pdf->SetFillColor(224,235,255);
        $pdf->SetTextColor(0);
        $pdf->SetFont('Arial','',8);
        $no=0;
        for ($i=0; $i < count($query_weeklys); $i++) { 
            $row1_nama = $this->biodataKaryawan->where('nik',$query_weeklys[$i]['nik'])->orderBy('nama','asc')->first();
            $row1_upah_lembur = [];
            $row1_pengerjaans = $this->pengerjaan->where('operator_karyawan_id',$query_weeklys[$i]['operator_karyawan_id'])
                                        ->where('kode_pengerjaan',$kode_pengerjaan)
                                        ->get();
                                        // dd($row1_pengerjaans);
            // $nos = 1;
            $row1_total_upah_hasil_kerja = [];
            $row1_total_lembur_kerja = [];
            
            foreach ($row1_pengerjaans as $key_row1_pengerjaan => $row1_pengerjaan) {
                // Borongan Packing
                if ($row1_pengerjaan->operator_karyawan->jenis_operator_detail_pekerjaan_id == 1) {
                    // Hasil Kerja 1
                    ${"row1_explode_hasil_kerja_1"} = explode("|",$row1_pengerjaan['hasil_kerja_1']);
                    ${"row1_umk_borongan_lokal_1"} = $this->umkBoronganLokal->select('id','jenis_produk','umk_packing','umk_bandrol','umk_inner','umk_outer')
                                                    ->where('id',${"row1_explode_hasil_kerja_1"}[0])
                                                    ->first();
                    if(empty(${"row1_umk_borongan_lokal_1"})){
                        ${"row1_jenis_produk_1"} = '-';
                        ${"row1_hasil_kerja_1"} = null;
                        ${"row1_data_explode_hasil_kerja_1"} = '-';
                        ${"row1_lembur_1"} = 1;
                        ${"row1_total_hasil_1"} = 0;
                    }else{
                        ${"row1_jenis_produk_1"} = ${"row1_umk_borongan_lokal_1"}['jenis_produk'];
                        ${"row1_hasil_kerja_1"} = ${"row1_explode_hasil_kerja_1"}[1]*${"row1_umk_borongan_lokal_1"}['umk_packing'];
                        ${"row1_data_explode_hasil_kerja_1"} = ${"row1_explode_hasil_kerja_1"}[1];
                        ${"row1_explode_lembur_1"} = explode("|",$row1_pengerjaan['lembur']);
                        ${"row1_explode_status_lembur_1"} = explode("-",${"row1_explode_lembur_1"}[1]);
                        if(${"row1_explode_status_lembur_1"}[1] == 'y'){
                            ${"row1_lembur_1"} = 1.5;
                        }else{
                            ${"row1_lembur_1"} = 1;
                        }
                    }
                    // End Hasil Kerja 1

                    // Hasil Kerja 2
                    ${"row1_explode_hasil_kerja_2"} = explode("|",$row1_pengerjaan['hasil_kerja_2']);
                    ${"row1_umk_borongan_lokal_2"} = $this->umkBoronganLokal->select('id','jenis_produk','umk_packing','umk_bandrol','umk_inner','umk_outer')
                                                    ->where('id',${"row1_explode_hasil_kerja_2"}[0])
                                                    ->first();
                    if(empty(${"row1_umk_borongan_lokal_2"})){
                        ${"row1_jenis_produk_2"} = '-';
                        ${"row1_hasil_kerja_2"} = null;
                        ${"row1_data_explode_hasil_kerja_2"} = '-';
                        ${"row1_lembur_2"} = 1;
                        ${"row1_total_hasil_2"} = 0;
                    }else{
                        ${"row1_jenis_produk_2"} = ${"row1_umk_borongan_lokal_2"}['jenis_produk'];
                        ${"row1_hasil_kerja_2"} = ${"row1_explode_hasil_kerja_2"}[1]*${"row1_umk_borongan_lokal_2"}['umk_packing'];
                        ${"row1_data_explode_hasil_kerja_2"} = ${"row1_explode_hasil_kerja_2"}[1];
                        ${"row1_explode_lembur_2"} = explode("|",$row1_pengerjaan['lembur']);
                        ${"row1_explode_status_lembur_2"} = explode("-",${"row1_explode_lembur_2"}[2]);
                        if(${"row1_explode_status_lembur_2"}[1] == 'y'){
                            ${"row1_lembur_2"} = 1.5;
                        }else{
                            ${"row1_lembur_2"} = 1;
                        }
                    }
                    // End Hasil Kerja 2
                    
                    // Hasil Kerja 3
                    ${"row1_explode_hasil_kerja_3"} = explode("|",$row1_pengerjaan['hasil_kerja_3']);
                    ${"row1_umk_borongan_lokal_3"} = $this->umkBoronganLokal->select('id','jenis_produk','umk_packing','umk_bandrol','umk_inner','umk_outer')
                                                    ->where('id',${"row1_explode_hasil_kerja_3"}[0])
                                                    ->first();
                    if(empty(${"row1_umk_borongan_lokal_3"})){
                        ${"row1_jenis_produk_3"} = '-';
                        ${"row1_hasil_kerja_3"} = null;
                        ${"row1_data_explode_hasil_kerja_3"} = '-';
                        ${"row1_lembur_3"} = 1;
                        ${"row1_total_hasil_3"} = 0;
                    }else{
                        ${"row1_jenis_produk_3"} = ${"row1_umk_borongan_lokal_3"}['jenis_produk'];
                        ${"row1_hasil_kerja_3"} = ${"row1_explode_hasil_kerja_3"}[1]*${"row1_umk_borongan_lokal_3"}['umk_packing'];
                        ${"row1_data_explode_hasil_kerja_3"} = ${"row1_explode_hasil_kerja_3"}[1];
                        ${"row1_explode_lembur_3"} = explode("|",$row1_pengerjaan['lembur']);
                        ${"row1_explode_status_lembur_3"} = explode("-",${"row1_explode_lembur_3"}[3]);
                        if(${"row1_explode_status_lembur_3"}[1] == 'y'){
                            ${"row1_lembur_3"} = 1.5;
                        }else{
                            ${"row1_lembur_3"} = 1;
                        }
                    }
                    // End Hasil Kerja 3

                    // Hasil Kerja 4
                    ${"row1_explode_hasil_kerja_4"} = explode("|",$row1_pengerjaan['hasil_kerja_4']);
                    ${"row1_umk_borongan_lokal_4"} = $this->umkBoronganLokal->select('id','jenis_produk','umk_packing','umk_bandrol','umk_inner','umk_outer')
                                                    ->where('id',${"row1_explode_hasil_kerja_4"}[0])
                                                    ->first();
                    if(empty(${"row1_umk_borongan_lokal_4"})){
                        ${"row1_jenis_produk_4"} = '-';
                        ${"row1_hasil_kerja_4"} = null;
                        ${"row1_data_explode_hasil_kerja_4"} = '-';
                        ${"row1_lembur_4"} = 1;
                        ${"row1_total_hasil_4"} = 0;
                    }else{
                        ${"row1_jenis_produk_4"} = ${"row1_umk_borongan_lokal_4"}['jenis_produk'];
                        ${"row1_hasil_kerja_4"} = ${"row1_explode_hasil_kerja_4"}[1]*${"row1_umk_borongan_lokal_4"}['umk_packing'];
                        ${"row1_data_explode_hasil_kerja_4"} = ${"row1_explode_hasil_kerja_4"}[1];
                        ${"row1_explode_lembur_4"} = explode("|",$row1_pengerjaan['lembur']);
                        ${"row1_explode_status_lembur_4"} = explode("-",${"row1_explode_lembur_4"}[4]);
                        if(${"row1_explode_status_lembur_4"}[1] == 'y'){
                            ${"row1_lembur_4"} = 1.5;
                        }else{
                            ${"row1_lembur_4"} = 1;
                        }
                    }
                    // End Hasil Kerja 4

                    // Hasil Kerja 5
                    ${"row1_explode_hasil_kerja_5"} = explode("|",$row1_pengerjaan['hasil_kerja_5']);
                    ${"row1_umk_borongan_lokal_5"} = $this->umkBoronganLokal->select('id','jenis_produk','umk_packing','umk_bandrol','umk_inner','umk_outer')
                                                    ->where('id',${"row1_explode_hasil_kerja_5"}[0])
                                                    ->first();
                    if(empty(${"row1_umk_borongan_lokal_5"})){
                        ${"row1_jenis_produk_5"} = '-';
                        ${"row1_hasil_kerja_5"} = null;
                        ${"row1_data_explode_hasil_kerja_5"} = '-';
                        ${"row1_lembur_5"} = 1;
                        ${"row1_total_hasil_5"} = 0;
                    }else{
                        ${"row1_jenis_produk_5"} = ${"row1_umk_borongan_lokal_5"}['jenis_produk'];
                        ${"row1_hasil_kerja_5"} = ${"row1_explode_hasil_kerja_5"}[1]*${"row1_umk_borongan_lokal_5"}['umk_packing'];
                        ${"row1_data_explode_hasil_kerja_5"} = ${"row1_explode_hasil_kerja_5"}[1];
                        ${"row1_explode_lembur_5"} = explode("|",$row1_pengerjaan['lembur']);
                        ${"row1_explode_status_lembur_5"} = explode("-",${"row1_explode_lembur_5"}[5]);
                        if(${"row1_explode_status_lembur_5"}[1] == 'y'){
                            ${"row1_lembur_5"} = 1.5;
                        }else{
                            ${"row1_lembur_5"} = 1;
                        }
                    }
                    // End Hasil Kerja 5
                }

                if ($row1_pengerjaan->operator_karyawan->jenis_operator_detail_pekerjaan_id == 2) {
                    // Hasil Kerja 1
                    ${"row1_explode_hasil_kerja_1"} = explode("|",$row1_pengerjaan['hasil_kerja_1']);
                    ${"row1_umk_borongan_lokal_1"} = $this->umkBoronganLokal->select('id','jenis_produk','umk_packing','umk_bandrol','umk_inner','umk_outer')
                                                    ->where('id',${"row1_explode_hasil_kerja_1"}[0])
                                                    ->first();
                    if(empty(${"row1_umk_borongan_lokal_1"})){
                        ${"row1_jenis_produk_1"} = '-';
                        ${"row1_hasil_kerja_1"} = null;
                        ${"row1_data_explode_hasil_kerja_1"} = '-';
                        ${"row1_lembur_1"} = 1;
                        ${"row1_total_hasil_1"} = 0;
                    }else{
                        ${"row1_jenis_produk_1"} = ${"row1_umk_borongan_lokal_1"}['jenis_produk'];
                        ${"row1_hasil_kerja_1"} = ${"row1_explode_hasil_kerja_1"}[1]*${"row1_umk_borongan_lokal_1"}['umk_bandrol'];
                        ${"row1_data_explode_hasil_kerja_1"} = ${"row1_explode_hasil_kerja_1"}[1];
                        ${"row1_explode_lembur_1"} = explode("|",$row1_pengerjaan['lembur']);
                        ${"row1_explode_status_lembur_1"} = explode("-",${"row1_explode_lembur_1"}[1]);
                        if(${"row1_explode_status_lembur_1"}[1] == 'y'){
                            ${"row1_lembur_1"} = 1.5;
                        }else{
                            ${"row1_lembur_1"} = 1;
                        }
                    }
                    // End Hasil Kerja 1

                    // Hasil Kerja 2
                    ${"row1_explode_hasil_kerja_2"} = explode("|",$row1_pengerjaan['hasil_kerja_2']);
                    ${"row1_umk_borongan_lokal_2"} = $this->umkBoronganLokal->select('id','jenis_produk','umk_packing','umk_bandrol','umk_inner','umk_outer')
                                                    ->where('id',${"row1_explode_hasil_kerja_2"}[0])
                                                    ->first();
                    if(empty(${"row1_umk_borongan_lokal_2"})){
                        ${"row1_jenis_produk_2"} = '-';
                        ${"row1_hasil_kerja_2"} = null;
                        ${"row1_data_explode_hasil_kerja_2"} = '-';
                        ${"row1_lembur_2"} = 1;
                        ${"row1_total_hasil_2"} = 0;
                    }else{
                        ${"row1_jenis_produk_2"} = ${"row1_umk_borongan_lokal_2"}['jenis_produk'];
                        ${"row1_hasil_kerja_2"} = ${"row1_explode_hasil_kerja_2"}[1]*${"row1_umk_borongan_lokal_2"}['umk_bandrol'];
                        ${"row1_data_explode_hasil_kerja_2"} = ${"row1_explode_hasil_kerja_2"}[1];
                        ${"row1_explode_lembur_2"} = explode("|",$row1_pengerjaan['lembur']);
                        ${"row1_explode_status_lembur_2"} = explode("-",${"row1_explode_lembur_2"}[2]);
                        if(${"row1_explode_status_lembur_2"}[1] == 'y'){
                            ${"row1_lembur_2"} = 1.5;
                        }else{
                            ${"row1_lembur_2"} = 1;
                        }
                    }
                    // End Hasil Kerja 2
                    
                    // Hasil Kerja 3
                    ${"row1_explode_hasil_kerja_3"} = explode("|",$row1_pengerjaan['hasil_kerja_3']);
                    ${"row1_umk_borongan_lokal_3"} = $this->umkBoronganLokal->select('id','jenis_produk','umk_packing','umk_bandrol','umk_inner','umk_outer')
                                                    ->where('id',${"row1_explode_hasil_kerja_3"}[0])
                                                    ->first();
                    if(empty(${"row1_umk_borongan_lokal_3"})){
                        ${"row1_jenis_produk_3"} = '-';
                        ${"row1_hasil_kerja_3"} = null;
                        ${"row1_data_explode_hasil_kerja_3"} = '-';
                        ${"row1_lembur_3"} = 1;
                        ${"row1_total_hasil_3"} = 0;
                    }else{
                        ${"row1_jenis_produk_3"} = ${"row1_umk_borongan_lokal_3"}['jenis_produk'];
                        ${"row1_hasil_kerja_3"} = ${"row1_explode_hasil_kerja_3"}[1]*${"row1_umk_borongan_lokal_3"}['umk_bandrol'];
                        ${"row1_data_explode_hasil_kerja_3"} = ${"row1_explode_hasil_kerja_3"}[1];
                        ${"row1_explode_lembur_3"} = explode("|",$row1_pengerjaan['lembur']);
                        ${"row1_explode_status_lembur_3"} = explode("-",${"row1_explode_lembur_3"}[3]);
                        if(${"row1_explode_status_lembur_3"}[1] == 'y'){
                            ${"row1_lembur_3"} = 1.5;
                        }else{
                            ${"row1_lembur_3"} = 1;
                        }
                    }
                    // End Hasil Kerja 3

                    // Hasil Kerja 4
                    ${"row1_explode_hasil_kerja_4"} = explode("|",$row1_pengerjaan['hasil_kerja_4']);
                    ${"row1_umk_borongan_lokal_4"} = $this->umkBoronganLokal->select('id','jenis_produk','umk_packing','umk_bandrol','umk_inner','umk_outer')
                                                    ->where('id',${"row1_explode_hasil_kerja_4"}[0])
                                                    ->first();
                    if(empty(${"row1_umk_borongan_lokal_4"})){
                        ${"row1_jenis_produk_4"} = '-';
                        ${"row1_hasil_kerja_4"} = null;
                        ${"row1_data_explode_hasil_kerja_4"} = '-';
                        ${"row1_lembur_4"} = 1;
                        ${"row1_total_hasil_4"} = 0;
                    }else{
                        ${"row1_jenis_produk_4"} = ${"row1_umk_borongan_lokal_4"}['jenis_produk'];
                        ${"row1_hasil_kerja_4"} = ${"row1_explode_hasil_kerja_4"}[1]*${"row1_umk_borongan_lokal_4"}['umk_bandrol'];
                        ${"row1_data_explode_hasil_kerja_4"} = ${"row1_explode_hasil_kerja_4"}[1];
                        ${"row1_explode_lembur_4"} = explode("|",$row1_pengerjaan['lembur']);
                        ${"row1_explode_status_lembur_4"} = explode("-",${"row1_explode_lembur_4"}[4]);
                        if(${"row1_explode_status_lembur_4"}[1] == 'y'){
                            ${"row1_lembur_4"} = 1.5;
                        }else{
                            ${"row1_lembur_4"} = 1;
                        }
                    }
                    // End Hasil Kerja 4

                    // Hasil Kerja 5
                    ${"row1_explode_hasil_kerja_5"} = explode("|",$row1_pengerjaan['hasil_kerja_5']);
                    ${"row1_umk_borongan_lokal_5"} = $this->umkBoronganLokal->select('id','jenis_produk','umk_packing','umk_bandrol','umk_inner','umk_outer')
                                                    ->where('id',${"row1_explode_hasil_kerja_5"}[0])
                                                    ->first();
                    if(empty(${"row1_umk_borongan_lokal_5"})){
                        ${"row1_jenis_produk_5"} = '-';
                        ${"row1_hasil_kerja_5"} = null;
                        ${"row1_data_explode_hasil_kerja_5"} = '-';
                        ${"row1_lembur_5"} = 1;
                        ${"row1_total_hasil_5"} = 0;
                    }else{
                        ${"row1_jenis_produk_5"} = ${"row1_umk_borongan_lokal_5"}['jenis_produk'];
                        ${"row1_hasil_kerja_5"} = ${"row1_explode_hasil_kerja_5"}[1]*${"row1_umk_borongan_lokal_5"}['umk_bandrol'];
                        ${"row1_data_explode_hasil_kerja_5"} = ${"row1_explode_hasil_kerja_5"}[1];
                        ${"row1_explode_lembur_5"} = explode("|",$row1_pengerjaan['lembur']);
                        ${"row1_explode_status_lembur_5"} = explode("-",${"row1_explode_lembur_5"}[5]);
                        if(${"row1_explode_status_lembur_5"}[1] == 'y'){
                            ${"row1_lembur_5"} = 1.5;
                        }else{
                            ${"row1_lembur_5"} = 1;
                        }
                    }
                    // End Hasil Kerja 5
                }

                if ($row1_pengerjaan->operator_karyawan->jenis_operator_detail_pekerjaan_id == 3) {
                    // Hasil Kerja 1
                    ${"row1_explode_hasil_kerja_1"} = explode("|",$row1_pengerjaan['hasil_kerja_1']);
                    ${"row1_umk_borongan_lokal_1"} = $this->umkBoronganLokal->select('id','jenis_produk','umk_packing','umk_bandrol','umk_inner','umk_outer')
                                                    ->where('id',${"row1_explode_hasil_kerja_1"}[0])
                                                    ->first();
                    if(empty(${"row1_umk_borongan_lokal_1"})){
                        ${"row1_jenis_produk_1"} = '-';
                        ${"row1_hasil_kerja_1"} = null;
                        ${"row1_data_explode_hasil_kerja_1"} = '-';
                        ${"row1_lembur_1"} = 1;
                        ${"row1_total_hasil_1"} = 0;
                    }else{
                        ${"row1_jenis_produk_1"} = ${"row1_umk_borongan_lokal_1"}['jenis_produk'];
                        ${"row1_hasil_kerja_1"} = ${"row1_explode_hasil_kerja_1"}[1]*${"row1_umk_borongan_lokal_1"}['umk_inner'];
                        ${"row1_data_explode_hasil_kerja_1"} = ${"row1_explode_hasil_kerja_1"}[1];
                        ${"row1_explode_lembur_1"} = explode("|",$row1_pengerjaan['lembur']);
                        ${"row1_explode_status_lembur_1"} = explode("-",${"row1_explode_lembur_1"}[1]);
                        if(${"row1_explode_status_lembur_1"}[1] == 'y'){
                            ${"row1_lembur_1"} = 1.5;
                        }else{
                            ${"row1_lembur_1"} = 1;
                        }
                    }
                    // End Hasil Kerja 1

                    // Hasil Kerja 2
                    ${"row1_explode_hasil_kerja_2"} = explode("|",$row1_pengerjaan['hasil_kerja_2']);
                    ${"row1_umk_borongan_lokal_2"} = $this->umkBoronganLokal->select('id','jenis_produk','umk_packing','umk_bandrol','umk_inner','umk_outer')
                                                    ->where('id',${"row1_explode_hasil_kerja_2"}[0])
                                                    ->first();
                    if(empty(${"row1_umk_borongan_lokal_2"})){
                        ${"row1_jenis_produk_2"} = '-';
                        ${"row1_hasil_kerja_2"} = null;
                        ${"row1_data_explode_hasil_kerja_2"} = '-';
                        ${"row1_lembur_2"} = 1;
                        ${"row1_total_hasil_2"} = 0;
                    }else{
                        ${"row1_jenis_produk_2"} = ${"row1_umk_borongan_lokal_2"}['jenis_produk'];
                        ${"row1_hasil_kerja_2"} = ${"row1_explode_hasil_kerja_2"}[1]*${"row1_umk_borongan_lokal_2"}['umk_inner'];
                        ${"row1_data_explode_hasil_kerja_2"} = ${"row1_explode_hasil_kerja_2"}[1];
                        ${"row1_explode_lembur_2"} = explode("|",$row1_pengerjaan['lembur']);
                        ${"row1_explode_status_lembur_2"} = explode("-",${"row1_explode_lembur_2"}[2]);
                        if(${"row1_explode_status_lembur_2"}[1] == 'y'){
                            ${"row1_lembur_2"} = 1.5;
                        }else{
                            ${"row1_lembur_2"} = 1;
                        }
                    }
                    // End Hasil Kerja 2
                    
                    // Hasil Kerja 3
                    ${"row1_explode_hasil_kerja_3"} = explode("|",$row1_pengerjaan['hasil_kerja_3']);
                    ${"row1_umk_borongan_lokal_3"} = $this->umkBoronganLokal->select('id','jenis_produk','umk_packing','umk_bandrol','umk_inner','umk_outer')
                                                    ->where('id',${"row1_explode_hasil_kerja_3"}[0])
                                                    ->first();
                    if(empty(${"row1_umk_borongan_lokal_3"})){
                        ${"row1_jenis_produk_3"} = '-';
                        ${"row1_hasil_kerja_3"} = null;
                        ${"row1_data_explode_hasil_kerja_3"} = '-';
                        ${"row1_lembur_3"} = 1;
                        ${"row1_total_hasil_3"} = 0;
                    }else{
                        ${"row1_jenis_produk_3"} = ${"row1_umk_borongan_lokal_3"}['jenis_produk'];
                        ${"row1_hasil_kerja_3"} = ${"row1_explode_hasil_kerja_3"}[1]*${"row1_umk_borongan_lokal_3"}['umk_inner'];
                        ${"row1_data_explode_hasil_kerja_3"} = ${"row1_explode_hasil_kerja_3"}[1];
                        ${"row1_explode_lembur_3"} = explode("|",$row1_pengerjaan['lembur']);
                        ${"row1_explode_status_lembur_3"} = explode("-",${"row1_explode_lembur_3"}[3]);
                        if(${"row1_explode_status_lembur_3"}[1] == 'y'){
                            ${"row1_lembur_3"} = 1.5;
                        }else{
                            ${"row1_lembur_3"} = 1;
                        }
                    }
                    // End Hasil Kerja 3

                    // Hasil Kerja 4
                    ${"row1_explode_hasil_kerja_4"} = explode("|",$row1_pengerjaan['hasil_kerja_4']);
                    ${"row1_umk_borongan_lokal_4"} = $this->umkBoronganLokal->select('id','jenis_produk','umk_packing','umk_bandrol','umk_inner','umk_outer')
                                                    ->where('id',${"row1_explode_hasil_kerja_4"}[0])
                                                    ->first();
                    if(empty(${"row1_umk_borongan_lokal_4"})){
                        ${"row1_jenis_produk_4"} = '-';
                        ${"row1_hasil_kerja_4"} = null;
                        ${"row1_data_explode_hasil_kerja_4"} = '-';
                        ${"row1_lembur_4"} = 1;
                        ${"row1_total_hasil_4"} = 0;
                    }else{
                        ${"row1_jenis_produk_4"} = ${"row1_umk_borongan_lokal_4"}['jenis_produk'];
                        ${"row1_hasil_kerja_4"} = ${"row1_explode_hasil_kerja_4"}[1]*${"row1_umk_borongan_lokal_4"}['umk_inner'];
                        ${"row1_data_explode_hasil_kerja_4"} = ${"row1_explode_hasil_kerja_4"}[1];
                        ${"row1_explode_lembur_4"} = explode("|",$row1_pengerjaan['lembur']);
                        ${"row1_explode_status_lembur_4"} = explode("-",${"row1_explode_lembur_4"}[4]);
                        if(${"row1_explode_status_lembur_4"}[1] == 'y'){
                            ${"row1_lembur_4"} = 1.5;
                        }else{
                            ${"row1_lembur_4"} = 1;
                        }
                    }
                    // End Hasil Kerja 4

                    // Hasil Kerja 5
                    ${"row1_explode_hasil_kerja_5"} = explode("|",$row1_pengerjaan['hasil_kerja_5']);
                    ${"row1_umk_borongan_lokal_5"} = $this->umkBoronganLokal->select('id','jenis_produk','umk_packing','umk_bandrol','umk_inner','umk_outer')
                                                    ->where('id',${"row1_explode_hasil_kerja_5"}[0])
                                                    ->first();
                    if(empty(${"row1_umk_borongan_lokal_5"})){
                        ${"row1_jenis_produk_5"} = '-';
                        ${"row1_hasil_kerja_5"} = null;
                        ${"row1_data_explode_hasil_kerja_5"} = '-';
                        ${"row1_lembur_5"} = 1;
                        ${"row1_total_hasil_5"} = 0;
                    }else{
                        ${"row1_jenis_produk_5"} = ${"row1_umk_borongan_lokal_5"}['jenis_produk'];
                        ${"row1_hasil_kerja_5"} = ${"row1_explode_hasil_kerja_5"}[1]*${"row1_umk_borongan_lokal_5"}['umk_inner'];
                        ${"row1_data_explode_hasil_kerja_5"} = ${"row1_explode_hasil_kerja_5"}[1];
                        ${"row1_explode_lembur_5"} = explode("|",$row1_pengerjaan['lembur']);
                        ${"row1_explode_status_lembur_5"} = explode("-",${"row1_explode_lembur_5"}[5]);
                        if(${"row1_explode_status_lembur_5"}[1] == 'y'){
                            ${"row1_lembur_5"} = 1.5;
                        }else{
                            ${"row1_lembur_5"} = 1;
                        }
                    }
                    // End Hasil Kerja 5
                }

                if ($row1_pengerjaan->operator_karyawan->jenis_operator_detail_pekerjaan_id == 4) {
                    // Hasil Kerja 1
                    ${"row1_explode_hasil_kerja_1"} = explode("|",$row1_pengerjaan['hasil_kerja_1']);
                    ${"row1_umk_borongan_lokal_1"} = $this->umkBoronganLokal->select('id','jenis_produk','umk_packing','umk_bandrol','umk_inner','umk_outer')
                                                    ->where('id',${"row1_explode_hasil_kerja_1"}[0])
                                                    ->first();
                    if(empty(${"row1_umk_borongan_lokal_1"})){
                        ${"row1_jenis_produk_1"} = '-';
                        ${"row1_hasil_kerja_1"} = null;
                        ${"row1_data_explode_hasil_kerja_1"} = '-';
                        ${"row1_lembur_1"} = 1;
                        ${"row1_total_hasil_1"} = 0;
                    }else{
                        ${"row1_jenis_produk_1"} = ${"row1_umk_borongan_lokal_1"}['jenis_produk'];
                        ${"row1_hasil_kerja_1"} = ${"row1_explode_hasil_kerja_1"}[1]*${"row1_umk_borongan_lokal_1"}['umk_outer'];
                        ${"row1_data_explode_hasil_kerja_1"} = ${"row1_explode_hasil_kerja_1"}[1];
                        ${"row1_explode_lembur_1"} = explode("|",$row1_pengerjaan['lembur']);
                        ${"row1_explode_status_lembur_1"} = explode("-",${"row1_explode_lembur_1"}[1]);
                        if(${"row1_explode_status_lembur_1"}[1] == 'y'){
                            ${"row1_lembur_1"} = 1.5;
                        }else{
                            ${"row1_lembur_1"} = 1;
                        }
                    }
                    // End Hasil Kerja 1

                    // Hasil Kerja 2
                    ${"row1_explode_hasil_kerja_2"} = explode("|",$row1_pengerjaan['hasil_kerja_2']);
                    ${"row1_umk_borongan_lokal_2"} = $this->umkBoronganLokal->select('id','jenis_produk','umk_packing','umk_bandrol','umk_inner','umk_outer')
                                                    ->where('id',${"row1_explode_hasil_kerja_2"}[0])
                                                    ->first();
                    if(empty(${"row1_umk_borongan_lokal_2"})){
                        ${"row1_jenis_produk_2"} = '-';
                        ${"row1_hasil_kerja_2"} = null;
                        ${"row1_data_explode_hasil_kerja_2"} = '-';
                        ${"row1_lembur_2"} = 1;
                        ${"row1_total_hasil_2"} = 0;
                    }else{
                        ${"row1_jenis_produk_2"} = ${"row1_umk_borongan_lokal_2"}['jenis_produk'];
                        ${"row1_hasil_kerja_2"} = ${"row1_explode_hasil_kerja_2"}[1]*${"row1_umk_borongan_lokal_2"}['umk_outer'];
                        ${"row1_data_explode_hasil_kerja_2"} = ${"row1_explode_hasil_kerja_2"}[1];
                        ${"row1_explode_lembur_2"} = explode("|",$row1_pengerjaan['lembur']);
                        ${"row1_explode_status_lembur_2"} = explode("-",${"row1_explode_lembur_2"}[2]);
                        if(${"row1_explode_status_lembur_2"}[1] == 'y'){
                            ${"row1_lembur_2"} = 1.5;
                        }else{
                            ${"row1_lembur_2"} = 1;
                        }
                    }
                    // End Hasil Kerja 2
                    
                    // Hasil Kerja 3
                    ${"row1_explode_hasil_kerja_3"} = explode("|",$row1_pengerjaan['hasil_kerja_3']);
                    ${"row1_umk_borongan_lokal_3"} = $this->umkBoronganLokal->select('id','jenis_produk','umk_packing','umk_bandrol','umk_inner','umk_outer')
                                                    ->where('id',${"row1_explode_hasil_kerja_3"}[0])
                                                    ->first();
                    if(empty(${"row1_umk_borongan_lokal_3"})){
                        ${"row1_jenis_produk_3"} = '-';
                        ${"row1_hasil_kerja_3"} = null;
                        ${"row1_data_explode_hasil_kerja_3"} = '-';
                        ${"row1_lembur_3"} = 1;
                        ${"row1_total_hasil_3"} = 0;
                    }else{
                        ${"row1_jenis_produk_3"} = ${"row1_umk_borongan_lokal_3"}['jenis_produk'];
                        ${"row1_hasil_kerja_3"} = ${"row1_explode_hasil_kerja_3"}[1]*${"row1_umk_borongan_lokal_3"}['umk_outer'];
                        ${"row1_data_explode_hasil_kerja_3"} = ${"row1_explode_hasil_kerja_3"}[1];
                        ${"row1_explode_lembur_3"} = explode("|",$row1_pengerjaan['lembur']);
                        ${"row1_explode_status_lembur_3"} = explode("-",${"row1_explode_lembur_3"}[3]);
                        if(${"row1_explode_status_lembur_3"}[1] == 'y'){
                            ${"row1_lembur_3"} = 1.5;
                        }else{
                            ${"row1_lembur_3"} = 1;
                        }
                    }
                    // End Hasil Kerja 3

                    // Hasil Kerja 4
                    ${"row1_explode_hasil_kerja_4"} = explode("|",$row1_pengerjaan['hasil_kerja_4']);
                    ${"row1_umk_borongan_lokal_4"} = $this->umkBoronganLokal->select('id','jenis_produk','umk_packing','umk_bandrol','umk_inner','umk_outer')
                                                    ->where('id',${"row1_explode_hasil_kerja_4"}[0])
                                                    ->first();
                    if(empty(${"row1_umk_borongan_lokal_4"})){
                        ${"row1_jenis_produk_4"} = '-';
                        ${"row1_hasil_kerja_4"} = null;
                        ${"row1_data_explode_hasil_kerja_4"} = '-';
                        ${"row1_lembur_4"} = 1;
                        ${"row1_total_hasil_4"} = 0;
                    }else{
                        ${"row1_jenis_produk_4"} = ${"row1_umk_borongan_lokal_4"}['jenis_produk'];
                        ${"row1_hasil_kerja_4"} = ${"row1_explode_hasil_kerja_4"}[1]*${"row1_umk_borongan_lokal_4"}['umk_outer'];
                        ${"row1_data_explode_hasil_kerja_4"} = ${"row1_explode_hasil_kerja_4"}[1];
                        ${"row1_explode_lembur_4"} = explode("|",$row1_pengerjaan['lembur']);
                        ${"row1_explode_status_lembur_4"} = explode("-",${"row1_explode_lembur_4"}[4]);
                        if(${"row1_explode_status_lembur_4"}[1] == 'y'){
                            ${"row1_lembur_4"} = 1.5;
                        }else{
                            ${"row1_lembur_4"} = 1;
                        }
                    }
                    // End Hasil Kerja 4

                    // Hasil Kerja 5
                    ${"row1_explode_hasil_kerja_5"} = explode("|",$row1_pengerjaan['hasil_kerja_5']);
                    ${"row1_umk_borongan_lokal_5"} = $this->umkBoronganLokal->select('id','jenis_produk','umk_packing','umk_bandrol','umk_inner','umk_outer')
                                                    ->where('id',${"row1_explode_hasil_kerja_5"}[0])
                                                    ->first();
                    if(empty(${"row1_umk_borongan_lokal_5"})){
                        ${"row1_jenis_produk_5"} = '-';
                        ${"row1_hasil_kerja_5"} = null;
                        ${"row1_data_explode_hasil_kerja_5"} = '-';
                        ${"row1_lembur_5"} = 1;
                        ${"row1_total_hasil_5"} = 0;
                    }else{
                        ${"row1_jenis_produk_5"} = ${"row1_umk_borongan_lokal_5"}['jenis_produk'];
                        ${"row1_hasil_kerja_5"} = ${"row1_explode_hasil_kerja_5"}[1]*${"row1_umk_borongan_lokal_5"}['umk_outer'];
                        ${"row1_data_explode_hasil_kerja_5"} = ${"row1_explode_hasil_kerja_5"}[1];
                        ${"row1_explode_lembur_5"} = explode("|",$row1_pengerjaan['lembur']);
                        ${"row1_explode_status_lembur_5"} = explode("-",${"row1_explode_lembur_5"}[5]);
                        if(${"row1_explode_status_lembur_5"}[1] == 'y'){
                            ${"row1_lembur_5"} = 1.5;
                        }else{
                            ${"row1_lembur_5"} = 1;
                        }
                    }
                    // End Hasil Kerja 5
                }

                // Export
                if ($row1_pengerjaan->operator_karyawan->jenis_operator_detail_pekerjaan_id == 5) {
                    // Hasil Kerja 1
                    ${"row1_explode_hasil_kerja_1"} = explode("|",$row1_pengerjaan['hasil_kerja_1']);
                    ${"row1_umk_borongan_lokal_1"} = $this->umkBoronganEkspor->select('id','jenis_produk','umk_packing','umk_kemas','umk_pilih_gagang')
                                                    ->where('id',${"row1_explode_hasil_kerja_1"}[0])
                                                    ->first();
                    if(empty(${"row1_umk_borongan_lokal_1"})){
                        ${"row1_jenis_produk_1"} = '-';
                        ${"row1_hasil_kerja_1"} = null;
                        ${"row1_data_explode_hasil_kerja_1"} = '-';
                        ${"row1_lembur_1"} = 1;
                        ${"row1_total_hasil_1"} = 0;
                    }else{
                        ${"row1_jenis_produk_1"} = ${"row1_umk_borongan_lokal_1"}['jenis_produk'];
                        ${"row1_hasil_kerja_1"} = ${"row1_explode_hasil_kerja_1"}[1]*${"row1_umk_borongan_lokal_1"}['umk_packing'];
                        ${"row1_data_explode_hasil_kerja_1"} = ${"row1_explode_hasil_kerja_1"}[1];
                        ${"row1_explode_lembur_1"} = explode("|",$row1_pengerjaan['lembur']);
                        ${"row1_explode_status_lembur_1"} = explode("-",${"row1_explode_lembur_1"}[1]);
                        if(${"row1_explode_status_lembur_1"}[1] == 'y'){
                            ${"row1_lembur_1"} = 1.5;
                        }else{
                            ${"row1_lembur_1"} = 1;
                        }
                    }
                    // End Hasil Kerja 1

                    // Hasil Kerja 2
                    ${"row1_explode_hasil_kerja_2"} = explode("|",$row1_pengerjaan['hasil_kerja_2']);
                    ${"row1_umk_borongan_lokal_2"} = $this->umkBoronganEkspor->select('id','jenis_produk','umk_packing','umk_kemas','umk_pilih_gagang')
                                                    ->where('id',${"row1_explode_hasil_kerja_2"}[0])
                                                    ->first();
                    if(empty(${"row1_umk_borongan_lokal_2"})){
                        ${"row1_jenis_produk_2"} = '-';
                        ${"row1_hasil_kerja_2"} = null;
                        ${"row1_data_explode_hasil_kerja_2"} = '-';
                        ${"row1_lembur_2"} = 1;
                        ${"row1_total_hasil_2"} = 0;
                    }else{
                        ${"row1_jenis_produk_2"} = ${"row1_umk_borongan_lokal_2"}['jenis_produk'];
                        ${"row1_hasil_kerja_2"} = ${"row1_explode_hasil_kerja_2"}[1]*${"row1_umk_borongan_lokal_2"}['umk_packing'];
                        ${"row1_data_explode_hasil_kerja_2"} = ${"row1_explode_hasil_kerja_2"}[1];
                        ${"row1_explode_lembur_2"} = explode("|",$row1_pengerjaan['lembur']);
                        ${"row1_explode_status_lembur_2"} = explode("-",${"row1_explode_lembur_2"}[2]);
                        if(${"row1_explode_status_lembur_2"}[1] == 'y'){
                            ${"row1_lembur_2"} = 1.5;
                        }else{
                            ${"row1_lembur_2"} = 1;
                        }
                    }
                    // End Hasil Kerja 2
                    
                    // Hasil Kerja 3
                    ${"row1_explode_hasil_kerja_3"} = explode("|",$row1_pengerjaan['hasil_kerja_3']);
                    ${"row1_umk_borongan_lokal_3"} = $this->umkBoronganEkspor->select('id','jenis_produk','umk_packing','umk_kemas','umk_pilih_gagang')
                                                    ->where('id',${"row1_explode_hasil_kerja_3"}[0])
                                                    ->first();
                    if(empty(${"row1_umk_borongan_lokal_3"})){
                        ${"row1_jenis_produk_3"} = '-';
                        ${"row1_hasil_kerja_3"} = null;
                        ${"row1_data_explode_hasil_kerja_3"} = '-';
                        ${"row1_lembur_3"} = 1;
                        ${"row1_total_hasil_3"} = 0;
                    }else{
                        ${"row1_jenis_produk_3"} = ${"row1_umk_borongan_lokal_3"}['jenis_produk'];
                        ${"row1_hasil_kerja_3"} = ${"row1_explode_hasil_kerja_3"}[1]*${"row1_umk_borongan_lokal_3"}['umk_packing'];
                        ${"row1_data_explode_hasil_kerja_3"} = ${"row1_explode_hasil_kerja_3"}[1];
                        ${"row1_explode_lembur_3"} = explode("|",$row1_pengerjaan['lembur']);
                        ${"row1_explode_status_lembur_3"} = explode("-",${"row1_explode_lembur_3"}[3]);
                        if(${"row1_explode_status_lembur_3"}[1] == 'y'){
                            ${"row1_lembur_3"} = 1.5;
                        }else{
                            ${"row1_lembur_3"} = 1;
                        }
                    }
                    // End Hasil Kerja 3

                    // Hasil Kerja 4
                    ${"row1_explode_hasil_kerja_4"} = explode("|",$row1_pengerjaan['hasil_kerja_4']);
                    ${"row1_umk_borongan_lokal_4"} = $this->umkBoronganEkspor->select('id','jenis_produk','umk_packing','umk_kemas','umk_pilih_gagang')
                                                    ->where('id',${"row1_explode_hasil_kerja_4"}[0])
                                                    ->first();
                    if(empty(${"row1_umk_borongan_lokal_4"})){
                        ${"row1_jenis_produk_4"} = '-';
                        ${"row1_hasil_kerja_4"} = null;
                        ${"row1_data_explode_hasil_kerja_4"} = '-';
                        ${"row1_lembur_4"} = 1;
                        ${"row1_total_hasil_4"} = 0;
                    }else{
                        ${"row1_jenis_produk_4"} = ${"row1_umk_borongan_lokal_4"}['jenis_produk'];
                        ${"row1_hasil_kerja_4"} = ${"row1_explode_hasil_kerja_4"}[1]*${"row1_umk_borongan_lokal_4"}['umk_packing'];
                        ${"row1_data_explode_hasil_kerja_4"} = ${"row1_explode_hasil_kerja_4"}[1];
                        ${"row1_explode_lembur_4"} = explode("|",$row1_pengerjaan['lembur']);
                        ${"row1_explode_status_lembur_4"} = explode("-",${"row1_explode_lembur_4"}[4]);
                        if(${"row1_explode_status_lembur_4"}[1] == 'y'){
                            ${"row1_lembur_4"} = 1.5;
                        }else{
                            ${"row1_lembur_4"} = 1;
                        }
                    }
                    // End Hasil Kerja 4

                    // Hasil Kerja 5
                    ${"row1_explode_hasil_kerja_5"} = explode("|",$row1_pengerjaan['hasil_kerja_5']);
                    ${"row1_umk_borongan_lokal_5"} = $this->umkBoronganEkspor->select('id','jenis_produk','umk_packing','umk_kemas','umk_pilih_gagang')
                                                    ->where('id',${"row1_explode_hasil_kerja_5"}[0])
                                                    ->first();
                    if(empty(${"row1_umk_borongan_lokal_5"})){
                        ${"row1_jenis_produk_5"} = '-';
                        ${"row1_hasil_kerja_5"} = null;
                        ${"row1_data_explode_hasil_kerja_5"} = '-';
                        ${"row1_lembur_5"} = 1;
                        ${"row1_total_hasil_5"} = 0;
                    }else{
                        ${"row1_jenis_produk_5"} = ${"row1_umk_borongan_lokal_5"}['jenis_produk'];
                        ${"row1_hasil_kerja_5"} = ${"row1_explode_hasil_kerja_5"}[1]*${"row1_umk_borongan_lokal_5"}['umk_packing'];
                        ${"row1_data_explode_hasil_kerja_5"} = ${"row1_explode_hasil_kerja_5"}[1];
                        ${"row1_explode_lembur_5"} = explode("|",$row1_pengerjaan['lembur']);
                        ${"row1_explode_status_lembur_5"} = explode("-",${"row1_explode_lembur_5"}[5]);
                        if(${"row1_explode_status_lembur_5"}[1] == 'y'){
                            ${"row1_lembur_5"} = 1.5;
                        }else{
                            ${"row1_lembur_5"} = 1;
                        }
                    }
                    // End Hasil Kerja 5
                }

                if ($row1_pengerjaan->operator_karyawan->jenis_operator_detail_pekerjaan_id == 6) {
                    // Hasil Kerja 1
                    ${"row1_explode_hasil_kerja_1"} = explode("|",$row1_pengerjaan['hasil_kerja_1']);
                    ${"row1_umk_borongan_lokal_1"} = $this->umkBoronganEkspor->select('id','jenis_produk','umk_packing','umk_kemas','umk_pilih_gagang')
                                                    ->where('id',${"row1_explode_hasil_kerja_1"}[0])
                                                    ->first();
                    if(empty(${"row1_umk_borongan_lokal_1"})){
                        ${"row1_jenis_produk_1"} = '-';
                        ${"row1_hasil_kerja_1"} = null;
                        ${"row1_data_explode_hasil_kerja_1"} = '-';
                        ${"row1_lembur_1"} = 1;
                        ${"row1_total_hasil_1"} = 0;
                    }else{
                        ${"row1_jenis_produk_1"} = ${"row1_umk_borongan_lokal_1"}['jenis_produk'];
                        ${"row1_hasil_kerja_1"} = ${"row1_explode_hasil_kerja_1"}[1]*${"row1_umk_borongan_lokal_1"}['umk_kemas'];
                        ${"row1_data_explode_hasil_kerja_1"} = ${"row1_explode_hasil_kerja_1"}[1];
                        ${"row1_explode_lembur_1"} = explode("|",$row1_pengerjaan['lembur']);
                        ${"row1_explode_status_lembur_1"} = explode("-",${"row1_explode_lembur_1"}[1]);
                        if(${"row1_explode_status_lembur_1"}[1] == 'y'){
                            ${"row1_lembur_1"} = 1.5;
                        }else{
                            ${"row1_lembur_1"} = 1;
                        }
                    }
                    // End Hasil Kerja 1

                    // Hasil Kerja 2
                    ${"row1_explode_hasil_kerja_2"} = explode("|",$row1_pengerjaan['hasil_kerja_2']);
                    ${"row1_umk_borongan_lokal_2"} = $this->umkBoronganEkspor->select('id','jenis_produk','umk_packing','umk_kemas','umk_pilih_gagang')
                                                    ->where('id',${"row1_explode_hasil_kerja_2"}[0])
                                                    ->first();
                    if(empty(${"row1_umk_borongan_lokal_2"})){
                        ${"row1_jenis_produk_2"} = '-';
                        ${"row1_hasil_kerja_2"} = null;
                        ${"row1_data_explode_hasil_kerja_2"} = '-';
                        ${"row1_lembur_2"} = 1;
                        ${"row1_total_hasil_2"} = 0;
                    }else{
                        ${"row1_jenis_produk_2"} = ${"row1_umk_borongan_lokal_2"}['jenis_produk'];
                        ${"row1_hasil_kerja_2"} = ${"row1_explode_hasil_kerja_2"}[1]*${"row1_umk_borongan_lokal_2"}['umk_kemas'];
                        ${"row1_data_explode_hasil_kerja_2"} = ${"row1_explode_hasil_kerja_2"}[1];
                        ${"row1_explode_lembur_2"} = explode("|",$row1_pengerjaan['lembur']);
                        ${"row1_explode_status_lembur_2"} = explode("-",${"row1_explode_lembur_2"}[2]);
                        if(${"row1_explode_status_lembur_2"}[1] == 'y'){
                            ${"row1_lembur_2"} = 1.5;
                        }else{
                            ${"row1_lembur_2"} = 1;
                        }
                    }
                    // End Hasil Kerja 2
                    
                    // Hasil Kerja 3
                    ${"row1_explode_hasil_kerja_3"} = explode("|",$row1_pengerjaan['hasil_kerja_3']);
                    ${"row1_umk_borongan_lokal_3"} = $this->umkBoronganEkspor->select('id','jenis_produk','umk_packing','umk_kemas','umk_pilih_gagang')
                                                    ->where('id',${"row1_explode_hasil_kerja_3"}[0])
                                                    ->first();
                    if(empty(${"row1_umk_borongan_lokal_3"})){
                        ${"row1_jenis_produk_3"} = '-';
                        ${"row1_hasil_kerja_3"} = null;
                        ${"row1_data_explode_hasil_kerja_3"} = '-';
                        ${"row1_lembur_3"} = 1;
                        ${"row1_total_hasil_3"} = 0;
                    }else{
                        ${"row1_jenis_produk_3"} = ${"row1_umk_borongan_lokal_3"}['jenis_produk'];
                        ${"row1_hasil_kerja_3"} = ${"row1_explode_hasil_kerja_3"}[1]*${"row1_umk_borongan_lokal_3"}['umk_kemas'];
                        ${"row1_data_explode_hasil_kerja_3"} = ${"row1_explode_hasil_kerja_3"}[1];
                        ${"row1_explode_lembur_3"} = explode("|",$row1_pengerjaan['lembur']);
                        ${"row1_explode_status_lembur_3"} = explode("-",${"row1_explode_lembur_3"}[3]);
                        if(${"row1_explode_status_lembur_3"}[1] == 'y'){
                            ${"row1_lembur_3"} = 1.5;
                        }else{
                            ${"row1_lembur_3"} = 1;
                        }
                    }
                    // End Hasil Kerja 3

                    // Hasil Kerja 4
                    ${"row1_explode_hasil_kerja_4"} = explode("|",$row1_pengerjaan['hasil_kerja_4']);
                    ${"row1_umk_borongan_lokal_4"} = $this->umkBoronganEkspor->select('id','jenis_produk','umk_packing','umk_kemas','umk_pilih_gagang')
                                                    ->where('id',${"row1_explode_hasil_kerja_4"}[0])
                                                    ->first();
                    if(empty(${"row1_umk_borongan_lokal_4"})){
                        ${"row1_jenis_produk_4"} = '-';
                        ${"row1_hasil_kerja_4"} = null;
                        ${"row1_data_explode_hasil_kerja_4"} = '-';
                        ${"row1_lembur_4"} = 1;
                        ${"row1_total_hasil_4"} = 0;
                    }else{
                        ${"row1_jenis_produk_4"} = ${"row1_umk_borongan_lokal_4"}['jenis_produk'];
                        ${"row1_hasil_kerja_4"} = ${"row1_explode_hasil_kerja_4"}[1]*${"row1_umk_borongan_lokal_4"}['umk_kemas'];
                        ${"row1_data_explode_hasil_kerja_4"} = ${"row1_explode_hasil_kerja_4"}[1];
                        ${"row1_explode_lembur_4"} = explode("|",$row1_pengerjaan['lembur']);
                        ${"row1_explode_status_lembur_4"} = explode("-",${"row1_explode_lembur_4"}[4]);
                        if(${"row1_explode_status_lembur_4"}[1] == 'y'){
                            ${"row1_lembur_4"} = 1.5;
                        }else{
                            ${"row1_lembur_4"} = 1;
                        }
                    }
                    // End Hasil Kerja 4

                    // Hasil Kerja 5
                    ${"row1_explode_hasil_kerja_5"} = explode("|",$row1_pengerjaan['hasil_kerja_5']);
                    ${"row1_umk_borongan_lokal_5"} = $this->umkBoronganEkspor->select('id','jenis_produk','umk_packing','umk_kemas','umk_pilih_gagang')
                                                    ->where('id',${"row1_explode_hasil_kerja_5"}[0])
                                                    ->first();
                    if(empty(${"row1_umk_borongan_lokal_5"})){
                        ${"row1_jenis_produk_5"} = '-';
                        ${"row1_hasil_kerja_5"} = null;
                        ${"row1_data_explode_hasil_kerja_5"} = '-';
                        ${"row1_lembur_5"} = 1;
                        ${"row1_total_hasil_5"} = 0;
                    }else{
                        ${"row1_jenis_produk_5"} = ${"row1_umk_borongan_lokal_5"}['jenis_produk'];
                        ${"row1_hasil_kerja_5"} = ${"row1_explode_hasil_kerja_5"}[1]*${"row1_umk_borongan_lokal_5"}['umk_kemas'];
                        ${"row1_data_explode_hasil_kerja_5"} = ${"row1_explode_hasil_kerja_5"}[1];
                        ${"row1_explode_lembur_5"} = explode("|",$row1_pengerjaan['lembur']);
                        ${"row1_explode_status_lembur_5"} = explode("-",${"row1_explode_lembur_5"}[5]);
                        if(${"row1_explode_status_lembur_5"}[1] == 'y'){
                            ${"row1_lembur_5"} = 1.5;
                        }else{
                            ${"row1_lembur_5"} = 1;
                        }
                    }
                    // End Hasil Kerja 5
                }
                
                if ($row1_pengerjaan->operator_karyawan->jenis_operator_detail_pekerjaan_id == 7) {
                    // Hasil Kerja 1
                    ${"row1_explode_hasil_kerja_1"} = explode("|",$row1_pengerjaan['hasil_kerja_1']);
                    ${"row1_umk_borongan_lokal_1"} = $this->umkBoronganEkspor->select('id','jenis_produk','umk_packing','umk_kemas','umk_pilih_gagang')
                                                    ->where('id',${"row1_explode_hasil_kerja_1"}[0])
                                                    ->first();
                    if(empty(${"row1_umk_borongan_lokal_1"})){
                        ${"row1_jenis_produk_1"} = '-';
                        ${"row1_hasil_kerja_1"} = null;
                        ${"row1_data_explode_hasil_kerja_1"} = '-';
                        ${"row1_lembur_1"} = 1;
                        ${"row1_total_hasil_1"} = 0;
                    }else{
                        ${"row1_jenis_produk_1"} = ${"row1_umk_borongan_lokal_1"}['jenis_produk'];
                        ${"row1_hasil_kerja_1"} = ${"row1_explode_hasil_kerja_1"}[1]*${"row1_umk_borongan_lokal_1"}['umk_pilih_gagang'];
                        ${"row1_data_explode_hasil_kerja_1"} = ${"row1_explode_hasil_kerja_1"}[1];
                        ${"row1_explode_lembur_1"} = explode("|",$row1_pengerjaan['lembur']);
                        ${"row1_explode_status_lembur_1"} = explode("-",${"row1_explode_lembur_1"}[1]);
                        if(${"row1_explode_status_lembur_1"}[1] == 'y'){
                            ${"row1_lembur_1"} = 1.5;
                        }else{
                            ${"row1_lembur_1"} = 1;
                        }
                    }
                    // End Hasil Kerja 1

                    // Hasil Kerja 2
                    ${"row1_explode_hasil_kerja_2"} = explode("|",$row1_pengerjaan['hasil_kerja_2']);
                    ${"row1_umk_borongan_lokal_2"} = $this->umkBoronganEkspor->select('id','jenis_produk','umk_packing','umk_kemas','umk_pilih_gagang')
                                                    ->where('id',${"row1_explode_hasil_kerja_2"}[0])
                                                    ->first();
                    if(empty(${"row1_umk_borongan_lokal_2"})){
                        ${"row1_jenis_produk_2"} = '-';
                        ${"row1_hasil_kerja_2"} = null;
                        ${"row1_data_explode_hasil_kerja_2"} = '-';
                        ${"row1_lembur_2"} = 1;
                        ${"row1_total_hasil_2"} = 0;
                    }else{
                        ${"row1_jenis_produk_2"} = ${"row1_umk_borongan_lokal_2"}['jenis_produk'];
                        ${"row1_hasil_kerja_2"} = ${"row1_explode_hasil_kerja_2"}[1]*${"row1_umk_borongan_lokal_2"}['umk_pilih_gagang'];
                        ${"row1_data_explode_hasil_kerja_2"} = ${"row1_explode_hasil_kerja_2"}[1];
                        ${"row1_explode_lembur_2"} = explode("|",$row1_pengerjaan['lembur']);
                        ${"row1_explode_status_lembur_2"} = explode("-",${"row1_explode_lembur_2"}[2]);
                        if(${"row1_explode_status_lembur_2"}[1] == 'y'){
                            ${"row1_lembur_2"} = 1.5;
                        }else{
                            ${"row1_lembur_2"} = 1;
                        }
                    }
                    // End Hasil Kerja 2
                    
                    // Hasil Kerja 3
                    ${"row1_explode_hasil_kerja_3"} = explode("|",$row1_pengerjaan['hasil_kerja_3']);
                    ${"row1_umk_borongan_lokal_3"} = $this->umkBoronganEkspor->select('id','jenis_produk','umk_packing','umk_kemas','umk_pilih_gagang')
                                                    ->where('id',${"row1_explode_hasil_kerja_3"}[0])
                                                    ->first();
                    if(empty(${"row1_umk_borongan_lokal_3"})){
                        ${"row1_jenis_produk_3"} = '-';
                        ${"row1_hasil_kerja_3"} = null;
                        ${"row1_data_explode_hasil_kerja_3"} = '-';
                        ${"row1_lembur_3"} = 1;
                        ${"row1_total_hasil_3"} = 0;
                    }else{
                        ${"row1_jenis_produk_3"} = ${"row1_umk_borongan_lokal_3"}['jenis_produk'];
                        ${"row1_hasil_kerja_3"} = ${"row1_explode_hasil_kerja_3"}[1]*${"row1_umk_borongan_lokal_3"}['umk_pilih_gagang'];
                        ${"row1_data_explode_hasil_kerja_3"} = ${"row1_explode_hasil_kerja_3"}[1];
                        ${"row1_explode_lembur_3"} = explode("|",$row1_pengerjaan['lembur']);
                        ${"row1_explode_status_lembur_3"} = explode("-",${"row1_explode_lembur_3"}[3]);
                        if(${"row1_explode_status_lembur_3"}[1] == 'y'){
                            ${"row1_lembur_3"} = 1.5;
                        }else{
                            ${"row1_lembur_3"} = 1;
                        }
                    }
                    // End Hasil Kerja 3

                    // Hasil Kerja 4
                    ${"row1_explode_hasil_kerja_4"} = explode("|",$row1_pengerjaan['hasil_kerja_4']);
                    ${"row1_umk_borongan_lokal_4"} = $this->umkBoronganEkspor->select('id','jenis_produk','umk_packing','umk_kemas','umk_pilih_gagang')
                                                    ->where('id',${"row1_explode_hasil_kerja_4"}[0])
                                                    ->first();
                    if(empty(${"row1_umk_borongan_lokal_4"})){
                        ${"row1_jenis_produk_4"} = '-';
                        ${"row1_hasil_kerja_4"} = null;
                        ${"row1_data_explode_hasil_kerja_4"} = '-';
                        ${"row1_lembur_4"} = 1;
                        ${"row1_total_hasil_4"} = 0;
                    }else{
                        ${"row1_jenis_produk_4"} = ${"row1_umk_borongan_lokal_4"}['jenis_produk'];
                        ${"row1_hasil_kerja_4"} = ${"row1_explode_hasil_kerja_4"}[1]*${"row1_umk_borongan_lokal_4"}['umk_pilih_gagang'];
                        ${"row1_data_explode_hasil_kerja_4"} = ${"row1_explode_hasil_kerja_4"}[1];
                        ${"row1_explode_lembur_4"} = explode("|",$row1_pengerjaan['lembur']);
                        ${"row1_explode_status_lembur_4"} = explode("-",${"row1_explode_lembur_4"}[4]);
                        if(${"row1_explode_status_lembur_4"}[1] == 'y'){
                            ${"row1_lembur_4"} = 1.5;
                        }else{
                            ${"row1_lembur_4"} = 1;
                        }
                    }
                    // End Hasil Kerja 4

                    // Hasil Kerja 5
                    ${"row1_explode_hasil_kerja_5"} = explode("|",$row1_pengerjaan['hasil_kerja_5']);
                    ${"row1_umk_borongan_lokal_5"} = $this->umkBoronganEkspor->select('id','jenis_produk','umk_packing','umk_kemas','umk_pilih_gagang')
                                                    ->where('id',${"row1_explode_hasil_kerja_5"}[0])
                                                    ->first();
                    if(empty(${"row1_umk_borongan_lokal_5"})){
                        ${"row1_jenis_produk_5"} = '-';
                        ${"row1_hasil_kerja_5"} = null;
                        ${"row1_data_explode_hasil_kerja_5"} = '-';
                        ${"row1_lembur_5"} = 1;
                        ${"row1_total_hasil_5"} = 0;
                    }else{
                        ${"row1_jenis_produk_5"} = ${"row1_umk_borongan_lokal_5"}['jenis_produk'];
                        ${"row1_hasil_kerja_5"} = ${"row1_explode_hasil_kerja_5"}[1]*${"row1_umk_borongan_lokal_5"}['umk_pilih_gagang'];
                        ${"row1_data_explode_hasil_kerja_5"} = ${"row1_explode_hasil_kerja_5"}[1];
                        ${"row1_explode_lembur_5"} = explode("|",$row1_pengerjaan['lembur']);
                        ${"row1_explode_status_lembur_5"} = explode("-",${"row1_explode_lembur_5"}[5]);
                        if(${"row1_explode_status_lembur_5"}[1] == 'y'){
                            ${"row1_lembur_5"} = 1.5;
                        }else{
                            ${"row1_lembur_5"} = 1;
                        }
                    }
                    // End Hasil Kerja 5
                }

                // Ambri
                if ($row1_pengerjaan->operator_karyawan->jenis_operator_detail_pekerjaan_id == 8) {
                    // Hasil Kerja 1
                    ${"row1_explode_hasil_kerja_1"} = explode("|",$row1_pengerjaan['hasil_kerja_1']);
                    ${"row1_umk_borongan_lokal_1"} = $this->umkBoronganAmbri->select('id','jenis_produk','umk_etiket','umk_las_tepi','umk_las_pojok','umk_ambri')
                                                    ->where('id',${"row1_explode_hasil_kerja_1"}[0])
                                                    ->first();
                    if(empty(${"row1_umk_borongan_lokal_1"})){
                        ${"row1_jenis_produk_1"} = '-';
                        ${"row1_hasil_kerja_1"} = null;
                        ${"row1_data_explode_hasil_kerja_1"} = '-';
                        ${"row1_lembur_1"} = 1;
                        ${"row1_total_hasil_1"} = 0;
                    }else{
                        ${"row1_jenis_produk_1"} = ${"row1_umk_borongan_lokal_1"}['jenis_produk'];
                        ${"row1_hasil_kerja_1"} = ${"row1_explode_hasil_kerja_1"}[1]*${"row1_umk_borongan_lokal_1"}['umk_etiket'];
                        ${"row1_data_explode_hasil_kerja_1"} = ${"row1_explode_hasil_kerja_1"}[1];
                        ${"row1_explode_lembur_1"} = explode("|",$row1_pengerjaan['lembur']);
                        ${"row1_explode_status_lembur_1"} = explode("-",${"row1_explode_lembur_1"}[1]);
                        if(${"row1_explode_status_lembur_1"}[1] == 'y'){
                            ${"row1_lembur_1"} = 1.5;
                        }else{
                            ${"row1_lembur_1"} = 1;
                        }
                    }
                    // End Hasil Kerja 1

                    // Hasil Kerja 2
                    ${"row1_explode_hasil_kerja_2"} = explode("|",$row1_pengerjaan['hasil_kerja_2']);
                    ${"row1_umk_borongan_lokal_2"} = $this->umkBoronganAmbri->select('id','jenis_produk','umk_etiket','umk_las_tepi','umk_las_pojok','umk_ambri')
                                                    ->where('id',${"row1_explode_hasil_kerja_2"}[0])
                                                    ->first();
                    if(empty(${"row1_umk_borongan_lokal_2"})){
                        ${"row1_jenis_produk_2"} = '-';
                        ${"row1_hasil_kerja_2"} = null;
                        ${"row1_data_explode_hasil_kerja_2"} = '-';
                        ${"row1_lembur_2"} = 1;
                        ${"row1_total_hasil_2"} = 0;
                    }else{
                        ${"row1_jenis_produk_2"} = ${"row1_umk_borongan_lokal_2"}['jenis_produk'];
                        ${"row1_hasil_kerja_2"} = ${"row1_explode_hasil_kerja_2"}[1]*${"row1_umk_borongan_lokal_2"}['umk_etiket'];
                        ${"row1_data_explode_hasil_kerja_2"} = ${"row1_explode_hasil_kerja_2"}[1];
                        ${"row1_explode_lembur_2"} = explode("|",$row1_pengerjaan['lembur']);
                        ${"row1_explode_status_lembur_2"} = explode("-",${"row1_explode_lembur_2"}[2]);
                        if(${"row1_explode_status_lembur_2"}[1] == 'y'){
                            ${"row1_lembur_2"} = 1.5;
                        }else{
                            ${"row1_lembur_2"} = 1;
                        }
                    }
                    // End Hasil Kerja 2
                    
                    // Hasil Kerja 3
                    ${"row1_explode_hasil_kerja_3"} = explode("|",$row1_pengerjaan['hasil_kerja_3']);
                    ${"row1_umk_borongan_lokal_3"} = $this->umkBoronganAmbri->select('id','jenis_produk','umk_etiket','umk_las_tepi','umk_las_pojok','umk_ambri')
                                                    ->where('id',${"row1_explode_hasil_kerja_3"}[0])
                                                    ->first();
                    if(empty(${"row1_umk_borongan_lokal_3"})){
                        ${"row1_jenis_produk_3"} = '-';
                        ${"row1_hasil_kerja_3"} = null;
                        ${"row1_data_explode_hasil_kerja_3"} = '-';
                        ${"row1_lembur_3"} = 1;
                        ${"row1_total_hasil_3"} = 0;
                    }else{
                        ${"row1_jenis_produk_3"} = ${"row1_umk_borongan_lokal_3"}['jenis_produk'];
                        ${"row1_hasil_kerja_3"} = ${"row1_explode_hasil_kerja_3"}[1]*${"row1_umk_borongan_lokal_3"}['umk_etiket'];
                        ${"row1_data_explode_hasil_kerja_3"} = ${"row1_explode_hasil_kerja_3"}[1];
                        ${"row1_explode_lembur_3"} = explode("|",$row1_pengerjaan['lembur']);
                        ${"row1_explode_status_lembur_3"} = explode("-",${"row1_explode_lembur_3"}[3]);
                        if(${"row1_explode_status_lembur_3"}[1] == 'y'){
                            ${"row1_lembur_3"} = 1.5;
                        }else{
                            ${"row1_lembur_3"} = 1;
                        }
                    }
                    // End Hasil Kerja 3

                    // Hasil Kerja 4
                    ${"row1_explode_hasil_kerja_4"} = explode("|",$row1_pengerjaan['hasil_kerja_4']);
                    ${"row1_umk_borongan_lokal_4"} = $this->umkBoronganAmbri->select('id','jenis_produk','umk_etiket','umk_las_tepi','umk_las_pojok','umk_ambri')
                                                    ->where('id',${"row1_explode_hasil_kerja_4"}[0])
                                                    ->first();
                    if(empty(${"row1_umk_borongan_lokal_4"})){
                        ${"row1_jenis_produk_4"} = '-';
                        ${"row1_hasil_kerja_4"} = null;
                        ${"row1_data_explode_hasil_kerja_4"} = '-';
                        ${"row1_lembur_4"} = 1;
                        ${"row1_total_hasil_4"} = 0;
                    }else{
                        ${"row1_jenis_produk_4"} = ${"row1_umk_borongan_lokal_4"}['jenis_produk'];
                        ${"row1_hasil_kerja_4"} = ${"row1_explode_hasil_kerja_4"}[1]*${"row1_umk_borongan_lokal_4"}['umk_etiket'];
                        ${"row1_data_explode_hasil_kerja_4"} = ${"row1_explode_hasil_kerja_4"}[1];
                        ${"row1_explode_lembur_4"} = explode("|",$row1_pengerjaan['lembur']);
                        ${"row1_explode_status_lembur_4"} = explode("-",${"row1_explode_lembur_4"}[4]);
                        if(${"row1_explode_status_lembur_4"}[1] == 'y'){
                            ${"row1_lembur_4"} = 1.5;
                        }else{
                            ${"row1_lembur_4"} = 1;
                        }
                    }
                    // End Hasil Kerja 4

                    // Hasil Kerja 5
                    ${"row1_explode_hasil_kerja_5"} = explode("|",$row1_pengerjaan['hasil_kerja_5']);
                    ${"row1_umk_borongan_lokal_5"} = $this->umkBoronganAmbri->select('id','jenis_produk','umk_etiket','umk_las_tepi','umk_las_pojok','umk_ambri')
                                                    ->where('id',${"row1_explode_hasil_kerja_5"}[0])
                                                    ->first();
                    if(empty(${"row1_umk_borongan_lokal_5"})){
                        ${"row1_jenis_produk_5"} = '-';
                        ${"row1_hasil_kerja_5"} = null;
                        ${"row1_data_explode_hasil_kerja_5"} = '-';
                        ${"row1_lembur_5"} = 1;
                        ${"row1_total_hasil_5"} = 0;
                    }else{
                        ${"row1_jenis_produk_5"} = ${"row1_umk_borongan_lokal_5"}['jenis_produk'];
                        ${"row1_hasil_kerja_5"} = ${"row1_explode_hasil_kerja_5"}[1]*${"row1_umk_borongan_lokal_5"}['umk_etiket'];
                        ${"row1_data_explode_hasil_kerja_5"} = ${"row1_explode_hasil_kerja_5"}[1];
                        ${"row1_explode_lembur_5"} = explode("|",$row1_pengerjaan['lembur']);
                        ${"row1_explode_status_lembur_5"} = explode("-",${"row1_explode_lembur_5"}[5]);
                        if(${"row1_explode_status_lembur_5"}[1] == 'y'){
                            ${"row1_lembur_5"} = 1.5;
                        }else{
                            ${"row1_lembur_5"} = 1;
                        }
                    }
                    // End Hasil Kerja 5
                }

                if ($row1_pengerjaan->operator_karyawan->jenis_operator_detail_pekerjaan_id == 9) {
                    // Hasil Kerja 1
                    ${"row1_explode_hasil_kerja_1"} = explode("|",$row1_pengerjaan['hasil_kerja_1']);
                    ${"row1_umk_borongan_lokal_1"} = $this->umkBoronganAmbri->select('id','jenis_produk','umk_etiket','umk_las_tepi','umk_las_pojok','umk_ambri')
                                                    ->where('id',${"row1_explode_hasil_kerja_1"}[0])
                                                    ->first();
                    if(empty(${"row1_umk_borongan_lokal_1"})){
                        ${"row1_jenis_produk_1"} = '-';
                        ${"row1_hasil_kerja_1"} = null;
                        ${"row1_data_explode_hasil_kerja_1"} = '-';
                        ${"row1_lembur_1"} = 1;
                        ${"row1_total_hasil_1"} = 0;
                    }else{
                        ${"row1_jenis_produk_1"} = ${"row1_umk_borongan_lokal_1"}['jenis_produk'];
                        ${"row1_hasil_kerja_1"} = ${"row1_explode_hasil_kerja_1"}[1]*${"row1_umk_borongan_lokal_1"}['umk_las_tepi'];
                        ${"row1_data_explode_hasil_kerja_1"} = ${"row1_explode_hasil_kerja_1"}[1];
                        ${"row1_explode_lembur_1"} = explode("|",$row1_pengerjaan['lembur']);
                        ${"row1_explode_status_lembur_1"} = explode("-",${"row1_explode_lembur_1"}[1]);
                        if(${"row1_explode_status_lembur_1"}[1] == 'y'){
                            ${"row1_lembur_1"} = 1.5;
                        }else{
                            ${"row1_lembur_1"} = 1;
                        }
                    }
                    // End Hasil Kerja 1

                    // Hasil Kerja 2
                    ${"row1_explode_hasil_kerja_2"} = explode("|",$row1_pengerjaan['hasil_kerja_2']);
                    ${"row1_umk_borongan_lokal_2"} = $this->umkBoronganAmbri->select('id','jenis_produk','umk_etiket','umk_las_tepi','umk_las_pojok','umk_ambri')
                                                    ->where('id',${"row1_explode_hasil_kerja_2"}[0])
                                                    ->first();
                    if(empty(${"row1_umk_borongan_lokal_2"})){
                        ${"row1_jenis_produk_2"} = '-';
                        ${"row1_hasil_kerja_2"} = null;
                        ${"row1_data_explode_hasil_kerja_2"} = '-';
                        ${"row1_lembur_2"} = 1;
                        ${"row1_total_hasil_2"} = 0;
                    }else{
                        ${"row1_jenis_produk_2"} = ${"row1_umk_borongan_lokal_2"}['jenis_produk'];
                        ${"row1_hasil_kerja_2"} = ${"row1_explode_hasil_kerja_2"}[1]*${"row1_umk_borongan_lokal_2"}['umk_las_tepi'];
                        ${"row1_data_explode_hasil_kerja_2"} = ${"row1_explode_hasil_kerja_2"}[1];
                        ${"row1_explode_lembur_2"} = explode("|",$row1_pengerjaan['lembur']);
                        ${"row1_explode_status_lembur_2"} = explode("-",${"row1_explode_lembur_2"}[2]);
                        if(${"row1_explode_status_lembur_2"}[1] == 'y'){
                            ${"row1_lembur_2"} = 1.5;
                        }else{
                            ${"row1_lembur_2"} = 1;
                        }
                    }
                    // End Hasil Kerja 2
                    
                    // Hasil Kerja 3
                    ${"row1_explode_hasil_kerja_3"} = explode("|",$row1_pengerjaan['hasil_kerja_3']);
                    ${"row1_umk_borongan_lokal_3"} = $this->umkBoronganAmbri->select('id','jenis_produk','umk_etiket','umk_las_tepi','umk_las_pojok','umk_ambri')
                                                    ->where('id',${"row1_explode_hasil_kerja_3"}[0])
                                                    ->first();
                    if(empty(${"row1_umk_borongan_lokal_3"})){
                        ${"row1_jenis_produk_3"} = '-';
                        ${"row1_hasil_kerja_3"} = null;
                        ${"row1_data_explode_hasil_kerja_3"} = '-';
                        ${"row1_lembur_3"} = 1;
                        ${"row1_total_hasil_3"} = 0;
                    }else{
                        ${"row1_jenis_produk_3"} = ${"row1_umk_borongan_lokal_3"}['jenis_produk'];
                        ${"row1_hasil_kerja_3"} = ${"row1_explode_hasil_kerja_3"}[1]*${"row1_umk_borongan_lokal_3"}['umk_las_tepi'];
                        ${"row1_data_explode_hasil_kerja_3"} = ${"row1_explode_hasil_kerja_3"}[1];
                        ${"row1_explode_lembur_3"} = explode("|",$row1_pengerjaan['lembur']);
                        ${"row1_explode_status_lembur_3"} = explode("-",${"row1_explode_lembur_3"}[3]);
                        if(${"row1_explode_status_lembur_3"}[1] == 'y'){
                            ${"row1_lembur_3"} = 1.5;
                        }else{
                            ${"row1_lembur_3"} = 1;
                        }
                    }
                    // End Hasil Kerja 3

                    // Hasil Kerja 4
                    ${"row1_explode_hasil_kerja_4"} = explode("|",$row1_pengerjaan['hasil_kerja_4']);
                    ${"row1_umk_borongan_lokal_4"} = $this->umkBoronganAmbri->select('id','jenis_produk','umk_etiket','umk_las_tepi','umk_las_pojok','umk_ambri')
                                                    ->where('id',${"row1_explode_hasil_kerja_4"}[0])
                                                    ->first();
                    if(empty(${"row1_umk_borongan_lokal_4"})){
                        ${"row1_jenis_produk_4"} = '-';
                        ${"row1_hasil_kerja_4"} = null;
                        ${"row1_data_explode_hasil_kerja_4"} = '-';
                        ${"row1_lembur_4"} = 1;
                        ${"row1_total_hasil_4"} = 0;
                    }else{
                        ${"row1_jenis_produk_4"} = ${"row1_umk_borongan_lokal_4"}['jenis_produk'];
                        ${"row1_hasil_kerja_4"} = ${"row1_explode_hasil_kerja_4"}[1]*${"row1_umk_borongan_lokal_4"}['umk_las_tepi'];
                        ${"row1_data_explode_hasil_kerja_4"} = ${"row1_explode_hasil_kerja_4"}[1];
                        ${"row1_explode_lembur_4"} = explode("|",$row1_pengerjaan['lembur']);
                        ${"row1_explode_status_lembur_4"} = explode("-",${"row1_explode_lembur_4"}[4]);
                        if(${"row1_explode_status_lembur_4"}[1] == 'y'){
                            ${"row1_lembur_4"} = 1.5;
                        }else{
                            ${"row1_lembur_4"} = 1;
                        }
                    }
                    // End Hasil Kerja 4

                    // Hasil Kerja 5
                    ${"row1_explode_hasil_kerja_5"} = explode("|",$row1_pengerjaan['hasil_kerja_5']);
                    ${"row1_umk_borongan_lokal_5"} = $this->umkBoronganAmbri->select('id','jenis_produk','umk_etiket','umk_las_tepi','umk_las_pojok','umk_ambri')
                                                    ->where('id',${"row1_explode_hasil_kerja_5"}[0])
                                                    ->first();
                    if(empty(${"row1_umk_borongan_lokal_5"})){
                        ${"row1_jenis_produk_5"} = '-';
                        ${"row1_hasil_kerja_5"} = null;
                        ${"row1_data_explode_hasil_kerja_5"} = '-';
                        ${"row1_lembur_5"} = 1;
                        ${"row1_total_hasil_5"} = 0;
                    }else{
                        ${"row1_jenis_produk_5"} = ${"row1_umk_borongan_lokal_5"}['jenis_produk'];
                        ${"row1_hasil_kerja_5"} = ${"row1_explode_hasil_kerja_5"}[1]*${"row1_umk_borongan_lokal_5"}['umk_las_tepi'];
                        ${"row1_data_explode_hasil_kerja_5"} = ${"row1_explode_hasil_kerja_5"}[1];
                        ${"row1_explode_lembur_5"} = explode("|",$row1_pengerjaan['lembur']);
                        ${"row1_explode_status_lembur_5"} = explode("-",${"row1_explode_lembur_5"}[5]);
                        if(${"row1_explode_status_lembur_5"}[1] == 'y'){
                            ${"row1_lembur_5"} = 1.5;
                        }else{
                            ${"row1_lembur_5"} = 1;
                        }
                    }
                    // End Hasil Kerja 5
                }

                if ($row1_pengerjaan->operator_karyawan->jenis_operator_detail_pekerjaan_id == 11) {
                    // Hasil Kerja 1
                    ${"row1_explode_hasil_kerja_1"} = explode("|",$row1_pengerjaan['hasil_kerja_1']);
                    ${"row1_umk_borongan_lokal_1"} = $this->umkBoronganAmbri->select('id','jenis_produk','umk_etiket','umk_las_tepi','umk_las_pojok','umk_ambri')
                                                    ->where('id',${"row1_explode_hasil_kerja_1"}[0])
                                                    ->first();
                    if(empty(${"row1_umk_borongan_lokal_1"})){
                        ${"row1_jenis_produk_1"} = '-';
                        ${"row1_hasil_kerja_1"} = null;
                        ${"row1_data_explode_hasil_kerja_1"} = '-';
                        ${"row1_lembur_1"} = 1;
                        ${"row1_total_hasil_1"} = 0;
                    }else{
                        ${"row1_jenis_produk_1"} = ${"row1_umk_borongan_lokal_1"}['jenis_produk'];
                        ${"row1_hasil_kerja_1"} = ${"row1_explode_hasil_kerja_1"}[1]*${"row1_umk_borongan_lokal_1"}['umk_ambri'];
                        ${"row1_data_explode_hasil_kerja_1"} = ${"row1_explode_hasil_kerja_1"}[1];
                        ${"row1_explode_lembur_1"} = explode("|",$row1_pengerjaan['lembur']);
                        ${"row1_explode_status_lembur_1"} = explode("-",${"row1_explode_lembur_1"}[1]);
                        if(${"row1_explode_status_lembur_1"}[1] == 'y'){
                            ${"row1_lembur_1"} = 1.5;
                        }else{
                            ${"row1_lembur_1"} = 1;
                        }
                    }
                    // End Hasil Kerja 1

                    // Hasil Kerja 2
                    ${"row1_explode_hasil_kerja_2"} = explode("|",$row1_pengerjaan['hasil_kerja_2']);
                    ${"row1_umk_borongan_lokal_2"} = $this->umkBoronganAmbri->select('id','jenis_produk','umk_etiket','umk_las_tepi','umk_las_pojok','umk_ambri')
                                                    ->where('id',${"row1_explode_hasil_kerja_2"}[0])
                                                    ->first();
                    if(empty(${"row1_umk_borongan_lokal_2"})){
                        ${"row1_jenis_produk_2"} = '-';
                        ${"row1_hasil_kerja_2"} = null;
                        ${"row1_data_explode_hasil_kerja_2"} = '-';
                        ${"row1_lembur_2"} = 1;
                        ${"row1_total_hasil_2"} = 0;
                    }else{
                        ${"row1_jenis_produk_2"} = ${"row1_umk_borongan_lokal_2"}['jenis_produk'];
                        ${"row1_hasil_kerja_2"} = ${"row1_explode_hasil_kerja_2"}[1]*${"row1_umk_borongan_lokal_2"}['umk_ambri'];
                        ${"row1_data_explode_hasil_kerja_2"} = ${"row1_explode_hasil_kerja_2"}[1];
                        ${"row1_explode_lembur_2"} = explode("|",$row1_pengerjaan['lembur']);
                        ${"row1_explode_status_lembur_2"} = explode("-",${"row1_explode_lembur_2"}[2]);
                        if(${"row1_explode_status_lembur_2"}[1] == 'y'){
                            ${"row1_lembur_2"} = 1.5;
                        }else{
                            ${"row1_lembur_2"} = 1;
                        }
                    }
                    // End Hasil Kerja 2
                    
                    // Hasil Kerja 3
                    ${"row1_explode_hasil_kerja_3"} = explode("|",$row1_pengerjaan['hasil_kerja_3']);
                    ${"row1_umk_borongan_lokal_3"} = $this->umkBoronganAmbri->select('id','jenis_produk','umk_etiket','umk_las_tepi','umk_las_pojok','umk_ambri')
                                                    ->where('id',${"row1_explode_hasil_kerja_3"}[0])
                                                    ->first();
                    if(empty(${"row1_umk_borongan_lokal_3"})){
                        ${"row1_jenis_produk_3"} = '-';
                        ${"row1_hasil_kerja_3"} = null;
                        ${"row1_data_explode_hasil_kerja_3"} = '-';
                        ${"row1_lembur_3"} = 1;
                        ${"row1_total_hasil_3"} = 0;
                    }else{
                        ${"row1_jenis_produk_3"} = ${"row1_umk_borongan_lokal_3"}['jenis_produk'];
                        ${"row1_hasil_kerja_3"} = ${"row1_explode_hasil_kerja_3"}[1]*${"row1_umk_borongan_lokal_3"}['umk_ambri'];
                        ${"row1_data_explode_hasil_kerja_3"} = ${"row1_explode_hasil_kerja_3"}[1];
                        ${"row1_explode_lembur_3"} = explode("|",$row1_pengerjaan['lembur']);
                        ${"row1_explode_status_lembur_3"} = explode("-",${"row1_explode_lembur_3"}[3]);
                        if(${"row1_explode_status_lembur_3"}[1] == 'y'){
                            ${"row1_lembur_3"} = 1.5;
                        }else{
                            ${"row1_lembur_3"} = 1;
                        }
                    }
                    // End Hasil Kerja 3

                    // Hasil Kerja 4
                    ${"row1_explode_hasil_kerja_4"} = explode("|",$row1_pengerjaan['hasil_kerja_4']);
                    ${"row1_umk_borongan_lokal_4"} = $this->umkBoronganAmbri->select('id','jenis_produk','umk_etiket','umk_las_tepi','umk_las_pojok','umk_ambri')
                                                    ->where('id',${"row1_explode_hasil_kerja_4"}[0])
                                                    ->first();
                    if(empty(${"row1_umk_borongan_lokal_4"})){
                        ${"row1_jenis_produk_4"} = '-';
                        ${"row1_hasil_kerja_4"} = null;
                        ${"row1_data_explode_hasil_kerja_4"} = '-';
                        ${"row1_lembur_4"} = 1;
                        ${"row1_total_hasil_4"} = 0;
                    }else{
                        ${"row1_jenis_produk_4"} = ${"row1_umk_borongan_lokal_4"}['jenis_produk'];
                        ${"row1_hasil_kerja_4"} = ${"row1_explode_hasil_kerja_4"}[1]*${"row1_umk_borongan_lokal_4"}['umk_ambri'];
                        ${"row1_data_explode_hasil_kerja_4"} = ${"row1_explode_hasil_kerja_4"}[1];
                        ${"row1_explode_lembur_4"} = explode("|",$row1_pengerjaan['lembur']);
                        ${"row1_explode_status_lembur_4"} = explode("-",${"row1_explode_lembur_4"}[4]);
                        if(${"row1_explode_status_lembur_4"}[1] == 'y'){
                            ${"row1_lembur_4"} = 1.5;
                        }else{
                            ${"row1_lembur_4"} = 1;
                        }
                    }
                    // End Hasil Kerja 4

                    // Hasil Kerja 5
                    ${"row1_explode_hasil_kerja_5"} = explode("|",$row1_pengerjaan['hasil_kerja_5']);
                    ${"row1_umk_borongan_lokal_5"} = $this->umkBoronganAmbri->select('id','jenis_produk','umk_etiket','umk_las_tepi','umk_las_pojok','umk_ambri')
                                                    ->where('id',${"row1_explode_hasil_kerja_5"}[0])
                                                    ->first();
                    if(empty(${"row1_umk_borongan_lokal_5"})){
                        ${"row1_jenis_produk_5"} = '-';
                        ${"row1_hasil_kerja_5"} = null;
                        ${"row1_data_explode_hasil_kerja_5"} = '-';
                        ${"row1_lembur_5"} = 1;
                        ${"row1_total_hasil_5"} = 0;
                    }else{
                        ${"row1_jenis_produk_5"} = ${"row1_umk_borongan_lokal_5"}['jenis_produk'];
                        ${"row1_hasil_kerja_5"} = ${"row1_explode_hasil_kerja_5"}[1]*${"row1_umk_borongan_lokal_5"}['umk_ambri'];
                        ${"row1_data_explode_hasil_kerja_5"} = ${"row1_explode_hasil_kerja_5"}[1];
                        ${"row1_explode_lembur_5"} = explode("|",$row1_pengerjaan['lembur']);
                        ${"row1_explode_status_lembur_5"} = explode("-",${"row1_explode_lembur_5"}[5]);
                        if(${"row1_explode_status_lembur_5"}[1] == 'y'){
                            ${"row1_lembur_5"} = 1.5;
                        }else{
                            ${"row1_lembur_5"} = 1;
                        }
                    }
                    // End Hasil Kerja 5
                }

                if ($row1_pengerjaan->operator_karyawan->jenis_operator_detail_pekerjaan_id == 25) {
                    // Hasil Kerja 1
                    ${"row1_explode_hasil_kerja_1"} = explode("|",$row1_pengerjaan['hasil_kerja_1']);
                    ${"row1_umk_borongan_lokal_1"} = $this->umkBoronganStempel->select('id','jenis_produk','nominal_umk')
                                                    ->where('id',${"row1_explode_hasil_kerja_1"}[0])
                                                    ->first();
                    if(empty(${"row1_umk_borongan_lokal_1"})){
                        ${"row1_jenis_produk_1"} = '-';
                        ${"row1_hasil_kerja_1"} = null;
                        ${"row1_data_explode_hasil_kerja_1"} = '-';
                        ${"row1_lembur_1"} = 1;
                        ${"row1_total_hasil_1"} = 0;
                    }else{
                        ${"row1_jenis_produk_1"} = ${"row1_umk_borongan_lokal_1"}['jenis_produk'];
                        ${"row1_hasil_kerja_1"} = ${"row1_explode_hasil_kerja_1"}[1]*${"row1_umk_borongan_lokal_1"}['nominal_umk'];
                        ${"row1_data_explode_hasil_kerja_1"} = ${"row1_explode_hasil_kerja_1"}[1];
                        ${"row1_explode_lembur_1"} = explode("|",$row1_pengerjaan['lembur']);
                        ${"row1_explode_status_lembur_1"} = explode("-",${"row1_explode_lembur_1"}[1]);
                        if(${"row1_explode_status_lembur_1"}[1] == 'y'){
                            ${"row1_lembur_1"} = 1.5;
                        }else{
                            ${"row1_lembur_1"} = 1;
                        }
                    }
                    // End Hasil Kerja 1

                    // Hasil Kerja 2
                    ${"row1_explode_hasil_kerja_2"} = explode("|",$row1_pengerjaan['hasil_kerja_2']);
                    ${"row1_umk_borongan_lokal_2"} = $this->umkBoronganStempel->select('id','jenis_produk','nominal_umk')
                                                    ->where('id',${"row1_explode_hasil_kerja_2"}[0])
                                                    ->first();
                    if(empty(${"row1_umk_borongan_lokal_2"})){
                        ${"row1_jenis_produk_2"} = '-';
                        ${"row1_hasil_kerja_2"} = null;
                        ${"row1_data_explode_hasil_kerja_2"} = '-';
                        ${"row1_lembur_2"} = 1;
                        ${"row1_total_hasil_2"} = 0;
                    }else{
                        ${"row1_jenis_produk_2"} = ${"row1_umk_borongan_lokal_2"}['jenis_produk'];
                        ${"row1_hasil_kerja_2"} = ${"row1_explode_hasil_kerja_2"}[1]*${"row1_umk_borongan_lokal_2"}['nominal_umk'];
                        ${"row1_data_explode_hasil_kerja_2"} = ${"row1_explode_hasil_kerja_2"}[1];
                        ${"row1_explode_lembur_2"} = explode("|",$row1_pengerjaan['lembur']);
                        ${"row1_explode_status_lembur_2"} = explode("-",${"row1_explode_lembur_2"}[2]);
                        if(${"row1_explode_status_lembur_2"}[1] == 'y'){
                            ${"row1_lembur_2"} = 1.5;
                        }else{
                            ${"row1_lembur_2"} = 1;
                        }
                    }
                    // End Hasil Kerja 2
                    
                    // Hasil Kerja 3
                    ${"row1_explode_hasil_kerja_3"} = explode("|",$row1_pengerjaan['hasil_kerja_3']);
                    ${"row1_umk_borongan_lokal_3"} = $this->umkBoronganStempel->select('id','jenis_produk','nominal_umk')
                                                    ->where('id',${"row1_explode_hasil_kerja_3"}[0])
                                                    ->first();
                    if(empty(${"row1_umk_borongan_lokal_3"})){
                        ${"row1_jenis_produk_3"} = '-';
                        ${"row1_hasil_kerja_3"} = null;
                        ${"row1_data_explode_hasil_kerja_3"} = '-';
                        ${"row1_lembur_3"} = 1;
                        ${"row1_total_hasil_3"} = 0;
                    }else{
                        ${"row1_jenis_produk_3"} = ${"row1_umk_borongan_lokal_3"}['jenis_produk'];
                        ${"row1_hasil_kerja_3"} = ${"row1_explode_hasil_kerja_3"}[1]*${"row1_umk_borongan_lokal_3"}['nominal_umk'];
                        ${"row1_data_explode_hasil_kerja_3"} = ${"row1_explode_hasil_kerja_3"}[1];
                        ${"row1_explode_lembur_3"} = explode("|",$row1_pengerjaan['lembur']);
                        ${"row1_explode_status_lembur_3"} = explode("-",${"row1_explode_lembur_3"}[3]);
                        if(${"row1_explode_status_lembur_3"}[1] == 'y'){
                            ${"row1_lembur_3"} = 1.5;
                        }else{
                            ${"row1_lembur_3"} = 1;
                        }
                    }
                    // End Hasil Kerja 3

                    // Hasil Kerja 4
                    ${"row1_explode_hasil_kerja_4"} = explode("|",$row1_pengerjaan['hasil_kerja_4']);
                    ${"row1_umk_borongan_lokal_4"} = $this->umkBoronganStempel->select('id','jenis_produk','nominal_umk')
                                                    ->where('id',${"row1_explode_hasil_kerja_4"}[0])
                                                    ->first();
                    if(empty(${"row1_umk_borongan_lokal_4"})){
                        ${"row1_jenis_produk_4"} = '-';
                        ${"row1_hasil_kerja_4"} = null;
                        ${"row1_data_explode_hasil_kerja_4"} = '-';
                        ${"row1_lembur_4"} = 1;
                        ${"row1_total_hasil_4"} = 0;
                    }else{
                        ${"row1_jenis_produk_4"} = ${"row1_umk_borongan_lokal_4"}['jenis_produk'];
                        ${"row1_hasil_kerja_4"} = ${"row1_explode_hasil_kerja_4"}[1]*${"row1_umk_borongan_lokal_4"}['nominal_umk'];
                        ${"row1_data_explode_hasil_kerja_4"} = ${"row1_explode_hasil_kerja_4"}[1];
                        ${"row1_explode_lembur_4"} = explode("|",$row1_pengerjaan['lembur']);
                        ${"row1_explode_status_lembur_4"} = explode("-",${"row1_explode_lembur_4"}[4]);
                        if(${"row1_explode_status_lembur_4"}[1] == 'y'){
                            ${"row1_lembur_4"} = 1.5;
                        }else{
                            ${"row1_lembur_4"} = 1;
                        }
                    }
                    // End Hasil Kerja 4

                    // Hasil Kerja 5
                    ${"row1_explode_hasil_kerja_5"} = explode("|",$row1_pengerjaan['hasil_kerja_5']);
                    ${"row1_umk_borongan_lokal_5"} = $this->umkBoronganStempel->select('id','jenis_produk','nominal_umk')
                                                    ->where('id',${"row1_explode_hasil_kerja_5"}[0])
                                                    ->first();
                    if(empty(${"row1_umk_borongan_lokal_5"})){
                        ${"row1_jenis_produk_5"} = '-';
                        ${"row1_hasil_kerja_5"} = null;
                        ${"row1_data_explode_hasil_kerja_5"} = '-';
                        ${"row1_lembur_5"} = 1;
                        ${"row1_total_hasil_5"} = 0;
                    }else{
                        ${"row1_jenis_produk_5"} = ${"row1_umk_borongan_lokal_5"}['jenis_produk'];
                        ${"row1_hasil_kerja_5"} = ${"row1_explode_hasil_kerja_5"}[1]*${"row1_umk_borongan_lokal_5"}['nominal_umk'];
                        ${"row1_data_explode_hasil_kerja_5"} = ${"row1_explode_hasil_kerja_5"}[1];
                        ${"row1_explode_lembur_5"} = explode("|",$row1_pengerjaan['lembur']);
                        ${"row1_explode_status_lembur_5"} = explode("-",${"row1_explode_lembur_5"}[5]);
                        if(${"row1_explode_status_lembur_5"}[1] == 'y'){
                            ${"row1_lembur_5"} = 1.5;
                        }else{
                            ${"row1_lembur_5"} = 1;
                        }
                    }
                    // End Hasil Kerja 5
                }

                // $row1_total_hasil_kerja = (round(($row1_hasil_kerja_1*$row1_lembur_1)+($row1_hasil_kerja_2*$row1_lembur_2)+($row1_hasil_kerja_3*$row1_lembur_3)+($row1_hasil_kerja_4*$row1_lembur_4)+($row1_hasil_kerja_5*$row1_lembur_5)));
                $row1_total_hasil_kerja = (round(($row1_hasil_kerja_1*$row1_lembur_1)+($row1_hasil_kerja_2*$row1_lembur_2)+($row1_hasil_kerja_3*$row1_lembur_3)+($row1_hasil_kerja_4*$row1_lembur_4)+($row1_hasil_kerja_5*$row1_lembur_5)))-$row1_pengerjaan['uang_lembur'];
                // $row1_total_hasil_kerja = (($row1_hasil_kerja_1*$row1_lembur_1)+($row1_hasil_kerja_2*$row1_lembur_2)+($row1_hasil_kerja_3*$row1_lembur_3)+($row1_hasil_kerja_4*$row1_lembur_4)+($row1_hasil_kerja_5*$row1_lembur_5))-$row1_pengerjaan['uang_lembur'];
                $row1_total_lembur = $row1_pengerjaan['uang_lembur'];
                array_push($row1_total_upah_hasil_kerja,$row1_total_hasil_kerja);
                array_push($row1_total_lembur_kerja,$row1_total_lembur);
                
                if ($data['new_data_pengerjaan']['akhir_bulan'] == 'y') {
                    if (empty($query_weeklys[$i]['tunjangan_kerja'])) {
                        $row1_tunjangan_kerja = 0;
                    }else{
                        $row1_tunjangan_kerja = $query_weeklys[$i]['tunjangan_kerja'];
                    }
                }else{
                    $row1_tunjangan_kerja = 0;
                }

                if (empty($query_weeklys[$i]['tunjangan_kehadiran'])) {
                    $row1_tunjangan_kehadiran = 0;
                }else{
                    $row1_tunjangan_kehadiran = $query_weeklys[$i]['tunjangan_kehadiran'];
                }

                if (empty($query_weeklys[$i]['uang_makan'])) {
                    $row1_uang_makan = 0;
                }else{
                    $row1_uang_makan = $query_weeklys[$i]['uang_makan'];
                }

                if (empty($query_weeklys[$i]['plus_1'])) {
                    $row1_plus_1 = 0;
                    $row1_ket_plus_1 = null;
                }else{
                    $row1_explode_plus_1 = explode("|",$query_weeklys[$i]['plus_1']);
                    $row1_plus_1 = floatval($row1_explode_plus_1[0]);
                    $row1_ket_plus_1 = $row1_explode_plus_1[1];
                }

                if (empty($query_weeklys[$i]['plus_2'])) {
                    $row1_plus_2 = 0;
                    $row1_ket_plus_2 = null;
                }else{
                    $row1_explode_plus_2 = explode("|",$query_weeklys[$i]['plus_2']);
                    $row1_plus_2 = floatval($row1_explode_plus_2[0]);
                    $row1_ket_plus_2 = $row1_explode_plus_2[1];
                }

                if (empty($query_weeklys[$i]['plus_3'])) {
                    $row1_plus_3 = 0;
                    $row1_ket_plus_3 = null;
                }else{
                    $row1_explode_plus_3 = explode("|",$query_weeklys[$i]['plus_3']);
                    $row1_plus_3 = floatval($row1_explode_plus_3[0]);
                    $row1_ket_plus_3 = $row1_explode_plus_3[1];
                }

                if (empty($query_weeklys[$i]['jht'])) {
                    $row1_jht = 0;
                }else{
                    $row1_jht = $query_weeklys[$i]['jht'];
                }

                if (empty($query_weeklys[$i]['bpjs_kesehatan'])) {
                    $row1_bpjs_kesehatan = 0;
                }else{
                    $row1_bpjs_kesehatan = $query_weeklys[$i]['bpjs_kesehatan'];
                }

                if (empty($query_weeklys[$i]['minus_1'])) {
                    $row1_minus_1 = '0';
                    $row1_ket_minus_1 = null;
                }else{
                    $row1_explode_minus_1 = explode("|",$query_weeklys[$i]['minus_1']);
                    $row1_minus_1 = floatval($row1_explode_minus_1[0]);
                    $row1_ket_minus_1 = $row1_explode_minus_1[1];
                }

                if (empty($query_weeklys[$i]['minus_2'])) {
                    $row1_minus_2 = 0;
                    $row1_ket_minus_2 = null;
                }else{
                    $row1_explode_minus_2 = explode("|",$query_weeklys[$i]['minus_2']);
                    $row1_minus_2 = floatval($row1_explode_minus_2[0]);
                    $row1_ket_minus_2 = $row1_explode_minus_2[1];
                }

                $row1_total_gaji_diterima = (array_sum($row1_total_upah_hasil_kerja)
                                            +array_sum($row1_total_lembur_kerja)
                                            +$row1_tunjangan_kerja
                                            +$row1_tunjangan_kehadiran
                                            +$row1_uang_makan
                                            +$row1_plus_1
                                            +$row1_plus_2
                                            +$row1_plus_3
                                            )
                                            -
                                            ($row1_jht+$row1_bpjs_kesehatan+$row1_minus_1+$row1_minus_2)
                                            ;

                #################endrow 1
                #################row 2
                $id_selanjutnya = $query_weeklys[$i]['id']+1;
                $row2_weekly = PengerjaanWeekly::select([
                                                'pengerjaan_weekly.id as id',
                                                'pengerjaan_weekly.operator_karyawan_id as operator_karyawan_id',
                                                'jenis_operator_detail_pekerjaan.jenis_posisi_pekerjaan as jenis_posisi_pekerjaan',
                                                'operator_karyawan.nik as nik',
                                                'biodata_karyawan.nama as nama',
                                                'pengerjaan_weekly.upah_dasar as upah_dasar',
                                                'pengerjaan_weekly.tunjangan_kerja as tunjangan_kerja',
                                                'pengerjaan_weekly.tunjangan_kehadiran as tunjangan_kehadiran',
                                                'pengerjaan_weekly.uang_makan as uang_makan',
                                                'pengerjaan_weekly.plus_1 as plus_1',
                                                'pengerjaan_weekly.plus_2 as plus_2',
                                                'pengerjaan_weekly.plus_3 as plus_3',
                                                'pengerjaan_weekly.minus_1 as minus_1',
                                                'pengerjaan_weekly.minus_2 as minus_2',
                                                'pengerjaan_weekly.jht as jht',
                                                'pengerjaan_weekly.bpjs_kesehatan as bpjs_kesehatan',
                                                'operator_karyawan.jenis_operator_detail_pekerjaan_id as jenis_operator_detail_pekerjaan_id'
                                            ])
                                            ->leftJoin('operator_karyawan','operator_karyawan.id','=','pengerjaan_weekly.operator_karyawan_id')
                                            ->leftJoin('itic_emp_new.biodata_karyawan','biodata_karyawan.nik','=','operator_karyawan.nik')
                                            ->leftJoin('jenis_operator_detail_pekerjaan','jenis_operator_detail_pekerjaan.id','=','operator_karyawan.jenis_operator_detail_pekerjaan_id')
                                            ->where('pengerjaan_weekly.id',$id_selanjutnya)
                                            ->where('pengerjaan_weekly.kode_pengerjaan',$kode_pengerjaan)
                                            ->orderBy('operator_karyawan.jenis_operator_detail_pekerjaan_id','asc')
                                            ->orderBy('biodata_karyawan.nama','asc')
                                            ->first();
                if (empty($row2_weekly)) {
                    $row2_nik = null;
                    $row2_operator_karyawan_id = null;
                    $row2_jenis_posisi_pekerjaan = 0;
                }else{
                    $row2_nik = $row2_weekly->nik;
                    $row2_operator_karyawan_id = $row2_weekly->operator_karyawan_id;
                    $row2_jenis_posisi_pekerjaan = $row2_weekly->jenis_posisi_pekerjaan;
                }

                $row2_nama = BiodataKaryawan::where('nik',$row2_nik)->orderBy('nama','asc')->first();
                if (empty($row2_nama)) {
                    $row2_nama_2 = null;
                    $row2_nik = null;
                }else{
                    $row2_nama_2 = $row2_nama->nama;
                    $row2_nik = $row2_nama->nik;
                }
                
                $row2_total_upah_hasil_kerja = [];
                $row2_total_lembur_kerja = [];
                $row2_pengerjaans = Pengerjaan::where('operator_karyawan_id',$row2_operator_karyawan_id)
                                                ->where('kode_pengerjaan',$kode_pengerjaan)
                                                ->get();
                foreach ($row2_pengerjaans as $key_row2_pengerjaan => $row2_pengerjaan) {
                    // Borongan Packing
                    if ($row2_pengerjaan->operator_karyawan->jenis_operator_detail_pekerjaan_id == 1) {
                        // Hasil Kerja 1
                        ${"row2_explode_hasil_kerja_1"} = explode("|",$row2_pengerjaan['hasil_kerja_1']);
                        ${"row2_umk_borongan_lokal_1"} = $this->umkBoronganLokal->select('id','jenis_produk','umk_packing','umk_bandrol','umk_inner','umk_outer')
                                                        ->where('id',${"row2_explode_hasil_kerja_1"}[0])
                                                        ->first();
                        if(empty(${"row2_umk_borongan_lokal_1"})){
                            ${"row2_jenis_produk_1"} = '-';
                            ${"row2_hasil_kerja_1"} = null;
                            ${"row2_data_explode_hasil_kerja_1"} = '-';
                            ${"row2_lembur_1"} = 1;
                            ${"row2_total_hasil_1"} = 0;
                        }else{
                            ${"row2_jenis_produk_1"} = ${"row2_umk_borongan_lokal_1"}['jenis_produk'];
                            ${"row2_hasil_kerja_1"} = ${"row2_explode_hasil_kerja_1"}[1]*${"row2_umk_borongan_lokal_1"}['umk_packing'];
                            ${"row2_data_explode_hasil_kerja_1"} = ${"row2_explode_hasil_kerja_1"}[1];
                            ${"row2_explode_lembur_1"} = explode("|",$row2_pengerjaan['lembur']);
                            ${"row2_explode_status_lembur_1"} = explode("-",${"row2_explode_lembur_1"}[1]);
                            if(${"row2_explode_status_lembur_1"}[1] == 'y'){
                                ${"row2_lembur_1"} = 1.5;
                            }else{
                                ${"row2_lembur_1"} = 1;
                            }
                        }
                        // End Hasil Kerja 1
    
                        // Hasil Kerja 2
                        ${"row2_explode_hasil_kerja_2"} = explode("|",$row2_pengerjaan['hasil_kerja_2']);
                        ${"row2_umk_borongan_lokal_2"} = $this->umkBoronganLokal->select('id','jenis_produk','umk_packing','umk_bandrol','umk_inner','umk_outer')
                                                        ->where('id',${"row2_explode_hasil_kerja_2"}[0])
                                                        ->first();
                        if(empty(${"row2_umk_borongan_lokal_2"})){
                            ${"row2_jenis_produk_2"} = '-';
                            ${"row2_hasil_kerja_2"} = null;
                            ${"row2_data_explode_hasil_kerja_2"} = '-';
                            ${"row2_lembur_2"} = 1;
                            ${"row2_total_hasil_2"} = 0;
                        }else{
                            ${"row2_jenis_produk_2"} = ${"row2_umk_borongan_lokal_2"}['jenis_produk'];
                            ${"row2_hasil_kerja_2"} = ${"row2_explode_hasil_kerja_2"}[1]*${"row2_umk_borongan_lokal_2"}['umk_packing'];
                            ${"row2_data_explode_hasil_kerja_2"} = ${"row2_explode_hasil_kerja_2"}[1];
                            ${"row2_explode_lembur_2"} = explode("|",$row2_pengerjaan['lembur']);
                            ${"row2_explode_status_lembur_2"} = explode("-",${"row2_explode_lembur_2"}[2]);
                            if(${"row2_explode_status_lembur_2"}[1] == 'y'){
                                ${"row2_lembur_2"} = 1.5;
                            }else{
                                ${"row2_lembur_2"} = 1;
                            }
                        }
                        // End Hasil Kerja 2
                        
                        // Hasil Kerja 3
                        ${"row2_explode_hasil_kerja_3"} = explode("|",$row2_pengerjaan['hasil_kerja_3']);
                        ${"row2_umk_borongan_lokal_3"} = $this->umkBoronganLokal->select('id','jenis_produk','umk_packing','umk_bandrol','umk_inner','umk_outer')
                                                        ->where('id',${"row2_explode_hasil_kerja_3"}[0])
                                                        ->first();
                        if(empty(${"row2_umk_borongan_lokal_3"})){
                            ${"row2_jenis_produk_3"} = '-';
                            ${"row2_hasil_kerja_3"} = null;
                            ${"row2_data_explode_hasil_kerja_3"} = '-';
                            ${"row2_lembur_3"} = 1;
                            ${"row2_total_hasil_3"} = 0;
                        }else{
                            ${"row2_jenis_produk_3"} = ${"row2_umk_borongan_lokal_3"}['jenis_produk'];
                            ${"row2_hasil_kerja_3"} = ${"row2_explode_hasil_kerja_3"}[1]*${"row2_umk_borongan_lokal_3"}['umk_packing'];
                            ${"row2_data_explode_hasil_kerja_3"} = ${"row2_explode_hasil_kerja_3"}[1];
                            ${"row2_explode_lembur_3"} = explode("|",$row2_pengerjaan['lembur']);
                            ${"row2_explode_status_lembur_3"} = explode("-",${"row2_explode_lembur_3"}[3]);
                            if(${"row2_explode_status_lembur_3"}[1] == 'y'){
                                ${"row2_lembur_3"} = 1.5;
                            }else{
                                ${"row2_lembur_3"} = 1;
                            }
                        }
                        // End Hasil Kerja 3
    
                        // Hasil Kerja 4
                        ${"row2_explode_hasil_kerja_4"} = explode("|",$row2_pengerjaan['hasil_kerja_4']);
                        ${"row2_umk_borongan_lokal_4"} = $this->umkBoronganLokal->select('id','jenis_produk','umk_packing','umk_bandrol','umk_inner','umk_outer')
                                                        ->where('id',${"row2_explode_hasil_kerja_4"}[0])
                                                        ->first();
                        if(empty(${"row2_umk_borongan_lokal_4"})){
                            ${"row2_jenis_produk_4"} = '-';
                            ${"row2_hasil_kerja_4"} = null;
                            ${"row2_data_explode_hasil_kerja_4"} = '-';
                            ${"row2_lembur_4"} = 1;
                            ${"row2_total_hasil_4"} = 0;
                        }else{
                            ${"row2_jenis_produk_4"} = ${"row2_umk_borongan_lokal_4"}['jenis_produk'];
                            ${"row2_hasil_kerja_4"} = ${"row2_explode_hasil_kerja_4"}[1]*${"row2_umk_borongan_lokal_4"}['umk_packing'];
                            ${"row2_data_explode_hasil_kerja_4"} = ${"row2_explode_hasil_kerja_4"}[1];
                            ${"row2_explode_lembur_4"} = explode("|",$row2_pengerjaan['lembur']);
                            ${"row2_explode_status_lembur_4"} = explode("-",${"row2_explode_lembur_4"}[4]);
                            if(${"row2_explode_status_lembur_4"}[1] == 'y'){
                                ${"row2_lembur_4"} = 1.5;
                            }else{
                                ${"row2_lembur_4"} = 1;
                            }
                        }
                        // End Hasil Kerja 4
    
                        // Hasil Kerja 5
                        ${"row2_explode_hasil_kerja_5"} = explode("|",$row2_pengerjaan['hasil_kerja_5']);
                        ${"row2_umk_borongan_lokal_5"} = $this->umkBoronganLokal->select('id','jenis_produk','umk_packing','umk_bandrol','umk_inner','umk_outer')
                                                        ->where('id',${"row2_explode_hasil_kerja_5"}[0])
                                                        ->first();
                        if(empty(${"row2_umk_borongan_lokal_5"})){
                            ${"row2_jenis_produk_5"} = '-';
                            ${"row2_hasil_kerja_5"} = null;
                            ${"row2_data_explode_hasil_kerja_5"} = '-';
                            ${"row2_lembur_5"} = 1;
                            ${"row2_total_hasil_5"} = 0;
                        }else{
                            ${"row2_jenis_produk_5"} = ${"row2_umk_borongan_lokal_5"}['jenis_produk'];
                            ${"row2_hasil_kerja_5"} = ${"row2_explode_hasil_kerja_5"}[1]*${"row2_umk_borongan_lokal_5"}['umk_packing'];
                            ${"row2_data_explode_hasil_kerja_5"} = ${"row2_explode_hasil_kerja_5"}[1];
                            ${"row2_explode_lembur_5"} = explode("|",$row2_pengerjaan['lembur']);
                            ${"row2_explode_status_lembur_5"} = explode("-",${"row2_explode_lembur_5"}[5]);
                            if(${"row2_explode_status_lembur_5"}[1] == 'y'){
                                ${"row2_lembur_5"} = 1.5;
                            }else{
                                ${"row2_lembur_5"} = 1;
                            }
                        }
                        // End Hasil Kerja 5
                    }
                    
                    if ($row2_pengerjaan->operator_karyawan->jenis_operator_detail_pekerjaan_id == 2) {
                        // Hasil Kerja 1
                        ${"row2_explode_hasil_kerja_1"} = explode("|",$row2_pengerjaan['hasil_kerja_1']);
                        ${"row2_umk_borongan_lokal_1"} = $this->umkBoronganLokal->select('id','jenis_produk','umk_packing','umk_bandrol','umk_inner','umk_outer')
                                                        ->where('id',${"row2_explode_hasil_kerja_1"}[0])
                                                        ->first();
                        if(empty(${"row2_umk_borongan_lokal_1"})){
                            ${"row2_jenis_produk_1"} = '-';
                            ${"row2_hasil_kerja_1"} = null;
                            ${"row2_data_explode_hasil_kerja_1"} = '-';
                            ${"row2_lembur_1"} = 1;
                            ${"row2_total_hasil_1"} = 0;
                        }else{
                            ${"row2_jenis_produk_1"} = ${"row2_umk_borongan_lokal_1"}['jenis_produk'];
                            ${"row2_hasil_kerja_1"} = ${"row2_explode_hasil_kerja_1"}[1]*${"row2_umk_borongan_lokal_1"}['umk_bandrol'];
                            ${"row2_data_explode_hasil_kerja_1"} = ${"row2_explode_hasil_kerja_1"}[1];
                            ${"row2_explode_lembur_1"} = explode("|",$row2_pengerjaan['lembur']);
                            ${"row2_explode_status_lembur_1"} = explode("-",${"row2_explode_lembur_1"}[1]);
                            if(${"row2_explode_status_lembur_1"}[1] == 'y'){
                                ${"row2_lembur_1"} = 1.5;
                            }else{
                                ${"row2_lembur_1"} = 1;
                            }
                        }
                        // End Hasil Kerja 1
    
                        // Hasil Kerja 2
                        ${"row2_explode_hasil_kerja_2"} = explode("|",$row2_pengerjaan['hasil_kerja_2']);
                        ${"row2_umk_borongan_lokal_2"} = $this->umkBoronganLokal->select('id','jenis_produk','umk_packing','umk_bandrol','umk_inner','umk_outer')
                                                        ->where('id',${"row2_explode_hasil_kerja_2"}[0])
                                                        ->first();
                        if(empty(${"row2_umk_borongan_lokal_2"})){
                            ${"row2_jenis_produk_2"} = '-';
                            ${"row2_hasil_kerja_2"} = null;
                            ${"row2_data_explode_hasil_kerja_2"} = '-';
                            ${"row2_lembur_2"} = 1;
                            ${"row2_total_hasil_2"} = 0;
                        }else{
                            ${"row2_jenis_produk_2"} = ${"row2_umk_borongan_lokal_2"}['jenis_produk'];
                            ${"row2_hasil_kerja_2"} = ${"row2_explode_hasil_kerja_2"}[1]*${"row2_umk_borongan_lokal_2"}['umk_bandrol'];
                            ${"row2_data_explode_hasil_kerja_2"} = ${"row2_explode_hasil_kerja_2"}[1];
                            ${"row2_explode_lembur_2"} = explode("|",$row2_pengerjaan['lembur']);
                            ${"row2_explode_status_lembur_2"} = explode("-",${"row2_explode_lembur_2"}[2]);
                            if(${"row2_explode_status_lembur_2"}[1] == 'y'){
                                ${"row2_lembur_2"} = 1.5;
                            }else{
                                ${"row2_lembur_2"} = 1;
                            }
                        }
                        // End Hasil Kerja 2
                        
                        // Hasil Kerja 3
                        ${"row2_explode_hasil_kerja_3"} = explode("|",$row2_pengerjaan['hasil_kerja_3']);
                        ${"row2_umk_borongan_lokal_3"} = $this->umkBoronganLokal->select('id','jenis_produk','umk_packing','umk_bandrol','umk_inner','umk_outer')
                                                        ->where('id',${"row2_explode_hasil_kerja_3"}[0])
                                                        ->first();
                        if(empty(${"row2_umk_borongan_lokal_3"})){
                            ${"row2_jenis_produk_3"} = '-';
                            ${"row2_hasil_kerja_3"} = null;
                            ${"row2_data_explode_hasil_kerja_3"} = '-';
                            ${"row2_lembur_3"} = 1;
                            ${"row2_total_hasil_3"} = 0;
                        }else{
                            ${"row2_jenis_produk_3"} = ${"row2_umk_borongan_lokal_3"}['jenis_produk'];
                            ${"row2_hasil_kerja_3"} = ${"row2_explode_hasil_kerja_3"}[1]*${"row2_umk_borongan_lokal_3"}['umk_bandrol'];
                            ${"row2_data_explode_hasil_kerja_3"} = ${"row2_explode_hasil_kerja_3"}[1];
                            ${"row2_explode_lembur_3"} = explode("|",$row2_pengerjaan['lembur']);
                            ${"row2_explode_status_lembur_3"} = explode("-",${"row2_explode_lembur_3"}[3]);
                            if(${"row2_explode_status_lembur_3"}[1] == 'y'){
                                ${"row2_lembur_3"} = 1.5;
                            }else{
                                ${"row2_lembur_3"} = 1;
                            }
                        }
                        // End Hasil Kerja 3
    
                        // Hasil Kerja 4
                        ${"row2_explode_hasil_kerja_4"} = explode("|",$row2_pengerjaan['hasil_kerja_4']);
                        ${"row2_umk_borongan_lokal_4"} = $this->umkBoronganLokal->select('id','jenis_produk','umk_packing','umk_bandrol','umk_inner','umk_outer')
                                                        ->where('id',${"row2_explode_hasil_kerja_4"}[0])
                                                        ->first();
                        if(empty(${"row2_umk_borongan_lokal_4"})){
                            ${"row2_jenis_produk_4"} = '-';
                            ${"row2_hasil_kerja_4"} = null;
                            ${"row2_data_explode_hasil_kerja_4"} = '-';
                            ${"row2_lembur_4"} = 1;
                            ${"row2_total_hasil_4"} = 0;
                        }else{
                            ${"row2_jenis_produk_4"} = ${"row2_umk_borongan_lokal_4"}['jenis_produk'];
                            ${"row2_hasil_kerja_4"} = ${"row2_explode_hasil_kerja_4"}[1]*${"row2_umk_borongan_lokal_4"}['umk_bandrol'];
                            ${"row2_data_explode_hasil_kerja_4"} = ${"row2_explode_hasil_kerja_4"}[1];
                            ${"row2_explode_lembur_4"} = explode("|",$row2_pengerjaan['lembur']);
                            ${"row2_explode_status_lembur_4"} = explode("-",${"row2_explode_lembur_4"}[4]);
                            if(${"row2_explode_status_lembur_4"}[1] == 'y'){
                                ${"row2_lembur_4"} = 1.5;
                            }else{
                                ${"row2_lembur_4"} = 1;
                            }
                        }
                        // End Hasil Kerja 4
    
                        // Hasil Kerja 5
                        ${"row2_explode_hasil_kerja_5"} = explode("|",$row2_pengerjaan['hasil_kerja_5']);
                        ${"row2_umk_borongan_lokal_5"} = $this->umkBoronganLokal->select('id','jenis_produk','umk_packing','umk_bandrol','umk_inner','umk_outer')
                                                        ->where('id',${"row2_explode_hasil_kerja_5"}[0])
                                                        ->first();
                        if(empty(${"row2_umk_borongan_lokal_5"})){
                            ${"row2_jenis_produk_5"} = '-';
                            ${"row2_hasil_kerja_5"} = null;
                            ${"row2_data_explode_hasil_kerja_5"} = '-';
                            ${"row2_lembur_5"} = 1;
                            ${"row2_total_hasil_5"} = 0;
                        }else{
                            ${"row2_jenis_produk_5"} = ${"row2_umk_borongan_lokal_5"}['jenis_produk'];
                            ${"row2_hasil_kerja_5"} = ${"row2_explode_hasil_kerja_5"}[1]*${"row2_umk_borongan_lokal_5"}['umk_bandrol'];
                            ${"row2_data_explode_hasil_kerja_5"} = ${"row2_explode_hasil_kerja_5"}[1];
                            ${"row2_explode_lembur_5"} = explode("|",$row2_pengerjaan['lembur']);
                            ${"row2_explode_status_lembur_5"} = explode("-",${"row2_explode_lembur_5"}[5]);
                            if(${"row2_explode_status_lembur_5"}[1] == 'y'){
                                ${"row2_lembur_5"} = 1.5;
                            }else{
                                ${"row2_lembur_5"} = 1;
                            }
                        }
                        // End Hasil Kerja 5
                    }
    
                    if ($row2_pengerjaan->operator_karyawan->jenis_operator_detail_pekerjaan_id == 3) {
                        // Hasil Kerja 1
                        ${"row2_explode_hasil_kerja_1"} = explode("|",$row2_pengerjaan['hasil_kerja_1']);
                        ${"row2_umk_borongan_lokal_1"} = $this->umkBoronganLokal->select('id','jenis_produk','umk_packing','umk_bandrol','umk_inner','umk_outer')
                                                        ->where('id',${"row2_explode_hasil_kerja_1"}[0])
                                                        ->first();
                        if(empty(${"row2_umk_borongan_lokal_1"})){
                            ${"row2_jenis_produk_1"} = '-';
                            ${"row2_hasil_kerja_1"} = null;
                            ${"row2_data_explode_hasil_kerja_1"} = '-';
                            ${"row2_lembur_1"} = 1;
                            ${"row2_total_hasil_1"} = 0;
                        }else{
                            ${"row2_jenis_produk_1"} = ${"row2_umk_borongan_lokal_1"}['jenis_produk'];
                            ${"row2_hasil_kerja_1"} = ${"row2_explode_hasil_kerja_1"}[1]*${"row2_umk_borongan_lokal_1"}['umk_inner'];
                            ${"row2_data_explode_hasil_kerja_1"} = ${"row2_explode_hasil_kerja_1"}[1];
                            ${"row2_explode_lembur_1"} = explode("|",$row2_pengerjaan['lembur']);
                            ${"row2_explode_status_lembur_1"} = explode("-",${"row2_explode_lembur_1"}[1]);
                            if(${"row2_explode_status_lembur_1"}[1] == 'y'){
                                ${"row2_lembur_1"} = 1.5;
                            }else{
                                ${"row2_lembur_1"} = 1;
                            }
                        }
                        // End Hasil Kerja 1
    
                        // Hasil Kerja 2
                        ${"row2_explode_hasil_kerja_2"} = explode("|",$row2_pengerjaan['hasil_kerja_2']);
                        ${"row2_umk_borongan_lokal_2"} = $this->umkBoronganLokal->select('id','jenis_produk','umk_packing','umk_bandrol','umk_inner','umk_outer')
                                                        ->where('id',${"row2_explode_hasil_kerja_2"}[0])
                                                        ->first();
                        if(empty(${"row2_umk_borongan_lokal_2"})){
                            ${"row2_jenis_produk_2"} = '-';
                            ${"row2_hasil_kerja_2"} = null;
                            ${"row2_data_explode_hasil_kerja_2"} = '-';
                            ${"row2_lembur_2"} = 1;
                            ${"row2_total_hasil_2"} = 0;
                        }else{
                            ${"row2_jenis_produk_2"} = ${"row2_umk_borongan_lokal_2"}['jenis_produk'];
                            ${"row2_hasil_kerja_2"} = ${"row2_explode_hasil_kerja_2"}[1]*${"row2_umk_borongan_lokal_2"}['umk_inner'];
                            ${"row2_data_explode_hasil_kerja_2"} = ${"row2_explode_hasil_kerja_2"}[1];
                            ${"row2_explode_lembur_2"} = explode("|",$row2_pengerjaan['lembur']);
                            ${"row2_explode_status_lembur_2"} = explode("-",${"row2_explode_lembur_2"}[2]);
                            if(${"row2_explode_status_lembur_2"}[1] == 'y'){
                                ${"row2_lembur_2"} = 1.5;
                            }else{
                                ${"row2_lembur_2"} = 1;
                            }
                        }
                        // End Hasil Kerja 2
                        
                        // Hasil Kerja 3
                        ${"row2_explode_hasil_kerja_3"} = explode("|",$row2_pengerjaan['hasil_kerja_3']);
                        ${"row2_umk_borongan_lokal_3"} = $this->umkBoronganLokal->select('id','jenis_produk','umk_packing','umk_bandrol','umk_inner','umk_outer')
                                                        ->where('id',${"row2_explode_hasil_kerja_3"}[0])
                                                        ->first();
                        if(empty(${"row2_umk_borongan_lokal_3"})){
                            ${"row2_jenis_produk_3"} = '-';
                            ${"row2_hasil_kerja_3"} = null;
                            ${"row2_data_explode_hasil_kerja_3"} = '-';
                            ${"row2_lembur_3"} = 1;
                            ${"row2_total_hasil_3"} = 0;
                        }else{
                            ${"row2_jenis_produk_3"} = ${"row2_umk_borongan_lokal_3"}['jenis_produk'];
                            ${"row2_hasil_kerja_3"} = ${"row2_explode_hasil_kerja_3"}[1]*${"row2_umk_borongan_lokal_3"}['umk_inner'];
                            ${"row2_data_explode_hasil_kerja_3"} = ${"row2_explode_hasil_kerja_3"}[1];
                            ${"row2_explode_lembur_3"} = explode("|",$row2_pengerjaan['lembur']);
                            ${"row2_explode_status_lembur_3"} = explode("-",${"row2_explode_lembur_3"}[3]);
                            if(${"row2_explode_status_lembur_3"}[1] == 'y'){
                                ${"row2_lembur_3"} = 1.5;
                            }else{
                                ${"row2_lembur_3"} = 1;
                            }
                        }
                        // End Hasil Kerja 3
    
                        // Hasil Kerja 4
                        ${"row2_explode_hasil_kerja_4"} = explode("|",$row2_pengerjaan['hasil_kerja_4']);
                        ${"row2_umk_borongan_lokal_4"} = $this->umkBoronganLokal->select('id','jenis_produk','umk_packing','umk_bandrol','umk_inner','umk_outer')
                                                        ->where('id',${"row2_explode_hasil_kerja_4"}[0])
                                                        ->first();
                        if(empty(${"row2_umk_borongan_lokal_4"})){
                            ${"row2_jenis_produk_4"} = '-';
                            ${"row2_hasil_kerja_4"} = null;
                            ${"row2_data_explode_hasil_kerja_4"} = '-';
                            ${"row2_lembur_4"} = 1;
                            ${"row2_total_hasil_4"} = 0;
                        }else{
                            ${"row2_jenis_produk_4"} = ${"row2_umk_borongan_lokal_4"}['jenis_produk'];
                            ${"row2_hasil_kerja_4"} = ${"row2_explode_hasil_kerja_4"}[1]*${"row2_umk_borongan_lokal_4"}['umk_inner'];
                            ${"row2_data_explode_hasil_kerja_4"} = ${"row2_explode_hasil_kerja_4"}[1];
                            ${"row2_explode_lembur_4"} = explode("|",$row2_pengerjaan['lembur']);
                            ${"row2_explode_status_lembur_4"} = explode("-",${"row2_explode_lembur_4"}[4]);
                            if(${"row2_explode_status_lembur_4"}[1] == 'y'){
                                ${"row2_lembur_4"} = 1.5;
                            }else{
                                ${"row2_lembur_4"} = 1;
                            }
                        }
                        // End Hasil Kerja 4
    
                        // Hasil Kerja 5
                        ${"row2_explode_hasil_kerja_5"} = explode("|",$row2_pengerjaan['hasil_kerja_5']);
                        ${"row2_umk_borongan_lokal_5"} = $this->umkBoronganLokal->select('id','jenis_produk','umk_packing','umk_bandrol','umk_inner','umk_outer')
                                                        ->where('id',${"row2_explode_hasil_kerja_5"}[0])
                                                        ->first();
                        if(empty(${"row2_umk_borongan_lokal_5"})){
                            ${"row2_jenis_produk_5"} = '-';
                            ${"row2_hasil_kerja_5"} = null;
                            ${"row2_data_explode_hasil_kerja_5"} = '-';
                            ${"row2_lembur_5"} = 1;
                            ${"row2_total_hasil_5"} = 0;
                        }else{
                            ${"row2_jenis_produk_5"} = ${"row2_umk_borongan_lokal_5"}['jenis_produk'];
                            ${"row2_hasil_kerja_5"} = ${"row2_explode_hasil_kerja_5"}[1]*${"row2_umk_borongan_lokal_5"}['umk_inner'];
                            ${"row2_data_explode_hasil_kerja_5"} = ${"row2_explode_hasil_kerja_5"}[1];
                            ${"row2_explode_lembur_5"} = explode("|",$row2_pengerjaan['lembur']);
                            ${"row2_explode_status_lembur_5"} = explode("-",${"row2_explode_lembur_5"}[5]);
                            if(${"row2_explode_status_lembur_5"}[1] == 'y'){
                                ${"row2_lembur_5"} = 1.5;
                            }else{
                                ${"row2_lembur_5"} = 1;
                            }
                        }
                        // End Hasil Kerja 5
                    }
    
                    if ($row2_pengerjaan->operator_karyawan->jenis_operator_detail_pekerjaan_id == 4) {
                        // Hasil Kerja 1
                        ${"row2_explode_hasil_kerja_1"} = explode("|",$row2_pengerjaan['hasil_kerja_1']);
                        ${"row2_umk_borongan_lokal_1"} = $this->umkBoronganLokal->select('id','jenis_produk','umk_packing','umk_bandrol','umk_inner','umk_outer')
                                                        ->where('id',${"row2_explode_hasil_kerja_1"}[0])
                                                        ->first();
                        if(empty(${"row2_umk_borongan_lokal_1"})){
                            ${"row2_jenis_produk_1"} = '-';
                            ${"row2_hasil_kerja_1"} = null;
                            ${"row2_data_explode_hasil_kerja_1"} = '-';
                            ${"row2_lembur_1"} = 1;
                            ${"row2_total_hasil_1"} = 0;
                        }else{
                            ${"row2_jenis_produk_1"} = ${"row2_umk_borongan_lokal_1"}['jenis_produk'];
                            ${"row2_hasil_kerja_1"} = ${"row2_explode_hasil_kerja_1"}[1]*${"row2_umk_borongan_lokal_1"}['umk_outer'];
                            ${"row2_data_explode_hasil_kerja_1"} = ${"row2_explode_hasil_kerja_1"}[1];
                            ${"row2_explode_lembur_1"} = explode("|",$row2_pengerjaan['lembur']);
                            ${"row2_explode_status_lembur_1"} = explode("-",${"row2_explode_lembur_1"}[1]);
                            if(${"row2_explode_status_lembur_1"}[1] == 'y'){
                                ${"row2_lembur_1"} = 1.5;
                            }else{
                                ${"row2_lembur_1"} = 1;
                            }
                        }
                        // End Hasil Kerja 1
    
                        // Hasil Kerja 2
                        ${"row2_explode_hasil_kerja_2"} = explode("|",$row2_pengerjaan['hasil_kerja_2']);
                        ${"row2_umk_borongan_lokal_2"} = $this->umkBoronganLokal->select('id','jenis_produk','umk_packing','umk_bandrol','umk_inner','umk_outer')
                                                        ->where('id',${"row2_explode_hasil_kerja_2"}[0])
                                                        ->first();
                        if(empty(${"row2_umk_borongan_lokal_2"})){
                            ${"row2_jenis_produk_2"} = '-';
                            ${"row2_hasil_kerja_2"} = null;
                            ${"row2_data_explode_hasil_kerja_2"} = '-';
                            ${"row2_lembur_2"} = 1;
                            ${"row2_total_hasil_2"} = 0;
                        }else{
                            ${"row2_jenis_produk_2"} = ${"row2_umk_borongan_lokal_2"}['jenis_produk'];
                            ${"row2_hasil_kerja_2"} = ${"row2_explode_hasil_kerja_2"}[1]*${"row2_umk_borongan_lokal_2"}['umk_outer'];
                            ${"row2_data_explode_hasil_kerja_2"} = ${"row2_explode_hasil_kerja_2"}[1];
                            ${"row2_explode_lembur_2"} = explode("|",$row2_pengerjaan['lembur']);
                            ${"row2_explode_status_lembur_2"} = explode("-",${"row2_explode_lembur_2"}[2]);
                            if(${"row2_explode_status_lembur_2"}[1] == 'y'){
                                ${"row2_lembur_2"} = 1.5;
                            }else{
                                ${"row2_lembur_2"} = 1;
                            }
                        }
                        // End Hasil Kerja 2
                        
                        // Hasil Kerja 3
                        ${"row2_explode_hasil_kerja_3"} = explode("|",$row2_pengerjaan['hasil_kerja_3']);
                        ${"row2_umk_borongan_lokal_3"} = $this->umkBoronganLokal->select('id','jenis_produk','umk_packing','umk_bandrol','umk_inner','umk_outer')
                                                        ->where('id',${"row2_explode_hasil_kerja_3"}[0])
                                                        ->first();
                        if(empty(${"row2_umk_borongan_lokal_3"})){
                            ${"row2_jenis_produk_3"} = '-';
                            ${"row2_hasil_kerja_3"} = null;
                            ${"row2_data_explode_hasil_kerja_3"} = '-';
                            ${"row2_lembur_3"} = 1;
                            ${"row2_total_hasil_3"} = 0;
                        }else{
                            ${"row2_jenis_produk_3"} = ${"row2_umk_borongan_lokal_3"}['jenis_produk'];
                            ${"row2_hasil_kerja_3"} = ${"row2_explode_hasil_kerja_3"}[1]*${"row2_umk_borongan_lokal_3"}['umk_outer'];
                            ${"row2_data_explode_hasil_kerja_3"} = ${"row2_explode_hasil_kerja_3"}[1];
                            ${"row2_explode_lembur_3"} = explode("|",$row2_pengerjaan['lembur']);
                            ${"row2_explode_status_lembur_3"} = explode("-",${"row2_explode_lembur_3"}[3]);
                            if(${"row2_explode_status_lembur_3"}[1] == 'y'){
                                ${"row2_lembur_3"} = 1.5;
                            }else{
                                ${"row2_lembur_3"} = 1;
                            }
                        }
                        // End Hasil Kerja 3
    
                        // Hasil Kerja 4
                        ${"row2_explode_hasil_kerja_4"} = explode("|",$row2_pengerjaan['hasil_kerja_4']);
                        ${"row2_umk_borongan_lokal_4"} = $this->umkBoronganLokal->select('id','jenis_produk','umk_packing','umk_bandrol','umk_inner','umk_outer')
                                                        ->where('id',${"row2_explode_hasil_kerja_4"}[0])
                                                        ->first();
                        if(empty(${"row2_umk_borongan_lokal_4"})){
                            ${"row2_jenis_produk_4"} = '-';
                            ${"row2_hasil_kerja_4"} = null;
                            ${"row2_data_explode_hasil_kerja_4"} = '-';
                            ${"row2_lembur_4"} = 1;
                            ${"row2_total_hasil_4"} = 0;
                        }else{
                            ${"row2_jenis_produk_4"} = ${"row2_umk_borongan_lokal_4"}['jenis_produk'];
                            ${"row2_hasil_kerja_4"} = ${"row2_explode_hasil_kerja_4"}[1]*${"row2_umk_borongan_lokal_4"}['umk_outer'];
                            ${"row2_data_explode_hasil_kerja_4"} = ${"row2_explode_hasil_kerja_4"}[1];
                            ${"row2_explode_lembur_4"} = explode("|",$row2_pengerjaan['lembur']);
                            ${"row2_explode_status_lembur_4"} = explode("-",${"row2_explode_lembur_4"}[4]);
                            if(${"row2_explode_status_lembur_4"}[1] == 'y'){
                                ${"row2_lembur_4"} = 1.5;
                            }else{
                                ${"row2_lembur_4"} = 1;
                            }
                        }
                        // End Hasil Kerja 4
    
                        // Hasil Kerja 5
                        ${"row2_explode_hasil_kerja_5"} = explode("|",$row2_pengerjaan['hasil_kerja_5']);
                        ${"row2_umk_borongan_lokal_5"} = $this->umkBoronganLokal->select('id','jenis_produk','umk_packing','umk_bandrol','umk_inner','umk_outer')
                                                        ->where('id',${"row2_explode_hasil_kerja_5"}[0])
                                                        ->first();
                        if(empty(${"row2_umk_borongan_lokal_5"})){
                            ${"row2_jenis_produk_5"} = '-';
                            ${"row2_hasil_kerja_5"} = null;
                            ${"row2_data_explode_hasil_kerja_5"} = '-';
                            ${"row2_lembur_5"} = 1;
                            ${"row2_total_hasil_5"} = 0;
                        }else{
                            ${"row2_jenis_produk_5"} = ${"row2_umk_borongan_lokal_5"}['jenis_produk'];
                            ${"row2_hasil_kerja_5"} = ${"row2_explode_hasil_kerja_5"}[1]*${"row2_umk_borongan_lokal_5"}['umk_outer'];
                            ${"row2_data_explode_hasil_kerja_5"} = ${"row2_explode_hasil_kerja_5"}[1];
                            ${"row2_explode_lembur_5"} = explode("|",$row2_pengerjaan['lembur']);
                            ${"row2_explode_status_lembur_5"} = explode("-",${"row2_explode_lembur_5"}[5]);
                            if(${"row2_explode_status_lembur_5"}[1] == 'y'){
                                ${"row2_lembur_5"} = 1.5;
                            }else{
                                ${"row2_lembur_5"} = 1;
                            }
                        }
                        // End Hasil Kerja 5
                    }

                    // Export
                    if ($row2_pengerjaan->operator_karyawan->jenis_operator_detail_pekerjaan_id == 5) {
                        // Hasil Kerja 1
                        ${"row2_explode_hasil_kerja_1"} = explode("|",$row2_pengerjaan['hasil_kerja_1']);
                        ${"row2_umk_borongan_lokal_1"} = $this->umkBoronganEkspor->select('id','jenis_produk','umk_packing','umk_kemas','umk_pilih_gagang')
                                                        ->where('id',${"row2_explode_hasil_kerja_1"}[0])
                                                        ->first();
                        if(empty(${"row2_umk_borongan_lokal_1"})){
                            ${"row2_jenis_produk_1"} = '-';
                            ${"row2_hasil_kerja_1"} = null;
                            ${"row2_data_explode_hasil_kerja_1"} = '-';
                            ${"row2_lembur_1"} = 1;
                            ${"row2_total_hasil_1"} = 0;
                        }else{
                            ${"row2_jenis_produk_1"} = ${"row2_umk_borongan_lokal_1"}['jenis_produk'];
                            ${"row2_hasil_kerja_1"} = ${"row2_explode_hasil_kerja_1"}[1]*${"row2_umk_borongan_lokal_1"}['umk_packing'];
                            ${"row2_data_explode_hasil_kerja_1"} = ${"row2_explode_hasil_kerja_1"}[1];
                            ${"row2_explode_lembur_1"} = explode("|",$row2_pengerjaan['lembur']);
                            ${"row2_explode_status_lembur_1"} = explode("-",${"row2_explode_lembur_1"}[1]);
                            if(${"row2_explode_status_lembur_1"}[1] == 'y'){
                                ${"row2_lembur_1"} = 1.5;
                            }else{
                                ${"row2_lembur_1"} = 1;
                            }
                        }
                        // End Hasil Kerja 1

                        // Hasil Kerja 2
                        ${"row2_explode_hasil_kerja_2"} = explode("|",$row2_pengerjaan['hasil_kerja_2']);
                        ${"row2_umk_borongan_lokal_2"} = $this->umkBoronganEkspor->select('id','jenis_produk','umk_packing','umk_kemas','umk_pilih_gagang')
                                                        ->where('id',${"row2_explode_hasil_kerja_2"}[0])
                                                        ->first();
                        if(empty(${"row2_umk_borongan_lokal_2"})){
                            ${"row2_jenis_produk_2"} = '-';
                            ${"row2_hasil_kerja_2"} = null;
                            ${"row2_data_explode_hasil_kerja_2"} = '-';
                            ${"row2_lembur_2"} = 1;
                            ${"row2_total_hasil_2"} = 0;
                        }else{
                            ${"row2_jenis_produk_2"} = ${"row2_umk_borongan_lokal_2"}['jenis_produk'];
                            ${"row2_hasil_kerja_2"} = ${"row2_explode_hasil_kerja_2"}[1]*${"row2_umk_borongan_lokal_2"}['umk_packing'];
                            ${"row2_data_explode_hasil_kerja_2"} = ${"row2_explode_hasil_kerja_2"}[1];
                            ${"row2_explode_lembur_2"} = explode("|",$row2_pengerjaan['lembur']);
                            ${"row2_explode_status_lembur_2"} = explode("-",${"row2_explode_lembur_2"}[2]);
                            if(${"row2_explode_status_lembur_2"}[1] == 'y'){
                                ${"row2_lembur_2"} = 1.5;
                            }else{
                                ${"row2_lembur_2"} = 1;
                            }
                        }
                        // End Hasil Kerja 2
                        
                        // Hasil Kerja 3
                        ${"row2_explode_hasil_kerja_3"} = explode("|",$row2_pengerjaan['hasil_kerja_3']);
                        ${"row2_umk_borongan_lokal_3"} = $this->umkBoronganEkspor->select('id','jenis_produk','umk_packing','umk_kemas','umk_pilih_gagang')
                                                        ->where('id',${"row2_explode_hasil_kerja_3"}[0])
                                                        ->first();
                        if(empty(${"row2_umk_borongan_lokal_3"})){
                            ${"row2_jenis_produk_3"} = '-';
                            ${"row2_hasil_kerja_3"} = null;
                            ${"row2_data_explode_hasil_kerja_3"} = '-';
                            ${"row2_lembur_3"} = 1;
                            ${"row2_total_hasil_3"} = 0;
                        }else{
                            ${"row2_jenis_produk_3"} = ${"row2_umk_borongan_lokal_3"}['jenis_produk'];
                            ${"row2_hasil_kerja_3"} = ${"row2_explode_hasil_kerja_3"}[1]*${"row2_umk_borongan_lokal_3"}['umk_packing'];
                            ${"row2_data_explode_hasil_kerja_3"} = ${"row2_explode_hasil_kerja_3"}[1];
                            ${"row2_explode_lembur_3"} = explode("|",$row2_pengerjaan['lembur']);
                            ${"row2_explode_status_lembur_3"} = explode("-",${"row2_explode_lembur_3"}[3]);
                            if(${"row2_explode_status_lembur_3"}[1] == 'y'){
                                ${"row2_lembur_3"} = 1.5;
                            }else{
                                ${"row2_lembur_3"} = 1;
                            }
                        }
                        // End Hasil Kerja 3

                        // Hasil Kerja 4
                        ${"row2_explode_hasil_kerja_4"} = explode("|",$row2_pengerjaan['hasil_kerja_4']);
                        ${"row2_umk_borongan_lokal_4"} = $this->umkBoronganEkspor->select('id','jenis_produk','umk_packing','umk_kemas','umk_pilih_gagang')
                                                        ->where('id',${"row2_explode_hasil_kerja_4"}[0])
                                                        ->first();
                        if(empty(${"row2_umk_borongan_lokal_4"})){
                            ${"row2_jenis_produk_4"} = '-';
                            ${"row2_hasil_kerja_4"} = null;
                            ${"row2_data_explode_hasil_kerja_4"} = '-';
                            ${"row2_lembur_4"} = 1;
                            ${"row2_total_hasil_4"} = 0;
                        }else{
                            ${"row2_jenis_produk_4"} = ${"row2_umk_borongan_lokal_4"}['jenis_produk'];
                            ${"row2_hasil_kerja_4"} = ${"row2_explode_hasil_kerja_4"}[1]*${"row2_umk_borongan_lokal_4"}['umk_packing'];
                            ${"row2_data_explode_hasil_kerja_4"} = ${"row2_explode_hasil_kerja_4"}[1];
                            ${"row2_explode_lembur_4"} = explode("|",$row2_pengerjaan['lembur']);
                            ${"row2_explode_status_lembur_4"} = explode("-",${"row2_explode_lembur_4"}[4]);
                            if(${"row2_explode_status_lembur_4"}[1] == 'y'){
                                ${"row2_lembur_4"} = 1.5;
                            }else{
                                ${"row2_lembur_4"} = 1;
                            }
                        }
                        // End Hasil Kerja 4

                        // Hasil Kerja 5
                        ${"row2_explode_hasil_kerja_5"} = explode("|",$row2_pengerjaan['hasil_kerja_5']);
                        ${"row2_umk_borongan_lokal_5"} = $this->umkBoronganEkspor->select('id','jenis_produk','umk_packing','umk_kemas','umk_pilih_gagang')
                                                        ->where('id',${"row2_explode_hasil_kerja_5"}[0])
                                                        ->first();
                        if(empty(${"row2_umk_borongan_lokal_5"})){
                            ${"row2_jenis_produk_5"} = '-';
                            ${"row2_hasil_kerja_5"} = null;
                            ${"row2_data_explode_hasil_kerja_5"} = '-';
                            ${"row2_lembur_5"} = 1;
                            ${"row2_total_hasil_5"} = 0;
                        }else{
                            ${"row2_jenis_produk_5"} = ${"row2_umk_borongan_lokal_5"}['jenis_produk'];
                            ${"row2_hasil_kerja_5"} = ${"row2_explode_hasil_kerja_5"}[1]*${"row2_umk_borongan_lokal_5"}['umk_packing'];
                            ${"row2_data_explode_hasil_kerja_5"} = ${"row2_explode_hasil_kerja_5"}[1];
                            ${"row2_explode_lembur_5"} = explode("|",$row2_pengerjaan['lembur']);
                            ${"row2_explode_status_lembur_5"} = explode("-",${"row2_explode_lembur_5"}[5]);
                            if(${"row2_explode_status_lembur_5"}[1] == 'y'){
                                ${"row2_lembur_5"} = 1.5;
                            }else{
                                ${"row2_lembur_5"} = 1;
                            }
                        }
                        // End Hasil Kerja 5
                    }

                    if ($row2_pengerjaan->operator_karyawan->jenis_operator_detail_pekerjaan_id == 6) {
                        // Hasil Kerja 1
                        ${"row2_explode_hasil_kerja_1"} = explode("|",$row2_pengerjaan['hasil_kerja_1']);
                        ${"row2_umk_borongan_lokal_1"} = $this->umkBoronganEkspor->select('id','jenis_produk','umk_packing','umk_kemas','umk_pilih_gagang')
                                                        ->where('id',${"row2_explode_hasil_kerja_1"}[0])
                                                        ->first();
                        if(empty(${"row2_umk_borongan_lokal_1"})){
                            ${"row2_jenis_produk_1"} = '-';
                            ${"row2_hasil_kerja_1"} = null;
                            ${"row2_data_explode_hasil_kerja_1"} = '-';
                            ${"row2_lembur_1"} = 1;
                            ${"row2_total_hasil_1"} = 0;
                        }else{
                            ${"row2_jenis_produk_1"} = ${"row2_umk_borongan_lokal_1"}['jenis_produk'];
                            ${"row2_hasil_kerja_1"} = ${"row2_explode_hasil_kerja_1"}[1]*${"row2_umk_borongan_lokal_1"}['umk_kemas'];
                            ${"row2_data_explode_hasil_kerja_1"} = ${"row2_explode_hasil_kerja_1"}[1];
                            ${"row2_explode_lembur_1"} = explode("|",$row2_pengerjaan['lembur']);
                            ${"row2_explode_status_lembur_1"} = explode("-",${"row2_explode_lembur_1"}[1]);
                            if(${"row2_explode_status_lembur_1"}[1] == 'y'){
                                ${"row2_lembur_1"} = 1.5;
                            }else{
                                ${"row2_lembur_1"} = 1;
                            }
                        }
                        // End Hasil Kerja 1

                        // Hasil Kerja 2
                        ${"row2_explode_hasil_kerja_2"} = explode("|",$row2_pengerjaan['hasil_kerja_2']);
                        ${"row2_umk_borongan_lokal_2"} = $this->umkBoronganEkspor->select('id','jenis_produk','umk_packing','umk_kemas','umk_pilih_gagang')
                                                        ->where('id',${"row2_explode_hasil_kerja_2"}[0])
                                                        ->first();
                        if(empty(${"row2_umk_borongan_lokal_2"})){
                            ${"row2_jenis_produk_2"} = '-';
                            ${"row2_hasil_kerja_2"} = null;
                            ${"row2_data_explode_hasil_kerja_2"} = '-';
                            ${"row2_lembur_2"} = 1;
                            ${"row2_total_hasil_2"} = 0;
                        }else{
                            ${"row2_jenis_produk_2"} = ${"row2_umk_borongan_lokal_2"}['jenis_produk'];
                            ${"row2_hasil_kerja_2"} = ${"row2_explode_hasil_kerja_2"}[1]*${"row2_umk_borongan_lokal_2"}['umk_kemas'];
                            ${"row2_data_explode_hasil_kerja_2"} = ${"row2_explode_hasil_kerja_2"}[1];
                            ${"row2_explode_lembur_2"} = explode("|",$row2_pengerjaan['lembur']);
                            ${"row2_explode_status_lembur_2"} = explode("-",${"row2_explode_lembur_2"}[2]);
                            if(${"row2_explode_status_lembur_2"}[1] == 'y'){
                                ${"row2_lembur_2"} = 1.5;
                            }else{
                                ${"row2_lembur_2"} = 1;
                            }
                        }
                        // End Hasil Kerja 2
                        
                        // Hasil Kerja 3
                        ${"row2_explode_hasil_kerja_3"} = explode("|",$row2_pengerjaan['hasil_kerja_3']);
                        ${"row2_umk_borongan_lokal_3"} = $this->umkBoronganEkspor->select('id','jenis_produk','umk_packing','umk_kemas','umk_pilih_gagang')
                                                        ->where('id',${"row2_explode_hasil_kerja_3"}[0])
                                                        ->first();
                        if(empty(${"row2_umk_borongan_lokal_3"})){
                            ${"row2_jenis_produk_3"} = '-';
                            ${"row2_hasil_kerja_3"} = null;
                            ${"row2_data_explode_hasil_kerja_3"} = '-';
                            ${"row2_lembur_3"} = 1;
                            ${"row2_total_hasil_3"} = 0;
                        }else{
                            ${"row2_jenis_produk_3"} = ${"row2_umk_borongan_lokal_3"}['jenis_produk'];
                            ${"row2_hasil_kerja_3"} = ${"row2_explode_hasil_kerja_3"}[1]*${"row2_umk_borongan_lokal_3"}['umk_kemas'];
                            ${"row2_data_explode_hasil_kerja_3"} = ${"row2_explode_hasil_kerja_3"}[1];
                            ${"row2_explode_lembur_3"} = explode("|",$row2_pengerjaan['lembur']);
                            ${"row2_explode_status_lembur_3"} = explode("-",${"row2_explode_lembur_3"}[3]);
                            if(${"row2_explode_status_lembur_3"}[1] == 'y'){
                                ${"row2_lembur_3"} = 1.5;
                            }else{
                                ${"row2_lembur_3"} = 1;
                            }
                        }
                        // End Hasil Kerja 3

                        // Hasil Kerja 4
                        ${"row2_explode_hasil_kerja_4"} = explode("|",$row2_pengerjaan['hasil_kerja_4']);
                        ${"row2_umk_borongan_lokal_4"} = $this->umkBoronganEkspor->select('id','jenis_produk','umk_packing','umk_kemas','umk_pilih_gagang')
                                                        ->where('id',${"row2_explode_hasil_kerja_4"}[0])
                                                        ->first();
                        if(empty(${"row2_umk_borongan_lokal_4"})){
                            ${"row2_jenis_produk_4"} = '-';
                            ${"row2_hasil_kerja_4"} = null;
                            ${"row2_data_explode_hasil_kerja_4"} = '-';
                            ${"row2_lembur_4"} = 1;
                            ${"row2_total_hasil_4"} = 0;
                        }else{
                            ${"row2_jenis_produk_4"} = ${"row2_umk_borongan_lokal_4"}['jenis_produk'];
                            ${"row2_hasil_kerja_4"} = ${"row2_explode_hasil_kerja_4"}[1]*${"row2_umk_borongan_lokal_4"}['umk_kemas'];
                            ${"row2_data_explode_hasil_kerja_4"} = ${"row2_explode_hasil_kerja_4"}[1];
                            ${"row2_explode_lembur_4"} = explode("|",$row2_pengerjaan['lembur']);
                            ${"row2_explode_status_lembur_4"} = explode("-",${"row2_explode_lembur_4"}[4]);
                            if(${"row2_explode_status_lembur_4"}[1] == 'y'){
                                ${"row2_lembur_4"} = 1.5;
                            }else{
                                ${"row2_lembur_4"} = 1;
                            }
                        }
                        // End Hasil Kerja 4

                        // Hasil Kerja 5
                        ${"row2_explode_hasil_kerja_5"} = explode("|",$row2_pengerjaan['hasil_kerja_5']);
                        ${"row2_umk_borongan_lokal_5"} = $this->umkBoronganEkspor->select('id','jenis_produk','umk_packing','umk_kemas','umk_pilih_gagang')
                                                        ->where('id',${"row2_explode_hasil_kerja_5"}[0])
                                                        ->first();
                        if(empty(${"row2_umk_borongan_lokal_5"})){
                            ${"row2_jenis_produk_5"} = '-';
                            ${"row2_hasil_kerja_5"} = null;
                            ${"row2_data_explode_hasil_kerja_5"} = '-';
                            ${"row2_lembur_5"} = 1;
                            ${"row2_total_hasil_5"} = 0;
                        }else{
                            ${"row2_jenis_produk_5"} = ${"row2_umk_borongan_lokal_5"}['jenis_produk'];
                            ${"row2_hasil_kerja_5"} = ${"row2_explode_hasil_kerja_5"}[1]*${"row2_umk_borongan_lokal_5"}['umk_kemas'];
                            ${"row2_data_explode_hasil_kerja_5"} = ${"row2_explode_hasil_kerja_5"}[1];
                            ${"row2_explode_lembur_5"} = explode("|",$row2_pengerjaan['lembur']);
                            ${"row2_explode_status_lembur_5"} = explode("-",${"row2_explode_lembur_5"}[5]);
                            if(${"row2_explode_status_lembur_5"}[1] == 'y'){
                                ${"row2_lembur_5"} = 1.5;
                            }else{
                                ${"row2_lembur_5"} = 1;
                            }
                        }
                        // End Hasil Kerja 5
                    }
                    
                    if ($row2_pengerjaan->operator_karyawan->jenis_operator_detail_pekerjaan_id == 7) {
                        // Hasil Kerja 1
                        ${"row2_explode_hasil_kerja_1"} = explode("|",$row2_pengerjaan['hasil_kerja_1']);
                        ${"row2_umk_borongan_lokal_1"} = $this->umkBoronganEkspor->select('id','jenis_produk','umk_packing','umk_kemas','umk_pilih_gagang')
                                                        ->where('id',${"row2_explode_hasil_kerja_1"}[0])
                                                        ->first();
                        if(empty(${"row2_umk_borongan_lokal_1"})){
                            ${"row2_jenis_produk_1"} = '-';
                            ${"row2_hasil_kerja_1"} = null;
                            ${"row2_data_explode_hasil_kerja_1"} = '-';
                            ${"row2_lembur_1"} = 1;
                            ${"row2_total_hasil_1"} = 0;
                        }else{
                            ${"row2_jenis_produk_1"} = ${"row2_umk_borongan_lokal_1"}['jenis_produk'];
                            ${"row2_hasil_kerja_1"} = ${"row2_explode_hasil_kerja_1"}[1]*${"row2_umk_borongan_lokal_1"}['umk_pilih_gagang'];
                            ${"row2_data_explode_hasil_kerja_1"} = ${"row2_explode_hasil_kerja_1"}[1];
                            ${"row2_explode_lembur_1"} = explode("|",$row2_pengerjaan['lembur']);
                            ${"row2_explode_status_lembur_1"} = explode("-",${"row2_explode_lembur_1"}[1]);
                            if(${"row2_explode_status_lembur_1"}[1] == 'y'){
                                ${"row2_lembur_1"} = 1.5;
                            }else{
                                ${"row2_lembur_1"} = 1;
                            }
                        }
                        // End Hasil Kerja 1

                        // Hasil Kerja 2
                        ${"row2_explode_hasil_kerja_2"} = explode("|",$row2_pengerjaan['hasil_kerja_2']);
                        ${"row2_umk_borongan_lokal_2"} = $this->umkBoronganEkspor->select('id','jenis_produk','umk_packing','umk_kemas','umk_pilih_gagang')
                                                        ->where('id',${"row2_explode_hasil_kerja_2"}[0])
                                                        ->first();
                        if(empty(${"row2_umk_borongan_lokal_2"})){
                            ${"row2_jenis_produk_2"} = '-';
                            ${"row2_hasil_kerja_2"} = null;
                            ${"row2_data_explode_hasil_kerja_2"} = '-';
                            ${"row2_lembur_2"} = 1;
                            ${"row2_total_hasil_2"} = 0;
                        }else{
                            ${"row2_jenis_produk_2"} = ${"row2_umk_borongan_lokal_2"}['jenis_produk'];
                            ${"row2_hasil_kerja_2"} = ${"row2_explode_hasil_kerja_2"}[1]*${"row2_umk_borongan_lokal_2"}['umk_pilih_gagang'];
                            ${"row2_data_explode_hasil_kerja_2"} = ${"row2_explode_hasil_kerja_2"}[1];
                            ${"row2_explode_lembur_2"} = explode("|",$row2_pengerjaan['lembur']);
                            ${"row2_explode_status_lembur_2"} = explode("-",${"row2_explode_lembur_2"}[2]);
                            if(${"row2_explode_status_lembur_2"}[1] == 'y'){
                                ${"row2_lembur_2"} = 1.5;
                            }else{
                                ${"row2_lembur_2"} = 1;
                            }
                        }
                        // End Hasil Kerja 2
                        
                        // Hasil Kerja 3
                        ${"row2_explode_hasil_kerja_3"} = explode("|",$row2_pengerjaan['hasil_kerja_3']);
                        ${"row2_umk_borongan_lokal_3"} = $this->umkBoronganEkspor->select('id','jenis_produk','umk_packing','umk_kemas','umk_pilih_gagang')
                                                        ->where('id',${"row2_explode_hasil_kerja_3"}[0])
                                                        ->first();
                        if(empty(${"row2_umk_borongan_lokal_3"})){
                            ${"row2_jenis_produk_3"} = '-';
                            ${"row2_hasil_kerja_3"} = null;
                            ${"row2_data_explode_hasil_kerja_3"} = '-';
                            ${"row2_lembur_3"} = 1;
                            ${"row2_total_hasil_3"} = 0;
                        }else{
                            ${"row2_jenis_produk_3"} = ${"row2_umk_borongan_lokal_3"}['jenis_produk'];
                            ${"row2_hasil_kerja_3"} = ${"row2_explode_hasil_kerja_3"}[1]*${"row2_umk_borongan_lokal_3"}['umk_pilih_gagang'];
                            ${"row2_data_explode_hasil_kerja_3"} = ${"row2_explode_hasil_kerja_3"}[1];
                            ${"row2_explode_lembur_3"} = explode("|",$row2_pengerjaan['lembur']);
                            ${"row2_explode_status_lembur_3"} = explode("-",${"row2_explode_lembur_3"}[3]);
                            if(${"row2_explode_status_lembur_3"}[1] == 'y'){
                                ${"row2_lembur_3"} = 1.5;
                            }else{
                                ${"row2_lembur_3"} = 1;
                            }
                        }
                        // End Hasil Kerja 3

                        // Hasil Kerja 4
                        ${"row2_explode_hasil_kerja_4"} = explode("|",$row2_pengerjaan['hasil_kerja_4']);
                        ${"row2_umk_borongan_lokal_4"} = $this->umkBoronganEkspor->select('id','jenis_produk','umk_packing','umk_kemas','umk_pilih_gagang')
                                                        ->where('id',${"row2_explode_hasil_kerja_4"}[0])
                                                        ->first();
                        if(empty(${"row2_umk_borongan_lokal_4"})){
                            ${"row2_jenis_produk_4"} = '-';
                            ${"row2_hasil_kerja_4"} = null;
                            ${"row2_data_explode_hasil_kerja_4"} = '-';
                            ${"row2_lembur_4"} = 1;
                            ${"row2_total_hasil_4"} = 0;
                        }else{
                            ${"row2_jenis_produk_4"} = ${"row2_umk_borongan_lokal_4"}['jenis_produk'];
                            ${"row2_hasil_kerja_4"} = ${"row2_explode_hasil_kerja_4"}[1]*${"row2_umk_borongan_lokal_4"}['umk_pilih_gagang'];
                            ${"row2_data_explode_hasil_kerja_4"} = ${"row2_explode_hasil_kerja_4"}[1];
                            ${"row2_explode_lembur_4"} = explode("|",$row2_pengerjaan['lembur']);
                            ${"row2_explode_status_lembur_4"} = explode("-",${"row2_explode_lembur_4"}[4]);
                            if(${"row2_explode_status_lembur_4"}[1] == 'y'){
                                ${"row2_lembur_4"} = 1.5;
                            }else{
                                ${"row2_lembur_4"} = 1;
                            }
                        }
                        // End Hasil Kerja 4

                        // Hasil Kerja 5
                        ${"row2_explode_hasil_kerja_5"} = explode("|",$row2_pengerjaan['hasil_kerja_5']);
                        ${"row2_umk_borongan_lokal_5"} = $this->umkBoronganEkspor->select('id','jenis_produk','umk_packing','umk_kemas','umk_pilih_gagang')
                                                        ->where('id',${"row2_explode_hasil_kerja_5"}[0])
                                                        ->first();
                        if(empty(${"row2_umk_borongan_lokal_5"})){
                            ${"row2_jenis_produk_5"} = '-';
                            ${"row2_hasil_kerja_5"} = null;
                            ${"row2_data_explode_hasil_kerja_5"} = '-';
                            ${"row2_lembur_5"} = 1;
                            ${"row2_total_hasil_5"} = 0;
                        }else{
                            ${"row2_jenis_produk_5"} = ${"row2_umk_borongan_lokal_5"}['jenis_produk'];
                            ${"row2_hasil_kerja_5"} = ${"row2_explode_hasil_kerja_5"}[1]*${"row2_umk_borongan_lokal_5"}['umk_pilih_gagang'];
                            ${"row2_data_explode_hasil_kerja_5"} = ${"row2_explode_hasil_kerja_5"}[1];
                            ${"row2_explode_lembur_5"} = explode("|",$row2_pengerjaan['lembur']);
                            ${"row2_explode_status_lembur_5"} = explode("-",${"row2_explode_lembur_5"}[5]);
                            if(${"row2_explode_status_lembur_5"}[1] == 'y'){
                                ${"row2_lembur_5"} = 1.5;
                            }else{
                                ${"row2_lembur_5"} = 1;
                            }
                        }
                        // End Hasil Kerja 5
                    }

                    // Ambri
                    if ($row2_pengerjaan->operator_karyawan->jenis_operator_detail_pekerjaan_id == 8) {
                        // Hasil Kerja 1
                        ${"row2_explode_hasil_kerja_1"} = explode("|",$row2_pengerjaan['hasil_kerja_1']);
                        ${"row2_umk_borongan_lokal_1"} = $this->umkBoronganAmbri->select('id','jenis_produk','umk_etiket','umk_las_tepi','umk_las_pojok','umk_ambri')
                                                        ->where('id',${"row2_explode_hasil_kerja_1"}[0])
                                                        ->first();
                        if(empty(${"row2_umk_borongan_lokal_1"})){
                            ${"row2_jenis_produk_1"} = '-';
                            ${"row2_hasil_kerja_1"} = null;
                            ${"row2_data_explode_hasil_kerja_1"} = '-';
                            ${"row2_lembur_1"} = 1;
                            ${"row2_total_hasil_1"} = 0;
                        }else{
                            ${"row2_jenis_produk_1"} = ${"row2_umk_borongan_lokal_1"}['jenis_produk'];
                            ${"row2_hasil_kerja_1"} = ${"row2_explode_hasil_kerja_1"}[1]*${"row2_umk_borongan_lokal_1"}['umk_etiket'];
                            ${"row2_data_explode_hasil_kerja_1"} = ${"row2_explode_hasil_kerja_1"}[1];
                            ${"row2_explode_lembur_1"} = explode("|",$row2_pengerjaan['lembur']);
                            ${"row2_explode_status_lembur_1"} = explode("-",${"row2_explode_lembur_1"}[1]);
                            if(${"row2_explode_status_lembur_1"}[1] == 'y'){
                                ${"row2_lembur_1"} = 1.5;
                            }else{
                                ${"row2_lembur_1"} = 1;
                            }
                        }
                        // End Hasil Kerja 1

                        // Hasil Kerja 2
                        ${"row2_explode_hasil_kerja_2"} = explode("|",$row2_pengerjaan['hasil_kerja_2']);
                        ${"row2_umk_borongan_lokal_2"} = $this->umkBoronganAmbri->select('id','jenis_produk','umk_etiket','umk_las_tepi','umk_las_pojok','umk_ambri')
                                                        ->where('id',${"row2_explode_hasil_kerja_2"}[0])
                                                        ->first();
                        if(empty(${"row2_umk_borongan_lokal_2"})){
                            ${"row2_jenis_produk_2"} = '-';
                            ${"row2_hasil_kerja_2"} = null;
                            ${"row2_data_explode_hasil_kerja_2"} = '-';
                            ${"row2_lembur_2"} = 1;
                            ${"row2_total_hasil_2"} = 0;
                        }else{
                            ${"row2_jenis_produk_2"} = ${"row2_umk_borongan_lokal_2"}['jenis_produk'];
                            ${"row2_hasil_kerja_2"} = ${"row2_explode_hasil_kerja_2"}[1]*${"row2_umk_borongan_lokal_2"}['umk_etiket'];
                            ${"row2_data_explode_hasil_kerja_2"} = ${"row2_explode_hasil_kerja_2"}[1];
                            ${"row2_explode_lembur_2"} = explode("|",$row2_pengerjaan['lembur']);
                            ${"row2_explode_status_lembur_2"} = explode("-",${"row2_explode_lembur_2"}[2]);
                            if(${"row2_explode_status_lembur_2"}[1] == 'y'){
                                ${"row2_lembur_2"} = 1.5;
                            }else{
                                ${"row2_lembur_2"} = 1;
                            }
                        }
                        // End Hasil Kerja 2
                        
                        // Hasil Kerja 3
                        ${"row2_explode_hasil_kerja_3"} = explode("|",$row2_pengerjaan['hasil_kerja_3']);
                        ${"row2_umk_borongan_lokal_3"} = $this->umkBoronganAmbri->select('id','jenis_produk','umk_etiket','umk_las_tepi','umk_las_pojok','umk_ambri')
                                                        ->where('id',${"row2_explode_hasil_kerja_3"}[0])
                                                        ->first();
                        if(empty(${"row2_umk_borongan_lokal_3"})){
                            ${"row2_jenis_produk_3"} = '-';
                            ${"row2_hasil_kerja_3"} = null;
                            ${"row2_data_explode_hasil_kerja_3"} = '-';
                            ${"row2_lembur_3"} = 1;
                            ${"row2_total_hasil_3"} = 0;
                        }else{
                            ${"row2_jenis_produk_3"} = ${"row2_umk_borongan_lokal_3"}['jenis_produk'];
                            ${"row2_hasil_kerja_3"} = ${"row2_explode_hasil_kerja_3"}[1]*${"row2_umk_borongan_lokal_3"}['umk_etiket'];
                            ${"row2_data_explode_hasil_kerja_3"} = ${"row2_explode_hasil_kerja_3"}[1];
                            ${"row2_explode_lembur_3"} = explode("|",$row2_pengerjaan['lembur']);
                            ${"row2_explode_status_lembur_3"} = explode("-",${"row2_explode_lembur_3"}[3]);
                            if(${"row2_explode_status_lembur_3"}[1] == 'y'){
                                ${"row2_lembur_3"} = 1.5;
                            }else{
                                ${"row2_lembur_3"} = 1;
                            }
                        }
                        // End Hasil Kerja 3

                        // Hasil Kerja 4
                        ${"row2_explode_hasil_kerja_4"} = explode("|",$row2_pengerjaan['hasil_kerja_4']);
                        ${"row2_umk_borongan_lokal_4"} = $this->umkBoronganAmbri->select('id','jenis_produk','umk_etiket','umk_las_tepi','umk_las_pojok','umk_ambri')
                                                        ->where('id',${"row2_explode_hasil_kerja_4"}[0])
                                                        ->first();
                        if(empty(${"row2_umk_borongan_lokal_4"})){
                            ${"row2_jenis_produk_4"} = '-';
                            ${"row2_hasil_kerja_4"} = null;
                            ${"row2_data_explode_hasil_kerja_4"} = '-';
                            ${"row2_lembur_4"} = 1;
                            ${"row2_total_hasil_4"} = 0;
                        }else{
                            ${"row2_jenis_produk_4"} = ${"row2_umk_borongan_lokal_4"}['jenis_produk'];
                            ${"row2_hasil_kerja_4"} = ${"row2_explode_hasil_kerja_4"}[1]*${"row2_umk_borongan_lokal_4"}['umk_etiket'];
                            ${"row2_data_explode_hasil_kerja_4"} = ${"row2_explode_hasil_kerja_4"}[1];
                            ${"row2_explode_lembur_4"} = explode("|",$row2_pengerjaan['lembur']);
                            ${"row2_explode_status_lembur_4"} = explode("-",${"row2_explode_lembur_4"}[4]);
                            if(${"row2_explode_status_lembur_4"}[1] == 'y'){
                                ${"row2_lembur_4"} = 1.5;
                            }else{
                                ${"row2_lembur_4"} = 1;
                            }
                        }
                        // End Hasil Kerja 4

                        // Hasil Kerja 5
                        ${"row2_explode_hasil_kerja_5"} = explode("|",$row2_pengerjaan['hasil_kerja_5']);
                        ${"row2_umk_borongan_lokal_5"} = $this->umkBoronganAmbri->select('id','jenis_produk','umk_etiket','umk_las_tepi','umk_las_pojok','umk_ambri')
                                                        ->where('id',${"row2_explode_hasil_kerja_5"}[0])
                                                        ->first();
                        if(empty(${"row2_umk_borongan_lokal_5"})){
                            ${"row2_jenis_produk_5"} = '-';
                            ${"row2_hasil_kerja_5"} = null;
                            ${"row2_data_explode_hasil_kerja_5"} = '-';
                            ${"row2_lembur_5"} = 1;
                            ${"row2_total_hasil_5"} = 0;
                        }else{
                            ${"row2_jenis_produk_5"} = ${"row2_umk_borongan_lokal_5"}['jenis_produk'];
                            ${"row2_hasil_kerja_5"} = ${"row2_explode_hasil_kerja_5"}[1]*${"row2_umk_borongan_lokal_5"}['umk_etiket'];
                            ${"row2_data_explode_hasil_kerja_5"} = ${"row2_explode_hasil_kerja_5"}[1];
                            ${"row2_explode_lembur_5"} = explode("|",$row2_pengerjaan['lembur']);
                            ${"row2_explode_status_lembur_5"} = explode("-",${"row2_explode_lembur_5"}[5]);
                            if(${"row2_explode_status_lembur_5"}[1] == 'y'){
                                ${"row2_lembur_5"} = 1.5;
                            }else{
                                ${"row2_lembur_5"} = 1;
                            }
                        }
                        // End Hasil Kerja 5
                    }

                    if ($row2_pengerjaan->operator_karyawan->jenis_operator_detail_pekerjaan_id == 9) {
                        // Hasil Kerja 1
                        ${"row2_explode_hasil_kerja_1"} = explode("|",$row2_pengerjaan['hasil_kerja_1']);
                        ${"row2_umk_borongan_lokal_1"} = $this->umkBoronganAmbri->select('id','jenis_produk','umk_etiket','umk_las_tepi','umk_las_pojok','umk_ambri')
                                                        ->where('id',${"row2_explode_hasil_kerja_1"}[0])
                                                        ->first();
                        if(empty(${"row2_umk_borongan_lokal_1"})){
                            ${"row2_jenis_produk_1"} = '-';
                            ${"row2_hasil_kerja_1"} = null;
                            ${"row2_data_explode_hasil_kerja_1"} = '-';
                            ${"row2_lembur_1"} = 1;
                            ${"row2_total_hasil_1"} = 0;
                        }else{
                            ${"row2_jenis_produk_1"} = ${"row2_umk_borongan_lokal_1"}['jenis_produk'];
                            ${"row2_hasil_kerja_1"} = ${"row2_explode_hasil_kerja_1"}[1]*${"row2_umk_borongan_lokal_1"}['umk_las_tepi'];
                            ${"row2_data_explode_hasil_kerja_1"} = ${"row2_explode_hasil_kerja_1"}[1];
                            ${"row2_explode_lembur_1"} = explode("|",$row2_pengerjaan['lembur']);
                            ${"row2_explode_status_lembur_1"} = explode("-",${"row2_explode_lembur_1"}[1]);
                            if(${"row2_explode_status_lembur_1"}[1] == 'y'){
                                ${"row2_lembur_1"} = 1.5;
                            }else{
                                ${"row2_lembur_1"} = 1;
                            }
                        }
                        // End Hasil Kerja 1

                        // Hasil Kerja 2
                        ${"row2_explode_hasil_kerja_2"} = explode("|",$row2_pengerjaan['hasil_kerja_2']);
                        ${"row2_umk_borongan_lokal_2"} = $this->umkBoronganAmbri->select('id','jenis_produk','umk_etiket','umk_las_tepi','umk_las_pojok','umk_ambri')
                                                        ->where('id',${"row2_explode_hasil_kerja_2"}[0])
                                                        ->first();
                        if(empty(${"row2_umk_borongan_lokal_2"})){
                            ${"row2_jenis_produk_2"} = '-';
                            ${"row2_hasil_kerja_2"} = null;
                            ${"row2_data_explode_hasil_kerja_2"} = '-';
                            ${"row2_lembur_2"} = 1;
                            ${"row2_total_hasil_2"} = 0;
                        }else{
                            ${"row2_jenis_produk_2"} = ${"row2_umk_borongan_lokal_2"}['jenis_produk'];
                            ${"row2_hasil_kerja_2"} = ${"row2_explode_hasil_kerja_2"}[1]*${"row2_umk_borongan_lokal_2"}['umk_las_tepi'];
                            ${"row2_data_explode_hasil_kerja_2"} = ${"row2_explode_hasil_kerja_2"}[1];
                            ${"row2_explode_lembur_2"} = explode("|",$row2_pengerjaan['lembur']);
                            ${"row2_explode_status_lembur_2"} = explode("-",${"row2_explode_lembur_2"}[2]);
                            if(${"row2_explode_status_lembur_2"}[1] == 'y'){
                                ${"row2_lembur_2"} = 1.5;
                            }else{
                                ${"row2_lembur_2"} = 1;
                            }
                        }
                        // End Hasil Kerja 2
                        
                        // Hasil Kerja 3
                        ${"row2_explode_hasil_kerja_3"} = explode("|",$row2_pengerjaan['hasil_kerja_3']);
                        ${"row2_umk_borongan_lokal_3"} = $this->umkBoronganAmbri->select('id','jenis_produk','umk_etiket','umk_las_tepi','umk_las_pojok','umk_ambri')
                                                        ->where('id',${"row2_explode_hasil_kerja_3"}[0])
                                                        ->first();
                        if(empty(${"row2_umk_borongan_lokal_3"})){
                            ${"row2_jenis_produk_3"} = '-';
                            ${"row2_hasil_kerja_3"} = null;
                            ${"row2_data_explode_hasil_kerja_3"} = '-';
                            ${"row2_lembur_3"} = 1;
                            ${"row2_total_hasil_3"} = 0;
                        }else{
                            ${"row2_jenis_produk_3"} = ${"row2_umk_borongan_lokal_3"}['jenis_produk'];
                            ${"row2_hasil_kerja_3"} = ${"row2_explode_hasil_kerja_3"}[1]*${"row2_umk_borongan_lokal_3"}['umk_las_tepi'];
                            ${"row2_data_explode_hasil_kerja_3"} = ${"row2_explode_hasil_kerja_3"}[1];
                            ${"row2_explode_lembur_3"} = explode("|",$row2_pengerjaan['lembur']);
                            ${"row2_explode_status_lembur_3"} = explode("-",${"row2_explode_lembur_3"}[3]);
                            if(${"row2_explode_status_lembur_3"}[1] == 'y'){
                                ${"row2_lembur_3"} = 1.5;
                            }else{
                                ${"row2_lembur_3"} = 1;
                            }
                        }
                        // End Hasil Kerja 3

                        // Hasil Kerja 4
                        ${"row2_explode_hasil_kerja_4"} = explode("|",$row2_pengerjaan['hasil_kerja_4']);
                        ${"row2_umk_borongan_lokal_4"} = $this->umkBoronganAmbri->select('id','jenis_produk','umk_etiket','umk_las_tepi','umk_las_pojok','umk_ambri')
                                                        ->where('id',${"row2_explode_hasil_kerja_4"}[0])
                                                        ->first();
                        if(empty(${"row2_umk_borongan_lokal_4"})){
                            ${"row2_jenis_produk_4"} = '-';
                            ${"row2_hasil_kerja_4"} = null;
                            ${"row2_data_explode_hasil_kerja_4"} = '-';
                            ${"row2_lembur_4"} = 1;
                            ${"row2_total_hasil_4"} = 0;
                        }else{
                            ${"row2_jenis_produk_4"} = ${"row2_umk_borongan_lokal_4"}['jenis_produk'];
                            ${"row2_hasil_kerja_4"} = ${"row2_explode_hasil_kerja_4"}[1]*${"row2_umk_borongan_lokal_4"}['umk_las_tepi'];
                            ${"row2_data_explode_hasil_kerja_4"} = ${"row2_explode_hasil_kerja_4"}[1];
                            ${"row2_explode_lembur_4"} = explode("|",$row2_pengerjaan['lembur']);
                            ${"row2_explode_status_lembur_4"} = explode("-",${"row2_explode_lembur_4"}[4]);
                            if(${"row2_explode_status_lembur_4"}[1] == 'y'){
                                ${"row2_lembur_4"} = 1.5;
                            }else{
                                ${"row2_lembur_4"} = 1;
                            }
                        }
                        // End Hasil Kerja 4

                        // Hasil Kerja 5
                        ${"row2_explode_hasil_kerja_5"} = explode("|",$row2_pengerjaan['hasil_kerja_5']);
                        ${"row2_umk_borongan_lokal_5"} = $this->umkBoronganAmbri->select('id','jenis_produk','umk_etiket','umk_las_tepi','umk_las_pojok','umk_ambri')
                                                        ->where('id',${"row2_explode_hasil_kerja_5"}[0])
                                                        ->first();
                        if(empty(${"row2_umk_borongan_lokal_5"})){
                            ${"row2_jenis_produk_5"} = '-';
                            ${"row2_hasil_kerja_5"} = null;
                            ${"row2_data_explode_hasil_kerja_5"} = '-';
                            ${"row2_lembur_5"} = 1;
                            ${"row2_total_hasil_5"} = 0;
                        }else{
                            ${"row2_jenis_produk_5"} = ${"row2_umk_borongan_lokal_5"}['jenis_produk'];
                            ${"row2_hasil_kerja_5"} = ${"row2_explode_hasil_kerja_5"}[1]*${"row2_umk_borongan_lokal_5"}['umk_las_tepi'];
                            ${"row2_data_explode_hasil_kerja_5"} = ${"row2_explode_hasil_kerja_5"}[1];
                            ${"row2_explode_lembur_5"} = explode("|",$row2_pengerjaan['lembur']);
                            ${"row2_explode_status_lembur_5"} = explode("-",${"row2_explode_lembur_5"}[5]);
                            if(${"row2_explode_status_lembur_5"}[1] == 'y'){
                                ${"row2_lembur_5"} = 1.5;
                            }else{
                                ${"row2_lembur_5"} = 1;
                            }
                        }
                        // End Hasil Kerja 5
                    }

                    if ($row2_pengerjaan->operator_karyawan->jenis_operator_detail_pekerjaan_id == 11) {
                        // Hasil Kerja 1
                        ${"row2_explode_hasil_kerja_1"} = explode("|",$row2_pengerjaan['hasil_kerja_1']);
                        ${"row2_umk_borongan_lokal_1"} = $this->umkBoronganAmbri->select('id','jenis_produk','umk_etiket','umk_las_tepi','umk_las_pojok','umk_ambri')
                                                        ->where('id',${"row2_explode_hasil_kerja_1"}[0])
                                                        ->first();
                        if(empty(${"row2_umk_borongan_lokal_1"})){
                            ${"row2_jenis_produk_1"} = '-';
                            ${"row2_hasil_kerja_1"} = null;
                            ${"row2_data_explode_hasil_kerja_1"} = '-';
                            ${"row2_lembur_1"} = 1;
                            ${"row2_total_hasil_1"} = 0;
                        }else{
                            ${"row2_jenis_produk_1"} = ${"row2_umk_borongan_lokal_1"}['jenis_produk'];
                            ${"row2_hasil_kerja_1"} = ${"row2_explode_hasil_kerja_1"}[1]*${"row2_umk_borongan_lokal_1"}['umk_ambri'];
                            ${"row2_data_explode_hasil_kerja_1"} = ${"row2_explode_hasil_kerja_1"}[1];
                            ${"row2_explode_lembur_1"} = explode("|",$row2_pengerjaan['lembur']);
                            ${"row2_explode_status_lembur_1"} = explode("-",${"row2_explode_lembur_1"}[1]);
                            if(${"row2_explode_status_lembur_1"}[1] == 'y'){
                                ${"row2_lembur_1"} = 1.5;
                            }else{
                                ${"row2_lembur_1"} = 1;
                            }
                        }
                        // End Hasil Kerja 1

                        // Hasil Kerja 2
                        ${"row2_explode_hasil_kerja_2"} = explode("|",$row2_pengerjaan['hasil_kerja_2']);
                        ${"row2_umk_borongan_lokal_2"} = $this->umkBoronganAmbri->select('id','jenis_produk','umk_etiket','umk_las_tepi','umk_las_pojok','umk_ambri')
                                                        ->where('id',${"row2_explode_hasil_kerja_2"}[0])
                                                        ->first();
                        if(empty(${"row2_umk_borongan_lokal_2"})){
                            ${"row2_jenis_produk_2"} = '-';
                            ${"row2_hasil_kerja_2"} = null;
                            ${"row2_data_explode_hasil_kerja_2"} = '-';
                            ${"row2_lembur_2"} = 1;
                            ${"row2_total_hasil_2"} = 0;
                        }else{
                            ${"row2_jenis_produk_2"} = ${"row2_umk_borongan_lokal_2"}['jenis_produk'];
                            ${"row2_hasil_kerja_2"} = ${"row2_explode_hasil_kerja_2"}[1]*${"row2_umk_borongan_lokal_2"}['umk_ambri'];
                            ${"row2_data_explode_hasil_kerja_2"} = ${"row2_explode_hasil_kerja_2"}[1];
                            ${"row2_explode_lembur_2"} = explode("|",$row2_pengerjaan['lembur']);
                            ${"row2_explode_status_lembur_2"} = explode("-",${"row2_explode_lembur_2"}[2]);
                            if(${"row2_explode_status_lembur_2"}[1] == 'y'){
                                ${"row2_lembur_2"} = 1.5;
                            }else{
                                ${"row2_lembur_2"} = 1;
                            }
                        }
                        // End Hasil Kerja 2
                        
                        // Hasil Kerja 3
                        ${"row2_explode_hasil_kerja_3"} = explode("|",$row2_pengerjaan['hasil_kerja_3']);
                        ${"row2_umk_borongan_lokal_3"} = $this->umkBoronganAmbri->select('id','jenis_produk','umk_etiket','umk_las_tepi','umk_las_pojok','umk_ambri')
                                                        ->where('id',${"row2_explode_hasil_kerja_3"}[0])
                                                        ->first();
                        if(empty(${"row2_umk_borongan_lokal_3"})){
                            ${"row2_jenis_produk_3"} = '-';
                            ${"row2_hasil_kerja_3"} = null;
                            ${"row2_data_explode_hasil_kerja_3"} = '-';
                            ${"row2_lembur_3"} = 1;
                            ${"row2_total_hasil_3"} = 0;
                        }else{
                            ${"row2_jenis_produk_3"} = ${"row2_umk_borongan_lokal_3"}['jenis_produk'];
                            ${"row2_hasil_kerja_3"} = ${"row2_explode_hasil_kerja_3"}[1]*${"row2_umk_borongan_lokal_3"}['umk_ambri'];
                            ${"row2_data_explode_hasil_kerja_3"} = ${"row2_explode_hasil_kerja_3"}[1];
                            ${"row2_explode_lembur_3"} = explode("|",$row2_pengerjaan['lembur']);
                            ${"row2_explode_status_lembur_3"} = explode("-",${"row2_explode_lembur_3"}[3]);
                            if(${"row2_explode_status_lembur_3"}[1] == 'y'){
                                ${"row2_lembur_3"} = 1.5;
                            }else{
                                ${"row2_lembur_3"} = 1;
                            }
                        }
                        // End Hasil Kerja 3

                        // Hasil Kerja 4
                        ${"row2_explode_hasil_kerja_4"} = explode("|",$row2_pengerjaan['hasil_kerja_4']);
                        ${"row2_umk_borongan_lokal_4"} = $this->umkBoronganAmbri->select('id','jenis_produk','umk_etiket','umk_las_tepi','umk_las_pojok','umk_ambri')
                                                        ->where('id',${"row2_explode_hasil_kerja_4"}[0])
                                                        ->first();
                        if(empty(${"row2_umk_borongan_lokal_4"})){
                            ${"row2_jenis_produk_4"} = '-';
                            ${"row2_hasil_kerja_4"} = null;
                            ${"row2_data_explode_hasil_kerja_4"} = '-';
                            ${"row2_lembur_4"} = 1;
                            ${"row2_total_hasil_4"} = 0;
                        }else{
                            ${"row2_jenis_produk_4"} = ${"row2_umk_borongan_lokal_4"}['jenis_produk'];
                            ${"row2_hasil_kerja_4"} = ${"row2_explode_hasil_kerja_4"}[1]*${"row2_umk_borongan_lokal_4"}['umk_ambri'];
                            ${"row2_data_explode_hasil_kerja_4"} = ${"row2_explode_hasil_kerja_4"}[1];
                            ${"row2_explode_lembur_4"} = explode("|",$row2_pengerjaan['lembur']);
                            ${"row2_explode_status_lembur_4"} = explode("-",${"row2_explode_lembur_4"}[4]);
                            if(${"row2_explode_status_lembur_4"}[1] == 'y'){
                                ${"row2_lembur_4"} = 1.5;
                            }else{
                                ${"row2_lembur_4"} = 1;
                            }
                        }
                        // End Hasil Kerja 4

                        // Hasil Kerja 5
                        ${"row2_explode_hasil_kerja_5"} = explode("|",$row2_pengerjaan['hasil_kerja_5']);
                        ${"row2_umk_borongan_lokal_5"} = $this->umkBoronganAmbri->select('id','jenis_produk','umk_etiket','umk_las_tepi','umk_las_pojok','umk_ambri')
                                                        ->where('id',${"row2_explode_hasil_kerja_5"}[0])
                                                        ->first();
                        if(empty(${"row2_umk_borongan_lokal_5"})){
                            ${"row2_jenis_produk_5"} = '-';
                            ${"row2_hasil_kerja_5"} = null;
                            ${"row2_data_explode_hasil_kerja_5"} = '-';
                            ${"row2_lembur_5"} = 1;
                            ${"row2_total_hasil_5"} = 0;
                        }else{
                            ${"row2_jenis_produk_5"} = ${"row2_umk_borongan_lokal_5"}['jenis_produk'];
                            ${"row2_hasil_kerja_5"} = ${"row2_explode_hasil_kerja_5"}[1]*${"row2_umk_borongan_lokal_5"}['umk_ambri'];
                            ${"row2_data_explode_hasil_kerja_5"} = ${"row2_explode_hasil_kerja_5"}[1];
                            ${"row2_explode_lembur_5"} = explode("|",$row2_pengerjaan['lembur']);
                            ${"row2_explode_status_lembur_5"} = explode("-",${"row2_explode_lembur_5"}[5]);
                            if(${"row2_explode_status_lembur_5"}[1] == 'y'){
                                ${"row2_lembur_5"} = 1.5;
                            }else{
                                ${"row2_lembur_5"} = 1;
                            }
                        }
                        // End Hasil Kerja 5
                    }

                    //Stempel Kantong
                    if ($row2_pengerjaan->operator_karyawan->jenis_operator_detail_pekerjaan_id == 25) {
                        // Hasil Kerja 1
                        ${"row2_explode_hasil_kerja_1"} = explode("|",$row2_pengerjaan['hasil_kerja_1']);
                        ${"row2_umk_borongan_lokal_1"} = $this->umkBoronganStempel->select('id','jenis_produk','nominal_umk')
                                                        ->where('id',${"row2_explode_hasil_kerja_1"}[0])
                                                        ->first();
                        if(empty(${"row2_umk_borongan_lokal_1"})){
                            ${"row2_jenis_produk_1"} = '-';
                            ${"row2_hasil_kerja_1"} = null;
                            ${"row2_data_explode_hasil_kerja_1"} = '-';
                            ${"row2_lembur_1"} = 1;
                            ${"row2_total_hasil_1"} = 0;
                        }else{
                            ${"row2_jenis_produk_1"} = ${"row2_umk_borongan_lokal_1"}['jenis_produk'];
                            ${"row2_hasil_kerja_1"} = ${"row2_explode_hasil_kerja_1"}[1]*${"row2_umk_borongan_lokal_1"}['nominal_umk'];
                            ${"row2_data_explode_hasil_kerja_1"} = ${"row2_explode_hasil_kerja_1"}[1];
                            ${"row2_explode_lembur_1"} = explode("|",$row2_pengerjaan['lembur']);
                            ${"row2_explode_status_lembur_1"} = explode("-",${"row2_explode_lembur_1"}[1]);
                            if(${"row2_explode_status_lembur_1"}[1] == 'y'){
                                ${"row2_lembur_1"} = 1.5;
                            }else{
                                ${"row2_lembur_1"} = 1;
                            }
                        }
                        // End Hasil Kerja 1
    
                        // Hasil Kerja 2
                        ${"row2_explode_hasil_kerja_2"} = explode("|",$row2_pengerjaan['hasil_kerja_2']);
                        ${"row2_umk_borongan_lokal_2"} = $this->umkBoronganStempel->select('id','jenis_produk','nominal_umk')
                                                        ->where('id',${"row2_explode_hasil_kerja_2"}[0])
                                                        ->first();
                        if(empty(${"row2_umk_borongan_lokal_2"})){
                            ${"row2_jenis_produk_2"} = '-';
                            ${"row2_hasil_kerja_2"} = null;
                            ${"row2_data_explode_hasil_kerja_2"} = '-';
                            ${"row2_lembur_2"} = 1;
                            ${"row2_total_hasil_2"} = 0;
                        }else{
                            ${"row2_jenis_produk_2"} = ${"row2_umk_borongan_lokal_2"}['jenis_produk'];
                            ${"row2_hasil_kerja_2"} = ${"row2_explode_hasil_kerja_2"}[1]*${"row2_umk_borongan_lokal_2"}['nominal_umk'];
                            ${"row2_data_explode_hasil_kerja_2"} = ${"row2_explode_hasil_kerja_2"}[1];
                            ${"row2_explode_lembur_2"} = explode("|",$row2_pengerjaan['lembur']);
                            ${"row2_explode_status_lembur_2"} = explode("-",${"row2_explode_lembur_2"}[2]);
                            if(${"row2_explode_status_lembur_2"}[1] == 'y'){
                                ${"row2_lembur_2"} = 1.5;
                            }else{
                                ${"row2_lembur_2"} = 1;
                            }
                        }
                        // End Hasil Kerja 2
                        
                        // Hasil Kerja 3
                        ${"row2_explode_hasil_kerja_3"} = explode("|",$row2_pengerjaan['hasil_kerja_3']);
                        ${"row2_umk_borongan_lokal_3"} = $this->umkBoronganStempel->select('id','jenis_produk','nominal_umk')
                                                        ->where('id',${"row2_explode_hasil_kerja_3"}[0])
                                                        ->first();
                        if(empty(${"row2_umk_borongan_lokal_3"})){
                            ${"row2_jenis_produk_3"} = '-';
                            ${"row2_hasil_kerja_3"} = null;
                            ${"row2_data_explode_hasil_kerja_3"} = '-';
                            ${"row2_lembur_3"} = 1;
                            ${"row2_total_hasil_3"} = 0;
                        }else{
                            ${"row2_jenis_produk_3"} = ${"row2_umk_borongan_lokal_3"}['jenis_produk'];
                            ${"row2_hasil_kerja_3"} = ${"row2_explode_hasil_kerja_3"}[1]*${"row2_umk_borongan_lokal_3"}['nominal_umk'];
                            ${"row2_data_explode_hasil_kerja_3"} = ${"row2_explode_hasil_kerja_3"}[1];
                            ${"row2_explode_lembur_3"} = explode("|",$row2_pengerjaan['lembur']);
                            ${"row2_explode_status_lembur_3"} = explode("-",${"row2_explode_lembur_3"}[3]);
                            if(${"row2_explode_status_lembur_3"}[1] == 'y'){
                                ${"row2_lembur_3"} = 1.5;
                            }else{
                                ${"row2_lembur_3"} = 1;
                            }
                        }
                        // End Hasil Kerja 3
    
                        // Hasil Kerja 4
                        ${"row2_explode_hasil_kerja_4"} = explode("|",$row2_pengerjaan['hasil_kerja_4']);
                        ${"row2_umk_borongan_lokal_4"} = $this->umkBoronganStempel->select('id','jenis_produk','nominal_umk')
                                                        ->where('id',${"row2_explode_hasil_kerja_4"}[0])
                                                        ->first();
                        if(empty(${"row2_umk_borongan_lokal_4"})){
                            ${"row2_jenis_produk_4"} = '-';
                            ${"row2_hasil_kerja_4"} = null;
                            ${"row2_data_explode_hasil_kerja_4"} = '-';
                            ${"row2_lembur_4"} = 1;
                            ${"row2_total_hasil_4"} = 0;
                        }else{
                            ${"row2_jenis_produk_4"} = ${"row2_umk_borongan_lokal_4"}['jenis_produk'];
                            ${"row2_hasil_kerja_4"} = ${"row2_explode_hasil_kerja_4"}[1]*${"row2_umk_borongan_lokal_4"}['nominal_umk'];
                            ${"row2_data_explode_hasil_kerja_4"} = ${"row2_explode_hasil_kerja_4"}[1];
                            ${"row2_explode_lembur_4"} = explode("|",$row2_pengerjaan['lembur']);
                            ${"row2_explode_status_lembur_4"} = explode("-",${"row2_explode_lembur_4"}[4]);
                            if(${"row2_explode_status_lembur_4"}[1] == 'y'){
                                ${"row2_lembur_4"} = 1.5;
                            }else{
                                ${"row2_lembur_4"} = 1;
                            }
                        }
                        // End Hasil Kerja 4
    
                        // Hasil Kerja 5
                        ${"row2_explode_hasil_kerja_5"} = explode("|",$row2_pengerjaan['hasil_kerja_5']);
                        ${"row2_umk_borongan_lokal_5"} = $this->umkBoronganStempel->select('id','jenis_produk','nominal_umk')
                                                        ->where('id',${"row2_explode_hasil_kerja_5"}[0])
                                                        ->first();
                        if(empty(${"row2_umk_borongan_lokal_5"})){
                            ${"row2_jenis_produk_5"} = '-';
                            ${"row2_hasil_kerja_5"} = null;
                            ${"row2_data_explode_hasil_kerja_5"} = '-';
                            ${"row2_lembur_5"} = 1;
                            ${"row2_total_hasil_5"} = 0;
                        }else{
                            ${"row2_jenis_produk_5"} = ${"row2_umk_borongan_lokal_5"}['jenis_produk'];
                            ${"row2_hasil_kerja_5"} = ${"row2_explode_hasil_kerja_5"}[1]*${"row2_umk_borongan_lokal_5"}['nominal_umk'];
                            ${"row2_data_explode_hasil_kerja_5"} = ${"row2_explode_hasil_kerja_5"}[1];
                            ${"row2_explode_lembur_5"} = explode("|",$row2_pengerjaan['lembur']);
                            ${"row2_explode_status_lembur_5"} = explode("-",${"row2_explode_lembur_5"}[5]);
                            if(${"row2_explode_status_lembur_5"}[1] == 'y'){
                                ${"row2_lembur_5"} = 1.5;
                            }else{
                                ${"row2_lembur_5"} = 1;
                            }
                        }
                        // End Hasil Kerja 5
                    }

                    $row2_total_hasil_kerja = (round(($row2_hasil_kerja_1*$row2_lembur_1)+($row2_hasil_kerja_2*$row2_lembur_2)+($row2_hasil_kerja_3*$row2_lembur_3)+($row2_hasil_kerja_4*$row2_lembur_4)+($row2_hasil_kerja_5*$row2_lembur_5)))-$row2_pengerjaan['uang_lembur'];
                    // $row2_total_hasil_kerja = (($row2_hasil_kerja_1*$row2_lembur_1)+($row2_hasil_kerja_2*$row2_lembur_2)+($row2_hasil_kerja_3*$row2_lembur_3)+($row2_hasil_kerja_4*$row2_lembur_4)+($row2_hasil_kerja_5*$row2_lembur_5));
                    $row2_total_lembur = $row2_pengerjaan['uang_lembur'];
                    array_push($row2_total_upah_hasil_kerja,$row2_total_hasil_kerja);
                    array_push($row2_total_lembur_kerja,$row2_total_lembur);

                    if ($data['new_data_pengerjaan']['akhir_bulan'] == 'y') {
                        if (empty($row2_weekly['tunjangan_kerja'])) {
                            $row2_tunjangan_kerja = 0;
                        }else{
                            $row2_tunjangan_kerja = $row2_weekly['tunjangan_kerja'];
                        }
                    }else{
                        $row2_tunjangan_kerja = 0;
                    }

                    if (empty($row2_weekly['tunjangan_kehadiran'])) {
                        $row2_tunjangan_kehadiran = 0;
                    }else{
                        $row2_tunjangan_kehadiran = $row2_weekly['tunjangan_kehadiran'];
                    }
    
                    if (empty($row2_weekly['uang_makan'])) {
                        $row2_uang_makan = 0;
                    }else{
                        $row2_uang_makan = $row2_weekly['uang_makan'];
                    }

                    if (empty($row2_weekly['plus_1'])) {
                        $row2_plus_1 = 0;
                        $row2_ket_plus_1 = null;
                    }else{
                        $row2_explode_plus_1 = explode("|",$row2_weekly['plus_1']);
                        $row2_plus_1 = floatval($row2_explode_plus_1[0]);
                        $row2_ket_plus_1 = $row2_explode_plus_1[1];
                    }
    
                    if (empty($row2_weekly['plus_2'])) {
                        $row2_plus_2 = 0;
                        $row2_ket_plus_2 = null;
                    }else{
                        $row2_explode_plus_2 = explode("|",$row2_weekly['plus_2']);
                        $row2_plus_2 = floatval($row2_explode_plus_2[0]);
                        $row2_ket_plus_2 = $row2_explode_plus_2[1];
                    }
    
                    if (empty($row2_weekly['plus_3'])) {
                        $row2_plus_3 = 0;
                        $row2_ket_plus_3 = null;
                    }else{
                        $row2_explode_plus_3 = explode("|",$row2_weekly['plus_3']);
                        $row2_plus_3 = floatval($row2_explode_plus_3[0]);
                        $row2_ket_plus_3 = $row2_explode_plus_3[1];
                    }

                    if (empty($row2_weekly['jht'])) {
                        $row2_jht = 0;
                    }else{
                        $row2_jht = $row2_weekly['jht'];
                    }
    
                    if (empty($row2_weekly['bpjs_kesehatan'])) {
                        $row2_bpjs_kesehatan = 0;
                    }else{
                        $row2_bpjs_kesehatan = $row2_weekly['bpjs_kesehatan'];
                    }

                    if (empty($row2_weekly['minus_1'])) {
                        $row2_minus_1 = '0';
                        $row2_ket_minus_1 = null;
                    }else{
                        $row2_explode_minus_1 = explode("|",$row2_weekly['minus_1']);
                        $row2_minus_1 = floatval($row2_explode_minus_1[0]);
                        $row2_ket_minus_1 = $row2_explode_minus_1[1];
                    }
    
                    if (empty($row2_weekly['minus_2'])) {
                        $row2_minus_2 = 0;
                        $row2_ket_minus_2 = null;
                    }else{
                        $row2_explode_minus_2 = explode("|",$row2_weekly['minus_2']);
                        $row2_minus_2 = floatval($row2_explode_minus_2[0]);
                        $row2_ket_minus_2 = $row2_explode_minus_2[1];
                    }

                    $row2_total_gaji_diterima = (array_sum($row2_total_upah_hasil_kerja)
                                            +array_sum($row2_total_lembur_kerja)
                                            +$row2_tunjangan_kerja
                                            +$row2_tunjangan_kehadiran
                                            +$row2_uang_makan
                                            +$row2_plus_1
                                            +$row2_plus_2
                                            +$row2_plus_3
                                            )
                                            -
                                            ($row2_jht+$row2_bpjs_kesehatan+$row2_minus_1+$row2_minus_2)
                                            ;
                }
            }
            // dd($datas);
            // ### LINE 1 ###
            $pdf->SetFont('Arial','B',8);
            $pdf->Cell(35,5,'TANGGAL GAJI ','LT',0,'C'); 
            $pdf->SetFont('Arial','',8);
            $pdf->Cell(60,5,Carbon::parse($exp_tgl_awal[0] . '-' . $exp_tgl_awal[1] . '-' . $exp_tgl_awal[2])->isoFormat('D MMMM').' s/d '.Carbon::parse($exp_tgl_akhir[0] . '-' . $exp_tgl_akhir[1] . '-' . $exp_tgl_akhir[2])->isoFormat('D MMMM YYYY'),'TR',0,'L');
            
            $pdf->Cell(2,5,'','LF',0,'L');
            
            $pdf->SetFont('Arial','B',8);
            $pdf->Cell(35,5,'TANGGAL GAJI ','LT',0,'C'); 
            $pdf->SetFont('Arial','',8);
            $pdf->Cell(60,5,Carbon::parse($exp_tgl_awal[0] . '-' . $exp_tgl_awal[1] . '-' . $exp_tgl_awal[2])->isoFormat('D MMMM').' s/d '.Carbon::parse($exp_tgl_akhir[0] . '-' . $exp_tgl_akhir[1] . '-' . $exp_tgl_akhir[2])->isoFormat('D MMMM YYYY'),'TR',0,'L');
            $pdf->ln(3);

            $pdf->SetFont('Arial','B',8);
            $pdf->Cell(35,5,'Nama','L',0,'L'); 
            $pdf->SetFont('Arial','B',7);
            $pdf->Cell(60,5,strtoupper($row1_nama->nama).' ('.$row1_nama->nik.')','R',0,'L');
            
            $pdf->Cell(2,5,'','LF',0,'L');

            $pdf->SetFont('Arial','B',8);
            $pdf->Cell(35,5,'Nama','L',0,'L'); 
            $pdf->SetFont('Arial','B',7);
            $pdf->Cell(60,5,strtoupper($row2_nama_2).' ('.$row2_nik.')','R',0,'L');
            $pdf->ln(2.5);

            $pdf->SetFont('Arial','B',8);
            $pdf->Cell(35,5,'Departemen','L',0,'L'); 
            $pdf->SetFont('Arial','B',7);
            $pdf->Cell(60,5,strtoupper($query_weeklys[$i]['jenis_posisi_pekerjaan']),'R',0,'L');

            $pdf->Cell(2,5,'','LF',0,'L');

            $pdf->SetFont('Arial','B',8);
            $pdf->Cell(35,5,'Departemen','L',0,'L'); 
            $pdf->SetFont('Arial','B',7);
            $pdf->Cell(60,5,strtoupper($row2_jenis_posisi_pekerjaan ),'R',0,'L');
            $pdf->ln(3);

            $pdf->SetFont('Arial','',8);
            $pdf->Cell(35,5,'GAJI','L',0,'L'); 
            $pdf->Cell(22,5,$a.' HARI','',0,'L');
            $pdf->Cell(3,5,'Rp','',0,'L');
            $pdf->Cell(35,5,number_format(array_sum($row1_total_upah_hasil_kerja),0,',','.'),'R',0,'R');
            
            $pdf->Cell(2,5,'','LF',0,'L');

            $pdf->SetFont('Arial','',8);
            $pdf->Cell(35,5,'GAJI','L',0,'L'); 
            $pdf->Cell(22,5,$a.' HARI','',0,'L');
            $pdf->Cell(3,5,'Rp','',0,'L');
            $pdf->Cell(35,5,number_format(array_sum($row2_total_upah_hasil_kerja),0,',','.'),'R',0,'R');
            $pdf->ln(3);

            $pdf->SetFont('Arial','',8);
            $pdf->Cell(35,5,'Lembur','L',0,'L'); 
            $pdf->Cell(22,5,'','',0,'L');
            $pdf->Cell(3,5,'Rp','',0,'L');
            $pdf->Cell(35,5,number_format(array_sum($row1_total_lembur_kerja),0,',','.'),'R',0,'R');
            
            $pdf->Cell(2,5,'','LF',0,'L');

            $pdf->SetFont('Arial','',8);
            $pdf->Cell(35,5,'Lembur','L',0,'L'); 
            $pdf->Cell(22,5,'','',0,'L');
            $pdf->Cell(3,5,'Rp','',0,'L');
            $pdf->Cell(35,5,number_format(array_sum($row2_total_lembur_kerja),0,',','.'),'R',0,'R');
            $pdf->ln(3);

            $pdf->SetFont('Arial','',8);
            $pdf->Cell(35,5,'Insentif Kehadiran','L',0,'L'); 
            $pdf->Cell(22,5,'','',0,'L');
            $pdf->Cell(3,5,'Rp','',0,'L');
            $pdf->Cell(35,5,number_format($row1_tunjangan_kehadiran,0,',','.'),'R',0,'R');
            
            $pdf->Cell(2,5,'','LF',0,'L');
            
            $pdf->SetFont('Arial','',8);
            $pdf->Cell(35,5,'Insentif Kehadiran','L',0,'L'); 
            $pdf->Cell(22,5,'','',0,'L');
            $pdf->Cell(3,5,'Rp','',0,'L');
            $pdf->Cell(35,5,number_format($row2_tunjangan_kehadiran,0,',','.'),'R',0,'R');
            $pdf->ln(3);

            $pdf->SetFont('Arial','',8);
            $pdf->Cell(35,5,'Tunjangan Kerja','L',0,'L'); 
            $pdf->Cell(22,5,'','',0,'L');
            $pdf->Cell(3,5,'Rp','',0,'L');
            $pdf->Cell(35,5,number_format($row1_tunjangan_kerja,0,',','.'),'R',0,'R');
            
            $pdf->Cell(2,5,'','LF',0,'L');

            $pdf->SetFont('Arial','',8);
            $pdf->Cell(35,5,'Tunjangan Kerja','L',0,'L'); 
            $pdf->Cell(22,5,'','',0,'L');
            $pdf->Cell(3,5,'Rp','',0,'L');
            $pdf->Cell(35,5,number_format($row2_tunjangan_kerja,0,',','.'),'R',0,'R');
            $pdf->ln(3);

            $pdf->SetFont('Arial','',8);
            $pdf->Cell(12,5,'Plus','L',0,'L'); 
            $pdf->Cell(45,5,"(".$row1_ket_plus_1.")",'',0,'L');
            $pdf->Cell(3,5,'Rp','',0,'L');
            $pdf->Cell(35,5,number_format($row1_plus_1,0,',','.'),'R',0,'R');

            $pdf->Cell(2,5,'','LF',0,'L');

            $pdf->SetFont('Arial','',8);
            $pdf->Cell(12,5,'Plus','L',0,'L'); 
            $pdf->Cell(45,5,"(".$row2_ket_plus_1.")",'',0,'L');
            $pdf->Cell(3,5,'Rp','',0,'L');
            $pdf->Cell(35,5,number_format($row2_plus_1,0,',','.'),'R',0,'R');
            $pdf->ln(3);

            $pdf->SetFont('Arial','',8);
            $pdf->Cell(12,5,'','L',0,'L'); 
            $pdf->Cell(45,5,"(".$row1_ket_plus_2.")",'',0,'L');
            $pdf->Cell(3,5,'Rp','',0,'L');
            $pdf->Cell(35,5,number_format($row1_plus_2,0,',','.'),'R',0,'R');

            $pdf->Cell(2,5,'','LF',0,'L');

            $pdf->SetFont('Arial','',8);
            $pdf->Cell(12,5,'','L',0,'L'); 
            $pdf->Cell(45,5,"(".$row2_ket_plus_2.")",'',0,'L');
            $pdf->Cell(3,5,'Rp','',0,'L');
            $pdf->Cell(35,5,number_format($row2_plus_2,0,',','.'),'R',0,'R');
            $pdf->ln(3);

            $pdf->SetFont('Arial','',8);
            $pdf->Cell(12,5,'','L',0,'L'); 
            $pdf->Cell(45,5,"(".$row1_ket_plus_3.")",'',0,'L');
            $pdf->Cell(3,5,'Rp','',0,'L');
            $pdf->Cell(35,5,number_format($row1_plus_3,0,',','.'),'R',0,'R');

            $pdf->Cell(2,5,'','LF',0,'L');

            $pdf->SetFont('Arial','',8);
            $pdf->Cell(12,5,'','L',0,'L'); 
            $pdf->Cell(45,5,"(".$row2_ket_plus_3.")",'',0,'L');
            $pdf->Cell(3,5,'Rp','',0,'L');
            $pdf->Cell(35,5,number_format($row2_plus_3,0,',','.'),'R',0,'R');
            $pdf->ln(3);

            $pdf->SetFont('Arial','',8);
            $pdf->Cell(35,5,'Uang Makan','L',0,'L'); 
            $pdf->Cell(22,5,'','',0,'L');
            $pdf->Cell(3,5,'Rp','',0,'L');
            $pdf->Cell(35,5,number_format($row1_uang_makan,0,',','.'),'R',0,'R');
            $pdf->SetFont('Arial','',8);
            
            $pdf->Cell(2,5,'','LF',0,'L');

            $pdf->SetFont('Arial','',8);
            $pdf->Cell(35,5,'Uang Makan','L',0,'L'); 
            $pdf->Cell(22,5,'','',0,'L');
            $pdf->Cell(3,5,'Rp','',0,'L');
            $pdf->Cell(35,5,number_format($row2_uang_makan,0,',','.'),'R',0,'R');
            $pdf->SetFont('Arial','',8);
            $pdf->ln(4);

            $pdf->SetFont('Arial','B',8);
            $pdf->Cell(20,5,'POTONGAN','L',0,'L'); 
            $pdf->Cell(37,5,'BPJS > JHT + JP','',0,'L');
            $pdf->Cell(3,5,'Rp','',0,'L');
            $pdf->Cell(35,5,number_format($row1_jht,0,',','.'),'R',0,'R');

            $pdf->Cell(2,5,'','LF',0,'L');

            $pdf->SetFont('Arial','B',8);
            $pdf->Cell(20,5,'POTONGAN','L',0,'L'); 
            $pdf->Cell(37,5,'BPJS > JHT + JP','',0,'L');
            $pdf->Cell(3,5,'Rp','',0,'L');
            $pdf->Cell(35,5,number_format($row2_jht,0,',','.'),'R',0,'R');
            $pdf->ln(3);

            $pdf->SetFont('Arial','B',8);
            $pdf->Cell(20,5,'','L',0,'L'); 
            $pdf->Cell(37,5,'BPJS Kesehatan','',0,'L');
            $pdf->Cell(3,5,'Rp','',0,'L');
            $pdf->Cell(35,5,number_format($row1_bpjs_kesehatan,0,',','.'),'R',0,'R');
            
            $pdf->Cell(2,5,'','LF',0,'L');

            $pdf->SetFont('Arial','B',8);
            $pdf->Cell(20,5,'','L',0,'L'); 
            $pdf->Cell(37,5,'BPJS Kesehatan','',0,'L');
            $pdf->Cell(3,5,'Rp','',0,'L');
            $pdf->Cell(35,5,number_format($row2_bpjs_kesehatan,0,',','.'),'R',0,'R');
            $pdf->ln(3);

            $pdf->SetFont('Arial','B',8);
            $pdf->Cell(20,5,'','L',0,'L'); 
            $pdf->Cell(37,5,"( ".$row1_ket_minus_1." )",'',0,'L');
            $pdf->Cell(3,5,'Rp','',0,'L');
            $pdf->Cell(35,5,number_format($row1_minus_1,0,',','.'),'R',0,'R');

            $pdf->Cell(2,5,'','LF',0,'L');
            
            $pdf->SetFont('Arial','B',8);
            $pdf->Cell(20,5,'','L',0,'L'); 
            $pdf->Cell(37,5,"( ".$row2_ket_minus_1." )",'',0,'L');
            $pdf->Cell(3,5,'Rp','',0,'L');
            $pdf->Cell(35,5,number_format($row2_minus_1,0,',','.'),'R',0,'R');
            $pdf->ln(3);

            $pdf->SetFont('Arial','B',8);
            $pdf->Cell(20,5,'','L',0,'L'); 
            $pdf->Cell(37,5,"( ".$row1_ket_minus_2." )",'',0,'L');
            $pdf->Cell(3,5,'Rp','',0,'L');
            $pdf->Cell(35,5,number_format($row1_minus_2,0,',','.'),'R',0,'R');

            $pdf->Cell(2,5,'','LF',0,'L');
            
            $pdf->SetFont('Arial','B',8);
            $pdf->Cell(20,5,'','L',0,'L'); 
            $pdf->Cell(37,5,"( ".$row2_ket_minus_2." )",'',0,'L');
            $pdf->Cell(3,5,'Rp','',0,'L');
            $pdf->Cell(35,5,number_format($row2_minus_2,0,',','.'),'R',0,'R');
            $pdf->ln(5);

            $pdf->SetFont('Arial','B',9);
            $pdf->Cell(35,5,'TOTAL DITERIMA','BL',0,'L'); 
            $pdf->Cell(22,5,'','B',0,'L');
            $pdf->Cell(3,5,'Rp','B',0,'L');
            $pdf->Cell(35,5,number_format($row1_total_gaji_diterima,0,',','.'),'BR',0,'R');

            $pdf->Cell(2,5,'','LF',0,'L');

            $pdf->SetFont('Arial','B',9);
            $pdf->Cell(35,5,'TOTAL DITERIMA','BL',0,'L'); 
            $pdf->Cell(22,5,'','B',0,'L');
            $pdf->Cell(3,5,'Rp','B',0,'L');
            $pdf->Cell(35,5,number_format($row2_total_gaji_diterima,0,',','.'),'BR',0,'R');
            $pdf->ln(3);

            // $no=$no+1;
            // if ($no%5==0)
            // {
            //     $page=round($no/5);
            //     $pdf->ln(2);
            //     $pdf->SetFont('Arial','',7);
            //     $pdf->Cell(35,5,"- page $page -",'',0,''); 
            // }
            $pdf->ln(3);
        }
        // for ($i=0; $i < count($pengerjaan_weeklys) ; $i++) { 
        //     // ### LINE 1 ###
        //     $pdf->SetFont('Arial','B',8);
        //     $pdf->Cell(35,5,'TANGGAL GAJI ','LT',0,'C'); 
        //     $pdf->SetFont('Arial','',8);
        //     $pdf->Cell(60,5,Carbon::parse($exp_tgl_awal[0] . '-' . $exp_tgl_awal[1] . '-' . $exp_tgl_awal[2])->isoFormat('D MMMM').' s/d '.Carbon::parse($exp_tgl_akhir[0] . '-' . $exp_tgl_akhir[1] . '-' . $exp_tgl_akhir[2])->isoFormat('D MMMM YYYY'),'TR',0,'L');
            
        //     $pdf->Cell(2,5,'','LF',0,'L');
            
        //     $pdf->SetFont('Arial','B',8);
        //     $pdf->Cell(35,5,'TANGGAL GAJI ','LT',0,'C'); 
        //     $pdf->SetFont('Arial','',8);
        //     $pdf->Cell(60,5,Carbon::parse($exp_tgl_awal[0] . '-' . $exp_tgl_awal[1] . '-' . $exp_tgl_awal[2])->isoFormat('D MMMM').' s/d '.Carbon::parse($exp_tgl_akhir[0] . '-' . $exp_tgl_akhir[1] . '-' . $exp_tgl_akhir[2])->isoFormat('D MMMM YYYY'),'TR',0,'L');
        //     $pdf->ln(3);

        //     $pdf->SetFont('Arial','B',8);
        //     $pdf->Cell(35,5,'Nama','L',0,'L'); 
        //     $pdf->SetFont('Arial','B',7);
        //     $pdf->Cell(60,5,'','R',0,'L');
            
        //     $pdf->Cell(2,5,'','LF',0,'L');

        //     $pdf->SetFont('Arial','B',8);
        //     $pdf->Cell(35,5,'Nama','L',0,'L'); 
        //     $pdf->SetFont('Arial','B',7);
        //     $pdf->Cell(60,5,'','R',0,'L');
        //     $pdf->ln(2.5);

        //     $pdf->SetFont('Arial','B',8);
        //     $pdf->Cell(35,5,'Departemen','L',0,'L'); 
        //     $pdf->SetFont('Arial','B',7);
        //     $pdf->Cell(60,5,'','R',0,'L');

        //     $pdf->Cell(2,5,'','LF',0,'L');

        //     $pdf->SetFont('Arial','B',8);
        //     $pdf->Cell(35,5,'Departemen','L',0,'L'); 
        //     $pdf->SetFont('Arial','B',7);
        //     $pdf->Cell(60,5,'','R',0,'L');
        //     $pdf->ln(3);

        //     $pdf->SetFont('Arial','',8);
        //     $pdf->Cell(35,5,'GAJI','L',0,'L'); 
        //     $pdf->Cell(22,5,''.' HARI','',0,'L');
        //     $pdf->Cell(3,5,'Rp','',0,'L');
        //     $pdf->Cell(35,5,'','R',0,'R');
            
        //     $pdf->Cell(2,5,'','LF',0,'L');

        //     $pdf->SetFont('Arial','',8);
        //     $pdf->Cell(35,5,'GAJI','L',0,'L'); 
        //     $pdf->Cell(22,5,$a.' HARI','',0,'L');
        //     $pdf->Cell(3,5,'Rp','',0,'L');
        //     $pdf->Cell(35,5,'','R',0,'R');
        //     $pdf->ln(3);

        //     $pdf->SetFont('Arial','',8);
        //     $pdf->Cell(35,5,'Lembur','L',0,'L'); 
        //     $pdf->Cell(22,5,'','',0,'L');
        //     $pdf->Cell(3,5,'Rp','',0,'L');
        //     $pdf->Cell(35,5,'','R',0,'R');
            
        //     $pdf->Cell(2,5,'','LF',0,'L');

        //     $pdf->SetFont('Arial','',8);
        //     $pdf->Cell(35,5,'Lembur','L',0,'L'); 
        //     $pdf->Cell(22,5,'','',0,'L');
        //     $pdf->Cell(3,5,'Rp','',0,'L');
        //     $pdf->Cell(35,5,'','R',0,'R');
        //     $pdf->ln(3);

        //     $pdf->SetFont('Arial','',8);
        //     $pdf->Cell(35,5,'Insentif Kehadiran','L',0,'L'); 
        //     $pdf->Cell(22,5,'','',0,'L');
        //     $pdf->Cell(3,5,'Rp','',0,'L');
        //     $pdf->Cell(35,5,'','R',0,'R');
            
        //     $pdf->Cell(2,5,'','LF',0,'L');
            
        //     $pdf->SetFont('Arial','',8);
        //     $pdf->Cell(35,5,'Insentif Kehadiran','L',0,'L'); 
        //     $pdf->Cell(22,5,'','',0,'L');
        //     $pdf->Cell(3,5,'Rp','',0,'L');
        //     $pdf->Cell(35,5,'','R',0,'R');
        //     $pdf->ln(3);

        //     $pdf->SetFont('Arial','',8);
        //     $pdf->Cell(35,5,'Tunjangan Kerja','L',0,'L'); 
        //     $pdf->Cell(22,5,'','',0,'L');
        //     $pdf->Cell(3,5,'Rp','',0,'L');
        //     $pdf->Cell(35,5,'','R',0,'R');
            
        //     $pdf->Cell(2,5,'','LF',0,'L');

        //     $pdf->SetFont('Arial','',8);
        //     $pdf->Cell(35,5,'Tunjangan Kerja','L',0,'L'); 
        //     $pdf->Cell(22,5,'','',0,'L');
        //     $pdf->Cell(3,5,'Rp','',0,'L');
        //     $pdf->Cell(35,5,'','R',0,'R');
        //     $pdf->ln(3);

        //     $pdf->SetFont('Arial','',8);
        //     $pdf->Cell(12,5,'Plus','L',0,'L'); 
        //     $pdf->Cell(45,5,"",'',0,'L');
        //     $pdf->Cell(3,5,'Rp','',0,'L');
        //     $pdf->Cell(35,5,'','R',0,'R');

        //     $pdf->Cell(2,5,'','LF',0,'L');

        //     $pdf->SetFont('Arial','',8);
        //     $pdf->Cell(12,5,'Plus','L',0,'L'); 
        //     $pdf->Cell(45,5,"",'',0,'L');
        //     $pdf->Cell(3,5,'Rp','',0,'L');
        //     $pdf->Cell(35,5,'','R',0,'R');
        //     $pdf->ln(3);

        //     $pdf->SetFont('Arial','',8);
        //     $pdf->Cell(12,5,'','L',0,'L'); 
        //     $pdf->Cell(45,5,"",'',0,'L');
        //     $pdf->Cell(3,5,'Rp','',0,'L');
        //     $pdf->Cell(35,5,'','R',0,'R');

        //     $pdf->Cell(2,5,'','LF',0,'L');

        //     $pdf->SetFont('Arial','',8);
        //     $pdf->Cell(12,5,'','L',0,'L'); 
        //     $pdf->Cell(45,5,"",'',0,'L');
        //     $pdf->Cell(3,5,'Rp','',0,'L');
        //     $pdf->Cell(35,5,'','R',0,'R');
        //     $pdf->ln(3);

        //     $pdf->SetFont('Arial','',8);
        //     $pdf->Cell(12,5,'','L',0,'L'); 
        //     $pdf->Cell(45,5,"",'',0,'L');
        //     $pdf->Cell(3,5,'Rp','',0,'L');
        //     $pdf->Cell(35,5,'','R',0,'R');

        //     $pdf->Cell(2,5,'','LF',0,'L');

        //     $pdf->SetFont('Arial','',8);
        //     $pdf->Cell(12,5,'','L',0,'L'); 
        //     $pdf->Cell(45,5,"",'',0,'L');
        //     $pdf->Cell(3,5,'Rp','',0,'L');
        //     $pdf->Cell(35,5,'','R',0,'R');
        //     $pdf->ln(3);

        //     $pdf->SetFont('Arial','',8);
        //     $pdf->Cell(35,5,'Uang Makan','L',0,'L'); 
        //     $pdf->Cell(22,5,'','',0,'L');
        //     $pdf->Cell(3,5,'Rp','',0,'L');
        //     $pdf->Cell(35,5,'','R',0,'R');
        //     $pdf->SetFont('Arial','',8);
            
        //     $pdf->Cell(2,5,'','LF',0,'L');

        //     $pdf->SetFont('Arial','',8);
        //     $pdf->Cell(35,5,'Uang Makan','L',0,'L'); 
        //     $pdf->Cell(22,5,'','',0,'L');
        //     $pdf->Cell(3,5,'Rp','',0,'L');
        //     $pdf->Cell(35,5,'','R',0,'R');
        //     $pdf->SetFont('Arial','',8);
        //     $pdf->ln(4);

        //     $pdf->SetFont('Arial','B',8);
        //     $pdf->Cell(20,5,'POTONGAN','L',0,'L'); 
        //     $pdf->Cell(37,5,'BPJS > JHT + JP','',0,'L');
        //     $pdf->Cell(3,5,'Rp','',0,'L');
        //     $pdf->Cell(35,5,'','R',0,'R');

        //     $pdf->Cell(2,5,'','LF',0,'L');

        //     $pdf->SetFont('Arial','B',8);
        //     $pdf->Cell(20,5,'POTONGAN','L',0,'L'); 
        //     $pdf->Cell(37,5,'BPJS > JHT + JP','',0,'L');
        //     $pdf->Cell(3,5,'Rp','',0,'L');
        //     $pdf->Cell(35,5,'','R',0,'R');
        //     $pdf->ln(3);

        //     $pdf->SetFont('Arial','B',8);
        //     $pdf->Cell(20,5,'','L',0,'L'); 
        //     $pdf->Cell(37,5,'BPJS Kesehatan','',0,'L');
        //     $pdf->Cell(3,5,'Rp','',0,'L');
        //     $pdf->Cell(35,5,'','R',0,'R');
            
        //     $pdf->Cell(2,5,'','LF',0,'L');

        //     $pdf->SetFont('Arial','B',8);
        //     $pdf->Cell(20,5,'','L',0,'L'); 
        //     $pdf->Cell(37,5,'BPJS Kesehatan','',0,'L');
        //     $pdf->Cell(3,5,'Rp','',0,'L');
        //     $pdf->Cell(35,5,'','R',0,'R');
        //     $pdf->ln(3);

        //     $pdf->SetFont('Arial','B',8);
        //     $pdf->Cell(20,5,'','L',0,'L'); 
        //     $pdf->Cell(37,5,"",'',0,'L');
        //     $pdf->Cell(3,5,'Rp','',0,'L');
        //     $pdf->Cell(35,5,'','R',0,'R');

        //     $pdf->Cell(2,5,'','LF',0,'L');
            
        //     $pdf->SetFont('Arial','B',8);
        //     $pdf->Cell(20,5,'','L',0,'L'); 
        //     $pdf->Cell(37,5,"",'',0,'L');
        //     $pdf->Cell(3,5,'Rp','',0,'L');
        //     $pdf->Cell(35,5,'','R',0,'R');
        //     $pdf->ln(3);

        //     $pdf->SetFont('Arial','B',8);
        //     $pdf->Cell(20,5,'','L',0,'L'); 
        //     $pdf->Cell(37,5,"",'',0,'L');
        //     $pdf->Cell(3,5,'Rp','',0,'L');
        //     $pdf->Cell(35,5,'','R',0,'R');

        //     $pdf->Cell(2,5,'','LF',0,'L');
            
        //     $pdf->SetFont('Arial','B',8);
        //     $pdf->Cell(20,5,'','L',0,'L'); 
        //     $pdf->Cell(37,5,"",'',0,'L');
        //     $pdf->Cell(3,5,'Rp','',0,'L');
        //     $pdf->Cell(35,5,'','R',0,'R');
        //     $pdf->ln(5);

        //     $pdf->SetFont('Arial','B',9);
        //     $pdf->Cell(35,5,'TOTAL DITERIMA','BL',0,'L'); 
        //     $pdf->Cell(22,5,'','B',0,'L');
        //     $pdf->Cell(3,5,'Rp','B',0,'L');
        //     $pdf->Cell(35,5,'','BR',0,'R');

        //     $pdf->Cell(2,5,'','LF',0,'L');

        //     $pdf->SetFont('Arial','B',9);
        //     $pdf->Cell(35,5,'TOTAL DITERIMA','BL',0,'L'); 
        //     $pdf->Cell(22,5,'','B',0,'L');
        //     $pdf->Cell(3,5,'Rp','B',0,'L');
        //     $pdf->Cell(35,5,'','BR',0,'R');
        //     $pdf->ln(3);

        //     // $no=$no+1;
        //     // if ($no%5==0)
        //     // {
        //     //     $page=round($no/5);
        //     //     $pdf->ln(2);
        //     //     $pdf->SetFont('Arial','',7);
        //     //     $pdf->Cell(35,5,"- page $page -",'',0,''); 
        //     // }
        //     $pdf->ln(3);
        // }
        $pdf->Output('Slip Gaji Borongan '.Carbon::parse($exp_tgl_awal[0] . '-' . $exp_tgl_awal[1] . '-' . $exp_tgl_awal[2])->isoFormat('D MMMM').' sd '.Carbon::parse($exp_tgl_akhir[0] . '-' . $exp_tgl_akhir[1] . '-' . $exp_tgl_akhir[2])->isoFormat('D MMMM YYYY').'.pdf','I');
        exit;
        // dd($datas);
    }

//     public function borongan_slip_gaji($kode_pengerjaan)
//     {
//         $data['new_data_pengerjaan'] = NewDataPengerjaan::where('kode_pengerjaan',$kode_pengerjaan)->first();
//         $explode_tanggal_pengerjaans = explode('#',$data['new_data_pengerjaan']['tanggal']);
//         $exp_tanggals = array_filter($explode_tanggal_pengerjaans);
//         $a = count($exp_tanggals);
//         // dd($exp_tanggals);
//         $exp_tgl_awal = explode('-', $exp_tanggals[1]);
//         $exp_tgl_akhir = explode('-', $exp_tanggals[$a]);
        
//         $jenis_operator_id = [];
//         $jenis_pengerjaan_operator = JenisOperatorDetailPengerjaan::where('id','<=',11)->orderBy('id','asc')->get();
//         foreach ($jenis_pengerjaan_operator as $jpo) {
//             $jenis_operator_id[] = $jpo->id;
//         }
//         // dd($jenis_operator_id);

//         $first_pengerjaan_weekly_id = PengerjaanWeekly::select([
//                                                         'pengerjaan_weekly.id as id',
//                                                         'operator_karyawan.nik as nik',
//                                                         'operator_karyawan.jenis_operator_detail_pekerjaan_id as jenis_operator_detail_pekerjaan_id'
//                                                     ])
//                                                     ->leftJoin('operator_karyawan','operator_karyawan.id','=','pengerjaan_weekly.operator_karyawan_id')
//                                                     ->leftJoin('itic_emp_new.biodata_karyawan','biodata_karyawan.nik','=','operator_karyawan.nik')
//                                                     ->whereIn('operator_karyawan.jenis_operator_detail_pekerjaan_id',$jenis_operator_id)
//                                                     ->where('pengerjaan_weekly.kode_pengerjaan',$kode_pengerjaan)
//                                                     // ->orderBy('operator_karyawan.jenis_operator_detail_pekerjaan_id','asc')
//                                                     // ->orderBy('biodata_karyawan.nama','asc')
//                                                     ->first();
//         // dd($first_pengerjaan_weekly_id);
//         if ($first_pengerjaan_weekly_id->id %2 == 0) {
//             $ganjil_genap = 'MOD(pengerjaan_weekly.id, 2) = 0';
//             // $ganjil_genap = 'MOD(pengerjaan_weekly.id, 2) <> 0';
//         }else{
//             $ganjil_genap = 'MOD(pengerjaan_weekly.id, 2) <> 0';
//             // $ganjil_genap = 'MOD(pengerjaan_weekly.id, 2) = 0';
//         }

//         // $query_weeklys = PengerjaanWeekly::select([
//         //                 'pengerjaan_weekly.id as id',
//         //                 'pengerjaan_weekly.operator_karyawan_id as operator_karyawan_id',
//         //                 'operator_karyawan.nik as nik',
//         //                 'biodata_karyawan.nama as nama',
//         //                 'pengerjaan_weekly.upah_dasar as upah_dasar',
//         //                 'pengerjaan_weekly.tunjangan_kerja as tunjangan_kerja',
//         //                 'pengerjaan_weekly.tunjangan_kehadiran as tunjangan_kehadiran',
//         //                 'pengerjaan_weekly.uang_makan as uang_makan',
//         //                 'pengerjaan_weekly.plus_1 as plus_1',
//         //                 'pengerjaan_weekly.plus_2 as plus_2',
//         //                 'pengerjaan_weekly.plus_3 as plus_3',
//         //                 'pengerjaan_weekly.minus_1 as minus_1',
//         //                 'pengerjaan_weekly.minus_2 as minus_2',
//         //                 'pengerjaan_weekly.jht as jht',
//         //                 'pengerjaan_weekly.bpjs_kesehatan as bpjs_kesehatan',
//         //             ])
//         //             ->leftJoin('operator_karyawan','operator_karyawan.id','=','pengerjaan_weekly.operator_karyawan_id')
//         //             ->leftJoin('itic_emp_new.biodata_karyawan','biodata_karyawan.nik','=','operator_karyawan.nik')
//         //             ->whereRaw($ganjil_genap)
//         //             ->where('pengerjaan_weekly.kode_pengerjaan',$kode_pengerjaan)
//         //             ->orderBy('operator_karyawan.jenis_operator_detail_pekerjaan_id','asc')
//         //             ->orderBy('biodata_karyawan.nama','asc')
//         //             ->get();
//         // dd($jenis_operator_id);
//         // dd($ganjil_genap);
//         $query_weeklys = PengerjaanWeekly::select([
//                         'pengerjaan_weekly.id as id',
//                         'pengerjaan_weekly.kode_payrol as kode_payrol',
//                         'pengerjaan_weekly.operator_karyawan_id as operator_karyawan_id',
//                         'jenis_operator_detail_pekerjaan.jenis_posisi_pekerjaan as jenis_posisi_pekerjaan',
//                         'operator_karyawan.nik as nik',
//                         'biodata_karyawan.nama as nama',
//                         'pengerjaan_weekly.upah_dasar as upah_dasar',
//                         'pengerjaan_weekly.tunjangan_kerja as tunjangan_kerja',
//                         'pengerjaan_weekly.tunjangan_kehadiran as tunjangan_kehadiran',
//                         'pengerjaan_weekly.uang_makan as uang_makan',
//                         'pengerjaan_weekly.plus_1 as plus_1',
//                         'pengerjaan_weekly.plus_2 as plus_2',
//                         'pengerjaan_weekly.plus_3 as plus_3',
//                         'pengerjaan_weekly.minus_1 as minus_1',
//                         'pengerjaan_weekly.minus_2 as minus_2',
//                         'pengerjaan_weekly.jht as jht',
//                         'pengerjaan_weekly.bpjs_kesehatan as bpjs_kesehatan',
//                         'operator_karyawan.jenis_operator_detail_pekerjaan_id as jenis_operator_detail_pekerjaan_id'
//                     ])
//                     ->leftJoin('operator_karyawan','operator_karyawan.id','=','pengerjaan_weekly.operator_karyawan_id')
//                     ->leftJoin('itic_emp_new.biodata_karyawan','biodata_karyawan.nik','=','operator_karyawan.nik')
//                     ->leftJoin('jenis_operator_detail_pekerjaan','jenis_operator_detail_pekerjaan.id','=','operator_karyawan.jenis_operator_detail_pekerjaan_id')
//                     ->whereRaw($ganjil_genap)
//                     // ->where('pengerjaan_weekly.operator_karyawan_id',26)
//                     ->whereIn('operator_karyawan.jenis_operator_detail_pekerjaan_id',$jenis_operator_id)
//                     ->where('pengerjaan_weekly.kode_pengerjaan',$kode_pengerjaan)
//                     ->orderBy('operator_karyawan.jenis_operator_detail_pekerjaan_id','asc')
//                     ->orderBy('biodata_karyawan.nama','asc')
//                     ->get();
//         // dd($query_weeklys);
//         $data_array_1 = [];
//         $data_array_2 = [];

//         $pdf = new Fpdf('P', 'mm', 'A4');
//         $pdf->AddPage();
//         $pdf->SetFillColor(153,153,153);
//         $pdf->SetTextColor(255);
//         $pdf->SetDrawColor(0,0,0);
//         $pdf->SetLineWidth(.3);
//         $pdf->SetFont('Arial','B',8);
//         $pdf->SetFillColor(224,235,255);
//         $pdf->SetTextColor(0);
//         $pdf->SetFont('Arial','',8);

//         $no=0;
//         //True
//         foreach ($query_weeklys as $key => $qw) {
//             ###############row 1
//             $row1_nama = BiodataKaryawan::where('nik',$qw->nik)->orderBy('nama','asc')->first();
//             $row1_upah_lembur = [];
//             $row1_total_upah_hasil_kerja = [];
//             $row1_pengerjaans = Pengerjaan::where('operator_karyawan_id',$qw->operator_karyawan_id)
//                                     ->where('kode_pengerjaan',$kode_pengerjaan)
//                                     ->get();
//                                     // dd($row1_pengerjaans);
//             foreach ($row1_pengerjaans as $keys => $row1_pengerjaan) {
//                 //Begin Lokal
//                 if ($row1_pengerjaan->operator_karyawan->jenis_operator_detail_pekerjaan_id == 1) {
//                     $row1_explode_hasil_kerja_1 = explode("|",$row1_pengerjaan->hasil_kerja_1);
//                     $row1_umk_borongan_lokal_1 = $this->umkBoronganLokal->select('id','jenis_produk','umk_packing','umk_bandrol','umk_inner','umk_outer')->where('id',$row1_explode_hasil_kerja_1[0])->first();
//                     if(empty($row1_umk_borongan_lokal_1)){
//                         $row1_jenis_produk_1 = '-';
//                         $row1_hasil_kerja_1 = null;
//                         $row1_data_explode_hasil_kerja_1 = '-';
//                         $row1_lembur_1 = 1;
//                         $row1_total_hasil_1 = 0;
//                     }else{
//                         $row1_jenis_produk_1 = $row1_umk_borongan_lokal_1->jenis_produk;
//                         $row1_hasil_kerja_1 = $row1_explode_hasil_kerja_1[1]*$row1_umk_borongan_lokal_1->umk_packing;
//                         $row1_data_explode_hasil_kerja_1 = $row1_explode_hasil_kerja_1[1];
//                         // $row1_lembur_1 = 1.5;
//                         $explode_lembur_1 = explode("|",$row1_pengerjaan->lembur);
//                         $explode_status_lembur_1 = explode("-",$explode_lembur_1[1]);
//                         if($explode_status_lembur_1[1] == 'y'){
//                             $row1_lembur_1 = 1.5;
//                         }else{
//                             $row1_lembur_1 = 1;
//                         }
//                     }

//                     $row1_explode_hasil_kerja_2 = explode("|",$row1_pengerjaan->hasil_kerja_2);
//                     $row1_umk_borongan_lokal_2 = $this->umkBoronganLokal->select('id','jenis_produk','umk_packing','umk_bandrol','umk_inner','umk_outer')->where('id',$row1_explode_hasil_kerja_2[0])->first();
//                     if(empty($row1_umk_borongan_lokal_2)){
//                         $row1_jenis_produk_2 = '-';
//                         $row1_hasil_kerja_2 = null;
//                         $row1_data_explode_hasil_kerja_2 = '-';
//                         $row1_lembur_2 = 1;
//                     }else{
//                         $row1_jenis_produk_2 = $row1_umk_borongan_lokal_2->jenis_produk;
//                         $row1_hasil_kerja_2 = $row1_explode_hasil_kerja_2[1]*$row1_umk_borongan_lokal_2->umk_packing;
//                         $row1_data_explode_hasil_kerja_2 = $row1_explode_hasil_kerja_2[1];
//                         // $row1_lembur_2 = 1.5;
//                         $explode_lembur_2 = explode("|",$row1_pengerjaan->lembur);
//                         $explode_status_lembur_2 = explode("-",$explode_lembur_2[2]);
//                         if($explode_status_lembur_2[1] == 'y'){
//                             $row1_lembur_2 = 1.5;
//                         }else{
//                             $row1_lembur_2 = 1;
//                         }
//                     }

//                     $row1_explode_hasil_kerja_3 = explode("|",$row1_pengerjaan->hasil_kerja_3);
//                     $row1_umk_borongan_lokal_3 = $this->umkBoronganLokal->select('id','jenis_produk','umk_packing','umk_bandrol','umk_inner','umk_outer')->where('id',$row1_explode_hasil_kerja_3[0])->first();
//                     if(empty($row1_umk_borongan_lokal_3)){
//                         $row1_jenis_produk_3 = '-';
//                         $row1_hasil_kerja_3 = null;
//                         $row1_data_explode_hasil_kerja_3 = '-';
//                         $row1_lembur_3 = 1;
//                     }else{
//                         $row1_jenis_produk_3 = $row1_umk_borongan_lokal_3->jenis_produk;
//                         $row1_hasil_kerja_3 = $row1_explode_hasil_kerja_3[1]*$row1_umk_borongan_lokal_3->umk_packing;
//                         $row1_data_explode_hasil_kerja_3 = $row1_explode_hasil_kerja_3[1];
//                         // $row1_lembur_3 = 1.5;
//                         $explode_lembur_3 = explode("|",$row1_pengerjaan->lembur);
//                         $explode_status_lembur_3 = explode("-",$explode_lembur_3[3]);
//                         if($explode_status_lembur_3[1] == 'y'){
//                             $row1_lembur_3 = 1.5;
//                         }else{
//                             $row1_lembur_3 = 1;
//                         }
//                     }

//                     $row1_explode_hasil_kerja_4 = explode("|",$row1_pengerjaan->hasil_kerja_4);
//                     $row1_umk_borongan_lokal_4 = $this->umkBoronganLokal->select('id','jenis_produk','umk_packing','umk_bandrol','umk_inner','umk_outer')->where('id',$row1_explode_hasil_kerja_4[0])->first();
//                     if(empty($row1_umk_borongan_lokal_4)){
//                         $row1_jenis_produk_4 = '-';
//                         $row1_hasil_kerja_4 = null;
//                         $row1_data_explode_hasil_kerja_4 = '-';
//                         $row1_lembur_4 = 1;
//                     }else{
//                         $row1_jenis_produk_4 = $row1_umk_borongan_lokal_4->jenis_produk;
//                         $row1_hasil_kerja_4 = $row1_explode_hasil_kerja_4[1]*$row1_umk_borongan_lokal_4->umk_packing;
//                         $row1_data_explode_hasil_kerja_4 = $row1_explode_hasil_kerja_4[1];
//                         // $row1_lembur_4 = 1.5;
//                         $explode_lembur_4 = explode("|",$row1_pengerjaan->lembur);
//                         $explode_status_lembur_4 = explode("-",$explode_lembur_4[4]);
//                         if($explode_status_lembur_4[1] == 'y'){
//                             $row1_lembur_4 = 1.5;
//                         }else{
//                             $row1_lembur_4 = 1;
//                         }
//                     }

//                     $row1_explode_hasil_kerja_5 = explode("|",$row1_pengerjaan->hasil_kerja_5);
//                     $row1_umk_borongan_lokal_5 = $this->umkBoronganLokal->select('id','jenis_produk','umk_packing','umk_bandrol','umk_inner','umk_outer')->where('id',$row1_explode_hasil_kerja_5[0])->first();
//                     if(empty($row1_umk_borongan_lokal_5)){
//                         $row1_jenis_produk_5 = '-';
//                         $row1_hasil_kerja_5 = null;
//                         $row1_data_explode_hasil_kerja_5 = '-';
//                         $row1_lembur_5 = 1;
//                     }else{
//                         $row1_jenis_produk_5 = $row1_umk_borongan_lokal_5->jenis_produk;
//                         $row1_hasil_kerja_5 = $row1_explode_hasil_kerja_5[1]*$row1_umk_borongan_lokal_5->umk_packing;
//                         $row1_data_explode_hasil_kerja_5 = $row1_explode_hasil_kerja_5[1];
//                         // $row1_lembur_5 = 1.5;
//                         $explode_lembur_5 = explode("|",$row1_pengerjaan->lembur);
//                         $explode_status_lembur_5 = explode("-",$explode_lembur_5[5]);
//                         if($explode_status_lembur_5[1] == 'y'){
//                             $row1_lembur_5 = 1.5;
//                         }else{
//                             $row1_lembur_5 = 1;
//                         }
//                     }
//                 }

//                 if ($row1_pengerjaan->operator_karyawan->jenis_operator_detail_pekerjaan_id == 2) {
//                     $row1_explode_hasil_kerja_1 = explode("|",$row1_pengerjaan->hasil_kerja_1);
//                     $row1_umk_borongan_lokal_1 = $this->umkBoronganLokal->select('id','jenis_produk','umk_packing','umk_bandrol','umk_inner','umk_outer')->where('id',$row1_explode_hasil_kerja_1[0])->first();
//                     if(empty($row1_umk_borongan_lokal_1)){
//                         $row1_jenis_produk_1 = '-';
//                         $row1_hasil_kerja_1 = null;
//                         $row1_data_explode_hasil_kerja_1 = '-';
//                         $row1_lembur_1 = 1;
//                     }else{
//                         $row1_jenis_produk_1 = $row1_umk_borongan_lokal_1->jenis_produk;
//                         $row1_hasil_kerja_1 = $row1_explode_hasil_kerja_1[1]*$row1_umk_borongan_lokal_1->umk_bandrol;
//                         $row1_data_explode_hasil_kerja_1 = $row1_explode_hasil_kerja_1[1];
//                         // $row1_lembur_1 = 1.5;
//                         $explode_lembur_1 = explode("|",$row1_pengerjaan->lembur);
//                         $explode_status_lembur_1 = explode("-",$explode_lembur_1[1]);
//                         if($explode_status_lembur_1[1] == 'y'){
//                             $row1_lembur_1 = 1.5;
//                         }else{
//                             $row1_lembur_1 = 1;
//                         }
// $explode_lembur_2 = explode("|",$row1_pengerjaan->lembur);
//                         $explode_status_lembur_2 = explode("-",$explode_lembur_2[2]);
//                         if($explode_status_lembur_2[1] == 'y'){
//                             $row1_lembur_2 = 1.5;
//                         }else{
//                             $row1_lembur_2 = 1;
//                         }
// $explode_lembur_3 = explode("|",$row1_pengerjaan->lembur);
//                         $explode_status_lembur_3 = explode("-",$explode_lembur_3[3]);
//                         if($explode_status_lembur_3[1] == 'y'){
//                             $row1_lembur_3 = 1.5;
//                         }else{
//                             $row1_lembur_3 = 1;
//                         }
// $explode_lembur_4 = explode("|",$row1_pengerjaan->lembur);
//                         $explode_status_lembur_4 = explode("-",$explode_lembur_4[4]);
//                         if($explode_status_lembur_4[1] == 'y'){
//                             $row1_lembur_4 = 1.5;
//                         }else{
//                             $row1_lembur_4 = 1;
//                         }
// $explode_lembur_5 = explode("|",$row1_pengerjaan->lembur);
//                         $explode_status_lembur_5 = explode("-",$explode_lembur_5[5]);
//                         if($explode_status_lembur_5[1] == 'y'){
//                             $row1_lembur_5 = 1.5;
//                         }else{
//                             $row1_lembur_5 = 1;
//                         }
//                     }

//                     $row1_explode_hasil_kerja_2 = explode("|",$row1_pengerjaan->hasil_kerja_2);
//                     $row1_umk_borongan_lokal_2 = $this->umkBoronganLokal->select('id','jenis_produk','umk_packing','umk_bandrol','umk_inner','umk_outer')->where('id',$row1_explode_hasil_kerja_2[0])->first();
//                     if(empty($row1_umk_borongan_lokal_2)){
//                         $row1_jenis_produk_2 = '-';
//                         $row1_hasil_kerja_2 = null;
//                         $row1_data_explode_hasil_kerja_2 = '-';
//                         $row1_lembur_2 = 1;
//                     }else{
//                         $row1_jenis_produk_2 = $row1_umk_borongan_lokal_2->jenis_produk;
//                         $row1_hasil_kerja_2 = $row1_explode_hasil_kerja_2[1]*$row1_umk_borongan_lokal_2->umk_bandrol;
//                         $row1_data_explode_hasil_kerja_2 = $row1_explode_hasil_kerja_2[1];
//                         // $row1_lembur_2 = 1.5;
//                         $explode_lembur_1 = explode("|",$row1_pengerjaan->lembur);
//                         $explode_status_lembur_1 = explode("-",$explode_lembur_1[1]);
//                         if($explode_status_lembur_1[1] == 'y'){
//                             $row1_lembur_1 = 1.5;
//                         }else{
//                             $row1_lembur_1 = 1;
//                         }
// $explode_lembur_2 = explode("|",$row1_pengerjaan->lembur);
//                         $explode_status_lembur_2 = explode("-",$explode_lembur_2[2]);
//                         if($explode_status_lembur_2[1] == 'y'){
//                             $row1_lembur_2 = 1.5;
//                         }else{
//                             $row1_lembur_2 = 1;
//                         }
// $explode_lembur_3 = explode("|",$row1_pengerjaan->lembur);
//                         $explode_status_lembur_3 = explode("-",$explode_lembur_3[3]);
//                         if($explode_status_lembur_3[1] == 'y'){
//                             $row1_lembur_3 = 1.5;
//                         }else{
//                             $row1_lembur_3 = 1;
//                         }
// $explode_lembur_4 = explode("|",$row1_pengerjaan->lembur);
//                         $explode_status_lembur_4 = explode("-",$explode_lembur_4[4]);
//                         if($explode_status_lembur_4[1] == 'y'){
//                             $row1_lembur_4 = 1.5;
//                         }else{
//                             $row1_lembur_4 = 1;
//                         }
// $explode_lembur_5 = explode("|",$row1_pengerjaan->lembur);
//                         $explode_status_lembur_5 = explode("-",$explode_lembur_5[5]);
//                         if($explode_status_lembur_5[1] == 'y'){
//                             $row1_lembur_5 = 1.5;
//                         }else{
//                             $row1_lembur_5 = 1;
//                         }
//                     }

//                     $row1_explode_hasil_kerja_3 = explode("|",$row1_pengerjaan->hasil_kerja_3);
//                     $row1_umk_borongan_lokal_3 = $this->umkBoronganLokal->select('id','jenis_produk','umk_packing','umk_bandrol','umk_inner','umk_outer')->where('id',$row1_explode_hasil_kerja_3[0])->first();
//                     if(empty($row1_umk_borongan_lokal_3)){
//                         $row1_jenis_produk_3 = '-';
//                         $row1_hasil_kerja_3 = null;
//                         $row1_data_explode_hasil_kerja_3 = '-';
//                         $row1_lembur_3 = 1;
//                     }else{
//                         $row1_jenis_produk_3 = $row1_umk_borongan_lokal_3->jenis_produk;
//                         $row1_hasil_kerja_3 = $row1_explode_hasil_kerja_3[1]*$row1_umk_borongan_lokal_3->umk_bandrol;
//                         $row1_data_explode_hasil_kerja_3 = $row1_explode_hasil_kerja_3[1];
//                         // $row1_lembur_3 = 1.5;
//                         $explode_lembur_1 = explode("|",$row1_pengerjaan->lembur);
//                         $explode_status_lembur_1 = explode("-",$explode_lembur_1[1]);
//                         if($explode_status_lembur_1[1] == 'y'){
//                             $row1_lembur_1 = 1.5;
//                         }else{
//                             $row1_lembur_1 = 1;
//                         }
// $explode_lembur_2 = explode("|",$row1_pengerjaan->lembur);
//                         $explode_status_lembur_2 = explode("-",$explode_lembur_2[2]);
//                         if($explode_status_lembur_2[1] == 'y'){
//                             $row1_lembur_2 = 1.5;
//                         }else{
//                             $row1_lembur_2 = 1;
//                         }
// $explode_lembur_3 = explode("|",$row1_pengerjaan->lembur);
//                         $explode_status_lembur_3 = explode("-",$explode_lembur_3[3]);
//                         if($explode_status_lembur_3[1] == 'y'){
//                             $row1_lembur_3 = 1.5;
//                         }else{
//                             $row1_lembur_3 = 1;
//                         }
// $explode_lembur_4 = explode("|",$row1_pengerjaan->lembur);
//                         $explode_status_lembur_4 = explode("-",$explode_lembur_4[4]);
//                         if($explode_status_lembur_4[1] == 'y'){
//                             $row1_lembur_4 = 1.5;
//                         }else{
//                             $row1_lembur_4 = 1;
//                         }
// $explode_lembur_5 = explode("|",$row1_pengerjaan->lembur);
//                         $explode_status_lembur_5 = explode("-",$explode_lembur_5[5]);
//                         if($explode_status_lembur_5[1] == 'y'){
//                             $row1_lembur_5 = 1.5;
//                         }else{
//                             $row1_lembur_5 = 1;
//                         }
//                     }

//                     $row1_explode_hasil_kerja_4 = explode("|",$row1_pengerjaan->hasil_kerja_4);
//                     $row1_umk_borongan_lokal_4 = $this->umkBoronganLokal->select('id','jenis_produk','umk_packing','umk_bandrol','umk_inner','umk_outer')->where('id',$row1_explode_hasil_kerja_4[0])->first();
//                     if(empty($row1_umk_borongan_lokal_4)){
//                         $row1_jenis_produk_4 = '-';
//                         $row1_hasil_kerja_4 = null;
//                         $row1_data_explode_hasil_kerja_4 = '-';
//                         $row1_lembur_4 = 1;
//                     }else{
//                         $row1_jenis_produk_4 = $row1_umk_borongan_lokal_4->jenis_produk;
//                         $row1_hasil_kerja_4 = $row1_explode_hasil_kerja_4[1]*$row1_umk_borongan_lokal_4->umk_bandrol;
//                         $row1_data_explode_hasil_kerja_4 = $row1_explode_hasil_kerja_4[1];
//                         // $row1_lembur_4 = 1.5;
//                         $explode_lembur_1 = explode("|",$row1_pengerjaan->lembur);
//                         $explode_status_lembur_1 = explode("-",$explode_lembur_1[1]);
//                         if($explode_status_lembur_1[1] == 'y'){
//                             $row1_lembur_1 = 1.5;
//                         }else{
//                             $row1_lembur_1 = 1;
//                         }
// $explode_lembur_2 = explode("|",$row1_pengerjaan->lembur);
//                         $explode_status_lembur_2 = explode("-",$explode_lembur_2[2]);
//                         if($explode_status_lembur_2[1] == 'y'){
//                             $row1_lembur_2 = 1.5;
//                         }else{
//                             $row1_lembur_2 = 1;
//                         }
// $explode_lembur_3 = explode("|",$row1_pengerjaan->lembur);
//                         $explode_status_lembur_3 = explode("-",$explode_lembur_3[3]);
//                         if($explode_status_lembur_3[1] == 'y'){
//                             $row1_lembur_3 = 1.5;
//                         }else{
//                             $row1_lembur_3 = 1;
//                         }
// $explode_lembur_4 = explode("|",$row1_pengerjaan->lembur);
//                         $explode_status_lembur_4 = explode("-",$explode_lembur_4[4]);
//                         if($explode_status_lembur_4[1] == 'y'){
//                             $row1_lembur_4 = 1.5;
//                         }else{
//                             $row1_lembur_4 = 1;
//                         }
// $explode_lembur_5 = explode("|",$row1_pengerjaan->lembur);
//                         $explode_status_lembur_5 = explode("-",$explode_lembur_5[5]);
//                         if($explode_status_lembur_5[1] == 'y'){
//                             $row1_lembur_5 = 1.5;
//                         }else{
//                             $row1_lembur_5 = 1;
//                         }
//                     }

//                     $row1_explode_hasil_kerja_5 = explode("|",$row1_pengerjaan->hasil_kerja_5);
//                     $row1_umk_borongan_lokal_5 = $this->umkBoronganLokal->select('id','jenis_produk','umk_packing','umk_bandrol','umk_inner','umk_outer')->where('id',$row1_explode_hasil_kerja_5[0])->first();
//                     if(empty($row1_umk_borongan_lokal_5)){
//                         $row1_jenis_produk_5 = '-';
//                         $row1_hasil_kerja_5 = null;
//                         $row1_data_explode_hasil_kerja_5 = '-';
//                         $row1_lembur_5 = 1;
//                     }else{
//                         $row1_jenis_produk_5 = $row1_umk_borongan_lokal_5->jenis_produk;
//                         $row1_hasil_kerja_5 = $row1_explode_hasil_kerja_5[1]*$row1_umk_borongan_lokal_5->umk_bandrol;
//                         $row1_data_explode_hasil_kerja_5 = $row1_explode_hasil_kerja_5[1];
//                         // $row1_lembur_5 = 1.5;
//                         $explode_lembur_1 = explode("|",$row1_pengerjaan->lembur);
//                         $explode_status_lembur_1 = explode("-",$explode_lembur_1[1]);
//                         if($explode_status_lembur_1[1] == 'y'){
//                             $row1_lembur_1 = 1.5;
//                         }else{
//                             $row1_lembur_1 = 1;
//                         }
// $explode_lembur_2 = explode("|",$row1_pengerjaan->lembur);
//                         $explode_status_lembur_2 = explode("-",$explode_lembur_2[2]);
//                         if($explode_status_lembur_2[1] == 'y'){
//                             $row1_lembur_2 = 1.5;
//                         }else{
//                             $row1_lembur_2 = 1;
//                         }
// $explode_lembur_3 = explode("|",$row1_pengerjaan->lembur);
//                         $explode_status_lembur_3 = explode("-",$explode_lembur_3[3]);
//                         if($explode_status_lembur_3[1] == 'y'){
//                             $row1_lembur_3 = 1.5;
//                         }else{
//                             $row1_lembur_3 = 1;
//                         }
// $explode_lembur_4 = explode("|",$row1_pengerjaan->lembur);
//                         $explode_status_lembur_4 = explode("-",$explode_lembur_4[4]);
//                         if($explode_status_lembur_4[1] == 'y'){
//                             $row1_lembur_4 = 1.5;
//                         }else{
//                             $row1_lembur_4 = 1;
//                         }
// $explode_lembur_5 = explode("|",$row1_pengerjaan->lembur);
//                         $explode_status_lembur_5 = explode("-",$explode_lembur_5[5]);
//                         if($explode_status_lembur_5[1] == 'y'){
//                             $row1_lembur_5 = 1.5;
//                         }else{
//                             $row1_lembur_5 = 1;
//                         }
//                     }
//                 }

//                 if ($row1_pengerjaan->operator_karyawan->jenis_operator_detail_pekerjaan_id == 3) {
//                     $row1_explode_hasil_kerja_1 = explode("|",$row1_pengerjaan->hasil_kerja_1);
//                     $row1_umk_borongan_lokal_1 = $this->umkBoronganLokal->select('id','jenis_produk','umk_packing','umk_bandrol','umk_inner','umk_outer')->where('id',$row1_explode_hasil_kerja_1[0])->first();
//                     if(empty($row1_umk_borongan_lokal_1)){
//                         $row1_jenis_produk_1 = '-';
//                         $row1_hasil_kerja_1 = null;
//                         $row1_data_explode_hasil_kerja_1 = '-';
//                         $row1_lembur_1 = 1;
//                     }else{
//                         $row1_jenis_produk_1 = $row1_umk_borongan_lokal_1->jenis_produk;
//                         $row1_hasil_kerja_1 = $row1_explode_hasil_kerja_1[1]*$row1_umk_borongan_lokal_1->umk_inner;
//                         $row1_data_explode_hasil_kerja_1 = $row1_explode_hasil_kerja_1[1];
//                         // $row1_lembur_1 = 1.5;
//                         $explode_lembur_1 = explode("|",$row1_pengerjaan->lembur);
//                         $explode_status_lembur_1 = explode("-",$explode_lembur_1[1]);
//                         if($explode_status_lembur_1[1] == 'y'){
//                             $row1_lembur_1 = 1.5;
//                         }else{
//                             $row1_lembur_1 = 1;
//                         }
// $explode_lembur_2 = explode("|",$row1_pengerjaan->lembur);
//                         $explode_status_lembur_2 = explode("-",$explode_lembur_2[2]);
//                         if($explode_status_lembur_2[1] == 'y'){
//                             $row1_lembur_2 = 1.5;
//                         }else{
//                             $row1_lembur_2 = 1;
//                         }
// $explode_lembur_3 = explode("|",$row1_pengerjaan->lembur);
//                         $explode_status_lembur_3 = explode("-",$explode_lembur_3[3]);
//                         if($explode_status_lembur_3[1] == 'y'){
//                             $row1_lembur_3 = 1.5;
//                         }else{
//                             $row1_lembur_3 = 1;
//                         }
// $explode_lembur_4 = explode("|",$row1_pengerjaan->lembur);
//                         $explode_status_lembur_4 = explode("-",$explode_lembur_4[4]);
//                         if($explode_status_lembur_4[1] == 'y'){
//                             $row1_lembur_4 = 1.5;
//                         }else{
//                             $row1_lembur_4 = 1;
//                         }
// $explode_lembur_5 = explode("|",$row1_pengerjaan->lembur);
//                         $explode_status_lembur_5 = explode("-",$explode_lembur_5[5]);
//                         if($explode_status_lembur_5[1] == 'y'){
//                             $row1_lembur_5 = 1.5;
//                         }else{
//                             $row1_lembur_5 = 1;
//                         }
//                     }

//                     $row1_explode_hasil_kerja_2 = explode("|",$row1_pengerjaan->hasil_kerja_2);
//                     $row1_umk_borongan_lokal_2 = $this->umkBoronganLokal->select('id','jenis_produk','umk_packing','umk_bandrol','umk_inner','umk_outer')->where('id',$row1_explode_hasil_kerja_2[0])->first();
//                     if(empty($row1_umk_borongan_lokal_2)){
//                         $row1_jenis_produk_2 = '-';
//                         $row1_hasil_kerja_2 = null;
//                         $row1_data_explode_hasil_kerja_2 = '-';
//                         $row1_lembur_2 = 1;
//                     }else{
//                         $row1_jenis_produk_2 = $row1_umk_borongan_lokal_2->jenis_produk;
//                         $row1_hasil_kerja_2 = $row1_explode_hasil_kerja_2[1]*$row1_umk_borongan_lokal_2->umk_inner;
//                         $row1_data_explode_hasil_kerja_2 = $row1_explode_hasil_kerja_2[1];
//                         // $row1_lembur_2 = 1.5;
//                         $explode_lembur_1 = explode("|",$row1_pengerjaan->lembur);
//                         $explode_status_lembur_1 = explode("-",$explode_lembur_1[1]);
//                         if($explode_status_lembur_1[1] == 'y'){
//                             $row1_lembur_1 = 1.5;
//                         }else{
//                             $row1_lembur_1 = 1;
//                         }
// $explode_lembur_2 = explode("|",$row1_pengerjaan->lembur);
//                         $explode_status_lembur_2 = explode("-",$explode_lembur_2[2]);
//                         if($explode_status_lembur_2[1] == 'y'){
//                             $row1_lembur_2 = 1.5;
//                         }else{
//                             $row1_lembur_2 = 1;
//                         }
// $explode_lembur_3 = explode("|",$row1_pengerjaan->lembur);
//                         $explode_status_lembur_3 = explode("-",$explode_lembur_3[3]);
//                         if($explode_status_lembur_3[1] == 'y'){
//                             $row1_lembur_3 = 1.5;
//                         }else{
//                             $row1_lembur_3 = 1;
//                         }
// $explode_lembur_4 = explode("|",$row1_pengerjaan->lembur);
//                         $explode_status_lembur_4 = explode("-",$explode_lembur_4[4]);
//                         if($explode_status_lembur_4[1] == 'y'){
//                             $row1_lembur_4 = 1.5;
//                         }else{
//                             $row1_lembur_4 = 1;
//                         }
// $explode_lembur_5 = explode("|",$row1_pengerjaan->lembur);
//                         $explode_status_lembur_5 = explode("-",$explode_lembur_5[5]);
//                         if($explode_status_lembur_5[1] == 'y'){
//                             $row1_lembur_5 = 1.5;
//                         }else{
//                             $row1_lembur_5 = 1;
//                         }
//                     }

//                     $row1_explode_hasil_kerja_3 = explode("|",$row1_pengerjaan->hasil_kerja_3);
//                     $row1_umk_borongan_lokal_3 = $this->umkBoronganLokal->select('id','jenis_produk','umk_packing','umk_bandrol','umk_inner','umk_outer')->where('id',$row1_explode_hasil_kerja_3[0])->first();
//                     if(empty($row1_umk_borongan_lokal_3)){
//                         $row1_jenis_produk_3 = '-';
//                         $row1_hasil_kerja_3 = null;
//                         $row1_data_explode_hasil_kerja_3 = '-';
//                         $row1_lembur_3 = 1;
//                     }else{
//                         $row1_jenis_produk_3 = $row1_umk_borongan_lokal_3->jenis_produk;
//                         $row1_hasil_kerja_3 = $row1_explode_hasil_kerja_3[1]*$row1_umk_borongan_lokal_3->umk_inner;
//                         $row1_data_explode_hasil_kerja_3 = $row1_explode_hasil_kerja_3[1];
//                         // $row1_lembur_3 = 1.5;
//                         $explode_lembur_1 = explode("|",$row1_pengerjaan->lembur);
//                         $explode_status_lembur_1 = explode("-",$explode_lembur_1[1]);
//                         if($explode_status_lembur_1[1] == 'y'){
//                             $row1_lembur_1 = 1.5;
//                         }else{
//                             $row1_lembur_1 = 1;
//                         }
// $explode_lembur_2 = explode("|",$row1_pengerjaan->lembur);
//                         $explode_status_lembur_2 = explode("-",$explode_lembur_2[2]);
//                         if($explode_status_lembur_2[1] == 'y'){
//                             $row1_lembur_2 = 1.5;
//                         }else{
//                             $row1_lembur_2 = 1;
//                         }
// $explode_lembur_3 = explode("|",$row1_pengerjaan->lembur);
//                         $explode_status_lembur_3 = explode("-",$explode_lembur_3[3]);
//                         if($explode_status_lembur_3[1] == 'y'){
//                             $row1_lembur_3 = 1.5;
//                         }else{
//                             $row1_lembur_3 = 1;
//                         }
// $explode_lembur_4 = explode("|",$row1_pengerjaan->lembur);
//                         $explode_status_lembur_4 = explode("-",$explode_lembur_4[4]);
//                         if($explode_status_lembur_4[1] == 'y'){
//                             $row1_lembur_4 = 1.5;
//                         }else{
//                             $row1_lembur_4 = 1;
//                         }
// $explode_lembur_5 = explode("|",$row1_pengerjaan->lembur);
//                         $explode_status_lembur_5 = explode("-",$explode_lembur_5[5]);
//                         if($explode_status_lembur_5[1] == 'y'){
//                             $row1_lembur_5 = 1.5;
//                         }else{
//                             $row1_lembur_5 = 1;
//                         }
//                     }

//                     $row1_explode_hasil_kerja_4 = explode("|",$row1_pengerjaan->hasil_kerja_4);
//                     $row1_umk_borongan_lokal_4 = $this->umkBoronganLokal->select('id','jenis_produk','umk_packing','umk_bandrol','umk_inner','umk_outer')->where('id',$row1_explode_hasil_kerja_4[0])->first();
//                     if(empty($row1_umk_borongan_lokal_4)){
//                         $row1_jenis_produk_4 = '-';
//                         $row1_hasil_kerja_4 = null;
//                         $row1_data_explode_hasil_kerja_4 = '-';
//                         $row1_lembur_4 = 1;
//                     }else{
//                         $row1_jenis_produk_4 = $row1_umk_borongan_lokal_4->jenis_produk;
//                         $row1_hasil_kerja_4 = $row1_explode_hasil_kerja_4[1]*$row1_umk_borongan_lokal_4->umk_inner;
//                         $row1_data_explode_hasil_kerja_4 = $row1_explode_hasil_kerja_4[1];
//                         // $row1_lembur_4 = 1.5;
//                         $explode_lembur_1 = explode("|",$row1_pengerjaan->lembur);
//                         $explode_status_lembur_1 = explode("-",$explode_lembur_1[1]);
//                         if($explode_status_lembur_1[1] == 'y'){
//                             $row1_lembur_1 = 1.5;
//                         }else{
//                             $row1_lembur_1 = 1;
//                         }
// $explode_lembur_2 = explode("|",$row1_pengerjaan->lembur);
//                         $explode_status_lembur_2 = explode("-",$explode_lembur_2[2]);
//                         if($explode_status_lembur_2[1] == 'y'){
//                             $row1_lembur_2 = 1.5;
//                         }else{
//                             $row1_lembur_2 = 1;
//                         }
// $explode_lembur_3 = explode("|",$row1_pengerjaan->lembur);
//                         $explode_status_lembur_3 = explode("-",$explode_lembur_3[3]);
//                         if($explode_status_lembur_3[1] == 'y'){
//                             $row1_lembur_3 = 1.5;
//                         }else{
//                             $row1_lembur_3 = 1;
//                         }
// $explode_lembur_4 = explode("|",$row1_pengerjaan->lembur);
//                         $explode_status_lembur_4 = explode("-",$explode_lembur_4[4]);
//                         if($explode_status_lembur_4[1] == 'y'){
//                             $row1_lembur_4 = 1.5;
//                         }else{
//                             $row1_lembur_4 = 1;
//                         }
// $explode_lembur_5 = explode("|",$row1_pengerjaan->lembur);
//                         $explode_status_lembur_5 = explode("-",$explode_lembur_5[5]);
//                         if($explode_status_lembur_5[1] == 'y'){
//                             $row1_lembur_5 = 1.5;
//                         }else{
//                             $row1_lembur_5 = 1;
//                         }
//                     }

//                     $row1_explode_hasil_kerja_5 = explode("|",$row1_pengerjaan->hasil_kerja_5);
//                     $row1_umk_borongan_lokal_5 = $this->umkBoronganLokal->select('id','jenis_produk','umk_packing','umk_bandrol','umk_inner','umk_outer')->where('id',$row1_explode_hasil_kerja_5[0])->first();
//                     if(empty($row1_umk_borongan_lokal_5)){
//                         $row1_jenis_produk_5 = '-';
//                         $row1_hasil_kerja_5 = null;
//                         $row1_data_explode_hasil_kerja_5 = '-';
//                         $row1_lembur_5 = 1;
//                     }else{
//                         $row1_jenis_produk_5 = $row1_umk_borongan_lokal_5->jenis_produk;
//                         $row1_hasil_kerja_5 = $row1_explode_hasil_kerja_5[1]*$row1_umk_borongan_lokal_5->umk_inner;
//                         $row1_data_explode_hasil_kerja_5 = $row1_explode_hasil_kerja_5[1];
//                         // $row1_lembur_5 = 1.5;
//                         $explode_lembur_1 = explode("|",$row1_pengerjaan->lembur);
//                         $explode_status_lembur_1 = explode("-",$explode_lembur_1[1]);
//                         if($explode_status_lembur_1[1] == 'y'){
//                             $row1_lembur_1 = 1.5;
//                         }else{
//                             $row1_lembur_1 = 1;
//                         }
// $explode_lembur_2 = explode("|",$row1_pengerjaan->lembur);
//                         $explode_status_lembur_2 = explode("-",$explode_lembur_2[2]);
//                         if($explode_status_lembur_2[1] == 'y'){
//                             $row1_lembur_2 = 1.5;
//                         }else{
//                             $row1_lembur_2 = 1;
//                         }
// $explode_lembur_3 = explode("|",$row1_pengerjaan->lembur);
//                         $explode_status_lembur_3 = explode("-",$explode_lembur_3[3]);
//                         if($explode_status_lembur_3[1] == 'y'){
//                             $row1_lembur_3 = 1.5;
//                         }else{
//                             $row1_lembur_3 = 1;
//                         }
// $explode_lembur_4 = explode("|",$row1_pengerjaan->lembur);
//                         $explode_status_lembur_4 = explode("-",$explode_lembur_4[4]);
//                         if($explode_status_lembur_4[1] == 'y'){
//                             $row1_lembur_4 = 1.5;
//                         }else{
//                             $row1_lembur_4 = 1;
//                         }
// $explode_lembur_5 = explode("|",$row1_pengerjaan->lembur);
//                         $explode_status_lembur_5 = explode("-",$explode_lembur_5[5]);
//                         if($explode_status_lembur_5[1] == 'y'){
//                             $row1_lembur_5 = 1.5;
//                         }else{
//                             $row1_lembur_5 = 1;
//                         }
//                     }
//                 }

//                 if ($row1_pengerjaan->operator_karyawan->jenis_operator_detail_pekerjaan_id == 4) {
//                     $row1_explode_hasil_kerja_1 = explode("|",$row1_pengerjaan->hasil_kerja_1);
//                     $row1_umk_borongan_lokal_1 = $this->umkBoronganLokal->select('id','jenis_produk','umk_packing','umk_bandrol','umk_inner','umk_outer')->where('id',$row1_explode_hasil_kerja_1[0])->first();
//                     if(empty($row1_umk_borongan_lokal_1)){
//                         $row1_jenis_produk_1 = '-';
//                         $row1_hasil_kerja_1 = null;
//                         $row1_data_explode_hasil_kerja_1 = '-';
//                         $row1_lembur_1 = 1;
//                     }else{
//                         $row1_jenis_produk_1 = $row1_umk_borongan_lokal_1->jenis_produk;
//                         $row1_hasil_kerja_1 = $row1_explode_hasil_kerja_1[1]*$row1_umk_borongan_lokal_1->umk_outer;
//                         $row1_data_explode_hasil_kerja_1 = $row1_explode_hasil_kerja_1[1];
//                         // $row1_lembur_1 = 1.5;
//                         $explode_lembur_1 = explode("|",$row1_pengerjaan->lembur);
//                         $explode_status_lembur_1 = explode("-",$explode_lembur_1[1]);
//                         if($explode_status_lembur_1[1] == 'y'){
//                             $row1_lembur_1 = 1.5;
//                         }else{
//                             $row1_lembur_1 = 1;
//                         }
// $explode_lembur_2 = explode("|",$row1_pengerjaan->lembur);
//                         $explode_status_lembur_2 = explode("-",$explode_lembur_2[2]);
//                         if($explode_status_lembur_2[1] == 'y'){
//                             $row1_lembur_2 = 1.5;
//                         }else{
//                             $row1_lembur_2 = 1;
//                         }
// $explode_lembur_3 = explode("|",$row1_pengerjaan->lembur);
//                         $explode_status_lembur_3 = explode("-",$explode_lembur_3[3]);
//                         if($explode_status_lembur_3[1] == 'y'){
//                             $row1_lembur_3 = 1.5;
//                         }else{
//                             $row1_lembur_3 = 1;
//                         }
// $explode_lembur_4 = explode("|",$row1_pengerjaan->lembur);
//                         $explode_status_lembur_4 = explode("-",$explode_lembur_4[4]);
//                         if($explode_status_lembur_4[1] == 'y'){
//                             $row1_lembur_4 = 1.5;
//                         }else{
//                             $row1_lembur_4 = 1;
//                         }
// $explode_lembur_5 = explode("|",$row1_pengerjaan->lembur);
//                         $explode_status_lembur_5 = explode("-",$explode_lembur_5[5]);
//                         if($explode_status_lembur_5[1] == 'y'){
//                             $row1_lembur_5 = 1.5;
//                         }else{
//                             $row1_lembur_5 = 1;
//                         }
//                     }

//                     $row1_explode_hasil_kerja_2 = explode("|",$row1_pengerjaan->hasil_kerja_2);
//                     $row1_umk_borongan_lokal_2 = $this->umkBoronganLokal->select('id','jenis_produk','umk_packing','umk_bandrol','umk_inner','umk_outer')->where('id',$row1_explode_hasil_kerja_2[0])->first();
//                     if(empty($row1_umk_borongan_lokal_2)){
//                         $row1_jenis_produk_2 = '-';
//                         $row1_hasil_kerja_2 = null;
//                         $row1_data_explode_hasil_kerja_2 = '-';
//                         $row1_lembur_2 = 1;
//                     }else{
//                         $row1_jenis_produk_2 = $row1_umk_borongan_lokal_2->jenis_produk;
//                         $row1_hasil_kerja_2 = $row1_explode_hasil_kerja_2[1]*$row1_umk_borongan_lokal_2->umk_outer;
//                         $row1_data_explode_hasil_kerja_2 = $row1_explode_hasil_kerja_2[1];
//                         // $row1_lembur_2 = 1.5;
//                         $explode_lembur_1 = explode("|",$row1_pengerjaan->lembur);
//                         $explode_status_lembur_1 = explode("-",$explode_lembur_1[1]);
//                         if($explode_status_lembur_1[1] == 'y'){
//                             $row1_lembur_1 = 1.5;
//                         }else{
//                             $row1_lembur_1 = 1;
//                         }
// $explode_lembur_2 = explode("|",$row1_pengerjaan->lembur);
//                         $explode_status_lembur_2 = explode("-",$explode_lembur_2[2]);
//                         if($explode_status_lembur_2[1] == 'y'){
//                             $row1_lembur_2 = 1.5;
//                         }else{
//                             $row1_lembur_2 = 1;
//                         }
// $explode_lembur_3 = explode("|",$row1_pengerjaan->lembur);
//                         $explode_status_lembur_3 = explode("-",$explode_lembur_3[3]);
//                         if($explode_status_lembur_3[1] == 'y'){
//                             $row1_lembur_3 = 1.5;
//                         }else{
//                             $row1_lembur_3 = 1;
//                         }
// $explode_lembur_4 = explode("|",$row1_pengerjaan->lembur);
//                         $explode_status_lembur_4 = explode("-",$explode_lembur_4[4]);
//                         if($explode_status_lembur_4[1] == 'y'){
//                             $row1_lembur_4 = 1.5;
//                         }else{
//                             $row1_lembur_4 = 1;
//                         }
// $explode_lembur_5 = explode("|",$row1_pengerjaan->lembur);
//                         $explode_status_lembur_5 = explode("-",$explode_lembur_5[5]);
//                         if($explode_status_lembur_5[1] == 'y'){
//                             $row1_lembur_5 = 1.5;
//                         }else{
//                             $row1_lembur_5 = 1;
//                         }
//                     }

//                     $row1_explode_hasil_kerja_3 = explode("|",$row1_pengerjaan->hasil_kerja_3);
//                     $row1_umk_borongan_lokal_3 = $this->umkBoronganLokal->select('id','jenis_produk','umk_packing','umk_bandrol','umk_inner','umk_outer')->where('id',$row1_explode_hasil_kerja_3[0])->first();
//                     if(empty($row1_umk_borongan_lokal_3)){
//                         $row1_jenis_produk_3 = '-';
//                         $row1_hasil_kerja_3 = null;
//                         $row1_data_explode_hasil_kerja_3 = '-';
//                         $row1_lembur_3 = 1;
//                     }else{
//                         $row1_jenis_produk_3 = $row1_umk_borongan_lokal_3->jenis_produk;
//                         $row1_hasil_kerja_3 = $row1_explode_hasil_kerja_3[1]*$row1_umk_borongan_lokal_3->umk_outer;
//                         $row1_data_explode_hasil_kerja_3 = $row1_explode_hasil_kerja_3[1];
//                         // $row1_lembur_3 = 1.5;
//                         $explode_lembur_1 = explode("|",$row1_pengerjaan->lembur);
//                         $explode_status_lembur_1 = explode("-",$explode_lembur_1[1]);
//                         if($explode_status_lembur_1[1] == 'y'){
//                             $row1_lembur_1 = 1.5;
//                         }else{
//                             $row1_lembur_1 = 1;
//                         }
// $explode_lembur_2 = explode("|",$row1_pengerjaan->lembur);
//                         $explode_status_lembur_2 = explode("-",$explode_lembur_2[2]);
//                         if($explode_status_lembur_2[1] == 'y'){
//                             $row1_lembur_2 = 1.5;
//                         }else{
//                             $row1_lembur_2 = 1;
//                         }
// $explode_lembur_3 = explode("|",$row1_pengerjaan->lembur);
//                         $explode_status_lembur_3 = explode("-",$explode_lembur_3[3]);
//                         if($explode_status_lembur_3[1] == 'y'){
//                             $row1_lembur_3 = 1.5;
//                         }else{
//                             $row1_lembur_3 = 1;
//                         }
// $explode_lembur_4 = explode("|",$row1_pengerjaan->lembur);
//                         $explode_status_lembur_4 = explode("-",$explode_lembur_4[4]);
//                         if($explode_status_lembur_4[1] == 'y'){
//                             $row1_lembur_4 = 1.5;
//                         }else{
//                             $row1_lembur_4 = 1;
//                         }
// $explode_lembur_5 = explode("|",$row1_pengerjaan->lembur);
//                         $explode_status_lembur_5 = explode("-",$explode_lembur_5[5]);
//                         if($explode_status_lembur_5[1] == 'y'){
//                             $row1_lembur_5 = 1.5;
//                         }else{
//                             $row1_lembur_5 = 1;
//                         }
//                     }

//                     $row1_explode_hasil_kerja_4 = explode("|",$row1_pengerjaan->hasil_kerja_4);
//                     $row1_umk_borongan_lokal_4 = $this->umkBoronganLokal->select('id','jenis_produk','umk_packing','umk_bandrol','umk_inner','umk_outer')->where('id',$row1_explode_hasil_kerja_4[0])->first();
//                     if(empty($row1_umk_borongan_lokal_4)){
//                         $row1_jenis_produk_4 = '-';
//                         $row1_hasil_kerja_4 = null;
//                         $row1_data_explode_hasil_kerja_4 = '-';
//                         $row1_lembur_4 = 1;
//                     }else{
//                         $row1_jenis_produk_4 = $row1_umk_borongan_lokal_4->jenis_produk;
//                         $row1_hasil_kerja_4 = $row1_explode_hasil_kerja_4[1]*$row1_umk_borongan_lokal_4->umk_outer;
//                         $row1_data_explode_hasil_kerja_4 = $row1_explode_hasil_kerja_4[1];
//                         // $row1_lembur_4 = 1.5;
//                         $explode_lembur_1 = explode("|",$row1_pengerjaan->lembur);
//                         $explode_status_lembur_1 = explode("-",$explode_lembur_1[1]);
//                         if($explode_status_lembur_1[1] == 'y'){
//                             $row1_lembur_1 = 1.5;
//                         }else{
//                             $row1_lembur_1 = 1;
//                         }
// $explode_lembur_2 = explode("|",$row1_pengerjaan->lembur);
//                         $explode_status_lembur_2 = explode("-",$explode_lembur_2[2]);
//                         if($explode_status_lembur_2[1] == 'y'){
//                             $row1_lembur_2 = 1.5;
//                         }else{
//                             $row1_lembur_2 = 1;
//                         }
// $explode_lembur_3 = explode("|",$row1_pengerjaan->lembur);
//                         $explode_status_lembur_3 = explode("-",$explode_lembur_3[3]);
//                         if($explode_status_lembur_3[1] == 'y'){
//                             $row1_lembur_3 = 1.5;
//                         }else{
//                             $row1_lembur_3 = 1;
//                         }
// $explode_lembur_4 = explode("|",$row1_pengerjaan->lembur);
//                         $explode_status_lembur_4 = explode("-",$explode_lembur_4[4]);
//                         if($explode_status_lembur_4[1] == 'y'){
//                             $row1_lembur_4 = 1.5;
//                         }else{
//                             $row1_lembur_4 = 1;
//                         }
// $explode_lembur_5 = explode("|",$row1_pengerjaan->lembur);
//                         $explode_status_lembur_5 = explode("-",$explode_lembur_5[5]);
//                         if($explode_status_lembur_5[1] == 'y'){
//                             $row1_lembur_5 = 1.5;
//                         }else{
//                             $row1_lembur_5 = 1;
//                         }
//                     }

//                     $row1_explode_hasil_kerja_5 = explode("|",$row1_pengerjaan->hasil_kerja_5);
//                     $row1_umk_borongan_lokal_5 = $this->umkBoronganLokal->select('id','jenis_produk','umk_packing','umk_bandrol','umk_inner','umk_outer')->where('id',$row1_explode_hasil_kerja_5[0])->first();
//                     if(empty($row1_umk_borongan_lokal_5)){
//                         $row1_jenis_produk_5 = '-';
//                         $row1_hasil_kerja_5 = null;
//                         $row1_data_explode_hasil_kerja_5 = '-';
//                         $row1_lembur_5 = 1;
//                     }else{
//                         $row1_jenis_produk_5 = $row1_umk_borongan_lokal_5->jenis_produk;
//                         $row1_hasil_kerja_5 = $row1_explode_hasil_kerja_5[1]*$row1_umk_borongan_lokal_5->umk_outer;
//                         $row1_data_explode_hasil_kerja_5 = $row1_explode_hasil_kerja_5[1];
//                         // $row1_lembur_5 = 1.5;
//                         $explode_lembur_1 = explode("|",$row1_pengerjaan->lembur);
//                         $explode_status_lembur_1 = explode("-",$explode_lembur_1[1]);
//                         if($explode_status_lembur_1[1] == 'y'){
//                             $row1_lembur_1 = 1.5;
//                         }else{
//                             $row1_lembur_1 = 1;
//                         }
// $explode_lembur_2 = explode("|",$row1_pengerjaan->lembur);
//                         $explode_status_lembur_2 = explode("-",$explode_lembur_2[2]);
//                         if($explode_status_lembur_2[1] == 'y'){
//                             $row1_lembur_2 = 1.5;
//                         }else{
//                             $row1_lembur_2 = 1;
//                         }
// $explode_lembur_3 = explode("|",$row1_pengerjaan->lembur);
//                         $explode_status_lembur_3 = explode("-",$explode_lembur_3[3]);
//                         if($explode_status_lembur_3[1] == 'y'){
//                             $row1_lembur_3 = 1.5;
//                         }else{
//                             $row1_lembur_3 = 1;
//                         }
// $explode_lembur_4 = explode("|",$row1_pengerjaan->lembur);
//                         $explode_status_lembur_4 = explode("-",$explode_lembur_4[4]);
//                         if($explode_status_lembur_4[1] == 'y'){
//                             $row1_lembur_4 = 1.5;
//                         }else{
//                             $row1_lembur_4 = 1;
//                         }
// $explode_lembur_5 = explode("|",$row1_pengerjaan->lembur);
//                         $explode_status_lembur_5 = explode("-",$explode_lembur_5[5]);
//                         if($explode_status_lembur_5[1] == 'y'){
//                             $row1_lembur_5 = 1.5;
//                         }else{
//                             $row1_lembur_5 = 1;
//                         }
//                     }
//                 }
//                 //End Lokal

//                 //Begin Ekspor
//                 if ($row1_pengerjaan->operator_karyawan->jenis_operator_detail_pekerjaan_id == 5) {
//                     $row1_explode_hasil_kerja_1 = explode("|",$row1_pengerjaan->hasil_kerja_1);
//                     $row1_umk_borongan_lokal_1 = $this->umkBoronganEkspor->select('id','jenis_produk','umk_packing','umk_kemas','umk_pilih_gagang')->where('id',$row1_explode_hasil_kerja_1[0])->first();
//                     if(empty($row1_umk_borongan_lokal_1)){
//                         $row1_jenis_produk_1 = '-';
//                         $row1_hasil_kerja_1 = null;
//                         $row1_data_explode_hasil_kerja_1 = '-';
//                         $row1_lembur_1 = 1;
//                     }else{
//                         $row1_jenis_produk_1 = $row1_umk_borongan_lokal_1->jenis_produk;
//                         $row1_hasil_kerja_1 = $row1_explode_hasil_kerja_1[1]*$row1_umk_borongan_lokal_1->umk_packing;
//                         $row1_data_explode_hasil_kerja_1 = $row1_explode_hasil_kerja_1[1];
//                         // $row1_lembur_1 = 1.5;
//                         $explode_lembur_1 = explode("|",$row1_pengerjaan->lembur);
//                         $explode_status_lembur_1 = explode("-",$explode_lembur_1[1]);
//                         if($explode_status_lembur_1[1] == 'y'){
//                             $row1_lembur_1 = 1.5;
//                         }else{
//                             $row1_lembur_1 = 1;
//                         }
// $explode_lembur_2 = explode("|",$row1_pengerjaan->lembur);
//                         $explode_status_lembur_2 = explode("-",$explode_lembur_2[2]);
//                         if($explode_status_lembur_2[1] == 'y'){
//                             $row1_lembur_2 = 1.5;
//                         }else{
//                             $row1_lembur_2 = 1;
//                         }
// $explode_lembur_3 = explode("|",$row1_pengerjaan->lembur);
//                         $explode_status_lembur_3 = explode("-",$explode_lembur_3[3]);
//                         if($explode_status_lembur_3[1] == 'y'){
//                             $row1_lembur_3 = 1.5;
//                         }else{
//                             $row1_lembur_3 = 1;
//                         }
// $explode_lembur_4 = explode("|",$row1_pengerjaan->lembur);
//                         $explode_status_lembur_4 = explode("-",$explode_lembur_4[4]);
//                         if($explode_status_lembur_4[1] == 'y'){
//                             $row1_lembur_4 = 1.5;
//                         }else{
//                             $row1_lembur_4 = 1;
//                         }
// $explode_lembur_5 = explode("|",$row1_pengerjaan->lembur);
//                         $explode_status_lembur_5 = explode("-",$explode_lembur_5[5]);
//                         if($explode_status_lembur_5[1] == 'y'){
//                             $row1_lembur_5 = 1.5;
//                         }else{
//                             $row1_lembur_5 = 1;
//                         }
//                     }

//                     $row1_explode_hasil_kerja_2 = explode("|",$row1_pengerjaan->hasil_kerja_2);
//                     $row1_umk_borongan_lokal_2 = $this->umkBoronganEkspor->select('id','jenis_produk','umk_packing','umk_kemas','umk_pilih_gagang')->where('id',$row1_explode_hasil_kerja_1[0])->first();
//                     if(empty($row1_umk_borongan_lokal_2)){
//                         $row1_jenis_produk_2 = '-';
//                         $row1_hasil_kerja_2 = null;
//                         $row1_data_explode_hasil_kerja_2 = '-';
//                         $row1_lembur_2 = 1;
//                     }else{
//                         $row1_jenis_produk_2 = $row1_umk_borongan_lokal_2->jenis_produk;
//                         $row1_hasil_kerja_2 = $row1_explode_hasil_kerja_2[1]*$row1_umk_borongan_lokal_2->umk_packing;
//                         $row1_data_explode_hasil_kerja_2 = $row1_explode_hasil_kerja_2[1];
//                         // $row1_lembur_2 = 1.5;
//                         $explode_lembur_1 = explode("|",$row1_pengerjaan->lembur);
//                         $explode_status_lembur_1 = explode("-",$explode_lembur_1[1]);
//                         if($explode_status_lembur_1[1] == 'y'){
//                             $row1_lembur_1 = 1.5;
//                         }else{
//                             $row1_lembur_1 = 1;
//                         }
// $explode_lembur_2 = explode("|",$row1_pengerjaan->lembur);
//                         $explode_status_lembur_2 = explode("-",$explode_lembur_2[2]);
//                         if($explode_status_lembur_2[1] == 'y'){
//                             $row1_lembur_2 = 1.5;
//                         }else{
//                             $row1_lembur_2 = 1;
//                         }
// $explode_lembur_3 = explode("|",$row1_pengerjaan->lembur);
//                         $explode_status_lembur_3 = explode("-",$explode_lembur_3[3]);
//                         if($explode_status_lembur_3[1] == 'y'){
//                             $row1_lembur_3 = 1.5;
//                         }else{
//                             $row1_lembur_3 = 1;
//                         }
// $explode_lembur_4 = explode("|",$row1_pengerjaan->lembur);
//                         $explode_status_lembur_4 = explode("-",$explode_lembur_4[4]);
//                         if($explode_status_lembur_4[1] == 'y'){
//                             $row1_lembur_4 = 1.5;
//                         }else{
//                             $row1_lembur_4 = 1;
//                         }
// $explode_lembur_5 = explode("|",$row1_pengerjaan->lembur);
//                         $explode_status_lembur_5 = explode("-",$explode_lembur_5[5]);
//                         if($explode_status_lembur_5[1] == 'y'){
//                             $row1_lembur_5 = 1.5;
//                         }else{
//                             $row1_lembur_5 = 1;
//                         }
//                     }

//                     $row1_explode_hasil_kerja_3 = explode("|",$row1_pengerjaan->hasil_kerja_3);
//                     $row1_umk_borongan_lokal_3 = $this->umkBoronganEkspor->select('id','jenis_produk','umk_packing','umk_kemas','umk_pilih_gagang')->where('id',$row1_explode_hasil_kerja_1[0])->first();
//                     if(empty($row1_umk_borongan_lokal_3)){
//                         $row1_jenis_produk_3 = '-';
//                         $row1_hasil_kerja_3 = null;
//                         $row1_data_explode_hasil_kerja_3 = '-';
//                         $row1_lembur_3 = 1;
//                     }else{
//                         $row1_jenis_produk_3 = $row1_umk_borongan_lokal_3->jenis_produk;
//                         $row1_hasil_kerja_3 = $row1_explode_hasil_kerja_3[1]*$row1_umk_borongan_lokal_3->umk_packing;
//                         $row1_data_explode_hasil_kerja_3 = $row1_explode_hasil_kerja_3[1];
//                         // $row1_lembur_3 = 1.5;
//                         $explode_lembur_1 = explode("|",$row1_pengerjaan->lembur);
//                         $explode_status_lembur_1 = explode("-",$explode_lembur_1[1]);
//                         if($explode_status_lembur_1[1] == 'y'){
//                             $row1_lembur_1 = 1.5;
//                         }else{
//                             $row1_lembur_1 = 1;
//                         }
// $explode_lembur_2 = explode("|",$row1_pengerjaan->lembur);
//                         $explode_status_lembur_2 = explode("-",$explode_lembur_2[2]);
//                         if($explode_status_lembur_2[1] == 'y'){
//                             $row1_lembur_2 = 1.5;
//                         }else{
//                             $row1_lembur_2 = 1;
//                         }
// $explode_lembur_3 = explode("|",$row1_pengerjaan->lembur);
//                         $explode_status_lembur_3 = explode("-",$explode_lembur_3[3]);
//                         if($explode_status_lembur_3[1] == 'y'){
//                             $row1_lembur_3 = 1.5;
//                         }else{
//                             $row1_lembur_3 = 1;
//                         }
// $explode_lembur_4 = explode("|",$row1_pengerjaan->lembur);
//                         $explode_status_lembur_4 = explode("-",$explode_lembur_4[4]);
//                         if($explode_status_lembur_4[1] == 'y'){
//                             $row1_lembur_4 = 1.5;
//                         }else{
//                             $row1_lembur_4 = 1;
//                         }
// $explode_lembur_5 = explode("|",$row1_pengerjaan->lembur);
//                         $explode_status_lembur_5 = explode("-",$explode_lembur_5[5]);
//                         if($explode_status_lembur_5[1] == 'y'){
//                             $row1_lembur_5 = 1.5;
//                         }else{
//                             $row1_lembur_5 = 1;
//                         }
//                     }

//                     $row1_explode_hasil_kerja_4 = explode("|",$row1_pengerjaan->hasil_kerja_4);
//                     $row1_umk_borongan_lokal_4 = $this->umkBoronganEkspor->select('id','jenis_produk','umk_packing','umk_kemas','umk_pilih_gagang')->where('id',$row1_explode_hasil_kerja_1[0])->first();
//                     if(empty($row1_umk_borongan_lokal_4)){
//                         $row1_jenis_produk_4 = '-';
//                         $row1_hasil_kerja_4 = null;
//                         $row1_data_explode_hasil_kerja_4 = '-';
//                         $row1_lembur_4 = 1;
//                     }else{
//                         $row1_jenis_produk_4 = $row1_umk_borongan_lokal_4->jenis_produk;
//                         $row1_hasil_kerja_4 = $row1_explode_hasil_kerja_4[1]*$row1_umk_borongan_lokal_4->umk_packing;
//                         $row1_data_explode_hasil_kerja_4 = $row1_explode_hasil_kerja_4[1];
//                         // $row1_lembur_4 = 1.5;
//                         $explode_lembur_1 = explode("|",$row1_pengerjaan->lembur);
//                         $explode_status_lembur_1 = explode("-",$explode_lembur_1[1]);
//                         if($explode_status_lembur_1[1] == 'y'){
//                             $row1_lembur_1 = 1.5;
//                         }else{
//                             $row1_lembur_1 = 1;
//                         }
// $explode_lembur_2 = explode("|",$row1_pengerjaan->lembur);
//                         $explode_status_lembur_2 = explode("-",$explode_lembur_2[2]);
//                         if($explode_status_lembur_2[1] == 'y'){
//                             $row1_lembur_2 = 1.5;
//                         }else{
//                             $row1_lembur_2 = 1;
//                         }
// $explode_lembur_3 = explode("|",$row1_pengerjaan->lembur);
//                         $explode_status_lembur_3 = explode("-",$explode_lembur_3[3]);
//                         if($explode_status_lembur_3[1] == 'y'){
//                             $row1_lembur_3 = 1.5;
//                         }else{
//                             $row1_lembur_3 = 1;
//                         }
// $explode_lembur_4 = explode("|",$row1_pengerjaan->lembur);
//                         $explode_status_lembur_4 = explode("-",$explode_lembur_4[4]);
//                         if($explode_status_lembur_4[1] == 'y'){
//                             $row1_lembur_4 = 1.5;
//                         }else{
//                             $row1_lembur_4 = 1;
//                         }
// $explode_lembur_5 = explode("|",$row1_pengerjaan->lembur);
//                         $explode_status_lembur_5 = explode("-",$explode_lembur_5[5]);
//                         if($explode_status_lembur_5[1] == 'y'){
//                             $row1_lembur_5 = 1.5;
//                         }else{
//                             $row1_lembur_5 = 1;
//                         }
//                     }

//                     $row1_explode_hasil_kerja_5 = explode("|",$row1_pengerjaan->hasil_kerja_5);
//                     $row1_umk_borongan_lokal_5 = $this->umkBoronganEkspor->select('id','jenis_produk','umk_packing','umk_kemas','umk_pilih_gagang')->where('id',$row1_explode_hasil_kerja_1[0])->first();
//                     if(empty($row1_umk_borongan_lokal_5)){
//                         $row1_jenis_produk_5 = '-';
//                         $row1_hasil_kerja_5 = null;
//                         $row1_data_explode_hasil_kerja_5 = '-';
//                         $row1_lembur_5 = 1;
//                     }else{
//                         $row1_jenis_produk_5 = $row1_umk_borongan_lokal_5->jenis_produk;
//                         $row1_hasil_kerja_5 = $row1_explode_hasil_kerja_5[1]*$row1_umk_borongan_lokal_5->umk_packing;
//                         $row1_data_explode_hasil_kerja_5 = $row1_explode_hasil_kerja_5[1];
//                         // $row1_lembur_5 = 1.5;
//                         $explode_lembur_1 = explode("|",$row1_pengerjaan->lembur);
//                         $explode_status_lembur_1 = explode("-",$explode_lembur_1[1]);
//                         if($explode_status_lembur_1[1] == 'y'){
//                             $row1_lembur_1 = 1.5;
//                         }else{
//                             $row1_lembur_1 = 1;
//                         }
// $explode_lembur_2 = explode("|",$row1_pengerjaan->lembur);
//                         $explode_status_lembur_2 = explode("-",$explode_lembur_2[2]);
//                         if($explode_status_lembur_2[1] == 'y'){
//                             $row1_lembur_2 = 1.5;
//                         }else{
//                             $row1_lembur_2 = 1;
//                         }
// $explode_lembur_3 = explode("|",$row1_pengerjaan->lembur);
//                         $explode_status_lembur_3 = explode("-",$explode_lembur_3[3]);
//                         if($explode_status_lembur_3[1] == 'y'){
//                             $row1_lembur_3 = 1.5;
//                         }else{
//                             $row1_lembur_3 = 1;
//                         }
// $explode_lembur_4 = explode("|",$row1_pengerjaan->lembur);
//                         $explode_status_lembur_4 = explode("-",$explode_lembur_4[4]);
//                         if($explode_status_lembur_4[1] == 'y'){
//                             $row1_lembur_4 = 1.5;
//                         }else{
//                             $row1_lembur_4 = 1;
//                         }
// $explode_lembur_5 = explode("|",$row1_pengerjaan->lembur);
//                         $explode_status_lembur_5 = explode("-",$explode_lembur_5[5]);
//                         if($explode_status_lembur_5[1] == 'y'){
//                             $row1_lembur_5 = 1.5;
//                         }else{
//                             $row1_lembur_5 = 1;
//                         }
//                     }
//                 }

//                 if ($row1_pengerjaan->operator_karyawan->jenis_operator_detail_pekerjaan_id == 6) {
//                     $row1_explode_hasil_kerja_1 = explode("|",$row1_pengerjaan->hasil_kerja_1);
//                     $row1_umk_borongan_lokal_1 = $this->umkBoronganEkspor->select('id','jenis_produk','umk_packing','umk_kemas','umk_pilih_gagang')->where('id',$row1_explode_hasil_kerja_1[0])->first();
//                     if(empty($row1_umk_borongan_lokal_1)){
//                         $row1_jenis_produk_1 = '-';
//                         $row1_hasil_kerja_1 = null;
//                         $row1_data_explode_hasil_kerja_1 = '-';
//                         $row1_lembur_1 = 1;
//                     }else{
//                         $row1_jenis_produk_1 = $row1_umk_borongan_lokal_1->jenis_produk;
//                         $row1_hasil_kerja_1 = $row1_explode_hasil_kerja_1[1]*$row1_umk_borongan_lokal_1->umk_kemas;
//                         $row1_data_explode_hasil_kerja_1 = $row1_explode_hasil_kerja_1[1];
//                         // $row1_lembur_1 = 1.5;
//                         $explode_lembur_1 = explode("|",$row1_pengerjaan->lembur);
//                         $explode_status_lembur_1 = explode("-",$explode_lembur_1[1]);
//                         if($explode_status_lembur_1[1] == 'y'){
//                             $row1_lembur_1 = 1.5;
//                         }else{
//                             $row1_lembur_1 = 1;
//                         }
// $explode_lembur_2 = explode("|",$row1_pengerjaan->lembur);
//                         $explode_status_lembur_2 = explode("-",$explode_lembur_2[2]);
//                         if($explode_status_lembur_2[1] == 'y'){
//                             $row1_lembur_2 = 1.5;
//                         }else{
//                             $row1_lembur_2 = 1;
//                         }
// $explode_lembur_3 = explode("|",$row1_pengerjaan->lembur);
//                         $explode_status_lembur_3 = explode("-",$explode_lembur_3[3]);
//                         if($explode_status_lembur_3[1] == 'y'){
//                             $row1_lembur_3 = 1.5;
//                         }else{
//                             $row1_lembur_3 = 1;
//                         }
// $explode_lembur_4 = explode("|",$row1_pengerjaan->lembur);
//                         $explode_status_lembur_4 = explode("-",$explode_lembur_4[4]);
//                         if($explode_status_lembur_4[1] == 'y'){
//                             $row1_lembur_4 = 1.5;
//                         }else{
//                             $row1_lembur_4 = 1;
//                         }
// $explode_lembur_5 = explode("|",$row1_pengerjaan->lembur);
//                         $explode_status_lembur_5 = explode("-",$explode_lembur_5[5]);
//                         if($explode_status_lembur_5[1] == 'y'){
//                             $row1_lembur_5 = 1.5;
//                         }else{
//                             $row1_lembur_5 = 1;
//                         }
//                     }

//                     $row1_explode_hasil_kerja_2 = explode("|",$row1_pengerjaan->hasil_kerja_2);
//                     $row1_umk_borongan_lokal_2 = $this->umkBoronganEkspor->select('id','jenis_produk','umk_packing','umk_kemas','umk_pilih_gagang')->where('id',$row1_explode_hasil_kerja_1[0])->first();
//                     if(empty($row1_umk_borongan_lokal_2)){
//                         $row1_jenis_produk_2 = '-';
//                         $row1_hasil_kerja_2 = null;
//                         $row1_data_explode_hasil_kerja_2 = '-';
//                         $row1_lembur_2 = 1;
//                     }else{
//                         $row1_jenis_produk_2 = $row1_umk_borongan_lokal_2->jenis_produk;
//                         $row1_hasil_kerja_2 = $row1_explode_hasil_kerja_2[1]*$row1_umk_borongan_lokal_2->umk_kemas;
//                         $row1_data_explode_hasil_kerja_2 = $row1_explode_hasil_kerja_2[1];
//                         // $row1_lembur_2 = 1.5;
//                         $explode_lembur_1 = explode("|",$row1_pengerjaan->lembur);
//                         $explode_status_lembur_1 = explode("-",$explode_lembur_1[1]);
//                         if($explode_status_lembur_1[1] == 'y'){
//                             $row1_lembur_1 = 1.5;
//                         }else{
//                             $row1_lembur_1 = 1;
//                         }
// $explode_lembur_2 = explode("|",$row1_pengerjaan->lembur);
//                         $explode_status_lembur_2 = explode("-",$explode_lembur_2[2]);
//                         if($explode_status_lembur_2[1] == 'y'){
//                             $row1_lembur_2 = 1.5;
//                         }else{
//                             $row1_lembur_2 = 1;
//                         }
// $explode_lembur_3 = explode("|",$row1_pengerjaan->lembur);
//                         $explode_status_lembur_3 = explode("-",$explode_lembur_3[3]);
//                         if($explode_status_lembur_3[1] == 'y'){
//                             $row1_lembur_3 = 1.5;
//                         }else{
//                             $row1_lembur_3 = 1;
//                         }
// $explode_lembur_4 = explode("|",$row1_pengerjaan->lembur);
//                         $explode_status_lembur_4 = explode("-",$explode_lembur_4[4]);
//                         if($explode_status_lembur_4[1] == 'y'){
//                             $row1_lembur_4 = 1.5;
//                         }else{
//                             $row1_lembur_4 = 1;
//                         }
// $explode_lembur_5 = explode("|",$row1_pengerjaan->lembur);
//                         $explode_status_lembur_5 = explode("-",$explode_lembur_5[5]);
//                         if($explode_status_lembur_5[1] == 'y'){
//                             $row1_lembur_5 = 1.5;
//                         }else{
//                             $row1_lembur_5 = 1;
//                         }
//                     }

//                     $row1_explode_hasil_kerja_3 = explode("|",$row1_pengerjaan->hasil_kerja_3);
//                     $row1_umk_borongan_lokal_3 = $this->umkBoronganEkspor->select('id','jenis_produk','umk_packing','umk_kemas','umk_pilih_gagang')->where('id',$row1_explode_hasil_kerja_1[0])->first();
//                     if(empty($row1_umk_borongan_lokal_3)){
//                         $row1_jenis_produk_3 = '-';
//                         $row1_hasil_kerja_3 = null;
//                         $row1_data_explode_hasil_kerja_3 = '-';
//                         $row1_lembur_3 = 1;
//                     }else{
//                         $row1_jenis_produk_3 = $row1_umk_borongan_lokal_3->jenis_produk;
//                         $row1_hasil_kerja_3 = $row1_explode_hasil_kerja_3[1]*$row1_umk_borongan_lokal_3->umk_kemas;
//                         $row1_data_explode_hasil_kerja_3 = $row1_explode_hasil_kerja_3[1];
//                         // $row1_lembur_3 = 1.5;
//                         $explode_lembur_1 = explode("|",$row1_pengerjaan->lembur);
//                         $explode_status_lembur_1 = explode("-",$explode_lembur_1[1]);
//                         if($explode_status_lembur_1[1] == 'y'){
//                             $row1_lembur_1 = 1.5;
//                         }else{
//                             $row1_lembur_1 = 1;
//                         }
// $explode_lembur_2 = explode("|",$row1_pengerjaan->lembur);
//                         $explode_status_lembur_2 = explode("-",$explode_lembur_2[2]);
//                         if($explode_status_lembur_2[1] == 'y'){
//                             $row1_lembur_2 = 1.5;
//                         }else{
//                             $row1_lembur_2 = 1;
//                         }
// $explode_lembur_3 = explode("|",$row1_pengerjaan->lembur);
//                         $explode_status_lembur_3 = explode("-",$explode_lembur_3[3]);
//                         if($explode_status_lembur_3[1] == 'y'){
//                             $row1_lembur_3 = 1.5;
//                         }else{
//                             $row1_lembur_3 = 1;
//                         }
// $explode_lembur_4 = explode("|",$row1_pengerjaan->lembur);
//                         $explode_status_lembur_4 = explode("-",$explode_lembur_4[4]);
//                         if($explode_status_lembur_4[1] == 'y'){
//                             $row1_lembur_4 = 1.5;
//                         }else{
//                             $row1_lembur_4 = 1;
//                         }
// $explode_lembur_5 = explode("|",$row1_pengerjaan->lembur);
//                         $explode_status_lembur_5 = explode("-",$explode_lembur_5[5]);
//                         if($explode_status_lembur_5[1] == 'y'){
//                             $row1_lembur_5 = 1.5;
//                         }else{
//                             $row1_lembur_5 = 1;
//                         }
//                     }

//                     $row1_explode_hasil_kerja_4 = explode("|",$row1_pengerjaan->hasil_kerja_4);
//                     $row1_umk_borongan_lokal_4 = $this->umkBoronganEkspor->select('id','jenis_produk','umk_packing','umk_kemas','umk_pilih_gagang')->where('id',$row1_explode_hasil_kerja_1[0])->first();
//                     if(empty($row1_umk_borongan_lokal_4)){
//                         $row1_jenis_produk_4 = '-';
//                         $row1_hasil_kerja_4 = null;
//                         $row1_data_explode_hasil_kerja_4 = '-';
//                         $row1_lembur_4 = 1;
//                     }else{
//                         $row1_jenis_produk_4 = $row1_umk_borongan_lokal_4->jenis_produk;
//                         $row1_hasil_kerja_4 = $row1_explode_hasil_kerja_4[1]*$row1_umk_borongan_lokal_4->umk_kemas;
//                         $row1_data_explode_hasil_kerja_4 = $row1_explode_hasil_kerja_4[1];
//                         // $row1_lembur_4 = 1.5;
//                         $explode_lembur_1 = explode("|",$row1_pengerjaan->lembur);
//                         $explode_status_lembur_1 = explode("-",$explode_lembur_1[1]);
//                         if($explode_status_lembur_1[1] == 'y'){
//                             $row1_lembur_1 = 1.5;
//                         }else{
//                             $row1_lembur_1 = 1;
//                         }
// $explode_lembur_2 = explode("|",$row1_pengerjaan->lembur);
//                         $explode_status_lembur_2 = explode("-",$explode_lembur_2[2]);
//                         if($explode_status_lembur_2[1] == 'y'){
//                             $row1_lembur_2 = 1.5;
//                         }else{
//                             $row1_lembur_2 = 1;
//                         }
// $explode_lembur_3 = explode("|",$row1_pengerjaan->lembur);
//                         $explode_status_lembur_3 = explode("-",$explode_lembur_3[3]);
//                         if($explode_status_lembur_3[1] == 'y'){
//                             $row1_lembur_3 = 1.5;
//                         }else{
//                             $row1_lembur_3 = 1;
//                         }
// $explode_lembur_4 = explode("|",$row1_pengerjaan->lembur);
//                         $explode_status_lembur_4 = explode("-",$explode_lembur_4[4]);
//                         if($explode_status_lembur_4[1] == 'y'){
//                             $row1_lembur_4 = 1.5;
//                         }else{
//                             $row1_lembur_4 = 1;
//                         }
// $explode_lembur_5 = explode("|",$row1_pengerjaan->lembur);
//                         $explode_status_lembur_5 = explode("-",$explode_lembur_5[5]);
//                         if($explode_status_lembur_5[1] == 'y'){
//                             $row1_lembur_5 = 1.5;
//                         }else{
//                             $row1_lembur_5 = 1;
//                         }
//                     }

//                     $row1_explode_hasil_kerja_5 = explode("|",$row1_pengerjaan->hasil_kerja_5);
//                     $row1_umk_borongan_lokal_5 = $this->umkBoronganEkspor->select('id','jenis_produk','umk_packing','umk_kemas','umk_pilih_gagang')->where('id',$row1_explode_hasil_kerja_1[0])->first();
//                     if(empty($row1_umk_borongan_lokal_5)){
//                         $row1_jenis_produk_5 = '-';
//                         $row1_hasil_kerja_5 = null;
//                         $row1_data_explode_hasil_kerja_5 = '-';
//                         $row1_lembur_5 = 1;
//                     }else{
//                         $row1_jenis_produk_5 = $row1_umk_borongan_lokal_5->jenis_produk;
//                         $row1_hasil_kerja_5 = $row1_explode_hasil_kerja_5[1]*$row1_umk_borongan_lokal_5->umk_kemas;
//                         $row1_data_explode_hasil_kerja_5 = $row1_explode_hasil_kerja_5[1];
//                         // $row1_lembur_5 = 1.5;
//                         $explode_lembur_1 = explode("|",$row1_pengerjaan->lembur);
//                         $explode_status_lembur_1 = explode("-",$explode_lembur_1[1]);
//                         if($explode_status_lembur_1[1] == 'y'){
//                             $row1_lembur_1 = 1.5;
//                         }else{
//                             $row1_lembur_1 = 1;
//                         }
// $explode_lembur_2 = explode("|",$row1_pengerjaan->lembur);
//                         $explode_status_lembur_2 = explode("-",$explode_lembur_2[2]);
//                         if($explode_status_lembur_2[1] == 'y'){
//                             $row1_lembur_2 = 1.5;
//                         }else{
//                             $row1_lembur_2 = 1;
//                         }
// $explode_lembur_3 = explode("|",$row1_pengerjaan->lembur);
//                         $explode_status_lembur_3 = explode("-",$explode_lembur_3[3]);
//                         if($explode_status_lembur_3[1] == 'y'){
//                             $row1_lembur_3 = 1.5;
//                         }else{
//                             $row1_lembur_3 = 1;
//                         }
// $explode_lembur_4 = explode("|",$row1_pengerjaan->lembur);
//                         $explode_status_lembur_4 = explode("-",$explode_lembur_4[4]);
//                         if($explode_status_lembur_4[1] == 'y'){
//                             $row1_lembur_4 = 1.5;
//                         }else{
//                             $row1_lembur_4 = 1;
//                         }
// $explode_lembur_5 = explode("|",$row1_pengerjaan->lembur);
//                         $explode_status_lembur_5 = explode("-",$explode_lembur_5[5]);
//                         if($explode_status_lembur_5[1] == 'y'){
//                             $row1_lembur_5 = 1.5;
//                         }else{
//                             $row1_lembur_5 = 1;
//                         }
//                     }
//                 }

//                 if ($row1_pengerjaan->operator_karyawan->jenis_operator_detail_pekerjaan_id == 7) {
//                     $row1_explode_hasil_kerja_1 = explode("|",$row1_pengerjaan->hasil_kerja_1);
//                     $row1_umk_borongan_lokal_1 = $this->umkBoronganEkspor->select('id','jenis_produk','umk_packing','umk_kemas','umk_pilih_gagang')->where('id',$row1_explode_hasil_kerja_1[0])->first();
//                     if(empty($row1_umk_borongan_lokal_1)){
//                         $row1_jenis_produk_1 = '-';
//                         $row1_hasil_kerja_1 = null;
//                         $row1_data_explode_hasil_kerja_1 = '-';
//                         $row1_lembur_1 = 1;
//                     }else{
//                         $row1_jenis_produk_1 = $row1_umk_borongan_lokal_1->jenis_produk;
//                         $row1_hasil_kerja_1 = $row1_explode_hasil_kerja_1[1]*$row1_umk_borongan_lokal_1->umk_pilih_gagang;
//                         $row1_data_explode_hasil_kerja_1 = $row1_explode_hasil_kerja_1[1];
//                         // $row1_lembur_1 = 1.5;
//                         $explode_lembur_1 = explode("|",$row1_pengerjaan->lembur);
//                         $explode_status_lembur_1 = explode("-",$explode_lembur_1[1]);
//                         if($explode_status_lembur_1[1] == 'y'){
//                             $row1_lembur_1 = 1.5;
//                         }else{
//                             $row1_lembur_1 = 1;
//                         }
// $explode_lembur_2 = explode("|",$row1_pengerjaan->lembur);
//                         $explode_status_lembur_2 = explode("-",$explode_lembur_2[2]);
//                         if($explode_status_lembur_2[1] == 'y'){
//                             $row1_lembur_2 = 1.5;
//                         }else{
//                             $row1_lembur_2 = 1;
//                         }
// $explode_lembur_3 = explode("|",$row1_pengerjaan->lembur);
//                         $explode_status_lembur_3 = explode("-",$explode_lembur_3[3]);
//                         if($explode_status_lembur_3[1] == 'y'){
//                             $row1_lembur_3 = 1.5;
//                         }else{
//                             $row1_lembur_3 = 1;
//                         }
// $explode_lembur_4 = explode("|",$row1_pengerjaan->lembur);
//                         $explode_status_lembur_4 = explode("-",$explode_lembur_4[4]);
//                         if($explode_status_lembur_4[1] == 'y'){
//                             $row1_lembur_4 = 1.5;
//                         }else{
//                             $row1_lembur_4 = 1;
//                         }
// $explode_lembur_5 = explode("|",$row1_pengerjaan->lembur);
//                         $explode_status_lembur_5 = explode("-",$explode_lembur_5[5]);
//                         if($explode_status_lembur_5[1] == 'y'){
//                             $row1_lembur_5 = 1.5;
//                         }else{
//                             $row1_lembur_5 = 1;
//                         }
//                     }

//                     $row1_explode_hasil_kerja_2 = explode("|",$row1_pengerjaan->hasil_kerja_2);
//                     $row1_umk_borongan_lokal_2 = $this->umkBoronganEkspor->select('id','jenis_produk','umk_packing','umk_kemas','umk_pilih_gagang')->where('id',$row1_explode_hasil_kerja_1[0])->first();
//                     if(empty($row1_umk_borongan_lokal_2)){
//                         $row1_jenis_produk_2 = '-';
//                         $row1_hasil_kerja_2 = null;
//                         $row1_data_explode_hasil_kerja_2 = '-';
//                         $row1_lembur_2 = 1;
//                     }else{
//                         $row1_jenis_produk_2 = $row1_umk_borongan_lokal_2->jenis_produk;
//                         $row1_hasil_kerja_2 = $row1_explode_hasil_kerja_2[1]*$row1_umk_borongan_lokal_2->umk_pilih_gagang;
//                         $row1_data_explode_hasil_kerja_2 = $row1_explode_hasil_kerja_2[1];
//                         // $row1_lembur_2 = 1.5;
//                         $explode_lembur_1 = explode("|",$row1_pengerjaan->lembur);
//                         $explode_status_lembur_1 = explode("-",$explode_lembur_1[1]);
//                         if($explode_status_lembur_1[1] == 'y'){
//                             $row1_lembur_1 = 1.5;
//                         }else{
//                             $row1_lembur_1 = 1;
//                         }
// $explode_lembur_2 = explode("|",$row1_pengerjaan->lembur);
//                         $explode_status_lembur_2 = explode("-",$explode_lembur_2[2]);
//                         if($explode_status_lembur_2[1] == 'y'){
//                             $row1_lembur_2 = 1.5;
//                         }else{
//                             $row1_lembur_2 = 1;
//                         }
// $explode_lembur_3 = explode("|",$row1_pengerjaan->lembur);
//                         $explode_status_lembur_3 = explode("-",$explode_lembur_3[3]);
//                         if($explode_status_lembur_3[1] == 'y'){
//                             $row1_lembur_3 = 1.5;
//                         }else{
//                             $row1_lembur_3 = 1;
//                         }
// $explode_lembur_4 = explode("|",$row1_pengerjaan->lembur);
//                         $explode_status_lembur_4 = explode("-",$explode_lembur_4[4]);
//                         if($explode_status_lembur_4[1] == 'y'){
//                             $row1_lembur_4 = 1.5;
//                         }else{
//                             $row1_lembur_4 = 1;
//                         }
// $explode_lembur_5 = explode("|",$row1_pengerjaan->lembur);
//                         $explode_status_lembur_5 = explode("-",$explode_lembur_5[5]);
//                         if($explode_status_lembur_5[1] == 'y'){
//                             $row1_lembur_5 = 1.5;
//                         }else{
//                             $row1_lembur_5 = 1;
//                         }
//                     }

//                     $row1_explode_hasil_kerja_3 = explode("|",$row1_pengerjaan->hasil_kerja_3);
//                     $row1_umk_borongan_lokal_3 = $this->umkBoronganEkspor->select('id','jenis_produk','umk_packing','umk_kemas','umk_pilih_gagang')->where('id',$row1_explode_hasil_kerja_1[0])->first();
//                     if(empty($row1_umk_borongan_lokal_3)){
//                         $row1_jenis_produk_3 = '-';
//                         $row1_hasil_kerja_3 = null;
//                         $row1_data_explode_hasil_kerja_3 = '-';
//                         $row1_lembur_3 = 1;
//                     }else{
//                         $row1_jenis_produk_3 = $row1_umk_borongan_lokal_3->jenis_produk;
//                         $row1_hasil_kerja_3 = $row1_explode_hasil_kerja_3[1]*$row1_umk_borongan_lokal_3->umk_pilih_gagang;
//                         $row1_data_explode_hasil_kerja_3 = $row1_explode_hasil_kerja_3[1];
//                         // $row1_lembur_3 = 1.5;
//                         $explode_lembur_1 = explode("|",$row1_pengerjaan->lembur);
//                         $explode_status_lembur_1 = explode("-",$explode_lembur_1[1]);
//                         if($explode_status_lembur_1[1] == 'y'){
//                             $row1_lembur_1 = 1.5;
//                         }else{
//                             $row1_lembur_1 = 1;
//                         }
// $explode_lembur_2 = explode("|",$row1_pengerjaan->lembur);
//                         $explode_status_lembur_2 = explode("-",$explode_lembur_2[2]);
//                         if($explode_status_lembur_2[1] == 'y'){
//                             $row1_lembur_2 = 1.5;
//                         }else{
//                             $row1_lembur_2 = 1;
//                         }
// $explode_lembur_3 = explode("|",$row1_pengerjaan->lembur);
//                         $explode_status_lembur_3 = explode("-",$explode_lembur_3[3]);
//                         if($explode_status_lembur_3[1] == 'y'){
//                             $row1_lembur_3 = 1.5;
//                         }else{
//                             $row1_lembur_3 = 1;
//                         }
// $explode_lembur_4 = explode("|",$row1_pengerjaan->lembur);
//                         $explode_status_lembur_4 = explode("-",$explode_lembur_4[4]);
//                         if($explode_status_lembur_4[1] == 'y'){
//                             $row1_lembur_4 = 1.5;
//                         }else{
//                             $row1_lembur_4 = 1;
//                         }
// $explode_lembur_5 = explode("|",$row1_pengerjaan->lembur);
//                         $explode_status_lembur_5 = explode("-",$explode_lembur_5[5]);
//                         if($explode_status_lembur_5[1] == 'y'){
//                             $row1_lembur_5 = 1.5;
//                         }else{
//                             $row1_lembur_5 = 1;
//                         }
//                     }

//                     $row1_explode_hasil_kerja_4 = explode("|",$row1_pengerjaan->hasil_kerja_4);
//                     $row1_umk_borongan_lokal_4 = $this->umkBoronganEkspor->select('id','jenis_produk','umk_packing','umk_kemas','umk_pilih_gagang')->where('id',$row1_explode_hasil_kerja_1[0])->first();
//                     if(empty($row1_umk_borongan_lokal_4)){
//                         $row1_jenis_produk_4 = '-';
//                         $row1_hasil_kerja_4 = null;
//                         $row1_data_explode_hasil_kerja_4 = '-';
//                         $row1_lembur_4 = 1;
//                     }else{
//                         $row1_jenis_produk_4 = $row1_umk_borongan_lokal_4->jenis_produk;
//                         $row1_hasil_kerja_4 = $row1_explode_hasil_kerja_4[1]*$row1_umk_borongan_lokal_4->umk_pilih_gagang;
//                         $row1_data_explode_hasil_kerja_4 = $row1_explode_hasil_kerja_4[1];
//                         // $row1_lembur_4 = 1.5;
//                         $explode_lembur_1 = explode("|",$row1_pengerjaan->lembur);
//                         $explode_status_lembur_1 = explode("-",$explode_lembur_1[1]);
//                         if($explode_status_lembur_1[1] == 'y'){
//                             $row1_lembur_1 = 1.5;
//                         }else{
//                             $row1_lembur_1 = 1;
//                         }
// $explode_lembur_2 = explode("|",$row1_pengerjaan->lembur);
//                         $explode_status_lembur_2 = explode("-",$explode_lembur_2[2]);
//                         if($explode_status_lembur_2[1] == 'y'){
//                             $row1_lembur_2 = 1.5;
//                         }else{
//                             $row1_lembur_2 = 1;
//                         }
// $explode_lembur_3 = explode("|",$row1_pengerjaan->lembur);
//                         $explode_status_lembur_3 = explode("-",$explode_lembur_3[3]);
//                         if($explode_status_lembur_3[1] == 'y'){
//                             $row1_lembur_3 = 1.5;
//                         }else{
//                             $row1_lembur_3 = 1;
//                         }
// $explode_lembur_4 = explode("|",$row1_pengerjaan->lembur);
//                         $explode_status_lembur_4 = explode("-",$explode_lembur_4[4]);
//                         if($explode_status_lembur_4[1] == 'y'){
//                             $row1_lembur_4 = 1.5;
//                         }else{
//                             $row1_lembur_4 = 1;
//                         }
// $explode_lembur_5 = explode("|",$row1_pengerjaan->lembur);
//                         $explode_status_lembur_5 = explode("-",$explode_lembur_5[5]);
//                         if($explode_status_lembur_5[1] == 'y'){
//                             $row1_lembur_5 = 1.5;
//                         }else{
//                             $row1_lembur_5 = 1;
//                         }
//                     }

//                     $row1_explode_hasil_kerja_5 = explode("|",$row1_pengerjaan->hasil_kerja_5);
//                     $row1_umk_borongan_lokal_5 = $this->umkBoronganEkspor->select('id','jenis_produk','umk_packing','umk_kemas','umk_pilih_gagang')->where('id',$row1_explode_hasil_kerja_1[0])->first();
//                     if(empty($row1_umk_borongan_lokal_5)){
//                         $row1_jenis_produk_5 = '-';
//                         $row1_hasil_kerja_5 = null;
//                         $row1_data_explode_hasil_kerja_5 = '-';
//                         $row1_lembur_5 = 1;
//                     }else{
//                         $row1_jenis_produk_5 = $row1_umk_borongan_lokal_5->jenis_produk;
//                         $row1_hasil_kerja_5 = $row1_explode_hasil_kerja_5[1]*$row1_umk_borongan_lokal_5->umk_pilih_gagang;
//                         $row1_data_explode_hasil_kerja_5 = $row1_explode_hasil_kerja_5[1];
//                         // $row1_lembur_5 = 1.5;
//                         $explode_lembur_1 = explode("|",$row1_pengerjaan->lembur);
//                         $explode_status_lembur_1 = explode("-",$explode_lembur_1[1]);
//                         if($explode_status_lembur_1[1] == 'y'){
//                             $row1_lembur_1 = 1.5;
//                         }else{
//                             $row1_lembur_1 = 1;
//                         }
// $explode_lembur_2 = explode("|",$row1_pengerjaan->lembur);
//                         $explode_status_lembur_2 = explode("-",$explode_lembur_2[2]);
//                         if($explode_status_lembur_2[1] == 'y'){
//                             $row1_lembur_2 = 1.5;
//                         }else{
//                             $row1_lembur_2 = 1;
//                         }
// $explode_lembur_3 = explode("|",$row1_pengerjaan->lembur);
//                         $explode_status_lembur_3 = explode("-",$explode_lembur_3[3]);
//                         if($explode_status_lembur_3[1] == 'y'){
//                             $row1_lembur_3 = 1.5;
//                         }else{
//                             $row1_lembur_3 = 1;
//                         }
// $explode_lembur_4 = explode("|",$row1_pengerjaan->lembur);
//                         $explode_status_lembur_4 = explode("-",$explode_lembur_4[4]);
//                         if($explode_status_lembur_4[1] == 'y'){
//                             $row1_lembur_4 = 1.5;
//                         }else{
//                             $row1_lembur_4 = 1;
//                         }
// $explode_lembur_5 = explode("|",$row1_pengerjaan->lembur);
//                         $explode_status_lembur_5 = explode("-",$explode_lembur_5[5]);
//                         if($explode_status_lembur_5[1] == 'y'){
//                             $row1_lembur_5 = 1.5;
//                         }else{
//                             $row1_lembur_5 = 1;
//                         }
//                     }
//                 }
//                 //End Ekspor

//                 //Begin Ambri
//                 if ($row1_pengerjaan->operator_karyawan->jenis_operator_detail_pekerjaan_id == 8) {
//                     $row1_explode_hasil_kerja_1 = explode("|",$row1_pengerjaan->hasil_kerja_1);
//                     $row1_umk_borongan_lokal_1 = $this->umkBoronganAmbri->select('id','jenis_produk','umk_etiket','umk_las_tepi','umk_las_pojok','umk_ambri')->where('id',$row1_explode_hasil_kerja_1[0])->first();
//                     if(empty($row1_umk_borongan_lokal_1)){
//                         $row1_jenis_produk_1 = '-';
//                         $row1_hasil_kerja_1 = null;
//                         $row1_data_explode_hasil_kerja_1 = '-';
//                         $row1_lembur_1 = 1;
//                     }else{
//                         $row1_jenis_produk_1 = $row1_umk_borongan_lokal_1->jenis_produk;
//                         $row1_hasil_kerja_1 = $row1_explode_hasil_kerja_1[1]*$row1_umk_borongan_lokal_1->umk_etiket;
//                         $row1_data_explode_hasil_kerja_1 = $row1_explode_hasil_kerja_1[1];
//                         // $row1_lembur_1 = 1.5;
//                         $explode_lembur_1 = explode("|",$row1_pengerjaan->lembur);
//                         $explode_status_lembur_1 = explode("-",$explode_lembur_1[1]);
//                         if($explode_status_lembur_1[1] == 'y'){
//                             $row1_lembur_1 = 1.5;
//                         }else{
//                             $row1_lembur_1 = 1;
//                         }
// $explode_lembur_2 = explode("|",$row1_pengerjaan->lembur);
//                         $explode_status_lembur_2 = explode("-",$explode_lembur_2[2]);
//                         if($explode_status_lembur_2[1] == 'y'){
//                             $row1_lembur_2 = 1.5;
//                         }else{
//                             $row1_lembur_2 = 1;
//                         }
// $explode_lembur_3 = explode("|",$row1_pengerjaan->lembur);
//                         $explode_status_lembur_3 = explode("-",$explode_lembur_3[3]);
//                         if($explode_status_lembur_3[1] == 'y'){
//                             $row1_lembur_3 = 1.5;
//                         }else{
//                             $row1_lembur_3 = 1;
//                         }
// $explode_lembur_4 = explode("|",$row1_pengerjaan->lembur);
//                         $explode_status_lembur_4 = explode("-",$explode_lembur_4[4]);
//                         if($explode_status_lembur_4[1] == 'y'){
//                             $row1_lembur_4 = 1.5;
//                         }else{
//                             $row1_lembur_4 = 1;
//                         }
// $explode_lembur_5 = explode("|",$row1_pengerjaan->lembur);
//                         $explode_status_lembur_5 = explode("-",$explode_lembur_5[5]);
//                         if($explode_status_lembur_5[1] == 'y'){
//                             $row1_lembur_5 = 1.5;
//                         }else{
//                             $row1_lembur_5 = 1;
//                         }
//                     }

//                     $row1_explode_hasil_kerja_2 = explode("|",$row1_pengerjaan->hasil_kerja_2);
//                     $row1_umk_borongan_lokal_2 = $this->umkBoronganAmbri->select('id','jenis_produk','umk_etiket','umk_las_tepi','umk_las_pojok','umk_ambri')->where('id',$row1_explode_hasil_kerja_1[0])->first();
//                     if(empty($row1_umk_borongan_lokal_2)){
//                         $row1_jenis_produk_2 = '-';
//                         $row1_hasil_kerja_2 = null;
//                         $row1_data_explode_hasil_kerja_2 = '-';
//                         $row1_lembur_2 = 1;
//                     }else{
//                         $row1_jenis_produk_2 = $row1_umk_borongan_lokal_2->jenis_produk;
//                         $row1_hasil_kerja_2 = $row1_explode_hasil_kerja_2[1]*$row1_umk_borongan_lokal_2->umk_etiket;
//                         $row1_data_explode_hasil_kerja_2 = $row1_explode_hasil_kerja_2[1];
//                         // $row1_lembur_2 = 1.5;
//                         $explode_lembur_1 = explode("|",$row1_pengerjaan->lembur);
//                         $explode_status_lembur_1 = explode("-",$explode_lembur_1[1]);
//                         if($explode_status_lembur_1[1] == 'y'){
//                             $row1_lembur_1 = 1.5;
//                         }else{
//                             $row1_lembur_1 = 1;
//                         }
// $explode_lembur_2 = explode("|",$row1_pengerjaan->lembur);
//                         $explode_status_lembur_2 = explode("-",$explode_lembur_2[2]);
//                         if($explode_status_lembur_2[1] == 'y'){
//                             $row1_lembur_2 = 1.5;
//                         }else{
//                             $row1_lembur_2 = 1;
//                         }
// $explode_lembur_3 = explode("|",$row1_pengerjaan->lembur);
//                         $explode_status_lembur_3 = explode("-",$explode_lembur_3[3]);
//                         if($explode_status_lembur_3[1] == 'y'){
//                             $row1_lembur_3 = 1.5;
//                         }else{
//                             $row1_lembur_3 = 1;
//                         }
// $explode_lembur_4 = explode("|",$row1_pengerjaan->lembur);
//                         $explode_status_lembur_4 = explode("-",$explode_lembur_4[4]);
//                         if($explode_status_lembur_4[1] == 'y'){
//                             $row1_lembur_4 = 1.5;
//                         }else{
//                             $row1_lembur_4 = 1;
//                         }
// $explode_lembur_5 = explode("|",$row1_pengerjaan->lembur);
//                         $explode_status_lembur_5 = explode("-",$explode_lembur_5[5]);
//                         if($explode_status_lembur_5[1] == 'y'){
//                             $row1_lembur_5 = 1.5;
//                         }else{
//                             $row1_lembur_5 = 1;
//                         }
//                     }

//                     $row1_explode_hasil_kerja_3 = explode("|",$row1_pengerjaan->hasil_kerja_3);
//                     $row1_umk_borongan_lokal_3 = $this->umkBoronganAmbri->select('id','jenis_produk','umk_etiket','umk_las_tepi','umk_las_pojok','umk_ambri')->where('id',$row1_explode_hasil_kerja_1[0])->first();
//                     if(empty($row1_umk_borongan_lokal_3)){
//                         $row1_jenis_produk_3 = '-';
//                         $row1_hasil_kerja_3 = null;
//                         $row1_data_explode_hasil_kerja_3 = '-';
//                         $row1_lembur_3 = 1;
//                     }else{
//                         $row1_jenis_produk_3 = $row1_umk_borongan_lokal_3->jenis_produk;
//                         $row1_hasil_kerja_3 = $row1_explode_hasil_kerja_3[1]*$row1_umk_borongan_lokal_3->umk_etiket;
//                         $row1_data_explode_hasil_kerja_3 = $row1_explode_hasil_kerja_3[1];
//                         // $row1_lembur_3 = 1.5;
//                         $explode_lembur_1 = explode("|",$row1_pengerjaan->lembur);
//                         $explode_status_lembur_1 = explode("-",$explode_lembur_1[1]);
//                         if($explode_status_lembur_1[1] == 'y'){
//                             $row1_lembur_1 = 1.5;
//                         }else{
//                             $row1_lembur_1 = 1;
//                         }
// $explode_lembur_2 = explode("|",$row1_pengerjaan->lembur);
//                         $explode_status_lembur_2 = explode("-",$explode_lembur_2[2]);
//                         if($explode_status_lembur_2[1] == 'y'){
//                             $row1_lembur_2 = 1.5;
//                         }else{
//                             $row1_lembur_2 = 1;
//                         }
// $explode_lembur_3 = explode("|",$row1_pengerjaan->lembur);
//                         $explode_status_lembur_3 = explode("-",$explode_lembur_3[3]);
//                         if($explode_status_lembur_3[1] == 'y'){
//                             $row1_lembur_3 = 1.5;
//                         }else{
//                             $row1_lembur_3 = 1;
//                         }
// $explode_lembur_4 = explode("|",$row1_pengerjaan->lembur);
//                         $explode_status_lembur_4 = explode("-",$explode_lembur_4[4]);
//                         if($explode_status_lembur_4[1] == 'y'){
//                             $row1_lembur_4 = 1.5;
//                         }else{
//                             $row1_lembur_4 = 1;
//                         }
// $explode_lembur_5 = explode("|",$row1_pengerjaan->lembur);
//                         $explode_status_lembur_5 = explode("-",$explode_lembur_5[5]);
//                         if($explode_status_lembur_5[1] == 'y'){
//                             $row1_lembur_5 = 1.5;
//                         }else{
//                             $row1_lembur_5 = 1;
//                         }
//                     }

//                     $row1_explode_hasil_kerja_4 = explode("|",$row1_pengerjaan->hasil_kerja_4);
//                     $row1_umk_borongan_lokal_4 = $this->umkBoronganAmbri->select('id','jenis_produk','umk_etiket','umk_las_tepi','umk_las_pojok','umk_ambri')->where('id',$row1_explode_hasil_kerja_1[0])->first();
//                     if(empty($row1_umk_borongan_lokal_4)){
//                         $row1_jenis_produk_4 = '-';
//                         $row1_hasil_kerja_4 = null;
//                         $row1_data_explode_hasil_kerja_4 = '-';
//                         $row1_lembur_4 = 1;
//                     }else{
//                         $row1_jenis_produk_4 = $row1_umk_borongan_lokal_4->jenis_produk;
//                         $row1_hasil_kerja_4 = $row1_explode_hasil_kerja_4[1]*$row1_umk_borongan_lokal_4->umk_etiket;
//                         $row1_data_explode_hasil_kerja_4 = $row1_explode_hasil_kerja_4[1];
//                         // $row1_lembur_4 = 1.5;
//                         $explode_lembur_1 = explode("|",$row1_pengerjaan->lembur);
//                         $explode_status_lembur_1 = explode("-",$explode_lembur_1[1]);
//                         if($explode_status_lembur_1[1] == 'y'){
//                             $row1_lembur_1 = 1.5;
//                         }else{
//                             $row1_lembur_1 = 1;
//                         }
// $explode_lembur_2 = explode("|",$row1_pengerjaan->lembur);
//                         $explode_status_lembur_2 = explode("-",$explode_lembur_2[2]);
//                         if($explode_status_lembur_2[1] == 'y'){
//                             $row1_lembur_2 = 1.5;
//                         }else{
//                             $row1_lembur_2 = 1;
//                         }
// $explode_lembur_3 = explode("|",$row1_pengerjaan->lembur);
//                         $explode_status_lembur_3 = explode("-",$explode_lembur_3[3]);
//                         if($explode_status_lembur_3[1] == 'y'){
//                             $row1_lembur_3 = 1.5;
//                         }else{
//                             $row1_lembur_3 = 1;
//                         }
// $explode_lembur_4 = explode("|",$row1_pengerjaan->lembur);
//                         $explode_status_lembur_4 = explode("-",$explode_lembur_4[4]);
//                         if($explode_status_lembur_4[1] == 'y'){
//                             $row1_lembur_4 = 1.5;
//                         }else{
//                             $row1_lembur_4 = 1;
//                         }
// $explode_lembur_5 = explode("|",$row1_pengerjaan->lembur);
//                         $explode_status_lembur_5 = explode("-",$explode_lembur_5[5]);
//                         if($explode_status_lembur_5[1] == 'y'){
//                             $row1_lembur_5 = 1.5;
//                         }else{
//                             $row1_lembur_5 = 1;
//                         }
//                     }

//                     $row1_explode_hasil_kerja_5 = explode("|",$row1_pengerjaan->hasil_kerja_5);
//                     $row1_umk_borongan_lokal_5 = $this->umkBoronganAmbri->select('id','jenis_produk','umk_etiket','umk_las_tepi','umk_las_pojok','umk_ambri')->where('id',$row1_explode_hasil_kerja_1[0])->first();
//                     if(empty($row1_umk_borongan_lokal_5)){
//                         $row1_jenis_produk_5 = '-';
//                         $row1_hasil_kerja_5 = null;
//                         $row1_data_explode_hasil_kerja_5 = '-';
//                         $row1_lembur_5 = 1;
//                     }else{
//                         $row1_jenis_produk_5 = $row1_umk_borongan_lokal_5->jenis_produk;
//                         $row1_hasil_kerja_5 = $row1_explode_hasil_kerja_5[1]*$row1_umk_borongan_lokal_5->umk_etiket;
//                         $row1_data_explode_hasil_kerja_5 = $row1_explode_hasil_kerja_5[1];
//                         // $row1_lembur_5 = 1.5;
//                         $explode_lembur_1 = explode("|",$row1_pengerjaan->lembur);
//                         $explode_status_lembur_1 = explode("-",$explode_lembur_1[1]);
//                         if($explode_status_lembur_1[1] == 'y'){
//                             $row1_lembur_1 = 1.5;
//                         }else{
//                             $row1_lembur_1 = 1;
//                         }
// $explode_lembur_2 = explode("|",$row1_pengerjaan->lembur);
//                         $explode_status_lembur_2 = explode("-",$explode_lembur_2[2]);
//                         if($explode_status_lembur_2[1] == 'y'){
//                             $row1_lembur_2 = 1.5;
//                         }else{
//                             $row1_lembur_2 = 1;
//                         }
// $explode_lembur_3 = explode("|",$row1_pengerjaan->lembur);
//                         $explode_status_lembur_3 = explode("-",$explode_lembur_3[3]);
//                         if($explode_status_lembur_3[1] == 'y'){
//                             $row1_lembur_3 = 1.5;
//                         }else{
//                             $row1_lembur_3 = 1;
//                         }
// $explode_lembur_4 = explode("|",$row1_pengerjaan->lembur);
//                         $explode_status_lembur_4 = explode("-",$explode_lembur_4[4]);
//                         if($explode_status_lembur_4[1] == 'y'){
//                             $row1_lembur_4 = 1.5;
//                         }else{
//                             $row1_lembur_4 = 1;
//                         }
// $explode_lembur_5 = explode("|",$row1_pengerjaan->lembur);
//                         $explode_status_lembur_5 = explode("-",$explode_lembur_5[5]);
//                         if($explode_status_lembur_5[1] == 'y'){
//                             $row1_lembur_5 = 1.5;
//                         }else{
//                             $row1_lembur_5 = 1;
//                         }
//                     }
//                 }

//                 if ($row1_pengerjaan->operator_karyawan->jenis_operator_detail_pekerjaan_id == 9) {
//                     $row1_explode_hasil_kerja_1 = explode("|",$row1_pengerjaan->hasil_kerja_1);
//                     $row1_umk_borongan_lokal_1 = $this->umkBoronganAmbri->select('id','jenis_produk','umk_etiket','umk_las_tepi','umk_las_pojok','umk_ambri')->where('id',$row1_explode_hasil_kerja_1[0])->first();
//                     if(empty($row1_umk_borongan_lokal_1)){
//                         $row1_jenis_produk_1 = '-';
//                         $row1_hasil_kerja_1 = null;
//                         $row1_data_explode_hasil_kerja_1 = '-';
//                         $row1_lembur_1 = 1;
//                     }else{
//                         $row1_jenis_produk_1 = $row1_umk_borongan_lokal_1->jenis_produk;
//                         $row1_hasil_kerja_1 = $row1_explode_hasil_kerja_1[1]*$row1_umk_borongan_lokal_1->umk_las_tepi;
//                         $row1_data_explode_hasil_kerja_1 = $row1_explode_hasil_kerja_1[1];
//                         // $row1_lembur_1 = 1.5;
//                         $explode_lembur_1 = explode("|",$row1_pengerjaan->lembur);
//                         $explode_status_lembur_1 = explode("-",$explode_lembur_1[1]);
//                         if($explode_status_lembur_1[1] == 'y'){
//                             $row1_lembur_1 = 1.5;
//                         }else{
//                             $row1_lembur_1 = 1;
//                         }
// $explode_lembur_2 = explode("|",$row1_pengerjaan->lembur);
//                         $explode_status_lembur_2 = explode("-",$explode_lembur_2[2]);
//                         if($explode_status_lembur_2[1] == 'y'){
//                             $row1_lembur_2 = 1.5;
//                         }else{
//                             $row1_lembur_2 = 1;
//                         }
// $explode_lembur_3 = explode("|",$row1_pengerjaan->lembur);
//                         $explode_status_lembur_3 = explode("-",$explode_lembur_3[3]);
//                         if($explode_status_lembur_3[1] == 'y'){
//                             $row1_lembur_3 = 1.5;
//                         }else{
//                             $row1_lembur_3 = 1;
//                         }
// $explode_lembur_4 = explode("|",$row1_pengerjaan->lembur);
//                         $explode_status_lembur_4 = explode("-",$explode_lembur_4[4]);
//                         if($explode_status_lembur_4[1] == 'y'){
//                             $row1_lembur_4 = 1.5;
//                         }else{
//                             $row1_lembur_4 = 1;
//                         }
// $explode_lembur_5 = explode("|",$row1_pengerjaan->lembur);
//                         $explode_status_lembur_5 = explode("-",$explode_lembur_5[5]);
//                         if($explode_status_lembur_5[1] == 'y'){
//                             $row1_lembur_5 = 1.5;
//                         }else{
//                             $row1_lembur_5 = 1;
//                         }
//                     }

//                     $row1_explode_hasil_kerja_2 = explode("|",$row1_pengerjaan->hasil_kerja_2);
//                     $row1_umk_borongan_lokal_2 = $this->umkBoronganAmbri->select('id','jenis_produk','umk_etiket','umk_las_tepi','umk_las_pojok','umk_ambri')->where('id',$row1_explode_hasil_kerja_1[0])->first();
//                     if(empty($row1_umk_borongan_lokal_2)){
//                         $row1_jenis_produk_2 = '-';
//                         $row1_hasil_kerja_2 = null;
//                         $row1_data_explode_hasil_kerja_2 = '-';
//                         $row1_lembur_2 = 1;
//                     }else{
//                         $row1_jenis_produk_2 = $row1_umk_borongan_lokal_2->jenis_produk;
//                         $row1_hasil_kerja_2 = $row1_explode_hasil_kerja_2[1]*$row1_umk_borongan_lokal_2->umk_las_tepi;
//                         $row1_data_explode_hasil_kerja_2 = $row1_explode_hasil_kerja_2[1];
//                         // $row1_lembur_2 = 1.5;
//                         $explode_lembur_1 = explode("|",$row1_pengerjaan->lembur);
//                         $explode_status_lembur_1 = explode("-",$explode_lembur_1[1]);
//                         if($explode_status_lembur_1[1] == 'y'){
//                             $row1_lembur_1 = 1.5;
//                         }else{
//                             $row1_lembur_1 = 1;
//                         }
// $explode_lembur_2 = explode("|",$row1_pengerjaan->lembur);
//                         $explode_status_lembur_2 = explode("-",$explode_lembur_2[2]);
//                         if($explode_status_lembur_2[1] == 'y'){
//                             $row1_lembur_2 = 1.5;
//                         }else{
//                             $row1_lembur_2 = 1;
//                         }
// $explode_lembur_3 = explode("|",$row1_pengerjaan->lembur);
//                         $explode_status_lembur_3 = explode("-",$explode_lembur_3[3]);
//                         if($explode_status_lembur_3[1] == 'y'){
//                             $row1_lembur_3 = 1.5;
//                         }else{
//                             $row1_lembur_3 = 1;
//                         }
// $explode_lembur_4 = explode("|",$row1_pengerjaan->lembur);
//                         $explode_status_lembur_4 = explode("-",$explode_lembur_4[4]);
//                         if($explode_status_lembur_4[1] == 'y'){
//                             $row1_lembur_4 = 1.5;
//                         }else{
//                             $row1_lembur_4 = 1;
//                         }
// $explode_lembur_5 = explode("|",$row1_pengerjaan->lembur);
//                         $explode_status_lembur_5 = explode("-",$explode_lembur_5[5]);
//                         if($explode_status_lembur_5[1] == 'y'){
//                             $row1_lembur_5 = 1.5;
//                         }else{
//                             $row1_lembur_5 = 1;
//                         }
//                     }

//                     $row1_explode_hasil_kerja_3 = explode("|",$row1_pengerjaan->hasil_kerja_3);
//                     $row1_umk_borongan_lokal_3 = $this->umkBoronganAmbri->select('id','jenis_produk','umk_etiket','umk_las_tepi','umk_las_pojok','umk_ambri')->where('id',$row1_explode_hasil_kerja_1[0])->first();
//                     if(empty($row1_umk_borongan_lokal_3)){
//                         $row1_jenis_produk_3 = '-';
//                         $row1_hasil_kerja_3 = null;
//                         $row1_data_explode_hasil_kerja_3 = '-';
//                         $row1_lembur_3 = 1;
//                     }else{
//                         $row1_jenis_produk_3 = $row1_umk_borongan_lokal_3->jenis_produk;
//                         $row1_hasil_kerja_3 = $row1_explode_hasil_kerja_3[1]*$row1_umk_borongan_lokal_3->umk_las_tepi;
//                         $row1_data_explode_hasil_kerja_3 = $row1_explode_hasil_kerja_3[1];
//                         // $row1_lembur_3 = 1.5;
//                         $explode_lembur_1 = explode("|",$row1_pengerjaan->lembur);
//                         $explode_status_lembur_1 = explode("-",$explode_lembur_1[1]);
//                         if($explode_status_lembur_1[1] == 'y'){
//                             $row1_lembur_1 = 1.5;
//                         }else{
//                             $row1_lembur_1 = 1;
//                         }
// $explode_lembur_2 = explode("|",$row1_pengerjaan->lembur);
//                         $explode_status_lembur_2 = explode("-",$explode_lembur_2[2]);
//                         if($explode_status_lembur_2[1] == 'y'){
//                             $row1_lembur_2 = 1.5;
//                         }else{
//                             $row1_lembur_2 = 1;
//                         }
// $explode_lembur_3 = explode("|",$row1_pengerjaan->lembur);
//                         $explode_status_lembur_3 = explode("-",$explode_lembur_3[3]);
//                         if($explode_status_lembur_3[1] == 'y'){
//                             $row1_lembur_3 = 1.5;
//                         }else{
//                             $row1_lembur_3 = 1;
//                         }
// $explode_lembur_4 = explode("|",$row1_pengerjaan->lembur);
//                         $explode_status_lembur_4 = explode("-",$explode_lembur_4[4]);
//                         if($explode_status_lembur_4[1] == 'y'){
//                             $row1_lembur_4 = 1.5;
//                         }else{
//                             $row1_lembur_4 = 1;
//                         }
// $explode_lembur_5 = explode("|",$row1_pengerjaan->lembur);
//                         $explode_status_lembur_5 = explode("-",$explode_lembur_5[5]);
//                         if($explode_status_lembur_5[1] == 'y'){
//                             $row1_lembur_5 = 1.5;
//                         }else{
//                             $row1_lembur_5 = 1;
//                         }
//                     }

//                     $row1_explode_hasil_kerja_4 = explode("|",$row1_pengerjaan->hasil_kerja_4);
//                     $row1_umk_borongan_lokal_4 = $this->umkBoronganAmbri->select('id','jenis_produk','umk_etiket','umk_las_tepi','umk_las_pojok','umk_ambri')->where('id',$row1_explode_hasil_kerja_1[0])->first();
//                     if(empty($row1_umk_borongan_lokal_4)){
//                         $row1_jenis_produk_4 = '-';
//                         $row1_hasil_kerja_4 = null;
//                         $row1_data_explode_hasil_kerja_4 = '-';
//                         $row1_lembur_4 = 1;
//                     }else{
//                         $row1_jenis_produk_4 = $row1_umk_borongan_lokal_4->jenis_produk;
//                         $row1_hasil_kerja_4 = $row1_explode_hasil_kerja_4[1]*$row1_umk_borongan_lokal_4->umk_las_tepi;
//                         $row1_data_explode_hasil_kerja_4 = $row1_explode_hasil_kerja_4[1];
//                         // $row1_lembur_4 = 1.5;
//                         $explode_lembur_1 = explode("|",$row1_pengerjaan->lembur);
//                         $explode_status_lembur_1 = explode("-",$explode_lembur_1[1]);
//                         if($explode_status_lembur_1[1] == 'y'){
//                             $row1_lembur_1 = 1.5;
//                         }else{
//                             $row1_lembur_1 = 1;
//                         }
// $explode_lembur_2 = explode("|",$row1_pengerjaan->lembur);
//                         $explode_status_lembur_2 = explode("-",$explode_lembur_2[2]);
//                         if($explode_status_lembur_2[1] == 'y'){
//                             $row1_lembur_2 = 1.5;
//                         }else{
//                             $row1_lembur_2 = 1;
//                         }
// $explode_lembur_3 = explode("|",$row1_pengerjaan->lembur);
//                         $explode_status_lembur_3 = explode("-",$explode_lembur_3[3]);
//                         if($explode_status_lembur_3[1] == 'y'){
//                             $row1_lembur_3 = 1.5;
//                         }else{
//                             $row1_lembur_3 = 1;
//                         }
// $explode_lembur_4 = explode("|",$row1_pengerjaan->lembur);
//                         $explode_status_lembur_4 = explode("-",$explode_lembur_4[4]);
//                         if($explode_status_lembur_4[1] == 'y'){
//                             $row1_lembur_4 = 1.5;
//                         }else{
//                             $row1_lembur_4 = 1;
//                         }
// $explode_lembur_5 = explode("|",$row1_pengerjaan->lembur);
//                         $explode_status_lembur_5 = explode("-",$explode_lembur_5[5]);
//                         if($explode_status_lembur_5[1] == 'y'){
//                             $row1_lembur_5 = 1.5;
//                         }else{
//                             $row1_lembur_5 = 1;
//                         }
//                     }

//                     $row1_explode_hasil_kerja_5 = explode("|",$row1_pengerjaan->hasil_kerja_5);
//                     $row1_umk_borongan_lokal_5 = $this->umkBoronganAmbri->select('id','jenis_produk','umk_etiket','umk_las_tepi','umk_las_pojok','umk_ambri')->where('id',$row1_explode_hasil_kerja_1[0])->first();
//                     if(empty($row1_umk_borongan_lokal_5)){
//                         $row1_jenis_produk_5 = '-';
//                         $row1_hasil_kerja_5 = null;
//                         $row1_data_explode_hasil_kerja_5 = '-';
//                         $row1_lembur_5 = 1;
//                     }else{
//                         $row1_jenis_produk_5 = $row1_umk_borongan_lokal_5->jenis_produk;
//                         $row1_hasil_kerja_5 = $row1_explode_hasil_kerja_5[1]*$row1_umk_borongan_lokal_5->umk_las_tepi;
//                         $row1_data_explode_hasil_kerja_5 = $row1_explode_hasil_kerja_5[1];
//                         // $row1_lembur_5 = 1.5;
//                         $explode_lembur_1 = explode("|",$row1_pengerjaan->lembur);
//                         $explode_status_lembur_1 = explode("-",$explode_lembur_1[1]);
//                         if($explode_status_lembur_1[1] == 'y'){
//                             $row1_lembur_1 = 1.5;
//                         }else{
//                             $row1_lembur_1 = 1;
//                         }
// $explode_lembur_2 = explode("|",$row1_pengerjaan->lembur);
//                         $explode_status_lembur_2 = explode("-",$explode_lembur_2[2]);
//                         if($explode_status_lembur_2[1] == 'y'){
//                             $row1_lembur_2 = 1.5;
//                         }else{
//                             $row1_lembur_2 = 1;
//                         }
// $explode_lembur_3 = explode("|",$row1_pengerjaan->lembur);
//                         $explode_status_lembur_3 = explode("-",$explode_lembur_3[3]);
//                         if($explode_status_lembur_3[1] == 'y'){
//                             $row1_lembur_3 = 1.5;
//                         }else{
//                             $row1_lembur_3 = 1;
//                         }
// $explode_lembur_4 = explode("|",$row1_pengerjaan->lembur);
//                         $explode_status_lembur_4 = explode("-",$explode_lembur_4[4]);
//                         if($explode_status_lembur_4[1] == 'y'){
//                             $row1_lembur_4 = 1.5;
//                         }else{
//                             $row1_lembur_4 = 1;
//                         }
// $explode_lembur_5 = explode("|",$row1_pengerjaan->lembur);
//                         $explode_status_lembur_5 = explode("-",$explode_lembur_5[5]);
//                         if($explode_status_lembur_5[1] == 'y'){
//                             $row1_lembur_5 = 1.5;
//                         }else{
//                             $row1_lembur_5 = 1;
//                         }
//                     }
//                 }

//                 if ($row1_pengerjaan->operator_karyawan->jenis_operator_detail_pekerjaan_id == 11) {
//                     $row1_explode_hasil_kerja_1 = explode("|",$row1_pengerjaan->hasil_kerja_1);
//                     $row1_umk_borongan_lokal_1 = $this->umkBoronganAmbri->select('id','jenis_produk','umk_etiket','umk_las_tepi','umk_las_pojok','umk_ambri')->where('id',$row1_explode_hasil_kerja_1[0])->first();
//                     if(empty($row1_umk_borongan_lokal_1)){
//                         $row1_jenis_produk_1 = '-';
//                         $row1_hasil_kerja_1 = null;
//                         $row1_data_explode_hasil_kerja_1 = '-';
//                         $row1_lembur_1 = 1;
//                     }else{
//                         $row1_jenis_produk_1 = $row1_umk_borongan_lokal_1->jenis_produk;
//                         $row1_hasil_kerja_1 = $row1_explode_hasil_kerja_1[1]*$row1_umk_borongan_lokal_1->umk_ambri;
//                         $row1_data_explode_hasil_kerja_1 = $row1_explode_hasil_kerja_1[1];
//                         // $row1_lembur_1 = 1.5;
//                         $explode_lembur_1 = explode("|",$row1_pengerjaan->lembur);
//                         $explode_status_lembur_1 = explode("-",$explode_lembur_1[1]);
//                         if($explode_status_lembur_1[1] == 'y'){
//                             $row1_lembur_1 = 1.5;
//                         }else{
//                             $row1_lembur_1 = 1;
//                         }
// $explode_lembur_2 = explode("|",$row1_pengerjaan->lembur);
//                         $explode_status_lembur_2 = explode("-",$explode_lembur_2[2]);
//                         if($explode_status_lembur_2[1] == 'y'){
//                             $row1_lembur_2 = 1.5;
//                         }else{
//                             $row1_lembur_2 = 1;
//                         }
// $explode_lembur_3 = explode("|",$row1_pengerjaan->lembur);
//                         $explode_status_lembur_3 = explode("-",$explode_lembur_3[3]);
//                         if($explode_status_lembur_3[1] == 'y'){
//                             $row1_lembur_3 = 1.5;
//                         }else{
//                             $row1_lembur_3 = 1;
//                         }
// $explode_lembur_4 = explode("|",$row1_pengerjaan->lembur);
//                         $explode_status_lembur_4 = explode("-",$explode_lembur_4[4]);
//                         if($explode_status_lembur_4[1] == 'y'){
//                             $row1_lembur_4 = 1.5;
//                         }else{
//                             $row1_lembur_4 = 1;
//                         }
// $explode_lembur_5 = explode("|",$row1_pengerjaan->lembur);
//                         $explode_status_lembur_5 = explode("-",$explode_lembur_5[5]);
//                         if($explode_status_lembur_5[1] == 'y'){
//                             $row1_lembur_5 = 1.5;
//                         }else{
//                             $row1_lembur_5 = 1;
//                         }
//                     }

//                     $row1_explode_hasil_kerja_2 = explode("|",$row1_pengerjaan->hasil_kerja_2);
//                     $row1_umk_borongan_lokal_2 = $this->umkBoronganAmbri->select('id','jenis_produk','umk_etiket','umk_las_tepi','umk_las_pojok','umk_ambri')->where('id',$row1_explode_hasil_kerja_1[0])->first();
//                     if(empty($row1_umk_borongan_lokal_2)){
//                         $row1_jenis_produk_2 = '-';
//                         $row1_hasil_kerja_2 = null;
//                         $row1_data_explode_hasil_kerja_2 = '-';
//                         $row1_lembur_2 = 1;
//                     }else{
//                         $row1_jenis_produk_2 = $row1_umk_borongan_lokal_2->jenis_produk;
//                         $row1_hasil_kerja_2 = $row1_explode_hasil_kerja_2[1]*$row1_umk_borongan_lokal_2->umk_ambri;
//                         $row1_data_explode_hasil_kerja_2 = $row1_explode_hasil_kerja_2[1];
//                         // $row1_lembur_2 = 1.5;
//                         $explode_lembur_1 = explode("|",$row1_pengerjaan->lembur);
//                         $explode_status_lembur_1 = explode("-",$explode_lembur_1[1]);
//                         if($explode_status_lembur_1[1] == 'y'){
//                             $row1_lembur_1 = 1.5;
//                         }else{
//                             $row1_lembur_1 = 1;
//                         }
// $explode_lembur_2 = explode("|",$row1_pengerjaan->lembur);
//                         $explode_status_lembur_2 = explode("-",$explode_lembur_2[2]);
//                         if($explode_status_lembur_2[1] == 'y'){
//                             $row1_lembur_2 = 1.5;
//                         }else{
//                             $row1_lembur_2 = 1;
//                         }
// $explode_lembur_3 = explode("|",$row1_pengerjaan->lembur);
//                         $explode_status_lembur_3 = explode("-",$explode_lembur_3[3]);
//                         if($explode_status_lembur_3[1] == 'y'){
//                             $row1_lembur_3 = 1.5;
//                         }else{
//                             $row1_lembur_3 = 1;
//                         }
// $explode_lembur_4 = explode("|",$row1_pengerjaan->lembur);
//                         $explode_status_lembur_4 = explode("-",$explode_lembur_4[4]);
//                         if($explode_status_lembur_4[1] == 'y'){
//                             $row1_lembur_4 = 1.5;
//                         }else{
//                             $row1_lembur_4 = 1;
//                         }
// $explode_lembur_5 = explode("|",$row1_pengerjaan->lembur);
//                         $explode_status_lembur_5 = explode("-",$explode_lembur_5[5]);
//                         if($explode_status_lembur_5[1] == 'y'){
//                             $row1_lembur_5 = 1.5;
//                         }else{
//                             $row1_lembur_5 = 1;
//                         }
//                     }

//                     $row1_explode_hasil_kerja_3 = explode("|",$row1_pengerjaan->hasil_kerja_3);
//                     $row1_umk_borongan_lokal_3 = $this->umkBoronganAmbri->select('id','jenis_produk','umk_etiket','umk_las_tepi','umk_las_pojok','umk_ambri')->where('id',$row1_explode_hasil_kerja_1[0])->first();
//                     if(empty($row1_umk_borongan_lokal_3)){
//                         $row1_jenis_produk_3 = '-';
//                         $row1_hasil_kerja_3 = null;
//                         $row1_data_explode_hasil_kerja_3 = '-';
//                         $row1_lembur_3 = 1;
//                     }else{
//                         $row1_jenis_produk_3 = $row1_umk_borongan_lokal_3->jenis_produk;
//                         $row1_hasil_kerja_3 = $row1_explode_hasil_kerja_3[1]*$row1_umk_borongan_lokal_3->umk_ambri;
//                         $row1_data_explode_hasil_kerja_3 = $row1_explode_hasil_kerja_3[1];
//                         // $row1_lembur_3 = 1.5;
//                         $explode_lembur_1 = explode("|",$row1_pengerjaan->lembur);
//                         $explode_status_lembur_1 = explode("-",$explode_lembur_1[1]);
//                         if($explode_status_lembur_1[1] == 'y'){
//                             $row1_lembur_1 = 1.5;
//                         }else{
//                             $row1_lembur_1 = 1;
//                         }
// $explode_lembur_2 = explode("|",$row1_pengerjaan->lembur);
//                         $explode_status_lembur_2 = explode("-",$explode_lembur_2[2]);
//                         if($explode_status_lembur_2[1] == 'y'){
//                             $row1_lembur_2 = 1.5;
//                         }else{
//                             $row1_lembur_2 = 1;
//                         }
// $explode_lembur_3 = explode("|",$row1_pengerjaan->lembur);
//                         $explode_status_lembur_3 = explode("-",$explode_lembur_3[3]);
//                         if($explode_status_lembur_3[1] == 'y'){
//                             $row1_lembur_3 = 1.5;
//                         }else{
//                             $row1_lembur_3 = 1;
//                         }
// $explode_lembur_4 = explode("|",$row1_pengerjaan->lembur);
//                         $explode_status_lembur_4 = explode("-",$explode_lembur_4[4]);
//                         if($explode_status_lembur_4[1] == 'y'){
//                             $row1_lembur_4 = 1.5;
//                         }else{
//                             $row1_lembur_4 = 1;
//                         }
// $explode_lembur_5 = explode("|",$row1_pengerjaan->lembur);
//                         $explode_status_lembur_5 = explode("-",$explode_lembur_5[5]);
//                         if($explode_status_lembur_5[1] == 'y'){
//                             $row1_lembur_5 = 1.5;
//                         }else{
//                             $row1_lembur_5 = 1;
//                         }
//                     }

//                     $row1_explode_hasil_kerja_4 = explode("|",$row1_pengerjaan->hasil_kerja_4);
//                     $row1_umk_borongan_lokal_4 = $this->umkBoronganAmbri->select('id','jenis_produk','umk_etiket','umk_las_tepi','umk_las_pojok','umk_ambri')->where('id',$row1_explode_hasil_kerja_1[0])->first();
//                     if(empty($row1_umk_borongan_lokal_4)){
//                         $row1_jenis_produk_4 = '-';
//                         $row1_hasil_kerja_4 = null;
//                         $row1_data_explode_hasil_kerja_4 = '-';
//                         $row1_lembur_4 = 1;
//                     }else{
//                         $row1_jenis_produk_4 = $row1_umk_borongan_lokal_4->jenis_produk;
//                         $row1_hasil_kerja_4 = $row1_explode_hasil_kerja_4[1]*$row1_umk_borongan_lokal_4->umk_ambri;
//                         $row1_data_explode_hasil_kerja_4 = $row1_explode_hasil_kerja_4[1];
//                         // $row1_lembur_4 = 1.5;
//                         $explode_lembur_1 = explode("|",$row1_pengerjaan->lembur);
//                         $explode_status_lembur_1 = explode("-",$explode_lembur_1[1]);
//                         if($explode_status_lembur_1[1] == 'y'){
//                             $row1_lembur_1 = 1.5;
//                         }else{
//                             $row1_lembur_1 = 1;
//                         }
// $explode_lembur_2 = explode("|",$row1_pengerjaan->lembur);
//                         $explode_status_lembur_2 = explode("-",$explode_lembur_2[2]);
//                         if($explode_status_lembur_2[1] == 'y'){
//                             $row1_lembur_2 = 1.5;
//                         }else{
//                             $row1_lembur_2 = 1;
//                         }
// $explode_lembur_3 = explode("|",$row1_pengerjaan->lembur);
//                         $explode_status_lembur_3 = explode("-",$explode_lembur_3[3]);
//                         if($explode_status_lembur_3[1] == 'y'){
//                             $row1_lembur_3 = 1.5;
//                         }else{
//                             $row1_lembur_3 = 1;
//                         }
// $explode_lembur_4 = explode("|",$row1_pengerjaan->lembur);
//                         $explode_status_lembur_4 = explode("-",$explode_lembur_4[4]);
//                         if($explode_status_lembur_4[1] == 'y'){
//                             $row1_lembur_4 = 1.5;
//                         }else{
//                             $row1_lembur_4 = 1;
//                         }
// $explode_lembur_5 = explode("|",$row1_pengerjaan->lembur);
//                         $explode_status_lembur_5 = explode("-",$explode_lembur_5[5]);
//                         if($explode_status_lembur_5[1] == 'y'){
//                             $row1_lembur_5 = 1.5;
//                         }else{
//                             $row1_lembur_5 = 1;
//                         }
//                     }

//                     $row1_explode_hasil_kerja_5 = explode("|",$row1_pengerjaan->hasil_kerja_5);
//                     $row1_umk_borongan_lokal_5 = $this->umkBoronganAmbri->select('id','jenis_produk','umk_etiket','umk_las_tepi','umk_las_pojok','umk_ambri')->where('id',$row1_explode_hasil_kerja_1[0])->first();
//                     if(empty($row1_umk_borongan_lokal_5)){
//                         $row1_jenis_produk_5 = '-';
//                         $row1_hasil_kerja_5 = null;
//                         $row1_data_explode_hasil_kerja_5 = '-';
//                         $row1_lembur_5 = 1;
//                     }else{
//                         $row1_jenis_produk_5 = $row1_umk_borongan_lokal_5->jenis_produk;
//                         $row1_hasil_kerja_5 = $row1_explode_hasil_kerja_5[1]*$row1_umk_borongan_lokal_5->umk_ambri;
//                         $row1_data_explode_hasil_kerja_5 = $row1_explode_hasil_kerja_5[1];
//                         // $row1_lembur_5 = 1.5;
//                         $explode_lembur_1 = explode("|",$row1_pengerjaan->lembur);
//                         $explode_status_lembur_1 = explode("-",$explode_lembur_1[1]);
//                         if($explode_status_lembur_1[1] == 'y'){
//                             $row1_lembur_1 = 1.5;
//                         }else{
//                             $row1_lembur_1 = 1;
//                         }
// $explode_lembur_2 = explode("|",$row1_pengerjaan->lembur);
//                         $explode_status_lembur_2 = explode("-",$explode_lembur_2[2]);
//                         if($explode_status_lembur_2[1] == 'y'){
//                             $row1_lembur_2 = 1.5;
//                         }else{
//                             $row1_lembur_2 = 1;
//                         }
// $explode_lembur_3 = explode("|",$row1_pengerjaan->lembur);
//                         $explode_status_lembur_3 = explode("-",$explode_lembur_3[3]);
//                         if($explode_status_lembur_3[1] == 'y'){
//                             $row1_lembur_3 = 1.5;
//                         }else{
//                             $row1_lembur_3 = 1;
//                         }
// $explode_lembur_4 = explode("|",$row1_pengerjaan->lembur);
//                         $explode_status_lembur_4 = explode("-",$explode_lembur_4[4]);
//                         if($explode_status_lembur_4[1] == 'y'){
//                             $row1_lembur_4 = 1.5;
//                         }else{
//                             $row1_lembur_4 = 1;
//                         }
// $explode_lembur_5 = explode("|",$row1_pengerjaan->lembur);
//                         $explode_status_lembur_5 = explode("-",$explode_lembur_5[5]);
//                         if($explode_status_lembur_5[1] == 'y'){
//                             $row1_lembur_5 = 1.5;
//                         }else{
//                             $row1_lembur_5 = 1;
//                         }
//                     }
//                 }
//                 //End Ekspor
//                 // $row1_total_hasil_kerja = $row1_hasil_kerja_1+$row1_hasil_kerja_2+$row1_hasil_kerja_3+$row1_hasil_kerja_4+$row1_hasil_kerja_5;
//                 $row1_total_hasil_kerja = ($row1_hasil_kerja_1*$row1_lembur_1)+($row1_hasil_kerja_2*$row1_lembur_2)+($row1_hasil_kerja_3*$row1_lembur_3)+($row1_hasil_kerja_4*$row1_lembur_4)+($row1_hasil_kerja_5*$row1_lembur_5);
//                 // $row1_total_hasil_kerja = ($row1_hasil_kerja_1*$row1_lembur_1)+($row1_hasil_kerja_2*$row1_lembur_2)+($row1_hasil_kerja_3*$row1_lembur_3)+($row1_hasil_kerja_4*$row1_lembur_4)+($row1_hasil_kerja_5*$row1_lembur_5);
//                 array_push($row1_upah_lembur,$row1_pengerjaan->uang_lembur);
//                 $hasil_gaji_row1 = $row1_total_hasil_kerja-$row1_pengerjaan->uang_lembur;
//                 array_push($row1_total_upah_hasil_kerja,$hasil_gaji_row1);
//                 // array_push($row1_total_upah_hasil_kerja,$row1_total_hasil_kerja);
//             }

//             // dd($row1_upah_lembur);

//             if ($data['new_data_pengerjaan']['akhir_bulan'] == 'y') {
//                 if (empty($qw->tunjangan_kerja)) {
//                     $row1_tunjangan_kerja = 0;
//                 }else{
//                     $row1_tunjangan_kerja = $qw->tunjangan_kerja;
//                 }
//             }else{
//                 $row1_tunjangan_kerja = 0;
//             }

//             if (empty($qw->tunjangan_kehadiran)) {
//                 $row1_tunjangan_kehadiran = 0;
//             }else{
//                 $row1_tunjangan_kehadiran = $qw->tunjangan_kehadiran;
//             }

//             if (empty($qw->uang_makan)) {
//                 $row1_uang_makan = 0;
//             }else{
//                 $row1_uang_makan = $qw->uang_makan;
//             }

//             if (empty($qw->plus_1)) {
//                 $row1_plus_1 = 0;
//                 $row1_ket_plus_1 = null;
//             }else{
//                 $row1_explode_plus_1 = explode("|",$qw->plus_1);
//                 $row1_plus_1 = floatval($row1_explode_plus_1[0]);
//                 $row1_ket_plus_1 = $row1_explode_plus_1[1];
//             }

//             if (empty($qw->plus_2)) {
//                 $row1_plus_2 = 0;
//                 $row1_ket_plus_2 = null;
//             }else{
//                 $row1_explode_plus_2 = explode("|",$qw->plus_2);
//                 $row1_plus_2 = floatval($row1_explode_plus_2[0]);
//                 $row1_ket_plus_2 = $row1_explode_plus_2[1];
//             }

//             if (empty($qw->plus_3)) {
//                 $row1_plus_3 = 0;
//                 $row1_ket_plus_3 = null;
//             }else{
//                 $row1_explode_plus_3 = explode("|",$qw->plus_3);
//                 $row1_plus_3 = floatval($row1_explode_plus_3[0]);
//                 $row1_ket_plus_3 = $row1_explode_plus_3[1];
//             }

//             if (empty($qw->jht)) {
//                 $row1_jht = 0;
//             }else{
//                 $row1_jht = $qw->jht;
//             }

//             if (empty($qw->bpjs_kesehatan)) {
//                 $row1_bpjs_kesehatan = 0;
//             }else{
//                 $row1_bpjs_kesehatan = $qw->bpjs_kesehatan;
//             }

//             if (empty($qw->minus_1)) {
//                 $row1_minus_1 = '0';
//                 $row1_ket_minus_1 = null;
//             }else{
//                 $row1_explode_minus_1 = explode("|",$qw->minus_1);
//                 $row1_minus_1 = floatval($row1_explode_minus_1[0]);
//                 $row1_ket_minus_1 = $row1_explode_minus_1[1];
//             }

//             if (empty($qw->minus_2)) {
//                 $row1_minus_2 = 0;
//                 $row1_ket_minus_2 = null;
//             }else{
//                 $row1_explode_minus_2 = explode("|",$qw->minus_2);
//                 $row1_minus_2 = floatval($row1_explode_minus_2[0]);
//                 $row1_ket_minus_2 = $row1_explode_minus_2[1];
//             }

//             $row1_total_gaji_diterima = (array_sum($row1_total_upah_hasil_kerja)
//                                     +array_sum($row1_upah_lembur)
//                                     +$row1_tunjangan_kerja+
//                                     $row1_tunjangan_kehadiran+
//                                     $row1_uang_makan+
//                                     $row1_plus_1+
//                                     $row1_plus_2+
//                                     $row1_plus_3
//                                     )
//                                     -
//                                     ($row1_jht+$row1_bpjs_kesehatan+$row1_minus_1+$row1_minus_2)
//                                     ;
//             ###############endrow 1
            
//             #################row 2
//             $id_selanjutnya = $qw->id+1;
//             $row2_weekly = PengerjaanWeekly::select([
//                             'pengerjaan_weekly.id as id',
//                             'pengerjaan_weekly.operator_karyawan_id as operator_karyawan_id',
//                             'jenis_operator_detail_pekerjaan.jenis_posisi_pekerjaan as jenis_posisi_pekerjaan',
//                             'operator_karyawan.nik as nik',
//                             'biodata_karyawan.nama as nama',
//                             'pengerjaan_weekly.upah_dasar as upah_dasar',
//                             'pengerjaan_weekly.tunjangan_kerja as tunjangan_kerja',
//                             'pengerjaan_weekly.tunjangan_kehadiran as tunjangan_kehadiran',
//                             'pengerjaan_weekly.uang_makan as uang_makan',
//                             'pengerjaan_weekly.plus_1 as plus_1',
//                             'pengerjaan_weekly.plus_2 as plus_2',
//                             'pengerjaan_weekly.plus_3 as plus_3',
//                             'pengerjaan_weekly.minus_1 as minus_1',
//                             'pengerjaan_weekly.minus_2 as minus_2',
//                             'pengerjaan_weekly.jht as jht',
//                             'pengerjaan_weekly.bpjs_kesehatan as bpjs_kesehatan',
//                             'operator_karyawan.jenis_operator_detail_pekerjaan_id as jenis_operator_detail_pekerjaan_id'
//                         ])
//                         ->leftJoin('operator_karyawan','operator_karyawan.id','=','pengerjaan_weekly.operator_karyawan_id')
//                         ->leftJoin('itic_emp_new.biodata_karyawan','biodata_karyawan.nik','=','operator_karyawan.nik')
//                         ->leftJoin('jenis_operator_detail_pekerjaan','jenis_operator_detail_pekerjaan.id','=','operator_karyawan.jenis_operator_detail_pekerjaan_id')
//                         ->where('pengerjaan_weekly.id',$id_selanjutnya)
//                         ->where('pengerjaan_weekly.kode_pengerjaan',$kode_pengerjaan)
//                         ->orderBy('operator_karyawan.jenis_operator_detail_pekerjaan_id','asc')
//                         ->orderBy('biodata_karyawan.nama','asc')
//                         ->first();
//             // dd($row2_weekly);
//             if (empty($row2_weekly)) {
//                 $row2_nik = null;
//                 $row2_operator_karyawan_id = null;
//                 $row2_jenis_posisi_pekerjaan = 0;
//             }else{
//                 $row2_nik = $row2_weekly->nik;
//                 $row2_operator_karyawan_id = $row2_weekly->operator_karyawan_id;
//                 $row2_jenis_posisi_pekerjaan = $row2_weekly->jenis_posisi_pekerjaan;
//             }

//             $row2_nama = BiodataKaryawan::where('nik',$row2_nik)->orderBy('nama','asc')->first();
//             // dd($row2_nama);
//             if (empty($row2_nama)) {
//                 $row2_nama_2 = null;
//                 $row2_nik = null;
//             }else{
//                 $row2_nama_2 = $row2_nama->nama;
//                 $row2_nik = $row2_nama->nik;
//             }
            
//             $row2_upah_lembur = [];
//             $row2_total_upah_hasil_kerja = [];
//             $row2_pengerjaans = Pengerjaan::where('operator_karyawan_id',$row2_operator_karyawan_id)
//                                         ->where('kode_pengerjaan',$kode_pengerjaan)
//                                         ->get();
//             foreach ($row2_pengerjaans as $keys => $row2_pengerjaan) {
//                 //Begin Lokal
//                 if ($row2_pengerjaan->operator_karyawan->jenis_operator_detail_pekerjaan_id == 1) {
//                     $row2_explode_hasil_kerja_1 = explode("|",$row2_pengerjaan->hasil_kerja_1);
//                     $row2_umk_borongan_lokal_1 = $this->umkBoronganLokal->select('id','jenis_produk','umk_packing','umk_bandrol','umk_inner','umk_outer')->where('id',$row2_explode_hasil_kerja_1[0])->first();
//                     if(empty($row2_umk_borongan_lokal_1)){
//                         $row2_jenis_produk_1 = '-';
//                         $row2_hasil_kerja_1 = null;
//                         $row2_data_explode_hasil_kerja_1 = '-';
//                         $row2_lembur_1 = 1;
//                     }else{
//                         $row2_jenis_produk_1 = $row2_umk_borongan_lokal_1->jenis_produk;
//                         $row2_hasil_kerja_1 = $row2_explode_hasil_kerja_1[1]*$row2_umk_borongan_lokal_1->umk_packing;
//                         $row2_data_explode_hasil_kerja_1 = $row2_explode_hasil_kerja_1[1];
//                         // $row2_lembur_1 = 1.5;
//                         $explode2_lembur_1 = explode("|",$row2_pengerjaan->lembur);
//                         $explode2_status_lembur_1 = explode("-",$explode2_lembur_1[1]);
//                         if($explode2_status_lembur_1[1] == 'y'){
//                             $row2_lembur_1 = 1.5;
//                         }else{
//                             $row2_lembur_1 = 1;
//                         }
// $explode2_lembur_2 = explode("|",$row2_pengerjaan->lembur);
//                         $explode2_status_lembur_2 = explode("-",$explode2_lembur_2[2]);
//                         if($explode2_status_lembur_2[1] == 'y'){
//                             $row2_lembur_2 = 1.5;
//                         }else{
//                             $row2_lembur_2 = 1;
//                         }
// $explode2_lembur_3 = explode("|",$row2_pengerjaan->lembur);
//                         $explode2_status_lembur_3 = explode("-",$explode2_lembur_3[3]);
//                         if($explode2_status_lembur_3[1] == 'y'){
//                             $row2_lembur_3 = 1.5;
//                         }else{
//                             $row2_lembur_3 = 1;
//                         }
// $explode2_lembur_4 = explode("|",$row2_pengerjaan->lembur);
//                         $explode2_status_lembur_4 = explode("-",$explode2_lembur_4[4]);
//                         if($explode2_status_lembur_4[1] == 'y'){
//                             $row2_lembur_4 = 1.5;
//                         }else{
//                             $row2_lembur_4 = 1;
//                         }
// $explode2_lembur_5 = explode("|",$row2_pengerjaan->lembur);
//                         $explode2_status_lembur_5 = explode("-",$explode2_lembur_5[5]);
//                         if($explode2_status_lembur_5[1] == 'y'){
//                             $row2_lembur_5 = 1.5;
//                         }else{
//                             $row2_lembur_5 = 1;
//                         }
//                     }

//                     $row2_explode_hasil_kerja_2 = explode("|",$row2_pengerjaan->hasil_kerja_2);
//                     $row2_umk_borongan_lokal_2 = $this->umkBoronganLokal->select('id','jenis_produk','umk_packing','umk_bandrol','umk_inner','umk_outer')->where('id',$row2_explode_hasil_kerja_2[0])->first();
//                     if(empty($row2_umk_borongan_lokal_2)){
//                         $row2_jenis_produk_2 = '-';
//                         $row2_hasil_kerja_2 = null;
//                         $row2_data_explode_hasil_kerja_2 = '-';
//                         $row2_lembur_2 = 1;
//                     }else{
//                         $row2_jenis_produk_2 = $row2_umk_borongan_lokal_2->jenis_produk;
//                         $row2_hasil_kerja_2 = $row2_explode_hasil_kerja_2[1]*$row2_umk_borongan_lokal_2->umk_packing;
//                         $row2_data_explode_hasil_kerja_2 = $row2_explode_hasil_kerja_2[1];
//                         // $row2_lembur_2 = 1.5;
//                         $explode2_lembur_1 = explode("|",$row2_pengerjaan->lembur);
//                         $explode2_status_lembur_1 = explode("-",$explode2_lembur_1[1]);
//                         if($explode2_status_lembur_1[1] == 'y'){
//                             $row5_lembur_1 = 1.5;
//                         }else{
//                             $row5_lembur_1 = 1;
//                         }
// $explode2_lembur_2 = explode("|",$row2_pengerjaan->lembur);
//                         $explode2_status_lembur_2 = explode("-",$explode2_lembur_2[2]);
//                         if($explode2_status_lembur_2[1] == 'y'){
//                             $row5_lembur_2 = 1.5;
//                         }else{
//                             $row5_lembur_2 = 1;
//                         }
// $explode2_lembur_3 = explode("|",$row2_pengerjaan->lembur);
//                         $explode2_status_lembur_3 = explode("-",$explode2_lembur_3[3]);
//                         if($explode2_status_lembur_3[1] == 'y'){
//                             $row5_lembur_3 = 1.5;
//                         }else{
//                             $row5_lembur_3 = 1;
//                         }
// $explode2_lembur_4 = explode("|",$row2_pengerjaan->lembur);
//                         $explode2_status_lembur_4 = explode("-",$explode2_lembur_4[4]);
//                         if($explode2_status_lembur_4[1] == 'y'){
//                             $row5_lembur_4 = 1.5;
//                         }else{
//                             $row5_lembur_4 = 1;
//                         }
// $explode2_lembur_5 = explode("|",$row2_pengerjaan->lembur);
//                         $explode2_status_lembur_5 = explode("-",$explode2_lembur_5[5]);
//                         if($explode2_status_lembur_5[1] == 'y'){
//                             $row5_lembur_5 = 1.5;
//                         }else{
//                             $row5_lembur_5 = 1;
//                         }
//                     }

//                     $row2_explode_hasil_kerja_3 = explode("|",$row2_pengerjaan->hasil_kerja_3);
//                     $row2_umk_borongan_lokal_3 = $this->umkBoronganLokal->select('id','jenis_produk','umk_packing','umk_bandrol','umk_inner','umk_outer')->where('id',$row2_explode_hasil_kerja_3[0])->first();
//                     if(empty($row2_umk_borongan_lokal_3)){
//                         $row2_jenis_produk_3 = '-';
//                         $row2_hasil_kerja_3 = null;
//                         $row2_data_explode_hasil_kerja_3 = '-';
//                         $row2_lembur_3 = 1;
//                     }else{
//                         $row2_jenis_produk_3 = $row2_umk_borongan_lokal_3->jenis_produk;
//                         $row2_hasil_kerja_3 = $row2_explode_hasil_kerja_3[1]*$row2_umk_borongan_lokal_3->umk_packing;
//                         $row2_data_explode_hasil_kerja_3 = $row2_explode_hasil_kerja_3[1];
//                         // $row2_lembur_3 = 1.5;
//                         $explode2_lembur_1 = explode("|",$row2_pengerjaan->lembur);
//                         $explode2_status_lembur_1 = explode("-",$explode2_lembur_1[1]);
//                         if($explode2_status_lembur_1[1] == 'y'){
//                             $row5_lembur_1 = 1.5;
//                         }else{
//                             $row5_lembur_1 = 1;
//                         }
// $explode2_lembur_2 = explode("|",$row2_pengerjaan->lembur);
//                         $explode2_status_lembur_2 = explode("-",$explode2_lembur_2[2]);
//                         if($explode2_status_lembur_2[1] == 'y'){
//                             $row5_lembur_2 = 1.5;
//                         }else{
//                             $row5_lembur_2 = 1;
//                         }
// $explode2_lembur_3 = explode("|",$row2_pengerjaan->lembur);
//                         $explode2_status_lembur_3 = explode("-",$explode2_lembur_3[3]);
//                         if($explode2_status_lembur_3[1] == 'y'){
//                             $row5_lembur_3 = 1.5;
//                         }else{
//                             $row5_lembur_3 = 1;
//                         }
// $explode2_lembur_4 = explode("|",$row2_pengerjaan->lembur);
//                         $explode2_status_lembur_4 = explode("-",$explode2_lembur_4[4]);
//                         if($explode2_status_lembur_4[1] == 'y'){
//                             $row5_lembur_4 = 1.5;
//                         }else{
//                             $row5_lembur_4 = 1;
//                         }
// $explode2_lembur_5 = explode("|",$row2_pengerjaan->lembur);
//                         $explode2_status_lembur_5 = explode("-",$explode2_lembur_5[5]);
//                         if($explode2_status_lembur_5[1] == 'y'){
//                             $row5_lembur_5 = 1.5;
//                         }else{
//                             $row5_lembur_5 = 1;
//                         }
//                     }

//                     $row2_explode_hasil_kerja_4 = explode("|",$row2_pengerjaan->hasil_kerja_4);
//                     $row2_umk_borongan_lokal_4 = $this->umkBoronganLokal->select('id','jenis_produk','umk_packing','umk_bandrol','umk_inner','umk_outer')->where('id',$row2_explode_hasil_kerja_4[0])->first();
//                     if(empty($row2_umk_borongan_lokal_4)){
//                         $row2_jenis_produk_4 = '-';
//                         $row2_hasil_kerja_4 = null;
//                         $row2_data_explode_hasil_kerja_4 = '-';
//                         $row2_lembur_4 = 1;
//                     }else{
//                         $row2_jenis_produk_4 = $row2_umk_borongan_lokal_4->jenis_produk;
//                         $row2_hasil_kerja_4 = $row2_explode_hasil_kerja_4[1]*$row2_umk_borongan_lokal_4->umk_packing;
//                         $row2_data_explode_hasil_kerja_4 = $row2_explode_hasil_kerja_4[1];
//                         // $row2_lembur_4 = 1.5;
//                         $explode2_lembur_1 = explode("|",$row2_pengerjaan->lembur);
//                         $explode2_status_lembur_1 = explode("-",$explode2_lembur_1[1]);
//                         if($explode2_status_lembur_1[1] == 'y'){
//                             $row5_lembur_1 = 1.5;
//                         }else{
//                             $row5_lembur_1 = 1;
//                         }
// $explode2_lembur_2 = explode("|",$row2_pengerjaan->lembur);
//                         $explode2_status_lembur_2 = explode("-",$explode2_lembur_2[2]);
//                         if($explode2_status_lembur_2[1] == 'y'){
//                             $row5_lembur_2 = 1.5;
//                         }else{
//                             $row5_lembur_2 = 1;
//                         }
// $explode2_lembur_3 = explode("|",$row2_pengerjaan->lembur);
//                         $explode2_status_lembur_3 = explode("-",$explode2_lembur_3[3]);
//                         if($explode2_status_lembur_3[1] == 'y'){
//                             $row5_lembur_3 = 1.5;
//                         }else{
//                             $row5_lembur_3 = 1;
//                         }
// $explode2_lembur_4 = explode("|",$row2_pengerjaan->lembur);
//                         $explode2_status_lembur_4 = explode("-",$explode2_lembur_4[4]);
//                         if($explode2_status_lembur_4[1] == 'y'){
//                             $row5_lembur_4 = 1.5;
//                         }else{
//                             $row5_lembur_4 = 1;
//                         }
// $explode2_lembur_5 = explode("|",$row2_pengerjaan->lembur);
//                         $explode2_status_lembur_5 = explode("-",$explode2_lembur_5[5]);
//                         if($explode2_status_lembur_5[1] == 'y'){
//                             $row5_lembur_5 = 1.5;
//                         }else{
//                             $row5_lembur_5 = 1;
//                         }
//                     }

//                     $row2_explode_hasil_kerja_5 = explode("|",$row2_pengerjaan->hasil_kerja_5);
//                     $row2_umk_borongan_lokal_5 = $this->umkBoronganLokal->select('id','jenis_produk','umk_packing','umk_bandrol','umk_inner','umk_outer')->where('id',$row2_explode_hasil_kerja_5[0])->first();
//                     if(empty($row2_umk_borongan_lokal_5)){
//                         $row2_jenis_produk_5 = '-';
//                         $row2_hasil_kerja_5 = null;
//                         $row2_data_explode_hasil_kerja_5 = '-';
//                         $row2_lembur_5 = 1;
//                     }else{
//                         $row2_jenis_produk_5 = $row2_umk_borongan_lokal_5->jenis_produk;
//                         $row2_hasil_kerja_5 = $row2_explode_hasil_kerja_5[1]*$row2_umk_borongan_lokal_5->umk_packing;
//                         $row2_data_explode_hasil_kerja_5 = $row2_explode_hasil_kerja_5[1];
//                         // $row2_lembur_5 = 1.5;
//                         $explode2_lembur_1 = explode("|",$row2_pengerjaan->lembur);
//                         $explode2_status_lembur_1 = explode("-",$explode2_lembur_1[1]);
//                         if($explode2_status_lembur_1[1] == 'y'){
//                             $row5_lembur_1 = 1.5;
//                         }else{
//                             $row5_lembur_1 = 1;
//                         }
// $explode2_lembur_2 = explode("|",$row2_pengerjaan->lembur);
//                         $explode2_status_lembur_2 = explode("-",$explode2_lembur_2[2]);
//                         if($explode2_status_lembur_2[1] == 'y'){
//                             $row5_lembur_2 = 1.5;
//                         }else{
//                             $row5_lembur_2 = 1;
//                         }
// $explode2_lembur_3 = explode("|",$row2_pengerjaan->lembur);
//                         $explode2_status_lembur_3 = explode("-",$explode2_lembur_3[3]);
//                         if($explode2_status_lembur_3[1] == 'y'){
//                             $row5_lembur_3 = 1.5;
//                         }else{
//                             $row5_lembur_3 = 1;
//                         }
// $explode2_lembur_4 = explode("|",$row2_pengerjaan->lembur);
//                         $explode2_status_lembur_4 = explode("-",$explode2_lembur_4[4]);
//                         if($explode2_status_lembur_4[1] == 'y'){
//                             $row5_lembur_4 = 1.5;
//                         }else{
//                             $row5_lembur_4 = 1;
//                         }
// $explode2_lembur_5 = explode("|",$row2_pengerjaan->lembur);
//                         $explode2_status_lembur_5 = explode("-",$explode2_lembur_5[5]);
//                         if($explode2_status_lembur_5[1] == 'y'){
//                             $row5_lembur_5 = 1.5;
//                         }else{
//                             $row5_lembur_5 = 1;
//                         }
//                     }
//                 }

//                 if ($row2_pengerjaan->operator_karyawan->jenis_operator_detail_pekerjaan_id == 2) {
//                     $row2_explode_hasil_kerja_1 = explode("|",$row2_pengerjaan->hasil_kerja_1);
//                     $row2_umk_borongan_lokal_1 = $this->umkBoronganLokal->select('id','jenis_produk','umk_packing','umk_bandrol','umk_inner','umk_outer')->where('id',$row2_explode_hasil_kerja_1[0])->first();
//                     if(empty($row2_umk_borongan_lokal_1)){
//                         $row2_jenis_produk_1 = '-';
//                         $row2_hasil_kerja_1 = null;
//                         $row2_data_explode_hasil_kerja_1 = '-';
//                         $row2_lembur_1 = 1;
//                     }else{
//                         $row2_jenis_produk_1 = $row2_umk_borongan_lokal_1->jenis_produk;
//                         $row2_hasil_kerja_1 = $row2_explode_hasil_kerja_1[1]*$row2_umk_borongan_lokal_1->umk_bandrol;
//                         $row2_data_explode_hasil_kerja_1 = $row2_explode_hasil_kerja_1[1];
//                         // $row2_lembur_1 = 1.5;
//                         $explode2_lembur_1 = explode("|",$row2_pengerjaan->lembur);
//                         $explode2_status_lembur_1 = explode("-",$explode2_lembur_1[1]);
//                         if($explode2_status_lembur_1[1] == 'y'){
//                             $row2_lembur_1 = 1.5;
//                         }else{
//                             $row2_lembur_1 = 1;
//                         }
// $explode2_lembur_2 = explode("|",$row2_pengerjaan->lembur);
//                         $explode2_status_lembur_2 = explode("-",$explode2_lembur_2[2]);
//                         if($explode2_status_lembur_2[1] == 'y'){
//                             $row2_lembur_2 = 1.5;
//                         }else{
//                             $row2_lembur_2 = 1;
//                         }
// $explode2_lembur_3 = explode("|",$row2_pengerjaan->lembur);
//                         $explode2_status_lembur_3 = explode("-",$explode2_lembur_3[3]);
//                         if($explode2_status_lembur_3[1] == 'y'){
//                             $row2_lembur_3 = 1.5;
//                         }else{
//                             $row2_lembur_3 = 1;
//                         }
// $explode2_lembur_4 = explode("|",$row2_pengerjaan->lembur);
//                         $explode2_status_lembur_4 = explode("-",$explode2_lembur_4[4]);
//                         if($explode2_status_lembur_4[1] == 'y'){
//                             $row2_lembur_4 = 1.5;
//                         }else{
//                             $row2_lembur_4 = 1;
//                         }
// $explode2_lembur_5 = explode("|",$row2_pengerjaan->lembur);
//                         $explode2_status_lembur_5 = explode("-",$explode2_lembur_5[5]);
//                         if($explode2_status_lembur_5[1] == 'y'){
//                             $row2_lembur_5 = 1.5;
//                         }else{
//                             $row2_lembur_5 = 1;
//                         }
//                     }

//                     $row2_explode_hasil_kerja_2 = explode("|",$row2_pengerjaan->hasil_kerja_2);
//                     $row2_umk_borongan_lokal_2 = $this->umkBoronganLokal->select('id','jenis_produk','umk_packing','umk_bandrol','umk_inner','umk_outer')->where('id',$row2_explode_hasil_kerja_2[0])->first();
//                     if(empty($row2_umk_borongan_lokal_2)){
//                         $row2_jenis_produk_2 = '-';
//                         $row2_hasil_kerja_2 = null;
//                         $row2_data_explode_hasil_kerja_2 = '-';
//                         $row2_lembur_2 = 1;
//                     }else{
//                         $row2_jenis_produk_2 = $row2_umk_borongan_lokal_2->jenis_produk;
//                         $row2_hasil_kerja_2 = $row2_explode_hasil_kerja_2[1]*$row2_umk_borongan_lokal_2->umk_bandrol;
//                         $row2_data_explode_hasil_kerja_2 = $row2_explode_hasil_kerja_2[1];
//                         // $row2_lembur_2 = 1.5;
//                         $explode2_lembur_1 = explode("|",$row2_pengerjaan->lembur);
//                         $explode2_status_lembur_1 = explode("-",$explode2_lembur_1[1]);
//                         if($explode2_status_lembur_1[1] == 'y'){
//                             $row2_lembur_1 = 1.5;
//                         }else{
//                             $row2_lembur_1 = 1;
//                         }
// $explode2_lembur_2 = explode("|",$row2_pengerjaan->lembur);
//                         $explode2_status_lembur_2 = explode("-",$explode2_lembur_2[2]);
//                         if($explode2_status_lembur_2[1] == 'y'){
//                             $row2_lembur_2 = 1.5;
//                         }else{
//                             $row2_lembur_2 = 1;
//                         }
// $explode2_lembur_3 = explode("|",$row2_pengerjaan->lembur);
//                         $explode2_status_lembur_3 = explode("-",$explode2_lembur_3[3]);
//                         if($explode2_status_lembur_3[1] == 'y'){
//                             $row2_lembur_3 = 1.5;
//                         }else{
//                             $row2_lembur_3 = 1;
//                         }
// $explode2_lembur_4 = explode("|",$row2_pengerjaan->lembur);
//                         $explode2_status_lembur_4 = explode("-",$explode2_lembur_4[4]);
//                         if($explode2_status_lembur_4[1] == 'y'){
//                             $row2_lembur_4 = 1.5;
//                         }else{
//                             $row2_lembur_4 = 1;
//                         }
// $explode2_lembur_5 = explode("|",$row2_pengerjaan->lembur);
//                         $explode2_status_lembur_5 = explode("-",$explode2_lembur_5[5]);
//                         if($explode2_status_lembur_5[1] == 'y'){
//                             $row2_lembur_5 = 1.5;
//                         }else{
//                             $row2_lembur_5 = 1;
//                         }
//                     }

//                     $row2_explode_hasil_kerja_3 = explode("|",$row2_pengerjaan->hasil_kerja_3);
//                     $row2_umk_borongan_lokal_3 = $this->umkBoronganLokal->select('id','jenis_produk','umk_packing','umk_bandrol','umk_inner','umk_outer')->where('id',$row2_explode_hasil_kerja_3[0])->first();
//                     if(empty($row2_umk_borongan_lokal_3)){
//                         $row2_jenis_produk_3 = '-';
//                         $row2_hasil_kerja_3 = null;
//                         $row2_data_explode_hasil_kerja_3 = '-';
//                         $row2_lembur_3 = 1;
//                     }else{
//                         $row2_jenis_produk_3 = $row2_umk_borongan_lokal_3->jenis_produk;
//                         $row2_hasil_kerja_3 = $row2_explode_hasil_kerja_3[1]*$row2_umk_borongan_lokal_3->umk_bandrol;
//                         $row2_data_explode_hasil_kerja_3 = $row2_explode_hasil_kerja_3[1];
//                         // $row2_lembur_3 = 1.5;
//                         $explode2_lembur_1 = explode("|",$row2_pengerjaan->lembur);
//                         $explode2_status_lembur_1 = explode("-",$explode2_lembur_1[1]);
//                         if($explode2_status_lembur_1[1] == 'y'){
//                             $row2_lembur_1 = 1.5;
//                         }else{
//                             $row2_lembur_1 = 1;
//                         }
// $explode2_lembur_2 = explode("|",$row2_pengerjaan->lembur);
//                         $explode2_status_lembur_2 = explode("-",$explode2_lembur_2[2]);
//                         if($explode2_status_lembur_2[1] == 'y'){
//                             $row2_lembur_2 = 1.5;
//                         }else{
//                             $row2_lembur_2 = 1;
//                         }
// $explode2_lembur_3 = explode("|",$row2_pengerjaan->lembur);
//                         $explode2_status_lembur_3 = explode("-",$explode2_lembur_3[3]);
//                         if($explode2_status_lembur_3[1] == 'y'){
//                             $row2_lembur_3 = 1.5;
//                         }else{
//                             $row2_lembur_3 = 1;
//                         }
// $explode2_lembur_4 = explode("|",$row2_pengerjaan->lembur);
//                         $explode2_status_lembur_4 = explode("-",$explode2_lembur_4[4]);
//                         if($explode2_status_lembur_4[1] == 'y'){
//                             $row2_lembur_4 = 1.5;
//                         }else{
//                             $row2_lembur_4 = 1;
//                         }
// $explode2_lembur_5 = explode("|",$row2_pengerjaan->lembur);
//                         $explode2_status_lembur_5 = explode("-",$explode2_lembur_5[5]);
//                         if($explode2_status_lembur_5[1] == 'y'){
//                             $row2_lembur_5 = 1.5;
//                         }else{
//                             $row2_lembur_5 = 1;
//                         }
//                     }

//                     $row2_explode_hasil_kerja_4 = explode("|",$row2_pengerjaan->hasil_kerja_4);
//                     $row2_umk_borongan_lokal_4 = $this->umkBoronganLokal->select('id','jenis_produk','umk_packing','umk_bandrol','umk_inner','umk_outer')->where('id',$row2_explode_hasil_kerja_4[0])->first();
//                     if(empty($row2_umk_borongan_lokal_4)){
//                         $row2_jenis_produk_4 = '-';
//                         $row2_hasil_kerja_4 = null;
//                         $row2_data_explode_hasil_kerja_4 = '-';
//                         $row2_lembur_4 = 1;
//                     }else{
//                         $row2_jenis_produk_4 = $row2_umk_borongan_lokal_4->jenis_produk;
//                         $row2_hasil_kerja_4 = $row2_explode_hasil_kerja_4[1]*$row2_umk_borongan_lokal_4->umk_bandrol;
//                         $row2_data_explode_hasil_kerja_4 = $row2_explode_hasil_kerja_4[1];
//                         // $row2_lembur_4 = 1.5;
//                         $explode2_lembur_1 = explode("|",$row2_pengerjaan->lembur);
//                         $explode2_status_lembur_1 = explode("-",$explode2_lembur_1[1]);
//                         if($explode2_status_lembur_1[1] == 'y'){
//                             $row2_lembur_1 = 1.5;
//                         }else{
//                             $row2_lembur_1 = 1;
//                         }
// $explode2_lembur_2 = explode("|",$row2_pengerjaan->lembur);
//                         $explode2_status_lembur_2 = explode("-",$explode2_lembur_2[2]);
//                         if($explode2_status_lembur_2[1] == 'y'){
//                             $row2_lembur_2 = 1.5;
//                         }else{
//                             $row2_lembur_2 = 1;
//                         }
// $explode2_lembur_3 = explode("|",$row2_pengerjaan->lembur);
//                         $explode2_status_lembur_3 = explode("-",$explode2_lembur_3[3]);
//                         if($explode2_status_lembur_3[1] == 'y'){
//                             $row2_lembur_3 = 1.5;
//                         }else{
//                             $row2_lembur_3 = 1;
//                         }
// $explode2_lembur_4 = explode("|",$row2_pengerjaan->lembur);
//                         $explode2_status_lembur_4 = explode("-",$explode2_lembur_4[4]);
//                         if($explode2_status_lembur_4[1] == 'y'){
//                             $row2_lembur_4 = 1.5;
//                         }else{
//                             $row2_lembur_4 = 1;
//                         }
// $explode2_lembur_5 = explode("|",$row2_pengerjaan->lembur);
//                         $explode2_status_lembur_5 = explode("-",$explode2_lembur_5[5]);
//                         if($explode2_status_lembur_5[1] == 'y'){
//                             $row2_lembur_5 = 1.5;
//                         }else{
//                             $row2_lembur_5 = 1;
//                         }
//                     }

//                     $row2_explode_hasil_kerja_5 = explode("|",$row2_pengerjaan->hasil_kerja_5);
//                     $row2_umk_borongan_lokal_5 = $this->umkBoronganLokal->select('id','jenis_produk','umk_packing','umk_bandrol','umk_inner','umk_outer')->where('id',$row2_explode_hasil_kerja_5[0])->first();
//                     if(empty($row2_umk_borongan_lokal_5)){
//                         $row2_jenis_produk_5 = '-';
//                         $row2_hasil_kerja_5 = null;
//                         $row2_data_explode_hasil_kerja_5 = '-';
//                         $row2_lembur_5 = 1;
//                     }else{
//                         $row2_jenis_produk_5 = $row2_umk_borongan_lokal_5->jenis_produk;
//                         $row2_hasil_kerja_5 = $row2_explode_hasil_kerja_5[1]*$row2_umk_borongan_lokal_5->umk_bandrol;
//                         $row2_data_explode_hasil_kerja_5 = $row2_explode_hasil_kerja_5[1];
//                         // $row2_lembur_5 = 1.5;
//                         $explode2_lembur_1 = explode("|",$row2_pengerjaan->lembur);
//                         $explode2_status_lembur_1 = explode("-",$explode2_lembur_1[1]);
//                         if($explode2_status_lembur_1[1] == 'y'){
//                             $row2_lembur_1 = 1.5;
//                         }else{
//                             $row2_lembur_1 = 1;
//                         }
// $explode2_lembur_2 = explode("|",$row2_pengerjaan->lembur);
//                         $explode2_status_lembur_2 = explode("-",$explode2_lembur_2[2]);
//                         if($explode2_status_lembur_2[1] == 'y'){
//                             $row2_lembur_2 = 1.5;
//                         }else{
//                             $row2_lembur_2 = 1;
//                         }
// $explode2_lembur_3 = explode("|",$row2_pengerjaan->lembur);
//                         $explode2_status_lembur_3 = explode("-",$explode2_lembur_3[3]);
//                         if($explode2_status_lembur_3[1] == 'y'){
//                             $row2_lembur_3 = 1.5;
//                         }else{
//                             $row2_lembur_3 = 1;
//                         }
// $explode2_lembur_4 = explode("|",$row2_pengerjaan->lembur);
//                         $explode2_status_lembur_4 = explode("-",$explode2_lembur_4[4]);
//                         if($explode2_status_lembur_4[1] == 'y'){
//                             $row2_lembur_4 = 1.5;
//                         }else{
//                             $row2_lembur_4 = 1;
//                         }
// $explode2_lembur_5 = explode("|",$row2_pengerjaan->lembur);
//                         $explode2_status_lembur_5 = explode("-",$explode2_lembur_5[5]);
//                         if($explode2_status_lembur_5[1] == 'y'){
//                             $row2_lembur_5 = 1.5;
//                         }else{
//                             $row2_lembur_5 = 1;
//                         }
//                     }
//                 }

//                 if ($row2_pengerjaan->operator_karyawan->jenis_operator_detail_pekerjaan_id == 3) {
//                     $row2_explode_hasil_kerja_1 = explode("|",$row2_pengerjaan->hasil_kerja_1);
//                     $row2_umk_borongan_lokal_1 = $this->umkBoronganLokal->select('id','jenis_produk','umk_packing','umk_bandrol','umk_inner','umk_outer')->where('id',$row2_explode_hasil_kerja_1[0])->first();
//                     if(empty($row2_umk_borongan_lokal_1)){
//                         $row2_jenis_produk_1 = '-';
//                         $row2_hasil_kerja_1 = null;
//                         $row2_data_explode_hasil_kerja_1 = '-';
//                         $row2_lembur_1 = 1;
//                     }else{
//                         $row2_jenis_produk_1 = $row2_umk_borongan_lokal_1->jenis_produk;
//                         $row2_hasil_kerja_1 = $row2_explode_hasil_kerja_1[1]*$row2_umk_borongan_lokal_1->umk_inner;
//                         $row2_data_explode_hasil_kerja_1 = $row2_explode_hasil_kerja_1[1];
//                         // $row2_lembur_1 = 1.5;
//                         $explode2_lembur_1 = explode("|",$row2_pengerjaan->lembur);
//                         $explode2_status_lembur_1 = explode("-",$explode2_lembur_1[1]);
//                         if($explode2_status_lembur_1[1] == 'y'){
//                             $row2_lembur_1 = 1.5;
//                         }else{
//                             $row2_lembur_1 = 1;
//                         }
// $explode2_lembur_2 = explode("|",$row2_pengerjaan->lembur);
//                         $explode2_status_lembur_2 = explode("-",$explode2_lembur_2[2]);
//                         if($explode2_status_lembur_2[1] == 'y'){
//                             $row2_lembur_2 = 1.5;
//                         }else{
//                             $row2_lembur_2 = 1;
//                         }
// $explode2_lembur_3 = explode("|",$row2_pengerjaan->lembur);
//                         $explode2_status_lembur_3 = explode("-",$explode2_lembur_3[3]);
//                         if($explode2_status_lembur_3[1] == 'y'){
//                             $row2_lembur_3 = 1.5;
//                         }else{
//                             $row2_lembur_3 = 1;
//                         }
// $explode2_lembur_4 = explode("|",$row2_pengerjaan->lembur);
//                         $explode2_status_lembur_4 = explode("-",$explode2_lembur_4[4]);
//                         if($explode2_status_lembur_4[1] == 'y'){
//                             $row2_lembur_4 = 1.5;
//                         }else{
//                             $row2_lembur_4 = 1;
//                         }
// $explode2_lembur_5 = explode("|",$row2_pengerjaan->lembur);
//                         $explode2_status_lembur_5 = explode("-",$explode2_lembur_5[5]);
//                         if($explode2_status_lembur_5[1] == 'y'){
//                             $row2_lembur_5 = 1.5;
//                         }else{
//                             $row2_lembur_5 = 1;
//                         }
//                     }

//                     $row2_explode_hasil_kerja_2 = explode("|",$row2_pengerjaan->hasil_kerja_2);
//                     $row2_umk_borongan_lokal_2 = $this->umkBoronganLokal->select('id','jenis_produk','umk_packing','umk_bandrol','umk_inner','umk_outer')->where('id',$row2_explode_hasil_kerja_2[0])->first();
//                     if(empty($row2_umk_borongan_lokal_2)){
//                         $row2_jenis_produk_2 = '-';
//                         $row2_hasil_kerja_2 = null;
//                         $row2_data_explode_hasil_kerja_2 = '-';
//                         $row2_lembur_2 = 1;
//                     }else{
//                         $row2_jenis_produk_2 = $row2_umk_borongan_lokal_2->jenis_produk;
//                         $row2_hasil_kerja_2 = $row2_explode_hasil_kerja_2[1]*$row2_umk_borongan_lokal_2->umk_inner;
//                         $row2_data_explode_hasil_kerja_2 = $row2_explode_hasil_kerja_2[1];
//                         // $row2_lembur_2 = 1.5;
//                         $explode2_lembur_1 = explode("|",$row2_pengerjaan->lembur);
//                         $explode2_status_lembur_1 = explode("-",$explode2_lembur_1[1]);
//                         if($explode2_status_lembur_1[1] == 'y'){
//                             $row2_lembur_1 = 1.5;
//                         }else{
//                             $row2_lembur_1 = 1;
//                         }
// $explode2_lembur_2 = explode("|",$row2_pengerjaan->lembur);
//                         $explode2_status_lembur_2 = explode("-",$explode2_lembur_2[2]);
//                         if($explode2_status_lembur_2[1] == 'y'){
//                             $row2_lembur_2 = 1.5;
//                         }else{
//                             $row2_lembur_2 = 1;
//                         }
// $explode2_lembur_3 = explode("|",$row2_pengerjaan->lembur);
//                         $explode2_status_lembur_3 = explode("-",$explode2_lembur_3[3]);
//                         if($explode2_status_lembur_3[1] == 'y'){
//                             $row2_lembur_3 = 1.5;
//                         }else{
//                             $row2_lembur_3 = 1;
//                         }
// $explode2_lembur_4 = explode("|",$row2_pengerjaan->lembur);
//                         $explode2_status_lembur_4 = explode("-",$explode2_lembur_4[4]);
//                         if($explode2_status_lembur_4[1] == 'y'){
//                             $row2_lembur_4 = 1.5;
//                         }else{
//                             $row2_lembur_4 = 1;
//                         }
// $explode2_lembur_5 = explode("|",$row2_pengerjaan->lembur);
//                         $explode2_status_lembur_5 = explode("-",$explode2_lembur_5[5]);
//                         if($explode2_status_lembur_5[1] == 'y'){
//                             $row2_lembur_5 = 1.5;
//                         }else{
//                             $row2_lembur_5 = 1;
//                         }
//                     }

//                     $row2_explode_hasil_kerja_3 = explode("|",$row2_pengerjaan->hasil_kerja_3);
//                     $row2_umk_borongan_lokal_3 = $this->umkBoronganLokal->select('id','jenis_produk','umk_packing','umk_bandrol','umk_inner','umk_outer')->where('id',$row2_explode_hasil_kerja_3[0])->first();
//                     if(empty($row2_umk_borongan_lokal_3)){
//                         $row2_jenis_produk_3 = '-';
//                         $row2_hasil_kerja_3 = null;
//                         $row2_data_explode_hasil_kerja_3 = '-';
//                         $row2_lembur_3 = 1;
//                     }else{
//                         $row2_jenis_produk_3 = $row2_umk_borongan_lokal_3->jenis_produk;
//                         $row2_hasil_kerja_3 = $row2_explode_hasil_kerja_3[1]*$row2_umk_borongan_lokal_3->umk_inner;
//                         $row2_data_explode_hasil_kerja_3 = $row2_explode_hasil_kerja_3[1];
//                         // $row2_lembur_3 = 1.5;
//                         $explode2_lembur_1 = explode("|",$row2_pengerjaan->lembur);
//                         $explode2_status_lembur_1 = explode("-",$explode2_lembur_1[1]);
//                         if($explode2_status_lembur_1[1] == 'y'){
//                             $row2_lembur_1 = 1.5;
//                         }else{
//                             $row2_lembur_1 = 1;
//                         }
// $explode2_lembur_2 = explode("|",$row2_pengerjaan->lembur);
//                         $explode2_status_lembur_2 = explode("-",$explode2_lembur_2[2]);
//                         if($explode2_status_lembur_2[1] == 'y'){
//                             $row2_lembur_2 = 1.5;
//                         }else{
//                             $row2_lembur_2 = 1;
//                         }
// $explode2_lembur_3 = explode("|",$row2_pengerjaan->lembur);
//                         $explode2_status_lembur_3 = explode("-",$explode2_lembur_3[3]);
//                         if($explode2_status_lembur_3[1] == 'y'){
//                             $row2_lembur_3 = 1.5;
//                         }else{
//                             $row2_lembur_3 = 1;
//                         }
// $explode2_lembur_4 = explode("|",$row2_pengerjaan->lembur);
//                         $explode2_status_lembur_4 = explode("-",$explode2_lembur_4[4]);
//                         if($explode2_status_lembur_4[1] == 'y'){
//                             $row2_lembur_4 = 1.5;
//                         }else{
//                             $row2_lembur_4 = 1;
//                         }
// $explode2_lembur_5 = explode("|",$row2_pengerjaan->lembur);
//                         $explode2_status_lembur_5 = explode("-",$explode2_lembur_5[5]);
//                         if($explode2_status_lembur_5[1] == 'y'){
//                             $row2_lembur_5 = 1.5;
//                         }else{
//                             $row2_lembur_5 = 1;
//                         }
//                     }

//                     $row2_explode_hasil_kerja_4 = explode("|",$row2_pengerjaan->hasil_kerja_4);
//                     $row2_umk_borongan_lokal_4 = $this->umkBoronganLokal->select('id','jenis_produk','umk_packing','umk_bandrol','umk_inner','umk_outer')->where('id',$row2_explode_hasil_kerja_4[0])->first();
//                     if(empty($row2_umk_borongan_lokal_4)){
//                         $row2_jenis_produk_4 = '-';
//                         $row2_hasil_kerja_4 = null;
//                         $row2_data_explode_hasil_kerja_4 = '-';
//                         $row2_lembur_4 = 1;
//                     }else{
//                         $row2_jenis_produk_4 = $row2_umk_borongan_lokal_4->jenis_produk;
//                         $row2_hasil_kerja_4 = $row2_explode_hasil_kerja_4[1]*$row2_umk_borongan_lokal_4->umk_inner;
//                         $row2_data_explode_hasil_kerja_4 = $row2_explode_hasil_kerja_4[1];
//                         // $row2_lembur_4 = 1.5;
//                         $explode2_lembur_1 = explode("|",$row2_pengerjaan->lembur);
//                         $explode2_status_lembur_1 = explode("-",$explode2_lembur_1[1]);
//                         if($explode2_status_lembur_1[1] == 'y'){
//                             $row2_lembur_1 = 1.5;
//                         }else{
//                             $row2_lembur_1 = 1;
//                         }
// $explode2_lembur_2 = explode("|",$row2_pengerjaan->lembur);
//                         $explode2_status_lembur_2 = explode("-",$explode2_lembur_2[2]);
//                         if($explode2_status_lembur_2[1] == 'y'){
//                             $row2_lembur_2 = 1.5;
//                         }else{
//                             $row2_lembur_2 = 1;
//                         }
// $explode2_lembur_3 = explode("|",$row2_pengerjaan->lembur);
//                         $explode2_status_lembur_3 = explode("-",$explode2_lembur_3[3]);
//                         if($explode2_status_lembur_3[1] == 'y'){
//                             $row2_lembur_3 = 1.5;
//                         }else{
//                             $row2_lembur_3 = 1;
//                         }
// $explode2_lembur_4 = explode("|",$row2_pengerjaan->lembur);
//                         $explode2_status_lembur_4 = explode("-",$explode2_lembur_4[4]);
//                         if($explode2_status_lembur_4[1] == 'y'){
//                             $row2_lembur_4 = 1.5;
//                         }else{
//                             $row2_lembur_4 = 1;
//                         }
// $explode2_lembur_5 = explode("|",$row2_pengerjaan->lembur);
//                         $explode2_status_lembur_5 = explode("-",$explode2_lembur_5[5]);
//                         if($explode2_status_lembur_5[1] == 'y'){
//                             $row2_lembur_5 = 1.5;
//                         }else{
//                             $row2_lembur_5 = 1;
//                         }
//                     }

//                     $row2_explode_hasil_kerja_5 = explode("|",$row2_pengerjaan->hasil_kerja_5);
//                     $row2_umk_borongan_lokal_5 = $this->umkBoronganLokal->select('id','jenis_produk','umk_packing','umk_bandrol','umk_inner','umk_outer')->where('id',$row2_explode_hasil_kerja_5[0])->first();
//                     if(empty($row2_umk_borongan_lokal_5)){
//                         $row2_jenis_produk_5 = '-';
//                         $row2_hasil_kerja_5 = null;
//                         $row2_data_explode_hasil_kerja_5 = '-';
//                         $row2_lembur_5 = 1;
//                     }else{
//                         $row2_jenis_produk_5 = $row2_umk_borongan_lokal_5->jenis_produk;
//                         $row2_hasil_kerja_5 = $row2_explode_hasil_kerja_5[1]*$row2_umk_borongan_lokal_5->umk_inner;
//                         $row2_data_explode_hasil_kerja_5 = $row2_explode_hasil_kerja_5[1];
//                         // $row2_lembur_5 = 1.5;
//                         $explode2_lembur_1 = explode("|",$row2_pengerjaan->lembur);
//                         $explode2_status_lembur_1 = explode("-",$explode2_lembur_1[1]);
//                         if($explode2_status_lembur_1[1] == 'y'){
//                             $row2_lembur_1 = 1.5;
//                         }else{
//                             $row2_lembur_1 = 1;
//                         }
// $explode2_lembur_2 = explode("|",$row2_pengerjaan->lembur);
//                         $explode2_status_lembur_2 = explode("-",$explode2_lembur_2[2]);
//                         if($explode2_status_lembur_2[1] == 'y'){
//                             $row2_lembur_2 = 1.5;
//                         }else{
//                             $row2_lembur_2 = 1;
//                         }
// $explode2_lembur_3 = explode("|",$row2_pengerjaan->lembur);
//                         $explode2_status_lembur_3 = explode("-",$explode2_lembur_3[3]);
//                         if($explode2_status_lembur_3[1] == 'y'){
//                             $row2_lembur_3 = 1.5;
//                         }else{
//                             $row2_lembur_3 = 1;
//                         }
// $explode2_lembur_4 = explode("|",$row2_pengerjaan->lembur);
//                         $explode2_status_lembur_4 = explode("-",$explode2_lembur_4[4]);
//                         if($explode2_status_lembur_4[1] == 'y'){
//                             $row2_lembur_4 = 1.5;
//                         }else{
//                             $row2_lembur_4 = 1;
//                         }
// $explode2_lembur_5 = explode("|",$row2_pengerjaan->lembur);
//                         $explode2_status_lembur_5 = explode("-",$explode2_lembur_5[5]);
//                         if($explode2_status_lembur_5[1] == 'y'){
//                             $row2_lembur_5 = 1.5;
//                         }else{
//                             $row2_lembur_5 = 1;
//                         }
//                     }
//                 }

//                 if ($row2_pengerjaan->operator_karyawan->jenis_operator_detail_pekerjaan_id == 4) {
//                     $row2_explode_hasil_kerja_1 = explode("|",$row2_pengerjaan->hasil_kerja_1);
//                     $row2_umk_borongan_lokal_1 = $this->umkBoronganLokal->select('id','jenis_produk','umk_packing','umk_bandrol','umk_inner','umk_outer')->where('id',$row2_explode_hasil_kerja_1[0])->first();
//                     if(empty($row2_umk_borongan_lokal_1)){
//                         $row2_jenis_produk_1 = '-';
//                         $row2_hasil_kerja_1 = null;
//                         $row2_data_explode_hasil_kerja_1 = '-';
//                         $row2_lembur_1 = 1;
//                     }else{
//                         $row2_jenis_produk_1 = $row2_umk_borongan_lokal_1->jenis_produk;
//                         $row2_hasil_kerja_1 = $row2_explode_hasil_kerja_1[1]*$row2_umk_borongan_lokal_1->umk_outer;
//                         $row2_data_explode_hasil_kerja_1 = $row2_explode_hasil_kerja_1[1];
//                         // $row2_lembur_1 = 1.5;
//                         $explode2_lembur_1 = explode("|",$row2_pengerjaan->lembur);
//                         $explode2_status_lembur_1 = explode("-",$explode2_lembur_1[1]);
//                         if($explode2_status_lembur_1[1] == 'y'){
//                             $row2_lembur_1 = 1.5;
//                         }else{
//                             $row2_lembur_1 = 1;
//                         }
// $explode2_lembur_2 = explode("|",$row2_pengerjaan->lembur);
//                         $explode2_status_lembur_2 = explode("-",$explode2_lembur_2[2]);
//                         if($explode2_status_lembur_2[1] == 'y'){
//                             $row2_lembur_2 = 1.5;
//                         }else{
//                             $row2_lembur_2 = 1;
//                         }
// $explode2_lembur_3 = explode("|",$row2_pengerjaan->lembur);
//                         $explode2_status_lembur_3 = explode("-",$explode2_lembur_3[3]);
//                         if($explode2_status_lembur_3[1] == 'y'){
//                             $row2_lembur_3 = 1.5;
//                         }else{
//                             $row2_lembur_3 = 1;
//                         }
// $explode2_lembur_4 = explode("|",$row2_pengerjaan->lembur);
//                         $explode2_status_lembur_4 = explode("-",$explode2_lembur_4[4]);
//                         if($explode2_status_lembur_4[1] == 'y'){
//                             $row2_lembur_4 = 1.5;
//                         }else{
//                             $row2_lembur_4 = 1;
//                         }
// $explode2_lembur_5 = explode("|",$row2_pengerjaan->lembur);
//                         $explode2_status_lembur_5 = explode("-",$explode2_lembur_5[5]);
//                         if($explode2_status_lembur_5[1] == 'y'){
//                             $row2_lembur_5 = 1.5;
//                         }else{
//                             $row2_lembur_5 = 1;
//                         }
//                     }

//                     $row2_explode_hasil_kerja_2 = explode("|",$row2_pengerjaan->hasil_kerja_2);
//                     $row2_umk_borongan_lokal_2 = $this->umkBoronganLokal->select('id','jenis_produk','umk_packing','umk_bandrol','umk_inner','umk_outer')->where('id',$row2_explode_hasil_kerja_2[0])->first();
//                     if(empty($row2_umk_borongan_lokal_2)){
//                         $row2_jenis_produk_2 = '-';
//                         $row2_hasil_kerja_2 = null;
//                         $row2_data_explode_hasil_kerja_2 = '-';
//                         $row2_lembur_2 = 1;
//                     }else{
//                         $row2_jenis_produk_2 = $row2_umk_borongan_lokal_2->jenis_produk;
//                         $row2_hasil_kerja_2 = $row2_explode_hasil_kerja_2[1]*$row2_umk_borongan_lokal_2->umk_outer;
//                         $row2_data_explode_hasil_kerja_2 = $row2_explode_hasil_kerja_2[1];
//                         // $row2_lembur_2 = 1.5;
//                         $explode2_lembur_1 = explode("|",$row2_pengerjaan->lembur);
//                         $explode2_status_lembur_1 = explode("-",$explode2_lembur_1[1]);
//                         if($explode2_status_lembur_1[1] == 'y'){
//                             $row2_lembur_1 = 1.5;
//                         }else{
//                             $row2_lembur_1 = 1;
//                         }
// $explode2_lembur_2 = explode("|",$row2_pengerjaan->lembur);
//                         $explode2_status_lembur_2 = explode("-",$explode2_lembur_2[2]);
//                         if($explode2_status_lembur_2[1] == 'y'){
//                             $row2_lembur_2 = 1.5;
//                         }else{
//                             $row2_lembur_2 = 1;
//                         }
// $explode2_lembur_3 = explode("|",$row2_pengerjaan->lembur);
//                         $explode2_status_lembur_3 = explode("-",$explode2_lembur_3[3]);
//                         if($explode2_status_lembur_3[1] == 'y'){
//                             $row2_lembur_3 = 1.5;
//                         }else{
//                             $row2_lembur_3 = 1;
//                         }
// $explode2_lembur_4 = explode("|",$row2_pengerjaan->lembur);
//                         $explode2_status_lembur_4 = explode("-",$explode2_lembur_4[4]);
//                         if($explode2_status_lembur_4[1] == 'y'){
//                             $row2_lembur_4 = 1.5;
//                         }else{
//                             $row2_lembur_4 = 1;
//                         }
// $explode2_lembur_5 = explode("|",$row2_pengerjaan->lembur);
//                         $explode2_status_lembur_5 = explode("-",$explode2_lembur_5[5]);
//                         if($explode2_status_lembur_5[1] == 'y'){
//                             $row2_lembur_5 = 1.5;
//                         }else{
//                             $row2_lembur_5 = 1;
//                         }
//                     }

//                     $row2_explode_hasil_kerja_3 = explode("|",$row2_pengerjaan->hasil_kerja_3);
//                     $row2_umk_borongan_lokal_3 = $this->umkBoronganLokal->select('id','jenis_produk','umk_packing','umk_bandrol','umk_inner','umk_outer')->where('id',$row2_explode_hasil_kerja_3[0])->first();
//                     if(empty($row2_umk_borongan_lokal_3)){
//                         $row2_jenis_produk_3 = '-';
//                         $row2_hasil_kerja_3 = null;
//                         $row2_data_explode_hasil_kerja_3 = '-';
//                         $row2_lembur_3 = 1;
//                     }else{
//                         $row2_jenis_produk_3 = $row2_umk_borongan_lokal_3->jenis_produk;
//                         $row2_hasil_kerja_3 = $row2_explode_hasil_kerja_3[1]*$row2_umk_borongan_lokal_3->umk_outer;
//                         $row2_data_explode_hasil_kerja_3 = $row2_explode_hasil_kerja_3[1];
//                         // $row2_lembur_3 = 1.5;
//                         $explode2_lembur_1 = explode("|",$row2_pengerjaan->lembur);
//                         $explode2_status_lembur_1 = explode("-",$explode2_lembur_1[1]);
//                         if($explode2_status_lembur_1[1] == 'y'){
//                             $row2_lembur_1 = 1.5;
//                         }else{
//                             $row2_lembur_1 = 1;
//                         }
// $explode2_lembur_2 = explode("|",$row2_pengerjaan->lembur);
//                         $explode2_status_lembur_2 = explode("-",$explode2_lembur_2[2]);
//                         if($explode2_status_lembur_2[1] == 'y'){
//                             $row2_lembur_2 = 1.5;
//                         }else{
//                             $row2_lembur_2 = 1;
//                         }
// $explode2_lembur_3 = explode("|",$row2_pengerjaan->lembur);
//                         $explode2_status_lembur_3 = explode("-",$explode2_lembur_3[3]);
//                         if($explode2_status_lembur_3[1] == 'y'){
//                             $row2_lembur_3 = 1.5;
//                         }else{
//                             $row2_lembur_3 = 1;
//                         }
// $explode2_lembur_4 = explode("|",$row2_pengerjaan->lembur);
//                         $explode2_status_lembur_4 = explode("-",$explode2_lembur_4[4]);
//                         if($explode2_status_lembur_4[1] == 'y'){
//                             $row2_lembur_4 = 1.5;
//                         }else{
//                             $row2_lembur_4 = 1;
//                         }
// $explode2_lembur_5 = explode("|",$row2_pengerjaan->lembur);
//                         $explode2_status_lembur_5 = explode("-",$explode2_lembur_5[5]);
//                         if($explode2_status_lembur_5[1] == 'y'){
//                             $row2_lembur_5 = 1.5;
//                         }else{
//                             $row2_lembur_5 = 1;
//                         }
//                     }

//                     $row2_explode_hasil_kerja_4 = explode("|",$row2_pengerjaan->hasil_kerja_4);
//                     $row2_umk_borongan_lokal_4 = $this->umkBoronganLokal->select('id','jenis_produk','umk_packing','umk_bandrol','umk_inner','umk_outer')->where('id',$row2_explode_hasil_kerja_4[0])->first();
//                     if(empty($row2_umk_borongan_lokal_4)){
//                         $row2_jenis_produk_4 = '-';
//                         $row2_hasil_kerja_4 = null;
//                         $row2_data_explode_hasil_kerja_4 = '-';
//                         $row2_lembur_4 = 1;
//                     }else{
//                         $row2_jenis_produk_4 = $row2_umk_borongan_lokal_4->jenis_produk;
//                         $row2_hasil_kerja_4 = $row2_explode_hasil_kerja_4[1]*$row2_umk_borongan_lokal_4->umk_outer;
//                         $row2_data_explode_hasil_kerja_4 = $row2_explode_hasil_kerja_4[1];
//                         // $row2_lembur_4 = 1.5;
//                         $explode2_lembur_1 = explode("|",$row2_pengerjaan->lembur);
//                         $explode2_status_lembur_1 = explode("-",$explode2_lembur_1[1]);
//                         if($explode2_status_lembur_1[1] == 'y'){
//                             $row2_lembur_1 = 1.5;
//                         }else{
//                             $row2_lembur_1 = 1;
//                         }
// $explode2_lembur_2 = explode("|",$row2_pengerjaan->lembur);
//                         $explode2_status_lembur_2 = explode("-",$explode2_lembur_2[2]);
//                         if($explode2_status_lembur_2[1] == 'y'){
//                             $row2_lembur_2 = 1.5;
//                         }else{
//                             $row2_lembur_2 = 1;
//                         }
// $explode2_lembur_3 = explode("|",$row2_pengerjaan->lembur);
//                         $explode2_status_lembur_3 = explode("-",$explode2_lembur_3[3]);
//                         if($explode2_status_lembur_3[1] == 'y'){
//                             $row2_lembur_3 = 1.5;
//                         }else{
//                             $row2_lembur_3 = 1;
//                         }
// $explode2_lembur_4 = explode("|",$row2_pengerjaan->lembur);
//                         $explode2_status_lembur_4 = explode("-",$explode2_lembur_4[4]);
//                         if($explode2_status_lembur_4[1] == 'y'){
//                             $row2_lembur_4 = 1.5;
//                         }else{
//                             $row2_lembur_4 = 1;
//                         }
// $explode2_lembur_5 = explode("|",$row2_pengerjaan->lembur);
//                         $explode2_status_lembur_5 = explode("-",$explode2_lembur_5[5]);
//                         if($explode2_status_lembur_5[1] == 'y'){
//                             $row2_lembur_5 = 1.5;
//                         }else{
//                             $row2_lembur_5 = 1;
//                         }
//                     }

//                     $row2_explode_hasil_kerja_5 = explode("|",$row2_pengerjaan->hasil_kerja_5);
//                     $row2_umk_borongan_lokal_5 = $this->umkBoronganLokal->select('id','jenis_produk','umk_packing','umk_bandrol','umk_inner','umk_outer')->where('id',$row2_explode_hasil_kerja_5[0])->first();
//                     if(empty($row2_umk_borongan_lokal_5)){
//                         $row2_jenis_produk_5 = '-';
//                         $row2_hasil_kerja_5 = null;
//                         $row2_data_explode_hasil_kerja_5 = '-';
//                         $row2_lembur_5 = 1;
//                     }else{
//                         $row2_jenis_produk_5 = $row2_umk_borongan_lokal_5->jenis_produk;
//                         $row2_hasil_kerja_5 = $row2_explode_hasil_kerja_5[1]*$row2_umk_borongan_lokal_5->umk_outer;
//                         $row2_data_explode_hasil_kerja_5 = $row2_explode_hasil_kerja_5[1];
//                         // $row2_lembur_5 = 1.5;
//                         $explode2_lembur_1 = explode("|",$row2_pengerjaan->lembur);
//                         $explode2_status_lembur_1 = explode("-",$explode2_lembur_1[1]);
//                         if($explode2_status_lembur_1[1] == 'y'){
//                             $row2_lembur_1 = 1.5;
//                         }else{
//                             $row2_lembur_1 = 1;
//                         }
// $explode2_lembur_2 = explode("|",$row2_pengerjaan->lembur);
//                         $explode2_status_lembur_2 = explode("-",$explode2_lembur_2[2]);
//                         if($explode2_status_lembur_2[1] == 'y'){
//                             $row2_lembur_2 = 1.5;
//                         }else{
//                             $row2_lembur_2 = 1;
//                         }
// $explode2_lembur_3 = explode("|",$row2_pengerjaan->lembur);
//                         $explode2_status_lembur_3 = explode("-",$explode2_lembur_3[3]);
//                         if($explode2_status_lembur_3[1] == 'y'){
//                             $row2_lembur_3 = 1.5;
//                         }else{
//                             $row2_lembur_3 = 1;
//                         }
// $explode2_lembur_4 = explode("|",$row2_pengerjaan->lembur);
//                         $explode2_status_lembur_4 = explode("-",$explode2_lembur_4[4]);
//                         if($explode2_status_lembur_4[1] == 'y'){
//                             $row2_lembur_4 = 1.5;
//                         }else{
//                             $row2_lembur_4 = 1;
//                         }
// $explode2_lembur_5 = explode("|",$row2_pengerjaan->lembur);
//                         $explode2_status_lembur_5 = explode("-",$explode2_lembur_5[5]);
//                         if($explode2_status_lembur_5[1] == 'y'){
//                             $row2_lembur_5 = 1.5;
//                         }else{
//                             $row2_lembur_5 = 1;
//                         }
//                     }
//                 }
//                 //End Lokal

//                 //Begin Ekspor
//                 if ($row2_pengerjaan->operator_karyawan->jenis_operator_detail_pekerjaan_id == 5) {
//                     $row2_explode_hasil_kerja_1 = explode("|",$row2_pengerjaan->hasil_kerja_1);
//                     $row2_umk_borongan_lokal_1 = $this->umkBoronganEkspor->select('id','jenis_produk','umk_packing','umk_kemas','umk_pilih_gagang')->where('id',$row2_explode_hasil_kerja_1[0])->first();
//                     if(empty($row2_umk_borongan_lokal_1)){
//                         $row2_jenis_produk_1 = '-';
//                         $row2_hasil_kerja_1 = null;
//                         $row2_data_explode_hasil_kerja_1 = '-';
//                         $row2_lembur_1 = 1;
//                     }else{
//                         $row2_jenis_produk_1 = $row2_umk_borongan_lokal_1->jenis_produk;
//                         $row2_hasil_kerja_1 = $row2_explode_hasil_kerja_1[1]*$row2_umk_borongan_lokal_1->umk_packing;
//                         $row2_data_explode_hasil_kerja_1 = $row2_explode_hasil_kerja_1[1];
//                         // $row2_lembur_1 = 1.5;
//                         $explode2_lembur_1 = explode("|",$row2_pengerjaan->lembur);
//                         $explode2_status_lembur_1 = explode("-",$explode2_lembur_1[1]);
//                         if($explode2_status_lembur_1[1] == 'y'){
//                             $row2_lembur_1 = 1.5;
//                         }else{
//                             $row2_lembur_1 = 1;
//                         }
// $explode2_lembur_2 = explode("|",$row2_pengerjaan->lembur);
//                         $explode2_status_lembur_2 = explode("-",$explode2_lembur_2[2]);
//                         if($explode2_status_lembur_2[1] == 'y'){
//                             $row2_lembur_2 = 1.5;
//                         }else{
//                             $row2_lembur_2 = 1;
//                         }
// $explode2_lembur_3 = explode("|",$row2_pengerjaan->lembur);
//                         $explode2_status_lembur_3 = explode("-",$explode2_lembur_3[3]);
//                         if($explode2_status_lembur_3[1] == 'y'){
//                             $row2_lembur_3 = 1.5;
//                         }else{
//                             $row2_lembur_3 = 1;
//                         }
// $explode2_lembur_4 = explode("|",$row2_pengerjaan->lembur);
//                         $explode2_status_lembur_4 = explode("-",$explode2_lembur_4[4]);
//                         if($explode2_status_lembur_4[1] == 'y'){
//                             $row2_lembur_4 = 1.5;
//                         }else{
//                             $row2_lembur_4 = 1;
//                         }
// $explode2_lembur_5 = explode("|",$row2_pengerjaan->lembur);
//                         $explode2_status_lembur_5 = explode("-",$explode2_lembur_5[5]);
//                         if($explode2_status_lembur_5[1] == 'y'){
//                             $row2_lembur_5 = 1.5;
//                         }else{
//                             $row2_lembur_5 = 1;
//                         }
//                     }

//                     $row2_explode_hasil_kerja_2 = explode("|",$row2_pengerjaan->hasil_kerja_2);
//                     $row2_umk_borongan_lokal_2 = $this->umkBoronganEkspor->select('id','jenis_produk','umk_packing','umk_kemas','umk_pilih_gagang')->where('id',$row2_explode_hasil_kerja_2[0])->first();
//                     if(empty($row2_umk_borongan_lokal_2)){
//                         $row2_jenis_produk_2 = '-';
//                         $row2_hasil_kerja_2 = null;
//                         $row2_data_explode_hasil_kerja_2 = '-';
//                         $row2_lembur_2 = 1;
//                     }else{
//                         $row2_jenis_produk_2 = $row2_umk_borongan_lokal_2->jenis_produk;
//                         $row2_hasil_kerja_2 = $row2_explode_hasil_kerja_2[1]*$row2_umk_borongan_lokal_2->umk_packing;
//                         $row2_data_explode_hasil_kerja_2 = $row2_explode_hasil_kerja_2[1];
//                         // $row2_lembur_2 = 1.5;
//                         $explode2_lembur_1 = explode("|",$row2_pengerjaan->lembur);
//                         $explode2_status_lembur_1 = explode("-",$explode2_lembur_1[1]);
//                         if($explode2_status_lembur_1[1] == 'y'){
//                             $row2_lembur_1 = 1.5;
//                         }else{
//                             $row2_lembur_1 = 1;
//                         }
// $explode2_lembur_2 = explode("|",$row2_pengerjaan->lembur);
//                         $explode2_status_lembur_2 = explode("-",$explode2_lembur_2[2]);
//                         if($explode2_status_lembur_2[1] == 'y'){
//                             $row2_lembur_2 = 1.5;
//                         }else{
//                             $row2_lembur_2 = 1;
//                         }
// $explode2_lembur_3 = explode("|",$row2_pengerjaan->lembur);
//                         $explode2_status_lembur_3 = explode("-",$explode2_lembur_3[3]);
//                         if($explode2_status_lembur_3[1] == 'y'){
//                             $row2_lembur_3 = 1.5;
//                         }else{
//                             $row2_lembur_3 = 1;
//                         }
// $explode2_lembur_4 = explode("|",$row2_pengerjaan->lembur);
//                         $explode2_status_lembur_4 = explode("-",$explode2_lembur_4[4]);
//                         if($explode2_status_lembur_4[1] == 'y'){
//                             $row2_lembur_4 = 1.5;
//                         }else{
//                             $row2_lembur_4 = 1;
//                         }
// $explode2_lembur_5 = explode("|",$row2_pengerjaan->lembur);
//                         $explode2_status_lembur_5 = explode("-",$explode2_lembur_5[5]);
//                         if($explode2_status_lembur_5[1] == 'y'){
//                             $row2_lembur_5 = 1.5;
//                         }else{
//                             $row2_lembur_5 = 1;
//                         }
//                     }

//                     $row2_explode_hasil_kerja_3 = explode("|",$row2_pengerjaan->hasil_kerja_3);
//                     $row2_umk_borongan_lokal_3 = $this->umkBoronganEkspor->select('id','jenis_produk','umk_packing','umk_kemas','umk_pilih_gagang')->where('id',$row2_explode_hasil_kerja_3[0])->first();
//                     if(empty($row2_umk_borongan_lokal_3)){
//                         $row2_jenis_produk_3 = '-';
//                         $row2_hasil_kerja_3 = null;
//                         $row2_data_explode_hasil_kerja_3 = '-';
//                         $row2_lembur_3 = 1;
//                     }else{
//                         $row2_jenis_produk_3 = $row2_umk_borongan_lokal_3->jenis_produk;
//                         $row2_hasil_kerja_3 = $row2_explode_hasil_kerja_3[1]*$row2_umk_borongan_lokal_3->umk_packing;
//                         $row2_data_explode_hasil_kerja_3 = $row2_explode_hasil_kerja_3[1];
//                         // $row2_lembur_3 = 1.5;
//                         $explode2_lembur_1 = explode("|",$row2_pengerjaan->lembur);
//                         $explode2_status_lembur_1 = explode("-",$explode2_lembur_1[1]);
//                         if($explode2_status_lembur_1[1] == 'y'){
//                             $row2_lembur_1 = 1.5;
//                         }else{
//                             $row2_lembur_1 = 1;
//                         }
// $explode2_lembur_2 = explode("|",$row2_pengerjaan->lembur);
//                         $explode2_status_lembur_2 = explode("-",$explode2_lembur_2[2]);
//                         if($explode2_status_lembur_2[1] == 'y'){
//                             $row2_lembur_2 = 1.5;
//                         }else{
//                             $row2_lembur_2 = 1;
//                         }
// $explode2_lembur_3 = explode("|",$row2_pengerjaan->lembur);
//                         $explode2_status_lembur_3 = explode("-",$explode2_lembur_3[3]);
//                         if($explode2_status_lembur_3[1] == 'y'){
//                             $row2_lembur_3 = 1.5;
//                         }else{
//                             $row2_lembur_3 = 1;
//                         }
// $explode2_lembur_4 = explode("|",$row2_pengerjaan->lembur);
//                         $explode2_status_lembur_4 = explode("-",$explode2_lembur_4[4]);
//                         if($explode2_status_lembur_4[1] == 'y'){
//                             $row2_lembur_4 = 1.5;
//                         }else{
//                             $row2_lembur_4 = 1;
//                         }
// $explode2_lembur_5 = explode("|",$row2_pengerjaan->lembur);
//                         $explode2_status_lembur_5 = explode("-",$explode2_lembur_5[5]);
//                         if($explode2_status_lembur_5[1] == 'y'){
//                             $row2_lembur_5 = 1.5;
//                         }else{
//                             $row2_lembur_5 = 1;
//                         }
//                     }

//                     $row2_explode_hasil_kerja_4 = explode("|",$row2_pengerjaan->hasil_kerja_4);
//                     $row2_umk_borongan_lokal_4 = $this->umkBoronganEkspor->select('id','jenis_produk','umk_packing','umk_kemas','umk_pilih_gagang')->where('id',$row2_explode_hasil_kerja_4[0])->first();
//                     if(empty($row2_umk_borongan_lokal_4)){
//                         $row2_jenis_produk_4 = '-';
//                         $row2_hasil_kerja_4 = null;
//                         $row2_data_explode_hasil_kerja_4 = '-';
//                         $row2_lembur_4 = 1;
//                     }else{
//                         $row2_jenis_produk_4 = $row2_umk_borongan_lokal_4->jenis_produk;
//                         $row2_hasil_kerja_4 = $row2_explode_hasil_kerja_4[1]*$row2_umk_borongan_lokal_4->umk_packing;
//                         $row2_data_explode_hasil_kerja_4 = $row2_explode_hasil_kerja_4[1];
//                         // $row2_lembur_4 = 1.5;
//                         $explode2_lembur_1 = explode("|",$row2_pengerjaan->lembur);
//                         $explode2_status_lembur_1 = explode("-",$explode2_lembur_1[1]);
//                         if($explode2_status_lembur_1[1] == 'y'){
//                             $row2_lembur_1 = 1.5;
//                         }else{
//                             $row2_lembur_1 = 1;
//                         }
// $explode2_lembur_2 = explode("|",$row2_pengerjaan->lembur);
//                         $explode2_status_lembur_2 = explode("-",$explode2_lembur_2[2]);
//                         if($explode2_status_lembur_2[1] == 'y'){
//                             $row2_lembur_2 = 1.5;
//                         }else{
//                             $row2_lembur_2 = 1;
//                         }
// $explode2_lembur_3 = explode("|",$row2_pengerjaan->lembur);
//                         $explode2_status_lembur_3 = explode("-",$explode2_lembur_3[3]);
//                         if($explode2_status_lembur_3[1] == 'y'){
//                             $row2_lembur_3 = 1.5;
//                         }else{
//                             $row2_lembur_3 = 1;
//                         }
// $explode2_lembur_4 = explode("|",$row2_pengerjaan->lembur);
//                         $explode2_status_lembur_4 = explode("-",$explode2_lembur_4[4]);
//                         if($explode2_status_lembur_4[1] == 'y'){
//                             $row2_lembur_4 = 1.5;
//                         }else{
//                             $row2_lembur_4 = 1;
//                         }
// $explode2_lembur_5 = explode("|",$row2_pengerjaan->lembur);
//                         $explode2_status_lembur_5 = explode("-",$explode2_lembur_5[5]);
//                         if($explode2_status_lembur_5[1] == 'y'){
//                             $row2_lembur_5 = 1.5;
//                         }else{
//                             $row2_lembur_5 = 1;
//                         }
//                     }

//                     $row2_explode_hasil_kerja_5 = explode("|",$row2_pengerjaan->hasil_kerja_5);
//                     $row2_umk_borongan_lokal_5 = $this->umkBoronganEkspor->select('id','jenis_produk','umk_packing','umk_kemas','umk_pilih_gagang')->where('id',$row2_explode_hasil_kerja_5[0])->first();
//                     if(empty($row2_umk_borongan_lokal_5)){
//                         $row2_jenis_produk_5 = '-';
//                         $row2_hasil_kerja_5 = null;
//                         $row2_data_explode_hasil_kerja_5 = '-';
//                         $row2_lembur_5 = 1;
//                     }else{
//                         $row2_jenis_produk_5 = $row2_umk_borongan_lokal_5->jenis_produk;
//                         $row2_hasil_kerja_5 = $row2_explode_hasil_kerja_5[1]*$row2_umk_borongan_lokal_5->umk_packing;
//                         $row2_data_explode_hasil_kerja_5 = $row2_explode_hasil_kerja_5[1];
//                         // $row2_lembur_5 = 1.5;
//                         $explode2_lembur_1 = explode("|",$row2_pengerjaan->lembur);
//                         $explode2_status_lembur_1 = explode("-",$explode2_lembur_1[1]);
//                         if($explode2_status_lembur_1[1] == 'y'){
//                             $row2_lembur_1 = 1.5;
//                         }else{
//                             $row2_lembur_1 = 1;
//                         }
// $explode2_lembur_2 = explode("|",$row2_pengerjaan->lembur);
//                         $explode2_status_lembur_2 = explode("-",$explode2_lembur_2[2]);
//                         if($explode2_status_lembur_2[1] == 'y'){
//                             $row2_lembur_2 = 1.5;
//                         }else{
//                             $row2_lembur_2 = 1;
//                         }
// $explode2_lembur_3 = explode("|",$row2_pengerjaan->lembur);
//                         $explode2_status_lembur_3 = explode("-",$explode2_lembur_3[3]);
//                         if($explode2_status_lembur_3[1] == 'y'){
//                             $row2_lembur_3 = 1.5;
//                         }else{
//                             $row2_lembur_3 = 1;
//                         }
// $explode2_lembur_4 = explode("|",$row2_pengerjaan->lembur);
//                         $explode2_status_lembur_4 = explode("-",$explode2_lembur_4[4]);
//                         if($explode2_status_lembur_4[1] == 'y'){
//                             $row2_lembur_4 = 1.5;
//                         }else{
//                             $row2_lembur_4 = 1;
//                         }
// $explode2_lembur_5 = explode("|",$row2_pengerjaan->lembur);
//                         $explode2_status_lembur_5 = explode("-",$explode2_lembur_5[5]);
//                         if($explode2_status_lembur_5[1] == 'y'){
//                             $row2_lembur_5 = 1.5;
//                         }else{
//                             $row2_lembur_5 = 1;
//                         }
//                     }
//                 }

//                 if ($row2_pengerjaan->operator_karyawan->jenis_operator_detail_pekerjaan_id == 6) {
//                     $row2_explode_hasil_kerja_1 = explode("|",$row2_pengerjaan->hasil_kerja_1);
//                     $row2_umk_borongan_lokal_1 = $this->umkBoronganEkspor->select('id','jenis_produk','umk_packing','umk_kemas','umk_pilih_gagang')->where('id',$row2_explode_hasil_kerja_1[0])->first();
//                     if(empty($row2_umk_borongan_lokal_1)){
//                         $row2_jenis_produk_1 = '-';
//                         $row2_hasil_kerja_1 = null;
//                         $row2_data_explode_hasil_kerja_1 = '-';
//                         $row2_lembur_1 = 1;
//                     }else{
//                         $row2_jenis_produk_1 = $row2_umk_borongan_lokal_1->jenis_produk;
//                         $row2_hasil_kerja_1 = $row2_explode_hasil_kerja_1[1]*$row2_umk_borongan_lokal_1->umk_kemas;
//                         $row2_data_explode_hasil_kerja_1 = $row2_explode_hasil_kerja_1[1];
//                         // $row2_lembur_1 = 1.5;
//                         $explode2_lembur_1 = explode("|",$row2_pengerjaan->lembur);
//                         $explode2_status_lembur_1 = explode("-",$explode2_lembur_1[1]);
//                         if($explode2_status_lembur_1[1] == 'y'){
//                             $row2_lembur_1 = 1.5;
//                         }else{
//                             $row2_lembur_1 = 1;
//                         }
// $explode2_lembur_2 = explode("|",$row2_pengerjaan->lembur);
//                         $explode2_status_lembur_2 = explode("-",$explode2_lembur_2[2]);
//                         if($explode2_status_lembur_2[1] == 'y'){
//                             $row2_lembur_2 = 1.5;
//                         }else{
//                             $row2_lembur_2 = 1;
//                         }
// $explode2_lembur_3 = explode("|",$row2_pengerjaan->lembur);
//                         $explode2_status_lembur_3 = explode("-",$explode2_lembur_3[3]);
//                         if($explode2_status_lembur_3[1] == 'y'){
//                             $row2_lembur_3 = 1.5;
//                         }else{
//                             $row2_lembur_3 = 1;
//                         }
// $explode2_lembur_4 = explode("|",$row2_pengerjaan->lembur);
//                         $explode2_status_lembur_4 = explode("-",$explode2_lembur_4[4]);
//                         if($explode2_status_lembur_4[1] == 'y'){
//                             $row2_lembur_4 = 1.5;
//                         }else{
//                             $row2_lembur_4 = 1;
//                         }
// $explode2_lembur_5 = explode("|",$row2_pengerjaan->lembur);
//                         $explode2_status_lembur_5 = explode("-",$explode2_lembur_5[5]);
//                         if($explode2_status_lembur_5[1] == 'y'){
//                             $row2_lembur_5 = 1.5;
//                         }else{
//                             $row2_lembur_5 = 1;
//                         }
//                     }

//                     $row2_explode_hasil_kerja_2 = explode("|",$row2_pengerjaan->hasil_kerja_2);
//                     $row2_umk_borongan_lokal_2 = $this->umkBoronganEkspor->select('id','jenis_produk','umk_packing','umk_kemas','umk_pilih_gagang')->where('id',$row2_explode_hasil_kerja_2[0])->first();
//                     if(empty($row2_umk_borongan_lokal_2)){
//                         $row2_jenis_produk_2 = '-';
//                         $row2_hasil_kerja_2 = null;
//                         $row2_data_explode_hasil_kerja_2 = '-';
//                         $row2_lembur_2 = 1;
//                     }else{
//                         $row2_jenis_produk_2 = $row2_umk_borongan_lokal_2->jenis_produk;
//                         $row2_hasil_kerja_2 = $row2_explode_hasil_kerja_2[1]*$row2_umk_borongan_lokal_2->umk_kemas;
//                         $row2_data_explode_hasil_kerja_2 = $row2_explode_hasil_kerja_2[1];
//                         // $row2_lembur_2 = 1.5;
//                         $explode2_lembur_1 = explode("|",$row2_pengerjaan->lembur);
//                         $explode2_status_lembur_1 = explode("-",$explode2_lembur_1[1]);
//                         if($explode2_status_lembur_1[1] == 'y'){
//                             $row2_lembur_1 = 1.5;
//                         }else{
//                             $row2_lembur_1 = 1;
//                         }
// $explode2_lembur_2 = explode("|",$row2_pengerjaan->lembur);
//                         $explode2_status_lembur_2 = explode("-",$explode2_lembur_2[2]);
//                         if($explode2_status_lembur_2[1] == 'y'){
//                             $row2_lembur_2 = 1.5;
//                         }else{
//                             $row2_lembur_2 = 1;
//                         }
// $explode2_lembur_3 = explode("|",$row2_pengerjaan->lembur);
//                         $explode2_status_lembur_3 = explode("-",$explode2_lembur_3[3]);
//                         if($explode2_status_lembur_3[1] == 'y'){
//                             $row2_lembur_3 = 1.5;
//                         }else{
//                             $row2_lembur_3 = 1;
//                         }
// $explode2_lembur_4 = explode("|",$row2_pengerjaan->lembur);
//                         $explode2_status_lembur_4 = explode("-",$explode2_lembur_4[4]);
//                         if($explode2_status_lembur_4[1] == 'y'){
//                             $row2_lembur_4 = 1.5;
//                         }else{
//                             $row2_lembur_4 = 1;
//                         }
// $explode2_lembur_5 = explode("|",$row2_pengerjaan->lembur);
//                         $explode2_status_lembur_5 = explode("-",$explode2_lembur_5[5]);
//                         if($explode2_status_lembur_5[1] == 'y'){
//                             $row2_lembur_5 = 1.5;
//                         }else{
//                             $row2_lembur_5 = 1;
//                         }
//                     }

//                     $row2_explode_hasil_kerja_3 = explode("|",$row2_pengerjaan->hasil_kerja_3);
//                     $row2_umk_borongan_lokal_3 = $this->umkBoronganEkspor->select('id','jenis_produk','umk_packing','umk_kemas','umk_pilih_gagang')->where('id',$row2_explode_hasil_kerja_3[0])->first();
//                     if(empty($row2_umk_borongan_lokal_3)){
//                         $row2_jenis_produk_3 = '-';
//                         $row2_hasil_kerja_3 = null;
//                         $row2_data_explode_hasil_kerja_3 = '-';
//                         $row2_lembur_3 = 1;
//                     }else{
//                         $row2_jenis_produk_3 = $row2_umk_borongan_lokal_3->jenis_produk;
//                         $row2_hasil_kerja_3 = $row2_explode_hasil_kerja_3[1]*$row2_umk_borongan_lokal_3->umk_kemas;
//                         $row2_data_explode_hasil_kerja_3 = $row2_explode_hasil_kerja_3[1];
//                         // $row2_lembur_3 = 1.5;
//                         $explode2_lembur_1 = explode("|",$row2_pengerjaan->lembur);
//                         $explode2_status_lembur_1 = explode("-",$explode2_lembur_1[1]);
//                         if($explode2_status_lembur_1[1] == 'y'){
//                             $row2_lembur_1 = 1.5;
//                         }else{
//                             $row2_lembur_1 = 1;
//                         }
// $explode2_lembur_2 = explode("|",$row2_pengerjaan->lembur);
//                         $explode2_status_lembur_2 = explode("-",$explode2_lembur_2[2]);
//                         if($explode2_status_lembur_2[1] == 'y'){
//                             $row2_lembur_2 = 1.5;
//                         }else{
//                             $row2_lembur_2 = 1;
//                         }
// $explode2_lembur_3 = explode("|",$row2_pengerjaan->lembur);
//                         $explode2_status_lembur_3 = explode("-",$explode2_lembur_3[3]);
//                         if($explode2_status_lembur_3[1] == 'y'){
//                             $row2_lembur_3 = 1.5;
//                         }else{
//                             $row2_lembur_3 = 1;
//                         }
// $explode2_lembur_4 = explode("|",$row2_pengerjaan->lembur);
//                         $explode2_status_lembur_4 = explode("-",$explode2_lembur_4[4]);
//                         if($explode2_status_lembur_4[1] == 'y'){
//                             $row2_lembur_4 = 1.5;
//                         }else{
//                             $row2_lembur_4 = 1;
//                         }
// $explode2_lembur_5 = explode("|",$row2_pengerjaan->lembur);
//                         $explode2_status_lembur_5 = explode("-",$explode2_lembur_5[5]);
//                         if($explode2_status_lembur_5[1] == 'y'){
//                             $row2_lembur_5 = 1.5;
//                         }else{
//                             $row2_lembur_5 = 1;
//                         }
//                     }

//                     $row2_explode_hasil_kerja_4 = explode("|",$row2_pengerjaan->hasil_kerja_4);
//                     $row2_umk_borongan_lokal_4 = $this->umkBoronganEkspor->select('id','jenis_produk','umk_packing','umk_kemas','umk_pilih_gagang')->where('id',$row2_explode_hasil_kerja_4[0])->first();
//                     if(empty($row2_umk_borongan_lokal_4)){
//                         $row2_jenis_produk_4 = '-';
//                         $row2_hasil_kerja_4 = null;
//                         $row2_data_explode_hasil_kerja_4 = '-';
//                         $row2_lembur_4 = 1;
//                     }else{
//                         $row2_jenis_produk_4 = $row2_umk_borongan_lokal_4->jenis_produk;
//                         $row2_hasil_kerja_4 = $row2_explode_hasil_kerja_4[1]*$row2_umk_borongan_lokal_4->umk_kemas;
//                         $row2_data_explode_hasil_kerja_4 = $row2_explode_hasil_kerja_4[1];
//                         // $row2_lembur_4 = 1.5;
//                         $explode2_lembur_1 = explode("|",$row2_pengerjaan->lembur);
//                         $explode2_status_lembur_1 = explode("-",$explode2_lembur_1[1]);
//                         if($explode2_status_lembur_1[1] == 'y'){
//                             $row2_lembur_1 = 1.5;
//                         }else{
//                             $row2_lembur_1 = 1;
//                         }
// $explode2_lembur_2 = explode("|",$row2_pengerjaan->lembur);
//                         $explode2_status_lembur_2 = explode("-",$explode2_lembur_2[2]);
//                         if($explode2_status_lembur_2[1] == 'y'){
//                             $row2_lembur_2 = 1.5;
//                         }else{
//                             $row2_lembur_2 = 1;
//                         }
// $explode2_lembur_3 = explode("|",$row2_pengerjaan->lembur);
//                         $explode2_status_lembur_3 = explode("-",$explode2_lembur_3[3]);
//                         if($explode2_status_lembur_3[1] == 'y'){
//                             $row2_lembur_3 = 1.5;
//                         }else{
//                             $row2_lembur_3 = 1;
//                         }
// $explode2_lembur_4 = explode("|",$row2_pengerjaan->lembur);
//                         $explode2_status_lembur_4 = explode("-",$explode2_lembur_4[4]);
//                         if($explode2_status_lembur_4[1] == 'y'){
//                             $row2_lembur_4 = 1.5;
//                         }else{
//                             $row2_lembur_4 = 1;
//                         }
// $explode2_lembur_5 = explode("|",$row2_pengerjaan->lembur);
//                         $explode2_status_lembur_5 = explode("-",$explode2_lembur_5[5]);
//                         if($explode2_status_lembur_5[1] == 'y'){
//                             $row2_lembur_5 = 1.5;
//                         }else{
//                             $row2_lembur_5 = 1;
//                         }
//                     }

//                     $row2_explode_hasil_kerja_5 = explode("|",$row2_pengerjaan->hasil_kerja_5);
//                     $row2_umk_borongan_lokal_5 = $this->umkBoronganEkspor->select('id','jenis_produk','umk_packing','umk_kemas','umk_pilih_gagang')->where('id',$row2_explode_hasil_kerja_5[0])->first();
//                     if(empty($row2_umk_borongan_lokal_5)){
//                         $row2_jenis_produk_5 = '-';
//                         $row2_hasil_kerja_5 = null;
//                         $row2_data_explode_hasil_kerja_5 = '-';
//                         $row2_lembur_5 = 1;
//                     }else{
//                         $row2_jenis_produk_5 = $row2_umk_borongan_lokal_5->jenis_produk;
//                         $row2_hasil_kerja_5 = $row2_explode_hasil_kerja_5[1]*$row2_umk_borongan_lokal_5->umk_kemas;
//                         $row2_data_explode_hasil_kerja_5 = $row2_explode_hasil_kerja_5[1];
//                         // $row2_lembur_5 = 1.5;
//                         $explode2_lembur_1 = explode("|",$row2_pengerjaan->lembur);
//                         $explode2_status_lembur_1 = explode("-",$explode2_lembur_1[1]);
//                         if($explode2_status_lembur_1[1] == 'y'){
//                             $row2_lembur_1 = 1.5;
//                         }else{
//                             $row2_lembur_1 = 1;
//                         }
// $explode2_lembur_2 = explode("|",$row2_pengerjaan->lembur);
//                         $explode2_status_lembur_2 = explode("-",$explode2_lembur_2[2]);
//                         if($explode2_status_lembur_2[1] == 'y'){
//                             $row2_lembur_2 = 1.5;
//                         }else{
//                             $row2_lembur_2 = 1;
//                         }
// $explode2_lembur_3 = explode("|",$row2_pengerjaan->lembur);
//                         $explode2_status_lembur_3 = explode("-",$explode2_lembur_3[3]);
//                         if($explode2_status_lembur_3[1] == 'y'){
//                             $row2_lembur_3 = 1.5;
//                         }else{
//                             $row2_lembur_3 = 1;
//                         }
// $explode2_lembur_4 = explode("|",$row2_pengerjaan->lembur);
//                         $explode2_status_lembur_4 = explode("-",$explode2_lembur_4[4]);
//                         if($explode2_status_lembur_4[1] == 'y'){
//                             $row2_lembur_4 = 1.5;
//                         }else{
//                             $row2_lembur_4 = 1;
//                         }
// $explode2_lembur_5 = explode("|",$row2_pengerjaan->lembur);
//                         $explode2_status_lembur_5 = explode("-",$explode2_lembur_5[5]);
//                         if($explode2_status_lembur_5[1] == 'y'){
//                             $row2_lembur_5 = 1.5;
//                         }else{
//                             $row2_lembur_5 = 1;
//                         }
//                     }
//                 }

//                 if ($row2_pengerjaan->operator_karyawan->jenis_operator_detail_pekerjaan_id == 7) {
//                     $row2_explode_hasil_kerja_1 = explode("|",$row2_pengerjaan->hasil_kerja_1);
//                     $row2_umk_borongan_lokal_1 = $this->umkBoronganEkspor->select('id','jenis_produk','umk_packing','umk_kemas','umk_pilih_gagang')->where('id',$row2_explode_hasil_kerja_1[0])->first();
//                     if(empty($row2_umk_borongan_lokal_1)){
//                         $row2_jenis_produk_1 = '-';
//                         $row2_hasil_kerja_1 = null;
//                         $row2_data_explode_hasil_kerja_1 = '-';
//                         $row2_lembur_1 = 1;
//                     }else{
//                         $row2_jenis_produk_1 = $row2_umk_borongan_lokal_1->jenis_produk;
//                         $row2_hasil_kerja_1 = $row2_explode_hasil_kerja_1[1]*$row2_umk_borongan_lokal_1->umk_pilih_gagang;
//                         $row2_data_explode_hasil_kerja_1 = $row2_explode_hasil_kerja_1[1];
//                         // $row2_lembur_1 = 1.5;
//                         $explode2_lembur_1 = explode("|",$row2_pengerjaan->lembur);
//                         $explode2_status_lembur_1 = explode("-",$explode2_lembur_1[1]);
//                         if($explode2_status_lembur_1[1] == 'y'){
//                             $row2_lembur_1 = 1.5;
//                         }else{
//                             $row2_lembur_1 = 1;
//                         }
// $explode2_lembur_2 = explode("|",$row2_pengerjaan->lembur);
//                         $explode2_status_lembur_2 = explode("-",$explode2_lembur_2[2]);
//                         if($explode2_status_lembur_2[1] == 'y'){
//                             $row2_lembur_2 = 1.5;
//                         }else{
//                             $row2_lembur_2 = 1;
//                         }
// $explode2_lembur_3 = explode("|",$row2_pengerjaan->lembur);
//                         $explode2_status_lembur_3 = explode("-",$explode2_lembur_3[3]);
//                         if($explode2_status_lembur_3[1] == 'y'){
//                             $row2_lembur_3 = 1.5;
//                         }else{
//                             $row2_lembur_3 = 1;
//                         }
// $explode2_lembur_4 = explode("|",$row2_pengerjaan->lembur);
//                         $explode2_status_lembur_4 = explode("-",$explode2_lembur_4[4]);
//                         if($explode2_status_lembur_4[1] == 'y'){
//                             $row2_lembur_4 = 1.5;
//                         }else{
//                             $row2_lembur_4 = 1;
//                         }
// $explode2_lembur_5 = explode("|",$row2_pengerjaan->lembur);
//                         $explode2_status_lembur_5 = explode("-",$explode2_lembur_5[5]);
//                         if($explode2_status_lembur_5[1] == 'y'){
//                             $row2_lembur_5 = 1.5;
//                         }else{
//                             $row2_lembur_5 = 1;
//                         }
//                     }

//                     $row2_explode_hasil_kerja_2 = explode("|",$row2_pengerjaan->hasil_kerja_2);
//                     $row2_umk_borongan_lokal_2 = $this->umkBoronganEkspor->select('id','jenis_produk','umk_packing','umk_kemas','umk_pilih_gagang')->where('id',$row2_explode_hasil_kerja_2[0])->first();
//                     if(empty($row2_umk_borongan_lokal_2)){
//                         $row2_jenis_produk_2 = '-';
//                         $row2_hasil_kerja_2 = null;
//                         $row2_data_explode_hasil_kerja_2 = '-';
//                         $row2_lembur_2 = 1;
//                     }else{
//                         $row2_jenis_produk_2 = $row2_umk_borongan_lokal_2->jenis_produk;
//                         $row2_hasil_kerja_2 = $row2_explode_hasil_kerja_2[1]*$row2_umk_borongan_lokal_2->umk_pilih_gagang;
//                         $row2_data_explode_hasil_kerja_2 = $row2_explode_hasil_kerja_2[1];
//                         // $row2_lembur_2 = 1.5;
//                         $explode2_lembur_1 = explode("|",$row2_pengerjaan->lembur);
//                         $explode2_status_lembur_1 = explode("-",$explode2_lembur_1[1]);
//                         if($explode2_status_lembur_1[1] == 'y'){
//                             $row2_lembur_1 = 1.5;
//                         }else{
//                             $row2_lembur_1 = 1;
//                         }
// $explode2_lembur_2 = explode("|",$row2_pengerjaan->lembur);
//                         $explode2_status_lembur_2 = explode("-",$explode2_lembur_2[2]);
//                         if($explode2_status_lembur_2[1] == 'y'){
//                             $row2_lembur_2 = 1.5;
//                         }else{
//                             $row2_lembur_2 = 1;
//                         }
// $explode2_lembur_3 = explode("|",$row2_pengerjaan->lembur);
//                         $explode2_status_lembur_3 = explode("-",$explode2_lembur_3[3]);
//                         if($explode2_status_lembur_3[1] == 'y'){
//                             $row2_lembur_3 = 1.5;
//                         }else{
//                             $row2_lembur_3 = 1;
//                         }
// $explode2_lembur_4 = explode("|",$row2_pengerjaan->lembur);
//                         $explode2_status_lembur_4 = explode("-",$explode2_lembur_4[4]);
//                         if($explode2_status_lembur_4[1] == 'y'){
//                             $row2_lembur_4 = 1.5;
//                         }else{
//                             $row2_lembur_4 = 1;
//                         }
// $explode2_lembur_5 = explode("|",$row2_pengerjaan->lembur);
//                         $explode2_status_lembur_5 = explode("-",$explode2_lembur_5[5]);
//                         if($explode2_status_lembur_5[1] == 'y'){
//                             $row2_lembur_5 = 1.5;
//                         }else{
//                             $row2_lembur_5 = 1;
//                         }
//                     }

//                     $row2_explode_hasil_kerja_3 = explode("|",$row2_pengerjaan->hasil_kerja_3);
//                     $row2_umk_borongan_lokal_3 = $this->umkBoronganEkspor->select('id','jenis_produk','umk_packing','umk_kemas','umk_pilih_gagang')->where('id',$row2_explode_hasil_kerja_3[0])->first();
//                     if(empty($row2_umk_borongan_lokal_3)){
//                         $row2_jenis_produk_3 = '-';
//                         $row2_hasil_kerja_3 = null;
//                         $row2_data_explode_hasil_kerja_3 = '-';
//                         $row2_lembur_3 = 1;
//                     }else{
//                         $row2_jenis_produk_3 = $row2_umk_borongan_lokal_3->jenis_produk;
//                         $row2_hasil_kerja_3 = $row2_explode_hasil_kerja_3[1]*$row2_umk_borongan_lokal_3->umk_pilih_gagang;
//                         $row2_data_explode_hasil_kerja_3 = $row2_explode_hasil_kerja_3[1];
//                         // $row2_lembur_3 = 1.5;
//                         $explode2_lembur_1 = explode("|",$row2_pengerjaan->lembur);
//                         $explode2_status_lembur_1 = explode("-",$explode2_lembur_1[1]);
//                         if($explode2_status_lembur_1[1] == 'y'){
//                             $row2_lembur_1 = 1.5;
//                         }else{
//                             $row2_lembur_1 = 1;
//                         }
// $explode2_lembur_2 = explode("|",$row2_pengerjaan->lembur);
//                         $explode2_status_lembur_2 = explode("-",$explode2_lembur_2[2]);
//                         if($explode2_status_lembur_2[1] == 'y'){
//                             $row2_lembur_2 = 1.5;
//                         }else{
//                             $row2_lembur_2 = 1;
//                         }
// $explode2_lembur_3 = explode("|",$row2_pengerjaan->lembur);
//                         $explode2_status_lembur_3 = explode("-",$explode2_lembur_3[3]);
//                         if($explode2_status_lembur_3[1] == 'y'){
//                             $row2_lembur_3 = 1.5;
//                         }else{
//                             $row2_lembur_3 = 1;
//                         }
// $explode2_lembur_4 = explode("|",$row2_pengerjaan->lembur);
//                         $explode2_status_lembur_4 = explode("-",$explode2_lembur_4[4]);
//                         if($explode2_status_lembur_4[1] == 'y'){
//                             $row2_lembur_4 = 1.5;
//                         }else{
//                             $row2_lembur_4 = 1;
//                         }
// $explode2_lembur_5 = explode("|",$row2_pengerjaan->lembur);
//                         $explode2_status_lembur_5 = explode("-",$explode2_lembur_5[5]);
//                         if($explode2_status_lembur_5[1] == 'y'){
//                             $row2_lembur_5 = 1.5;
//                         }else{
//                             $row2_lembur_5 = 1;
//                         }
//                     }

//                     $row2_explode_hasil_kerja_4 = explode("|",$row2_pengerjaan->hasil_kerja_4);
//                     $row2_umk_borongan_lokal_4 = $this->umkBoronganEkspor->select('id','jenis_produk','umk_packing','umk_kemas','umk_pilih_gagang')->where('id',$row2_explode_hasil_kerja_4[0])->first();
//                     if(empty($row2_umk_borongan_lokal_4)){
//                         $row2_jenis_produk_4 = '-';
//                         $row2_hasil_kerja_4 = null;
//                         $row2_data_explode_hasil_kerja_4 = '-';
//                         $row2_lembur_4 = 1;
//                     }else{
//                         $row2_jenis_produk_4 = $row2_umk_borongan_lokal_4->jenis_produk;
//                         $row2_hasil_kerja_4 = $row2_explode_hasil_kerja_4[1]*$row2_umk_borongan_lokal_4->umk_pilih_gagang;
//                         $row2_data_explode_hasil_kerja_4 = $row2_explode_hasil_kerja_4[1];
//                         // $row2_lembur_4 = 1.5;
//                         $explode2_lembur_1 = explode("|",$row2_pengerjaan->lembur);
//                         $explode2_status_lembur_1 = explode("-",$explode2_lembur_1[1]);
//                         if($explode2_status_lembur_1[1] == 'y'){
//                             $row2_lembur_1 = 1.5;
//                         }else{
//                             $row2_lembur_1 = 1;
//                         }
// $explode2_lembur_2 = explode("|",$row2_pengerjaan->lembur);
//                         $explode2_status_lembur_2 = explode("-",$explode2_lembur_2[2]);
//                         if($explode2_status_lembur_2[1] == 'y'){
//                             $row2_lembur_2 = 1.5;
//                         }else{
//                             $row2_lembur_2 = 1;
//                         }
// $explode2_lembur_3 = explode("|",$row2_pengerjaan->lembur);
//                         $explode2_status_lembur_3 = explode("-",$explode2_lembur_3[3]);
//                         if($explode2_status_lembur_3[1] == 'y'){
//                             $row2_lembur_3 = 1.5;
//                         }else{
//                             $row2_lembur_3 = 1;
//                         }
// $explode2_lembur_4 = explode("|",$row2_pengerjaan->lembur);
//                         $explode2_status_lembur_4 = explode("-",$explode2_lembur_4[4]);
//                         if($explode2_status_lembur_4[1] == 'y'){
//                             $row2_lembur_4 = 1.5;
//                         }else{
//                             $row2_lembur_4 = 1;
//                         }
// $explode2_lembur_5 = explode("|",$row2_pengerjaan->lembur);
//                         $explode2_status_lembur_5 = explode("-",$explode2_lembur_5[5]);
//                         if($explode2_status_lembur_5[1] == 'y'){
//                             $row2_lembur_5 = 1.5;
//                         }else{
//                             $row2_lembur_5 = 1;
//                         }
//                     }

//                     $row2_explode_hasil_kerja_5 = explode("|",$row2_pengerjaan->hasil_kerja_5);
//                     $row2_umk_borongan_lokal_5 = $this->umkBoronganEkspor->select('id','jenis_produk','umk_packing','umk_kemas','umk_pilih_gagang')->where('id',$row2_explode_hasil_kerja_5[0])->first();
//                     if(empty($row2_umk_borongan_lokal_5)){
//                         $row2_jenis_produk_5 = '-';
//                         $row2_hasil_kerja_5 = null;
//                         $row2_data_explode_hasil_kerja_5 = '-';
//                         $row2_lembur_5 = 1;
//                     }else{
//                         $row2_jenis_produk_5 = $row2_umk_borongan_lokal_5->jenis_produk;
//                         $row2_hasil_kerja_5 = $row2_explode_hasil_kerja_5[1]*$row2_umk_borongan_lokal_5->umk_pilih_gagang;
//                         $row2_data_explode_hasil_kerja_5 = $row2_explode_hasil_kerja_5[1];
//                         // $row2_lembur_5 = 1.5;
//                         $explode2_lembur_1 = explode("|",$row2_pengerjaan->lembur);
//                         $explode2_status_lembur_1 = explode("-",$explode2_lembur_1[1]);
//                         if($explode2_status_lembur_1[1] == 'y'){
//                             $row2_lembur_1 = 1.5;
//                         }else{
//                             $row2_lembur_1 = 1;
//                         }
// $explode2_lembur_2 = explode("|",$row2_pengerjaan->lembur);
//                         $explode2_status_lembur_2 = explode("-",$explode2_lembur_2[2]);
//                         if($explode2_status_lembur_2[1] == 'y'){
//                             $row2_lembur_2 = 1.5;
//                         }else{
//                             $row2_lembur_2 = 1;
//                         }
// $explode2_lembur_3 = explode("|",$row2_pengerjaan->lembur);
//                         $explode2_status_lembur_3 = explode("-",$explode2_lembur_3[3]);
//                         if($explode2_status_lembur_3[1] == 'y'){
//                             $row2_lembur_3 = 1.5;
//                         }else{
//                             $row2_lembur_3 = 1;
//                         }
// $explode2_lembur_4 = explode("|",$row2_pengerjaan->lembur);
//                         $explode2_status_lembur_4 = explode("-",$explode2_lembur_4[4]);
//                         if($explode2_status_lembur_4[1] == 'y'){
//                             $row2_lembur_4 = 1.5;
//                         }else{
//                             $row2_lembur_4 = 1;
//                         }
// $explode2_lembur_5 = explode("|",$row2_pengerjaan->lembur);
//                         $explode2_status_lembur_5 = explode("-",$explode2_lembur_5[5]);
//                         if($explode2_status_lembur_5[1] == 'y'){
//                             $row2_lembur_5 = 1.5;
//                         }else{
//                             $row2_lembur_5 = 1;
//                         }
//                     }
//                 }
//                 //End Ekspor

//                 //Begin Ambri
//                 if ($row2_pengerjaan->operator_karyawan->jenis_operator_detail_pekerjaan_id == 8) {
//                     $row2_explode_hasil_kerja_1 = explode("|",$row2_pengerjaan->hasil_kerja_1);
//                     $row2_umk_borongan_lokal_1 = $this->umkBoronganAmbri->select('id','jenis_produk','umk_etiket','umk_las_tepi','umk_las_pojok','umk_ambri')->where('id',$row2_explode_hasil_kerja_1[0])->first();
//                     if(empty($row2_umk_borongan_lokal_1)){
//                         $row2_jenis_produk_1 = '-';
//                         $row2_hasil_kerja_1 = null;
//                         $row2_data_explode_hasil_kerja_1 = '-';
//                         $row2_lembur_1 = 1;
//                     }else{
//                         $row2_jenis_produk_1 = $row2_umk_borongan_lokal_1->jenis_produk;
//                         $row2_hasil_kerja_1 = $row2_explode_hasil_kerja_1[1]*$row2_umk_borongan_lokal_1->umk_etiket;
//                         $row2_data_explode_hasil_kerja_1 = $row2_explode_hasil_kerja_1[1];
//                         // $row2_lembur_1 = 1.5;
//                         $explode2_lembur_1 = explode("|",$row2_pengerjaan->lembur);
//                         $explode2_status_lembur_1 = explode("-",$explode2_lembur_1[1]);
//                         if($explode2_status_lembur_1[1] == 'y'){
//                             $row2_lembur_1 = 1.5;
//                         }else{
//                             $row2_lembur_1 = 1;
//                         }
// $explode2_lembur_2 = explode("|",$row2_pengerjaan->lembur);
//                         $explode2_status_lembur_2 = explode("-",$explode2_lembur_2[2]);
//                         if($explode2_status_lembur_2[1] == 'y'){
//                             $row2_lembur_2 = 1.5;
//                         }else{
//                             $row2_lembur_2 = 1;
//                         }
// $explode2_lembur_3 = explode("|",$row2_pengerjaan->lembur);
//                         $explode2_status_lembur_3 = explode("-",$explode2_lembur_3[3]);
//                         if($explode2_status_lembur_3[1] == 'y'){
//                             $row2_lembur_3 = 1.5;
//                         }else{
//                             $row2_lembur_3 = 1;
//                         }
// $explode2_lembur_4 = explode("|",$row2_pengerjaan->lembur);
//                         $explode2_status_lembur_4 = explode("-",$explode2_lembur_4[4]);
//                         if($explode2_status_lembur_4[1] == 'y'){
//                             $row2_lembur_4 = 1.5;
//                         }else{
//                             $row2_lembur_4 = 1;
//                         }
// $explode2_lembur_5 = explode("|",$row2_pengerjaan->lembur);
//                         $explode2_status_lembur_5 = explode("-",$explode2_lembur_5[5]);
//                         if($explode2_status_lembur_5[1] == 'y'){
//                             $row2_lembur_5 = 1.5;
//                         }else{
//                             $row2_lembur_5 = 1;
//                         }
//                     }

//                     $row2_explode_hasil_kerja_2 = explode("|",$row2_pengerjaan->hasil_kerja_2);
//                     $row2_umk_borongan_lokal_2 = $this->umkBoronganAmbri->select('id','jenis_produk','umk_etiket','umk_las_tepi','umk_las_pojok','umk_ambri')->where('id',$row2_explode_hasil_kerja_1[0])->first();
//                     if(empty($row2_umk_borongan_lokal_2)){
//                         $row2_jenis_produk_2 = '-';
//                         $row2_hasil_kerja_2 = null;
//                         $row2_data_explode_hasil_kerja_2 = '-';
//                         $row2_lembur_2 = 1;
//                     }else{
//                         $row2_jenis_produk_2 = $row2_umk_borongan_lokal_2->jenis_produk;
//                         $row2_hasil_kerja_2 = $row2_explode_hasil_kerja_2[1]*$row2_umk_borongan_lokal_2->umk_etiket;
//                         $row2_data_explode_hasil_kerja_2 = $row2_explode_hasil_kerja_2[1];
//                         // $row2_lembur_2 = 1.5;
//                         $explode2_lembur_1 = explode("|",$row2_pengerjaan->lembur);
//                         $explode2_status_lembur_1 = explode("-",$explode2_lembur_1[1]);
//                         if($explode2_status_lembur_1[1] == 'y'){
//                             $row2_lembur_1 = 1.5;
//                         }else{
//                             $row2_lembur_1 = 1;
//                         }
// $explode2_lembur_2 = explode("|",$row2_pengerjaan->lembur);
//                         $explode2_status_lembur_2 = explode("-",$explode2_lembur_2[2]);
//                         if($explode2_status_lembur_2[1] == 'y'){
//                             $row2_lembur_2 = 1.5;
//                         }else{
//                             $row2_lembur_2 = 1;
//                         }
// $explode2_lembur_3 = explode("|",$row2_pengerjaan->lembur);
//                         $explode2_status_lembur_3 = explode("-",$explode2_lembur_3[3]);
//                         if($explode2_status_lembur_3[1] == 'y'){
//                             $row2_lembur_3 = 1.5;
//                         }else{
//                             $row2_lembur_3 = 1;
//                         }
// $explode2_lembur_4 = explode("|",$row2_pengerjaan->lembur);
//                         $explode2_status_lembur_4 = explode("-",$explode2_lembur_4[4]);
//                         if($explode2_status_lembur_4[1] == 'y'){
//                             $row2_lembur_4 = 1.5;
//                         }else{
//                             $row2_lembur_4 = 1;
//                         }
// $explode2_lembur_5 = explode("|",$row2_pengerjaan->lembur);
//                         $explode2_status_lembur_5 = explode("-",$explode2_lembur_5[5]);
//                         if($explode2_status_lembur_5[1] == 'y'){
//                             $row2_lembur_5 = 1.5;
//                         }else{
//                             $row2_lembur_5 = 1;
//                         }
//                     }

//                     $row2_explode_hasil_kerja_3 = explode("|",$row2_pengerjaan->hasil_kerja_3);
//                     $row2_umk_borongan_lokal_3 = $this->umkBoronganAmbri->select('id','jenis_produk','umk_etiket','umk_las_tepi','umk_las_pojok','umk_ambri')->where('id',$row2_explode_hasil_kerja_1[0])->first();
//                     if(empty($row2_umk_borongan_lokal_3)){
//                         $row2_jenis_produk_3 = '-';
//                         $row2_hasil_kerja_3 = null;
//                         $row2_data_explode_hasil_kerja_3 = '-';
//                         $row2_lembur_3 = 1;
//                     }else{
//                         $row2_jenis_produk_3 = $row2_umk_borongan_lokal_3->jenis_produk;
//                         $row2_hasil_kerja_3 = $row2_explode_hasil_kerja_3[1]*$row2_umk_borongan_lokal_3->umk_etiket;
//                         $row2_data_explode_hasil_kerja_3 = $row2_explode_hasil_kerja_3[1];
//                         // $row2_lembur_3 = 1.5;
//                         $explode2_lembur_1 = explode("|",$row2_pengerjaan->lembur);
//                         $explode2_status_lembur_1 = explode("-",$explode2_lembur_1[1]);
//                         if($explode2_status_lembur_1[1] == 'y'){
//                             $row2_lembur_1 = 1.5;
//                         }else{
//                             $row2_lembur_1 = 1;
//                         }
// $explode2_lembur_2 = explode("|",$row2_pengerjaan->lembur);
//                         $explode2_status_lembur_2 = explode("-",$explode2_lembur_2[2]);
//                         if($explode2_status_lembur_2[1] == 'y'){
//                             $row2_lembur_2 = 1.5;
//                         }else{
//                             $row2_lembur_2 = 1;
//                         }
// $explode2_lembur_3 = explode("|",$row2_pengerjaan->lembur);
//                         $explode2_status_lembur_3 = explode("-",$explode2_lembur_3[3]);
//                         if($explode2_status_lembur_3[1] == 'y'){
//                             $row2_lembur_3 = 1.5;
//                         }else{
//                             $row2_lembur_3 = 1;
//                         }
// $explode2_lembur_4 = explode("|",$row2_pengerjaan->lembur);
//                         $explode2_status_lembur_4 = explode("-",$explode2_lembur_4[4]);
//                         if($explode2_status_lembur_4[1] == 'y'){
//                             $row2_lembur_4 = 1.5;
//                         }else{
//                             $row2_lembur_4 = 1;
//                         }
// $explode2_lembur_5 = explode("|",$row2_pengerjaan->lembur);
//                         $explode2_status_lembur_5 = explode("-",$explode2_lembur_5[5]);
//                         if($explode2_status_lembur_5[1] == 'y'){
//                             $row2_lembur_5 = 1.5;
//                         }else{
//                             $row2_lembur_5 = 1;
//                         }
//                     }

//                     $row2_explode_hasil_kerja_4 = explode("|",$row2_pengerjaan->hasil_kerja_4);
//                     $row2_umk_borongan_lokal_4 = $this->umkBoronganAmbri->select('id','jenis_produk','umk_etiket','umk_las_tepi','umk_las_pojok','umk_ambri')->where('id',$row2_explode_hasil_kerja_1[0])->first();
//                     if(empty($row2_umk_borongan_lokal_4)){
//                         $row2_jenis_produk_4 = '-';
//                         $row2_hasil_kerja_4 = null;
//                         $row2_data_explode_hasil_kerja_4 = '-';
//                         $row2_lembur_4 = 1;
//                     }else{
//                         $row2_jenis_produk_4 = $row2_umk_borongan_lokal_4->jenis_produk;
//                         $row2_hasil_kerja_4 = $row2_explode_hasil_kerja_4[1]*$row2_umk_borongan_lokal_4->umk_etiket;
//                         $row2_data_explode_hasil_kerja_4 = $row2_explode_hasil_kerja_4[1];
//                         // $row2_lembur_4 = 1.5;
//                         $explode2_lembur_1 = explode("|",$row2_pengerjaan->lembur);
//                         $explode2_status_lembur_1 = explode("-",$explode2_lembur_1[1]);
//                         if($explode2_status_lembur_1[1] == 'y'){
//                             $row2_lembur_1 = 1.5;
//                         }else{
//                             $row2_lembur_1 = 1;
//                         }
// $explode2_lembur_2 = explode("|",$row2_pengerjaan->lembur);
//                         $explode2_status_lembur_2 = explode("-",$explode2_lembur_2[2]);
//                         if($explode2_status_lembur_2[1] == 'y'){
//                             $row2_lembur_2 = 1.5;
//                         }else{
//                             $row2_lembur_2 = 1;
//                         }
// $explode2_lembur_3 = explode("|",$row2_pengerjaan->lembur);
//                         $explode2_status_lembur_3 = explode("-",$explode2_lembur_3[3]);
//                         if($explode2_status_lembur_3[1] == 'y'){
//                             $row2_lembur_3 = 1.5;
//                         }else{
//                             $row2_lembur_3 = 1;
//                         }
// $explode2_lembur_4 = explode("|",$row2_pengerjaan->lembur);
//                         $explode2_status_lembur_4 = explode("-",$explode2_lembur_4[4]);
//                         if($explode2_status_lembur_4[1] == 'y'){
//                             $row2_lembur_4 = 1.5;
//                         }else{
//                             $row2_lembur_4 = 1;
//                         }
// $explode2_lembur_5 = explode("|",$row2_pengerjaan->lembur);
//                         $explode2_status_lembur_5 = explode("-",$explode2_lembur_5[5]);
//                         if($explode2_status_lembur_5[1] == 'y'){
//                             $row2_lembur_5 = 1.5;
//                         }else{
//                             $row2_lembur_5 = 1;
//                         }
//                     }

//                     $row2_explode_hasil_kerja_5 = explode("|",$row2_pengerjaan->hasil_kerja_5);
//                     $row2_umk_borongan_lokal_5 = $this->umkBoronganAmbri->select('id','jenis_produk','umk_etiket','umk_las_tepi','umk_las_pojok','umk_ambri')->where('id',$row2_explode_hasil_kerja_1[0])->first();
//                     if(empty($row2_umk_borongan_lokal_5)){
//                         $row2_jenis_produk_5 = '-';
//                         $row2_hasil_kerja_5 = null;
//                         $row2_data_explode_hasil_kerja_5 = '-';
//                         $row2_lembur_5 = 1;
//                     }else{
//                         $row2_jenis_produk_5 = $row2_umk_borongan_lokal_5->jenis_produk;
//                         $row2_hasil_kerja_5 = $row2_explode_hasil_kerja_5[1]*$row2_umk_borongan_lokal_5->umk_etiket;
//                         $row2_data_explode_hasil_kerja_5 = $row2_explode_hasil_kerja_5[1];
//                         // $row2_lembur_5 = 1.5;
//                         $explode2_lembur_1 = explode("|",$row2_pengerjaan->lembur);
//                         $explode2_status_lembur_1 = explode("-",$explode2_lembur_1[1]);
//                         if($explode2_status_lembur_1[1] == 'y'){
//                             $row2_lembur_1 = 1.5;
//                         }else{
//                             $row2_lembur_1 = 1;
//                         }
// $explode2_lembur_2 = explode("|",$row2_pengerjaan->lembur);
//                         $explode2_status_lembur_2 = explode("-",$explode2_lembur_2[2]);
//                         if($explode2_status_lembur_2[1] == 'y'){
//                             $row2_lembur_2 = 1.5;
//                         }else{
//                             $row2_lembur_2 = 1;
//                         }
// $explode2_lembur_3 = explode("|",$row2_pengerjaan->lembur);
//                         $explode2_status_lembur_3 = explode("-",$explode2_lembur_3[3]);
//                         if($explode2_status_lembur_3[1] == 'y'){
//                             $row2_lembur_3 = 1.5;
//                         }else{
//                             $row2_lembur_3 = 1;
//                         }
// $explode2_lembur_4 = explode("|",$row2_pengerjaan->lembur);
//                         $explode2_status_lembur_4 = explode("-",$explode2_lembur_4[4]);
//                         if($explode2_status_lembur_4[1] == 'y'){
//                             $row2_lembur_4 = 1.5;
//                         }else{
//                             $row2_lembur_4 = 1;
//                         }
// $explode2_lembur_5 = explode("|",$row2_pengerjaan->lembur);
//                         $explode2_status_lembur_5 = explode("-",$explode2_lembur_5[5]);
//                         if($explode2_status_lembur_5[1] == 'y'){
//                             $row2_lembur_5 = 1.5;
//                         }else{
//                             $row2_lembur_5 = 1;
//                         }
//                     }
//                 }

//                 if ($row2_pengerjaan->operator_karyawan->jenis_operator_detail_pekerjaan_id == 9) {
//                     $row2_explode_hasil_kerja_1 = explode("|",$row2_pengerjaan->hasil_kerja_1);
//                     $row2_umk_borongan_lokal_1 = $this->umkBoronganAmbri->select('id','jenis_produk','umk_etiket','umk_las_tepi','umk_las_pojok','umk_ambri')->where('id',$row2_explode_hasil_kerja_1[0])->first();
//                     if(empty($row2_umk_borongan_lokal_1)){
//                         $row2_jenis_produk_1 = '-';
//                         $row2_hasil_kerja_1 = null;
//                         $row2_data_explode_hasil_kerja_1 = '-';
//                         $row2_lembur_1 = 1;
//                     }else{
//                         $row2_jenis_produk_1 = $row2_umk_borongan_lokal_1->jenis_produk;
//                         $row2_hasil_kerja_1 = $row2_explode_hasil_kerja_1[1]*$row2_umk_borongan_lokal_1->umk_las_tepi;
//                         $row2_data_explode_hasil_kerja_1 = $row2_explode_hasil_kerja_1[1];
//                         // $row2_lembur_1 = 1.5;
//                         $explode2_lembur_1 = explode("|",$row2_pengerjaan->lembur);
//                         $explode2_status_lembur_1 = explode("-",$explode2_lembur_1[1]);
//                         if($explode2_status_lembur_1[1] == 'y'){
//                             $row2_lembur_1 = 1.5;
//                         }else{
//                             $row2_lembur_1 = 1;
//                         }
// $explode2_lembur_2 = explode("|",$row2_pengerjaan->lembur);
//                         $explode2_status_lembur_2 = explode("-",$explode2_lembur_2[2]);
//                         if($explode2_status_lembur_2[1] == 'y'){
//                             $row2_lembur_2 = 1.5;
//                         }else{
//                             $row2_lembur_2 = 1;
//                         }
// $explode2_lembur_3 = explode("|",$row2_pengerjaan->lembur);
//                         $explode2_status_lembur_3 = explode("-",$explode2_lembur_3[3]);
//                         if($explode2_status_lembur_3[1] == 'y'){
//                             $row2_lembur_3 = 1.5;
//                         }else{
//                             $row2_lembur_3 = 1;
//                         }
// $explode2_lembur_4 = explode("|",$row2_pengerjaan->lembur);
//                         $explode2_status_lembur_4 = explode("-",$explode2_lembur_4[4]);
//                         if($explode2_status_lembur_4[1] == 'y'){
//                             $row2_lembur_4 = 1.5;
//                         }else{
//                             $row2_lembur_4 = 1;
//                         }
// $explode2_lembur_5 = explode("|",$row2_pengerjaan->lembur);
//                         $explode2_status_lembur_5 = explode("-",$explode2_lembur_5[5]);
//                         if($explode2_status_lembur_5[1] == 'y'){
//                             $row2_lembur_5 = 1.5;
//                         }else{
//                             $row2_lembur_5 = 1;
//                         }
//                     }

//                     $row2_explode_hasil_kerja_2 = explode("|",$row2_pengerjaan->hasil_kerja_2);
//                     $row2_umk_borongan_lokal_2 = $this->umkBoronganAmbri->select('id','jenis_produk','umk_etiket','umk_las_tepi','umk_las_pojok','umk_ambri')->where('id',$row2_explode_hasil_kerja_1[0])->first();
//                     if(empty($row2_umk_borongan_lokal_2)){
//                         $row2_jenis_produk_2 = '-';
//                         $row2_hasil_kerja_2 = null;
//                         $row2_data_explode_hasil_kerja_2 = '-';
//                         $row2_lembur_2 = 1;
//                     }else{
//                         $row2_jenis_produk_2 = $row2_umk_borongan_lokal_2->jenis_produk;
//                         $row2_hasil_kerja_2 = $row2_explode_hasil_kerja_2[1]*$row2_umk_borongan_lokal_2->umk_las_tepi;
//                         $row2_data_explode_hasil_kerja_2 = $row2_explode_hasil_kerja_2[1];
//                         // $row2_lembur_2 = 1.5;
//                         $explode2_lembur_1 = explode("|",$row2_pengerjaan->lembur);
//                         $explode2_status_lembur_1 = explode("-",$explode2_lembur_1[1]);
//                         if($explode2_status_lembur_1[1] == 'y'){
//                             $row2_lembur_1 = 1.5;
//                         }else{
//                             $row2_lembur_1 = 1;
//                         }
// $explode2_lembur_2 = explode("|",$row2_pengerjaan->lembur);
//                         $explode2_status_lembur_2 = explode("-",$explode2_lembur_2[2]);
//                         if($explode2_status_lembur_2[1] == 'y'){
//                             $row2_lembur_2 = 1.5;
//                         }else{
//                             $row2_lembur_2 = 1;
//                         }
// $explode2_lembur_3 = explode("|",$row2_pengerjaan->lembur);
//                         $explode2_status_lembur_3 = explode("-",$explode2_lembur_3[3]);
//                         if($explode2_status_lembur_3[1] == 'y'){
//                             $row2_lembur_3 = 1.5;
//                         }else{
//                             $row2_lembur_3 = 1;
//                         }
// $explode2_lembur_4 = explode("|",$row2_pengerjaan->lembur);
//                         $explode2_status_lembur_4 = explode("-",$explode2_lembur_4[4]);
//                         if($explode2_status_lembur_4[1] == 'y'){
//                             $row2_lembur_4 = 1.5;
//                         }else{
//                             $row2_lembur_4 = 1;
//                         }
// $explode2_lembur_5 = explode("|",$row2_pengerjaan->lembur);
//                         $explode2_status_lembur_5 = explode("-",$explode2_lembur_5[5]);
//                         if($explode2_status_lembur_5[1] == 'y'){
//                             $row2_lembur_5 = 1.5;
//                         }else{
//                             $row2_lembur_5 = 1;
//                         }
//                     }

//                     $row2_explode_hasil_kerja_3 = explode("|",$row2_pengerjaan->hasil_kerja_3);
//                     $row2_umk_borongan_lokal_3 = $this->umkBoronganAmbri->select('id','jenis_produk','umk_etiket','umk_las_tepi','umk_las_pojok','umk_ambri')->where('id',$row2_explode_hasil_kerja_1[0])->first();
//                     if(empty($row2_umk_borongan_lokal_3)){
//                         $row2_jenis_produk_3 = '-';
//                         $row2_hasil_kerja_3 = null;
//                         $row2_data_explode_hasil_kerja_3 = '-';
//                         $row2_lembur_3 = 1;
//                     }else{
//                         $row2_jenis_produk_3 = $row2_umk_borongan_lokal_3->jenis_produk;
//                         $row2_hasil_kerja_3 = $row2_explode_hasil_kerja_3[1]*$row2_umk_borongan_lokal_3->umk_las_tepi;
//                         $row2_data_explode_hasil_kerja_3 = $row2_explode_hasil_kerja_3[1];
//                         // $row2_lembur_3 = 1.5;
//                         $explode2_lembur_1 = explode("|",$row2_pengerjaan->lembur);
//                         $explode2_status_lembur_1 = explode("-",$explode2_lembur_1[1]);
//                         if($explode2_status_lembur_1[1] == 'y'){
//                             $row2_lembur_1 = 1.5;
//                         }else{
//                             $row2_lembur_1 = 1;
//                         }
// $explode2_lembur_2 = explode("|",$row2_pengerjaan->lembur);
//                         $explode2_status_lembur_2 = explode("-",$explode2_lembur_2[2]);
//                         if($explode2_status_lembur_2[1] == 'y'){
//                             $row2_lembur_2 = 1.5;
//                         }else{
//                             $row2_lembur_2 = 1;
//                         }
// $explode2_lembur_3 = explode("|",$row2_pengerjaan->lembur);
//                         $explode2_status_lembur_3 = explode("-",$explode2_lembur_3[3]);
//                         if($explode2_status_lembur_3[1] == 'y'){
//                             $row2_lembur_3 = 1.5;
//                         }else{
//                             $row2_lembur_3 = 1;
//                         }
// $explode2_lembur_4 = explode("|",$row2_pengerjaan->lembur);
//                         $explode2_status_lembur_4 = explode("-",$explode2_lembur_4[4]);
//                         if($explode2_status_lembur_4[1] == 'y'){
//                             $row2_lembur_4 = 1.5;
//                         }else{
//                             $row2_lembur_4 = 1;
//                         }
// $explode2_lembur_5 = explode("|",$row2_pengerjaan->lembur);
//                         $explode2_status_lembur_5 = explode("-",$explode2_lembur_5[5]);
//                         if($explode2_status_lembur_5[1] == 'y'){
//                             $row2_lembur_5 = 1.5;
//                         }else{
//                             $row2_lembur_5 = 1;
//                         }
//                     }

//                     $row2_explode_hasil_kerja_4 = explode("|",$row2_pengerjaan->hasil_kerja_4);
//                     $row2_umk_borongan_lokal_4 = $this->umkBoronganAmbri->select('id','jenis_produk','umk_etiket','umk_las_tepi','umk_las_pojok','umk_ambri')->where('id',$row2_explode_hasil_kerja_1[0])->first();
//                     if(empty($row2_umk_borongan_lokal_4)){
//                         $row2_jenis_produk_4 = '-';
//                         $row2_hasil_kerja_4 = null;
//                         $row2_data_explode_hasil_kerja_4 = '-';
//                         $row2_lembur_4 = 1;
//                     }else{
//                         $row2_jenis_produk_4 = $row2_umk_borongan_lokal_4->jenis_produk;
//                         $row2_hasil_kerja_4 = $row2_explode_hasil_kerja_4[1]*$row2_umk_borongan_lokal_4->umk_las_tepi;
//                         $row2_data_explode_hasil_kerja_4 = $row2_explode_hasil_kerja_4[1];
//                         // $row2_lembur_4 = 1.5;
//                         $explode2_lembur_1 = explode("|",$row2_pengerjaan->lembur);
//                         $explode2_status_lembur_1 = explode("-",$explode2_lembur_1[1]);
//                         if($explode2_status_lembur_1[1] == 'y'){
//                             $row2_lembur_1 = 1.5;
//                         }else{
//                             $row2_lembur_1 = 1;
//                         }
// $explode2_lembur_2 = explode("|",$row2_pengerjaan->lembur);
//                         $explode2_status_lembur_2 = explode("-",$explode2_lembur_2[2]);
//                         if($explode2_status_lembur_2[1] == 'y'){
//                             $row2_lembur_2 = 1.5;
//                         }else{
//                             $row2_lembur_2 = 1;
//                         }
// $explode2_lembur_3 = explode("|",$row2_pengerjaan->lembur);
//                         $explode2_status_lembur_3 = explode("-",$explode2_lembur_3[3]);
//                         if($explode2_status_lembur_3[1] == 'y'){
//                             $row2_lembur_3 = 1.5;
//                         }else{
//                             $row2_lembur_3 = 1;
//                         }
// $explode2_lembur_4 = explode("|",$row2_pengerjaan->lembur);
//                         $explode2_status_lembur_4 = explode("-",$explode2_lembur_4[4]);
//                         if($explode2_status_lembur_4[1] == 'y'){
//                             $row2_lembur_4 = 1.5;
//                         }else{
//                             $row2_lembur_4 = 1;
//                         }
// $explode2_lembur_5 = explode("|",$row2_pengerjaan->lembur);
//                         $explode2_status_lembur_5 = explode("-",$explode2_lembur_5[5]);
//                         if($explode2_status_lembur_5[1] == 'y'){
//                             $row2_lembur_5 = 1.5;
//                         }else{
//                             $row2_lembur_5 = 1;
//                         }
//                     }

//                     $row2_explode_hasil_kerja_5 = explode("|",$row2_pengerjaan->hasil_kerja_5);
//                     $row2_umk_borongan_lokal_5 = $this->umkBoronganAmbri->select('id','jenis_produk','umk_etiket','umk_las_tepi','umk_las_pojok','umk_ambri')->where('id',$row2_explode_hasil_kerja_1[0])->first();
//                     if(empty($row2_umk_borongan_lokal_5)){
//                         $row2_jenis_produk_5 = '-';
//                         $row2_hasil_kerja_5 = null;
//                         $row2_data_explode_hasil_kerja_5 = '-';
//                         $row2_lembur_5 = 1;
//                     }else{
//                         $row2_jenis_produk_5 = $row2_umk_borongan_lokal_5->jenis_produk;
//                         $row2_hasil_kerja_5 = $row2_explode_hasil_kerja_5[1]*$row2_umk_borongan_lokal_5->umk_las_tepi;
//                         $row2_data_explode_hasil_kerja_5 = $row2_explode_hasil_kerja_5[1];
//                         // $row2_lembur_5 = 1.5;
//                         $explode2_lembur_1 = explode("|",$row2_pengerjaan->lembur);
//                         $explode2_status_lembur_1 = explode("-",$explode2_lembur_1[1]);
//                         if($explode2_status_lembur_1[1] == 'y'){
//                             $row2_lembur_1 = 1.5;
//                         }else{
//                             $row2_lembur_1 = 1;
//                         }
// $explode2_lembur_2 = explode("|",$row2_pengerjaan->lembur);
//                         $explode2_status_lembur_2 = explode("-",$explode2_lembur_2[2]);
//                         if($explode2_status_lembur_2[1] == 'y'){
//                             $row2_lembur_2 = 1.5;
//                         }else{
//                             $row2_lembur_2 = 1;
//                         }
// $explode2_lembur_3 = explode("|",$row2_pengerjaan->lembur);
//                         $explode2_status_lembur_3 = explode("-",$explode2_lembur_3[3]);
//                         if($explode2_status_lembur_3[1] == 'y'){
//                             $row2_lembur_3 = 1.5;
//                         }else{
//                             $row2_lembur_3 = 1;
//                         }
// $explode2_lembur_4 = explode("|",$row2_pengerjaan->lembur);
//                         $explode2_status_lembur_4 = explode("-",$explode2_lembur_4[4]);
//                         if($explode2_status_lembur_4[1] == 'y'){
//                             $row2_lembur_4 = 1.5;
//                         }else{
//                             $row2_lembur_4 = 1;
//                         }
// $explode2_lembur_5 = explode("|",$row2_pengerjaan->lembur);
//                         $explode2_status_lembur_5 = explode("-",$explode2_lembur_5[5]);
//                         if($explode2_status_lembur_5[1] == 'y'){
//                             $row2_lembur_5 = 1.5;
//                         }else{
//                             $row2_lembur_5 = 1;
//                         }
//                     }
//                 }

//                 if ($row2_pengerjaan->operator_karyawan->jenis_operator_detail_pekerjaan_id == 11) {
//                     $row2_explode_hasil_kerja_1 = explode("|",$row2_pengerjaan->hasil_kerja_1);
//                     $row2_umk_borongan_lokal_1 = $this->umkBoronganAmbri->select('id','jenis_produk','umk_etiket','umk_las_tepi','umk_las_pojok','umk_ambri')->where('id',$row2_explode_hasil_kerja_1[0])->first();
//                     if(empty($row2_umk_borongan_lokal_1)){
//                         $row2_jenis_produk_1 = '-';
//                         $row2_hasil_kerja_1 = null;
//                         $row2_data_explode_hasil_kerja_1 = '-';
//                         $row2_lembur_1 = 1;
//                     }else{
//                         $row2_jenis_produk_1 = $row2_umk_borongan_lokal_1->jenis_produk;
//                         $row2_hasil_kerja_1 = $row2_explode_hasil_kerja_1[1]*$row2_umk_borongan_lokal_1->umk_ambri;
//                         $row2_data_explode_hasil_kerja_1 = $row2_explode_hasil_kerja_1[1];
//                         // $row2_lembur_1 = 1.5;
//                         $explode2_lembur_1 = explode("|",$row2_pengerjaan->lembur);
//                         $explode2_status_lembur_1 = explode("-",$explode2_lembur_1[1]);
//                         if($explode2_status_lembur_1[1] == 'y'){
//                             $row2_lembur_1 = 1.5;
//                         }else{
//                             $row2_lembur_1 = 1;
//                         }
// $explode2_lembur_2 = explode("|",$row2_pengerjaan->lembur);
//                         $explode2_status_lembur_2 = explode("-",$explode2_lembur_2[2]);
//                         if($explode2_status_lembur_2[1] == 'y'){
//                             $row2_lembur_2 = 1.5;
//                         }else{
//                             $row2_lembur_2 = 1;
//                         }
// $explode2_lembur_3 = explode("|",$row2_pengerjaan->lembur);
//                         $explode2_status_lembur_3 = explode("-",$explode2_lembur_3[3]);
//                         if($explode2_status_lembur_3[1] == 'y'){
//                             $row2_lembur_3 = 1.5;
//                         }else{
//                             $row2_lembur_3 = 1;
//                         }
// $explode2_lembur_4 = explode("|",$row2_pengerjaan->lembur);
//                         $explode2_status_lembur_4 = explode("-",$explode2_lembur_4[4]);
//                         if($explode2_status_lembur_4[1] == 'y'){
//                             $row2_lembur_4 = 1.5;
//                         }else{
//                             $row2_lembur_4 = 1;
//                         }
// $explode2_lembur_5 = explode("|",$row2_pengerjaan->lembur);
//                         $explode2_status_lembur_5 = explode("-",$explode2_lembur_5[5]);
//                         if($explode2_status_lembur_5[1] == 'y'){
//                             $row2_lembur_5 = 1.5;
//                         }else{
//                             $row2_lembur_5 = 1;
//                         }
//                     }

//                     $row2_explode_hasil_kerja_2 = explode("|",$row2_pengerjaan->hasil_kerja_2);
//                     $row2_umk_borongan_lokal_2 = $this->umkBoronganAmbri->select('id','jenis_produk','umk_etiket','umk_las_tepi','umk_las_pojok','umk_ambri')->where('id',$row2_explode_hasil_kerja_1[0])->first();
//                     if(empty($row2_umk_borongan_lokal_2)){
//                         $row2_jenis_produk_2 = '-';
//                         $row2_hasil_kerja_2 = null;
//                         $row2_data_explode_hasil_kerja_2 = '-';
//                         $row2_lembur_2 = 1;
//                     }else{
//                         $row2_jenis_produk_2 = $row2_umk_borongan_lokal_2->jenis_produk;
//                         $row2_hasil_kerja_2 = $row2_explode_hasil_kerja_2[1]*$row2_umk_borongan_lokal_2->umk_ambri;
//                         $row2_data_explode_hasil_kerja_2 = $row2_explode_hasil_kerja_2[1];
//                         // $row2_lembur_2 = 1.5;
//                         $explode2_lembur_1 = explode("|",$row2_pengerjaan->lembur);
//                         $explode2_status_lembur_1 = explode("-",$explode2_lembur_1[1]);
//                         if($explode2_status_lembur_1[1] == 'y'){
//                             $row2_lembur_1 = 1.5;
//                         }else{
//                             $row2_lembur_1 = 1;
//                         }
// $explode2_lembur_2 = explode("|",$row2_pengerjaan->lembur);
//                         $explode2_status_lembur_2 = explode("-",$explode2_lembur_2[2]);
//                         if($explode2_status_lembur_2[1] == 'y'){
//                             $row2_lembur_2 = 1.5;
//                         }else{
//                             $row2_lembur_2 = 1;
//                         }
// $explode2_lembur_3 = explode("|",$row2_pengerjaan->lembur);
//                         $explode2_status_lembur_3 = explode("-",$explode2_lembur_3[3]);
//                         if($explode2_status_lembur_3[1] == 'y'){
//                             $row2_lembur_3 = 1.5;
//                         }else{
//                             $row2_lembur_3 = 1;
//                         }
// $explode2_lembur_4 = explode("|",$row2_pengerjaan->lembur);
//                         $explode2_status_lembur_4 = explode("-",$explode2_lembur_4[4]);
//                         if($explode2_status_lembur_4[1] == 'y'){
//                             $row2_lembur_4 = 1.5;
//                         }else{
//                             $row2_lembur_4 = 1;
//                         }
// $explode2_lembur_5 = explode("|",$row2_pengerjaan->lembur);
//                         $explode2_status_lembur_5 = explode("-",$explode2_lembur_5[5]);
//                         if($explode2_status_lembur_5[1] == 'y'){
//                             $row2_lembur_5 = 1.5;
//                         }else{
//                             $row2_lembur_5 = 1;
//                         }
//                     }

//                     $row2_explode_hasil_kerja_3 = explode("|",$row2_pengerjaan->hasil_kerja_3);
//                     $row2_umk_borongan_lokal_3 = $this->umkBoronganAmbri->select('id','jenis_produk','umk_etiket','umk_las_tepi','umk_las_pojok','umk_ambri')->where('id',$row2_explode_hasil_kerja_1[0])->first();
//                     if(empty($row2_umk_borongan_lokal_3)){
//                         $row2_jenis_produk_3 = '-';
//                         $row2_hasil_kerja_3 = null;
//                         $row2_data_explode_hasil_kerja_3 = '-';
//                         $row2_lembur_3 = 1;
//                     }else{
//                         $row2_jenis_produk_3 = $row2_umk_borongan_lokal_3->jenis_produk;
//                         $row2_hasil_kerja_3 = $row2_explode_hasil_kerja_3[1]*$row2_umk_borongan_lokal_3->umk_ambri;
//                         $row2_data_explode_hasil_kerja_3 = $row2_explode_hasil_kerja_3[1];
//                         // $row2_lembur_3 = 1.5;
//                         $explode2_lembur_1 = explode("|",$row2_pengerjaan->lembur);
//                         $explode2_status_lembur_1 = explode("-",$explode2_lembur_1[1]);
//                         if($explode2_status_lembur_1[1] == 'y'){
//                             $row2_lembur_1 = 1.5;
//                         }else{
//                             $row2_lembur_1 = 1;
//                         }
// $explode2_lembur_2 = explode("|",$row2_pengerjaan->lembur);
//                         $explode2_status_lembur_2 = explode("-",$explode2_lembur_2[2]);
//                         if($explode2_status_lembur_2[1] == 'y'){
//                             $row2_lembur_2 = 1.5;
//                         }else{
//                             $row2_lembur_2 = 1;
//                         }
// $explode2_lembur_3 = explode("|",$row2_pengerjaan->lembur);
//                         $explode2_status_lembur_3 = explode("-",$explode2_lembur_3[3]);
//                         if($explode2_status_lembur_3[1] == 'y'){
//                             $row2_lembur_3 = 1.5;
//                         }else{
//                             $row2_lembur_3 = 1;
//                         }
// $explode2_lembur_4 = explode("|",$row2_pengerjaan->lembur);
//                         $explode2_status_lembur_4 = explode("-",$explode2_lembur_4[4]);
//                         if($explode2_status_lembur_4[1] == 'y'){
//                             $row2_lembur_4 = 1.5;
//                         }else{
//                             $row2_lembur_4 = 1;
//                         }
// $explode2_lembur_5 = explode("|",$row2_pengerjaan->lembur);
//                         $explode2_status_lembur_5 = explode("-",$explode2_lembur_5[5]);
//                         if($explode2_status_lembur_5[1] == 'y'){
//                             $row2_lembur_5 = 1.5;
//                         }else{
//                             $row2_lembur_5 = 1;
//                         }
//                     }

//                     $row2_explode_hasil_kerja_4 = explode("|",$row2_pengerjaan->hasil_kerja_4);
//                     $row2_umk_borongan_lokal_4 = $this->umkBoronganAmbri->select('id','jenis_produk','umk_etiket','umk_las_tepi','umk_las_pojok','umk_ambri')->where('id',$row2_explode_hasil_kerja_1[0])->first();
//                     if(empty($row2_umk_borongan_lokal_4)){
//                         $row2_jenis_produk_4 = '-';
//                         $row2_hasil_kerja_4 = null;
//                         $row2_data_explode_hasil_kerja_4 = '-';
//                         $row2_lembur_4 = 1;
//                     }else{
//                         $row2_jenis_produk_4 = $row2_umk_borongan_lokal_4->jenis_produk;
//                         $row2_hasil_kerja_4 = $row2_explode_hasil_kerja_4[1]*$row2_umk_borongan_lokal_4->umk_ambri;
//                         $row2_data_explode_hasil_kerja_4 = $row2_explode_hasil_kerja_4[1];
//                         // $row2_lembur_4 = 1.5;
//                         $explode2_lembur_1 = explode("|",$row2_pengerjaan->lembur);
//                         $explode2_status_lembur_1 = explode("-",$explode2_lembur_1[1]);
//                         if($explode2_status_lembur_1[1] == 'y'){
//                             $row2_lembur_1 = 1.5;
//                         }else{
//                             $row2_lembur_1 = 1;
//                         }
// $explode2_lembur_2 = explode("|",$row2_pengerjaan->lembur);
//                         $explode2_status_lembur_2 = explode("-",$explode2_lembur_2[2]);
//                         if($explode2_status_lembur_2[1] == 'y'){
//                             $row2_lembur_2 = 1.5;
//                         }else{
//                             $row2_lembur_2 = 1;
//                         }
// $explode2_lembur_3 = explode("|",$row2_pengerjaan->lembur);
//                         $explode2_status_lembur_3 = explode("-",$explode2_lembur_3[3]);
//                         if($explode2_status_lembur_3[1] == 'y'){
//                             $row2_lembur_3 = 1.5;
//                         }else{
//                             $row2_lembur_3 = 1;
//                         }
// $explode2_lembur_4 = explode("|",$row2_pengerjaan->lembur);
//                         $explode2_status_lembur_4 = explode("-",$explode2_lembur_4[4]);
//                         if($explode2_status_lembur_4[1] == 'y'){
//                             $row2_lembur_4 = 1.5;
//                         }else{
//                             $row2_lembur_4 = 1;
//                         }
// $explode2_lembur_5 = explode("|",$row2_pengerjaan->lembur);
//                         $explode2_status_lembur_5 = explode("-",$explode2_lembur_5[5]);
//                         if($explode2_status_lembur_5[1] == 'y'){
//                             $row2_lembur_5 = 1.5;
//                         }else{
//                             $row2_lembur_5 = 1;
//                         }
//                     }

//                     $row2_explode_hasil_kerja_5 = explode("|",$row2_pengerjaan->hasil_kerja_5);
//                     $row2_umk_borongan_lokal_5 = $this->umkBoronganAmbri->select('id','jenis_produk','umk_etiket','umk_las_tepi','umk_las_pojok','umk_ambri')->where('id',$row2_explode_hasil_kerja_1[0])->first();
//                     if(empty($row2_umk_borongan_lokal_5)){
//                         $row2_jenis_produk_5 = '-';
//                         $row2_hasil_kerja_5 = null;
//                         $row2_data_explode_hasil_kerja_5 = '-';
//                         $row2_lembur_5 = 1;
//                     }else{
//                         $row2_jenis_produk_5 = $row2_umk_borongan_lokal_5->jenis_produk;
//                         $row2_hasil_kerja_5 = $row2_explode_hasil_kerja_5[1]*$row2_umk_borongan_lokal_5->umk_ambri;
//                         $row2_data_explode_hasil_kerja_5 = $row2_explode_hasil_kerja_5[1];
//                         // $row2_lembur_5 = 1.5;
//                         $explode2_lembur_1 = explode("|",$row2_pengerjaan->lembur);
//                         $explode2_status_lembur_1 = explode("-",$explode2_lembur_1[1]);
//                         if($explode2_status_lembur_1[1] == 'y'){
//                             $row2_lembur_1 = 1.5;
//                         }else{
//                             $row2_lembur_1 = 1;
//                         }
// $explode2_lembur_2 = explode("|",$row2_pengerjaan->lembur);
//                         $explode2_status_lembur_2 = explode("-",$explode2_lembur_2[2]);
//                         if($explode2_status_lembur_2[1] == 'y'){
//                             $row2_lembur_2 = 1.5;
//                         }else{
//                             $row2_lembur_2 = 1;
//                         }
// $explode2_lembur_3 = explode("|",$row2_pengerjaan->lembur);
//                         $explode2_status_lembur_3 = explode("-",$explode2_lembur_3[3]);
//                         if($explode2_status_lembur_3[1] == 'y'){
//                             $row2_lembur_3 = 1.5;
//                         }else{
//                             $row2_lembur_3 = 1;
//                         }
// $explode2_lembur_4 = explode("|",$row2_pengerjaan->lembur);
//                         $explode2_status_lembur_4 = explode("-",$explode2_lembur_4[4]);
//                         if($explode2_status_lembur_4[1] == 'y'){
//                             $row2_lembur_4 = 1.5;
//                         }else{
//                             $row2_lembur_4 = 1;
//                         }
// $explode2_lembur_5 = explode("|",$row2_pengerjaan->lembur);
//                         $explode2_status_lembur_5 = explode("-",$explode2_lembur_5[5]);
//                         if($explode2_status_lembur_5[1] == 'y'){
//                             $row2_lembur_5 = 1.5;
//                         }else{
//                             $row2_lembur_5 = 1;
//                         }
//                     }
//                 }
//                 //End Ambri

//                 $row2_total_hasil_kerja = ($row2_hasil_kerja_1*$row2_lembur_2)+($row2_hasil_kerja_2*$row2_lembur_2)+($row2_hasil_kerja_3*$row2_lembur_3)+($row2_hasil_kerja_4*$row2_lembur_4)+($row2_hasil_kerja_5*$row2_lembur_5);
//                 // $row2_total_hasil_kerja = $row2_hasil_kerja_1+$row2_hasil_kerja_2+$row2_hasil_kerja_3+$row2_hasil_kerja_4+$row2_hasil_kerja_5;
//                 array_push($row2_upah_lembur,$row2_pengerjaan->uang_lembur);
//                 $hasil_gaji_row2 = $row2_total_hasil_kerja-$row2_pengerjaan->uang_lembur;
//                 array_push($row2_total_upah_hasil_kerja,$hasil_gaji_row2);
//             }

//             if ($data['new_data_pengerjaan']['akhir_bulan'] == 'y') {
//                 if (empty($row2_weekly->tunjangan_kerja)) {
//                     $row2_tunjangan_kerja = 0;
//                 }else{
//                     $row2_tunjangan_kerja = $row2_weekly->tunjangan_kerja;
//                 }
//             }else{
//                 $row2_tunjangan_kerja = 0;
//             }

//             if ($data['new_data_pengerjaan']['akhir_bulan'] == 'y') {
//                 if (empty($row2_weekly->tunjangan_kehadiran)) {
//                     $row2_tunjangan_kehadiran = 0;
//                 }else{
//                     $row2_tunjangan_kehadiran = $row2_weekly->tunjangan_kehadiran;
//                 }
//             }else{
//                 $row2_tunjangan_kehadiran = 0;
//             }

//             if (empty($row2_weekly->uang_makan)) {
//                 $row2_uang_makan = 0;
//             }else{
//                 $row2_uang_makan = $row2_weekly->uang_makan;
//             }

//             if (empty($row2_weekly->plus_1)) {
//                 $row2_plus_1 = 0;
//                 $row2_ket_plus_1 = null;
//             }else{
//                 $row2_explode_plus_1 = explode("|",$row2_weekly->plus_1);
//                 $row2_plus_1 = floatval($row2_explode_plus_1[0]);
//                 $row2_ket_plus_1 = $row2_explode_plus_1[1];
//             }

//             if (empty($row2_weekly->plus_2)) {
//                 $row2_plus_2 = 0;
//                 $row2_ket_plus_2 = null;
//             }else{
//                 $row2_explode_plus_2 = explode("|",$row2_weekly->plus_2);
//                 $row2_plus_2 = floatval($row2_explode_plus_2[0]);
//                 $row2_ket_plus_2 = $row2_explode_plus_2[1];
//             }

//             if (empty($row2_weekly->plus_3)) {
//                 $row2_plus_3 = 0;
//                 $row2_ket_plus_3 = null;
//             }else{
//                 $row2_explode_plus_3 = explode("|",$row2_weekly->plus_3);
//                 $row2_plus_3 = floatval($row2_explode_plus_3[0]);
//                 $row2_ket_plus_3 = $row2_explode_plus_3[1];
//             }

//             if (empty($row2_weekly->jht)) {
//                 $row2_jht = 0;
//             }else{
//                 $row2_jht = $row2_weekly->jht;
//             }

//             if (empty($row2_weekly->bpjs_kesehatan)) {
//                 $row2_bpjs_kesehatan = 0;
//             }else{
//                 $row2_bpjs_kesehatan = $row2_weekly->bpjs_kesehatan;
//             }

//             if (empty($row2_weekly->minus_1)) {
//                 $row2_minus_1 = '0';
//                 $row2_ket_minus_1 = null;
//             }else{
//                 $row2_explode_minus_1 = explode("|",$row2_weekly->minus_1);
//                 $row2_minus_1 = floatval($row2_explode_minus_1[0]);
//                 $row2_ket_minus_1 = $row2_explode_minus_1[1];
//             }

//             if (empty($row2_weekly->minus_2)) {
//                 $row2_minus_2 = 0;
//                 $row2_ket_minus_2 = null;
//             }else{
//                 $row2_explode_minus_2 = explode("|",$row2_weekly->minus_2);
//                 $row2_minus_2 = floatval($row2_explode_minus_2[0]);
//                 $row2_ket_minus_2 = $row2_explode_minus_2[1];
//             }

//             $row2_total_gaji_diterima = (array_sum($row2_total_upah_hasil_kerja)
//                                     +array_sum($row2_upah_lembur)
//                                     +$row2_tunjangan_kerja+
//                                     $row2_tunjangan_kehadiran+
//                                     $row2_uang_makan+
//                                     $row2_plus_1+
//                                     $row2_plus_2+
//                                     $row2_plus_3
//                                     )
//                                     -
//                                     ($row2_jht+$row2_bpjs_kesehatan+$row2_minus_1+$row2_minus_2)
//                                     ;

//             ###############endrow 2

//             // ### LINE 1 ###
//             $pdf->SetFont('Arial','B',8);
//             $pdf->Cell(35,5,'TANGGAL GAJI '.$qw->jenis_operator_detail_pekerjaan_id,'LT',0,'C'); 
//             $pdf->SetFont('Arial','',8);
//             $pdf->Cell(60,5,Carbon::parse($exp_tgl_awal[0] . '-' . $exp_tgl_awal[1] . '-' . $exp_tgl_awal[2])->isoFormat('D MMMM').' s/d '.Carbon::parse($exp_tgl_akhir[0] . '-' . $exp_tgl_akhir[1] . '-' . $exp_tgl_akhir[2])->isoFormat('D MMMM YYYY'),'TR',0,'L');
            
//             $pdf->Cell(2,5,'','LF',0,'L');
            
//             $pdf->SetFont('Arial','B',8);
//             $pdf->Cell(35,5,'TANGGAL GAJI '.$qw->jenis_operator_detail_pekerjaan_id,'LT',0,'C'); 
//             $pdf->SetFont('Arial','',8);
//             $pdf->Cell(60,5,Carbon::parse($exp_tgl_awal[0] . '-' . $exp_tgl_awal[1] . '-' . $exp_tgl_awal[2])->isoFormat('D MMMM').' s/d '.Carbon::parse($exp_tgl_akhir[0] . '-' . $exp_tgl_akhir[1] . '-' . $exp_tgl_akhir[2])->isoFormat('D MMMM YYYY'),'TR',0,'L');
//             $pdf->ln(3);

//             $pdf->SetFont('Arial','B',8);
//             $pdf->Cell(35,5,'Nama','L',0,'L'); 
//             $pdf->SetFont('Arial','B',7);
//             $pdf->Cell(60,5,strtoupper($row1_nama->nama).' ('.$row1_nama->nik.')','R',0,'L');
            
//             $pdf->Cell(2,5,'','LF',0,'L');

//             $pdf->SetFont('Arial','B',8);
//             $pdf->Cell(35,5,'Nama','L',0,'L'); 
//             $pdf->SetFont('Arial','B',7);
//             $pdf->Cell(60,5,strtoupper($row2_nama_2).' ('.$row2_nik.')','R',0,'L');
//             $pdf->ln(2.5);

//             $pdf->SetFont('Arial','B',8);
//             $pdf->Cell(35,5,'Departemen','L',0,'L'); 
//             $pdf->SetFont('Arial','B',7);
//             $pdf->Cell(60,5,strtoupper($qw->jenis_posisi_pekerjaan),'R',0,'L');

//             $pdf->Cell(2,5,'','LF',0,'L');

//             $pdf->SetFont('Arial','B',8);
//             $pdf->Cell(35,5,'Departemen','L',0,'L'); 
//             $pdf->SetFont('Arial','B',7);
//             $pdf->Cell(60,5,strtoupper($row2_jenis_posisi_pekerjaan),'R',0,'L');
//             $pdf->ln(3);

//             $pdf->SetFont('Arial','',8);
//             $pdf->Cell(35,5,'GAJI','L',0,'L'); 
//             $pdf->Cell(22,5,$a.' HARI','',0,'L');
//             $pdf->Cell(3,5,'Rp','',0,'L');
//             $pdf->Cell(35,5,number_format(array_sum($row1_total_upah_hasil_kerja),0,',','.'),'R',0,'R');
            
//             $pdf->Cell(2,5,'','LF',0,'L');

//             $pdf->SetFont('Arial','',8);
//             $pdf->Cell(35,5,'GAJI','L',0,'L'); 
//             $pdf->Cell(22,5,$a.' HARI','',0,'L');
//             $pdf->Cell(3,5,'Rp','',0,'L');
//             $pdf->Cell(35,5,number_format(array_sum($row2_total_upah_hasil_kerja),0,',','.'),'R',0,'R');
//             $pdf->ln(3);

//             $pdf->SetFont('Arial','',8);
//             $pdf->Cell(35,5,'Lembur','L',0,'L'); 
//             $pdf->Cell(22,5,'','',0,'L');
//             $pdf->Cell(3,5,'Rp','',0,'L');
//             $pdf->Cell(35,5,number_format(array_sum($row1_upah_lembur),0,',','.'),'R',0,'R');
            
//             $pdf->Cell(2,5,'','LF',0,'L');

//             $pdf->SetFont('Arial','',8);
//             $pdf->Cell(35,5,'Lembur','L',0,'L'); 
//             $pdf->Cell(22,5,'','',0,'L');
//             $pdf->Cell(3,5,'Rp','',0,'L');
//             $pdf->Cell(35,5,number_format(array_sum($row2_upah_lembur),0,',','.'),'R',0,'R');
//             $pdf->ln(3);

//             $pdf->SetFont('Arial','',8);
//             $pdf->Cell(35,5,'Insentif Kehadiran','L',0,'L'); 
//             $pdf->Cell(22,5,'','',0,'L');
//             $pdf->Cell(3,5,'Rp','',0,'L');
//             $pdf->Cell(35,5,number_format($row1_tunjangan_kehadiran,0,',','.'),'R',0,'R');
            
//             $pdf->Cell(2,5,'','LF',0,'L');
            
//             $pdf->SetFont('Arial','',8);
//             $pdf->Cell(35,5,'Insentif Kehadiran','L',0,'L'); 
//             $pdf->Cell(22,5,'','',0,'L');
//             $pdf->Cell(3,5,'Rp','',0,'L');
//             $pdf->Cell(35,5,number_format($row2_tunjangan_kehadiran,0,',','.'),'R',0,'R');
//             $pdf->ln(3);

//             $pdf->SetFont('Arial','',8);
//             $pdf->Cell(35,5,'Tunjangan Kerja','L',0,'L'); 
//             $pdf->Cell(22,5,'','',0,'L');
//             $pdf->Cell(3,5,'Rp','',0,'L');
//             $pdf->Cell(35,5,number_format($row1_tunjangan_kerja,0,',','.'),'R',0,'R');
            
//             $pdf->Cell(2,5,'','LF',0,'L');

//             $pdf->SetFont('Arial','',8);
//             $pdf->Cell(35,5,'Tunjangan Kerja','L',0,'L'); 
//             $pdf->Cell(22,5,'','',0,'L');
//             $pdf->Cell(3,5,'Rp','',0,'L');
//             $pdf->Cell(35,5,number_format($row2_tunjangan_kerja,0,',','.'),'R',0,'R');
//             $pdf->ln(3);

//             $pdf->SetFont('Arial','',8);
//             $pdf->Cell(12,5,'Plus','L',0,'L'); 
//             $pdf->Cell(45,5,"( ".$row1_ket_plus_1." )",'',0,'L');
//             $pdf->Cell(3,5,'Rp','',0,'L');
//             $pdf->Cell(35,5,number_format($row1_plus_1,0,',','.'),'R',0,'R');

//             $pdf->Cell(2,5,'','LF',0,'L');

//             $pdf->SetFont('Arial','',8);
//             $pdf->Cell(12,5,'Plus','L',0,'L'); 
//             $pdf->Cell(45,5,"( ".$row2_ket_plus_1." )",'',0,'L');
//             $pdf->Cell(3,5,'Rp','',0,'L');
//             $pdf->Cell(35,5,number_format($row2_plus_1,0,',','.'),'R',0,'R');
//             $pdf->ln(3);

//             $pdf->SetFont('Arial','',8);
//             $pdf->Cell(12,5,'','L',0,'L'); 
//             $pdf->Cell(45,5,"( ".$row1_ket_plus_2." )",'',0,'L');
//             $pdf->Cell(3,5,'Rp','',0,'L');
//             $pdf->Cell(35,5,number_format($row1_plus_2,0,',','.'),'R',0,'R');

//             $pdf->Cell(2,5,'','LF',0,'L');

//             $pdf->SetFont('Arial','',8);
//             $pdf->Cell(12,5,'','L',0,'L'); 
//             $pdf->Cell(45,5,"( ".$row2_ket_plus_2." )",'',0,'L');
//             $pdf->Cell(3,5,'Rp','',0,'L');
//             $pdf->Cell(35,5,number_format($row2_plus_2,0,',','.'),'R',0,'R');
//             $pdf->ln(3);

//             $pdf->SetFont('Arial','',8);
//             $pdf->Cell(12,5,'','L',0,'L'); 
//             $pdf->Cell(45,5,"( ".$row1_ket_plus_3." )",'',0,'L');
//             $pdf->Cell(3,5,'Rp','',0,'L');
//             $pdf->Cell(35,5,number_format($row1_plus_3,0,',','.'),'R',0,'R');

//             $pdf->Cell(2,5,'','LF',0,'L');

//             $pdf->SetFont('Arial','',8);
//             $pdf->Cell(12,5,'','L',0,'L'); 
//             $pdf->Cell(45,5,"( ".$row2_ket_plus_3." )",'',0,'L');
//             $pdf->Cell(3,5,'Rp','',0,'L');
//             $pdf->Cell(35,5,number_format($row2_plus_3,0,',','.'),'R',0,'R');
//             $pdf->ln(3);

//             $pdf->SetFont('Arial','',8);
//             $pdf->Cell(35,5,'Uang Makan','L',0,'L'); 
//             $pdf->Cell(22,5,'','',0,'L');
//             $pdf->Cell(3,5,'Rp','',0,'L');
//             $pdf->Cell(35,5,number_format($row1_uang_makan,0,',','.'),'R',0,'R');
//             $pdf->SetFont('Arial','',8);
            
//             $pdf->Cell(2,5,'','LF',0,'L');

//             $pdf->SetFont('Arial','',8);
//             $pdf->Cell(35,5,'Uang Makan','L',0,'L'); 
//             $pdf->Cell(22,5,'','',0,'L');
//             $pdf->Cell(3,5,'Rp','',0,'L');
//             $pdf->Cell(35,5,number_format($row2_uang_makan,0,',','.'),'R',0,'R');
//             $pdf->SetFont('Arial','',8);
//             $pdf->ln(4);

//             $pdf->SetFont('Arial','B',8);
//             $pdf->Cell(20,5,'POTONGAN','L',0,'L'); 
//             $pdf->Cell(37,5,'BPJS > JHT + JP','',0,'L');
//             $pdf->Cell(3,5,'Rp','',0,'L');
//             $pdf->Cell(35,5,number_format($row1_jht,0,',','.'),'R',0,'R');

//             $pdf->Cell(2,5,'','LF',0,'L');

//             $pdf->SetFont('Arial','B',8);
//             $pdf->Cell(20,5,'POTONGAN','L',0,'L'); 
//             $pdf->Cell(37,5,'BPJS > JHT + JP','',0,'L');
//             $pdf->Cell(3,5,'Rp','',0,'L');
//             $pdf->Cell(35,5,number_format($row2_jht,0,',','.'),'R',0,'R');
//             $pdf->ln(3);

//             $pdf->SetFont('Arial','B',8);
//             $pdf->Cell(20,5,'','L',0,'L'); 
//             $pdf->Cell(37,5,'BPJS Kesehatan','',0,'L');
//             $pdf->Cell(3,5,'Rp','',0,'L');
//             $pdf->Cell(35,5,number_format($row1_bpjs_kesehatan,0,',','.'),'R',0,'R');
            
//             $pdf->Cell(2,5,'','LF',0,'L');

//             $pdf->SetFont('Arial','B',8);
//             $pdf->Cell(20,5,'','L',0,'L'); 
//             $pdf->Cell(37,5,'BPJS Kesehatan','',0,'L');
//             $pdf->Cell(3,5,'Rp','',0,'L');
//             $pdf->Cell(35,5,number_format($row2_bpjs_kesehatan,0,',','.'),'R',0,'R');
//             $pdf->ln(3);

//             $pdf->SetFont('Arial','B',8);
//             $pdf->Cell(20,5,'','L',0,'L'); 
//             $pdf->Cell(37,5,"( ".$row1_ket_minus_1." )",'',0,'L');
//             $pdf->Cell(3,5,'Rp','',0,'L');
//             $pdf->Cell(35,5,number_format($row1_minus_1,0,',','.'),'R',0,'R');

//             $pdf->Cell(2,5,'','LF',0,'L');
            
//             $pdf->SetFont('Arial','B',8);
//             $pdf->Cell(20,5,'','L',0,'L'); 
//             $pdf->Cell(37,5,"( ".$row2_ket_minus_1." )",'',0,'L');
//             $pdf->Cell(3,5,'Rp','',0,'L');
//             $pdf->Cell(35,5,number_format($row2_minus_1,0,',','.'),'R',0,'R');
//             $pdf->ln(3);

//             $pdf->SetFont('Arial','B',8);
//             $pdf->Cell(20,5,'','L',0,'L'); 
//             $pdf->Cell(37,5,"( ".$row1_ket_minus_2." )",'',0,'L');
//             $pdf->Cell(3,5,'Rp','',0,'L');
//             $pdf->Cell(35,5,number_format($row1_minus_2,0,',','.'),'R',0,'R');

//             $pdf->Cell(2,5,'','LF',0,'L');
            
//             $pdf->SetFont('Arial','B',8);
//             $pdf->Cell(20,5,'','L',0,'L'); 
//             $pdf->Cell(37,5,"( ".$row2_ket_minus_2." )",'',0,'L');
//             $pdf->Cell(3,5,'Rp','',0,'L');
//             $pdf->Cell(35,5,number_format($row2_minus_2,0,',','.'),'R',0,'R');
//             $pdf->ln(5);

//             $pdf->SetFont('Arial','B',9);
//             $pdf->Cell(35,5,'TOTAL DITERIMA','BL',0,'L'); 
//             $pdf->Cell(22,5,'','B',0,'L');
//             $pdf->Cell(3,5,'Rp','B',0,'L');
//             $pdf->Cell(35,5,number_format($row1_total_gaji_diterima,0,',','.'),'BR',0,'R');

//             $pdf->Cell(2,5,'','LF',0,'L');

//             $pdf->SetFont('Arial','B',9);
//             $pdf->Cell(35,5,'TOTAL DITERIMA','BL',0,'L'); 
//             $pdf->Cell(22,5,'','B',0,'L');
//             $pdf->Cell(3,5,'Rp','B',0,'L');
//             $pdf->Cell(35,5,number_format($row2_total_gaji_diterima,0,',','.'),'BR',0,'R');
//             $pdf->ln(3);

//             // $no=$no+1;
//             // if ($no%5==0)
//             // {
//             //     $page=round($no/5);
//             //     $pdf->ln(2);
//             //     $pdf->SetFont('Arial','',7);
//             //     $pdf->Cell(35,5,"- page $page -",'',0,''); 
//             // }
//             $pdf->ln(3);
//         }

//         $pdf->Output('Slip Gaji Borongan '.Carbon::parse($exp_tgl_awal[0] . '-' . $exp_tgl_awal[1] . '-' . $exp_tgl_awal[2])->isoFormat('D MMMM').' sd '.Carbon::parse($exp_tgl_akhir[0] . '-' . $exp_tgl_akhir[1] . '-' . $exp_tgl_akhir[2])->isoFormat('D MMMM YYYY').'.pdf','I');
//         exit;
//     }

    public function borongan_bank($kode_pengerjaan)
    {
        $data['kode_pengerjaan'] = $kode_pengerjaan;
        // dd($data);
        $data['new_data_pengerjaan'] = $this->newDataPengerjaan->where('kode_pengerjaan',$kode_pengerjaan)->first();
        $data['karyawan_operators'] = $this->pengerjaanWeekly->select([
                                                      'pengerjaan_weekly.id as id',
                                                      'operator_karyawan.nik as nik',
                                                      'biodata_karyawan.nama as nama',
                                                      'biodata_karyawan.rekening as rekening',
                                                      'pengerjaan_weekly.upah_dasar as upah_dasar'
                                                    ])
                                                    ->leftJoin('operator_karyawan','operator_karyawan.id','=','pengerjaan_weekly.operator_karyawan_id')
                                                    ->leftJoin('itic_emp_new.biodata_karyawan','biodata_karyawan.nik','=','operator_karyawan.nik')
                                                    ->where('kode_pengerjaan',$kode_pengerjaan)
                                                    ->orderBy('biodata_karyawan.nama','asc')
                                                    ->get();
        $pdf = Pdf::loadView('backend.payrol.penggajian.borongan.bank',$data);
        return $pdf->stream();
    }

    public function borongan_weekly_report($kode_pengerjaan)
    {
        $data['kode_pengerjaan'] = $kode_pengerjaan;
        $data['new_data_pengerjaan'] = $this->newDataPengerjaan->where('kode_pengerjaan',$kode_pengerjaan)->first();
        $data['jenis_operator_detail_pengerjaans'] = $this->jenisOperatorDetailPengerjaan->where('jenis_operator_detail_id',1)->get();

        // $options = new Options();
        // $options->set('isJavascriptEnabled', TRUE);
        
        // $pdf = Pdf::loadView('backend.payrol.penggajian.borongan.weekly_report',$data);
        // $pdf->setOption('enable-javascript', true);
        // $pdf->setOption('enable-smart-shrinking', true);
        // $pdf->setPaper('a4', 'landscape');
        // return $pdf->stream();
        $view = view('backend.payrol.penggajian.borongan.weekly_report',$data);
        $pdf = \App::make('dompdf.wrapper');
        $pdf->loadHTML($view)->setPaper('a4','landscape')->setWarnings(false);
        return $pdf->stream();
        // return view('backend.payrol.penggajian.borongan.weekly_report',$data);
    }

    public function harian(Request $request)
    {
        if ($request->ajax()) {
            $data = $this->newDataPengerjaan->where('kode_pengerjaan','LIKE','%PH%')->get();
            return DataTables::of($data)
                            ->addIndexColumn()
                            ->addColumn('tanggal_penggajian', function($row){
                                $explode_tanggal_pengerjaans = explode("#",$row->tanggal);
                                foreach ($explode_tanggal_pengerjaans as $key => $explode_tanggal_pengerjaan) {
                                    if ($key != 0) {
                                        $hasil_tanggal_pengerjaan[] = Carbon::parse($explode_tanggal_pengerjaan)->isoFormat('D MMMM');
                                    }
                                }
                                return $hasil_tanggal_pengerjaan;
                            })
                            ->addColumn('status', function($row){
                                if ($row->status == 'y') {
                                    return '<span class="badge badge-outline-primary">Progress</span>';
                                }else{
                                    return '<span class="badge badge-outline-success">Selesai</span>';
                                }
                                return '-';
                            })
                            ->addColumn('action', function($row){
                                $btn = '';
                                // $btn .= '<div class="button-items">';
                                // $btn .= '<button class="btn btn-success btn-icon-circle btn-icon-circle-md"><i class="far fa-file-pdf"></i></button>';
                                // $btn .= '</div>';
                                $btn.=  '<div class="btn-group" role="group">';
                                $btn.=      '<a href='.route('payrol.harian.slip_gaji',['kode_pengerjaan' => $row->kode_pengerjaan]).' class="btn btn-success" target="_blank"><i class="far fa-file-pdf"></i> Slip Gaji<a>';
                                // $btn.=      '<a href='.route('payrol.harian.bank',['kode_pengerjaan' => $row->kode_pengerjaan]).' class="btn btn-primary" target="_blank"><i class="far fa-file-pdf"></i> Bank<a>';
                                // $btn.=      '<a href='.route('payrol.harian.weekly_report',['kode_pengerjaan' => $row->kode_pengerjaan]).' class="btn btn-danger" target="_blank"><i class="fas fa-book"></i> Weekly Report<a>';
                                if ($row->status != 'y') {
                                    $btn.=      '<a href='.route('payrol.harian.harian_detail_kirim_slip_gaji',['kode_pengerjaan' => $row->kode_pengerjaan]).' class="btn btn-primary" target="_blank"><i class="fas fa-envelope"></i> Detail Slip Gaji</a>';
                                }
                                $btn.=  '</div>';
                                return $btn;
                            })
                            // ->addColumn('action_report', function($row){
                            //     $btn = '';
                            //     $btn.=  '<div class="btn-group" role="group">';
                            //     $jenis_operator_detail_pengerjaans = JenisOperatorDetailPengerjaan::where('jenis_operator_detail_id',4)->get();
                            //     foreach ($jenis_operator_detail_pengerjaans as $jenis_operator_detail_pengerjaan) {
                            //         // $new_data_pengerjaan = NewDataPengerjaan::where('kode_pengerjaan','LIKE','%'.$jenis_operator->kode_operator.'%')->first();
                            //         $btn.='<a href="" class="btn btn-success tippy-btn" target="_blank" title="Download Excel '.$jenis_operator_detail_pengerjaan->jenis_posisi_pekerjaan.'" data-tippy-animation="perspective" data-tippy-arrow="true"><i class="far fa-file-pdf"></i></a>';
                            //     }
                            //     $btn.=  '</div>';
                            //     return $btn;
                            // })
                            ->rawColumns(['action','status'])
                            ->make(true);
        }
        return view('backend.payrol.penggajian.harian.index');
    }

    public function harian_slip_gaji($kode_pengerjaan)
    {
        $data['new_data_pengerjaan'] = $this->newDataPengerjaan->where('kode_pengerjaan',$kode_pengerjaan)->first();
        
        $explode_tanggal_pengerjaans = explode('#',$data['new_data_pengerjaan']['tanggal']);
        $exp_tanggals = array_filter($explode_tanggal_pengerjaans);
        $a = count($exp_tanggals);
        // dd($a);
        $exp_tgl_awal = explode('-', $exp_tanggals[1]);
        $exp_tgl_akhir = explode('-', $exp_tanggals[$a]);

        $jenis_operator_id = [];
        $jenis_pengerjaan_operator = $this->jenisOperatorDetailPengerjaan->where('id','>=',12)->orderBy('id','asc')->get();
        foreach ($jenis_pengerjaan_operator as $jpo) {
            $jenis_operator_id[] = $jpo->id;
        }
        // dd($jenis_operator_id);

        $first_weekly_id = $this->pengerjaanHarian->where('kode_pengerjaan',$kode_pengerjaan)->first();
        // dd($first_weekly_id);
        if ($first_weekly_id->id%2==0) {
            $ganjil_genap = 'MOD(pengerjaan_harian.id, 2) = 0';
        }else{
            $ganjil_genap = 'MOD(pengerjaan_harian.id, 2) <> 0';
        }

        $pengerjaan_harians = $this->pengerjaanHarian->select([
                                                'pengerjaan_harian.id as id',
                                                'pengerjaan_harian.operator_harian_karyawan_id as operator_harian_karyawan_id',
                                                'jenis_operator_detail_pekerjaan.jenis_posisi_pekerjaan as jenis_posisi_pekerjaan',
                                                'operator_harian_karyawan.nik as nik',
                                                'biodata_karyawan.nama as nama',
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
                                                'operator_harian_karyawan.jenis_operator_detail_pekerjaan_id as jenis_operator_detail_pekerjaan_id'
                                            ])
                                            ->leftJoin('operator_harian_karyawan','operator_harian_karyawan.id','=','pengerjaan_harian.operator_harian_karyawan_id')
                                            ->leftJoin('itic_emp_new.biodata_karyawan','biodata_karyawan.nik','=','operator_harian_karyawan.nik')
                                            ->leftJoin('jenis_operator_detail_pekerjaan','jenis_operator_detail_pekerjaan.id','=','operator_harian_karyawan.jenis_operator_detail_pekerjaan_id')
                                            ->where('pengerjaan_harian.kode_pengerjaan',$kode_pengerjaan)
                                            ->whereRaw($ganjil_genap)
                                            ->whereIn('operator_harian_karyawan.jenis_operator_detail_pekerjaan_id',$jenis_operator_id)
                                            ->orderBy('operator_harian_karyawan.jenis_operator_detail_pekerjaan_id','asc')
                                            ->orderBy('biodata_karyawan.nama','asc')
                                            // ->orderBy('pengerjaan_harian.id','asc')
                                            ->get();
        // dd($pengerjaan_harians);

        $pdf = new Fpdf('P', 'mm', 'A4');
        $pdf->AddPage();
        $pdf->SetFillColor(153,153,153);
        $pdf->SetTextColor(255);
        $pdf->SetDrawColor(0,0,0);
        $pdf->SetLineWidth(.3);
        $pdf->SetFont('Arial','B',8);
        $pdf->SetFillColor(224,235,255);
        $pdf->SetTextColor(0);
        $pdf->SetFont('Arial','',8);

        $no=0;
        foreach ($pengerjaan_harians as $key => $ph) {

            $row1_nama = $this->biodataKaryawan->where('nik',$ph->nik)->first();
            
            // dd($row2_weekly);
            // $row2_nama = BiodataKaryawan::where('nik',$row2_weekly->nik)->first();
            // dd($row2_nama);
            if (empty($ph->lembur)) {
                $hasil_lembur = 0;
                $lembur_1 = 0;
                $lembur_2 = 0;
            }else{
                $exlode_lembur = explode("|",$ph->lembur);
                if (empty($exlode_lembur)) {
                    $hasil_lembur = 0;
                    $lembur_1 = 0;
                    $lembur_2 = 0;
                }else{
                    $hasil_lembur = $exlode_lembur[0];
                    $lembur_1 = $exlode_lembur[1];
                    $lembur_2 = $exlode_lembur[2];
                }
            }
            // dd($lembur_1);
            $total_jam_lembur = floatval($lembur_1)+floatval($lembur_2);
            // dd($lembur_1);

            if (empty($ph->upah_dasar_weekly)) {
                $upah_dasar_weekly = 0;
            }else{
                $upah_dasar_weekly = $ph->upah_dasar_weekly;
            }

            if($data['new_data_pengerjaan']['akhir_bulan'] == 'y'){
                if (empty($ph->tunjangan_kehadiran)) {
                    $tunjangan_kehadiran = 0;
                }else{
                    $tunjangan_kehadiran = $ph->tunjangan_kehadiran;
                }
            }else{
                $tunjangan_kehadiran = 0;
            }

            if ($data['new_data_pengerjaan']['akhir_bulan'] == 'y') {
                if (empty($ph->tunjangan_kerja)) {
                    $tunjangan_kerja = 0;
                }else{
                    $tunjangan_kerja = $ph->tunjangan_kerja;
                }
            }else{
                $tunjangan_kerja = 0;
            }

            if (empty($ph->uang_makan)) {
                $uang_makan = 0;
            }else{
                $uang_makan = $ph->uang_makan;
            }

            if (empty($ph->plus_1)) {
                $plus_1 = 0;
                $ket_plus_1 = "";
            }else{
                $explode_plus_1 = explode("|",$ph->plus_1);
                $plus_1 = intval($explode_plus_1[0]);
                $ket_plus_1 = $explode_plus_1[1];
            }

            if (empty($ph->plus_2)) {
                $plus_2 = 0;
                $ket_plus_2 = "";
            }else{
                $explode_plus_2 = explode("|",$ph->plus_2);
                $plus_2 = intval($explode_plus_2[0]);
                $ket_plus_2 = $explode_plus_2[1];
            }

            if (empty($ph->plus_3)) {
                $plus_3 = 0;
                $ket_plus_3 = "";
            }else{
                $explode_plus_3 = explode("|",$ph->plus_3);
                $plus_3 = intval($explode_plus_3[0]);
                $ket_plus_3 = $explode_plus_3[1];
            }

            if (empty($ph->minus_1)) {
                $minus_1 = 0;
                $ket_minus_1 = "";
            }else{
                $explode_minus_1 = explode("|",$ph->minus_1);
                if (empty($explode_minus_1[0])) {
                    $minus_1 = 0;
                }else{
                    $minus_1 = intval($explode_minus_1[0]);
                }
                $ket_minus_1 = $explode_minus_1[1];
            }

            if (empty($ph->minus_2)) {
                $minus_2 = 0;
                $ket_minus_2 = "";
            }else{
                $explode_minus_2 = explode("|",$ph->minus_2);
                if (empty($explode_minus_2[0])) {
                    $minus_2 = 0;
                }else{
                    $minus_2 = intval($explode_minus_2[0]);
                }
                $ket_minus_2 = $explode_minus_2[1];
            }

            if (empty($ph->jht)) {
                $jht = 0;
            }else{
                $jht = intval($ph->jht);
            }

            if (empty($ph->bpjs_kesehatan)) {
                $bpjs_kesehatan = 0;
            }else{
                $bpjs_kesehatan = intval($ph->bpjs_kesehatan);
            }

            $total_gaji_diterima = ($ph->upah_dasar_weekly+$hasil_lembur+$tunjangan_kehadiran+$tunjangan_kerja+
                                    $plus_1+$plus_2+$plus_3+$ph->uang_makan)-
                                    ($jht+$bpjs_kesehatan+$minus_1+$minus_2);
            
            $id_selanjutnya = $ph->id+1;
            // dd($id_selanjutnya);
            $row2_weekly = $this->pengerjaanHarian->select([
                                                'pengerjaan_harian.id as id',
                                                'pengerjaan_harian.operator_harian_karyawan_id as operator_harian_karyawan_id',
                                                'jenis_operator_detail_pekerjaan.jenis_posisi_pekerjaan as jenis_posisi_pekerjaan',
                                                'operator_harian_karyawan.nik as nik',
                                                'biodata_karyawan.nama as nama',
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
                                                'operator_harian_karyawan.jenis_operator_detail_pekerjaan_id as jenis_operator_detail_pekerjaan_id'
                                            ])
                                            ->leftJoin('operator_harian_karyawan','operator_harian_karyawan.id','=','pengerjaan_harian.operator_harian_karyawan_id')
                                            ->leftJoin('itic_emp_new.biodata_karyawan','biodata_karyawan.nik','=','operator_harian_karyawan.nik')
                                            ->leftJoin('jenis_operator_detail_pekerjaan','jenis_operator_detail_pekerjaan.id','=','operator_harian_karyawan.jenis_operator_detail_pekerjaan_id')
                                            ->whereIn('operator_harian_karyawan.jenis_operator_detail_pekerjaan_id',$jenis_operator_id)
                                            ->where('pengerjaan_harian.id',$id_selanjutnya)
                                            ->where('pengerjaan_harian.kode_pengerjaan',$kode_pengerjaan)
                                            ->orderBy('operator_harian_karyawan.jenis_operator_detail_pekerjaan_id','asc')
                                            ->orderBy('biodata_karyawan.nama','asc')
                                            ->first();
            // dd($row2_weekly);

            if (empty($row2_weekly)) {
                $row2_nama = null;
                $row2_nik = null;
                $row2_hari_kerja = null;
                $row2_upah_dasar = null;
                $row2_upah_dasar_weekly = null;
                $row2_hasil_lembur = 0;
                $row2_lembur_1 = 0;
                $row2_lembur_2 = 0;
                $row2_jenis_posisi_pekerjaan = 0;
            }else{
                $row2_nama = $row2_weekly->nama;
                $row2_nik = $row2_weekly->nik;
                $row2_hari_kerja = $row2_weekly->hari_kerja;
                $row2_upah_dasar = $row2_weekly->upah_dasar;
                $row2_upah_dasar_weekly = $row2_weekly->upah_dasar_weekly;
                $row2_jenis_posisi_pekerjaan = $row2_weekly->jenis_posisi_pekerjaan;
                // $row2_lembur = $row2_weekly->lembur;
                if (empty($row2_weekly->lembur)) {
                    $row2_hasil_lembur = 0;
                    $row2_lembur_1 = 0;
                    $row2_lembur_2 = 0;
                    $row2_total_jam_lembur = 0;
                    $row2_tunjangan_kehadiran = 0;
                    $row2_tunjangan_kerja = 0;
                    $row2_uang_makan = 0;
                    $row2_plus_1 = 0;
                    $row2_ket_plus_1 = "";
                    $row2_plus_2 = 0;
                    $row2_ket_plus_2 = "";
                    $row2_plus_3 = 0;
                    $row2_ket_plus_3 = "";
                    $row2_minus_1 = 0;
                    $row2_ket_minus_1 = "";
                    $row2_minus_2 = 0;
                    $row2_ket_minus_2 = "";
                    $row2_jht = 0;
                    $row2_bpjs_kesehatan = 0;
                    $row2_total_gaji_diterima = 0;
                }else{
                    $row2_exlode_lembur = explode("|",$row2_weekly->lembur);
                    if (empty($row2_exlode_lembur)) {
                        $row2_hasil_lembur = 0;
                        $row2_lembur_1 = 0;
                        $row2_lembur_2 = 0;
                    }else{
                        $row2_hasil_lembur = $row2_exlode_lembur[0];
                        $row2_lembur_1 = $row2_exlode_lembur[1];
                        $row2_lembur_2 = $row2_exlode_lembur[2];
                    }

                    $row2_total_jam_lembur = floatval($row2_lembur_1)+floatval($row2_lembur_2);

                    if($data['new_data_pengerjaan']['akhir_bulan'] == 'y'){
                        if (empty($row2_weekly->tunjangan_kehadiran)) {
                            $row2_tunjangan_kehadiran = 0;
                        }else{
                            $row2_tunjangan_kehadiran = $row2_weekly->tunjangan_kehadiran;
                        }
                    }else{
                        $row2_tunjangan_kehadiran = 0;
                    }

                    if($data['new_data_pengerjaan']['akhir_bulan'] == 'y'){
                        if (empty($row2_weekly->tunjangan_kerja)) {
                            $row2_tunjangan_kerja = 0;
                        }else{
                            $row2_tunjangan_kerja = $row2_weekly->tunjangan_kerja;
                        }
                    }else{
                        $row2_tunjangan_kerja = 0;
                    }
        
                    if (empty($row2_weekly->uang_makan)) {
                        $row2_uang_makan = 0;
                    }else{
                        $row2_uang_makan = $row2_weekly->uang_makan;
                    }

                    if (empty($row2_weekly->plus_1)) {
                        $row2_plus_1 = 0;
                        $row2_ket_plus_1 = "";
                    }else{
                        $row2_explode_plus_1 = explode("|",$row2_weekly->plus_1);
                        $row2_plus_1 = intval($row2_explode_plus_1[0]);
                        $row2_ket_plus_1 = $row2_explode_plus_1[1];
                    }
        
                    if (empty($row2_weekly->plus_2)) {
                        $row2_plus_2 = 0;
                        $row2_ket_plus_2 = "";
                    }else{
                        $row2_explode_plus_2 = explode("|",$row2_weekly->plus_2);
                        $row2_plus_2 = intval($row2_explode_plus_2[0]);
                        $row2_ket_plus_2 = $row2_explode_plus_2[1];
                    }
        
                    if (empty($row2_weekly->plus_3)) {
                        $row2_plus_3 = 0;
                        $row2_ket_plus_3 = "";
                    }else{
                        $row2_explode_plus_3 = explode("|",$row2_weekly->plus_3);
                        $row2_plus_3 = intval($row2_explode_plus_3[0]);
                        $row2_ket_plus_3 = $row2_explode_plus_3[1];
                    }

                    if (empty($row2_weekly->minus_1)) {
                        $row2_minus_1 = 0;
                        $row2_ket_minus_1 = "";
                    }else{
                        $row2_explode_minus_1 = explode("|",$row2_weekly->minus_1);
                        if (empty($row2_explode_minus_1[0])) {
                            $row2_minus_1 = 0;
                        }else{
                            $row2_minus_1 = intval($row2_explode_minus_1[0]);
                        }
                        $row2_ket_minus_1 = $row2_explode_minus_1[1];
                    }
        
                    if (empty($row2_weekly->minus_2)) {
                        $row2_minus_2 = 0;
                        $row2_ket_minus_2 = "";
                    }else{
                        $row2_explode_minus_2 = explode("|",$row2_weekly->minus_2);
                        if (empty($row2_explode_minus_2[0])) {
                            $row2_minus_2 = 0;
                        }else{
                            $row2_minus_2 = intval($row2_explode_minus_2[0]);
                        }
                        $row2_ket_minus_2 = $row2_explode_minus_2[1];
                    }

                    if (empty($row2_weekly->jht)) {
                        $row2_jht = 0;
                    }else{
                        $row2_jht = intval($row2_weekly->jht);
                    }
        
                    if (empty($row2_weekly->bpjs_kesehatan)) {
                        $row2_bpjs_kesehatan = 0;
                    }else{
                        $row2_bpjs_kesehatan = intval($row2_weekly->bpjs_kesehatan);
                    }

                    $row2_total_gaji_diterima = ($row2_weekly->upah_dasar_weekly+$row2_hasil_lembur+$row2_tunjangan_kehadiran+$row2_tunjangan_kerja+
                                    $row2_plus_1+$row2_plus_2+$row2_plus_3+$row2_weekly->uang_makan)-
                                    ($row2_jht+$row2_bpjs_kesehatan+$row2_minus_1+$row2_minus_2);
                }
            }

            // $row2_nama = $row2_weekly[0];

            $pdf->SetFont('Arial','B',8);
            $pdf->Cell(35,5,'TANGGAL GAJI','LT',0,'C'); 
            $pdf->SetFont('Arial','',8);
            $pdf->Cell(60,5,Carbon::parse($exp_tgl_awal[0] . '-' . $exp_tgl_awal[1] . '-' . $exp_tgl_awal[2])->isoFormat('D MMMM').' s/d '.Carbon::parse($exp_tgl_akhir[0] . '-' . $exp_tgl_akhir[1] . '-' . $exp_tgl_akhir[2])->isoFormat('D MMMM YYYY'),'TR',0,'L');
            
            $pdf->Cell(2,5,'','LF',0,'L');
            
            $pdf->SetFont('Arial','B',8);
            $pdf->Cell(35,5,'TANGGAL GAJI','LT',0,'C'); 
            $pdf->SetFont('Arial','',8);
            $pdf->Cell(60,5,Carbon::parse($exp_tgl_awal[0] . '-' . $exp_tgl_awal[1] . '-' . $exp_tgl_awal[2])->isoFormat('D MMMM').' s/d '.Carbon::parse($exp_tgl_akhir[0] . '-' . $exp_tgl_akhir[1] . '-' . $exp_tgl_akhir[2])->isoFormat('D MMMM YYYY'),'TR',0,'L');
            $pdf->ln(3);

            $pdf->SetFont('Arial','B',8);
            $pdf->Cell(35,5,'Nama','L',0,'L'); 
            $pdf->SetFont('Arial','B',7);
            $pdf->Cell(60,5,strtoupper($row1_nama->nama).' ('.$row1_nama->nik.')','R',0,'L');
            
            $pdf->Cell(2,5,'','LF',0,'L');

            $pdf->SetFont('Arial','B',8);
            $pdf->Cell(35,5,'Nama','L',0,'L'); 
            $pdf->SetFont('Arial','B',7);
            // $pdf->Cell(60,5,strtoupper('').' ('.''.')','R',0,'L');
            $pdf->Cell(60,5,strtoupper($row2_nama).' ('.$row2_nik.')','R',0,'L');
            $pdf->ln(2.5);


            $pdf->SetFont('Arial','B',8);
            $pdf->Cell(35,5,'Departemen','L',0,'L'); 
            $pdf->SetFont('Arial','B',7);
            $pdf->Cell(60,5,strtoupper($ph->jenis_posisi_pekerjaan),'R',0,'L');
            
            $pdf->Cell(2,5,'','LF',0,'L');

            $pdf->SetFont('Arial','B',8);
            $pdf->Cell(35,5,'Departemen','L',0,'L'); 
            $pdf->SetFont('Arial','B',7);
            // $pdf->Cell(60,5,strtoupper('').' ('.''.')','R',0,'L');
            $pdf->Cell(60,5,strtoupper($row2_jenis_posisi_pekerjaan),'R',0,'L');
            $pdf->ln(3);


            $pdf->SetFont('Arial','',8);
            $pdf->Cell(35,5,'GAJI','L',0,'L'); 
            $pdf->Cell(22,5,$ph->hari_kerja.' HARI','',0,'L');
            $pdf->Cell(3,5,'Rp','',0,'L');
            $pdf->Cell(35,5,number_format($upah_dasar_weekly,0,',','.'),'R',0,'R');

            $pdf->Cell(2,5,'','LF',0,'L');

            $pdf->SetFont('Arial','',8);
            $pdf->Cell(35,5,'GAJI','L',0,'L'); 
            // $pdf->Cell(22,5,''.' HARI','',0,'L');
            $pdf->Cell(22,5,$row2_hari_kerja.' HARI','',0,'L');
            $pdf->Cell(3,5,'Rp','',0,'L');
            $pdf->Cell(35,5,number_format($row2_upah_dasar_weekly,0,',','.'),'R',0,'R');
            // $pdf->Cell(35,5,number_format(0,0,',','.'),'R',0,'R');
            $pdf->ln(3);

            $pdf->SetFont('Arial','',8);
            $pdf->Cell(35,5,'Lembur '.'( '.$total_jam_lembur.' Jam )','L',0,'L'); 
            $pdf->Cell(22,5,'','',0,'L');
            $pdf->Cell(3,5,'Rp','',0,'L');
            $pdf->Cell(35,5,number_format($hasil_lembur,0,',','.'),'R',0,'R');

            $pdf->Cell(2,5,'','LF',0,'L');

            $pdf->SetFont('Arial','',8);
            $pdf->Cell(35,5,'Lembur '.'( '.$row2_total_jam_lembur.' Jam )','L',0,'L'); 
            // $pdf->Cell(35,5,'Lembur '.'( '.''.' Jam )','L',0,'L'); 
            $pdf->Cell(22,5,'','',0,'L');
            $pdf->Cell(3,5,'Rp','',0,'L');
            $pdf->Cell(35,5,number_format($row2_hasil_lembur,0,',','.'),'R',0,'R');
            // $pdf->Cell(35,5,number_format(0,0,',','.'),'R',0,'R');
            $pdf->ln(3);

            $pdf->SetFont('Arial','',8);
            $pdf->Cell(35,5,'Insentif Kehadiran','L',0,'L'); 
            $pdf->Cell(22,5,'','',0,'L');
            $pdf->Cell(3,5,'Rp','',0,'L');
            $pdf->Cell(35,5,number_format($tunjangan_kehadiran,0,',','.'),'R',0,'R');
            
            $pdf->Cell(2,5,'','LF',0,'L');

            $pdf->SetFont('Arial','',8);
            $pdf->Cell(35,5,'Insentif Kehadiran','L',0,'L'); 
            $pdf->Cell(22,5,'','',0,'L');
            $pdf->Cell(3,5,'Rp','',0,'L');
            $pdf->Cell(35,5,number_format($row2_tunjangan_kehadiran,0,',','.'),'R',0,'R');
            // $pdf->Cell(35,5,number_format(0,0,',','.'),'R',0,'R');
            $pdf->ln(3);

            $pdf->SetFont('Arial','',8);
            $pdf->Cell(35,5,'Tunjangan Kerja','L',0,'L'); 
            $pdf->Cell(22,5,'','',0,'L');
            $pdf->Cell(3,5,'Rp','',0,'L');
            $pdf->Cell(35,5,number_format($tunjangan_kerja,0,',','.'),'R',0,'R');

            $pdf->Cell(2,5,'','LF',0,'L');

            $pdf->SetFont('Arial','',8);
            $pdf->Cell(35,5,'Tunjangan Kerja','L',0,'L'); 
            $pdf->Cell(22,5,'','',0,'L');
            $pdf->Cell(3,5,'Rp','',0,'L');
            $pdf->Cell(35,5,number_format($row2_tunjangan_kerja,0,',','.'),'R',0,'R');
            // $pdf->Cell(35,5,number_format(0,0,',','.'),'R',0,'R');
            $pdf->ln(3);

            $pdf->SetFont('Arial','',8);
            $pdf->Cell(12,5,'Plus','L',0,'L'); 
            $pdf->Cell(45,5,"( ".$ket_plus_1." )",'',0,'L');
            $pdf->Cell(3,5,'Rp',0,0,'L');
            $pdf->Cell(35,5,number_format($plus_1,0,',','.'),'R',0,'R');

            $pdf->Cell(2,5,'','LF',0,'L');

            $pdf->SetFont('Arial','',8);
            $pdf->Cell(12,5,'Plus','L',0,'L'); 
            $pdf->Cell(45,5,"( ".$row2_ket_plus_1." )",'',0,'L');
            // $pdf->Cell(45,5,"( ".''." )",'',0,'L');
            $pdf->Cell(3,5,'Rp',0,0,'L');
            $pdf->Cell(35,5,number_format($row2_plus_1,0,',','.'),'R',0,'R');
            // $pdf->Cell(35,5,number_format(0,0,',','.'),'R',0,'R');
            $pdf->ln(3);

            $pdf->SetFont('Arial','',8);
            $pdf->Cell(12,5,'','L',0,'L'); 
            $pdf->Cell(45,5,"( ".$ket_plus_2." )",'',0,'L');
            $pdf->Cell(3,5,'Rp','',0,'L');
            $pdf->Cell(35,5,number_format($plus_2,0,',','.'),'R',0,'R');

            $pdf->Cell(2,5,'','LF',0,'L');

            $pdf->SetFont('Arial','',8);
            $pdf->Cell(12,5,'','L',0,'L'); 
            $pdf->Cell(45,5,"( ".$row2_ket_plus_2." )",'',0,'L');
            // $pdf->Cell(45,5,"( ".''." )",'',0,'L');
            $pdf->Cell(3,5,'Rp','',0,'L');
            $pdf->Cell(35,5,number_format($row2_plus_2,0,',','.'),'R',0,'R');
            $pdf->ln(3);

            $pdf->SetFont('Arial','',8);
            $pdf->Cell(12,5,'','L',0,'L'); 
            $pdf->Cell(45,5,"( ".$ket_plus_3." )",'',0,'L');
            $pdf->Cell(3,5,'Rp','',0,'L');
            $pdf->Cell(35,5,number_format($plus_3,0,',','.'),'R',0,'R');

            $pdf->Cell(2,5,'','LF',0,'L');

            $pdf->Cell(12,5,'','L',0,'L'); 
            $pdf->Cell(45,5,"( ".$row2_ket_plus_3." )",'',0,'L');
            // $pdf->Cell(45,5,"( ".''." )",'',0,'L');
            $pdf->Cell(3,5,'Rp','',0,'L');
            $pdf->Cell(35,5,number_format($row2_plus_3,0,',','.'),'R',0,'R');
            // $pdf->Cell(35,5,number_format(0,0,',','.'),'R',0,'R');
            $pdf->ln(3);

            $pdf->Cell(35,5,'Uang Makan','L',0,'L'); 
            $pdf->Cell(22,5,'','',0,'L');
            $pdf->Cell(3,5,'Rp','',0,'L');
            $pdf->Cell(35,5,number_format($uang_makan,0,',','.'),'R',0,'R');
            
            $pdf->Cell(2,5,'','LF',0,'L');
            
            $pdf->Cell(35,5,'Uang Makan','L',0,'L'); 
            $pdf->Cell(22,5,'','',0,'L');
            $pdf->Cell(3,5,'Rp','',0,'L');
            $pdf->Cell(35,5,number_format($row2_uang_makan,0,',','.'),'R',0,'R');
            // $pdf->Cell(35,5,number_format(0,0,',','.'),'R',0,'R');
            $pdf->ln(4);

            $pdf->SetFont('Arial','B',8);
            $pdf->Cell(20,5,'POTONGAN','L',0,'L'); 
            $pdf->Cell(37,5,'BPJS > JHT + JP','',0,'L');
            $pdf->Cell(3,5,'Rp','',0,'L');
            $pdf->Cell(35,5,number_format($jht,0,',','.'),'R',0,'R');

            $pdf->Cell(2,5,'','LF',0,'L');

            $pdf->SetFont('Arial','B',8);
            $pdf->Cell(20,5,'POTONGAN','L',0,'L'); 
            $pdf->Cell(37,5,'BPJS > JHT + JP','',0,'L');
            $pdf->Cell(3,5,'Rp','',0,'L');
            $pdf->Cell(35,5,number_format($row2_jht,0,',','.'),'R',0,'R');
            // $pdf->Cell(35,5,number_format(0,0,',','.'),'R',0,'R');
            $pdf->ln(3);

            $pdf->SetFont('Arial','B',8);
            $pdf->Cell(20,5,'','L',0,'L'); 
            $pdf->Cell(37,5,'BPJS Kesehatan','',0,'L');
            $pdf->Cell(3,5,'Rp','',0,'L');
            $pdf->Cell(35,5,number_format($bpjs_kesehatan,0,',','.'),'R',0,'R');

            $pdf->Cell(2,5,'','LF',0,'L');

            $pdf->Cell(20,5,'','L',0,'L'); 
            $pdf->Cell(37,5,'BPJS Kesehatan','',0,'L');
            $pdf->Cell(3,5,'Rp','',0,'L');
            $pdf->Cell(35,5,number_format($row2_bpjs_kesehatan,0,',','.'),'R',0,'R');
            // $pdf->Cell(35,5,number_format(0,0,',','.'),'R',0,'R');
            $pdf->ln(3);

            $pdf->SetFont('Arial','B',8);
            $pdf->Cell(20,5,'','L',0,'L'); 
            $pdf->Cell(37,5,"( ".$ket_minus_1." )",'',0,'L');
            $pdf->Cell(3,5,'Rp','',0,'L');
            $pdf->Cell(35,5,number_format($minus_1,0,',','.'),'R',0,'R');

            $pdf->Cell(2,5,'','LF',0,'L');
            
            $pdf->SetFont('Arial','B',8);
            $pdf->Cell(20,5,'','L',0,'L'); 
            $pdf->Cell(37,5,"( ".$row2_ket_minus_1." )",'',0,'L');
            // $pdf->Cell(37,5,"( ".''." )",'',0,'L');
            $pdf->Cell(3,5,'Rp','',0,'L');
            $pdf->Cell(35,5,number_format($row2_minus_1,0,',','.'),'R',0,'R');
            // $pdf->Cell(35,5,number_format(0,0,',','.'),'R',0,'R');
            $pdf->ln(3);

            $pdf->SetFont('Arial','B',8);
            $pdf->Cell(20,5,'','L',0,'L'); 
            $pdf->Cell(37,5,"( ".$ket_minus_2." )",'',0,'L');
            $pdf->Cell(3,5,'Rp','',0,'L');
            $pdf->Cell(35,5,number_format($minus_2,0,',','.'),'R',0,'R');

            $pdf->Cell(2,5,'','LF',0,'L');

            $pdf->SetFont('Arial','B',8);
            $pdf->Cell(20,5,'','L',0,'L'); 
            $pdf->Cell(37,5,"( ".$row2_ket_minus_2." )",'',0,'L');
            // $pdf->Cell(37,5,"( ".''." )",'',0,'L');
            $pdf->Cell(3,5,'Rp','',0,'L');
            $pdf->Cell(35,5,number_format($row2_minus_2,0,',','.'),'R',0,'R');
            $pdf->ln(5);

            $pdf->SetFont('Arial','B',9);
            $pdf->Cell(35,5,'TOTAL DITERIMA','BL',0,'L'); 
            $pdf->Cell(22,5,'','B',0,'L');
            $pdf->Cell(3,5,'Rp','B',0,'L');
            $pdf->Cell(35,5,number_format($total_gaji_diterima,0,',','.'),'BR',0,'R');

            $pdf->Cell(2,5,'','LF',0,'L');

            $pdf->SetFont('Arial','B',9);
            $pdf->Cell(35,5,'TOTAL DITERIMA','BL',0,'L'); 
            $pdf->Cell(22,5,'','B',0,'L');
            $pdf->Cell(3,5,'Rp','B',0,'L');
            $pdf->Cell(35,5,number_format($row2_total_gaji_diterima,0,',','.'),'BR',0,'R');
            // $pdf->Cell(35,5,number_format(0,0,',','.'),'BR',0,'R');
            $pdf->ln(3);

            // $no=$no+1;
            // if ($no%5==0)
            // {
            //     $page=round($no/5);
            //     $pdf->ln(2);
            //     $pdf->SetFont('Arial','',7);
            //     $pdf->Cell(35,5,"- page $page -",'',0,''); 
            // }
            $pdf->ln(3);
        }
        
        $pdf->Output('Slip Gaji Harian '.Carbon::parse($exp_tgl_awal[0] . '-' . $exp_tgl_awal[1] . '-' . $exp_tgl_awal[2])->isoFormat('D MMMM').' sd '.Carbon::parse($exp_tgl_akhir[0] . '-' . $exp_tgl_akhir[1] . '-' . $exp_tgl_akhir[2])->isoFormat('D MMMM YYYY').'.pdf','I');
        exit;
    }

    public function harian_bank($kode_pengerjaan)
    {
        $data['kode_pengerjaan'] = $kode_pengerjaan;
        $data['new_data_pengerjaan'] = $this->newDataPengerjaan->where('kode_pengerjaan',$kode_pengerjaan)->first();
        $data['pengerjaan_harians'] = $this->pengerjaanHarian->select([
                                                        'pengerjaan_harian.id as id',
                                                        'operator_harian_karyawan.nik as nik',
                                                        'biodata_karyawan.nama as nama',
                                                        'pengerjaan_harian.upah_dasar_weekly as upah_dasar_weekly',
                                                    ])
                                                    ->leftJoin('operator_harian_karyawan','operator_harian_karyawan.id','=','pengerjaan_harian.operator_harian_karyawan_id')
                                                    ->leftJoin('itic_emp_new.biodata_karyawan','biodata_karyawan.nik','=','operator_harian_karyawan.nik')
                                                    ->where('pengerjaan_harian.kode_pengerjaan',$kode_pengerjaan)
                                                    ->orderBy('biodata_karyawan.nama','asc')
                                                    ->get();
        $pdf = Pdf::loadView('backend.payrol.penggajian.harian.bank',$data);
        return $pdf->stream();
    }

    public function harian_weekly_report($kode_pengerjaan)
    {
        $data['kode_pengerjaan'] = $kode_pengerjaan;
        $data['new_data_pengerjaan'] = $this->newDataPengerjaan->where('kode_pengerjaan',$kode_pengerjaan)->first();
        $data['jenis_operator_detail_pengerjaans'] = $this->jenisOperatorDetailPengerjaan->where('jenis_operator_detail_id',4)->get();

        $pdf = Pdf::loadView('backend.payrol.penggajian.harian.weekly_report',$data);
        $pdf->setPaper('a4','landscape')->setWarnings(false);
        return $pdf->stream();
        // return view('backend.payrol.penggajian.harian.weekly_report',$data);
    }

    public function supir_rit(Request $request)
    {
        if ($request->ajax()) {
            $data = $this->newDataPengerjaan->where('kode_pengerjaan','LIKE','%PS%')->get();
            return DataTables::of($data)
                            ->addIndexColumn()
                            ->addColumn('tanggal_penggajian', function($row){
                                $explode_tanggal_pengerjaans = explode("#",$row->tanggal);
                                foreach ($explode_tanggal_pengerjaans as $key => $explode_tanggal_pengerjaan) {
                                    if ($key != 0) {
                                        $hasil_tanggal_pengerjaan[] = Carbon::parse($explode_tanggal_pengerjaan)->isoFormat('D MMMM');
                                    }
                                }
                                return $hasil_tanggal_pengerjaan;
                            })
                            ->addColumn('status', function($row){
                                if ($row->status == 'y') {
                                    return '<span class="badge badge-outline-primary">Progress</span>';
                                }else{
                                    return '<span class="badge badge-outline-success">Selesai</span>';
                                }
                                return '-';
                            })
                            ->addColumn('action', function($row){
                                $btn = '';
                                // $btn .= '<div class="button-items">';
                                // $btn .= '<button class="btn btn-success btn-icon-circle btn-icon-circle-md"><i class="far fa-file-pdf"></i></button>';
                                // $btn .= '</div>';
                                $btn.=  '<div class="btn-group" role="group">';
                                $btn.=      '<a href='.route('payrol.supir_rit.slip_gaji',['kode_pengerjaan' => $row->kode_pengerjaan]).' class="btn btn-success" target="_blank"><i class="far fa-file-pdf"></i> Slip Gaji<a>';
                                // $btn.=      '<a href='.route('payrol.supir_rit.bank',['kode_pengerjaan' => $row->kode_pengerjaan]).' class="btn btn-primary" target="_blank"><i class="far fa-file-pdf"></i> Bank<a>';
                                // $btn.=      '<a href='.route('payrol.supir_rit.weekly_report',['kode_pengerjaan' => $row->kode_pengerjaan]).' class="btn btn-danger" target="_blank"><i class="fas fa-book"></i> Weekly Report<a>';
                                if ($row->status != 'y') {
                                    $btn.=      '<a href='.route('payrol.supir_rit.supir_rit_detail_kirim_slip_gaji',['kode_pengerjaan' => $row->kode_pengerjaan]).' class="btn btn-primary" target="_blank"><i class="fas fa-envelope"></i> Detail Slip Gaji</a>';
                                }
                                $btn.=  '</div>';
                                return $btn;
                            })
                            // ->addColumn('action_report', function($row){
                            //     $btn = '';
                            //     $btn.=  '<div class="btn-group" role="group">';
                            //     $jenis_operator_detail_pengerjaans = JenisOperatorDetailPengerjaan::where('jenis_operator_detail_id',4)->get();
                            //     foreach ($jenis_operator_detail_pengerjaans as $jenis_operator_detail_pengerjaan) {
                            //         // $new_data_pengerjaan = NewDataPengerjaan::where('kode_pengerjaan','LIKE','%'.$jenis_operator->kode_operator.'%')->first();
                            //         $btn.='<a href="" class="btn btn-success tippy-btn" target="_blank" title="Download Excel '.$jenis_operator_detail_pengerjaan->jenis_posisi_pekerjaan.'" data-tippy-animation="perspective" data-tippy-arrow="true"><i class="far fa-file-pdf"></i></a>';
                            //     }
                            //     $btn.=  '</div>';
                            //     return $btn;
                            // })
                            ->rawColumns(['action','status'])
                            ->make(true);
        }
        return view('backend.payrol.penggajian.supir_rit.index');
    }

    public function supir_rit_slip_gaji($kode_pengerjaan)
    {
        $data['new_data_pengerjaan'] = $this->newDataPengerjaan->where('kode_pengerjaan',$kode_pengerjaan)->first();
        // dd($data['new_data_pengerjaan']['akhir_bulan']);
        $explode_tanggal_pengerjaans = explode('#',$data['new_data_pengerjaan']['tanggal']);
        $exp_tanggals = array_filter($explode_tanggal_pengerjaans);
        $a = count($exp_tanggals);
        // dd($a);
        $exp_tgl_awal = explode('-', $exp_tanggals[1]);
        $exp_tgl_akhir = explode('-', $exp_tanggals[$a]);

        $first_weekly_id = $this->pengerjaanRitWeekly->where('kode_pengerjaan',$kode_pengerjaan)->first();

        if ($first_weekly_id->id%2==0) {
            $ganjil_genap = 'MOD(pengerjaan_supir_rit_weekly.id, 2) = 0';
        }else{
            $ganjil_genap = 'MOD(pengerjaan_supir_rit_weekly.id, 2) <> 0';
        }

        $pengerjaan_rit_weeklys = $this->pengerjaanRitWeekly->select([
                                                        'pengerjaan_supir_rit_weekly.id as id',
                                                        'pengerjaan_supir_rit_weekly.karyawan_supir_rit_id as karyawan_supir_rit_id',
                                                        'pengerjaan_supir_rit_weekly.lembur as lembur',
                                                        'pengerjaan_supir_rit_weekly.total_hasil as total_hasil',
                                                        'pengerjaan_supir_rit_weekly.tunjangan_kerja as tunjangan_kerja',
                                                        'pengerjaan_supir_rit_weekly.tunjangan_kehadiran as tunjangan_kehadiran',
                                                        'pengerjaan_supir_rit_weekly.uang_makan as uang_makan',
                                                        'pengerjaan_supir_rit_weekly.plus_1 as plus_1',
                                                        'pengerjaan_supir_rit_weekly.plus_2 as plus_2',
                                                        'pengerjaan_supir_rit_weekly.plus_3 as plus_3',
                                                        'pengerjaan_supir_rit_weekly.minus_1 as minus_1',
                                                        'pengerjaan_supir_rit_weekly.minus_2 as minus_2',
                                                        'pengerjaan_supir_rit_weekly.jht as jht',
                                                        'pengerjaan_supir_rit_weekly.bpjs_kesehatan as bpjs_kesehatan',
                                                        'pengerjaan_supir_rit_weekly.pensiun as pensiun',
                                                        'operator_supir_rit_karyawan.nik as nik',
                                                        'biodata_karyawan.nama as nama',
                                                    ])
                                                    ->leftJoin('operator_supir_rit_karyawan','operator_supir_rit_karyawan.id','=','pengerjaan_supir_rit_weekly.karyawan_supir_rit_id')
                                                    ->leftJoin('itic_emp_new.biodata_karyawan','biodata_karyawan.nik','=','operator_supir_rit_karyawan.nik')
                                                    ->where('pengerjaan_supir_rit_weekly.kode_pengerjaan',$kode_pengerjaan)
                                                    ->whereRaw($ganjil_genap)
                                                    // ->orderBy('biodata_karyawan.nama','asc')
                                                    ->orderBy('pengerjaan_supir_rit_weekly.id','desc')
                                                    ->get();
        // dd($pengerjaan_rit_weeklys);

        if ($a > 6) {
            $pdf = new Fpdf('P', 'mm', 'legal');
        }else{
            $pdf = new Fpdf('P', 'mm', 'a4');
        }
        $title='Payrol Supir RIT '.Carbon::parse($exp_tgl_awal[0] . '-' . $exp_tgl_awal[1] . '-' . $exp_tgl_awal[2])->isoFormat('D MMMM').' s/d '.Carbon::parse($exp_tgl_akhir[0] . '-' . $exp_tgl_akhir[1] . '-' . $exp_tgl_akhir[2])->isoFormat('D MMMM YYYY');
        $pdf->SetTitle($title);
        $pdf->AddPage();
        $pdf->SetFillColor(153,153,153);
        $pdf->SetTextColor(255);
        $pdf->SetDrawColor(0,0,0);
        $pdf->SetLineWidth(.3);
        $pdf->SetFont('Arial','B',8);
        $pdf->SetFillColor(224,235,255);
        $pdf->SetTextColor(0);
        $pdf->SetFont('Arial','',8);

        $no=0;

        foreach ($pengerjaan_rit_weeklys as $prw => $pengerjaan_rit_weekly) {
            $row1_nama = $this->biodataKaryawan->where('nik',$pengerjaan_rit_weekly->nik)->first();
            $id_selanjutnya=$pengerjaan_rit_weekly->id+1;
            // dd($id_selanjutnya);
            $row2_weekly = $this->pengerjaanRitWeekly->select([
                                                    'pengerjaan_supir_rit_weekly.id as id',
                                                    'pengerjaan_supir_rit_weekly.karyawan_supir_rit_id as karyawan_supir_rit_id',
                                                    'pengerjaan_supir_rit_weekly.lembur as lembur',
                                                    'pengerjaan_supir_rit_weekly.total_hasil as total_hasil',
                                                    'pengerjaan_supir_rit_weekly.tunjangan_kerja as tunjangan_kerja',
                                                    'pengerjaan_supir_rit_weekly.tunjangan_kehadiran as tunjangan_kehadiran',
                                                    'pengerjaan_supir_rit_weekly.uang_makan as uang_makan',
                                                    'pengerjaan_supir_rit_weekly.plus_1 as plus_1',
                                                    'pengerjaan_supir_rit_weekly.plus_2 as plus_2',
                                                    'pengerjaan_supir_rit_weekly.plus_3 as plus_3',
                                                    'pengerjaan_supir_rit_weekly.minus_1 as minus_1',
                                                    'pengerjaan_supir_rit_weekly.minus_2 as minus_2',
                                                    'pengerjaan_supir_rit_weekly.jht as jht',
                                                    'pengerjaan_supir_rit_weekly.bpjs_kesehatan as bpjs_kesehatan',
                                                    'pengerjaan_supir_rit_weekly.pensiun as pensiun',
                                                    'operator_supir_rit_karyawan.nik as nik',
                                                    'biodata_karyawan.nama as nama',
                                                ])
                                                ->leftJoin('operator_supir_rit_karyawan','operator_supir_rit_karyawan.id','=','pengerjaan_supir_rit_weekly.karyawan_supir_rit_id')
                                                ->leftJoin('itic_emp_new.biodata_karyawan','biodata_karyawan.nik','=','operator_supir_rit_karyawan.nik')
                                                ->where('pengerjaan_supir_rit_weekly.kode_pengerjaan',$kode_pengerjaan)
                                                ->where('pengerjaan_supir_rit_weekly.id',$id_selanjutnya)
                                                ->first();
            if (empty($row2_weekly)) {
                $row2_nik = null;
                $row2_karyawan_supir_rit_id = null;
            }else{
                $row2_nik = $row2_weekly->nik;
                $row2_karyawan_supir_rit_id = $row2_weekly->karyawan_supir_rit_id;
            }

            $row2_nama = $this->biodataKaryawan->where('nik',$row2_nik)->first();

            if (empty($row2_nama)) {
                $row2_nama_new = null;
            }else{
                $row2_nama_new = $row2_nama->nama;
            }
            ### LINE 1 ###
            $pdf->SetFont('Arial','B',9);
            $pdf->Cell(95,5,'GAJI RIT-RITAN '.strtoupper($pengerjaan_rit_weekly->nama),'LTR',0,'C'); 
            
            $pdf->Cell(2,5,'','LF',0,'L');
            
            $pdf->SetFont('Arial','B',9);
            $pdf->Cell(95,5,'GAJI RIT-RITAN '.strtoupper($row2_nama_new),'LTR',0,'C'); 
            $pdf->ln(4);

            ### LINE 2 ###
            $pdf->SetFont('Arial','B',9);
            $pdf->Cell(95,5,'Tanggal: '.Carbon::parse($exp_tgl_awal[0] . '-' . $exp_tgl_awal[1] . '-' . $exp_tgl_awal[2])->isoFormat('D MMMM').' s/d '.Carbon::parse($exp_tgl_akhir[0] . '-' . $exp_tgl_akhir[1] . '-' . $exp_tgl_akhir[2])->isoFormat('D MMMM YYYY'),'LRB',0,'C');
            
            $pdf->Cell(2,5,'','LF',0,'L');
            
            $pdf->SetFont('Arial','B',9);
            $pdf->Cell(95,5,'Tanggal: '.Carbon::parse($exp_tgl_awal[0] . '-' . $exp_tgl_awal[1] . '-' . $exp_tgl_awal[2])->isoFormat('D MMMM').' s/d '.Carbon::parse($exp_tgl_akhir[0] . '-' . $exp_tgl_akhir[1] . '-' . $exp_tgl_akhir[2])->isoFormat('D MMMM YYYY'),'LRB',0,'C');
            $pdf->ln(5);

            ### LINE 3 ###
            $pdf->SetFont('Arial','B',9);
            $pdf->Cell(20,5,'TGL','LB',0,'C'); 
            $pdf->Cell(53,5,'KETERANGAN','LRB',0,'C');
            $pdf->Cell(22,5,'Rp','BR',0,'C');

            $pdf->Cell(2,5,'','LF',0,'L');

            $pdf->SetFont('Arial','B',9);
            $pdf->Cell(20,5,'TGL','LB',0,'C'); 
            $pdf->Cell(53,5,'KETERANGAN','LRB',0,'C');
            $pdf->Cell(22,5,'Rp','BR',0,'C');
            $pdf->ln(5);

            $row1_upah_dasar = array();
            $row2_upah_dasar = array();

            for ($i=0;$i<$a;$i++) { 
                $row_daily_1 = $this->pengerjaanRitHarian->where('kode_pengerjaan',$kode_pengerjaan)
                                                ->where('karyawan_supir_rit_id',$pengerjaan_rit_weekly->karyawan_supir_rit_id)
                                                ->get();
                $row_daily_2 = $this->pengerjaanRitHarian->where('kode_pengerjaan',$kode_pengerjaan)
                                                ->where('karyawan_supir_rit_id',$row2_karyawan_supir_rit_id)
                                                ->get();
                // dd($row_daily_1);
                if (empty($row_daily_1[$i]->hasil_kerja_1)) {
                    $row_daily1_tanggal_pengerjaan = 0;
                    $row_daily1_hasil_kerja_1 = 0;
                    $row_daily1_hasil_umk_rit = 0;
                    $row_daily1_tarif_umk = 0;
                    $row_daily1_dpb = 0;
                    $row_daily1_jenis_umk = '-';
                }else{
                    // $row_daily1_tanggal_pengerjaan = $row_daily_1[$i]->tanggal_pengerjaan;
                    $row_daily1_tanggal_pengerjaan = Carbon::create($row_daily_1[$i]->tanggal_pengerjaan)->isoFormat('D MMM');
                    $row_daily1_explode_hasil_kerja_1 = explode("|",$row_daily_1[$i]->hasil_kerja_1);
                    $row_daily1_umk_rit = $this->ritUmk->where('id',$row_daily1_explode_hasil_kerja_1[0])->first();
                    if (empty($row_daily1_umk_rit)) {
                        $row_daily1_hasil_kerja_1 = 0;
                        $row_daily1_hasil_umk_rit = 0;
                        $row_daily1_tarif_umk = 0;
                        $row_daily1_dpb = 0;
                        $row_daily1_jenis_umk = '-';
                    }else{
                        $row_daily1_hasil_kerja_1 = $row_daily1_umk_rit->tarif*$row_daily1_explode_hasil_kerja_1[1];
                        $row_daily1_hasil_umk_rit = $row_daily1_umk_rit->kategori_upah;
                        $row_daily1_tarif_umk = $row_daily1_umk_rit->tarif;
                        $row_daily1_dpb = $row_daily_1[$i]->dpb/7*$row_daily_1[$i]->upah_dasar;
                        if (empty($row_daily1_umk_rit->rit_tujuan)) {
                            $row_daily1_jenis_umk = '-';
                        }else{
                            $row_daily1_jenis_umk = $row_daily1_umk_rit->rit_tujuan->tujuan.' - '.$row_daily1_umk_rit->rit_kendaraan->jenis_kendaraan;
                        }
                        $row1_total_upah_dasar = $row_daily1_hasil_kerja_1+$row_daily1_dpb;
                        // $row1_total_upah_dasar = $row_daily1_tarif_umk+$row_daily1_dpb;
                        array_push($row1_upah_dasar,$row1_total_upah_dasar);
                    }
                }

                // dd($row_daily1_tanggal_pengerjaan);

                if (empty($row_daily_2[$i]->hasil_kerja_1)) {
                    $row_daily2_tanggal_pengerjaan = 0;
                    $row_daily2_hasil_kerja_1 = 0;
                    $row_daily2_hasil_umk_rit = 0;
                    $row_daily2_tarif_umk = 0;
                    $row_daily2_dpb = 0;
                    $row_daily2_jenis_umk = '-';
                }else{
                    $row_daily2_tanggal_pengerjaan = Carbon::create($row_daily_2[$i]->tanggal_pengerjaan)->isoFormat('D MMM');
                    $row_daily2_explode_hasil_kerja_1 = explode("|",$row_daily_2[$i]->hasil_kerja_1);
                    $row_daily2_umk_rit = $this->ritUmk->where('id',$row_daily2_explode_hasil_kerja_1[0])->first();
                    if (empty($row_daily2_umk_rit)) {
                        $row_daily2_hasil_kerja_1 = 0;
                        $row_daily2_hasil_umk_rit = 0;
                        $row_daily2_tarif_umk = 0;
                        $row_daily2_dpb = 0;
                        $row_daily2_jenis_umk = '-';
                    }else{
                        // $row_daily2_hasil_kerja_1 = $row_daily2_umk_rit->tarif;
                        $row_daily2_hasil_kerja_1 = $row_daily2_umk_rit->tarif*$row_daily2_explode_hasil_kerja_1[1];
                        $row_daily2_hasil_umk_rit = $row_daily2_umk_rit->kategori_upah;
                        $row_daily2_tarif_umk = $row_daily2_umk_rit->tarif;
                        $row_daily2_dpb = $row_daily_2[$i]->dpb/7*$row_daily_2[$i]->upah_dasar;
                        if (empty($row_daily2_umk_rit->rit_tujuan)) {
                            $row_daily2_jenis_umk = '-';
                        }else{
                            $row_daily2_jenis_umk = $row_daily2_umk_rit->rit_tujuan->tujuan.' - '.$row_daily2_umk_rit->rit_kendaraan->jenis_kendaraan;
                        }
                        $row2_total_upah_dasar = $row_daily2_hasil_kerja_1+$row_daily2_dpb;
                        // $row2_total_upah_dasar = $row_daily2_tarif_umk+$row_daily2_dpb;
                        array_push($row2_upah_dasar,$row2_total_upah_dasar);
                    }
                }
                // dd($row_daily2_hasil_kerja_1);
                ### LINE 4 ###
                $pdf->SetFont('Arial','',9);
                $pdf->Cell(20,5,$row_daily1_tanggal_pengerjaan,'L',0,'C'); 
                $pdf->Cell(53,5,'DPB','LR',0,'L');
                $pdf->Cell(22,5,number_format($row_daily1_dpb,0,',','.'),'R',0,'R');
                
                $pdf->Cell(2,5,'','LF',0,'L');
                
                $pdf->SetFont('Arial','',9);
                $pdf->Cell(20,5,$row_daily2_tanggal_pengerjaan,'L',0,'C'); 
                $pdf->Cell(53,5,'DPB','LR',0,'L');
                $pdf->Cell(22,5,number_format($row_daily2_dpb,0,',','.'),'R',0,'R');
                $pdf->ln(4);

                ### LINE 5 ###
                $pdf->SetFont('Arial','',7);
                $pdf->Cell(20,5,'','BLR',0,'C'); 
                $pdf->Cell(53,5,$row_daily1_jenis_umk,'BLR',0,'L');
                $pdf->SetFont('Arial','',9);
                $pdf->Cell(22,5,number_format($row_daily1_hasil_kerja_1,0,',','.'),'BLR',0,'R');
                //batas//
                $pdf->Cell(2,5,'','LF',0,'L');
                //batas//
                $pdf->SetFont('Arial','',7);
                $pdf->Cell(20,5,'','BLR',0,'C'); 
                $pdf->Cell(53,5,$row_daily2_jenis_umk,'BLR',0,'L');
                $pdf->SetFont('Arial','',9);
                $pdf->Cell(22,5,number_format($row_daily2_hasil_kerja_1,0,',','.'),'BLR',0,'R');
                $pdf->ln(5);
            }

            $row1_hasil_upah_dasar = array_sum($row1_upah_dasar);
            $row2_hasil_upah_dasar = array_sum($row2_upah_dasar);

            if (empty($pengerjaan_rit_weekly->lembur)) {
                $row1_lembur_1 = 0;
                $row1_lembur_2 = 0;
                $row1_hasil_lembur = 0;
            }else{
                $row1_explode_lembur = explode("|",$pengerjaan_rit_weekly->lembur);
                $row1_lembur_1 = $row1_explode_lembur[1];
                $row1_lembur_2 = $row1_explode_lembur[2];
                $row1_hasil_lembur = $row1_explode_lembur[0];
            }
            
            if (empty($row2_weekly->lembur)) {
                $row2_lembur_1 = 0;
                $row2_lembur_2 = 0;
                $row2_hasil_lembur = 0;
            }else{
                $row2_explode_lembur = explode("|",$row2_weekly->lembur);
                $row2_lembur_1 = $row2_explode_lembur[1];
                $row2_lembur_2 = $row2_explode_lembur[2];
                $row2_hasil_lembur = $row2_explode_lembur[0];
            }
            $row1_total_jam_lembur = floatval($row1_lembur_1)+floatval($row1_lembur_2);
            $row2_total_jam_lembur = floatval($row2_lembur_1)+floatval($row2_lembur_2);

            if (empty($pengerjaan_rit_weekly->tunjangan_kehadiran)) {
                $row1_tunjangan_kehadiran = 0;
            }else{
                $row1_tunjangan_kehadiran = $pengerjaan_rit_weekly->tunjangan_kehadiran;
            }

            // if ($data['new_data_pengerjaan']['akhir_bulan'] == 'y') {
            //     if (empty($row1_weekly->tunjangan_kehadiran)) {
            //         $row1_tunjangan_kehadiran = 0;
            //     }else{
            //         $row1_tunjangan_kehadiran = $row1_weekly->tunjangan_kehadiran;
            //     }
            // }else{
            //     $row1_tunjangan_kehadiran = 0;
            //     $row2_tunjangan_kehadiran = 0;
            // }

            if ($data['new_data_pengerjaan']['akhir_bulan'] == 'y') {
                if (empty($row2_weekly->tunjangan_kehadiran)) {
                    $row2_tunjangan_kehadiran = 0;
                }else{
                    $row2_tunjangan_kehadiran = $row2_weekly->tunjangan_kehadiran;
                }
            }else{
                $row1_tunjangan_kehadiran = 0;
                $row2_tunjangan_kehadiran = 0;
            }
            
            if ($data['new_data_pengerjaan']['akhir_bulan'] == 'y') {
                if (empty($pengerjaan_rit_weekly->tunjangan_kerja)) {
                    $row1_tunjangan_kerja = 0;
                }else{
                    $row1_tunjangan_kerja = $pengerjaan_rit_weekly->tunjangan_kerja;
                }

                if (empty($row2_weekly->tunjangan_kerja)) {
                    $row2_tunjangan_kerja = 0;
                }else{
                    $row2_tunjangan_kerja = $row2_weekly->tunjangan_kerja;
                }
            }else{
                $row1_tunjangan_kerja = 0;
                $row2_tunjangan_kerja = 0;
            }

            if (empty($pengerjaan_rit_weekly->uang_makan)) {
                $row1_uang_makan = 0;
            }else{
                $row1_uang_makan = $pengerjaan_rit_weekly->uang_makan;
            }

            if (empty($row2_weekly->uang_makan)) {
                $row2_uang_makan = 0;
            }else{
                $row2_uang_makan = $row2_weekly->uang_makan;
            }

            if (empty($pengerjaan_rit_weekly->plus_1)) {
                $row1_plus_1 = 0;
                $row1_keterangan_plus_1 = '';
            }else{
                $row1_explode_plus_1 = explode("|",$pengerjaan_rit_weekly->plus_1);
                $row1_plus_1 = floatval($row1_explode_plus_1[0]);
                $row1_keterangan_plus_1 = $row1_explode_plus_1[1];
            }

            if (empty($row2_weekly->plus_1)) {
                $row2_plus_1 = 0;
                $row2_keterangan_plus_1 = '';
            }else{
                $row2_explode_plus_1 = explode("|",$row2_weekly->plus_1);
                $row2_plus_1 = floatval($row2_explode_plus_1[0]);
                $row2_keterangan_plus_1 = $row2_explode_plus_1[1];
            }

            if (empty($pengerjaan_rit_weekly->plus_2)) {
                $row1_plus_2 = 0;
                $row1_keterangan_plus_2 = '';
            }else{
                $row1_explode_plus_2 = explode("|",$pengerjaan_rit_weekly->plus_2);
                $row1_plus_2 = floatval($row1_explode_plus_2[0]);
                $row1_keterangan_plus_2 = $row1_explode_plus_2[1];
            }

            if (empty($row2_weekly->plus_2)) {
                $row2_plus_2 = 0;
                $row2_keterangan_plus_2 = '';
            }else{
                $row2_explode_plus_2 = explode("|",$row2_weekly->plus_2);
                $row2_plus_2 = floatval($row2_explode_plus_2[0]);
                $row2_keterangan_plus_2 = $row2_explode_plus_2[1];
            }

            if (empty($pengerjaan_rit_weekly->plus_3)) {
                $row1_plus_3 = 0;
                $row1_keterangan_plus_3 = '';
            }else{
                $row1_explode_plus_3 = explode("|",$pengerjaan_rit_weekly->plus_3);
                $row1_plus_3 = floatval($row1_explode_plus_3[0]);
                $row1_keterangan_plus_3 = $row1_explode_plus_3[1];
            }

            if (empty($row2_weekly->plus_3)) {
                $row2_plus_3 = 0;
                $row2_keterangan_plus_3 = '';
            }else{
                $row2_explode_plus_3 = explode("|",$row2_weekly->plus_3);
                $row2_plus_3 = floatval($row2_explode_plus_3[0]);
                $row2_keterangan_plus_3 = $row2_explode_plus_3[1];
            }

            $row1_total_gaji = $row1_hasil_upah_dasar+$row1_plus_1+$row1_plus_2+$row1_plus_3+$row1_uang_makan+$row1_hasil_lembur+$row1_tunjangan_kerja+$row1_tunjangan_kehadiran;
            $row2_total_gaji = $row2_hasil_upah_dasar+$row2_plus_1+$row2_plus_2+$row2_plus_3+$row2_uang_makan+$row2_hasil_lembur+$row2_tunjangan_kerja+$row2_tunjangan_kehadiran;
            
            if (empty($pengerjaan_rit_weekly->minus_1)) {
                $row1_minus_1 = 0;
                $row1_keterangan_minus_1 = '';
            }else{
                $row1_explode_minus_1 = explode("|",$pengerjaan_rit_weekly->minus_1);
                $row1_minus_1 = $row1_explode_minus_1[0];
                $row1_keterangan_minus_1 = $row1_explode_minus_1[1];
            }

            if (empty($row2_weekly->minus_1)) {
                $row2_minus_1 = 0;
                $row2_keterangan_minus_1 = '';
            }else{
                $row2_explode_minus_1 = explode("|",$row2_weekly->minus_1);
                $row2_minus_1 = $row2_explode_minus_1[0];
                $row2_keterangan_minus_1 = $row2_explode_minus_1[1];
            }

            if (empty($pengerjaan_rit_weekly->minus_2)) {
                $row1_minus_2 = 0;
                $row1_keterangan_minus_2 = '';
            }else{
                $row1_explode_minus_2 = explode("|",$pengerjaan_rit_weekly->minus_2);
                $row1_minus_2 = $row1_explode_minus_2[0];
                $row1_keterangan_minus_2 = $row1_explode_minus_2[1];
            }

            if (empty($row2_weekly->minus_2)) {
                $row2_minus_2 = 0;
                $row2_keterangan_minus_2 = '';
            }else{
                $row2_explode_minus_2 = explode("|",$row2_weekly->minus_2);
                $row2_minus_2 = $row2_explode_minus_2[0];
                $row2_keterangan_minus_2 = $row2_explode_minus_2[1];
            }

            if (empty($pengerjaan_rit_weekly->jht)) {
                $row1_jht = 0;
            }else{
                $row1_jht = $pengerjaan_rit_weekly->jht;
            }

            if (empty($row2_weekly->jht)) {
                $row2_jht = 0;
            }else{
                $row2_jht = $row2_weekly->jht;
            }

            if (empty($pengerjaan_rit_weekly->bpjs_kesehatan)) {
                $row1_bpjs_kesehatan = 0;
            }else{
                $row1_bpjs_kesehatan = $pengerjaan_rit_weekly->bpjs_kesehatan;
            }

            if (empty($row2_weekly->bpjs_kesehatan)) {
                $row2_bpjs_kesehatan = 0;
            }else{
                $row2_bpjs_kesehatan = $row2_weekly->bpjs_kesehatan;
            }

            if (empty($pengerjaan_rit_weekly->pensiun)) {
                $row1_pensiun = 0;
            }else{
                $row1_pensiun = $pengerjaan_rit_weekly->pensiun;
            }

            if (empty($row2_weekly->pensiun)) {
                $row2_pensiun = 0;
            }else{
                $row2_pensiun = $row2_weekly->pensiun;
            }

            $row1_total_upah_diterima = $row1_total_gaji-$row1_minus_1-$row1_minus_2-$row1_jht-$row1_bpjs_kesehatan-$row1_pensiun;
            $row2_total_upah_diterima = $row2_total_gaji-$row2_minus_1-$row2_minus_2-$row2_jht-$row2_bpjs_kesehatan-$row2_pensiun;

            ### LINE 5 ###
            $pdf->SetFont('Arial','',9);
            $pdf->Cell(35,5,"Lembur ".'( '.$row1_total_jam_lembur.' Jam )','L',0,'L'); 
            $pdf->Cell(22,5,'','',0,'L');
            $pdf->Cell(3,5,'Rp','',0,'L');
            $pdf->Cell(35,5,number_format($row1_hasil_lembur,0,',','.'),'R',0,'R');
            
            $pdf->Cell(2,5,'','LF',0,'L');

            $pdf->SetFont('Arial','',9);
            $pdf->Cell(35,5,"Lembur ".'( '.$row2_total_jam_lembur.' Jam )','L',0,'L'); 
            $pdf->Cell(22,5,'','',0,'L');
            $pdf->Cell(3,5,'Rp','',0,'L');
            $pdf->Cell(35,5,number_format($row2_hasil_lembur,0,',','.'),'R',0,'R');
            $pdf->ln(4);

            ### LINE 6 ###
            $pdf->SetFont('Arial','',9);
            $pdf->Cell(35,5,'Insentif Kehadiran','L',0,'L'); 
            $pdf->Cell(22,5,'','',0,'L');
            $pdf->Cell(3,5,'Rp','',0,'L');
            $pdf->Cell(35,5,number_format($row1_tunjangan_kehadiran,0,',','.'),'R',0,'R');
            
            $pdf->Cell(2,5,'','LF',0,'L');
            
            $pdf->SetFont('Arial','',9);
            $pdf->Cell(35,5,'Insentif Kehadiran','L',0,'L'); 
            $pdf->Cell(22,5,'','',0,'L');
            $pdf->Cell(3,5,'Rp','',0,'L');
            $pdf->Cell(35,5,number_format($row2_tunjangan_kehadiran,0,',','.'),'R',0,'R');
            $pdf->ln(4);

            ### LINE 6 ###
            $pdf->SetFont('Arial','',9);
            $pdf->Cell(35,5,'Tunjangan Kerja','L',0,'L'); 
            $pdf->Cell(22,5,'','',0,'L');
            $pdf->Cell(3,5,'Rp','',0,'L');
            $pdf->Cell(35,5,number_format($row1_tunjangan_kerja,0,',','.'),'R',0,'R');
            
            $pdf->Cell(2,5,'','LF',0,'L');
            
            $pdf->SetFont('Arial','',9);
            $pdf->Cell(35,5,'Tunjangan Kerja','L',0,'L'); 
            $pdf->Cell(22,5,'','',0,'L');
            $pdf->Cell(3,5,'Rp','',0,'L');
            $pdf->Cell(35,5,number_format($row2_tunjangan_kerja,0,',','.'),'R',0,'R');
            $pdf->ln(4);
    
            ### LINE 7 ###
            $pdf->SetFont('Arial','',9);
            $pdf->Cell(12,5,'Plus','L',0,'L'); 
            $pdf->Cell(45,5,"( ".$row1_keterangan_plus_1." )",'',0,'L');
            $pdf->Cell(3,5,'Rp','',0,'L');
            $pdf->Cell(35,5,number_format($row1_plus_1,0,',','.'),'R',0,'R');
            
            $pdf->Cell(2,5,'','LF',0,'L');
            
            $pdf->SetFont('Arial','',9);
            $pdf->Cell(12,5,'Plus','L',0,'L'); 
            $pdf->Cell(45,5,"( ".$row2_keterangan_plus_1." )",'',0,'L');
            $pdf->Cell(3,5,'Rp','',0,'L');
            $pdf->Cell(35,5,number_format($row2_plus_1,0,',','.'),'R',0,'R');
            $pdf->ln(4);
    
            $pdf->SetFont('Arial','',9);
            $pdf->Cell(12,5,'','L',0,'L'); 
            $pdf->Cell(45,5,"( ".$row1_keterangan_plus_2." )",'',0,'L');
            $pdf->Cell(3,5,'Rp','',0,'L');
            $pdf->Cell(35,5,number_format($row1_plus_2,0,',','.'),'R',0,'R');
            
            $pdf->Cell(2,5,'','LF',0,'L');

            $pdf->SetFont('Arial','',9);
            $pdf->Cell(12,5,'','L',0,'L'); 
            $pdf->Cell(45,5,"( ".$row2_keterangan_plus_2." )",'',0,'L');
            $pdf->Cell(3,5,'Rp','',0,'L');
            $pdf->Cell(35,5,number_format($row2_plus_2,0,',','.'),'R',0,'R');
            $pdf->ln(4);
    
            $pdf->SetFont('Arial','',9);
            $pdf->Cell(12,5,'','L',0,'L'); 
            $pdf->Cell(45,5,"( ".$row1_keterangan_plus_3." )",'',0,'L');
            $pdf->Cell(3,5,'Rp','',0,'L');
            $pdf->Cell(35,5,number_format($row1_plus_3,0,',','.'),'R',0,'R');
            
            $pdf->Cell(2,5,'','LF',0,'L');
            
            $pdf->SetFont('Arial','',9);
            $pdf->Cell(12,5,'','L',0,'L'); 
            $pdf->Cell(45,5,"( ".$row2_keterangan_plus_3." )",'',0,'L');
            $pdf->Cell(3,5,'Rp','',0,'L');
            $pdf->Cell(35,5,number_format($row2_plus_3,0,',','.'),'R',0,'R');
            $pdf->ln(4);
    
            $pdf->SetFont('Arial','',9);
            $pdf->Cell(35,5,'Uang Makan','L',0,'L'); 
            $pdf->Cell(22,5,'','',0,'L');
            $pdf->Cell(3,5,'Rp','',0,'L');
            $pdf->Cell(35,5,number_format($row1_uang_makan,0,',','.'),'R',0,'R');
            
            $pdf->Cell(2,5,'','LF',0,'L');
            
            $pdf->SetFont('Arial','',9);
            $pdf->Cell(35,5,'Uang Makan','L',0,'L'); 
            $pdf->Cell(22,5,'','',0,'L');
            $pdf->Cell(3,5,'Rp','',0,'L');
            $pdf->Cell(35,5,number_format($row2_uang_makan,0,',','.'),'R',0,'R');
            $pdf->ln(5);
    
            $pdf->SetFont('Arial','B',9);
            $pdf->Cell(20,5,'POTONGAN','L',0,'L'); 
            $pdf->Cell(37,5,'BPJS > JHT + JP','',0,'L');
            $pdf->Cell(3,5,'Rp','',0,'L');
            $pdf->Cell(35,5,number_format($row1_jht+$row1_pensiun,0,',','.'),'R',0,'R');
            
            $pdf->Cell(2,5,'','LF',0,'L');
            
            $pdf->SetFont('Arial','B',9);
            $pdf->Cell(20,5,'POTONGAN','L',0,'L'); 
            $pdf->Cell(37,5,'BPJS > JHT + JP','',0,'L');
            $pdf->Cell(3,5,'Rp','',0,'L');
            $pdf->Cell(35,5,number_format($row2_jht+$row2_pensiun,0,',','.'),'R',0,'R');
            $pdf->ln(4);
    
            $pdf->SetFont('Arial','B',9);
            $pdf->Cell(20,5,'','L',0,'L'); 
            $pdf->Cell(37,5,'BPJS Kesehatan','',0,'L');
            $pdf->Cell(3,5,'Rp','',0,'L');
            $pdf->Cell(35,5,number_format($row1_bpjs_kesehatan,0,',','.'),'R',0,'R');
            
            $pdf->Cell(2,5,'','LF',0,'L');
            
            $pdf->SetFont('Arial','B',9);
            $pdf->Cell(20,5,'','L',0,'L'); 
            $pdf->Cell(37,5,'BPJS Kesehatan','',0,'L');
            $pdf->Cell(3,5,'Rp','',0,'L');
            $pdf->Cell(35,5,number_format($row2_bpjs_kesehatan,0,',','.'),'R',0,'R');
            $pdf->ln(4);
    
            $pdf->SetFont('Arial','B',9);
            $pdf->Cell(20,5,'','L',0,'L'); 
            $pdf->Cell(37,5,"( ".$row1_keterangan_minus_1." )",'',0,'L');
            $pdf->Cell(3,5,'Rp','',0,'L');
            $pdf->Cell(35,5,number_format($row1_minus_1,0,',','.'),'R',0,'R');
            
            $pdf->Cell(2,5,'','LF',0,'L');
            
            $pdf->SetFont('Arial','B',9);
            $pdf->Cell(20,5,'','L',0,'L'); 
            $pdf->Cell(37,5,"( ".$row2_keterangan_minus_1." )",'',0,'L');
            $pdf->Cell(3,5,'Rp','',0,'L');
            $pdf->Cell(35,5,number_format($row2_minus_1,0,',','.'),'R',0,'R');
            $pdf->ln(4);
    
            $pdf->SetFont('Arial','B',9);
            $pdf->Cell(20,5,'','L',0,'L'); 
            $pdf->Cell(37,5,"( ".$row1_keterangan_minus_2." )",'',0,'L');
            $pdf->Cell(3,5,'Rp','',0,'L');
            $pdf->Cell(35,5,number_format($row1_minus_2,0,',','.'),'R',0,'R');
            
            $pdf->Cell(2,5,'','LF',0,'L');
            
            $pdf->SetFont('Arial','B',9);
            $pdf->Cell(20,5,'','L',0,'L'); 
            $pdf->Cell(37,5,"( ".$row2_keterangan_minus_2." )",'',0,'L');
            $pdf->Cell(3,5,'Rp','',0,'L');
            $pdf->Cell(35,5,number_format($row2_minus_2,0,',','.'),'R',0,'R');
            $pdf->ln(5);
    
            $pdf->SetFont('Arial','B',9);
            $pdf->Cell(35,5,'TOTAL DITERIMA','BL',0,'L'); 
            $pdf->Cell(22,5,'','B',0,'L');
            $pdf->Cell(3,5,'Rp','B',0,'L');
            $pdf->Cell(35,5,number_format($row1_total_upah_diterima,0,',','.'),'BR',0,'R');
            
            $pdf->Cell(2,5,'','LF',0,'L');
            
            $pdf->SetFont('Arial','B',9);
            $pdf->Cell(35,5,'TOTAL DITERIMA','BL',0,'L'); 
            $pdf->Cell(22,5,'','B',0,'L');
            $pdf->Cell(3,5,'Rp','B',0,'L');
            $pdf->Cell(35,5,number_format($row2_total_upah_diterima,0,',','.'),'BR',0,'R');
            $pdf->ln(3);

            $no=$no+1;
            if ($no%2==0)
            {
                $page=round($no/2);
                $pdf->ln(3);
                $pdf->SetFont('Arial','',7);
                $pdf->Cell(35,5,"- page $page -",'',0,''); 
                $pdf->ln(12);
            }
            // if ($a == 3) {
                
            // }
            // elseif ($a == 7) {
            //     // 7 hari kerja
            //     $pdf->ln(4);
            // }elseif ($a == 6) {
            //     // 6 hari kerja
            //     $pdf->ln(8);
            // }else{
            //     // 5 hari kerja
            //     $pdf->ln(16);
            // }
            switch ($a) {
                case '3':
                    // 3 hari kerja
                    $pdf->ln(34);
                    break;
                case '5':
                    // 3 hari kerja
                    $pdf->ln(18);
                    break;
                case '6':
                    // 6 hari kerja
                    $pdf->ln(8);
                    break;
                case '7':
                    // 7 hari kerja
                    $pdf->ln(28);
                    break;
                case '8':
                    // 7 hari kerja
                    $pdf->ln(18);
                    break;
                default:
                    // 5 hari kerja
                    $pdf->ln(16);
                    break;
            }
        }

        // foreach ($pengerjaan_rit_weeklys as $prw => $pengerjaan_rit_weekly) {
        //     $pengerjaan_rit_dailys = PengerjaanRITHarian::where('karyawan_supir_rit_id',$pengerjaan_rit_weekly->karyawan_supir_rit_id)
        //                                                 ->where('kode_pengerjaan',$kode_pengerjaan)
        //                                                 ->get();
        //     if ($prw%2===0) {
        //         ### LINE 1 ###
        //         $pdf->SetFont('Arial','B',9);
        //         $pdf->Cell(95,5,'GAJI RIT-RITAN '.strtoupper($pengerjaan_rit_weekly->nama),'LTR',0,'C'); 
        //         $pdf->ln(4);
        
        //         ### LINE 2 ###
        //         $pdf->SetFont('Arial','B',9);
        //         $pdf->Cell(95,5,'Tanggal: '.Carbon::parse($exp_tgl_awal[0] . '-' . $exp_tgl_awal[1] . '-' . $exp_tgl_awal[2])->isoFormat('D MMMM').' s/d '.Carbon::parse($exp_tgl_akhir[0] . '-' . $exp_tgl_akhir[1] . '-' . $exp_tgl_akhir[2])->isoFormat('D MMMM YYYY'),'LRB',0,'C');
        //         $pdf->ln(5);
        
        //         ### LINE 3 ###
        //         $pdf->SetFont('Arial','B',9);
        //         $pdf->Cell(20,5,'TGL','LB',0,'C'); 
        //         $pdf->Cell(53,5,'KETERANGAN','LRB',0,'C');
        //         $pdf->Cell(22,5,'Rp','BR',0,'C');
        //         $pdf->ln(5);

        //         $upah_dasar = array();
        //         foreach ($pengerjaan_rit_dailys as $prd => $pengerjaan_rit_daily) {
        //             if (empty($pengerjaan_rit_daily->hasil_kerja_1)) {
        //                 // $jenis_umk = 0;
        //                 $hasil_kerja_1 = 0;
        //                 $hasil_umk_rit = 0;
        //                 $tarif_umk = 0;
        //                 $dpb = 0;
        //                 $jenis_umk = '-';
        //             }else{
        //                 $explode_hasil_kerja_1 = explode("|",$pengerjaan_rit_daily->hasil_kerja_1);
        //                 $umk_rit = RitUMK::where('id',$explode_hasil_kerja_1[0])->first();
        //                 // dd($umk_rit->rit_tujuan);
        //                 if (empty($umk_rit)) {
        //                     $hasil_kerja_1 = 0;
        //                     $hasil_umk_rit = 0;
        //                     $tarif_umk = 0;
        //                     $dpb = 0;
        //                     $jenis_umk = '-';
        //                 }else{
        //                     $hasil_kerja_1 = $umk_rit->tarif;
        //                     $hasil_umk_rit = $umk_rit->kategori_upah;
        //                     $tarif_umk = $umk_rit->tarif;
        //                     $dpb = $pengerjaan_rit_daily->dpb/7*$pengerjaan_rit_daily->upah_dasar;
        //                     if (empty($umk_rit->rit_tujuan)) {
        //                         $jenis_umk = '-';
        //                     }else{
        //                         $jenis_umk = $umk_rit->rit_tujuan->tujuan.' - '.$umk_rit->rit_kendaraan->jenis_kendaraan;
        //                     }
        //                 }
        //                 $total_upah_dasar = $tarif_umk+$dpb;
        //                 array_push($upah_dasar,$total_upah_dasar);
        //             }
        //             ### LINE 4 ###
        //             $pdf->SetFont('Arial','',9);
        //             $pdf->Cell(20,5,Carbon::parse($pengerjaan_rit_daily->tanggal_pengerjaan)->isoFormat('D MMMM'),'L',0,'C'); 
        //             $pdf->Cell(53,5,'DPB','LR',0,'L');
        //             $pdf->Cell(22,5,number_format($dpb,0,',','.'),'R',0,'R');
        //             $pdf->ln(4);
            
        //             ### LINE 5 ###
        //             $pdf->SetFont('Arial','',7);
        //             $pdf->Cell(20,5,'','BLR',0,'C'); 
        //             $pdf->Cell(53,5,$jenis_umk,'BLR',0,'L');
        //             $pdf->SetFont('Arial','',9);
        //             $pdf->Cell(22,5,number_format($hasil_kerja_1,0,',','.'),'BLR',0,'R');
        //             $pdf->ln(5);
        //         }

        //         $hasil_upah_dasar = array_sum($upah_dasar);

        //         if (empty($pengerjaan_rit_weekly->lembur)) {
        //             $lembur_1 = 0;
        //             $lembur_2 = 0;
        //             $hasil_lembur = 0;
        //         }else{
        //             $explode_lembur = explode("|",$pengerjaan_rit_weekly->lembur);
        //             $lembur_1 = $explode_lembur[1];
        //             $lembur_2 = $explode_lembur[2];
        //             $hasil_lembur = $explode_lembur[0];
        //         }

        //         $total_jam_lembur = floatval($lembur_1)+floatval($lembur_2);
        
        //         if (empty($pengerjaan_rit_weekly->tunjangan_kehadiran)) {
        //             $tunjangan_kehadiran = 0;
        //         }else{
        //             $tunjangan_kehadiran = $pengerjaan_rit_weekly->tunjangan_kehadiran;
        //         }

        //         if (empty($pengerjaan_rit_weekly->tunjangan_kerja)) {
        //             $tunjangan_kerja = 0;
        //         }else{
        //             $tunjangan_kerja = $pengerjaan_rit_weekly->tunjangan_kerja;
        //         }
        //         // dd($tunjangan_kerja);

        //         if (empty($pengerjaan_rit_weekly->uang_makan)) {
        //             $uang_makan = 0;
        //         }else{
        //             $uang_makan = $pengerjaan_rit_weekly->uang_makan;
        //         }

        //         if (empty($pengerjaan_rit_weekly->plus_1)) {
        //             $plus_1 = 0;
        //             $keterangan_plus_1 = '';
        //         }else{
        //             $explode_plus_1 = explode("|",$pengerjaan_rit_weekly->plus_1);
        //             $plus_1 = $explode_plus_1[0];
        //             $keterangan_plus_1 = $explode_plus_1[1];
        //         }

        //         if (empty($pengerjaan_rit_weekly->plus_2)) {
        //             $plus_2 = 0;
        //             $keterangan_plus_2 = '';
        //         }else{
        //             $explode_plus_2 = explode("|",$pengerjaan_rit_weekly->plus_2);
        //             $plus_2 = $explode_plus_2[0];
        //             $keterangan_plus_2 = $explode_plus_2[1];
        //         }

        //         if (empty($pengerjaan_rit_weekly->plus_3)) {
        //             $plus_3 = 0;
        //             $keterangan_plus_3 = '';
        //         }else{
        //             $explode_plus_3 = explode("|",$pengerjaan_rit_weekly->plus_3);
        //             $plus_3 = $explode_plus_3[0];
        //             $keterangan_plus_3 = $explode_plus_3[1];
        //         }

        //         $total_gaji = $hasil_upah_dasar+$plus_1+$plus_2+$plus_3+$uang_makan+$hasil_lembur+$tunjangan_kerja+$tunjangan_kehadiran;

        //         if (empty($pengerjaan_rit_weekly->minus_1)) {
        //             $minus_1 = 0;
        //             $keterangan_minus_1 = '';
        //         }else{
        //             $explode_minus_1 = explode("|",$pengerjaan_rit_weekly->minus_1);
        //             $minus_1 = $explode_minus_1[0];
        //             $keterangan_minus_1 = $explode_minus_1[1];
        //         }

        //         if (empty($pengerjaan_rit_weekly->minus_2)) {
        //             $minus_2 = 0;
        //             $keterangan_minus_2 = '';
        //         }else{
        //             $explode_minus_2 = explode("|",$pengerjaan_rit_weekly->minus_2);
        //             $minus_2 = $explode_minus_2[0];
        //             $keterangan_minus_2 = $explode_minus_2[1];
        //         }

        //         if (empty($pengerjaan_rit_weekly->jht)) {
        //             $jht = 0;
        //         }else{
        //             $jht = $pengerjaan_rit_weekly->jht;
        //         }

        //         if (empty($pengerjaan_rit_weekly->bpjs_kesehatan)) {
        //             $bpjs_kesehatan = 0;
        //         }else{
        //             $bpjs_kesehatan = $pengerjaan_rit_weekly->bpjs_kesehatan;
        //         }

        //         if (empty($pengerjaan_rit_weekly->pensiun)) {
        //             $pensiun = 0;
        //         }else{
        //             $pensiun = $pengerjaan_rit_weekly->pensiun;
        //         }

        //         $total_upah_diterima = $total_gaji-$minus_1-$minus_2-$jht-$bpjs_kesehatan-$pensiun;

        //         ### LINE 6 ###
        //         $pdf->SetFont('Arial','',9);
        //         $pdf->Cell(35,5,"Lembur ".'( '.$total_jam_lembur.' Jam )','L',0,'L'); 
        //         $pdf->Cell(22,5,'','',0,'L');
        //         $pdf->Cell(3,5,'Rp','',0,'L');
        //         $pdf->Cell(35,5,number_format($hasil_lembur,0,',','.'),'R',0,'R');
        //         $pdf->ln(4);
        
        //         ### LINE 5 ###
        //         $pdf->SetFont('Arial','',9);
        //         $pdf->Cell(35,5,'Insentif Kehadiran','L',0,'L'); 
        //         $pdf->Cell(22,5,'','',0,'L');
        //         $pdf->Cell(3,5,'Rp','',0,'L');
        //         $pdf->Cell(35,5,number_format($tunjangan_kehadiran,0,',','.'),'R',0,'R');
        //         $pdf->ln(4);
        
        //         ### LINE 6 ###
        //         $pdf->SetFont('Arial','',9);
        //         $pdf->Cell(35,5,'Tunjangan Kerja','L',0,'L'); 
        //         $pdf->Cell(22,5,'','',0,'L');
        //         $pdf->Cell(3,5,'Rp','',0,'L');
        //         $pdf->Cell(35,5,number_format($tunjangan_kerja,0,',','.'),'R',0,'R');
        //         $pdf->ln(4);
        
        //         ### LINE 7 ###
        //         $pdf->SetFont('Arial','',9);
        //         $pdf->Cell(12,5,'Plus','L',0,'L'); 
        //         $pdf->Cell(45,5,"( ".$keterangan_plus_1." )",'',0,'L');
        //         $pdf->Cell(3,5,'Rp','',0,'L');
        //         $pdf->Cell(35,5,number_format($plus_1,0,',','.'),'R',0,'R');
        //         $pdf->ln(4);
        
        //         $pdf->SetFont('Arial','',9);
        //         $pdf->Cell(12,5,'','L',0,'L'); 
        //         $pdf->Cell(45,5,"( ".$keterangan_plus_2." )",'',0,'L');
        //         $pdf->Cell(3,5,'Rp','',0,'L');
        //         $pdf->Cell(35,5,number_format($plus_2,0,',','.'),'R',0,'R');
        //         $pdf->ln(4);
        
        //         $pdf->SetFont('Arial','',9);
        //         $pdf->Cell(12,5,'','L',0,'L'); 
        //         $pdf->Cell(45,5,"( ".$keterangan_plus_3." )",'',0,'L');
        //         $pdf->Cell(3,5,'Rp','',0,'L');
        //         $pdf->Cell(35,5,number_format($plus_3,0,',','.'),'R',0,'R');
        //         $pdf->ln(4);
        
        //         $pdf->SetFont('Arial','',9);
        //         $pdf->Cell(35,5,'Uang Makan','L',0,'L'); 
        //         $pdf->Cell(22,5,'','',0,'L');
        //         $pdf->Cell(3,5,'Rp','',0,'L');
        //         $pdf->Cell(35,5,number_format($uang_makan,0,',','.'),'R',0,'R');
        //         $pdf->ln(5);
        
        //         $pdf->SetFont('Arial','B',9);
        //         $pdf->Cell(20,5,'POTONGAN','L',0,'L'); 
        //         $pdf->Cell(37,5,'BPJS > JHT + JP','',0,'L');
        //         $pdf->Cell(3,5,'Rp','',0,'L');
        //         $pdf->Cell(35,5,number_format($jht+$pensiun,0,',','.'),'R',0,'R');
        //         $pdf->ln(4);
        
        //         $pdf->SetFont('Arial','B',9);
        //         $pdf->Cell(20,5,'','L',0,'L'); 
        //         $pdf->Cell(37,5,'BPJS Kesehatan','',0,'L');
        //         $pdf->Cell(3,5,'Rp','',0,'L');
        //         $pdf->Cell(35,5,number_format($bpjs_kesehatan,0,',','.'),'R',0,'R');
        //         $pdf->ln(4);
        
        //         $pdf->SetFont('Arial','B',9);
        //         $pdf->Cell(20,5,'','L',0,'L'); 
        //         $pdf->Cell(37,5,"( ".$keterangan_minus_1." )",'',0,'L');
        //         $pdf->Cell(3,5,'Rp','',0,'L');
        //         $pdf->Cell(35,5,number_format($minus_1,0,',','.'),'R',0,'R');
        //         $pdf->ln(4);
        
        //         $pdf->SetFont('Arial','B',9);
        //         $pdf->Cell(20,5,'','L',0,'L'); 
        //         $pdf->Cell(37,5,"( ".$keterangan_minus_2." )",'',0,'L');
        //         $pdf->Cell(3,5,'Rp','',0,'L');
        //         $pdf->Cell(35,5,number_format($minus_2,0,',','.'),'R',0,'R');
        //         $pdf->ln(5);
        
        //         $pdf->SetFont('Arial','B',9);
        //         $pdf->Cell(35,5,'TOTAL DITERIMA','BL',0,'L'); 
        //         $pdf->Cell(22,5,'','B',0,'L');
        //         $pdf->Cell(3,5,'Rp','B',0,'L');
        //         $pdf->Cell(35,5,number_format($total_upah_diterima,0,',','.'),'BR',0,'R');
        //         $pdf->ln(3);
        
        //         $no=$no+1;
        //         if ($no%2==0)
        //         {
        //             $page=round($no/2);
        //             $pdf->ln(1);
        //             $pdf->SetFont('Arial','',7);
        //             $pdf->Cell(35,5,"- page $page -",'',0,''); 
        //             $pdf->ln(12);
        //         }
        //         $pdf->ln(8);
        //     }else{
        //         $x = 105.5;
        //         ### LINE 1 ###
        //         $pdf->SetX($x);
        //         $pdf->SetFont('Arial','B',9);
        //         $pdf->Cell(95,5,'GAJI RIT-RITAN '.strtoupper($pengerjaan_rit_weekly->nama),'LTR',0,'C'); 
        //         $pdf->ln(4);
        
        //         ### LINE 2 ###
        //         $pdf->SetX($x);
        //         $pdf->SetFont('Arial','B',9);
        //         $pdf->Cell(95,5,'Tanggal: '.Carbon::parse($exp_tgl_awal[0] . '-' . $exp_tgl_awal[1] . '-' . $exp_tgl_awal[2])->isoFormat('D MMMM').' s/d '.Carbon::parse($exp_tgl_akhir[0] . '-' . $exp_tgl_akhir[1] . '-' . $exp_tgl_akhir[2])->isoFormat('D MMMM YYYY'),'LRB',0,'C');
        //         $pdf->ln(5);
        
        //         ### LINE 3 ###
        //         $pdf->SetX($x);
        //         $pdf->SetFont('Arial','B',9);
        //         $pdf->Cell(20,5,'TGL','LB',0,'C'); 
        //         $pdf->Cell(53,5,'KETERANGAN','LRB',0,'C');
        //         $pdf->Cell(22,5,'Rp','BR',0,'C');
        //         $pdf->ln(5);
    
        //         $upah_dasar = array();
        //         foreach ($pengerjaan_rit_dailys as $prd => $pengerjaan_rit_daily) {
        //             if (empty($pengerjaan_rit_daily->hasil_kerja_1)) {
        //                 // $jenis_umk = 0;
        //                 $hasil_kerja_1 = 0;
        //                 $hasil_umk_rit = 0;
        //                 $tarif_umk = 0;
        //                 $dpb = 0;
        //                 $jenis_umk = '-';
        //             }else{
        //                 $explode_hasil_kerja_1 = explode("|",$pengerjaan_rit_daily->hasil_kerja_1);
        //                 $umk_rit = RitUMK::where('id',$explode_hasil_kerja_1[0])->first();
        //                 // dd($umk_rit->rit_tujuan);
        //                 if (empty($umk_rit)) {
        //                     $hasil_kerja_1 = 0;
        //                     $hasil_umk_rit = 0;
        //                     $tarif_umk = 0;
        //                     $dpb = 0;
        //                     $jenis_umk = '-';
        //                 }else{
        //                     $hasil_kerja_1 = $umk_rit->tarif;
        //                     $hasil_umk_rit = $umk_rit->kategori_upah;
        //                     $tarif_umk = $umk_rit->tarif;
        //                     $dpb = $pengerjaan_rit_daily->dpb/7*$pengerjaan_rit_daily->upah_dasar;
        //                     if (empty($umk_rit->rit_tujuan)) {
        //                         $jenis_umk = '-';
        //                     }else{
        //                         $jenis_umk = $umk_rit->rit_tujuan->tujuan.' - '.$umk_rit->rit_kendaraan->jenis_kendaraan;
        //                     }
        //                 }
        //                 $total_upah_dasar = $tarif_umk+$dpb;
        //                 array_push($upah_dasar,$total_upah_dasar);
        //             }
        //             ### LINE 4 ###
        //             $pdf->SetX($x);
        //             $pdf->SetFont('Arial','',9);
        //             $pdf->Cell(20,5,Carbon::parse($pengerjaan_rit_daily->tanggal_pengerjaan)->isoFormat('D MMMM'),'L',0,'C'); 
        //             $pdf->Cell(53,5,'DPB','LR',0,'L');
        //             $pdf->Cell(22,5,number_format($dpb,0,',','.'),'R',0,'R');
        //             $pdf->ln(4);
            
        //             ### LINE 5 ###
        //             $pdf->SetX($x);
        //             $pdf->SetFont('Arial','',7);
        //             $pdf->Cell(20,5,'','BLR',0,'C'); 
        //             $pdf->Cell(53,5,$jenis_umk,'BLR',0,'L');
        //             $pdf->SetFont('Arial','',9);
        //             $pdf->Cell(22,5,number_format($hasil_kerja_1,0,',','.'),'BLR',0,'R');
        //             $pdf->ln(5);
        //         }
    
        //         $hasil_upah_dasar = array_sum($upah_dasar);
    
        //         if (empty($pengerjaan_rit_weekly->lembur)) {
        //             $lembur_1 = 0;
        //             $lembur_2 = 0;
        //             $hasil_lembur = 0;
        //         }else{
        //             $explode_lembur = explode("|",$pengerjaan_rit_weekly->lembur);
        //             $lembur_1 = $explode_lembur[1];
        //             $lembur_2 = $explode_lembur[2];
        //             $hasil_lembur = $explode_lembur[0];
        //         }
    
        //         $total_jam_lembur = floatval($lembur_1)+floatval($lembur_2);
        
        //         if (empty($pengerjaan_rit_weekly->tunjangan_kehadiran)) {
        //             $tunjangan_kehadiran = 0;
        //         }else{
        //             $tunjangan_kehadiran = $pengerjaan_rit_weekly->tunjangan_kehadiran;
        //         }
    
        //         if (empty($pengerjaan_rit_weekly->tunjangan_kerja)) {
        //             $tunjangan_kerja = 0;
        //         }else{
        //             $tunjangan_kerja = $pengerjaan_rit_weekly->tunjangan_kerja;
        //         }
        //         // dd($tunjangan_kerja);
    
        //         if (empty($pengerjaan_rit_weekly->uang_makan)) {
        //             $uang_makan = 0;
        //         }else{
        //             $uang_makan = $pengerjaan_rit_weekly->uang_makan;
        //         }
    
        //         if (empty($pengerjaan_rit_weekly->plus_1)) {
        //             $plus_1 = 0;
        //             $keterangan_plus_1 = '';
        //         }else{
        //             $explode_plus_1 = explode("|",$pengerjaan_rit_weekly->plus_1);
        //             $plus_1 = $explode_plus_1[0];
        //             $keterangan_plus_1 = $explode_plus_1[1];
        //         }
    
        //         if (empty($pengerjaan_rit_weekly->plus_2)) {
        //             $plus_2 = 0;
        //             $keterangan_plus_2 = '';
        //         }else{
        //             $explode_plus_2 = explode("|",$pengerjaan_rit_weekly->plus_2);
        //             $plus_2 = $explode_plus_2[0];
        //             $keterangan_plus_2 = $explode_plus_2[1];
        //         }
    
        //         if (empty($pengerjaan_rit_weekly->plus_3)) {
        //             $plus_3 = 0;
        //             $keterangan_plus_3 = '';
        //         }else{
        //             $explode_plus_3 = explode("|",$pengerjaan_rit_weekly->plus_3);
        //             $plus_3 = $explode_plus_3[0];
        //             $keterangan_plus_3 = $explode_plus_3[1];
        //         }
    
        //         $total_gaji = $hasil_upah_dasar+$plus_1+$plus_2+$plus_3+$uang_makan+$hasil_lembur+$tunjangan_kerja+$tunjangan_kehadiran;
    
        //         if (empty($pengerjaan_rit_weekly->minus_1)) {
        //             $minus_1 = 0;
        //             $keterangan_minus_1 = '';
        //         }else{
        //             $explode_minus_1 = explode("|",$pengerjaan_rit_weekly->minus_1);
        //             $minus_1 = $explode_minus_1[0];
        //             $keterangan_minus_1 = $explode_minus_1[1];
        //         }
    
        //         if (empty($pengerjaan_rit_weekly->minus_2)) {
        //             $minus_2 = 0;
        //             $keterangan_minus_2 = '';
        //         }else{
        //             $explode_minus_2 = explode("|",$pengerjaan_rit_weekly->minus_2);
        //             $minus_2 = $explode_minus_2[0];
        //             $keterangan_minus_2 = $explode_minus_2[1];
        //         }
    
        //         if (empty($pengerjaan_rit_weekly->jht)) {
        //             $jht = 0;
        //         }else{
        //             $jht = $pengerjaan_rit_weekly->jht;
        //         }
    
        //         if (empty($pengerjaan_rit_weekly->bpjs_kesehatan)) {
        //             $bpjs_kesehatan = 0;
        //         }else{
        //             $bpjs_kesehatan = $pengerjaan_rit_weekly->bpjs_kesehatan;
        //         }
    
        //         if (empty($pengerjaan_rit_weekly->pensiun)) {
        //             $pensiun = 0;
        //         }else{
        //             $pensiun = $pengerjaan_rit_weekly->pensiun;
        //         }
    
        //         $total_upah_diterima = $total_gaji-$minus_1-$minus_2-$jht-$bpjs_kesehatan-$pensiun;
    
        //         ### LINE 6 ###
        //         $pdf->SetX($x);
        //         $pdf->SetFont('Arial','',9);
        //         $pdf->Cell(35,5,"Lembur ".'( '.$total_jam_lembur.' Jam )','L',0,'L'); 
        //         $pdf->Cell(22,5,'','',0,'L');
        //         $pdf->Cell(3,5,'Rp','',0,'L');
        //         $pdf->Cell(35,5,number_format($hasil_lembur,0,',','.'),'R',0,'R');
        //         $pdf->ln(4);
        
        //         ### LINE 7 ###
        //         $pdf->SetX($x);
        //         $pdf->SetFont('Arial','',9);
        //         $pdf->Cell(35,5,'Insentif Kehadiran','L',0,'L'); 
        //         $pdf->Cell(22,5,'','',0,'L');
        //         $pdf->Cell(3,5,'Rp','',0,'L');
        //         $pdf->Cell(35,5,number_format($tunjangan_kehadiran,0,',','.'),'R',0,'R');
        //         $pdf->ln(4);
        
        //         ### LINE 8 ###
        //         $pdf->SetX($x);
        //         $pdf->SetFont('Arial','',9);
        //         $pdf->Cell(35,5,'Tunjangan Kerja','L',0,'L'); 
        //         $pdf->Cell(22,5,'','',0,'L');
        //         $pdf->Cell(3,5,'Rp','',0,'L');
        //         $pdf->Cell(35,5,number_format($tunjangan_kerja,0,',','.'),'R',0,'R');
        //         $pdf->ln(4);
        
        //         ### LINE 9 ###
        //         $pdf->SetX($x);
        //         $pdf->SetFont('Arial','',9);
        //         $pdf->Cell(12,5,'Plus','L',0,'L'); 
        //         $pdf->Cell(45,5,"( ".$keterangan_plus_1." )",'',0,'L');
        //         $pdf->Cell(3,5,'Rp','',0,'L');
        //         $pdf->Cell(35,5,number_format($plus_1,0,',','.'),'R',0,'R');
        //         $pdf->ln(4);
        
        //         $pdf->SetX($x);
        //         $pdf->SetFont('Arial','',9);
        //         $pdf->Cell(12,5,'','L',0,'L'); 
        //         $pdf->Cell(45,5,"( ".$keterangan_plus_2." )",'',0,'L');
        //         $pdf->Cell(3,5,'Rp','',0,'L');
        //         $pdf->Cell(35,5,number_format($plus_2,0,',','.'),'R',0,'R');
        //         $pdf->ln(4);
        
        //         $pdf->SetX($x);
        //         $pdf->SetFont('Arial','',9);
        //         $pdf->Cell(12,5,'','L',0,'L'); 
        //         $pdf->Cell(45,5,"( ".$keterangan_plus_3." )",'',0,'L');
        //         $pdf->Cell(3,5,'Rp','',0,'L');
        //         $pdf->Cell(35,5,number_format($plus_3,0,',','.'),'R',0,'R');
        //         $pdf->ln(4);
        
        //         $pdf->SetX($x);
        //         $pdf->SetFont('Arial','',9);
        //         $pdf->Cell(35,5,'Uang Makan','L',0,'L'); 
        //         $pdf->Cell(22,5,'','',0,'L');
        //         $pdf->Cell(3,5,'Rp','',0,'L');
        //         $pdf->Cell(35,5,number_format($uang_makan,0,',','.'),'R',0,'R');
        //         $pdf->ln(5);
        
        //         $pdf->SetX($x);
        //         $pdf->SetFont('Arial','B',9);
        //         $pdf->Cell(20,5,'POTONGAN','L',0,'L'); 
        //         $pdf->Cell(37,5,'BPJS > JHT + JP','',0,'L');
        //         $pdf->Cell(3,5,'Rp','',0,'L');
        //         $pdf->Cell(35,5,number_format($jht+$pensiun,0,',','.'),'R',0,'R');
        //         $pdf->ln(4);
        
        //         $pdf->SetX($x);
        //         $pdf->SetFont('Arial','B',9);
        //         $pdf->Cell(20,5,'','L',0,'L'); 
        //         $pdf->Cell(37,5,'BPJS Kesehatan','',0,'L');
        //         $pdf->Cell(3,5,'Rp','',0,'L');
        //         $pdf->Cell(35,5,number_format($bpjs_kesehatan,0,',','.'),'R',0,'R');
        //         $pdf->ln(4);
        
        //         $pdf->SetX($x);
        //         $pdf->SetFont('Arial','B',9);
        //         $pdf->Cell(20,5,'','L',0,'L'); 
        //         $pdf->Cell(37,5,"( ".$keterangan_minus_1." )",'',0,'L');
        //         $pdf->Cell(3,5,'Rp','',0,'L');
        //         $pdf->Cell(35,5,number_format($minus_1,0,',','.'),'R',0,'R');
        //         $pdf->ln(4);
        
        //         $pdf->SetX($x);
        //         $pdf->SetFont('Arial','B',9);
        //         $pdf->Cell(20,5,'','L',0,'L'); 
        //         $pdf->Cell(37,5,"( ".$keterangan_minus_2." )",'',0,'L');
        //         $pdf->Cell(3,5,'Rp','',0,'L');
        //         $pdf->Cell(35,5,number_format($minus_2,0,',','.'),'R',0,'R');
        //         $pdf->ln(5);
        
        //         $pdf->SetX($x);
        //         $pdf->SetFont('Arial','B',9);
        //         $pdf->Cell(35,5,'TOTAL DITERIMA','BL',0,'L'); 
        //         $pdf->Cell(22,5,'','B',0,'L');
        //         $pdf->Cell(3,5,'Rp','B',0,'L');
        //         $pdf->Cell(35,5,number_format($total_upah_diterima,0,',','.'),'BR',0,'R');
        //         $pdf->ln(3);
        
        //         $no=$no+1;
        //         if ($no%2==0)
        //         {
        //             $page=round($no/2);
        //             $pdf->ln(1);
        //             $pdf->SetFont('Arial','',7);
        //             $pdf->Cell(35,5,"- page $page -",'',0,''); 
        //             $pdf->ln(12);
        //         }
        //         $pdf->ln(8);
        //     }
        // }

        $pdf->Output('Slip Gaji Supir RIT '.Carbon::parse($exp_tgl_awal[0] . '-' . $exp_tgl_awal[1] . '-' . $exp_tgl_awal[2])->isoFormat('D MMMM').' sd '.Carbon::parse($exp_tgl_akhir[0] . '-' . $exp_tgl_akhir[1] . '-' . $exp_tgl_akhir[2])->isoFormat('D MMMM YYYY').'.pdf', "I");
        exit;
        // return view('backend.payrol.penggajian.supir_rit.bank');
    }

    public function supir_rit_bank($kode_pengerjaan)
    {
        $data['kode_pengerjaan'] = $kode_pengerjaan;
        $data['new_data_pengerjaan'] = $this->newDataPengerjaan->where('kode_pengerjaan',$kode_pengerjaan)->first();
        $data['pengerjaan_supir_rits'] = $this->pengerjaanRitWeekly->select([
                                                            'pengerjaan_supir_rit_weekly.id as id',
                                                            'pengerjaan_supir_rit_weekly.karyawan_supir_rit_id as karyawan_supir_rit_id',
                                                            'operator_supir_rit_karyawan.nik as nik',
                                                            'biodata_karyawan.nama as nama',
                                                            'biodata_karyawan.rekening as rekening',
                                                            'pengerjaan_supir_rit_weekly.total_hasil as total_hasil',
                                                        ])
                                                        ->leftJoin('operator_supir_rit_karyawan','operator_supir_rit_karyawan.id','=','pengerjaan_supir_rit_weekly.karyawan_supir_rit_id')
                                                        ->leftJoin('itic_emp_new.biodata_karyawan','biodata_karyawan.nik','=','operator_supir_rit_karyawan.nik')
                                                        ->where('kode_pengerjaan',$kode_pengerjaan)
                                                        ->orderBy('biodata_karyawan.nama','asc')
                                                        ->get();
        // return view('backend.payrol.penggajian.supir_rit.bank',$data);
        $pdf = Pdf::loadView('backend.payrol.penggajian.supir_rit.bank',$data);
        return $pdf->stream();
    }

    public function supir_rit_weekly_report($kode_pengerjaan)
    {
        $data['kode_pengerjaan'] = $kode_pengerjaan;
        $data['new_data_pengerjaan'] = $this->newDataPengerjaan->where('kode_pengerjaan',$kode_pengerjaan)->first();

        $data['rit_posisis'] = $this->ritPosisi->all();
        // return view('backend.payrol.penggajian.supir_rit.weekly_report',$data);
        $pdf = Pdf::loadView('backend.payrol.penggajian.supir_rit.weekly_report',$data);
        $pdf->setPaper('a4','landscape')->setWarnings(false);
        return $pdf->stream();
    }

    public function borongan_detail_kirim_slip_gaji($kode_pengerjaan)
    {
        $data['new_data_pengerjaan'] = $this->newDataPengerjaan->where('kode_pengerjaan',$kode_pengerjaan)->first();
        // dd($data);
        // $data['pengerjaan_weeklys'] = $this->pengerjaanWeekly->where('kode_pengerjaan',$kode_pengerjaan)
        //                                                     // ->limit($this->sendLimit)
        //                                                     // ->get();
        //                                                     ->paginate($this->sendLimit);
        $data['pengerjaan_weeklys'] = $this->pengerjaanWeekly->where('kode_pengerjaan',$kode_pengerjaan)
                                                            ->paginate($this->sendLimit);
        // dd($data);
        return view('backend.payrol.penggajian.borongan.detail_kirim_slip',$data);
    }

    public function borongan_cek_slip_gaji($kode_pengerjaan,$id)
    {
        $data['id'] = $id;
        $data['kode_pengerjaan'] = $kode_pengerjaan;
        $pdf = Pdf::loadView('backend.payrol.penggajian.borongan.pdf_cek_gaji2',$data);
        $pdf->setPaper(array(0,0,560,380));
        return $pdf->stream();
    }

    // public function borongan_kirim_slip_gaji($kode_pengerjaan,$id)
    // {
    //     // $pdf = new Fpdf('L', 'mm', array(115,90));
    //     $data['id'] = $id;
    //     $data['kode_pengerjaan'] = $kode_pengerjaan;
        
    //     $pdf = Pdf::loadView('backend.payrol.penggajian.borongan.pdf_cek_gaji2',$data);
    //     $pdf->setPaper(array(0,0,560,360));

    //     // $data['emails'] = [
    //     //     [
    //     //         'email' => 'rioanugrah999@gmail.com'
    //     //     ],
    //     //     [
    //     //         'email' => 'ronyachmad91@gmail.com'
    //     //     ],
    //     //     [
    //     //         'email' => 'ict.indonesiantobacco@gmail.com'
    //     //     ],
    //     //     [
    //     //         'email' => 'ict.indonesiantobacco.backup@gmail.com'
    //     //     ],
    //     //     [
    //     //         'email' => 'zonnete.bd@gmail.com'
    //     //     ],
    //     // ];

    //     // foreach ($data['emails'] as $key => $email) {
    //     //     Mail::send('backend.payrol.penggajian.borongan.pdf_cek_gaji1',$data, function($message) use($data,$pdf,$email){
    //     //         $message->to($email['email'])
    //     //                 ->subject('Laporan Slip Gaji '.date('d-m-Y'))
    //     //                 ->attachData($pdf->output(), 'Laporan Slip Gaji.pdf');
    //     //     });
    //     // }

    //     // Mail::send('backend.payrol.penggajian.borongan.pdf_cek_gaji1',$data, function($message) use($data,$pdf,$email){
    //     //     $message->to($email['email'])
    //     //             ->subject('Laporan Slip Gaji '.date('d-m-Y'))
    //     //             ->attachData($pdf->output(), 'Laporan Slip Gaji.pdf');
    //     // });

    //     Mail::send('backend.payrol.penggajian.borongan.pdf_cek_gaji1',$data, function($message) use($data,$pdf,$email){
    //         $message->to()
    //                 ->subject('Laporan Slip Gaji '.date('d-m-Y'))
    //                 ->attachData($pdf->output(), 'Laporan Slip Gaji '.date('d-m-Y').'.pdf');
    //     });

    //     // return $pdf->stream();
    //     // return view('backend.payrol.penggajian.borongan.pdf_cek_gaji2');
    // }

    public function borongan_kirim_slip_gaji(Request $request,$kode_pengerjaan)
    {
        $pengerjaan_weeklys = $this->pengerjaanWeekly->where('kode_pengerjaan',$kode_pengerjaan)
                                                    // ->whereIn('id',[11365])
                                                    ->whereIn('id',$request->id)
                                                    // ->limit(50)
                                                    ->get();
        // dd($pengerjaan_weeklys);
        // dd($pengerjaan_weeklys);
        foreach ($pengerjaan_weeklys as $key => $pengerjaan_weekly) {
            $data['id'] = $pengerjaan_weekly->id;
            $data['kode_pengerjaan'] = $kode_pengerjaan;
            // $data['nama'] = $pengerjaan_weekly->operator_karyawan->nama;
            $new_data_pengerjaan = $this->newDataPengerjaan->where('kode_pengerjaan',$kode_pengerjaan)->first();
            $explode_tanggal_pengerjaans = explode('#', $new_data_pengerjaan['tanggal']);
            $exp_tanggals = array_filter($explode_tanggal_pengerjaans);
            $a = count($exp_tanggals);
            $exp_tgl_awal = explode('-', $exp_tanggals[1]);
            $exp_tgl_akhir = explode('-', $exp_tanggals[$a]);
            
            $data['tanggal'] = Carbon::parse($exp_tgl_awal[0] . '-' . $exp_tgl_awal[1] . '-' . $exp_tgl_awal[2])->isoFormat('D MMMM').' sampai '.Carbon::parse($exp_tgl_akhir[0] . '-' . $exp_tgl_akhir[1] . '-' . $exp_tgl_akhir[2])->isoFormat('D MMMM YYYY');
            $data['nama'] = $pengerjaan_weekly->operator_karyawan->biodata_karyawan->nama;
            
            $kirim_gaji = $this->kirim_gaji->where('pengerjaan_id',$pengerjaan_weekly->id)
                                            ->where('nik',$pengerjaan_weekly->operator_karyawan->nik)
                                            ->first();
                                            // dd($kirim_gaji);
            if (empty($kirim_gaji)) {
                $pdf = Pdf::loadView('backend.payrol.penggajian.borongan.pdf_cek_gaji2',$data);
                $pdf->setPaper(array(0,0,560,380));   
                $pdf->setEncryption(Carbon::create($pengerjaan_weekly->operator_karyawan->biodata_karyawan->tgl_lahir)->format('dmY'),Carbon::create($pengerjaan_weekly->operator_karyawan->biodata_karyawan->tgl_lahir)->format('dmY'));
                
                // Mail::send('backend.payrol.penggajian.email_kirim_gaji',$data, function($message) use($data,$pdf,$pengerjaan_weekly){
                //     $message->to('rioanugrah999@gmail.com')
                //             ->subject('Laporan Slip Gaji '.$pengerjaan_weekly->operator_karyawan->biodata_karyawan->nama.' '.date('d-m-Y'))
                //             ->attachData($pdf->output(), 'Laporan Slip Gaji '.$pengerjaan_weekly->operator_karyawan->biodata_karyawan->nama.' '.date('d-m-Y').'.pdf');
                // });
                Mail::send('backend.payrol.penggajian.email_kirim_gaji',$data, function($message) use($data,$pdf,$pengerjaan_weekly){
                    $message->to(strtolower($pengerjaan_weekly->operator_karyawan->biodata_karyawan->email))
                            ->subject('Laporan Slip Gaji '.$pengerjaan_weekly->operator_karyawan->biodata_karyawan->nama.' '.date('d-m-Y'))
                            ->attachData($pdf->output(), 'Laporan Slip Gaji '.$pengerjaan_weekly->operator_karyawan->biodata_karyawan->nama.' '.date('d-m-Y').'.pdf');
                });
            }

            if (Mail::failures()) {
                $this->kirim_gaji->firstOrCreate(
                    [
                        'kode_pengerjaan' => $kode_pengerjaan,
                        'nik' => $pengerjaan_weekly->operator_karyawan->nik,
                        'pengerjaan_id' => $pengerjaan_weekly->id,
                    ],
                    [
                        'kode_payrol' => $pengerjaan_weekly->kode_payrol,
                        'pengerjaan_id' => $pengerjaan_weekly->id,
                        'nama_karyawan' => $pengerjaan_weekly->operator_karyawan->biodata_karyawan->nama,
                        // 'nik' => $pengerjaan_weekly->operator_karyawan->nik,
                        'nominal_gaji' => $request['nominal_gaji'][$key],
                        'status' => 'gagal'
                    ]
                );
            }

            $this->kirim_gaji->firstOrCreate(
                [
                    'kode_pengerjaan' => $kode_pengerjaan,
                    'nik' => $pengerjaan_weekly->operator_karyawan->nik,
                    'pengerjaan_id' => $pengerjaan_weekly->id,
                ],
                [
                    'kode_payrol' => $pengerjaan_weekly->kode_payrol,
                    'pengerjaan_id' => $pengerjaan_weekly->id,
                    'nama_karyawan' => $pengerjaan_weekly->operator_karyawan->biodata_karyawan->nama,
                    // 'nik' => $pengerjaan_weekly->operator_karyawan->nik,
                    'nominal_gaji' => $request['nominal_gaji'][$key],
                    'status' => 'terkirim'
                ]
            );
        }

        // if (Mail::failures()) {
        //     // return response showing failed emails
        // }

        return response()->json([
            'success' => true,
            'message_title' => 'Berhasil!',
            'message_content' => 'Kirim Slip Gaji Berhasil Terkirim'
        ]);
    }

    public function borongan_cek_email_slip_gaji($kode_pengerjaan)
    {
        // $data['kode_pengerjaan'] = $kode_pengerjaan;
        $data['new_data_pengerjaan'] = $this->newDataPengerjaan->where('kode_pengerjaan',$kode_pengerjaan)->first();
        $data['check_kirim_gajis'] = $this->kirim_gaji->where('kode_pengerjaan',$kode_pengerjaan)
                                            ->get();
        
        return view('backend.payrol.penggajian.borongan.cek_kirim_gaji',$data);
    }

    public function borongan_cek_email_kirim_ulang($kode_pengerjaan,$id)
    {
        $kirim_gaji = $this->kirim_gaji->where('id',$id)
                                        ->first();
        // dd($kirim_gaji->pengerjaan_weekly->operator_karyawan->biodata_karyawan->nama);
        $data['id'] = $kirim_gaji->pengerjaan_weekly->id;
        $data['kode_pengerjaan'] = $kode_pengerjaan;
        $data['nama'] = $kirim_gaji->pengerjaan_weekly->operator_karyawan->biodata_karyawan->nama;

        $new_data_pengerjaan = $this->newDataPengerjaan->where('kode_pengerjaan',$kode_pengerjaan)->first();

        $explode_tanggal_pengerjaans = explode('#', $new_data_pengerjaan['tanggal']);
        $exp_tanggals = array_filter($explode_tanggal_pengerjaans);
        $a = count($exp_tanggals);
        $exp_tgl_awal = explode('-', $exp_tanggals[1]);
        $exp_tgl_akhir = explode('-', $exp_tanggals[$a]);
        $data['tanggal'] = Carbon::parse($exp_tgl_awal[0] . '-' . $exp_tgl_awal[1] . '-' . $exp_tgl_awal[2])->isoFormat('D MMMM').' sampai '.Carbon::parse($exp_tgl_akhir[0] . '-' . $exp_tgl_akhir[1] . '-' . $exp_tgl_akhir[2])->isoFormat('D MMMM YYYY');
        // dd(Carbon::parse($exp_tgl_awal[0] . '-' . $exp_tgl_awal[1] . '-' . $exp_tgl_awal[2])->isoFormat('D MMMM').' s/d '.Carbon::parse($exp_tgl_akhir[0] . '-' . $exp_tgl_akhir[1] . '-' . $exp_tgl_akhir[2])->isoFormat('D MMMM YYYY'));
        
        $pdf = Pdf::loadView('backend.payrol.penggajian.borongan.pdf_cek_gaji2',$data);
        $pdf->setPaper(array(0,0,560,380));   
        $pdf->setEncryption(Carbon::create($kirim_gaji->pengerjaan_weekly->operator_karyawan->biodata_karyawan->tgl_lahir)->format('dmY'),Carbon::create($kirim_gaji->pengerjaan_weekly->operator_karyawan->biodata_karyawan->tgl_lahir)->format('dmY'));

        Mail::send('backend.payrol.penggajian.email_kirim_gaji',$data, function($message) use($data,$pdf,$kirim_gaji){
            $message->to(strtolower($kirim_gaji->pengerjaan_weekly->operator_karyawan->biodata_karyawan->email))
                    ->subject('Laporan Slip Gaji '.$kirim_gaji->pengerjaan_weekly->operator_karyawan->biodata_karyawan->nama.' '.date('d-m-Y'))
                    ->attachData($pdf->output(), 'Laporan Slip Gaji '.$kirim_gaji->pengerjaan_weekly->operator_karyawan->biodata_karyawan->nama.' '.date('d-m-Y').'.pdf');
        });

        if (Mail::failures()) {
            $this->kirim_gaji->updateOrCreate(
                [
                    'kode_pengerjaan' => $kode_pengerjaan,
                    'nik' => $kirim_gaji->nik,
                ],
                [
                    'kode_payrol' => $kirim_gaji->kode_payrol,
                    'pengerjaan_id' => $kirim_gaji->pengerjaan_id,
                    'nama_karyawan' => $kirim_gaji->nama_karyawan,
                    // 'nik' => $pengerjaan_weekly->operator_karyawan->nik,
                    'nominal_gaji' => $kirim_gaji->nominal_gaji,
                    'status' => 'gagal'
                ]
            );
        }

        $this->kirim_gaji->updateOrCreate(
            [
                'kode_pengerjaan' => $kode_pengerjaan,
                'nik' => $kirim_gaji->nik,
            ],
            [
                'kode_payrol' => $kirim_gaji->kode_payrol,
                'pengerjaan_id' => $kirim_gaji->pengerjaan_id,
                'nama_karyawan' => $kirim_gaji->nama_karyawan,
                // 'nik' => $pengerjaan_weekly->operator_karyawan->nik,
                'nominal_gaji' => $kirim_gaji->nominal_gaji,
                'status' => 'terkirim'
            ]
        );

        return response()->json([
            'success' => true,
            'message_title' => 'Berhasil',
            'message_content' => 'Slip Gaji Berhasil Dikirim'
        ]);
    }

    public function harian_detail_kirim_slip_gaji($kode_pengerjaan)
    {
        $data['new_data_pengerjaan'] = $this->newDataPengerjaan->where('kode_pengerjaan',$kode_pengerjaan)->first();

        $data['pengerjaan_harians'] = $this->pengerjaanHarian->where('kode_pengerjaan',$kode_pengerjaan)
                                                            // ->limit(1)
                                                            ->paginate($this->sendLimit);
        
        return view('backend.payrol.penggajian.harian.detail_kirim_slip',$data);
    }

    public function harian_cek_slip_gaji($kode_pengerjaan,$id)
    {
        $data['id'] = $id;
        $data['kode_pengerjaan'] = $kode_pengerjaan;
        $pdf = Pdf::loadView('backend.payrol.penggajian.harian.pdf_cek_gaji',$data);
        $pdf->setPaper(array(0,0,560,380));
        return $pdf->stream();
    }

    public function harian_kirim_slip_gaji(Request $request,$kode_pengerjaan)
    {
        $pengerjaan_harians = $this->pengerjaanHarian->where('kode_pengerjaan',$kode_pengerjaan)
                                                    ->whereIn('id',$request->id)
                                                    ->get();
        foreach ($pengerjaan_harians as $key => $pengerjaan_harian) {
            $data['id'] = $pengerjaan_harian->id;
            $data['kode_pengerjaan'] = $kode_pengerjaan;
            $data['nama'] = $pengerjaan_harian->operator_karyawan->biodata_karyawan->nama;

            $new_data_pengerjaan = $this->newDataPengerjaan->where('kode_pengerjaan',$kode_pengerjaan)->first();
            $explode_tanggal_pengerjaans = explode('#', $new_data_pengerjaan['tanggal']);
            $exp_tanggals = array_filter($explode_tanggal_pengerjaans);
            $a = count($exp_tanggals);
            $exp_tgl_awal = explode('-', $exp_tanggals[1]);
            $exp_tgl_akhir = explode('-', $exp_tanggals[$a]);

            $data['tanggal'] = Carbon::parse($exp_tgl_awal[0] . '-' . $exp_tgl_awal[1] . '-' . $exp_tgl_awal[2])->isoFormat('D MMMM').' sampai '.Carbon::parse($exp_tgl_akhir[0] . '-' . $exp_tgl_akhir[1] . '-' . $exp_tgl_akhir[2])->isoFormat('D MMMM YYYY');

            $kirim_gaji = $this->kirim_gaji->where('pengerjaan_id',$pengerjaan_harian->id)
                                            ->where('nik',$pengerjaan_harian->operator_karyawan->nik)
                                            ->first();
            // dd($kirim_gaji);

            if (empty($kirim_gaji)) {
                $pdf = Pdf::loadView('backend.payrol.penggajian.harian.pdf_cek_gaji',$data);
                $pdf->setPaper(array(0,0,560,380));   
                $pdf->setEncryption(Carbon::create($pengerjaan_harian->operator_karyawan->biodata_karyawan->tgl_lahir)->format('dmY'),Carbon::create($pengerjaan_harian->operator_karyawan->biodata_karyawan->tgl_lahir)->format('dmY'));
    
                Mail::send('backend.payrol.penggajian.email_kirim_gaji',$data, function($message) use($data,$pdf,$pengerjaan_harian){
                    $message->to(strtolower($pengerjaan_harian->operator_karyawan->biodata_karyawan->email))
                            ->subject('Laporan Slip Gaji '.$pengerjaan_harian->operator_karyawan->biodata_karyawan->nama.' '.date('d-m-Y'))
                            ->attachData($pdf->output(), 'Laporan Slip Gaji '.$pengerjaan_harian->operator_karyawan->biodata_karyawan->nama.' '.date('d-m-Y').'.pdf');
                });
            }

            if (Mail::failures()) {
                $this->kirim_gaji->firstOrCreate(
                    [
                        'kode_pengerjaan' => $kode_pengerjaan,
                        'nik' => $pengerjaan_harian->operator_karyawan->nik,
                    ],
                    [
                        'kode_payrol' => $pengerjaan_harian->kode_payrol,
                        'pengerjaan_id' => $pengerjaan_harian->id,
                        'nama_karyawan' => $pengerjaan_harian->operator_karyawan->biodata_karyawan->nama,
                        // 'nik' => $pengerjaan_weekly->operator_karyawan->nik,
                        'nominal_gaji' => $request['nominal_gaji'][$key],
                        'status' => 'gagal'
                    ]
                );
            }

            $this->kirim_gaji->firstOrCreate(
                [
                    'kode_pengerjaan' => $kode_pengerjaan,
                    'nik' => $pengerjaan_harian->operator_karyawan->nik,
                ],
                [
                    'kode_payrol' => $pengerjaan_harian->kode_payrol,
                    'pengerjaan_id' => $pengerjaan_harian->id,
                    'nama_karyawan' => $pengerjaan_harian->operator_karyawan->biodata_karyawan->nama,
                    // 'nik' => $pengerjaan_weekly->operator_karyawan->nik,
                    'nominal_gaji' => $request['nominal_gaji'][$key],
                    'status' => 'terkirim'
                ]
            );
        }

        // if (Mail::failures()) {
        //     // return response showing failed emails
        // }

        return response()->json([
            'success' => true,
            'message_title' => 'Berhasil!',
            'message_content' => 'Kirim Slip Gaji Berhasil Terkirim'
        ]);
    }

    public function harian_cek_email_slip_gaji($kode_pengerjaan)
    {
        $data['new_data_pengerjaan'] = $this->newDataPengerjaan->where('kode_pengerjaan',$kode_pengerjaan)->first();
        $data['check_kirim_gajis'] = $this->kirim_gaji->where('kode_pengerjaan',$kode_pengerjaan)
                                                    ->get();
        return view('backend.payrol.penggajian.harian.cek_kirim_gaji',$data);
    }

    public function harian_cek_email_kirim_ulang($kode_pengerjaan,$id)
    {
        $kirim_gaji = $this->kirim_gaji->where('id',$id)->first();

        $data['id'] = $kirim_gaji->pengerjaan_harian->id;
        $data['kode_pengerjaan'] = $kode_pengerjaan;
        $data['nama'] = $kirim_gaji->pengerjaan_harian->operator_karyawan->biodata_karyawan->nama;

        $new_data_pengerjaan = $this->newDataPengerjaan->where('kode_pengerjaan',$kode_pengerjaan)->first();

        $explode_tanggal_pengerjaans = explode('#', $new_data_pengerjaan['tanggal']);
        $exp_tanggals = array_filter($explode_tanggal_pengerjaans);
        $a = count($exp_tanggals);
        $exp_tgl_awal = explode('-', $exp_tanggals[1]);
        $exp_tgl_akhir = explode('-', $exp_tanggals[$a]);
        $data['tanggal'] = Carbon::parse($exp_tgl_awal[0] . '-' . $exp_tgl_awal[1] . '-' . $exp_tgl_awal[2])->isoFormat('D MMMM').' sampai '.Carbon::parse($exp_tgl_akhir[0] . '-' . $exp_tgl_akhir[1] . '-' . $exp_tgl_akhir[2])->isoFormat('D MMMM YYYY');

        $pdf = Pdf::loadView('backend.payrol.penggajian.harian.pdf_cek_gaji',$data);
        $pdf->setPaper(array(0,0,560,380));   
        $pdf->setEncryption(Carbon::create($kirim_gaji->pengerjaan_harian->operator_karyawan->biodata_karyawan->tgl_lahir)->format('dmY'),Carbon::create($kirim_gaji->pengerjaan_harian->operator_karyawan->biodata_karyawan->tgl_lahir)->format('dmY'));

        Mail::send('backend.payrol.penggajian.email_kirim_gaji',$data, function($message) use($data,$pdf,$kirim_gaji){
            $message->to(strtolower($kirim_gaji->pengerjaan_harian->operator_karyawan->biodata_karyawan->email))
                    ->subject('Laporan Slip Gaji '.$kirim_gaji->pengerjaan_harian->operator_karyawan->biodata_karyawan->nama.' '.date('d-m-Y'))
                    ->attachData($pdf->output(), 'Laporan Slip Gaji '.$kirim_gaji->pengerjaan_harian->operator_karyawan->biodata_karyawan->nama.' '.date('d-m-Y').'.pdf');
        });

        if (Mail::failures()) {
            $this->kirim_gaji->updateOrCreate(
                [
                    'kode_pengerjaan' => $kode_pengerjaan,
                    'nik' => $kirim_gaji->nik,
                ],
                [
                    'kode_payrol' => $kirim_gaji->kode_payrol,
                    'pengerjaan_id' => $kirim_gaji->pengerjaan_id,
                    'nama_karyawan' => $kirim_gaji->nama_karyawan,
                    // 'nik' => $pengerjaan_weekly->operator_karyawan->nik,
                    'nominal_gaji' => $kirim_gaji->nominal_gaji,
                    'status' => 'gagal'
                ]
            );
        }

        $this->kirim_gaji->updateOrCreate(
            [
                'kode_pengerjaan' => $kode_pengerjaan,
                'nik' => $kirim_gaji->nik,
            ],
            [
                'kode_payrol' => $kirim_gaji->kode_payrol,
                'pengerjaan_id' => $kirim_gaji->pengerjaan_id,
                'nama_karyawan' => $kirim_gaji->nama_karyawan,
                // 'nik' => $pengerjaan_weekly->operator_karyawan->nik,
                'nominal_gaji' => $kirim_gaji->nominal_gaji,
                'status' => 'terkirim'
            ]
        );

        return response()->json([
            'success' => true,
            'message_title' => 'Berhasil',
            'message_content' => 'Slip Gaji Berhasil Dikirim'
        ]);
    }

    public function supir_rit_detail_kirim_slip_gaji($kode_pengerjaan)
    {
        $data['new_data_pengerjaan'] = $this->newDataPengerjaan->where('kode_pengerjaan',$kode_pengerjaan)->first();
        $data['pengerjaan_rit_weeklys'] = $this->pengerjaanRitWeekly->where('kode_pengerjaan',$kode_pengerjaan)
                                                                    // ->get();
                                                                    ->paginate($this->sendLimit);
        // dd($data);
        return view('backend.payrol.penggajian.supir_rit.detail_kirim_slip',$data);
    }

    public function supir_rit_cek_slip_gaji($kode_pengerjaan,$id)
    {
        $data['id'] = $id;
        $data['kode_pengerjaan'] = $kode_pengerjaan;

        $pdf = Pdf::loadView('backend.payrol.penggajian.supir_rit.pdf_cek_gaji',$data);
        $pdf->setPaper(array(0,0,400,500));
        return $pdf->stream();
    }

    public function supir_rit_kirim_slip_gaji(Request $request,$kode_pengerjaan)
    {
        $pengerjaan_rit_weeklys = $this->pengerjaanRitWeekly->where('kode_pengerjaan',$kode_pengerjaan)
                                                            ->whereIn('id',$request->id)
                                                            ->get();
        foreach ($pengerjaan_rit_weeklys as $key => $pengerjaan_rit_weekly) {
            $data['id'] = $pengerjaan_rit_weekly->id;
            $data['kode_pengerjaan'] = $kode_pengerjaan;

            $new_data_pengerjaan = $this->newDataPengerjaan->where('kode_pengerjaan',$kode_pengerjaan)->first();
            
            $explode_tanggal_pengerjaans = explode('#', $new_data_pengerjaan['tanggal']);
            $exp_tanggals = array_filter($explode_tanggal_pengerjaans);
            $a = count($exp_tanggals);
            $exp_tgl_awal = explode('-', $exp_tanggals[1]);
            $exp_tgl_akhir = explode('-', $exp_tanggals[$a]);
            
            $data['tanggal'] = Carbon::parse($exp_tgl_awal[0] . '-' . $exp_tgl_awal[1] . '-' . $exp_tgl_awal[2])->isoFormat('D MMMM').' sampai '.Carbon::parse($exp_tgl_akhir[0] . '-' . $exp_tgl_akhir[1] . '-' . $exp_tgl_akhir[2])->isoFormat('D MMMM YYYY');
            $data['nama'] = $pengerjaan_rit_weekly->operator_supir_rit->biodata_karyawan->nama;

            $kirim_gaji = $this->kirim_gaji->where('pengerjaan_id',$pengerjaan_rit_weekly->id)
                                            ->where('nik',$pengerjaan_rit_weekly->operator_supir_rit->nik)
                                            ->first();
            if (empty($kirim_gaji)) {
                $pdf = Pdf::loadView('backend.payrol.penggajian.supir_rit.pdf_cek_gaji',$data);
                $pdf->setPaper(array(0,0,400,500));   
                $pdf->setEncryption(Carbon::create($pengerjaan_rit_weekly->operator_supir_rit->biodata_karyawan->tgl_lahir)->format('dmY'),Carbon::create($pengerjaan_rit_weekly->operator_supir_rit->biodata_karyawan->tgl_lahir)->format('dmY'));
                
                Mail::send('backend.payrol.penggajian.email_kirim_gaji',$data, function($message) use($data,$pdf,$pengerjaan_rit_weekly){
                    $message->to(strtolower($pengerjaan_rit_weekly->operator_supir_rit->biodata_karyawan->email))
                            ->subject('Laporan Slip Gaji '.$pengerjaan_rit_weekly->operator_supir_rit->biodata_karyawan->nama.' '.date('d-m-Y'))
                            ->attachData($pdf->output(), 'Laporan Slip Gaji '.$pengerjaan_rit_weekly->operator_supir_rit->biodata_karyawan->nama.' '.date('d-m-Y').'.pdf');
                });
            }
            if (Mail::failures()) {
                $this->kirim_gaji->firstOrCreate(
                    [
                        'kode_pengerjaan' => $kode_pengerjaan,
                        'nik' => $pengerjaan_rit_weekly->operator_supir_rit->nik,
                    ],
                    [
                        'kode_payrol' => $pengerjaan_rit_weekly->kode_payrol,
                        'pengerjaan_id' => $pengerjaan_rit_weekly->id,
                        'nama_karyawan' => $pengerjaan_rit_weekly->operator_supir_rit->biodata_karyawan->nama,
                        // 'nik' => $pengerjaan_weekly->operator_karyawan->nik,
                        'nominal_gaji' => $request['nominal_gaji'][$key],
                        'status' => 'gagal'
                    ]
                );
            }

            $this->kirim_gaji->firstOrCreate(
                [
                    'kode_pengerjaan' => $kode_pengerjaan,
                    'nik' => $pengerjaan_rit_weekly->operator_supir_rit->nik,
                ],
                [
                    'kode_payrol' => $pengerjaan_rit_weekly->kode_payrol,
                    'pengerjaan_id' => $pengerjaan_rit_weekly->id,
                    'nama_karyawan' => $pengerjaan_rit_weekly->operator_supir_rit->biodata_karyawan->nama,
                    // 'nik' => $pengerjaan_weekly->operator_karyawan->nik,
                    'nominal_gaji' => $request['nominal_gaji'][$key],
                    'status' => 'terkirim'
                ]
            );
        }

        return response()->json([
            'success' => true,
            'message_title' => 'Berhasil!',
            'message_content' => 'Kirim Slip Gaji Berhasil Terkirim'
        ]);
    }

    public function supir_rit_cek_email_slip_gaji($kode_pengerjaan)
    {
        $data['new_data_pengerjaan'] = $this->newDataPengerjaan->where('kode_pengerjaan',$kode_pengerjaan)->first();
        $data['check_kirim_gajis'] = $this->kirim_gaji->where('kode_pengerjaan',$kode_pengerjaan)
                                                    ->get();
        return view('backend.payrol.penggajian.supir_rit.cek_kirim_gaji',$data);

    }

    public function supir_rit_cek_email_kirim_ulang($kode_pengerjaan,$id)
    {
        $kirim_gaji = $this->kirim_gaji->where('id',$id)
                                        ->first();
        $data['id'] = $kirim_gaji->pengerjaan_id;
        $data['kode_pengerjaan'] = $kode_pengerjaan;

        $new_data_pengerjaan = $this->newDataPengerjaan->where('kode_pengerjaan',$kode_pengerjaan)->first();

        $explode_tanggal_pengerjaans = explode('#', $new_data_pengerjaan['tanggal']);
        $exp_tanggals = array_filter($explode_tanggal_pengerjaans);
        $a = count($exp_tanggals);
        $exp_tgl_awal = explode('-', $exp_tanggals[1]);
        $exp_tgl_akhir = explode('-', $exp_tanggals[$a]);
        
        $data['tanggal'] = Carbon::parse($exp_tgl_awal[0] . '-' . $exp_tgl_awal[1] . '-' . $exp_tgl_awal[2])->isoFormat('D MMMM').' sampai '.Carbon::parse($exp_tgl_akhir[0] . '-' . $exp_tgl_akhir[1] . '-' . $exp_tgl_akhir[2])->isoFormat('D MMMM YYYY');
        $data['nama'] = $kirim_gaji->pengerjaan_supir_rit->operator_supir_rit->biodata_karyawan->nama;

        $pdf = Pdf::loadView('backend.payrol.penggajian.supir_rit.pdf_cek_gaji',$data);
        $pdf->setPaper(array(0,0,400,500));   
        $pdf->setEncryption(Carbon::create($kirim_gaji->pengerjaan_supir_rit->operator_supir_rit->biodata_karyawan->tgl_lahir)->format('dmY'),Carbon::create($kirim_gaji->pengerjaan_supir_rit->operator_supir_rit->biodata_karyawan->tgl_lahir)->format('dmY'));
        
        Mail::send('backend.payrol.penggajian.email_kirim_gaji',$data, function($message) use($data,$pdf,$kirim_gaji){
            $message->to(strtolower($kirim_gaji->pengerjaan_supir_rit->operator_supir_rit->biodata_karyawan->email))
                    ->subject('Laporan Slip Gaji '.$kirim_gaji->pengerjaan_supir_rit->operator_supir_rit->biodata_karyawan->nama.' '.date('d-m-Y'))
                    ->attachData($pdf->output(), 'Laporan Slip Gaji '.$kirim_gaji->pengerjaan_supir_rit->operator_supir_rit->biodata_karyawan->nama.' '.date('d-m-Y').'.pdf');
        });

        if (Mail::failures()) {
            $this->kirim_gaji->updateOrCreate(
                [
                    'kode_pengerjaan' => $kode_pengerjaan,
                    'nik' => $kirim_gaji->nik,
                ],
                [
                    'kode_payrol' => $kirim_gaji->kode_payrol,
                    'pengerjaan_id' => $kirim_gaji->pengerjaan_id,
                    'nama_karyawan' => $kirim_gaji->nama_karyawan,
                    // 'nik' => $pengerjaan_weekly->operator_karyawan->nik,
                    'nominal_gaji' => $kirim_gaji->nominal_gaji,
                    'status' => 'gagal'
                ]
            );
        }

        $this->kirim_gaji->updateOrCreate(
            [
                'kode_pengerjaan' => $kode_pengerjaan,
                'nik' => $kirim_gaji->nik,
            ],
            [
                'kode_payrol' => $kirim_gaji->kode_payrol,
                'pengerjaan_id' => $kirim_gaji->pengerjaan_id,
                'nama_karyawan' => $kirim_gaji->nama_karyawan,
                // 'nik' => $pengerjaan_weekly->operator_karyawan->nik,
                'nominal_gaji' => $kirim_gaji->nominal_gaji,
                'status' => 'terkirim'
            ]
        );

        return response()->json([
            'success' => true,
            'message_title' => 'Berhasil',
            'message_content' => 'Slip Gaji Berhasil Dikirim'
        ]);
    }
}
