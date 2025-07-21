<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;

use App\Models\UMKBoronganLokal;
use App\Models\UMKBoronganLokalStempel;
use App\Models\UMKBoronganEkspor;
use App\Models\UMKBoronganAmbri;

use \Carbon\Carbon;
use Validator;
use DataTables;

class JenisUMKBoronganController extends Controller
{
    function __construct(
        UMKBoronganLokalStempel $umkBoronganStempel
    ){
        $this->umkBoronganStempel = $umkBoronganStempel;
    }

    public function lokal(Request $request)
    {
        if ($request->ajax()) {
            $data = UMKBoronganLokal::all();

            return DataTables::of($data)
                    ->addIndexColumn()
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
        return view('backend.jenis_umk_borongan.lokal.index');
    }

    public function lokal_simpan(Request $request)
    {
        $rules = [
            'jenis_produk' => 'required|unique:borongan_umk_lokal',
            'umk_packing' => 'required',
            'umk_bandrol' => 'required',
            'umk_inner' => 'required',
            'umk_outer' => 'required',
            'tahun_aktif' => 'required',
            'status' => 'required',
        ];

        $messages = [
            'jenis_produk.required'  => 'Jenis Produk wajib diisi.',
            'jenis_produk.unique'  => 'Jenis Produk sudah ada.',
            'umk_packing.required'  => 'UMK Packing wajib diisi.',
            'umk_bandrol.required'  => 'UMK Bandrol wajib diisi.',
            'umk_inner.required'  => 'UMK Inner wajib diisi.',
            'umk_outer.required'  => 'UMK Outer wajib diisi.',
            'tahun_aktif.required'  => 'Tahun Aktif wajib diisi.',
            'status.required'  => 'Status wajib diisi.',
        ];

        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->passes()) {
            $input = $request->all();
            $norut = UMKBoronganLokal::max('id');
            if(empty($norut)){
                $id = 1;
            }else{
                $id = $norut+1;
            }
            $input['id'] = $id;
            $umk_borongan_lokal = UMKBoronganLokal::create($input);

            if($umk_borongan_lokal){
                $message_title="Berhasil !";
                $message_content="UMK Borongan Lokal ".$input['jenis_produk']." Berhasil Dibuat";
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

    public function lokal_update(Request $request)
    {
        $umk_borongan_lokal = UMKBoronganLokal::where('id',$request->edit_id)->first();
            
        $input['jenis_produk'] = $request->edit_jenis_produk;
        $input['umk_packing'] = $request->edit_umk_packing;
        $input['umk_bandrol'] = $request->edit_umk_bandrol;
        $input['umk_inner'] = $request->edit_umk_inner;
        $input['umk_outer'] = $request->edit_umk_outer;
        $input['tahun_aktif'] = $request->edit_tahun_aktif;
        $input['status'] = $request->edit_status;

        $umk_borongan_lokal->update($input);

        if($umk_borongan_lokal){
            $message_title="Berhasil !";
            $message_content="UMK Borongan Lokal ".$request->edit_jenis_produk." Berhasil Diupdate";
            $message_type="success";
            $message_succes = true;
        }else{
            $message_title="Tidak Berhasil !";
            $message_content="UMK Borongan Lokal ".$request->edit_jenis_produk." Belum Berhasil Diupdate";
            $message_type="danger";
            $message_succes = false;
        }

        $array_message = array(
            'success' => $message_succes,
            'message_title' => $message_title,
            'message_content' => $message_content,
            'message_type' => $message_type,
        );
        return response()->json($array_message);
    }

    public function lokal_delete($id)
    {
        $umk_borongan_lokal = UMKBoronganLokal::where('id',$id)->delete();
        return response()->json([
            'message_title' => 'Berhasil !',
            'message_content' => 'Data Berhasil Dihapus',
            'message_type' => 'success',
            'message_success' => true
        ]);
    }

    public function lokal_detail($id)
    {
        $umk_borongan_lokal = UMKBoronganLokal::find($id);
        if(empty($umk_borongan_lokal)){
            return response()->json([
                'success' => false,
                'message' => 'Data Tidak Ada'
            ]);
        }
        return response()->json([
            'success' => true,
            'data' => $umk_borongan_lokal
        ],200);
    }

    public function lokal_umk_periode()
    {
        $data['tahun_berjalan'] = 2025;
        // $data['tahun_berjalan'] = Carbon::now()->format('Y');
        // $data['umk_borongan_lokals'] = UMKBoronganLokal::whereIn('tahun_aktif',[2023,2024])->get();
        return view('backend.jenis_umk_borongan.umk_periode.index',$data);
    }

    public function lokal_umk_periode_simpan(Request $request, $tahun_aktif)
    {
        $rules = [
            'umk_packing' => 'required',
            'umk_bandrol' => 'required',
            'umk_inner' => 'required',
            'umk_outer' => 'required',
        ];

        $messages = [
            'umk_packing.required'  => 'UMK Packing wajib diisi.',
            'umk_bandrol.required'  => 'UMK Bandrol wajib diisi.',
            'umk_inner.required'  => 'UMK Inner wajib diisi.',
            'umk_outer.required'  => 'UMK Outer wajib diisi.',
        ];

        $validator = Validator::make($request->all(), $rules, $messages);
        if ($validator->passes()) {
            $norut = UMKBoronganLokal::max('id');
            foreach ($request->jenis_produk as $key => $value) {
                // dd($request->tahun_aktif[$key]);
                // dd(Carbon::create($request->tahun_aktif[$key])->format('Y')-1);
                
                $umk_borongan_lokal = UMKBoronganLokal::where('jenis_produk',$request->jenis_produk[$key])->where('tahun_aktif',Carbon::create($request->tahun_aktif[$key])->format('Y')-1)->first();
                if (!empty($umk_borongan_lokal)) {
                    $input['id'] = $norut+$key+1;
                    $input['jenis_produk'] = $request->jenis_produk[$key];
                    $input['umk_packing'] = $request->umk_packing[$key];
                    $input['umk_bandrol'] = $request->umk_bandrol[$key];
                    $input['umk_inner'] = $request->umk_inner[$key];
                    $input['umk_outer'] = $request->umk_outer[$key];
                    $input['tahun_aktif'] = Carbon::create($request->tahun_aktif[$key])->format('Y');
                    $input['status'] = "Y";
                    $umk_borongan_lokal->update([
                        'status' => "T"
                    ]);
                    UMKBoronganLokal::create($input);
                }
            }
        }
        return $validator->errors()->all();
        // return 'Lokal Simpan';
    }

    public function lokal_umk_stempel(Request $request)
    {
        $data = $this->umkBoronganStempel->all();

        return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('status', function($row){
                    if($row->status == 'Y'){
                        return '<span class="text-success">Aktif</span>';
                    }else{
                        return '<span class="text-secondary">Tidak Aktif</span>';
                    }
                })
                ->addColumn('action', function($row){
                    $btn = '<button type="button" onclick="edit_stempel(`'.$row->id.'`)" class="btn btn-warning btn-icon">
                                <i class="fa fa-edit"></i>
                            </button>
                            <button type="button" onclick="hapus_stempel(`'.$row->id.'`)" class="btn btn-danger btn-icon">
                                <i class="fa fa-trash"></i>
                            </button>';
                    return $btn;
                })
                ->rawColumns(['action','status'])
                ->make(true);
    }

    public function lokal_umk_stempel_simpan(Request $request)
    {
        $rules = [
            'stempel_jenis_produk' => 'required|unique:borongan_umk_stempel',
            'stempel_nominal_umk' => 'required',
            'stempel_target_pengerjaan' => 'required',
            'stempel_tahun_aktif' => 'required',
            'stempel_status' => 'required',
        ];

        $messages = [
            'stempel_jenis_produk.required'  => 'Jenis Produk wajib diisi.',
            'stempel_jenis_produk.unique'  => 'Jenis Produk sudah ada.',
            'stempel_nominal_umk.required'  => 'Nominal UMK wajib diisi.',
            'stempel_target_pengerjaan.required'  => 'Target Pengerjaan wajib diisi.',
            'stempel_tahun_aktif.required'  => 'Tahun Aktif wajib diisi.',
            'stempel_status.required'  => 'Status wajib diisi.',
        ];

        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->passes()) {
            $norut = $this->umkBoronganStempel->max('id');

            if(empty($norut)){
                $id = 1;
            }else{
                $id = $norut+1;
            }

            $input['id'] = $id;
            $input['jenis_produk'] = $request->stempel_jenis_produk;
            $input['nominal_umk'] = $request->stempel_nominal_umk;
            $input['target_pengerjaan'] = $request->stempel_target_pengerjaan;
            $input['tahun_aktif'] = $request->stempel_tahun_aktif;
            $input['status'] = $request->stempel_status;

            $simpanUmkBoronganStempel = $this->umkBoronganStempel->create($input);

            if($simpanUmkBoronganStempel){
                $message_title="Berhasil !";
                $message_content="UMK Borongan Stempel ".$input['jenis_produk']." Berhasil Dibuat";
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

    public function lokal_umk_stempel_detail($id)
    {
        $umkBoronganStempel = $this->umkBoronganStempel->find($id);
        if(empty($umkBoronganStempel)){
            return response()->json([
                'success' => false,
                'message_title' => 'Gagal!',
                'message_content' => 'UMK Borongan Stempel Tidak Tersedia',
            ]);
        }

        return response()->json([
            'success' => true,
            'data' => $umkBoronganStempel
        ],200);
    }

    public function lokal_umk_stempel_update(Request $request, $id)
    {
        $umkBoronganStempel = $this->umkBoronganStempel->find($id);

        if(empty($umkBoronganStempel)){
            return response()->json([
                'success' => false,
                'message_title' => 'Gagal!',
                'message_content' => 'UMK Borongan Stempel Tidak Tersedia',
            ]);
        }

        $input['jenis_produk'] = $request->edit_stempel_jenis_produk;
        $input['nominal_umk'] = $request->edit_stempel_nominal_umk;
        $input['target_pengerjaan'] = $request->edit_stempel_target_pengerjaan;
        $input['tahun_aktif'] = $request->edit_stempel_tahun_aktif;
        $input['status'] = $request->edit_stempel_status;

        $umkBoronganStempel->update($input);

        if($umkBoronganStempel){
            $message_title="Berhasil !";
            $message_content="UMK Borongan Stempel ".$input['jenis_produk']." Berhasil Diupdate";
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

    public function lokal_umk_stempel_delete($id)
    {
        $umkBoronganStempel = $this->umkBoronganStempel->find($id);

        if(empty($umkBoronganStempel)){
            return response()->json([
                'success' => false,
                'message_title' => 'Gagal!',
                'message_content' => 'UMK Borongan Stempel Tidak Tersedia',
            ]);
        }

        $umkBoronganStempel->delete();

        return response()->json([
            'message_title' => 'Berhasil !',
            'message_content' => 'Data Berhasil Dihapus',
            'message_type' => 'success',
            'message_success' => true
        ]);
    }

    public function ekspor(Request $request)
    {
        if ($request->ajax()) {
            $data = UMKBoronganEkspor::all();

            return DataTables::of($data)
                    ->addIndexColumn()
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
        return view('backend.jenis_umk_borongan.ekspor.index');
    }

    public function ekspor_simpan(Request $request)
    {
        $rules = [
            'jenis_produk' => 'required|unique:borongan_umk_ekspor',
            'umk_packing' => 'required',
            'umk_kemas' => 'required',
            'umk_pilih_gagang' => 'required',
            'tahun_aktif' => 'required',
            'status' => 'required',
        ];

        $messages = [
            'jenis_produk.required'  => 'Jenis Produk wajib diisi.',
            'jenis_produk.unique'  => 'Jenis Produk sudah ada.',
            'umk_packing.required'  => 'UMK Packing wajib diisi.',
            'umk_kemas.required'  => 'UMK Kemas wajib diisi.',
            'umk_pilih_gagang.required'  => 'UMK Pilih Gagang wajib diisi.',
            'tahun_aktif.required'  => 'Tahun Aktif wajib diisi.',
            'status.required'  => 'Status wajib diisi.',
        ];

        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->passes()) {
            $input = $request->all();
            $norut = UMKBoronganEkspor::max('id');
            if(empty($norut)){
                $id = 1;
            }else{
                $id = $norut+1;
            }
            $input['id'] = $id;
            $umk_borongan_ekspor = UMKBoronganEkspor::create($input);

            if($umk_borongan_ekspor){
                $message_title="Berhasil !";
                $message_content="UMK Borongan Ekspor ".$input['jenis_produk']." Berhasil Dibuat";
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

    public function ekspor_detail($id)
    {
        $umk_borongan_expor = UMKBoronganEkspor::find($id);
        if(empty($umk_borongan_expor)){
            return response()->json([
                'success' => false,
                'message' => 'Data Tidak Ada'
            ]);
        }
        return response()->json([
            'success' => true,
            'data' => $umk_borongan_expor
        ],200);
    }

    public function ekspor_update(Request $request)
    {
        $umk_borongan_ekspor = UMKBoronganEkspor::where('id',$request->edit_id)->first();
            
        $input['jenis_produk'] = $request->edit_jenis_produk;
        $input['umk_packing'] = $request->edit_umk_packing;
        $input['umk_kemas'] = $request->edit_umk_kemas;
        $input['umk_pilih_gagang'] = $request->edit_umk_pilih_gagang;
        $input['tahun_aktif'] = $request->edit_tahun_aktif;
        $input['status'] = $request->edit_status;

        $umk_borongan_ekspor->update($input);

        if($umk_borongan_ekspor){
            $message_title="Berhasil !";
            $message_content="UMK Borongan Ekspor ".$request->edit_jenis_produk." Berhasil Diupdate";
            $message_type="success";
            $message_succes = true;
        }else{
            $message_title="Tidak Berhasil !";
            $message_content="UMK Borongan Ekspor ".$request->edit_jenis_produk." Belum Berhasil Diupdate";
            $message_type="danger";
            $message_succes = false;
        }

        $array_message = array(
            'success' => $message_succes,
            'message_title' => $message_title,
            'message_content' => $message_content,
            'message_type' => $message_type,
        );
        return response()->json($array_message);
    }

    public function ekspor_delete($id)
    {
        $umk_borongan_ekspor = UMKBoronganEkspor::where('id',$id)->delete();
        return response()->json([
            'message_title' => 'Berhasil !',
            'message_content' => 'Data Berhasil Dihapus',
            'message_type' => 'success',
            'message_success' => true
        ]);
    }

    public function ambri(Request $request)
    {
        if ($request->ajax()) {
            $data = UMKBoronganAmbri::all();

            return DataTables::of($data)
                    ->addIndexColumn()
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
        return view('backend.jenis_umk_borongan.ambri.index');
    }

    public function ambri_simpan(Request $request)
    {
        $rules = [
            'jenis_produk' => 'required|unique:borongan_umk_ambri',
            'umk_etiket' => 'required',
            'umk_las_tepi' => 'required',
            'umk_las_pojok' => 'required',
            'umk_ambri' => 'required',
            'tahun_aktif' => 'required',
            'status' => 'required',
        ];

        $messages = [
            'jenis_produk.required'  => 'Jenis Produk wajib diisi.',
            'jenis_produk.unique'  => 'Jenis Produk sudah ada.',
            'umk_etiket.required'  => 'UMK Etiket wajib diisi.',
            'umk_las_tepi.required'  => 'UMK Las Tepi wajib diisi.',
            'umk_las_pojok.required'  => 'UMK Las Pojok wajib diisi.',
            'umk_ambri.required'  => 'UMK Ambri wajib diisi.',
            'tahun_aktif.required'  => 'Tahun Aktif wajib diisi.',
            'status.required'  => 'Status wajib diisi.',
        ];

        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->passes()) {
            $input = $request->all();
            $norut = UMKBoronganAmbri::max('id');
            if(empty($norut)){
                $id = 1;
            }else{
                $id = $norut+1;
            }
            $input['id'] = $id;
            $umk_borongan_ambri = UMKBoronganAmbri::create($input);

            if($umk_borongan_ambri){
                $message_title="Berhasil !";
                $message_content="UMK Borongan Ambri ".$input['jenis_produk']." Berhasil Dibuat";
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

    public function ambri_detail($id)
    {
        $umk_borongan_ambri = UMKBoronganAmbri::find($id);
        if(empty($umk_borongan_ambri)){
            return response()->json([
                'success' => false,
                'message' => 'Data Tidak Ada'
            ]);
        }
        return response()->json([
            'success' => true,
            'data' => $umk_borongan_ambri
        ],200);
    }

    public function ambri_update(Request $request)
    {
        $umk_borongan_ambri = UMKBoronganAmbri::where('id',$request->edit_id)->first();
            
        $input['umk_jenis_produk'] = $request->edit_umk_jenis_produk;
        $input['umk_etiket'] = $request->edit_umk_etiket;
        $input['umk_las_tepi'] = $request->edit_umk_las_tepi;
        $input['umk_las_pojok'] = $request->edit_umk_las_pojok;
        $input['umk_ambri'] = $request->edit_umk_ambri;
        $input['tahun_aktif'] = $request->edit_tahun_aktif;
        $input['status'] = $request->edit_status;

        $umk_borongan_ambri->update($input);

        if($umk_borongan_ambri){
            $message_title="Berhasil !";
            $message_content="UMK Borongan Ambri ".$request->edit_jenis_produk." Berhasil Diupdate";
            $message_type="success";
            $message_succes = true;
        }else{
            $message_title="Tidak Berhasil !";
            $message_content="UMK Borongan Ambri ".$request->edit_jenis_produk." Belum Berhasil Diupdate";
            $message_type="danger";
            $message_succes = false;
        }

        $array_message = array(
            'success' => $message_succes,
            'message_title' => $message_title,
            'message_content' => $message_content,
            'message_type' => $message_type,
        );
        return response()->json($array_message);
    }

    public function ambri_delete($id)
    {
        $umk_borongan_ambri = UMKBoronganAmbri::where('id',$id)->delete();
        return response()->json([
            'message_title' => 'Berhasil !',
            'message_content' => 'Data Berhasil Dihapus',
            'message_type' => 'success',
            'message_success' => true
        ]);
    }
}
