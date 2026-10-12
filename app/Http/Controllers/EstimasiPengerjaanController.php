<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;

use App\Models\NewDataPengerjaan;
use App\Models\MasterCase;
use App\Models\EstimasiRkhBorongan;

use App\Models\BPJSKesehatan;
use App\Models\UMKBoronganLokal;
use App\Models\UMKBoronganEkspor;

use \Carbon\Carbon;

use DataTables;

class EstimasiPengerjaanController extends Controller
{
    function __construct(
        NewDataPengerjaan $newDataPengerjaan,
        MasterCase $masterCase,
        EstimasiRkhBorongan $estimasiRkhBorongan,
        UMKBoronganLokal $umkBoronganLokal,
        UMKBoronganEkspor $umkBoronganEkspor,
        BPJSKesehatan $bpjsKesehatan,
    ){
        $this->newDataPengerjaan = $newDataPengerjaan;
        $this->masterCase = $masterCase;
        $this->estimasiRkhBorongan = $estimasiRkhBorongan;
        $this->umkBoronganLokal = $umkBoronganLokal;
        $this->umkBoronganEkspor = $umkBoronganEkspor;
        $this->bpjsKesehatan = $bpjsKesehatan;
    }

    public function estimasi_pengerjaan_borongan_index(Request $request)
    {
        if ($request->ajax()) {
            $data = $this->newDataPengerjaan->where('status','y')->get();
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
                                $btn_jenis_operator = '';
                                if($explode_jenis_operator[0] == 'PB'){
                                    $btn_jenis_operator = '<div class="button-items">';
                                    $btn_jenis_operator .= '<a href='.route("estimasi.pengerjaan.borongan.packing",["kode_pengerjaan" => $row->kode_pengerjaan]).' class="btn btn-primary">Packing</a>';
                                    $btn_jenis_operator .= '<a class="btn btn-primary">Ambri</a>';
                                    $btn_jenis_operator .= '</div>';
                                }
                                return $btn_jenis_operator;
                            })
                            ->rawColumns(['status','jenis_kerja'])
                            ->make(true);
        }

        return view('backend.estimasi.pengerjaan.borongan.index');
    }

    public function estimasi_pengerjaan_borongan_packing($kode_pengerjaan)
    {
        $data['kode_pengerjaan'] = $kode_pengerjaan;
        $data['new_data_pengerjaan'] = $this->newDataPengerjaan->where('kode_pengerjaan',$kode_pengerjaan)->first();
        $data['explode_tanggal_pengerjaans'] = array_filter(explode('#',$data['new_data_pengerjaan']['tanggal']));
        // dd($data);
        $data['masterCases'] = $this->masterCase->whereYear('created_at',explode('_',$kode_pengerjaan)[1])->get();

        return view('backend.estimasi.pengerjaan.borongan.packing.detail',$data);
    }

    public function estimasi_pengerjaan_borongan_buat_packing($kode_pengerjaan,$date)
    {
        $data['date'] = $date;
        $data['kode_pengerjaan'] = $kode_pengerjaan;
        
        $data['estimasiRkhBorongans'] = $this->estimasiRkhBorongan->where('work_date',$date)->get();

        $umkBoronganEksport = $this->umkBoronganEkspor->selectRaw(
                                                        '
                                                        jenis_produk,
                                                        0 as umk_packing,
                                                        0 as umk_bandrol,
                                                        0 as umk_inner,
                                                        0 as umk_outer,
                                                        umk_packing as umk_packing_export,
                                                        umk_kemas as umk_kemas_export,
                                                        master_case.pouch_packing as pouch_packing,
                                                        master_case.pouch_bandrol as pouch_bandrol,
                                                        master_case.pouch_inner as pouch_inner,
                                                        master_case.pouch_outer as pouch_outer
                                                        ',
                                                        // 'umk_packing'
                                                        // 'NULL as umk_bandrol'
                                                    )
                                                    ->leftJoin('master_case','master_case.name','=','borongan_umk_ekspor.jenis_produk')
                                                    ->where('tahun_aktif',Carbon::create($date)->format('Y'));

        $data['umkBoronganLokals'] = $this->umkBoronganLokal->selectRaw(
                                                                '
                                                                jenis_produk,
                                                                umk_packing,
                                                                umk_bandrol,
                                                                umk_inner,
                                                                umk_outer,
                                                                0 as umk_packing_export,
                                                                0 as umk_kemas_export,
                                                                master_case.pouch_packing as pouch_packing,
                                                                master_case.pouch_bandrol as pouch_bandrol,
                                                                master_case.pouch_inner as pouch_inner,
                                                                master_case.pouch_outer as pouch_outer
                                                                ',
                                                                // 'umk_bandrol'
                                                            )
                                                            ->leftJoin('master_case','master_case.name','=','borongan_umk_lokal.jenis_produk')
                                                            ->union($umkBoronganEksport)
                                                            ->where('tahun_aktif',Carbon::create($date)->format('Y'))
                                                            ->get();

                                                            // dd($data);

        $data['bpjsKesehatan'] = $this->bpjsKesehatan->select('nominal')->where('tahun',Carbon::create($date)->format('Y'))->first();

        return view('backend.estimasi.pengerjaan.borongan.packing.buat',$data);
    }

    public function estimasi_pengerjaan_borongan_simpan_packing(Request $request, $kode_pengerjaan, $date)
    {
        // dd($request->all());
        $dataAll = [];
        foreach ($request->rkh as $key => $value) {
            $dataAll[] = [
                'rkh' => $value,
                'work_date' => $date,
                'master_case' => $request['mc'][$key],
                'pouch' => $request['pouch'][$key],
                'umk_packing' => $request['umk_packing'][$key],
                'price_packing' => $request['price_packing'][$key],
                'umk_bandrol' => $request['umk_bandrol'][$key],
                'price_bandrol' => $request['price_bandrol'][$key],
                'umk_inner' => $request['umk_inner'][$key],
                'price_inner' => $request['price_inner'][$key],
                'umk_outer' => $request['umk_outer'][$key],
                'price_outer' => $request['price_outer'][$key],
                'etiket' => $request['jml_etiket'][$key],
                'umk_etiket' => $request['umk_etiket'][$key],
                'price_etiket' => $request['price_etiket'][$key],
                'umk_export_packing' => $request['umk_export_packing'][$key],
                'price_export_packing' => $request['price_export_packing'][$key],
                'umk_export_kemas' => $request['umk_export_kemas'][$key],
                'price_export_kemas' => $request['price_export_kemas'][$key],
            ];

            $estimasi_rkh_borongan = $this->estimasiRkhBorongan->where('rkh',$value)
                                                                ->where('work_date',$date)
                                                                ->first();

            if (empty($estimasi_rkh_borongan)) {
                $this->estimasiRkhBorongan->create([
                    'rkh' => $value,
                    'work_date' => $date,
                    'master_case' => $request['mc'][$key],
                    'pouch' => $request['pouch'][$key],
                    'umk_packing' => $request['umk_packing'][$key],
                    'price_packing' => round($request['price_packing'][$key]),
                    'umk_bandrol' => $request['umk_bandrol'][$key],
                    'price_bandrol' => round($request['price_bandrol'][$key]),
                    'umk_inner' => $request['umk_inner'][$key],
                    'price_inner' => round($request['price_inner'][$key]),
                    'umk_outer' => $request['umk_outer'][$key],
                    'price_outer' => round($request['price_outer'][$key]),
                    'etiket' => $request['jml_etiket'][$key],
                    'umk_etiket' => $request['umk_etiket'][$key],
                    'price_etiket' => round($request['price_etiket'][$key]),
                    'umk_export_packing' => $request['umk_export_packing'][$key],
                    'price_export_packing' => round($request['price_export_packing'][$key]),
                    'umk_export_kemas' => $request['umk_export_kemas'][$key],
                    'price_export_kemas' => round($request['price_export_kemas'][$key]),
                ]);
            }else{
                $estimasi_rkh_borongan->update([
                    'rkh' => $value,
                    'work_date' => $date,
                    'master_case' => $request['mc'][$key],
                    'pouch' => $request['pouch'][$key],
                    'umk_packing' => $request['umk_packing'][$key],
                    'price_packing' => round($request['price_packing'][$key]),
                    'umk_bandrol' => $request['umk_bandrol'][$key],
                    'price_bandrol' => round($request['price_bandrol'][$key]),
                    'umk_inner' => $request['umk_inner'][$key],
                    'price_inner' => round($request['price_inner'][$key]),
                    'umk_outer' => $request['umk_outer'][$key],
                    'price_outer' => round($request['price_outer'][$key]),
                    'etiket' => $request['jml_etiket'][$key],
                    'umk_etiket' => $request['umk_etiket'][$key],
                    'price_etiket' => round($request['price_etiket'][$key]),
                    'umk_export_packing' => $request['umk_export_packing'][$key],
                    'price_export_packing' => round($request['price_export_packing'][$key]),
                    'umk_export_kemas' => $request['umk_export_kemas'][$key],
                    'price_export_kemas' => round($request['price_export_kemas'][$key]),
                ]);
            }
        }

        // $cek_estimasi_rkh_borongan = $this->estimasiRkhBorongan->where('')

        return redirect()->back();
        // dd($dataAll);
    }
}
