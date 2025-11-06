<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;

use App\Models\RitUMK;
use App\Models\RitPosisi;
use App\Models\RitKendaraan;
use App\Models\RitTujuan;

use \Carbon\Carbon;
use Validator;
use DataTables;

class JenisUMKSupirRitController extends Controller
{
    function __construct(
        RitUMK $ritUMK,
        RitPosisi $ritPosisi,
        RitTujuan $ritTujuan,
        RitKendaraan $ritKendaraan
    ){
        $this->ritUMK = $ritUMK;
        $this->ritPosisi = $ritPosisi;
        $this->ritTujuan = $ritTujuan;
        $this->ritKendaraan = $ritKendaraan;
    }

    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = $this->ritUMK->all();

            return DataTables::of($data)
                            ->addIndexColumn()
                            ->addColumn('rit_posisi', function($row){
                                if (empty($row->rit_posisi->nama_posisi)) {
                                    return '-';
                                }
                                return $row->rit_posisi->nama_posisi;
                            })
                            ->addColumn('rit_kendaraan', function($row){
                                if (empty($row->rit_kendaraan->jenis_kendaraan)) {
                                    return '-';
                                }
                                return $row->rit_kendaraan->jenis_kendaraan;
                            })
                            ->addColumn('rit_tujuan', function($row){
                                if (empty($row->rit_tujuan->tujuan)) {
                                    return '-';
                                }
                                return $row->rit_tujuan->tujuan;
                            })
                            ->addColumn('tarif', function($row){
                                if (empty($row->tarif)) {
                                    return number_format(0,0,',','.');
                                }
                                return 'Rp. '.number_format($row->tarif,0,',','.');
                            })
                            ->addColumn('status', function($row){
                                if($row->status == 'y'){
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
                            ->rawColumns(['packing','bandrol','inner','outer','action','status'])
                            ->make(true);
        }

        $data['rit_umks'] = $this->ritUMK->where('status','y')->get();
        $data['rit_posisis'] = $this->ritPosisi->get();
        $data['rit_tujuans'] = $this->ritTujuan->get();
        $data['rit_kendaraans'] = $this->ritKendaraan->get();

        return view('backend.umk_supir_rit.index',$data);
    }

    public function simpan(Request $request)
    {
        $input = $request->all();
        
        $norut = $this->ritUMK->max('id');
        
        if(empty($norut)){
            $id = 1;
        }else{
            $id = $norut+1;
        }

        // dd($request->no);
        $data = [];

        $this->ritUMK->where('status','y')->update([
            'status' => 't'
        ]);

        foreach ($request->no as $key => $value) {
            // $this->ritUMK->firstOrCreate([
            //     'kategori_upah' => $request['kategori_upah'][$key],
            //     'tahun_aktif' => $request['tahun_aktif'][$key],
            // ],[
            //     'id' => $id+1,
            //     'rit_posisi_id' => $request['rit_posisi_id'][$key],
            //     'rit_kendaraan_id' => $request['rit_kendaraan_id'][$key],
            //     'rit_tujuan_id' => $request['rit_tujuan_id'][$key],
            //     'tarif' => $request['tarif'][$key],
            //     'tahun_aktif' => $request['tahun_aktif'][$key],
            // ]);

            if (empty($request['tarif'][$key])) {
                $tarif = 0;
            }else{
                $tarif = $request['tarif'][$key];
            }

            $this->ritUMK->create([
                'id' => $id+$key,
                'kategori_upah' => $request['kategori_upah'][$key],
                'rit_posisi_id' => $request['rit_posisi_id'][$key],
                'rit_kendaraan_id' => $request['rit_kendaraan_id'][$key],
                'rit_tujuan_id' => $request['rit_tujuan_id'][$key],
                'tarif' => $tarif,
                'tahun_aktif' => $request['tahun_aktif'][$key],
                'status' => 'y'
            ]);
            // $data[] = [
            //     'id' => $id+$key,
            //     'kategori_upah' => $request['kategori_upah'][$key],
            //     'rit_posisi_id' => $request['rit_posisi_id'][$key],
            //     'rit_kendaraan_id' => $request['rit_kendaraan_id'][$key],
            //     'rit_tujuan_id' => $request['rit_tujuan_id'][$key],
            //     'tarif' => $tarif,
            //     'tahun_aktif' => $request['tahun_aktif'][$key],
            //     'status' => 'y'
            // ];
        }

        // dd($data);
        return response()->json([
            'success' => true,
            'message_title' => 'Berhasil',
            'message_content' => 'UMK RIT Berhasil Disimpan'
        ]);
        // dd($data);
    }

    public function detail($id)
    {
        $rit_umk = $this->ritUMK->find($id);
        
        if (empty($rit_umk)) {
            return response()->json([
                'success' => false,
                'message_title' => 'Gagal',
                'message_content' => 'Data RIT UMK Tidak Ditemukan'
            ]);
        }

        return response()->json([
            'success' => true,
            'data' => $rit_umk
        ]);
    }

    public function update(Request $request)
    {
        $rules = [
            'kategori_upah' => 'required',
            'rit_posisi_id' => 'required',
            'rit_kendaraan_id' => 'required',
            'rit_tujuan_id' => 'required',
            'tarif' => 'required',
            'tahun_aktif' => 'required',
            'status' => 'required',
        ];

        $messages = [
            'kategori_upah.required'  => 'Kategori Upah wajib diisi.',
            'rit_posisi_id.required'  => 'Rit Posisi wajib diisi.',
            'rit_kendaraan_id.required'  => 'Rit Kendaraan wajib diisi.',
            'rit_tujuan_id.required'  => 'Rit Tujuan wajib diisi.',
            'tarif.required'  => 'Tarif wajib diisi.',
            'tahun_aktif.required'  => 'Tahun Aktif wajib diisi.',
            'status.required'  => 'Status wajib diisi.',
        ];

        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->passes()) {
            $input = $request->all();
            $ritUMK = $this->ritUMK->find($request->id);
            
            if (empty($ritUMK)) {
                return response()->json([
                    'success' => false,
                    'message_title' => 'Gagal',
                    'message_content' => 'RIT UMK Tidak Ditemukan'
                ]);
            }

            $ritUMK->update($input);

            if ($ritUMK) {
                $message_title="Berhasil !";
                $message_content="RIT UMK ".$request->kategori_upah." Berhasil Diupdate";
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

    public function delete($id)
    {
        $ritUMK = $this->ritUMK->find($id);
        if (empty($ritUMK)) {
            return response()->json([
                'success' => false,
                'message_title' => 'Gagal',
                'message_content' => 'RIT UMK Tidak Ditemukan'
            ]);
        }

        $ritUMK->delete();

        return response()->json([
            'success' => true,
            'message_title' => 'Berhasil',
            'message_content' => 'RIT UMK Berhasil Dihapus'
        ]);
    }

}
