<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\BPJSJHT;
use App\Models\BPJSKesehatan;

use \Carbon\Carbon;
use Validator;
use DataTables;

class BpjsController extends Controller
{
    public function jht_index(Request $request)
    {
        if ($request->ajax()) {
            $data = BPJSJHT::all();
            return DataTables::of($data)
                            ->addIndexColumn()
                            ->addColumn('status', function($row){
                                if ($row->status == 'y') {
                                    return '<span class="text-success">Aktif</span>';
                                }elseif ($row->status == 'n') {
                                    return '<span class="text-danger">Tidak Aktif</span>';
                                }
                            })
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
                            ->rawColumns(['action','status'])
                            ->make(true);
        }
        return view('backend.bpjs.jht.index');
    }

    public function jht_simpan(Request $request)
    {
        $rules = [
            'keterangan' => 'required',
            'nominal' => 'required',
            'masa_kerja' => 'required',
            'tahun' => 'required',
        ];

        $messages = [
            'keterangan.required'  => 'Keterangan wajib diisi.',
            'nominal.required'  => 'Nominal wajib diisi.',
            'masa_kerja.required'  => 'Masa Kerja wajib diisi.',
            'tahun.required'  => 'Tahun wajib diisi.',
        ];

        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->passes()) {
            $input = $request->all();
            $norut = BPJSJHT::max('id');
            if(empty($norut)){
                $id = 1;
            }else{
                $id = $norut+1;
            }
            $input['id'] = $id;
            $input['status'] = 'y';
            $bpjs_jht = BPJSJHT::create($input);

            if($bpjs_jht){
                $message_title="Berhasil !";
                $message_content="BPJS JHT ".$input['keterangan']." Berhasil Dibuat";
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

    public function jht_detail($id)
    {
        $bpjs_jht = BPJSJHT::find($id);
        if(empty($bpjs_jht)){
            return response()->json([
                'success' => false,
                'message' => 'Data tidak ditemukan'
            ]);
        }

        return response()->json([
            'success' => true,
            'data' => $bpjs_jht
        ],200);
    }

    public function jht_update(Request $request)
    {
        $rules = [
            'edit_keterangan' => 'required',
            'edit_nominal' => 'required',
            'edit_masa_kerja' => 'required',
            'edit_tahun' => 'required',
            'edit_status' => 'required',
        ];

        $messages = [
            'edit_keterangan.required'  => 'Keterangan wajib diisi.',
            'edit_nominal.required'  => 'Nominal wajib diisi.',
            'edit_masa_kerja.required'  => 'Masa Kerja wajib diisi.',
            'edit_tahun.required'  => 'Tahun wajib diisi.',
            'edit_status.required'  => 'Status wajib diisi.',
        ];

        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->passes()) {

            $bpjs_jht = BPJSJHT::find($request->edit_id);

            $input['id'] = $request->edit_id;
            $input['keterangan'] = $request->edit_keterangan;
            $input['nominal'] = $request->edit_nominal;
            $input['masa_kerja'] = $request->edit_masa_kerja;
            $input['tahun'] = $request->edit_tahun;
            $input['status'] = $request->edit_status;
            $bpjs_jht->update($input);

            if($bpjs_jht){
                $message_title="Berhasil !";
                $message_content="BPJS JHT ".$request->edit_keterangan." Berhasil Diupdate";
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

    public function jht_delete($id)
    {
        $bpjs_jht = BPJSJHT::find($id);
        if(empty($bpjs_jht)){
            return response()->json([
                'success' => false,
                'message' => 'Data tidak ditemukan'
            ]);
        }
        $bpjs_jht->delete();
        return response()->json([
            'message_title' => 'Berhasil !',
            'message_content' => 'Data Berhasil Dihapus',
            'message_type' => 'success',
            'message_success' => true
        ]);
    }

    public function bpjs_kesehatan_index(Request $request)
    {
        if ($request->ajax()) {
            $data = BPJSKesehatan::all();
            return DataTables::of($data)
                            ->addIndexColumn()
                            ->addColumn('status', function($row){
                                if ($row->status == 'y') {
                                    return '<span class="text-success">Aktif</span>';
                                }elseif ($row->status == 'n') {
                                    return '<span class="text-danger">Tidak Aktif</span>';
                                }
                            })
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
                            ->rawColumns(['action','status'])
                            ->make(true);
        }
        return view('backend.bpjs.kesehatan.index');
    }

    public function bpjs_kesehatan_simpan(Request $request)
    {
        $rules = [
            'keterangan' => 'required',
            'nominal' => 'required',
            'tahun' => 'required',
        ];

        $messages = [
            'keterangan.required'  => 'Keterangan wajib diisi.',
            'nominal.required'  => 'Nominal wajib diisi.',
            'tahun.required'  => 'Tahun wajib diisi.',
        ];

        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->passes()) {
            $input = $request->all();
            $norut = BPJSKesehatan::max('id');
            if(empty($norut)){
                $id = 1;
            }else{
                $id = $norut+1;
            }
            $input['id'] = $id;
            $input['status'] = 'y';
            $bpjs_kesehatan = BPJSKesehatan::create($input);

            if($bpjs_kesehatan){
                $message_title="Berhasil !";
                $message_content="BPJS Kesehatan ".$input['keterangan']." Berhasil Dibuat";
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

    public function bpjs_kesehatan_detail($id)
    {
        $bpjs_kesehatan = BPJSKesehatan::find($id);
        if(empty($bpjs_kesehatan)){
            return response()->json([
                'success' => false,
                'message' => 'Data tidak ditemukan'
            ]);
        }

        return response()->json([
            'success' => true,
            'data' => $bpjs_kesehatan
        ],200);
    }

    public function bpjs_kesehatan_update(Request $request)
    {
        $rules = [
            'edit_keterangan' => 'required',
            'edit_nominal' => 'required',
            'edit_tahun' => 'required',
            'edit_status' => 'required',
        ];

        $messages = [
            'edit_keterangan.required'  => 'Keterangan wajib diisi.',
            'edit_nominal.required'  => 'Nominal wajib diisi.',
            'edit_tahun.required'  => 'Tahun wajib diisi.',
            'edit_status.required'  => 'Status wajib diisi.',
        ];

        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->passes()) {

            $bpjs_kesehatan = BPJSKesehatan::find($request->edit_id);

            $input['id'] = $request->edit_id;
            $input['keterangan'] = $request->edit_keterangan;
            $input['nominal'] = $request->edit_nominal;
            $input['tahun'] = $request->edit_tahun;
            $input['status'] = $request->edit_status;
            $bpjs_kesehatan->update($input);

            if($bpjs_kesehatan){
                $message_title="Berhasil !";
                $message_content="BPJS Kesehatan ".$request->edit_keterangan." Berhasil Diupdate";
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

    public function bpjs_kesehatan_delete($id)
    {
        $bpjs_kesehatan = BPJSKesehatan::find($id);
        if(empty($bpjs_kesehatan)){
            return response()->json([
                'success' => false,
                'message' => 'Data tidak ditemukan'
            ]);
        }
        $bpjs_kesehatan->delete();
        return response()->json([
            'message_title' => 'Berhasil !',
            'message_content' => 'Data Berhasil Dihapus',
            'message_type' => 'success',
            'message_success' => true
        ]);
    }
}
