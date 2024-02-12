<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;

use App\Models\UserManagement;
use App\Models\KaryawanOperator;
use App\Models\KaryawanOperatorHarian;
use App\Models\RitKaryawan;
use App\Models\JenisOperator;
use App\Models\JenisOperatorDetail;
use App\Models\JenisOperatorDetailPengerjaan;
use App\Models\RitPosisi;

use App\Models\BiodataKaryawan;
use App\Models\TunjanganKerja;

use \Carbon\Carbon;
use Validator;
use DataTables;

class OperatorKaryawanController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            if (auth()->user()->roles != 1) {
                $data = KaryawanOperator::where('jenis_operator_id',1)->where('status','y')->get();
            }else{
                $data = KaryawanOperator::where('jenis_operator_id',1)->get();
            }
            return DataTables::of($data)
                    ->addIndexColumn()
                    ->addColumn('jenis_operator_id', function($row){
                        return '<span class="badge bg-secondary">'.$row->jenis_operator->jenis_operator.'</span>'.' <i class="fas fa-long-arrow-alt-right"></i> '.'<span class="badge bg-purple">'.$row->jenis_operator_detail->jenis_posisi.'</span>'.' <i class="fas fa-long-arrow-alt-right"></i> '.'<span class="badge bg-primary">'.$row->jenis_operator_detail_pengerjaan->jenis_posisi_pekerjaan.'</span>';
                    })
                    ->addColumn('tunjangan_kerja_id', function($row){
                        if($row->tunjangan_kerja_id == 1){
                            return $row->tunjangan_kerja->golongan;
                        }elseif($row->tunjangan_kerja_id == 2){
                            return $row->tunjangan_kerja->golongan;
                        }elseif($row->tunjangan_kerja_id == 3){
                            return $row->tunjangan_kerja->golongan;
                        }elseif($row->tunjangan_kerja_id == 4){
                            return $row->tunjangan_kerja->golongan;
                        }elseif($row->tunjangan_kerja_id == 5){
                            return $row->tunjangan_kerja->golongan;
                        }
                        else{
                            return 0;
                        }
                    })
                    ->addColumn('nominal_tunjangan_kerja', function($row){
                        if (empty($row->tunjangan_kerja)) {
                            return '-';
                        }else{
                            return 'Rp. '.number_format($row->tunjangan_kerja->nominal,0,',','.');
                        }
                    })
                    ->addColumn('nama_karyawan', function($row){
                        $biodata_karyawan = BiodataKaryawan::where('nik',$row->nik)->first();
                        // $biodata_karyawan = BiodataKaryawan::where('nik','1611778')->first();
                        // dd($biodata_karyawan);
                        if(empty($biodata_karyawan)){
                            return '-';
                        }else{
                            return $biodata_karyawan->nama;
                        }
                    })
                    ->addColumn('status', function($row){
                        if ($row->status == 'Y') {
                            return '<span class="text-success">Aktif</span>';
                        }elseif ($row->status == 'T') {
                            return '<span class="text-danger">Tidak Aktif</span>';
                        }
                    })
                    // ->addColumn('status', function($row){
                    //     if($row->status == 'Y'){
                    //         return 'Aktif';
                    //     }else{
                    //         return 'Tidak Aktif';
                    //     }
                    // })
                    // ->addColumn('updated_at', function($row){
                    //     return Carbon::parse($row->updated_at)->isoFormat('LLLL');
                    // })
                    ->addColumn('action', function($row){
                        $btn = '<button type="button" onclick="edit(`'.$row->id.'`)" class="btn btn-warning btn-icon">
                                    <i class="fa fa-edit"></i>
                                </button>';
                        if (auth()->user()->roles == 1) {
                        $btn = $btn.'<button type="button" onclick="hapus(`'.$row->id.'`)" class="btn btn-danger btn-icon">
                                    <i class="fa fa-trash"></i>
                                </button>';
                        }
                        return $btn;
                    })
                    ->rawColumns(['action','jenis_operator_id','status'])
                    ->make(true);
        }
        $data['jenis_operators'] = JenisOperator::all();
        $data['jenis_operator_details'] = JenisOperatorDetail::all();
        $data['jenis_operator_detail_pengerjaans'] = JenisOperatorDetailPengerjaan::all();
        $data['tunjangan_kerjas'] = TunjanganKerja::all();
        $data['user_management'] = UserManagement::where('user_id',auth()->user()->id)->first();
        return view('backend.payrol.operartor_karyawan.index',$data);
    }

    public function create()
    {
        $data['jenis_operators'] = JenisOperator::all();
        return view('backend.payrol.operartor_karyawan.create',$data);
    }

    public function simpan(Request $request)
    {
        $rules = [
            // 'nik' => 'required|unique:operator_karyawan',
            'nik' => 'required',
            'nama_karyawan' => 'required',
            'jenis_operator_id' => 'required',
            'jenis_operator_detail_id' => 'required',
            'jenis_operator_detail_pekerjaan_id' => 'required',
            'jht' => 'required',
            'bpjs' => 'required',
            'training' => 'required',
        ];

        $messages = [
            'nik.required'  => 'NIK Karyawan wajib diisi.',
            // 'nik.unique'  => 'NIK Karyawan sudah terpakai.',
            'nama_karyawan.required'  => 'Nama Karyawan wajib diisi.',
            'jenis_operator_id.required'  => 'Posisi Karyawan wajib diisi.',
            'jenis_operator_detail_id.required'  => 'Posisi Pengerjaan Karyawan wajib diisi.',
            'jenis_operator_detail_pekerjaan_id.required'  => 'Jenis Posisi Pengerjaan Karyawan wajib diisi.',
            'jht.required'  => 'JHT wajib diisi.',
            'bpjs.required'  => 'BPJS wajib diisi.',
            'training.required'  => 'Training wajib diisi.',
        ];

        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->passes()) {
            $input = $request->all();
            $norut = KaryawanOperator::max('id');
            if(empty($norut)){
                $id = 1;
            }else{
                $id = $norut+1;
            }
            $input['id'] = $id;
            $input['status'] = 'Y';
            $operator_karyawan = KaryawanOperator::create($input);

            if($operator_karyawan){
                $message_title="Berhasil !";
                $message_content="NIK ".$input['nik']." - ".$input['nama_karyawan']." Berhasil Dibuat";
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
        $karyawan_operator = KaryawanOperator::find($id);
        if(empty($karyawan_operator)){
            return response()->json([
                'success' => false,
                'message' => 'Data tidak ditemukan'
            ]);
        }
        $biodata_karyawan = BiodataKaryawan::select('rekening')->where('nik',$karyawan_operator->nik)->first();
        // dd($biodata_karyawan);
        return response()->json([
            'success' => true,
            'data' => [
                'id' => $karyawan_operator->id,
                'nik' => $karyawan_operator->nik,
                'rekening' => $biodata_karyawan->rekening,
                'jenis_operator_id' => $karyawan_operator->jenis_operator_id,
                'jenis_operator_detail_id' => $karyawan_operator->jenis_operator_detail_id,
                'jenis_operator_detail_pekerjaan_id' => $karyawan_operator->jenis_operator_detail_pekerjaan_id,
                'tunjangan_kerja_id' => $karyawan_operator->tunjangan_kerja_id,
                'jht' => $karyawan_operator->jht,
                'bpjs' => $karyawan_operator->bpjs,
                'training' => $karyawan_operator->training,
                'status' => $karyawan_operator->status,
            ]
        ]);
    }

    public function update(Request $request)
    {
        $rules = [
            // 'edit_nik' => 'required|unique:operator_karyawan',
            // 'edit_nama_karyawan' => 'required',
            'edit_jenis_operator_id' => 'required',
            'edit_jenis_operator_detail_id' => 'required',
            'edit_jenis_operator_detail_pekerjaan_id' => 'required',
            'edit_jht' => 'required',
            'edit_bpjs' => 'required',
            'edit_training' => 'required',
            'edit_tunjangan_kerja_id' => 'required',
            'edit_status' => 'required',
            'edit_rekening' => 'required',
        ];

        $messages = [
            // 'edit_nik.required'  => 'NIK Karyawan wajib diisi.',
            // 'edit_nik.unique'  => 'NIK Karyawan sudah terpakai.',
            // 'edit_nama_karyawan.required'  => 'Nama Karyawan wajib diisi.',
            'edit_jenis_operator_id.required'  => 'Posisi Karyawan wajib diisi.',
            'edit_jenis_operator_detail_id.required'  => 'Posisi Pengerjaan Karyawan wajib diisi.',
            'edit_jenis_operator_detail_pekerjaan_id.required'  => 'Jenis Posisi Pengerjaan Karyawan wajib diisi.',
            'edit_jht.required'  => 'JHT wajib diisi.',
            'edit_bpjs.required'  => 'BPJS wajib diisi.',
            'edit_training.required'  => 'Training wajib diisi.',
            'edit_tunjangan_kerja_id.required'  => 'Golongan Tunjangan Kerja wajib diisi.',
            'edit_status.required'  => 'Status wajib diisi.',
            'edit_rekening.required'  => 'No.Rekening wajib diisi.',
        ];

        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->passes()) {
            // $input = $request->all();
            $operator_karyawan = KaryawanOperator::find($request->edit_id);

            $input['nik'] = $request->edit_nik;
            $input['jenis_operator_id'] = $request->edit_jenis_operator_id;
            $input['jenis_operator_detail_id'] = $request->edit_jenis_operator_detail_id;
            $input['jenis_operator_detail_pekerjaan_id'] = $request->edit_jenis_operator_detail_pekerjaan_id;
            $input['jht'] = $request->edit_jht;
            $input['bpjs'] = $request->edit_bpjs;
            $input['training'] = $request->edit_training;
            $input['status'] = $request->edit_status;
            $input['tunjangan_kerja_id'] = $request->edit_tunjangan_kerja_id;
            
            BiodataKaryawan::where('nik',$request->edit_nik)->update([
                'rekening' => $request->edit_rekening
            ]);

            $operator_karyawan->update($input);

            if($operator_karyawan){
                $message_title="Berhasil !";
                $message_content="NIK ".$input['nik']." - ".$request['nama_karyawan']." Berhasil Diupdate";
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

    public function select_jenis_operator_detail(Request $request)
    {
        $get_id = (int)$request->id;
        $jenis_operator_detail = JenisOperatorDetail::where('jenis_operator_id',$get_id)->pluck('jenis_posisi', 'id');
        return response()->json($jenis_operator_detail);
    }

    public function select_biodata_karyawan(Request $request)
    {
        $get_nik = $request->nik;
        $biodata_karyawan = BiodataKaryawan::select('nik','nama')->where('nik',$get_nik)->first();
        if (empty($biodata_karyawan)) {
            return response()->json([
                'success' => false,
                'data' => 'Data Karyawan Tidak Ditemukan'
            ]);
        }
        // dd($get_nik);
        return response()->json([
            'success' => true,
            'data' => $biodata_karyawan
        ]);
    }

    public function select_jenis_operator_detail_pekerjaan(Request $request)
    {
        $get_id = (int)$request->id;
        $jenis_operator_detail_pekerjaan = JenisOperatorDetailPengerjaan::where('jenis_operator_detail_id',$get_id)->pluck('jenis_posisi_pekerjaan', 'id');
        return response()->json($jenis_operator_detail_pekerjaan);
    }

    public function karyawan_operator_harian(Request $request)
    {
        if ($request->ajax()) {
            if (auth()->user()->roles != 1) {
                $data = KaryawanOperatorHarian::where('jenis_operator_id',2)->where('status','y')->get();
            }else{
                $data = KaryawanOperatorHarian::where('jenis_operator_id',2)->get();
            }

            return DataTables::of($data)
                            ->addIndexColumn()
                            ->addColumn('jenis_operator_id', function($row){
                                if (empty($row->jenis_operator_detail_pengerjaan)) {
                                    return '<span class="badge bg-secondary">'.$row->jenis_operator->jenis_operator.'</span>';
                                }else{
                                    return '<span class="badge bg-secondary">'.$row->jenis_operator->jenis_operator.'</span>'.' <i class="fas fa-long-arrow-alt-right"></i> '.'<span class="badge bg-primary">'.$row->jenis_operator_detail_pengerjaan->jenis_posisi_pekerjaan.'</span>';
                                }
                                
                                // return '<span class="badge bg-secondary">'.$row->jenis_operator->jenis_operator.'</span>'.' <i class="fas fa-long-arrow-alt-right"></i> '.'<span class="badge bg-purple">'.$row->jenis_operator_detail->jenis_posisi.'</span>'.' <i class="fas fa-long-arrow-alt-right"></i> '.'<span class="badge bg-primary">'.$row->jenis_operator_detail_pengerjaan->jenis_posisi_pekerjaan.'</span>';
                            })
                            ->addColumn('tunjangan_kerja_id', function($row){
                                if($row->tunjangan_kerja_id == 1){
                                    return $row->tunjangan_kerja->golongan;
                                }elseif($row->tunjangan_kerja_id == 2){
                                    return $row->tunjangan_kerja->golongan;
                                }elseif($row->tunjangan_kerja_id == 3){
                                    return $row->tunjangan_kerja->golongan;
                                }elseif($row->tunjangan_kerja_id == 4){
                                    return $row->tunjangan_kerja->golongan;
                                }elseif($row->tunjangan_kerja_id == 5){
                                    return $row->tunjangan_kerja->golongan;
                                }
                                else{
                                    return 0;
                                }
                            })

                            ->addColumn('nominal_tunjangan_kerja', function($row){
                                if (empty($row->tunjangan_kerja)) {
                                    return '-';
                                }else{
                                    return 'Rp. '.number_format($row->tunjangan_kerja->nominal,0,',','.');
                                }
                            })

                            ->addColumn('nama_karyawan', function($row){
                                $biodata_karyawan = BiodataKaryawan::where('nik',$row->nik)->first();
                                // $biodata_karyawan = BiodataKaryawan::where('nik','1611778')->first();
                                // dd($biodata_karyawan);
                                if(empty($biodata_karyawan)){
                                    return '-';
                                }else{
                                    return $biodata_karyawan->nama;
                                }
                            })
                            ->addColumn('status', function($row){
                                if ($row->status == 'y') {
                                    return '<span class="text-success">Aktif</span>';
                                }elseif ($row->status == 'n') {
                                    return '<span class="text-danger">Tidak Aktif</span>';
                                }
                            })
                            ->addColumn('action', function($row){
                                $btn = '<button type="button" onclick="edit(`'.$row->id.'`)" class="btn btn-warning btn-icon">
                                            <i class="fa fa-edit"></i>
                                        </button>';
                                if (auth()->user()->roles == 1) {
                                $btn = $btn.'<button type="button" onclick="hapus(`'.$row->id.'`)" class="btn btn-danger btn-icon">
                                            <i class="fa fa-trash"></i>
                                        </button>';
                                }
                                return $btn;
                            })
                            ->rawColumns(['action','jenis_operator_id','status'])
                            ->make(true);
        }
        $data['jenis_operators'] = JenisOperator::all();
        $data['jenis_operator_details'] = JenisOperatorDetail::all();
        $data['jenis_operator_detail_pengerjaans'] = JenisOperatorDetailPengerjaan::all();
        $data['tunjangan_kerjas'] = TunjanganKerja::all();
        $data['user_management'] = UserManagement::where('user_id',auth()->user()->id)->first();
        return view('backend.payrol.operator_karyawan_harian.index',$data);
    }

    public function karyawan_operator_harian_simpan(Request $request)
    {
        $rules = [
            // 'nik' => 'required|unique:operator_harian_karyawan',
            // 'nik' => 'required',
            'nama_karyawan' => 'required',
            'jenis_operator_id' => 'required',
            'jenis_operator_detail_id' => 'required',
            'jenis_operator_detail_pekerjaan_id' => 'required',
            'jht' => 'required',
            'bpjs' => 'required',
            // 'training' => 'required',
        ];

        $messages = [
            // 'nik.required'  => 'NIK Karyawan wajib diisi.',
            // 'nik.unique'  => 'NIK Karyawan sudah ada.',
            // 'nik.unique'  => 'NIK Karyawan sudah terpakai.',
            'nama_karyawan.required'  => 'Nama Karyawan wajib diisi.',
            'jenis_operator_id.required'  => 'Posisi Karyawan wajib diisi.',
            'jenis_operator_detail_id.required'  => 'Posisi Pengerjaan Karyawan wajib diisi.',
            'jenis_operator_detail_pekerjaan_id.required'  => 'Jenis Posisi Pengerjaan Karyawan wajib diisi.',
            'jht.required'  => 'JHT wajib diisi.',
            'bpjs.required'  => 'BPJS wajib diisi.',
            // 'training.required'  => 'Training wajib diisi.',
        ];

        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->passes()) {
            $input = $request->all();
            $norut = KaryawanOperatorHarian::max('id');
            if(empty($norut)){
                $id = 1;
            }else{
                $id = $norut+1;
            }
            $input['id'] = $id;
            $input['status'] = 'y';
            $operator_karyawan_harian = KaryawanOperatorHarian::create($input);

            if($operator_karyawan_harian){
                $message_title="Berhasil !";
                $message_content="NIK ".$input['nik']." - ".$input['nama_karyawan']." Berhasil Dibuat";
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

    public function karyawan_operator_harian_detail($id)
    {
        $karyawan_operator_harian = KaryawanOperatorHarian::find($id);
        $biodata_karyawan = BiodataKaryawan::select('rekening')->where('nik',$karyawan_operator_harian->nik)->first();
        if(empty($karyawan_operator_harian)){
            return response()->json([
                'success' => false,
                'message' => 'Data tidak ditemukan'
            ]);
        }
        return response()->json([
            'success' => true,
            'data' => [
                'id' => $karyawan_operator_harian->id,
                'nik' => $karyawan_operator_harian->nik,
                'rekening' => $biodata_karyawan->rekening,
                'jenis_operator_id' => $karyawan_operator_harian->jenis_operator_id,
                'jenis_operator_detail_id' => $karyawan_operator_harian->jenis_operator_detail_id,
                'jenis_operator_detail_pekerjaan_id' => $karyawan_operator_harian->jenis_operator_detail_pekerjaan_id,
                'tunjangan_kerja_id' => $karyawan_operator_harian->tunjangan_kerja_id,
                'hari_kerja' => $karyawan_operator_harian->hari_kerja,
                'upah_dasar' => $karyawan_operator_harian->upah_dasar,
                'jht' => $karyawan_operator_harian->jht,
                'bpjs' => $karyawan_operator_harian->bpjs,
                'status' => $karyawan_operator_harian->status,
            ]
        ]);
    }
    
    public function karyawan_operator_harian_update(Request $request)
    {
        $rules = [
            // 'edit_nik' => 'required|unique:operator_karyawan',
            // 'edit_nama_karyawan' => 'required',
            'edit_jenis_operator_id' => 'required',
            'edit_jenis_operator_detail_id' => 'required',
            'edit_jenis_operator_detail_pekerjaan_id' => 'required',
            'edit_jht' => 'required',
            'edit_bpjs' => 'required',
            'edit_tunjangan_kerja_id' => 'required',
            'edit_status' => 'required',
            'edit_rekening' => 'required',
        ];

        $messages = [
            // 'edit_nik.required'  => 'NIK Karyawan wajib diisi.',
            // 'edit_nik.unique'  => 'NIK Karyawan sudah terpakai.',
            // 'edit_nama_karyawan.required'  => 'Nama Karyawan wajib diisi.',
            'edit_jenis_operator_id.required'  => 'Posisi Karyawan wajib diisi.',
            'edit_jenis_operator_detail_id.required'  => 'Posisi Pengerjaan Karyawan wajib diisi.',
            'edit_jenis_operator_detail_pekerjaan_id.required'  => 'Jenis Posisi Pengerjaan Karyawan wajib diisi.',
            'edit_jht.required'  => 'JHT wajib diisi.',
            'edit_bpjs.required'  => 'BPJS wajib diisi.',
            'edit_tunjangan_kerja_id.required'  => 'Golongan Tunjangan Kerja wajib diisi.',
            'edit_status.required'  => 'Status wajib diisi.',
            'edit_rekening.required'  => 'No.Rekening wajib diisi.',
        ];

        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->passes()) {
            // $input = $request->all();
            $operator_karyawan_harian = KaryawanOperatorHarian::find($request->edit_id);

            $input['nik'] = $request->edit_nik;
            $input['jenis_operator_id'] = $request->edit_jenis_operator_id;
            $input['jenis_operator_detail_id'] = $request->edit_jenis_operator_detail_id;
            $input['jenis_operator_detail_pekerjaan_id'] = $request->edit_jenis_operator_detail_pekerjaan_id;
            $input['upah_dasar'] = $request->edit_upah_dasar;
            $input['jht'] = $request->edit_jht;
            $input['bpjs'] = $request->edit_bpjs;
            $input['status'] = $request->edit_status;
            $input['tunjangan_kerja_id'] = $request->edit_tunjangan_kerja_id;

            BiodataKaryawan::where('nik',$request->edit_nik)->update([
                'rekening' => $request->edit_rekening
            ]);

            $operator_karyawan_harian->update($input);

            if($operator_karyawan_harian){
                $message_title="Berhasil !";
                $message_content="NIK ".$input['nik']." - ".$request['nama_karyawan']." Berhasil Diupdate";
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

    public function karyawan_operator_harian_hapus($id)
    {
        $karyawan_operator_harian = KaryawanOperatorHarian::find($id);
        if(empty($karyawan_operator_harian)){
            return response()->json([
                'success' => false,
                'message_title' => 'Gagal',
                'message_content' => 'Data tidak ditemukan'
            ]);
        }
        $karyawan_operator_harian->delete();
        return response()->json([
            'success' => true,
            'message_title' => 'Berhasil',
            'message_content' => 'Data Berhasil Dihapus'
        ]);
    }

    public function karyawan_operator_supir_rit(Request $request)
    {
        if ($request->ajax()) {
            if (auth()->user()->roles != 1) {
                $data = RitKaryawan::where('status','y')->get();
            }else{
                $data = RitKaryawan::all();
            }

            return DataTables::of($data)
                            ->addIndexColumn()
                            ->addColumn('tunjangan_kerja_id', function($row){
                                if($row->tunjangan_kerja_id == 1){
                                    return $row->tunjangan_kerja->golongan;
                                }elseif($row->tunjangan_kerja_id == 2){
                                    return $row->tunjangan_kerja->golongan;
                                }elseif($row->tunjangan_kerja_id == 3){
                                    return $row->tunjangan_kerja->golongan;
                                }elseif($row->tunjangan_kerja_id == 4){
                                    return $row->tunjangan_kerja->golongan;
                                }elseif($row->tunjangan_kerja_id == 5){
                                    return $row->tunjangan_kerja->golongan;
                                }
                                else{
                                    return 0;
                                }
                            })
                            ->addColumn('nominal_tunjangan_kerja', function($row){
                                if (empty($row->tunjangan_kerja)) {
                                    return '-';
                                }else{
                                    return 'Rp. '.number_format($row->tunjangan_kerja->nominal,0,',','.');
                                }
                            })
                            ->addColumn('nama_karyawan', function($row){
                                $biodata_karyawan = BiodataKaryawan::where('nik',$row->nik)->first();
                                // $biodata_karyawan = BiodataKaryawan::where('nik','1611778')->first();
                                // dd($biodata_karyawan);
                                if(empty($biodata_karyawan)){
                                    return '-';
                                }else{
                                    return $biodata_karyawan->nama;
                                }
                            })
                            ->addColumn('rit_posisi_id', function($row){
                                return $row->rit_posisi->kode_posisi.' - '.$row->rit_posisi->nama_posisi;
                            })
                            ->addColumn('status', function($row){
                                if ($row->status == 'y') {
                                    return '<span class="text-success">Aktif</span>';
                                }elseif ($row->status == 'n') {
                                    return '<span class="text-danger">Tidak Aktif</span>';
                                }
                            })
                            ->addColumn('action', function($row){
                                $btn = '<button type="button" onclick="edit(`'.$row->id.'`)" class="btn btn-warning btn-icon">
                                            <i class="fa fa-edit"></i>
                                        </button>';
                                if (auth()->user()->roles == 1) {
                                $btn = $btn.'<button type="button" onclick="hapus(`'.$row->id.'`)" class="btn btn-danger btn-icon">
                                            <i class="fa fa-trash"></i>
                                        </button>';
                                }
                                return $btn;
                            })
                            ->rawColumns(['action','status'])
                            ->make(true);
        }
        $data['rit_posisis'] = RitPosisi::all();
        $data['tunjangan_kerjas'] = TunjanganKerja::all();
        $data['user_management'] = UserManagement::where('user_id',auth()->user()->id)->first();
        return view('backend.payrol.operator_karyawan_supir_rit.index',$data);
    }

    public function karyawan_operator_supir_rit_simpan(Request $request)
    {
        $rules = [
            // 'nik' => 'required|unique:operator_karyawan',
            'nik' => 'required',
            'rit_posisi_id' => 'required',
            'tunjangan_kerja_id' => 'required',
            'jht' => 'required',
            'bpjs' => 'required',
        ];

        $messages = [
            'nik.required'  => 'NIK Karyawan wajib diisi.',
            // 'nik.unique'  => 'NIK Karyawan sudah terpakai.',
            'nama_karyawan.required'  => 'Nama Karyawan wajib diisi.',
            'rit_posisi_id.required'  => 'Posisi RIT wajib diisi.',
            'tunjangan_kerja_id.required'  => 'Tunjangan Kerja wajib diisi.',
            'jht.required'  => 'JHT wajib diisi.',
            'bpjs.required'  => 'BPJS wajib diisi.',
        ];

        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->passes()) {
            $input = $request->all();
            $norut = RitKaryawan::max('id');
            if(empty($norut)){
                $id = 1;
            }else{
                $id = $norut+1;
            }
            $input['id'] = $id;
            $input['status'] = 'y';
            $operator_karyawan_supir_rit = RitKaryawan::create($input);

            if($operator_karyawan_supir_rit){
                $message_title="Berhasil !";
                $message_content="NIK ".$input['nik']." - ".$input['nama_karyawan']." Berhasil Dibuat";
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

    public function karyawan_operator_supir_rit_detail($id)
    {
        $karyawan_operator_supir_rit = RitKaryawan::find($id);
        $biodata_karyawan = BiodataKaryawan::select('rekening')->where('nik',$karyawan_operator_supir_rit->nik)->first();
        
        if(empty($karyawan_operator_supir_rit)){
            return response()->json([
                'success' => false,
                'message' => 'Data tidak ditemukan'
            ]);
        }
        return response()->json([
            'success' => true,
            'data' => [
                'id' => $karyawan_operator_supir_rit->id,
                'nik' => $karyawan_operator_supir_rit->nik,
                'rekening' => $biodata_karyawan->rekening,
                'rit_posisi_id' => $karyawan_operator_supir_rit->rit_posisi_id,
                'tunjangan_kerja_id' => $karyawan_operator_supir_rit->tunjangan_kerja_id,
                'upah_dasar' => $karyawan_operator_supir_rit->upah_dasar,
                'jht' => $karyawan_operator_supir_rit->jht,
                'bpjs' => $karyawan_operator_supir_rit->bpjs,
                'status' => $karyawan_operator_supir_rit->status,
            ]
        ]);
    }

    public function karyawan_operator_supir_rit_update(Request $request)
    {
        $rules = [
            // 'edit_nik' => 'required|unique:operator_karyawan',
            // 'edit_nama_karyawan' => 'required',
            'edit_jht' => 'required',
            'edit_bpjs' => 'required',
            'edit_tunjangan_kerja_id' => 'required',
            'edit_status' => 'required',
            'edit_rekening' => 'required',
        ];

        $messages = [
            // 'edit_nik.required'  => 'NIK Karyawan wajib diisi.',
            // 'edit_nik.unique'  => 'NIK Karyawan sudah terpakai.',
            // 'edit_nama_karyawan.required'  => 'Nama Karyawan wajib diisi.',
            'edit_jht.required'  => 'JHT wajib diisi.',
            'edit_bpjs.required'  => 'BPJS wajib diisi.',
            'edit_tunjangan_kerja_id.required'  => 'Golongan Tunjangan Kerja wajib diisi.',
            'edit_status.required'  => 'Status wajib diisi.',
            'edit_rekening.required'  => 'No.Rekening wajib diisi.',
        ];

        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->passes()) {
            // $input = $request->all();
            $operator_karyawan_supir_rit = RitKaryawan::find($request->edit_id);

            $input['nik'] = $request->edit_nik;
            $input['upah_dasar'] = $request->edit_upah_dasar;
            $input['rit_posisi_id'] = $request->edit_rit_posisi_id;
            $input['jht'] = $request->edit_jht;
            $input['bpjs'] = $request->edit_bpjs;
            $input['status'] = $request->edit_status;
            $input['tunjangan_kerja_id'] = $request->edit_tunjangan_kerja_id;

            BiodataKaryawan::where('nik',$request->edit_nik)->update([
                'rekening' => $request->edit_rekening
            ]);
            
            $operator_karyawan_supir_rit->update($input);

            if($operator_karyawan_supir_rit){
                $message_title="Berhasil !";
                $message_content="NIK ".$input['nik']." - ".$request['nama_karyawan']." Berhasil Diupdate";
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

    public function karyawan_operator_supir_rit_hapus($id)
    {
        $karyawan_operator_supir_rit = RitKaryawan::find($id);
        if(empty($karyawan_operator_supir_rit)){
            return response()->json([
                'success' => false,
                'message_title' => 'Gagal',
                'message_content' => 'Data tidak ditemukan'
            ]);
        }
        $karyawan_operator_supir_rit->delete();
        return response()->json([
            'success' => true,
            'message_title' => 'Berhasil',
            'message_content' => 'Data Berhasil Dihapus'
        ]);
    }
}
