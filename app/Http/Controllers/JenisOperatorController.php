<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;

use App\Models\JenisOperator;
use App\Models\JenisOperatorDetail;
use App\Models\JenisOperatorDetailPengerjaan;

use \Carbon\Carbon;
use Validator;
use DataTables;
class JenisOperatorController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = JenisOperator::all();
            return DataTables::of($data)
                    ->addIndexColumn()
                    ->addColumn('status', function($row){
                        if($row->status == 'Y'){
                            return '<span class="text-success">Aktif</span>';
                        }else{
                            return '<span class="text-secondary">Tidak Aktif</span>';
                        }
                    })
                    // ->addColumn('updated_at', function($row){
                    //     return Carbon::parse($row->updated_at)->isoFormat('LLLL');
                    // })
                    ->addColumn('action', function($row){
                        $btn = '<a href='.route('jenis_operator.detail',['id' => $row->id]).' class="btn btn-primary btn-icon">
                                    <i class="fa fa-eye"></i> Detail Jenis Operator
                                </a>
                                <button type="button" onclick="edit(`'.$row->id.'`)" class="btn btn-warning btn-icon">
                                    <i class="fa fa-edit"></i>
                                </button>
                                <button type="button" onclick="hapus(`'.$row->id.'`)" class="btn btn-danger btn-icon">
                                    <i class="fa fa-trash"></i>
                                </button>';
                        return $btn;
                    })
                    ->rawColumns(['action','status'])
                    ->make(true);
        }
        return view('backend.jenis_payrol.index');
    }

    public function simpan(Request $request)
    {
        $rules = [
            'kode_operator' => 'required',
            'jenis_operator' => 'required',
        ];

        $messages = [
            'kode_operator.required'  => 'Kode Operator wajib diisi.',
            'jenis_operator.required'  => 'Jenis Operator wajib diisi.',
        ];

        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->passes()) {
            $input = $request->all();
            $input['status'] = 'Y';
            $jenis_operator = JenisOperator::create($input);

            if($jenis_operator){
                $message_title="Berhasil !";
                $message_content="Kode Operator ".$input['kode_operator']." Berhasil Dibuat";
                $message_type="success";
                $message_succes = true;
            }

            $array_message = array(
                'success' => $message_succes,
                'message_title' => $message_title,
                'message_content' => $message_content,
                'message_type' => $message_type,
            );
            return response()->json($array_message);
        }

        return response()->json(
            [
                'success' => false,
                'error' => $validator->errors()->all()
            ]
        );
    }

    public function detail(Request $request, $id)
    {
        $data['jenis_operator'] = JenisOperator::find($id);
        if(empty($data['jenis_operator'])){
            return redirect()->back();
        }
        if ($request->ajax()) {
            $data = JenisOperatorDetail::where('jenis_operator_id',$id)->get();
            return DataTables::of($data)
                    ->addIndexColumn()
                    // ->addColumn('created_at', function($row){
                    //     return Carbon::parse($row->created_at)->isoFormat('LLLL');
                    // })
                    // ->addColumn('updated_at', function($row){
                    //     return Carbon::parse($row->updated_at)->isoFormat('LLLL');
                    // })
                    ->addColumn('status', function($row){
                        if($row->status == 'Y'){
                            return '<span class="text-success">Aktif</span>';
                        }else{
                            return '<span class="text-secondary">Tidak Aktif</span>';
                        }
                    })
                    ->addColumn('action', function($row){
                        $btn = '<a href='.route('jenis_operator.detail.pengerjaan',['id' => $row->jenis_operator_id, 'id_pengerjaan' => $row->id]).' class="btn btn-primary btn-icon">
                                    <i class="fa fa-edit"></i> Jenis Pekerjaan
                                </a>
                                <button type="button" onclick="edit(`'.$row->id.'`)" class="btn btn-warning btn-icon">
                                    <i class="fa fa-edit"></i>
                                </button>
                                <button type="button" onclick="hapus(`'.$row->id.'`)" class="btn btn-danger btn-icon">
                                    <i class="fa fa-trash"></i>
                                </button>';
                        return $btn;
                    })
                    ->rawColumns(['action','status'])
                    ->make(true);
        }
        return view('backend.jenis_payrol.detail.index',$data);
    }

    public function detail_simpan(Request $request, $id)
    {
        $rules = [
            'jenis_posisi' => 'required',
        ];

        $messages = [
            'jenis_posisi.required'  => 'Jenis Posisi wajib diisi.',
        ];

        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->passes()) {
            $input = $request->all();
            $input['jenis_operator_id'] = $id;
            $input['status'] = 'Y';
            $jenis_operator_detail = JenisOperatorDetail::create($input);

            if($jenis_operator_detail){
                $message_title="Berhasil !";
                $message_content="Jenis Posisi ".$input['jenis_posisi']." Berhasil Dibuat";
                $message_type="success";
                $message_succes = true;
            }

            $array_message = array(
                'success' => $message_succes,
                'message_title' => $message_title,
                'message_content' => $message_content,
                'message_type' => $message_type,
            );
            return response()->json($array_message);
        }

        return response()->json(
            [
                'success' => false,
                'error' => $validator->errors()->all()
            ]
        );
    }

    public function detail_pengerjaan(Request $request,$id,$id_pengerjaan)
    {
        $data['id'] = $id;
        $data['jenis_operator_detail'] = JenisOperatorDetail::find($id_pengerjaan);
        // dd($data['jenis_operator_detail']);
        if(empty($data['jenis_operator_detail'])){
            return redirect()->back();
        }
        if ($request->ajax()) {
            $data = JenisOperatorDetailPengerjaan::where('jenis_operator_detail_id',$id_pengerjaan)->get();
            return DataTables::of($data)
                    ->addIndexColumn()
                    // ->addColumn('created_at', function($row){
                    //     return Carbon::parse($row->created_at)->isoFormat('LLLL');
                    // })
                    // ->addColumn('updated_at', function($row){
                    //     return Carbon::parse($row->updated_at)->isoFormat('LLLL');
                    // })
                    ->addColumn('status', function($row){
                        if($row->status == 'Y'){
                            return '<span class="text-success">Aktif</span>';
                        }else{
                            return '<span class="text-secondary">Tidak Aktif</span>';
                        }
                    })
                    ->addColumn('action', function($row){
                        $btn = '<button type="button" onclick="edit(`'.$row->id.'`)" class="btn btn-warning btn-icon">
                                    <i class="fa fa-edit"></i>
                                </button>
                                <button type="button" onclick="hapus(`'.$row->id.'`)" class="btn btn-danger btn-icon">
                                    <i class="fa fa-trash"></i>
                                </button>';
                        return $btn;
                    })
                    ->rawColumns(['action','status'])
                    ->make(true);
        }
        return view('backend.jenis_payrol.pengerjaan.index',$data);
    }

    public function detail_pengerjaan_simpan(Request $request, $id, $id_pengerjaan)
    {
        $rules = [
            'jenis_posisi_pekerjaan' => 'required',
        ];

        $messages = [
            'jenis_posisi_pekerjaan.required'  => 'Jenis Posisi Pekerja wajib diisi.',
        ];

        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->passes()) {
            $input = $request->all();
            $input['jenis_operator_detail_id'] = $id_pengerjaan;
            $input['status'] = 'Y';
            $jenis_operator_detail_pekerja = JenisOperatorDetailPengerjaan::create($input);

            if($jenis_operator_detail_pekerja){
                $message_title="Berhasil !";
                $message_content="Jenis Posisi Pekerja ".$input['jenis_posisi_pekerjaan']." Berhasil Dibuat";
                $message_type="success";
                $message_succes = true;
            }

            $array_message = array(
                'success' => $message_succes,
                'message_title' => $message_title,
                'message_content' => $message_content,
                'message_type' => $message_type,
            );
            return response()->json($array_message);
        }

        return response()->json(
            [
                'success' => false,
                'error' => $validator->errors()->all()
            ]
        );
    }
}
