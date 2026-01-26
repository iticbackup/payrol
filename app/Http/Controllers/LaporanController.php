<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;

use App\Exports\BoronganMultiSheets;
use App\Exports\LaporanBoronganLokalExport;
use App\Exports\LaporanHarianExport;
use App\Exports\LaporanSupirRitExport;
use App\Exports\RekapLaporanPayrolExport;

use App\Models\NewDataPengerjaan;
use App\Models\JenisOperator;
use App\Models\JenisOperatorDetail;
use App\Models\JenisOperatorDetailPengerjaan;
use App\Models\PengerjaanWeekly;
use App\Models\PengerjaanHarian;
use App\Models\PengerjaanRITHarian;
use App\Models\PengerjaanRITWeekly;

use App\Models\CutOff;

use \Carbon\Carbon;
use Validator;
use DataTables;
use Excel;

class LaporanController extends Controller
{

    function __construct(
        CutOff $cutOff
    )
    {
        $this->cutOff = $cutOff;
    }

    public function laporan(Request $request)
    {
        if ($request->ajax()) {
            $data = NewDataPengerjaan::select('date','tanggal','status')->groupBy('date','tanggal','status')->get();
            return DataTables::of($data)
                            ->addIndexColumn()
                            ->addColumn('tanggal_dibuat', function($row){
                                return $row->date;
                            })
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
                                $new_data_pengerjaans = NewDataPengerjaan::where('date',$row->date)->orderBy('kode_pengerjaan','asc')->get();
                                $btn = '';
                                $btn .= '<div>';
                                foreach ($new_data_pengerjaans as $key => $new_data_pengerjaan) {
                                    $explode_kode_pengerjaan = explode('_',$new_data_pengerjaan->kode_pengerjaan);
                                    $jenis_operator = JenisOperator::select('id','kode_operator')->where('kode_operator',$explode_kode_pengerjaan[0])->first();
                                    switch ($explode_kode_pengerjaan[0]) {
                                        case 'PB':
                                            $btn .= '<a href='.route('laporan.download',['id_jenis_operator' => $jenis_operator->id, 'kode_pengerjaan' => $new_data_pengerjaan->kode_pengerjaan]).' class="btn btn-outline-primary">Download Laporan Borongan '.$explode_kode_pengerjaan[1].'_'.$explode_kode_pengerjaan[2].'</a>';
                                            break;
                                        case 'PH':
                                            $btn .= '<a href='.route('laporan.download',['id_jenis_operator' => $jenis_operator->id, 'kode_pengerjaan' => $new_data_pengerjaan->kode_pengerjaan]).' class="btn btn-outline-primary">Download Laporan Harian '.$explode_kode_pengerjaan[1].'_'.$explode_kode_pengerjaan[2].'</a>';
                                            break;
                                        case 'PS':
                                            $btn .= '<a href='.route('laporan.download',['id_jenis_operator' => $jenis_operator->id, 'kode_pengerjaan' => $new_data_pengerjaan->kode_pengerjaan]).' class="btn btn-outline-primary">Download Laporan Supir RIT '.$explode_kode_pengerjaan[1].'_'.$explode_kode_pengerjaan[2].'</a>';
                                            break;

                                        default:
                                            # code...
                                            break;
                                    }
                                }
                                $btn .= '</div>';
                                // $explode_jenis_operator = explode('_',$row->kode_pengerjaan);
                                // $jenis_operator = JenisOperator::where('kode_operator',$explode_jenis_operator[0])->first();
                                // $jenis_operator_details = JenisOperatorDetail::where('jenis_operator_id',$jenis_operator->id)->where('status','y')->get();

                                // $btn = '';
                                // $btn .= '<div>';
                                // $btn .=     '<a href='.$row->kode_pengerjaan.' class="btn btn-outline-success">Download Laporan</a>';
                                // $btn .= '</div>';
                                return $btn;
                            })
                            ->rawColumns(['action','status'])
                            ->make(true);
        }

        return view('backend.payrol.penggajian.index');
    }

    public function laporan_excel($id_jenis_operator,$kode_pengerjaan)
    {
        // dd($id_jenis_operator,$kode_pengerjaan);
        if ($id_jenis_operator == 1) {
            $data['kode_pengerjaan'] = $kode_pengerjaan;
            $data['new_data_pengerjaan'] = NewDataPengerjaan::where('kode_pengerjaan',$kode_pengerjaan)->first();
            $data['explode_tanggal_pengerjaans'] = explode('#',$data['new_data_pengerjaan']['tanggal']);

            $exp_tanggals = array_filter($data['explode_tanggal_pengerjaans']);
            $a = count($exp_tanggals);
            $exp_tgl_awal = explode('-', $exp_tanggals[1]);
            $exp_tgl_akhir = explode('-', $exp_tanggals[$a]);

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
                                                                    ->where('pengerjaan_weekly.kode_pengerjaan',$kode_pengerjaan)
                                                                    ->where('operator_karyawan.jenis_operator_id',$id_jenis_operator)
                                                                    ->where('operator_karyawan.status','Y')
                                                                    ->orderBy('biodata_karyawan.nama','asc')
                                                                    ->get();
            // return view('backend.laporan.borongan.excel_lp_borongan',$data);
            return Excel::download(new RekapLaporanPayrolExport(1), 'Laporan Payrol.xlsx');
            // return Excel::download(new RekapLaporanPayrolExport(), 'Laporan Payrol.xlsx');
        }
    }

    public function laporan_borongan_index(Request $request)
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
                                $explode_jenis_operator = explode('_',$row->kode_pengerjaan);
                                $jenis_operator = JenisOperator::where('kode_operator',$explode_jenis_operator[0])->first();
                                $jenis_operator_details = JenisOperatorDetail::where('jenis_operator_id',$jenis_operator->id)->where('status','y')->get();

                                $btn_jenis_operator = '';
                                // $btn_jenis_operator = $btn_jenis_operator.='<div class="btn-group">';
                                // $no = 1;
                                // $btn_jenis_operator = $btn_jenis_operator.='<div class="accordion accordion-flush" id="accordionFlushExample">';
                                // foreach ($jenis_operator_details as $key => $jenis_operator_detail) {
                                //     if ($jenis_operator_detail->jenis_operator_id == 1) {
                                //         $jenis_operator_detail_pengerjaans = JenisOperatorDetailPengerjaan::where('jenis_operator_detail_id',$jenis_operator_detail->id)->get();
                                //         $btn_jenis_operator.= '<div class="accordion-item">';
                                //         $btn_jenis_operator.= '<h5 class="accordion-header m-0" id="flush-headingOne-'.$no.'">';
                                //         $btn_jenis_operator.= '<button class="accordion-button fw-semibold collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#flush-collapseOne-'.$no.'" aria-expanded="false" aria-controls="flush-collapseOne-'.$no.'">'.'Download Laporan '.$jenis_operator_detail->jenis_posisi.'</button>';
                                //         $btn_jenis_operator.= '</h5>';
                                //         $btn_jenis_operator.= '<div id="flush-collapseOne-'.$no.'" class="accordion-collapse collapse" aria-labelledby="flush-headingOne-'.$no.'" data-bs-parent="#accordionFlushExample" style="">';
                                //         $btn_jenis_operator.=   '<div class="accordion-body">';
                                //         $btn_jenis_operator.=       '<div class="btn-group">';
                                //         foreach ($jenis_operator_detail_pengerjaans as $jenis_operator_detail_pengerjaan) {
                                //         // $btn_jenis_operator.=       '<button onclick="download_excel(`'.$jenis_operator_detail_pengerjaan->id.'`,`'.$row->kode_pengerjaan.'`)" class="btn btn-outline-primary">'.'<i class="far fa-file-excel"></i>'.' Download '.$jenis_operator_detail_pengerjaan->jenis_posisi_pekerjaan.'</button>';
                                //         $btn_jenis_operator.=       '<a href='.route('laporan.borongan.export',['id_jenis_pekerjaan' => $jenis_operator_detail_pengerjaan->id,'id' => $jenis_operator_detail_pengerjaan->jenis_operator_detail_id, 'kode_pengerjaan' => $row->kode_pengerjaan]).' target="_blank" class="btn btn-outline-primary">'.'<i class="far fa-file-excel"></i> '.$jenis_operator_detail_pengerjaan->jenis_posisi_pekerjaan.'</a>';
                                //         }
                                //         $btn_jenis_operator.=       '</div>';
                                //         $btn_jenis_operator.=   '</div>';
                                //         $btn_jenis_operator.= '</div>';
                                //         $btn_jenis_operator.= '</div>';
                                //     }
                                //     $no++;
                                // }
                                // $btn_jenis_operator = $btn_jenis_operator.='</div>';

                                foreach ($jenis_operator_details as $key => $jenis_operator_detail) {
                                    if ($jenis_operator_detail->jenis_operator_id == 1) {
                                        $jenis_operator_detail_pengerjaans = JenisOperatorDetailPengerjaan::where('jenis_operator_detail_id',$jenis_operator_detail->id)->get();
                                        $btn_jenis_operator = $btn_jenis_operator.='<div class="card">';
                                        $btn_jenis_operator = $btn_jenis_operator.= '<div class="card-header"><h5 class="card-title">'.'Download Laporan '.$jenis_operator_detail->jenis_posisi.'</h5></div>';
                                        $btn_jenis_operator = $btn_jenis_operator.=     '<div class="card-body">';
                                        $btn_jenis_operator = $btn_jenis_operator.=         '<div class="button-items">';
                                        foreach ($jenis_operator_detail_pengerjaans as $jenis_operator_detail_pengerjaan) {
                                            $btn_jenis_operator.='<a href='.route('laporan.borongan.export',['id_jenis_pekerjaan' => $jenis_operator_detail_pengerjaan->id,'id' => $jenis_operator_detail_pengerjaan->jenis_operator_detail_id, 'kode_pengerjaan' => $row->kode_pengerjaan]).' target="_blank" class="btn btn-outline-primary">'.'<i class="far fa-file-excel"></i> '.$jenis_operator_detail_pengerjaan->jenis_posisi_pekerjaan.'</a>';
                                        }
                                        // $btn_jenis_operator = $btn_jenis_operator.='<div class="button-items">';
                                        // foreach ($jenis_operator_details as $key => $jenis_operator_detail) {
                                        //     if ($jenis_operator_detail->jenis_operator_id == 1) {
                                        //         $jenis_operator_detail_pengerjaans = JenisOperatorDetailPengerjaan::where('jenis_operator_detail_id',$jenis_operator_detail->id)->get();
                                        //         $btn_jenis_operator.='<button type="button" class="btn btn-outline-primary">Primary</button>';
                                        //     }
                                        // }
                                        $btn_jenis_operator = $btn_jenis_operator.=         '</div>';
                                        $btn_jenis_operator = $btn_jenis_operator.=     '</div>';
                                        $btn_jenis_operator = $btn_jenis_operator.='</div>';
                                    }
                                }

                                // $btn_jenis_operator = $btn_jenis_operator.='<div class="button-items">';
                                // foreach ($jenis_operator_details as $key => $jenis_operator_detail) {
                                //     if ($jenis_operator_detail->jenis_operator_id == 1) {
                                //         $jenis_operator_detail_pengerjaans = JenisOperatorDetailPengerjaan::where('jenis_operator_detail_id',$jenis_operator_detail->id)->get();
                                //         $btn_jenis_operator.='<div class="btn-group">';
                                //         $btn_jenis_operator.='<button class="btn btn-primary me-0">'.'Laporan '.$jenis_operator_detail->jenis_posisi.'</button>';
                                //         $btn_jenis_operator.='<a type="button" class="btn btn-outline-primary dropdown-toggle dropdown-toggle-split" data-bs-toggle="dropdown" aria-expanded="false">
                                //                                 <span class="sr-only">Toggle Dropdown</span> <i class="mdi mdi-chevron-down"></i>
                                //                             </a>';
                                //         $btn_jenis_operator.='<div class="dropdown-menu" style="">';
                                //         foreach ($jenis_operator_detail_pengerjaans as $jenis_operator_detail_pengerjaan) {
                                //             $btn_jenis_operator.='<a class="dropdown-item" target="_blank">'.$jenis_operator_detail_pengerjaan->jenis_posisi_pekerjaan.'</a>';
                                //         }
                                //     }
                                //     $btn_jenis_operator.='</div>';
                                //     $btn_jenis_operator.='</div>';
                                // }
                                // $btn_jenis_operator = $btn_jenis_operator.='</div>';
                                // $btn = '<a href='.route('laporan.borongan.export',['kode_pengerjaan' => $row->kode_pengerjaan]).' class="btn btn-primary btn-icon" target="_blank">
                                //             <i class="far fa-file-excel"></i> Laporan Payrol
                                //         </a>
                                //         <button type="button" class="btn btn-warning btn-icon">
                                //             <i class="fa fa-edit"></i>
                                //         </button>
                                //         <button type="button" class="btn btn-danger btn-icon">
                                //             <i class="fa fa-trash"></i>
                                //         </button>';
                                return $btn_jenis_operator;
                            })
                            ->rawColumns(['action','status'])
                            ->make(true);
        }
        return view('backend.laporan.borongan.index');
    }

    public function laporan_borongan_export($id_jenis_pekerjaan,$id, $kode_pengerjaan)
    {
        if($id == 1){
            $kode_jenis_operator_detail = 'L';
        }
        elseif($id == 2){
            $kode_jenis_operator_detail = 'E';
        }
        elseif($id == 3){
            $kode_jenis_operator_detail = 'A';
        }
        $data['kode_id'] = $id;
        $data['kode_pengerjaan'] = $kode_pengerjaan;
        // dd(substr($kode_pengerjaan,0,2).$kode_jenis_operator_detail.'_'.substr($kode_pengerjaan,3));
        $data['new_data_pengerjaan'] = NewDataPengerjaan::where('kode_pengerjaan',$kode_pengerjaan)->first();
        $data['explode_tanggal_pengerjaans'] = explode('#',$data['new_data_pengerjaan']['tanggal']);

        $exp_tanggals = array_filter($data['explode_tanggal_pengerjaans']);
        $a = count($exp_tanggals);
        $exp_tgl_awal = explode('-', $exp_tanggals[1]);
        $exp_tgl_akhir = explode('-', $exp_tanggals[$a]);

        $data['jenis_operator_detail_pengerjaan'] = JenisOperatorDetailPengerjaan::where('id',$id_jenis_pekerjaan)->first();
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
                                            ->where('pengerjaan_weekly.kode_pengerjaan',$kode_pengerjaan)
                                            // ->where('pengerjaan_weekly.kode_payrol',substr($kode_pengerjaan,0,2).$kode_jenis_operator_detail.'_'.substr($kode_pengerjaan,3))
                                            // ->where('biodata_karyawan.status_karyawan','!=','R')
                                            ->where('operator_karyawan.jenis_operator_detail_pekerjaan_id',$id_jenis_pekerjaan)
                                            // ->where('operator_karyawan.status','Y')
                                            ->orderBy('biodata_karyawan.nama','asc')
                                            ->get();
        $baris_akhir = $data['pengerjaan_borongan_weeklys']->count()+10;
        // foreach ($data['jenis_operator_detail_pengerjaans'] as $key => $value) {
        // }

        // return view('backend.laporan.borongan.excel_laporan_borongan',$data);
        return Excel::download(new LaporanBoronganLokalExport($id_jenis_pekerjaan,$id,$kode_pengerjaan,$baris_akhir), 'Laporan Borongan '.$data['jenis_operator_detail_pengerjaan']['jenis_posisi_pekerjaan'].' '.Carbon::parse($exp_tgl_awal[0] . '-' . $exp_tgl_awal[1] . '-' . $exp_tgl_awal[2])->isoFormat('D MMMM').' sd '.Carbon::parse($exp_tgl_akhir[0] . '-' . $exp_tgl_akhir[1] . '-' . $exp_tgl_akhir[2])->isoFormat('D MMMM YYYY').'.xlsx');
        // return Excel::download(new LaporanBoronganLokalExport($id_jenis_pekerjaan,$id,$kode_pengerjaan), 'Laporan Borongan '.$data['jenis_operator_detail_pengerjaan']['jenis_posisi_pekerjaan'].' '.$kode_pengerjaan.'.xlsx');
        // return Excel::download(new BoronganMultiSheets($id,$kode_pengerjaan), 'Laporan Borongan Lokal '.$kode_pengerjaan.'.xlsx');
        // return Excel::download(new LaporanBoronganLokalExport($id,$kode_pengerjaan), 'Laporan Borongan Lokal '.$kode_pengerjaan.'.xlsx');
    }

    public function laporan_harian_index(Request $request)
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
                                $explode_jenis_operator = explode('_',$row->kode_pengerjaan);
                                $jenis_operator = JenisOperator::where('kode_operator',$explode_jenis_operator[0])->first();
                                $jenis_operator_details = JenisOperatorDetail::where('jenis_operator_id',$jenis_operator->id)->where('status','y')->get();

                                $btn_jenis_operator = '';
                                // $btn_jenis_operator = $btn_jenis_operator.='<div class="accordion accordion-flush" id="accordionFlushExample">';
                                // foreach ($jenis_operator_details as $key => $jenis_operator_detail) {
                                //     if ($jenis_operator_detail->jenis_operator_id == 2) {
                                //         $jenis_operator_detail_pengerjaans = JenisOperatorDetailPengerjaan::where('jenis_operator_detail_id',$jenis_operator_detail->id)->get();
                                //         $btn_jenis_operator.= '<div class="accordion-item">';
                                //         $btn_jenis_operator.= '<h5 class="accordion-header m-0" id="flush-headingOne-'.$key.'">';
                                //         $btn_jenis_operator.= '<button class="accordion-button fw-semibold collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#flush-collapseOne-'.$key.'" aria-expanded="false" aria-controls="flush-collapseOne-'.$key.'">'.'Download Laporan '.$jenis_operator_detail->jenis_posisi.'</button>';
                                //         $btn_jenis_operator.= '</h5>';
                                //         $btn_jenis_operator.= '<div id="flush-collapseOne-'.$key.'" class="accordion-collapse collapse" aria-labelledby="flush-headingOne-'.$key.'" data-bs-parent="#accordionFlushExample" style="">';
                                //         $btn_jenis_operator.=   '<div class="accordion-body">';
                                //         $btn_jenis_operator.=       '<div class="btn-group">';
                                //         foreach ($jenis_operator_detail_pengerjaans as $jenis_operator_detail_pengerjaan) {
                                //         // $btn_jenis_operator.=       '<button onclick="download_excel(`'.$jenis_operator_detail_pengerjaan->id.'`,`'.$row->kode_pengerjaan.'`)" class="btn btn-outline-primary">'.'<i class="far fa-file-excel"></i>'.' Download '.$jenis_operator_detail_pengerjaan->jenis_posisi_pekerjaan.'</button>';
                                //         $btn_jenis_operator.=       '<a href='.route('laporan.harian.export',['id_jenis_pekerjaan' => $jenis_operator_detail_pengerjaan->id,'id' => $jenis_operator_detail_pengerjaan->jenis_operator_detail_id, 'kode_pengerjaan' => $row->kode_pengerjaan]).' target="_blank" class="btn btn-outline-primary">'.'<i class="far fa-file-excel"></i> '.$jenis_operator_detail_pengerjaan->jenis_posisi_pekerjaan.'</a>';
                                //         }
                                //         $btn_jenis_operator.=       '</div>';
                                //         $btn_jenis_operator.=   '</div>';
                                //         $btn_jenis_operator.= '</div>';
                                //         $btn_jenis_operator.= '</div>';
                                //     }
                                // }
                                // $btn_jenis_operator = $btn_jenis_operator.='</div>';

                                foreach ($jenis_operator_details as $key => $jenis_operator_detail) {
                                    if ($jenis_operator_detail->jenis_operator_id == 2) {
                                        $jenis_operator_detail_pengerjaans = JenisOperatorDetailPengerjaan::where('jenis_operator_detail_id',$jenis_operator_detail->id)->get();
                                        $btn_jenis_operator = $btn_jenis_operator.='<div class="card">';
                                        $btn_jenis_operator = $btn_jenis_operator.= '<div class="card-header"><h5 class="card-title">'.'Download Laporan '.$jenis_operator_detail->jenis_posisi.'</h5></div>';
                                        $btn_jenis_operator = $btn_jenis_operator.=     '<div class="card-body">';
                                        $btn_jenis_operator = $btn_jenis_operator.=         '<div class="button-items">';
                                        foreach ($jenis_operator_detail_pengerjaans as $jenis_operator_detail_pengerjaan) {
                                            $btn_jenis_operator.='<a href='.route('laporan.harian.export',['id_jenis_pekerjaan' => $jenis_operator_detail_pengerjaan->id,'id' => $jenis_operator_detail_pengerjaan->jenis_operator_detail_id, 'kode_pengerjaan' => $row->kode_pengerjaan]).' target="_blank" class="btn btn-outline-primary">'.'<i class="far fa-file-excel"></i> '.$jenis_operator_detail_pengerjaan->jenis_posisi_pekerjaan.'</a>';
                                        }
                                        // $btn_jenis_operator = $btn_jenis_operator.='<div class="button-items">';
                                        // foreach ($jenis_operator_details as $key => $jenis_operator_detail) {
                                        //     if ($jenis_operator_detail->jenis_operator_id == 1) {
                                        //         $jenis_operator_detail_pengerjaans = JenisOperatorDetailPengerjaan::where('jenis_operator_detail_id',$jenis_operator_detail->id)->get();
                                        //         $btn_jenis_operator.='<button type="button" class="btn btn-outline-primary">Primary</button>';
                                        //     }
                                        // }
                                        $btn_jenis_operator = $btn_jenis_operator.=         '</div>';
                                        $btn_jenis_operator = $btn_jenis_operator.=     '</div>';
                                        $btn_jenis_operator = $btn_jenis_operator.='</div>';
                                    }
                                }
                                return $btn_jenis_operator;
                            })
                            ->rawColumns(['action','status'])
                            ->make(true);
        }
        return view('backend.laporan.harian.index');
    }

    public function laporan_harian_export($id_jenis_pekerjaan, $id, $kode_pengerjaan)
    {
        $data['kode_id'] = $id;
        $data['kode_pengerjaan'] = $kode_pengerjaan;
        $data['new_data_pengerjaan'] = NewDataPengerjaan::where('kode_pengerjaan',$kode_pengerjaan)->first();
        $data['explode_tanggal_pengerjaans'] = explode('#',$data['new_data_pengerjaan']['tanggal']);

        $exp_tanggals = array_filter($data['explode_tanggal_pengerjaans']);
        $a = count($exp_tanggals);
        $exp_tgl_awal = explode('-', $exp_tanggals[1]);
        $exp_tgl_akhir = explode('-', $exp_tanggals[$a]);

        $data['jenis_operator_detail_pengerjaan'] = JenisOperatorDetailPengerjaan::where('id',$id_jenis_pekerjaan)->first();
        $data['pengerjaan_harians'] = PengerjaanHarian::select(
                                                        [
                                                            'pengerjaan_harian.id as id',
                                                            'operator_harian_karyawan.nik as nik',
                                                            'biodata_karyawan.nama as nama',
                                                            'biodata_karyawan.tanggal_masuk as tanggal',
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
                                                    ->where('pengerjaan_harian.kode_pengerjaan',$kode_pengerjaan)
                                                    ->where('operator_harian_karyawan.jenis_operator_detail_pekerjaan_id',$id_jenis_pekerjaan)
                                                    ->orderBy('biodata_karyawan.nama','asc')
                                                    ->get();
        $baris_akhir = $data['pengerjaan_harians']->count()+10;
        // dd($data);
        $data['cut_off'] = $this->cutOff;
        return view('backend.laporan.harian.excel_laporan_harian',$data);
        $document_name= str_replace(array("/", "\\", ":", "*", "?", "«", "<", ">", "|"), "-", 'Laporan Harian '.$data['jenis_operator_detail_pengerjaan']['jenis_posisi_pekerjaan'].' '.Carbon::parse($exp_tgl_awal[0] . '-' . $exp_tgl_awal[1] . '-' . $exp_tgl_awal[2])->isoFormat('D MMMM').' sd '.Carbon::parse($exp_tgl_akhir[0] . '-' . $exp_tgl_akhir[1] . '-' . $exp_tgl_akhir[2])->isoFormat('D MMMM YYYY').'.xlsx');

        return Excel::download(new LaporanHarianExport($id_jenis_pekerjaan,$id,$kode_pengerjaan,$baris_akhir), $document_name);

    }

    public function laporan_supir_rit_index(Request $request)
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
                                $explode_jenis_operator = explode('_',$row->kode_pengerjaan);
                                $jenis_operator = JenisOperator::where('kode_operator',$explode_jenis_operator[0])->first();
                                $jenis_operator_details = JenisOperatorDetail::where('jenis_operator_id',$jenis_operator->id)->where('status','y')->get();

                                $btn_jenis_operator = '';
                                $btn_jenis_operator = $btn_jenis_operator.='<div class="button-items">';
                                foreach ($jenis_operator_details as $key => $jenis_operator_detail) {
                                    $btn_jenis_operator.='<a href='.route('laporan.supir_rit.export',['kode_pengerjaan' => $row->kode_pengerjaan]).' class="btn btn-outline-primary" target="_blank">'.'<i class="far fa-file-excel"></i>'.' Download '.$jenis_operator_detail->jenis_posisi.'</a>';
                                }
                                $btn_jenis_operator = $btn_jenis_operator.='</div>';
                                return $btn_jenis_operator;
                            })
                            ->rawColumns(['action','status'])
                            ->make(true);
        }
        return view('backend.laporan.supir_rit.index');
    }

    public function laporan_supir_rit_export($kode_pengerjaan)
    {
        $data['kode_pengerjaan'] = $kode_pengerjaan;
        $data['new_data_pengerjaan'] = NewDataPengerjaan::where('kode_pengerjaan',$kode_pengerjaan)->first();
        $data['explode_tanggal_pengerjaans'] = explode('#',$data['new_data_pengerjaan']['tanggal']);

        $exp_tanggals = array_filter($data['explode_tanggal_pengerjaans']);
        $a = count($exp_tanggals);
        $exp_tgl_awal = explode('-', $exp_tanggals[1]);
        $exp_tgl_akhir = explode('-', $exp_tanggals[$a]);

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
                                                        ->leftJoin('itic_emp_new.biodata_karyawan','biodata_karyawan.nik','=','operator_supir_rit_karyawan.nik')
                                                        ->where('pengerjaan_supir_rit_weekly.kode_pengerjaan',$kode_pengerjaan)
                                                        ->orderBy('biodata_karyawan.nama','asc')
                                                        ->get();
        $baris_akhir = $data['pengerjaan_supir_rits']->count()+10;
        // return view('backend.laporan.supir_rit.excel_laporan_supir_rit',$data);
        return Excel::download(new LaporanSupirRitExport($kode_pengerjaan,$baris_akhir), 'Laporan Supir RIT '.Carbon::parse($exp_tgl_awal[0] . '-' . $exp_tgl_awal[1] . '-' . $exp_tgl_awal[2])->isoFormat('D MMMM').' sd '.Carbon::parse($exp_tgl_akhir[0] . '-' . $exp_tgl_akhir[1] . '-' . $exp_tgl_akhir[2])->isoFormat('D MMMM YYYY').'.xlsx');
    }
}
