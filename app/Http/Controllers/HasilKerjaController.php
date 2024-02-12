<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Models\NewDataPengerjaan;
use App\Models\JenisOperator;
use App\Models\JenisOperatorDetail;
use App\Models\JenisOperatorDetailPengerjaan;
use \Carbon\Carbon;
use DataTables;

class HasilKerjaController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = NewDataPengerjaan::where('status','n')->get();
            return DataTables::of($data)
                    ->addIndexColumn()
                    ->addColumn('status', function($row){
                        if($row->status == 'y'){
                            return '<span class="badge bg-primary">Berjalan</span>';
                        }elseif($row->status == 'n'){
                            return '<span class="badge bg-success">Selesai</span>';
                        }
                    })
                    ->addColumn('tanggal_pengerjaan', function($row){
                        $explode_tanggal_pengerjaans = explode("#",$row->tanggal);
                        foreach ($explode_tanggal_pengerjaans as $key => $explode_tanggal_pengerjaan) {
                            if ($key != 0) {
                                $hasil_tanggal_pengerjaan[] = Carbon::parse($explode_tanggal_pengerjaan)->isoFormat('D MMMM');
                            }
                        }
                        return $hasil_tanggal_pengerjaan;
                    })
                    ->addColumn('jenis_kerja', function($row){
                        $explode_jenis_operator = explode('_',$row->kode_pengerjaan);
                        $jenis_operator = JenisOperator::where('kode_operator',$explode_jenis_operator[0])->first();
                        $jenis_operator_details = JenisOperatorDetail::where('jenis_operator_id',$jenis_operator->id)->where('status','y')->get();
                        // dd($jenis_operator_details);
                        $btn_jenis_operator = '';
                        // $btn_jenis_operator = $btn_jenis_operator.='<div class="btn-group">';
                        $btn_jenis_operator = $btn_jenis_operator.='<div class="button-items">';
                        foreach ($jenis_operator_details as $key => $jenis_operator_detail) {
                            if ($jenis_operator_detail->jenis_operator_id == 1) {
                                $jenis_operator_detail_pengerjaans = JenisOperatorDetailPengerjaan::where('jenis_operator_detail_id',$jenis_operator_detail->id)->get();
                                $btn_jenis_operator.='<div class="btn-group">';
                                $btn_jenis_operator.='<a href="'.route('pengerjaan.karyawan',['kode_pengerjaan' => $jenis_operator->kode_operator,'id' => $jenis_operator_detail->id, 'kode_payrol' => $row->kode_pengerjaan]).'" class="btn btn-outline-primary me-0">'.'Karyawan '.$jenis_operator_detail->jenis_posisi.'</a>';
                                $btn_jenis_operator.='<a type="button" class="btn btn-outline-primary dropdown-toggle dropdown-toggle-split" data-bs-toggle="dropdown" aria-expanded="false">
                                                        <span class="sr-only">Toggle Dropdown</span> <i class="mdi mdi-chevron-down"></i>
                                                    </a>';
                                $btn_jenis_operator.='<div class="dropdown-menu" style="">';
                                foreach ($jenis_operator_detail_pengerjaans as $jenis_operator_detail_pengerjaan) {
                                    $jenis_operator = $jenis_operator_detail_pengerjaan->jenis_operator_detail->jenis_operator;
                                    // dd($jenis_operator->kode_operator);
                                    // $new_data_pengerjaan = NewDataPengerjaan::where('kode_pengerjaan','LIKE','%'.$jenis_operator->kode_operator.'%')->first();
                                    // dd($new_data_pengerjaan);
                                    $btn_jenis_operator.='<a class="dropdown-item" href='.route($jenis_operator_detail_pengerjaan->link,['id' => $jenis_operator_detail_pengerjaan->jenis_operator_detail_id, 'kode_pengerjaan' => $row->kode_pengerjaan]).' target="_blank">'.$jenis_operator_detail_pengerjaan->jenis_posisi_pekerjaan.'</a>';
                                }
                                                        // <a class="dropdown-item" href="#" target="_blank">Action</a>
                                                        // <a class="dropdown-item" href="#" target="_blank">Another action</a>
                                                        // <a class="dropdown-item" href="#" target="_blank">Something else here</a>
                                                        // <div class="dropdown-divider"></div>
                                                        // <a class="dropdown-item" href="#" target="_blank">Separated link</a>
                                $btn_jenis_operator.='</div>';
    
                                $btn_jenis_operator.='</div>';
                            }elseif($jenis_operator_detail->jenis_operator_id == 2){
                                $jenis_operator_detail_pengerjaans = JenisOperatorDetailPengerjaan::where('jenis_operator_detail_id',4)->get();
                                foreach ($jenis_operator_detail_pengerjaans as $jenis_operator_detail_pengerjaan) {
                                    // $new_data_pengerjaan = NewDataPengerjaan::where('kode_pengerjaan','LIKE','%'.$jenis_operator->kode_operator.'%')->first();
                                    $btn_jenis_operator.='<a href='.route($jenis_operator_detail_pengerjaan->link,['id' => $jenis_operator_detail_pengerjaan->jenis_operator_detail_id, 'kode_pengerjaan' => $row->kode_pengerjaan]).' class="btn btn-outline-primary" target="_blank">'.$jenis_operator_detail_pengerjaan->jenis_posisi_pekerjaan.'</a>';
                                }
                            }elseif($jenis_operator_detail->jenis_operator_id == 3){
                                $btn_jenis_operator.='<a href='.route("hasil_kerja.supir_rit",['kode_pengerjaan' => $row->kode_pengerjaan]).' class="btn btn-outline-primary" target="_blank">'.$jenis_operator_detail->jenis_posisi.'</a>';
                            }
                        }
                        $btn_jenis_operator = $btn_jenis_operator.='</div>';
                        return $btn_jenis_operator;
                    })
                    // ->addColumn('action', function($row){
                    //     $btn = '<button type="button" onclick="edit(`'.$row->id.'`)" class="btn btn-warning btn-icon">
                    //                 <i class="fa fa-edit"></i>
                    //             </button>
                    //             <button type="button" onclick="hapus(`'.$row->id.'`)" class="btn btn-danger btn-icon">
                    //                 <i class="fa fa-trash"></i>
                    //             </button>';
                    //     return $btn;
                    // })
                    ->rawColumns(['status','jenis_kerja'])
                    ->make(true);
        }
        return view('backend.hasil_kerja.index');
    }
}
