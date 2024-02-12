<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;

use App\Models\NewDataPengerjaan;
use App\Models\KaryawanOperator;
use App\Models\PengerjaanWeekly;
use App\Models\Pengerjaan;
use App\Models\PengerjaanHarian;
use App\Models\PengerjaanRITHarian;
use App\Models\PengerjaanRITWeekly;

use App\Models\RitPosisi;
use App\Models\RitUMK;
use App\Models\RitTujuan;

use App\Models\UMKBoronganLokal;
use App\Models\UMKBoronganEkspor;
use App\Models\UMKBoronganAmbri;
use App\Models\JenisOperatorDetailPengerjaan;
use \Carbon\Carbon;
use DataTables;

use Pdf;
use Dompdf\Options;
use \Codedge\Fpdf\Fpdf\Fpdf;

class PayrolController extends Controller
{
    public function borongan(Request $request)
    {
        if ($request->ajax()) {
            $data = NewDataPengerjaan::where('kode_pengerjaan','LIKE','%PB%')->get();
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
                                $btn.=      '<a href='.route('payrol.borongan.bank',['kode_pengerjaan' => $row->kode_pengerjaan]).' class="btn btn-primary" target="_blank"><i class="far fa-file-pdf"></i> Bank<a>';
                                $btn.=      '<a href='.route('payrol.borongan.weekly_report',['kode_pengerjaan' => $row->kode_pengerjaan]).' class="btn btn-danger" target="_blank"><i class="fas fa-book"></i> Weekly Report<a>';
                                $btn.=  '</div>';
                                return $btn;
                            })
                            ->rawColumns(['action','status'])
                            ->make(true);
        }
        return view('backend.payrol.penggajian.borongan.index');
    }

    public function borongan_slip_gaji($kode_pengerjaan)
    {
        $data['new_data_pengerjaan'] = NewDataPengerjaan::where('kode_pengerjaan',$kode_pengerjaan)->first();
        $explode_tanggal_pengerjaans = explode('#',$data['new_data_pengerjaan']['tanggal']);
        $exp_tanggals = array_filter($explode_tanggal_pengerjaans);
        $a = count($exp_tanggals);
        // dd($exp_tanggals);
        $exp_tgl_awal = explode('-', $exp_tanggals[1]);
        $exp_tgl_akhir = explode('-', $exp_tanggals[$a]);
        
        $pengerjaan_weekly = PengerjaanWeekly::select([
                                                        'pengerjaan_weekly.id as id',
                                                        'pengerjaan_weekly.operator_karyawan_id as operator_karyawan_id',
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
                                                    ])
                                                    ->leftJoin('operator_karyawan','operator_karyawan.id','=','pengerjaan_weekly.operator_karyawan_id')
                                                    ->leftJoin('itic_emp.biodata_karyawan','biodata_karyawan.nik','=','operator_karyawan.nik')
                                                    ->where('pengerjaan_weekly.kode_pengerjaan',$kode_pengerjaan)
                                                    ->orderBy('operator_karyawan.jenis_operator_detail_pekerjaan_id','asc')
                                                    ->orderBy('biodata_karyawan.nama','asc')
                                                    ->get();
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
        //True
        foreach ($pengerjaan_weekly as $key => $pw) {

            $upah_lembur = [];
            $total_upah_hasil_kerja = [];
            $pengerjaans = Pengerjaan::where('operator_karyawan_id',$pw->operator_karyawan_id)
                                    ->where('kode_pengerjaan',$kode_pengerjaan)
                                    ->get();
            // dd($pengerjaans);
            foreach ($pengerjaans as $keys => $pengerjaan) {
                //Begin Lokal
                if ($pengerjaan->operator_karyawan->jenis_operator_detail_pekerjaan_id == 1) {
                    $explode_hasil_kerja_1 = explode("|",$pengerjaan->hasil_kerja_1);
                    $umk_borongan_lokal_1 = UMKBoronganLokal::select('id','jenis_produk','umk_packing','umk_bandrol','umk_inner','umk_outer')->where('id',$explode_hasil_kerja_1[0])->first();
                    if(empty($umk_borongan_lokal_1)){
                        $jenis_produk_1 = '-';
                        $hasil_kerja_1 = null;
                        $data_explode_hasil_kerja_1 = '-';
                        $lembur_1 = 1;
                    }else{
                        $jenis_produk_1 = $umk_borongan_lokal_1->jenis_produk;
                        $hasil_kerja_1 = $explode_hasil_kerja_1[1]*$umk_borongan_lokal_1->umk_packing;
                        $data_explode_hasil_kerja_1 = $explode_hasil_kerja_1[1];
                        $lembur_1 = 1.5;
                    }

                    $explode_hasil_kerja_2 = explode("|",$pengerjaan->hasil_kerja_2);
                    $umk_borongan_lokal_2 = \App\Models\UMKBoronganLokal::select('id','jenis_produk','umk_packing','umk_bandrol','umk_inner','umk_outer')->where('id',$explode_hasil_kerja_2[0])->where('status','Y')->first();
                    if(empty($umk_borongan_lokal_2)){
                        $jenis_produk_2 = '-';
                        $hasil_kerja_2 = 0;
                        $data_explode_hasil_kerja_2 = '-';
                        $lembur_2 = 1;
                    }else{
                        $jenis_produk_2 = $umk_borongan_lokal_2->jenis_produk;
                        $hasil_kerja_2 = $explode_hasil_kerja_2[1]*$umk_borongan_lokal_2->umk_packing;
                        $data_explode_hasil_kerja_2 = $explode_hasil_kerja_2[1];
                        $lembur_2 = 1.5;
                    }

                    $explode_hasil_kerja_3 = explode("|",$pengerjaan->hasil_kerja_3);
                    $umk_borongan_lokal_3 = \App\Models\UMKBoronganLokal::select('id','jenis_produk','umk_packing','umk_bandrol','umk_inner','umk_outer')->where('id',$explode_hasil_kerja_3[0])->where('status','Y')->first();
                    if(empty($umk_borongan_lokal_3)){
                        $jenis_produk_3 = '-';
                        $hasil_kerja_3 = 0;
                        $data_explode_hasil_kerja_3 = '-';
                        $lembur_3 = 1;
                    }else{
                        $jenis_produk_3 = $umk_borongan_lokal_3->jenis_produk;
                        $hasil_kerja_3 = $explode_hasil_kerja_3[1]*$umk_borongan_lokal_3->umk_packing;
                        $data_explode_hasil_kerja_3 = $explode_hasil_kerja_3[1];
                        $lembur_3 = 1.5;
                    }

                    $explode_hasil_kerja_4 = explode("|",$pengerjaan->hasil_kerja_4);
                    $umk_borongan_lokal_4 = \App\Models\UMKBoronganLokal::select('id','jenis_produk','umk_packing','umk_bandrol','umk_inner','umk_outer')->where('id',$explode_hasil_kerja_4[0])->where('status','Y')->first();
                    if(empty($umk_borongan_lokal_4)){
                        $jenis_produk_4 = '-';
                        $hasil_kerja_4 = 0;
                        $data_explode_hasil_kerja_4 = '-';
                        $lembur_4 = 1;
                    }else{
                        $jenis_produk_4 = $umk_borongan_lokal_4->jenis_produk;
                        $hasil_kerja_4 = $explode_hasil_kerja_4[1]*$umk_borongan_lokal_4->umk_packing;
                        $data_explode_hasil_kerja_4 = $explode_hasil_kerja_4[1];
                        $lembur_4 = 1.5;
                    }

                    $explode_hasil_kerja_5 = explode("|",$pengerjaan->hasil_kerja_5);
                    $umk_borongan_lokal_5 = \App\Models\UMKBoronganLokal::select('id','jenis_produk','umk_packing','umk_bandrol','umk_inner','umk_outer')->where('id',$explode_hasil_kerja_5[0])->where('status','Y')->first();
                    if(empty($umk_borongan_lokal_5)){
                        $jenis_produk_5 = '-';
                        $hasil_kerja_5 = 0;
                        $data_explode_hasil_kerja_5 = '-';
                        $lembur_5 = 1;
                    }else{
                        $jenis_produk_5 = $umk_borongan_lokal_5->jenis_produk;
                        $hasil_kerja_5 = $explode_hasil_kerja_5[1]*$umk_borongan_lokal_5->umk_packing;
                        $data_explode_hasil_kerja_5 = $explode_hasil_kerja_5[1];
                        $lembur_5 = 1.5;
                    }
                }

                if ($pengerjaan->operator_karyawan->jenis_operator_detail_pekerjaan_id == 2) {
                    $explode_hasil_kerja_1 = explode("|",$pengerjaan->hasil_kerja_1);
                    $umk_borongan_lokal_1 = UMKBoronganLokal::select('id','jenis_produk','umk_packing','umk_bandrol','umk_inner','umk_outer')->where('id',$explode_hasil_kerja_1[0])->first();
                    if(empty($umk_borongan_lokal_1)){
                        $jenis_produk_1 = '-';
                        $hasil_kerja_1 = null;
                        $data_explode_hasil_kerja_1 = '-';
                        $lembur_1 = 1;
                    }else{
                        $jenis_produk_1 = $umk_borongan_lokal_1->jenis_produk;
                        $hasil_kerja_1 = $explode_hasil_kerja_1[1]*$umk_borongan_lokal_1->umk_bandrol;
                        $data_explode_hasil_kerja_1 = $explode_hasil_kerja_1[1];
                        $lembur_1 = 1.5;
                    }

                    $explode_hasil_kerja_2 = explode("|",$pengerjaan->hasil_kerja_2);
                    $umk_borongan_lokal_2 = \App\Models\UMKBoronganLokal::select('id','jenis_produk','umk_packing','umk_bandrol','umk_inner','umk_outer')->where('id',$explode_hasil_kerja_2[0])->where('status','Y')->first();
                    if(empty($umk_borongan_lokal_2)){
                        $jenis_produk_2 = '-';
                        $hasil_kerja_2 = 0;
                        $data_explode_hasil_kerja_2 = '-';
                        $lembur_2 = 1;
                    }else{
                        $jenis_produk_2 = $umk_borongan_lokal_2->jenis_produk;
                        $hasil_kerja_2 = $explode_hasil_kerja_2[1]*$umk_borongan_lokal_2->umk_bandrol;
                        $data_explode_hasil_kerja_2 = $explode_hasil_kerja_2[1];
                        $lembur_2 = 1.5;
                    }

                    $explode_hasil_kerja_3 = explode("|",$pengerjaan->hasil_kerja_3);
                    $umk_borongan_lokal_3 = \App\Models\UMKBoronganLokal::select('id','jenis_produk','umk_packing','umk_bandrol','umk_inner','umk_outer')->where('id',$explode_hasil_kerja_3[0])->where('status','Y')->first();
                    if(empty($umk_borongan_lokal_3)){
                        $jenis_produk_3 = '-';
                        $hasil_kerja_3 = 0;
                        $data_explode_hasil_kerja_3 = '-';
                        $lembur_3 = 1;
                    }else{
                        $jenis_produk_3 = $umk_borongan_lokal_3->jenis_produk;
                        $hasil_kerja_3 = $explode_hasil_kerja_3[1]*$umk_borongan_lokal_3->umk_bandrol;
                        $data_explode_hasil_kerja_3 = $explode_hasil_kerja_3[1];
                        $lembur_3 = 1.5;
                    }

                    $explode_hasil_kerja_4 = explode("|",$pengerjaan->hasil_kerja_4);
                    $umk_borongan_lokal_4 = \App\Models\UMKBoronganLokal::select('id','jenis_produk','umk_packing','umk_bandrol','umk_inner','umk_outer')->where('id',$explode_hasil_kerja_4[0])->where('status','Y')->first();
                    if(empty($umk_borongan_lokal_4)){
                        $jenis_produk_4 = '-';
                        $hasil_kerja_4 = 0;
                        $data_explode_hasil_kerja_4 = '-';
                        $lembur_4 = 1;
                    }else{
                        $jenis_produk_4 = $umk_borongan_lokal_4->jenis_produk;
                        $hasil_kerja_4 = $explode_hasil_kerja_4[1]*$umk_borongan_lokal_4->umk_bandrol;
                        $data_explode_hasil_kerja_4 = $explode_hasil_kerja_4[1];
                        $lembur_4 = 1.5;
                    }

                    $explode_hasil_kerja_5 = explode("|",$pengerjaan->hasil_kerja_5);
                    $umk_borongan_lokal_5 = \App\Models\UMKBoronganLokal::select('id','jenis_produk','umk_packing','umk_bandrol','umk_inner','umk_outer')->where('id',$explode_hasil_kerja_5[0])->where('status','Y')->first();
                    if(empty($umk_borongan_lokal_5)){
                        $jenis_produk_5 = '-';
                        $hasil_kerja_5 = 0;
                        $data_explode_hasil_kerja_5 = '-';
                        $lembur_5 = 1;
                    }else{
                        $jenis_produk_5 = $umk_borongan_lokal_5->jenis_produk;
                        $hasil_kerja_5 = $explode_hasil_kerja_5[1]*$umk_borongan_lokal_5->umk_bandrol;
                        $data_explode_hasil_kerja_5 = $explode_hasil_kerja_5[1];
                        $lembur_5 = 1.5;
                    }
                }

                if ($pengerjaan->operator_karyawan->jenis_operator_detail_pekerjaan_id == 3) {
                    $explode_hasil_kerja_1 = explode("|",$pengerjaan->hasil_kerja_1);
                    $umk_borongan_lokal_1 = UMKBoronganLokal::select('id','jenis_produk','umk_packing','umk_bandrol','umk_inner','umk_outer')->where('id',$explode_hasil_kerja_1[0])->first();
                    if(empty($umk_borongan_lokal_1)){
                        $jenis_produk_1 = '-';
                        $hasil_kerja_1 = null;
                        $data_explode_hasil_kerja_1 = '-';
                        $lembur_1 = 1;
                    }else{
                        $jenis_produk_1 = $umk_borongan_lokal_1->jenis_produk;
                        $hasil_kerja_1 = $explode_hasil_kerja_1[1]*$umk_borongan_lokal_1->umk_inner;
                        $data_explode_hasil_kerja_1 = $explode_hasil_kerja_1[1];
                        $lembur_1 = 1.5;
                    }

                    $explode_hasil_kerja_2 = explode("|",$pengerjaan->hasil_kerja_2);
                    $umk_borongan_lokal_2 = \App\Models\UMKBoronganLokal::select('id','jenis_produk','umk_packing','umk_bandrol','umk_inner','umk_outer')->where('id',$explode_hasil_kerja_2[0])->where('status','Y')->first();
                    if(empty($umk_borongan_lokal_2)){
                        $jenis_produk_2 = '-';
                        $hasil_kerja_2 = 0;
                        $data_explode_hasil_kerja_2 = '-';
                        $lembur_2 = 1;
                    }else{
                        $jenis_produk_2 = $umk_borongan_lokal_2->jenis_produk;
                        $hasil_kerja_2 = $explode_hasil_kerja_2[1]*$umk_borongan_lokal_2->umk_inner;
                        $data_explode_hasil_kerja_2 = $explode_hasil_kerja_2[1];
                        $lembur_2 = 1.5;
                    }

                    $explode_hasil_kerja_3 = explode("|",$pengerjaan->hasil_kerja_3);
                    $umk_borongan_lokal_3 = \App\Models\UMKBoronganLokal::select('id','jenis_produk','umk_packing','umk_bandrol','umk_inner','umk_outer')->where('id',$explode_hasil_kerja_3[0])->where('status','Y')->first();
                    if(empty($umk_borongan_lokal_3)){
                        $jenis_produk_3 = '-';
                        $hasil_kerja_3 = 0;
                        $data_explode_hasil_kerja_3 = '-';
                        $lembur_3 = 1;
                    }else{
                        $jenis_produk_3 = $umk_borongan_lokal_3->jenis_produk;
                        $hasil_kerja_3 = $explode_hasil_kerja_3[1]*$umk_borongan_lokal_3->umk_inner;
                        $data_explode_hasil_kerja_3 = $explode_hasil_kerja_3[1];
                        $lembur_3 = 1.5;
                    }

                    $explode_hasil_kerja_4 = explode("|",$pengerjaan->hasil_kerja_4);
                    $umk_borongan_lokal_4 = \App\Models\UMKBoronganLokal::select('id','jenis_produk','umk_packing','umk_bandrol','umk_inner','umk_outer')->where('id',$explode_hasil_kerja_4[0])->where('status','Y')->first();
                    if(empty($umk_borongan_lokal_4)){
                        $jenis_produk_4 = '-';
                        $hasil_kerja_4 = 0;
                        $data_explode_hasil_kerja_4 = '-';
                        $lembur_4 = 1;
                    }else{
                        $jenis_produk_4 = $umk_borongan_lokal_4->jenis_produk;
                        $hasil_kerja_4 = $explode_hasil_kerja_4[1]*$umk_borongan_lokal_4->umk_inner;
                        $data_explode_hasil_kerja_4 = $explode_hasil_kerja_4[1];
                        $lembur_4 = 1.5;
                    }

                    $explode_hasil_kerja_5 = explode("|",$pengerjaan->hasil_kerja_5);
                    $umk_borongan_lokal_5 = \App\Models\UMKBoronganLokal::select('id','jenis_produk','umk_packing','umk_bandrol','umk_inner','umk_outer')->where('id',$explode_hasil_kerja_5[0])->where('status','Y')->first();
                    if(empty($umk_borongan_lokal_5)){
                        $jenis_produk_5 = '-';
                        $hasil_kerja_5 = 0;
                        $data_explode_hasil_kerja_5 = '-';
                        $lembur_5 = 1;
                    }else{
                        $jenis_produk_5 = $umk_borongan_lokal_5->jenis_produk;
                        $hasil_kerja_5 = $explode_hasil_kerja_5[1]*$umk_borongan_lokal_5->umk_inner;
                        $data_explode_hasil_kerja_5 = $explode_hasil_kerja_5[1];
                        $lembur_5 = 1.5;
                    }
                }

                if ($pengerjaan->operator_karyawan->jenis_operator_detail_pekerjaan_id == 4) {
                    $explode_hasil_kerja_1 = explode("|",$pengerjaan->hasil_kerja_1);
                    $umk_borongan_lokal_1 = UMKBoronganLokal::select('id','jenis_produk','umk_packing','umk_bandrol','umk_inner','umk_outer')->where('id',$explode_hasil_kerja_1[0])->first();
                    if(empty($umk_borongan_lokal_1)){
                        $jenis_produk_1 = '-';
                        $hasil_kerja_1 = null;
                        $data_explode_hasil_kerja_1 = '-';
                        $lembur_1 = 1;
                    }else{
                        $jenis_produk_1 = $umk_borongan_lokal_1->jenis_produk;
                        $hasil_kerja_1 = $explode_hasil_kerja_1[1]*$umk_borongan_lokal_1->umk_outer;
                        $data_explode_hasil_kerja_1 = $explode_hasil_kerja_1[1];
                        $lembur_1 = 1.5;
                    }

                    $explode_hasil_kerja_2 = explode("|",$pengerjaan->hasil_kerja_2);
                    $umk_borongan_lokal_2 = \App\Models\UMKBoronganLokal::select('id','jenis_produk','umk_packing','umk_bandrol','umk_inner','umk_outer')->where('id',$explode_hasil_kerja_2[0])->where('status','Y')->first();
                    if(empty($umk_borongan_lokal_2)){
                        $jenis_produk_2 = '-';
                        $hasil_kerja_2 = 0;
                        $data_explode_hasil_kerja_2 = '-';
                        $lembur_2 = 1;
                    }else{
                        $jenis_produk_2 = $umk_borongan_lokal_2->jenis_produk;
                        $hasil_kerja_2 = $explode_hasil_kerja_2[1]*$umk_borongan_lokal_2->umk_outer;
                        $data_explode_hasil_kerja_2 = $explode_hasil_kerja_2[1];
                        $lembur_2 = 1.5;
                    }

                    $explode_hasil_kerja_3 = explode("|",$pengerjaan->hasil_kerja_3);
                    $umk_borongan_lokal_3 = \App\Models\UMKBoronganLokal::select('id','jenis_produk','umk_packing','umk_bandrol','umk_inner','umk_outer')->where('id',$explode_hasil_kerja_3[0])->where('status','Y')->first();
                    if(empty($umk_borongan_lokal_3)){
                        $jenis_produk_3 = '-';
                        $hasil_kerja_3 = 0;
                        $data_explode_hasil_kerja_3 = '-';
                        $lembur_3 = 1;
                    }else{
                        $jenis_produk_3 = $umk_borongan_lokal_3->jenis_produk;
                        $hasil_kerja_3 = $explode_hasil_kerja_3[1]*$umk_borongan_lokal_3->umk_outer;
                        $data_explode_hasil_kerja_3 = $explode_hasil_kerja_3[1];
                        $lembur_3 = 1.5;
                    }

                    $explode_hasil_kerja_4 = explode("|",$pengerjaan->hasil_kerja_4);
                    $umk_borongan_lokal_4 = \App\Models\UMKBoronganLokal::select('id','jenis_produk','umk_packing','umk_bandrol','umk_inner','umk_outer')->where('id',$explode_hasil_kerja_4[0])->where('status','Y')->first();
                    if(empty($umk_borongan_lokal_4)){
                        $jenis_produk_4 = '-';
                        $hasil_kerja_4 = 0;
                        $data_explode_hasil_kerja_4 = '-';
                        $lembur_4 = 1;
                    }else{
                        $jenis_produk_4 = $umk_borongan_lokal_4->jenis_produk;
                        $hasil_kerja_4 = $explode_hasil_kerja_4[1]*$umk_borongan_lokal_4->umk_outer;
                        $data_explode_hasil_kerja_4 = $explode_hasil_kerja_4[1];
                        $lembur_4 = 1.5;
                    }

                    $explode_hasil_kerja_5 = explode("|",$pengerjaan->hasil_kerja_5);
                    $umk_borongan_lokal_5 = \App\Models\UMKBoronganLokal::select('id','jenis_produk','umk_packing','umk_bandrol','umk_inner','umk_outer')->where('id',$explode_hasil_kerja_5[0])->where('status','Y')->first();
                    if(empty($umk_borongan_lokal_5)){
                        $jenis_produk_5 = '-';
                        $hasil_kerja_5 = 0;
                        $data_explode_hasil_kerja_5 = '-';
                        $lembur_5 = 1;
                    }else{
                        $jenis_produk_5 = $umk_borongan_lokal_5->jenis_produk;
                        $hasil_kerja_5 = $explode_hasil_kerja_5[1]*$umk_borongan_lokal_5->umk_outer;
                        $data_explode_hasil_kerja_5 = $explode_hasil_kerja_5[1];
                        $lembur_5 = 1.5;
                    }
                }
                //End Lokal

                //Begin Ekspor
                if ($pengerjaan->operator_karyawan->jenis_operator_detail_pekerjaan_id == 5) {
                    $explode_hasil_kerja_1 = explode("|",$pengerjaan->hasil_kerja_1);
                    $umk_borongan_lokal_1 = UMKBoronganEkspor::select('id','jenis_produk','umk_packing','umk_kemas','umk_pilih_gagang')->where('id',$explode_hasil_kerja_1[0])->first();
                    // dd($umk_borongan_lokal_1);
                    if(empty($umk_borongan_lokal_1)){
                        $jenis_produk_1 = '-';
                        $hasil_kerja_1 = null;
                        $data_explode_hasil_kerja_1 = '-';
                        $lembur_1 = 1;
                    }else{
                        $jenis_produk_1 = $umk_borongan_lokal_1->jenis_produk;
                        $hasil_kerja_1 = $explode_hasil_kerja_1[1]*$umk_borongan_lokal_1->umk_packing;
                        $data_explode_hasil_kerja_1 = $explode_hasil_kerja_1[1];
                        $lembur_1 = 1.5;
                    }

                    $explode_hasil_kerja_2 = explode("|",$pengerjaan->hasil_kerja_2);
                    $umk_borongan_lokal_2 = UMKBoronganEkspor::select('id','jenis_produk','umk_packing','umk_kemas','umk_pilih_gagang')->where('id',$explode_hasil_kerja_2[0])->first();
                    // dd($umk_borongan_lokal_1);
                    if(empty($umk_borongan_lokal_2)){
                        $jenis_produk_2 = '-';
                        $hasil_kerja_2 = null;
                        $data_explode_hasil_kerja_2 = '-';
                        $lembur_2 = 1;
                    }else{
                        $jenis_produk_2 = $umk_borongan_lokal_2->jenis_produk;
                        $hasil_kerja_2 = $explode_hasil_kerja_2[1]*$umk_borongan_lokal_2->umk_packing;
                        $data_explode_hasil_kerja_2 = $explode_hasil_kerja_2[1];
                        $lembur_2 = 1.5;
                    }

                    $explode_hasil_kerja_3 = explode("|",$pengerjaan->hasil_kerja_3);
                    $umk_borongan_lokal_3 = UMKBoronganEkspor::select('id','jenis_produk','umk_packing','umk_kemas','umk_pilih_gagang')->where('id',$explode_hasil_kerja_3[0])->first();
                    // dd($umk_borongan_lokal_1);
                    if(empty($umk_borongan_lokal_3)){
                        $jenis_produk_3 = '-';
                        $hasil_kerja_3 = null;
                        $data_explode_hasil_kerja_3 = '-';
                        $lembur_3 = 1;
                    }else{
                        $jenis_produk_3 = $umk_borongan_lokal_3->jenis_produk;
                        $hasil_kerja_3 = $explode_hasil_kerja_3[1]*$umk_borongan_lokal_3->umk_packing;
                        $data_explode_hasil_kerja_3 = $explode_hasil_kerja_3[1];
                        $lembur_3 = 1.5;
                    }

                    $explode_hasil_kerja_4 = explode("|",$pengerjaan->hasil_kerja_4);
                    $umk_borongan_lokal_4 = UMKBoronganEkspor::select('id','jenis_produk','umk_packing','umk_kemas','umk_pilih_gagang')->where('id',$explode_hasil_kerja_4[0])->first();
                    // dd($umk_borongan_lokal_1);
                    if(empty($umk_borongan_lokal_4)){
                        $jenis_produk_4 = '-';
                        $hasil_kerja_4 = null;
                        $data_explode_hasil_kerja_4 = '-';
                        $lembur_4 = 1;
                    }else{
                        $jenis_produk_4 = $umk_borongan_lokal_4->jenis_produk;
                        $hasil_kerja_4 = $explode_hasil_kerja_4[1]*$umk_borongan_lokal_4->umk_packing;
                        $data_explode_hasil_kerja_4 = $explode_hasil_kerja_4[1];
                        $lembur_4 = 1.5;
                    }

                    $explode_hasil_kerja_5 = explode("|",$pengerjaan->hasil_kerja_5);
                    $umk_borongan_lokal_5 = UMKBoronganEkspor::select('id','jenis_produk','umk_packing','umk_kemas','umk_pilih_gagang')->where('id',$explode_hasil_kerja_5[0])->first();
                    // dd($umk_borongan_lokal_1);
                    if(empty($umk_borongan_lokal_5)){
                        $jenis_produk_5 = '-';
                        $hasil_kerja_5 = null;
                        $data_explode_hasil_kerja_5 = '-';
                        $lembur_5 = 1;
                    }else{
                        $jenis_produk_5 = $umk_borongan_lokal_5->jenis_produk;
                        $hasil_kerja_5 = $explode_hasil_kerja_5[1]*$umk_borongan_lokal_5->umk_packing;
                        $data_explode_hasil_kerja_5 = $explode_hasil_kerja_5[1];
                        $lembur_5 = 1.5;
                    }
                }

                if ($pengerjaan->operator_karyawan->jenis_operator_detail_pekerjaan_id == 6) {
                    $explode_hasil_kerja_1 = explode("|",$pengerjaan->hasil_kerja_1);
                    $umk_borongan_lokal_1 = UMKBoronganEkspor::select('id','jenis_produk','umk_packing','umk_kemas','umk_pilih_gagang')->where('id',$explode_hasil_kerja_1[0])->first();
                    // dd($umk_borongan_lokal_1);
                    if(empty($umk_borongan_lokal_1)){
                        $jenis_produk_1 = '-';
                        $hasil_kerja_1 = null;
                        $data_explode_hasil_kerja_1 = '-';
                        $lembur_1 = 1;
                    }else{
                        $jenis_produk_1 = $umk_borongan_lokal_1->jenis_produk;
                        $hasil_kerja_1 = $explode_hasil_kerja_1[1]*$umk_borongan_lokal_1->umk_kemas;
                        $data_explode_hasil_kerja_1 = $explode_hasil_kerja_1[1];
                        $lembur_1 = 1.5;
                    }

                    $explode_hasil_kerja_2 = explode("|",$pengerjaan->hasil_kerja_2);
                    $umk_borongan_lokal_2 = UMKBoronganEkspor::select('id','jenis_produk','umk_packing','umk_kemas','umk_pilih_gagang')->where('id',$explode_hasil_kerja_2[0])->first();
                    // dd($umk_borongan_lokal_1);
                    if(empty($umk_borongan_lokal_2)){
                        $jenis_produk_2 = '-';
                        $hasil_kerja_2 = null;
                        $data_explode_hasil_kerja_2 = '-';
                        $lembur_2 = 1;
                    }else{
                        $jenis_produk_2 = $umk_borongan_lokal_2->jenis_produk;
                        $hasil_kerja_2 = $explode_hasil_kerja_2[1]*$umk_borongan_lokal_2->umk_kemas;
                        $data_explode_hasil_kerja_2 = $explode_hasil_kerja_2[1];
                        $lembur_2 = 1.5;
                    }

                    $explode_hasil_kerja_3 = explode("|",$pengerjaan->hasil_kerja_3);
                    $umk_borongan_lokal_3 = UMKBoronganEkspor::select('id','jenis_produk','umk_packing','umk_kemas','umk_pilih_gagang')->where('id',$explode_hasil_kerja_3[0])->first();
                    // dd($umk_borongan_lokal_1);
                    if(empty($umk_borongan_lokal_3)){
                        $jenis_produk_3 = '-';
                        $hasil_kerja_3 = null;
                        $data_explode_hasil_kerja_3 = '-';
                        $lembur_3 = 1;
                    }else{
                        $jenis_produk_3 = $umk_borongan_lokal_3->jenis_produk;
                        $hasil_kerja_3 = $explode_hasil_kerja_3[1]*$umk_borongan_lokal_3->umk_kemas;
                        $data_explode_hasil_kerja_3 = $explode_hasil_kerja_3[1];
                        $lembur_3 = 1.5;
                    }

                    $explode_hasil_kerja_4 = explode("|",$pengerjaan->hasil_kerja_4);
                    $umk_borongan_lokal_4 = UMKBoronganEkspor::select('id','jenis_produk','umk_packing','umk_kemas','umk_pilih_gagang')->where('id',$explode_hasil_kerja_4[0])->first();
                    // dd($umk_borongan_lokal_1);
                    if(empty($umk_borongan_lokal_4)){
                        $jenis_produk_4 = '-';
                        $hasil_kerja_4 = null;
                        $data_explode_hasil_kerja_4 = '-';
                        $lembur_4 = 1;
                    }else{
                        $jenis_produk_4 = $umk_borongan_lokal_4->jenis_produk;
                        $hasil_kerja_4 = $explode_hasil_kerja_4[1]*$umk_borongan_lokal_4->umk_kemas;
                        $data_explode_hasil_kerja_4 = $explode_hasil_kerja_4[1];
                        $lembur_4 = 1.5;
                    }

                    $explode_hasil_kerja_5 = explode("|",$pengerjaan->hasil_kerja_5);
                    $umk_borongan_lokal_5 = UMKBoronganEkspor::select('id','jenis_produk','umk_packing','umk_kemas','umk_pilih_gagang')->where('id',$explode_hasil_kerja_5[0])->first();
                    // dd($umk_borongan_lokal_1);
                    if(empty($umk_borongan_lokal_5)){
                        $jenis_produk_5 = '-';
                        $hasil_kerja_5 = null;
                        $data_explode_hasil_kerja_5 = '-';
                        $lembur_5 = 1;
                    }else{
                        $jenis_produk_5 = $umk_borongan_lokal_5->jenis_produk;
                        $hasil_kerja_5 = $explode_hasil_kerja_5[1]*$umk_borongan_lokal_5->umk_kemas;
                        $data_explode_hasil_kerja_5 = $explode_hasil_kerja_5[1];
                        $lembur_5 = 1.5;
                    }
                }

                if ($pengerjaan->operator_karyawan->jenis_operator_detail_pekerjaan_id == 7) {
                    $explode_hasil_kerja_1 = explode("|",$pengerjaan->hasil_kerja_1);
                    $umk_borongan_lokal_1 = UMKBoronganEkspor::select('id','jenis_produk','umk_packing','umk_kemas','umk_pilih_gagang')->where('id',$explode_hasil_kerja_1[0])->first();
                    // dd($umk_borongan_lokal_1);
                    if(empty($umk_borongan_lokal_1)){
                        $jenis_produk_1 = '-';
                        $hasil_kerja_1 = null;
                        $data_explode_hasil_kerja_1 = '-';
                        $lembur_1 = 1;
                    }else{
                        $jenis_produk_1 = $umk_borongan_lokal_1->jenis_produk;
                        $hasil_kerja_1 = $explode_hasil_kerja_1[1]*$umk_borongan_lokal_1->umk_pilih_gagang;
                        $data_explode_hasil_kerja_1 = $explode_hasil_kerja_1[1];
                        $lembur_1 = 1.5;
                    }

                    $explode_hasil_kerja_2 = explode("|",$pengerjaan->hasil_kerja_2);
                    $umk_borongan_lokal_2 = UMKBoronganEkspor::select('id','jenis_produk','umk_packing','umk_kemas','umk_pilih_gagang')->where('id',$explode_hasil_kerja_2[0])->first();
                    // dd($umk_borongan_lokal_1);
                    if(empty($umk_borongan_lokal_2)){
                        $jenis_produk_2 = '-';
                        $hasil_kerja_2 = null;
                        $data_explode_hasil_kerja_2 = '-';
                        $lembur_2 = 1;
                    }else{
                        $jenis_produk_2 = $umk_borongan_lokal_2->jenis_produk;
                        $hasil_kerja_2 = $explode_hasil_kerja_2[1]*$umk_borongan_lokal_2->umk_pilih_gagang;
                        $data_explode_hasil_kerja_2 = $explode_hasil_kerja_2[1];
                        $lembur_2 = 1.5;
                    }

                    $explode_hasil_kerja_3 = explode("|",$pengerjaan->hasil_kerja_3);
                    $umk_borongan_lokal_3 = UMKBoronganEkspor::select('id','jenis_produk','umk_packing','umk_kemas','umk_pilih_gagang')->where('id',$explode_hasil_kerja_3[0])->first();
                    // dd($umk_borongan_lokal_1);
                    if(empty($umk_borongan_lokal_3)){
                        $jenis_produk_3 = '-';
                        $hasil_kerja_3 = null;
                        $data_explode_hasil_kerja_3 = '-';
                        $lembur_3 = 1;
                    }else{
                        $jenis_produk_3 = $umk_borongan_lokal_3->jenis_produk;
                        $hasil_kerja_3 = $explode_hasil_kerja_3[1]*$umk_borongan_lokal_3->umk_pilih_gagang;
                        $data_explode_hasil_kerja_3 = $explode_hasil_kerja_3[1];
                        $lembur_3 = 1.5;
                    }

                    $explode_hasil_kerja_4 = explode("|",$pengerjaan->hasil_kerja_4);
                    $umk_borongan_lokal_4 = UMKBoronganEkspor::select('id','jenis_produk','umk_packing','umk_kemas','umk_pilih_gagang')->where('id',$explode_hasil_kerja_4[0])->first();
                    // dd($umk_borongan_lokal_1);
                    if(empty($umk_borongan_lokal_4)){
                        $jenis_produk_4 = '-';
                        $hasil_kerja_4 = null;
                        $data_explode_hasil_kerja_4 = '-';
                        $lembur_4 = 1;
                    }else{
                        $jenis_produk_4 = $umk_borongan_lokal_4->jenis_produk;
                        $hasil_kerja_4 = $explode_hasil_kerja_4[1]*$umk_borongan_lokal_4->umk_pilih_gagang;
                        $data_explode_hasil_kerja_4 = $explode_hasil_kerja_4[1];
                        $lembur_4 = 1.5;
                    }

                    $explode_hasil_kerja_5 = explode("|",$pengerjaan->hasil_kerja_5);
                    $umk_borongan_lokal_5 = UMKBoronganEkspor::select('id','jenis_produk','umk_packing','umk_kemas','umk_pilih_gagang')->where('id',$explode_hasil_kerja_5[0])->first();
                    // dd($umk_borongan_lokal_1);
                    if(empty($umk_borongan_lokal_5)){
                        $jenis_produk_5 = '-';
                        $hasil_kerja_5 = null;
                        $data_explode_hasil_kerja_5 = '-';
                        $lembur_5 = 1;
                    }else{
                        $jenis_produk_5 = $umk_borongan_lokal_5->jenis_produk;
                        $hasil_kerja_5 = $explode_hasil_kerja_5[1]*$umk_borongan_lokal_5->umk_pilih_gagang;
                        $data_explode_hasil_kerja_5 = $explode_hasil_kerja_5[1];
                        $lembur_5 = 1.5;
                    }
                }
                //End Ekspor

                //Begin Ambri
                if ($pengerjaan->operator_karyawan->jenis_operator_detail_pekerjaan_id == 8) {
                    $explode_hasil_kerja_1 = explode("|",$pengerjaan->hasil_kerja_1);
                    $umk_borongan_lokal_1 = UMKBoronganAmbri::select('id','jenis_produk','umk_etiket','umk_las_tepi','umk_las_pojok','umk_ambri')->where('id',$explode_hasil_kerja_1[0])->first();
                    // dd($umk_borongan_lokal_1);
                    if(empty($umk_borongan_lokal_1)){
                        $jenis_produk_1 = '-';
                        $hasil_kerja_1 = null;
                        $data_explode_hasil_kerja_1 = '-';
                        $lembur_1 = 1;
                    }else{
                        $jenis_produk_1 = $umk_borongan_lokal_1->jenis_produk;
                        $hasil_kerja_1 = $explode_hasil_kerja_1[1]*$umk_borongan_lokal_1->umk_etiket;
                        $data_explode_hasil_kerja_1 = $explode_hasil_kerja_1[1];
                        $lembur_1 = 1.5;
                    }

                    $explode_hasil_kerja_2 = explode("|",$pengerjaan->hasil_kerja_2);
                    $umk_borongan_lokal_2 = UMKBoronganAmbri::select('id','jenis_produk','umk_etiket','umk_las_tepi','umk_las_pojok','umk_ambri')->where('id',$explode_hasil_kerja_2[0])->first();
                    // dd($umk_borongan_lokal_1);
                    if(empty($umk_borongan_lokal_2)){
                        $jenis_produk_2 = '-';
                        $hasil_kerja_2 = null;
                        $data_explode_hasil_kerja_2 = '-';
                        $lembur_2 = 1;
                    }else{
                        $jenis_produk_2 = $umk_borongan_lokal_2->jenis_produk;
                        $hasil_kerja_2 = $explode_hasil_kerja_2[1]*$umk_borongan_lokal_2->umk_etiket;
                        $data_explode_hasil_kerja_2 = $explode_hasil_kerja_2[1];
                        $lembur_2 = 1.5;
                    }

                    $explode_hasil_kerja_3 = explode("|",$pengerjaan->hasil_kerja_3);
                    $umk_borongan_lokal_3 = UMKBoronganAmbri::select('id','jenis_produk','umk_etiket','umk_las_tepi','umk_las_pojok','umk_ambri')->where('id',$explode_hasil_kerja_3[0])->first();
                    // dd($umk_borongan_lokal_1);
                    if(empty($umk_borongan_lokal_3)){
                        $jenis_produk_3 = '-';
                        $hasil_kerja_3 = null;
                        $data_explode_hasil_kerja_3 = '-';
                        $lembur_3 = 1;
                    }else{
                        $jenis_produk_3 = $umk_borongan_lokal_3->jenis_produk;
                        $hasil_kerja_3 = $explode_hasil_kerja_3[1]*$umk_borongan_lokal_3->umk_etiket;
                        $data_explode_hasil_kerja_3 = $explode_hasil_kerja_3[1];
                        $lembur_3 = 1.5;
                    }

                    $explode_hasil_kerja_4 = explode("|",$pengerjaan->hasil_kerja_4);
                    $umk_borongan_lokal_4 = UMKBoronganAmbri::select('id','jenis_produk','umk_etiket','umk_las_tepi','umk_las_pojok','umk_ambri')->where('id',$explode_hasil_kerja_4[0])->first();
                    // dd($umk_borongan_lokal_1);
                    if(empty($umk_borongan_lokal_4)){
                        $jenis_produk_4 = '-';
                        $hasil_kerja_4 = null;
                        $data_explode_hasil_kerja_4 = '-';
                        $lembur_4 = 1;
                    }else{
                        $jenis_produk_4 = $umk_borongan_lokal_4->jenis_produk;
                        $hasil_kerja_4 = $explode_hasil_kerja_4[1]*$umk_borongan_lokal_4->umk_etiket;
                        $data_explode_hasil_kerja_4 = $explode_hasil_kerja_4[1];
                        $lembur_4 = 1.5;
                    }

                    $explode_hasil_kerja_5 = explode("|",$pengerjaan->hasil_kerja_5);
                    $umk_borongan_lokal_5 = UMKBoronganAmbri::select('id','jenis_produk','umk_etiket','umk_las_tepi','umk_las_pojok','umk_ambri')->where('id',$explode_hasil_kerja_5[0])->first();
                    // dd($umk_borongan_lokal_1);
                    if(empty($umk_borongan_lokal_5)){
                        $jenis_produk_5 = '-';
                        $hasil_kerja_5 = null;
                        $data_explode_hasil_kerja_5 = '-';
                        $lembur_5 = 1;
                    }else{
                        $jenis_produk_5 = $umk_borongan_lokal_5->jenis_produk;
                        $hasil_kerja_5 = $explode_hasil_kerja_5[1]*$umk_borongan_lokal_5->umk_etiket;
                        $data_explode_hasil_kerja_5 = $explode_hasil_kerja_5[1];
                        $lembur_5 = 1.5;
                    }
                }

                if ($pengerjaan->operator_karyawan->jenis_operator_detail_pekerjaan_id == 9) {
                    $explode_hasil_kerja_1 = explode("|",$pengerjaan->hasil_kerja_1);
                    $umk_borongan_lokal_1 = UMKBoronganAmbri::select('id','jenis_produk','umk_etiket','umk_las_tepi','umk_las_pojok','umk_ambri')->where('id',$explode_hasil_kerja_1[0])->first();
                    // dd($umk_borongan_lokal_1);
                    if(empty($umk_borongan_lokal_1)){
                        $jenis_produk_1 = '-';
                        $hasil_kerja_1 = null;
                        $data_explode_hasil_kerja_1 = '-';
                        $lembur_1 = 1;
                    }else{
                        $jenis_produk_1 = $umk_borongan_lokal_1->jenis_produk;
                        $hasil_kerja_1 = $explode_hasil_kerja_1[1]*$umk_borongan_lokal_1->umk_las_tepi;
                        $data_explode_hasil_kerja_1 = $explode_hasil_kerja_1[1];
                        $lembur_1 = 1.5;
                    }

                    $explode_hasil_kerja_2 = explode("|",$pengerjaan->hasil_kerja_2);
                    $umk_borongan_lokal_2 = UMKBoronganAmbri::select('id','jenis_produk','umk_etiket','umk_las_tepi','umk_las_pojok','umk_ambri')->where('id',$explode_hasil_kerja_2[0])->first();
                    // dd($umk_borongan_lokal_1);
                    if(empty($umk_borongan_lokal_2)){
                        $jenis_produk_2 = '-';
                        $hasil_kerja_2 = null;
                        $data_explode_hasil_kerja_2 = '-';
                        $lembur_2 = 1;
                    }else{
                        $jenis_produk_2 = $umk_borongan_lokal_2->jenis_produk;
                        $hasil_kerja_2 = $explode_hasil_kerja_2[1]*$umk_borongan_lokal_2->umk_las_tepi;
                        $data_explode_hasil_kerja_2 = $explode_hasil_kerja_2[1];
                        $lembur_2 = 1.5;
                    }

                    $explode_hasil_kerja_3 = explode("|",$pengerjaan->hasil_kerja_3);
                    $umk_borongan_lokal_3 = UMKBoronganAmbri::select('id','jenis_produk','umk_etiket','umk_las_tepi','umk_las_pojok','umk_ambri')->where('id',$explode_hasil_kerja_3[0])->first();
                    // dd($umk_borongan_lokal_1);
                    if(empty($umk_borongan_lokal_3)){
                        $jenis_produk_3 = '-';
                        $hasil_kerja_3 = null;
                        $data_explode_hasil_kerja_3 = '-';
                        $lembur_3 = 1;
                    }else{
                        $jenis_produk_3 = $umk_borongan_lokal_3->jenis_produk;
                        $hasil_kerja_3 = $explode_hasil_kerja_3[1]*$umk_borongan_lokal_3->umk_las_tepi;
                        $data_explode_hasil_kerja_3 = $explode_hasil_kerja_3[1];
                        $lembur_3 = 1.5;
                    }

                    $explode_hasil_kerja_4 = explode("|",$pengerjaan->hasil_kerja_4);
                    $umk_borongan_lokal_4 = UMKBoronganAmbri::select('id','jenis_produk','umk_etiket','umk_las_tepi','umk_las_pojok','umk_ambri')->where('id',$explode_hasil_kerja_4[0])->first();
                    // dd($umk_borongan_lokal_1);
                    if(empty($umk_borongan_lokal_4)){
                        $jenis_produk_4 = '-';
                        $hasil_kerja_4 = null;
                        $data_explode_hasil_kerja_4 = '-';
                        $lembur_4 = 1;
                    }else{
                        $jenis_produk_4 = $umk_borongan_lokal_4->jenis_produk;
                        $hasil_kerja_4 = $explode_hasil_kerja_4[1]*$umk_borongan_lokal_4->umk_las_tepi;
                        $data_explode_hasil_kerja_4 = $explode_hasil_kerja_4[1];
                        $lembur_4 = 1.5;
                    }

                    $explode_hasil_kerja_5 = explode("|",$pengerjaan->hasil_kerja_5);
                    $umk_borongan_lokal_5 = UMKBoronganAmbri::select('id','jenis_produk','umk_etiket','umk_las_tepi','umk_las_pojok','umk_ambri')->where('id',$explode_hasil_kerja_5[0])->first();
                    // dd($umk_borongan_lokal_1);
                    if(empty($umk_borongan_lokal_5)){
                        $jenis_produk_5 = '-';
                        $hasil_kerja_5 = null;
                        $data_explode_hasil_kerja_5 = '-';
                        $lembur_5 = 1;
                    }else{
                        $jenis_produk_5 = $umk_borongan_lokal_5->jenis_produk;
                        $hasil_kerja_5 = $explode_hasil_kerja_5[1]*$umk_borongan_lokal_5->umk_las_tepi;
                        $data_explode_hasil_kerja_5 = $explode_hasil_kerja_5[1];
                        $lembur_5 = 1.5;
                    }
                }

                if ($pengerjaan->operator_karyawan->jenis_operator_detail_pekerjaan_id == 11) {
                    $explode_hasil_kerja_1 = explode("|",$pengerjaan->hasil_kerja_1);
                    $umk_borongan_lokal_1 = UMKBoronganAmbri::select('id','jenis_produk','umk_etiket','umk_las_tepi','umk_las_pojok','umk_ambri')->where('id',$explode_hasil_kerja_1[0])->first();
                    // dd($umk_borongan_lokal_1);
                    if(empty($umk_borongan_lokal_1)){
                        $jenis_produk_1 = '-';
                        $hasil_kerja_1 = null;
                        $data_explode_hasil_kerja_1 = '-';
                        $lembur_1 = 1;
                    }else{
                        $jenis_produk_1 = $umk_borongan_lokal_1->jenis_produk;
                        $hasil_kerja_1 = $explode_hasil_kerja_1[1]*$umk_borongan_lokal_1->umk_ambri;
                        $data_explode_hasil_kerja_1 = $explode_hasil_kerja_1[1];
                        $lembur_1 = 1.5;
                    }

                    $explode_hasil_kerja_2 = explode("|",$pengerjaan->hasil_kerja_2);
                    $umk_borongan_lokal_2 = UMKBoronganAmbri::select('id','jenis_produk','umk_etiket','umk_las_tepi','umk_las_pojok','umk_ambri')->where('id',$explode_hasil_kerja_2[0])->first();
                    // dd($umk_borongan_lokal_1);
                    if(empty($umk_borongan_lokal_2)){
                        $jenis_produk_2 = '-';
                        $hasil_kerja_2 = null;
                        $data_explode_hasil_kerja_2 = '-';
                        $lembur_2 = 1;
                    }else{
                        $jenis_produk_2 = $umk_borongan_lokal_2->jenis_produk;
                        $hasil_kerja_2 = $explode_hasil_kerja_2[1]*$umk_borongan_lokal_2->umk_ambri;
                        $data_explode_hasil_kerja_2 = $explode_hasil_kerja_2[1];
                        $lembur_2 = 1.5;
                    }

                    $explode_hasil_kerja_3 = explode("|",$pengerjaan->hasil_kerja_3);
                    $umk_borongan_lokal_3 = UMKBoronganAmbri::select('id','jenis_produk','umk_etiket','umk_las_tepi','umk_las_pojok','umk_ambri')->where('id',$explode_hasil_kerja_3[0])->first();
                    // dd($umk_borongan_lokal_1);
                    if(empty($umk_borongan_lokal_3)){
                        $jenis_produk_3 = '-';
                        $hasil_kerja_3 = null;
                        $data_explode_hasil_kerja_3 = '-';
                        $lembur_3 = 1;
                    }else{
                        $jenis_produk_3 = $umk_borongan_lokal_3->jenis_produk;
                        $hasil_kerja_3 = $explode_hasil_kerja_3[1]*$umk_borongan_lokal_3->umk_ambri;
                        $data_explode_hasil_kerja_3 = $explode_hasil_kerja_3[1];
                        $lembur_3 = 1.5;
                    }

                    $explode_hasil_kerja_4 = explode("|",$pengerjaan->hasil_kerja_4);
                    $umk_borongan_lokal_4 = UMKBoronganAmbri::select('id','jenis_produk','umk_etiket','umk_las_tepi','umk_las_pojok','umk_ambri')->where('id',$explode_hasil_kerja_4[0])->first();
                    // dd($umk_borongan_lokal_1);
                    if(empty($umk_borongan_lokal_4)){
                        $jenis_produk_4 = '-';
                        $hasil_kerja_4 = null;
                        $data_explode_hasil_kerja_4 = '-';
                        $lembur_4 = 1;
                    }else{
                        $jenis_produk_4 = $umk_borongan_lokal_4->jenis_produk;
                        $hasil_kerja_4 = $explode_hasil_kerja_4[1]*$umk_borongan_lokal_4->umk_ambri;
                        $data_explode_hasil_kerja_4 = $explode_hasil_kerja_4[1];
                        $lembur_4 = 1.5;
                    }

                    $explode_hasil_kerja_5 = explode("|",$pengerjaan->hasil_kerja_5);
                    $umk_borongan_lokal_5 = UMKBoronganAmbri::select('id','jenis_produk','umk_etiket','umk_las_tepi','umk_las_pojok','umk_ambri')->where('id',$explode_hasil_kerja_5[0])->first();
                    // dd($umk_borongan_lokal_1);
                    if(empty($umk_borongan_lokal_5)){
                        $jenis_produk_5 = '-';
                        $hasil_kerja_5 = null;
                        $data_explode_hasil_kerja_5 = '-';
                        $lembur_5 = 1;
                    }else{
                        $jenis_produk_5 = $umk_borongan_lokal_5->jenis_produk;
                        $hasil_kerja_5 = $explode_hasil_kerja_5[1]*$umk_borongan_lokal_5->umk_ambri;
                        $data_explode_hasil_kerja_5 = $explode_hasil_kerja_5[1];
                        $lembur_5 = 1.5;
                    }
                }
                //End Ekspor

                $total_hasil_kerja = $hasil_kerja_1+$hasil_kerja_2+$hasil_kerja_3+$hasil_kerja_4+$hasil_kerja_5;
                // dd($hasil_kerja_1);
                array_push($upah_lembur,$pengerjaan->uang_lembur);
                array_push($total_upah_hasil_kerja,$total_hasil_kerja);
            }

            if (empty($pw->tunjangan_kerja)) {
                $tunjangan_kerja = 0;
            }else{
                $tunjangan_kerja = $pw->tunjangan_kerja;
            }

            if (empty($pw->tunjangan_kehadiran)) {
                $tunjangan_kehadiran = 0;
            }else{
                $tunjangan_kehadiran = $pw->tunjangan_kehadiran;
            }

            if (empty($pw->uang_makan)) {
                $uang_makan = 0;
            }else{
                $uang_makan = $pw->uang_makan;
            }

            if (empty($pw->plus_1)) {
                $plus_1 = 0;
                $ket_plus_1 = null;
            }else{
                $explode_plus_1 = explode("|",$pw->plus_1);
                $plus_1 = floatval($explode_plus_1[0]);
                $ket_plus_1 = $explode_plus_1[1];
            }

            if (empty($pw->plus_2)) {
                $plus_2 = 0;
                $ket_plus_2 = null;
            }else{
                $explode_plus_2 = explode("|",$pw->plus_2);
                $plus_2 = floatval($explode_plus_2[0]);
                $ket_plus_2 = $explode_plus_2[1];
            }

            if (empty($pw->plus_3)) {
                $plus_3 = 0;
                $ket_plus_3 = null;
            }else{
                $explode_plus_3 = explode("|",$pw->plus_3);
                $plus_3 = floatval($explode_plus_3[0]);
                $ket_plus_3 = $explode_plus_3[1];
            }

            if (empty($pw->jht)) {
                $jht = 0;
            }else{
                $jht = $pw->jht;
            }

            if (empty($pw->bpjs_kesehatan)) {
                $bpjs_kesehatan = 0;
            }else{
                $bpjs_kesehatan = $pw->bpjs_kesehatan;
            }

            if (empty($pw->minus_1)) {
                $minus_1 = '0';
                $ket_minus_1 = null;
            }else{
                $explode_minus_1 = explode("|",$pw->minus_1);
                $minus_1 = floatval($explode_minus_1[0]);
                $ket_minus_1 = $explode_minus_1[1];
            }

            if (empty($pw->minus_2)) {
                $minus_2 = 0;
                $ket_minus_2 = null;
            }else{
                $explode_minus_2 = explode("|",$pw->minus_2);
                $minus_2 = floatval($explode_minus_2[0]);
                $ket_minus_2 = $explode_minus_2[1];
            }

            $total_gaji_diterima = (array_sum($total_upah_hasil_kerja)
                                    +array_sum($upah_lembur)
                                    +$tunjangan_kerja+
                                    $tunjangan_kehadiran+
                                    $uang_makan+
                                    $plus_1+
                                    $plus_2+
                                    $plus_3
                                    )
                                    -
                                    ($jht+$bpjs_kesehatan+$minus_1+$minus_2)
                                    ;

            if($key%2===0){
                // ### LINE 1 ###
                $pdf->SetFont('Arial','B',8);
                $pdf->Cell(35,5,'TANGGAL GAJI','LT',0,'C'); 
                $pdf->SetFont('Arial','',8);
                $pdf->Cell(60,5,Carbon::parse($exp_tgl_awal[0] . '-' . $exp_tgl_awal[1] . '-' . $exp_tgl_awal[2])->isoFormat('D MMMM').' s/d '.Carbon::parse($exp_tgl_akhir[0] . '-' . $exp_tgl_akhir[1] . '-' . $exp_tgl_akhir[2])->isoFormat('D MMMM YYYY'),'TR',0,'L');
                $pdf->ln(3);

                $pdf->SetFont('Arial','B',8);
                $pdf->Cell(35,5,'Nama','L',0,'L'); 
                $pdf->SetFont('Arial','B',7);
                $pdf->Cell(60,5,strtoupper($pw->nama).' ('.$pw->nik.')','R',0,'L');
                $pdf->ln(4);

                $pdf->SetFont('Arial','',8);
                $pdf->Cell(35,5,'GAJI','L',0,'L'); 
                $pdf->Cell(22,5,$a.' HARI','',0,'L');
                $pdf->Cell(3,5,'Rp','',0,'L');
                $pdf->Cell(35,5,number_format(array_sum($total_upah_hasil_kerja),0,',','.'),'R',0,'R');
                $pdf->ln(3);

                $pdf->SetFont('Arial','',8);
                $pdf->Cell(35,5,'Lembur','L',0,'L'); 
                $pdf->Cell(22,5,'','',0,'L');
                $pdf->Cell(3,5,'Rp','',0,'L');
                $pdf->Cell(35,5,number_format(array_sum($upah_lembur),0,',','.'),'R',0,'R');
                $pdf->ln(3);

                $pdf->SetFont('Arial','',8);
                $pdf->Cell(35,5,'Insentif Kehadiran','L',0,'L'); 
                $pdf->Cell(22,5,'','',0,'L');
                $pdf->Cell(3,5,'Rp','',0,'L');
                $pdf->Cell(35,5,number_format($tunjangan_kehadiran,0,',','.'),'R',0,'R');
                $pdf->ln(3);

                $pdf->SetFont('Arial','',8);
                $pdf->Cell(35,5,'Tunjangan Kerja','L',0,'L'); 
                $pdf->Cell(22,5,'','',0,'L');
                $pdf->Cell(3,5,'Rp','',0,'L');
                $pdf->Cell(35,5,number_format($tunjangan_kerja,0,',','.'),'R',0,'R');
                $pdf->ln(3);

                $pdf->SetFont('Arial','',8);
                $pdf->Cell(12,5,'Plus','L',0,'L'); 
                $pdf->Cell(45,5,"( ".$ket_plus_1." )",'',0,'L');
                $pdf->Cell(3,5,'Rp','',0,'L');
                $pdf->Cell(35,5,number_format($plus_1,0,',','.'),'R',0,'R');
                $pdf->ln(3);

                $pdf->SetFont('Arial','',8);
                $pdf->Cell(12,5,'','L',0,'L'); 
                $pdf->Cell(45,5,"( ".$ket_plus_2." )",'',0,'L');
                $pdf->Cell(3,5,'Rp','',0,'L');
                $pdf->Cell(35,5,number_format($plus_2,0,',','.'),'R',0,'R');
                $pdf->ln(3);

                $pdf->SetFont('Arial','',8);
                $pdf->Cell(12,5,'','L',0,'L'); 
                $pdf->Cell(45,5,"( ".$ket_plus_3." )",'',0,'L');
                $pdf->Cell(3,5,'Rp','',0,'L');
                $pdf->Cell(35,5,number_format($plus_3,0,',','.'),'R',0,'R');
                $pdf->ln(3);

                $pdf->SetFont('Arial','',8);
                $pdf->Cell(35,5,'Uang Makan','L',0,'L'); 
                $pdf->Cell(22,5,'','',0,'L');
                $pdf->Cell(3,5,'Rp','',0,'L');
                $pdf->Cell(35,5,number_format($uang_makan,0,',','.'),'R',0,'R');
                $pdf->SetFont('Arial','',8);
                $pdf->ln(4);

                $pdf->SetFont('Arial','B',8);
                $pdf->Cell(20,5,'POTONGAN','L',0,'L'); 
                $pdf->Cell(37,5,'BPJS > JHT + JP','',0,'L');
                $pdf->Cell(3,5,'Rp','',0,'L');
                $pdf->Cell(35,5,number_format($jht,0,',','.'),'R',0,'R');
                $pdf->ln(3);

                $pdf->SetFont('Arial','B',8);
                $pdf->Cell(20,5,'','L',0,'L'); 
                $pdf->Cell(37,5,'BPJS Kesehatan','',0,'L');
                $pdf->Cell(3,5,'Rp','',0,'L');
                $pdf->Cell(35,5,number_format($bpjs_kesehatan,0,',','.'),'R',0,'R');
                $pdf->ln(3);

                $pdf->SetFont('Arial','B',8);
                $pdf->Cell(20,5,'','L',0,'L'); 
                $pdf->Cell(37,5,"( ".$ket_minus_1." )",'',0,'L');
                $pdf->Cell(3,5,'Rp','',0,'L');
                $pdf->Cell(35,5,number_format($minus_1,0,',','.'),'R',0,'R');
                $pdf->ln(3);

                $pdf->SetFont('Arial','B',8);
                $pdf->Cell(20,5,'','L',0,'L'); 
                $pdf->Cell(37,5,"( ".$ket_minus_2." )",'',0,'L');
                $pdf->Cell(3,5,'Rp','',0,'L');
                $pdf->Cell(35,5,number_format($minus_2,0,',','.'),'R',0,'R');
                $pdf->ln(5);

                $pdf->SetFont('Arial','B',9);
                $pdf->Cell(35,5,'TOTAL DITERIMA','BL',0,'L'); 
                $pdf->Cell(22,5,'','B',0,'L');
                $pdf->Cell(3,5,'Rp','B',0,'L');
                $pdf->Cell(35,5,number_format($total_gaji_diterima,0,',','.'),'BR',0,'R');
                $pdf->ln(3);

                $no=$no+1;
                if ($no%5==0)
                {
                    $page=round($no/5);
                    $pdf->ln(2);
                    $pdf->SetFont('Arial','',7);
                    $pdf->Cell(35,5,"- page $page -",'',0,''); 
                }
                $pdf->ln(3);
            }else{
                // ### LINE 1 ###
                $pdf->SetX(106);
                $pdf->SetFont('Arial','B',8);
                $pdf->Cell(35,5,'TANGGAL GAJI','LT',0,'C'); 
                $pdf->SetFont('Arial','',8);
                $pdf->Cell(60,5,Carbon::parse($exp_tgl_awal[0] . '-' . $exp_tgl_awal[1] . '-' . $exp_tgl_awal[2])->isoFormat('D MMMM').' s/d '.Carbon::parse($exp_tgl_akhir[0] . '-' . $exp_tgl_akhir[1] . '-' . $exp_tgl_akhir[2])->isoFormat('D MMMM YYYY'),'TR',0,'L');
                $pdf->ln(3);

                $pdf->SetX(106);
                $pdf->SetFont('Arial','B',8);
                $pdf->Cell(35,5,'Nama','L',0,'L'); 
                $pdf->SetFont('Arial','B',7);
                $pdf->Cell(60,5,strtoupper($pw->nama).' ('.$pw->nik.')','R',0,'L');
                $pdf->ln(4);

                $pdf->SetX(106);
                $pdf->SetFont('Arial','',8);
                $pdf->Cell(35,5,'GAJI','L',0,'L'); 
                $pdf->Cell(22,5,$a.' HARI','',0,'L');
                $pdf->Cell(3,5,'Rp','',0,'L');
                $pdf->Cell(35,5,number_format(array_sum($total_upah_hasil_kerja),0,',','.'),'R',0,'R');
                $pdf->ln(3);

                $pdf->SetX(106);
                $pdf->SetFont('Arial','',8);
                $pdf->Cell(35,5,'Lembur','L',0,'L'); 
                $pdf->Cell(22,5,'','',0,'L');
                $pdf->Cell(3,5,'Rp','',0,'L');
                $pdf->Cell(35,5,number_format(array_sum($upah_lembur),0,',','.'),'R',0,'R');
                $pdf->ln(3);

                $pdf->SetX(106);
                $pdf->SetFont('Arial','',8);
                $pdf->Cell(35,5,'Insentif Kehadiran','L',0,'L'); 
                $pdf->Cell(22,5,'','',0,'L');
                $pdf->Cell(3,5,'Rp','',0,'L');
                $pdf->Cell(35,5,number_format($tunjangan_kehadiran,0,',','.'),'R',0,'R');
                $pdf->ln(3);

                $pdf->SetX(106);
                $pdf->SetFont('Arial','',8);
                $pdf->Cell(35,5,'Tunjangan Kerja','L',0,'L'); 
                $pdf->Cell(22,5,'','',0,'L');
                $pdf->Cell(3,5,'Rp','',0,'L');
                $pdf->Cell(35,5,number_format($tunjangan_kerja,0,',','.'),'R',0,'R');
                $pdf->ln(3);

                $pdf->SetX(106);
                $pdf->SetFont('Arial','',8);
                $pdf->Cell(12,5,'Plus','L',0,'L'); 
                $pdf->Cell(45,5,"( ".$ket_plus_1." )",'',0,'L');
                $pdf->Cell(3,5,'Rp','',0,'L');
                $pdf->Cell(35,5,number_format($plus_1,0,',','.'),'R',0,'R');
                $pdf->ln(3);

                $pdf->SetX(106);
                $pdf->SetFont('Arial','',8);
                $pdf->Cell(12,5,'','L',0,'L'); 
                $pdf->Cell(45,5,"( ".''." )",'',0,'L');
                $pdf->Cell(3,5,'Rp','',0,'L');
                $pdf->Cell(35,5,number_format($plus_2,0,',','.'),'R',0,'R');
                $pdf->ln(3);

                $pdf->SetX(106);
                $pdf->SetFont('Arial','',8);
                $pdf->Cell(12,5,'','L',0,'L'); 
                $pdf->Cell(45,5,"( ".''." )",'',0,'L');
                $pdf->Cell(3,5,'Rp','',0,'L');
                $pdf->Cell(35,5,number_format($plus_3,0,',','.'),'R',0,'R');
                $pdf->ln(3);

                $pdf->SetX(106);
                $pdf->SetFont('Arial','',8);
                $pdf->Cell(35,5,'Uang Makan','L',0,'L'); 
                $pdf->Cell(22,5,'','',0,'L');
                $pdf->Cell(3,5,'Rp','',0,'L');
                $pdf->Cell(35,5,number_format($uang_makan,0,',','.'),'R',0,'R');
                $pdf->SetFont('Arial','',8);
                $pdf->ln(4);

                $pdf->SetX(106);
                $pdf->SetFont('Arial','B',8);
                $pdf->Cell(20,5,'POTONGAN','L',0,'L'); 
                $pdf->Cell(37,5,'BPJS > JHT + JP','',0,'L');
                $pdf->Cell(3,5,'Rp','',0,'L');
                $pdf->Cell(35,5,number_format($jht,0,',','.'),'R',0,'R');
                $pdf->ln(3);

                $pdf->SetX(106);
                $pdf->SetFont('Arial','B',8);
                $pdf->Cell(20,5,'','L',0,'L'); 
                $pdf->Cell(37,5,'BPJS Kesehatan','',0,'L');
                $pdf->Cell(3,5,'Rp','',0,'L');
                $pdf->Cell(35,5,number_format($bpjs_kesehatan,0,',','.'),'R',0,'R');
                $pdf->ln(3);

                $pdf->SetX(106);
                $pdf->SetFont('Arial','B',8);
                $pdf->Cell(20,5,'','L',0,'L'); 
                $pdf->Cell(37,5,"( ".$ket_minus_1." )",'',0,'L');
                $pdf->Cell(3,5,'Rp','',0,'L');
                $pdf->Cell(35,5,number_format($minus_1,0,',','.'),'R',0,'R');
                $pdf->ln(3);

                $pdf->SetX(106);
                $pdf->SetFont('Arial','B',8);
                $pdf->Cell(20,5,'','L',0,'L'); 
                $pdf->Cell(37,5,"( ".$ket_minus_2." )",'',0,'L');
                $pdf->Cell(3,5,'Rp','',0,'L');
                $pdf->Cell(35,5,number_format($minus_2,0,',','.'),'R',0,'R');
                $pdf->ln(5);

                $pdf->SetX(106);
                $pdf->SetFont('Arial','B',9);
                $pdf->Cell(35,5,'TOTAL DITERIMA','BL',0,'L'); 
                $pdf->Cell(22,5,'','B',0,'L');
                $pdf->Cell(3,5,'Rp','B',0,'L');
                $pdf->Cell(35,5,number_format($total_gaji_diterima,0,',','.'),'BR',0,'R');
                $pdf->ln(3);

                $no=$no+1;
                if ($no%5==0)
                {
                    $page=round($no/5);
                    $pdf->ln(2);
                    $pdf->SetFont('Arial','',7);
                    $pdf->Cell(35,5,"- page $page -",'',0,''); 
                }
                $pdf->ln(3);
            }
        }
        $pdf->Output();
        exit;
    }

    public function borongan_bank($kode_pengerjaan)
    {
        $data['kode_pengerjaan'] = $kode_pengerjaan;
        // dd($data);
        $data['new_data_pengerjaan'] = NewDataPengerjaan::where('kode_pengerjaan',$kode_pengerjaan)->first();
        $data['karyawan_operators'] = PengerjaanWeekly::select([
                                                      'pengerjaan_weekly.id as id',
                                                      'operator_karyawan.nik as nik',
                                                      'biodata_karyawan.nama as nama',
                                                      'biodata_karyawan.rekening as rekening',
                                                      'pengerjaan_weekly.upah_dasar as upah_dasar'
                                                    ])
                                                    ->leftJoin('operator_karyawan','operator_karyawan.id','=','pengerjaan_weekly.operator_karyawan_id')
                                                    ->leftJoin('itic_emp.biodata_karyawan','biodata_karyawan.nik','=','operator_karyawan.nik')
                                                    ->where('kode_pengerjaan',$kode_pengerjaan)
                                                    ->orderBy('biodata_karyawan.nama','asc')
                                                    ->get();
        $pdf = Pdf::loadView('backend.payrol.penggajian.borongan.bank',$data);
        return $pdf->stream();
    }

    public function borongan_weekly_report($kode_pengerjaan)
    {
        $data['kode_pengerjaan'] = $kode_pengerjaan;
        $data['new_data_pengerjaan'] = NewDataPengerjaan::where('kode_pengerjaan',$kode_pengerjaan)->first();
        $data['jenis_operator_detail_pengerjaans'] = JenisOperatorDetailPengerjaan::where('jenis_operator_detail_id',1)->get();

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
            $data = NewDataPengerjaan::where('kode_pengerjaan','LIKE','%PH%')->get();
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
                                $btn.=      '<a href='.route('payrol.harian.bank',['kode_pengerjaan' => $row->kode_pengerjaan]).' class="btn btn-primary" target="_blank"><i class="far fa-file-pdf"></i> Bank<a>';
                                $btn.=      '<a href='.route('payrol.harian.weekly_report',['kode_pengerjaan' => $row->kode_pengerjaan]).' class="btn btn-danger" target="_blank"><i class="fas fa-book"></i> Weekly Report<a>';
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
        $data['new_data_pengerjaan'] = NewDataPengerjaan::where('kode_pengerjaan',$kode_pengerjaan)->first();
        
        $explode_tanggal_pengerjaans = explode('#',$data['new_data_pengerjaan']['tanggal']);
        $exp_tanggals = array_filter($explode_tanggal_pengerjaans);
        $a = count($exp_tanggals);
        $exp_tgl_awal = explode('-', $exp_tanggals[1]);
        $exp_tgl_akhir = explode('-', $exp_tanggals[$a]);

        $pengerjaan_harians = PengerjaanHarian::select([
                                                'pengerjaan_harian.id as id',
                                                'pengerjaan_harian.operator_harian_karyawan_id as operator_harian_karyawan_id',
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
                                            ])
                                            ->leftJoin('operator_harian_karyawan','operator_harian_karyawan.id','=','pengerjaan_harian.operator_harian_karyawan_id')
                                            ->leftJoin('itic_emp.biodata_karyawan','biodata_karyawan.nik','=','operator_harian_karyawan.nik')
                                            ->where('pengerjaan_harian.kode_pengerjaan',$kode_pengerjaan)
                                            ->orderBy('operator_harian_karyawan.jenis_operator_detail_pekerjaan_id','asc')
                                            // ->orderBy('biodata_karyawan.nama','asc')
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

            if (empty($ph->tunjangan_kehadiran)) {
                $tunjangan_kehadiran = 0;
            }else{
                $tunjangan_kehadiran = $ph->tunjangan_kehadiran;
            }

            if (empty($ph->tunjangan_kerja)) {
                $tunjangan_kerja = 0;
            }else{
                $tunjangan_kerja = $ph->tunjangan_kerja;
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
                $plus_1 = $explode_plus_1[0];
                $ket_plus_1 = $explode_plus_1[1];
            }

            if (empty($ph->plus_2)) {
                $plus_2 = 0;
                $ket_plus_2 = "";
            }else{
                $explode_plus_2 = explode("|",$ph->plus_2);
                $plus_2 = $explode_plus_2[0];
                $ket_plus_2 = $explode_plus_2[1];
            }

            if (empty($ph->plus_3)) {
                $plus_3 = 0;
                $ket_plus_3 = "";
            }else{
                $explode_plus_3 = explode("|",$ph->plus_3);
                $plus_3 = $explode_plus_3[0];
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
                    $minus_1 = $explode_minus_1[0];
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
                    $minus_2 = $explode_minus_2[0];
                }
                $ket_minus_2 = $explode_minus_2[1];
            }

            if (empty($ph->jht)) {
                $jht = 0;
            }else{
                $jht = $ph->jht;
            }

            if (empty($ph->bpjs_kesehatan)) {
                $bpjs_kesehatan = 0;
            }else{
                $bpjs_kesehatan = $ph->bpjs_kesehatan;
            }

            $total_gaji_diterima = ($ph->upah_dasar_weekly+$hasil_lembur+$ph->tunjangan_kehadiran+$ph->tunjangan_kerja+
                                    $plus_1+$plus_2+$plus_3+$ph->uang_makan)-
                                    ($jht+$bpjs_kesehatan+$minus_1+$minus_2);

            if($key%2===0){
                // ### LINE 1 ###
                $pdf->SetFont('Arial','B',8);
                $pdf->Cell(35,5,'TANGGAL GAJI','LT',0,'C'); 
                $pdf->SetFont('Arial','',8);
                $pdf->Cell(60,5,Carbon::parse($exp_tgl_awal[0] . '-' . $exp_tgl_awal[1] . '-' . $exp_tgl_awal[2])->isoFormat('D MMMM').' s/d '.Carbon::parse($exp_tgl_akhir[0] . '-' . $exp_tgl_akhir[1] . '-' . $exp_tgl_akhir[2])->isoFormat('D MMMM YYYY'),'TR',0,'L');
                $pdf->ln(3);

                // ### LINE 2 ###
                $pdf->SetFont('Arial','B',8);
                $pdf->Cell(35,5,'Nama','L',0,'L'); 
                $pdf->SetFont('Arial','B',7);
                $pdf->Cell(60,5,strtoupper($ph->nama).' ('.$ph->nik.')','R',0,'L');
                $pdf->ln(4);

                // ### LINE 3 ###
                $pdf->SetFont('Arial','',8);
                $pdf->Cell(35,5,'GAJI','L',0,'L'); 
                $pdf->Cell(22,5,$ph->hari_kerja.' HARI','',0,'L');
                $pdf->Cell(3,5,'Rp','',0,'L');
                $pdf->Cell(35,5,number_format($ph->upah_dasar_weekly,0,',','.'),'R',0,'R');
                $pdf->ln(3);

                // ### LINE 4 ###
                $pdf->SetFont('Arial','',8);
                $pdf->Cell(35,5,'Lembur '.'( '.$total_jam_lembur.' Jam )','L',0,'L'); 
                $pdf->Cell(22,5,'','',0,'L');
                $pdf->Cell(3,5,'Rp','',0,'L');
                $pdf->Cell(35,5,number_format($hasil_lembur,0,',','.'),'R',0,'R');
                $pdf->ln(3);

                // ### LINE 5 ###
                $pdf->SetFont('Arial','',8);
                $pdf->Cell(35,5,'Insentif Kehadiran','L',0,'L'); 
                $pdf->Cell(22,5,'','',0,'L');
                $pdf->Cell(3,5,'Rp','',0,'L');
                $pdf->Cell(35,5,number_format($tunjangan_kehadiran,0,',','.'),'R',0,'R');
                $pdf->ln(3);

                // ### LINE 6 ###
                $pdf->SetFont('Arial','',8);
                $pdf->Cell(35,5,'Tunjangan Kerja','L',0,'L'); 
                $pdf->Cell(22,5,'','',0,'L');
                $pdf->Cell(3,5,'Rp','',0,'L');
                $pdf->Cell(35,5,number_format($tunjangan_kerja,0,',','.'),'R',0,'R');
                $pdf->ln(3);

                // ### LINE 7 ###
                $pdf->SetFont('Arial','',8);
                $pdf->Cell(12,5,'Plus','L',0,'L'); 
                $pdf->Cell(45,5,"( ".$ket_plus_1." )",'',0,'L');
                $pdf->Cell(3,5,'Rp',0,0,'L');
                $pdf->Cell(35,5,number_format($plus_1,0,',','.'),'R',0,'R');
                $pdf->ln(3);

                // ### LINE 8 ###
                $pdf->SetFont('Arial','',8);
                $pdf->Cell(12,5,'','L',0,'L'); 
                $pdf->Cell(45,5,"( ".$ket_plus_2." )",'',0,'L');
                $pdf->Cell(3,5,'Rp','',0,'L');
                $pdf->Cell(35,5,number_format($plus_2,0,',','.'),'R',0,'R');
                $pdf->ln(3);

                $pdf->SetFont('Arial','',8);
                $pdf->Cell(12,5,'','L',0,'L'); 
                $pdf->Cell(45,5,"( ".$ket_plus_3." )",'',0,'L');
                $pdf->Cell(3,5,'Rp','',0,'L');
                $pdf->Cell(35,5,number_format($plus_3,0,',','.'),'R',0,'R');
                $pdf->ln(3);

                // ### LINE 10 ###
                $pdf->Cell(35,5,'Uang Makan','L',0,'L'); 
                $pdf->Cell(22,5,'','',0,'L');
                $pdf->Cell(3,5,'Rp','',0,'L');
                $pdf->Cell(35,5,number_format($uang_makan,0,',','.'),'R',0,'R');
                $pdf->ln(4);

                // ### LINE 11 ###
                $pdf->SetFont('Arial','B',8);
                $pdf->Cell(20,5,'POTONGAN','L',0,'L'); 
                $pdf->Cell(37,5,'BPJS > JHT + JP','',0,'L');
                $pdf->Cell(3,5,'Rp','',0,'L');
                $pdf->Cell(35,5,number_format($jht,0,',','.'),'R',0,'R');
                $pdf->ln(3);

                // ### LINE 12 ###
                $pdf->SetFont('Arial','B',8);
                $pdf->Cell(20,5,'','L',0,'L'); 
                $pdf->Cell(37,5,'BPJS Kesehatan','',0,'L');
                $pdf->Cell(3,5,'Rp','',0,'L');
                $pdf->Cell(35,5,number_format($bpjs_kesehatan,0,',','.'),'R',0,'R');
                $pdf->ln(3);

                // ### LINE 13 ###
                $pdf->SetFont('Arial','B',8);
                $pdf->Cell(20,5,'','L',0,'L'); 
                $pdf->Cell(37,5,"( ".$ket_minus_1." )",'',0,'L');
                $pdf->Cell(3,5,'Rp','',0,'L');
                $pdf->Cell(35,5,number_format($minus_1,0,',','.'),'R',0,'R');
                $pdf->ln(3);

                // ### LINE 14 ###
                $pdf->SetFont('Arial','B',8);
                $pdf->Cell(20,5,'','L',0,'L'); 
                $pdf->Cell(37,5,"( ".$ket_minus_2." )",'',0,'L');
                $pdf->Cell(3,5,'Rp','',0,'L');
                $pdf->Cell(35,5,number_format($minus_2,0,',','.'),'R',0,'R');
                $pdf->ln(5);

                // ### LINE 15 ###
                $pdf->SetFont('Arial','B',9);
                $pdf->Cell(35,5,'TOTAL DITERIMA','BL',0,'L'); 
                $pdf->Cell(22,5,'','B',0,'L');
                $pdf->Cell(3,5,'Rp','B',0,'L');
                $pdf->Cell(35,5,number_format($total_gaji_diterima,0,',','.'),'BR',0,'R');
                $pdf->ln(3);

                $no=$no+1;
                if ($no%5==0)
                {
                    $page=round($no/5);
                    $pdf->ln(2);
                    $pdf->SetFont('Arial','',7);
                    $pdf->Cell(35,5,"- page $page -",'',0,''); 
                }
                $pdf->ln(3);
            }else{
                $x = 105.5;
                // ### LINE 1 ###
                $pdf->SetX($x);
                $pdf->SetFont('Arial','B',8);
                $pdf->Cell(35,5,'TANGGAL GAJI','LT',0,'C'); 
                $pdf->SetFont('Arial','',8);
                $pdf->Cell(60,5,Carbon::parse($exp_tgl_awal[0] . '-' . $exp_tgl_awal[1] . '-' . $exp_tgl_awal[2])->isoFormat('D MMMM').' s/d '.Carbon::parse($exp_tgl_akhir[0] . '-' . $exp_tgl_akhir[1] . '-' . $exp_tgl_akhir[2])->isoFormat('D MMMM YYYY'),'TR',0,'L');
                $pdf->ln(3);

                // ### LINE 2 ###
                $pdf->SetX($x);
                $pdf->SetFont('Arial','B',8);
                $pdf->Cell(35,5,'Nama','L',0,'L'); 
                $pdf->SetFont('Arial','B',7);
                $pdf->Cell(60,5,strtoupper($ph->nama).' ('.$ph->nik.')','R',0,'L');
                $pdf->ln(4);

                // ### LINE 3 ###
                $pdf->SetX($x);
                $pdf->SetFont('Arial','',8);
                $pdf->Cell(35,5,'GAJI','L',0,'L'); 
                $pdf->Cell(22,5,$ph->hari_kerja.' HARI','',0,'L');
                $pdf->Cell(3,5,'Rp','',0,'L');
                $pdf->Cell(35,5,number_format($ph->upah_dasar_weekly,0,',','.'),'R',0,'R');
                $pdf->ln(3);

                // ### LINE 4 ###
                $pdf->SetX($x);
                $pdf->SetFont('Arial','',8);
                $pdf->Cell(35,5,'Lembur '.'( '.$total_jam_lembur.' Jam )','L',0,'L'); 
                $pdf->Cell(22,5,'','',0,'L');
                $pdf->Cell(3,5,'Rp','',0,'L');
                $pdf->Cell(35,5,number_format($hasil_lembur,0,',','.'),'R',0,'R');
                $pdf->ln(3);

                // ### LINE 5 ###
                $pdf->SetX($x);
                $pdf->SetFont('Arial','',8);
                $pdf->Cell(35,5,'Insentif Kehadiran','L',0,'L'); 
                $pdf->Cell(22,5,'','',0,'L');
                $pdf->Cell(3,5,'Rp','',0,'L');
                $pdf->Cell(35,5,number_format($tunjangan_kehadiran,0,',','.'),'R',0,'R');
                $pdf->ln(3);

                // ### LINE 6 ###
                $pdf->SetX($x);
                $pdf->SetFont('Arial','',8);
                $pdf->Cell(35,5,'Tunjangan Kerja','L',0,'L'); 
                $pdf->Cell(22,5,'','',0,'L');
                $pdf->Cell(3,5,'Rp','',0,'L');
                $pdf->Cell(35,5,number_format($tunjangan_kerja,0,',','.'),'R',0,'R');
                $pdf->ln(3);

                // ### LINE 7 ###
                $pdf->SetX($x);
                $pdf->SetFont('Arial','',8);
                $pdf->Cell(12,5,'Plus','L',0,'L'); 
                $pdf->Cell(45,5,"( ".$ket_plus_1." )",'',0,'L');
                $pdf->Cell(3,5,'Rp',0,0,'L');
                $pdf->Cell(35,5,number_format($plus_1,0,',','.'),'R',0,'R');
                $pdf->ln(3);

                // ### LINE 8 ###
                $pdf->SetX($x);
                $pdf->SetFont('Arial','',8);
                $pdf->Cell(12,5,'','L',0,'L'); 
                $pdf->Cell(45,5,"( ".$ket_plus_2." )",'',0,'L');
                $pdf->Cell(3,5,'Rp','',0,'L');
                $pdf->Cell(35,5,number_format($plus_2,0,',','.'),'R',0,'R');
                $pdf->ln(3);

                $pdf->SetX($x);
                $pdf->SetFont('Arial','',8);
                $pdf->Cell(12,5,'','L',0,'L'); 
                $pdf->Cell(45,5,"( ".$ket_plus_3." )",'',0,'L');
                $pdf->Cell(3,5,'Rp','',0,'L');
                $pdf->Cell(35,5,number_format($plus_3,0,',','.'),'R',0,'R');
                $pdf->ln(3);

                // ### LINE 10 ###
                $pdf->SetX($x);
                $pdf->Cell(35,5,'Uang Makan','L',0,'L'); 
                $pdf->Cell(22,5,'','',0,'L');
                $pdf->Cell(3,5,'Rp','',0,'L');
                $pdf->Cell(35,5,number_format($uang_makan,0,',','.'),'R',0,'R');
                $pdf->ln(4);

                // ### LINE 11 ###
                $pdf->SetX($x);
                $pdf->SetFont('Arial','B',8);
                $pdf->Cell(20,5,'POTONGAN','L',0,'L'); 
                $pdf->Cell(37,5,'BPJS > JHT + JP','',0,'L');
                $pdf->Cell(3,5,'Rp','',0,'L');
                $pdf->Cell(35,5,number_format($jht,0,',','.'),'R',0,'R');
                $pdf->ln(3);

                // ### LINE 12 ###
                $pdf->SetX($x);
                $pdf->SetFont('Arial','B',8);
                $pdf->Cell(20,5,'','L',0,'L'); 
                $pdf->Cell(37,5,'BPJS Kesehatan','',0,'L');
                $pdf->Cell(3,5,'Rp','',0,'L');
                $pdf->Cell(35,5,number_format($bpjs_kesehatan,0,',','.'),'R',0,'R');
                $pdf->ln(3);

                // ### LINE 13 ###
                $pdf->SetX($x);
                $pdf->SetFont('Arial','B',8);
                $pdf->Cell(20,5,'','L',0,'L'); 
                $pdf->Cell(37,5,"( ".$ket_minus_1." )",'',0,'L');
                $pdf->Cell(3,5,'Rp','',0,'L');
                $pdf->Cell(35,5,number_format($minus_1,0,',','.'),'R',0,'R');
                $pdf->ln(3);

                // ### LINE 14 ###
                $pdf->SetX($x);
                $pdf->SetFont('Arial','B',8);
                $pdf->Cell(20,5,'','L',0,'L'); 
                $pdf->Cell(37,5,"( ".$ket_minus_2." )",'',0,'L');
                $pdf->Cell(3,5,'Rp','',0,'L');
                $pdf->Cell(35,5,number_format($minus_2,0,',','.'),'R',0,'R');
                $pdf->ln(5);

                // ### LINE 15 ###
                $pdf->SetX($x);
                $pdf->SetFont('Arial','B',9);
                $pdf->Cell(35,5,'TOTAL DITERIMA','BL',0,'L'); 
                $pdf->Cell(22,5,'','B',0,'L');
                $pdf->Cell(3,5,'Rp','B',0,'L');
                $pdf->Cell(35,5,number_format($total_gaji_diterima,0,',','.'),'BR',0,'R');
                $pdf->ln(3);

                $no=$no+1;
                if ($no%5==0)
                {
                    $page=round($no/5);
                    $pdf->ln(2);
                    $pdf->SetFont('Arial','',7);
                    $pdf->Cell(35,5,"- page $page -",'',0,''); 
                }
                $pdf->ln(3);
            }

            // if($key%2===0){
            //     $pdf->SetFont('Arial','B',8);
            //     $pdf->Cell(35,5,'TANGGAL GAJI','LT',0,'C'); 
            //     $pdf->SetFont('Arial','',8);
            //     $pdf->Cell(60,5,Carbon::parse($exp_tgl_awal[0] . '-' . $exp_tgl_awal[1] . '-' . $exp_tgl_awal[2])->isoFormat('D MMMM').' s/d '.Carbon::parse($exp_tgl_akhir[0] . '-' . $exp_tgl_akhir[1] . '-' . $exp_tgl_akhir[2])->isoFormat('D MMMM YYYY'),'TR',0,'L');
                
            //     // $pdf->Cell(2,5,'','LF',0,'L');

            //     // $pdf->SetFont('Arial','B',8);
            //     // $pdf->Cell(35,5,'TANGGAL GAJI','LT',0,'C'); 
            //     // $pdf->SetFont('Arial','',8);
            //     // // $pdf->Cell(60,5,Carbon::parse($exp_tgl_awal[0] . '-' . $exp_tgl_awal[1] . '-' . $exp_tgl_awal[2])->isoFormat('D MMMM').' s/d '.Carbon::parse($exp_tgl_akhir[0] . '-' . $exp_tgl_akhir[1] . '-' . $exp_tgl_akhir[2])->isoFormat('D MMMM YYYY'),'TR',0,'L');
            //     // $pdf->Cell(60,5,'1','TR',0,'L');
            //     // $pdf->ln(3);

            //     // $pdf->SetFont('Arial','B',8);
            //     // $pdf->Cell(35,5,'Nama','L',0,'L'); 
            //     // $pdf->SetFont('Arial','B',7);
            //     // $pdf->Cell(60,5,strtoupper($nama_1).' ('.$nik_1.')','R',0,'L');
                
            //     // $pdf->Cell(2,5,'','LF',0,'L');

            //     // $pdf->SetFont('Arial','B',8);
            //     // $pdf->Cell(35,5,'Nama','L',0,'L'); 
            //     // $pdf->SetFont('Arial','B',7);
            //     // $pdf->Cell(60,5,strtoupper($nama_2).' ('.$nik_2.')','R',0,'L');

            // }
            // $pdf->Cell(2,5,'','LF',0,'L');
            // if($key%2===1){
            //     $pdf->SetFont('Arial','B',8);
            //     $pdf->Cell(35,5,'TANGGAL GAJI','LT',0,'C'); 
            //     $pdf->SetFont('Arial','',8);
            //     // $pdf->Cell(60,5,Carbon::parse($exp_tgl_awal[0] . '-' . $exp_tgl_awal[1] . '-' . $exp_tgl_awal[2])->isoFormat('D MMMM').' s/d '.Carbon::parse($exp_tgl_akhir[0] . '-' . $exp_tgl_akhir[1] . '-' . $exp_tgl_akhir[2])->isoFormat('D MMMM YYYY'),'TR',0,'L');
            //     $pdf->Cell(60,5,'1','TR',0,'L');
            // }
            // $pdf->ln(3);
            // // $pdf->ln(3);
            // // $pdf->Cell(2,5,'','LF',0,'L');
            // // if($key%2===1){
            // //     $pdf->SetFont('Arial','B',8);
            // //     $pdf->Cell(35,5,'TANGGAL GAJI','LT',0,'C'); 
            // //     $pdf->SetFont('Arial','',8);
            // //     // $pdf->Cell(60,5,Carbon::parse($exp_tgl_awal[0] . '-' . $exp_tgl_awal[1] . '-' . $exp_tgl_awal[2])->isoFormat('D MMMM').' s/d '.Carbon::parse($exp_tgl_akhir[0] . '-' . $exp_tgl_akhir[1] . '-' . $exp_tgl_akhir[2])->isoFormat('D MMMM YYYY'),'TR',0,'L');
            // //     $pdf->Cell(60,5,'1','TR',0,'L');
            // //     $pdf->ln(3);
            // // }
        }
        $pdf->Output();
        exit;
    }

    public function harian_bank($kode_pengerjaan)
    {
        $data['kode_pengerjaan'] = $kode_pengerjaan;
        $data['new_data_pengerjaan'] = NewDataPengerjaan::where('kode_pengerjaan',$kode_pengerjaan)->first();
        $data['pengerjaan_harians'] = PengerjaanHarian::select([
                                                        'pengerjaan_harian.id as id',
                                                        'operator_harian_karyawan.nik as nik',
                                                        'biodata_karyawan.nama as nama',
                                                        'pengerjaan_harian.upah_dasar_weekly as upah_dasar_weekly',
                                                    ])
                                                    ->leftJoin('operator_harian_karyawan','operator_harian_karyawan.id','=','pengerjaan_harian.operator_harian_karyawan_id')
                                                    ->leftJoin('itic_emp.biodata_karyawan','biodata_karyawan.nik','=','operator_harian_karyawan.nik')
                                                    ->where('pengerjaan_harian.kode_pengerjaan',$kode_pengerjaan)
                                                    ->orderBy('biodata_karyawan.nama','asc')
                                                    ->get();
        $pdf = Pdf::loadView('backend.payrol.penggajian.harian.bank',$data);
        return $pdf->stream();
    }

    public function harian_weekly_report($kode_pengerjaan)
    {
        $data['kode_pengerjaan'] = $kode_pengerjaan;
        $data['new_data_pengerjaan'] = NewDataPengerjaan::where('kode_pengerjaan',$kode_pengerjaan)->first();
        $data['jenis_operator_detail_pengerjaans'] = JenisOperatorDetailPengerjaan::where('jenis_operator_detail_id',4)->get();

        $pdf = Pdf::loadView('backend.payrol.penggajian.harian.weekly_report',$data);
        $pdf->setPaper('a4','landscape')->setWarnings(false);
        return $pdf->stream();
        // return view('backend.payrol.penggajian.harian.weekly_report',$data);
    }

    public function supir_rit(Request $request)
    {
        if ($request->ajax()) {
            $data = NewDataPengerjaan::where('kode_pengerjaan','LIKE','%PS%')->get();
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
                                $btn.=      '<a href='.route('payrol.supir_rit.bank',['kode_pengerjaan' => $row->kode_pengerjaan]).' class="btn btn-primary" target="_blank"><i class="far fa-file-pdf"></i> Bank<a>';
                                $btn.=      '<a href='.route('payrol.supir_rit.weekly_report',['kode_pengerjaan' => $row->kode_pengerjaan]).' class="btn btn-danger" target="_blank"><i class="fas fa-book"></i> Weekly Report<a>';
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
        $data['new_data_pengerjaan'] = NewDataPengerjaan::where('kode_pengerjaan',$kode_pengerjaan)->first();
        $explode_tanggal_pengerjaans = explode('#',$data['new_data_pengerjaan']['tanggal']);
        $exp_tanggals = array_filter($explode_tanggal_pengerjaans);
        $a = count($exp_tanggals);
        // dd($exp_tanggals);
        $exp_tgl_awal = explode('-', $exp_tanggals[1]);
        $exp_tgl_akhir = explode('-', $exp_tanggals[$a]);

        $pengerjaan_rit_weeklys = PengerjaanRITWeekly::select([
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
                                                    ->leftJoin('itic_emp.biodata_karyawan','biodata_karyawan.nik','=','operator_supir_rit_karyawan.nik')
                                                    ->where('pengerjaan_supir_rit_weekly.kode_pengerjaan',$kode_pengerjaan)
                                                    ->orderBy('biodata_karyawan.nama','asc')
                                                    ->get();
            // dd($pengerjaan_rit_weeklys);
        $pdf = new Fpdf('P', 'mm', 'A4');
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
            $pengerjaan_rit_dailys = PengerjaanRITHarian::where('karyawan_supir_rit_id',$pengerjaan_rit_weekly->karyawan_supir_rit_id)
                                                        ->where('kode_pengerjaan',$kode_pengerjaan)
                                                        ->get();
            if ($prw%2===0) {
                ### LINE 1 ###
                $pdf->SetFont('Arial','B',9);
                $pdf->Cell(95,5,'GAJI RIT-RITAN '.strtoupper($pengerjaan_rit_weekly->nama),'LTR',0,'C'); 
                $pdf->ln(4);
        
                ### LINE 2 ###
                $pdf->SetFont('Arial','B',9);
                $pdf->Cell(95,5,'Tanggal: '.Carbon::parse($exp_tgl_awal[0] . '-' . $exp_tgl_awal[1] . '-' . $exp_tgl_awal[2])->isoFormat('D MMMM').' s/d '.Carbon::parse($exp_tgl_akhir[0] . '-' . $exp_tgl_akhir[1] . '-' . $exp_tgl_akhir[2])->isoFormat('D MMMM YYYY'),'LRB',0,'C');
                $pdf->ln(5);
        
                ### LINE 3 ###
                $pdf->SetFont('Arial','B',9);
                $pdf->Cell(20,5,'TGL','LB',0,'C'); 
                $pdf->Cell(53,5,'KETERANGAN','LRB',0,'C');
                $pdf->Cell(22,5,'Rp','BR',0,'C');
                $pdf->ln(5);

                $upah_dasar = array();
                foreach ($pengerjaan_rit_dailys as $prd => $pengerjaan_rit_daily) {
                    if (empty($pengerjaan_rit_daily->hasil_kerja_1)) {
                        // $jenis_umk = 0;
                        $hasil_kerja_1 = 0;
                        $hasil_umk_rit = 0;
                        $tarif_umk = 0;
                        $dpb = 0;
                        $jenis_umk = '-';
                    }else{
                        $explode_hasil_kerja_1 = explode("|",$pengerjaan_rit_daily->hasil_kerja_1);
                        $umk_rit = RitUMK::where('id',$explode_hasil_kerja_1[0])->first();
                        // dd($umk_rit->rit_tujuan);
                        if (empty($umk_rit)) {
                            $hasil_kerja_1 = 0;
                            $hasil_umk_rit = 0;
                            $tarif_umk = 0;
                            $dpb = 0;
                            $jenis_umk = '-';
                        }else{
                            $hasil_kerja_1 = $umk_rit->tarif;
                            $hasil_umk_rit = $umk_rit->kategori_upah;
                            $tarif_umk = $umk_rit->tarif;
                            $dpb = $pengerjaan_rit_daily->dpb/7*$pengerjaan_rit_daily->upah_dasar;
                            if (empty($umk_rit->rit_tujuan)) {
                                $jenis_umk = '-';
                            }else{
                                $jenis_umk = $umk_rit->rit_tujuan->tujuan.' - '.$umk_rit->rit_kendaraan->jenis_kendaraan;
                            }
                        }
                        $total_upah_dasar = $tarif_umk+$dpb;
                        array_push($upah_dasar,$total_upah_dasar);
                    }
                    ### LINE 4 ###
                    $pdf->SetFont('Arial','',9);
                    $pdf->Cell(20,5,Carbon::parse($pengerjaan_rit_daily->tanggal_pengerjaan)->isoFormat('D MMMM'),'L',0,'C'); 
                    $pdf->Cell(53,5,'DPB','LR',0,'L');
                    $pdf->Cell(22,5,number_format($dpb,0,',','.'),'R',0,'R');
                    $pdf->ln(4);
            
                    ### LINE 5 ###
                    $pdf->SetFont('Arial','',7);
                    $pdf->Cell(20,5,'','BLR',0,'C'); 
                    $pdf->Cell(53,5,$jenis_umk,'BLR',0,'L');
                    $pdf->SetFont('Arial','',9);
                    $pdf->Cell(22,5,number_format($hasil_kerja_1,0,',','.'),'BLR',0,'R');
                    $pdf->ln(5);
                }

                $hasil_upah_dasar = array_sum($upah_dasar);

                if (empty($pengerjaan_rit_weekly->lembur)) {
                    $lembur_1 = 0;
                    $lembur_2 = 0;
                    $hasil_lembur = 0;
                }else{
                    $explode_lembur = explode("|",$pengerjaan_rit_weekly->lembur);
                    $lembur_1 = $explode_lembur[1];
                    $lembur_2 = $explode_lembur[2];
                    $hasil_lembur = $explode_lembur[0];
                }

                $total_jam_lembur = floatval($lembur_1)+floatval($lembur_2);
        
                if (empty($pengerjaan_rit_weekly->tunjangan_kehadiran)) {
                    $tunjangan_kehadiran = 0;
                }else{
                    $tunjangan_kehadiran = $pengerjaan_rit_weekly->tunjangan_kehadiran;
                }

                if (empty($pengerjaan_rit_weekly->tunjangan_kerja)) {
                    $tunjangan_kerja = 0;
                }else{
                    $tunjangan_kerja = $pengerjaan_rit_weekly->tunjangan_kerja;
                }
                // dd($tunjangan_kerja);

                if (empty($pengerjaan_rit_weekly->uang_makan)) {
                    $uang_makan = 0;
                }else{
                    $uang_makan = $pengerjaan_rit_weekly->uang_makan;
                }

                if (empty($pengerjaan_rit_weekly->plus_1)) {
                    $plus_1 = 0;
                    $keterangan_plus_1 = '';
                }else{
                    $explode_plus_1 = explode("|",$pengerjaan_rit_weekly->plus_1);
                    $plus_1 = $explode_plus_1[0];
                    $keterangan_plus_1 = $explode_plus_1[1];
                }

                if (empty($pengerjaan_rit_weekly->plus_2)) {
                    $plus_2 = 0;
                    $keterangan_plus_2 = '';
                }else{
                    $explode_plus_2 = explode("|",$pengerjaan_rit_weekly->plus_2);
                    $plus_2 = $explode_plus_2[0];
                    $keterangan_plus_2 = $explode_plus_2[1];
                }

                if (empty($pengerjaan_rit_weekly->plus_3)) {
                    $plus_3 = 0;
                    $keterangan_plus_3 = '';
                }else{
                    $explode_plus_3 = explode("|",$pengerjaan_rit_weekly->plus_3);
                    $plus_3 = $explode_plus_3[0];
                    $keterangan_plus_3 = $explode_plus_3[1];
                }

                $total_gaji = $hasil_upah_dasar+$plus_1+$plus_2+$plus_3+$uang_makan+$hasil_lembur+$tunjangan_kerja+$tunjangan_kehadiran;

                if (empty($pengerjaan_rit_weekly->minus_1)) {
                    $minus_1 = 0;
                    $keterangan_minus_1 = '';
                }else{
                    $explode_minus_1 = explode("|",$pengerjaan_rit_weekly->minus_1);
                    $minus_1 = $explode_minus_1[0];
                    $keterangan_minus_1 = $explode_minus_1[1];
                }

                if (empty($pengerjaan_rit_weekly->minus_2)) {
                    $minus_2 = 0;
                    $keterangan_minus_2 = '';
                }else{
                    $explode_minus_2 = explode("|",$pengerjaan_rit_weekly->minus_2);
                    $minus_2 = $explode_minus_2[0];
                    $keterangan_minus_2 = $explode_minus_2[1];
                }

                if (empty($pengerjaan_rit_weekly->jht)) {
                    $jht = 0;
                }else{
                    $jht = $pengerjaan_rit_weekly->jht;
                }

                if (empty($pengerjaan_rit_weekly->bpjs_kesehatan)) {
                    $bpjs_kesehatan = 0;
                }else{
                    $bpjs_kesehatan = $pengerjaan_rit_weekly->bpjs_kesehatan;
                }

                if (empty($pengerjaan_rit_weekly->pensiun)) {
                    $pensiun = 0;
                }else{
                    $pensiun = $pengerjaan_rit_weekly->pensiun;
                }

                $total_upah_diterima = $total_gaji-$minus_1-$minus_2-$jht-$bpjs_kesehatan-$pensiun;

                ### LINE 6 ###
                $pdf->SetFont('Arial','',9);
                $pdf->Cell(35,5,"Lembur ".'( '.$total_jam_lembur.' Jam )','L',0,'L'); 
                $pdf->Cell(22,5,'','',0,'L');
                $pdf->Cell(3,5,'Rp','',0,'L');
                $pdf->Cell(35,5,number_format($hasil_lembur,0,',','.'),'R',0,'R');
                $pdf->ln(4);
        
                ### LINE 5 ###
                $pdf->SetFont('Arial','',9);
                $pdf->Cell(35,5,'Insentif Kehadiran','L',0,'L'); 
                $pdf->Cell(22,5,'','',0,'L');
                $pdf->Cell(3,5,'Rp','',0,'L');
                $pdf->Cell(35,5,number_format($tunjangan_kehadiran,0,',','.'),'R',0,'R');
                $pdf->ln(4);
        
                ### LINE 6 ###
                $pdf->SetFont('Arial','',9);
                $pdf->Cell(35,5,'Tunjangan Kerja','L',0,'L'); 
                $pdf->Cell(22,5,'','',0,'L');
                $pdf->Cell(3,5,'Rp','',0,'L');
                $pdf->Cell(35,5,number_format($tunjangan_kerja,0,',','.'),'R',0,'R');
                $pdf->ln(4);
        
                ### LINE 7 ###
                $pdf->SetFont('Arial','',9);
                $pdf->Cell(12,5,'Plus','L',0,'L'); 
                $pdf->Cell(45,5,"( ".$keterangan_plus_1." )",'',0,'L');
                $pdf->Cell(3,5,'Rp','',0,'L');
                $pdf->Cell(35,5,number_format($plus_1,0,',','.'),'R',0,'R');
                $pdf->ln(4);
        
                $pdf->SetFont('Arial','',9);
                $pdf->Cell(12,5,'','L',0,'L'); 
                $pdf->Cell(45,5,"( ".$keterangan_plus_2." )",'',0,'L');
                $pdf->Cell(3,5,'Rp','',0,'L');
                $pdf->Cell(35,5,number_format($plus_2,0,',','.'),'R',0,'R');
                $pdf->ln(4);
        
                $pdf->SetFont('Arial','',9);
                $pdf->Cell(12,5,'','L',0,'L'); 
                $pdf->Cell(45,5,"( ".$keterangan_plus_3." )",'',0,'L');
                $pdf->Cell(3,5,'Rp','',0,'L');
                $pdf->Cell(35,5,number_format($plus_3,0,',','.'),'R',0,'R');
                $pdf->ln(4);
        
                $pdf->SetFont('Arial','',9);
                $pdf->Cell(35,5,'Uang Makan','L',0,'L'); 
                $pdf->Cell(22,5,'','',0,'L');
                $pdf->Cell(3,5,'Rp','',0,'L');
                $pdf->Cell(35,5,number_format($uang_makan,0,',','.'),'R',0,'R');
                $pdf->ln(5);
        
                $pdf->SetFont('Arial','B',9);
                $pdf->Cell(20,5,'POTONGAN','L',0,'L'); 
                $pdf->Cell(37,5,'BPJS > JHT + JP','',0,'L');
                $pdf->Cell(3,5,'Rp','',0,'L');
                $pdf->Cell(35,5,number_format($jht+$pensiun,0,',','.'),'R',0,'R');
                $pdf->ln(4);
        
                $pdf->SetFont('Arial','B',9);
                $pdf->Cell(20,5,'','L',0,'L'); 
                $pdf->Cell(37,5,'BPJS Kesehatan','',0,'L');
                $pdf->Cell(3,5,'Rp','',0,'L');
                $pdf->Cell(35,5,number_format($bpjs_kesehatan,0,',','.'),'R',0,'R');
                $pdf->ln(4);
        
                $pdf->SetFont('Arial','B',9);
                $pdf->Cell(20,5,'','L',0,'L'); 
                $pdf->Cell(37,5,"( ".$keterangan_minus_1." )",'',0,'L');
                $pdf->Cell(3,5,'Rp','',0,'L');
                $pdf->Cell(35,5,number_format($minus_1,0,',','.'),'R',0,'R');
                $pdf->ln(4);
        
                $pdf->SetFont('Arial','B',9);
                $pdf->Cell(20,5,'','L',0,'L'); 
                $pdf->Cell(37,5,"( ".$keterangan_minus_2." )",'',0,'L');
                $pdf->Cell(3,5,'Rp','',0,'L');
                $pdf->Cell(35,5,number_format($minus_2,0,',','.'),'R',0,'R');
                $pdf->ln(5);
        
                $pdf->SetFont('Arial','B',9);
                $pdf->Cell(35,5,'TOTAL DITERIMA','BL',0,'L'); 
                $pdf->Cell(22,5,'','B',0,'L');
                $pdf->Cell(3,5,'Rp','B',0,'L');
                $pdf->Cell(35,5,number_format($total_upah_diterima,0,',','.'),'BR',0,'R');
                $pdf->ln(3);
        
                $no=$no+1;
                if ($no%2==0)
                {
                    $page=round($no/2);
                    $pdf->ln(1);
                    $pdf->SetFont('Arial','',7);
                    $pdf->Cell(35,5,"- page $page -",'',0,''); 
                    $pdf->ln(12);
                }
                $pdf->ln(8);
            }else{
                $x = 105.5;
                ### LINE 1 ###
                $pdf->SetX($x);
                $pdf->SetFont('Arial','B',9);
                $pdf->Cell(95,5,'GAJI RIT-RITAN '.strtoupper($pengerjaan_rit_weekly->nama),'LTR',0,'C'); 
                $pdf->ln(4);
        
                ### LINE 2 ###
                $pdf->SetX($x);
                $pdf->SetFont('Arial','B',9);
                $pdf->Cell(95,5,'Tanggal: '.Carbon::parse($exp_tgl_awal[0] . '-' . $exp_tgl_awal[1] . '-' . $exp_tgl_awal[2])->isoFormat('D MMMM').' s/d '.Carbon::parse($exp_tgl_akhir[0] . '-' . $exp_tgl_akhir[1] . '-' . $exp_tgl_akhir[2])->isoFormat('D MMMM YYYY'),'LRB',0,'C');
                $pdf->ln(5);
        
                ### LINE 3 ###
                $pdf->SetX($x);
                $pdf->SetFont('Arial','B',9);
                $pdf->Cell(20,5,'TGL','LB',0,'C'); 
                $pdf->Cell(53,5,'KETERANGAN','LRB',0,'C');
                $pdf->Cell(22,5,'Rp','BR',0,'C');
                $pdf->ln(5);
    
                $upah_dasar = array();
                foreach ($pengerjaan_rit_dailys as $prd => $pengerjaan_rit_daily) {
                    if (empty($pengerjaan_rit_daily->hasil_kerja_1)) {
                        // $jenis_umk = 0;
                        $hasil_kerja_1 = 0;
                        $hasil_umk_rit = 0;
                        $tarif_umk = 0;
                        $dpb = 0;
                        $jenis_umk = '-';
                    }else{
                        $explode_hasil_kerja_1 = explode("|",$pengerjaan_rit_daily->hasil_kerja_1);
                        $umk_rit = RitUMK::where('id',$explode_hasil_kerja_1[0])->first();
                        // dd($umk_rit->rit_tujuan);
                        if (empty($umk_rit)) {
                            $hasil_kerja_1 = 0;
                            $hasil_umk_rit = 0;
                            $tarif_umk = 0;
                            $dpb = 0;
                            $jenis_umk = '-';
                        }else{
                            $hasil_kerja_1 = $umk_rit->tarif;
                            $hasil_umk_rit = $umk_rit->kategori_upah;
                            $tarif_umk = $umk_rit->tarif;
                            $dpb = $pengerjaan_rit_daily->dpb/7*$pengerjaan_rit_daily->upah_dasar;
                            if (empty($umk_rit->rit_tujuan)) {
                                $jenis_umk = '-';
                            }else{
                                $jenis_umk = $umk_rit->rit_tujuan->tujuan.' - '.$umk_rit->rit_kendaraan->jenis_kendaraan;
                            }
                        }
                        $total_upah_dasar = $tarif_umk+$dpb;
                        array_push($upah_dasar,$total_upah_dasar);
                    }
                    ### LINE 4 ###
                    $pdf->SetX($x);
                    $pdf->SetFont('Arial','',9);
                    $pdf->Cell(20,5,Carbon::parse($pengerjaan_rit_daily->tanggal_pengerjaan)->isoFormat('D MMMM'),'L',0,'C'); 
                    $pdf->Cell(53,5,'DPB','LR',0,'L');
                    $pdf->Cell(22,5,number_format($dpb,0,',','.'),'R',0,'R');
                    $pdf->ln(4);
            
                    ### LINE 5 ###
                    $pdf->SetX($x);
                    $pdf->SetFont('Arial','',7);
                    $pdf->Cell(20,5,'','BLR',0,'C'); 
                    $pdf->Cell(53,5,$jenis_umk,'BLR',0,'L');
                    $pdf->SetFont('Arial','',9);
                    $pdf->Cell(22,5,number_format($hasil_kerja_1,0,',','.'),'BLR',0,'R');
                    $pdf->ln(5);
                }
    
                $hasil_upah_dasar = array_sum($upah_dasar);
    
                if (empty($pengerjaan_rit_weekly->lembur)) {
                    $lembur_1 = 0;
                    $lembur_2 = 0;
                    $hasil_lembur = 0;
                }else{
                    $explode_lembur = explode("|",$pengerjaan_rit_weekly->lembur);
                    $lembur_1 = $explode_lembur[1];
                    $lembur_2 = $explode_lembur[2];
                    $hasil_lembur = $explode_lembur[0];
                }
    
                $total_jam_lembur = floatval($lembur_1)+floatval($lembur_2);
        
                if (empty($pengerjaan_rit_weekly->tunjangan_kehadiran)) {
                    $tunjangan_kehadiran = 0;
                }else{
                    $tunjangan_kehadiran = $pengerjaan_rit_weekly->tunjangan_kehadiran;
                }
    
                if (empty($pengerjaan_rit_weekly->tunjangan_kerja)) {
                    $tunjangan_kerja = 0;
                }else{
                    $tunjangan_kerja = $pengerjaan_rit_weekly->tunjangan_kerja;
                }
                // dd($tunjangan_kerja);
    
                if (empty($pengerjaan_rit_weekly->uang_makan)) {
                    $uang_makan = 0;
                }else{
                    $uang_makan = $pengerjaan_rit_weekly->uang_makan;
                }
    
                if (empty($pengerjaan_rit_weekly->plus_1)) {
                    $plus_1 = 0;
                    $keterangan_plus_1 = '';
                }else{
                    $explode_plus_1 = explode("|",$pengerjaan_rit_weekly->plus_1);
                    $plus_1 = $explode_plus_1[0];
                    $keterangan_plus_1 = $explode_plus_1[1];
                }
    
                if (empty($pengerjaan_rit_weekly->plus_2)) {
                    $plus_2 = 0;
                    $keterangan_plus_2 = '';
                }else{
                    $explode_plus_2 = explode("|",$pengerjaan_rit_weekly->plus_2);
                    $plus_2 = $explode_plus_2[0];
                    $keterangan_plus_2 = $explode_plus_2[1];
                }
    
                if (empty($pengerjaan_rit_weekly->plus_3)) {
                    $plus_3 = 0;
                    $keterangan_plus_3 = '';
                }else{
                    $explode_plus_3 = explode("|",$pengerjaan_rit_weekly->plus_3);
                    $plus_3 = $explode_plus_3[0];
                    $keterangan_plus_3 = $explode_plus_3[1];
                }
    
                $total_gaji = $hasil_upah_dasar+$plus_1+$plus_2+$plus_3+$uang_makan+$hasil_lembur+$tunjangan_kerja+$tunjangan_kehadiran;
    
                if (empty($pengerjaan_rit_weekly->minus_1)) {
                    $minus_1 = 0;
                    $keterangan_minus_1 = '';
                }else{
                    $explode_minus_1 = explode("|",$pengerjaan_rit_weekly->minus_1);
                    $minus_1 = $explode_minus_1[0];
                    $keterangan_minus_1 = $explode_minus_1[1];
                }
    
                if (empty($pengerjaan_rit_weekly->minus_2)) {
                    $minus_2 = 0;
                    $keterangan_minus_2 = '';
                }else{
                    $explode_minus_2 = explode("|",$pengerjaan_rit_weekly->minus_2);
                    $minus_2 = $explode_minus_2[0];
                    $keterangan_minus_2 = $explode_minus_2[1];
                }
    
                if (empty($pengerjaan_rit_weekly->jht)) {
                    $jht = 0;
                }else{
                    $jht = $pengerjaan_rit_weekly->jht;
                }
    
                if (empty($pengerjaan_rit_weekly->bpjs_kesehatan)) {
                    $bpjs_kesehatan = 0;
                }else{
                    $bpjs_kesehatan = $pengerjaan_rit_weekly->bpjs_kesehatan;
                }
    
                if (empty($pengerjaan_rit_weekly->pensiun)) {
                    $pensiun = 0;
                }else{
                    $pensiun = $pengerjaan_rit_weekly->pensiun;
                }
    
                $total_upah_diterima = $total_gaji-$minus_1-$minus_2-$jht-$bpjs_kesehatan-$pensiun;
    
                ### LINE 6 ###
                $pdf->SetX($x);
                $pdf->SetFont('Arial','',9);
                $pdf->Cell(35,5,"Lembur ".'( '.$total_jam_lembur.' Jam )','L',0,'L'); 
                $pdf->Cell(22,5,'','',0,'L');
                $pdf->Cell(3,5,'Rp','',0,'L');
                $pdf->Cell(35,5,number_format($hasil_lembur,0,',','.'),'R',0,'R');
                $pdf->ln(4);
        
                ### LINE 7 ###
                $pdf->SetX($x);
                $pdf->SetFont('Arial','',9);
                $pdf->Cell(35,5,'Insentif Kehadiran','L',0,'L'); 
                $pdf->Cell(22,5,'','',0,'L');
                $pdf->Cell(3,5,'Rp','',0,'L');
                $pdf->Cell(35,5,number_format($tunjangan_kehadiran,0,',','.'),'R',0,'R');
                $pdf->ln(4);
        
                ### LINE 8 ###
                $pdf->SetX($x);
                $pdf->SetFont('Arial','',9);
                $pdf->Cell(35,5,'Tunjangan Kerja','L',0,'L'); 
                $pdf->Cell(22,5,'','',0,'L');
                $pdf->Cell(3,5,'Rp','',0,'L');
                $pdf->Cell(35,5,number_format($tunjangan_kerja,0,',','.'),'R',0,'R');
                $pdf->ln(4);
        
                ### LINE 9 ###
                $pdf->SetX($x);
                $pdf->SetFont('Arial','',9);
                $pdf->Cell(12,5,'Plus','L',0,'L'); 
                $pdf->Cell(45,5,"( ".$keterangan_plus_1." )",'',0,'L');
                $pdf->Cell(3,5,'Rp','',0,'L');
                $pdf->Cell(35,5,number_format($plus_1,0,',','.'),'R',0,'R');
                $pdf->ln(4);
        
                $pdf->SetX($x);
                $pdf->SetFont('Arial','',9);
                $pdf->Cell(12,5,'','L',0,'L'); 
                $pdf->Cell(45,5,"( ".$keterangan_plus_2." )",'',0,'L');
                $pdf->Cell(3,5,'Rp','',0,'L');
                $pdf->Cell(35,5,number_format($plus_2,0,',','.'),'R',0,'R');
                $pdf->ln(4);
        
                $pdf->SetX($x);
                $pdf->SetFont('Arial','',9);
                $pdf->Cell(12,5,'','L',0,'L'); 
                $pdf->Cell(45,5,"( ".$keterangan_plus_3." )",'',0,'L');
                $pdf->Cell(3,5,'Rp','',0,'L');
                $pdf->Cell(35,5,number_format($plus_3,0,',','.'),'R',0,'R');
                $pdf->ln(4);
        
                $pdf->SetX($x);
                $pdf->SetFont('Arial','',9);
                $pdf->Cell(35,5,'Uang Makan','L',0,'L'); 
                $pdf->Cell(22,5,'','',0,'L');
                $pdf->Cell(3,5,'Rp','',0,'L');
                $pdf->Cell(35,5,number_format($uang_makan,0,',','.'),'R',0,'R');
                $pdf->ln(5);
        
                $pdf->SetX($x);
                $pdf->SetFont('Arial','B',9);
                $pdf->Cell(20,5,'POTONGAN','L',0,'L'); 
                $pdf->Cell(37,5,'BPJS > JHT + JP','',0,'L');
                $pdf->Cell(3,5,'Rp','',0,'L');
                $pdf->Cell(35,5,number_format($jht+$pensiun,0,',','.'),'R',0,'R');
                $pdf->ln(4);
        
                $pdf->SetX($x);
                $pdf->SetFont('Arial','B',9);
                $pdf->Cell(20,5,'','L',0,'L'); 
                $pdf->Cell(37,5,'BPJS Kesehatan','',0,'L');
                $pdf->Cell(3,5,'Rp','',0,'L');
                $pdf->Cell(35,5,number_format($bpjs_kesehatan,0,',','.'),'R',0,'R');
                $pdf->ln(4);
        
                $pdf->SetX($x);
                $pdf->SetFont('Arial','B',9);
                $pdf->Cell(20,5,'','L',0,'L'); 
                $pdf->Cell(37,5,"( ".$keterangan_minus_1." )",'',0,'L');
                $pdf->Cell(3,5,'Rp','',0,'L');
                $pdf->Cell(35,5,number_format($minus_1,0,',','.'),'R',0,'R');
                $pdf->ln(4);
        
                $pdf->SetX($x);
                $pdf->SetFont('Arial','B',9);
                $pdf->Cell(20,5,'','L',0,'L'); 
                $pdf->Cell(37,5,"( ".$keterangan_minus_2." )",'',0,'L');
                $pdf->Cell(3,5,'Rp','',0,'L');
                $pdf->Cell(35,5,number_format($minus_2,0,',','.'),'R',0,'R');
                $pdf->ln(5);
        
                $pdf->SetX($x);
                $pdf->SetFont('Arial','B',9);
                $pdf->Cell(35,5,'TOTAL DITERIMA','BL',0,'L'); 
                $pdf->Cell(22,5,'','B',0,'L');
                $pdf->Cell(3,5,'Rp','B',0,'L');
                $pdf->Cell(35,5,number_format($total_upah_diterima,0,',','.'),'BR',0,'R');
                $pdf->ln(3);
        
                $no=$no+1;
                if ($no%2==0)
                {
                    $page=round($no/2);
                    $pdf->ln(1);
                    $pdf->SetFont('Arial','',7);
                    $pdf->Cell(35,5,"- page $page -",'',0,''); 
                    $pdf->ln(12);
                }
                $pdf->ln(8);
            }
        }

        $pdf->Output('Slip Gaji Supir RIT '.Carbon::parse($exp_tgl_awal[0] . '-' . $exp_tgl_awal[1] . '-' . $exp_tgl_awal[2])->isoFormat('D MMMM').' sd '.Carbon::parse($exp_tgl_akhir[0] . '-' . $exp_tgl_akhir[1] . '-' . $exp_tgl_akhir[2])->isoFormat('D MMMM YYYY').'.pdf', "I");
        exit;
        // return view('backend.payrol.penggajian.supir_rit.bank');
    }

    public function supir_rit_bank($kode_pengerjaan)
    {
        $data['kode_pengerjaan'] = $kode_pengerjaan;
        $data['new_data_pengerjaan'] = NewDataPengerjaan::where('kode_pengerjaan',$kode_pengerjaan)->first();
        $data['pengerjaan_supir_rits'] = PengerjaanRITWeekly::select([
                                                            'pengerjaan_supir_rit_weekly.id as id',
                                                            'pengerjaan_supir_rit_weekly.karyawan_supir_rit_id as karyawan_supir_rit_id',
                                                            'operator_supir_rit_karyawan.nik as nik',
                                                            'biodata_karyawan.nama as nama',
                                                            'biodata_karyawan.rekening as rekening',
                                                            'pengerjaan_supir_rit_weekly.total_hasil as total_hasil',
                                                        ])
                                                        ->leftJoin('operator_supir_rit_karyawan','operator_supir_rit_karyawan.id','=','pengerjaan_supir_rit_weekly.karyawan_supir_rit_id')
                                                        ->leftJoin('itic_emp.biodata_karyawan','biodata_karyawan.nik','=','operator_supir_rit_karyawan.nik')
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
        $data['new_data_pengerjaan'] = NewDataPengerjaan::where('kode_pengerjaan',$kode_pengerjaan)->first();

        $data['rit_posisis'] = RitPosisi::all();
        // return view('backend.payrol.penggajian.supir_rit.weekly_report',$data);
        $pdf = Pdf::loadView('backend.payrol.penggajian.supir_rit.weekly_report',$data);
        $pdf->setPaper('a4','landscape')->setWarnings(false);
        return $pdf->stream();
    }
}
