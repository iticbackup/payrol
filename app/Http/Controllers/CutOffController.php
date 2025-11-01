<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\CutOff;
use \Carbon\Carbon;
use Validator;
use DataTables;

class CutOffController extends Controller
{
    function __construct(CutOff $cutOff)
    {
        $this->cutOff = $cutOff;
    }

    public function index(Request $request)
    {
        // dd(auth()->user()->role->nama_role);
        if ($request->ajax()) {
            $data = $this->cutOff->all();
            return DataTables::of($data)
                            ->addIndexColumn()
                            ->addColumn('status', function($row){
                                if ($row->status == 'Y') {
                                    return '<span class="text-success">Aktif</span>';
                                }elseif ($row->status == 'T') {
                                    return '<span class="text-danger">Tidak Aktif</span>';
                                }
                            })
                            ->addColumn('action', function($row){
                                $btn = '<button type="button" onclick="edit(`'.$row->id.'`)" class="btn btn-warning btn-icon">
                                            <i class="fa fa-edit"></i>
                                        </button>';
                                if (auth()->user()->role->nama_role == 'Administrator') {
                                $btn = $btn.'<button type="button" onclick="hapus(`'.$row->id.'`)" class="btn btn-danger btn-icon">
                                            <i class="fa fa-trash"></i>
                                        </button>';
                                }
                                return $btn;
                            })
                            ->rawColumns(['action','status'])
                            ->make(true);
        }

        return view('backend.cutoff.index');
    }

    public function simpan(Request $request)
    {
        $rules = [
            'periode' => 'required',
            'tanggal' => 'required',
            'tahun' => 'required',
            // 'status' => 'required',
        ];

        $messages = [
            'periode.required'  => 'Periode Cut Off wajib diisi.',
            'tanggal.required'  => 'Tanggal Cut Off wajib diisi.',
            'tahun.required'  => 'Tahun Periode Cut Off wajib diisi.',
        ];

        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->passes()) {
            $input = $request->all();
            $input['status'] = 'Y';
            $cutOff = $this->cutOff->create($input);

            if($cutOff){
                $message_title="Berhasil !";
                $message_content="Cut Off Periode ".$request->periode." Berhasil Dibuat";
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

    public function detail($id)
    {
        $cutOff = $this->cutOff->find($id);

        if (empty($cutOff)) {
            return response()->json([
                'success' => false,
                'message' => 'Data tidak ditemukan'
            ]);
        }

        return response()->json([
            'success' => true,
            'data' => $cutOff
        ]);
    }

    public function update(Request $request)
    {
        $rules = [
            'periode' => 'required',
            'tanggal' => 'required',
            'tahun' => 'required',
            // 'status' => 'required',
        ];

        $messages = [
            'periode.required'  => 'Periode Cut Off wajib diisi.',
            'tanggal.required'  => 'Tanggal Cut Off wajib diisi.',
            'tahun.required'  => 'Tahun Periode Cut Off wajib diisi.',
        ];

        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->passes()) {
            $input = $request->all();
            $cutOff = $this->cutOff->find($request->id)->update($input);

            if($cutOff){
                $message_title="Berhasil !";
                $message_content="Cut Off Periode ".$request->periode." Berhasil Diupdate";
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
