<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TunjanganKerja;

use \Carbon\Carbon;
use Validator;
use DataTables;

class TunjanganKerjaController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = TunjanganKerja::all();
            return DataTables::of($data)
                            ->addIndexColumn()
                            ->addColumn('nominal', function($row){
                                return 'Rp. '.number_format($row->nominal,0,',','.');
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
                            ->rawColumns(['action'])
                            ->make(true);
        }
        return view('backend.tunjangan_kerja.index');
    }

    public function simpan(Request $request)
    {
        $rules = [
            'golongan' => 'required',
            'nominal' => 'required',
        ];

        $messages = [
            'golongan.required'  => 'Golongan wajib diisi.',
            'nominal.required'  => 'Nominal wajib diisi.',
        ];

        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->passes()) {
            $input = $request->all();
            $norut = TunjanganKerja::max('id');
            if(empty($norut)){
                $id = 1;
            }else{
                $id = $norut+1;
            }
            $input['id'] = $id;
            $bpjs_kesehatan = TunjanganKerja::create($input);

            if($bpjs_kesehatan){
                $message_title="Berhasil !";
                $message_content="Golongan Tunjangan Kerja ".$input['golongan']." Berhasil Dibuat";
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
