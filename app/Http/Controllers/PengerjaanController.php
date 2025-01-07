<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Models\NewDataPengerjaan;
use App\Models\JenisOperator;
use App\Models\JenisOperatorDetail;
use App\Models\JenisOperatorDetailPengerjaan;
use App\Models\KaryawanOperator;
use App\Models\KaryawanOperatorHarian;
use App\Models\RitKaryawan;
use App\Models\BiodataKaryawan;

use App\Models\Pengerjaan;
// use App\Models\PengerjaanDetail;
use App\Models\PengerjaanWeekly;
use App\Models\PengerjaanHarian;
use App\Models\PengerjaanRITHarian;
use App\Models\PengerjaanRITWeekly;

use App\Models\TunjanganKerja;
use App\Models\UMKBoronganLokal;
use App\Models\UMKBoronganEkspor;
use App\Models\UMKBoronganAmbri;

use App\Models\RitUMK;

use App\Models\BPJSJHT;
use App\Models\BPJSKesehatan;

use App\Models\LogPosisi;
use App\Models\PresensiInfo;
use App\Models\KeluarMasuk;
use App\Models\FtmAttLog;

use \Carbon\Carbon;
use DB;
use DateTime;
use Validator;
use DataTables;

class PengerjaanController extends Controller
{
    protected $newDataPengerjaan;
    protected $jenisOperator;
    protected $jenisOperatorDetail;
    protected $jenisOperatorDetailPengerjaan;
    protected $karyawanOperator;
    protected $karyawanOperatorHarian;
    protected $ritKaryawan;
    protected $biodataKaryawan;
    protected $pengerjaan;
    protected $pengerjaanWeekly;
    protected $pengerjaanHarian;
    protected $pengerjaanRitHarian;
    protected $pengerjaanRitWeekly;
    protected $tunjanganKerja;
    protected $umkBoronganLokal;
    protected $umkBoronganEkspor;
    protected $umkBoronganAmbri;
    protected $ritUmk;
    protected $bpjsJht;
    protected $bpjsKesehatan;
    protected $logPosisi;
    protected $presensiInfo;
    protected $keluarMasuk;
    protected $ftmAttLog;

    function __construct(
        NewDataPengerjaan $newDataPengerjaan,
        JenisOperator $jenisOperator,
        JenisOperatorDetail $jenisOperatorDetail,
        JenisOperatorDetailPengerjaan $jenisOperatorDetailPengerjaan,
        KaryawanOperator $karyawanOperator,
        KaryawanOperatorHarian $karyawanOperatorHarian,
        RitKaryawan $ritKaryawan,
        BiodataKaryawan $biodataKaryawan,
        Pengerjaan $pengerjaan,
        PengerjaanWeekly $pengerjaanWeekly,
        PengerjaanHarian $pengerjaanHarian,
        PengerjaanRITHarian $pengerjaanRitHarian,
        PengerjaanRITWeekly $pengerjaanRitWeekly,
        TunjanganKerja $tunjanganKerja,
        UMKBoronganLokal $umkBoronganLokal,
        UMKBoronganEkspor $umkBoronganEkspor,
        UMKBoronganAmbri $umkBoronganAmbri,
        RitUMK $ritUmk,
        BPJSJHT $bpjsJht,
        BPJSKesehatan $bpjsKesehatan,
        LogPosisi $logPosisi,
        PresensiInfo $presensiInfo,
        KeluarMasuk $keluarMasuk,
        FtmAttLog $ftmAttLog
    ){
        $this->newDataPengerjaan = $newDataPengerjaan;
        $this->jenisOperator = $jenisOperator;
        $this->jenisOperatorDetail = $jenisOperatorDetail;
        $this->jenisOperatorDetailPengerjaan = $jenisOperatorDetailPengerjaan;
        $this->karyawanOperator = $karyawanOperator;
        $this->karyawanOperatorHarian = $karyawanOperatorHarian;
        $this->ritKaryawan = $ritKaryawan;
        $this->biodataKaryawan = $biodataKaryawan;
        $this->pengerjaan = $pengerjaan;
        $this->pengerjaanWeekly = $pengerjaanWeekly;
        $this->pengerjaanHarian = $pengerjaanHarian;
        $this->pengerjaanRitHarian = $pengerjaanRitHarian;
        $this->pengerjaanRitWeekly = $pengerjaanRitWeekly;
        $this->tunjanganKerja = $tunjanganKerja;
        $this->umkBoronganLokal = $umkBoronganLokal;
        $this->umkBoronganEkspor = $umkBoronganEkspor;
        $this->umkBoronganAmbri = $umkBoronganAmbri;
        $this->ritUmk = $ritUmk;
        $this->bpjsJht = $bpjsJht;
        $this->bpjsKesehatan = $bpjsKesehatan;
        $this->logPosisi = $logPosisi;
        $this->presensiInfo = $presensiInfo;
        $this->keluarMasuk = $keluarMasuk;
        $this->ftmAttLog = $ftmAttLog;
    }
    public function index()
    {
        $year = Carbon::now()->format('Y');
        $new_data_pengerjaan = $this->newDataPengerjaan->where('status','y')->get();

        if (!$new_data_pengerjaan->isEmpty()) {
            return redirect()->route('pengerjaan.hasil_kerja');
        }

        $data['data_new_pengerjaan_awal'] = [
            [
                'kode_payrol' => 'PB_'.$year.'_0000'
            ],
            [
                'kode_payrol' => 'PH_'.$year.'_0000'
            ],
            [
                'kode_payrol' => 'PS_'.$year.'_0000'
            ],
        ];
        $data['data_new_pengerjaans'] = $this->newDataPengerjaan->where('kode_pengerjaan','LIKE','%'.$year.'%')
                                                        ->where('status','n')
                                                        ->orderBy('id','desc')
                                                        ->take(3)
                                                        ->get();
        // dd($data_new_pengerjaan);
        return view('backend.new_data_pengerjaan.index',$data);

    }

    public function simpan(Request $request)
    {
        $explode_tanggal = explode(',',$request->tanggal);
        foreach ($explode_tanggal as $key => $tanggal) {
            $tanggal_bulans = explode('-',$tanggal);
            $explode_tanggal_bulans[] = '#'.$tanggal_bulans[2].'-'.$tanggal_bulans[1].'-'.$tanggal_bulans[0];
            // $explode_tanggal_bulans[] = '#'.str_replace('0','',$tanggal_bulans[0]).str_replace('0','',$tanggal_bulans[1]);
        }
        $hasil_convert_tanggal_bulan = implode($explode_tanggal_bulans);
        $explode_tanggal_pengerjaan = explode("#",$hasil_convert_tanggal_bulan);
        $hasil_export_tanggal_pengerjaan = array_filter($explode_tanggal_pengerjaan);
        // dd($hasil_export_tanggal_pengerjaan);
        // dd(count($explode_tanggal_bulans));
        foreach ($request->kode_payrol as $key1 => $kp) {
            $no = 0;
            $number_id = $no+1;
            $new_data_pengerjaans = $this->newDataPengerjaan->orderBy('id','desc')->first();

            if (empty($new_data_pengerjaans)) {
                $new_data_pengerjaan = new NewDataPengerjaan();
                $input['id'] = $number_id;
                $input['kode_pengerjaan'] = $kp;
                $input['date'] = Carbon::now()->format('Y-m-d');
                $input['tanggal'] = implode($explode_tanggal_bulans);
                $input['akhir_bulan'] = $request->akhir_bulan;
                $input['status'] = 'y';
                $new_data_pengerjaan->create($input);
            }else{
                $input['id'] = $new_data_pengerjaans->id+1;
                $input['kode_pengerjaan'] = $kp;
                $input['date'] = Carbon::now()->format('Y-m-d');
                $input['tanggal'] = implode($explode_tanggal_bulans);
                $input['akhir_bulan'] = $request->akhir_bulan;
                $input['status'] = 'y';
                $new_data_pengerjaans->create($input);
            }

            // if ($key1 == 1) {
            //     $operator_karyawan_harians = KaryawanOperatorHarian::select([
            //                                                             'operator_harian_karyawan.id as id',
            //                                                             'operator_harian_karyawan.nik as nik',
            //                                                             'operator_harian_karyawan.jenis_operator_id as jenis_operator_id',
            //                                                             'operator_harian_karyawan.jenis_operator_detail_id as jenis_operator_detail_id',
            //                                                             'operator_harian_karyawan.jenis_operator_detail_pekerjaan_id as jenis_operator_detail_pekerjaan_id',
            //                                                             'operator_harian_karyawan.tunjangan_kerja_id as tunjangan_kerja_id',
            //                                                             'operator_harian_karyawan.hari_kerja as hari_kerja',
            //                                                             'operator_harian_karyawan.upah_dasar as upah_dasar',
            //                                                             'operator_harian_karyawan.jht as jht',
            //                                                             'operator_harian_karyawan.bpjs as bpjs',
            //                                                             'operator_harian_karyawan.status as status',
            //                                                         ])
            //                                                         ->leftJoin('itic_emp_new.biodata_karyawan','biodata_karyawan.nik','=','operator_harian_karyawan.nik')
            //                                                         ->where('jenis_operator_detail_id',4)
            //                                                         ->where('status','y')
            //                                                         ->orderBy('jenis_operator_detail_pekerjaan_id','asc')
            //                                                         ->orderBy('biodata_karyawan.nama','asc')
            //                                                         ->get();

            //     foreach($operator_karyawan_harians as $operator_karyawan_harian){
            //         $pengerjaan_harian_weeklys = PengerjaanHarian::orderBy('id','desc')->first();
            //         if (empty($pengerjaan_harian_weeklys)) {
            //             $pengerjaan_harian_weekly = new PengerjaanHarian();
            //             $input_ph['id'] = $number_id;
            //             $input_ph['kode_pengerjaan'] = $kp;
            //             $input_ph['kode_payrol'] = $kp;
            //             $input_ph['operator_harian_karyawan_id'] = $operator_karyawan_harian->id;
            //             $input_ph['upah_dasar'] = $operator_karyawan_harian->upah_dasar;
            //             $input_ph['hari_kerja'] = $operator_karyawan_harian->hari_kerja;
            //             $input_ph['hasil_kerja'] = '0|0|0|0|0|0|';
            //             $pengerjaan_harian_weekly->create($input_ph);
            //         }else{
            //             $input_ph['id'] = $pengerjaan_harian_weeklys->id+1;
            //             $input_ph['kode_pengerjaan'] = $kp;
            //             $input_ph['kode_payrol'] = $kp;
            //             $input_ph['operator_harian_karyawan_id'] = $operator_karyawan_harian->id;
            //             $input_ph['upah_dasar'] = $operator_karyawan_harian->upah_dasar;
            //             $input_ph['hari_kerja'] = $operator_karyawan_harian->hari_kerja;
            //             $input_ph['hasil_kerja'] = '0|0|0|0|0|0|';
            //             $pengerjaan_harian_weeklys->create($input_ph);
            //         }
            //         // $pengerjaan_harian_weekly = PengerjaanHarian::create([
            //         //     'kode_pengerjaan' => $kp,
            //         //     'kode_payrol' => $kp,
            //         //     'operator_harian_karyawan_id' => $operator_karyawan_harian->id,
            //         //     'upah_dasar' => $operator_karyawan_harian->upah_dasar,
            //         //     'hari_kerja' => $operator_karyawan_harian->hari_kerja,
            //         //     'hasil_kerja' => '0|0|0|0|0|0|'
            //         // ]);
            //     }
            // }

            // if ($key1 == 2) {
            //     $operator_karyawan_supir_rits = RitKaryawan::where('status','y')->get();
            //     foreach ($operator_karyawan_supir_rits as $key => $operator_karyawan_supir_rit) {
            //         for ($i=1; $i <=count($explode_tanggal_bulans); $i++) {
            //             $pengerjaan_supir_rit_dailys = PengerjaanRITHarian::orderBy('id','desc')->first();
            //             if (empty($pengerjaan_supir_rit_dailys)) {
            //                 $pengerjaan_supir_rit_daily = new PengerjaanRITHarian();
            //                 $input_rit_daily['id'] = $number_id;
            //                 $input_rit_daily['kode_pengerjaan'] = $kp;
            //                 $input_rit_daily['kode_payrol'] = $kp;
            //                 $input_rit_daily['karyawan_supir_rit_id'] = $operator_karyawan_supir_rit->id;
            //                 $input_rit_daily['tanggal_pengerjaan'] = $hasil_export_tanggal_pengerjaan[$i];
            //                 $pengerjaan_supir_rit_daily->create($input_rit_daily);
            //                 // $no++;
            //             }else{
            //                 $input_rit_daily['id'] = $pengerjaan_supir_rit_dailys->id+1;
            //                 $input_rit_daily['kode_pengerjaan'] = $kp;
            //                 $input_rit_daily['kode_payrol'] = $kp;
            //                 $input_rit_daily['karyawan_supir_rit_id'] = $operator_karyawan_supir_rit->id;
            //                 $input_rit_daily['tanggal_pengerjaan'] = $hasil_export_tanggal_pengerjaan[$i];
            //                 $pengerjaan_supir_rit_dailys->create($input_rit_daily);
            //             }
            //             // $pengerjaan_supir_rit_daily = PengerjaanRITHarian::create([
            //             //     'kode_pengerjaan' => $kp,
            //             //     'kode_payrol' => $kp,
            //             //     'karyawan_supir_rit_id' => $operator_karyawan_supir_rit->id,
            //             //     'tanggal_pengerjaan' => $hasil_export_tanggal_pengerjaan[$i],
            //             // ]);
            //         }

            //         $pengerjaan_supir_rit_weeklys = PengerjaanRITWeekly::orderBy('id','desc')->first();
            //         if (empty($pengerjaan_supir_rit_weeklys)) {
            //             $pengerjaan_supir_rit_weekly = new PengerjaanRITWeekly();
            //             $input_rit_weekly['id'] = $number_id;
            //             $input_rit_weekly['kode_pengerjaan'] = $kp;
            //             $input_rit_weekly['kode_payrol'] = $kp;
            //             $input_rit_weekly['karyawan_supir_rit_id'] = $operator_karyawan_supir_rit->id;
            //             $input_rit_weekly['tunjangan_kerja'] = $operator_karyawan_supir_rit->tunjangan_kerja->nominal;
            //             $pengerjaan_supir_rit_weekly->create($input_rit_weekly);
            //             // $no++;
            //         }else{
            //             $input_rit_weekly['id'] = $pengerjaan_supir_rit_weeklys->id+1;
            //             $input_rit_weekly['kode_pengerjaan'] = $kp;
            //             $input_rit_weekly['kode_payrol'] = $kp;
            //             $input_rit_weekly['karyawan_supir_rit_id'] = $operator_karyawan_supir_rit->id;
            //             $input_rit_weekly['tunjangan_kerja'] = $operator_karyawan_supir_rit->tunjangan_kerja->nominal;
            //             $pengerjaan_supir_rit_weeklys->create($input_rit_weekly);
            //         }
            //         // $pengerjaan_supir_rit_weekly = PengerjaanRITWeekly::create([
            //         //     'kode_pengerjaan' => $kp,
            //         //     'kode_payrol' => $kp,
            //         //     'karyawan_supir_rit_id' => $operator_karyawan_supir_rit->id,
            //         //     // 'upah_dasar' => $operator_karyawan_supir_rit->upah_dasar
            //         // ]);
            //     }
            // }
            // $no++;

            // Testing
            // $kode_karyawan_pengerjaan = substr($kp,0,2).'_'.substr($kp,3,4);
            $kode_karyawan_pengerjaan = substr($kp,0,2);

            if ($kode_karyawan_pengerjaan == 'PH') {
                $operator_karyawan_harians = $this->karyawanOperatorHarian->select([
                                                                    'operator_harian_karyawan.id as id',
                                                                    'operator_harian_karyawan.nik as nik',
                                                                    'operator_harian_karyawan.jenis_operator_id as jenis_operator_id',
                                                                    'operator_harian_karyawan.jenis_operator_detail_id as jenis_operator_detail_id',
                                                                    'operator_harian_karyawan.jenis_operator_detail_pekerjaan_id as jenis_operator_detail_pekerjaan_id',
                                                                    'operator_harian_karyawan.tunjangan_kerja_id as tunjangan_kerja_id',
                                                                    'operator_harian_karyawan.hari_kerja as hari_kerja',
                                                                    'operator_harian_karyawan.upah_dasar as upah_dasar',
                                                                    'operator_harian_karyawan.jht as jht',
                                                                    'operator_harian_karyawan.bpjs as bpjs',
                                                                    'operator_harian_karyawan.status as status',
                                                                ])
                                                                ->leftJoin('itic_emp_new.biodata_karyawan','biodata_karyawan.nik','=','operator_harian_karyawan.nik')
                                                                ->where('jenis_operator_detail_id',4)
                                                                ->where('status','y')
                                                                ->orderBy('jenis_operator_detail_pekerjaan_id','asc')
                                                                ->orderBy('biodata_karyawan.nama','asc')
                                                                ->get();
                foreach ($hasil_export_tanggal_pengerjaan as $key => $etp) {
                    $hasil_kerja_harian[] = "0|";
                }
                foreach($operator_karyawan_harians as $operator_karyawan_harian){
                    $pengerjaan_harian_weeklys = $this->pengerjaanHarian->orderBy('id','desc')->first();
                    // dd(implode($hasil_kerja_harian));
                    if (empty($pengerjaan_harian_weeklys)) {
                        $pengerjaan_harian_weekly = new PengerjaanHarian();
                        $input_ph['id'] = $number_id;
                        $input_ph['kode_pengerjaan'] = $kp;
                        $input_ph['kode_payrol'] = $kp;
                        $input_ph['operator_harian_karyawan_id'] = $operator_karyawan_harian->id;
                        $input_ph['upah_dasar'] = $operator_karyawan_harian->upah_dasar;
                        $input_ph['hari_kerja'] = $operator_karyawan_harian->hari_kerja;
                        // $input_ph['hasil_kerja'] = implode($hasil_kerja_harian);
                        // $input_ph['hasil_kerja'] = '0|0|0|0|0|0|';
                        $pengerjaan_harian_weekly->create($input_ph);
                    }else{
                        $input_ph['id'] = $pengerjaan_harian_weeklys->id+1;
                        $input_ph['kode_pengerjaan'] = $kp;
                        $input_ph['kode_payrol'] = $kp;
                        $input_ph['operator_harian_karyawan_id'] = $operator_karyawan_harian->id;
                        $input_ph['upah_dasar'] = $operator_karyawan_harian->upah_dasar;
                        $input_ph['hari_kerja'] = $operator_karyawan_harian->hari_kerja;
                        // $input_ph['hasil_kerja'] = implode($hasil_kerja_harian);
                        // $input_ph['hasil_kerja'] = '0|0|0|0|0|0|';
                        $pengerjaan_harian_weeklys->create($input_ph);
                    }
                    // $pengerjaan_harian_weekly = PengerjaanHarian::create([
                    //     'kode_pengerjaan' => $kp,
                    //     'kode_payrol' => $kp,
                    //     'operator_harian_karyawan_id' => $operator_karyawan_harian->id,
                    //     'upah_dasar' => $operator_karyawan_harian->upah_dasar,
                    //     'hari_kerja' => $operator_karyawan_harian->hari_kerja,
                    //     // 'hasil_kerja' => implode($hasil_kerja_harian)
                    //     'hasil_kerja' => '0|0|0|0|0|0|'
                    // ]);
                }
                // return 'PH';
            }

            if ($kode_karyawan_pengerjaan == 'PS') {
                $operator_karyawan_supir_rits = $this->ritKaryawan->where('status','y')->get();
                foreach ($operator_karyawan_supir_rits as $key => $operator_karyawan_supir_rit) {
                    for ($i=1; $i <=count($explode_tanggal_bulans); $i++) {
                        $pengerjaan_supir_rit_dailys = $this->pengerjaanRitHarian->orderBy('id','desc')->first();
                        if (empty($pengerjaan_supir_rit_dailys)) {
                            $pengerjaan_supir_rit_daily = new PengerjaanRITHarian();
                            $input_rit_daily['id'] = $number_id;
                            $input_rit_daily['kode_pengerjaan'] = $kp;
                            $input_rit_daily['kode_payrol'] = $kp;
                            $input_rit_daily['karyawan_supir_rit_id'] = $operator_karyawan_supir_rit->id;
                            $input_rit_daily['tanggal_pengerjaan'] = $hasil_export_tanggal_pengerjaan[$i];
                            $pengerjaan_supir_rit_daily->create($input_rit_daily);
                            // $no++;
                        }else{
                            $input_rit_daily['id'] = $pengerjaan_supir_rit_dailys->id+1;
                            $input_rit_daily['kode_pengerjaan'] = $kp;
                            $input_rit_daily['kode_payrol'] = $kp;
                            $input_rit_daily['karyawan_supir_rit_id'] = $operator_karyawan_supir_rit->id;
                            $input_rit_daily['tanggal_pengerjaan'] = $hasil_export_tanggal_pengerjaan[$i];
                            $pengerjaan_supir_rit_dailys->create($input_rit_daily);
                        }
                        // $pengerjaan_supir_rit_daily = PengerjaanRITHarian::create([
                        //     'kode_pengerjaan' => $kp,
                        //     'kode_payrol' => $kp,
                        //     'karyawan_supir_rit_id' => $operator_karyawan_supir_rit->id,
                        //     'tanggal_pengerjaan' => $hasil_export_tanggal_pengerjaan[$i],
                        // ]);
                    }

                    $pengerjaan_supir_rit_weeklys = $this->pengerjaanRitWeekly->orderBy('id','desc')->first();
                    if (empty($pengerjaan_supir_rit_weeklys)) {
                        $pengerjaan_supir_rit_weekly = new PengerjaanRITWeekly();
                        $input_rit_weekly['id'] = $number_id;
                        $input_rit_weekly['kode_pengerjaan'] = $kp;
                        $input_rit_weekly['kode_payrol'] = $kp;
                        $input_rit_weekly['karyawan_supir_rit_id'] = $operator_karyawan_supir_rit->id;
                        $input_rit_weekly['tunjangan_kerja'] = $operator_karyawan_supir_rit->tunjangan_kerja->nominal;
                        $pengerjaan_supir_rit_weekly->create($input_rit_weekly);
                        // $no++;
                    }else{
                        $input_rit_weekly['id'] = $pengerjaan_supir_rit_weeklys->id+1;
                        $input_rit_weekly['kode_pengerjaan'] = $kp;
                        $input_rit_weekly['kode_payrol'] = $kp;
                        $input_rit_weekly['karyawan_supir_rit_id'] = $operator_karyawan_supir_rit->id;
                        $input_rit_weekly['tunjangan_kerja'] = $operator_karyawan_supir_rit->tunjangan_kerja->nominal;
                        $pengerjaan_supir_rit_weeklys->create($input_rit_weekly);
                    }
                    // $pengerjaan_supir_rit_weekly = PengerjaanRITWeekly::create([
                    //     'kode_pengerjaan' => $kp,
                    //     'kode_payrol' => $kp,
                    //     'karyawan_supir_rit_id' => $operator_karyawan_supir_rit->id,
                    //     // 'upah_dasar' => $operator_karyawan_supir_rit->upah_dasar
                    // ]);
                }

                // return 'PS';
            }

            $no++;
            // dd(substr($kp,0,2).'_'.substr($kp,3,4));
        }
        // dd($input);
        // dd(count($explode_tanggal_bulans));

        return redirect()->route('pengerjaan');
    }

    public function karyawan_pengerjaan($kode_pengerjaan,$id,$kode_payrol)
    {
        $data['new_data_pengerjaan'] = $this->newDataPengerjaan->where('kode_pengerjaan',$kode_payrol)->first();
        // dd($data['new_data_pengerjaan']);
        if(empty($data['new_data_pengerjaan'])){
            return redirect()->back();
        }
        $data['id'] = $id;
        $data['kode_pengerjaan'] = $kode_pengerjaan;
        $data['kode_payrol'] = $kode_payrol;
        $data['jenis_operators'] = $this->jenisOperator->where('kode_operator',$kode_pengerjaan)->first();
        $data['jenis_operator_details'] = $this->jenisOperatorDetail->where('jenis_operator_id',$data['jenis_operators']['id'])->first();
        $data['jenis_operator_detail_pekerjaans'] = $this->jenisOperatorDetailPengerjaan->where('jenis_operator_detail_id',$data['id'])->get();
        // dd($data['jenis_operators']);
        return view('backend.pengerjaan.karyawan_pengerjaan',$data);
    }

    public function karyawan_pengerjaan_simpan(Request $request, $kode_pengerjaan, $id, $kode_payrol)
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
        $new_data_pengerjaan = $this->newDataPengerjaan->where('kode_pengerjaan',$kode_payrol)->first();
        $explode_tanggals = explode('#',$new_data_pengerjaan->tanggal);

        for ($i=1; $i <= 11; $i++) {
            if ($request['checkbox_'.$i]) {
                $data_checkbox = $request['checkbox_'.$i];
                $no = 0;
                $number_id = $no+1;
                foreach ($data_checkbox as $key => $checkbox) {
                    foreach ($explode_tanggals as $key => $explode_tanggal) {
                        if($key != 0){
                            $pengerjaans = $this->pengerjaan->orderBy('id','desc')->first();
                            if (empty($pengerjaans)) {
                                $pengerjaan = new Pengerjaan();
                                $input_pengerjaan['id'] = $no+1;
                                $input_pengerjaan['kode_pengerjaan'] = $new_data_pengerjaan->kode_pengerjaan;
                                $input_pengerjaan['kode_payrol'] = substr($request->kode_pengerjaan,0,2).$kode_jenis_operator_detail.'_'.substr($request->kode_pengerjaan,3);
                                $input_pengerjaan['tanggal_pengerjaan'] = $explode_tanggal;
                                $input_pengerjaan['operator_karyawan_id'] = $checkbox;
                                $pengerjaan->create($input_pengerjaan);
                            }else{
                                $input_pengerjaan['id'] = $pengerjaans->id+1;
                                $input_pengerjaan['kode_pengerjaan'] = $new_data_pengerjaan->kode_pengerjaan;
                                $input_pengerjaan['kode_payrol'] = substr($request->kode_pengerjaan,0,2).$kode_jenis_operator_detail.'_'.substr($request->kode_pengerjaan,3);
                                $input_pengerjaan['tanggal_pengerjaan'] = $explode_tanggal;
                                $input_pengerjaan['operator_karyawan_id'] = $checkbox;
                                $pengerjaans->create($input_pengerjaan);
                            }
                            // $pengerjaan = Pengerjaan::create([
                            //                             'kode_pengerjaan' => $new_data_pengerjaan->kode_pengerjaan,
                            //                             'kode_payrol' => substr($request->kode_pengerjaan,0,2).$kode_jenis_operator_detail.'_'.substr($request->kode_pengerjaan,3),
                            //                             'tanggal_pengerjaan' => $explode_tanggal,
                            //                             'operator_karyawan_id' => $checkbox,
                            //                         ]);
                        }
                    }
                    $pengerjaan_weekly = $this->pengerjaanWeekly->orderBy('id','desc')->first();
                    if (empty($pengerjaan_weekly)) {
                        $pengerjaan_weeklys = new PengerjaanWeekly();
                        $input_pengerjaan_weekly['id'] = $no+1;
                        $input_pengerjaan_weekly['kode_pengerjaan'] = $request->kode_pengerjaan;
                        $input_pengerjaan_weekly['kode_payrol'] = substr($request->kode_pengerjaan,0,2).$kode_jenis_operator_detail.'_'.substr($request->kode_pengerjaan,3);
                        $input_pengerjaan_weekly['operator_karyawan_id'] = $checkbox;
                        $pengerjaan_weeklys->create($input_pengerjaan_weekly);
                    }else{
                        $input_pengerjaan_weekly['id'] = $pengerjaan_weekly->id+1;
                        $input_pengerjaan_weekly['kode_pengerjaan'] = $request->kode_pengerjaan;
                        $input_pengerjaan_weekly['kode_payrol'] = substr($request->kode_pengerjaan,0,2).$kode_jenis_operator_detail.'_'.substr($request->kode_pengerjaan,3);
                        $input_pengerjaan_weekly['operator_karyawan_id'] = $checkbox;
                        $pengerjaan_weekly->create($input_pengerjaan_weekly);
                    }
                    // $pengerjaan_weekly = PengerjaanWeekly::create([
                    //     'kode_pengerjaan' => $request->kode_pengerjaan,
                    //     'kode_payrol' => substr($request->kode_pengerjaan,0,2).$kode_jenis_operator_detail.'_'.substr($request->kode_pengerjaan,3),
                    //     'operator_karyawan_id' => $checkbox,
                    // ]);
                }
                $no++;
            }
        }

    //    return redirect()->route('pengerjaan.hasil_kerja');
        return response()->json([
            'success' => true,
            'message_title' => 'Karyawan Pengerjaan Berhasil dibuat'
        ]);
    }

    public function tambah_karyawan_pengerjaan($kode_pengerjaan,$id,$jenis_pekerja_id)
    {
        $data['id'] = $id;
        $data['kode_pengerjaan'] = $kode_pengerjaan;
        $data['jenis_pekerjaan_id'] = $jenis_pekerja_id;

        $data['jenis_operator_detail_pekerjaans'] = $this->jenisOperatorDetailPengerjaan->whereBetween('jenis_operator_detail_id',[1,3])->get();
        // dd($data);
        return view('backend.pengerjaan.popup_tambah_pegawai_borongan',$data);
    }

    public function tambah_karyawan_pengerjaan_simpan(Request $request, $kode_pengerjaan,$id,$jenis_pekerja_id)
    {
        $total_id = $this->karyawanOperator->max('id');
        for ($i=1; $i <= 11 ; $i++) { 
            if ($request['checkbox_'.$i]) {
                $data_checkbox = $request['checkbox_'.$i];
                foreach ($data_checkbox as $key => $checkbox) {
                    $cek_karyawan_operator = $this->karyawanOperator->where('nik',$checkbox)->orderBy('id','desc')->first();
                    $data[] = [
                        'id' => $total_id+1,
                        'nik' => $checkbox,
                        'jenis_operator_id' => 1,
                        'jenis_operator_detail_id' => $id,
                        'jenis_operator_detail_pekerjaan_id' => $jenis_pekerja_id,
                        'jht' => $cek_karyawan_operator->jht,
                        'bpjs' => $cek_karyawan_operator->bpjs,
                        'training' => $cek_karyawan_operator->training,
                        'status' => $cek_karyawan_operator->status,
                    ];
                    $this->karyawanOperator->create([
                        'id' => $total_id+$key+1,
                        'nik' => $checkbox,
                        'jenis_operator_id' => 1,
                        'jenis_operator_detail_id' => $id,
                        'jenis_operator_detail_pekerjaan_id' => $jenis_pekerja_id,
                        'tunjangan_kerja_id' => $cek_karyawan_operator->tunjangan_kerja_id,
                        'jht' => $cek_karyawan_operator->jht,
                        'bpjs' => $cek_karyawan_operator->bpjs,
                        'training' => $cek_karyawan_operator->training,
                        'status' => $cek_karyawan_operator->status,
                    ]);
                }
                // dd($data);
            }
        }

        return response()->json([
            'success' => true,
            'message_title' => 'Berhasil',
            'message_content' => 'Data Berhasil Ditambah'
        ]);
    }

    public function hasil_kerja(Request $request)
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
                        $jenis_operator = $this->jenisOperator->where('kode_operator',$explode_jenis_operator[0])->first();
                        $jenis_operator_details = $this->jenisOperatorDetail->where('jenis_operator_id',$jenis_operator->id)->where('status','y')->get();
                        // dd($jenis_operator_details);
                        $btn_jenis_operator = '';
                        // $btn_jenis_operator = $btn_jenis_operator.='<div class="btn-group">';
                        $btn_jenis_operator = $btn_jenis_operator.='<div class="button-items">';
                        foreach ($jenis_operator_details as $key => $jenis_operator_detail) {
                            if ($jenis_operator_detail->jenis_operator_id == 1) {
                                $jenis_operator_detail_pengerjaans = $this->jenisOperatorDetailPengerjaan->where('jenis_operator_detail_id',$jenis_operator_detail->id)->get();
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
                                $jenis_operator_detail_pengerjaans = $this->jenisOperatorDetailPengerjaan->where('jenis_operator_detail_id',4)->get();
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
        // $data['new_data_pengerjaan'] = NewDataPengerjaan::where('status','n')->orderBy('id','desc')->first();
        return view('backend.pengerjaan.index');
    }

    public function b_hasil_kerja_packing(Request $request)
    {
        if ($request->ajax()) {
            $data = $this->newDataPengerjaan->where('kode_pengerjaan','LIKE','%PB_%')->where('status','n')->get();
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
                        $jenis_operator = $this->jenisOperator->where('kode_operator',$explode_jenis_operator[0])->first();
                        $jenis_operator_details = $this->jenisOperatorDetail->where('jenis_operator_id',$jenis_operator->id)->where('status','y')->get();
                        $btn_jenis_operator = '';
                        $btn_jenis_operator = $btn_jenis_operator.='<div class="button-items">';
                        foreach ($jenis_operator_details as $key => $jenis_operator_detail) {
                            if ($jenis_operator_detail->jenis_operator_id == 1) {
                                $jenis_operator_detail_pengerjaans = $this->jenisOperatorDetailPengerjaan->where('jenis_operator_detail_id',$jenis_operator_detail->id)->get();
                                $btn_jenis_operator.='<div class="btn-group">';
                                $btn_jenis_operator.='<a href="'.route('pengerjaan.karyawan',['kode_pengerjaan' => $jenis_operator->kode_operator,'id' => $jenis_operator_detail->id, 'kode_payrol' => $row->kode_pengerjaan]).'" class="btn btn-outline-primary me-0">'.'Karyawan '.$jenis_operator_detail->jenis_posisi.'</a>';
                                $btn_jenis_operator.='<a type="button" class="btn btn-outline-primary dropdown-toggle dropdown-toggle-split" data-bs-toggle="dropdown" aria-expanded="false">
                                                        <span class="sr-only">Toggle Dropdown</span> <i class="mdi mdi-chevron-down"></i>
                                                    </a>';
                                $btn_jenis_operator.='<div class="dropdown-menu" style="">';
                                foreach ($jenis_operator_detail_pengerjaans as $jenis_operator_detail_pengerjaan) {
                                    $jenis_operator = $jenis_operator_detail_pengerjaan->jenis_operator_detail->jenis_operator;
                                    $btn_jenis_operator.='<a class="dropdown-item" href='.route($jenis_operator_detail_pengerjaan->link,['id' => $jenis_operator_detail_pengerjaan->jenis_operator_detail_id, 'kode_pengerjaan' => $row->kode_pengerjaan]).' target="_blank">'.$jenis_operator_detail_pengerjaan->jenis_posisi_pekerjaan.'</a>';
                                }
                                $btn_jenis_operator.='</div>';
    
                                $btn_jenis_operator.='</div>';
                            }elseif($jenis_operator_detail->jenis_operator_id == 2){
                                $jenis_operator_detail_pengerjaans = $this->jenisOperatorDetailPengerjaan->where('jenis_operator_detail_id',4)->get();
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
                    ->rawColumns(['status','jenis_kerja'])
                    ->make(true);
        }
    }

    public function b_hasil_kerja_harian(Request $request)
    {
        if ($request->ajax()) {
            $data = $this->newDataPengerjaan->where('kode_pengerjaan','LIKE','%PH_%')->where('status','n')->get();
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
                        $jenis_operator = $this->jenisOperator->where('kode_operator',$explode_jenis_operator[0])->first();
                        $jenis_operator_details = $this->jenisOperatorDetail->where('jenis_operator_id',$jenis_operator->id)->where('status','y')->get();
                        $btn_jenis_operator = '';
                        $btn_jenis_operator = $btn_jenis_operator.='<div class="button-items">';
                        foreach ($jenis_operator_details as $key => $jenis_operator_detail) {
                            if ($jenis_operator_detail->jenis_operator_id == 1) {
                                $jenis_operator_detail_pengerjaans = $this->jenisOperatorDetailPengerjaan->where('jenis_operator_detail_id',$jenis_operator_detail->id)->get();
                                $btn_jenis_operator.='<div class="btn-group">';
                                $btn_jenis_operator.='<a href="'.route('pengerjaan.karyawan',['kode_pengerjaan' => $jenis_operator->kode_operator,'id' => $jenis_operator_detail->id, 'kode_payrol' => $row->kode_pengerjaan]).'" class="btn btn-outline-primary me-0">'.'Karyawan '.$jenis_operator_detail->jenis_posisi.'</a>';
                                $btn_jenis_operator.='<a type="button" class="btn btn-outline-primary dropdown-toggle dropdown-toggle-split" data-bs-toggle="dropdown" aria-expanded="false">
                                                        <span class="sr-only">Toggle Dropdown</span> <i class="mdi mdi-chevron-down"></i>
                                                    </a>';
                                $btn_jenis_operator.='<div class="dropdown-menu" style="">';
                                foreach ($jenis_operator_detail_pengerjaans as $jenis_operator_detail_pengerjaan) {
                                    $jenis_operator = $jenis_operator_detail_pengerjaan->jenis_operator_detail->jenis_operator;
                                    $btn_jenis_operator.='<a class="dropdown-item" href='.route($jenis_operator_detail_pengerjaan->link,['id' => $jenis_operator_detail_pengerjaan->jenis_operator_detail_id, 'kode_pengerjaan' => $row->kode_pengerjaan]).' target="_blank">'.$jenis_operator_detail_pengerjaan->jenis_posisi_pekerjaan.'</a>';
                                }
                                $btn_jenis_operator.='</div>';
    
                                $btn_jenis_operator.='</div>';
                            }elseif($jenis_operator_detail->jenis_operator_id == 2){
                                $jenis_operator_detail_pengerjaans = $this->jenisOperatorDetailPengerjaan->where('jenis_operator_detail_id',4)->get();
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
                    ->rawColumns(['status','jenis_kerja'])
                    ->make(true);
        }
    }

    public function b_hasil_kerja_supir(Request $request)
    {
        if ($request->ajax()) {
            $data = $this->newDataPengerjaan->where('kode_pengerjaan','LIKE','%PS_%')->where('status','n')->get();
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
                        $jenis_operator = $this->jenisOperator->where('kode_operator',$explode_jenis_operator[0])->first();
                        $jenis_operator_details = $this->jenisOperatorDetail->where('jenis_operator_id',$jenis_operator->id)->where('status','y')->get();
                        $btn_jenis_operator = '';
                        $btn_jenis_operator = $btn_jenis_operator.='<div class="button-items">';
                        foreach ($jenis_operator_details as $key => $jenis_operator_detail) {
                            if ($jenis_operator_detail->jenis_operator_id == 1) {
                                $jenis_operator_detail_pengerjaans = $this->jenisOperatorDetailPengerjaan->where('jenis_operator_detail_id',$jenis_operator_detail->id)->get();
                                $btn_jenis_operator.='<div class="btn-group">';
                                $btn_jenis_operator.='<a href="'.route('pengerjaan.karyawan',['kode_pengerjaan' => $jenis_operator->kode_operator,'id' => $jenis_operator_detail->id, 'kode_payrol' => $row->kode_pengerjaan]).'" class="btn btn-outline-primary me-0">'.'Karyawan '.$jenis_operator_detail->jenis_posisi.'</a>';
                                $btn_jenis_operator.='<a type="button" class="btn btn-outline-primary dropdown-toggle dropdown-toggle-split" data-bs-toggle="dropdown" aria-expanded="false">
                                                        <span class="sr-only">Toggle Dropdown</span> <i class="mdi mdi-chevron-down"></i>
                                                    </a>';
                                $btn_jenis_operator.='<div class="dropdown-menu" style="">';
                                foreach ($jenis_operator_detail_pengerjaans as $jenis_operator_detail_pengerjaan) {
                                    $jenis_operator = $jenis_operator_detail_pengerjaan->jenis_operator_detail->jenis_operator;
                                    $btn_jenis_operator.='<a class="dropdown-item" href='.route($jenis_operator_detail_pengerjaan->link,['id' => $jenis_operator_detail_pengerjaan->jenis_operator_detail_id, 'kode_pengerjaan' => $row->kode_pengerjaan]).' target="_blank">'.$jenis_operator_detail_pengerjaan->jenis_posisi_pekerjaan.'</a>';
                                }
                                $btn_jenis_operator.='</div>';
    
                                $btn_jenis_operator.='</div>';
                            }elseif($jenis_operator_detail->jenis_operator_id == 2){
                                $jenis_operator_detail_pengerjaans = $this->jenisOperatorDetailPengerjaan->where('jenis_operator_detail_id',4)->get();
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
                    ->rawColumns(['status','jenis_kerja'])
                    ->make(true);
        }
    }

    public function hasil_kerja_packing($id, $kode_pengerjaan)
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

        $data['id'] = $id;
        $data['kode_pengerjaan'] = $kode_pengerjaan;
        // dd($data['id_jenis_operator_pekerjaan']);
        $data['jenis_operator_detail_pekerjaan'] = $this->jenisOperatorDetailPengerjaan->find($id);
        // dd($data['jenis_operator_detail_pekerjaan']);
        $data['new_data_pengerjaan'] = $this->newDataPengerjaan->where('kode_pengerjaan',$kode_pengerjaan)->first();
        $data['explode_tanggal_pengerjaans'] = explode('#',$data['new_data_pengerjaan']['tanggal']);
        $data['count_tanggal'] = count($data['explode_tanggal_pengerjaans'])-1;
        // dd(count($data['explode_tanggal_pengerjaans'])-1);
        // $data['packing_lokals'] = KaryawanOperator::select([
        //                             'operator_karyawan.id as id',
        //                             'operator_karyawan.nik as nik',
        //                             'biodata_karyawan.nama as nama',
        //                             ])
        //                             ->where('jenis_operator_id',1)
        //                             ->where('jenis_operator_detail_id',1)
        //                             ->where('jenis_operator_detail_pekerjaan_id',$id)
        //                             ->leftJoin('itic_emp_new.biodata_karyawan','biodata_karyawan.nik','=','operator_karyawan.nik')
        //                             ->rightJoin('pengerjaan','pengerjaan.operator_karyawan_id','=','operator_karyawan.id')
        //                             // ->with('biodata_karyawan')
        //                             ->orderBy('biodata_karyawan.nama','asc')
        //                             ->get();
        // dd($data['packing_lokals']);
        $data['pengerjaans'] = $this->pengerjaanWeekly->select([
                                                    'pengerjaan_weekly.kode_payrol as kode_payrol',
                                                    'pengerjaan_weekly.operator_karyawan_id as operator_karyawan_id',
                                                    'operator_karyawan.nik as nik',
                                                    'biodata_karyawan.nama as nama',
                                                    'pengerjaan_weekly.upah_dasar as upah_dasar',
                                                    'pengerjaan_weekly.tunjangan_kerja as tunjangan_kerja',
                                                    'pengerjaan_weekly.tunjangan_kehadiran as tunjangan_kehadiran',
                                                    'pengerjaan_weekly.uang_makan as uang_makan',
                                                    'pengerjaan_weekly.plus_1 as plus_1',
                                                    'pengerjaan_weekly.plus_2 as plus_2',
                                                    'pengerjaan_weekly.plus_3 as plus_3',
                                                    'pengerjaan_weekly.minus_1 as minus_1',
                                                    'pengerjaan_weekly.minus_2 as minus_2',
                                                    'pengerjaan_weekly.jht as jht',
                                                    'pengerjaan_weekly.bpjs_kesehatan as bpjs_kesehatan',
                                                ])
                                                ->where('kode_payrol',substr($kode_pengerjaan,0,2).$kode_jenis_operator_detail.'_'.substr($kode_pengerjaan,3))
                                                ->where('operator_karyawan.jenis_operator_id',1)
                                                ->where('operator_karyawan.jenis_operator_detail_id',1)
                                                ->where('operator_karyawan.jenis_operator_detail_pekerjaan_id',1)
                                                ->leftJoin('operator_karyawan','operator_karyawan.id','=','pengerjaan_weekly.operator_karyawan_id')
                                                ->leftJoin('itic_emp_new.biodata_karyawan','biodata_karyawan.nik','=','operator_karyawan.nik')
                                                ->orderBy('biodata_karyawan.nama','asc')
                                                ->get();
        // dd($data['pengerjaans']);
        // $data['pengerjaans'] = Pengerjaan::where('kode_pengerjaan',$kode_pengerjaan)
        //                                 ->where('kode_payrol','LIKE','%%')
        //                                 ->get();
        // dd($data);
        return view('backend.pengerjaan.packing_lokal.packing_lokal',$data);
        // return $kode_pengerjaan.'_'.$id;
        // $data['new_data_pengerjaan'] = NewDataPengerjaan::where('kode_pengerjaan','LIKE','%PB%')->where('status','y')->first();
        // $data['pengerjaans'] = Pengerjaan::where('kode_pengerjaan','LIKE','%PB%')
        //                                 ->leftJoin('operator_karyawan','operator_karyawan.id','=','operator_karyawan_id')
        //                                 ->get();
        // $data['packing_lokals'] = KaryawanOperator::select([
        //                             'operator_karyawan.id as id',
        //                             'operator_karyawan.nik as nik',
        //                             'biodata_karyawan.nama as nama'
        //                             ])
        //                             ->where('jenis_operator_id',1)
        //                             ->where('jenis_operator_detail_id',1)
        //                             ->where('jenis_operator_detail_pekerjaan_id',1)
        //                             ->leftJoin('biodata_karyawan','biodata_karyawan.nik','=','operator_karyawan.nik')
        //                             // ->with('biodata_karyawan')
        //                             ->orderBy('biodata_karyawan.nama','asc')
        //                             ->get();

        // $data['new_data_pengerjaan'] = NewDataPengerjaan::where('kode_pengerjaan','LIKE','%PB%')->where('status','y')->first();
        // $data['explode_tanggals'] = explode('#',$data['new_data_pengerjaan']['tanggal']);
        // dd($explode_tanggal);
        // if ($request->ajax()) {
        //     $data = KaryawanOperator::where('jenis_operator_id',1)
        //                             ->where('jenis_operator_detail_id',1)
        //                             ->where('jenis_operator_detail_pekerjaan_id',1)
        //                             ->get();
        //     return DataTables::of($data)
        //                     ->addIndexColumn()
        //                     ->addColumn('nama_karyawan', function($row){
        //                         $biodata_karyawan = BiodataKaryawan::where('nik',$row->nik)->first();
        //                         // $biodata_karyawan = BiodataKaryawan::where('nik','1611778')->first();
        //                         // dd($biodata_karyawan);
        //                         if(empty($biodata_karyawan)){
        //                             return '-';
        //                         }else{
        //                             return $biodata_karyawan->nama;
        //                         }
        //                     })
        //                     ->addColumn('action', function($row){
        //                         $btn = '<button type="button" onclick="edit(`'.$row->id.'`)" class="btn btn-warning btn-icon">
        //                                     <i class="fa fa-edit"></i>
        //                                 </button>
        //                                 <button type="button" onclick="hapus(`'.$row->id.'`)" class="btn btn-danger btn-icon">
        //                                     <i class="fa fa-trash"></i>
        //                                 </button>';
        //                         return $btn;
        //                     })
        //                     ->rawColumns(['action'])
        //                     ->make(true);
        // }
        // return view('backend.pengerjaan.packing_lokal.packing_lokal',$data);
        // return '-';
    }

    public function hasil_kerja_packing_view_hasil($id, $kode_pengerjaan, $tanggal)
    {
        $data['id'] = $id;
        $data['kode_pengerjaan'] = $kode_pengerjaan;
        $data['tanggal'] = $tanggal;
        $data['packing_lokals'] = $this->karyawanOperator->select([
                                                    'operator_karyawan.id as id',
                                                    'operator_karyawan.nik as nik',
                                                    'biodata_karyawan.nama as nama',
                                                    'pengerjaan.hasil_kerja_1 as hasil_kerja_1',
                                                    'pengerjaan.hasil_kerja_2 as hasil_kerja_2',
                                                    'pengerjaan.hasil_kerja_3 as hasil_kerja_3',
                                                    'pengerjaan.hasil_kerja_4 as hasil_kerja_4',
                                                    'pengerjaan.hasil_kerja_5 as hasil_kerja_5',
                                                    'pengerjaan.total_jam_kerja_1 as total_jam_kerja_1',
                                                    'pengerjaan.total_jam_kerja_2 as total_jam_kerja_2',
                                                    'pengerjaan.total_jam_kerja_3 as total_jam_kerja_3',
                                                    'pengerjaan.total_jam_kerja_4 as total_jam_kerja_4',
                                                    'pengerjaan.total_jam_kerja_5 as total_jam_kerja_5',
                                                    ])
                                                    ->leftJoin('itic_emp_new.biodata_karyawan','biodata_karyawan.nik','=','operator_karyawan.nik')
                                                    ->rightJoin('pengerjaan','pengerjaan.operator_karyawan_id','=','operator_karyawan.id')
                                                    ->where('jenis_operator_id',1)
                                                    ->where('jenis_operator_detail_id',1)
                                                    ->where('jenis_operator_detail_pekerjaan_id',1)
                                                    ->where('pengerjaan.tanggal_pengerjaan',$tanggal)
                                                    ->where('pengerjaan.kode_pengerjaan',$kode_pengerjaan)
                                                    // ->with('biodata_karyawan')
                                                    ->orderBy('biodata_karyawan.nama','asc')
                                                    ->get();

        $data['umk_borongan_lokals'] = $this->umkBoronganLokal->select('id','jenis_produk','umk_packing')->where('status','Y')->get();
        // dd($data);
        return view('backend.pengerjaan.packing_lokal.input_hasil_kerja',$data);
    }

    public function hasil_kerja_packing_view_simpan(Request $request, $id, $kode_pengerjaan, $tanggal)
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

        $input = $request->all();
        $input['id'] = $id;
        $input['kode_pengerjaan'] = $kode_pengerjaan;
        $input['tanggal'] = $tanggal;

        $data['karyawan_pengerjaans'] = $this->pengerjaan->select([
                                                        'pengerjaan.id as id',
                                                        'operator_karyawan.nik as nik',
                                                        'pengerjaan.operator_karyawan_id as id_operator_karyawan'
                                                    ])
                                                    ->leftJoin('operator_karyawan','operator_karyawan.id','=','pengerjaan.operator_karyawan_id')
                                                    ->leftJoin('itic_emp_new.biodata_karyawan','biodata_karyawan.nik','=','operator_karyawan.nik')
                                                    ->where('tanggal_pengerjaan',$tanggal)
                                                    // ->where('kode_pengerjaan',$kode_pengerjaan)
                                                    // ->where('pengerjaan.operator_karyawan_id','operator_karyawan.id')
                                                    ->where('operator_karyawan.jenis_operator_id',1)
                                                    ->where('operator_karyawan.jenis_operator_detail_id',1)
                                                    ->where('operator_karyawan.jenis_operator_detail_pekerjaan_id',1)
                                                    ->where('pengerjaan.kode_pengerjaan',$kode_pengerjaan)
                                                    ->orderBy('biodata_karyawan.nama','asc')
                                                    ->get();
        
        foreach ($data['karyawan_pengerjaans'] as $key => $karyawan_pengerjaan) {
            // dd($request->umk_borongan_lokal_kerja_1);
            if ($request->umk_borongan_lokal_kerja_1) {
                $umk_borongan_lokal_1 = $this->umkBoronganLokal->select('id','jenis_produk','umk_packing')
                                                        ->where('id',$request->umk_borongan_lokal_kerja_1)
                                                        ->where('status','Y')
                                                        ->first();

                // $hasil_pengerjaan_1 = $umk_borongan_lokal_1->id.'|'.$umk_borongan_lokal_1->umk_packing*$request->hasil_kerja_1[$key];
                
                // if ($request->hasil_kerja_1[$key]) {
                // }

                if ($request->hasil_kerja_1[$key]) {
                    $hasil_pengerjaan_1 = $umk_borongan_lokal_1->id.'|'.$request->hasil_kerja_1[$key];
                    $hasil_kerja_1 = $umk_borongan_lokal_1->umk_packing*$request->hasil_kerja_1[$key];
                }else{
                    $hasil_pengerjaan_1 = $umk_borongan_lokal_1->id.'|'.'0';
                    $hasil_kerja_1 = 0;
                }

                if($request->lembur_kerja_1){
                    $lembur_1 = '1-y';
                    $hasil_lembur_1 = '1.5';
                    // $total_upah_lembur_1 = ($hasil_kerja_1*1.5)-$hasil_kerja_1;
                    $total_upah_lembur_1 = $hasil_kerja_1*1.5;
                }else{
                    $lembur_1 = '1-n';
                    $hasil_lembur_1 = '1';
                    $total_upah_lembur_1 = $hasil_kerja_1*0;
                }

                if($request->total_jam_1[$key]){
                    $total_jam_1 = $request->total_jam_1[$key];
                    // $explode_input_total_jam_1_koma = explode(",",$request->total_jam_1[$key]);

                    // if($explode_input_total_jam_1_koma){
                    //     $total_jam_1 = $explode_input_total_jam_1_koma[0].','.$explode_input_total_jam_1_koma[1];
                    //     dd('ok1');
                    // }else{
                    //     // dd($explode_input_total_jam_1_koma);
                    //     dd('ok2');
                    //     $explode_input_total_jam_1 = explode(".",$request->total_jam_1[$key]);
                    //     $total_jam_1 = $explode_input_total_jam_1[0].','.$explode_input_total_jam_1[1];
                    // }
                }else{
                    $total_jam_1 = '0';
                }
                
            }else{
                $hasil_pengerjaan_1 = '0'.'|'.'0';
                $lembur_1 = '1-n';
                $hasil_lembur_1 = '1';
                $total_jam_1 = '0';
                $hasil_kerja_1 = 0;
                $total_upah_lembur_1 = '0';
            }

            $total_hasil_lembur_1 = $total_upah_lembur_1;

            // dd($hasil_pengerjaan_1);
            
            if ($request->umk_borongan_lokal_kerja_2) {
                $umk_borongan_lokal_2 = $this->umkBoronganLokal->select('id','jenis_produk','umk_packing')
                                                        ->where('id',$request->umk_borongan_lokal_kerja_2)
                                                        ->where('status','Y')
                                                        ->first();

                // $hasil_pengerjaan_2 = $umk_borongan_lokal_2->id.'|'.$umk_borongan_lokal_2->umk_packing*$request->hasil_kerja_2[$key];
                
                if ($request->hasil_kerja_2[$key]) {
                    $hasil_pengerjaan_2 = $umk_borongan_lokal_2->id.'|'.$request->hasil_kerja_2[$key];
                    $hasil_kerja_2 = $umk_borongan_lokal_2->umk_packing*$request->hasil_kerja_2[$key];
                }else{
                    $hasil_pengerjaan_2 = $umk_borongan_lokal_2->id.'|'.'0';
                    $hasil_kerja_2 = 0;
                }

                if($request->lembur_kerja_2){
                    $lembur_2 = '2-y';
                    $hasil_lembur_2 = '1.5';
                    // $total_upah_lembur_2 = ($hasil_kerja_◘2*1.5)-$hasil_kerja_2;
                    $total_upah_lembur_2 = $hasil_kerja_2*1.5;
                }else{
                    $lembur_2 = '2-n';
                    $hasil_lembur_2 = '1';
                    $total_upah_lembur_2 = $hasil_kerja_2*0;
                }

                if($request->total_jam_2[$key]){
                    // $explode_input_total_jam_2 = explode(".",$request->total_jam_2[$key]);
                    $total_jam_2 = $request->total_jam_2[$key];
                }else{
                    $total_jam_2 = '0';
                }

            }else{
                $hasil_pengerjaan_2 = '0'.'|'.'0';
                $lembur_2 = '2-n';
                $hasil_lembur_2 = '1';
                $total_jam_2 = '0';
                $hasil_kerja_2 = 0;
                $total_upah_lembur_2 = '0';
            }

            $total_hasil_lembur_2 = $total_upah_lembur_2;

            if ($request->umk_borongan_lokal_kerja_3) {
                $umk_borongan_lokal_3 = $this->umkBoronganLokal->select('id','jenis_produk','umk_packing')
                                                        ->where('id',$request->umk_borongan_lokal_kerja_3)
                                                        ->where('status','Y')
                                                        ->first();

                // $hasil_pengerjaan_3 = $umk_borongan_lokal_3->id.'|'.$umk_borongan_lokal_3->umk_packing*$request->hasil_kerja_3[$key];
                
                if ($request->hasil_kerja_3[$key]) {
                    $hasil_pengerjaan_3 = $umk_borongan_lokal_3->id.'|'.$request->hasil_kerja_3[$key];
                    $hasil_kerja_3 = $umk_borongan_lokal_3->umk_packing*$request->hasil_kerja_3[$key];
                }else{
                    $hasil_pengerjaan_3 = $umk_borongan_lokal_3->id.'|'.'0';
                    $hasil_kerja_3 = 0;
                }
            
                if($request->lembur_kerja_3){
                    $lembur_3 = '3-y';
                    $hasil_lembur_3 = '1.5';
                    // $total_upah_lembur_3 = ($hasil_kerja_3*1.5)-$hasil_kerja_3;
                    $total_upah_lembur_3 = $hasil_kerja_3*1.5;
                }else{
                    $lembur_3 = '3-n';
                    $hasil_lembur_3 = '1';
                    $total_upah_lembur_3 = $hasil_kerja_3*0;
                }

                if($request->total_jam_3[$key]){
                    // $explode_input_total_jam_3 = explode(".",$request->total_jam_3[$key]);
                    $total_jam_3 = $request->total_jam_3[$key];
                }else{
                    $total_jam_3 = '0';
                }

            }else{
                $hasil_pengerjaan_3 = '0'.'|'.'0';
                $lembur_3 = '3-n';
                $hasil_lembur_3 = '1';
                $total_jam_3 = '0';
                $hasil_kerja_3 = 0;
                $total_upah_lembur_3 = '0';
            }

            $total_hasil_lembur_3 = $total_upah_lembur_3;

            if ($request->umk_borongan_lokal_kerja_4) {
                $umk_borongan_lokal_4 = $this->umkBoronganLokal->select('id','jenis_produk','umk_packing')
                                                        ->where('id',$request->umk_borongan_lokal_kerja_4)
                                                        ->where('status','Y')
                                                        ->first();

                // $hasil_pengerjaan_4 = $umk_borongan_lokal_4->id.'|'.$umk_borongan_lokal_4->umk_packing*$request->hasil_kerja_4[$key];
                
                if ($request->hasil_kerja_4[$key]) {
                    $hasil_pengerjaan_4 = $umk_borongan_lokal_4->id.'|'.$request->hasil_kerja_4[$key];
                    $hasil_kerja_4 = $umk_borongan_lokal_4->umk_packing*$request->hasil_kerja_4[$key];
                }else{
                    $hasil_pengerjaan_4 = $umk_borongan_lokal_4->id.'|'.'0';
                    $hasil_kerja_4 = 0;
                }
            
                if($request->lembur_kerja_4){
                    $lembur_4 = '4-y';
                    $hasil_lembur_4 = '1.5';
                    // $total_upah_lembur_4 = ($hasil_kerja_4*1.5)-$hasil_kerja_4;
                    $total_upah_lembur_4 = $hasil_kerja_4*1.5;
                }else{
                    $lembur_4 = '4-n';
                    $hasil_lembur_4 = '1';
                    $total_upah_lembur_4 = $hasil_kerja_4*0;
                }

                if($request->total_jam_4[$key]){
                    // $explode_input_total_jam_4 = explode(".",$request->total_jam_4[$key]);
                    $total_jam_4 = $request->total_jam_4[$key];
                }else{
                    $total_jam_4 = '0';
                }

            }else{
                $hasil_pengerjaan_4 = '0'.'|'.'0';
                $lembur_4 = '4-n';
                $hasil_lembur_4 = '1';
                $total_jam_4 = '0';
                $hasil_kerja_4 = 0;
                $total_upah_lembur_4 = '0';
            }

            $total_hasil_lembur_4 = $total_upah_lembur_4;

            if ($request->umk_borongan_lokal_kerja_5) {
                $umk_borongan_lokal_5 = $this->umkBoronganLokal->select('id','jenis_produk','umk_packing')
                                                        ->where('id',$request->umk_borongan_lokal_kerja_5)
                                                        ->where('status','Y')
                                                        ->first();

                // $hasil_pengerjaan_5 = $umk_borongan_lokal_5->id.'|'.$umk_borongan_lokal_5->umk_packing*$request->hasil_kerja_5[$key];
                
                if ($request->hasil_kerja_5[$key]) {
                    $hasil_pengerjaan_5 = $umk_borongan_lokal_5->id.'|'.$request->hasil_kerja_5[$key];
                    $hasil_kerja_5 = $umk_borongan_lokal_5->umk_packing*$request->hasil_kerja_5[$key];
                }else{
                    $hasil_pengerjaan_5 = $umk_borongan_lokal_5->id.'|'.'0';
                    $hasil_kerja_5 = 0;
                }
            
                if($request->lembur_kerja_5){
                    $lembur_5 = '5-y';
                    $hasil_lembur_5 = '1.5';
                    // $total_upah_lembur_5 = ($hasil_kerja_5*1.5)-$hasil_kerja_5;
                    $total_upah_lembur_5 = $hasil_kerja_5*1.5;
                }else{
                    $lembur_5 = '5-n';
                    $hasil_lembur_5 = '1';
                    $total_upah_lembur_5 = $hasil_kerja_5*0;
                }

                if($request->total_jam_5[$key]){
                    // $explode_input_total_jam_5 = explode(".",$request->total_jam_5[$key]);
                    $total_jam_5 = $request->total_jam_5[$key];
                }else{
                    $total_jam_5 = '0';
                }

            }else{
                $hasil_pengerjaan_5 = '0'.'|'.'0';
                $lembur_5 = '5-n';
                $hasil_lembur_5 = '1';
                $total_jam_5 = '0';
                $hasil_kerja_5 = 0;
                $total_upah_lembur_5 = '0';
            }
            $total_hasil_lembur_5 = $total_upah_lembur_5;

            // $uang_lembur = 0;
            $lemburs = '|'.$lembur_1.'|'.$lembur_2.'|'.$lembur_3.'|'.$lembur_4.'|'.$lembur_5;
            $hasil_upah_dasar = $hasil_kerja_1+$hasil_kerja_2+$hasil_kerja_3+$hasil_kerja_4+$hasil_kerja_5;
            
            // $penjumlahan_lembur = round($total_hasil_lembur_1);
            $penjumlahan_lembur = round($total_hasil_lembur_1+$total_hasil_lembur_2+$total_hasil_lembur_3+$total_hasil_lembur_4+$total_hasil_lembur_5);
            // dd($hasil_upah_dasar);
            $karyawan_pengerjaan->update([
                'hasil_kerja_1' => $hasil_pengerjaan_1,
                'hasil_kerja_2' => $hasil_pengerjaan_2,
                'hasil_kerja_3' => $hasil_pengerjaan_3,
                'hasil_kerja_4' => $hasil_pengerjaan_4,
                'hasil_kerja_5' => $hasil_pengerjaan_5,
                'uang_lembur' => $penjumlahan_lembur,
                'lembur' => $lemburs,
                'total_jam_kerja_1' => $total_jam_1,
                'total_jam_kerja_2' => $total_jam_2,
                'total_jam_kerja_3' => $total_jam_3,
                'total_jam_kerja_4' => $total_jam_4,
                'total_jam_kerja_5' => $total_jam_5,
            ]);
        }

        return response()->json([
            'success' => true,
            'message_title' => 'Berhasil',
            'message_content' => 'Data Berhasil Disimpan'
        ]);
    }

    public function hasil_kerja_karyawan_packing_view($id, $kode_pengerjaan, $nik, $month, $year)
    {
        $data['id'] = $id;
        $data['nik'] = $nik;
        $data['kode_pengerjaan'] = $kode_pengerjaan;
        $data['month'] = $month;
        $data['year'] = $year;
        // dd($data);

        if($id == 1){
            $kode_jenis_operator_detail = 'L';
        }
        elseif($id == 2){
            $kode_jenis_operator_detail = 'E';
        }
        elseif($id == 3){
            $kode_jenis_operator_detail = 'A';
        }
        $data['karyawan'] = $this->karyawanOperator->select([
                                                    'operator_karyawan.id as id',
                                                    'biodata_karyawan.nama as nama',
                                                    'operator_karyawan.nik as nik',
                                                    'biodata_karyawan.pin as pin',
                                                    'biodata_karyawan.tanggal_masuk as tanggal',
                                                    'operator_karyawan.tunjangan_kerja_id as tunjangan_kerja_id',
                                                    'operator_karyawan.jenis_operator_detail_pekerjaan_id as jenis_operator_detail_pekerjaan_id'
                                                    ])
                                                    ->leftJoin('itic_emp_new.biodata_karyawan','biodata_karyawan.nik','=','operator_karyawan.nik')
                                                    ->where('operator_karyawan.nik',$nik)
                                                    ->where('status','Y')
                                                    // ->where('biodata_karyawan.status_karyawan',null)
                                                    ->where('operator_karyawan.jenis_operator_detail_pekerjaan_id',1)
                                                    ->first();
                                                    // dd($data['karyawan']);
        $data['new_data_pengerjaan'] = $this->newDataPengerjaan->select('akhir_bulan')->where('kode_pengerjaan',$kode_pengerjaan)->first();
        $data['jhts'] = $this->bpjsJht->where('status','y')->get();
        $data['bpjs_kesehatan'] = $this->bpjsKesehatan->select('nominal')->where('status','y')->first();
        // dd($data['new_data_pengerjaan']);

        $data['pengerjaans'] = $this->pengerjaan->where('operator_karyawan_id',$data['karyawan']['id'])->where('kode_pengerjaan',$kode_pengerjaan)->get();
        
        $data['upah'] = array();

        foreach ($data['pengerjaans'] as $key => $pengerjaan) {
            $explode_hasil_kerja_1 = explode("|",$pengerjaan->hasil_kerja_1);
            $umk_borongan_lokal_1 = $this->umkBoronganLokal->select('id','jenis_produk','umk_packing','umk_bandrol','umk_inner','umk_outer')->where('id',$explode_hasil_kerja_1[0])->first();
            if(empty($umk_borongan_lokal_1)){
                $jenis_produk_1 = '-';
                $hasil_kerja_1 = null;
                $data_explode_hasil_kerja_1 = '-';
                $lembur_1 = 1;
            }else{
                $jenis_produk_1 = $umk_borongan_lokal_1->jenis_produk;
                $hasil_kerja_1 = $explode_hasil_kerja_1[1]*$umk_borongan_lokal_1->umk_packing;
                $data_explode_hasil_kerja_1 = $explode_hasil_kerja_1[1];

                $explode_lembur_1 = explode("|",$pengerjaan->lembur);
                $explode_status_lembur_1 = explode("-",$explode_lembur_1[1]);

                if($explode_status_lembur_1[1] == 'y'){
                    $lembur_1 = 1.5;
                }else{
                    $lembur_1 = 1;
                }
            }

            $explode_hasil_kerja_2 = explode("|",$pengerjaan->hasil_kerja_2);
            $umk_borongan_lokal_2 = $this->umkBoronganLokal->select('id','jenis_produk','umk_packing','umk_bandrol','umk_inner','umk_outer')->where('id',$explode_hasil_kerja_2[0])->first();
            if(empty($umk_borongan_lokal_2)){
                $jenis_produk_2 = '-';
                $hasil_kerja_2 = null;
                $data_explode_hasil_kerja_2 = '-';
                $lembur_2 = 1;
            }else{
                $jenis_produk_2 = $umk_borongan_lokal_2->jenis_produk;
                $hasil_kerja_2 = $explode_hasil_kerja_2[1]*$umk_borongan_lokal_2->umk_packing;
                $data_explode_hasil_kerja_2 = $explode_hasil_kerja_2[1];

                $explode_lembur_2 = explode("|",$pengerjaan->lembur);
                $explode_status_lembur_2 = explode("-",$explode_lembur_2[2]);

                if($explode_status_lembur_2[1] == 'y'){
                    $lembur_2 = 1.5;
                }else{
                    $lembur_2 = 1;
                }
            }

            $explode_hasil_kerja_3 = explode("|",$pengerjaan->hasil_kerja_3);
            $umk_borongan_lokal_3 = $this->umkBoronganLokal->select('id','jenis_produk','umk_packing','umk_bandrol','umk_inner','umk_outer')->where('id',$explode_hasil_kerja_3[0])->first();
            if(empty($umk_borongan_lokal_3)){
                $jenis_produk_3 = '-';
                $hasil_kerja_3 = null;
                $data_explode_hasil_kerja_3 = '-';
                $lembur_3 = 1;
            }else{
                $jenis_produk_3 = $umk_borongan_lokal_3->jenis_produk;
                $hasil_kerja_3 = $explode_hasil_kerja_3[1]*$umk_borongan_lokal_3->umk_packing;
                $data_explode_hasil_kerja_3 = $explode_hasil_kerja_3[1];

                $explode_lembur_3 = explode("|",$pengerjaan->lembur);
                $explode_status_lembur_3 = explode("-",$explode_lembur_3[3]);

                if($explode_status_lembur_3[1] == 'y'){
                    $lembur_3 = 1.5;
                }else{
                    $lembur_3 = 1;
                }
            }

            $explode_hasil_kerja_4 = explode("|",$pengerjaan->hasil_kerja_4);
            $umk_borongan_lokal_4 = $this->umkBoronganLokal->select('id','jenis_produk','umk_packing','umk_bandrol','umk_inner','umk_outer')->where('id',$explode_hasil_kerja_4[0])->first();
            if(empty($umk_borongan_lokal_4)){
                $jenis_produk_4 = '-';
                $hasil_kerja_4 = null;
                $data_explode_hasil_kerja_4 = '-';
                $lembur_4 = 1;
            }else{
                $jenis_produk_4 = $umk_borongan_lokal_4->jenis_produk;
                $hasil_kerja_4 = $explode_hasil_kerja_4[1]*$umk_borongan_lokal_4->umk_packing;
                $data_explode_hasil_kerja_4 = $explode_hasil_kerja_4[1];

                $explode_lembur_4 = explode("|",$pengerjaan->lembur);
                $explode_status_lembur_4 = explode("-",$explode_lembur_4[4]);

                if($explode_status_lembur_4[1] == 'y'){
                    $lembur_4 = 1.5;
                }else{
                    $lembur_4 = 1;
                }
            }

            $explode_hasil_kerja_5 = explode("|",$pengerjaan->hasil_kerja_5);
            $umk_borongan_lokal_5 = $this->umkBoronganLokal->select('id','jenis_produk','umk_packing','umk_bandrol','umk_inner','umk_outer')->where('id',$explode_hasil_kerja_5[0])->first();
            if(empty($umk_borongan_lokal_5)){
                $jenis_produk_5 = '-';
                $hasil_kerja_5 = null;
                $data_explode_hasil_kerja_5 = '-';
                $lembur_5 = 1;
            }else{
                $jenis_produk_5 = $umk_borongan_lokal_5->jenis_produk;
                $hasil_kerja_5 = $explode_hasil_kerja_5[1]*$umk_borongan_lokal_5->umk_packing;
                $data_explode_hasil_kerja_5 = $explode_hasil_kerja_5[1];

                $explode_lembur_5 = explode("|",$pengerjaan->lembur);
                $explode_status_lembur_5 = explode("-",$explode_lembur_5[5]);

                if($explode_status_lembur_5[1] == 'y'){
                    $lembur_5 = 1.5;
                }else{
                    $lembur_5 = 1;
                }
            }

            $hasil_upah = ($hasil_kerja_1*$lembur_1)+($hasil_kerja_2*$lembur_2)+($hasil_kerja_3*$lembur_3)+($hasil_kerja_4*$lembur_4)+($hasil_kerja_5*$lembur_5);

            array_push($data['upah'],$hasil_upah);
        }

        //Hitung Masa Kerja
        // $data['karyawan_masa_kerja'] = DB::connection('emp')->table('log_posisi')
        //                                                     ->select('tanggal')
        //                                                     ->where('nik',$nik)
        //                                                     ->first();
        $awal  = new DateTime($data['karyawan']->tanggal);
        $akhir = new DateTime(); // Waktu sekarang
        $diff  = $awal->diff($akhir);
        $data['masa_kerja'] = $diff->y.' Tahun '.$diff->m.' Bulan '.$diff->d.' Hari';
        $data['masa_kerja_tahun'] = $diff->y;
        $data['masa_kerja_hari'] = $diff->d;
        //End Hitung Masa Kerja

        $data['upah_dasar_karyawan'] = [];
        foreach ($data['jhts'] as $key => $jht) {
            if ($data['masa_kerja_tahun'] > 15) {
                if ($jht->urutan == 3) {
                    $data['upah_dasar_karyawan'] = ($data['bpjs_kesehatan']['nominal'] + 100000)/25;
                }
            }
            elseif($data['masa_kerja_tahun'] >= 10 && $data['masa_kerja_tahun'] <= 15 && $data['masa_kerja_hari'] >= 1){
                if ($jht->urutan == 2) {
                    $data['upah_dasar_karyawan'] = ($data['bpjs_kesehatan']['nominal'] + 50000)/25;
                }
            }
            elseif($data['masa_kerja_tahun'] <= 10 || $data['masa_kerja_hari'] >= 1){
                if ($jht->urutan == 1) {
                    $data['upah_dasar_karyawan'] = ($data['bpjs_kesehatan']['nominal'] + 0)/25;
                }
            }
        }

        // echo 'Selisih waktu: ';
        // echo $diff->y . ' tahun, ';
        // echo $diff->m . ' bulan, ';
        // echo $diff->d . ' hari, ';
        // echo $diff->h . ' jam, ';
        // echo $diff->i . ' menit, ';
        // echo $diff->s . ' detik, ';
        // dd($data);

        $data['pengerjaan_weekly'] = $this->pengerjaanWeekly->where('operator_karyawan_id',$data['karyawan']['id'])
                                                    ->where('kode_payrol',substr($kode_pengerjaan,0,2).$kode_jenis_operator_detail.'_'.substr($kode_pengerjaan,3))
                                                    ->first();
        // $data['presensi_info'] = PresensiInfo::all();
        // dd($data['presensi_info']);
        $tgl_current_active = Carbon::now()->format('d');
        $bln_active = Carbon::now()->format('m');
        $bln_current_active = $month;
        $thn_current_active = $year;

        // $bulan_kemarin = Carbon::now()->subMonth()->format('Y-m').'-26';
        // $bulan_sekarang = Carbon::now()->format('Y-m').'-25';

        if($month == 1 && $tgl_current_active <10){
            $thn_prev_active = $year-1;
            $bulan_kemarin="$thn_prev_active-11-26";
            $bulan_sekarang="$thn_prev_active-12-26";
        }elseif($month == 1 && $tgl_current_active>10 or $month == 2 && $tgl_current_active<10 && $bln_active != 3){
            $thn_prev_active = $year-1;
            $bulan_kemarin="$thn_prev_active-12-26";
            $bulan_sekarang="$thn_current_active-01-26";
        }else{
            $bln_prev_active = $month-1; 
            $thn_prev_active = $year;
            $bulan_kemarin="$thn_prev_active-$bln_prev_active-26";
            $bulan_sekarang="$thn_current_active-$bln_current_active-26";
        }

        $data['tunjangan_kerjas'] = $this->tunjanganKerja->select('nominal')->where('id',$data['karyawan']['tunjangan_kerja_id'])->first();
        if(empty($data['tunjangan_kerjas'])){
            $data['tunjangan_kerja'] = 0;
        }else{
            $data['tunjangan_kerja'] = $data['tunjangan_kerjas']['nominal'];
        }
        //jumlah alpa
        $data['alpa'] = $this->presensiInfo->where('pin',$data['karyawan']['pin'])
                                    ->whereBetween('scan_date',[$bulan_kemarin,$bulan_sekarang])
                                    ->where('status',7)
                                    ->get();
        //jumlah diliburkan
        $data['diliburkan'] = $this->presensiInfo->where('pin',$data['karyawan']['pin'])
                                    ->whereBetween('scan_date',[$bulan_kemarin,$bulan_sekarang])
                                    ->where('status',14)
                                    ->get();
        //jumlah cuti
        $data['cuti'] = $this->presensiInfo->where('pin',$data['karyawan']['pin'])
                                    ->whereBetween('scan_date',[$bulan_kemarin,$bulan_sekarang])
                                    ->where('status',13)
                                    ->get();
        //jumlah sakit
        $data['sakit'] = $this->presensiInfo->where('pin',$data['karyawan']['pin'])
                                    ->whereBetween('scan_date',[$bulan_kemarin,$bulan_sekarang])
                                    ->where('status',4)
                                    ->get();
        // dd($data['sakit']);
        //jumlah ijin full
        // $data['ijin_full'] = $this->presensiInfo->where('pin',1090)
        //                             ->whereBetween('scan_date',[$bulan_kemarin,$bulan_sekarang])
        //                             ->where('status',6)
        //                             ->get();
        $data['ijin_full'] = $this->presensiInfo->where('pin',$data['karyawan']['pin'])
                                    ->whereBetween('scan_date',[$bulan_kemarin,$bulan_sekarang])
                                    ->where('status',6)
                                    ->get();
        // dd($data['ijin_full']);
        //jumlah status terlambat
        $data['terlambats'] = $this->presensiInfo->where('pin',$data['karyawan']['pin'])
                                    ->whereBetween('scan_date',[$bulan_kemarin,$bulan_sekarang])
                                    ->where('status',3)
                                    ->get();
        $telat_1=0; //terlambat < 5 menit
        $telat_2=0; //terlambat > 5 menit < 15 menit
        $telat_3=0; //terlambat > 15 menit < 1 jam
        $telat_4=0; //terlambat > 1 jam < 3 jam	
        $telat_5=0; //terlambat > 3 jam	
        $data['telat_1'] = $telat_1;
        $data['telat_2'] = $telat_2;
        $data['telat_3'] = $telat_3;
        $data['telat_4'] = $telat_4;
        $data['telat_5'] = $telat_4;
        foreach ($data['terlambats'] as $key => $terlambat) {
            $explode_keterangan_terlambat = explode("@",$terlambat->keterangan);
            $jam_keterangan_terlambat=strtotime($explode_keterangan_terlambat[1]);
            $jam_datang_terlambat=strtotime(Carbon::parse($terlambat['scan_date'])->format('H:i'));
            // $jam_datang_terlambat=strtotime($terlambat['jam_datang_telat']);
            $menit_telat=(($jam_datang_terlambat-$jam_keterangan_terlambat)/60);
            // dd($menit_telat);
            if($menit_telat <= 4){
                $telat_1=$telat_1+1;
                $data['telat_1'] = $telat_1;
            }
            else if(($menit_telat >= 5) && ($menit_telat <= 15) ){
                $telat_2=$telat_2+1;
                $data['telat_2'] = $telat_2;
            }
            else if(($menit_telat >= 16) && ($menit_telat <= 60)){
                $telat_3=$telat_3+1;
                $data['telat_3'] = $telat_3;
            }
            else if(($menit_telat >= 61) && ($menit_telat <= 179)){
                $telat_4=$telat_4+1;
                $data['telat_4'] = $telat_4;
            }else if($menit_telat > 179){
                $telat_5=$telat_5+1;
                $data['telat_5'] = $telat_5;
            }
            // else{
            //     $telat_4=$telat_4+1;
            //     $data['telat_4'] = $telat_4;
            // }
        }

        //jumlah status pulang awal
        $data['pulang_awals'] = $this->presensiInfo->where('pin',$data['karyawan']['pin'])
                                    ->whereBetween('scan_date',[$bulan_kemarin,$bulan_sekarang])
                                    ->where(function($query) {
                                        $query->where('status','=',9)
                                            ->orWhere('status','=',10);
                                    })
                                    // ->where('status',9)
                                    // ->where('status',10)
                                    ->get();
        // dd($data['pulang_awals']);
        $pulang_1=0; //pulang awal < 4 jam
		$pulang_2=0; //tpulang awal > 4 jam
        $data['pulang_1'] = $pulang_1;
        $data['pulang_2'] = $pulang_2;
        foreach ($data['pulang_awals'] as $key => $pulang_awal) {
            $explode_pulang_seharusnya=explode("@",$pulang_awal->keterangan);
			$jam_pulang_seharusnya=strtotime($explode_pulang_seharusnya[3]);
			$jam_pulang_awal=strtotime(Carbon::parse($pulang_awal['scan_date'])->format('H:i'));
            // $jam_pulang_awal=strtotime($pulang_awal->jam_pulang_awal);
			$menit_sisa_jam_kerja=(($jam_pulang_seharusnya-$jam_pulang_awal)/60);
			if ($menit_sisa_jam_kerja<=300){
                $pulang_1=$pulang_1+1;
                $data['pulang_1'] = $pulang_1;
            }
			else{
                $pulang_2=$pulang_2+1;
                $data['pulang_2'] = $pulang_2;
            }
        }

        //jumlah ijin saat jam kerja
        $data['keluar_masuks'] = $this->keluarMasuk->where('nik',$data['karyawan']['nik'])
                                    ->whereBetween('tanggal_ijin',[$bulan_kemarin,$bulan_sekarang])
                                    ->get();
        // dd($data['keluar_masuks']);
        $ijin_15=0; //ijin kurang dari 15 menit
		$ijin_k4=0; //ijin > 15menit < 4 jam
		$ijin_l4=0; //ijin > 4 jam
        $data['ijin_15']=$ijin_15;
        $data['ijin_k4']=$ijin_k4;
        $data['ijin_l4']=$ijin_l4;
        foreach ($data['keluar_masuks'] as $key => $keluar_masuk) {
            $jam_keluar=strtotime($keluar_masuk->jam_keluar);
			$jam_datang=strtotime($keluar_masuk->jam_datang);
			$menit_keluar_masuk=(($jam_datang-$jam_keluar)/60);
			if ($menit_keluar_masuk<=15){
                $ijin_15=$ijin_15+1;
                $data['ijin_15']=$ijin_15;
            }
			else if (($menit_keluar_masuk>15)&&($menit_keluar_masuk<=239)){
                $ijin_k4=$ijin_k4+1;
                $data['ijin_k4']=$ijin_k4;
            }
			else {
                $ijin_l4=$ijin_l4+1;
                $data['ijin_l4']=$ijin_l4;
            }
        }

        //lihat tanggal masuk
        // $data['log_posisi'] = $this->logPosisi->select('tanggal')->where('nik',$nik)->first();
		$selisih_tanggal_masuk=strtotime($bulan_kemarin)-strtotime($data['karyawan']['tanggal']);
		if ($selisih_tanggal_masuk>=0)$div_tk=0;else $div_tk=75000;

        $total_potongan_tk=(75000*$data['alpa']->count())+(75000*$data['diliburkan']->count())+(75000*$data['sakit']->count())+(75000*$data['cuti']->count())+(75000*$data['ijin_full']->count())+($data['ijin_15']*25000)+($data['ijin_k4']*40000)+
        ($data['ijin_l4']*75000)+($data['pulang_1']*40000)+($data['pulang_2']*75000)+($data['telat_1']*15000)+($data['telat_2']*25000)+($data['telat_3']*30000)+($data['telat_4']*40000)+$div_tk;
        if ($total_potongan_tk>75000){$total_potongan_tk=75000;}else {}

        // if ($data['pengerjaan_weekly']['tunjangan_kehadiran'] == 75000) {
        //     $data['total_potongan_tk']=0;
        // }
        // // elseif($data['pengerjaan_weekly']['tunjangan_kehadiran'] < 75000){
        // //     $data['total_potongan_tk']=$data['pengerjaan_weekly']['tunjangan_kehadiran'];
        // // }
        // else{
        //     $data['total_potongan_tk']=$total_potongan_tk; 
        // }

        $data['total_potongan_tk']=$total_potongan_tk;
        return view('backend.pengerjaan.packing_lokal.input_hasil_kerja_karyawan',$data);
    }

    public function hasil_kerja_karyawan_packing_view_simpan(Request $request, $id, $kode_pengerjaan, $nik)
    {
        // $input = $request->all();
        if($id == 1){
            $kode_jenis_operator_detail = 'L';
        }
        elseif($id == 2){
            $kode_jenis_operator_detail = 'E';
        }
        elseif($id == 3){
            $kode_jenis_operator_detail = 'A';
        }

        $data['id'] = $id;
        $data['kode_pengerjaan'] = $kode_pengerjaan;
        $data['nik'] = $nik;

        $input['upah_dasar'] = $request->upah_dasar;
        $input['upah_makan'] = $request->upah_makan;
        
        $input['uang_makan'] = $request->uang_makan;
        $input['plus_1'] = $request->plus_1."|".$request->keterangan_plus_1;
        $input['plus_2'] = $request->plus_2."|".$request->keterangan_plus_2;
        $input['plus_3'] = $request->plus_3."|".$request->keterangan_plus_3;
        
        $input['minus_1'] = $request->minus_1."|".$request->keterangan_minus_1;
        $input['minus_2'] = $request->minus_2."|".$request->keterangan_minus_2;

        if ($request->akhir_bulan == 'n') {
            $input['tunjangan_kerja'] = 0;
            $input['tunjangan_kehadiran'] = 0;
        }else{
            $input['tunjangan_kerja'] = $request->tunjangan_kerja;
            $input['tunjangan_kehadiran'] = 75000-$request->pot_tunjangan_kehadiran;
        }

        if ($request->check_jht == 'on') {
            $input['jht'] = $request->jht;
        }else{
            $input['jht'] = 0;
        }

        if($request->check_bpjs_kesehatan == 'on'){
            $input['bpjs_kesehatan'] = $request->bpjs_kesehatan;
        }else{
            $input['bpjs_kesehatan'] = 0;
        }
        // dd($input);
        $operator_karyawan = $this->karyawanOperator->select('id')->where('nik',$nik)->where('status','Y')->where('jenis_operator_detail_pekerjaan_id',1)->first();
        $pengerjaan_weekly = $this->pengerjaanWeekly->where('kode_payrol',substr($kode_pengerjaan,0,2).$kode_jenis_operator_detail.'_'.substr($kode_pengerjaan,3))
                                            ->where('operator_karyawan_id',$operator_karyawan->id)
                                            ->first();
        // dd($operator_karyawan);
        if (!empty($pengerjaan_weekly)) {
            $pengerjaan_weekly->update($input);
            return response()->json([
                'success' => true,
                'message_title' => 'Berhasil',
                'message_content' => 'Data Berhasil Disimpan'
            ]);
        }else{
            return response()->json([
                'success' => false,
                'message_title' => 'Gagal',
                'message_content' => 'Data Tidak Berhasil Disimpan'
            ]);
        }
    }

    public function hasil_kerja_bandrol_lokal($id, $kode_pengerjaan)
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

        $data['id'] = $id;
        $data['kode_pengerjaan'] = $kode_pengerjaan;
        $data['jenis_operator_detail_pekerjaan'] = $this->jenisOperatorDetailPengerjaan->find(2);
        $data['new_data_pengerjaan'] = $this->newDataPengerjaan->where('kode_pengerjaan',$kode_pengerjaan)->first();
        $data['explode_tanggal_pengerjaans'] = explode('#',$data['new_data_pengerjaan']['tanggal']);
        $data['count_tanggal'] = count($data['explode_tanggal_pengerjaans'])-1;
        // $data['packing_lokals'] = KaryawanOperator::select([
        //                             'operator_karyawan.id as id',
        //                             'operator_karyawan.nik as nik',
        //                             'biodata_karyawan.nama as nama',
        //                             ])
        //                             ->where('jenis_operator_id',1)
        //                             ->where('jenis_operator_detail_id',1)
        //                             ->where('jenis_operator_detail_pekerjaan_id',2)
        //                             ->leftJoin('itic_emp_new.biodata_karyawan','biodata_karyawan.nik','=','operator_karyawan.nik')
        //                             ->rightJoin('pengerjaan','pengerjaan.operator_karyawan_id','=','operator_karyawan.id')
        //                             ->orderBy('biodata_karyawan.nama','asc')
        //                             ->get();

        $data['pengerjaans'] = $this->pengerjaanWeekly->select([
                                                    'pengerjaan_weekly.kode_payrol as kode_payrol',
                                                    'pengerjaan_weekly.operator_karyawan_id as operator_karyawan_id',
                                                    'operator_karyawan.nik as nik',
                                                    'biodata_karyawan.nama as nama',
                                                    'pengerjaan_weekly.upah_dasar as upah_dasar',
                                                    'pengerjaan_weekly.tunjangan_kerja as tunjangan_kerja',
                                                    'pengerjaan_weekly.tunjangan_kehadiran as tunjangan_kehadiran',
                                                    'pengerjaan_weekly.uang_makan as uang_makan',
                                                    'pengerjaan_weekly.plus_1 as plus_1',
                                                    'pengerjaan_weekly.plus_2 as plus_2',
                                                    'pengerjaan_weekly.plus_3 as plus_3',
                                                    'pengerjaan_weekly.minus_1 as minus_1',
                                                    'pengerjaan_weekly.minus_2 as minus_2',
                                                    'pengerjaan_weekly.jht as jht',
                                                    'pengerjaan_weekly.bpjs_kesehatan as bpjs_kesehatan',
                                                ])
                                                ->where('kode_payrol',substr($kode_pengerjaan,0,2).$kode_jenis_operator_detail.'_'.substr($kode_pengerjaan,3))
                                                ->where('operator_karyawan.jenis_operator_id',1)
                                                ->where('operator_karyawan.jenis_operator_detail_id',1)
                                                ->where('operator_karyawan.jenis_operator_detail_pekerjaan_id',2)
                                                ->leftJoin('operator_karyawan','operator_karyawan.id','=','pengerjaan_weekly.operator_karyawan_id')
                                                ->leftJoin('itic_emp_new.biodata_karyawan','biodata_karyawan.nik','=','operator_karyawan.nik')
                                                ->orderBy('biodata_karyawan.nama','asc')
                                                ->get();
        return view('backend.pengerjaan.packing_bandrol.packing_bandrol',$data);
    }

    public function hasil_kerja_bandrol_lokal_view_hasil($id, $kode_pengerjaan, $tanggal)
    {
        $data['id'] = $id;
        $data['kode_pengerjaan'] = $kode_pengerjaan;
        $data['tanggal'] = $tanggal;
        $data['packing_lokals'] = $this->karyawanOperator->select([
                                                    'operator_karyawan.id as id',
                                                    'operator_karyawan.nik as nik',
                                                    'biodata_karyawan.nama as nama',
                                                    'pengerjaan.hasil_kerja_1 as hasil_kerja_1',
                                                    'pengerjaan.hasil_kerja_2 as hasil_kerja_2',
                                                    'pengerjaan.hasil_kerja_3 as hasil_kerja_3',
                                                    'pengerjaan.hasil_kerja_4 as hasil_kerja_4',
                                                    'pengerjaan.hasil_kerja_5 as hasil_kerja_5',
                                                    'pengerjaan.total_jam_kerja_1 as total_jam_kerja_1',
                                                    'pengerjaan.total_jam_kerja_2 as total_jam_kerja_2',
                                                    'pengerjaan.total_jam_kerja_3 as total_jam_kerja_3',
                                                    'pengerjaan.total_jam_kerja_4 as total_jam_kerja_4',
                                                    'pengerjaan.total_jam_kerja_5 as total_jam_kerja_5',
                                                    ])
                                                    ->leftJoin('itic_emp_new.biodata_karyawan','biodata_karyawan.nik','=','operator_karyawan.nik')
                                                    ->rightJoin('pengerjaan','pengerjaan.operator_karyawan_id','=','operator_karyawan.id')
                                                    ->where('jenis_operator_id',1)
                                                    ->where('jenis_operator_detail_id',1)
                                                    ->where('jenis_operator_detail_pekerjaan_id',2)
                                                    ->where('pengerjaan.tanggal_pengerjaan',$tanggal)
                                                    ->where('pengerjaan.kode_pengerjaan',$kode_pengerjaan)
                                                    // ->with('biodata_karyawan')
                                                    ->orderBy('biodata_karyawan.nama','asc')
                                                    ->get();

        $data['umk_borongan_lokals'] = $this->umkBoronganLokal->select('id','jenis_produk','umk_bandrol')->where('status','Y')->get();
        // dd($data);
        return view('backend.pengerjaan.packing_bandrol.input_hasil_kerja',$data);
    }

    public function hasil_kerja_bandrol_view_simpan(Request $request, $id, $kode_pengerjaan, $tanggal)
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

        $input = $request->all();
        $input['id'] = $id;
        $input['kode_pengerjaan'] = $kode_pengerjaan;
        $input['tanggal'] = $tanggal;

        $data['karyawan_pengerjaans'] = $this->pengerjaan->select([
                                                        'pengerjaan.id as id',
                                                        'operator_karyawan.nik as nik',
                                                        'pengerjaan.operator_karyawan_id as id_operator_karyawan'
                                                    ])
                                                    ->leftJoin('operator_karyawan','operator_karyawan.id','=','pengerjaan.operator_karyawan_id')
                                                    ->leftJoin('itic_emp_new.biodata_karyawan','biodata_karyawan.nik','=','operator_karyawan.nik')
                                                    ->where('tanggal_pengerjaan',$tanggal)
                                                    // ->where('kode_pengerjaan',$kode_pengerjaan)
                                                    // ->where('pengerjaan.operator_karyawan_id','operator_karyawan.id')
                                                    ->where('operator_karyawan.jenis_operator_id',1)
                                                    ->where('operator_karyawan.jenis_operator_detail_id',1)
                                                    ->where('operator_karyawan.jenis_operator_detail_pekerjaan_id',2)
                                                    ->where('pengerjaan.kode_pengerjaan',$kode_pengerjaan)
                                                    ->orderBy('biodata_karyawan.nama','asc')
                                                    ->get();
        
        foreach ($data['karyawan_pengerjaans'] as $key => $karyawan_pengerjaan) {
            // dd($request->umk_borongan_lokal_kerja_1);
            if ($request->umk_borongan_lokal_kerja_1) {
                $umk_borongan_lokal_1 = $this->umkBoronganLokal->select('id','jenis_produk','umk_bandrol')
                                                        ->where('id',$request->umk_borongan_lokal_kerja_1)
                                                        ->where('status','Y')
                                                        ->first();

                // $hasil_pengerjaan_1 = $umk_borongan_lokal_1->id.'|'.$umk_borongan_lokal_1->umk_packing*$request->hasil_kerja_1[$key];
                
                // if ($request->hasil_kerja_1[$key]) {
                // }

                if ($request->hasil_kerja_1[$key]) {
                    $hasil_pengerjaan_1 = $umk_borongan_lokal_1->id.'|'.$request->hasil_kerja_1[$key];
                    $hasil_kerja_1 = $umk_borongan_lokal_1->umk_bandrol*$request->hasil_kerja_1[$key];
                }else{
                    $hasil_pengerjaan_1 = $umk_borongan_lokal_1->id.'|'.'0';
                    $hasil_kerja_1 = 0;
                }

                if($request->lembur_kerja_1){
                    $lembur_1 = '1-y';
                    $hasil_lembur_1 = '1.5';
                    // $total_upah_lembur_1 = ($hasil_kerja_1*1.5)-$hasil_kerja_1;
                    $total_upah_lembur_1 = $hasil_kerja_1*1.5;
                }else{
                    $lembur_1 = '1-n';
                    $hasil_lembur_1 = '1';
                    $total_upah_lembur_1 = $hasil_kerja_1*0;
                }

                if($request->total_jam_1[$key]){
                    $total_jam_1 = $request->total_jam_1[$key];
                }else{
                    $total_jam_1 = '0';
                }
                
            }else{
                $hasil_pengerjaan_1 = '0'.'|'.'0';
                $lembur_1 = '1-n';
                $hasil_lembur_1 = '1';
                $total_jam_1 = '0';
                $hasil_kerja_1 = 0;
                $total_upah_lembur_1 = '0';
            }

            $total_hasil_lembur_1 = $total_upah_lembur_1;

            // dd($hasil_pengerjaan_1);
            
            if ($request->umk_borongan_lokal_kerja_2) {
                $umk_borongan_lokal_2 = $this->umkBoronganLokal->select('id','jenis_produk','umk_bandrol')
                                                        ->where('id',$request->umk_borongan_lokal_kerja_2)
                                                        ->where('status','Y')
                                                        ->first();

                // $hasil_pengerjaan_2 = $umk_borongan_lokal_2->id.'|'.$umk_borongan_lokal_2->umk_packing*$request->hasil_kerja_2[$key];
                
                if ($request->hasil_kerja_2[$key]) {
                    $hasil_pengerjaan_2 = $umk_borongan_lokal_2->id.'|'.$request->hasil_kerja_2[$key];
                    $hasil_kerja_2 = $umk_borongan_lokal_2->umk_bandrol*$request->hasil_kerja_2[$key];
                }else{
                    $hasil_pengerjaan_2 = $umk_borongan_lokal_2->id.'|'.'0';
                    $hasil_kerja_2 = 0;
                }

                if($request->lembur_kerja_2){
                    $lembur_2 = '2-y';
                    $hasil_lembur_2 = '1.5';
                    // $total_upah_lembur_2 = ($hasil_kerja_2*1.5)-$hasil_kerja_2;
                    $total_upah_lembur_2 = $hasil_kerja_2*1.5;
                }else{
                    $lembur_2 = '2-n';
                    $hasil_lembur_2 = '1';
                    $total_upah_lembur_2 = $hasil_kerja_2*0;
                }

                if($request->total_jam_2[$key]){
                    $total_jam_2 = $request->total_jam_2[$key];
                }else{
                    $total_jam_2 = '0';
                }

            }else{
                $hasil_pengerjaan_2 = '0'.'|'.'0';
                $lembur_2 = '2-n';
                $hasil_lembur_2 = '1';
                $total_jam_2 = '0';
                $hasil_kerja_2 = 0;
                $total_upah_lembur_2 = '0';
            }

            $total_hasil_lembur_2 = $total_upah_lembur_2;

            if ($request->umk_borongan_lokal_kerja_3) {
                $umk_borongan_lokal_3 = $this->umkBoronganLokal->select('id','jenis_produk','umk_bandrol')
                                                        ->where('id',$request->umk_borongan_lokal_kerja_3)
                                                        ->where('status','Y')
                                                        ->first();

                // $hasil_pengerjaan_3 = $umk_borongan_lokal_3->id.'|'.$umk_borongan_lokal_3->umk_packing*$request->hasil_kerja_3[$key];
                
                if ($request->hasil_kerja_3[$key]) {
                    $hasil_pengerjaan_3 = $umk_borongan_lokal_3->id.'|'.$request->hasil_kerja_3[$key];
                    $hasil_kerja_3 = $umk_borongan_lokal_3->umk_bandrol*$request->hasil_kerja_3[$key];
                }else{
                    $hasil_pengerjaan_3 = $umk_borongan_lokal_3->id.'|'.'0';
                    $hasil_kerja_3 = 0;
                }
            
                if($request->lembur_kerja_3){
                    $lembur_3 = '3-y';
                    $hasil_lembur_3 = '1.5';
                    // $total_upah_lembur_3 = ($hasil_kerja_3*1.5)-$hasil_kerja_3;
                    $total_upah_lembur_3 = $hasil_kerja_3*1.5;
                }else{
                    $lembur_3 = '3-n';
                    $hasil_lembur_3 = '1';
                    $total_upah_lembur_3 = $hasil_kerja_3*0;
                }

                if($request->total_jam_3[$key]){
                    $total_jam_3 = $request->total_jam_3[$key];
                }else{
                    $total_jam_3 = '0';
                }

            }else{
                $hasil_pengerjaan_3 = '0'.'|'.'0';
                $lembur_3 = '3-n';
                $hasil_lembur_3 = '1';
                $total_jam_3 = '0';
                $hasil_kerja_3 = 0;
                $total_upah_lembur_3 = '0';
            }

            $total_hasil_lembur_3 = $total_upah_lembur_3;

            if ($request->umk_borongan_lokal_kerja_4) {
                $umk_borongan_lokal_4 = $this->umkBoronganLokal->select('id','jenis_produk','umk_bandrol')
                                                        ->where('id',$request->umk_borongan_lokal_kerja_4)
                                                        ->where('status','Y')
                                                        ->first();

                // $hasil_pengerjaan_4 = $umk_borongan_lokal_4->id.'|'.$umk_borongan_lokal_4->umk_packing*$request->hasil_kerja_4[$key];
                
                if ($request->hasil_kerja_4[$key]) {
                    $hasil_pengerjaan_4 = $umk_borongan_lokal_4->id.'|'.$request->hasil_kerja_4[$key];
                    $hasil_kerja_4 = $umk_borongan_lokal_4->umk_bandrol*$request->hasil_kerja_4[$key];
                }else{
                    $hasil_pengerjaan_4 = $umk_borongan_lokal_4->id.'|'.'0';
                    $hasil_kerja_4 = 0;
                }
            
                if($request->lembur_kerja_4){
                    $lembur_4 = '4-y';
                    $hasil_lembur_4 = '1.5';
                    // $total_upah_lembur_4 = ($hasil_kerja_4*1.5)-$hasil_kerja_4;
                    $total_upah_lembur_4 = $hasil_kerja_4*1.5;
                }else{
                    $lembur_4 = '4-n';
                    $hasil_lembur_4 = '1';
                    $total_upah_lembur_4 = $hasil_kerja_4*0;
                }

                if($request->total_jam_4[$key]){
                    $total_jam_4 = $request->total_jam_4[$key];
                }else{
                    $total_jam_4 = '0';
                }

            }else{
                $hasil_pengerjaan_4 = '0'.'|'.'0';
                $lembur_4 = '4-n';
                $hasil_lembur_4 = '1';
                $total_jam_4 = '0';
                $hasil_kerja_4 = 0;
                $total_upah_lembur_4 = '0';
            }

            $total_hasil_lembur_4 = $total_upah_lembur_4;

            if ($request->umk_borongan_lokal_kerja_5) {
                $umk_borongan_lokal_5 = $this->umkBoronganLokal->select('id','jenis_produk','umk_bandrol')
                                                        ->where('id',$request->umk_borongan_lokal_kerja_5)
                                                        ->where('status','Y')
                                                        ->first();

                // $hasil_pengerjaan_5 = $umk_borongan_lokal_5->id.'|'.$umk_borongan_lokal_5->umk_packing*$request->hasil_kerja_5[$key];
                
                if ($request->hasil_kerja_5[$key]) {
                    $hasil_pengerjaan_5 = $umk_borongan_lokal_5->id.'|'.$request->hasil_kerja_5[$key];
                    $hasil_kerja_5 = $umk_borongan_lokal_5->umk_bandrol*$request->hasil_kerja_5[$key];
                }else{
                    $hasil_pengerjaan_5 = $umk_borongan_lokal_5->id.'|'.'0';
                    $hasil_kerja_5 = 0;
                }
            
                if($request->lembur_kerja_5){
                    $lembur_5 = '5-y';
                    $hasil_lembur_5 = '1.5';
                    // $total_upah_lembur_5 = ($hasil_kerja_5*1.5)-$hasil_kerja_5;
                    $total_upah_lembur_5 = $hasil_kerja_5*1.5;
                }else{
                    $lembur_5 = '5-n';
                    $hasil_lembur_5 = '1';
                    $total_upah_lembur_5 = $hasil_kerja_5*0;
                }

                if($request->total_jam_5[$key]){
                    $total_jam_5 = $request->total_jam_5[$key];
                }else{
                    $total_jam_5 = '0';
                }

            }else{
                $hasil_pengerjaan_5 = '0'.'|'.'0';
                $lembur_5 = '5-n';
                $hasil_lembur_5 = '1';
                $total_jam_5 = '0';
                $hasil_kerja_5 = 0;
                $total_upah_lembur_5 = '0';
            }
            $total_hasil_lembur_5 = $total_upah_lembur_5;

            $lemburs = '|'.$lembur_1.'|'.$lembur_2.'|'.$lembur_3.'|'.$lembur_4.'|'.$lembur_5;
            $hasil_upah_dasar = $hasil_kerja_1+$hasil_kerja_2+$hasil_kerja_3+$hasil_kerja_4+$hasil_kerja_5;
            // dd($total_jam_1);
            $penjumlahan_lembur = round($total_hasil_lembur_1+$total_hasil_lembur_2+$total_hasil_lembur_3+$total_hasil_lembur_4+$total_hasil_lembur_5);

            $karyawan_pengerjaan->update([
                'hasil_kerja_1' => $hasil_pengerjaan_1,
                'hasil_kerja_2' => $hasil_pengerjaan_2,
                'hasil_kerja_3' => $hasil_pengerjaan_3,
                'hasil_kerja_4' => $hasil_pengerjaan_4,
                'hasil_kerja_5' => $hasil_pengerjaan_5,
                'uang_lembur' => $penjumlahan_lembur,
                'lembur' => $lemburs,
                'total_jam_kerja_1' => $total_jam_1,
                'total_jam_kerja_2' => $total_jam_2,
                'total_jam_kerja_3' => $total_jam_3,
                'total_jam_kerja_4' => $total_jam_4,
                'total_jam_kerja_5' => $total_jam_5,
            ]);
        }

        return response()->json([
            'success' => true,
            'message_title' => 'Berhasil',
            'message_content' => 'Data Berhasil Disimpan'
        ]);
    }

    public function hasil_kerja_karyawan_bandrol_view($id, $kode_pengerjaan, $nik, $month, $year)
    {
        $data['id'] = $id;
        $data['nik'] = $nik;
        $data['kode_pengerjaan'] = $kode_pengerjaan;
        $data['month'] = $month;
        $data['year'] = $year;
        // dd($data);

        if($id == 1){
            $kode_jenis_operator_detail = 'L';
        }
        elseif($id == 2){
            $kode_jenis_operator_detail = 'E';
        }
        elseif($id == 3){
            $kode_jenis_operator_detail = 'A';
        }
        $data['karyawan'] = $this->karyawanOperator->select([
                                                    'operator_karyawan.id as id',
                                                    'biodata_karyawan.nama as nama',
                                                    'operator_karyawan.nik as nik',
                                                    'biodata_karyawan.pin as pin',
                                                    'biodata_karyawan.tanggal_masuk as tanggal',
                                                    'operator_karyawan.tunjangan_kerja_id as tunjangan_kerja_id'
                                                    ])
                                                    ->leftJoin('itic_emp_new.biodata_karyawan','biodata_karyawan.nik','=','operator_karyawan.nik')
                                                    ->where('operator_karyawan.nik',$nik)
                                                    ->where('status','Y')
                                                    ->where('operator_karyawan.jenis_operator_detail_pekerjaan_id',2)
                                                    ->first();

        $data['new_data_pengerjaan'] = $this->newDataPengerjaan->select('akhir_bulan')->where('kode_pengerjaan',$kode_pengerjaan)->first();
        $data['jhts'] = $this->bpjsJht->where('status','y')->get();
        $data['bpjs_kesehatan'] = $this->bpjsKesehatan->select('nominal')->where('status','y')->first();
        // dd($data['new_data_pengerjaan']);

        $data['pengerjaans'] = $this->pengerjaan->where('operator_karyawan_id',$data['karyawan']['id'])->where('kode_pengerjaan',$kode_pengerjaan)->get();
        
        $data['upah'] = array();

        foreach ($data['pengerjaans'] as $key => $pengerjaan) {
            $explode_hasil_kerja_1 = explode("|",$pengerjaan->hasil_kerja_1);
            $umk_borongan_lokal_1 = $this->umkBoronganLokal->select('id','jenis_produk','umk_packing','umk_bandrol','umk_inner','umk_outer')->where('id',$explode_hasil_kerja_1[0])->first();
            if(empty($umk_borongan_lokal_1)){
                $jenis_produk_1 = '-';
                $hasil_kerja_1 = null;
                $data_explode_hasil_kerja_1 = '-';
                $lembur_1 = 1;
            }else{
                $jenis_produk_1 = $umk_borongan_lokal_1->jenis_produk;
                $hasil_kerja_1 = $explode_hasil_kerja_1[1]*$umk_borongan_lokal_1->umk_bandrol;
                $data_explode_hasil_kerja_1 = $explode_hasil_kerja_1[1];

                $explode_lembur_1 = explode("|",$pengerjaan->lembur);
                $explode_status_lembur_1 = explode("-",$explode_lembur_1[1]);

                if($explode_status_lembur_1[1] == 'y'){
                    $lembur_1 = 1.5;
                }else{
                    $lembur_1 = 1;
                }
            }

            $explode_hasil_kerja_2 = explode("|",$pengerjaan->hasil_kerja_2);
            $umk_borongan_lokal_2 = $this->umkBoronganLokal->select('id','jenis_produk','umk_packing','umk_bandrol','umk_inner','umk_outer')->where('id',$explode_hasil_kerja_2[0])->first();
            if(empty($umk_borongan_lokal_2)){
                $jenis_produk_2 = '-';
                $hasil_kerja_2 = null;
                $data_explode_hasil_kerja_2 = '-';
                $lembur_2 = 1;
            }else{
                $jenis_produk_2 = $umk_borongan_lokal_2->jenis_produk;
                $hasil_kerja_2 = $explode_hasil_kerja_2[1]*$umk_borongan_lokal_2->umk_bandrol;
                $data_explode_hasil_kerja_2 = $explode_hasil_kerja_2[1];

                $explode_lembur_2 = explode("|",$pengerjaan->lembur);
                $explode_status_lembur_2 = explode("-",$explode_lembur_2[2]);

                if($explode_status_lembur_2[1] == 'y'){
                    $lembur_2 = 1.5;
                }else{
                    $lembur_2 = 1;
                }
            }

            $explode_hasil_kerja_3 = explode("|",$pengerjaan->hasil_kerja_3);
            $umk_borongan_lokal_3 = $this->umkBoronganLokal->select('id','jenis_produk','umk_packing','umk_bandrol','umk_inner','umk_outer')->where('id',$explode_hasil_kerja_3[0])->first();
            if(empty($umk_borongan_lokal_3)){
                $jenis_produk_3 = '-';
                $hasil_kerja_3 = null;
                $data_explode_hasil_kerja_3 = '-';
                $lembur_3 = 1;
            }else{
                $jenis_produk_3 = $umk_borongan_lokal_3->jenis_produk;
                $hasil_kerja_3 = $explode_hasil_kerja_3[1]*$umk_borongan_lokal_3->umk_bandrol;
                $data_explode_hasil_kerja_3 = $explode_hasil_kerja_3[1];

                $explode_lembur_3 = explode("|",$pengerjaan->lembur);
                $explode_status_lembur_3 = explode("-",$explode_lembur_3[3]);

                if($explode_status_lembur_3[1] == 'y'){
                    $lembur_3 = 1.5;
                }else{
                    $lembur_3 = 1;
                }
            }

            $explode_hasil_kerja_4 = explode("|",$pengerjaan->hasil_kerja_4);
            $umk_borongan_lokal_4 = $this->umkBoronganLokal->select('id','jenis_produk','umk_packing','umk_bandrol','umk_inner','umk_outer')->where('id',$explode_hasil_kerja_4[0])->first();
            if(empty($umk_borongan_lokal_4)){
                $jenis_produk_4 = '-';
                $hasil_kerja_4 = null;
                $data_explode_hasil_kerja_4 = '-';
                $lembur_4 = 1;
            }else{
                $jenis_produk_4 = $umk_borongan_lokal_4->jenis_produk;
                $hasil_kerja_4 = $explode_hasil_kerja_4[1]*$umk_borongan_lokal_4->umk_bandrol;
                $data_explode_hasil_kerja_4 = $explode_hasil_kerja_4[1];

                $explode_lembur_4 = explode("|",$pengerjaan->lembur);
                $explode_status_lembur_4 = explode("-",$explode_lembur_4[4]);

                if($explode_status_lembur_4[1] == 'y'){
                    $lembur_4 = 1.5;
                }else{
                    $lembur_4 = 1;
                }
            }

            $explode_hasil_kerja_5 = explode("|",$pengerjaan->hasil_kerja_5);
            $umk_borongan_lokal_5 = $this->umkBoronganLokal->select('id','jenis_produk','umk_packing','umk_bandrol','umk_inner','umk_outer')->where('id',$explode_hasil_kerja_5[0])->first();
            if(empty($umk_borongan_lokal_5)){
                $jenis_produk_5 = '-';
                $hasil_kerja_5 = null;
                $data_explode_hasil_kerja_5 = '-';
                $lembur_5 = 1;
            }else{
                $jenis_produk_5 = $umk_borongan_lokal_5->jenis_produk;
                $hasil_kerja_5 = $explode_hasil_kerja_5[1]*$umk_borongan_lokal_5->umk_bandrol;
                $data_explode_hasil_kerja_5 = $explode_hasil_kerja_5[1];

                $explode_lembur_5 = explode("|",$pengerjaan->lembur);
                $explode_status_lembur_5 = explode("-",$explode_lembur_5[5]);

                if($explode_status_lembur_5[1] == 'y'){
                    $lembur_5 = 1.5;
                }else{
                    $lembur_5 = 1;
                }
            }

            $hasil_upah = ($hasil_kerja_1*$lembur_1)+($hasil_kerja_2*$lembur_2)+($hasil_kerja_3*$lembur_3)+($hasil_kerja_4*$lembur_4)+($hasil_kerja_5*$lembur_5);

            array_push($data['upah'],$hasil_upah);
        }

        //Hitung Masa Kerja
        // $data['karyawan_masa_kerja'] = DB::connection('emp')->table('log_posisi')
        //                                                     ->select('tanggal')
        //                                                     ->where('nik',$nik)
        //                                                     ->first();
        $awal  = new DateTime($data['karyawan']->tanggal);
        $akhir = new DateTime(); // Waktu sekarang
        $diff  = $awal->diff($akhir);
        $data['masa_kerja'] = $diff->y.' Tahun '.$diff->m.' Bulan '.$diff->d.' Hari';
        $data['masa_kerja_tahun'] = $diff->y;
        $data['masa_kerja_hari'] = $diff->d;
        //End Hitung Masa Kerja

        $data['upah_dasar_karyawan'] = [];
        foreach ($data['jhts'] as $key => $jht) {
            if ($data['masa_kerja_tahun'] > 15) {
                if ($jht->urutan == 3) {
                    $data['upah_dasar_karyawan'] = ($data['bpjs_kesehatan']['nominal'] + 100000)/25;
                }
            }
            elseif($data['masa_kerja_tahun'] >= 10 && $data['masa_kerja_tahun'] <= 15 && $data['masa_kerja_hari'] >= 1){
                if ($jht->urutan == 2) {
                    $data['upah_dasar_karyawan'] = ($data['bpjs_kesehatan']['nominal'] + 50000)/25;
                }
            }
            elseif($data['masa_kerja_tahun'] <= 10 || $data['masa_kerja_hari'] >= 1){
                if ($jht->urutan == 1) {
                    $data['upah_dasar_karyawan'] = ($data['bpjs_kesehatan']['nominal'] + 0)/25;
                }
            }
        }

        // echo 'Selisih waktu: ';
        // echo $diff->y . ' tahun, ';
        // echo $diff->m . ' bulan, ';
        // echo $diff->d . ' hari, ';
        // echo $diff->h . ' jam, ';
        // echo $diff->i . ' menit, ';
        // echo $diff->s . ' detik, ';
        // dd($data);

        $data['pengerjaan_weekly'] = $this->pengerjaanWeekly->where('operator_karyawan_id',$data['karyawan']['id'])
                                                    ->where('kode_payrol',substr($kode_pengerjaan,0,2).$kode_jenis_operator_detail.'_'.substr($kode_pengerjaan,3))
                                                    ->first();
        // dd($data['pengerjaan_weekly']);
        // $data['presensi_info'] = PresensiInfo::all();
        // dd($data['presensi_info']);
        $tgl_current_active = Carbon::now()->format('d');
        $bln_active = Carbon::now()->format('m');
        $bln_current_active = $month;
        $thn_current_active = $year;

        // $bulan_kemarin = Carbon::now()->subMonth()->format('Y-m').'-26';
        // $bulan_sekarang = Carbon::now()->format('Y-m').'-25';

        if($month == 1 && $tgl_current_active <10){
            $thn_prev_active = $year-1;
            $bulan_kemarin="$thn_prev_active-11-26";
            $bulan_sekarang="$thn_prev_active-12-26";
        }elseif($month == 1 && $tgl_current_active>10 or $month == 2 && $tgl_current_active<10 && $bln_active != 3){
            $thn_prev_active = $year-1;
            $bulan_kemarin="$thn_prev_active-12-26";
            $bulan_sekarang="$thn_current_active-01-26";
        }else{
            $bln_prev_active = $month-1; 
            $thn_prev_active = $year;
            $bulan_kemarin="$thn_prev_active-$bln_prev_active-26";
            $bulan_sekarang="$thn_current_active-$bln_current_active-26";
        }

        $data['tunjangan_kerjas'] = $this->tunjanganKerja->select('nominal')->where('id',$data['karyawan']['tunjangan_kerja_id'])->first();
        if(empty($data['tunjangan_kerjas'])){
            $data['tunjangan_kerja'] = 0;
        }else{
            $data['tunjangan_kerja'] = $data['tunjangan_kerjas']['nominal'];
        }
        //jumlah alpa
        $data['alpa'] = $this->presensiInfo->where('pin',$data['karyawan']['pin'])
                                    ->whereBetween('scan_date',[$bulan_kemarin,$bulan_sekarang])
                                    ->where('status',7)
                                    ->get();
        //jumlah diliburkan
        $data['diliburkan'] = $this->presensiInfo->where('pin',$data['karyawan']['pin'])
                                    ->whereBetween('scan_date',[$bulan_kemarin,$bulan_sekarang])
                                    ->where('status',14)
                                    ->get();
        //jumlah cuti
        $data['cuti'] = $this->presensiInfo->where('pin',$data['karyawan']['pin'])
                                    ->whereBetween('scan_date',[$bulan_kemarin,$bulan_sekarang])
                                    ->where('status',13)
                                    ->get();
        //jumlah sakit
        $data['sakit'] = $this->presensiInfo->where('pin',$data['karyawan']['pin'])
                                    ->whereBetween('scan_date',[$bulan_kemarin,$bulan_sekarang])
                                    ->where('status',4)
                                    ->get();
        // dd($data['sakit']);
        //jumlah ijin full
        $data['ijin_full'] = $this->presensiInfo->where('pin',$data['karyawan']['pin'])
                                    ->whereBetween('scan_date',[$bulan_kemarin,$bulan_sekarang])
                                    ->where('status',6)
                                    ->get();
        //jumlah status terlambat
        $data['terlambats'] = $this->presensiInfo->where('pin',$data['karyawan']['pin'])
                                    ->whereBetween('scan_date',[$bulan_kemarin,$bulan_sekarang])
                                    ->where('status',3)
                                    ->get();
        $telat_1=0; //terlambat < 5 menit
        $telat_2=0; //terlambat > 5 menit < 15 menit
        $telat_3=0; //terlambat > 15 menit < 1 jam
        $telat_4=0; //terlambat > 1 jam < 3 jam	
        $telat_5=0; //terlambat > 3 jam	
        $data['telat_1'] = $telat_1;
        $data['telat_2'] = $telat_2;
        $data['telat_3'] = $telat_3;
        $data['telat_4'] = $telat_4;
        $data['telat_5'] = $telat_5;
        foreach ($data['terlambats'] as $key => $terlambat) {
            $explode_keterangan_terlambat = explode("@",$terlambat->keterangan);
            $jam_keterangan_terlambat=strtotime($explode_keterangan_terlambat[1]);
			$jam_datang_terlambat=strtotime(Carbon::parse($terlambat['scan_date'])->format('H:i'));
            // $jam_datang_terlambat=strtotime($row_terlambat['jam_datang_telat']);
			$menit_telat=(($jam_datang_terlambat-$jam_keterangan_terlambat)/60);
			if($menit_telat<=4){
                $telat_1=$telat_1+1;
                $data['telat_1'] = $telat_1;
            }
			else if(($menit_telat>=5) && ($menit_telat<=15) ){
                $telat_2=$telat_2+1;
                $data['telat_2'] = $telat_2;
            }
			else if(($menit_telat>=16) && ($menit_telat<=60)){
                $telat_3=$telat_3+1;
                $data['telat_3'] = $telat_3;
            }
			else if(($menit_telat>=61) && ($menit_telat<=179)){
                $telat_4=$telat_4+1;
                $data['telat_4'] = $telat_4;
            }else if($menit_telat > 179){
                $telat_5=$telat_5+1;
                $data['telat_5'] = $telat_5;
            }
			// else{
            //     $telat_4=$telat_4+1;
            //     $data['telat_4'] = $telat_4;
            // }
        }

        //jumlah status pulang awal
        $data['pulang_awals'] = $this->presensiInfo->where('pin',$data['karyawan']['pin'])
                                    ->whereBetween('scan_date',[$bulan_kemarin,$bulan_sekarang])
                                    ->where(function($query) {
                                        $query->where('status','=',9)
                                            ->orWhere('status','=',10);
                                    })
                                    // ->where('status',9)
                                    // ->where('status',10)
                                    ->get();
        // dd($data['pulang_awals']);
        $pulang_1=0; //pulang awal < 4 jam
		$pulang_2=0; //tpulang awal > 4 jam
        $data['pulang_1'] = $pulang_1;
        $data['pulang_2'] = $pulang_2;
        foreach ($data['pulang_awals'] as $key => $pulang_awal) {
            $explode_pulang_seharusnya=explode("@",$pulang_awal->keterangan);
			$jam_pulang_seharusnya=strtotime($explode_pulang_seharusnya[3]);
			$jam_pulang_awal=strtotime(Carbon::parse($pulang_awal['scan_date'])->format('H:i'));
            // $jam_pulang_awal=strtotime($pulang_awal->jam_pulang_awal);
			$menit_sisa_jam_kerja=(($jam_pulang_seharusnya-$jam_pulang_awal)/60);
			if ($menit_sisa_jam_kerja<=300){
                $pulang_1=$pulang_1+1;
                $data['pulang_1'] = $pulang_1;
            }
			else{
                $pulang_2=$pulang_2+1;
                $data['pulang_2'] = $pulang_2;
            }
        }

        //jumlah ijin saat jam kerja
        $data['keluar_masuks'] = $this->keluarMasuk->where('nik',$data['karyawan']['nik'])
                                    ->whereBetween('tanggal_ijin',[$bulan_kemarin,$bulan_sekarang])
                                    ->get();
        // dd($data['keluar_masuks']);
        $ijin_15=0; //ijin kurang dari 15 menit
		$ijin_k4=0; //ijin > 15menit < 4 jam
		$ijin_l4=0; //ijin > 4 jam
        $data['ijin_15']=$ijin_15;
        $data['ijin_k4']=$ijin_k4;
        $data['ijin_l4']=$ijin_l4;
        foreach ($data['keluar_masuks'] as $key => $keluar_masuk) {
            $jam_keluar=strtotime($keluar_masuk->jam_keluar);
			$jam_datang=strtotime($keluar_masuk->jam_datang);
			$menit_keluar_masuk=(($jam_datang-$jam_keluar)/60);
			if ($menit_keluar_masuk<=15){
                $ijin_15=$ijin_15+1;
                $data['ijin_15']=$ijin_15;
            }
			else if (($menit_keluar_masuk>15)&&($menit_keluar_masuk<=239)){
                $ijin_k4=$ijin_k4+1;
                $data['ijin_k4']=$ijin_k4;
            }
			else {
                $ijin_l4=$ijin_l4+1;
                $data['ijin_l4']=$ijin_l4;
            }
        }

        //lihat tanggal masuk
        // $data['log_posisi'] = $this->logPosisi->select('tanggal')->where('nik',$nik)->first();
		$selisih_tanggal_masuk=strtotime($bulan_kemarin)-strtotime($data['karyawan']['tanggal']);
		if ($selisih_tanggal_masuk>=0)$div_tk=0;else $div_tk=75000;

        $total_potongan_tk=(75000*$data['alpa']->count())+(75000*$data['diliburkan']->count())+(75000*$data['sakit']->count())+(75000*$data['cuti']->count())+(75000*$data['ijin_full']->count())+($data['ijin_15']*25000)+($data['ijin_k4']*40000)+
        ($data['ijin_l4']*75000)+($data['pulang_1']*40000)+($data['pulang_2']*75000)+($data['telat_1']*15000)+($data['telat_2']*25000)+($data['telat_3']*30000)+($data['telat_4']*40000)+$div_tk;
        if ($total_potongan_tk>75000){$total_potongan_tk=75000;}else {}

        // if ($data['pengerjaan_weekly']['tunjangan_kehadiran'] == 75000) {
        //     $data['total_potongan_tk']=0;
        // }
        // // elseif($data['pengerjaan_weekly']['tunjangan_kehadiran'] < 75000){
        // //     $data['total_potongan_tk']=$data['pengerjaan_weekly']['tunjangan_kehadiran'];
        // // }
        // else{
        //     $data['total_potongan_tk']=$total_potongan_tk; 
        // }

        $data['total_potongan_tk']=$total_potongan_tk;
        return view('backend.pengerjaan.packing_bandrol.input_hasil_kerja_karyawan',$data);
    }

    public function hasil_kerja_karyawan_bandrol_view_simpan(Request $request, $id, $kode_pengerjaan, $nik)
    {
        // $input = $request->all();
        if($id == 1){
            $kode_jenis_operator_detail = 'L';
        }
        elseif($id == 2){
            $kode_jenis_operator_detail = 'E';
        }
        elseif($id == 3){
            $kode_jenis_operator_detail = 'A';
        }

        $data['id'] = $id;
        $data['kode_pengerjaan'] = $kode_pengerjaan;
        $data['nik'] = $nik;

        $input['upah_dasar'] = $request->upah_dasar;
        $input['upah_makan'] = $request->upah_makan;
        
        $input['uang_makan'] = $request->uang_makan;
        $input['plus_1'] = $request->plus_1."|".$request->keterangan_plus_1;
        $input['plus_2'] = $request->plus_2."|".$request->keterangan_plus_2;
        $input['plus_3'] = $request->plus_3."|".$request->keterangan_plus_3;
        
        $input['minus_1'] = $request->minus_1."|".$request->keterangan_minus_1;
        $input['minus_2'] = $request->minus_2."|".$request->keterangan_minus_2;

        if ($request->akhir_bulan == 'n') {
            $input['tunjangan_kerja'] = 0;
            $input['tunjangan_kehadiran'] = 0;
        }else{
            $input['tunjangan_kerja'] = $request->tunjangan_kerja;
            $input['tunjangan_kehadiran'] = 75000-$request->pot_tunjangan_kehadiran;
        }

        if ($request->check_jht == 'on') {
            $input['jht'] = $request->jht;
        }else{
            $input['jht'] = 0;
        }

        if($request->check_bpjs_kesehatan == 'on'){
            $input['bpjs_kesehatan'] = $request->bpjs_kesehatan;
        }else{
            $input['bpjs_kesehatan'] = 0;
        }
        // dd($input);
        $operator_karyawan = $this->karyawanOperator->select('id')->where('nik',$nik)->where('status','Y')->where('jenis_operator_detail_pekerjaan_id',2)->first();
        $pengerjaan_weekly = $this->pengerjaanWeekly->where('kode_payrol',substr($kode_pengerjaan,0,2).$kode_jenis_operator_detail.'_'.substr($kode_pengerjaan,3))
                                            ->where('operator_karyawan_id',$operator_karyawan->id)
                                            ->first();
        // $pengerjaan_weekly->update($input);
        // dd($pengerjaan_weekly);
        if (!empty($pengerjaan_weekly)) {
            $pengerjaan_weekly->update($input);
            return response()->json([
                'success' => true,
                'message_title' => 'Berhasil',
                'message_content' => 'Data Berhasil Disimpan'
            ]);
        }else{
            return response()->json([
                'success' => false,
                'message_title' => 'Gagal',
                'message_content' => 'Data Tidak Berhasil Disimpan'
            ]);
        }
    }

    public function hasil_kerja_inner_lokal($id, $kode_pengerjaan)
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

        $data['id'] = $id;
        $data['kode_pengerjaan'] = $kode_pengerjaan;
        $data['jenis_operator_detail_pekerjaan'] = $this->jenisOperatorDetailPengerjaan->find(3);
        $data['new_data_pengerjaan'] = $this->newDataPengerjaan->where('kode_pengerjaan',$kode_pengerjaan)->first();
        $data['explode_tanggal_pengerjaans'] = explode('#',$data['new_data_pengerjaan']['tanggal']);
        $data['count_tanggal'] = count($data['explode_tanggal_pengerjaans'])-1;
        // $data['packing_lokals'] = KaryawanOperator::select([
        //                             'operator_karyawan.id as id',
        //                             'operator_karyawan.nik as nik',
        //                             'biodata_karyawan.nama as nama',
        //                             ])
        //                             ->where('jenis_operator_id',1)
        //                             ->where('jenis_operator_detail_id',1)
        //                             ->where('jenis_operator_detail_pekerjaan_id',3)
        //                             ->leftJoin('itic_emp_new.biodata_karyawan','biodata_karyawan.nik','=','operator_karyawan.nik')
        //                             ->rightJoin('pengerjaan','pengerjaan.operator_karyawan_id','=','operator_karyawan.id')
        //                             ->orderBy('biodata_karyawan.nama','asc')
        //                             ->get();

        $data['pengerjaans'] = $this->pengerjaanWeekly->select([
                                                    'pengerjaan_weekly.kode_payrol as kode_payrol',
                                                    'pengerjaan_weekly.operator_karyawan_id as operator_karyawan_id',
                                                    'operator_karyawan.nik as nik',
                                                    'biodata_karyawan.nama as nama',
                                                    'pengerjaan_weekly.upah_dasar as upah_dasar',
                                                    'pengerjaan_weekly.tunjangan_kerja as tunjangan_kerja',
                                                    'pengerjaan_weekly.tunjangan_kehadiran as tunjangan_kehadiran',
                                                    'pengerjaan_weekly.uang_makan as uang_makan',
                                                    'pengerjaan_weekly.plus_1 as plus_1',
                                                    'pengerjaan_weekly.plus_2 as plus_2',
                                                    'pengerjaan_weekly.plus_3 as plus_3',
                                                    'pengerjaan_weekly.minus_1 as minus_1',
                                                    'pengerjaan_weekly.minus_2 as minus_2',
                                                    'pengerjaan_weekly.jht as jht',
                                                    'pengerjaan_weekly.bpjs_kesehatan as bpjs_kesehatan',
                                                ])
                                                ->where('kode_payrol',substr($kode_pengerjaan,0,2).$kode_jenis_operator_detail.'_'.substr($kode_pengerjaan,3))
                                                ->where('operator_karyawan.jenis_operator_id',1)
                                                ->where('operator_karyawan.jenis_operator_detail_id',1)
                                                ->where('operator_karyawan.jenis_operator_detail_pekerjaan_id',3)
                                                ->leftJoin('operator_karyawan','operator_karyawan.id','=','pengerjaan_weekly.operator_karyawan_id')
                                                ->leftJoin('itic_emp_new.biodata_karyawan','biodata_karyawan.nik','=','operator_karyawan.nik')
                                                ->orderBy('biodata_karyawan.nama','asc')
                                                ->get();
        // dd($data['pengerjaans']);
        return view('backend.pengerjaan.packing_inner.packing_inner',$data);
    }

    public function hasil_kerja_inner_lokal_view_hasil($id, $kode_pengerjaan, $tanggal)
    {
        $data['id'] = $id;
        $data['kode_pengerjaan'] = $kode_pengerjaan;
        $data['tanggal'] = $tanggal;
        $data['packing_lokals'] = $this->karyawanOperator->select([
                                                    'operator_karyawan.id as id',
                                                    'operator_karyawan.nik as nik',
                                                    'biodata_karyawan.nama as nama',
                                                    'pengerjaan.hasil_kerja_1 as hasil_kerja_1',
                                                    'pengerjaan.hasil_kerja_2 as hasil_kerja_2',
                                                    'pengerjaan.hasil_kerja_3 as hasil_kerja_3',
                                                    'pengerjaan.hasil_kerja_4 as hasil_kerja_4',
                                                    'pengerjaan.hasil_kerja_5 as hasil_kerja_5',
                                                    'pengerjaan.total_jam_kerja_1 as total_jam_kerja_1',
                                                    'pengerjaan.total_jam_kerja_2 as total_jam_kerja_2',
                                                    'pengerjaan.total_jam_kerja_3 as total_jam_kerja_3',
                                                    'pengerjaan.total_jam_kerja_4 as total_jam_kerja_4',
                                                    'pengerjaan.total_jam_kerja_5 as total_jam_kerja_5',
                                                    ])
                                                    ->leftJoin('itic_emp_new.biodata_karyawan','biodata_karyawan.nik','=','operator_karyawan.nik')
                                                    ->rightJoin('pengerjaan','pengerjaan.operator_karyawan_id','=','operator_karyawan.id')
                                                    ->where('jenis_operator_id',1)
                                                    ->where('jenis_operator_detail_id',1)
                                                    ->where('jenis_operator_detail_pekerjaan_id',3)
                                                    ->where('pengerjaan.tanggal_pengerjaan',$tanggal)
                                                    ->where('pengerjaan.kode_pengerjaan',$kode_pengerjaan)
                                                    // ->with('biodata_karyawan')
                                                    ->orderBy('biodata_karyawan.nama','asc')
                                                    ->get();

        $data['umk_borongan_lokals'] = $this->umkBoronganLokal->select('id','jenis_produk','umk_inner')->where('status','Y')->get();
        // dd($data);
        return view('backend.pengerjaan.packing_inner.input_hasil_kerja',$data);
    }

    public function hasil_kerja_inner_view_simpan(Request $request, $id, $kode_pengerjaan, $tanggal)
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

        $input = $request->all();
        $input['id'] = $id;
        $input['kode_pengerjaan'] = $kode_pengerjaan;
        $input['tanggal'] = $tanggal;

        $data['karyawan_pengerjaans'] = $this->pengerjaan->select([
                                                        'pengerjaan.id as id',
                                                        'operator_karyawan.nik as nik',
                                                        'pengerjaan.operator_karyawan_id as id_operator_karyawan'
                                                    ])
                                                    ->leftJoin('operator_karyawan','operator_karyawan.id','=','pengerjaan.operator_karyawan_id')
                                                    ->leftJoin('itic_emp_new.biodata_karyawan','biodata_karyawan.nik','=','operator_karyawan.nik')
                                                    ->where('tanggal_pengerjaan',$tanggal)
                                                    // ->where('kode_pengerjaan',$kode_pengerjaan)
                                                    // ->where('pengerjaan.operator_karyawan_id','operator_karyawan.id')
                                                    ->where('operator_karyawan.jenis_operator_id',1)
                                                    ->where('operator_karyawan.jenis_operator_detail_id',1)
                                                    ->where('operator_karyawan.jenis_operator_detail_pekerjaan_id',3)
                                                    ->where('pengerjaan.kode_pengerjaan',$kode_pengerjaan)
                                                    ->orderBy('biodata_karyawan.nama','asc')
                                                    ->get();
        
        foreach ($data['karyawan_pengerjaans'] as $key => $karyawan_pengerjaan) {
            // dd($request->umk_borongan_lokal_kerja_1);
            if ($request->umk_borongan_lokal_kerja_1) {
                $umk_borongan_lokal_1 = $this->umkBoronganLokal->select('id','jenis_produk','umk_inner')
                                                        ->where('id',$request->umk_borongan_lokal_kerja_1)
                                                        ->where('status','Y')
                                                        ->first();

                // $hasil_pengerjaan_1 = $umk_borongan_lokal_1->id.'|'.$umk_borongan_lokal_1->umk_packing*$request->hasil_kerja_1[$key];
                
                // if ($request->hasil_kerja_1[$key]) {
                // }

                if ($request->hasil_kerja_1[$key]) {
                    $hasil_pengerjaan_1 = $umk_borongan_lokal_1->id.'|'.$request->hasil_kerja_1[$key];
                    $hasil_kerja_1 = $umk_borongan_lokal_1->umk_inner*$request->hasil_kerja_1[$key];
                }else{
                    $hasil_pengerjaan_1 = $umk_borongan_lokal_1->id.'|'.'0';
                    $hasil_kerja_1 = 0;
                }

                if($request->lembur_kerja_1){
                    $lembur_1 = '1-y';
                    $hasil_lembur_1 = '1.5';
                    $total_upah_lembur_1 = $hasil_kerja_1*1.5;
                    // $total_upah_lembur_1 = ($hasil_kerja_1*1.5)-$hasil_kerja_1;
                }else{
                    $lembur_1 = '1-n';
                    $hasil_lembur_1 = '1';
                    $total_upah_lembur_1 = $hasil_kerja_1*0;
                }

                if($request->total_jam_1[$key]){
                    $total_jam_1 = $request->total_jam_1[$key];
                }else{
                    $total_jam_1 = '0';
                }
                
            }else{
                $hasil_pengerjaan_1 = '0'.'|'.'0';
                $lembur_1 = '1-n';
                $hasil_lembur_1 = '1';
                $total_jam_1 = '0';
                $hasil_kerja_1 = 0;
                $total_upah_lembur_1 = '0';
            }

            $total_hasil_lembur_1 = $total_upah_lembur_1;

            // dd($hasil_pengerjaan_1);
            
            if ($request->umk_borongan_lokal_kerja_2) {
                $umk_borongan_lokal_2 = $this->umkBoronganLokal->select('id','jenis_produk','umk_inner')
                                                        ->where('id',$request->umk_borongan_lokal_kerja_2)
                                                        ->where('status','Y')
                                                        ->first();

                // $hasil_pengerjaan_2 = $umk_borongan_lokal_2->id.'|'.$umk_borongan_lokal_2->umk_packing*$request->hasil_kerja_2[$key];
                
                if ($request->hasil_kerja_2[$key]) {
                    $hasil_pengerjaan_2 = $umk_borongan_lokal_2->id.'|'.$request->hasil_kerja_2[$key];
                    $hasil_kerja_2 = $umk_borongan_lokal_2->umk_inner*$request->hasil_kerja_2[$key];
                }else{
                    $hasil_pengerjaan_2 = $umk_borongan_lokal_2->id.'|'.'0';
                    $hasil_kerja_2 = 0;
                }

                if($request->lembur_kerja_2){
                    $lembur_2 = '2-y';
                    $hasil_lembur_2 = '1.5';
                    // $total_upah_lembur_2 = ($hasil_kerja_2*1.5)-$hasil_kerja_2;
                    $total_upah_lembur_2 = $hasil_kerja_2*1.5;
                }else{
                    $lembur_2 = '2-n';
                    $hasil_lembur_2 = '1';
                    $total_upah_lembur_2 = $hasil_kerja_2*0;
                }

                if($request->total_jam_2[$key]){
                    $total_jam_2 = $request->total_jam_2[$key];
                }else{
                    $total_jam_2 = '0';
                }

            }else{
                $hasil_pengerjaan_2 = '0'.'|'.'0';
                $lembur_2 = '2-n';
                $hasil_lembur_2 = '1';
                $total_jam_2 = '0';
                $hasil_kerja_2 = 0;
                $total_upah_lembur_2 = '0';
            }

            $total_hasil_lembur_2 = $total_upah_lembur_2;

            if ($request->umk_borongan_lokal_kerja_3) {
                $umk_borongan_lokal_3 = $this->umkBoronganLokal->select('id','jenis_produk','umk_inner')
                                                        ->where('id',$request->umk_borongan_lokal_kerja_3)
                                                        ->where('status','Y')
                                                        ->first();

                // $hasil_pengerjaan_3 = $umk_borongan_lokal_3->id.'|'.$umk_borongan_lokal_3->umk_packing*$request->hasil_kerja_3[$key];
                
                if ($request->hasil_kerja_3[$key]) {
                    $hasil_pengerjaan_3 = $umk_borongan_lokal_3->id.'|'.$request->hasil_kerja_3[$key];
                    $hasil_kerja_3 = $umk_borongan_lokal_3->umk_inner*$request->hasil_kerja_3[$key];
                }else{
                    $hasil_pengerjaan_3 = $umk_borongan_lokal_3->id.'|'.'0';
                    $hasil_kerja_3 = 0;
                }
            
                if($request->lembur_kerja_3){
                    $lembur_3 = '3-y';
                    $hasil_lembur_3 = '1.5';
                    // $total_upah_lembur_3 = ($hasil_kerja_3*1.5)-$hasil_kerja_3;
                    $total_upah_lembur_3 = $hasil_kerja_3*1.5;
                }else{
                    $lembur_3 = '3-n';
                    $hasil_lembur_3 = '1';
                    $total_upah_lembur_3 = $hasil_kerja_3*0;
                }

                if($request->total_jam_3[$key]){
                    $total_jam_3 = $request->total_jam_3[$key];
                }else{
                    $total_jam_3 = '0';
                }

            }else{
                $hasil_pengerjaan_3 = '0'.'|'.'0';
                $lembur_3 = '3-n';
                $hasil_lembur_3 = '1';
                $total_jam_3 = '0';
                $hasil_kerja_3 = 0;
                $total_upah_lembur_3 = '0';
            }

            $total_hasil_lembur_3 = $total_upah_lembur_3;

            if ($request->umk_borongan_lokal_kerja_4) {
                $umk_borongan_lokal_4 = $this->umkBoronganLokal->select('id','jenis_produk','umk_inner')
                                                        ->where('id',$request->umk_borongan_lokal_kerja_4)
                                                        ->where('status','Y')
                                                        ->first();

                // $hasil_pengerjaan_4 = $umk_borongan_lokal_4->id.'|'.$umk_borongan_lokal_4->umk_packing*$request->hasil_kerja_4[$key];
                
                if ($request->hasil_kerja_4[$key]) {
                    $hasil_pengerjaan_4 = $umk_borongan_lokal_4->id.'|'.$request->hasil_kerja_4[$key];
                    $hasil_kerja_4 = $umk_borongan_lokal_4->umk_inner*$request->hasil_kerja_4[$key];
                }else{
                    $hasil_pengerjaan_4 = $umk_borongan_lokal_4->id.'|'.'0';
                    $hasil_kerja_4 = 0;
                }
            
                if($request->lembur_kerja_4){
                    $lembur_4 = '4-y';
                    $hasil_lembur_4 = '1.5';
                    // $total_upah_lembur_4 = ($hasil_kerja_4*1.5)-$hasil_kerja_4;
                    $total_upah_lembur_4 = $hasil_kerja_4*1.5;
                }else{
                    $lembur_4 = '4-n';
                    $hasil_lembur_4 = '1';
                    $total_upah_lembur_4 = $hasil_kerja_4*0;
                }

                if($request->total_jam_4[$key]){
                    $total_jam_4 = $request->total_jam_4[$key];
                }else{
                    $total_jam_4 = '0';
                }

            }else{
                $hasil_pengerjaan_4 = '0'.'|'.'0';
                $lembur_4 = '4-n';
                $hasil_lembur_4 = '1';
                $total_jam_4 = '0';
                $hasil_kerja_4 = 0;
                $total_upah_lembur_4 = '0';
            }

            $total_hasil_lembur_4 = $total_upah_lembur_4;

            if ($request->umk_borongan_lokal_kerja_5) {
                $umk_borongan_lokal_5 = $this->umkBoronganLokal->select('id','jenis_produk','umk_inner')
                                                        ->where('id',$request->umk_borongan_lokal_kerja_5)
                                                        ->where('status','Y')
                                                        ->first();

                // $hasil_pengerjaan_5 = $umk_borongan_lokal_5->id.'|'.$umk_borongan_lokal_5->umk_packing*$request->hasil_kerja_5[$key];
                
                if ($request->hasil_kerja_5[$key]) {
                    $hasil_pengerjaan_5 = $umk_borongan_lokal_5->id.'|'.$request->hasil_kerja_5[$key];
                    $hasil_kerja_5 = $umk_borongan_lokal_5->umk_inner*$request->hasil_kerja_5[$key];
                }else{
                    $hasil_pengerjaan_5 = $umk_borongan_lokal_5->id.'|'.'0';
                    $hasil_kerja_5 = 0;
                }
            
                if($request->lembur_kerja_5){
                    $lembur_5 = '5-y';
                    $hasil_lembur_5 = '1.5';
                    // $total_upah_lembur_5 = ($hasil_kerja_5*1.5)-$hasil_kerja_5;
                    $total_upah_lembur_5 = $hasil_kerja_5*1.5;
                }else{
                    $lembur_5 = '5-n';
                    $hasil_lembur_5 = '1';
                    $total_upah_lembur_5 = $hasil_kerja_5*0;
                }

                if($request->total_jam_5[$key]){
                    $total_jam_5 = $request->total_jam_5[$key];
                }else{
                    $total_jam_5 = '0';
                }

            }else{
                $hasil_pengerjaan_5 = '0'.'|'.'0';
                $lembur_5 = '5-n';
                $hasil_lembur_5 = '1';
                $total_jam_5 = '0';
                $hasil_kerja_5 = 0;
                $total_upah_lembur_5 = '0';
            }

            $total_hasil_lembur_5 = $total_upah_lembur_5;

            $lemburs = '|'.$lembur_1.'|'.$lembur_2.'|'.$lembur_3.'|'.$lembur_4.'|'.$lembur_5;
            $hasil_upah_dasar = $hasil_kerja_1+$hasil_kerja_2+$hasil_kerja_3+$hasil_kerja_4+$hasil_kerja_5;
            
            $penjumlahan_lembur = round($total_hasil_lembur_1+$total_hasil_lembur_2+$total_hasil_lembur_3+$total_hasil_lembur_4+$total_hasil_lembur_5);

            // dd($hasil_upah_dasar);
            $karyawan_pengerjaan->update([
                'hasil_kerja_1' => $hasil_pengerjaan_1,
                'hasil_kerja_2' => $hasil_pengerjaan_2,
                'hasil_kerja_3' => $hasil_pengerjaan_3,
                'hasil_kerja_4' => $hasil_pengerjaan_4,
                'hasil_kerja_5' => $hasil_pengerjaan_5,
                'uang_lembur' => $penjumlahan_lembur,
                'lembur' => $lemburs,
                'total_jam_kerja_1' => $total_jam_1,
                'total_jam_kerja_2' => $total_jam_2,
                'total_jam_kerja_3' => $total_jam_3,
                'total_jam_kerja_4' => $total_jam_4,
                'total_jam_kerja_5' => $total_jam_5,
            ]);
        }

        return response()->json([
            'success' => true,
            'message_title' => 'Berhasil',
            'message_content' => 'Data Berhasil Disimpan'
        ]);
    }

    public function hasil_kerja_karyawan_inner_view($id, $kode_pengerjaan, $nik, $month, $year)
    {
        $data['id'] = $id;
        $data['nik'] = $nik;
        $data['kode_pengerjaan'] = $kode_pengerjaan;
        $data['month'] = $month;
        $data['year'] = $year;
        // dd($data);

        if($id == 1){
            $kode_jenis_operator_detail = 'L';
        }
        elseif($id == 2){
            $kode_jenis_operator_detail = 'E';
        }
        elseif($id == 3){
            $kode_jenis_operator_detail = 'A';
        }
        $data['karyawan'] = $this->karyawanOperator->select([
                                                    'operator_karyawan.id as id',
                                                    'biodata_karyawan.nama as nama',
                                                    'operator_karyawan.nik as nik',
                                                    'biodata_karyawan.pin as pin',
                                                    'biodata_karyawan.tanggal_masuk as tanggal',
                                                    'operator_karyawan.tunjangan_kerja_id as tunjangan_kerja_id'
                                                    ])
                                                    ->leftJoin('itic_emp_new.biodata_karyawan','biodata_karyawan.nik','=','operator_karyawan.nik')
                                                    ->where('operator_karyawan.nik',$nik)
                                                    ->where('status','Y')
                                                    ->first();

        $data['new_data_pengerjaan'] = $this->newDataPengerjaan->select('akhir_bulan')->where('kode_pengerjaan',$kode_pengerjaan)->first();
        $data['jhts'] = $this->bpjsJht->where('status','y')->get();
        $data['bpjs_kesehatan'] = $this->bpjsKesehatan->select('nominal')->where('status','y')->first();
        // dd($data['new_data_pengerjaan']);

        $data['pengerjaans'] = $this->pengerjaan->where('operator_karyawan_id',$data['karyawan']['id'])->where('kode_pengerjaan',$kode_pengerjaan)->get();
        
        $data['upah'] = array();

        foreach ($data['pengerjaans'] as $key => $pengerjaan) {
            $explode_hasil_kerja_1 = explode("|",$pengerjaan->hasil_kerja_1);
            $umk_borongan_lokal_1 = $this->umkBoronganLokal->select('id','jenis_produk','umk_packing','umk_bandrol','umk_inner','umk_outer')->where('id',$explode_hasil_kerja_1[0])->first();
            if(empty($umk_borongan_lokal_1)){
                $jenis_produk_1 = '-';
                $hasil_kerja_1 = null;
                $data_explode_hasil_kerja_1 = '-';
                $lembur_1 = 1;
            }else{
                $jenis_produk_1 = $umk_borongan_lokal_1->jenis_produk;
                $hasil_kerja_1 = $explode_hasil_kerja_1[1]*$umk_borongan_lokal_1->umk_inner;
                $data_explode_hasil_kerja_1 = $explode_hasil_kerja_1[1];

                $explode_lembur_1 = explode("|",$pengerjaan->lembur);
                $explode_status_lembur_1 = explode("-",$explode_lembur_1[1]);

                if($explode_status_lembur_1[1] == 'y'){
                    $lembur_1 = 1.5;
                }else{
                    $lembur_1 = 1;
                }
            }

            $explode_hasil_kerja_2 = explode("|",$pengerjaan->hasil_kerja_2);
            $umk_borongan_lokal_2 = $this->umkBoronganLokal->select('id','jenis_produk','umk_packing','umk_bandrol','umk_inner','umk_outer')->where('id',$explode_hasil_kerja_2[0])->first();
            if(empty($umk_borongan_lokal_2)){
                $jenis_produk_2 = '-';
                $hasil_kerja_2 = null;
                $data_explode_hasil_kerja_2 = '-';
                $lembur_2 = 1;
            }else{
                $jenis_produk_2 = $umk_borongan_lokal_2->jenis_produk;
                $hasil_kerja_2 = $explode_hasil_kerja_2[1]*$umk_borongan_lokal_2->umk_inner;
                $data_explode_hasil_kerja_2 = $explode_hasil_kerja_2[1];

                $explode_lembur_2 = explode("|",$pengerjaan->lembur);
                $explode_status_lembur_2 = explode("-",$explode_lembur_2[2]);

                if($explode_status_lembur_2[1] == 'y'){
                    $lembur_2 = 1.5;
                }else{
                    $lembur_2 = 1;
                }
            }

            $explode_hasil_kerja_3 = explode("|",$pengerjaan->hasil_kerja_3);
            $umk_borongan_lokal_3 = $this->umkBoronganLokal->select('id','jenis_produk','umk_packing','umk_bandrol','umk_inner','umk_outer')->where('id',$explode_hasil_kerja_3[0])->first();
            if(empty($umk_borongan_lokal_3)){
                $jenis_produk_3 = '-';
                $hasil_kerja_3 = null;
                $data_explode_hasil_kerja_3 = '-';
                $lembur_3 = 1;
            }else{
                $jenis_produk_3 = $umk_borongan_lokal_3->jenis_produk;
                $hasil_kerja_3 = $explode_hasil_kerja_3[1]*$umk_borongan_lokal_3->umk_inner;
                $data_explode_hasil_kerja_3 = $explode_hasil_kerja_3[1];

                $explode_lembur_3 = explode("|",$pengerjaan->lembur);
                $explode_status_lembur_3 = explode("-",$explode_lembur_3[3]);

                if($explode_status_lembur_3[1] == 'y'){
                    $lembur_3 = 1.5;
                }else{
                    $lembur_3 = 1;
                }
            }

            $explode_hasil_kerja_4 = explode("|",$pengerjaan->hasil_kerja_4);
            $umk_borongan_lokal_4 = $this->umkBoronganLokal->select('id','jenis_produk','umk_packing','umk_bandrol','umk_inner','umk_outer')->where('id',$explode_hasil_kerja_4[0])->first();
            if(empty($umk_borongan_lokal_4)){
                $jenis_produk_4 = '-';
                $hasil_kerja_4 = null;
                $data_explode_hasil_kerja_4 = '-';
                $lembur_4 = 1;
            }else{
                $jenis_produk_4 = $umk_borongan_lokal_4->jenis_produk;
                $hasil_kerja_4 = $explode_hasil_kerja_4[1]*$umk_borongan_lokal_4->umk_inner;
                $data_explode_hasil_kerja_4 = $explode_hasil_kerja_4[1];

                $explode_lembur_4 = explode("|",$pengerjaan->lembur);
                $explode_status_lembur_4 = explode("-",$explode_lembur_4[4]);

                if($explode_status_lembur_4[1] == 'y'){
                    $lembur_4 = 1.5;
                }else{
                    $lembur_4 = 1;
                }
            }

            $explode_hasil_kerja_5 = explode("|",$pengerjaan->hasil_kerja_5);
            $umk_borongan_lokal_5 = $this->umkBoronganLokal->select('id','jenis_produk','umk_packing','umk_bandrol','umk_inner','umk_outer')->where('id',$explode_hasil_kerja_5[0])->first();
            if(empty($umk_borongan_lokal_5)){
                $jenis_produk_5 = '-';
                $hasil_kerja_5 = null;
                $data_explode_hasil_kerja_5 = '-';
                $lembur_5 = 1;
            }else{
                $jenis_produk_5 = $umk_borongan_lokal_5->jenis_produk;
                $hasil_kerja_5 = $explode_hasil_kerja_5[1]*$umk_borongan_lokal_5->umk_inner;
                $data_explode_hasil_kerja_5 = $explode_hasil_kerja_5[1];

                $explode_lembur_5 = explode("|",$pengerjaan->lembur);
                $explode_status_lembur_5 = explode("-",$explode_lembur_5[5]);

                if($explode_status_lembur_5[1] == 'y'){
                    $lembur_5 = 1.5;
                }else{
                    $lembur_5 = 1;
                }
            }

            $hasil_upah = ($hasil_kerja_1*$lembur_1)+($hasil_kerja_2*$lembur_2)+($hasil_kerja_3*$lembur_3)+($hasil_kerja_4*$lembur_4)+($hasil_kerja_5*$lembur_5);

            array_push($data['upah'],$hasil_upah);
        }

        //Hitung Masa Kerja
        // $data['karyawan_masa_kerja'] = DB::connection('emp')->table('log_posisi')
        //                                                     ->select('tanggal')
        //                                                     ->where('nik',$nik)
        //                                                     ->first();
        $awal  = new DateTime($data['karyawan']->tanggal);
        $akhir = new DateTime(); // Waktu sekarang
        $diff  = $awal->diff($akhir);
        $data['masa_kerja'] = $diff->y.' Tahun '.$diff->m.' Bulan '.$diff->d.' Hari';
        $data['masa_kerja_tahun'] = $diff->y;
        $data['masa_kerja_hari'] = $diff->d;
        //End Hitung Masa Kerja

        $data['upah_dasar_karyawan'] = [];
        foreach ($data['jhts'] as $key => $jht) {
            if ($data['masa_kerja_tahun'] > 15) {
                if ($jht->urutan == 3) {
                    $data['upah_dasar_karyawan'] = ($data['bpjs_kesehatan']['nominal'] + 100000)/25;
                }
            }
            elseif($data['masa_kerja_tahun'] >= 10 && $data['masa_kerja_tahun'] <= 15 && $data['masa_kerja_hari'] >= 1){
                if ($jht->urutan == 2) {
                    $data['upah_dasar_karyawan'] = ($data['bpjs_kesehatan']['nominal'] + 50000)/25;
                }
            }
            elseif($data['masa_kerja_tahun'] <= 10 || $data['masa_kerja_hari'] >= 1){
                if ($jht->urutan == 1) {
                    $data['upah_dasar_karyawan'] = ($data['bpjs_kesehatan']['nominal'] + 0)/25;
                }
            }
        }

        // echo 'Selisih waktu: ';
        // echo $diff->y . ' tahun, ';
        // echo $diff->m . ' bulan, ';
        // echo $diff->d . ' hari, ';
        // echo $diff->h . ' jam, ';
        // echo $diff->i . ' menit, ';
        // echo $diff->s . ' detik, ';
        // dd($data);

        $data['pengerjaan_weekly'] = $this->pengerjaanWeekly->where('operator_karyawan_id',$data['karyawan']['id'])
                                                    ->where('kode_payrol',substr($kode_pengerjaan,0,2).$kode_jenis_operator_detail.'_'.substr($kode_pengerjaan,3))
                                                    ->first();
        // dd($data['pengerjaan_weekly']);
        // $data['presensi_info'] = PresensiInfo::all();
        // dd($data['presensi_info']);
        $tgl_current_active = Carbon::now()->format('d');
        $bln_active = Carbon::now()->format('m');
        $bln_current_active = $month;
        $thn_current_active = $year;

        // $bulan_kemarin = Carbon::now()->subMonth()->format('Y-m').'-26';
        // $bulan_sekarang = Carbon::now()->format('Y-m').'-25';

        if($month == 1 && $tgl_current_active <10){
            $thn_prev_active = $year-1;
            $bulan_kemarin="$thn_prev_active-11-26";
            $bulan_sekarang="$thn_prev_active-12-26";
        }elseif($month == 1 && $tgl_current_active>10 or $month == 2 && $tgl_current_active<10 && $bln_active != 3){
            $thn_prev_active = $year-1;
            $bulan_kemarin="$thn_prev_active-12-26";
            $bulan_sekarang="$thn_current_active-01-26";
        }else{
            $bln_prev_active = $month-1; 
            $thn_prev_active = $year;
            $bulan_kemarin="$thn_prev_active-$bln_prev_active-26";
            $bulan_sekarang="$thn_current_active-$bln_current_active-26";
        }

        $data['tunjangan_kerjas'] = $this->tunjanganKerja->select('nominal')->where('id',$data['karyawan']['tunjangan_kerja_id'])->first();
        if(empty($data['tunjangan_kerjas'])){
            $data['tunjangan_kerja'] = 0;
        }else{
            $data['tunjangan_kerja'] = $data['tunjangan_kerjas']['nominal'];
        }
        //jumlah alpa
        $data['alpa'] = $this->presensiInfo->where('pin',$data['karyawan']['pin'])
                                    ->whereBetween('scan_date',[$bulan_kemarin,$bulan_sekarang])
                                    ->where('status',7)
                                    ->get();
        //jumlah diliburkan
        $data['diliburkan'] = $this->presensiInfo->where('pin',$data['karyawan']['pin'])
                                    ->whereBetween('scan_date',[$bulan_kemarin,$bulan_sekarang])
                                    ->where('status',14)
                                    ->get();
        //jumlah cuti
        $data['cuti'] = $this->presensiInfo->where('pin',$data['karyawan']['pin'])
                                    ->whereBetween('scan_date',[$bulan_kemarin,$bulan_sekarang])
                                    ->where('status',13)
                                    ->get();
        //jumlah sakit
        $data['sakit'] = $this->presensiInfo->where('pin',$data['karyawan']['pin'])
                                    ->whereBetween('scan_date',[$bulan_kemarin,$bulan_sekarang])
                                    ->where('status',4)
                                    ->get();
        // dd($data['sakit']);
        //jumlah ijin full
        $data['ijin_full'] = $this->presensiInfo->where('pin',$data['karyawan']['pin'])
                                    ->whereBetween('scan_date',[$bulan_kemarin,$bulan_sekarang])
                                    ->where('status',6)
                                    ->get();
        //jumlah status terlambat
        $data['terlambats'] = $this->presensiInfo->where('pin',$data['karyawan']['pin'])
                                    ->whereBetween('scan_date',[$bulan_kemarin,$bulan_sekarang])
                                    ->where('status',3)
                                    ->get();
        $telat_1=0; //terlambat < 5 menit
        $telat_2=0; //terlambat > 5 menit < 15 menit
        $telat_3=0; //terlambat > 15 menit < 1 jam
        $telat_4=0; //terlambat > 1 jam < 3 jam	
        $telat_5=0; //terlambat > 3 jam	
        $data['telat_1'] = $telat_1;
        $data['telat_2'] = $telat_2;
        $data['telat_3'] = $telat_3;
        $data['telat_4'] = $telat_4;
        $data['telat_5'] = $telat_5;
        foreach ($data['terlambats'] as $key => $terlambat) {
            $explode_keterangan_terlambat = explode("@",$terlambat->keterangan);
            $jam_keterangan_terlambat=strtotime($explode_keterangan_terlambat[1]);
            $jam_datang_terlambat=strtotime(Carbon::parse($terlambat['scan_date'])->format('H:i'));
			// $jam_datang_terlambat=strtotime($terlambat['jam_datang_telat']);
			$menit_telat=(($jam_datang_terlambat-$jam_keterangan_terlambat)/60);
			if($menit_telat<=4){
                $telat_1=$telat_1+1;
                $data['telat_1'] = $telat_1;
            }
			else if(($menit_telat>=5) && ($menit_telat<=15) ){
                $telat_2=$telat_2+1;
                $data['telat_2'] = $telat_2;
            }
			else if(($menit_telat>=16) && ($menit_telat<=60)){
                $telat_3=$telat_3+1;
                $data['telat_3'] = $telat_3;
            }
			else if(($menit_telat>=61) && ($menit_telat<=179)){
                $telat_4=$telat_4+1;
                $data['telat_4'] = $telat_4;
            }else if($menit_telat > 179){
                $telat_5=$telat_5+1;
                $data['telat_5'] = $telat_5;
            }
			// else{
            //     $telat_4=$telat_4+1;
            //     $data['telat_4'] = $telat_4;
            // }
        }

        //jumlah status pulang awal
        $data['pulang_awals'] = $this->presensiInfo->where('pin',$data['karyawan']['pin'])
                                    ->whereBetween('scan_date',[$bulan_kemarin,$bulan_sekarang])
                                    ->where(function($query) {
                                        $query->where('status','=',9)
                                            ->orWhere('status','=',10);
                                    })
                                    // ->where('status',9)
                                    // ->where('status',10)
                                    ->get();
        // dd($data['pulang_awals']);
        $pulang_1=0; //pulang awal < 4 jam
		$pulang_2=0; //tpulang awal > 4 jam
        $data['pulang_1'] = $pulang_1;
        $data['pulang_2'] = $pulang_2;
        foreach ($data['pulang_awals'] as $key => $pulang_awal) {
            $explode_pulang_seharusnya=explode("@",$pulang_awal->keterangan);
			$jam_pulang_seharusnya=strtotime($explode_pulang_seharusnya[3]);
			$jam_pulang_awal=strtotime(Carbon::parse($pulang_awal['scan_date'])->format('H:i'));
            // $jam_pulang_awal=strtotime($pulang_awal->jam_pulang_awal);
			$menit_sisa_jam_kerja=(($jam_pulang_seharusnya-$jam_pulang_awal)/60);
			if ($menit_sisa_jam_kerja<=300){
                $pulang_1=$pulang_1+1;
                $data['pulang_1'] = $pulang_1;
            }
			else{
                $pulang_2=$pulang_2+1;
                $data['pulang_2'] = $pulang_2;
            }
        }

        //jumlah ijin saat jam kerja
        $data['keluar_masuks'] = $this->keluarMasuk->where('nik',$data['karyawan']['nik'])
                                    ->whereBetween('tanggal_ijin',[$bulan_kemarin,$bulan_sekarang])
                                    ->get();
        // dd($data['keluar_masuks']);
        $ijin_15=0; //ijin kurang dari 15 menit
		$ijin_k4=0; //ijin > 15menit < 4 jam
		$ijin_l4=0; //ijin > 4 jam
        $data['ijin_15']=$ijin_15;
        $data['ijin_k4']=$ijin_k4;
        $data['ijin_l4']=$ijin_l4;
        foreach ($data['keluar_masuks'] as $key => $keluar_masuk) {
            $jam_keluar=strtotime($keluar_masuk->jam_keluar);
			$jam_datang=strtotime($keluar_masuk->jam_datang);
			$menit_keluar_masuk=(($jam_datang-$jam_keluar)/60);
			if ($menit_keluar_masuk<=15){
                $ijin_15=$ijin_15+1;
                $data['ijin_15']=$ijin_15;
            }
			else if (($menit_keluar_masuk>15)&&($menit_keluar_masuk<=239)){
                $ijin_k4=$ijin_k4+1;
                $data['ijin_k4']=$ijin_k4;
            }
			else {
                $ijin_l4=$ijin_l4+1;
                $data['ijin_l4']=$ijin_l4;
            }
        }

        //lihat tanggal masuk
        // $data['log_posisi'] = $this->logPosisi->select('tanggal')->where('nik',$nik)->first();
		$selisih_tanggal_masuk=strtotime($bulan_kemarin)-strtotime($data['karyawan']['tanggal']);
		if ($selisih_tanggal_masuk>=0)$div_tk=0;else $div_tk=75000;

        $total_potongan_tk=(75000*$data['alpa']->count())+(75000*$data['diliburkan']->count())+(75000*$data['sakit']->count())+(75000*$data['cuti']->count())+(75000*$data['ijin_full']->count())+($data['ijin_15']*25000)+($data['ijin_k4']*40000)+
        ($data['ijin_l4']*75000)+($data['pulang_1']*40000)+($data['pulang_2']*75000)+($data['telat_1']*15000)+($data['telat_2']*25000)+($data['telat_3']*30000)+($data['telat_4']*40000)+$div_tk;
        if ($total_potongan_tk>75000){$total_potongan_tk=75000;}else {}

        // if ($data['pengerjaan_weekly']['tunjangan_kehadiran'] == 75000) {
        //     $data['total_potongan_tk']=0;
        // }
        // // elseif($data['pengerjaan_weekly']['tunjangan_kehadiran'] < 75000){
        // //     $data['total_potongan_tk']=$data['pengerjaan_weekly']['tunjangan_kehadiran'];
        // // }
        // else{
        //     $data['total_potongan_tk']=$total_potongan_tk; 
        // }

        $data['total_potongan_tk']=$total_potongan_tk;
        return view('backend.pengerjaan.packing_inner.input_hasil_kerja_karyawan',$data);
    }

    public function hasil_kerja_karyawan_inner_view_simpan(Request $request, $id, $kode_pengerjaan, $nik)
    {
        // $input = $request->all();
        if($id == 1){
            $kode_jenis_operator_detail = 'L';
        }
        elseif($id == 2){
            $kode_jenis_operator_detail = 'E';
        }
        elseif($id == 3){
            $kode_jenis_operator_detail = 'A';
        }

        $data['id'] = $id;
        $data['kode_pengerjaan'] = $kode_pengerjaan;
        $data['nik'] = $nik;

        $input['upah_dasar'] = $request->upah_dasar;
        $input['upah_makan'] = $request->upah_makan;
        
        $input['uang_makan'] = $request->uang_makan;
        $input['plus_1'] = $request->plus_1."|".$request->keterangan_plus_1;
        $input['plus_2'] = $request->plus_2."|".$request->keterangan_plus_2;
        $input['plus_3'] = $request->plus_3."|".$request->keterangan_plus_3;
        
        $input['minus_1'] = $request->minus_1."|".$request->keterangan_minus_1;
        $input['minus_2'] = $request->minus_2."|".$request->keterangan_minus_2;

        if ($request->akhir_bulan == 'n') {
            $input['tunjangan_kerja'] = 0;
            $input['tunjangan_kehadiran'] = 0;
        }else{
            $input['tunjangan_kerja'] = $request->tunjangan_kerja;
            $input['tunjangan_kehadiran'] = 75000-$request->pot_tunjangan_kehadiran;
        }

        if ($request->check_jht == 'on') {
            $input['jht'] = $request->jht;
        }else{
            $input['jht'] = 0;
        }

        if($request->check_bpjs_kesehatan == 'on'){
            $input['bpjs_kesehatan'] = $request->bpjs_kesehatan;
        }else{
            $input['bpjs_kesehatan'] = 0;
        }
        // dd($input);
        $operator_karyawan = $this->karyawanOperator->select('id')
                                            ->where('nik',$nik)
                                            ->where('status','Y')
                                            ->where('jenis_operator_detail_pekerjaan_id',3)
                                            // ->orderBy('id','desc')
                                            ->first();
        // dd($operator_karyawan->id);
        // dd(substr($kode_pengerjaan,0,2).$kode_jenis_operator_detail.'_'.substr($kode_pengerjaan,3));
        // $pengerjaan_weekly = PengerjaanWeekly::where('kode_payrol',$kode_pengerjaan)
        $pengerjaan_weekly = $this->pengerjaanWeekly->where('kode_payrol',substr($kode_pengerjaan,0,2).$kode_jenis_operator_detail.'_'.substr($kode_pengerjaan,3))
                                            ->where('operator_karyawan_id',$operator_karyawan->id)
                                            ->first();
        // dd($pengerjaan_weekly);
        // $pengerjaan_weekly->update($input);
        // dd($pengerjaan_weekly);
        if (!empty($pengerjaan_weekly)) {
            $pengerjaan_weekly->update($input);
            return response()->json([
                'success' => true,
                'message_title' => 'Berhasil',
                'message_content' => 'Data Berhasil Disimpan'
            ]);
        }
        else{
            return response()->json([
                'success' => false,
                'message_title' => 'Gagal',
                'message_content' => 'Data Tidak Berhasil Disimpan'
            ]);
        }
    }

    public function hasil_kerja_outer_lokal($id, $kode_pengerjaan)
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

        $data['id'] = $id;
        $data['kode_pengerjaan'] = $kode_pengerjaan;
        $data['jenis_operator_detail_pekerjaan'] = $this->jenisOperatorDetailPengerjaan->find(4);
        $data['new_data_pengerjaan'] = $this->newDataPengerjaan->where('kode_pengerjaan',$kode_pengerjaan)->first();
        $data['explode_tanggal_pengerjaans'] = explode('#',$data['new_data_pengerjaan']['tanggal']);
        $data['count_tanggal'] = count($data['explode_tanggal_pengerjaans'])-1;
        // $data['packing_lokals'] = KaryawanOperator::select([
        //                             'operator_karyawan.id as id',
        //                             'operator_karyawan.nik as nik',
        //                             'biodata_karyawan.nama as nama',
        //                             ])
        //                             ->where('jenis_operator_id',1)
        //                             ->where('jenis_operator_detail_id',1)
        //                             ->where('jenis_operator_detail_pekerjaan_id',4)
        //                             ->leftJoin('itic_emp_new.biodata_karyawan','biodata_karyawan.nik','=','operator_karyawan.nik')
        //                             ->rightJoin('pengerjaan','pengerjaan.operator_karyawan_id','=','operator_karyawan.id')
        //                             ->orderBy('biodata_karyawan.nama','asc')
        //                             ->get();

        $data['pengerjaans'] = $this->pengerjaanWeekly->select([
                                                    'pengerjaan_weekly.kode_payrol as kode_payrol',
                                                    'pengerjaan_weekly.operator_karyawan_id as operator_karyawan_id',
                                                    'operator_karyawan.nik as nik',
                                                    'biodata_karyawan.nama as nama',
                                                    'pengerjaan_weekly.upah_dasar as upah_dasar',
                                                    'pengerjaan_weekly.tunjangan_kerja as tunjangan_kerja',
                                                    'pengerjaan_weekly.tunjangan_kehadiran as tunjangan_kehadiran',
                                                    'pengerjaan_weekly.uang_makan as uang_makan',
                                                    'pengerjaan_weekly.plus_1 as plus_1',
                                                    'pengerjaan_weekly.plus_2 as plus_2',
                                                    'pengerjaan_weekly.plus_3 as plus_3',
                                                    'pengerjaan_weekly.minus_1 as minus_1',
                                                    'pengerjaan_weekly.minus_2 as minus_2',
                                                    'pengerjaan_weekly.jht as jht',
                                                    'pengerjaan_weekly.bpjs_kesehatan as bpjs_kesehatan',
                                                ])
                                                ->where('kode_payrol',substr($kode_pengerjaan,0,2).$kode_jenis_operator_detail.'_'.substr($kode_pengerjaan,3))
                                                ->where('operator_karyawan.jenis_operator_id',1)
                                                ->where('operator_karyawan.jenis_operator_detail_id',1)
                                                ->where('operator_karyawan.jenis_operator_detail_pekerjaan_id',4)
                                                ->leftJoin('operator_karyawan','operator_karyawan.id','=','pengerjaan_weekly.operator_karyawan_id')
                                                ->leftJoin('itic_emp_new.biodata_karyawan','biodata_karyawan.nik','=','operator_karyawan.nik')
                                                ->orderBy('biodata_karyawan.nama','asc')
                                                ->get();

        return view('backend.pengerjaan.packing_outer.packing_outer',$data);
    }

    public function hasil_kerja_outer_lokal_view_hasil($id, $kode_pengerjaan, $tanggal)
    {
        $data['id'] = $id;
        $data['kode_pengerjaan'] = $kode_pengerjaan;
        $data['tanggal'] = $tanggal;
        $data['packing_lokals'] = $this->karyawanOperator->select([
                                                    'operator_karyawan.id as id',
                                                    'operator_karyawan.nik as nik',
                                                    'biodata_karyawan.nama as nama',
                                                    'pengerjaan.hasil_kerja_1 as hasil_kerja_1',
                                                    'pengerjaan.hasil_kerja_2 as hasil_kerja_2',
                                                    'pengerjaan.hasil_kerja_3 as hasil_kerja_3',
                                                    'pengerjaan.hasil_kerja_4 as hasil_kerja_4',
                                                    'pengerjaan.hasil_kerja_5 as hasil_kerja_5',
                                                    'pengerjaan.total_jam_kerja_1 as total_jam_kerja_1',
                                                    'pengerjaan.total_jam_kerja_2 as total_jam_kerja_2',
                                                    'pengerjaan.total_jam_kerja_3 as total_jam_kerja_3',
                                                    'pengerjaan.total_jam_kerja_4 as total_jam_kerja_4',
                                                    'pengerjaan.total_jam_kerja_5 as total_jam_kerja_5',
                                                    ])
                                                    ->leftJoin('itic_emp_new.biodata_karyawan','biodata_karyawan.nik','=','operator_karyawan.nik')
                                                    ->rightJoin('pengerjaan','pengerjaan.operator_karyawan_id','=','operator_karyawan.id')
                                                    ->where('jenis_operator_id',1)
                                                    ->where('jenis_operator_detail_id',1)
                                                    ->where('jenis_operator_detail_pekerjaan_id',4)
                                                    ->where('pengerjaan.tanggal_pengerjaan',$tanggal)
                                                    ->where('pengerjaan.kode_pengerjaan',$kode_pengerjaan)
                                                    // ->with('biodata_karyawan')
                                                    ->orderBy('biodata_karyawan.nama','asc')
                                                    ->get();

        $data['umk_borongan_lokals'] = $this->umkBoronganLokal->select('id','jenis_produk','umk_outer')->where('status','Y')->get();
        // dd($data);
        return view('backend.pengerjaan.packing_outer.input_hasil_kerja',$data);
    }

    public function hasil_kerja_outer_view_simpan(Request $request, $id, $kode_pengerjaan, $tanggal)
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

        $input = $request->all();
        $input['id'] = $id;
        $input['kode_pengerjaan'] = $kode_pengerjaan;
        $input['tanggal'] = $tanggal;

        $data['karyawan_pengerjaans'] = $this->pengerjaan->select([
                                                        'pengerjaan.id as id',
                                                        'operator_karyawan.nik as nik',
                                                        'pengerjaan.operator_karyawan_id as id_operator_karyawan'
                                                    ])
                                                    ->leftJoin('operator_karyawan','operator_karyawan.id','=','pengerjaan.operator_karyawan_id')
                                                    ->leftJoin('itic_emp_new.biodata_karyawan','biodata_karyawan.nik','=','operator_karyawan.nik')
                                                    ->where('tanggal_pengerjaan',$tanggal)
                                                    // ->where('kode_pengerjaan',$kode_pengerjaan)
                                                    // ->where('pengerjaan.operator_karyawan_id','operator_karyawan.id')
                                                    ->where('operator_karyawan.jenis_operator_id',1)
                                                    ->where('operator_karyawan.jenis_operator_detail_id',1)
                                                    ->where('operator_karyawan.jenis_operator_detail_pekerjaan_id',4)
                                                    ->where('pengerjaan.kode_pengerjaan',$kode_pengerjaan)
                                                    ->orderBy('biodata_karyawan.nama','asc')
                                                    ->get();
        
        foreach ($data['karyawan_pengerjaans'] as $key => $karyawan_pengerjaan) {
            // dd($request->umk_borongan_lokal_kerja_1);
            if ($request->umk_borongan_lokal_kerja_1) {
                $umk_borongan_lokal_1 = $this->umkBoronganLokal->select('id','jenis_produk','umk_outer')
                                                        ->where('id',$request->umk_borongan_lokal_kerja_1)
                                                        ->where('status','Y')
                                                        ->first();

                // $hasil_pengerjaan_1 = $umk_borongan_lokal_1->id.'|'.$umk_borongan_lokal_1->umk_packing*$request->hasil_kerja_1[$key];
                
                // if ($request->hasil_kerja_1[$key]) {
                // }

                if ($request->hasil_kerja_1[$key]) {
                    $hasil_pengerjaan_1 = $umk_borongan_lokal_1->id.'|'.$request->hasil_kerja_1[$key];
                    $hasil_kerja_1 = $umk_borongan_lokal_1->umk_outer*$request->hasil_kerja_1[$key];
                }else{
                    $hasil_pengerjaan_1 = $umk_borongan_lokal_1->id.'|'.'0';
                    $hasil_kerja_1 = 0;
                }

                if($request->lembur_kerja_1){
                    $lembur_1 = '1-y';
                    $hasil_lembur_1 = '1.5';
                    // $total_upah_lembur_1 = ($hasil_kerja_1*1.5)-$hasil_kerja_1;
                    $total_upah_lembur_1 = $hasil_kerja_1*1.5;
                }else{
                    $lembur_1 = '1-n';
                    $hasil_lembur_1 = '1';
                    $total_upah_lembur_1 = $hasil_kerja_1*0;
                }

                if($request->total_jam_1[$key]){
                    $total_jam_1 = $request->total_jam_1[$key];
                }else{
                    $total_jam_1 = '0';
                }
                
            }else{
                $hasil_pengerjaan_1 = '0'.'|'.'0';
                $lembur_1 = '1-n';
                $hasil_lembur_1 = '1';
                $total_jam_1 = '0';
                $hasil_kerja_1 = 0;
                $total_upah_lembur_1 = '0';
            }

            $total_hasil_lembur_1 = $total_upah_lembur_1;

            // dd($hasil_pengerjaan_1);
            
            if ($request->umk_borongan_lokal_kerja_2) {
                $umk_borongan_lokal_2 = $this->umkBoronganLokal->select('id','jenis_produk','umk_outer')
                                                        ->where('id',$request->umk_borongan_lokal_kerja_2)
                                                        ->where('status','Y')
                                                        ->first();

                // $hasil_pengerjaan_2 = $umk_borongan_lokal_2->id.'|'.$umk_borongan_lokal_2->umk_packing*$request->hasil_kerja_2[$key];
                
                if ($request->hasil_kerja_2[$key]) {
                    $hasil_pengerjaan_2 = $umk_borongan_lokal_2->id.'|'.$request->hasil_kerja_2[$key];
                    $hasil_kerja_2 = $umk_borongan_lokal_2->umk_outer*$request->hasil_kerja_2[$key];
                }else{
                    $hasil_pengerjaan_2 = $umk_borongan_lokal_2->id.'|'.'0';
                    $hasil_kerja_2 = 0;
                }

                if($request->lembur_kerja_2){
                    $lembur_2 = '2-y';
                    $hasil_lembur_2 = '1.5';
                    // $total_upah_lembur_2 = ($hasil_kerja_2*1.5)-$hasil_kerja_2;
                    $total_upah_lembur_2 = $hasil_kerja_2*1.5;
                }else{
                    $lembur_2 = '2-n';
                    $hasil_lembur_2 = '1';
                    $total_upah_lembur_2 = $hasil_kerja_2*0;
                }

                if($request->total_jam_2[$key]){
                    $total_jam_2 = $request->total_jam_2[$key];
                }else{
                    $total_jam_2 = '0';
                }

            }else{
                $hasil_pengerjaan_2 = '0'.'|'.'0';
                $lembur_2 = '2-n';
                $hasil_lembur_2 = '1';
                $total_jam_2 = '0';
                $hasil_kerja_2 = 0;
                $total_upah_lembur_2 = '0';
            }

            $total_hasil_lembur_2 = $total_upah_lembur_2;

            if ($request->umk_borongan_lokal_kerja_3) {
                $umk_borongan_lokal_3 = $this->umkBoronganLokal->select('id','jenis_produk','umk_outer')
                                                        ->where('id',$request->umk_borongan_lokal_kerja_3)
                                                        ->where('status','Y')
                                                        ->first();

                // $hasil_pengerjaan_3 = $umk_borongan_lokal_3->id.'|'.$umk_borongan_lokal_3->umk_packing*$request->hasil_kerja_3[$key];
                
                if ($request->hasil_kerja_3[$key]) {
                    $hasil_pengerjaan_3 = $umk_borongan_lokal_3->id.'|'.$request->hasil_kerja_3[$key];
                    $hasil_kerja_3 = $umk_borongan_lokal_3->umk_outer*$request->hasil_kerja_3[$key];
                }else{
                    $hasil_pengerjaan_3 = $umk_borongan_lokal_3->id.'|'.'0';
                    $hasil_kerja_3 = 0;
                }
            
                if($request->lembur_kerja_3){
                    $lembur_3 = '3-y';
                    $hasil_lembur_3 = '1.5';
                    // $total_upah_lembur_3 = ($hasil_kerja_3*1.5)-$hasil_kerja_3;
                    $total_upah_lembur_3 = $hasil_kerja_3*1.5;
                }else{
                    $lembur_3 = '3-n';
                    $hasil_lembur_3 = '1';
                    $total_upah_lembur_3 = $hasil_kerja_3*0;
                }

                if($request->total_jam_3[$key]){
                    $total_jam_3 = $request->total_jam_3[$key];
                }else{
                    $total_jam_3 = '0';
                }

            }else{
                $hasil_pengerjaan_3 = '0'.'|'.'0';
                $lembur_3 = '3-n';
                $hasil_lembur_3 = '1';
                $total_jam_3 = '0';
                $hasil_kerja_3 = 0;
                $total_upah_lembur_3 = '0';
            }

            $total_hasil_lembur_3 = $total_upah_lembur_3;

            if ($request->umk_borongan_lokal_kerja_4) {
                $umk_borongan_lokal_4 = $this->umkBoronganLokal->select('id','jenis_produk','umk_outer')
                                                        ->where('id',$request->umk_borongan_lokal_kerja_4)
                                                        ->where('status','Y')
                                                        ->first();

                // $hasil_pengerjaan_4 = $umk_borongan_lokal_4->id.'|'.$umk_borongan_lokal_4->umk_packing*$request->hasil_kerja_4[$key];
                
                if ($request->hasil_kerja_4[$key]) {
                    $hasil_pengerjaan_4 = $umk_borongan_lokal_4->id.'|'.$request->hasil_kerja_4[$key];
                    $hasil_kerja_4 = $umk_borongan_lokal_4->umk_outer*$request->hasil_kerja_4[$key];
                }else{
                    $hasil_pengerjaan_4 = $umk_borongan_lokal_4->id.'|'.'0';
                    $hasil_kerja_4 = 0;
                }
            
                if($request->lembur_kerja_4){
                    $lembur_4 = '4-y';
                    $hasil_lembur_4 = '1.5';
                    // $total_upah_lembur_4 = ($hasil_kerja_4*1.5)-$hasil_kerja_4;
                    $total_upah_lembur_4 = $hasil_kerja_4*1.5;
                }else{
                    $lembur_4 = '4-n';
                    $hasil_lembur_4 = '1';
                    $total_upah_lembur_4 = $hasil_kerja_4*0;
                }

                if($request->total_jam_4[$key]){
                    $total_jam_4 = $request->total_jam_4[$key];
                }else{
                    $total_jam_4 = '0';
                }

            }else{
                $hasil_pengerjaan_4 = '0'.'|'.'0';
                $lembur_4 = '4-n';
                $hasil_lembur_4 = '1';
                $total_jam_4 = '0';
                $hasil_kerja_4 = 0;
                $total_upah_lembur_4 = '0';
            }

            $total_hasil_lembur_4 = $total_upah_lembur_4;

            if ($request->umk_borongan_lokal_kerja_5) {
                $umk_borongan_lokal_5 = $this->umkBoronganLokal->select('id','jenis_produk','umk_outer')
                                                        ->where('id',$request->umk_borongan_lokal_kerja_5)
                                                        ->where('status','Y')
                                                        ->first();

                // $hasil_pengerjaan_5 = $umk_borongan_lokal_5->id.'|'.$umk_borongan_lokal_5->umk_packing*$request->hasil_kerja_5[$key];
                
                if ($request->hasil_kerja_5[$key]) {
                    $hasil_pengerjaan_5 = $umk_borongan_lokal_5->id.'|'.$request->hasil_kerja_5[$key];
                    $hasil_kerja_5 = $umk_borongan_lokal_5->umk_outer*$request->hasil_kerja_5[$key];
                }else{
                    $hasil_pengerjaan_5 = $umk_borongan_lokal_5->id.'|'.'0';
                    $hasil_kerja_5 = 0;
                }
            
                if($request->lembur_kerja_5){
                    $lembur_5 = '5-y';
                    $hasil_lembur_5 = '1.5';
                    // $total_upah_lembur_5 = ($hasil_kerja_5*1.5)-$hasil_kerja_5;
                    $total_upah_lembur_5 = $hasil_kerja_5*1.5;
                }else{
                    $lembur_5 = '5-n';
                    $hasil_lembur_5 = '1';
                    $total_upah_lembur_5 = $hasil_kerja_5*0;
                }

                if($request->total_jam_5[$key]){
                    $total_jam_5 = $request->total_jam_5[$key];
                }else{
                    $total_jam_5 = '0';
                }

            }else{
                $hasil_pengerjaan_5 = '0'.'|'.'0';
                $lembur_5 = '5-n';
                $hasil_lembur_5 = '1';
                $total_jam_5 = '0';
                $hasil_kerja_5 = 0;
                $total_upah_lembur_5 = '0';
            }

            $total_hasil_lembur_5 = $total_upah_lembur_5;

            $lemburs = '|'.$lembur_1.'|'.$lembur_2.'|'.$lembur_3.'|'.$lembur_4.'|'.$lembur_5;
            $hasil_upah_dasar = $hasil_kerja_1+$hasil_kerja_2+$hasil_kerja_3+$hasil_kerja_4+$hasil_kerja_5;
            
            $penjumlahan_lembur = round($total_hasil_lembur_1+$total_hasil_lembur_2+$total_hasil_lembur_3+$total_hasil_lembur_4+$total_hasil_lembur_5);
            
            // dd($hasil_upah_dasar);
            $karyawan_pengerjaan->update([
                'hasil_kerja_1' => $hasil_pengerjaan_1,
                'hasil_kerja_2' => $hasil_pengerjaan_2,
                'hasil_kerja_3' => $hasil_pengerjaan_3,
                'hasil_kerja_4' => $hasil_pengerjaan_4,
                'hasil_kerja_5' => $hasil_pengerjaan_5,
                'uang_lembur' => $penjumlahan_lembur,
                'lembur' => $lemburs,
                'total_jam_kerja_1' => $total_jam_1,
                'total_jam_kerja_2' => $total_jam_2,
                'total_jam_kerja_3' => $total_jam_3,
                'total_jam_kerja_4' => $total_jam_4,
                'total_jam_kerja_5' => $total_jam_5,
            ]);
        }
        return response()->json([
            'success' => true,
            'message_title' => 'Berhasil',
            'message_content' => 'Data Berhasil Disimpan'
        ]);
    }

    public function hasil_kerja_karyawan_outer_view($id, $kode_pengerjaan, $nik, $month, $year)
    {
        $data['id'] = $id;
        $data['nik'] = $nik;
        $data['kode_pengerjaan'] = $kode_pengerjaan;
        $data['month'] = $month;
        $data['year'] = $year;
        // dd($data);

        if($id == 1){
            $kode_jenis_operator_detail = 'L';
        }
        elseif($id == 2){
            $kode_jenis_operator_detail = 'E';
        }
        elseif($id == 3){
            $kode_jenis_operator_detail = 'A';
        }
        $data['karyawan'] = $this->karyawanOperator->select([
                                                    'operator_karyawan.id as id',
                                                    'biodata_karyawan.nama as nama',
                                                    'operator_karyawan.nik as nik',
                                                    'biodata_karyawan.pin as pin',
                                                    'biodata_karyawan.tanggal_masuk as tanggal',
                                                    'operator_karyawan.tunjangan_kerja_id as tunjangan_kerja_id'
                                                    ])
                                                    ->leftJoin('itic_emp_new.biodata_karyawan','biodata_karyawan.nik','=','operator_karyawan.nik')
                                                    ->where('operator_karyawan.nik',$nik)
                                                    ->where('operator_karyawan.jenis_operator_detail_pekerjaan_id',4)
                                                    ->where('status','Y')
                                                    ->first();

        $data['new_data_pengerjaan'] = $this->newDataPengerjaan->select('akhir_bulan')->where('kode_pengerjaan',$kode_pengerjaan)->first();
        $data['jhts'] = $this->bpjsJht->where('status','y')->get();
        $data['bpjs_kesehatan'] = $this->bpjsKesehatan->select('nominal')->where('status','y')->first();
        // dd($data['new_data_pengerjaan']);

        $data['pengerjaans'] = $this->pengerjaan->where('operator_karyawan_id',$data['karyawan']['id'])->where('kode_pengerjaan',$kode_pengerjaan)->get();
        
        $data['upah'] = array();

        foreach ($data['pengerjaans'] as $key => $pengerjaan) {
            $explode_hasil_kerja_1 = explode("|",$pengerjaan->hasil_kerja_1);
            $umk_borongan_lokal_1 = $this->umkBoronganLokal->select('id','jenis_produk','umk_packing','umk_bandrol','umk_inner','umk_outer')->where('id',$explode_hasil_kerja_1[0])->first();
            if(empty($umk_borongan_lokal_1)){
                $jenis_produk_1 = '-';
                $hasil_kerja_1 = null;
                $data_explode_hasil_kerja_1 = '-';
                $lembur_1 = 1;
            }else{
                $jenis_produk_1 = $umk_borongan_lokal_1->jenis_produk;
                $hasil_kerja_1 = $explode_hasil_kerja_1[1]*$umk_borongan_lokal_1->umk_outer;
                $data_explode_hasil_kerja_1 = $explode_hasil_kerja_1[1];

                $explode_lembur_1 = explode("|",$pengerjaan->lembur);
                $explode_status_lembur_1 = explode("-",$explode_lembur_1[1]);

                if($explode_status_lembur_1[1] == 'y'){
                    $lembur_1 = 1.5;
                }else{
                    $lembur_1 = 1;
                }
            }

            $explode_hasil_kerja_2 = explode("|",$pengerjaan->hasil_kerja_2);
            $umk_borongan_lokal_2 = $this->umkBoronganLokal->select('id','jenis_produk','umk_packing','umk_bandrol','umk_inner','umk_outer')->where('id',$explode_hasil_kerja_2[0])->first();
            if(empty($umk_borongan_lokal_2)){
                $jenis_produk_2 = '-';
                $hasil_kerja_2 = null;
                $data_explode_hasil_kerja_2 = '-';
                $lembur_2 = 1;
            }else{
                $jenis_produk_2 = $umk_borongan_lokal_2->jenis_produk;
                $hasil_kerja_2 = $explode_hasil_kerja_2[1]*$umk_borongan_lokal_2->umk_outer;
                $data_explode_hasil_kerja_2 = $explode_hasil_kerja_2[1];

                $explode_lembur_2 = explode("|",$pengerjaan->lembur);
                $explode_status_lembur_2 = explode("-",$explode_lembur_2[2]);

                if($explode_status_lembur_2[1] == 'y'){
                    $lembur_2 = 1.5;
                }else{
                    $lembur_2 = 1;
                }
            }

            $explode_hasil_kerja_3 = explode("|",$pengerjaan->hasil_kerja_3);
            $umk_borongan_lokal_3 = $this->umkBoronganLokal->select('id','jenis_produk','umk_packing','umk_bandrol','umk_inner','umk_outer')->where('id',$explode_hasil_kerja_3[0])->first();
            if(empty($umk_borongan_lokal_3)){
                $jenis_produk_3 = '-';
                $hasil_kerja_3 = null;
                $data_explode_hasil_kerja_3 = '-';
                $lembur_3 = 1;
            }else{
                $jenis_produk_3 = $umk_borongan_lokal_3->jenis_produk;
                $hasil_kerja_3 = $explode_hasil_kerja_3[1]*$umk_borongan_lokal_3->umk_outer;
                $data_explode_hasil_kerja_3 = $explode_hasil_kerja_3[1];

                $explode_lembur_3 = explode("|",$pengerjaan->lembur);
                $explode_status_lembur_3 = explode("-",$explode_lembur_3[3]);

                if($explode_status_lembur_3[1] == 'y'){
                    $lembur_3 = 1.5;
                }else{
                    $lembur_3 = 1;
                }
            }

            $explode_hasil_kerja_4 = explode("|",$pengerjaan->hasil_kerja_4);
            $umk_borongan_lokal_4 = $this->umkBoronganLokal->select('id','jenis_produk','umk_packing','umk_bandrol','umk_inner','umk_outer')->where('id',$explode_hasil_kerja_4[0])->first();
            if(empty($umk_borongan_lokal_4)){
                $jenis_produk_4 = '-';
                $hasil_kerja_4 = null;
                $data_explode_hasil_kerja_4 = '-';
                $lembur_4 = 1;
            }else{
                $jenis_produk_4 = $umk_borongan_lokal_4->jenis_produk;
                $hasil_kerja_4 = $explode_hasil_kerja_4[1]*$umk_borongan_lokal_4->umk_outer;
                $data_explode_hasil_kerja_4 = $explode_hasil_kerja_4[1];

                $explode_lembur_4 = explode("|",$pengerjaan->lembur);
                $explode_status_lembur_4 = explode("-",$explode_lembur_4[4]);

                if($explode_status_lembur_4[1] == 'y'){
                    $lembur_4 = 1.5;
                }else{
                    $lembur_4 = 1;
                }
            }

            $explode_hasil_kerja_5 = explode("|",$pengerjaan->hasil_kerja_5);
            $umk_borongan_lokal_5 = $this->umkBoronganLokal->select('id','jenis_produk','umk_packing','umk_bandrol','umk_inner','umk_outer')->where('id',$explode_hasil_kerja_5[0])->first();
            if(empty($umk_borongan_lokal_5)){
                $jenis_produk_5 = '-';
                $hasil_kerja_5 = null;
                $data_explode_hasil_kerja_5 = '-';
                $lembur_5 = 1;
            }else{
                $jenis_produk_5 = $umk_borongan_lokal_5->jenis_produk;
                $hasil_kerja_5 = $explode_hasil_kerja_5[1]*$umk_borongan_lokal_5->umk_outer;
                $data_explode_hasil_kerja_5 = $explode_hasil_kerja_5[1];

                $explode_lembur_5 = explode("|",$pengerjaan->lembur);
                $explode_status_lembur_5 = explode("-",$explode_lembur_5[5]);

                if($explode_status_lembur_5[1] == 'y'){
                    $lembur_5 = 1.5;
                }else{
                    $lembur_5 = 1;
                }
            }

            $hasil_upah = ($hasil_kerja_1*$lembur_1)+($hasil_kerja_2*$lembur_2)+($hasil_kerja_3*$lembur_3)+($hasil_kerja_4*$lembur_4)+($hasil_kerja_5*$lembur_5);

            array_push($data['upah'],$hasil_upah);
        }

        //Hitung Masa Kerja
        // $data['karyawan_masa_kerja'] = DB::connection('emp')->table('log_posisi')
        //                                                     ->select('tanggal')
        //                                                     ->where('nik',$nik)
        //                                                     ->first();
        $awal  = new DateTime($data['karyawan']->tanggal);
        $akhir = new DateTime(); // Waktu sekarang
        $diff  = $awal->diff($akhir);
        $data['masa_kerja'] = $diff->y.' Tahun '.$diff->m.' Bulan '.$diff->d.' Hari';
        $data['masa_kerja_tahun'] = $diff->y;
        $data['masa_kerja_hari'] = $diff->d;
        //End Hitung Masa Kerja

        $data['upah_dasar_karyawan'] = [];
        foreach ($data['jhts'] as $key => $jht) {
            if ($data['masa_kerja_tahun'] > 15) {
                if ($jht->urutan == 3) {
                    $data['upah_dasar_karyawan'] = ($data['bpjs_kesehatan']['nominal'] + 100000)/25;
                }
            }
            elseif($data['masa_kerja_tahun'] >= 10 && $data['masa_kerja_tahun'] <= 15 && $data['masa_kerja_hari'] >= 1){
                if ($jht->urutan == 2) {
                    $data['upah_dasar_karyawan'] = ($data['bpjs_kesehatan']['nominal'] + 50000)/25;
                }
            }
            elseif($data['masa_kerja_tahun'] <= 10 || $data['masa_kerja_hari'] >= 1){
                if ($jht->urutan == 1) {
                    $data['upah_dasar_karyawan'] = ($data['bpjs_kesehatan']['nominal'] + 0)/25;
                }
            }
        }

        // echo 'Selisih waktu: ';
        // echo $diff->y . ' tahun, ';
        // echo $diff->m . ' bulan, ';
        // echo $diff->d . ' hari, ';
        // echo $diff->h . ' jam, ';
        // echo $diff->i . ' menit, ';
        // echo $diff->s . ' detik, ';
        // dd($data);

        $data['pengerjaan_weekly'] = $this->pengerjaanWeekly->where('operator_karyawan_id',$data['karyawan']['id'])
                                                    ->where('kode_payrol',substr($kode_pengerjaan,0,2).$kode_jenis_operator_detail.'_'.substr($kode_pengerjaan,3))
                                                    ->first();
        // dd($data['pengerjaan_weekly']);
        // $data['presensi_info'] = PresensiInfo::all();
        // dd($data['presensi_info']);
        $tgl_current_active = Carbon::now()->format('d');
        $bln_active = Carbon::now()->format('m');
        $bln_current_active = $month;
        $thn_current_active = $year;

        // $bulan_kemarin = Carbon::now()->subMonth()->format('Y-m').'-26';
        // $bulan_sekarang = Carbon::now()->format('Y-m').'-25';

        if($month == 1 && $tgl_current_active <10){
            $thn_prev_active = $year-1;
            $bulan_kemarin="$thn_prev_active-11-26";
            $bulan_sekarang="$thn_prev_active-12-26";
        }elseif($month == 1 && $tgl_current_active>10 or $month == 2 && $tgl_current_active<10 && $bln_active != 3){
            $thn_prev_active = $year-1;
            $bulan_kemarin="$thn_prev_active-12-26";
            $bulan_sekarang="$thn_current_active-01-26";
        }else{
            $bln_prev_active = $month-1; 
            $thn_prev_active = $year;
            $bulan_kemarin="$thn_prev_active-$bln_prev_active-26";
            $bulan_sekarang="$thn_current_active-$bln_current_active-26";
        }

        $data['tunjangan_kerjas'] = $this->tunjanganKerja->select('nominal')->where('id',$data['karyawan']['tunjangan_kerja_id'])->first();
        if(empty($data['tunjangan_kerjas'])){
            $data['tunjangan_kerja'] = 0;
        }else{
            $data['tunjangan_kerja'] = $data['tunjangan_kerjas']['nominal'];
        }
        //jumlah alpa
        $data['alpa'] = $this->presensiInfo->where('pin',$data['karyawan']['pin'])
                                    ->whereBetween('scan_date',[$bulan_kemarin,$bulan_sekarang])
                                    ->where('status',7)
                                    ->get();
        //jumlah diliburkan
        $data['diliburkan'] = $this->presensiInfo->where('pin',$data['karyawan']['pin'])
                                    ->whereBetween('scan_date',[$bulan_kemarin,$bulan_sekarang])
                                    ->where('status',14)
                                    ->get();
        //jumlah cuti
        $data['cuti'] = $this->presensiInfo->where('pin',$data['karyawan']['pin'])
                                    ->whereBetween('scan_date',[$bulan_kemarin,$bulan_sekarang])
                                    ->where('status',13)
                                    ->get();
        //jumlah sakit
        $data['sakit'] = $this->presensiInfo->where('pin',$data['karyawan']['pin'])
                                    ->whereBetween('scan_date',[$bulan_kemarin,$bulan_sekarang])
                                    ->where('status',4)
                                    ->get();
        // dd($data['sakit']);
        //jumlah ijin full
        $data['ijin_full'] = $this->presensiInfo->where('pin',$data['karyawan']['pin'])
                                    ->whereBetween('scan_date',[$bulan_kemarin,$bulan_sekarang])
                                    ->where('status',6)
                                    ->get();
        //jumlah status terlambat
        $data['terlambats'] = $this->presensiInfo->where('pin',$data['karyawan']['pin'])
                                    ->whereBetween('scan_date',[$bulan_kemarin,$bulan_sekarang])
                                    ->where('status',3)
                                    ->get();
        $telat_1=0; //terlambat < 5 menit
        $telat_2=0; //terlambat > 5 menit < 15 menit
        $telat_3=0; //terlambat > 15 menit < 1 jam
        $telat_4=0; //terlambat > 1 jam < 3 jam	
        $telat_5=0; //terlambat > 3 jam	
        $data['telat_1'] = $telat_1;
        $data['telat_2'] = $telat_2;
        $data['telat_3'] = $telat_3;
        $data['telat_4'] = $telat_4;
        $data['telat_5'] = $telat_5;
        foreach ($data['terlambats'] as $key => $terlambat) {
            $explode_keterangan_terlambat = explode("@",$terlambat->keterangan);
            $jam_keterangan_terlambat=strtotime($explode_keterangan_terlambat[1]);
			$jam_datang_terlambat=strtotime(Carbon::parse($terlambat['scan_date'])->format('H:i'));
			// $jam_datang_terlambat=strtotime($row_terlambat['jam_datang_telat']);
			$menit_telat=(($jam_datang_terlambat-$jam_keterangan_terlambat)/60);
			if($menit_telat<=4){
                $telat_1=$telat_1+1;
                $data['telat_1'] = $telat_1;
            }
			else if(($menit_telat>=5) && ($menit_telat<=15) ){
                $telat_2=$telat_2+1;
                $data['telat_2'] = $telat_2;
            }
			else if(($menit_telat>=16) && ($menit_telat<=60)){
                $telat_3=$telat_3+1;
                $data['telat_3'] = $telat_3;
            }
			else if(($menit_telat>=61) && ($menit_telat<=179)){
                $telat_4=$telat_4+1;
                $data['telat_4'] = $telat_4;
            }else if($menit_telat > 179){
                $telat_5=$telat_5+1;
                $data['telat_5'] = $telat_5;
            }
			// else{
            //     $telat_4=$telat_4+1;
            //     $data['telat_4'] = $telat_4;
            // }
        }

        //jumlah status pulang awal
        $data['pulang_awals'] = $this->presensiInfo->where('pin',$data['karyawan']['pin'])
                                    ->whereBetween('scan_date',[$bulan_kemarin,$bulan_sekarang])
                                    ->where(function($query) {
                                        $query->where('status','=',9)
                                            ->orWhere('status','=',10);
                                    })
                                    // ->where('status',9)
                                    // ->where('status',10)
                                    ->get();
        // dd($data['pulang_awals']);
        $pulang_1=0; //pulang awal < 4 jam
		$pulang_2=0; //tpulang awal > 4 jam
        $data['pulang_1'] = $pulang_1;
        $data['pulang_2'] = $pulang_2;
        foreach ($data['pulang_awals'] as $key => $pulang_awal) {
            $explode_pulang_seharusnya=explode("@",$pulang_awal->keterangan);
			$jam_pulang_seharusnya=strtotime($explode_pulang_seharusnya[3]);
			$jam_pulang_awal=strtotime(Carbon::parse($pulang_awal['scan_date'])->format('H:i'));
            // $jam_pulang_awal=strtotime($pulang_awal->jam_pulang_awal);
			$menit_sisa_jam_kerja=(($jam_pulang_seharusnya-$jam_pulang_awal)/60);
			if ($menit_sisa_jam_kerja<=300){
                $pulang_1=$pulang_1+1;
                $data['pulang_1'] = $pulang_1;
            }
			else{
                $pulang_2=$pulang_2+1;
                $data['pulang_2'] = $pulang_2;
            }
        }

        //jumlah ijin saat jam kerja
        $data['keluar_masuks'] = $this->keluarMasuk->where('nik',$data['karyawan']['nik'])
                                    ->whereBetween('tanggal_ijin',[$bulan_kemarin,$bulan_sekarang])
                                    ->get();
        // dd($data['keluar_masuks']);
        $ijin_15=0; //ijin kurang dari 15 menit
		$ijin_k4=0; //ijin > 15menit < 4 jam
		$ijin_l4=0; //ijin > 4 jam
        $data['ijin_15']=$ijin_15;
        $data['ijin_k4']=$ijin_k4;
        $data['ijin_l4']=$ijin_l4;
        foreach ($data['keluar_masuks'] as $key => $keluar_masuk) {
            $jam_keluar=strtotime($keluar_masuk->jam_keluar);
			$jam_datang=strtotime($keluar_masuk->jam_datang);
			$menit_keluar_masuk=(($jam_datang-$jam_keluar)/60);
			if ($menit_keluar_masuk<=15){
                $ijin_15=$ijin_15+1;
                $data['ijin_15']=$ijin_15;
            }
			else if (($menit_keluar_masuk>15)&&($menit_keluar_masuk<=239)){
                $ijin_k4=$ijin_k4+1;
                $data['ijin_k4']=$ijin_k4;
            }
			else {
                $ijin_l4=$ijin_l4+1;
                $data['ijin_l4']=$ijin_l4;
            }
        }

        //lihat tanggal masuk
        // $data['log_posisi'] = $this->logPosisi->select('tanggal')->where('nik',$nik)->first();
		$selisih_tanggal_masuk=strtotime($bulan_kemarin)-strtotime($data['karyawan']['tanggal']);
		if ($selisih_tanggal_masuk>=0)$div_tk=0;else $div_tk=75000;

        $total_potongan_tk=(75000*$data['alpa']->count())+(75000*$data['diliburkan']->count())+(75000*$data['sakit']->count())+(75000*$data['cuti']->count())+(75000*$data['ijin_full']->count())+($data['ijin_15']*25000)+($data['ijin_k4']*40000)+
        ($data['ijin_l4']*75000)+($data['pulang_1']*40000)+($data['pulang_2']*75000)+($data['telat_1']*15000)+($data['telat_2']*25000)+($data['telat_3']*30000)+($data['telat_4']*40000)+$div_tk;
        if ($total_potongan_tk>75000){$total_potongan_tk=75000;}else {}

        // if ($data['pengerjaan_weekly']['tunjangan_kehadiran'] == 75000) {
        //     $data['total_potongan_tk']=0;
        // }
        // // elseif($data['pengerjaan_weekly']['tunjangan_kehadiran'] < 75000){
        // //     $data['total_potongan_tk']=$data['pengerjaan_weekly']['tunjangan_kehadiran'];
        // // }
        // else{
        //     $data['total_potongan_tk']=$total_potongan_tk; 
        // }

        $data['total_potongan_tk']=$total_potongan_tk;
        return view('backend.pengerjaan.packing_outer.input_hasil_kerja_karyawan',$data);
    }

    public function hasil_kerja_karyawan_outer_view_simpan(Request $request, $id, $kode_pengerjaan, $nik)
    {
        // $input = $request->all();
        if($id == 1){
            $kode_jenis_operator_detail = 'L';
        }
        elseif($id == 2){
            $kode_jenis_operator_detail = 'E';
        }
        elseif($id == 3){
            $kode_jenis_operator_detail = 'A';
        }

        $data['id'] = $id;
        $data['kode_pengerjaan'] = $kode_pengerjaan;
        $data['nik'] = $nik;

        $input['upah_dasar'] = $request->upah_dasar;
        $input['upah_makan'] = $request->upah_makan;
        
        $input['uang_makan'] = $request->uang_makan;
        $input['plus_1'] = $request->plus_1."|".$request->keterangan_plus_1;
        $input['plus_2'] = $request->plus_2."|".$request->keterangan_plus_2;
        $input['plus_3'] = $request->plus_3."|".$request->keterangan_plus_3;
        
        $input['minus_1'] = $request->minus_1."|".$request->keterangan_minus_1;
        $input['minus_2'] = $request->minus_2."|".$request->keterangan_minus_2;

        if ($request->akhir_bulan == 'n') {
            $input['tunjangan_kerja'] = 0;
            $input['tunjangan_kehadiran'] = 0;
        }else{
            $input['tunjangan_kerja'] = $request->tunjangan_kerja;
            $input['tunjangan_kehadiran'] = 75000-$request->pot_tunjangan_kehadiran;
        }

        if ($request->check_jht == 'on') {
            $input['jht'] = $request->jht;
        }else{
            $input['jht'] = 0;
        }

        if($request->check_bpjs_kesehatan == 'on'){
            $input['bpjs_kesehatan'] = $request->bpjs_kesehatan;
        }else{
            $input['bpjs_kesehatan'] = 0;
        }

        // dd($input);
        $operator_karyawan = $this->karyawanOperator->select('id')
                                            ->where('nik',$nik)
                                            ->where('status','Y')
                                            ->where('jenis_operator_detail_pekerjaan_id',4)
                                            ->first();
        $pengerjaan_weekly = $this->pengerjaanWeekly->where('kode_payrol',substr($kode_pengerjaan,0,2).$kode_jenis_operator_detail.'_'.substr($kode_pengerjaan,3))
                                            ->where('operator_karyawan_id',$operator_karyawan->id)
                                            ->first();
        // $pengerjaan_weekly->update($input);
        // dd($pengerjaan_weekly);
        if (!empty($pengerjaan_weekly)) {
            $pengerjaan_weekly->update($input);
            return response()->json([
                'success' => true,
                'message_title' => 'Berhasil',
                'message_content' => 'Data Berhasil Disimpan'
            ]);
        }else{
            return response()->json([
                'success' => false,
                'message_title' => 'Gagal',
                'message_content' => 'Data Tidak Berhasil Disimpan'
            ]);
        }
    }

    public function hasil_kerja_packing_ekspor($id, $kode_pengerjaan)
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

        $data['id'] = $id;
        $data['kode_pengerjaan'] = $kode_pengerjaan;
        $data['jenis_operator_detail_pekerjaan'] = $this->jenisOperatorDetailPengerjaan->find(5);
        $data['new_data_pengerjaan'] = $this->newDataPengerjaan->where('kode_pengerjaan',$kode_pengerjaan)->first();
        $data['explode_tanggal_pengerjaans'] = explode('#',$data['new_data_pengerjaan']['tanggal']);
        $data['count_tanggal'] = count($data['explode_tanggal_pengerjaans'])-1;
        // $data['packing_ekspors'] = KaryawanOperator::select([
        //                             'operator_karyawan.id as id',
        //                             'operator_karyawan.nik as nik',
        //                             'biodata_karyawan.nama as nama',
        //                             ])
        //                             ->where('jenis_operator_id',1)
        //                             ->where('jenis_operator_detail_id',2)
        //                             ->where('jenis_operator_detail_pekerjaan_id',5)
        //                             ->leftJoin('biodata_karyawan','biodata_karyawan.nik','=','operator_karyawan.nik')
        //                             ->rightJoin('pengerjaan','pengerjaan.operator_karyawan_id','=','operator_karyawan.id')
        //                             ->orderBy('biodata_karyawan.nama','asc')
        //                             ->get();

        $data['pengerjaans'] = $this->pengerjaanWeekly->select([
                                                    'pengerjaan_weekly.kode_payrol as kode_payrol',
                                                    'pengerjaan_weekly.operator_karyawan_id as operator_karyawan_id',
                                                    'operator_karyawan.nik as nik',
                                                    'biodata_karyawan.nama as nama',
                                                    'pengerjaan_weekly.upah_dasar as upah_dasar',
                                                    'pengerjaan_weekly.tunjangan_kerja as tunjangan_kerja',
                                                    'pengerjaan_weekly.tunjangan_kehadiran as tunjangan_kehadiran',
                                                    'pengerjaan_weekly.uang_makan as uang_makan',
                                                    'pengerjaan_weekly.plus_1 as plus_1',
                                                    'pengerjaan_weekly.plus_2 as plus_2',
                                                    'pengerjaan_weekly.plus_3 as plus_3',
                                                    'pengerjaan_weekly.minus_1 as minus_1',
                                                    'pengerjaan_weekly.minus_2 as minus_2',
                                                    'pengerjaan_weekly.jht as jht',
                                                    'pengerjaan_weekly.bpjs_kesehatan as bpjs_kesehatan',
                                                ])
                                                ->where('kode_payrol',substr($kode_pengerjaan,0,2).$kode_jenis_operator_detail.'_'.substr($kode_pengerjaan,3))
                                                ->where('operator_karyawan.jenis_operator_id',1)
                                                ->where('operator_karyawan.jenis_operator_detail_id',2)
                                                ->where('operator_karyawan.jenis_operator_detail_pekerjaan_id',5)
                                                ->where('operator_karyawan.status','Y')
                                                ->leftJoin('operator_karyawan','operator_karyawan.id','=','pengerjaan_weekly.operator_karyawan_id')
                                                ->leftJoin('itic_emp_new.biodata_karyawan','biodata_karyawan.nik','=','operator_karyawan.nik')
                                                ->orderBy('biodata_karyawan.nama','asc')
                                                ->get();

        return view('backend.pengerjaan.packing_ekspor.packing_ekspor',$data);
    }

    public function hasil_kerja_packing_ekspor_view_hasil($id, $kode_pengerjaan, $tanggal)
    {
        $data['id'] = $id;
        $data['kode_pengerjaan'] = $kode_pengerjaan;
        $data['tanggal'] = $tanggal;
        $data['packing_ekspors'] = $this->karyawanOperator->select([
                                                    'operator_karyawan.id as id',
                                                    'operator_karyawan.nik as nik',
                                                    'biodata_karyawan.nama as nama',
                                                    'pengerjaan.hasil_kerja_1 as hasil_kerja_1',
                                                    'pengerjaan.hasil_kerja_2 as hasil_kerja_2',
                                                    'pengerjaan.hasil_kerja_3 as hasil_kerja_3',
                                                    'pengerjaan.hasil_kerja_4 as hasil_kerja_4',
                                                    'pengerjaan.hasil_kerja_5 as hasil_kerja_5',
                                                    'pengerjaan.total_jam_kerja_1 as total_jam_kerja_1',
                                                    'pengerjaan.total_jam_kerja_2 as total_jam_kerja_2',
                                                    'pengerjaan.total_jam_kerja_3 as total_jam_kerja_3',
                                                    'pengerjaan.total_jam_kerja_4 as total_jam_kerja_4',
                                                    'pengerjaan.total_jam_kerja_5 as total_jam_kerja_5',
                                                    ])
                                                    ->leftJoin('itic_emp_new.biodata_karyawan','biodata_karyawan.nik','=','operator_karyawan.nik')
                                                    ->rightJoin('pengerjaan','pengerjaan.operator_karyawan_id','=','operator_karyawan.id')
                                                    ->where('jenis_operator_id',1)
                                                    ->where('jenis_operator_detail_id',2)
                                                    ->where('jenis_operator_detail_pekerjaan_id',5)
                                                    ->where('pengerjaan.tanggal_pengerjaan',$tanggal)
                                                    ->where('operator_karyawan.status','Y')
                                                    ->where('pengerjaan.kode_pengerjaan',$kode_pengerjaan)
                                                    // ->with('biodata_karyawan')
                                                    ->orderBy('biodata_karyawan.nama','asc')
                                                    ->get();

        $data['umk_borongan_ekspors'] = $this->umkBoronganEkspor->select('id','jenis_produk','umk_packing')->where('status','Y')->get();
        // dd($data);
        return view('backend.pengerjaan.packing_ekspor.input_hasil_kerja',$data);
    }

    public function hasil_kerja_packing_ekspor_view_simpan(Request $request, $id, $kode_pengerjaan, $tanggal)
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

        $input = $request->all();
        $input['id'] = $id;
        $input['kode_pengerjaan'] = $kode_pengerjaan;
        $input['tanggal'] = $tanggal;

        $data['karyawan_pengerjaans'] = $this->pengerjaan->select([
                                                        'pengerjaan.id as id',
                                                        'operator_karyawan.nik as nik',
                                                        'pengerjaan.operator_karyawan_id as id_operator_karyawan'
                                                    ])
                                                    ->leftJoin('operator_karyawan','operator_karyawan.id','=','pengerjaan.operator_karyawan_id')
                                                    ->leftJoin('itic_emp_new.biodata_karyawan','biodata_karyawan.nik','=','operator_karyawan.nik')
                                                    ->where('tanggal_pengerjaan',$tanggal)
                                                    // ->where('kode_pengerjaan',$kode_pengerjaan)
                                                    // ->where('pengerjaan.operator_karyawan_id','operator_karyawan.id')
                                                    ->where('operator_karyawan.jenis_operator_id',1)
                                                    ->where('operator_karyawan.jenis_operator_detail_id',2)
                                                    ->where('operator_karyawan.jenis_operator_detail_pekerjaan_id',5)
                                                    ->where('operator_karyawan.status','Y')
                                                    ->where('pengerjaan.kode_pengerjaan',$kode_pengerjaan)
                                                    ->orderBy('biodata_karyawan.nama','asc')
                                                    ->get();
        
        foreach ($data['karyawan_pengerjaans'] as $key => $karyawan_pengerjaan) {
            // dd($request->umk_borongan_lokal_kerja_1);
            if ($request->umk_borongan_lokal_kerja_1) {
                $umk_borongan_lokal_1 = $this->umkBoronganEkspor->select('id','jenis_produk','umk_packing')
                                                        ->where('id',$request->umk_borongan_lokal_kerja_1)
                                                        ->where('status','Y')
                                                        ->first();

                // $hasil_pengerjaan_1 = $umk_borongan_lokal_1->id.'|'.$umk_borongan_lokal_1->umk_packing*$request->hasil_kerja_1[$key];
                
                // if ($request->hasil_kerja_1[$key]) {
                // }

                if ($request->hasil_kerja_1[$key]) {
                    $hasil_pengerjaan_1 = $umk_borongan_lokal_1->id.'|'.$request->hasil_kerja_1[$key];
                    $hasil_kerja_1 = $umk_borongan_lokal_1->umk_packing*$request->hasil_kerja_1[$key];
                }else{
                    $hasil_pengerjaan_1 = $umk_borongan_lokal_1->id.'|'.'0';
                    $hasil_kerja_1 = 0;
                }

                if($request->lembur_kerja_1){
                    $lembur_1 = '1-y';
                    $hasil_lembur_1 = '1.5';
                    $total_upah_lembur_1 = $hasil_kerja_1*1.5;
                }else{
                    $lembur_1 = '1-n';
                    $hasil_lembur_1 = '1';
                    $total_upah_lembur_1 = $hasil_kerja_1*0;
                }

                if($request->total_jam_1[$key]){
                    $total_jam_1 = $request->total_jam_1[$key];
                }else{
                    $total_jam_1 = '0';
                }
                
            }else{
                $hasil_pengerjaan_1 = '0'.'|'.'0';
                $lembur_1 = '1-n';
                $hasil_lembur_1 = '1';
                $total_jam_1 = '0';
                $hasil_kerja_1 = 0;
                $total_upah_lembur_1 = '0';
            }

            $total_hasil_lembur_1 = $total_upah_lembur_1;

            // dd($hasil_pengerjaan_1);
            
            if ($request->umk_borongan_lokal_kerja_2) {
                $umk_borongan_lokal_2 = $this->umkBoronganEkspor->select('id','jenis_produk','umk_packing')
                                                        ->where('id',$request->umk_borongan_lokal_kerja_2)
                                                        ->where('status','Y')
                                                        ->first();

                // $hasil_pengerjaan_2 = $umk_borongan_lokal_2->id.'|'.$umk_borongan_lokal_2->umk_packing*$request->hasil_kerja_2[$key];
                
                if ($request->hasil_kerja_2[$key]) {
                    $hasil_pengerjaan_2 = $umk_borongan_lokal_2->id.'|'.$request->hasil_kerja_2[$key];
                    $hasil_kerja_2 = $umk_borongan_lokal_2->umk_packing*$request->hasil_kerja_2[$key];
                }else{
                    $hasil_pengerjaan_2 = $umk_borongan_lokal_2->id.'|'.'0';
                    $hasil_kerja_2 = 0;
                }

                if($request->lembur_kerja_2){
                    $lembur_2 = '2-y';
                    $hasil_lembur_2 = '1.5';
                    $total_upah_lembur_2 = $hasil_kerja_2*1.5;
                }else{
                    $lembur_2 = '2-n';
                    $hasil_lembur_2 = '1';
                    $total_upah_lembur_2 = $hasil_kerja_2*0;
                }

                if($request->total_jam_2[$key]){
                    $total_jam_2 = $request->total_jam_2[$key];
                }else{
                    $total_jam_2 = '0';
                }

            }else{
                $hasil_pengerjaan_2 = '0'.'|'.'0';
                $lembur_2 = '2-n';
                $hasil_lembur_2 = '1';
                $total_jam_2 = '0';
                $hasil_kerja_2 = 0;
                $total_upah_lembur_2 = '0';
            }

            $total_hasil_lembur_2 = $total_upah_lembur_2;

            if ($request->umk_borongan_lokal_kerja_3) {
                $umk_borongan_lokal_3 = $this->umkBoronganEkspor->select('id','jenis_produk','umk_packing')
                                                        ->where('id',$request->umk_borongan_lokal_kerja_3)
                                                        ->where('status','Y')
                                                        ->first();

                // $hasil_pengerjaan_3 = $umk_borongan_lokal_3->id.'|'.$umk_borongan_lokal_3->umk_packing*$request->hasil_kerja_3[$key];
                
                if ($request->hasil_kerja_3[$key]) {
                    $hasil_pengerjaan_3 = $umk_borongan_lokal_3->id.'|'.$request->hasil_kerja_3[$key];
                    $hasil_kerja_3 = $umk_borongan_lokal_3->umk_packing*$request->hasil_kerja_3[$key];
                }else{
                    $hasil_pengerjaan_3 = $umk_borongan_lokal_3->id.'|'.'0';
                    $hasil_kerja_3 = 0;
                }
            
                if($request->lembur_kerja_3){
                    $lembur_3 = '3-y';
                    $hasil_lembur_3 = '1.5';
                    $total_upah_lembur_3 = $hasil_kerja_3*1.5;
                }else{
                    $lembur_3 = '3-n';
                    $hasil_lembur_3 = '1';
                    $total_upah_lembur_3 = $hasil_kerja_3*0;
                }

                if($request->total_jam_3[$key]){
                    $total_jam_3 = $request->total_jam_3[$key];
                }else{
                    $total_jam_3 = '0';
                }

            }else{
                $hasil_pengerjaan_3 = '0'.'|'.'0';
                $lembur_3 = '3-n';
                $hasil_lembur_3 = '1';
                $total_jam_3 = '0';
                $hasil_kerja_3 = 0;
                $total_upah_lembur_3 = '0';
            }

            $total_hasil_lembur_3 = $total_upah_lembur_3;

            if ($request->umk_borongan_lokal_kerja_4) {
                $umk_borongan_lokal_4 = $this->umkBoronganEkspor->select('id','jenis_produk','umk_packing')
                                                        ->where('id',$request->umk_borongan_lokal_kerja_4)
                                                        ->where('status','Y')
                                                        ->first();

                // $hasil_pengerjaan_4 = $umk_borongan_lokal_4->id.'|'.$umk_borongan_lokal_4->umk_packing*$request->hasil_kerja_4[$key];
                
                if ($request->hasil_kerja_4[$key]) {
                    $hasil_pengerjaan_4 = $umk_borongan_lokal_4->id.'|'.$request->hasil_kerja_4[$key];
                    $hasil_kerja_4 = $umk_borongan_lokal_4->umk_packing*$request->hasil_kerja_4[$key];
                }else{
                    $hasil_pengerjaan_4 = $umk_borongan_lokal_4->id.'|'.'0';
                    $hasil_kerja_4 = 0;
                }
            
                if($request->lembur_kerja_4){
                    $lembur_4 = '4-y';
                    $hasil_lembur_4 = '1.5';
                    $total_upah_lembur_4 = $hasil_kerja_4*1.5;
                }else{
                    $lembur_4 = '4-n';
                    $hasil_lembur_4 = '1';
                    $total_upah_lembur_4 = $hasil_kerja_4*0;
                }

                if($request->total_jam_4[$key]){
                    $total_jam_4 = $request->total_jam_4[$key];
                }else{
                    $total_jam_4 = '0';
                }

            }else{
                $hasil_pengerjaan_4 = '0'.'|'.'0';
                $lembur_4 = '4-n';
                $hasil_lembur_4 = '1';
                $total_jam_4 = '0';
                $hasil_kerja_4 = 0;
                $total_upah_lembur_4 = '0';
            }

            $total_hasil_lembur_4 = $total_upah_lembur_4;

            if ($request->umk_borongan_lokal_kerja_5) {
                $umk_borongan_lokal_5 = $this->umkBoronganEkspor->select('id','jenis_produk','umk_packing')
                                                        ->where('id',$request->umk_borongan_lokal_kerja_5)
                                                        ->where('status','Y')
                                                        ->first();

                // $hasil_pengerjaan_5 = $umk_borongan_lokal_5->id.'|'.$umk_borongan_lokal_5->umk_packing*$request->hasil_kerja_5[$key];
                
                if ($request->hasil_kerja_5[$key]) {
                    $hasil_pengerjaan_5 = $umk_borongan_lokal_5->id.'|'.$request->hasil_kerja_5[$key];
                    $hasil_kerja_5 = $umk_borongan_lokal_5->umk_packing*$request->hasil_kerja_5[$key];
                }else{
                    $hasil_pengerjaan_5 = $umk_borongan_lokal_5->id.'|'.'0';
                    $hasil_kerja_5 = 0;
                }
            
                if($request->lembur_kerja_5){
                    $lembur_5 = '5-y';
                    $hasil_lembur_5 = '1.5';
                    $total_upah_lembur_5 = $hasil_kerja_5*1.5;
                }else{
                    $lembur_5 = '5-n';
                    $hasil_lembur_5 = '1';
                    $total_upah_lembur_5 = $hasil_kerja_5*0;
                }

                if($request->total_jam_5[$key]){
                    $total_jam_5 = $request->total_jam_5[$key];
                }else{
                    $total_jam_5 = '0';
                }

            }else{
                $hasil_pengerjaan_5 = '0'.'|'.'0';
                $lembur_5 = '5-n';
                $hasil_lembur_5 = '1';
                $total_jam_5 = '0';
                $hasil_kerja_5 = 0;
                $total_upah_lembur_5 = '0';
            }

            $total_hasil_lembur_5 = $total_upah_lembur_5;

            $lemburs = '|'.$lembur_1.'|'.$lembur_2.'|'.$lembur_3.'|'.$lembur_4.'|'.$lembur_5;
            $hasil_upah_dasar = $hasil_kerja_1+$hasil_kerja_2+$hasil_kerja_3+$hasil_kerja_4+$hasil_kerja_5;
            // dd($hasil_upah_dasar);
            $penjumlahan_lembur = round($total_hasil_lembur_1+$total_hasil_lembur_2+$total_hasil_lembur_3+$total_hasil_lembur_4+$total_hasil_lembur_5);

            $karyawan_pengerjaan->update([
                'hasil_kerja_1' => $hasil_pengerjaan_1,
                'hasil_kerja_2' => $hasil_pengerjaan_2,
                'hasil_kerja_3' => $hasil_pengerjaan_3,
                'hasil_kerja_4' => $hasil_pengerjaan_4,
                'hasil_kerja_5' => $hasil_pengerjaan_5,
                'uang_lembur' => $penjumlahan_lembur,
                'lembur' => $lemburs,
                'total_jam_kerja_1' => $total_jam_1,
                'total_jam_kerja_2' => $total_jam_2,
                'total_jam_kerja_3' => $total_jam_3,
                'total_jam_kerja_4' => $total_jam_4,
                'total_jam_kerja_5' => $total_jam_5,
            ]);
        }

        return response()->json([
            'success' => true,
            'message_title' => 'Berhasil',
            'message_content' => 'Data Berhasil Disimpan'
        ]);
    }

    public function hasil_kerja_karyawan_packing_ekspor_view($id, $kode_pengerjaan, $nik, $month, $year)
    {
        $data['id'] = $id;
        $data['nik'] = $nik;
        $data['kode_pengerjaan'] = $kode_pengerjaan;
        $data['month'] = $month;
        $data['year'] = $year;
        // dd($data);

        if($id == 1){
            $kode_jenis_operator_detail = 'L';
        }
        elseif($id == 2){
            $kode_jenis_operator_detail = 'E';
        }
        elseif($id == 3){
            $kode_jenis_operator_detail = 'A';
        }
        $data['karyawan'] = $this->karyawanOperator->select([
                                                    'operator_karyawan.id as id',
                                                    'biodata_karyawan.nama as nama',
                                                    'operator_karyawan.nik as nik',
                                                    'biodata_karyawan.pin as pin',
                                                    'biodata_karyawan.tanggal_masuk as tanggal',
                                                    'operator_karyawan.tunjangan_kerja_id as tunjangan_kerja_id'
                                                    ])
                                                    ->leftJoin('itic_emp_new.biodata_karyawan','biodata_karyawan.nik','=','operator_karyawan.nik')
                                                    ->where('operator_karyawan.nik',$nik)
                                                    ->where('operator_karyawan.status','Y')
                                                    ->where('operator_karyawan.jenis_operator_detail_pekerjaan_id',5)
                                                    ->first();

        $data['new_data_pengerjaan'] = $this->newDataPengerjaan->select('akhir_bulan')->where('kode_pengerjaan',$kode_pengerjaan)->first();
        $data['jhts'] = $this->bpjsJht->where('status','y')->get();
        $data['bpjs_kesehatan'] = $this->bpjsKesehatan->select('nominal')->where('status','y')->first();
        // dd($data['new_data_pengerjaan']);

        $data['pengerjaans'] = $this->pengerjaan->where('operator_karyawan_id',$data['karyawan']['id'])->where('kode_pengerjaan',$kode_pengerjaan)->get();
        
        $data['upah'] = array();

        foreach ($data['pengerjaans'] as $key => $pengerjaan) {
            $explode_hasil_kerja_1 = explode("|",$pengerjaan->hasil_kerja_1);
            $umk_borongan_lokal_1 = $this->umkBoronganEkspor->select('id','jenis_produk','umk_packing','umk_kemas','umk_pilih_gagang')->where('id',$explode_hasil_kerja_1[0])->first();
            if(empty($umk_borongan_lokal_1)){
                $jenis_produk_1 = '-';
                $hasil_kerja_1 = null;
                $data_explode_hasil_kerja_1 = '-';
                $lembur_1 = 1;
            }else{
                $jenis_produk_1 = $umk_borongan_lokal_1->jenis_produk;
                $hasil_kerja_1 = $explode_hasil_kerja_1[1]*$umk_borongan_lokal_1->umk_packing;
                $data_explode_hasil_kerja_1 = $explode_hasil_kerja_1[1];

                $explode_lembur_1 = explode("|",$pengerjaan->lembur);
                $explode_status_lembur_1 = explode("-",$explode_lembur_1[1]);

                if($explode_status_lembur_1[1] == 'y'){
                    $lembur_1 = 1.5;
                }else{
                    $lembur_1 = 1;
                }
            }

            $explode_hasil_kerja_2 = explode("|",$pengerjaan->hasil_kerja_2);
            $umk_borongan_lokal_2 = $this->umkBoronganEkspor->select('id','jenis_produk','umk_packing','umk_kemas','umk_pilih_gagang')->where('id',$explode_hasil_kerja_2[0])->first();
            if(empty($umk_borongan_lokal_2)){
                $jenis_produk_2 = '-';
                $hasil_kerja_2 = null;
                $data_explode_hasil_kerja_2 = '-';
                $lembur_2 = 1;
            }else{
                $jenis_produk_2 = $umk_borongan_lokal_2->jenis_produk;
                $hasil_kerja_2 = $explode_hasil_kerja_2[1]*$umk_borongan_lokal_2->umk_packing;
                $data_explode_hasil_kerja_2 = $explode_hasil_kerja_2[1];

                $explode_lembur_2 = explode("|",$pengerjaan->lembur);
                $explode_status_lembur_2 = explode("-",$explode_lembur_2[2]);

                if($explode_status_lembur_2[1] == 'y'){
                    $lembur_2 = 1.5;
                }else{
                    $lembur_2 = 1;
                }
            }

            $explode_hasil_kerja_3 = explode("|",$pengerjaan->hasil_kerja_3);
            $umk_borongan_lokal_3 = $this->umkBoronganEkspor->select('id','jenis_produk','umk_packing','umk_kemas','umk_pilih_gagang')->where('id',$explode_hasil_kerja_3[0])->first();
            if(empty($umk_borongan_lokal_3)){
                $jenis_produk_3 = '-';
                $hasil_kerja_3 = null;
                $data_explode_hasil_kerja_3 = '-';
                $lembur_3 = 1;
            }else{
                $jenis_produk_3 = $umk_borongan_lokal_3->jenis_produk;
                $hasil_kerja_3 = $explode_hasil_kerja_3[1]*$umk_borongan_lokal_3->umk_packing;
                $data_explode_hasil_kerja_3 = $explode_hasil_kerja_3[1];

                $explode_lembur_3 = explode("|",$pengerjaan->lembur);
                $explode_status_lembur_3 = explode("-",$explode_lembur_3[3]);

                if($explode_status_lembur_3[1] == 'y'){
                    $lembur_3 = 1.5;
                }else{
                    $lembur_3 = 1;
                }
            }

            $explode_hasil_kerja_4 = explode("|",$pengerjaan->hasil_kerja_4);
            $umk_borongan_lokal_4 = $this->umkBoronganEkspor->select('id','jenis_produk','umk_packing','umk_kemas','umk_pilih_gagang')->where('id',$explode_hasil_kerja_4[0])->first();
            if(empty($umk_borongan_lokal_4)){
                $jenis_produk_4 = '-';
                $hasil_kerja_4 = null;
                $data_explode_hasil_kerja_4 = '-';
                $lembur_4 = 1;
            }else{
                $jenis_produk_4 = $umk_borongan_lokal_4->jenis_produk;
                $hasil_kerja_4 = $explode_hasil_kerja_4[1]*$umk_borongan_lokal_4->umk_packing;
                $data_explode_hasil_kerja_4 = $explode_hasil_kerja_4[1];

                $explode_lembur_4 = explode("|",$pengerjaan->lembur);
                $explode_status_lembur_4 = explode("-",$explode_lembur_4[4]);

                if($explode_status_lembur_4[1] == 'y'){
                    $lembur_4 = 1.5;
                }else{
                    $lembur_4 = 1;
                }
            }

            $explode_hasil_kerja_5 = explode("|",$pengerjaan->hasil_kerja_5);
            $umk_borongan_lokal_5 = $this->umkBoronganEkspor->select('id','jenis_produk','umk_packing','umk_kemas','umk_pilih_gagang')->where('id',$explode_hasil_kerja_5[0])->first();
            if(empty($umk_borongan_lokal_5)){
                $jenis_produk_5 = '-';
                $hasil_kerja_5 = null;
                $data_explode_hasil_kerja_5 = '-';
                $lembur_5 = 1;
            }else{
                $jenis_produk_5 = $umk_borongan_lokal_5->jenis_produk;
                $hasil_kerja_5 = $explode_hasil_kerja_5[1]*$umk_borongan_lokal_5->umk_packing;
                $data_explode_hasil_kerja_5 = $explode_hasil_kerja_5[1];

                $explode_lembur_5 = explode("|",$pengerjaan->lembur);
                $explode_status_lembur_5 = explode("-",$explode_lembur_5[5]);

                if($explode_status_lembur_5[1] == 'y'){
                    $lembur_5 = 1.5;
                }else{
                    $lembur_5 = 1;
                }
            }

            $hasil_upah = ($hasil_kerja_1*$lembur_1)+($hasil_kerja_2*$lembur_2)+($hasil_kerja_3*$lembur_3)+($hasil_kerja_4*$lembur_4)+($hasil_kerja_5*$lembur_5);

            array_push($data['upah'],$hasil_upah);
        }

        //Hitung Masa Kerja
        // $data['karyawan_masa_kerja'] = DB::connection('emp')->table('log_posisi')
        //                                                     ->select('tanggal')
        //                                                     ->where('nik',$nik)
        //                                                     ->first();
        $awal  = new DateTime($data['karyawan']->tanggal);
        $akhir = new DateTime(); // Waktu sekarang
        $diff  = $awal->diff($akhir);
        $data['masa_kerja'] = $diff->y.' Tahun '.$diff->m.' Bulan '.$diff->d.' Hari';
        $data['masa_kerja_tahun'] = $diff->y;
        $data['masa_kerja_hari'] = $diff->d;
        //End Hitung Masa Kerja

        $data['upah_dasar_karyawan'] = [];
        foreach ($data['jhts'] as $key => $jht) {
            if ($data['masa_kerja_tahun'] > 15) {
                if ($jht->urutan == 3) {
                    $data['upah_dasar_karyawan'] = ($data['bpjs_kesehatan']['nominal'] + 100000)/25;
                }
            }
            elseif($data['masa_kerja_tahun'] >= 10 && $data['masa_kerja_tahun'] <= 15 && $data['masa_kerja_hari'] >= 1){
                if ($jht->urutan == 2) {
                    $data['upah_dasar_karyawan'] = ($data['bpjs_kesehatan']['nominal'] + 50000)/25;
                }
            }
            elseif($data['masa_kerja_tahun'] <= 10 || $data['masa_kerja_hari'] >= 1){
                if ($jht->urutan == 1) {
                    $data['upah_dasar_karyawan'] = ($data['bpjs_kesehatan']['nominal'] + 0)/25;
                }
            }
        }

        // echo 'Selisih waktu: ';
        // echo $diff->y . ' tahun, ';
        // echo $diff->m . ' bulan, ';
        // echo $diff->d . ' hari, ';
        // echo $diff->h . ' jam, ';
        // echo $diff->i . ' menit, ';
        // echo $diff->s . ' detik, ';
        // dd($data);

        $data['pengerjaan_weekly'] = $this->pengerjaanWeekly->where('operator_karyawan_id',$data['karyawan']['id'])
                                                    ->where('kode_payrol',substr($kode_pengerjaan,0,2).$kode_jenis_operator_detail.'_'.substr($kode_pengerjaan,3))
                                                    ->first();
        // dd($data['pengerjaan_weekly']);
        // $data['presensi_info'] = PresensiInfo::all();
        // dd($data['presensi_info']);
        $tgl_current_active = Carbon::now()->format('d');
        $bln_active = Carbon::now()->format('m');
        $bln_current_active = $month;
        $thn_current_active = $year;

        // $bulan_kemarin = Carbon::now()->subMonth()->format('Y-m').'-26';
        // $bulan_sekarang = Carbon::now()->format('Y-m').'-25';

        if($month == 1 && $tgl_current_active <10){
            $thn_prev_active = $year-1;
            $bulan_kemarin="$thn_prev_active-11-26";
            $bulan_sekarang="$thn_prev_active-12-26";
        }elseif($month == 1 && $tgl_current_active>10 or $month == 2 && $tgl_current_active<10 && $bln_active != 3){
            $thn_prev_active = $year-1;
            $bulan_kemarin="$thn_prev_active-12-26";
            $bulan_sekarang="$thn_current_active-01-26";
        }else{
            $bln_prev_active = $month-1; 
            $thn_prev_active = $year;
            $bulan_kemarin="$thn_prev_active-$bln_prev_active-26";
            $bulan_sekarang="$thn_current_active-$bln_current_active-26";
        }

        $data['tunjangan_kerjas'] = $this->tunjanganKerja->select('nominal')->where('id',$data['karyawan']['tunjangan_kerja_id'])->first();
        if(empty($data['tunjangan_kerjas'])){
            $data['tunjangan_kerja'] = 0;
        }else{
            $data['tunjangan_kerja'] = $data['tunjangan_kerjas']['nominal'];
        }
        //jumlah alpa
        $data['alpa'] = $this->presensiInfo->where('pin',$data['karyawan']['pin'])
                                    ->whereBetween('scan_date',[$bulan_kemarin,$bulan_sekarang])
                                    ->where('status',7)
                                    ->get();
        //jumlah diliburkan
        $data['diliburkan'] = $this->presensiInfo->where('pin',$data['karyawan']['pin'])
                                    ->whereBetween('scan_date',[$bulan_kemarin,$bulan_sekarang])
                                    ->where('status',14)
                                    ->get();
        //jumlah cuti
        $data['cuti'] = $this->presensiInfo->where('pin',$data['karyawan']['pin'])
                                    ->whereBetween('scan_date',[$bulan_kemarin,$bulan_sekarang])
                                    ->where('status',13)
                                    ->get();
        //jumlah sakit
        $data['sakit'] = $this->presensiInfo->where('pin',$data['karyawan']['pin'])
                                    ->whereBetween('scan_date',[$bulan_kemarin,$bulan_sekarang])
                                    ->where('status',4)
                                    ->get();
        // dd($data['sakit']);
        //jumlah ijin full
        $data['ijin_full'] = $this->presensiInfo->where('pin',$data['karyawan']['pin'])
                                    ->whereBetween('scan_date',[$bulan_kemarin,$bulan_sekarang])
                                    ->where('status',6)
                                    ->get();
        //jumlah status terlambat
        $data['terlambats'] = $this->presensiInfo->where('pin',$data['karyawan']['pin'])
                                    ->whereBetween('scan_date',[$bulan_kemarin,$bulan_sekarang])
                                    ->where('status',3)
                                    ->get();
        $telat_1=0; //terlambat < 5 menit
        $telat_2=0; //terlambat > 5 menit < 15 menit
        $telat_3=0; //terlambat > 15 menit < 1 jam
        $telat_4=0; //terlambat > 1 jam < 3 jam	
        $telat_5=0; //terlambat > 3 jam	
        $data['telat_1'] = $telat_1;
        $data['telat_2'] = $telat_2;
        $data['telat_3'] = $telat_3;
        $data['telat_4'] = $telat_4;
        $data['telat_5'] = $telat_5;
        foreach ($data['terlambats'] as $key => $terlambat) {
            $explode_keterangan_terlambat = explode("@",$terlambat->keterangan);
            $jam_keterangan_terlambat=strtotime($explode_keterangan_terlambat[1]);
            $jam_datang_terlambat=strtotime(Carbon::parse($terlambat['scan_date'])->format('H:i'));
            // $jam_datang_terlambat=strtotime($terlambat['jam_datang_telat']);
            $menit_telat=(($jam_datang_terlambat-$jam_keterangan_terlambat)/60);
            // dd($menit_telat);
            if($menit_telat <= 4){
                $telat_1=$telat_1+1;
                $data['telat_1'] = $telat_1;
            }
            else if(($menit_telat >= 5) && ($menit_telat <= 15) ){
                $telat_2=$telat_2+1;
                $data['telat_2'] = $telat_2;
            }
            else if(($menit_telat >= 16) && ($menit_telat <= 60)){
                $telat_3=$telat_3+1;
                $data['telat_3'] = $telat_3;
            }
            else if(($menit_telat >= 61) && ($menit_telat <= 179)){
                $telat_4=$telat_4+1;
                $data['telat_4'] = $telat_4;
            }else if($menit_telat > 179){
                $telat_5=$telat_5+1;
                $data['telat_5'] = $telat_5;
            }
            // else{
            //     $telat_4=$telat_4+1;
            //     $data['telat_4'] = $telat_4;
            // }
        }

        //jumlah status pulang awal
        $data['pulang_awals'] = $this->presensiInfo->where('pin',$data['karyawan']['pin'])
                                    ->whereBetween('scan_date',[$bulan_kemarin,$bulan_sekarang])
                                    ->where(function($query) {
                                        $query->where('status','=',9)
                                            ->orWhere('status','=',10);
                                    })
                                    // ->where('status',9)
                                    // ->where('status',10)
                                    ->get();
        // dd($data['pulang_awals']);
        $pulang_1=0; //pulang awal < 4 jam
		$pulang_2=0; //tpulang awal > 4 jam
        $data['pulang_1'] = $pulang_1;
        $data['pulang_2'] = $pulang_2;
        foreach ($data['pulang_awals'] as $key => $pulang_awal) {
            $explode_pulang_seharusnya=explode("@",$pulang_awal->keterangan);
			$jam_pulang_seharusnya=strtotime($explode_pulang_seharusnya[3]);
			$jam_pulang_awal=strtotime(Carbon::parse($pulang_awal['scan_date'])->format('H:i'));
            // $jam_pulang_awal=strtotime($pulang_awal->jam_pulang_awal);
			$menit_sisa_jam_kerja=(($jam_pulang_seharusnya-$jam_pulang_awal)/60);
			if ($menit_sisa_jam_kerja<=300){
                $pulang_1=$pulang_1+1;
                $data['pulang_1'] = $pulang_1;
            }
			else{
                $pulang_2=$pulang_2+1;
                $data['pulang_2'] = $pulang_2;
            }
        }

        //jumlah ijin saat jam kerja
        $data['keluar_masuks'] = $this->keluarMasuk->where('nik',$data['karyawan']['nik'])
                                    ->whereBetween('tanggal_ijin',[$bulan_kemarin,$bulan_sekarang])
                                    ->get();
        // dd($data['keluar_masuks']);
        $ijin_15=0; //ijin kurang dari 15 menit
		$ijin_k4=0; //ijin > 15menit < 4 jam
		$ijin_l4=0; //ijin > 4 jam
        $data['ijin_15']=$ijin_15;
        $data['ijin_k4']=$ijin_k4;
        $data['ijin_l4']=$ijin_l4;
        foreach ($data['keluar_masuks'] as $key => $keluar_masuk) {
            $jam_keluar=strtotime($keluar_masuk->jam_keluar);
			$jam_datang=strtotime($keluar_masuk->jam_datang);
			$menit_keluar_masuk=(($jam_datang-$jam_keluar)/60);
			if ($menit_keluar_masuk<=15){
                $ijin_15=$ijin_15+1;
                $data['ijin_15']=$ijin_15;
            }
			else if (($menit_keluar_masuk>15)&&($menit_keluar_masuk<=239)){
                $ijin_k4=$ijin_k4+1;
                $data['ijin_k4']=$ijin_k4;
            }
			else {
                $ijin_l4=$ijin_l4+1;
                $data['ijin_l4']=$ijin_l4;
            }
        }

        //lihat tanggal masuk
        // $data['log_posisi'] = $this->logPosisi->select('tanggal')->where('nik',$nik)->first();
		$selisih_tanggal_masuk=strtotime($bulan_kemarin)-strtotime($data['karyawan']['tanggal']);
		if ($selisih_tanggal_masuk>=0)$div_tk=0;else $div_tk=75000;

        $total_potongan_tk=(75000*$data['alpa']->count())+(75000*$data['diliburkan']->count())+(75000*$data['sakit']->count())+(75000*$data['cuti']->count())+(75000*$data['ijin_full']->count())+($data['ijin_15']*25000)+($data['ijin_k4']*40000)+
        ($data['ijin_l4']*75000)+($data['pulang_1']*40000)+($data['pulang_2']*75000)+($data['telat_1']*15000)+($data['telat_2']*25000)+($data['telat_3']*30000)+($data['telat_4']*40000)+$div_tk;
        if ($total_potongan_tk>75000){$total_potongan_tk=75000;}else {}

        // if ($data['pengerjaan_weekly']['tunjangan_kehadiran'] == 75000) {
        //     $data['total_potongan_tk']=0;
        // }
        // // elseif($data['pengerjaan_weekly']['tunjangan_kehadiran'] < 75000){
        // //     $data['total_potongan_tk']=$data['pengerjaan_weekly']['tunjangan_kehadiran'];
        // // }
        // else{
        //     $data['total_potongan_tk']=$total_potongan_tk; 
        // }

        $data['total_potongan_tk']=$total_potongan_tk;
        return view('backend.pengerjaan.packing_ekspor.input_hasil_kerja_karyawan',$data);
    }

    public function hasil_kerja_karyawan_packing_ekspor_view_simpan(Request $request, $id, $kode_pengerjaan, $nik)
    {
        // $input = $request->all();
        if($id == 1){
            $kode_jenis_operator_detail = 'L';
        }
        elseif($id == 2){
            $kode_jenis_operator_detail = 'E';
        }
        elseif($id == 3){
            $kode_jenis_operator_detail = 'A';
        }

        $data['id'] = $id;
        $data['kode_pengerjaan'] = $kode_pengerjaan;
        $data['nik'] = $nik;

        $input['upah_dasar'] = $request->upah_dasar;
        $input['upah_makan'] = $request->upah_makan;
        
        $input['uang_makan'] = $request->uang_makan;
        $input['plus_1'] = $request->plus_1."|".$request->keterangan_plus_1;
        $input['plus_2'] = $request->plus_2."|".$request->keterangan_plus_2;
        $input['plus_3'] = $request->plus_3."|".$request->keterangan_plus_3;
        
        $input['minus_1'] = $request->minus_1."|".$request->keterangan_minus_1;
        $input['minus_2'] = $request->minus_2."|".$request->keterangan_minus_2;

        if ($request->akhir_bulan == 'n') {
            $input['tunjangan_kerja'] = 0;
            $input['tunjangan_kehadiran'] = 0;
        }else{
            $input['tunjangan_kerja'] = $request->tunjangan_kerja;
            $input['tunjangan_kehadiran'] = 75000-$request->pot_tunjangan_kehadiran;
        }

        if ($request->check_jht == 'on') {
            $input['jht'] = $request->jht;
        }else{
            $input['jht'] = 0;
        }

        if($request->check_bpjs_kesehatan == 'on'){
            $input['bpjs_kesehatan'] = $request->bpjs_kesehatan;
        }else{
            $input['bpjs_kesehatan'] = 0;
        }
        // dd($input);
        $operator_karyawan = $this->karyawanOperator->select('id')->where('nik',$nik)->where('status','Y')->where('jenis_operator_detail_pekerjaan_id',5)->orderBy('id','desc')->first();
        $pengerjaan_weekly = $this->pengerjaanWeekly->where('kode_payrol',substr($kode_pengerjaan,0,2).$kode_jenis_operator_detail.'_'.substr($kode_pengerjaan,3))
                                            ->where('operator_karyawan_id',$operator_karyawan->id)
                                            ->first();
        // $pengerjaan_weekly->update($input);
        // dd($pengerjaan_weekly);
        if (!empty($pengerjaan_weekly)) {
            $pengerjaan_weekly->update($input);
            return response()->json([
                'success' => true,
                'message_title' => 'Berhasil',
                'message_content' => 'Data Berhasil Disimpan'
            ]);
        }else{
            return response()->json([
                'success' => false,
                'message_title' => 'Gagal',
                'message_content' => 'Data Tidak Berhasil Disimpan'
            ]);
        }
    }

    public function hasil_kerja_kemas_ekspor($id, $kode_pengerjaan)
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

        $data['id'] = $id;
        $data['kode_pengerjaan'] = $kode_pengerjaan;
        $data['jenis_operator_detail_pekerjaan'] = $this->jenisOperatorDetailPengerjaan->find(6);
        $data['new_data_pengerjaan'] = $this->newDataPengerjaan->where('kode_pengerjaan',$kode_pengerjaan)->first();
        $data['explode_tanggal_pengerjaans'] = explode('#',$data['new_data_pengerjaan']['tanggal']);
        $data['count_tanggal'] = count($data['explode_tanggal_pengerjaans'])-1;
        // $data['packing_ekspors'] = KaryawanOperator::select([
        //                             'operator_karyawan.id as id',
        //                             'operator_karyawan.nik as nik',
        //                             'biodata_karyawan.nama as nama',
        //                             ])
        //                             ->where('jenis_operator_id',1)
        //                             ->where('jenis_operator_detail_id',2)
        //                             ->where('jenis_operator_detail_pekerjaan_id',5)
        //                             ->leftJoin('biodata_karyawan','biodata_karyawan.nik','=','operator_karyawan.nik')
        //                             ->rightJoin('pengerjaan','pengerjaan.operator_karyawan_id','=','operator_karyawan.id')
        //                             ->orderBy('biodata_karyawan.nama','asc')
        //                             ->get();

        $data['pengerjaans'] = $this->pengerjaanWeekly->select([
                                                    'pengerjaan_weekly.kode_payrol as kode_payrol',
                                                    'pengerjaan_weekly.operator_karyawan_id as operator_karyawan_id',
                                                    'operator_karyawan.nik as nik',
                                                    'biodata_karyawan.nama as nama',
                                                    'pengerjaan_weekly.upah_dasar as upah_dasar',
                                                    'pengerjaan_weekly.tunjangan_kerja as tunjangan_kerja',
                                                    'pengerjaan_weekly.tunjangan_kehadiran as tunjangan_kehadiran',
                                                    'pengerjaan_weekly.uang_makan as uang_makan',
                                                    'pengerjaan_weekly.plus_1 as plus_1',
                                                    'pengerjaan_weekly.plus_2 as plus_2',
                                                    'pengerjaan_weekly.plus_3 as plus_3',
                                                    'pengerjaan_weekly.minus_1 as minus_1',
                                                    'pengerjaan_weekly.minus_2 as minus_2',
                                                    'pengerjaan_weekly.jht as jht',
                                                    'pengerjaan_weekly.bpjs_kesehatan as bpjs_kesehatan',
                                                ])
                                                ->where('kode_payrol',substr($kode_pengerjaan,0,2).$kode_jenis_operator_detail.'_'.substr($kode_pengerjaan,3))
                                                ->where('operator_karyawan.jenis_operator_id',1)
                                                ->where('operator_karyawan.jenis_operator_detail_id',2)
                                                ->where('operator_karyawan.jenis_operator_detail_pekerjaan_id',6)
                                                ->where('operator_karyawan.status','Y')
                                                ->leftJoin('operator_karyawan','operator_karyawan.id','=','pengerjaan_weekly.operator_karyawan_id')
                                                ->leftJoin('itic_emp_new.biodata_karyawan','biodata_karyawan.nik','=','operator_karyawan.nik')
                                                ->orderBy('biodata_karyawan.nama','asc')
                                                ->get();

        return view('backend.pengerjaan.packing_ekspor_kemas.packing_ekspor_kemas',$data);
    }

    public function hasil_kerja_kemas_ekspor_view_hasil($id, $kode_pengerjaan, $tanggal)
    {
        $data['id'] = $id;
        $data['kode_pengerjaan'] = $kode_pengerjaan;
        $data['tanggal'] = $tanggal;
        $data['packing_ekspors'] = $this->karyawanOperator->select([
                                                    'operator_karyawan.id as id',
                                                    'operator_karyawan.nik as nik',
                                                    'biodata_karyawan.nama as nama',
                                                    'pengerjaan.hasil_kerja_1 as hasil_kerja_1',
                                                    'pengerjaan.hasil_kerja_2 as hasil_kerja_2',
                                                    'pengerjaan.hasil_kerja_3 as hasil_kerja_3',
                                                    'pengerjaan.hasil_kerja_4 as hasil_kerja_4',
                                                    'pengerjaan.hasil_kerja_5 as hasil_kerja_5',
                                                    'pengerjaan.total_jam_kerja_1 as total_jam_kerja_1',
                                                    'pengerjaan.total_jam_kerja_2 as total_jam_kerja_2',
                                                    'pengerjaan.total_jam_kerja_3 as total_jam_kerja_3',
                                                    'pengerjaan.total_jam_kerja_4 as total_jam_kerja_4',
                                                    'pengerjaan.total_jam_kerja_5 as total_jam_kerja_5',
                                                    ])
                                                    ->leftJoin('itic_emp_new.biodata_karyawan','biodata_karyawan.nik','=','operator_karyawan.nik')
                                                    ->rightJoin('pengerjaan','pengerjaan.operator_karyawan_id','=','operator_karyawan.id')
                                                    ->where('jenis_operator_id',1)
                                                    ->where('jenis_operator_detail_id',2)
                                                    ->where('jenis_operator_detail_pekerjaan_id',6)
                                                    ->where('pengerjaan.tanggal_pengerjaan',$tanggal)
                                                    ->where('pengerjaan.kode_pengerjaan',$kode_pengerjaan)
                                                    // ->with('biodata_karyawan')
                                                    ->orderBy('biodata_karyawan.nama','asc')
                                                    ->get();

        $data['umk_borongan_ekspors'] = $this->umkBoronganEkspor->select('id','jenis_produk','umk_kemas')->where('status','Y')->get();
        // dd($data);
        return view('backend.pengerjaan.packing_ekspor_kemas.input_hasil_kerja',$data);
    }

    public function hasil_kerja_kemas_ekspor_view_simpan(Request $request, $id, $kode_pengerjaan, $tanggal)
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

        $input = $request->all();
        $input['id'] = $id;
        $input['kode_pengerjaan'] = $kode_pengerjaan;
        $input['tanggal'] = $tanggal;

        $data['karyawan_pengerjaans'] = $this->pengerjaan->select([
                                                        'pengerjaan.id as id',
                                                        'operator_karyawan.nik as nik',
                                                        'pengerjaan.operator_karyawan_id as id_operator_karyawan'
                                                    ])
                                                    ->leftJoin('operator_karyawan','operator_karyawan.id','=','pengerjaan.operator_karyawan_id')
                                                    ->leftJoin('itic_emp_new.biodata_karyawan','biodata_karyawan.nik','=','operator_karyawan.nik')
                                                    ->where('tanggal_pengerjaan',$tanggal)
                                                    // ->where('kode_pengerjaan',$kode_pengerjaan)
                                                    // ->where('pengerjaan.operator_karyawan_id','operator_karyawan.id')
                                                    ->where('operator_karyawan.jenis_operator_id',1)
                                                    ->where('operator_karyawan.jenis_operator_detail_id',2)
                                                    ->where('operator_karyawan.jenis_operator_detail_pekerjaan_id',6)
                                                    ->where('pengerjaan.kode_pengerjaan',$kode_pengerjaan)
                                                    ->orderBy('biodata_karyawan.nama','asc')
                                                    ->get();
        
        foreach ($data['karyawan_pengerjaans'] as $key => $karyawan_pengerjaan) {
            // dd($request->umk_borongan_lokal_kerja_1);
            if ($request->umk_borongan_lokal_kerja_1) {
                $umk_borongan_lokal_1 = $this->umkBoronganEkspor->select('id','jenis_produk','umk_kemas')
                                                        ->where('id',$request->umk_borongan_lokal_kerja_1)
                                                        ->where('status','Y')
                                                        ->first();

                // $hasil_pengerjaan_1 = $umk_borongan_lokal_1->id.'|'.$umk_borongan_lokal_1->umk_packing*$request->hasil_kerja_1[$key];
                
                // if ($request->hasil_kerja_1[$key]) {
                // }

                if ($request->hasil_kerja_1[$key]) {
                    $hasil_pengerjaan_1 = $umk_borongan_lokal_1->id.'|'.$request->hasil_kerja_1[$key];
                    $hasil_kerja_1 = $umk_borongan_lokal_1->umk_kemas*$request->hasil_kerja_1[$key];
                }else{
                    $hasil_pengerjaan_1 = $umk_borongan_lokal_1->id.'|'.'0';
                    $hasil_kerja_1 = 0;
                }

                if($request->lembur_kerja_1){
                    $lembur_1 = '1-y';
                    $hasil_lembur_1 = '1.5';
                    $total_upah_lembur_1 = $hasil_kerja_1*1.5;
                }else{
                    $lembur_1 = '1-n';
                    $hasil_lembur_1 = '1';
                    $total_upah_lembur_1 = $hasil_kerja_1*0;
                }

                if($request->total_jam_1[$key]){
                    $total_jam_1 = $request->total_jam_1[$key];
                }else{
                    $total_jam_1 = '0';
                }
                
            }else{
                $hasil_pengerjaan_1 = '0'.'|'.'0';
                $lembur_1 = '1-n';
                $hasil_lembur_1 = '1';
                $total_jam_1 = '0';
                $hasil_kerja_1 = 0;
                $total_upah_lembur_1 = '0';
            }

            $total_hasil_lembur_1 = $total_upah_lembur_1;

            // dd($hasil_pengerjaan_1);
            
            if ($request->umk_borongan_lokal_kerja_2) {
                $umk_borongan_lokal_2 = $this->umkBoronganEkspor->select('id','jenis_produk','umk_kemas')
                                                        ->where('id',$request->umk_borongan_lokal_kerja_2)
                                                        ->where('status','Y')
                                                        ->first();

                // $hasil_pengerjaan_2 = $umk_borongan_lokal_2->id.'|'.$umk_borongan_lokal_2->umk_packing*$request->hasil_kerja_2[$key];
                
                if ($request->hasil_kerja_2[$key]) {
                    $hasil_pengerjaan_2 = $umk_borongan_lokal_2->id.'|'.$request->hasil_kerja_2[$key];
                    $hasil_kerja_2 = $umk_borongan_lokal_2->umk_kemas*$request->hasil_kerja_2[$key];
                }else{
                    $hasil_pengerjaan_2 = $umk_borongan_lokal_2->id.'|'.'0';
                    $hasil_kerja_2 = 0;
                }

                if($request->lembur_kerja_2){
                    $lembur_2 = '2-y';
                    $hasil_lembur_2 = '1.5';
                    $total_upah_lembur_2 = $hasil_kerja_2*1.5;
                }else{
                    $lembur_2 = '2-n';
                    $hasil_lembur_2 = '1';
                    $total_upah_lembur_2 = $hasil_kerja_2*0;
                }

                if($request->total_jam_2[$key]){
                    $total_jam_2 = $request->total_jam_2[$key];
                }else{
                    $total_jam_2 = '0';
                }

            }else{
                $hasil_pengerjaan_2 = '0'.'|'.'0';
                $lembur_2 = '2-n';
                $hasil_lembur_2 = '1';
                $total_jam_2 = '0';
                $hasil_kerja_2 = 0;
                $total_upah_lembur_2 = '0';
            }

            $total_hasil_lembur_2 = $total_upah_lembur_2;

            if ($request->umk_borongan_lokal_kerja_3) {
                $umk_borongan_lokal_3 = $this->umkBoronganEkspor->select('id','jenis_produk','umk_kemas')
                                                        ->where('id',$request->umk_borongan_lokal_kerja_3)
                                                        ->where('status','Y')
                                                        ->first();

                // $hasil_pengerjaan_3 = $umk_borongan_lokal_3->id.'|'.$umk_borongan_lokal_3->umk_packing*$request->hasil_kerja_3[$key];
                
                if ($request->hasil_kerja_3[$key]) {
                    $hasil_pengerjaan_3 = $umk_borongan_lokal_3->id.'|'.$request->hasil_kerja_3[$key];
                    $hasil_kerja_3 = $umk_borongan_lokal_3->umk_kemas*$request->hasil_kerja_3[$key];
                }else{
                    $hasil_pengerjaan_3 = $umk_borongan_lokal_3->id.'|'.'0';
                    $hasil_kerja_3 = 0;
                }
            
                if($request->lembur_kerja_3){
                    $lembur_3 = '3-y';
                    $hasil_lembur_3 = '1.5';
                    $total_upah_lembur_3 = $hasil_kerja_3*1.5;
                }else{
                    $lembur_3 = '3-n';
                    $hasil_lembur_3 = '1';
                    $total_upah_lembur_3 = $hasil_kerja_3*0;
                }

                if($request->total_jam_3[$key]){
                    $total_jam_3 = $request->total_jam_3[$key];
                }else{
                    $total_jam_3 = '0';
                }

            }else{
                $hasil_pengerjaan_3 = '0'.'|'.'0';
                $lembur_3 = '3-n';
                $hasil_lembur_3 = '1';
                $total_jam_3 = '0';
                $hasil_kerja_3 = 0;
                $total_upah_lembur_3 = '0';
            }

            $total_hasil_lembur_3 = $total_upah_lembur_3;

            if ($request->umk_borongan_lokal_kerja_4) {
                $umk_borongan_lokal_4 = $this->umkBoronganEkspor->select('id','jenis_produk','umk_kemas')
                                                        ->where('id',$request->umk_borongan_lokal_kerja_4)
                                                        ->where('status','Y')
                                                        ->first();

                // $hasil_pengerjaan_4 = $umk_borongan_lokal_4->id.'|'.$umk_borongan_lokal_4->umk_packing*$request->hasil_kerja_4[$key];
                
                if ($request->hasil_kerja_4[$key]) {
                    $hasil_pengerjaan_4 = $umk_borongan_lokal_4->id.'|'.$request->hasil_kerja_4[$key];
                    $hasil_kerja_4 = $umk_borongan_lokal_4->umk_kemas*$request->hasil_kerja_4[$key];
                }else{
                    $hasil_pengerjaan_4 = $umk_borongan_lokal_4->id.'|'.'0';
                    $hasil_kerja_4 = 0;
                }
            
                if($request->lembur_kerja_4){
                    $lembur_4 = '4-y';
                    $hasil_lembur_4 = '1.5';
                    $total_upah_lembur_4 = $hasil_kerja_4*1.5;
                }else{
                    $lembur_4 = '4-n';
                    $hasil_lembur_4 = '1';
                    $total_upah_lembur_4 = $hasil_kerja_4*0;
                }

                if($request->total_jam_4[$key]){
                    $total_jam_4 = $request->total_jam_4[$key];
                }else{
                    $total_jam_4 = '0';
                }

            }else{
                $hasil_pengerjaan_4 = '0'.'|'.'0';
                $lembur_4 = '4-n';
                $hasil_lembur_4 = '1';
                $total_jam_4 = '0';
                $hasil_kerja_4 = 0;
                $total_upah_lembur_4 = '0';
            }

            $total_hasil_lembur_4 = $total_upah_lembur_4;

            if ($request->umk_borongan_lokal_kerja_5) {
                $umk_borongan_lokal_5 = $this->umkBoronganEkspor->select('id','jenis_produk','umk_kemas')
                                                        ->where('id',$request->umk_borongan_lokal_kerja_5)
                                                        ->where('status','Y')
                                                        ->first();

                // $hasil_pengerjaan_5 = $umk_borongan_lokal_5->id.'|'.$umk_borongan_lokal_5->umk_packing*$request->hasil_kerja_5[$key];
                
                if ($request->hasil_kerja_5[$key]) {
                    $hasil_pengerjaan_5 = $umk_borongan_lokal_5->id.'|'.$request->hasil_kerja_5[$key];
                    $hasil_kerja_5 = $umk_borongan_lokal_5->umk_kemas*$request->hasil_kerja_5[$key];
                }else{
                    $hasil_pengerjaan_5 = $umk_borongan_lokal_5->id.'|'.'0';
                    $hasil_kerja_5 = 0;
                }
            
                if($request->lembur_kerja_5){
                    $lembur_5 = '5-y';
                    $hasil_lembur_5 = '1.5';
                    $total_upah_lembur_5 = $hasil_kerja_5*1.5;
                }else{
                    $lembur_5 = '5-n';
                    $hasil_lembur_5 = '1';
                    $total_upah_lembur_5 = $hasil_kerja_5*0;
                }

                if($request->total_jam_5[$key]){
                    $total_jam_5 = $request->total_jam_5[$key];
                }else{
                    $total_jam_5 = '0';
                }

            }else{
                $hasil_pengerjaan_5 = '0'.'|'.'0';
                $lembur_5 = '5-n';
                $hasil_lembur_5 = '1';
                $total_jam_5 = '0';
                $hasil_kerja_5 = 0;
                $total_upah_lembur_5 = '0';
            }

            $total_hasil_lembur_5 = $total_upah_lembur_5;

            $lemburs = '|'.$lembur_1.'|'.$lembur_2.'|'.$lembur_3.'|'.$lembur_4.'|'.$lembur_5;
            $hasil_upah_dasar = $hasil_kerja_1+$hasil_kerja_2+$hasil_kerja_3+$hasil_kerja_4+$hasil_kerja_5;

            $penjumlahan_lembur = round($total_hasil_lembur_1+$total_hasil_lembur_2+$total_hasil_lembur_3+$total_hasil_lembur_4+$total_hasil_lembur_5);
            // dd($hasil_upah_dasar);
            $karyawan_pengerjaan->update([
                'hasil_kerja_1' => $hasil_pengerjaan_1,
                'hasil_kerja_2' => $hasil_pengerjaan_2,
                'hasil_kerja_3' => $hasil_pengerjaan_3,
                'hasil_kerja_4' => $hasil_pengerjaan_4,
                'hasil_kerja_5' => $hasil_pengerjaan_5,
                'uang_lembur' => $penjumlahan_lembur,
                'lembur' => $lemburs,
                'total_jam_kerja_1' => $total_jam_1,
                'total_jam_kerja_2' => $total_jam_2,
                'total_jam_kerja_3' => $total_jam_3,
                'total_jam_kerja_4' => $total_jam_4,
                'total_jam_kerja_5' => $total_jam_5,
            ]);
        }

        return response()->json([
            'success' => true,
            'message_title' => 'Berhasil',
            'message_content' => 'Data Berhasil Disimpan'
        ]);
    }

    public function hasil_kerja_karyawan_kemas_ekspor_view($id, $kode_pengerjaan, $nik, $month, $year)
    {
        $data['id'] = $id;
        $data['nik'] = $nik;
        $data['kode_pengerjaan'] = $kode_pengerjaan;
        $data['month'] = $month;
        $data['year'] = $year;
        // dd($data);

        if($id == 1){
            $kode_jenis_operator_detail = 'L';
        }
        elseif($id == 2){
            $kode_jenis_operator_detail = 'E';
        }
        elseif($id == 3){
            $kode_jenis_operator_detail = 'A';
        }
        $data['karyawan'] = $this->karyawanOperator->select([
                                                    'operator_karyawan.id as id',
                                                    'biodata_karyawan.nama as nama',
                                                    'operator_karyawan.nik as nik',
                                                    'biodata_karyawan.pin as pin',
                                                    'biodata_karyawan.tanggal as tanggal',
                                                    'operator_karyawan.tunjangan_kerja_id as tunjangan_kerja_id'
                                                    ])
                                                    ->leftJoin('itic_emp_new.biodata_karyawan','biodata_karyawan.nik','=','operator_karyawan.nik')
                                                    ->where('operator_karyawan.nik',$nik)
                                                    ->where('operator_karyawan.status','Y')
                                                    ->where('operator_karyawan.jenis_operator_detail_pekerjaan_id',6)
                                                    ->orderBy('id','desc')
                                                    ->first();
        // dd($data['karyawan']);
        $data['new_data_pengerjaan'] = $this->newDataPengerjaan->select('akhir_bulan')->where('kode_pengerjaan',$kode_pengerjaan)->first();
        $data['jhts'] = $this->bpjsJht->where('status','y')->get();
        $data['bpjs_kesehatan'] = $this->bpjsKesehatan->select('nominal')->where('status','y')->first();
        // dd($data['new_data_pengerjaan']);

        $data['pengerjaans'] = $this->pengerjaan->where('operator_karyawan_id',$data['karyawan']['id'])->where('kode_pengerjaan',$kode_pengerjaan)->get();
        
        $data['upah'] = array();

        foreach ($data['pengerjaans'] as $key => $pengerjaan) {
            $explode_hasil_kerja_1 = explode("|",$pengerjaan->hasil_kerja_1);
            $umk_borongan_lokal_1 = $this->umkBoronganEkspor->select('id','jenis_produk','umk_packing','umk_kemas','umk_pilih_gagang')->where('id',$explode_hasil_kerja_1[0])->first();
            if(empty($umk_borongan_lokal_1)){
                $jenis_produk_1 = '-';
                $hasil_kerja_1 = null;
                $data_explode_hasil_kerja_1 = '-';
                $lembur_1 = 1;
            }else{
                $jenis_produk_1 = $umk_borongan_lokal_1->jenis_produk;
                $hasil_kerja_1 = $explode_hasil_kerja_1[1]*$umk_borongan_lokal_1->umk_kemas;
                $data_explode_hasil_kerja_1 = $explode_hasil_kerja_1[1];

                $explode_lembur_1 = explode("|",$pengerjaan->lembur);
                $explode_status_lembur_1 = explode("-",$explode_lembur_1[1]);

                if($explode_status_lembur_1[1] == 'y'){
                    $lembur_1 = 1.5;
                }else{
                    $lembur_1 = 1;
                }
            }

            $explode_hasil_kerja_2 = explode("|",$pengerjaan->hasil_kerja_2);
            $umk_borongan_lokal_2 = $this->umkBoronganEkspor->select('id','jenis_produk','umk_packing','umk_kemas','umk_pilih_gagang')->where('id',$explode_hasil_kerja_2[0])->first();
            if(empty($umk_borongan_lokal_2)){
                $jenis_produk_2 = '-';
                $hasil_kerja_2 = null;
                $data_explode_hasil_kerja_2 = '-';
                $lembur_2 = 1;
            }else{
                $jenis_produk_2 = $umk_borongan_lokal_2->jenis_produk;
                $hasil_kerja_2 = $explode_hasil_kerja_2[1]*$umk_borongan_lokal_2->umk_kemas;
                $data_explode_hasil_kerja_2 = $explode_hasil_kerja_2[1];

                $explode_lembur_2 = explode("|",$pengerjaan->lembur);
                $explode_status_lembur_2 = explode("-",$explode_lembur_2[2]);

                if($explode_status_lembur_2[1] == 'y'){
                    $lembur_2 = 1.5;
                }else{
                    $lembur_2 = 1;
                }
            }

            $explode_hasil_kerja_3 = explode("|",$pengerjaan->hasil_kerja_3);
            $umk_borongan_lokal_3 = $this->umkBoronganEkspor->select('id','jenis_produk','umk_packing','umk_kemas','umk_pilih_gagang')->where('id',$explode_hasil_kerja_3[0])->first();
            if(empty($umk_borongan_lokal_3)){
                $jenis_produk_3 = '-';
                $hasil_kerja_3 = null;
                $data_explode_hasil_kerja_3 = '-';
                $lembur_3 = 1;
            }else{
                $jenis_produk_3 = $umk_borongan_lokal_3->jenis_produk;
                $hasil_kerja_3 = $explode_hasil_kerja_3[1]*$umk_borongan_lokal_3->umk_kemas;
                $data_explode_hasil_kerja_3 = $explode_hasil_kerja_3[1];

                $explode_lembur_3 = explode("|",$pengerjaan->lembur);
                $explode_status_lembur_3 = explode("-",$explode_lembur_3[3]);

                if($explode_status_lembur_3[1] == 'y'){
                    $lembur_3 = 1.5;
                }else{
                    $lembur_3 = 1;
                }
            }

            $explode_hasil_kerja_4 = explode("|",$pengerjaan->hasil_kerja_4);
            $umk_borongan_lokal_4 = $this->umkBoronganEkspor->select('id','jenis_produk','umk_packing','umk_kemas','umk_pilih_gagang')->where('id',$explode_hasil_kerja_4[0])->first();
            if(empty($umk_borongan_lokal_4)){
                $jenis_produk_4 = '-';
                $hasil_kerja_4 = null;
                $data_explode_hasil_kerja_4 = '-';
                $lembur_4 = 1;
            }else{
                $jenis_produk_4 = $umk_borongan_lokal_4->jenis_produk;
                $hasil_kerja_4 = $explode_hasil_kerja_4[1]*$umk_borongan_lokal_4->umk_kemas;
                $data_explode_hasil_kerja_4 = $explode_hasil_kerja_4[1];

                $explode_lembur_4 = explode("|",$pengerjaan->lembur);
                $explode_status_lembur_4 = explode("-",$explode_lembur_4[4]);

                if($explode_status_lembur_4[1] == 'y'){
                    $lembur_4 = 1.5;
                }else{
                    $lembur_4 = 1;
                }
            }

            $explode_hasil_kerja_5 = explode("|",$pengerjaan->hasil_kerja_5);
            $umk_borongan_lokal_5 = $this->umkBoronganEkspor->select('id','jenis_produk','umk_packing','umk_kemas','umk_pilih_gagang')->where('id',$explode_hasil_kerja_5[0])->first();
            if(empty($umk_borongan_lokal_5)){
                $jenis_produk_5 = '-';
                $hasil_kerja_5 = null;
                $data_explode_hasil_kerja_5 = '-';
                $lembur_5 = 1;
            }else{
                $jenis_produk_5 = $umk_borongan_lokal_5->jenis_produk;
                $hasil_kerja_5 = $explode_hasil_kerja_5[1]*$umk_borongan_lokal_5->umk_kemas;
                $data_explode_hasil_kerja_5 = $explode_hasil_kerja_5[1];

                $explode_lembur_5 = explode("|",$pengerjaan->lembur);
                $explode_status_lembur_5 = explode("-",$explode_lembur_5[5]);

                if($explode_status_lembur_5[1] == 'y'){
                    $lembur_5 = 1.5;
                }else{
                    $lembur_5 = 1;
                }
            }

            $hasil_upah = ($hasil_kerja_1*$lembur_1)+($hasil_kerja_2*$lembur_2)+($hasil_kerja_3*$lembur_3)+($hasil_kerja_4*$lembur_4)+($hasil_kerja_5*$lembur_5);

            array_push($data['upah'],$hasil_upah);
        }

        //Hitung Masa Kerja
        // $data['karyawan_masa_kerja'] = DB::connection('emp')->table('log_posisi')
        //                                                     ->select('tanggal')
        //                                                     ->where('nik',$nik)
        //                                                     ->first();
        $awal  = new DateTime($data['karyawan']->tanggal);
        $akhir = new DateTime(); // Waktu sekarang
        $diff  = $awal->diff($akhir);
        $data['masa_kerja'] = $diff->y.' Tahun '.$diff->m.' Bulan '.$diff->d.' Hari';
        $data['masa_kerja_tahun'] = $diff->y;
        $data['masa_kerja_hari'] = $diff->d;
        //End Hitung Masa Kerja

        $data['upah_dasar_karyawan'] = [];
        foreach ($data['jhts'] as $key => $jht) {
            if ($data['masa_kerja_tahun'] > 15) {
                if ($jht->urutan == 3) {
                    $data['upah_dasar_karyawan'] = ($data['bpjs_kesehatan']['nominal'] + 100000)/25;
                }
            }
            elseif($data['masa_kerja_tahun'] >= 10 && $data['masa_kerja_tahun'] <= 15 && $data['masa_kerja_hari'] >= 1){
                if ($jht->urutan == 2) {
                    $data['upah_dasar_karyawan'] = ($data['bpjs_kesehatan']['nominal'] + 50000)/25;
                }
            }
            elseif($data['masa_kerja_tahun'] <= 10 || $data['masa_kerja_hari'] >= 1){
                if ($jht->urutan == 1) {
                    $data['upah_dasar_karyawan'] = ($data['bpjs_kesehatan']['nominal'] + 0)/25;
                }
            }
        }

        // echo 'Selisih waktu: ';
        // echo $diff->y . ' tahun, ';
        // echo $diff->m . ' bulan, ';
        // echo $diff->d . ' hari, ';
        // echo $diff->h . ' jam, ';
        // echo $diff->i . ' menit, ';
        // echo $diff->s . ' detik, ';
        // dd($data);

        $data['pengerjaan_weekly'] = $this->pengerjaanWeekly->where('operator_karyawan_id',$data['karyawan']['id'])
                                                    // ->where('kode_payrol',substr($kode_pengerjaan,0,2).$kode_jenis_operator_detail.'_'.substr($kode_pengerjaan,3))
                                                    ->first();
        // dd($data['pengerjaan_weekly']);
        // $data['presensi_info'] = PresensiInfo::all();
        // dd($data['presensi_info']);
        $tgl_current_active = Carbon::now()->format('d');
        $bln_active = Carbon::now()->format('m');
        $bln_current_active = $month;
        $thn_current_active = $year;

        // $bulan_kemarin = Carbon::now()->subMonth()->format('Y-m').'-26';
        // $bulan_sekarang = Carbon::now()->format('Y-m').'-25';

        if($month == 1 && $tgl_current_active <10){
            $thn_prev_active = $year-1;
            $bulan_kemarin="$thn_prev_active-11-26";
            $bulan_sekarang="$thn_prev_active-12-26";
        }elseif($month == 1 && $tgl_current_active>10 or $month == 2 && $tgl_current_active<10 && $bln_active != 3){
            $thn_prev_active = $year-1;
            $bulan_kemarin="$thn_prev_active-12-26";
            $bulan_sekarang="$thn_current_active-01-26";
        }else{
            $bln_prev_active = $month-1; 
            $thn_prev_active = $year;
            $bulan_kemarin="$thn_prev_active-$bln_prev_active-26";
            $bulan_sekarang="$thn_current_active-$bln_current_active-26";
        }

        $data['tunjangan_kerjas'] = $this->tunjanganKerja->select('nominal')->where('id',$data['karyawan']['tunjangan_kerja_id'])->first();
        if(empty($data['tunjangan_kerjas'])){
            $data['tunjangan_kerja'] = 0;
        }else{
            $data['tunjangan_kerja'] = $data['tunjangan_kerjas']['nominal'];
        }
        //jumlah alpa
        $data['alpa'] = $this->presensiInfo->where('pin',$data['karyawan']['pin'])
                                    ->whereBetween('scan_date',[$bulan_kemarin,$bulan_sekarang])
                                    ->where('status',7)
                                    ->get();
        //jumlah diliburkan
        $data['diliburkan'] = $this->presensiInfo->where('pin',$data['karyawan']['pin'])
                                    ->whereBetween('scan_date',[$bulan_kemarin,$bulan_sekarang])
                                    ->where('status',14)
                                    ->get();
        //jumlah cuti
        $data['cuti'] = $this->presensiInfo->where('pin',$data['karyawan']['pin'])
                                    ->whereBetween('scan_date',[$bulan_kemarin,$bulan_sekarang])
                                    ->where('status',13)
                                    ->get();
        //jumlah sakit
        $data['sakit'] = $this->presensiInfo->where('pin',$data['karyawan']['pin'])
                                    ->whereBetween('scan_date',[$bulan_kemarin,$bulan_sekarang])
                                    ->where('status',4)
                                    ->get();
        // dd($data['sakit']);
        //jumlah ijin full
        $data['ijin_full'] = $this->presensiInfo->where('pin',$data['karyawan']['pin'])
                                    ->whereBetween('scan_date',[$bulan_kemarin,$bulan_sekarang])
                                    ->where('status',6)
                                    ->get();
        //jumlah status terlambat
        $data['terlambats'] = $this->presensiInfo->where('pin',$data['karyawan']['pin'])
                                    ->whereBetween('scan_date',[$bulan_kemarin,$bulan_sekarang])
                                    ->where('status',3)
                                    ->get();
        $telat_1=0; //terlambat < 5 menit
        $telat_2=0; //terlambat > 5 menit < 15 menit
        $telat_3=0; //terlambat > 15 menit < 1 jam
        $telat_4=0; //terlambat > 1 jam < 3 jam	
        $telat_5=0; //terlambat > 3 jam	
        $data['telat_1'] = $telat_1;
        $data['telat_2'] = $telat_2;
        $data['telat_3'] = $telat_3;
        $data['telat_4'] = $telat_4;
        $data['telat_5'] = $telat_4;
        foreach ($data['terlambats'] as $key => $terlambat) {
            $explode_keterangan_terlambat = explode("@",$terlambat->keterangan);
            $jam_keterangan_terlambat=strtotime($explode_keterangan_terlambat[1]);
            $jam_datang_terlambat=strtotime(Carbon::parse($terlambat['scan_date'])->format('H:i'));
            // $jam_datang_terlambat=strtotime($terlambat['jam_datang_telat']);
            $menit_telat=(($jam_datang_terlambat-$jam_keterangan_terlambat)/60);
            // dd($menit_telat);
            if($menit_telat <= 4){
                $telat_1=$telat_1+1;
                $data['telat_1'] = $telat_1;
            }
            else if(($menit_telat >= 5) && ($menit_telat <= 15) ){
                $telat_2=$telat_2+1;
                $data['telat_2'] = $telat_2;
            }
            else if(($menit_telat >= 16) && ($menit_telat <= 60)){
                $telat_3=$telat_3+1;
                $data['telat_3'] = $telat_3;
            }
            else if(($menit_telat >= 61) && ($menit_telat <= 179)){
                $telat_4=$telat_4+1;
                $data['telat_4'] = $telat_4;
            }else if($menit_telat > 179){
                $telat_5=$telat_5+1;
                $data['telat_5'] = $telat_5;
            }
            // else{
            //     $telat_4=$telat_4+1;
            //     $data['telat_4'] = $telat_4;
            // }
        }

        //jumlah status pulang awal
        $data['pulang_awals'] = $this->presensiInfo->where('pin',$data['karyawan']['pin'])
                                    ->whereBetween('scan_date',[$bulan_kemarin,$bulan_sekarang])
                                    ->where(function($query) {
                                        $query->where('status','=',9)
                                            ->orWhere('status','=',10);
                                    })
                                    // ->where('status',9)
                                    // ->where('status',10)
                                    ->get();
        // dd($data['pulang_awals']);
        $pulang_1=0; //pulang awal < 4 jam
		$pulang_2=0; //tpulang awal > 4 jam
        $data['pulang_1'] = $pulang_1;
        $data['pulang_2'] = $pulang_2;
        foreach ($data['pulang_awals'] as $key => $pulang_awal) {
            $explode_pulang_seharusnya=explode("@",$pulang_awal->keterangan);
			$jam_pulang_seharusnya=strtotime($explode_pulang_seharusnya[3]);
			$jam_pulang_awal=strtotime($pulang_awal->jam_pulang_awal);
			$menit_sisa_jam_kerja=(($jam_pulang_seharusnya-$jam_pulang_awal)/60);
			if ($menit_sisa_jam_kerja<=300){
                $pulang_1=$pulang_1+1;
                $data['pulang_1'] = $pulang_1;
            }
			else{
                $pulang_2=$pulang_2+1;
                $data['pulang_2'] = $pulang_2;
            }
        }

        //jumlah ijin saat jam kerja
        $data['keluar_masuks'] = $this->keluarMasuk->where('nik',$data['karyawan']['nik'])
                                    ->whereBetween('tanggal_ijin',[$bulan_kemarin,$bulan_sekarang])
                                    ->get();
        // dd($data['keluar_masuks']);
        $ijin_15=0; //ijin kurang dari 15 menit
		$ijin_k4=0; //ijin > 15menit < 4 jam
		$ijin_l4=0; //ijin > 4 jam
        $data['ijin_15']=$ijin_15;
        $data['ijin_k4']=$ijin_k4;
        $data['ijin_l4']=$ijin_l4;
        foreach ($data['keluar_masuks'] as $key => $keluar_masuk) {
            $jam_keluar=strtotime($keluar_masuk->jam_keluar);
			$jam_datang=strtotime($keluar_masuk->jam_datang);
			$menit_keluar_masuk=(($jam_datang-$jam_keluar)/60);
			if ($menit_keluar_masuk<=15){
                $ijin_15=$ijin_15+1;
                $data['ijin_15']=$ijin_15;
            }
			else if (($menit_keluar_masuk>15)&&($menit_keluar_masuk<=239)){
                $ijin_k4=$ijin_k4+1;
                $data['ijin_k4']=$ijin_k4;
            }
			else {
                $ijin_l4=$ijin_l4+1;
                $data['ijin_l4']=$ijin_l4;
            }
        }

        //lihat tanggal masuk
        // $data['log_posisi'] = $this->logPosisi->select('tanggal')->where('nik',$nik)->first();
		$selisih_tanggal_masuk=strtotime($bulan_kemarin)-strtotime($data['karyawan']['tanggal']);
		if ($selisih_tanggal_masuk>=0)$div_tk=0;else $div_tk=75000;

        $total_potongan_tk=(75000*$data['alpa']->count())+(75000*$data['diliburkan']->count())+(75000*$data['sakit']->count())+(75000*$data['cuti']->count())+(75000*$data['ijin_full']->count())+($data['ijin_15']*25000)+($data['ijin_k4']*40000)+
        ($data['ijin_l4']*75000)+($data['pulang_1']*40000)+($data['pulang_2']*75000)+($data['telat_1']*15000)+($data['telat_2']*25000)+($data['telat_3']*30000)+($data['telat_4']*40000)+$div_tk;
        if ($total_potongan_tk>75000){$total_potongan_tk=75000;}else {$total_potongan_tk=0;}

        // if ($data['pengerjaan_weekly']['tunjangan_kehadiran'] == 75000) {
        //     $data['total_potongan_tk']=0;
        // }
        // // elseif($data['pengerjaan_weekly']['tunjangan_kehadiran'] < 75000){
        // //     $data['total_potongan_tk']=$data['pengerjaan_weekly']['tunjangan_kehadiran'];
        // // }
        // else{
        //     $data['total_potongan_tk']=$total_potongan_tk; 
        // }

        $data['total_potongan_tk']=$total_potongan_tk;
        return view('backend.pengerjaan.packing_ekspor_kemas.input_hasil_kerja_karyawan',$data);
    }

    public function hasil_kerja_karyawan_kemas_ekspor_view_simpan(Request $request, $id, $kode_pengerjaan, $nik)
    {
        // $input = $request->all();
        if($id == 1){
            $kode_jenis_operator_detail = 'L';
        }
        elseif($id == 2){
            $kode_jenis_operator_detail = 'E';
        }
        elseif($id == 3){
            $kode_jenis_operator_detail = 'A';
        }

        $data['id'] = $id;
        $data['kode_pengerjaan'] = $kode_pengerjaan;
        $data['nik'] = $nik;

        $input['upah_dasar'] = $request->upah_dasar;
        $input['upah_makan'] = $request->upah_makan;
        
        $input['uang_makan'] = $request->uang_makan;
        $input['plus_1'] = $request->plus_1."|".$request->keterangan_plus_1;
        $input['plus_2'] = $request->plus_2."|".$request->keterangan_plus_2;
        $input['plus_3'] = $request->plus_3."|".$request->keterangan_plus_3;
        
        $input['minus_1'] = $request->minus_1."|".$request->keterangan_minus_1;
        $input['minus_2'] = $request->minus_2."|".$request->keterangan_minus_2;

        if ($request->akhir_bulan == 'n') {
            $input['tunjangan_kerja'] = 0;
            $input['tunjangan_kehadiran'] = 0;
        }else{
            $input['tunjangan_kerja'] = $request->tunjangan_kerja;
            $input['tunjangan_kehadiran'] = 75000-$request->pot_tunjangan_kehadiran;
        }

        if ($request->check_jht == 'on') {
            $input['jht'] = $request->jht;
        }else{
            $input['jht'] = 0;
        }

        if($request->check_bpjs_kesehatan == 'on'){
            $input['bpjs_kesehatan'] = $request->bpjs_kesehatan;
        }else{
            $input['bpjs_kesehatan'] = 0;
        }
        // dd($input);
        $operator_karyawan = $this->karyawanOperator->select('id')->where('nik',$nik)->where('status','Y')->where('jenis_operator_detail_pekerjaan_id',6)->orderBy('id','desc')->first();
        $pengerjaan_weekly = $this->pengerjaanWeekly->where('kode_payrol',substr($kode_pengerjaan,0,2).$kode_jenis_operator_detail.'_'.substr($kode_pengerjaan,3))
                                            ->where('operator_karyawan_id',$operator_karyawan->id)
                                            ->first();
        // $pengerjaan_weekly->update($input);
        // dd($pengerjaan_weekly);
        if (!empty($pengerjaan_weekly)) {
            $pengerjaan_weekly->update($input);
            return response()->json([
                'success' => true,
                'message_title' => 'Berhasil',
                'message_content' => 'Data Berhasil Disimpan'
            ]);
        }else{
            return response()->json([
                'success' => false,
                'message_title' => 'Gagal',
                'message_content' => 'Data Tidak Berhasil Disimpan'
            ]);
        }
    }

    public function hasil_kerja_gagang_ekspor($id, $kode_pengerjaan)
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

        $data['id'] = $id;
        $data['kode_pengerjaan'] = $kode_pengerjaan;
        $data['jenis_operator_detail_pekerjaan'] = $this->jenisOperatorDetailPengerjaan->find(7);
        $data['new_data_pengerjaan'] = $this->newDataPengerjaan->where('kode_pengerjaan',$kode_pengerjaan)->first();
        $data['explode_tanggal_pengerjaans'] = explode('#',$data['new_data_pengerjaan']['tanggal']);
        $data['count_tanggal'] = count($data['explode_tanggal_pengerjaans'])-1;
        // $data['packing_ekspors'] = KaryawanOperator::select([
        //                             'operator_karyawan.id as id',
        //                             'operator_karyawan.nik as nik',
        //                             'biodata_karyawan.nama as nama',
        //                             ])
        //                             ->where('jenis_operator_id',1)
        //                             ->where('jenis_operator_detail_id',2)
        //                             ->where('jenis_operator_detail_pekerjaan_id',5)
        //                             ->leftJoin('biodata_karyawan','biodata_karyawan.nik','=','operator_karyawan.nik')
        //                             ->rightJoin('pengerjaan','pengerjaan.operator_karyawan_id','=','operator_karyawan.id')
        //                             ->orderBy('biodata_karyawan.nama','asc')
        //                             ->get();

        $data['pengerjaans'] = $this->pengerjaanWeekly->select([
                                                    'pengerjaan_weekly.kode_payrol as kode_payrol',
                                                    'pengerjaan_weekly.operator_karyawan_id as operator_karyawan_id',
                                                    'operator_karyawan.nik as nik',
                                                    'biodata_karyawan.nama as nama',
                                                    'pengerjaan_weekly.upah_dasar as upah_dasar',
                                                    'pengerjaan_weekly.tunjangan_kerja as tunjangan_kerja',
                                                    'pengerjaan_weekly.tunjangan_kehadiran as tunjangan_kehadiran',
                                                    'pengerjaan_weekly.uang_makan as uang_makan',
                                                    'pengerjaan_weekly.plus_1 as plus_1',
                                                    'pengerjaan_weekly.plus_2 as plus_2',
                                                    'pengerjaan_weekly.plus_3 as plus_3',
                                                    'pengerjaan_weekly.minus_1 as minus_1',
                                                    'pengerjaan_weekly.minus_2 as minus_2',
                                                    'pengerjaan_weekly.jht as jht',
                                                    'pengerjaan_weekly.bpjs_kesehatan as bpjs_kesehatan',
                                                ])
                                                ->where('kode_payrol',substr($kode_pengerjaan,0,2).$kode_jenis_operator_detail.'_'.substr($kode_pengerjaan,3))
                                                ->where('operator_karyawan.jenis_operator_id',1)
                                                ->where('operator_karyawan.jenis_operator_detail_id',2)
                                                ->where('operator_karyawan.jenis_operator_detail_pekerjaan_id',7)
                                                ->leftJoin('operator_karyawan','operator_karyawan.id','=','pengerjaan_weekly.operator_karyawan_id')
                                                ->leftJoin('itic_emp_new.biodata_karyawan','biodata_karyawan.nik','=','operator_karyawan.nik')
                                                ->orderBy('biodata_karyawan.nama','asc')
                                                ->get();
        // dd($data['pengerjaans']);
        return view('backend.pengerjaan.packing_ekspor_gagang.packing_ekspor_gagang',$data);
    }

    public function hasil_kerja_gagang_ekspor_view_hasil($id, $kode_pengerjaan, $tanggal)
    {
        $data['id'] = $id;
        $data['kode_pengerjaan'] = $kode_pengerjaan;
        $data['tanggal'] = $tanggal;
        $data['packing_ekspors'] = $this->karyawanOperator->select([
                                                    'operator_karyawan.id as id',
                                                    'operator_karyawan.nik as nik',
                                                    'biodata_karyawan.nama as nama',
                                                    'pengerjaan.hasil_kerja_1 as hasil_kerja_1',
                                                    'pengerjaan.hasil_kerja_2 as hasil_kerja_2',
                                                    'pengerjaan.hasil_kerja_3 as hasil_kerja_3',
                                                    'pengerjaan.hasil_kerja_4 as hasil_kerja_4',
                                                    'pengerjaan.hasil_kerja_5 as hasil_kerja_5',
                                                    ])
                                                    ->leftJoin('itic_emp_new.biodata_karyawan','biodata_karyawan.nik','=','operator_karyawan.nik')
                                                    ->rightJoin('pengerjaan','pengerjaan.operator_karyawan_id','=','operator_karyawan.id')
                                                    ->where('jenis_operator_id',1)
                                                    ->where('jenis_operator_detail_id',2)
                                                    ->where('jenis_operator_detail_pekerjaan_id',7)
                                                    ->where('pengerjaan.tanggal_pengerjaan',$tanggal)
                                                    ->where('pengerjaan.kode_pengerjaan',$kode_pengerjaan)
                                                    // ->with('biodata_karyawan')
                                                    ->orderBy('biodata_karyawan.nama','asc')
                                                    ->get();

        $data['umk_borongan_ekspors'] = $this->umkBoronganEkspor->select('id','jenis_produk','umk_pilih_gagang')->where('status','Y')->get();
        // dd($data);
        return view('backend.pengerjaan.packing_ekspor_gagang.input_hasil_kerja',$data);
    }
    
    public function hasil_kerja_gagang_ekspor_view_simpan(Request $request, $id, $kode_pengerjaan, $tanggal)
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

        $input = $request->all();
        $input['id'] = $id;
        $input['kode_pengerjaan'] = $kode_pengerjaan;
        $input['tanggal'] = $tanggal;

        $data['karyawan_pengerjaans'] = $this->pengerjaan->select([
                                                        'pengerjaan.id as id',
                                                        'operator_karyawan.nik as nik',
                                                        'pengerjaan.operator_karyawan_id as id_operator_karyawan'
                                                    ])
                                                    ->leftJoin('operator_karyawan','operator_karyawan.id','=','pengerjaan.operator_karyawan_id')
                                                    ->leftJoin('itic_emp_new.biodata_karyawan','biodata_karyawan.nik','=','operator_karyawan.nik')
                                                    ->where('tanggal_pengerjaan',$tanggal)
                                                    // ->where('kode_pengerjaan',$kode_pengerjaan)
                                                    // ->where('pengerjaan.operator_karyawan_id','operator_karyawan.id')
                                                    ->where('operator_karyawan.jenis_operator_id',1)
                                                    ->where('operator_karyawan.jenis_operator_detail_id',2)
                                                    ->where('operator_karyawan.jenis_operator_detail_pekerjaan_id',6)
                                                    ->where('pengerjaan.kode_pengerjaan',$kode_pengerjaan)
                                                    ->orderBy('biodata_karyawan.nama','asc')
                                                    ->get();
        
        foreach ($data['karyawan_pengerjaans'] as $key => $karyawan_pengerjaan) {
            // dd($request->umk_borongan_lokal_kerja_1);
            if ($request->umk_borongan_lokal_kerja_1) {
                $umk_borongan_lokal_1 = $this->umkBoronganEkspor->select('id','jenis_produk','umk_kemas')
                                                        ->where('id',$request->umk_borongan_lokal_kerja_1)
                                                        ->where('status','Y')
                                                        ->first();

                // $hasil_pengerjaan_1 = $umk_borongan_lokal_1->id.'|'.$umk_borongan_lokal_1->umk_packing*$request->hasil_kerja_1[$key];
                
                // if ($request->hasil_kerja_1[$key]) {
                // }

                if ($request->hasil_kerja_1[$key]) {
                    $hasil_pengerjaan_1 = $umk_borongan_lokal_1->id.'|'.$request->hasil_kerja_1[$key];
                    $hasil_kerja_1 = $umk_borongan_lokal_1->umk_kemas*$request->hasil_kerja_1[$key];
                }else{
                    $hasil_pengerjaan_1 = $umk_borongan_lokal_1->id.'|'.'0';
                    $hasil_kerja_1 = 0;
                }

                if($request->lembur_kerja_1){
                    $lembur_1 = '1-y';
                    $hasil_lembur_1 = '1.5';
                }else{
                    $lembur_1 = '1-n';
                    $hasil_lembur_1 = '1';
                }

                if($request->total_jam_1[$key]){
                    $total_jam_1 = $request->total_jam_1[$key];
                }else{
                    $total_jam_1 = '0';
                }
                
            }else{
                $hasil_pengerjaan_1 = '0'.'|'.'0';
                $lembur_1 = '1-n';
                $hasil_lembur_1 = '1';
                $total_jam_1 = '0';
                $hasil_kerja_1 = 0;
            }

            // dd($hasil_pengerjaan_1);
            
            if ($request->umk_borongan_lokal_kerja_2) {
                $umk_borongan_lokal_2 = $this->umkBoronganEkspor->select('id','jenis_produk','umk_kemas')
                                                        ->where('id',$request->umk_borongan_lokal_kerja_2)
                                                        ->where('status','Y')
                                                        ->first();

                // $hasil_pengerjaan_2 = $umk_borongan_lokal_2->id.'|'.$umk_borongan_lokal_2->umk_packing*$request->hasil_kerja_2[$key];
                
                if ($request->hasil_kerja_2[$key]) {
                    $hasil_pengerjaan_2 = $umk_borongan_lokal_2->id.'|'.$request->hasil_kerja_2[$key];
                    $hasil_kerja_2 = $umk_borongan_lokal_2->umk_kemas*$request->hasil_kerja_2[$key];
                }else{
                    $hasil_pengerjaan_2 = $umk_borongan_lokal_2->id.'|'.'0';
                    $hasil_kerja_2 = 0;
                }

                if($request->lembur_kerja_2){
                    $lembur_2 = '2-y';
                    $hasil_lembur_2 = '1.5';
                }else{
                    $lembur_2 = '2-n';
                    $hasil_lembur_2 = '1';
                }

                if($request->total_jam_2[$key]){
                    $total_jam_2 = $request->total_jam_2[$key];
                }else{
                    $total_jam_2 = '0';
                }

            }else{
                $hasil_pengerjaan_2 = '0'.'|'.'0';
                $lembur_2 = '2-n';
                $hasil_lembur_2 = '1';
                $total_jam_2 = '0';
                $hasil_kerja_2 = 0;
            }

            if ($request->umk_borongan_lokal_kerja_3) {
                $umk_borongan_lokal_3 = $this->umkBoronganEkspor->select('id','jenis_produk','umk_kemas')
                                                        ->where('id',$request->umk_borongan_lokal_kerja_3)
                                                        ->where('status','Y')
                                                        ->first();

                // $hasil_pengerjaan_3 = $umk_borongan_lokal_3->id.'|'.$umk_borongan_lokal_3->umk_packing*$request->hasil_kerja_3[$key];
                
                if ($request->hasil_kerja_3[$key]) {
                    $hasil_pengerjaan_3 = $umk_borongan_lokal_3->id.'|'.$request->hasil_kerja_3[$key];
                    $hasil_kerja_3 = $umk_borongan_lokal_3->umk_kemas*$request->hasil_kerja_3[$key];
                }else{
                    $hasil_pengerjaan_3 = $umk_borongan_lokal_3->id.'|'.'0';
                    $hasil_kerja_3 = 0;
                }
            
                if($request->lembur_kerja_3){
                    $lembur_3 = '3-y';
                    $hasil_lembur_3 = '1.5';
                }else{
                    $lembur_3 = '3-n';
                    $hasil_lembur_3 = '1';
                }

                if($request->total_jam_3[$key]){
                    $total_jam_3 = $request->total_jam_3[$key];
                }else{
                    $total_jam_3 = '0';
                }

            }else{
                $hasil_pengerjaan_3 = '0'.'|'.'0';
                $lembur_3 = '3-n';
                $hasil_lembur_3 = '1';
                $total_jam_3 = '0';
                $hasil_kerja_3 = 0;
            }

            if ($request->umk_borongan_lokal_kerja_4) {
                $umk_borongan_lokal_4 = $this->umkBoronganEkspor->select('id','jenis_produk','umk_kemas')
                                                        ->where('id',$request->umk_borongan_lokal_kerja_4)
                                                        ->where('status','Y')
                                                        ->first();

                // $hasil_pengerjaan_4 = $umk_borongan_lokal_4->id.'|'.$umk_borongan_lokal_4->umk_packing*$request->hasil_kerja_4[$key];
                
                if ($request->hasil_kerja_4[$key]) {
                    $hasil_pengerjaan_4 = $umk_borongan_lokal_4->id.'|'.$request->hasil_kerja_4[$key];
                    $hasil_kerja_4 = $umk_borongan_lokal_4->umk_kemas*$request->hasil_kerja_4[$key];
                }else{
                    $hasil_pengerjaan_4 = $umk_borongan_lokal_4->id.'|'.'0';
                    $hasil_kerja_4 = 0;
                }
            
                if($request->lembur_kerja_4){
                    $lembur_4 = '4-y';
                    $hasil_lembur_4 = '1.5';
                }else{
                    $lembur_4 = '4-n';
                    $hasil_lembur_4 = '1';
                }

                if($request->total_jam_4[$key]){
                    $total_jam_4 = $request->total_jam_4[$key];
                }else{
                    $total_jam_4 = '0';
                }

            }else{
                $hasil_pengerjaan_4 = '0'.'|'.'0';
                $lembur_4 = '4-n';
                $hasil_lembur_4 = '1';
                $total_jam_4 = '0';
                $hasil_kerja_4 = 0;
            }

            if ($request->umk_borongan_lokal_kerja_5) {
                $umk_borongan_lokal_5 = $this->umkBoronganEkspor->select('id','jenis_produk','umk_kemas')
                                                        ->where('id',$request->umk_borongan_lokal_kerja_5)
                                                        ->where('status','Y')
                                                        ->first();

                // $hasil_pengerjaan_5 = $umk_borongan_lokal_5->id.'|'.$umk_borongan_lokal_5->umk_packing*$request->hasil_kerja_5[$key];
                
                if ($request->hasil_kerja_5[$key]) {
                    $hasil_pengerjaan_5 = $umk_borongan_lokal_5->id.'|'.$request->hasil_kerja_5[$key];
                    $hasil_kerja_5 = $umk_borongan_lokal_5->umk_kemas*$request->hasil_kerja_5[$key];
                }else{
                    $hasil_pengerjaan_5 = $umk_borongan_lokal_5->id.'|'.'0';
                    $hasil_kerja_5 = 0;
                }
            
                if($request->lembur_kerja_5){
                    $lembur_5 = '5-y';
                    $hasil_lembur_5 = '1.5';
                }else{
                    $lembur_5 = '5-n';
                    $hasil_lembur_5 = '1';
                }

                if($request->total_jam_5[$key]){
                    $total_jam_5 = $request->total_jam_5[$key];
                }else{
                    $total_jam_5 = '0';
                }

            }else{
                $hasil_pengerjaan_5 = '0'.'|'.'0';
                $lembur_5 = '5-n';
                $hasil_lembur_5 = '1';
                $total_jam_5 = '0';
                $hasil_kerja_5 = 0;
            }

            $lemburs = '|'.$lembur_1.'|'.$lembur_2.'|'.$lembur_3.'|'.$lembur_4.'|'.$lembur_5;
            $hasil_upah_dasar = $hasil_kerja_1+$hasil_kerja_2+$hasil_kerja_3+$hasil_kerja_4+$hasil_kerja_5;
            // dd($hasil_upah_dasar);
            $karyawan_pengerjaan->update([
                'hasil_kerja_1' => $hasil_pengerjaan_1,
                'hasil_kerja_2' => $hasil_pengerjaan_2,
                'hasil_kerja_3' => $hasil_pengerjaan_3,
                'hasil_kerja_4' => $hasil_pengerjaan_4,
                'hasil_kerja_5' => $hasil_pengerjaan_5,
                'lembur' => $lemburs,
                'total_jam_kerja_1' => $total_jam_1,
                'total_jam_kerja_2' => $total_jam_2,
                'total_jam_kerja_3' => $total_jam_3,
                'total_jam_kerja_4' => $total_jam_4,
                'total_jam_kerja_5' => $total_jam_5,
            ]);
        }

        return response()->json([
            'success' => true,
            'message_title' => 'Berhasil',
            'message_content' => 'Data Berhasil Disimpan'
        ]);
    }

    public function hasil_kerja_karyawan_gagang_ekspor_view($id, $kode_pengerjaan, $nik, $month, $year)
    {
        $data['id'] = $id;
        $data['nik'] = $nik;
        $data['kode_pengerjaan'] = $kode_pengerjaan;
        $data['month'] = $month;
        $data['year'] = $year;
        // dd($data);

        if($id == 1){
            $kode_jenis_operator_detail = 'L';
        }
        elseif($id == 2){
            $kode_jenis_operator_detail = 'E';
        }
        elseif($id == 3){
            $kode_jenis_operator_detail = 'A';
        }
        $data['karyawan'] = $this->karyawanOperator->select([
                                                    'operator_karyawan.id as id',
                                                    'biodata_karyawan.nama as nama',
                                                    'operator_karyawan.nik as nik',
                                                    'biodata_karyawan.pin as pin',
                                                    'operator_karyawan.tunjangan_kerja_id as tunjangan_kerja_id'
                                                    ])
                                                    ->leftJoin('itic_emp_new.biodata_karyawan','biodata_karyawan.nik','=','operator_karyawan.nik')
                                                    ->where('operator_karyawan.nik',$nik)
                                                    ->where('operator_karyawan.status','Y')
                                                    ->where('operator_karyawan.jenis_operator_detail_pekerjaan_id',7)
                                                    ->orderBy('id','desc')
                                                    ->first();
        // dd($data['karyawan']);
        $data['new_data_pengerjaan'] = $this->newDataPengerjaan->select('akhir_bulan')->where('kode_pengerjaan',$kode_pengerjaan)->first();
        $data['jhts'] = $this->bpjsJht->where('status','y')->get();
        $data['bpjs_kesehatan'] = $this->bpjsKesehatan->select('nominal')->where('status','y')->first();
        // dd($data['new_data_pengerjaan']);

        $data['pengerjaans'] = $this->pengerjaan->where('operator_karyawan_id',$data['karyawan']['id'])->where('kode_pengerjaan',$kode_pengerjaan)->get();
        
        $data['upah'] = array();

        foreach ($data['pengerjaans'] as $key => $pengerjaan) {
            $explode_hasil_kerja_1 = explode("|",$pengerjaan->hasil_kerja_1);
            $umk_borongan_lokal_1 = $this->umkBoronganEkspor->select('id','jenis_produk','umk_packing','umk_kemas','umk_pilih_gagang')->where('id',$explode_hasil_kerja_1[0])->first();
            if(empty($umk_borongan_lokal_1)){
                $jenis_produk_1 = '-';
                $hasil_kerja_1 = null;
                $data_explode_hasil_kerja_1 = '-';
                $lembur_1 = 1;
            }else{
                $jenis_produk_1 = $umk_borongan_lokal_1->jenis_produk;
                $hasil_kerja_1 = $explode_hasil_kerja_1[1]*$umk_borongan_lokal_1->umk_pilih_gagang;
                $data_explode_hasil_kerja_1 = $explode_hasil_kerja_1[1];

                $explode_lembur_1 = explode("|",$pengerjaan->lembur);
                $explode_status_lembur_1 = explode("-",$explode_lembur_1[1]);

                if($explode_status_lembur_1[1] == 'y'){
                    $lembur_1 = 1.5;
                }else{
                    $lembur_1 = 1;
                }
            }

            $explode_hasil_kerja_2 = explode("|",$pengerjaan->hasil_kerja_2);
            $umk_borongan_lokal_2 = $this->umkBoronganEkspor->select('id','jenis_produk','umk_packing','umk_kemas','umk_pilih_gagang')->where('id',$explode_hasil_kerja_2[0])->first();
            if(empty($umk_borongan_lokal_2)){
                $jenis_produk_2 = '-';
                $hasil_kerja_2 = null;
                $data_explode_hasil_kerja_2 = '-';
                $lembur_2 = 1;
            }else{
                $jenis_produk_2 = $umk_borongan_lokal_2->jenis_produk;
                $hasil_kerja_2 = $explode_hasil_kerja_2[1]*$umk_borongan_lokal_2->umk_pilih_gagang;
                $data_explode_hasil_kerja_2 = $explode_hasil_kerja_2[1];

                $explode_lembur_2 = explode("|",$pengerjaan->lembur);
                $explode_status_lembur_2 = explode("-",$explode_lembur_2[2]);

                if($explode_status_lembur_2[1] == 'y'){
                    $lembur_2 = 1.5;
                }else{
                    $lembur_2 = 1;
                }
            }

            $explode_hasil_kerja_3 = explode("|",$pengerjaan->hasil_kerja_3);
            $umk_borongan_lokal_3 = $this->umkBoronganEkspor->select('id','jenis_produk','umk_packing','umk_kemas','umk_pilih_gagang')->where('id',$explode_hasil_kerja_3[0])->first();
            if(empty($umk_borongan_lokal_3)){
                $jenis_produk_3 = '-';
                $hasil_kerja_3 = null;
                $data_explode_hasil_kerja_3 = '-';
                $lembur_3 = 1;
            }else{
                $jenis_produk_3 = $umk_borongan_lokal_3->jenis_produk;
                $hasil_kerja_3 = $explode_hasil_kerja_3[1]*$umk_borongan_lokal_3->umk_pilih_gagang;
                $data_explode_hasil_kerja_3 = $explode_hasil_kerja_3[1];

                $explode_lembur_3 = explode("|",$pengerjaan->lembur);
                $explode_status_lembur_3 = explode("-",$explode_lembur_3[3]);

                if($explode_status_lembur_3[1] == 'y'){
                    $lembur_3 = 1.5;
                }else{
                    $lembur_3 = 1;
                }
            }

            $explode_hasil_kerja_4 = explode("|",$pengerjaan->hasil_kerja_4);
            $umk_borongan_lokal_4 = $this->umkBoronganEkspor->select('id','jenis_produk','umk_packing','umk_kemas','umk_pilih_gagang')->where('id',$explode_hasil_kerja_4[0])->first();
            if(empty($umk_borongan_lokal_4)){
                $jenis_produk_4 = '-';
                $hasil_kerja_4 = null;
                $data_explode_hasil_kerja_4 = '-';
                $lembur_4 = 1;
            }else{
                $jenis_produk_4 = $umk_borongan_lokal_4->jenis_produk;
                $hasil_kerja_4 = $explode_hasil_kerja_4[1]*$umk_borongan_lokal_4->umk_pilih_gagang;
                $data_explode_hasil_kerja_4 = $explode_hasil_kerja_4[1];

                $explode_lembur_4 = explode("|",$pengerjaan->lembur);
                $explode_status_lembur_4 = explode("-",$explode_lembur_4[4]);

                if($explode_status_lembur_4[1] == 'y'){
                    $lembur_4 = 1.5;
                }else{
                    $lembur_4 = 1;
                }
            }

            $explode_hasil_kerja_5 = explode("|",$pengerjaan->hasil_kerja_5);
            $umk_borongan_lokal_5 = $this->umkBoronganEkspor->select('id','jenis_produk','umk_packing','umk_kemas','umk_pilih_gagang')->where('id',$explode_hasil_kerja_5[0])->first();
            if(empty($umk_borongan_lokal_5)){
                $jenis_produk_5 = '-';
                $hasil_kerja_5 = null;
                $data_explode_hasil_kerja_5 = '-';
                $lembur_5 = 1;
            }else{
                $jenis_produk_5 = $umk_borongan_lokal_5->jenis_produk;
                $hasil_kerja_5 = $explode_hasil_kerja_5[1]*$umk_borongan_lokal_5->umk_pilih_gagang;
                $data_explode_hasil_kerja_5 = $explode_hasil_kerja_5[1];

                $explode_lembur_5 = explode("|",$pengerjaan->lembur);
                $explode_status_lembur_5 = explode("-",$explode_lembur_5[5]);

                if($explode_status_lembur_5[1] == 'y'){
                    $lembur_5 = 1.5;
                }else{
                    $lembur_5 = 1;
                }
            }

            $hasil_upah = ($hasil_kerja_1*$lembur_1)+($hasil_kerja_2*$lembur_2)+($hasil_kerja_3*$lembur_3)+($hasil_kerja_4*$lembur_4)+($hasil_kerja_5*$lembur_5);

            array_push($data['upah'],$hasil_upah);
        }

        //Hitung Masa Kerja
        // $data['karyawan_masa_kerja'] = DB::connection('emp')->table('log_posisi')
        //                                                     ->select('tanggal')
        //                                                     ->where('nik',$nik)
        //                                                     ->first();
        $awal  = new DateTime($data['karyawan']->tanggal);
        $akhir = new DateTime(); // Waktu sekarang
        $diff  = $awal->diff($akhir);
        $data['masa_kerja'] = $diff->y.' Tahun '.$diff->m.' Bulan '.$diff->d.' Hari';
        $data['masa_kerja_tahun'] = $diff->y;
        $data['masa_kerja_hari'] = $diff->d;
        //End Hitung Masa Kerja

        $data['upah_dasar_karyawan'] = [];
        foreach ($data['jhts'] as $key => $jht) {
            if ($data['masa_kerja_tahun'] > 15) {
                if ($jht->urutan == 3) {
                    $data['upah_dasar_karyawan'] = ($data['bpjs_kesehatan']['nominal'] + 100000)/25;
                }
            }
            elseif($data['masa_kerja_tahun'] >= 10 && $data['masa_kerja_tahun'] <= 15 && $data['masa_kerja_hari'] >= 1){
                if ($jht->urutan == 2) {
                    $data['upah_dasar_karyawan'] = ($data['bpjs_kesehatan']['nominal'] + 50000)/25;
                }
            }
            elseif($data['masa_kerja_tahun'] <= 10 || $data['masa_kerja_hari'] >= 1){
                if ($jht->urutan == 1) {
                    $data['upah_dasar_karyawan'] = ($data['bpjs_kesehatan']['nominal'] + 0)/25;
                }
            }
        }

        // echo 'Selisih waktu: ';
        // echo $diff->y . ' tahun, ';
        // echo $diff->m . ' bulan, ';
        // echo $diff->d . ' hari, ';
        // echo $diff->h . ' jam, ';
        // echo $diff->i . ' menit, ';
        // echo $diff->s . ' detik, ';
        // dd($data);

        $data['pengerjaan_weekly'] = $this->pengerjaanWeekly->where('operator_karyawan_id',$data['karyawan']['id'])
                                                    // ->where('kode_payrol',substr($kode_pengerjaan,0,2).$kode_jenis_operator_detail.'_'.substr($kode_pengerjaan,3))
                                                    ->first();
        // dd($data['pengerjaan_weekly']);
        // $data['presensi_info'] = PresensiInfo::all();
        // dd($data['presensi_info']);
        $tgl_current_active = Carbon::now()->format('d');
        $bln_active = Carbon::now()->format('m');
        $bln_current_active = $month;
        $thn_current_active = $year;

        // $bulan_kemarin = Carbon::now()->subMonth()->format('Y-m').'-26';
        // $bulan_sekarang = Carbon::now()->format('Y-m').'-25';

        if($month == 1 && $tgl_current_active <10){
            $thn_prev_active = $year-1;
            $bulan_kemarin="$thn_prev_active-11-26";
            $bulan_sekarang="$thn_prev_active-12-26";
        }elseif($month == 1 && $tgl_current_active>10 or $month == 2 && $tgl_current_active<10 && $bln_active != 3){
            $thn_prev_active = $year-1;
            $bulan_kemarin="$thn_prev_active-12-26";
            $bulan_sekarang="$thn_current_active-01-26";
        }else{
            $bln_prev_active = $month-1; 
            $thn_prev_active = $year;
            $bulan_kemarin="$thn_prev_active-$bln_prev_active-26";
            $bulan_sekarang="$thn_current_active-$bln_current_active-26";
        }

        $data['tunjangan_kerjas'] = $this->tunjanganKerja->select('nominal')->where('id',$data['karyawan']['tunjangan_kerja_id'])->first();
        if(empty($data['tunjangan_kerjas'])){
            $data['tunjangan_kerja'] = 0;
        }else{
            $data['tunjangan_kerja'] = $data['tunjangan_kerjas']['nominal'];
        }
        //jumlah alpa
        $data['alpa'] = $this->presensiInfo->where('pin',$data['karyawan']['pin'])
                                    ->whereBetween('scan_date',[$bulan_kemarin,$bulan_sekarang])
                                    ->where('status',7)
                                    ->get();
        //jumlah diliburkan
        $data['diliburkan'] = $this->presensiInfo->where('pin',$data['karyawan']['pin'])
                                    ->whereBetween('scan_date',[$bulan_kemarin,$bulan_sekarang])
                                    ->where('status',14)
                                    ->get();
        //jumlah cuti
        $data['cuti'] = $this->presensiInfo->where('pin',$data['karyawan']['pin'])
                                    ->whereBetween('scan_date',[$bulan_kemarin,$bulan_sekarang])
                                    ->where('status',13)
                                    ->get();
        //jumlah sakit
        $data['sakit'] = $this->presensiInfo->where('pin',$data['karyawan']['pin'])
                                    ->whereBetween('scan_date',[$bulan_kemarin,$bulan_sekarang])
                                    ->where('status',4)
                                    ->get();
        // dd($data['sakit']);
        //jumlah ijin full
        $data['ijin_full'] = $this->presensiInfo->where('pin',$data['karyawan']['pin'])
                                    ->whereBetween('scan_date',[$bulan_kemarin,$bulan_sekarang])
                                    ->where('status',6)
                                    ->get();
        //jumlah status terlambat
        $data['terlambats'] = $this->presensiInfo->where('pin',$data['karyawan']['pin'])
                                    ->whereBetween('scan_date',[$bulan_kemarin,$bulan_sekarang])
                                    ->where('status',3)
                                    ->get();
        $telat_1=0; //terlambat < 5 menit
        $telat_2=0; //terlambat > 5 menit < 15 menit
        $telat_3=0; //terlambat > 15 menit < 1 jam
        $telat_4=0; //terlambat > 1 jam < 3 jam	
        $telat_5=0; //terlambat > 3 jam	
        $data['telat_1'] = $telat_1;
        $data['telat_2'] = $telat_2;
        $data['telat_3'] = $telat_3;
        $data['telat_4'] = $telat_4;
        $data['telat_5'] = $telat_5;
        foreach ($data['terlambats'] as $key => $terlambat) {
            $explode_keterangan_terlambat = explode("@",$terlambat->keterangan);
            $jam_keterangan_terlambat=strtotime($explode_keterangan_terlambat[1]);
            $jam_datang_terlambat=strtotime(Carbon::parse($terlambat['scan_date'])->format('H:i'));
            // $jam_datang_terlambat=strtotime($terlambat['jam_datang_telat']);
            $menit_telat=(($jam_datang_terlambat-$jam_keterangan_terlambat)/60);
            // dd($menit_telat);
            if($menit_telat <= 4){
                $telat_1=$telat_1+1;
                $data['telat_1'] = $telat_1;
            }
            else if(($menit_telat >= 5) && ($menit_telat <= 15) ){
                $telat_2=$telat_2+1;
                $data['telat_2'] = $telat_2;
            }
            else if(($menit_telat >= 16) && ($menit_telat <= 60)){
                $telat_3=$telat_3+1;
                $data['telat_3'] = $telat_3;
            }
            else if(($menit_telat >= 61) && ($menit_telat <= 179)){
                $telat_4=$telat_4+1;
                $data['telat_4'] = $telat_4;
            }else if($menit_telat > 179){
                $telat_5=$telat_5+1;
                $data['telat_5'] = $telat_5;
            }
            // else{
            //     $telat_4=$telat_4+1;
            //     $data['telat_4'] = $telat_4;
            // }
        }

        //jumlah status pulang awal
        $data['pulang_awals'] = $this->presensiInfo->where('pin',$data['karyawan']['pin'])
                                    ->whereBetween('scan_date',[$bulan_kemarin,$bulan_sekarang])
                                    ->where(function($query) {
                                        $query->where('status','=',9)
                                            ->orWhere('status','=',10);
                                    })
                                    // ->where('status',9)
                                    // ->where('status',10)
                                    ->get();
        // dd($data['pulang_awals']);
        $pulang_1=0; //pulang awal < 4 jam
		$pulang_2=0; //tpulang awal > 4 jam
        $data['pulang_1'] = $pulang_1;
        $data['pulang_2'] = $pulang_2;
        foreach ($data['pulang_awals'] as $key => $pulang_awal) {
            $explode_pulang_seharusnya=explode("@",$pulang_awal->keterangan);
			$jam_pulang_seharusnya=strtotime($explode_pulang_seharusnya[3]);
			$jam_pulang_awal=strtotime(Carbon::parse($pulang_awal['scan_date'])->format('H:i'));
            // $jam_pulang_awal=strtotime($pulang_awal->jam_pulang_awal);
			$menit_sisa_jam_kerja=(($jam_pulang_seharusnya-$jam_pulang_awal)/60);
			if ($menit_sisa_jam_kerja<=300){
                $pulang_1=$pulang_1+1;
                $data['pulang_1'] = $pulang_1;
            }
			else{
                $pulang_2=$pulang_2+1;
                $data['pulang_2'] = $pulang_2;
            }
        }

        //jumlah ijin saat jam kerja
        $data['keluar_masuks'] = $this->keluarMasuk->where('nik',$data['karyawan']['nik'])
                                    ->whereBetween('tanggal_ijin',[$bulan_kemarin,$bulan_sekarang])
                                    ->get();
        // dd($data['keluar_masuks']);
        $ijin_15=0; //ijin kurang dari 15 menit
		$ijin_k4=0; //ijin > 15menit < 4 jam
		$ijin_l4=0; //ijin > 4 jam
        $data['ijin_15']=$ijin_15;
        $data['ijin_k4']=$ijin_k4;
        $data['ijin_l4']=$ijin_l4;
        foreach ($data['keluar_masuks'] as $key => $keluar_masuk) {
            $jam_keluar=strtotime($keluar_masuk->jam_keluar);
			$jam_datang=strtotime($keluar_masuk->jam_datang);
			$menit_keluar_masuk=(($jam_datang-$jam_keluar)/60);
			if ($menit_keluar_masuk<=15){
                $ijin_15=$ijin_15+1;
                $data['ijin_15']=$ijin_15;
            }
			else if (($menit_keluar_masuk>15)&&($menit_keluar_masuk<=239)){
                $ijin_k4=$ijin_k4+1;
                $data['ijin_k4']=$ijin_k4;
            }
			else {
                $ijin_l4=$ijin_l4+1;
                $data['ijin_l4']=$ijin_l4;
            }
        }

        //lihat tanggal masuk
        // $data['log_posisi'] = $this->logPosisi->select('tanggal')->where('nik',$nik)->first();
		$selisih_tanggal_masuk=strtotime($bulan_kemarin)-strtotime($data['karyawan']['tanggal']);
		if ($selisih_tanggal_masuk>=0)$div_tk=0;else $div_tk=75000;

        $total_potongan_tk=(75000*$data['alpa']->count())+(75000*$data['diliburkan']->count())+(75000*$data['sakit']->count())+(75000*$data['cuti']->count())+(75000*$data['ijin_full']->count())+($data['ijin_15']*25000)+($data['ijin_k4']*40000)+
        ($data['ijin_l4']*75000)+($data['pulang_1']*40000)+($data['pulang_2']*75000)+($data['telat_1']*15000)+($data['telat_2']*25000)+($data['telat_3']*30000)+($data['telat_4']*40000)+$div_tk;
        if ($total_potongan_tk>75000){$total_potongan_tk=75000;}else {}

        // if ($data['pengerjaan_weekly']['tunjangan_kehadiran'] == 75000) {
        //     $data['total_potongan_tk']=0;
        // }
        // // elseif($data['pengerjaan_weekly']['tunjangan_kehadiran'] < 75000){
        // //     $data['total_potongan_tk']=$data['pengerjaan_weekly']['tunjangan_kehadiran'];
        // // }
        // else{
        //     $data['total_potongan_tk']=$total_potongan_tk; 
        // }

        $data['total_potongan_tk']=$total_potongan_tk;
        return view('backend.pengerjaan.packing_ekspor_gagang.input_hasil_kerja_karyawan',$data);
    }

    public function hasil_kerja_karyawan_gagang_ekspor_view_simpan(Request $request, $id, $kode_pengerjaan, $nik)
    {
        // $input = $request->all();
        if($id == 1){
            $kode_jenis_operator_detail = 'L';
        }
        elseif($id == 2){
            $kode_jenis_operator_detail = 'E';
        }
        elseif($id == 3){
            $kode_jenis_operator_detail = 'A';
        }

        $data['id'] = $id;
        $data['kode_pengerjaan'] = $kode_pengerjaan;
        $data['nik'] = $nik;

        $input['upah_dasar'] = $request->upah_dasar;
        $input['upah_makan'] = $request->upah_makan;
        
        $input['uang_makan'] = $request->uang_makan;
        $input['plus_1'] = $request->plus_1."|".$request->keterangan_plus_1;
        $input['plus_2'] = $request->plus_2."|".$request->keterangan_plus_2;
        $input['plus_3'] = $request->plus_3."|".$request->keterangan_plus_3;
        
        $input['minus_1'] = $request->minus_1."|".$request->keterangan_minus_1;
        $input['minus_2'] = $request->minus_2."|".$request->keterangan_minus_2;

        if ($request->akhir_bulan == 'n') {
            $input['tunjangan_kerja'] = 0;
            $input['tunjangan_kehadiran'] = 0;
        }else{
            $input['tunjangan_kerja'] = $request->tunjangan_kerja;
            $input['tunjangan_kehadiran'] = 75000-$request->pot_tunjangan_kehadiran;
        }

        if ($request->check_jht == 'on') {
            $input['jht'] = $request->jht;
        }else{
            $input['jht'] = 0;
        }

        if($request->check_bpjs_kesehatan == 'on'){
            $input['bpjs_kesehatan'] = $request->bpjs_kesehatan;
        }else{
            $input['bpjs_kesehatan'] = 0;
        }
        // dd($input);
        $operator_karyawan = $this->karyawanOperator->select('id')->where('nik',$nik)->where('status','Y')->where('jenis_operator_detail_pekerjaan_id',7)->orderBy('id','desc')->first();
        $pengerjaan_weekly = $this->pengerjaanWeekly->where('kode_payrol',substr($kode_pengerjaan,0,2).$kode_jenis_operator_detail.'_'.substr($kode_pengerjaan,3))
                                            ->where('operator_karyawan_id',$operator_karyawan->id)
                                            ->first();
        // $pengerjaan_weekly->update($input);
        // dd($pengerjaan_weekly);
        if (!empty($pengerjaan_weekly)) {
            $pengerjaan_weekly->update($input);
            return response()->json([
                'success' => true,
                'message_title' => 'Berhasil',
                'message_content' => 'Data Berhasil Disimpan'
            ]);
        }else{
            return response()->json([
                'success' => false,
                'message_title' => 'Gagal',
                'message_content' => 'Data Tidak Berhasil Disimpan'
            ]);
        }
    }

    public function hasil_kerja_ambri_isiEtiket($id, $kode_pengerjaan)
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

        $data['id'] = $id;
        $data['kode_pengerjaan'] = $kode_pengerjaan;
        $data['jenis_operator_detail_pekerjaan'] = $this->jenisOperatorDetailPengerjaan->find(8);
        $data['new_data_pengerjaan'] = $this->newDataPengerjaan->where('kode_pengerjaan',$kode_pengerjaan)->first();
        $data['explode_tanggal_pengerjaans'] = explode('#',$data['new_data_pengerjaan']['tanggal']);
        $data['count_tanggal'] = count($data['explode_tanggal_pengerjaans'])-1;
        // $data['packing_ekspors'] = KaryawanOperator::select([
        //                             'operator_karyawan.id as id',
        //                             'operator_karyawan.nik as nik',
        //                             'biodata_karyawan.nama as nama',
        //                             ])
        //                             ->where('jenis_operator_id',1)
        //                             ->where('jenis_operator_detail_id',2)
        //                             ->where('jenis_operator_detail_pekerjaan_id',5)
        //                             ->leftJoin('biodata_karyawan','biodata_karyawan.nik','=','operator_karyawan.nik')
        //                             ->rightJoin('pengerjaan','pengerjaan.operator_karyawan_id','=','operator_karyawan.id')
        //                             ->orderBy('biodata_karyawan.nama','asc')
        //                             ->get();

        $data['pengerjaans'] = $this->pengerjaanWeekly->select([
                                                    'pengerjaan_weekly.kode_payrol as kode_payrol',
                                                    'pengerjaan_weekly.operator_karyawan_id as operator_karyawan_id',
                                                    'operator_karyawan.nik as nik',
                                                    'biodata_karyawan.nama as nama',
                                                    'pengerjaan_weekly.upah_dasar as upah_dasar',
                                                    'pengerjaan_weekly.tunjangan_kerja as tunjangan_kerja',
                                                    'pengerjaan_weekly.tunjangan_kehadiran as tunjangan_kehadiran',
                                                    'pengerjaan_weekly.uang_makan as uang_makan',
                                                    'pengerjaan_weekly.plus_1 as plus_1',
                                                    'pengerjaan_weekly.plus_2 as plus_2',
                                                    'pengerjaan_weekly.plus_3 as plus_3',
                                                    'pengerjaan_weekly.minus_1 as minus_1',
                                                    'pengerjaan_weekly.minus_2 as minus_2',
                                                    'pengerjaan_weekly.jht as jht',
                                                    'pengerjaan_weekly.bpjs_kesehatan as bpjs_kesehatan',
                                                ])
                                                ->where('kode_payrol',substr($kode_pengerjaan,0,2).$kode_jenis_operator_detail.'_'.substr($kode_pengerjaan,3))
                                                ->where('operator_karyawan.jenis_operator_id',1)
                                                ->where('operator_karyawan.jenis_operator_detail_id',3)
                                                ->where('operator_karyawan.jenis_operator_detail_pekerjaan_id',8)
                                                ->where('operator_karyawan.status','Y')
                                                ->leftJoin('operator_karyawan','operator_karyawan.id','=','pengerjaan_weekly.operator_karyawan_id')
                                                ->leftJoin('itic_emp_new.biodata_karyawan','biodata_karyawan.nik','=','operator_karyawan.nik')
                                                ->orderBy('biodata_karyawan.nama','asc')
                                                ->get();

        return view('backend.pengerjaan.packing_ambri_isi_etiket.packing_ambri_isiEtiket',$data);
    }

    public function hasil_kerja_ambri_isiEtiket_view_hasil($id, $kode_pengerjaan, $tanggal)
    {
        $data['id'] = $id;
        $data['kode_pengerjaan'] = $kode_pengerjaan;
        $data['tanggal'] = $tanggal;
        $data['packing_ambris'] = $this->karyawanOperator->select([
                                                    'operator_karyawan.id as id',
                                                    'operator_karyawan.nik as nik',
                                                    'biodata_karyawan.nama as nama',
                                                    'pengerjaan.hasil_kerja_1 as hasil_kerja_1',
                                                    'pengerjaan.hasil_kerja_2 as hasil_kerja_2',
                                                    'pengerjaan.hasil_kerja_3 as hasil_kerja_3',
                                                    'pengerjaan.hasil_kerja_4 as hasil_kerja_4',
                                                    'pengerjaan.hasil_kerja_5 as hasil_kerja_5',
                                                    'pengerjaan.total_jam_kerja_1 as total_jam_kerja_1',
                                                    'pengerjaan.total_jam_kerja_2 as total_jam_kerja_2',
                                                    'pengerjaan.total_jam_kerja_3 as total_jam_kerja_3',
                                                    'pengerjaan.total_jam_kerja_4 as total_jam_kerja_4',
                                                    'pengerjaan.total_jam_kerja_5 as total_jam_kerja_5',
                                                    ])
                                                    ->leftJoin('itic_emp_new.biodata_karyawan','biodata_karyawan.nik','=','operator_karyawan.nik')
                                                    ->rightJoin('pengerjaan','pengerjaan.operator_karyawan_id','=','operator_karyawan.id')
                                                    ->where('jenis_operator_id',1)
                                                    ->where('jenis_operator_detail_id',3)
                                                    ->where('jenis_operator_detail_pekerjaan_id',8)
                                                    ->where('pengerjaan.tanggal_pengerjaan',$tanggal)
                                                    ->where('operator_karyawan.status','Y')
                                                    ->where('pengerjaan.kode_pengerjaan',$kode_pengerjaan)
                                                    // ->with('biodata_karyawan')
                                                    ->orderBy('biodata_karyawan.nama','asc')
                                                    ->get();

        $data['umk_borongan_ambris'] = $this->umkBoronganAmbri->select('id','jenis_produk','umk_etiket')->where('status','Y')->get();
        // dd($data);
        return view('backend.pengerjaan.packing_ambri_isi_etiket.input_hasil_kerja',$data);
    }

    public function hasil_kerja_ambri_isiEtiket_view_simpan(Request $request, $id, $kode_pengerjaan, $tanggal)
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

        $input = $request->all();
        $input['id'] = $id;
        $input['kode_pengerjaan'] = $kode_pengerjaan;
        $input['tanggal'] = $tanggal;

        $data['karyawan_pengerjaans'] = $this->pengerjaan->select([
                                                        'pengerjaan.id as id',
                                                        'operator_karyawan.nik as nik',
                                                        'pengerjaan.operator_karyawan_id as id_operator_karyawan'
                                                    ])
                                                    ->leftJoin('operator_karyawan','operator_karyawan.id','=','pengerjaan.operator_karyawan_id')
                                                    ->leftJoin('itic_emp_new.biodata_karyawan','biodata_karyawan.nik','=','operator_karyawan.nik')
                                                    ->where('tanggal_pengerjaan',$tanggal)
                                                    // ->where('kode_pengerjaan',$kode_pengerjaan)
                                                    // ->where('pengerjaan.operator_karyawan_id','operator_karyawan.id')
                                                    ->where('operator_karyawan.jenis_operator_id',1)
                                                    ->where('operator_karyawan.jenis_operator_detail_id',3)
                                                    ->where('operator_karyawan.jenis_operator_detail_pekerjaan_id',8)
                                                    ->where('operator_karyawan.status','Y')
                                                    ->where('pengerjaan.kode_pengerjaan',$kode_pengerjaan)
                                                    ->orderBy('biodata_karyawan.nama','asc')
                                                    ->get();
        
        foreach ($data['karyawan_pengerjaans'] as $key => $karyawan_pengerjaan) {
            // dd($request->umk_borongan_lokal_kerja_1);
            if ($request->umk_borongan_lokal_kerja_1) {
                $umk_borongan_lokal_1 = $this->umkBoronganAmbri->select('id','jenis_produk','umk_etiket')
                                                        ->where('id',$request->umk_borongan_lokal_kerja_1)
                                                        ->where('status','Y')
                                                        ->first();

                // $hasil_pengerjaan_1 = $umk_borongan_lokal_1->id.'|'.$umk_borongan_lokal_1->umk_packing*$request->hasil_kerja_1[$key];
                
                // if ($request->hasil_kerja_1[$key]) {
                // }

                if ($request->hasil_kerja_1[$key]) {
                    $hasil_pengerjaan_1 = $umk_borongan_lokal_1->id.'|'.$request->hasil_kerja_1[$key];
                    $hasil_kerja_1 = $umk_borongan_lokal_1->umk_etiket*$request->hasil_kerja_1[$key];
                }else{
                    $hasil_pengerjaan_1 = $umk_borongan_lokal_1->id.'|'.'0';
                    $hasil_kerja_1 = 0;
                }

                if($request->lembur_kerja_1){
                    $lembur_1 = '1-y';
                    $hasil_lembur_1 = '1.5';
                    // $total_upah_lembur_1 = ($hasil_kerja_1*1.5)-$hasil_kerja_1;
                    $total_upah_lembur_1 = $hasil_kerja_1*1.5;
                }else{
                    $lembur_1 = '1-n';
                    $hasil_lembur_1 = '1';
                    $total_upah_lembur_1 = $hasil_kerja_1*0;
                }

                if($request->total_jam_1[$key]){
                    $total_jam_1 = $request->total_jam_1[$key];
                }else{
                    $total_jam_1 = '0';
                }
                
            }else{
                $hasil_pengerjaan_1 = '0'.'|'.'0';
                $lembur_1 = '1-n';
                $hasil_lembur_1 = '1';
                $total_jam_1 = '0';
                $hasil_kerja_1 = 0;
                $total_upah_lembur_1 = '0';
            }

            $total_hasil_lembur_1 = $total_upah_lembur_1;

            // dd($hasil_pengerjaan_1);
            
            if ($request->umk_borongan_lokal_kerja_2) {
                $umk_borongan_lokal_2 = $this->umkBoronganAmbri->select('id','jenis_produk','umk_etiket')
                                                        ->where('id',$request->umk_borongan_lokal_kerja_2)
                                                        ->where('status','Y')
                                                        ->first();

                // $hasil_pengerjaan_2 = $umk_borongan_lokal_2->id.'|'.$umk_borongan_lokal_2->umk_packing*$request->hasil_kerja_2[$key];
                
                if ($request->hasil_kerja_2[$key]) {
                    $hasil_pengerjaan_2 = $umk_borongan_lokal_2->id.'|'.$request->hasil_kerja_2[$key];
                    $hasil_kerja_2 = $umk_borongan_lokal_2->umk_etiket*$request->hasil_kerja_2[$key];
                }else{
                    $hasil_pengerjaan_2 = $umk_borongan_lokal_2->id.'|'.'0';
                    $hasil_kerja_2 = 0;
                }

                if($request->lembur_kerja_2){
                    $lembur_2 = '2-y';
                    $hasil_lembur_2 = '1.5';
                    // $total_upah_lembur_2 = ($hasil_kerja_2*1.5)-$hasil_kerja_2;
                    $total_upah_lembur_2 = $hasil_kerja_2*1.5;
                }else{
                    $lembur_2 = '2-n';
                    $hasil_lembur_2 = '1';
                    $total_upah_lembur_2 = $hasil_kerja_2*0;
                }

                if($request->total_jam_2[$key]){
                    $total_jam_2 = $request->total_jam_2[$key];
                }else{
                    $total_jam_2 = '0';
                }

            }else{
                $hasil_pengerjaan_2 = '0'.'|'.'0';
                $lembur_2 = '2-n';
                $hasil_lembur_2 = '1';
                $total_jam_2 = '0';
                $hasil_kerja_2 = 0;
                $total_upah_lembur_2 = '0';
            }

            $total_hasil_lembur_2 = $total_upah_lembur_2;

            if ($request->umk_borongan_lokal_kerja_3) {
                $umk_borongan_lokal_3 = $this->umkBoronganAmbri->select('id','jenis_produk','umk_etiket')
                                                        ->where('id',$request->umk_borongan_lokal_kerja_3)
                                                        ->where('status','Y')
                                                        ->first();

                // $hasil_pengerjaan_3 = $umk_borongan_lokal_3->id.'|'.$umk_borongan_lokal_3->umk_packing*$request->hasil_kerja_3[$key];
                
                if ($request->hasil_kerja_3[$key]) {
                    $hasil_pengerjaan_3 = $umk_borongan_lokal_3->id.'|'.$request->hasil_kerja_3[$key];
                    $hasil_kerja_3 = $umk_borongan_lokal_3->umk_etiket*$request->hasil_kerja_3[$key];
                }else{
                    $hasil_pengerjaan_3 = $umk_borongan_lokal_3->id.'|'.'0';
                    $hasil_kerja_3 = 0;
                }
            
                if($request->lembur_kerja_3){
                    $lembur_3 = '3-y';
                    $hasil_lembur_3 = '1.5';
                    // $total_upah_lembur_3 = ($hasil_kerja_3*1.5)-$hasil_kerja_3;
                    $total_upah_lembur_3 = $hasil_kerja_3*1.5;
                }else{
                    $lembur_3 = '3-n';
                    $hasil_lembur_3 = '1';
                    $total_upah_lembur_3 = $hasil_kerja_3*0;
                }

                if($request->total_jam_3[$key]){
                    $total_jam_3 = $request->total_jam_3[$key];
                }else{
                    $total_jam_3 = '0';
                }

            }else{
                $hasil_pengerjaan_3 = '0'.'|'.'0';
                $lembur_3 = '3-n';
                $hasil_lembur_3 = '1';
                $total_jam_3 = '0';
                $hasil_kerja_3 = 0;
                $total_upah_lembur_3 = '0';
            }

            $total_hasil_lembur_3 = $total_upah_lembur_3;

            if ($request->umk_borongan_lokal_kerja_4) {
                $umk_borongan_lokal_4 = $this->umkBoronganAmbri->select('id','jenis_produk','umk_etiket')
                                                        ->where('id',$request->umk_borongan_lokal_kerja_4)
                                                        ->where('status','Y')
                                                        ->first();

                // $hasil_pengerjaan_4 = $umk_borongan_lokal_4->id.'|'.$umk_borongan_lokal_4->umk_packing*$request->hasil_kerja_4[$key];
                
                if ($request->hasil_kerja_4[$key]) {
                    $hasil_pengerjaan_4 = $umk_borongan_lokal_4->id.'|'.$request->hasil_kerja_4[$key];
                    $hasil_kerja_4 = $umk_borongan_lokal_4->umk_etiket*$request->hasil_kerja_4[$key];
                }else{
                    $hasil_pengerjaan_4 = $umk_borongan_lokal_4->id.'|'.'0';
                    $hasil_kerja_4 = 0;
                }
            
                if($request->lembur_kerja_4){
                    $lembur_4 = '4-y';
                    $hasil_lembur_4 = '1.5';
                    // $total_upah_lembur_4 = ($hasil_kerja_4*1.5)-$hasil_kerja_4;
                    $total_upah_lembur_4 = $hasil_kerja_4*1.5;
                }else{
                    $lembur_4 = '4-n';
                    $hasil_lembur_4 = '1';
                    $total_upah_lembur_4 = $hasil_kerja_4*0;
                }

                if($request->total_jam_4[$key]){
                    $total_jam_4 = $request->total_jam_4[$key];
                }else{
                    $total_jam_4 = '0';
                }

            }else{
                $hasil_pengerjaan_4 = '0'.'|'.'0';
                $lembur_4 = '4-n';
                $hasil_lembur_4 = '1';
                $total_jam_4 = '0';
                $hasil_kerja_4 = 0;
                $total_upah_lembur_4 = '0';
            }

            $total_hasil_lembur_4 = $total_upah_lembur_4;

            if ($request->umk_borongan_lokal_kerja_5) {
                $umk_borongan_lokal_5 = $this->umkBoronganAmbri->select('id','jenis_produk','umk_etiket')
                                                        ->where('id',$request->umk_borongan_lokal_kerja_5)
                                                        ->where('status','Y')
                                                        ->first();

                // $hasil_pengerjaan_5 = $umk_borongan_lokal_5->id.'|'.$umk_borongan_lokal_5->umk_packing*$request->hasil_kerja_5[$key];
                
                if ($request->hasil_kerja_5[$key]) {
                    $hasil_pengerjaan_5 = $umk_borongan_lokal_5->id.'|'.$request->hasil_kerja_5[$key];
                    $hasil_kerja_5 = $umk_borongan_lokal_5->umk_etiket*$request->hasil_kerja_5[$key];
                }else{
                    $hasil_pengerjaan_5 = $umk_borongan_lokal_5->id.'|'.'0';
                    $hasil_kerja_5 = 0;
                }
            
                if($request->lembur_kerja_5){
                    $lembur_5 = '5-y';
                    $hasil_lembur_5 = '1.5';
                    // $total_upah_lembur_5 = ($hasil_kerja_5*1.5)-$hasil_kerja_5;
                    $total_upah_lembur_5 = $hasil_kerja_5*1.5;
                }else{
                    $lembur_5 = '5-n';
                    $hasil_lembur_5 = '1';
                    $total_upah_lembur_5 = $hasil_kerja_5*0;
                }

                if($request->total_jam_5[$key]){
                    $total_jam_5 = $request->total_jam_5[$key];
                }else{
                    $total_jam_5 = '0';
                }

            }else{
                $hasil_pengerjaan_5 = '0'.'|'.'0';
                $lembur_5 = '5-n';
                $hasil_lembur_5 = '1';
                $total_jam_5 = '0';
                $hasil_kerja_5 = 0;
                $total_upah_lembur_5 = '0';
            }

            $total_hasil_lembur_5 = $total_upah_lembur_5;

            $lemburs = '|'.$lembur_1.'|'.$lembur_2.'|'.$lembur_3.'|'.$lembur_4.'|'.$lembur_5;
            $hasil_upah_dasar = $hasil_kerja_1+$hasil_kerja_2+$hasil_kerja_3+$hasil_kerja_4+$hasil_kerja_5;
            
            $penjumlahan_lembur = round($total_hasil_lembur_1+$total_hasil_lembur_2+$total_hasil_lembur_3+$total_hasil_lembur_4+$total_hasil_lembur_5);
            // dd($hasil_upah_dasar);
            $karyawan_pengerjaan->update([
                'hasil_kerja_1' => $hasil_pengerjaan_1,
                'hasil_kerja_2' => $hasil_pengerjaan_2,
                'hasil_kerja_3' => $hasil_pengerjaan_3,
                'hasil_kerja_4' => $hasil_pengerjaan_4,
                'hasil_kerja_5' => $hasil_pengerjaan_5,
                'uang_lembur' => $penjumlahan_lembur,
                'lembur' => $lemburs,
                'total_jam_kerja_1' => $total_jam_1,
                'total_jam_kerja_2' => $total_jam_2,
                'total_jam_kerja_3' => $total_jam_3,
                'total_jam_kerja_4' => $total_jam_4,
                'total_jam_kerja_5' => $total_jam_5,
            ]);
        }

        return response()->json([
            'success' => true,
            'message_title' => 'Berhasil',
            'message_content' => 'Data Berhasil Disimpan'
        ]);
    }

    public function hasil_kerja_karyawan_ambri_isiEtiket_view($id, $kode_pengerjaan, $nik, $month, $year)
    {
        $data['id'] = $id;
        $data['nik'] = $nik;
        $data['kode_pengerjaan'] = $kode_pengerjaan;
        $data['month'] = $month;
        $data['year'] = $year;
        // dd($data);

        if($id == 1){
            $kode_jenis_operator_detail = 'L';
        }
        elseif($id == 2){
            $kode_jenis_operator_detail = 'E';
        }
        elseif($id == 3){
            $kode_jenis_operator_detail = 'A';
        }
        $data['karyawan'] = $this->karyawanOperator->select([
                                                    'operator_karyawan.id as id',
                                                    'biodata_karyawan.nama as nama',
                                                    'operator_karyawan.nik as nik',
                                                    'biodata_karyawan.pin as pin',
                                                    'biodata_karyawan.tanggal_masuk as tanggal',
                                                    'operator_karyawan.tunjangan_kerja_id as tunjangan_kerja_id'
                                                    ])
                                                    ->leftJoin('itic_emp_new.biodata_karyawan','biodata_karyawan.nik','=','operator_karyawan.nik')
                                                    ->where('operator_karyawan.nik',$nik)
                                                    ->where('operator_karyawan.status','Y')
                                                    ->where('operator_karyawan.jenis_operator_detail_pekerjaan_id',8)
                                                    ->first();

        $data['new_data_pengerjaan'] = $this->newDataPengerjaan->select('akhir_bulan')->where('kode_pengerjaan',$kode_pengerjaan)->first();
        $data['jhts'] = $this->bpjsJht->where('status','y')->get();
        $data['bpjs_kesehatan'] = $this->bpjsKesehatan->select('nominal')->where('status','y')->first();
        // dd($data['new_data_pengerjaan']);

        $data['pengerjaans'] = $this->pengerjaan->where('operator_karyawan_id',$data['karyawan']['id'])->where('kode_pengerjaan',$kode_pengerjaan)->get();
        
        $data['upah'] = array();

        foreach ($data['pengerjaans'] as $key => $pengerjaan) {
            $explode_hasil_kerja_1 = explode("|",$pengerjaan->hasil_kerja_1);
            $umk_borongan_lokal_1 = $this->umkBoronganAmbri->select('id','jenis_produk','umk_etiket','umk_las_tepi','umk_las_pojok','umk_ambri')->where('id',$explode_hasil_kerja_1[0])->first();
            if(empty($umk_borongan_lokal_1)){
                $jenis_produk_1 = '-';
                $hasil_kerja_1 = null;
                $data_explode_hasil_kerja_1 = '-';
                $lembur_1 = 1;
            }else{
                $jenis_produk_1 = $umk_borongan_lokal_1->jenis_produk;
                $hasil_kerja_1 = $explode_hasil_kerja_1[1]*$umk_borongan_lokal_1->umk_etiket;
                $data_explode_hasil_kerja_1 = $explode_hasil_kerja_1[1];

                $explode_lembur_1 = explode("|",$pengerjaan->lembur);
                $explode_status_lembur_1 = explode("-",$explode_lembur_1[1]);

                if($explode_status_lembur_1[1] == 'y'){
                    $lembur_1 = 1.5;
                }else{
                    $lembur_1 = 1;
                }
            }

            $explode_hasil_kerja_2 = explode("|",$pengerjaan->hasil_kerja_2);
            $umk_borongan_lokal_2 = $this->umkBoronganAmbri->select('id','jenis_produk','umk_etiket','umk_las_tepi','umk_las_pojok','umk_ambri')->where('id',$explode_hasil_kerja_2[0])->first();
            if(empty($umk_borongan_lokal_2)){
                $jenis_produk_2 = '-';
                $hasil_kerja_2 = null;
                $data_explode_hasil_kerja_2 = '-';
                $lembur_2 = 1;
            }else{
                $jenis_produk_2 = $umk_borongan_lokal_2->jenis_produk;
                $hasil_kerja_2 = $explode_hasil_kerja_2[1]*$umk_borongan_lokal_2->umk_etiket;
                $data_explode_hasil_kerja_2 = $explode_hasil_kerja_2[1];

                $explode_lembur_2 = explode("|",$pengerjaan->lembur);
                $explode_status_lembur_2 = explode("-",$explode_lembur_2[2]);

                if($explode_status_lembur_2[1] == 'y'){
                    $lembur_2 = 1.5;
                }else{
                    $lembur_2 = 1;
                }
            }

            $explode_hasil_kerja_3 = explode("|",$pengerjaan->hasil_kerja_3);
            $umk_borongan_lokal_3 = $this->umkBoronganAmbri->select('id','jenis_produk','umk_etiket','umk_las_tepi','umk_las_pojok','umk_ambri')->where('id',$explode_hasil_kerja_3[0])->first();
            if(empty($umk_borongan_lokal_3)){
                $jenis_produk_3 = '-';
                $hasil_kerja_3 = null;
                $data_explode_hasil_kerja_3 = '-';
                $lembur_3 = 1;
            }else{
                $jenis_produk_3 = $umk_borongan_lokal_3->jenis_produk;
                $hasil_kerja_3 = $explode_hasil_kerja_3[1]*$umk_borongan_lokal_3->umk_etiket;
                $data_explode_hasil_kerja_3 = $explode_hasil_kerja_3[1];

                $explode_lembur_3 = explode("|",$pengerjaan->lembur);
                $explode_status_lembur_3 = explode("-",$explode_lembur_3[3]);

                if($explode_status_lembur_3[1] == 'y'){
                    $lembur_3 = 1.5;
                }else{
                    $lembur_3 = 1;
                }
            }

            $explode_hasil_kerja_4 = explode("|",$pengerjaan->hasil_kerja_4);
            $umk_borongan_lokal_4 = $this->umkBoronganAmbri->select('id','jenis_produk','umk_etiket','umk_las_tepi','umk_las_pojok','umk_ambri')->where('id',$explode_hasil_kerja_4[0])->first();
            if(empty($umk_borongan_lokal_4)){
                $jenis_produk_4 = '-';
                $hasil_kerja_4 = null;
                $data_explode_hasil_kerja_4 = '-';
                $lembur_4 = 1;
            }else{
                $jenis_produk_4 = $umk_borongan_lokal_4->jenis_produk;
                $hasil_kerja_4 = $explode_hasil_kerja_4[1]*$umk_borongan_lokal_4->umk_etiket;
                $data_explode_hasil_kerja_4 = $explode_hasil_kerja_4[1];

                $explode_lembur_4 = explode("|",$pengerjaan->lembur);
                $explode_status_lembur_4 = explode("-",$explode_lembur_4[4]);

                if($explode_status_lembur_4[1] == 'y'){
                    $lembur_4 = 1.5;
                }else{
                    $lembur_4 = 1;
                }
            }

            $explode_hasil_kerja_5 = explode("|",$pengerjaan->hasil_kerja_5);
            $umk_borongan_lokal_5 = $this->umkBoronganAmbri->select('id','jenis_produk','umk_etiket','umk_las_tepi','umk_las_pojok','umk_ambri')->where('id',$explode_hasil_kerja_5[0])->first();
            if(empty($umk_borongan_lokal_5)){
                $jenis_produk_5 = '-';
                $hasil_kerja_5 = null;
                $data_explode_hasil_kerja_5 = '-';
                $lembur_5 = 1;
            }else{
                $jenis_produk_5 = $umk_borongan_lokal_5->jenis_produk;
                $hasil_kerja_5 = $explode_hasil_kerja_5[1]*$umk_borongan_lokal_5->umk_etiket;
                $data_explode_hasil_kerja_5 = $explode_hasil_kerja_5[1];

                $explode_lembur_5 = explode("|",$pengerjaan->lembur);
                $explode_status_lembur_5 = explode("-",$explode_lembur_5[5]);

                if($explode_status_lembur_5[1] == 'y'){
                    $lembur_5 = 1.5;
                }else{
                    $lembur_5 = 1;
                }
            }

            $hasil_upah = ($hasil_kerja_1*$lembur_1)+($hasil_kerja_2*$lembur_2)+($hasil_kerja_3*$lembur_3)+($hasil_kerja_4*$lembur_4)+($hasil_kerja_5*$lembur_5);

            array_push($data['upah'],$hasil_upah);
        }

        //Hitung Masa Kerja
        // $data['karyawan_masa_kerja'] = DB::connection('emp')->table('log_posisi')
        //                                                     ->select('tanggal')
        //                                                     ->where('nik',$nik)
        //                                                     ->first();
        $awal  = new DateTime($data['karyawan']->tanggal);
        $akhir = new DateTime(); // Waktu sekarang
        $diff  = $awal->diff($akhir);
        $data['masa_kerja'] = $diff->y.' Tahun '.$diff->m.' Bulan '.$diff->d.' Hari';
        $data['masa_kerja_tahun'] = $diff->y;
        $data['masa_kerja_hari'] = $diff->d;
        //End Hitung Masa Kerja

        $data['upah_dasar_karyawan'] = [];
        foreach ($data['jhts'] as $key => $jht) {
            if ($data['masa_kerja_tahun'] > 15) {
                if ($jht->urutan == 3) {
                    $data['upah_dasar_karyawan'] = ($data['bpjs_kesehatan']['nominal'] + 100000)/25;
                }
            }
            elseif($data['masa_kerja_tahun'] >= 10 && $data['masa_kerja_tahun'] <= 15 && $data['masa_kerja_hari'] >= 1){
                if ($jht->urutan == 2) {
                    $data['upah_dasar_karyawan'] = ($data['bpjs_kesehatan']['nominal'] + 50000)/25;
                }
            }
            elseif($data['masa_kerja_tahun'] <= 10 || $data['masa_kerja_hari'] >= 1){
                if ($jht->urutan == 1) {
                    $data['upah_dasar_karyawan'] = ($data['bpjs_kesehatan']['nominal'] + 0)/25;
                }
            }
        }

        // echo 'Selisih waktu: ';
        // echo $diff->y . ' tahun, ';
        // echo $diff->m . ' bulan, ';
        // echo $diff->d . ' hari, ';
        // echo $diff->h . ' jam, ';
        // echo $diff->i . ' menit, ';
        // echo $diff->s . ' detik, ';
        // dd($data);

        $data['pengerjaan_weekly'] = $this->pengerjaanWeekly->where('operator_karyawan_id',$data['karyawan']['id'])
                                                    ->where('kode_payrol',substr($kode_pengerjaan,0,2).$kode_jenis_operator_detail.'_'.substr($kode_pengerjaan,3))
                                                    ->first();
        // dd($data['pengerjaan_weekly']);
        // $data['presensi_info'] = PresensiInfo::all();
        // dd($data['presensi_info']);
        $tgl_current_active = Carbon::now()->format('d');
        $bln_active = Carbon::now()->format('m');
        $bln_current_active = $month;
        $thn_current_active = $year;

        // $bulan_kemarin = Carbon::now()->subMonth()->format('Y-m').'-26';
        // $bulan_sekarang = Carbon::now()->format('Y-m').'-25';

        if($month == 1 && $tgl_current_active <10){
            $thn_prev_active = $year-1;
            $bulan_kemarin="$thn_prev_active-11-26";
            $bulan_sekarang="$thn_prev_active-12-26";
        }elseif($month == 1 && $tgl_current_active>10 or $month == 2 && $tgl_current_active<10 && $bln_active != 3){
            $thn_prev_active = $year-1;
            $bulan_kemarin="$thn_prev_active-12-26";
            $bulan_sekarang="$thn_current_active-01-26";
        }else{
            $bln_prev_active = $month-1; 
            $thn_prev_active = $year;
            $bulan_kemarin="$thn_prev_active-$bln_prev_active-26";
            $bulan_sekarang="$thn_current_active-$bln_current_active-26";
        }

        $data['tunjangan_kerjas'] = $this->tunjanganKerja->select('nominal')->where('id',$data['karyawan']['tunjangan_kerja_id'])->first();
        if(empty($data['tunjangan_kerjas'])){
            $data['tunjangan_kerja'] = 0;
        }else{
            $data['tunjangan_kerja'] = $data['tunjangan_kerjas']['nominal'];
        }
        //jumlah alpa
        $data['alpa'] = $this->presensiInfo->where('pin',$data['karyawan']['pin'])
                                    ->whereBetween('scan_date',[$bulan_kemarin,$bulan_sekarang])
                                    ->where('status',7)
                                    ->get();
        //jumlah diliburkan
        $data['diliburkan'] = $this->presensiInfo->where('pin',$data['karyawan']['pin'])
                                    ->whereBetween('scan_date',[$bulan_kemarin,$bulan_sekarang])
                                    ->where('status',14)
                                    ->get();
        //jumlah cuti
        $data['cuti'] = $this->presensiInfo->where('pin',$data['karyawan']['pin'])
                                    ->whereBetween('scan_date',[$bulan_kemarin,$bulan_sekarang])
                                    ->where('status',13)
                                    ->get();
        //jumlah sakit
        $data['sakit'] = $this->presensiInfo->where('pin',$data['karyawan']['pin'])
                                    ->whereBetween('scan_date',[$bulan_kemarin,$bulan_sekarang])
                                    ->where('status',4)
                                    ->get();
        // dd($data['sakit']);
        //jumlah ijin full
        $data['ijin_full'] = $this->presensiInfo->where('pin',$data['karyawan']['pin'])
                                    ->whereBetween('scan_date',[$bulan_kemarin,$bulan_sekarang])
                                    ->where('status',6)
                                    ->get();
        //jumlah status terlambat
        $data['terlambats'] = $this->presensiInfo->where('pin',$data['karyawan']['pin'])
                                    ->whereBetween('scan_date',[$bulan_kemarin,$bulan_sekarang])
                                    ->where('status',3)
                                    ->get();
        $telat_1=0; //terlambat < 5 menit
        $telat_2=0; //terlambat > 5 menit < 15 menit
        $telat_3=0; //terlambat > 15 menit < 1 jam
        $telat_4=0; //terlambat > 1 jam < 3 jam	
        $telat_5=0; //terlambat > 3 jam	
        $data['telat_1'] = $telat_1;
        $data['telat_2'] = $telat_2;
        $data['telat_3'] = $telat_3;
        $data['telat_4'] = $telat_4;
        $data['telat_5'] = $telat_5;
        foreach ($data['terlambats'] as $key => $terlambat) {
            $explode_keterangan_terlambat = explode("@",$terlambat->keterangan);
            $jam_keterangan_terlambat=strtotime($explode_keterangan_terlambat[1]);
            $jam_datang_terlambat=strtotime(Carbon::parse($terlambat['scan_date'])->format('H:i'));
            // $jam_datang_terlambat=strtotime($terlambat['jam_datang_telat']);
            $menit_telat=(($jam_datang_terlambat-$jam_keterangan_terlambat)/60);
            // dd($menit_telat);
            if($menit_telat <= 4){
                $telat_1=$telat_1+1;
                $data['telat_1'] = $telat_1;
            }
            else if(($menit_telat >= 5) && ($menit_telat <= 15) ){
                $telat_2=$telat_2+1;
                $data['telat_2'] = $telat_2;
            }
            else if(($menit_telat >= 16) && ($menit_telat <= 60)){
                $telat_3=$telat_3+1;
                $data['telat_3'] = $telat_3;
            }
            else if(($menit_telat >= 61) && ($menit_telat <= 179)){
                $telat_4=$telat_4+1;
                $data['telat_4'] = $telat_4;
            }else if($menit_telat > 179){
                $telat_5=$telat_5+1;
                $data['telat_5'] = $telat_5;
            }
            // else{
            //     $telat_4=$telat_4+1;
            //     $data['telat_4'] = $telat_4;
            // }
        }

        //jumlah status pulang awal
        $data['pulang_awals'] = $this->presensiInfo->where('pin',$data['karyawan']['pin'])
                                    ->whereBetween('scan_date',[$bulan_kemarin,$bulan_sekarang])
                                    ->where(function($query) {
                                        $query->where('status','=',9)
                                            ->orWhere('status','=',10);
                                    })
                                    // ->where('status',9)
                                    // ->where('status',10)
                                    ->get();
        // dd($data['pulang_awals']);
        $pulang_1=0; //pulang awal < 4 jam
		$pulang_2=0; //tpulang awal > 4 jam
        $data['pulang_1'] = $pulang_1;
        $data['pulang_2'] = $pulang_2;
        foreach ($data['pulang_awals'] as $key => $pulang_awal) {
            $explode_pulang_seharusnya=explode("@",$pulang_awal->keterangan);
			$jam_pulang_seharusnya=strtotime($explode_pulang_seharusnya[3]);
			$jam_pulang_awal=strtotime(Carbon::parse($pulang_awal['scan_date'])->format('H:i'));
			// $jam_pulang_awal=strtotime($pulang_awal->jam_pulang_awal);
			$menit_sisa_jam_kerja=(($jam_pulang_seharusnya-$jam_pulang_awal)/60);
			if ($menit_sisa_jam_kerja<=300){
                $pulang_1=$pulang_1+1;
                $data['pulang_1'] = $pulang_1;
            }
			else{
                $pulang_2=$pulang_2+1;
                $data['pulang_2'] = $pulang_2;
            }
        }

        //jumlah ijin saat jam kerja
        $data['keluar_masuks'] = $this->keluarMasuk->where('nik',$data['karyawan']['nik'])
                                    ->whereBetween('tanggal_ijin',[$bulan_kemarin,$bulan_sekarang])
                                    ->get();
        // dd($data['keluar_masuks']);
        $ijin_15=0; //ijin kurang dari 15 menit
		$ijin_k4=0; //ijin > 15menit < 4 jam
		$ijin_l4=0; //ijin > 4 jam
        $data['ijin_15']=$ijin_15;
        $data['ijin_k4']=$ijin_k4;
        $data['ijin_l4']=$ijin_l4;
        foreach ($data['keluar_masuks'] as $key => $keluar_masuk) {
            $jam_keluar=strtotime($keluar_masuk->jam_keluar);
			$jam_datang=strtotime($keluar_masuk->jam_datang);
			$menit_keluar_masuk=(($jam_datang-$jam_keluar)/60);
			if ($menit_keluar_masuk<=15){
                $ijin_15=$ijin_15+1;
                $data['ijin_15']=$ijin_15;
            }
			else if (($menit_keluar_masuk>15)&&($menit_keluar_masuk<=239)){
                $ijin_k4=$ijin_k4+1;
                $data['ijin_k4']=$ijin_k4;
            }
			else {
                $ijin_l4=$ijin_l4+1;
                $data['ijin_l4']=$ijin_l4;
            }
        }

        //lihat tanggal masuk
        // $data['log_posisi'] = $this->logPosisi->select('tanggal')->where('nik',$nik)->first();
		$selisih_tanggal_masuk=strtotime($bulan_kemarin)-strtotime($data['karyawan']['tanggal']);
		if ($selisih_tanggal_masuk>=0)$div_tk=0;else $div_tk=75000;

        $total_potongan_tk=(75000*$data['alpa']->count())+(75000*$data['diliburkan']->count())+(75000*$data['sakit']->count())+(75000*$data['cuti']->count())+(75000*$data['ijin_full']->count())+($data['ijin_15']*25000)+($data['ijin_k4']*40000)+
        ($data['ijin_l4']*75000)+($data['pulang_1']*40000)+($data['pulang_2']*75000)+($data['telat_1']*15000)+($data['telat_2']*25000)+($data['telat_3']*30000)+($data['telat_4']*40000)+$div_tk;
        if ($total_potongan_tk>75000){$total_potongan_tk=75000;}else {}

        // if ($data['pengerjaan_weekly']['tunjangan_kehadiran'] == 75000) {
        //     $data['total_potongan_tk']=0;
        // }
        // // elseif($data['pengerjaan_weekly']['tunjangan_kehadiran'] < 75000){
        // //     $data['total_potongan_tk']=$data['pengerjaan_weekly']['tunjangan_kehadiran'];
        // // }
        // else{
        //     $data['total_potongan_tk']=$total_potongan_tk; 
        // }

        $data['total_potongan_tk']=$total_potongan_tk;
        return view('backend.pengerjaan.packing_ambri_isi_etiket.input_hasil_kerja_karyawan',$data);
    }

    public function hasil_kerja_karyawan_ambri_isiEtiket_view_simpan(Request $request, $id, $kode_pengerjaan, $nik)
    {
        // $input = $request->all();
        if($id == 1){
            $kode_jenis_operator_detail = 'L';
        }
        elseif($id == 2){
            $kode_jenis_operator_detail = 'E';
        }
        elseif($id == 3){
            $kode_jenis_operator_detail = 'A';
        }

        $data['id'] = $id;
        $data['kode_pengerjaan'] = $kode_pengerjaan;
        $data['nik'] = $nik;

        $input['upah_dasar'] = $request->upah_dasar;
        $input['upah_makan'] = $request->upah_makan;
        
        $input['uang_makan'] = $request->uang_makan;
        $input['plus_1'] = $request->plus_1."|".$request->keterangan_plus_1;
        $input['plus_2'] = $request->plus_2."|".$request->keterangan_plus_2;
        $input['plus_3'] = $request->plus_3."|".$request->keterangan_plus_3;
        
        $input['minus_1'] = $request->minus_1."|".$request->keterangan_minus_1;
        $input['minus_2'] = $request->minus_2."|".$request->keterangan_minus_2;

        if ($request->akhir_bulan == 'n') {
            $input['tunjangan_kerja'] = 0;
            $input['tunjangan_kehadiran'] = 0;
        }else{
            $input['tunjangan_kerja'] = $request->tunjangan_kerja;
            $input['tunjangan_kehadiran'] = 75000-$request->pot_tunjangan_kehadiran;
        }

        if ($request->check_jht == 'on') {
            $input['jht'] = $request->jht;
        }else{
            $input['jht'] = 0;
        }

        if($request->check_bpjs_kesehatan == 'on'){
            $input['bpjs_kesehatan'] = $request->bpjs_kesehatan;
        }else{
            $input['bpjs_kesehatan'] = 0;
        }
        // dd($input);
        $operator_karyawan = $this->karyawanOperator->select('id')->where('nik',$nik)->where('status','Y')->where('jenis_operator_detail_pekerjaan_id',8)->orderBy('id','desc')->first();
        $pengerjaan_weekly = $this->pengerjaanWeekly->where('kode_payrol',substr($kode_pengerjaan,0,2).$kode_jenis_operator_detail.'_'.substr($kode_pengerjaan,3))
                                            ->where('operator_karyawan_id',$operator_karyawan->id)
                                            ->first();
        // $pengerjaan_weekly->update($input);
        if (!empty($pengerjaan_weekly)) {
            $pengerjaan_weekly->update($input);
            return response()->json([
                'success' => true,
                'message_title' => 'Berhasil',
                'message_content' => 'Data Berhasil Disimpan'
            ]);
        }else{
            return response()->json([
                'success' => false,
                'message_title' => 'Gagal',
                'message_content' => 'Data Tidak Berhasil Disimpan'
            ]);
        }
        // dd($pengerjaan_weekly);
    }

    public function hasil_kerja_ambri_las_tepi($id, $kode_pengerjaan)
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

        $data['id'] = $id;
        $data['kode_pengerjaan'] = $kode_pengerjaan;
        $data['jenis_operator_detail_pekerjaan'] = $this->jenisOperatorDetailPengerjaan->find(9);
        $data['new_data_pengerjaan'] = $this->newDataPengerjaan->where('kode_pengerjaan',$kode_pengerjaan)->first();
        $data['explode_tanggal_pengerjaans'] = explode('#',$data['new_data_pengerjaan']['tanggal']);
        $data['count_tanggal'] = count($data['explode_tanggal_pengerjaans'])-1;
        // $data['packing_ekspors'] = KaryawanOperator::select([
        //                             'operator_karyawan.id as id',
        //                             'operator_karyawan.nik as nik',
        //                             'biodata_karyawan.nama as nama',
        //                             ])
        //                             ->where('jenis_operator_id',1)
        //                             ->where('jenis_operator_detail_id',2)
        //                             ->where('jenis_operator_detail_pekerjaan_id',5)
        //                             ->leftJoin('biodata_karyawan','biodata_karyawan.nik','=','operator_karyawan.nik')
        //                             ->rightJoin('pengerjaan','pengerjaan.operator_karyawan_id','=','operator_karyawan.id')
        //                             ->orderBy('biodata_karyawan.nama','asc')
        //                             ->get();

        $data['pengerjaans'] = $this->pengerjaanWeekly->select([
                                                    'pengerjaan_weekly.kode_payrol as kode_payrol',
                                                    'pengerjaan_weekly.operator_karyawan_id as operator_karyawan_id',
                                                    'operator_karyawan.nik as nik',
                                                    'biodata_karyawan.nama as nama',
                                                    'pengerjaan_weekly.upah_dasar as upah_dasar',
                                                    'pengerjaan_weekly.tunjangan_kerja as tunjangan_kerja',
                                                    'pengerjaan_weekly.tunjangan_kehadiran as tunjangan_kehadiran',
                                                    'pengerjaan_weekly.uang_makan as uang_makan',
                                                    'pengerjaan_weekly.plus_1 as plus_1',
                                                    'pengerjaan_weekly.plus_2 as plus_2',
                                                    'pengerjaan_weekly.plus_3 as plus_3',
                                                    'pengerjaan_weekly.minus_1 as minus_1',
                                                    'pengerjaan_weekly.minus_2 as minus_2',
                                                    'pengerjaan_weekly.jht as jht',
                                                    'pengerjaan_weekly.bpjs_kesehatan as bpjs_kesehatan',
                                                    'pengerjaan_weekly.tunjangan_kerja as tunjangan_kerja',
                                                    'pengerjaan_weekly.tunjangan_kehadiran as tunjangan_kehadiran',
                                                ])
                                                ->where('kode_payrol',substr($kode_pengerjaan,0,2).$kode_jenis_operator_detail.'_'.substr($kode_pengerjaan,3))
                                                ->where('operator_karyawan.jenis_operator_id',1)
                                                ->where('operator_karyawan.jenis_operator_detail_id',3)
                                                ->where('operator_karyawan.jenis_operator_detail_pekerjaan_id',9)
                                                ->where('operator_karyawan.status','Y')
                                                ->leftJoin('operator_karyawan','operator_karyawan.id','=','pengerjaan_weekly.operator_karyawan_id')
                                                ->leftJoin('itic_emp_new.biodata_karyawan','biodata_karyawan.nik','=','operator_karyawan.nik')
                                                ->orderBy('biodata_karyawan.nama','asc')
                                                ->get();

        return view('backend.pengerjaan.packing_ambri_las_tepi.packing_las_tepi',$data);
    }

    public function hasil_kerja_ambri_las_tepi_view_hasil($id, $kode_pengerjaan, $tanggal)
    {
        $data['id'] = $id;
        $data['kode_pengerjaan'] = $kode_pengerjaan;
        $data['tanggal'] = $tanggal;
        $data['packing_ambris'] = $this->karyawanOperator->select([
                                                    'operator_karyawan.id as id',
                                                    'operator_karyawan.nik as nik',
                                                    'biodata_karyawan.nama as nama',
                                                    'pengerjaan.hasil_kerja_1 as hasil_kerja_1',
                                                    'pengerjaan.hasil_kerja_2 as hasil_kerja_2',
                                                    'pengerjaan.hasil_kerja_3 as hasil_kerja_3',
                                                    'pengerjaan.hasil_kerja_4 as hasil_kerja_4',
                                                    'pengerjaan.hasil_kerja_5 as hasil_kerja_5',
                                                    'pengerjaan.total_jam_kerja_1 as total_jam_kerja_1',
                                                    'pengerjaan.total_jam_kerja_2 as total_jam_kerja_2',
                                                    'pengerjaan.total_jam_kerja_3 as total_jam_kerja_3',
                                                    'pengerjaan.total_jam_kerja_4 as total_jam_kerja_4',
                                                    'pengerjaan.total_jam_kerja_5 as total_jam_kerja_5',
                                                    ])
                                                    ->leftJoin('itic_emp_new.biodata_karyawan','biodata_karyawan.nik','=','operator_karyawan.nik')
                                                    ->rightJoin('pengerjaan','pengerjaan.operator_karyawan_id','=','operator_karyawan.id')
                                                    ->where('jenis_operator_id',1)
                                                    ->where('jenis_operator_detail_id',3)
                                                    ->where('jenis_operator_detail_pekerjaan_id',9)
                                                    ->where('pengerjaan.tanggal_pengerjaan',$tanggal)
                                                    ->where('operator_karyawan.status','Y')
                                                    ->where('pengerjaan.kode_pengerjaan',$kode_pengerjaan)
                                                    // ->with('biodata_karyawan')
                                                    ->orderBy('biodata_karyawan.nama','asc')
                                                    ->get();

        $data['umk_borongan_ambris'] = $this->umkBoronganAmbri->select('id','jenis_produk','umk_las_tepi')->where('status','Y')->get();
        // dd($data);
        return view('backend.pengerjaan.packing_ambri_las_tepi.input_hasil_kerja',$data);
    }

    public function hasil_kerja_ambri_las_tepi_view_simpan(Request $request, $id, $kode_pengerjaan, $tanggal)
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

        $input = $request->all();
        $input['id'] = $id;
        $input['kode_pengerjaan'] = $kode_pengerjaan;
        $input['tanggal'] = $tanggal;

        $data['karyawan_pengerjaans'] = $this->pengerjaan->select([
                                                        'pengerjaan.id as id',
                                                        'operator_karyawan.nik as nik',
                                                        'pengerjaan.operator_karyawan_id as id_operator_karyawan'
                                                    ])
                                                    ->leftJoin('operator_karyawan','operator_karyawan.id','=','pengerjaan.operator_karyawan_id')
                                                    ->leftJoin('itic_emp_new.biodata_karyawan','biodata_karyawan.nik','=','operator_karyawan.nik')
                                                    ->where('tanggal_pengerjaan',$tanggal)
                                                    // ->where('kode_pengerjaan',$kode_pengerjaan)
                                                    // ->where('pengerjaan.operator_karyawan_id','operator_karyawan.id')
                                                    ->where('operator_karyawan.jenis_operator_id',1)
                                                    ->where('operator_karyawan.jenis_operator_detail_id',3)
                                                    ->where('operator_karyawan.jenis_operator_detail_pekerjaan_id',9)
                                                    ->where('operator_karyawan.status','Y')
                                                    ->where('pengerjaan.kode_pengerjaan',$kode_pengerjaan)
                                                    ->orderBy('biodata_karyawan.nama','asc')
                                                    ->get();
        
        foreach ($data['karyawan_pengerjaans'] as $key => $karyawan_pengerjaan) {
            // dd($request->umk_borongan_lokal_kerja_1);
            if ($request->umk_borongan_lokal_kerja_1) {
                $umk_borongan_lokal_1 = $this->umkBoronganAmbri->select('id','jenis_produk','umk_las_tepi')
                                                        ->where('id',$request->umk_borongan_lokal_kerja_1)
                                                        ->where('status','Y')
                                                        ->first();

                // $hasil_pengerjaan_1 = $umk_borongan_lokal_1->id.'|'.$umk_borongan_lokal_1->umk_packing*$request->hasil_kerja_1[$key];
                
                // if ($request->hasil_kerja_1[$key]) {
                // }

                if ($request->hasil_kerja_1[$key]) {
                    $hasil_pengerjaan_1 = $umk_borongan_lokal_1->id.'|'.$request->hasil_kerja_1[$key];
                    $hasil_kerja_1 = $umk_borongan_lokal_1->umk_las_tepi*$request->hasil_kerja_1[$key];
                }else{
                    $hasil_pengerjaan_1 = $umk_borongan_lokal_1->id.'|'.'0';
                    $hasil_kerja_1 = 0;
                }

                if($request->lembur_kerja_1){
                    $lembur_1 = '1-y';
                    $hasil_lembur_1 = '1.5';
                    // $total_upah_lembur_1 = ($hasil_kerja_1*1.5)-$hasil_kerja_1;
                    $total_upah_lembur_1 = $hasil_kerja_1*1.5;
                }else{
                    $lembur_1 = '1-n';
                    $hasil_lembur_1 = '1';
                    $total_upah_lembur_1 = $hasil_kerja_1*0;
                }

                if($request->total_jam_1[$key]){
                    $total_jam_1 = $request->total_jam_1[$key];
                }else{
                    $total_jam_1 = '0';
                }
                
            }else{
                $hasil_pengerjaan_1 = '0'.'|'.'0';
                $lembur_1 = '1-n';
                $hasil_lembur_1 = '1';
                $total_jam_1 = '0';
                $hasil_kerja_1 = 0;
                $total_upah_lembur_1 = '0';
            }

            $total_hasil_lembur_1 = $total_upah_lembur_1;

            // dd($hasil_pengerjaan_1);
            
            if ($request->umk_borongan_lokal_kerja_2) {
                $umk_borongan_lokal_2 = $this->umkBoronganAmbri->select('id','jenis_produk','umk_las_tepi')
                                                        ->where('id',$request->umk_borongan_lokal_kerja_2)
                                                        ->where('status','Y')
                                                        ->first();

                // $hasil_pengerjaan_2 = $umk_borongan_lokal_2->id.'|'.$umk_borongan_lokal_2->umk_packing*$request->hasil_kerja_2[$key];
                
                if ($request->hasil_kerja_2[$key]) {
                    $hasil_pengerjaan_2 = $umk_borongan_lokal_2->id.'|'.$request->hasil_kerja_2[$key];
                    $hasil_kerja_2 = $umk_borongan_lokal_2->umk_las_tepi*$request->hasil_kerja_2[$key];
                }else{
                    $hasil_pengerjaan_2 = $umk_borongan_lokal_2->id.'|'.'0';
                    $hasil_kerja_2 = 0;
                }

                if($request->lembur_kerja_2){
                    $lembur_2 = '2-y';
                    $hasil_lembur_2 = '1.5';
                    // $total_upah_lembur_2 = ($hasil_kerja_2*1.5)-$hasil_kerja_2;
                    $total_upah_lembur_2 = $hasil_kerja_2*1.5;
                }else{
                    $lembur_2 = '2-n';
                    $hasil_lembur_2 = '1';
                    $total_upah_lembur_2 = $hasil_kerja_2*0;
                }

                if($request->total_jam_2[$key]){
                    $total_jam_2 = $request->total_jam_2[$key];
                }else{
                    $total_jam_2 = '0';
                }

            }else{
                $hasil_pengerjaan_2 = '0'.'|'.'0';
                $lembur_2 = '2-n';
                $hasil_lembur_2 = '1';
                $total_jam_2 = '0';
                $hasil_kerja_2 = 0;
                $total_upah_lembur_2 = '0';
            }

            $total_hasil_lembur_2 = $total_upah_lembur_2;

            if ($request->umk_borongan_lokal_kerja_3) {
                $umk_borongan_lokal_3 = $this->umkBoronganAmbri->select('id','jenis_produk','umk_las_tepi')
                                                        ->where('id',$request->umk_borongan_lokal_kerja_3)
                                                        ->where('status','Y')
                                                        ->first();

                // $hasil_pengerjaan_3 = $umk_borongan_lokal_3->id.'|'.$umk_borongan_lokal_3->umk_packing*$request->hasil_kerja_3[$key];
                
                if ($request->hasil_kerja_3[$key]) {
                    $hasil_pengerjaan_3 = $umk_borongan_lokal_3->id.'|'.$request->hasil_kerja_3[$key];
                    $hasil_kerja_3 = $umk_borongan_lokal_3->umk_las_tepi*$request->hasil_kerja_3[$key];
                }else{
                    $hasil_pengerjaan_3 = $umk_borongan_lokal_3->id.'|'.'0';
                    $hasil_kerja_3 = 0;
                }
            
                if($request->lembur_kerja_3){
                    $lembur_3 = '3-y';
                    $hasil_lembur_3 = '1.5';
                    // $total_upah_lembur_3 = ($hasil_kerja_3*1.5)-$hasil_kerja_3;
                    $total_upah_lembur_3 = $hasil_kerja_3*1.5;
                }else{
                    $lembur_3 = '3-n';
                    $hasil_lembur_3 = '1';
                    $total_upah_lembur_3 = $hasil_kerja_3*0;
                }

                if($request->total_jam_3[$key]){
                    $total_jam_3 = $request->total_jam_3[$key];
                }else{
                    $total_jam_3 = '0';
                }

            }else{
                $hasil_pengerjaan_3 = '0'.'|'.'0';
                $lembur_3 = '3-n';
                $hasil_lembur_3 = '1';
                $total_jam_3 = '0';
                $hasil_kerja_3 = 0;
                $total_upah_lembur_3 = '0';
            }

            $total_hasil_lembur_3 = $total_upah_lembur_3;

            if ($request->umk_borongan_lokal_kerja_4) {
                $umk_borongan_lokal_4 = $this->umkBoronganAmbri->select('id','jenis_produk','umk_las_tepi')
                                                        ->where('id',$request->umk_borongan_lokal_kerja_4)
                                                        ->where('status','Y')
                                                        ->first();

                // $hasil_pengerjaan_4 = $umk_borongan_lokal_4->id.'|'.$umk_borongan_lokal_4->umk_packing*$request->hasil_kerja_4[$key];
                
                if ($request->hasil_kerja_4[$key]) {
                    $hasil_pengerjaan_4 = $umk_borongan_lokal_4->id.'|'.$request->hasil_kerja_4[$key];
                    $hasil_kerja_4 = $umk_borongan_lokal_4->umk_las_tepi*$request->hasil_kerja_4[$key];
                }else{
                    $hasil_pengerjaan_4 = $umk_borongan_lokal_4->id.'|'.'0';
                    $hasil_kerja_4 = 0;
                }
            
                if($request->lembur_kerja_4){
                    $lembur_4 = '4-y';
                    $hasil_lembur_4 = '1.5';
                    // $total_upah_lembur_4 = ($hasil_kerja_4*1.5)-$hasil_kerja_4;
                    $total_upah_lembur_4 = $hasil_kerja_4*1.5;
                }else{
                    $lembur_4 = '4-n';
                    $hasil_lembur_4 = '1';
                    $total_upah_lembur_4 = $hasil_kerja_4*0;
                }

                if($request->total_jam_4[$key]){
                    $total_jam_4 = $request->total_jam_4[$key];
                }else{
                    $total_jam_4 = '0';
                }

            }else{
                $hasil_pengerjaan_4 = '0'.'|'.'0';
                $lembur_4 = '4-n';
                $hasil_lembur_4 = '1';
                $total_jam_4 = '0';
                $hasil_kerja_4 = 0;
                $total_upah_lembur_4 = '0';
            }

            $total_hasil_lembur_4 = $total_upah_lembur_4;

            if ($request->umk_borongan_lokal_kerja_5) {
                $umk_borongan_lokal_5 = $this->umkBoronganAmbri->select('id','jenis_produk','umk_las_tepi')
                                                        ->where('id',$request->umk_borongan_lokal_kerja_5)
                                                        ->where('status','Y')
                                                        ->first();

                // $hasil_pengerjaan_5 = $umk_borongan_lokal_5->id.'|'.$umk_borongan_lokal_5->umk_packing*$request->hasil_kerja_5[$key];
                
                if ($request->hasil_kerja_5[$key]) {
                    $hasil_pengerjaan_5 = $umk_borongan_lokal_5->id.'|'.$request->hasil_kerja_5[$key];
                    $hasil_kerja_5 = $umk_borongan_lokal_5->umk_las_tepi*$request->hasil_kerja_5[$key];
                }else{
                    $hasil_pengerjaan_5 = $umk_borongan_lokal_5->id.'|'.'0';
                    $hasil_kerja_5 = 0;
                }
            
                if($request->lembur_kerja_5){
                    $lembur_5 = '5-y';
                    $hasil_lembur_5 = '1.5';
                    // $total_upah_lembur_5 = ($hasil_kerja_5*1.5)-$hasil_kerja_5;
                    $total_upah_lembur_5 = $hasil_kerja_5*1.5;
                }else{
                    $lembur_5 = '5-n';
                    $hasil_lembur_5 = '1';
                    $total_upah_lembur_5 = $hasil_kerja_5*0;
                }

                if($request->total_jam_5[$key]){
                    $total_jam_5 = $request->total_jam_5[$key];
                }else{
                    $total_jam_5 = '0';
                }

            }else{
                $hasil_pengerjaan_5 = '0'.'|'.'0';
                $lembur_5 = '5-n';
                $hasil_lembur_5 = '1';
                $total_jam_5 = '0';
                $hasil_kerja_5 = 0;
                $total_upah_lembur_5 = '0';
            }

            $total_hasil_lembur_5 = $total_upah_lembur_5;

            $lemburs = '|'.$lembur_1.'|'.$lembur_2.'|'.$lembur_3.'|'.$lembur_4.'|'.$lembur_5;
            $hasil_upah_dasar = $hasil_kerja_1+$hasil_kerja_2+$hasil_kerja_3+$hasil_kerja_4+$hasil_kerja_5;
            
            $penjumlahan_lembur = round($total_hasil_lembur_1+$total_hasil_lembur_2+$total_hasil_lembur_3+$total_hasil_lembur_4+$total_hasil_lembur_5);
            // dd($hasil_upah_dasar);
            $karyawan_pengerjaan->update([
                'hasil_kerja_1' => $hasil_pengerjaan_1,
                'hasil_kerja_2' => $hasil_pengerjaan_2,
                'hasil_kerja_3' => $hasil_pengerjaan_3,
                'hasil_kerja_4' => $hasil_pengerjaan_4,
                'hasil_kerja_5' => $hasil_pengerjaan_5,
                'uang_lembur' => $penjumlahan_lembur,
                'lembur' => $lemburs,
                'total_jam_kerja_1' => $total_jam_1,
                'total_jam_kerja_2' => $total_jam_2,
                'total_jam_kerja_3' => $total_jam_3,
                'total_jam_kerja_4' => $total_jam_4,
                'total_jam_kerja_5' => $total_jam_5,
            ]);
        }
        return response()->json([
            'success' => true,
            'message_title' => 'Berhasil',
            'message_content' => 'Data Berhasil Disimpan'
        ]);
    }

    public function hasil_kerja_karyawan_ambri_las_tepi_view($id, $kode_pengerjaan, $nik, $month, $year)
    {
        $data['id'] = $id;
        $data['nik'] = $nik;
        $data['kode_pengerjaan'] = $kode_pengerjaan;
        $data['month'] = $month;
        $data['year'] = $year;
        // dd($data);

        if($id == 1){
            $kode_jenis_operator_detail = 'L';
        }
        elseif($id == 2){
            $kode_jenis_operator_detail = 'E';
        }
        elseif($id == 3){
            $kode_jenis_operator_detail = 'A';
        }
        $data['karyawan'] = $this->karyawanOperator->select([
                                                    'operator_karyawan.id as id',
                                                    'biodata_karyawan.nama as nama',
                                                    'operator_karyawan.nik as nik',
                                                    'biodata_karyawan.pin as pin',
                                                    'biodata_karyawan.tanggal_masuk as tanggal',
                                                    'operator_karyawan.tunjangan_kerja_id as tunjangan_kerja_id'
                                                    ])
                                                    ->leftJoin('itic_emp_new.biodata_karyawan','biodata_karyawan.nik','=','operator_karyawan.nik')
                                                    ->where('operator_karyawan.nik',$nik)
                                                    ->where('operator_karyawan.status','Y')
                                                    ->where('operator_karyawan.jenis_operator_detail_pekerjaan_id',9)
                                                    ->first();

        $data['new_data_pengerjaan'] = $this->newDataPengerjaan->select('akhir_bulan')->where('kode_pengerjaan',$kode_pengerjaan)->first();
        $data['jhts'] = $this->bpjsJht->where('status','y')->get();
        $data['bpjs_kesehatan'] = $this->bpjsKesehatan->select('nominal')->where('status','y')->first();
        // dd($data['new_data_pengerjaan']);

        $data['pengerjaans'] = $this->pengerjaan->where('operator_karyawan_id',$data['karyawan']['id'])->where('kode_pengerjaan',$kode_pengerjaan)->get();
        
        $data['upah'] = array();

        foreach ($data['pengerjaans'] as $key => $pengerjaan) {
            $explode_hasil_kerja_1 = explode("|",$pengerjaan->hasil_kerja_1);
            $umk_borongan_lokal_1 = $this->umkBoronganAmbri->select('id','jenis_produk','umk_etiket','umk_las_tepi','umk_las_pojok','umk_ambri')->where('id',$explode_hasil_kerja_1[0])->first();
            if(empty($umk_borongan_lokal_1)){
                $jenis_produk_1 = '-';
                $hasil_kerja_1 = null;
                $data_explode_hasil_kerja_1 = '-';
                $lembur_1 = 1;
            }else{
                $jenis_produk_1 = $umk_borongan_lokal_1->jenis_produk;
                $hasil_kerja_1 = $explode_hasil_kerja_1[1]*$umk_borongan_lokal_1->umk_las_tepi;
                $data_explode_hasil_kerja_1 = $explode_hasil_kerja_1[1];

                $explode_lembur_1 = explode("|",$pengerjaan->lembur);
                $explode_status_lembur_1 = explode("-",$explode_lembur_1[1]);

                if($explode_status_lembur_1[1] == 'y'){
                    $lembur_1 = 1.5;
                }else{
                    $lembur_1 = 1;
                }
            }

            $explode_hasil_kerja_2 = explode("|",$pengerjaan->hasil_kerja_2);
            $umk_borongan_lokal_2 = $this->umkBoronganAmbri->select('id','jenis_produk','umk_etiket','umk_las_tepi','umk_las_pojok','umk_ambri')->where('id',$explode_hasil_kerja_2[0])->first();
            if(empty($umk_borongan_lokal_2)){
                $jenis_produk_2 = '-';
                $hasil_kerja_2 = null;
                $data_explode_hasil_kerja_2 = '-';
                $lembur_2 = 1;
            }else{
                $jenis_produk_2 = $umk_borongan_lokal_2->jenis_produk;
                $hasil_kerja_2 = $explode_hasil_kerja_2[1]*$umk_borongan_lokal_2->umk_las_tepi;
                $data_explode_hasil_kerja_2 = $explode_hasil_kerja_2[1];

                $explode_lembur_2 = explode("|",$pengerjaan->lembur);
                $explode_status_lembur_2 = explode("-",$explode_lembur_2[2]);

                if($explode_status_lembur_2[1] == 'y'){
                    $lembur_2 = 1.5;
                }else{
                    $lembur_2 = 1;
                }
            }

            $explode_hasil_kerja_3 = explode("|",$pengerjaan->hasil_kerja_3);
            $umk_borongan_lokal_3 = $this->umkBoronganAmbri->select('id','jenis_produk','umk_etiket','umk_las_tepi','umk_las_pojok','umk_ambri')->where('id',$explode_hasil_kerja_3[0])->first();
            if(empty($umk_borongan_lokal_3)){
                $jenis_produk_3 = '-';
                $hasil_kerja_3 = null;
                $data_explode_hasil_kerja_3 = '-';
                $lembur_3 = 1;
            }else{
                $jenis_produk_3 = $umk_borongan_lokal_3->jenis_produk;
                $hasil_kerja_3 = $explode_hasil_kerja_3[1]*$umk_borongan_lokal_3->umk_las_tepi;
                $data_explode_hasil_kerja_3 = $explode_hasil_kerja_3[1];

                $explode_lembur_3 = explode("|",$pengerjaan->lembur);
                $explode_status_lembur_3 = explode("-",$explode_lembur_3[3]);

                if($explode_status_lembur_3[1] == 'y'){
                    $lembur_3 = 1.5;
                }else{
                    $lembur_3 = 1;
                }
            }

            $explode_hasil_kerja_4 = explode("|",$pengerjaan->hasil_kerja_4);
            $umk_borongan_lokal_4 = $this->umkBoronganAmbri->select('id','jenis_produk','umk_etiket','umk_las_tepi','umk_las_pojok','umk_ambri')->where('id',$explode_hasil_kerja_4[0])->first();
            if(empty($umk_borongan_lokal_4)){
                $jenis_produk_4 = '-';
                $hasil_kerja_4 = null;
                $data_explode_hasil_kerja_4 = '-';
                $lembur_4 = 1;
            }else{
                $jenis_produk_4 = $umk_borongan_lokal_4->jenis_produk;
                $hasil_kerja_4 = $explode_hasil_kerja_4[1]*$umk_borongan_lokal_4->umk_las_tepi;
                $data_explode_hasil_kerja_4 = $explode_hasil_kerja_4[1];

                $explode_lembur_4 = explode("|",$pengerjaan->lembur);
                $explode_status_lembur_4 = explode("-",$explode_lembur_4[4]);

                if($explode_status_lembur_4[1] == 'y'){
                    $lembur_4 = 1.5;
                }else{
                    $lembur_4 = 1;
                }
            }

            $explode_hasil_kerja_5 = explode("|",$pengerjaan->hasil_kerja_5);
            $umk_borongan_lokal_5 = $this->umkBoronganAmbri->select('id','jenis_produk','umk_etiket','umk_las_tepi','umk_las_pojok','umk_ambri')->where('id',$explode_hasil_kerja_5[0])->first();
            if(empty($umk_borongan_lokal_5)){
                $jenis_produk_5 = '-';
                $hasil_kerja_5 = null;
                $data_explode_hasil_kerja_5 = '-';
                $lembur_5 = 1;
            }else{
                $jenis_produk_5 = $umk_borongan_lokal_5->jenis_produk;
                $hasil_kerja_5 = $explode_hasil_kerja_5[1]*$umk_borongan_lokal_5->umk_las_tepi;
                $data_explode_hasil_kerja_5 = $explode_hasil_kerja_5[1];

                $explode_lembur_5 = explode("|",$pengerjaan->lembur);
                $explode_status_lembur_5 = explode("-",$explode_lembur_5[5]);

                if($explode_status_lembur_5[1] == 'y'){
                    $lembur_5 = 1.5;
                }else{
                    $lembur_5 = 1;
                }
            }

            $hasil_upah = ($hasil_kerja_1*$lembur_1)+($hasil_kerja_2*$lembur_2)+($hasil_kerja_3*$lembur_3)+($hasil_kerja_4*$lembur_4)+($hasil_kerja_5*$lembur_5);

            array_push($data['upah'],$hasil_upah);
        }

        //Hitung Masa Kerja
        // $data['karyawan_masa_kerja'] = DB::connection('emp')->table('log_posisi')
        //                                                     ->select('tanggal')
        //                                                     ->where('nik',$nik)
        //                                                     ->first();
        $awal  = new DateTime($data['karyawan']->tanggal);
        $akhir = new DateTime(); // Waktu sekarang
        $diff  = $awal->diff($akhir);
        $data['masa_kerja'] = $diff->y.' Tahun '.$diff->m.' Bulan '.$diff->d.' Hari';
        $data['masa_kerja_tahun'] = $diff->y;
        $data['masa_kerja_hari'] = $diff->d;
        //End Hitung Masa Kerja

        $data['upah_dasar_karyawan'] = [];
        foreach ($data['jhts'] as $key => $jht) {
            if ($data['masa_kerja_tahun'] > 15) {
                if ($jht->urutan == 3) {
                    $data['upah_dasar_karyawan'] = ($data['bpjs_kesehatan']['nominal'] + 100000)/25;
                }
            }
            elseif($data['masa_kerja_tahun'] >= 10 && $data['masa_kerja_tahun'] <= 15 && $data['masa_kerja_hari'] >= 1){
                if ($jht->urutan == 2) {
                    $data['upah_dasar_karyawan'] = ($data['bpjs_kesehatan']['nominal'] + 50000)/25;
                }
            }
            elseif($data['masa_kerja_tahun'] <= 10 || $data['masa_kerja_hari'] >= 1){
                if ($jht->urutan == 1) {
                    $data['upah_dasar_karyawan'] = ($data['bpjs_kesehatan']['nominal'] + 0)/25;
                }
            }
        }

        // echo 'Selisih waktu: ';
        // echo $diff->y . ' tahun, ';
        // echo $diff->m . ' bulan, ';
        // echo $diff->d . ' hari, ';
        // echo $diff->h . ' jam, ';
        // echo $diff->i . ' menit, ';
        // echo $diff->s . ' detik, ';
        // dd($data);

        $data['pengerjaan_weekly'] = $this->pengerjaanWeekly->where('operator_karyawan_id',$data['karyawan']['id'])
                                                    ->where('kode_payrol',substr($kode_pengerjaan,0,2).$kode_jenis_operator_detail.'_'.substr($kode_pengerjaan,3))
                                                    ->first();
        // dd($data['pengerjaan_weekly']);
        // $data['presensi_info'] = PresensiInfo::all();
        // dd($data['presensi_info']);
        $tgl_current_active = Carbon::now()->format('d');
        $bln_active = Carbon::now()->format('m');
        $bln_current_active = $month;
        $thn_current_active = $year;

        // $bulan_kemarin = Carbon::now()->subMonth()->format('Y-m').'-26';
        // $bulan_sekarang = Carbon::now()->format('Y-m').'-25';

        if($month == 1 && $tgl_current_active <10){
            $thn_prev_active = $year-1;
            $bulan_kemarin="$thn_prev_active-11-26";
            $bulan_sekarang="$thn_prev_active-12-26";
        }elseif($month == 1 && $tgl_current_active>10 or $month == 2 && $tgl_current_active<10 && $bln_active != 3){
            $thn_prev_active = $year-1;
            $bulan_kemarin="$thn_prev_active-12-26";
            $bulan_sekarang="$thn_current_active-01-26";
        }else{
            $bln_prev_active = $month-1; 
            $thn_prev_active = $year;
            $bulan_kemarin="$thn_prev_active-$bln_prev_active-26";
            $bulan_sekarang="$thn_current_active-$bln_current_active-26";
        }

        $data['tunjangan_kerjas'] = $this->tunjanganKerja->select('nominal')->where('id',$data['karyawan']['tunjangan_kerja_id'])->first();
        if(empty($data['tunjangan_kerjas'])){
            $data['tunjangan_kerja'] = 0;
        }else{
            $data['tunjangan_kerja'] = $data['tunjangan_kerjas']['nominal'];
        }
        //jumlah alpa
        $data['alpa'] = $this->presensiInfo->where('pin',$data['karyawan']['pin'])
                                    ->whereBetween('scan_date',[$bulan_kemarin,$bulan_sekarang])
                                    ->where('status',7)
                                    ->get();
        //jumlah diliburkan
        $data['diliburkan'] = $this->presensiInfo->where('pin',$data['karyawan']['pin'])
                                    ->whereBetween('scan_date',[$bulan_kemarin,$bulan_sekarang])
                                    ->where('status',14)
                                    ->get();
        //jumlah cuti
        $data['cuti'] = $this->presensiInfo->where('pin',$data['karyawan']['pin'])
                                    ->whereBetween('scan_date',[$bulan_kemarin,$bulan_sekarang])
                                    ->where('status',13)
                                    ->get();
        //jumlah sakit
        $data['sakit'] = $this->presensiInfo->where('pin',$data['karyawan']['pin'])
                                    ->whereBetween('scan_date',[$bulan_kemarin,$bulan_sekarang])
                                    ->where('status',4)
                                    ->get();
        // dd($data['sakit']);
        //jumlah ijin full
        $data['ijin_full'] = $this->presensiInfo->where('pin',$data['karyawan']['pin'])
                                    ->whereBetween('scan_date',[$bulan_kemarin,$bulan_sekarang])
                                    ->where('status',6)
                                    ->get();
        //jumlah status terlambat
        $data['terlambats'] = $this->presensiInfo->where('pin',$data['karyawan']['pin'])
                                    ->whereBetween('scan_date',[$bulan_kemarin,$bulan_sekarang])
                                    ->where('status',3)
                                    ->get();
        $telat_1=0; //terlambat < 5 menit
        $telat_2=0; //terlambat > 5 menit < 15 menit
        $telat_3=0; //terlambat > 15 menit < 1 jam
        $telat_4=0; //terlambat > 1 jam < 3 jam	
        $telat_5=0; //terlambat > 3 jam	
        $data['telat_1'] = $telat_1;
        $data['telat_2'] = $telat_2;
        $data['telat_3'] = $telat_3;
        $data['telat_4'] = $telat_4;
        $data['telat_5'] = $telat_5;
        foreach ($data['terlambats'] as $key => $terlambat) {
            $explode_keterangan_terlambat = explode("@",$terlambat->keterangan);
            $jam_keterangan_terlambat=strtotime($explode_keterangan_terlambat[1]);
            $jam_datang_terlambat=strtotime(Carbon::parse($terlambat['scan_date'])->format('H:i'));
            // $jam_datang_terlambat=strtotime($terlambat['jam_datang_telat']);
            $menit_telat=(($jam_datang_terlambat-$jam_keterangan_terlambat)/60);
            // dd($menit_telat);
            if($menit_telat <= 4){
                $telat_1=$telat_1+1;
                $data['telat_1'] = $telat_1;
            }
            else if(($menit_telat >= 5) && ($menit_telat <= 15) ){
                $telat_2=$telat_2+1;
                $data['telat_2'] = $telat_2;
            }
            else if(($menit_telat >= 16) && ($menit_telat <= 60)){
                $telat_3=$telat_3+1;
                $data['telat_3'] = $telat_3;
            }
            else if(($menit_telat >= 61) && ($menit_telat <= 179)){
                $telat_4=$telat_4+1;
                $data['telat_4'] = $telat_4;
            }else if($menit_telat > 179){
                $telat_5=$telat_5+1;
                $data['telat_5'] = $telat_5;
            }
            // else{
            //     $telat_4=$telat_4+1;
            //     $data['telat_4'] = $telat_4;
            // }
        }

        //jumlah status pulang awal
        $data['pulang_awals'] = $this->presensiInfo->where('pin',$data['karyawan']['pin'])
                                    ->whereBetween('scan_date',[$bulan_kemarin,$bulan_sekarang])
                                    ->where(function($query) {
                                        $query->where('status','=',9)
                                            ->orWhere('status','=',10);
                                    })
                                    // ->where('status',9)
                                    // ->where('status',10)
                                    ->get();
        // dd($data['pulang_awals']);
        $pulang_1=0; //pulang awal < 4 jam
		$pulang_2=0; //tpulang awal > 4 jam
        $data['pulang_1'] = $pulang_1;
        $data['pulang_2'] = $pulang_2;
        foreach ($data['pulang_awals'] as $key => $pulang_awal) {
            $explode_pulang_seharusnya=explode("@",$pulang_awal->keterangan);
			$jam_pulang_seharusnya=strtotime($explode_pulang_seharusnya[3]);
            $jam_pulang_awal=strtotime(Carbon::parse($pulang_awal['scan_date'])->format('H:i'));
            // $jam_pulang_awal=strtotime($pulang_awal->jam_pulang_awal);
			$menit_sisa_jam_kerja=(($jam_pulang_seharusnya-$jam_pulang_awal)/60);
			if ($menit_sisa_jam_kerja<=300){
                $pulang_1=$pulang_1+1;
                $data['pulang_1'] = $pulang_1;
            }
			else{
                $pulang_2=$pulang_2+1;
                $data['pulang_2'] = $pulang_2;
            }
        }

        //jumlah ijin saat jam kerja
        $data['keluar_masuks'] = $this->keluarMasuk->where('nik',$data['karyawan']['nik'])
                                    ->whereBetween('tanggal_ijin',[$bulan_kemarin,$bulan_sekarang])
                                    ->get();
        // dd($data['keluar_masuks']);
        $ijin_15=0; //ijin kurang dari 15 menit
		$ijin_k4=0; //ijin > 15menit < 4 jam
		$ijin_l4=0; //ijin > 4 jam
        $data['ijin_15']=$ijin_15;
        $data['ijin_k4']=$ijin_k4;
        $data['ijin_l4']=$ijin_l4;
        foreach ($data['keluar_masuks'] as $key => $keluar_masuk) {
            $jam_keluar=strtotime($keluar_masuk->jam_keluar);
			$jam_datang=strtotime($keluar_masuk->jam_datang);
			$menit_keluar_masuk=(($jam_datang-$jam_keluar)/60);
			if ($menit_keluar_masuk<=15){
                $ijin_15=$ijin_15+1;
                $data['ijin_15']=$ijin_15;
            }
			else if (($menit_keluar_masuk>15)&&($menit_keluar_masuk<=239)){
                $ijin_k4=$ijin_k4+1;
                $data['ijin_k4']=$ijin_k4;
            }
			else {
                $ijin_l4=$ijin_l4+1;
                $data['ijin_l4']=$ijin_l4;
            }
        }

        //lihat tanggal masuk
        // $data['log_posisi'] = $this->logPosisi->select('tanggal')->where('nik',$nik)->first();
		$selisih_tanggal_masuk=strtotime($bulan_kemarin)-strtotime($data['karyawan']->tanggal);
		if ($selisih_tanggal_masuk>=0)$div_tk=0;else $div_tk=75000;

        $total_potongan_tk=(75000*$data['alpa']->count())+(75000*$data['diliburkan']->count())+(75000*$data['sakit']->count())+(75000*$data['cuti']->count())+(75000*$data['ijin_full']->count())+($data['ijin_15']*25000)+($data['ijin_k4']*40000)+
        ($data['ijin_l4']*75000)+($data['pulang_1']*40000)+($data['pulang_2']*75000)+($data['telat_1']*15000)+($data['telat_2']*25000)+($data['telat_3']*30000)+($data['telat_4']*40000)+$div_tk;
        if ($total_potongan_tk>75000){$total_potongan_tk=75000;}else {}

        // if ($data['pengerjaan_weekly']['tunjangan_kehadiran'] == 75000) {
        //     $data['total_potongan_tk']=0;
        // }
        // // elseif($data['pengerjaan_weekly']['tunjangan_kehadiran'] < 75000){
        // //     $data['total_potongan_tk']=$data['pengerjaan_weekly']['tunjangan_kehadiran'];
        // // }
        // else{
        //     $data['total_potongan_tk']=$total_potongan_tk; 
        // }

        $data['total_potongan_tk']=$total_potongan_tk;
        return view('backend.pengerjaan.packing_ambri_las_tepi.input_hasil_kerja_karyawan',$data);
    }

    public function hasil_kerja_karyawan_ambri_las_tepi_view_simpan(Request $request, $id, $kode_pengerjaan, $nik)
    {
        // $input = $request->all();
        if($id == 1){
            $kode_jenis_operator_detail = 'L';
        }
        elseif($id == 2){
            $kode_jenis_operator_detail = 'E';
        }
        elseif($id == 3){
            $kode_jenis_operator_detail = 'A';
        }

        $data['id'] = $id;
        $data['kode_pengerjaan'] = $kode_pengerjaan;
        $data['nik'] = $nik;

        $input['upah_dasar'] = $request->upah_dasar;
        $input['upah_makan'] = $request->upah_makan;
        
        $input['uang_makan'] = $request->uang_makan;
        $input['plus_1'] = $request->plus_1."|".$request->keterangan_plus_1;
        $input['plus_2'] = $request->plus_2."|".$request->keterangan_plus_2;
        $input['plus_3'] = $request->plus_3."|".$request->keterangan_plus_3;
        
        $input['minus_1'] = $request->minus_1."|".$request->keterangan_minus_1;
        $input['minus_2'] = $request->minus_2."|".$request->keterangan_minus_2;

        if ($request->akhir_bulan == 'n') {
            $input['tunjangan_kerja'] = 0;
            $input['tunjangan_kehadiran'] = 0;
        }else{
            $input['tunjangan_kerja'] = $request->tunjangan_kerja;
            $input['tunjangan_kehadiran'] = 75000-$request->pot_tunjangan_kehadiran;
        }

        if ($request->check_jht == 'on') {
            $input['jht'] = $request->jht;
        }else{
            $input['jht'] = 0;
        }

        if($request->check_bpjs_kesehatan == 'on'){
            $input['bpjs_kesehatan'] = $request->bpjs_kesehatan;
        }else{
            $input['bpjs_kesehatan'] = 0;
        }
        // dd($input);
        $operator_karyawan = $this->karyawanOperator->select('id')->where('nik',$nik)->where('status','Y')->where('jenis_operator_detail_pekerjaan_id',9)->orderBy('id','desc')->first();
        $pengerjaan_weekly = $this->pengerjaanWeekly->where('kode_payrol',substr($kode_pengerjaan,0,2).$kode_jenis_operator_detail.'_'.substr($kode_pengerjaan,3))
                                            ->where('operator_karyawan_id',$operator_karyawan->id)
                                            ->first();
        // $pengerjaan_weekly->update($input);
        // dd($pengerjaan_weekly);
        if (!empty($pengerjaan_weekly)) {
            $pengerjaan_weekly->update($input);
            return response()->json([
                'success' => true,
                'message_title' => 'Berhasil',
                'message_content' => 'Data Berhasil Disimpan'
            ]);
        }else{
            return response()->json([
                'success' => false,
                'message_title' => 'Gagal',
                'message_content' => 'Data Tidak Berhasil Disimpan'
            ]);
        }
    }

    public function hasil_kerja_las_pojok()
    {
        return '-';
    }

    public function hasil_kerja_ambri_isi_ambri($id, $kode_pengerjaan)
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

        $data['id'] = $id;
        $data['kode_pengerjaan'] = $kode_pengerjaan;
        $data['jenis_operator_detail_pekerjaan'] = $this->jenisOperatorDetailPengerjaan->find(11);
        $data['new_data_pengerjaan'] = $this->newDataPengerjaan->where('kode_pengerjaan',$kode_pengerjaan)->first();
        $data['explode_tanggal_pengerjaans'] = explode('#',$data['new_data_pengerjaan']['tanggal']);
        $data['count_tanggal'] = count($data['explode_tanggal_pengerjaans'])-1;
        // $data['packing_ekspors'] = KaryawanOperator::select([
        //                             'operator_karyawan.id as id',
        //                             'operator_karyawan.nik as nik',
        //                             'biodata_karyawan.nama as nama',
        //                             ])
        //                             ->where('jenis_operator_id',1)
        //                             ->where('jenis_operator_detail_id',2)
        //                             ->where('jenis_operator_detail_pekerjaan_id',5)
        //                             ->leftJoin('biodata_karyawan','biodata_karyawan.nik','=','operator_karyawan.nik')
        //                             ->rightJoin('pengerjaan','pengerjaan.operator_karyawan_id','=','operator_karyawan.id')
        //                             ->orderBy('biodata_karyawan.nama','asc')
        //                             ->get();

        $data['pengerjaans'] = $this->pengerjaanWeekly->select([
                                                    'pengerjaan_weekly.kode_payrol as kode_payrol',
                                                    'pengerjaan_weekly.operator_karyawan_id as operator_karyawan_id',
                                                    'operator_karyawan.nik as nik',
                                                    'biodata_karyawan.nama as nama',
                                                    'pengerjaan_weekly.upah_dasar as upah_dasar',
                                                    'pengerjaan_weekly.tunjangan_kerja as tunjangan_kerja',
                                                    'pengerjaan_weekly.tunjangan_kehadiran as tunjangan_kehadiran',
                                                    'pengerjaan_weekly.uang_makan as uang_makan',
                                                    'pengerjaan_weekly.plus_1 as plus_1',
                                                    'pengerjaan_weekly.plus_2 as plus_2',
                                                    'pengerjaan_weekly.plus_3 as plus_3',
                                                    'pengerjaan_weekly.minus_1 as minus_1',
                                                    'pengerjaan_weekly.minus_2 as minus_2',
                                                    'pengerjaan_weekly.jht as jht',
                                                    'pengerjaan_weekly.bpjs_kesehatan as bpjs_kesehatan',
                                                ])
                                                ->where('kode_payrol',substr($kode_pengerjaan,0,2).$kode_jenis_operator_detail.'_'.substr($kode_pengerjaan,3))
                                                ->where('operator_karyawan.jenis_operator_id',1)
                                                ->where('operator_karyawan.jenis_operator_detail_id',3)
                                                ->where('operator_karyawan.jenis_operator_detail_pekerjaan_id',11)
                                                ->where('operator_karyawan.status','Y')
                                                ->leftJoin('operator_karyawan','operator_karyawan.id','=','pengerjaan_weekly.operator_karyawan_id')
                                                ->leftJoin('itic_emp_new.biodata_karyawan','biodata_karyawan.nik','=','operator_karyawan.nik')
                                                ->orderBy('biodata_karyawan.nama','asc')
                                                ->get();

        return view('backend.pengerjaan.packing_ambri_isi_ambri.packing_isi_ambri',$data);
    }

    public function hasil_kerja_ambri_isi_ambri_view_hasil($id, $kode_pengerjaan, $tanggal)
    {
        $data['id'] = $id;
        $data['kode_pengerjaan'] = $kode_pengerjaan;
        $data['tanggal'] = $tanggal;
        $data['packing_ambris'] = $this->karyawanOperator->select([
                                                    'operator_karyawan.id as id',
                                                    'operator_karyawan.nik as nik',
                                                    'biodata_karyawan.nama as nama',
                                                    'pengerjaan.hasil_kerja_1 as hasil_kerja_1',
                                                    'pengerjaan.hasil_kerja_2 as hasil_kerja_2',
                                                    'pengerjaan.hasil_kerja_3 as hasil_kerja_3',
                                                    'pengerjaan.hasil_kerja_4 as hasil_kerja_4',
                                                    'pengerjaan.hasil_kerja_5 as hasil_kerja_5',
                                                    'pengerjaan.total_jam_kerja_1 as total_jam_kerja_1',
                                                    'pengerjaan.total_jam_kerja_2 as total_jam_kerja_2',
                                                    'pengerjaan.total_jam_kerja_3 as total_jam_kerja_3',
                                                    'pengerjaan.total_jam_kerja_4 as total_jam_kerja_4',
                                                    'pengerjaan.total_jam_kerja_5 as total_jam_kerja_5',
                                                    ])
                                                    ->leftJoin('itic_emp_new.biodata_karyawan','biodata_karyawan.nik','=','operator_karyawan.nik')
                                                    ->rightJoin('pengerjaan','pengerjaan.operator_karyawan_id','=','operator_karyawan.id')
                                                    ->where('jenis_operator_id',1)
                                                    ->where('jenis_operator_detail_id',3)
                                                    ->where('jenis_operator_detail_pekerjaan_id',11)
                                                    ->where('pengerjaan.tanggal_pengerjaan',$tanggal)
                                                    ->where('operator_karyawan.status','Y')
                                                    ->where('pengerjaan.kode_pengerjaan',$kode_pengerjaan)
                                                    // ->with('biodata_karyawan')
                                                    ->orderBy('biodata_karyawan.nama','asc')
                                                    ->get();

        $data['umk_borongan_ambris'] = $this->umkBoronganAmbri->select('id','jenis_produk','umk_ambri')->where('status','Y')->get();
        // dd($data);
        return view('backend.pengerjaan.packing_ambri_isi_ambri.input_hasil_kerja',$data);
    }

    public function hasil_kerja_ambri_isi_ambri_view_simpan(Request $request, $id, $kode_pengerjaan, $tanggal)
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

        $input = $request->all();
        $input['id'] = $id;
        $input['kode_pengerjaan'] = $kode_pengerjaan;
        $input['tanggal'] = $tanggal;

        $data['karyawan_pengerjaans'] = $this->pengerjaan->select([
                                                        'pengerjaan.id as id',
                                                        'operator_karyawan.nik as nik',
                                                        'pengerjaan.operator_karyawan_id as id_operator_karyawan'
                                                    ])
                                                    ->leftJoin('operator_karyawan','operator_karyawan.id','=','pengerjaan.operator_karyawan_id')
                                                    ->leftJoin('itic_emp_new.biodata_karyawan','biodata_karyawan.nik','=','operator_karyawan.nik')
                                                    ->where('tanggal_pengerjaan',$tanggal)
                                                    // ->where('kode_pengerjaan',$kode_pengerjaan)
                                                    // ->where('pengerjaan.operator_karyawan_id','operator_karyawan.id')
                                                    ->where('operator_karyawan.jenis_operator_id',1)
                                                    ->where('operator_karyawan.jenis_operator_detail_id',3)
                                                    ->where('operator_karyawan.jenis_operator_detail_pekerjaan_id',11)
                                                    ->where('operator_karyawan.status','Y')
                                                    ->where('pengerjaan.kode_pengerjaan',$kode_pengerjaan)
                                                    ->orderBy('biodata_karyawan.nama','asc')
                                                    ->get();
        
        foreach ($data['karyawan_pengerjaans'] as $key => $karyawan_pengerjaan) {
            // dd($request->umk_borongan_lokal_kerja_1);
            if ($request->umk_borongan_lokal_kerja_1) {
                $umk_borongan_lokal_1 = $this->umkBoronganAmbri->select('id','jenis_produk','umk_ambri')
                                                        ->where('id',$request->umk_borongan_lokal_kerja_1)
                                                        ->where('status','Y')
                                                        ->first();

                // $hasil_pengerjaan_1 = $umk_borongan_lokal_1->id.'|'.$umk_borongan_lokal_1->umk_packing*$request->hasil_kerja_1[$key];
                
                // if ($request->hasil_kerja_1[$key]) {
                // }

                if ($request->hasil_kerja_1[$key]) {
                    $hasil_pengerjaan_1 = $umk_borongan_lokal_1->id.'|'.$request->hasil_kerja_1[$key];
                    $hasil_kerja_1 = $umk_borongan_lokal_1->umk_ambri*$request->hasil_kerja_1[$key];
                }else{
                    $hasil_pengerjaan_1 = $umk_borongan_lokal_1->id.'|'.'0';
                    $hasil_kerja_1 = 0;
                }

                if($request->lembur_kerja_1){
                    $lembur_1 = '1-y';
                    $hasil_lembur_1 = '1.5';
                    // $total_upah_lembur_1 = ($hasil_kerja_1*1.5)-$hasil_kerja_1;
                    $total_upah_lembur_1 = $hasil_kerja_1*1.5;
                }else{
                    $lembur_1 = '1-n';
                    $hasil_lembur_1 = '1';
                    $total_upah_lembur_1 = $hasil_kerja_1*0;
                }

                if($request->total_jam_1[$key]){
                    $total_jam_1 = $request->total_jam_1[$key];
                }else{
                    $total_jam_1 = '0';
                }
                
            }else{
                $hasil_pengerjaan_1 = '0'.'|'.'0';
                $lembur_1 = '1-n';
                $hasil_lembur_1 = '1';
                $total_jam_1 = '0';
                $hasil_kerja_1 = 0;
                $total_upah_lembur_1 = '0';
            }

            $total_hasil_lembur_1 = $total_upah_lembur_1;

            // dd($hasil_pengerjaan_1);
            
            if ($request->umk_borongan_lokal_kerja_2) {
                $umk_borongan_lokal_2 = $this->umkBoronganAmbri->select('id','jenis_produk','umk_ambri')
                                                        ->where('id',$request->umk_borongan_lokal_kerja_2)
                                                        ->where('status','Y')
                                                        ->first();

                // $hasil_pengerjaan_2 = $umk_borongan_lokal_2->id.'|'.$umk_borongan_lokal_2->umk_packing*$request->hasil_kerja_2[$key];
                
                if ($request->hasil_kerja_2[$key]) {
                    $hasil_pengerjaan_2 = $umk_borongan_lokal_2->id.'|'.$request->hasil_kerja_2[$key];
                    $hasil_kerja_2 = $umk_borongan_lokal_2->umk_ambri*$request->hasil_kerja_2[$key];
                }else{
                    $hasil_pengerjaan_2 = $umk_borongan_lokal_2->id.'|'.'0';
                    $hasil_kerja_2 = 0;
                }

                if($request->lembur_kerja_2){
                    $lembur_2 = '2-y';
                    $hasil_lembur_2 = '1.5';
                    // $total_upah_lembur_2 = ($hasil_kerja_2*1.5)-$hasil_kerja_2;
                    $total_upah_lembur_2 = $hasil_kerja_2*1.5;
                }else{
                    $lembur_2 = '2-n';
                    $hasil_lembur_2 = '1';
                    $total_upah_lembur_2 = $hasil_kerja_2*0;
                }

                if($request->total_jam_2[$key]){
                    $total_jam_2 = $request->total_jam_2[$key];
                }else{
                    $total_jam_2 = '0';
                }

            }else{
                $hasil_pengerjaan_2 = '0'.'|'.'0';
                $lembur_2 = '2-n';
                $hasil_lembur_2 = '1';
                $total_jam_2 = '0';
                $hasil_kerja_2 = 0;
                $total_upah_lembur_2 = '0';
            }

            $total_hasil_lembur_2 = $total_upah_lembur_2;

            if ($request->umk_borongan_lokal_kerja_3) {
                $umk_borongan_lokal_3 = $this->umkBoronganAmbri->select('id','jenis_produk','umk_ambri')
                                                        ->where('id',$request->umk_borongan_lokal_kerja_3)
                                                        ->where('status','Y')
                                                        ->first();

                // $hasil_pengerjaan_3 = $umk_borongan_lokal_3->id.'|'.$umk_borongan_lokal_3->umk_packing*$request->hasil_kerja_3[$key];
                
                if ($request->hasil_kerja_3[$key]) {
                    $hasil_pengerjaan_3 = $umk_borongan_lokal_3->id.'|'.$request->hasil_kerja_3[$key];
                    $hasil_kerja_3 = $umk_borongan_lokal_3->umk_ambri*$request->hasil_kerja_3[$key];
                }else{
                    $hasil_pengerjaan_3 = $umk_borongan_lokal_3->id.'|'.'0';
                    $hasil_kerja_3 = 0;
                }
            
                if($request->lembur_kerja_3){
                    $lembur_3 = '3-y';
                    $hasil_lembur_3 = '1.5';
                    // $total_upah_lembur_3 = ($hasil_kerja_3*1.5)-$hasil_kerja_3;
                    $total_upah_lembur_3 = $hasil_kerja_3*1.5;
                }else{
                    $lembur_3 = '3-n';
                    $hasil_lembur_3 = '1';
                    $total_upah_lembur_3 = $hasil_kerja_3*0;
                }

                if($request->total_jam_3[$key]){
                    $total_jam_3 = $request->total_jam_3[$key];
                }else{
                    $total_jam_3 = '0';
                }

            }else{
                $hasil_pengerjaan_3 = '0'.'|'.'0';
                $lembur_3 = '3-n';
                $hasil_lembur_3 = '1';
                $total_jam_3 = '0';
                $hasil_kerja_3 = 0;
                $total_upah_lembur_3 = '0';
            }

            $total_hasil_lembur_3 = $total_upah_lembur_3;

            if ($request->umk_borongan_lokal_kerja_4) {
                $umk_borongan_lokal_4 = $this->umkBoronganAmbri->select('id','jenis_produk','umk_ambri')
                                                        ->where('id',$request->umk_borongan_lokal_kerja_4)
                                                        ->where('status','Y')
                                                        ->first();

                // $hasil_pengerjaan_4 = $umk_borongan_lokal_4->id.'|'.$umk_borongan_lokal_4->umk_packing*$request->hasil_kerja_4[$key];
                
                if ($request->hasil_kerja_4[$key]) {
                    $hasil_pengerjaan_4 = $umk_borongan_lokal_4->id.'|'.$request->hasil_kerja_4[$key];
                    $hasil_kerja_4 = $umk_borongan_lokal_4->umk_ambri*$request->hasil_kerja_4[$key];
                }else{
                    $hasil_pengerjaan_4 = $umk_borongan_lokal_4->id.'|'.'0';
                    $hasil_kerja_4 = 0;
                }
            
                if($request->lembur_kerja_4){
                    $lembur_4 = '4-y';
                    $hasil_lembur_4 = '1.5';
                    // $total_upah_lembur_4 = ($hasil_kerja_4*1.5)-$hasil_kerja_4;
                    $total_upah_lembur_4 = $hasil_kerja_4*1.5;
                }else{
                    $lembur_4 = '4-n';
                    $hasil_lembur_4 = '1';
                    $total_upah_lembur_4 = $hasil_kerja_4*0;
                }

                if($request->total_jam_4[$key]){
                    $total_jam_4 = $request->total_jam_4[$key];
                }else{
                    $total_jam_4 = '0';
                }

            }else{
                $hasil_pengerjaan_4 = '0'.'|'.'0';
                $lembur_4 = '4-n';
                $hasil_lembur_4 = '1';
                $total_jam_4 = '0';
                $hasil_kerja_4 = 0;
                $total_upah_lembur_4 = '0';
            }

            $total_hasil_lembur_4 = $total_upah_lembur_4;

            if ($request->umk_borongan_lokal_kerja_5) {
                $umk_borongan_lokal_5 = $this->umkBoronganAmbri->select('id','jenis_produk','umk_ambri')
                                                        ->where('id',$request->umk_borongan_lokal_kerja_5)
                                                        ->where('status','Y')
                                                        ->first();

                // $hasil_pengerjaan_5 = $umk_borongan_lokal_5->id.'|'.$umk_borongan_lokal_5->umk_packing*$request->hasil_kerja_5[$key];
                
                if ($request->hasil_kerja_5[$key]) {
                    $hasil_pengerjaan_5 = $umk_borongan_lokal_5->id.'|'.$request->hasil_kerja_5[$key];
                    $hasil_kerja_5 = $umk_borongan_lokal_5->umk_ambri*$request->hasil_kerja_5[$key];
                }else{
                    $hasil_pengerjaan_5 = $umk_borongan_lokal_5->id.'|'.'0';
                    $hasil_kerja_5 = 0;
                }
            
                if($request->lembur_kerja_5){
                    $lembur_5 = '5-y';
                    $hasil_lembur_5 = '1.5';
                    // $total_upah_lembur_5 = ($hasil_kerja_5*1.5)-$hasil_kerja_5;
                    $total_upah_lembur_5 = $hasil_kerja_5*1.5;
                }else{
                    $lembur_5 = '5-n';
                    $hasil_lembur_5 = '1';
                    $total_upah_lembur_5 = $hasil_kerja_5*0;
                }

                if($request->total_jam_5[$key]){
                    $total_jam_5 = $request->total_jam_5[$key];
                }else{
                    $total_jam_5 = '0';
                }

            }else{
                $hasil_pengerjaan_5 = '0'.'|'.'0';
                $lembur_5 = '5-n';
                $hasil_lembur_5 = '1';
                $total_jam_5 = '0';
                $hasil_kerja_5 = 0;
                $total_upah_lembur_5 = '0';
            }

            $total_hasil_lembur_5 = $total_upah_lembur_5;

            $lemburs = '|'.$lembur_1.'|'.$lembur_2.'|'.$lembur_3.'|'.$lembur_4.'|'.$lembur_5;
            $hasil_upah_dasar = $hasil_kerja_1+$hasil_kerja_2+$hasil_kerja_3+$hasil_kerja_4+$hasil_kerja_5;
            
            $penjumlahan_lembur = round($total_hasil_lembur_1+$total_hasil_lembur_2+$total_hasil_lembur_3+$total_hasil_lembur_4+$total_hasil_lembur_5);
            
            // dd($hasil_upah_dasar);
            $karyawan_pengerjaan->update([
                'hasil_kerja_1' => $hasil_pengerjaan_1,
                'hasil_kerja_2' => $hasil_pengerjaan_2,
                'hasil_kerja_3' => $hasil_pengerjaan_3,
                'hasil_kerja_4' => $hasil_pengerjaan_4,
                'hasil_kerja_5' => $hasil_pengerjaan_5,
                'uang_lembur' => $penjumlahan_lembur,
                'lembur' => $lemburs,
                'total_jam_kerja_1' => $total_jam_1,
                'total_jam_kerja_2' => $total_jam_2,
                'total_jam_kerja_3' => $total_jam_3,
                'total_jam_kerja_4' => $total_jam_4,
                'total_jam_kerja_5' => $total_jam_5,
            ]);
        }
        return response()->json([
            'success' => true,
            'message_title' => 'Berhasil',
            'message_content' => 'Data Berhasil Disimpan'
        ]);
    }

    public function hasil_kerja_karyawan_ambri_isi_ambri_view($id, $kode_pengerjaan, $nik, $month, $year)
    {
        $data['id'] = $id;
        $data['nik'] = $nik;
        $data['kode_pengerjaan'] = $kode_pengerjaan;
        $data['month'] = $month;
        $data['year'] = $year;
        // dd($data);

        if($id == 1){
            $kode_jenis_operator_detail = 'L';
        }
        elseif($id == 2){
            $kode_jenis_operator_detail = 'E';
        }
        elseif($id == 3){
            $kode_jenis_operator_detail = 'A';
        }
        $data['karyawan'] = $this->karyawanOperator->select([
                                                    'operator_karyawan.id as id',
                                                    'biodata_karyawan.nama as nama',
                                                    'operator_karyawan.nik as nik',
                                                    'biodata_karyawan.pin as pin',
                                                    'biodata_karyawan.tanggal_masuk as tanggal',
                                                    'operator_karyawan.tunjangan_kerja_id as tunjangan_kerja_id'
                                                    ])
                                                    ->leftJoin('itic_emp_new.biodata_karyawan','biodata_karyawan.nik','=','operator_karyawan.nik')
                                                    ->where('operator_karyawan.nik',$nik)
                                                    ->where('operator_karyawan.status','Y')
                                                    ->where('operator_karyawan.jenis_operator_detail_pekerjaan_id',11)
                                                    ->first();

        $data['new_data_pengerjaan'] = $this->newDataPengerjaan->select('akhir_bulan')->where('kode_pengerjaan',$kode_pengerjaan)->first();
        $data['jhts'] = $this->bpjsJht->where('status','y')->get();
        $data['bpjs_kesehatan'] = $this->bpjsKesehatan->select('nominal')->where('status','y')->first();
        // dd($data['new_data_pengerjaan']);

        $data['pengerjaans'] = $this->pengerjaan->where('operator_karyawan_id',$data['karyawan']['id'])->where('kode_pengerjaan',$kode_pengerjaan)->get();
        
        $data['upah'] = array();

        foreach ($data['pengerjaans'] as $key => $pengerjaan) {
            $explode_hasil_kerja_1 = explode("|",$pengerjaan->hasil_kerja_1);
            $umk_borongan_lokal_1 = $this->umkBoronganAmbri->select('id','jenis_produk','umk_etiket','umk_las_tepi','umk_las_pojok','umk_ambri')->where('id',$explode_hasil_kerja_1[0])->first();
            if(empty($umk_borongan_lokal_1)){
                $jenis_produk_1 = '-';
                $hasil_kerja_1 = null;
                $data_explode_hasil_kerja_1 = '-';
                $lembur_1 = 1;
            }else{
                $jenis_produk_1 = $umk_borongan_lokal_1->jenis_produk;
                $hasil_kerja_1 = $explode_hasil_kerja_1[1]*$umk_borongan_lokal_1->umk_ambri;
                $data_explode_hasil_kerja_1 = $explode_hasil_kerja_1[1];

                $explode_lembur_1 = explode("|",$pengerjaan->lembur);
                $explode_status_lembur_1 = explode("-",$explode_lembur_1[1]);

                if($explode_status_lembur_1[1] == 'y'){
                    $lembur_1 = 1.5;
                }else{
                    $lembur_1 = 1;
                }
            }

            $explode_hasil_kerja_2 = explode("|",$pengerjaan->hasil_kerja_2);
            $umk_borongan_lokal_2 = $this->umkBoronganAmbri->select('id','jenis_produk','umk_etiket','umk_las_tepi','umk_las_pojok','umk_ambri')->where('id',$explode_hasil_kerja_2[0])->first();
            if(empty($umk_borongan_lokal_2)){
                $jenis_produk_2 = '-';
                $hasil_kerja_2 = null;
                $data_explode_hasil_kerja_2 = '-';
                $lembur_2 = 1;
            }else{
                $jenis_produk_2 = $umk_borongan_lokal_2->jenis_produk;
                $hasil_kerja_2 = $explode_hasil_kerja_2[1]*$umk_borongan_lokal_2->umk_ambri;
                $data_explode_hasil_kerja_2 = $explode_hasil_kerja_2[1];

                $explode_lembur_2 = explode("|",$pengerjaan->lembur);
                $explode_status_lembur_2 = explode("-",$explode_lembur_2[2]);

                if($explode_status_lembur_2[1] == 'y'){
                    $lembur_2 = 1.5;
                }else{
                    $lembur_2 = 1;
                }
            }

            $explode_hasil_kerja_3 = explode("|",$pengerjaan->hasil_kerja_3);
            $umk_borongan_lokal_3 = $this->umkBoronganAmbri->select('id','jenis_produk','umk_etiket','umk_las_tepi','umk_las_pojok','umk_ambri')->where('id',$explode_hasil_kerja_3[0])->first();
            if(empty($umk_borongan_lokal_3)){
                $jenis_produk_3 = '-';
                $hasil_kerja_3 = null;
                $data_explode_hasil_kerja_3 = '-';
                $lembur_3 = 1;
            }else{
                $jenis_produk_3 = $umk_borongan_lokal_3->jenis_produk;
                $hasil_kerja_3 = $explode_hasil_kerja_3[1]*$umk_borongan_lokal_3->umk_ambri;
                $data_explode_hasil_kerja_3 = $explode_hasil_kerja_3[1];

                $explode_lembur_3 = explode("|",$pengerjaan->lembur);
                $explode_status_lembur_3 = explode("-",$explode_lembur_3[3]);

                if($explode_status_lembur_3[1] == 'y'){
                    $lembur_3 = 1.5;
                }else{
                    $lembur_3 = 1;
                }
            }

            $explode_hasil_kerja_4 = explode("|",$pengerjaan->hasil_kerja_4);
            $umk_borongan_lokal_4 = $this->umkBoronganAmbri->select('id','jenis_produk','umk_etiket','umk_las_tepi','umk_las_pojok','umk_ambri')->where('id',$explode_hasil_kerja_4[0])->first();
            if(empty($umk_borongan_lokal_4)){
                $jenis_produk_4 = '-';
                $hasil_kerja_4 = null;
                $data_explode_hasil_kerja_4 = '-';
                $lembur_4 = 1;
            }else{
                $jenis_produk_4 = $umk_borongan_lokal_4->jenis_produk;
                $hasil_kerja_4 = $explode_hasil_kerja_4[1]*$umk_borongan_lokal_4->umk_ambri;
                $data_explode_hasil_kerja_4 = $explode_hasil_kerja_4[1];

                $explode_lembur_4 = explode("|",$pengerjaan->lembur);
                $explode_status_lembur_4 = explode("-",$explode_lembur_4[4]);

                if($explode_status_lembur_4[1] == 'y'){
                    $lembur_4 = 1.5;
                }else{
                    $lembur_4 = 1;
                }
            }

            $explode_hasil_kerja_5 = explode("|",$pengerjaan->hasil_kerja_5);
            $umk_borongan_lokal_5 = $this->umkBoronganAmbri->select('id','jenis_produk','umk_etiket','umk_las_tepi','umk_las_pojok','umk_ambri')->where('id',$explode_hasil_kerja_5[0])->first();
            if(empty($umk_borongan_lokal_5)){
                $jenis_produk_5 = '-';
                $hasil_kerja_5 = null;
                $data_explode_hasil_kerja_5 = '-';
                $lembur_5 = 1;
            }else{
                $jenis_produk_5 = $umk_borongan_lokal_5->jenis_produk;
                $hasil_kerja_5 = $explode_hasil_kerja_5[1]*$umk_borongan_lokal_5->umk_ambri;
                $data_explode_hasil_kerja_5 = $explode_hasil_kerja_5[1];

                $explode_lembur_5 = explode("|",$pengerjaan->lembur);
                $explode_status_lembur_5 = explode("-",$explode_lembur_5[5]);

                if($explode_status_lembur_5[1] == 'y'){
                    $lembur_5 = 1.5;
                }else{
                    $lembur_5 = 1;
                }
            }

            $hasil_upah = ($hasil_kerja_1*$lembur_1)+($hasil_kerja_2*$lembur_2)+($hasil_kerja_3*$lembur_3)+($hasil_kerja_4*$lembur_4)+($hasil_kerja_5*$lembur_5);

            array_push($data['upah'],$hasil_upah);
        }

        //Hitung Masa Kerja
        // $data['karyawan_masa_kerja'] = DB::connection('emp')->table('log_posisi')
        //                                                     ->select('tanggal')
        //                                                     ->where('nik',$nik)
        //                                                     ->first();
        // dd($data['karyawan_masa_kerja']);
        $awal  = new DateTime($data['karyawan']->tanggal);
        $akhir = new DateTime(); // Waktu sekarang
        $diff  = $awal->diff($akhir);
        $data['masa_kerja'] = $diff->y.' Tahun '.$diff->m.' Bulan '.$diff->d.' Hari';
        $data['masa_kerja_tahun'] = $diff->y;
        $data['masa_kerja_hari'] = $diff->d;
        //End Hitung Masa Kerja

        $data['upah_dasar_karyawan'] = [];
        foreach ($data['jhts'] as $key => $jht) {
            if ($data['masa_kerja_tahun'] > 15) {
                if ($jht->urutan == 3) {
                    $data['upah_dasar_karyawan'] = ($data['bpjs_kesehatan']['nominal'] + 100000)/25;
                }
            }
            elseif($data['masa_kerja_tahun'] >= 10 && $data['masa_kerja_tahun'] <= 15 && $data['masa_kerja_hari'] >= 1){
                if ($jht->urutan == 2) {
                    $data['upah_dasar_karyawan'] = ($data['bpjs_kesehatan']['nominal'] + 50000)/25;
                }
            }
            elseif($data['masa_kerja_tahun'] <= 10 || $data['masa_kerja_hari'] >= 1){
                if ($jht->urutan == 1) {
                    $data['upah_dasar_karyawan'] = ($data['bpjs_kesehatan']['nominal'] + 0)/25;
                }
            }
        }

        // echo 'Selisih waktu: ';
        // echo $diff->y . ' tahun, ';
        // echo $diff->m . ' bulan, ';
        // echo $diff->d . ' hari, ';
        // echo $diff->h . ' jam, ';
        // echo $diff->i . ' menit, ';
        // echo $diff->s . ' detik, ';
        // dd($data);

        $data['pengerjaan_weekly'] = $this->pengerjaanWeekly->where('operator_karyawan_id',$data['karyawan']['id'])
                                                    ->where('kode_payrol',substr($kode_pengerjaan,0,2).$kode_jenis_operator_detail.'_'.substr($kode_pengerjaan,3))
                                                    ->first();
        // dd($data['pengerjaan_weekly']);
        // $data['presensi_info'] = PresensiInfo::all();
        // dd($data['presensi_info']);
        $tgl_current_active = Carbon::now()->format('d');
        $bln_active = Carbon::now()->format('m');
        $bln_current_active = $month;
        $thn_current_active = $year;

        // $bulan_kemarin = Carbon::now()->subMonth()->format('Y-m').'-26';
        // $bulan_sekarang = Carbon::now()->format('Y-m').'-25';

        if($month == 1 && $tgl_current_active <10){
            $thn_prev_active = $year-1;
            $bulan_kemarin="$thn_prev_active-11-26";
            $bulan_sekarang="$thn_prev_active-12-26";
        }elseif($month == 1 && $tgl_current_active>10 or $month == 2 && $tgl_current_active<10 && $bln_active != 3){
            $thn_prev_active = $year-1;
            $bulan_kemarin="$thn_prev_active-12-26";
            $bulan_sekarang="$thn_current_active-01-26";
        }else{
            $bln_prev_active = $month-1; 
            $thn_prev_active = $year;
            $bulan_kemarin="$thn_prev_active-$bln_prev_active-26";
            $bulan_sekarang="$thn_current_active-$bln_current_active-26";
        }

        $data['tunjangan_kerjas'] = $this->tunjanganKerja->select('nominal')->where('id',$data['karyawan']['tunjangan_kerja_id'])->first();
        if(empty($data['tunjangan_kerjas'])){
            $data['tunjangan_kerja'] = 0;
        }else{
            $data['tunjangan_kerja'] = $data['tunjangan_kerjas']['nominal'];
        }
        //jumlah alpa
        $data['alpa'] = $this->presensiInfo->where('pin',$data['karyawan']['pin'])
                                    ->whereBetween('scan_date',[$bulan_kemarin,$bulan_sekarang])
                                    ->where('status',7)
                                    ->get();
        //jumlah diliburkan
        $data['diliburkan'] = $this->presensiInfo->where('pin',$data['karyawan']['pin'])
                                    ->whereBetween('scan_date',[$bulan_kemarin,$bulan_sekarang])
                                    ->where('status',14)
                                    ->get();
        //jumlah cuti
        $data['cuti'] = $this->presensiInfo->where('pin',$data['karyawan']['pin'])
                                    ->whereBetween('scan_date',[$bulan_kemarin,$bulan_sekarang])
                                    ->where('status',13)
                                    ->get();
        //jumlah sakit
        $data['sakit'] = $this->presensiInfo->where('pin',$data['karyawan']['pin'])
                                    ->whereBetween('scan_date',[$bulan_kemarin,$bulan_sekarang])
                                    ->where('status',4)
                                    ->get();
        // dd($data['sakit']);
        //jumlah ijin full
        $data['ijin_full'] = $this->presensiInfo->where('pin',$data['karyawan']['pin'])
                                    ->whereBetween('scan_date',[$bulan_kemarin,$bulan_sekarang])
                                    ->where('status',6)
                                    ->get();
        //jumlah status terlambat
        $data['terlambats'] = $this->presensiInfo->where('pin',$data['karyawan']['pin'])
                                    ->whereBetween('scan_date',[$bulan_kemarin,$bulan_sekarang])
                                    ->where('status',3)
                                    ->get();
        $telat_1=0; //terlambat < 5 menit
        $telat_2=0; //terlambat > 5 menit < 15 menit
        $telat_3=0; //terlambat > 15 menit < 1 jam
        $telat_4=0; //terlambat > 1 jam < 3 jam	
        $telat_5=0; //terlambat > 3 jam	
        $data['telat_1'] = $telat_1;
        $data['telat_2'] = $telat_2;
        $data['telat_3'] = $telat_3;
        $data['telat_4'] = $telat_4;
        $data['telat_5'] = $telat_4;
        foreach ($data['terlambats'] as $key => $terlambat) {
            $explode_keterangan_terlambat = explode("@",$terlambat->keterangan);
            $jam_keterangan_terlambat=strtotime($explode_keterangan_terlambat[1]);
            $jam_datang_terlambat=strtotime(Carbon::parse($terlambat['scan_date'])->format('H:i'));
            // $jam_datang_terlambat=strtotime($terlambat['jam_datang_telat']);
            $menit_telat=(($jam_datang_terlambat-$jam_keterangan_terlambat)/60);
            // dd($menit_telat);
            if($menit_telat <= 4){
                $telat_1=$telat_1+1;
                $data['telat_1'] = $telat_1;
            }
            else if(($menit_telat >= 5) && ($menit_telat <= 15) ){
                $telat_2=$telat_2+1;
                $data['telat_2'] = $telat_2;
            }
            else if(($menit_telat >= 16) && ($menit_telat <= 60)){
                $telat_3=$telat_3+1;
                $data['telat_3'] = $telat_3;
            }
            else if(($menit_telat >= 61) && ($menit_telat <= 179)){
                $telat_4=$telat_4+1;
                $data['telat_4'] = $telat_4;
            }else if($menit_telat > 179){
                $telat_5=$telat_5+1;
                $data['telat_5'] = $telat_5;
            }
            // else{
            //     $telat_4=$telat_4+1;
            //     $data['telat_4'] = $telat_4;
            // }
        }

        //jumlah status pulang awal
        $data['pulang_awals'] = $this->presensiInfo->where('pin',$data['karyawan']['pin'])
                                    ->whereBetween('scan_date',[$bulan_kemarin,$bulan_sekarang])
                                    ->where(function($query) {
                                        $query->where('status','=',9)
                                            ->orWhere('status','=',10);
                                    })
                                    // ->where('status',9)
                                    // ->where('status',10)
                                    ->get();
        // dd($data['pulang_awals']);
        $pulang_1=0; //pulang awal < 4 jam
		$pulang_2=0; //tpulang awal > 4 jam
        $data['pulang_1'] = $pulang_1;
        $data['pulang_2'] = $pulang_2;
        foreach ($data['pulang_awals'] as $key => $pulang_awal) {
            $explode_pulang_seharusnya=explode("@",$pulang_awal->keterangan);
			$jam_pulang_seharusnya=strtotime($explode_pulang_seharusnya[0]);
			$jam_pulang_awal=strtotime(Carbon::parse($pulang_awal['scan_date'])->format('H:i'));
			// $jam_pulang_awal=strtotime($pulang_awal->jam_pulang_awal);
			$menit_sisa_jam_kerja=(($jam_pulang_seharusnya-$jam_pulang_awal)/60);
			if ($menit_sisa_jam_kerja<=300){
                $pulang_1=$pulang_1+1;
                $data['pulang_1'] = $pulang_1;
            }
			else{
                $pulang_2=$pulang_2+1;
                $data['pulang_2'] = $pulang_2;
            }
        }

        //jumlah ijin saat jam kerja
        $data['keluar_masuks'] = $this->keluarMasuk->where('nik',$data['karyawan']['nik'])
                                    ->whereBetween('tanggal_ijin',[$bulan_kemarin,$bulan_sekarang])
                                    ->get();
        // dd($data['keluar_masuks']);
        $ijin_15=0; //ijin kurang dari 15 menit
		$ijin_k4=0; //ijin > 15menit < 4 jam
		$ijin_l4=0; //ijin > 4 jam
        $data['ijin_15']=$ijin_15;
        $data['ijin_k4']=$ijin_k4;
        $data['ijin_l4']=$ijin_l4;
        foreach ($data['keluar_masuks'] as $key => $keluar_masuk) {
            $jam_keluar=strtotime($keluar_masuk->jam_keluar);
			$jam_datang=strtotime($keluar_masuk->jam_datang);
			$menit_keluar_masuk=(($jam_datang-$jam_keluar)/60);
			if ($menit_keluar_masuk<=15){
                $ijin_15=$ijin_15+1;
                $data['ijin_15']=$ijin_15;
            }
			else if (($menit_keluar_masuk>15)&&($menit_keluar_masuk<=239)){
                $ijin_k4=$ijin_k4+1;
                $data['ijin_k4']=$ijin_k4;
            }
			else {
                $ijin_l4=$ijin_l4+1;
                $data['ijin_l4']=$ijin_l4;
            }
        }

        //lihat tanggal masuk
        // $data['log_posisi'] = $this->logPosisi->select('tanggal')->where('nik',$nik)->first();
		$selisih_tanggal_masuk=strtotime($bulan_kemarin)-strtotime($data['karyawan']['tanggal']);
		if ($selisih_tanggal_masuk>=0)$div_tk=0;else $div_tk=75000;

        $total_potongan_tk=(75000*$data['alpa']->count())+(75000*$data['diliburkan']->count())+(75000*$data['sakit']->count())+(75000*$data['cuti']->count())+(75000*$data['ijin_full']->count())+($data['ijin_15']*25000)+($data['ijin_k4']*40000)+
        ($data['ijin_l4']*75000)+($data['pulang_1']*40000)+($data['pulang_2']*75000)+($data['telat_1']*15000)+($data['telat_2']*25000)+($data['telat_3']*30000)+($data['telat_4']*40000)+$div_tk;
        if ($total_potongan_tk>75000){$total_potongan_tk=75000;}else {}

        // if ($data['pengerjaan_weekly']['tunjangan_kehadiran'] == 75000) {
        //     $data['total_potongan_tk']=0;
        // }
        // // elseif($data['pengerjaan_weekly']['tunjangan_kehadiran'] < 75000){
        // //     $data['total_potongan_tk']=$data['pengerjaan_weekly']['tunjangan_kehadiran'];
        // // }
        // else{
        //     $data['total_potongan_tk']=$total_potongan_tk; 
        // }

        $data['total_potongan_tk']=$total_potongan_tk;
        return view('backend.pengerjaan.packing_ambri_isi_ambri.input_hasil_kerja_karyawan',$data);
    }

    public function hasil_kerja_karyawan_ambri_isi_ambri_view_simpan(Request $request, $id, $kode_pengerjaan, $nik)
    {
        // $input = $request->all();
        if($id == 1){
            $kode_jenis_operator_detail = 'L';
        }
        elseif($id == 2){
            $kode_jenis_operator_detail = 'E';
        }
        elseif($id == 3){
            $kode_jenis_operator_detail = 'A';
        }

        $data['id'] = $id;
        $data['kode_pengerjaan'] = $kode_pengerjaan;
        $data['nik'] = $nik;

        $input['upah_dasar'] = $request->upah_dasar;
        $input['upah_makan'] = $request->upah_makan;
        
        $input['uang_makan'] = $request->uang_makan;
        $input['plus_1'] = $request->plus_1."|".$request->keterangan_plus_1;
        $input['plus_2'] = $request->plus_2."|".$request->keterangan_plus_2;
        $input['plus_3'] = $request->plus_3."|".$request->keterangan_plus_3;
        
        $input['minus_1'] = $request->minus_1."|".$request->keterangan_minus_1;
        $input['minus_2'] = $request->minus_2."|".$request->keterangan_minus_2;

        if ($request->akhir_bulan == 'n') {
            $input['tunjangan_kerja'] = 0;
            $input['tunjangan_kehadiran'] = 0;
        }else{
            $input['tunjangan_kerja'] = $request->tunjangan_kerja;
            $input['tunjangan_kehadiran'] = 75000-$request->pot_tunjangan_kehadiran;
        }

        if ($request->check_jht == 'on') {
            $input['jht'] = $request->jht;
        }else{
            $input['jht'] = 0;
        }

        if($request->check_bpjs_kesehatan == 'on'){
            $input['bpjs_kesehatan'] = $request->bpjs_kesehatan;
        }else{
            $input['bpjs_kesehatan'] = 0;
        }
        // dd($input);
        $operator_karyawan = $this->karyawanOperator->select('id')->where('nik',$nik)->where('status','Y')->where('jenis_operator_detail_pekerjaan_id',11)->orderBy('id','desc')->first();
        $pengerjaan_weekly = $this->pengerjaanWeekly->where('kode_payrol',substr($kode_pengerjaan,0,2).$kode_jenis_operator_detail.'_'.substr($kode_pengerjaan,3))
                                            ->where('operator_karyawan_id',$operator_karyawan->id)
                                            ->first();
        // $pengerjaan_weekly->update($input);
        if (!empty($pengerjaan_weekly)) {
            $pengerjaan_weekly->update($input);
            return response()->json([
                'success' => true,
                'message_title' => 'Berhasil',
                'message_content' => 'Data Berhasil Disimpan'
            ]);
        }else{
            return response()->json([
                'success' => false,
                'message_title' => 'Gagal',
                'message_content' => 'Data Tidak Berhasil Disimpan'
            ]);
        }
        // dd($pengerjaan_weekly);
    }

    public function hasil_kerja_harian_marketing($id, $kode_pengerjaan)
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
        $data['id'] = $id;
        $data['kode_pengerjaan'] = $kode_pengerjaan;
        $data['jenis_operator_detail_pekerjaan'] = $this->jenisOperatorDetailPengerjaan->find(12);
        $data['new_data_pengerjaan'] = $this->newDataPengerjaan->where('kode_pengerjaan',$kode_pengerjaan)->first();
        $data['explode_tanggal_pengerjaans'] = explode('#',$data['new_data_pengerjaan']['tanggal']);
        // dd($data['hasil_harian_tanggal_pengerjaan']);
        // $data['karyawan_operator_harians'] = KaryawanOperatorHarian::select([
        //                                                           'operator_harian_karyawan.id as id',
        //                                                           'operator_harian_karyawan.nik as nik',
        //                                                           'biodata_karyawan.nama as nama',
        //                                                         ])
        //                                                         ->leftJoin('biodata_karyawan','biodata_karyawan.nik','=','operator_harian_karyawan.nik')
        //                                                         ->where('operator_harian_karyawan.jenis_operator_detail_pekerjaan_id',12)
        //                                                         ->where('operator_harian_karyawan.status','y')
        //                                                         ->get();
        $data['pengerjaan_harians'] = $this->pengerjaanHarian->select([
                                                        'pengerjaan_harian.id as id',
                                                        'pengerjaan_harian.operator_harian_karyawan_id as operator_harian_karyawan_id',
                                                        'operator_harian_karyawan.nik as nik',
                                                        'biodata_karyawan.nama as nama',
                                                        'pengerjaan_harian.uang_makan as uang_makan',
                                                        'pengerjaan_harian.upah_dasar_weekly as upah_dasar_weekly',
                                                        'pengerjaan_harian.plus_1 as plus_1',
                                                        'pengerjaan_harian.plus_2 as plus_2',
                                                        'pengerjaan_harian.plus_3 as plus_3',
                                                        'pengerjaan_harian.minus_1 as minus_1',
                                                        'pengerjaan_harian.minus_2 as minus_2',
                                                        'pengerjaan_harian.jht as jht',
                                                        'pengerjaan_harian.bpjs_kesehatan as bpjs_kesehatan',
                                                        'pengerjaan_harian.lembur as lembur',
                                                        'pengerjaan_harian.hasil_kerja as hasil_kerja',
                                                        'pengerjaan_harian.tunjangan_kerja as tunjangan_kerja',
                                                        'pengerjaan_harian.tunjangan_kehadiran as tunjangan_kehadiran',
                                                        'operator_harian_karyawan.tunjangan_kerja_id as tunjangan_kerja_id',
                                                    ])
                                                    ->leftJoin('operator_harian_karyawan','operator_harian_karyawan.id','=','pengerjaan_harian.operator_harian_karyawan_id')
                                                    ->leftJoin('itic_emp_new.biodata_karyawan','biodata_karyawan.nik','=','operator_harian_karyawan.nik')
                                                    ->where('operator_harian_karyawan.jenis_operator_detail_pekerjaan_id',12)
                                                    ->where('kode_pengerjaan',$kode_pengerjaan)
                                                    ->where('operator_harian_karyawan.status','y')
                                                    ->orderBy('biodata_karyawan.nama','asc')
                                                    ->get();
        // dd($data['pengerjaan_harians']);
        return view('backend.pengerjaan.harian.marketing.index',$data);
    }

    public function hasil_kerja_harian_marketing_view($id, $kode_pengerjaan, $nik, $month, $year)
    {
        $data['id'] = $id;
        $data['nik'] = $nik;
        $data['kode_pengerjaan'] = $kode_pengerjaan;
        $data['month'] = $month;
        $data['year']= $year;

        if($id == 4){
            $kode_jenis_operator_detail = 'H';
        }

        $data['karyawan_harian'] = $this->karyawanOperatorHarian->select([
                                                            'operator_harian_karyawan.id as id',
                                                            'operator_harian_karyawan.nik as nik',
                                                            'biodata_karyawan.nama as nama',
                                                            'biodata_karyawan.pin as pin',
                                                            'biodata_karyawan.tanggal_masuk as tanggal',
                                                            'operator_harian_karyawan.upah_dasar as upah_dasar',
                                                            'pengerjaan_harian.upah_dasar_weekly as upah_dasar_weekly',
                                                            'pengerjaan_harian.hari_kerja as hari_kerja',
                                                            'pengerjaan_harian.hasil_kerja as hasil_kerja',
                                                            'pengerjaan_harian.tunjangan_kerja as tunjangan_kerja',
                                                            'operator_harian_karyawan.tunjangan_kerja_id as tunjangan_kerja_id',
                                                            'pengerjaan_harian.plus_1 as plus_1',
                                                            'pengerjaan_harian.plus_2 as plus_2',
                                                            'pengerjaan_harian.plus_3 as plus_3',
                                                            'pengerjaan_harian.minus_1 as minus_1',
                                                            'pengerjaan_harian.minus_2 as minus_2',
                                                            'pengerjaan_harian.minus_3 as minus_3',
                                                            'pengerjaan_harian.uang_makan as uang_makan',
                                                            'pengerjaan_harian.lembur as lembur',
                                                            'operator_harian_karyawan.jht as jht',
                                                            'operator_harian_karyawan.bpjs as bpjs',
                                                            'operator_harian_karyawan.status as status',
                                                        ])
                                                        ->with('tunjangan_kerja_nominal')
                                                        ->leftJoin('itic_emp_new.biodata_karyawan','biodata_karyawan.nik','=','operator_harian_karyawan.nik')
                                                        ->rightJoin('pengerjaan_harian','pengerjaan_harian.operator_harian_karyawan_id','=','operator_harian_karyawan.id')
                                                        ->where('operator_harian_karyawan.nik',$nik)
                                                        ->where('operator_harian_karyawan.status','y')
                                                        ->where('pengerjaan_harian.kode_pengerjaan',$kode_pengerjaan)
                                                        ->first();
        
        $data['new_data_pengerjaan'] = $this->newDataPengerjaan->where('kode_pengerjaan',$kode_pengerjaan)->first();
        $data['explode_tanggal_pengerjaans'] = explode('#',$data['new_data_pengerjaan']['tanggal']);
        // dd($data['explode_tanggal_pengerjaans']);

        if (empty($data['karyawan_harian']['plus_1'])) {
            $plus_1=null;
            $ket_plus_1=null;
        }else{
            $exp_plus1=explode("|",$data['karyawan_harian']['plus_1']);
            // dd($exp_plus1);
            $plus_1=$exp_plus1[0];
            $ket_plus_1=$exp_plus1[1];
        }

        if (empty($data['karyawan_harian']['plus_2'])) {
            $plus_2=null;
            $ket_plus_2=null;
        }else{
            $exp_plus2=explode("|",$data['karyawan_harian']['plus_2']);
            $plus_2=$exp_plus2[0];
            $ket_plus_2=$exp_plus2[1];
        }

        if (empty($data['karyawan_harian']['plus_3'])) {
            $plus_3=null;
            $ket_plus_3=null;
        }else{
            $exp_plus3=explode("|",$data['karyawan_harian']['plus_3']);
            $plus_3=$exp_plus2[0];
            $ket_plus_3=$exp_plus2[1];
        }

        if (empty($data['karyawan_harian']['minus_1'])) {
            $minus_1=null;
            $ket_minus_1=null;
        }else{
            $exp_minus1=explode("|",$data['karyawan_harian']['minus_1']);
            $minus_1=$exp_minus1[0];
            $ket_minus_1=$exp_minus1[1];
        }

        if (empty($data['karyawan_harian']['minus_2'])) {
            $minus_2=null;
            $ket_minus_2=null;
        }else{
            $exp_minus2=explode("|",$data['karyawan_harian']['minus_2']);
            $minus_2=$exp_minus2[0];
            $ket_minus_2=$exp_minus2[1];
        }

        $data['jhts'] = $this->bpjsJht->where('status','y')->get();
        $data['bpjs_kesehatan'] = $this->bpjsKesehatan->select('nominal')->where('status','y')->first();

        $data['pengerjaan_harian_weekly'] = $this->pengerjaanHarian->where('operator_harian_karyawan_id',$data['karyawan_harian']['id'])
                                                            ->where('kode_payrol',$kode_pengerjaan)
                                                            ->first();
        // dd($data['pengerjaan_harian_weekly']);
        // if(!empty($data['pengerjaan_harian_weekly'])){
        // }
        $exp_lembur = explode("@",$data['pengerjaan_harian_weekly']['lembur']);
        // dd($exp_lembur);
        if(empty($exp_lembur[0])){
            $exp_lembur[0]==null;
        }
        // if (empty($exp_lembur[0]))$exp_lembur[0]==null;
        // if (empty($exp_lembur[1]))$exp_lembur[1]==null;
        // if (empty($exp_lembur[2]))$exp_lembur[2]==null;
        //Hitung Masa Kerja
        // $data['karyawan_masa_kerja'] = DB::connection('emp')->table('log_posisi')
        //                                                     ->select('tanggal')
        //                                                     ->where('nik',$nik)
        //                                                     ->first();
                                                            
        $awal  = new DateTime($data['karyawan_harian']->tanggal);
        $akhir = new DateTime(); // Waktu sekarang
        $diff  = $awal->diff($akhir);
        $data['masa_kerja'] = $diff->y.' Tahun '.$diff->m.' Bulan '.$diff->d.' Hari';
        $data['masa_kerja_tahun'] = $diff->y;
        $data['masa_kerja_hari'] = $diff->d;

        $data['upah_dasar_karyawan'] = [];
        foreach ($data['jhts'] as $key => $jht) {
            if ($data['masa_kerja_tahun'] > 15) {
                if ($jht->urutan == 3) {
                    $data['upah_dasar_karyawan'] = ($data['bpjs_kesehatan']['nominal'] + 100000)/25;
                }
            }
            elseif($data['masa_kerja_tahun'] >= 10 && $data['masa_kerja_tahun'] <= 15 && $data['masa_kerja_hari'] >= 1){
                if ($jht->urutan == 2) {
                    $data['upah_dasar_karyawan'] = ($data['bpjs_kesehatan']['nominal'] + 50000)/25;
                }
            }
            elseif($data['masa_kerja_tahun'] <= 10 || $data['masa_kerja_hari'] >= 1){
                if ($jht->urutan == 1) {
                    $data['upah_dasar_karyawan'] = ($data['bpjs_kesehatan']['nominal'] + 0)/25;
                }
            }
        }

        // dd(number_format($data['upah_dasar_karyawan'],0,',','.'));

        // dd($data['masa_kerja_tahun']);

        $tgl_current_active = Carbon::now()->format('d');
        $bln_active = Carbon::now()->format('m');
        $bln_current_active = $month;
        $thn_current_active = $year;

        if($month == 1 && $tgl_current_active <10){
            $thn_prev_active = $year-1;
            $bulan_kemarin="$thn_prev_active-11-26";
            $bulan_sekarang="$thn_prev_active-12-26";
        }elseif($month == 1 && $tgl_current_active>10 or $month == 2 && $tgl_current_active<10 && $bln_active != 3){
            $thn_prev_active = $year-1;
            $bulan_kemarin="$thn_prev_active-12-26";
            $bulan_sekarang="$thn_current_active-01-26";
        }else{
            $bln_prev_active = $month-1; 
            $thn_prev_active = $year;
            $bulan_kemarin="$thn_prev_active-$bln_prev_active-26";
            $bulan_sekarang="$thn_current_active-$bln_current_active-26";
        }

        $data['tunjangan_kerjas'] = $this->tunjanganKerja->select('nominal')->where('id',$data['karyawan_harian']['tunjangan_kerja_id'])->first();
        if(empty($data['tunjangan_kerjas'])){
            $data['tunjangan_kerja'] = 0;
        }else{
            $data['tunjangan_kerja'] = $data['tunjangan_kerjas']['nominal'];
        }

        //jumlah alpa
        $data['alpa'] = $this->presensiInfo->where('pin',$data['karyawan_harian']['pin'])
                                    ->whereBetween('scan_date',[$bulan_kemarin,$bulan_sekarang])
                                    ->where('status',7)
                                    ->get();
        //jumlah diliburkan
        $data['diliburkan'] = $this->presensiInfo->where('pin',$data['karyawan_harian']['pin'])
                                    ->whereBetween('scan_date',[$bulan_kemarin,$bulan_sekarang])
                                    ->where('status',14)
                                    ->get();
        //jumlah cuti
        $data['cuti'] = $this->presensiInfo->where('pin',$data['karyawan_harian']['pin'])
                                    ->whereBetween('scan_date',[$bulan_kemarin,$bulan_sekarang])
                                    ->where('status',13)
                                    ->get();
        //jumlah sakit
        $data['sakit'] = $this->presensiInfo->where('pin',$data['karyawan_harian']['pin'])
                                    ->whereBetween('scan_date',[$bulan_kemarin,$bulan_sekarang])
                                    ->where('status',4)
                                    ->get();
        // dd($data['sakit']);
        //jumlah ijin full
        $data['ijin_full'] = $this->presensiInfo->where('pin',$data['karyawan_harian']['pin'])
                                    ->whereBetween('scan_date',[$bulan_kemarin,$bulan_sekarang])
                                    ->where('status',6)
                                    ->get();
        //jumlah status terlambat
        $data['terlambats'] = $this->presensiInfo->where('pin',$data['karyawan_harian']['pin'])
                                    ->whereBetween('scan_date',[$bulan_kemarin,$bulan_sekarang])
                                    ->where('status',3)
                                    ->get();
        $telat_1=0; //terlambat < 5 menit
        $telat_2=0; //terlambat > 5 menit < 15 menit
        $telat_3=0; //terlambat > 15 menit < 1 jam
        $telat_4=0; //terlambat > 1 jam < 3 jam	
        $telat_5=0; //terlambat > 3 jam	
        $data['telat_1'] = $telat_1;
        $data['telat_2'] = $telat_2;
        $data['telat_3'] = $telat_3;
        $data['telat_4'] = $telat_4;
        $data['telat_5'] = $telat_4;
        foreach ($data['terlambats'] as $key => $terlambat) {
            $explode_keterangan_terlambat = explode("@",$terlambat->keterangan);
            $jam_keterangan_terlambat=strtotime($explode_keterangan_terlambat[1]);
            $jam_datang_terlambat=strtotime(Carbon::parse($terlambat['scan_date'])->format('H:i'));
            // $jam_datang_terlambat=strtotime($terlambat['jam_datang_telat']);
            $menit_telat=(($jam_datang_terlambat-$jam_keterangan_terlambat)/60);
            // dd($menit_telat);
            if($menit_telat <= 4){
                $telat_1=$telat_1+1;
                $data['telat_1'] = $telat_1;
            }
            else if(($menit_telat >= 5) && ($menit_telat <= 15) ){
                $telat_2=$telat_2+1;
                $data['telat_2'] = $telat_2;
            }
            else if(($menit_telat >= 16) && ($menit_telat <= 60)){
                $telat_3=$telat_3+1;
                $data['telat_3'] = $telat_3;
            }
            else if(($menit_telat >= 61) && ($menit_telat <= 179)){
                $telat_4=$telat_4+1;
                $data['telat_4'] = $telat_4;
            }else if($menit_telat > 179){
                $telat_5=$telat_5+1;
                $data['telat_5'] = $telat_5;
            }
            // else{
            //     $telat_4=$telat_4+1;
            //     $data['telat_4'] = $telat_4;
            // }
        }

        //jumlah status pulang awal
        $data['pulang_awals'] = $this->presensiInfo->where('pin',$data['karyawan_harian']['pin'])
                                    ->whereBetween('scan_date',[$bulan_kemarin,$bulan_sekarang])
                                    ->where(function($query) {
                                        $query->where('status','=',9)
                                            ->orWhere('status','=',10);
                                    })
                                    // ->where('status',9)
                                    // ->where('status',10)
                                    ->get();
        // dd($data['pulang_awals']);
        $pulang_1=0; //pulang awal < 4 jam
		$pulang_2=0; //tpulang awal > 4 jam
        $data['pulang_1'] = $pulang_1;
        $data['pulang_2'] = $pulang_2;
        foreach ($data['pulang_awals'] as $key => $pulang_awal) {
            $explode_pulang_seharusnya=explode("@",$pulang_awal->keterangan);
			$jam_pulang_seharusnya=strtotime($explode_pulang_seharusnya[3]);
			$jam_pulang_awal=strtotime(Carbon::parse($pulang_awal['scan_date'])->format('H:i'));
			// $jam_pulang_awal=strtotime($pulang_awal->jam_pulang_awal);
			$menit_sisa_jam_kerja=(($jam_pulang_seharusnya-$jam_pulang_awal)/60);
			if ($menit_sisa_jam_kerja<=300){
                $pulang_1=$pulang_1+1;
                $data['pulang_1'] = $pulang_1;
            }
			else{
                $pulang_2=$pulang_2+1;
                $data['pulang_2'] = $pulang_2;
            }
        }

        //jumlah ijin saat jam kerja
        $data['keluar_masuks'] = $this->keluarMasuk->where('nik',$data['karyawan_harian']['nik'])
                                    ->whereBetween('tanggal_ijin',[$bulan_kemarin,$bulan_sekarang])
                                    ->get();
        // dd($data['keluar_masuks']);
        $ijin_15=0; //ijin kurang dari 15 menit
		$ijin_k4=0; //ijin > 15menit < 4 jam
		$ijin_l4=0; //ijin > 4 jam
        $data['ijin_15']=$ijin_15;
        $data['ijin_k4']=$ijin_k4;
        $data['ijin_l4']=$ijin_l4;
        foreach ($data['keluar_masuks'] as $key => $keluar_masuk) {
            $jam_keluar=strtotime($keluar_masuk->jam_keluar);
			$jam_datang=strtotime($keluar_masuk->jam_datang);
			$menit_keluar_masuk=(($jam_datang-$jam_keluar)/60);
			if ($menit_keluar_masuk<=15){
                $ijin_15=$ijin_15+1;
                $data['ijin_15']=$ijin_15;
            }
			else if (($menit_keluar_masuk>15)&&($menit_keluar_masuk<=239)){
                $ijin_k4=$ijin_k4+1;
                $data['ijin_k4']=$ijin_k4;
            }
			else {
                $ijin_l4=$ijin_l4+1;
                $data['ijin_l4']=$ijin_l4;
            }
        }

        //lihat tanggal masuk

        // $data['log_posisi'] = $this->logPosisi->select('tanggal')->where('nik',$nik)->first();
		$selisih_tanggal_masuk=strtotime($bulan_kemarin)-strtotime($data['karyawan_harian']['tanggal']);
		if ($selisih_tanggal_masuk>=0)$div_tk=0;else $div_tk=75000;

        $total_potongan_tk=(75000*$data['alpa']->count())+(75000*$data['diliburkan']->count())+(75000*$data['sakit']->count())+(75000*$data['cuti']->count())+(75000*$data['ijin_full']->count())+($data['ijin_15']*25000)+($data['ijin_k4']*40000)+
        ($data['ijin_l4']*75000)+($data['pulang_1']*40000)+($data['pulang_2']*75000)+($data['telat_1']*15000)+($data['telat_2']*25000)+($data['telat_3']*30000)+($data['telat_4']*40000)+$div_tk;
        if ($total_potongan_tk>75000){$total_potongan_tk=75000;}else {}

        // if ($data['pengerjaan_harian_weekly']['tunjangan_kerja'] == 75000) {
        //     $data['total_potongan_tk']=0;
        // }
        // // elseif($data['pengerjaan_harian_weekly']['tunjangan_kerja'] < 75000){
        // //     $data['total_potongan_tk']=$data['pengerjaan_harian_weekly']['tunjangan_kerja'];
        // // }
        // else{
        //     $data['total_potongan_tk']=$total_potongan_tk; 
        // }

        $data['total_potongan_tk']=$total_potongan_tk;

        // dd($data);
        // $exp_tanggal = array_filter(explode("#",$data['new_data_pengerjaan']['tanggal']));
        // $a = count($exp_tanggal);
        // $a=$a-2;
        // $exp_tgl_awal = explode("-",$exp_tanggal[1]);
        // $exp_tgl_akhir = explode("-",$exp_tanggal[$a]);
        // $explode_posting = explode("-",$data['new_data_pengerjaan']['date']);
        // // dd($exp_tgl_awal);
        // for ($b=1;$b<=$a;$b++) { 
        //     $exp_per_tanggal = explode("-",$exp_tanggal[$b]);
        //     // dd($exp_per_tanggal);
        //     $m_date=$exp_per_tanggal[1];
        //     $d_date=$exp_per_tanggal[2];

        //     if (($exp_tgl_awal[2]<>$exp_tgl_akhir[2])&&($exp_per_tanggal[2]==1)) {
        //         if ($explode_posting[1]==12) {
        //             $y_date=$explode_posting[0]+1;
        //         }else{
        //             $y_date=$explode_posting[0];
        //         }
        //     }else{
        //         if (($explode_posting[1]==1)&&($exp_per_tanggal[1]<=9)) {
        //             $y_date=$explode_posting[0]-1;
        //         }else{
        //             $y_date=$explode_posting[0];
        //         }
        //     }
        //     $tgl_digunakan=$y_date."-".$m_date."-".$d_date;
        //     // dd($tgl_digunakan);

        //     /*##PEMOTONGAN GAJI BERDASARKAN JAM KERJA*/
		// 			//DARI TABEL IJIN KELUAR MASUK
        //     $get_km = KeluarMasuk::where('nik',$data['karyawan_harian']['nik'])
        //                         ->where('tanggal_ijin',$tgl_digunakan)
        //                         ->first();
        //     if (!empty($get_km)) {
        //         $jk_per_nik = strtotime($get_km->jam_keluar);
        //         $jd_per_nik = strtotime($get_km->jam_datang);
        //         $ist_awal_per_nik = strtotime($get_km->jam_istirahat);
        //         $ist_akhir_per_nik = strtotime($get_km->jam_istirahat);					
        //         $assembly_istirahat_akhir = explode(":", $get_km->jam_istirahat);
        //         $ist_akhir_per_nik = strtotime($assembly_istirahat_akhir[0].":59:59");
        //         if($jk_per_nik >= $ist_awal_per_nik && $jk_per_nik <= $ist_akhir_per_nik) $jk_per_nik = strtotime("13:00:00");
        //         if($jd_per_nik >= $ist_awal_per_nik && $jd_per_nik <= $ist_akhir_per_nik) $jd_per_nik = strtotime("11:59:59");
        //         if($jk_per_nik < $ist_awal_per_nik && $jd_per_nik > $ist_akhir_per_nik) $durasi_ijin = (($jd_per_nik-$jk_per_nik)/60)-1;
        //         else $durasi_ijin = ($jd_per_nik-$jk_per_nik)/60;
        //         $jam_ijin=floor($durasi_ijin/60);
        //         //ket : 1 menit normal dihitung 1.666 menit (1 jam = 100 menit)
        //         $menit_ijin=($durasi_ijin-($jam_ijin*60))/100*1.666;
        //     }else{
        //         $jam_ijin=0;
        //         $menit_ijin=0;
        //     }
        //     $total_ijin=$jam_ijin+$menit_ijin;
        //     // dd($total_ijin);
            
        //     //Terlambat - Pribadi
        //     $get_terlambat_per_tanggals = PresensiInfo::where('pin',$data['karyawan_harian']['pin'])
        //                                             ->where('scan_date','LIKE',"%$tgl_digunakan%")
        //                                             ->where('status',3)
        //                                             ->first();
        //     if(!empty($get_terlambat_per_tanggals)){
        //         $exp_keterangan_terlambat=explode("@",$get_terlambat_per_tanggals->keterangan);
        //         $jam_ket_terlambat=strtotime($exp_keterangan_terlambat[1]);
        //         $jam_dtg_terlambat=strtotime($get_terlambat_per_tanggals->jam_datang_telat);
        //         $menit_telat_per_tanggal=(($jam_dtg_terlambat-$jam_ket_terlambat)/60);
        //         $jam_terlambat=floor($menit_telat_per_tanggal/60);
        //         $menit_terlambat=($menit_telat_per_tanggal-($jam_terlambat*60))/100*1.666;
        //         // foreach ($get_terlambat_per_tanggals->get() as $set_terlambat_per_tanggal) {
        //         //     $exp_keterangan_terlambat=explode("@",$set_terlambat_per_tanggal->keterangan);
		// 		// 	$jam_ket_terlambat=strtotime($exp_keterangan_terlambat[1]);
		// 		// 	$jam_dtg_terlambat=strtotime($set_terlambat_per_tanggal->jam_datang_telat);
		// 		// 	$menit_telat_per_tanggal=(($jam_dtg_terlambat-$jam_ket_terlambat)/60);
		// 		// 	$jam_terlambat=floor($menit_telat_per_tanggal/60);
		// 		// 	$menit_terlambat=($menit_telat_per_tanggal-($jam_terlambat*60))/100*1.666;
        //         // }
        //     }else{
        //         $jam_terlambat=0;
        //         $menit_terlambat=0;
        //     }
		// 	$total_terlambat=$jam_terlambat+$menit_terlambat;
            
		// 	//9	Pulang Awal - Pribadi & 10	Pulang Awal - Sakit			
        //     $get_pulang_awal_per_tanggal = PresensiInfo::where('pin',$data['karyawan_harian']['pin'])
        //                                             ->where('scan_date','LIKE',"%$tgl_digunakan%")
        //                                             ->where('status',9)
        //                                             ->orWhere('status',10)
        //                                             ->first();
        //     // dd($tgl_digunakan);
        //     if(!empty($get_pulang_awal_per_tanggal)){
        //         $jam_pulang = strtotime($get_pulang_awal_per_tanggal->scan_date);
        //         // dd($jam_pulang);
        //         if (mb_ereg("@",$get_pulang_awal_per_tanggal->keterangan)) {
        //             $split_keterangan = explode("@", $get_pulang_awal_per_tanggal->keterangan);
		// 			$penyesuaian_istirahat = explode(":", $split_keterangan[2]);
        //             $penyesuaian_pulang = explode(":", $split_keterangan[3]); 

        //             $jam_istirahat = $split_keterangan[2];
        //             $jam_istirahat = strtotime($jam_istirahat);

        //             $max_pulang = strtotime($split_keterangan[3]);
        //             // dd($max_pulang);
        //         }else{
        //             $max_pulang = strtotime("17:00");
        //             $jam_istirahat = strtotime("12:00");
        //         }
        //         // dd($max_pulang);

		// 		// $pulang_dijam_istirahat=$total_ijin;
		// 		// $pulang_dijam_istirahat=($jam_pulang-$jam_istirahat)/60;
        //         // dd($pulang_dijam_istirahat);
        //         if(($jam_pulang <= $jam_istirahat)||($pulang_dijam_istirahat<=60))  
        //         {
        //             $durasi = (($max_pulang-$jam_pulang)/60)-60;
        //         }else{
        //             $durasi = ($max_pulang-$jam_pulang)/60;
        //         }

        //         $jam_plg=floor(($durasi)/60);
        //         // dd($durasi);
		// 		$menit_plg=($durasi-($jam_plg*60))/100*1.666;

        //     }else{
        //         $jam_plg=0;
        //         $menit_plg=0;
        //     }
        //     $total_plg_awal=$jam_plg+$menit_plg;
        //     // dd($jam_plg);
        //     $checklog=FtmAttLog::where('scan_date','LIKE',"%$tgl_digunakan%")
        //                     ->where('pin',$data['karyawan_harian']['pin'])
        //                     ->first();
        //     $presensi=PresensiInfo::where('scan_date','LIKE',"%$tgl_digunakan%")
        //                         ->where('pin',$data['karyawan_harian']['pin'])
        //                         ->where('status',1)
        //                         ->orWhere('status',2)
        //                         ->orWhere('status',3)
        //                         ->orWhere('status',5)
        //                         ->orWhere('status',8)
        //                         ->orWhere('status',9)
        //                         ->orWhere('status',10)
        //                         ->first();
        //     // dd($presensi);
        //     if(!empty($checklog)||!empty($presensi)){
        //         if ($data['karyawan_harian']['hari_kerja']==5) {
        //             $jam_kerja_seharusnya=8;
        //         }else{
        //             $jam_kerja_seharusnya=7;
        //         }
        //         //##lihat jam kerja per nik per hari
		// 		$jam_kerja=$jam_kerja_seharusnya-$total_ijin-$total_terlambat-$total_plg_awal;
        //         $exp_menit=explode(".",$jam_kerja);
        //         $menit_kerja=(("0.".$exp_menit[1])*60);

		// 		//############## JIKA DATA HARIAN KOSONG
        //         if (empty($data['pengerjaan_harian_weekly']['hasil_kerja'])) {
        //             $post_jam_kerja= $jam_kerja/$jam_kerja_seharusnya;
        //         }else{
        //             $array_b=$b-1;
        //             $explode_isi_jam_kerja=explode("|",$data['pengerjaan_harian_weekly']['hasil_kerja']);
		// 			$post_jam_kerja=$explode_isi_jam_kerja[$array_b];
        //         }
        //         // dd($jam_kerja);
        //     }else{
        //         if (empty($data['pengerjaan_harian_weekly']['hasil_kerja'])) {
        //             $jam_kerja=0;$menit_kerja=0;
        //             if ($b==$a)$post_jam_kerja=1;
        //             else $post_jam_kerja=0;
        //         }else{
        //             $jam_kerja=0;$menit_kerja=0;
        //             $array_b=$b-1;
        //             $explode_isi_jam_kerja=explode("|",$set_weekly[hasil_kerja]);
        //             $post_jam_kerja=$explode_isi_jam_kerja[$array_b];
        //         }
        //     }
        // }
        // $data['jam_kerja'] = $jam_kerja;


        // dd($jam_kerja);
        // dd($exp_tgl_akhir);
        // dd($exp_tanggal);

        // $data_keluar_masuks = KeluarMasuk::where('nik',$data['karyawan_harian']['nik'])
        //                                     // ->where('tanggal_ijin','2023-06-18')
        //                                     ->get();
        // if ($data_keluar_masuks->count()>0) {
        //     $jk_nik = strtotime($data_keluar_masuks[0]->jam_keluar);
        //     $jd_nik = strtotime($data_keluar_masuks[0]->jam_datang);
        //     $ist_nik = strtotime($data_keluar_masuks[0]->jam_istirahat);
        //     if ($jk_nik >= $ist_nik && $jk_nik <= $ist_nik) {
        //         $jk_nik = strtotime("13:00:00");
        //     }
        //     if ($jd_nik >= $ist_nik && $jd_nik <= $ist_nik) {
        //         $jd_nik = strtotime("11:59:59");
        //     }
        //     if($jk_nik < $ist_nik && $jd_nik > $ist_nik){
        //         $durasi_ijin = (($jd_nik-$jk_nik)/60)-1;
        //     }else{
        //         $durasi_ijin = ($jd_nik-$jk_nik)/60;
        //     }
        //     $jam_ijin=floor($durasi_ijin/60);
        //     //ket : 1 menit normal dihitung 1.666 menit (1 jam = 100 menit)
        //     $menit_ijin=($durasi_ijin-($jam_ijin*60))/100*1.666;

        //     // dd($jk_nik);
        //     // foreach ($data_keluar_masuks as $key => $data_keluar_masuk) {
        //     //     $jk_nik = strtotime($data_keluar_masuk->jam_keluar);
        //     //     $jd_nik = strtotime($data_keluar_masuk->jam_datang);
        //     //     $ist_nik = strtotime($data_keluar_masuk->jam_istirahat);
        //     //     if ($jk_nik >= $ist_nik && $jk_nik <= $ist_nik) {
        //     //         $jk_per_nik = strtotime("13:00:00");
        //     //     }
        //     //     if ($jd_nik >= $ist_nik && $jd_nik <= $ist_nik) {
        //     //         $jd_per_nik = strtotime("11:59:59");
        //     //     }
		// 	//     if($jk_per_nik < $ist_awal_per_nik && $jd_per_nik > $ist_akhir_per_nik){
        //     //         $durasi_ijin = (($jd_per_nik-$jk_per_nik)/60)-1;
        //     //     }else{
        //     //         $durasi_ijin = ($jd_per_nik-$jk_per_nik)/60;
        //     //     }
        //     //     $jam_ijin=floor($durasi_ijin/60);
        //     //     //ket : 1 menit normal dihitung 1.666 menit (1 jam = 100 menit)
        //     //     $menit_ijin=($durasi_ijin-($jam_ijin*60))/100*1.666;
        //     // }
        //     // dd($jk_nik);
        // }else{
        //     $jam_ijin=0; 
        //     $menit_ijin=0;
        // }
        // $total_ijin=$jam_ijin+$menit_ijin;
        // // dd($data['explode_tanggal_pengerjaans']);

        // // $data_terlambats = PresensiInfo::where('pin',$data['karyawan_harian']['pin'])
        // //                             ->where('scan_date','LIKE',"%".$data['explode_tanggal_pengerjaans']."%")
        // //                             ->where('status',3)
        // //                             ->get();
        // // dd($data_terlambats);
        // // $exp_keterangan_terlambat=explode("@",$set_terlambat[keterangan]);
        // // $jam_ket_terlambat=strtotime($exp_keterangan_terlambat[1]);
        // // $jam_dtg_terlambat=strtotime($set_terlambat[jam_datang_telat]);
        // // $menit_telat_per_tanggal=(($jam_dtg_terlambat-$jam_ket_terlambat)/60);
        // // $jam_terlambat=floor($menit_telat_per_tanggal/60);
        // // $menit_terlambat=($menit_telat_per_tanggal-($jam_terlambat*60))/100*1.666;

        // if ($data['karyawan_harian']['hari_kerja'] == 5) {
        //     $jam_kerja_seharusnya = 8;
        // }else{
        //     $jam_kerja_seharusnya = 7;
        // }

        // $jam_kerja = $jam_kerja_seharusnya+$total_ijin;
        
        // dd($jam_kerja);
        return view('backend.pengerjaan.harian.marketing.input_hasil_kerja_karyawan',$data);
    }

    public function hasil_kerja_harian_marketing_simpan(Request $request, $id, $kode_pengerjaan, $tanggal)
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

        $input['uang_makan'] = $request->uang_makan;

        $input['plus_1'] = $request->plus_1."|".$request->keterangan_plus_1;
        $input['plus_2'] = $request->plus_2."|".$request->keterangan_plus_2;
        $input['plus_3'] = $request->plus_3."|".$request->keterangan_plus_3;

        $input['minus_1'] = $request->minus_1."|".$request->keterangan_minus_1;
        $input['minus_2'] = $request->minus_2."|".$request->keterangan_minus_2;

        $hitung_lembur = round((($request->upah_dasar*25/173*$request->lembur_1)*1.5)+(($request->upah_dasar*25/173*$request->lembur_2)*2));
        
        $input['lembur'] = $hitung_lembur.'|'.$request->lembur_1.'|'.$request->lembur_2;

        $upah_dasar_weekly = 0;
        $hasil_kerja="";

        for ($i=1; $i <=$request->data_for; $i++) { 
            $var_hasil_kerja="jam_kerja_".$i;
            $hasil_kerja=$hasil_kerja.$request[$var_hasil_kerja]."|";
            $upah_dasar_harian=$request[$var_hasil_kerja]*$request->upah_dasar;
            $upah_dasar_weekly=$upah_dasar_weekly+$upah_dasar_harian;
            // $upah_dasar_harian=$request[$var_hasil_kerja]*($request->upah_dasar-0.1);
            // $upah_dasar_weekly=$upah_dasar_weekly+$upah_dasar_harian+0.2;
        }

        $input['hasil_kerja'] = $hasil_kerja;
        // $input['upah_dasar_harian'] = round($upah_dasar_harian);
        $input['upah_dasar_weekly'] = round($upah_dasar_weekly);
       
        if ($request->akhir_bulan == 'n') {
            $input['tunjangan_kerja'] = 0;
            $input['tunjangan_kehadiran'] = 0;
        }else{
            $input['tunjangan_kerja'] = $request->tunjangan_kerja;
            $input['tunjangan_kehadiran'] = 75000-$request->pot_tunjangan_kehadiran;
        }
        // $input['tunjangan_kehadiran'] = 75000-$request->pot_tunjangan_kehadiran;
        // $input['tunjangan_kerja'] = $request->tunjangan_kerja;

        if ($request->check_jht == 'on') {
            $input['jht'] = $request->jht;
        }else{
            $input['jht'] = 0;
        }

        if($request->check_bpjs_kesehatan == 'on'){
            $input['bpjs_kesehatan'] = $request->bpjs_kesehatan;
        }else{
            $input['bpjs_kesehatan'] = 0;
        }

        $pengerjaan_harian_karyawan = $this->pengerjaanHarian->where('operator_harian_karyawan_id',$request->operator_harian_karyawan_id)
                                                    ->where('kode_pengerjaan',$kode_pengerjaan)
                                                    // ->update($input)
                                                    ->first()
                                                    ;
        // return 'Success';
        if (!empty($pengerjaan_harian_karyawan)) {
            $pengerjaan_harian_karyawan->update($input);
            return response()->json([
                'success' => true,
                'message_title' => 'Berhasil',
                'message_content' => 'Data Berhasil Disimpan'
            ]);
        }else{
            return response()->json([
                'success' => false,
                'message_title' => 'Gagal',
                'message_content' => 'Data Tidak Berhasil Disimpan'
            ]);
        }

        // dd($request->all());
    }

    public function hasil_kerja_harian_ppic_tembakau($id, $kode_pengerjaan)
    {
        $data['id'] = $id;
        $data['kode_pengerjaan'] = $kode_pengerjaan;
        $data['jenis_operator_detail_pekerjaan'] = $this->jenisOperatorDetailPengerjaan->find(13);
        $data['new_data_pengerjaan'] = $this->newDataPengerjaan->where('kode_pengerjaan',$kode_pengerjaan)->first();
        $data['explode_tanggal_pengerjaans'] = explode('#',$data['new_data_pengerjaan']['tanggal']);

        $data['pengerjaan_harians'] = $this->pengerjaanHarian->select([
                                                        'pengerjaan_harian.id as id',
                                                        'operator_harian_karyawan.nik as nik',
                                                        'biodata_karyawan.nama as nama',
                                                        'pengerjaan_harian.operator_harian_karyawan_id as operator_harian_karyawan_id',
                                                        'pengerjaan_harian.uang_makan as uang_makan',
                                                        'pengerjaan_harian.upah_dasar_weekly as upah_dasar_weekly',
                                                        'pengerjaan_harian.plus_1 as plus_1',
                                                        'pengerjaan_harian.plus_2 as plus_2',
                                                        'pengerjaan_harian.plus_3 as plus_3',
                                                        'pengerjaan_harian.minus_1 as minus_1',
                                                        'pengerjaan_harian.minus_2 as minus_2',
                                                        'pengerjaan_harian.jht as jht',
                                                        'pengerjaan_harian.bpjs_kesehatan as bpjs_kesehatan',
                                                        'pengerjaan_harian.lembur as lembur',
                                                        'pengerjaan_harian.hasil_kerja as hasil_kerja',
                                                        'pengerjaan_harian.tunjangan_kerja as tunjangan_kerja',
                                                        'pengerjaan_harian.tunjangan_kehadiran as tunjangan_kehadiran',
                                                        'operator_harian_karyawan.tunjangan_kerja_id as tunjangan_kerja_id',
                                                    ])
                                                    ->leftJoin('operator_harian_karyawan','operator_harian_karyawan.id','=','pengerjaan_harian.operator_harian_karyawan_id')
                                                    ->leftJoin('itic_emp_new.biodata_karyawan','biodata_karyawan.nik','=','operator_harian_karyawan.nik')
                                                    ->where('operator_harian_karyawan.jenis_operator_detail_pekerjaan_id',13)
                                                    ->where('kode_pengerjaan',$kode_pengerjaan)
                                                    ->where('operator_harian_karyawan.status','y')
                                                    ->orderBy('biodata_karyawan.nama','asc')
                                                    ->get();
        return view('backend.pengerjaan.harian.ppic_tembakau.index',$data);
    }

    public function hasil_kerja_harian_ppic_tembakau_view($id, $kode_pengerjaan, $nik, $month, $year)
    {
        $data['id'] = $id;
        $data['nik'] = $nik;
        $data['kode_pengerjaan'] = $kode_pengerjaan;
        $data['month'] = $month;
        $data['year']= $year;

        if($id == 4){
            $kode_jenis_operator_detail = 'H';
        }

        $data['karyawan_harian'] = $this->karyawanOperatorHarian->select([
                                                            'operator_harian_karyawan.id as id',
                                                            'operator_harian_karyawan.nik as nik',
                                                            'biodata_karyawan.nama as nama',
                                                            'biodata_karyawan.pin as pin',
                                                            'biodata_karyawan.tanggal_masuk as tanggal',
                                                            'operator_harian_karyawan.upah_dasar as upah_dasar',
                                                            'pengerjaan_harian.upah_dasar_weekly as upah_dasar_weekly',
                                                            'pengerjaan_harian.hari_kerja as hari_kerja',
                                                            'pengerjaan_harian.hasil_kerja as hasil_kerja',
                                                            'pengerjaan_harian.tunjangan_kerja as tunjangan_kerja',
                                                            'operator_harian_karyawan.tunjangan_kerja_id as tunjangan_kerja_id',
                                                            'pengerjaan_harian.plus_1 as plus_1',
                                                            'pengerjaan_harian.plus_2 as plus_2',
                                                            'pengerjaan_harian.plus_3 as plus_3',
                                                            'pengerjaan_harian.minus_1 as minus_1',
                                                            'pengerjaan_harian.minus_2 as minus_2',
                                                            'pengerjaan_harian.minus_3 as minus_3',
                                                            'pengerjaan_harian.uang_makan as uang_makan',
                                                            'pengerjaan_harian.lembur as lembur',
                                                            'operator_harian_karyawan.jht as jht',
                                                            'operator_harian_karyawan.bpjs as bpjs',
                                                            'operator_harian_karyawan.status as status',
                                                        ])
                                                        ->with('tunjangan_kerja_nominal')
                                                        ->leftJoin('itic_emp_new.biodata_karyawan','biodata_karyawan.nik','=','operator_harian_karyawan.nik')
                                                        ->rightJoin('pengerjaan_harian','pengerjaan_harian.operator_harian_karyawan_id','=','operator_harian_karyawan.id')
                                                        ->where('operator_harian_karyawan.nik',$nik)
                                                        ->where('operator_harian_karyawan.status','y')
                                                        ->where('pengerjaan_harian.kode_pengerjaan',$kode_pengerjaan)
                                                        ->first();
        // dd($data['karyawan_harian']);
        $data['new_data_pengerjaan'] = $this->newDataPengerjaan->where('kode_pengerjaan',$kode_pengerjaan)->first();
        $data['explode_tanggal_pengerjaans'] = explode('#',$data['new_data_pengerjaan']['tanggal']);

        if (empty($data['karyawan_harian']['plus_1'])) {
            $plus_1=null;
            $ket_plus_1=null;
        }else{
            $exp_plus1=explode("|",$data['karyawan_harian']['plus_1']);
            // dd($exp_plus1);
            $plus_1=$exp_plus1[0];
            $ket_plus_1=$exp_plus1[1];
        }

        if (empty($data['karyawan_harian']['plus_2'])) {
            $plus_2=null;
            $ket_plus_2=null;
        }else{
            $exp_plus2=explode("|",$data['karyawan_harian']['plus_2']);
            $plus_2=$exp_plus2[0];
            $ket_plus_2=$exp_plus2[1];
        }

        if (empty($data['karyawan_harian']['plus_3'])) {
            $plus_3=null;
            $ket_plus_3=null;
        }else{
            $exp_plus3=explode("|",$data['karyawan_harian']['plus_3']);
            $plus_3=$exp_plus2[0];
            $ket_plus_3=$exp_plus2[1];
        }

        if (empty($data['karyawan_harian']['minus_1'])) {
            $minus_1=null;
            $ket_minus_1=null;
        }else{
            $exp_minus1=explode("|",$data['karyawan_harian']['minus_1']);
            $minus_1=$exp_minus1[0];
            $ket_minus_1=$exp_minus1[1];
        }

        if (empty($data['karyawan_harian']['minus_2'])) {
            $minus_2=null;
            $ket_minus_2=null;
        }else{
            $exp_minus2=explode("|",$data['karyawan_harian']['minus_2']);
            $minus_2=$exp_minus2[0];
            $ket_minus_2=$exp_minus2[1];
        }

        $data['jhts'] = $this->bpjsJht->where('status','y')->get();
        $data['bpjs_kesehatan'] = $this->bpjsKesehatan->select('nominal')->where('status','y')->first();

        $data['pengerjaan_harian_weekly'] = $this->pengerjaanHarian->where('operator_harian_karyawan_id',$data['karyawan_harian']['id'])
                                                            ->where('kode_payrol',$kode_pengerjaan)
                                                            ->first();
        $exp_lembur = explode("@",$data['pengerjaan_harian_weekly']['lembur']);
        // dd($exp_lembur);
        if(empty($exp_lembur[0])){
            $exp_lembur[0]==null;
        }

        //Hitung Masa Kerja
        // $data['karyawan_masa_kerja'] = DB::connection('emp')->table('log_posisi')
        //                                                     ->select('tanggal')
        //                                                     ->where('nik',$nik)
        //                                                     ->first();
        // dd($data['karyawan_masa_kerja']);
        $awal  = new DateTime($data['karyawan_harian']->tanggal);
        $akhir = new DateTime(); // Waktu sekarang
        $diff  = $awal->diff($akhir);
        $data['masa_kerja'] = $diff->y.' Tahun '.$diff->m.' Bulan '.$diff->d.' Hari';
        $data['masa_kerja_tahun'] = $diff->y;
        $data['masa_kerja_hari'] = $diff->d;

        $data['upah_dasar_karyawan'] = [];
        foreach ($data['jhts'] as $key => $jht) {
            if ($data['masa_kerja_tahun'] > 15) {
                if ($jht->urutan == 3) {
                    $data['upah_dasar_karyawan'] = ($data['bpjs_kesehatan']['nominal'] + 100000)/25;
                }
            }
            elseif($data['masa_kerja_tahun'] >= 10 && $data['masa_kerja_tahun'] <= 15 && $data['masa_kerja_hari'] >= 1){
                if ($jht->urutan == 2) {
                    $data['upah_dasar_karyawan'] = ($data['bpjs_kesehatan']['nominal'] + 50000)/25;
                }
            }
            elseif($data['masa_kerja_tahun'] <= 10 || $data['masa_kerja_hari'] >= 1){
                if ($jht->urutan == 1) {
                    $data['upah_dasar_karyawan'] = ($data['bpjs_kesehatan']['nominal'] + 0)/25;
                }
            }
        }

        $tgl_current_active = Carbon::now()->format('d');
        $bln_active = Carbon::now()->format('m');
        $bln_current_active = $month;
        $thn_current_active = $year;

        if($month == 1 && $tgl_current_active <10){
            $thn_prev_active = $year-1;
            $bulan_kemarin="$thn_prev_active-11-26";
            $bulan_sekarang="$thn_prev_active-12-26";
        }elseif($month == 1 && $tgl_current_active>10 or $month == 2 && $tgl_current_active<10 && $bln_active != 3){
            $thn_prev_active = $year-1;
            $bulan_kemarin="$thn_prev_active-12-26";
            $bulan_sekarang="$thn_current_active-01-26";
        }else{
            $bln_prev_active = $month-1; 
            $thn_prev_active = $year;
            $bulan_kemarin="$thn_prev_active-$bln_prev_active-26";
            $bulan_sekarang="$thn_current_active-$bln_current_active-26";
        }

        $data['tunjangan_kerjas'] = $this->tunjanganKerja->select('nominal')->where('id',$data['karyawan_harian']['tunjangan_kerja_id'])->first();
        if(empty($data['tunjangan_kerjas'])){
            $data['tunjangan_kerja'] = 0;
        }else{
            $data['tunjangan_kerja'] = $data['tunjangan_kerjas']['nominal'];
        }

        //jumlah alpa
        $data['alpa'] = $this->presensiInfo->where('pin',$data['karyawan_harian']['pin'])
                                    ->whereBetween('scan_date',[$bulan_kemarin,$bulan_sekarang])
                                    ->where('status',7)
                                    ->get();
        //jumlah diliburkan
        $data['diliburkan'] = $this->presensiInfo->where('pin',$data['karyawan_harian']['pin'])
                                    ->whereBetween('scan_date',[$bulan_kemarin,$bulan_sekarang])
                                    ->where('status',14)
                                    ->get();
        //jumlah cuti
        $data['cuti'] = $this->presensiInfo->where('pin',$data['karyawan_harian']['pin'])
                                    ->whereBetween('scan_date',[$bulan_kemarin,$bulan_sekarang])
                                    ->where('status',13)
                                    ->get();
        //jumlah sakit
        $data['sakit'] = $this->presensiInfo->where('pin',$data['karyawan_harian']['pin'])
                                    ->whereBetween('scan_date',[$bulan_kemarin,$bulan_sekarang])
                                    ->where('status',4)
                                    ->get();
        // dd($data['sakit']);
        //jumlah ijin full
        $data['ijin_full'] = $this->presensiInfo->where('pin',$data['karyawan_harian']['pin'])
                                    ->whereBetween('scan_date',[$bulan_kemarin,$bulan_sekarang])
                                    ->where('status',6)
                                    ->get();
        //jumlah status terlambat
        $data['terlambats'] = $this->presensiInfo->where('pin',$data['karyawan_harian']['pin'])
                                    ->whereBetween('scan_date',[$bulan_kemarin,$bulan_sekarang])
                                    ->where('status',3)
                                    ->get();
        // dd($data);
        $telat_1=0; //terlambat < 5 menit
        $telat_2=0; //terlambat > 5 menit < 15 menit
        $telat_3=0; //terlambat > 15 menit < 1 jam
        $telat_4=0; //terlambat > 1 jam < 3 jam	
        $telat_5=0; //terlambat > 3 jam	
        $data['telat_1'] = $telat_1;
        $data['telat_2'] = $telat_2;
        $data['telat_3'] = $telat_3;
        $data['telat_4'] = $telat_4;
        $data['telat_5'] = $telat_5;
        foreach ($data['terlambats'] as $key => $terlambat) {
            $explode_keterangan_terlambat = explode("@",$terlambat->keterangan);
            $jam_keterangan_terlambat=strtotime($explode_keterangan_terlambat[1]);
			$jam_datang_terlambat=strtotime(Carbon::parse($terlambat['scan_date'])->format('H:i'));
			// $jam_datang_terlambat=strtotime($terlambat['jam_datang_telat']);
			$menit_telat=(($jam_datang_terlambat-$jam_keterangan_terlambat)/60);
            // dd($menit_telat);
			if($menit_telat <= 4){
                $telat_1=$telat_1+1;
                $data['telat_1'] = $telat_1;
            }
			else if(($menit_telat >= 5) && ($menit_telat <= 15) ){
                $telat_2=$telat_2+1;
                $data['telat_2'] = $telat_2;
            }
			else if(($menit_telat >= 16) && ($menit_telat <= 60)){
                $telat_3=$telat_3+1;
                $data['telat_3'] = $telat_3;
            }
			else if(($menit_telat >= 61) && ($menit_telat <= 179)){
                $telat_4=$telat_4+1;
                $data['telat_4'] = $telat_4;
            }else if($menit_telat > 179){
                $telat_5=$telat_5+1;
                $data['telat_5'] = $telat_5;
            }
			// else{
            //     $telat_4=$telat_4+1;
            //     $data['telat_4'] = $telat_4;
            // }
        }

        //jumlah status pulang awal
        $data['pulang_awals'] = $this->presensiInfo->where('pin',$data['karyawan_harian']['pin'])
                                    ->whereBetween('scan_date',[$bulan_kemarin,$bulan_sekarang])
                                    ->where(function($query) {
                                        $query->where('status','=',9)
                                            ->orWhere('status','=',10);
                                    })
                                    // ->where('status',9)
                                    // ->where('status',10)
                                    ->get();
        // dd($data);
        $pulang_1=0; //pulang awal < 4 jam
		$pulang_2=0; //tpulang awal > 4 jam
        $data['pulang_1'] = $pulang_1;
        $data['pulang_2'] = $pulang_2;
        foreach ($data['pulang_awals'] as $key => $pulang_awal) {
            $explode_pulang_seharusnya=explode("@",$pulang_awal->keterangan);
			$jam_pulang_seharusnya=strtotime($explode_pulang_seharusnya[3]);
			$jam_pulang_awal=strtotime(Carbon::parse($pulang_awal['scan_date'])->format('H:i'));
			// $jam_pulang_awal=strtotime($pulang_awal->jam_pulang_awal);
			$menit_sisa_jam_kerja=(($jam_pulang_seharusnya-$jam_pulang_awal)/60);
			if ($menit_sisa_jam_kerja<=300){
                $pulang_1=$pulang_1+1;
                $data['pulang_1'] = $pulang_1;
            }
			else{
                $pulang_2=$pulang_2+1;
                $data['pulang_2'] = $pulang_2;
            }
        }

        //jumlah ijin saat jam kerja
        $data['keluar_masuks'] = $this->keluarMasuk->where('nik',$data['karyawan_harian']['nik'])
                                    ->whereBetween('tanggal_ijin',[$bulan_kemarin,$bulan_sekarang])
                                    ->get();
        // dd($data['keluar_masuks']);
        $ijin_15=0; //ijin kurang dari 15 menit
		$ijin_k4=0; //ijin > 15menit < 4 jam
		$ijin_l4=0; //ijin > 4 jam
        $data['ijin_15']=$ijin_15;
        $data['ijin_k4']=$ijin_k4;
        $data['ijin_l4']=$ijin_l4;
        foreach ($data['keluar_masuks'] as $key => $keluar_masuk) {
            $jam_keluar=strtotime($keluar_masuk->jam_keluar);
			$jam_datang=strtotime($keluar_masuk->jam_datang);
			$menit_keluar_masuk=(($jam_datang-$jam_keluar)/60);
			if ($menit_keluar_masuk<=15){
                $ijin_15=$ijin_15+1;
                $data['ijin_15']=$ijin_15;
            }
			else if (($menit_keluar_masuk>15)&&($menit_keluar_masuk<=239)){
                $ijin_k4=$ijin_k4+1;
                $data['ijin_k4']=$ijin_k4;
            }
			else {
                $ijin_l4=$ijin_l4+1;
                $data['ijin_l4']=$ijin_l4;
            }
        }

        // dd($data);

        //lihat tanggal masuk

        // $data['log_posisi'] = $this->logPosisi->select('tanggal')->where('nik',$nik)->first();
		$selisih_tanggal_masuk=strtotime($bulan_kemarin)-strtotime($data['karyawan_harian']['tanggal']);
		if ($selisih_tanggal_masuk>=0)$div_tk=0;else $div_tk=75000;

        $total_potongan_tk=(75000*$data['alpa']->count())+(75000*$data['diliburkan']->count())+(75000*$data['sakit']->count())+(75000*$data['cuti']->count())+(75000*$data['ijin_full']->count())+($data['ijin_15']*25000)+($data['ijin_k4']*40000)+
        ($data['ijin_l4']*75000)+($data['pulang_1']*40000)+($data['pulang_2']*75000)+($data['telat_1']*15000)+($data['telat_2']*25000)+($data['telat_3']*30000)+($data['telat_4']*40000)+$div_tk;
        
        // dd($total_potongan_tk);
        if ($total_potongan_tk>75000){$total_potongan_tk=75000;}else {}

        // if ($data['pengerjaan_harian_weekly']['tunjangan_kerja'] == 75000) {
        //     $data['total_potongan_tk']=0;
        // }
        // // elseif($data['pengerjaan_harian_weekly']['tunjangan_kerja'] < 75000){
        // //     $data['total_potongan_tk']=$data['pengerjaan_harian_weekly']['tunjangan_kerja'];
        // // }
        // else{
        //     $data['total_potongan_tk']=$total_potongan_tk; 
        // }
        $data['total_potongan_tk']=$total_potongan_tk;

        return view('backend.pengerjaan.harian.ppic_tembakau.input_hasil_kerja_karyawan',$data);

    }

    public function hasil_kerja_harian_ppic_tembakau_simpan(Request $request, $id, $kode_pengerjaan, $tanggal)
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

        $input['uang_makan'] = $request->uang_makan;

        $input['plus_1'] = $request->plus_1."|".$request->keterangan_plus_1;
        $input['plus_2'] = $request->plus_2."|".$request->keterangan_plus_2;
        $input['plus_3'] = $request->plus_3."|".$request->keterangan_plus_3;

        $input['minus_1'] = $request->minus_1."|".$request->keterangan_minus_1;
        $input['minus_2'] = $request->minus_2."|".$request->keterangan_minus_2;

        $hitung_lembur = round((($request->upah_dasar*25/173*$request->lembur_1)*1.5)+(($request->upah_dasar*25/173*$request->lembur_2)*2));
        
        $input['lembur'] = $hitung_lembur.'|'.$request->lembur_1.'|'.$request->lembur_2;

        // if (!$request->) {
        //     # code...
        // }

        $upah_dasar_weekly = 0;
        $hasil_kerja="";

        for ($i=1; $i <=$request->data_for; $i++) { 
            $var_hasil_kerja="jam_kerja_".$i;
            $hasil_kerja=$hasil_kerja.$request[$var_hasil_kerja]."|";
            // $upah_dasar_harian=$request[$var_hasil_kerja]*($request->upah_dasar);
            $upah_dasar_harian=$request[$var_hasil_kerja]*$request->upah_dasar;
            // $upah_dasar_harian=$request[$var_hasil_kerja]*($request->upah_dasar-0.1);
            $upah_dasar_weekly=$upah_dasar_weekly+$upah_dasar_harian;
            // $upah_dasar_weekly=$upah_dasar_weekly+$upah_dasar_harian+0.2;
        }

        $input['hasil_kerja'] = $hasil_kerja;
        // $input['upah_dasar_harian'] = round($upah_dasar_harian);
        $input['upah_dasar_weekly'] = round($upah_dasar_weekly);
        // $input['upah_dasar_weekly'] = round(50);
        if ($request->akhir_bulan == 'n') {
            $input['tunjangan_kerja'] = 0;
            $input['tunjangan_kehadiran'] = 0;
        }else{
            $input['tunjangan_kerja'] = $request->tunjangan_kerja;
            $input['tunjangan_kehadiran'] = 75000-$request->pot_tunjangan_kehadiran;
        }
        // $input['tunjangan_kehadiran'] = 75000-$request->pot_tunjangan_kehadiran;
        // $input['tunjangan_kerja'] = $request->tunjangan_kerja;

        if ($request->check_jht == 'on') {
            $input['jht'] = $request->jht;
        }else{
            $input['jht'] = 0;
        }

        if($request->check_bpjs_kesehatan == 'on'){
            $input['bpjs_kesehatan'] = $request->bpjs_kesehatan;
        }else{
            $input['bpjs_kesehatan'] = 0;
        }

        $pengerjaan_harian_karyawan = $this->pengerjaanHarian->where('operator_harian_karyawan_id',$request->operator_harian_karyawan_id)
                                                    ->where('kode_pengerjaan',$kode_pengerjaan)
                                                    // ->update($input)
                                                    ->first()
                                                    ;
        if (!empty($pengerjaan_harian_karyawan)) {
            $pengerjaan_harian_karyawan->update($input);
            return response()->json([
                'success' => true,
                'message_title' => 'Berhasil',
                'message_content' => 'Data Berhasil Disimpan'
            ]);
        }else{
            return response()->json([
                'success' => false,
                'message_title' => 'Gagal',
                'message_content' => 'Data Tidak Berhasil Disimpan'
            ]);
        }
        // return 'Success';

        // dd($request->all());
    }

    public function hasil_kerja_harian_primary_process($id, $kode_pengerjaan)
    {
        $data['id'] = $id;
        $data['kode_pengerjaan'] = $kode_pengerjaan;
        $data['jenis_operator_detail_pekerjaan'] = $this->jenisOperatorDetailPengerjaan->find(14);
        $data['new_data_pengerjaan'] = $this->newDataPengerjaan->where('kode_pengerjaan',$kode_pengerjaan)->first();
        $data['explode_tanggal_pengerjaans'] = explode('#',$data['new_data_pengerjaan']['tanggal']);

        $data['pengerjaan_harians'] = $this->pengerjaanHarian->select([
                                                        'pengerjaan_harian.id as id',
                                                        'operator_harian_karyawan.nik as nik',
                                                        'biodata_karyawan.nama as nama',
                                                        'pengerjaan_harian.operator_harian_karyawan_id as operator_harian_karyawan_id',
                                                        'pengerjaan_harian.uang_makan as uang_makan',
                                                        'pengerjaan_harian.upah_dasar_weekly as upah_dasar_weekly',
                                                        'pengerjaan_harian.plus_1 as plus_1',
                                                        'pengerjaan_harian.plus_2 as plus_2',
                                                        'pengerjaan_harian.plus_3 as plus_3',
                                                        'pengerjaan_harian.minus_1 as minus_1',
                                                        'pengerjaan_harian.minus_2 as minus_2',
                                                        'pengerjaan_harian.jht as jht',
                                                        'pengerjaan_harian.bpjs_kesehatan as bpjs_kesehatan',
                                                        'pengerjaan_harian.lembur as lembur',
                                                        'pengerjaan_harian.hasil_kerja as hasil_kerja',
                                                        'pengerjaan_harian.tunjangan_kerja as tunjangan_kerja',
                                                        'pengerjaan_harian.tunjangan_kehadiran as tunjangan_kehadiran',
                                                        'operator_harian_karyawan.tunjangan_kerja_id as tunjangan_kerja_id',
                                                    ])
                                                    ->leftJoin('operator_harian_karyawan','operator_harian_karyawan.id','=','pengerjaan_harian.operator_harian_karyawan_id')
                                                    ->leftJoin('itic_emp_new.biodata_karyawan','biodata_karyawan.nik','=','operator_harian_karyawan.nik')
                                                    ->where('operator_harian_karyawan.jenis_operator_detail_pekerjaan_id',14)
                                                    ->where('kode_pengerjaan',$kode_pengerjaan)
                                                    ->where('operator_harian_karyawan.status','y')
                                                    ->orderBy('biodata_karyawan.nama','asc')
                                                    ->get();
        return view('backend.pengerjaan.harian.primary_process.index',$data);
    }

    public function hasil_kerja_harian_primary_process_view($id, $kode_pengerjaan, $nik, $month, $year)
    {
        $data['id'] = $id;
        $data['nik'] = $nik;
        $data['kode_pengerjaan'] = $kode_pengerjaan;
        $data['month'] = $month;
        $data['year']= $year;

        if($id == 4){
            $kode_jenis_operator_detail = 'H';
        }

        $data['karyawan_harian'] = $this->karyawanOperatorHarian->select([
                                                            'operator_harian_karyawan.id as id',
                                                            'operator_harian_karyawan.nik as nik',
                                                            'biodata_karyawan.nama as nama',
                                                            'biodata_karyawan.pin as pin',
                                                            'biodata_karyawan.tanggal_masuk as tanggal',
                                                            'operator_harian_karyawan.upah_dasar as upah_dasar',
                                                            'pengerjaan_harian.upah_dasar_weekly as upah_dasar_weekly',
                                                            'pengerjaan_harian.hari_kerja as hari_kerja',
                                                            'pengerjaan_harian.hasil_kerja as hasil_kerja',
                                                            'pengerjaan_harian.tunjangan_kerja as tunjangan_kerja',
                                                            'operator_harian_karyawan.tunjangan_kerja_id as tunjangan_kerja_id',
                                                            'pengerjaan_harian.plus_1 as plus_1',
                                                            'pengerjaan_harian.plus_2 as plus_2',
                                                            'pengerjaan_harian.plus_3 as plus_3',
                                                            'pengerjaan_harian.minus_1 as minus_1',
                                                            'pengerjaan_harian.minus_2 as minus_2',
                                                            'pengerjaan_harian.minus_3 as minus_3',
                                                            'pengerjaan_harian.uang_makan as uang_makan',
                                                            'pengerjaan_harian.lembur as lembur',
                                                            'operator_harian_karyawan.jht as jht',
                                                            'operator_harian_karyawan.bpjs as bpjs',
                                                            'operator_harian_karyawan.status as status',
                                                        ])
                                                        ->with('tunjangan_kerja_nominal')
                                                        ->leftJoin('itic_emp_new.biodata_karyawan','biodata_karyawan.nik','=','operator_harian_karyawan.nik')
                                                        ->rightJoin('pengerjaan_harian','pengerjaan_harian.operator_harian_karyawan_id','=','operator_harian_karyawan.id')
                                                        ->where('operator_harian_karyawan.nik',$nik)
                                                        ->where('operator_harian_karyawan.status','y')
                                                        ->where('pengerjaan_harian.kode_pengerjaan',$kode_pengerjaan)
                                                        ->first();
        // dd($data['karyawan_harian']);
        $data['new_data_pengerjaan'] = $this->newDataPengerjaan->where('kode_pengerjaan',$kode_pengerjaan)->first();
        $data['explode_tanggal_pengerjaans'] = explode('#',$data['new_data_pengerjaan']['tanggal']);

        if (empty($data['karyawan_harian']['plus_1'])) {
            $plus_1=null;
            $ket_plus_1=null;
        }else{
            $exp_plus1=explode("|",$data['karyawan_harian']['plus_1']);
            // dd($exp_plus1);
            $plus_1=$exp_plus1[0];
            $ket_plus_1=$exp_plus1[1];
        }

        if (empty($data['karyawan_harian']['plus_2'])) {
            $plus_2=null;
            $ket_plus_2=null;
        }else{
            $exp_plus2=explode("|",$data['karyawan_harian']['plus_2']);
            $plus_2=$exp_plus2[0];
            $ket_plus_2=$exp_plus2[1];
        }

        if (empty($data['karyawan_harian']['plus_3'])) {
            $plus_3=null;
            $ket_plus_3=null;
        }else{
            $exp_plus3=explode("|",$data['karyawan_harian']['plus_3']);
            $plus_3=$exp_plus2[0];
            $ket_plus_3=$exp_plus2[1];
        }

        if (empty($data['karyawan_harian']['minus_1'])) {
            $minus_1=null;
            $ket_minus_1=null;
        }else{
            $exp_minus1=explode("|",$data['karyawan_harian']['minus_1']);
            $minus_1=$exp_minus1[0];
            $ket_minus_1=$exp_minus1[1];
        }

        if (empty($data['karyawan_harian']['minus_2'])) {
            $minus_2=null;
            $ket_minus_2=null;
        }else{
            $exp_minus2=explode("|",$data['karyawan_harian']['minus_2']);
            $minus_2=$exp_minus2[0];
            $ket_minus_2=$exp_minus2[1];
        }

        $data['jhts'] = $this->bpjsJht->where('status','y')->get();
        $data['bpjs_kesehatan'] = $this->bpjsKesehatan->select('nominal')->where('status','y')->first();

        $data['pengerjaan_harian_weekly'] = $this->pengerjaanHarian->where('operator_harian_karyawan_id',$data['karyawan_harian']['id'])
                                                            ->where('kode_payrol',$kode_pengerjaan)
                                                            ->first();
        $exp_lembur = explode("@",$data['pengerjaan_harian_weekly']['lembur']);
        // dd($exp_lembur);
        if(empty($exp_lembur[0])){
            $exp_lembur[0]==null;
        }

        //Hitung Masa Kerja
        // $data['karyawan_masa_kerja'] = DB::connection('emp')->table('log_posisi')
        //                                                     ->select('tanggal')
        //                                                     ->where('nik',$nik)
        //                                                     ->first();
                                                            
        $awal  = new DateTime($data['karyawan_harian']->tanggal);
        $akhir = new DateTime(); // Waktu sekarang
        $diff  = $awal->diff($akhir);
        $data['masa_kerja'] = $diff->y.' Tahun '.$diff->m.' Bulan '.$diff->d.' Hari';
        $data['masa_kerja_tahun'] = $diff->y;
        $data['masa_kerja_hari'] = $diff->d;

        $data['upah_dasar_karyawan'] = [];
        foreach ($data['jhts'] as $key => $jht) {
            if ($data['masa_kerja_tahun'] > 15) {
                if ($jht->urutan == 3) {
                    $data['upah_dasar_karyawan'] = ($data['bpjs_kesehatan']['nominal'] + 100000)/25;
                }
            }
            elseif($data['masa_kerja_tahun'] >= 10 && $data['masa_kerja_tahun'] <= 15 && $data['masa_kerja_hari'] >= 1){
                if ($jht->urutan == 2) {
                    $data['upah_dasar_karyawan'] = ($data['bpjs_kesehatan']['nominal'] + 50000)/25;
                }
            }
            elseif($data['masa_kerja_tahun'] <= 10 || $data['masa_kerja_hari'] >= 1){
                if ($jht->urutan == 1) {
                    $data['upah_dasar_karyawan'] = ($data['bpjs_kesehatan']['nominal'] + 0)/25;
                }
            }
        }

        $tgl_current_active = Carbon::now()->format('d');
        $bln_active = Carbon::now()->format('m');
        $bln_current_active = $month;
        $thn_current_active = $year;

        if($month == 1 && $tgl_current_active <10){
            $thn_prev_active = $year-1;
            $bulan_kemarin="$thn_prev_active-11-26";
            $bulan_sekarang="$thn_prev_active-12-26";
        }elseif($month == 1 && $tgl_current_active>10 or $month == 2 && $tgl_current_active<10 && $bln_active != 3){
            $thn_prev_active = $year-1;
            $bulan_kemarin="$thn_prev_active-12-26";
            $bulan_sekarang="$thn_current_active-01-26";
        }else{
            $bln_prev_active = $month-1; 
            $thn_prev_active = $year;
            $bulan_kemarin="$thn_prev_active-$bln_prev_active-26";
            $bulan_sekarang="$thn_current_active-$bln_current_active-26";
        }

        $data['tunjangan_kerjas'] = $this->tunjanganKerja->select('nominal')->where('id',$data['karyawan_harian']['tunjangan_kerja_id'])->first();
        if(empty($data['tunjangan_kerjas'])){
            $data['tunjangan_kerja'] = 0;
        }else{
            $data['tunjangan_kerja'] = $data['tunjangan_kerjas']['nominal'];
        }

        //jumlah alpa
        $data['alpa'] = $this->presensiInfo->where('pin',$data['karyawan_harian']['pin'])
                                    ->whereBetween('scan_date',[$bulan_kemarin,$bulan_sekarang])
                                    ->where('status',7)
                                    ->get();
        //jumlah diliburkan
        $data['diliburkan'] = $this->presensiInfo->where('pin',$data['karyawan_harian']['pin'])
                                    ->whereBetween('scan_date',[$bulan_kemarin,$bulan_sekarang])
                                    ->where('status',14)
                                    ->get();
        // dd($data['diliburkan']);
        //jumlah cuti
        $data['cuti'] = $this->presensiInfo->where('pin',$data['karyawan_harian']['pin'])
                                    ->whereBetween('scan_date',[$bulan_kemarin,$bulan_sekarang])
                                    ->where('status',13)
                                    ->get();
        //jumlah sakit
        $data['sakit'] = $this->presensiInfo->where('pin',$data['karyawan_harian']['pin'])
                                    ->whereBetween('scan_date',[$bulan_kemarin,$bulan_sekarang])
                                    ->where('status',4)
                                    ->get();
        // dd($data['sakit']);
        //jumlah ijin full
        $data['ijin_full'] = $this->presensiInfo->where('pin',$data['karyawan_harian']['pin'])
                                    ->whereBetween('scan_date',[$bulan_kemarin,$bulan_sekarang])
                                    ->where('status',6)
                                    ->get();
        //jumlah status terlambat
        $data['terlambats'] = $this->presensiInfo->where('pin',$data['karyawan_harian']['pin'])
                                    ->whereBetween('scan_date',[$bulan_kemarin,$bulan_sekarang])
                                    ->where('status',3)
                                    ->get();
                                    // dd($data['terlambats']);
        $telat_1=0; //terlambat < 5 menit
        $telat_2=0; //terlambat > 5 menit < 15 menit
        $telat_3=0; //terlambat > 15 menit < 1 jam
        $telat_4=0; //terlambat > 1 jam < 3 jam	
        $telat_5=0; //terlambat > 3 jam	
        $data['telat_1'] = $telat_1;
        $data['telat_2'] = $telat_2;
        $data['telat_3'] = $telat_3;
        $data['telat_4'] = $telat_4;
        $data['telat_5'] = $telat_4;
        foreach ($data['terlambats'] as $key => $terlambat) {
            $explode_keterangan_terlambat = explode("@",$terlambat->keterangan);
            $jam_keterangan_terlambat=strtotime($explode_keterangan_terlambat[1]);
            $jam_datang_terlambat=strtotime(Carbon::parse($terlambat['scan_date'])->format('H:i'));
            // $jam_datang_terlambat=strtotime($terlambat['jam_datang_telat']);
            $menit_telat=(($jam_datang_terlambat-$jam_keterangan_terlambat)/60);
            // dd($menit_telat);
            if($menit_telat <= 4){
                $telat_1=$telat_1+1;
                $data['telat_1'] = $telat_1;
            }
            else if(($menit_telat >= 5) && ($menit_telat <= 15) ){
                $telat_2=$telat_2+1;
                $data['telat_2'] = $telat_2;
            }
            else if(($menit_telat >= 16) && ($menit_telat <= 60)){
                $telat_3=$telat_3+1;
                $data['telat_3'] = $telat_3;
            }
            else if(($menit_telat >= 61) && ($menit_telat <= 179)){
                $telat_4=$telat_4+1;
                $data['telat_4'] = $telat_4;
            }else if($menit_telat > 179){
                $telat_5=$telat_5+1;
                $data['telat_5'] = $telat_5;
            }
            // else{
            //     $telat_4=$telat_4+1;
            //     $data['telat_4'] = $telat_4;
            // }
        }

        // dd($data['telat_3']);

        //jumlah status pulang awal
        $data['pulang_awals'] = $this->presensiInfo->where('pin',$data['karyawan_harian']['pin'])
                                    ->whereBetween('scan_date',[$bulan_kemarin,$bulan_sekarang])
                                    ->where(function($query) {
                                        $query->where('status','=',9)
                                            ->orWhere('status','=',10);
                                    })
                                    // ->where('status',9)
                                    // ->where('status',10)
                                    ->get();
        // dd($data['pulang_awals']);
        $pulang_1=0; //pulang awal < 4 jam
		$pulang_2=0; //tpulang awal > 4 jam
        $data['pulang_1'] = $pulang_1;
        $data['pulang_2'] = $pulang_2;
        // $data['menit_pa'] = [];
        foreach ($data['pulang_awals'] as $key => $pulang_awal) {
            $explode_pulang_seharusnya=explode("@",$pulang_awal->keterangan);
			$jam_pulang_seharusnya=strtotime($explode_pulang_seharusnya[3]);
			$jam_pulang_awal=strtotime(Carbon::parse($pulang_awal['scan_date'])->format('H:i'));
			// $jam_pulang_awal=strtotime($pulang_awal->jam_pulang_awal);
			// $menit_sisa_jam_kerja=(($jam_pulang_seharusnya-$jam_pulang_awal)/60);
			$menit_sisa_jam_kerja=(($jam_pulang_seharusnya-$jam_pulang_awal)/60);
            // $data['menit_pa'] = $menit_sisa_jam_kerja;
			if ($menit_sisa_jam_kerja<=300){
                $pulang_1=$pulang_1+1;
                $data['pulang_1'] = $pulang_1;
            }
			else{
                $pulang_2=$pulang_2+1;
                $data['pulang_2'] = $pulang_2;
            }
        }
        // dd($data['menit_pa']);
        //jumlah ijin saat jam kerja
        $data['keluar_masuks'] = $this->keluarMasuk->where('nik',$data['karyawan_harian']['nik'])
                                    ->whereBetween('tanggal_ijin',[$bulan_kemarin,$bulan_sekarang])
                                    ->get();
        // dd($data['keluar_masuks']);
        $ijin_15=0; //ijin kurang dari 15 menit
		$ijin_k4=0; //ijin > 15menit < 4 jam
		$ijin_l4=0; //ijin > 4 jam
        $data['ijin_15']=$ijin_15;
        $data['ijin_k4']=$ijin_k4;
        $data['ijin_l4']=$ijin_l4;
        foreach ($data['keluar_masuks'] as $key => $keluar_masuk) {
            $jam_keluar=strtotime($keluar_masuk->jam_keluar);
			$jam_datang=strtotime($keluar_masuk->jam_datang);
			$menit_keluar_masuk=(($jam_datang-$jam_keluar)/60);
			if ($menit_keluar_masuk<=15){
                $ijin_15=$ijin_15+1;
                $data['ijin_15']=$ijin_15;
            }
			else if (($menit_keluar_masuk>15)&&($menit_keluar_masuk<=239)){
                $ijin_k4=$ijin_k4+1;
                $data['ijin_k4']=$ijin_k4;
            }
			else {
                $ijin_l4=$ijin_l4+1;
                $data['ijin_l4']=$ijin_l4;
            }
        }

        //lihat tanggal masuk

        // $data['log_posisi'] = $this->logPosisi->select('tanggal')->where('nik',$nik)->first();
		$selisih_tanggal_masuk=strtotime($bulan_kemarin)-strtotime($data['karyawan_harian']['tanggal']);
		if ($selisih_tanggal_masuk>=0)$div_tk=0;else $div_tk=75000;

        $total_potongan_tk=(75000*$data['alpa']->count())+(75000*$data['diliburkan']->count())+(75000*$data['sakit']->count())+(75000*$data['cuti']->count())+(75000*$data['ijin_full']->count())+($data['ijin_15']*25000)+($data['ijin_k4']*40000)+
        ($data['ijin_l4']*75000)+($data['pulang_1']*40000)+($data['pulang_2']*75000)+($data['telat_1']*15000)+($data['telat_2']*25000)+($data['telat_3']*30000)+($data['telat_4']*40000)+$div_tk;
        if ($total_potongan_tk>75000){$total_potongan_tk=75000;}else {}

        // if ($data['pengerjaan_harian_weekly']['tunjangan_kerja'] == 75000) {
        //     $data['total_potongan_tk']=0;
        // }
        // elseif($data['pengerjaan_harian_weekly']['tunjangan_kerja'] < 75000){
        //     $data['total_potongan_tk']=$data['pengerjaan_harian_weekly']['tunjangan_kerja'];
        // }
        // else{
        //     $data['total_potongan_tk']=$total_potongan_tk; 
        // }

        $data['total_potongan_tk']=$total_potongan_tk;

        return view('backend.pengerjaan.harian.primary_process.input_hasil_kerja_karyawan',$data);

    }

    public function hasil_kerja_harian_primary_process_simpan(Request $request, $id, $kode_pengerjaan, $tanggal)
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

        $input['uang_makan'] = $request->uang_makan;

        $input['plus_1'] = $request->plus_1."|".$request->keterangan_plus_1;
        $input['plus_2'] = $request->plus_2."|".$request->keterangan_plus_2;
        $input['plus_3'] = $request->plus_3."|".$request->keterangan_plus_3;

        $input['minus_1'] = $request->minus_1."|".$request->keterangan_minus_1;
        $input['minus_2'] = $request->minus_2."|".$request->keterangan_minus_2;

        $hitung_lembur = round((($request->upah_dasar*25/173*$request->lembur_1)*1.5)+(($request->upah_dasar*25/173*$request->lembur_2)*2));
        
        $input['lembur'] = $hitung_lembur.'|'.$request->lembur_1.'|'.$request->lembur_2;

        $upah_dasar_weekly = 0;
        $hasil_kerja="";

        for ($i=1; $i <=$request->data_for; $i++) { 
            $var_hasil_kerja="jam_kerja_".$i;
            $hasil_kerja=$hasil_kerja.$request[$var_hasil_kerja]."|";
            $upah_dasar_harian=$request[$var_hasil_kerja]*$request->upah_dasar;
            $upah_dasar_weekly=$upah_dasar_weekly+$upah_dasar_harian;
            // $upah_dasar_harian=$request[$var_hasil_kerja]*($request->upah_dasar-0.1);
            // $upah_dasar_weekly=$upah_dasar_weekly+$upah_dasar_harian+0.2;
        }

        $input['hasil_kerja'] = $hasil_kerja;
        // $input['upah_dasar_harian'] = round($upah_dasar_harian);
        $input['upah_dasar_weekly'] = round($upah_dasar_weekly);
        if ($request->akhir_bulan == 'n') {
            $input['tunjangan_kerja'] = 0;
            $input['tunjangan_kehadiran'] = 0;
        }else{
            $input['tunjangan_kerja'] = $request->tunjangan_kerja;
            $input['tunjangan_kehadiran'] = 75000-$request->pot_tunjangan_kehadiran;
        }
        // $input['tunjangan_kehadiran'] = 75000-$request->pot_tunjangan_kehadiran;
        // $input['tunjangan_kerja'] = $request->tunjangan_kerja;

        if ($request->check_jht == 'on') {
            $input['jht'] = $request->jht;
        }else{
            $input['jht'] = 0;
        }

        if($request->check_bpjs_kesehatan == 'on'){
            $input['bpjs_kesehatan'] = $request->bpjs_kesehatan;
        }else{
            $input['bpjs_kesehatan'] = 0;
        }

        $pengerjaan_harian_karyawan = $this->pengerjaanHarian->where('operator_harian_karyawan_id',$request->operator_harian_karyawan_id)
                                                    ->where('kode_pengerjaan',$kode_pengerjaan)
                                                    // ->update($input)
                                                    ->first()
                                                    ;
        if (!empty($pengerjaan_harian_karyawan)) {
            $pengerjaan_harian_karyawan->update($input);
            return response()->json([
                'success' => true,
                'message_title' => 'Berhasil',
                'message_content' => 'Data Berhasil Disimpan'
            ]);
        }else{
            return response()->json([
                'success' => false,
                'message_title' => 'Gagal',
                'message_content' => 'Data Tidak Berhasil Disimpan'
            ]);
        }
        // return 'Success';
    }

    public function hasil_kerja_harian_packing_b($id, $kode_pengerjaan)
    {
        $data['id'] = $id;
        $data['kode_pengerjaan'] = $kode_pengerjaan;
        $data['jenis_operator_detail_pekerjaan'] = $this->jenisOperatorDetailPengerjaan->find(15);
        $data['new_data_pengerjaan'] = $this->newDataPengerjaan->where('kode_pengerjaan',$kode_pengerjaan)->first();
        $data['explode_tanggal_pengerjaans'] = explode('#',$data['new_data_pengerjaan']['tanggal']);

        $data['pengerjaan_harians'] = $this->pengerjaanHarian->select([
                                                        'pengerjaan_harian.id as id',
                                                        'operator_harian_karyawan.nik as nik',
                                                        'biodata_karyawan.nama as nama',
                                                        'pengerjaan_harian.operator_harian_karyawan_id as operator_harian_karyawan_id',
                                                        'pengerjaan_harian.uang_makan as uang_makan',
                                                        'pengerjaan_harian.upah_dasar_weekly as upah_dasar_weekly',
                                                        'pengerjaan_harian.plus_1 as plus_1',
                                                        'pengerjaan_harian.plus_2 as plus_2',
                                                        'pengerjaan_harian.plus_3 as plus_3',
                                                        'pengerjaan_harian.minus_1 as minus_1',
                                                        'pengerjaan_harian.minus_2 as minus_2',
                                                        'pengerjaan_harian.jht as jht',
                                                        'pengerjaan_harian.bpjs_kesehatan as bpjs_kesehatan',
                                                        'pengerjaan_harian.lembur as lembur',
                                                        'pengerjaan_harian.hasil_kerja as hasil_kerja',
                                                        'pengerjaan_harian.tunjangan_kerja as tunjangan_kerja',
                                                        'pengerjaan_harian.tunjangan_kehadiran as tunjangan_kehadiran',
                                                        'operator_harian_karyawan.tunjangan_kerja_id as tunjangan_kerja_id',
                                                    ])
                                                    ->leftJoin('operator_harian_karyawan','operator_harian_karyawan.id','=','pengerjaan_harian.operator_harian_karyawan_id')
                                                    ->leftJoin('itic_emp_new.biodata_karyawan','biodata_karyawan.nik','=','operator_harian_karyawan.nik')
                                                    ->where('operator_harian_karyawan.jenis_operator_detail_pekerjaan_id',15)
                                                    ->where('kode_pengerjaan',$kode_pengerjaan)
                                                    ->where('operator_harian_karyawan.status','y')
                                                    ->orderBy('biodata_karyawan.nama','asc')
                                                    ->get();
        return view('backend.pengerjaan.harian.packing_b.index',$data);
    }

    public function hasil_kerja_harian_packing_b_view($id, $kode_pengerjaan, $nik, $month, $year)
    {
        $data['id'] = $id;
        $data['nik'] = $nik;
        $data['kode_pengerjaan'] = $kode_pengerjaan;
        $data['month'] = $month;
        $data['year']= $year;

        if($id == 4){
            $kode_jenis_operator_detail = 'H';
        }

        $data['karyawan_harian'] = $this->karyawanOperatorHarian->select([
                                                            'operator_harian_karyawan.id as id',
                                                            'operator_harian_karyawan.nik as nik',
                                                            'biodata_karyawan.nama as nama',
                                                            'biodata_karyawan.pin as pin',
                                                            'biodata_karyawan.tanggal_masuk as tanggal',
                                                            'operator_harian_karyawan.upah_dasar as upah_dasar',
                                                            'pengerjaan_harian.upah_dasar_weekly as upah_dasar_weekly',
                                                            'pengerjaan_harian.hari_kerja as hari_kerja',
                                                            'pengerjaan_harian.hasil_kerja as hasil_kerja',
                                                            'pengerjaan_harian.tunjangan_kerja as tunjangan_kerja',
                                                            'operator_harian_karyawan.tunjangan_kerja_id as tunjangan_kerja_id',
                                                            'pengerjaan_harian.plus_1 as plus_1',
                                                            'pengerjaan_harian.plus_2 as plus_2',
                                                            'pengerjaan_harian.plus_3 as plus_3',
                                                            'pengerjaan_harian.minus_1 as minus_1',
                                                            'pengerjaan_harian.minus_2 as minus_2',
                                                            'pengerjaan_harian.minus_3 as minus_3',
                                                            'pengerjaan_harian.uang_makan as uang_makan',
                                                            'pengerjaan_harian.lembur as lembur',
                                                            'operator_harian_karyawan.jht as jht',
                                                            'operator_harian_karyawan.bpjs as bpjs',
                                                            'operator_harian_karyawan.status as status',
                                                        ])
                                                        ->with('tunjangan_kerja_nominal')
                                                        ->leftJoin('itic_emp_new.biodata_karyawan','biodata_karyawan.nik','=','operator_harian_karyawan.nik')
                                                        ->rightJoin('pengerjaan_harian','pengerjaan_harian.operator_harian_karyawan_id','=','operator_harian_karyawan.id')
                                                        ->where('operator_harian_karyawan.nik',$nik)
                                                        ->where('operator_harian_karyawan.status','y')
                                                        ->where('pengerjaan_harian.kode_pengerjaan',$kode_pengerjaan)
                                                        ->first();
        // dd($data['karyawan_harian']);
        $data['new_data_pengerjaan'] = $this->newDataPengerjaan->where('kode_pengerjaan',$kode_pengerjaan)->first();
        $data['explode_tanggal_pengerjaans'] = explode('#',$data['new_data_pengerjaan']['tanggal']);

        if (empty($data['karyawan_harian']['plus_1'])) {
            $plus_1=null;
            $ket_plus_1=null;
        }else{
            $exp_plus1=explode("|",$data['karyawan_harian']['plus_1']);
            // dd($exp_plus1);
            $plus_1=$exp_plus1[0];
            $ket_plus_1=$exp_plus1[1];
        }

        if (empty($data['karyawan_harian']['plus_2'])) {
            $plus_2=null;
            $ket_plus_2=null;
        }else{
            $exp_plus2=explode("|",$data['karyawan_harian']['plus_2']);
            $plus_2=$exp_plus2[0];
            $ket_plus_2=$exp_plus2[1];
        }

        if (empty($data['karyawan_harian']['plus_3'])) {
            $plus_3=null;
            $ket_plus_3=null;
        }else{
            $exp_plus3=explode("|",$data['karyawan_harian']['plus_3']);
            $plus_3=$exp_plus2[0];
            $ket_plus_3=$exp_plus2[1];
        }

        if (empty($data['karyawan_harian']['minus_1'])) {
            $minus_1=null;
            $ket_minus_1=null;
        }else{
            $exp_minus1=explode("|",$data['karyawan_harian']['minus_1']);
            $minus_1=$exp_minus1[0];
            $ket_minus_1=$exp_minus1[1];
        }

        if (empty($data['karyawan_harian']['minus_2'])) {
            $minus_2=null;
            $ket_minus_2=null;
        }else{
            $exp_minus2=explode("|",$data['karyawan_harian']['minus_2']);
            $minus_2=$exp_minus2[0];
            $ket_minus_2=$exp_minus2[1];
        }

        $data['jhts'] = $this->bpjsJht->where('status','y')->get();
        $data['bpjs_kesehatan'] = $this->bpjsKesehatan->select('nominal')->where('status','y')->first();

        $data['pengerjaan_harian_weekly'] = $this->pengerjaanHarian->where('operator_harian_karyawan_id',$data['karyawan_harian']['id'])
                                                            ->where('kode_payrol',$kode_pengerjaan)
                                                            ->first();
        $exp_lembur = explode("@",$data['pengerjaan_harian_weekly']['lembur']);
        // dd($exp_lembur);
        if(empty($exp_lembur[0])){
            $exp_lembur[0]==null;
        }

        //Hitung Masa Kerja
        // $data['karyawan_masa_kerja'] = DB::connection('emp')->table('log_posisi')
        //                                                     ->select('tanggal')
        //                                                     ->where('nik',$nik)
        //                                                     ->first();
                                                            
        $awal  = new DateTime($data['karyawan_harian']->tanggal);
        $akhir = new DateTime(); // Waktu sekarang
        $diff  = $awal->diff($akhir);
        $data['masa_kerja'] = $diff->y.' Tahun '.$diff->m.' Bulan '.$diff->d.' Hari';
        $data['masa_kerja_tahun'] = $diff->y;
        $data['masa_kerja_hari'] = $diff->d;

        $data['upah_dasar_karyawan'] = [];
        foreach ($data['jhts'] as $key => $jht) {
            if ($data['masa_kerja_tahun'] > 15) {
                if ($jht->urutan == 3) {
                    $data['upah_dasar_karyawan'] = ($data['bpjs_kesehatan']['nominal'] + 100000)/25;
                }
            }
            elseif($data['masa_kerja_tahun'] >= 10 && $data['masa_kerja_tahun'] <= 15 && $data['masa_kerja_hari'] >= 1){
                if ($jht->urutan == 2) {
                    $data['upah_dasar_karyawan'] = ($data['bpjs_kesehatan']['nominal'] + 50000)/25;
                }
            }
            elseif($data['masa_kerja_tahun'] <= 10 || $data['masa_kerja_hari'] >= 1){
                if ($jht->urutan == 1) {
                    $data['upah_dasar_karyawan'] = ($data['bpjs_kesehatan']['nominal'] + 0)/25;
                }
            }
        }

        $tgl_current_active = Carbon::now()->format('d');
        $bln_active = Carbon::now()->format('m');
        $bln_current_active = $month;
        $thn_current_active = $year;

        if($month == 1 && $tgl_current_active <10){
            $thn_prev_active = $year-1;
            $bulan_kemarin="$thn_prev_active-11-26";
            $bulan_sekarang="$thn_prev_active-12-26";
        }elseif($month == 1 && $tgl_current_active>10 or $month == 2 && $tgl_current_active<10 && $bln_active != 3){
            $thn_prev_active = $year-1;
            $bulan_kemarin="$thn_prev_active-12-26";
            $bulan_sekarang="$thn_current_active-01-26";
        }else{
            $bln_prev_active = $month-1;
            $thn_prev_active = $year;
            $bulan_kemarin="$thn_prev_active-$bln_prev_active-26";
            $bulan_sekarang="$thn_current_active-$bln_current_active-26";
        }

        $data['tunjangan_kerjas'] = $this->tunjanganKerja->select('nominal')->where('id',$data['karyawan_harian']['tunjangan_kerja_id'])->first();
        if(empty($data['tunjangan_kerjas'])){
            $data['tunjangan_kerja'] = 0;
        }else{
            $data['tunjangan_kerja'] = $data['tunjangan_kerjas']['nominal'];
        }

        //jumlah alpa
        $data['alpa'] = $this->presensiInfo->where('pin',$data['karyawan_harian']['pin'])
                                    ->whereBetween('scan_date',[$bulan_kemarin,$bulan_sekarang])
                                    ->where('status',7)
                                    ->get();
        //jumlah diliburkan
        $data['diliburkan'] = $this->presensiInfo->where('pin',$data['karyawan_harian']['pin'])
                                    ->whereBetween('scan_date',[$bulan_kemarin,$bulan_sekarang])
                                    ->where('status',14)
                                    ->get();
        //jumlah cuti
        $data['cuti'] = $this->presensiInfo->where('pin',$data['karyawan_harian']['pin'])
                                    ->whereBetween('scan_date',[$bulan_kemarin,$bulan_sekarang])
                                    ->where('status',13)
                                    ->get();
        //jumlah sakit
        $data['sakit'] = $this->presensiInfo->where('pin',$data['karyawan_harian']['pin'])
                                    ->whereBetween('scan_date',[$bulan_kemarin,$bulan_sekarang])
                                    ->where('status',4)
                                    ->get();
        // dd($data['sakit']);
        //jumlah ijin full
        $data['ijin_full'] = $this->presensiInfo->where('pin',$data['karyawan_harian']['pin'])
                                    ->whereBetween('scan_date',[$bulan_kemarin,$bulan_sekarang])
                                    ->where('status',6)
                                    ->get();
        //jumlah status terlambat
        $data['terlambats'] = $this->presensiInfo->where('pin',$data['karyawan_harian']['pin'])
                                    ->whereBetween('scan_date',[$bulan_kemarin,$bulan_sekarang])
                                    ->where('status',3)
                                    ->get();
                                    // dd($data);
        $telat_1=0; //terlambat < 5 menit
        $telat_2=0; //terlambat > 5 menit < 15 menit
        $telat_3=0; //terlambat > 15 menit < 1 jam
        $telat_4=0; //terlambat > 1 jam < 3 jam	
        $telat_5=0; //terlambat > 3 jam	
        $data['telat_1'] = $telat_1;
        $data['telat_2'] = $telat_2;
        $data['telat_3'] = $telat_3;
        $data['telat_4'] = $telat_4;
        $data['telat_5'] = $telat_5;
        foreach ($data['terlambats'] as $key => $terlambat) {
            $explode_keterangan_terlambat = explode("@",$terlambat->keterangan);
            $jam_keterangan_terlambat=strtotime($explode_keterangan_terlambat[1]);
            $jam_datang_terlambat=strtotime(Carbon::parse($terlambat['scan_date'])->format('H:i'));
            // $jam_datang_terlambat=strtotime($terlambat['jam_datang_telat']);
            $menit_telat=(($jam_datang_terlambat-$jam_keterangan_terlambat)/60);
            // dd($menit_telat);
            if($menit_telat <= 4){
                $telat_1=$telat_1+1;
                $data['telat_1'] = $telat_1;
            }
            else if(($menit_telat >= 5) && ($menit_telat <= 15) ){
                $telat_2=$telat_2+1;
                $data['telat_2'] = $telat_2;
            }
            else if(($menit_telat >= 16) && ($menit_telat <= 60)){
                $telat_3=$telat_3+1;
                $data['telat_3'] = $telat_3;
            }
            else if(($menit_telat >= 61) && ($menit_telat <= 179)){
                $telat_4=$telat_4+1;
                $data['telat_4'] = $telat_4;
            }else if($menit_telat > 179){
                $telat_5=$telat_5+1;
                $data['telat_5'] = $telat_5;
            }
            // else{
            //     $telat_4=$telat_4+1;
            //     $data['telat_4'] = $telat_4;
            // }
        }

        //jumlah status pulang awal
        $data['pulang_awals'] = $this->presensiInfo->where('pin',$data['karyawan_harian']['pin'])
                                    ->whereBetween('scan_date',[$bulan_kemarin,$bulan_sekarang])
                                    ->where(function($query) {
                                        $query->where('status','=',9)
                                            ->orWhere('status','=',10);
                                    })
                                    // ->where('status',9)
                                    // ->where('status',10)
                                    ->get();
        // dd($data['pulang_awals']);
        $pulang_1=0; //pulang awal < 4 jam
		$pulang_2=0; //tpulang awal > 4 jam
        $data['pulang_1'] = $pulang_1;
        $data['pulang_2'] = $pulang_2;
        foreach ($data['pulang_awals'] as $key => $pulang_awal) {
            $explode_pulang_seharusnya=explode("@",$pulang_awal->keterangan);
			$jam_pulang_seharusnya=strtotime($explode_pulang_seharusnya[3]);
			$jam_pulang_awal=strtotime(Carbon::parse($pulang_awal['scan_date'])->format('H:i'));
			// $jam_pulang_awal=strtotime($pulang_awal->jam_pulang_awal);
			$menit_sisa_jam_kerja=(($jam_pulang_seharusnya-$jam_pulang_awal)/60);
			if ($menit_sisa_jam_kerja<=300){
                $pulang_1=$pulang_1+1;
                $data['pulang_1'] = $pulang_1;
            }
			else{
                $pulang_2=$pulang_2+1;
                $data['pulang_2'] = $pulang_2;
            }
        }

        //jumlah ijin saat jam kerja
        $data['keluar_masuks'] = $this->keluarMasuk->where('nik',$data['karyawan_harian']['nik'])
                                    ->whereBetween('tanggal_ijin',[$bulan_kemarin,$bulan_sekarang])
                                    ->get();
        // dd($data['keluar_masuks']);
        $ijin_15=0; //ijin kurang dari 15 menit
		$ijin_k4=0; //ijin > 15menit < 4 jam
		$ijin_l4=0; //ijin > 4 jam
        $data['ijin_15']=$ijin_15;
        $data['ijin_k4']=$ijin_k4;
        $data['ijin_l4']=$ijin_l4;
        foreach ($data['keluar_masuks'] as $key => $keluar_masuk) {
            $jam_keluar=strtotime($keluar_masuk->jam_keluar);
			$jam_datang=strtotime($keluar_masuk->jam_datang);
			$menit_keluar_masuk=(($jam_datang-$jam_keluar)/60);
			if ($menit_keluar_masuk<=15){
                $ijin_15=$ijin_15+1;
                $data['ijin_15']=$ijin_15;
            }
			else if (($menit_keluar_masuk>15)&&($menit_keluar_masuk<=239)){
                $ijin_k4=$ijin_k4+1;
                $data['ijin_k4']=$ijin_k4;
            }
			else {
                $ijin_l4=$ijin_l4+1;
                $data['ijin_l4']=$ijin_l4;
            }
        }

        //lihat tanggal masuk

        // $data['log_posisi'] = $this->logPosisi->select('tanggal')->where('nik',$nik)->first();
		$selisih_tanggal_masuk=strtotime($bulan_kemarin)-strtotime($data['karyawan_harian']['tanggal']);
        // dd($bulan_kemarin,$data['log_posisi']['tanggal']);
		if ($selisih_tanggal_masuk>=0)$div_tk=0;else $div_tk=75000;

        $total_potongan_tk=(75000*$data['alpa']->count())+(75000*$data['diliburkan']->count())+(75000*$data['sakit']->count())+(75000*$data['cuti']->count())+(75000*$data['ijin_full']->count())+($ijin_15*25000)+($ijin_k4*40000)+
        ($ijin_l4*75000)+($pulang_1*40000)+($pulang_2*75000)+($telat_1*15000)+($telat_2*25000)+($telat_3*30000)+($telat_4*40000)+$div_tk;
        if ($total_potongan_tk>75000){$total_potongan_tk=75000;}else {}

        // if ($data['pengerjaan_harian_weekly']['tunjangan_kerja'] == 75000) {
        //     $data['total_potongan_tk']=0;
        // }
        // // elseif($data['pengerjaan_harian_weekly']['tunjangan_kerja'] < 75000){
        // //     $data['total_potongan_tk']=$data['pengerjaan_harian_weekly']['tunjangan_kerja'];
        // // }
        // else{
        //     $data['total_potongan_tk']=$total_potongan_tk; 
        // }

        $data['total_potongan_tk']=$total_potongan_tk;

        return view('backend.pengerjaan.harian.packing_b.input_hasil_kerja_karyawan',$data);

    }

    public function hasil_kerja_harian_packing_b_simpan(Request $request, $id, $kode_pengerjaan, $tanggal)
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

        $input['uang_makan'] = $request->uang_makan;

        $input['plus_1'] = $request->plus_1."|".$request->keterangan_plus_1;
        $input['plus_2'] = $request->plus_2."|".$request->keterangan_plus_2;
        $input['plus_3'] = $request->plus_3."|".$request->keterangan_plus_3;

        $input['minus_1'] = $request->minus_1."|".$request->keterangan_minus_1;
        $input['minus_2'] = $request->minus_2."|".$request->keterangan_minus_2;

        $hitung_lembur = round((($request->upah_dasar*25/173*$request->lembur_1)*1.5)+(($request->upah_dasar*25/173*$request->lembur_2)*2));
        
        $input['lembur'] = $hitung_lembur.'|'.$request->lembur_1.'|'.$request->lembur_2;

        $upah_dasar_weekly = 0;
        $hasil_kerja="";

        for ($i=1; $i <=$request->data_for; $i++) { 
            $var_hasil_kerja="jam_kerja_".$i;
            $hasil_kerja=$hasil_kerja.$request[$var_hasil_kerja]."|";
            $upah_dasar_harian=$request[$var_hasil_kerja]*$request->upah_dasar;
            $upah_dasar_weekly=$upah_dasar_weekly+$upah_dasar_harian;
            // $upah_dasar_harian=$request[$var_hasil_kerja]*($request->upah_dasar-0.1);
            // $upah_dasar_weekly=$upah_dasar_weekly+$upah_dasar_harian+0.2;
        }

        $input['hasil_kerja'] = $hasil_kerja;
        // $input['upah_dasar_harian'] = round($upah_dasar_harian);
        $input['upah_dasar_weekly'] = round($upah_dasar_weekly);
        if ($request->akhir_bulan == 'n') {
            $input['tunjangan_kerja'] = 0;
            $input['tunjangan_kehadiran'] = 0;
        }else{
            $input['tunjangan_kerja'] = $request->tunjangan_kerja;
            $input['tunjangan_kehadiran'] = 75000-$request->pot_tunjangan_kehadiran;
        }
        // $input['tunjangan_kehadiran'] = 75000-$request->pot_tunjangan_kehadiran;
        // $input['tunjangan_kerja'] = $request->tunjangan_kerja;

        if ($request->check_jht == 'on') {
            $input['jht'] = $request->jht;
        }else{
            $input['jht'] = 0;
        }

        if($request->check_bpjs_kesehatan == 'on'){
            $input['bpjs_kesehatan'] = $request->bpjs_kesehatan;
        }else{
            $input['bpjs_kesehatan'] = 0;
        }

        $pengerjaan_harian_karyawan = $this->pengerjaanHarian->where('operator_harian_karyawan_id',$request->operator_harian_karyawan_id)
                                                    ->where('kode_pengerjaan',$kode_pengerjaan)
                                                    // ->update($input)
                                                    ->first()
                                                    ;
        // return 'Success';
        if (!empty($pengerjaan_harian_karyawan)) {
            $pengerjaan_harian_karyawan->update($input);
            return response()->json([
                'success' => true,
                'message_title' => 'Berhasil',
                'message_content' => 'Data Berhasil Disimpan'
            ]);
        }else{
            return response()->json([
                'success' => false,
                'message_title' => 'Gagal',
                'message_content' => 'Data Tidak Berhasil Disimpan'
            ]);
        }
    }

    public function hasil_kerja_harian_ambri($id, $kode_pengerjaan)
    {
        $data['id'] = $id;
        $data['kode_pengerjaan'] = $kode_pengerjaan;
        $data['jenis_operator_detail_pekerjaan'] = $this->jenisOperatorDetailPengerjaan->find(16);
        $data['new_data_pengerjaan'] = $this->newDataPengerjaan->where('kode_pengerjaan',$kode_pengerjaan)->first();
        $data['explode_tanggal_pengerjaans'] = explode('#',$data['new_data_pengerjaan']['tanggal']);

        $data['pengerjaan_harians'] = $this->pengerjaanHarian->select([
                                                        'pengerjaan_harian.id as id',
                                                        'operator_harian_karyawan.nik as nik',
                                                        'biodata_karyawan.nama as nama',
                                                        'pengerjaan_harian.operator_harian_karyawan_id as operator_harian_karyawan_id',
                                                        'pengerjaan_harian.uang_makan as uang_makan',
                                                        'pengerjaan_harian.upah_dasar_weekly as upah_dasar_weekly',
                                                        'pengerjaan_harian.plus_1 as plus_1',
                                                        'pengerjaan_harian.plus_2 as plus_2',
                                                        'pengerjaan_harian.plus_3 as plus_3',
                                                        'pengerjaan_harian.minus_1 as minus_1',
                                                        'pengerjaan_harian.minus_2 as minus_2',
                                                        'pengerjaan_harian.jht as jht',
                                                        'pengerjaan_harian.bpjs_kesehatan as bpjs_kesehatan',
                                                        'pengerjaan_harian.lembur as lembur',
                                                        'pengerjaan_harian.hasil_kerja as hasil_kerja',
                                                        'pengerjaan_harian.tunjangan_kerja as tunjangan_kerja',
                                                        'pengerjaan_harian.tunjangan_kehadiran as tunjangan_kehadiran',
                                                        'operator_harian_karyawan.tunjangan_kerja_id as tunjangan_kerja_id',
                                                    ])
                                                    ->leftJoin('operator_harian_karyawan','operator_harian_karyawan.id','=','pengerjaan_harian.operator_harian_karyawan_id')
                                                    ->leftJoin('itic_emp_new.biodata_karyawan','biodata_karyawan.nik','=','operator_harian_karyawan.nik')
                                                    ->where('operator_harian_karyawan.jenis_operator_detail_pekerjaan_id',16)
                                                    ->where('kode_pengerjaan',$kode_pengerjaan)
                                                    ->where('operator_harian_karyawan.status','y')
                                                    ->orderBy('biodata_karyawan.nama','asc')
                                                    ->get();
        return view('backend.pengerjaan.harian.ambri.index',$data);
    }

    public function hasil_kerja_harian_ambri_view($id, $kode_pengerjaan, $nik, $month, $year)
    {
        $data['id'] = $id;
        $data['nik'] = $nik;
        $data['kode_pengerjaan'] = $kode_pengerjaan;
        $data['month'] = $month;
        $data['year']= $year;

        if($id == 4){
            $kode_jenis_operator_detail = 'H';
        }

        $data['karyawan_harian'] = $this->karyawanOperatorHarian->select([
                                                            'operator_harian_karyawan.id as id',
                                                            'operator_harian_karyawan.nik as nik',
                                                            'biodata_karyawan.nama as nama',
                                                            'biodata_karyawan.pin as pin',
                                                            'biodata_karyawan.tanggal_masuk as tanggal',
                                                            'operator_harian_karyawan.upah_dasar as upah_dasar',
                                                            'pengerjaan_harian.upah_dasar_weekly as upah_dasar_weekly',
                                                            'pengerjaan_harian.hari_kerja as hari_kerja',
                                                            'pengerjaan_harian.hasil_kerja as hasil_kerja',
                                                            'pengerjaan_harian.tunjangan_kerja as tunjangan_kerja',
                                                            'operator_harian_karyawan.tunjangan_kerja_id as tunjangan_kerja_id',
                                                            'pengerjaan_harian.plus_1 as plus_1',
                                                            'pengerjaan_harian.plus_2 as plus_2',
                                                            'pengerjaan_harian.plus_3 as plus_3',
                                                            'pengerjaan_harian.minus_1 as minus_1',
                                                            'pengerjaan_harian.minus_2 as minus_2',
                                                            'pengerjaan_harian.minus_3 as minus_3',
                                                            'pengerjaan_harian.uang_makan as uang_makan',
                                                            'pengerjaan_harian.lembur as lembur',
                                                            'operator_harian_karyawan.jht as jht',
                                                            'operator_harian_karyawan.bpjs as bpjs',
                                                            'operator_harian_karyawan.status as status',
                                                        ])
                                                        ->with('tunjangan_kerja_nominal')
                                                        ->leftJoin('itic_emp_new.biodata_karyawan','biodata_karyawan.nik','=','operator_harian_karyawan.nik')
                                                        ->rightJoin('pengerjaan_harian','pengerjaan_harian.operator_harian_karyawan_id','=','operator_harian_karyawan.id')
                                                        ->where('operator_harian_karyawan.nik',$nik)
                                                        ->where('operator_harian_karyawan.status','y')
                                                        ->where('pengerjaan_harian.kode_pengerjaan',$kode_pengerjaan)
                                                        ->first();
        // dd($data['karyawan_harian']);
        $data['new_data_pengerjaan'] = $this->newDataPengerjaan->where('kode_pengerjaan',$kode_pengerjaan)->first();
        $data['explode_tanggal_pengerjaans'] = explode('#',$data['new_data_pengerjaan']['tanggal']);

        if (empty($data['karyawan_harian']['plus_1'])) {
            $plus_1=null;
            $ket_plus_1=null;
        }else{
            $exp_plus1=explode("|",$data['karyawan_harian']['plus_1']);
            // dd($exp_plus1);
            $plus_1=$exp_plus1[0];
            $ket_plus_1=$exp_plus1[1];
        }

        if (empty($data['karyawan_harian']['plus_2'])) {
            $plus_2=null;
            $ket_plus_2=null;
        }else{
            $exp_plus2=explode("|",$data['karyawan_harian']['plus_2']);
            $plus_2=$exp_plus2[0];
            $ket_plus_2=$exp_plus2[1];
        }

        if (empty($data['karyawan_harian']['plus_3'])) {
            $plus_3=null;
            $ket_plus_3=null;
        }else{
            $exp_plus3=explode("|",$data['karyawan_harian']['plus_3']);
            $plus_3=$exp_plus2[0];
            $ket_plus_3=$exp_plus2[1];
        }

        if (empty($data['karyawan_harian']['minus_1'])) {
            $minus_1=null;
            $ket_minus_1=null;
        }else{
            $exp_minus1=explode("|",$data['karyawan_harian']['minus_1']);
            $minus_1=$exp_minus1[0];
            $ket_minus_1=$exp_minus1[1];
        }

        if (empty($data['karyawan_harian']['minus_2'])) {
            $minus_2=null;
            $ket_minus_2=null;
        }else{
            $exp_minus2=explode("|",$data['karyawan_harian']['minus_2']);
            $minus_2=$exp_minus2[0];
            $ket_minus_2=$exp_minus2[1];
        }

        $data['jhts'] = $this->bpjsJht->where('status','y')->get();
        $data['bpjs_kesehatan'] = $this->bpjsKesehatan->select('nominal')->where('status','y')->first();

        $data['pengerjaan_harian_weekly'] = $this->pengerjaanHarian->where('operator_harian_karyawan_id',$data['karyawan_harian']['id'])
                                                            ->where('kode_payrol',$kode_pengerjaan)
                                                            ->first();
        $exp_lembur = explode("@",$data['pengerjaan_harian_weekly']['lembur']);
        // dd($exp_lembur);
        if(empty($exp_lembur[0])){
            $exp_lembur[0]==null;
        }

        //Hitung Masa Kerja
        // $data['karyawan_masa_kerja'] = DB::connection('emp')->table('log_posisi')
        //                                                     ->select('tanggal')
        //                                                     ->where('nik',$nik)
        //                                                     ->first();
                                                            
        $awal  = new DateTime($data['karyawan_harian']->tanggal);
        $akhir = new DateTime(); // Waktu sekarang
        $diff  = $awal->diff($akhir);
        $data['masa_kerja'] = $diff->y.' Tahun '.$diff->m.' Bulan '.$diff->d.' Hari';
        $data['masa_kerja_tahun'] = $diff->y;
        $data['masa_kerja_hari'] = $diff->d;

        $data['upah_dasar_karyawan'] = [];
        foreach ($data['jhts'] as $key => $jht) {
            if ($data['masa_kerja_tahun'] > 15) {
                if ($jht->urutan == 3) {
                    $data['upah_dasar_karyawan'] = ($data['bpjs_kesehatan']['nominal'] + 100000)/25;
                }
            }
            elseif($data['masa_kerja_tahun'] >= 10 && $data['masa_kerja_tahun'] <= 15 && $data['masa_kerja_hari'] >= 1){
                if ($jht->urutan == 2) {
                    $data['upah_dasar_karyawan'] = ($data['bpjs_kesehatan']['nominal'] + 50000)/25;
                }
            }
            elseif($data['masa_kerja_tahun'] <= 10 || $data['masa_kerja_hari'] >= 1){
                if ($jht->urutan == 1) {
                    $data['upah_dasar_karyawan'] = ($data['bpjs_kesehatan']['nominal'] + 0)/25;
                }
            }
        }

        $tgl_current_active = Carbon::now()->format('d');
        $bln_active = Carbon::now()->format('m');
        $bln_current_active = $month;
        $thn_current_active = $year;

        if($month == 1 && $tgl_current_active <10){
            $thn_prev_active = $year-1;
            $bulan_kemarin="$thn_prev_active-11-26";
            $bulan_sekarang="$thn_prev_active-12-25";
        }elseif($month == 1 && $tgl_current_active>10 or $month == 2 && $tgl_current_active<10 && $bln_active != 3){
            $thn_prev_active = $year-1;
            $bulan_kemarin="$thn_prev_active-12-26";
            $bulan_sekarang="$thn_current_active-01-25";
        }else{
            $bln_prev_active = $month-1; 
            $thn_prev_active = $year;
            $bulan_kemarin="$thn_prev_active-$bln_prev_active-26";
            $bulan_sekarang="$thn_current_active-$bln_current_active-25";
        }

        $data['tunjangan_kerjas'] = $this->tunjanganKerja->select('nominal')->where('id',$data['karyawan_harian']['tunjangan_kerja_id'])->first();
        if(empty($data['tunjangan_kerjas'])){
            $data['tunjangan_kerja'] = 0;
        }else{
            $data['tunjangan_kerja'] = $data['tunjangan_kerjas']['nominal'];
        }

        //jumlah alpa
        $data['alpa'] = $this->presensiInfo->where('pin',$data['karyawan_harian']['pin'])
                                    ->whereBetween('scan_date',[$bulan_kemarin,$bulan_sekarang])
                                    ->where('status',7)
                                    ->get();
        //jumlah diliburkan
        $data['diliburkan'] = $this->presensiInfo->where('pin',$data['karyawan_harian']['pin'])
                                    ->whereBetween('scan_date',[$bulan_kemarin,$bulan_sekarang])
                                    ->where('status',14)
                                    ->get();
        //jumlah cuti
        $data['cuti'] = $this->presensiInfo->where('pin',$data['karyawan_harian']['pin'])
                                    ->whereBetween('scan_date',[$bulan_kemarin,$bulan_sekarang])
                                    ->where('status',13)
                                    ->get();
        //jumlah sakit
        $data['sakit'] = $this->presensiInfo->where('pin',$data['karyawan_harian']['pin'])
                                    ->whereBetween('scan_date',[$bulan_kemarin,$bulan_sekarang])
                                    ->where('status',4)
                                    ->get();
        // dd($data['sakit']);
        //jumlah ijin full
        $data['ijin_full'] = $this->presensiInfo->where('pin',$data['karyawan_harian']['pin'])
                                    ->whereBetween('scan_date',[$bulan_kemarin,$bulan_sekarang])
                                    ->where('status',6)
                                    ->get();
        //jumlah status terlambat
        $data['terlambats'] = $this->presensiInfo->where('pin',$data['karyawan_harian']['pin'])
                                    ->whereBetween('scan_date',[$bulan_kemarin,$bulan_sekarang])
                                    ->where('status',3)
                                    ->get();
        $telat_1=0; //terlambat < 5 menit
        $telat_2=0; //terlambat > 5 menit < 15 menit
        $telat_3=0; //terlambat > 15 menit < 1 jam
        $telat_4=0; //terlambat > 1 jam < 3 jam	
        $telat_5=0; //terlambat > 3 jam	
        $data['telat_1'] = $telat_1;
        $data['telat_2'] = $telat_2;
        $data['telat_3'] = $telat_3;
        $data['telat_4'] = $telat_4;
        $data['telat_5'] = $telat_5;
        foreach ($data['terlambats'] as $key => $terlambat) {
            $explode_keterangan_terlambat = explode("@",$terlambat->keterangan);
            $jam_keterangan_terlambat=strtotime($explode_keterangan_terlambat[1]);
            $jam_datang_terlambat=strtotime(Carbon::parse($terlambat['scan_date'])->format('H:i'));
            // $jam_datang_terlambat=strtotime($terlambat['jam_datang_telat']);
            $menit_telat=(($jam_datang_terlambat-$jam_keterangan_terlambat)/60);
            // dd($menit_telat);
            if($menit_telat <= 4){
                $telat_1=$telat_1+1;
                $data['telat_1'] = $telat_1;
            }
            else if(($menit_telat >= 5) && ($menit_telat <= 15) ){
                $telat_2=$telat_2+1;
                $data['telat_2'] = $telat_2;
            }
            else if(($menit_telat >= 16) && ($menit_telat <= 60)){
                $telat_3=$telat_3+1;
                $data['telat_3'] = $telat_3;
            }
            else if(($menit_telat >= 61) && ($menit_telat <= 179)){
                $telat_4=$telat_4+1;
                $data['telat_4'] = $telat_4;
            }else if($menit_telat > 179){
                $telat_5=$telat_5+1;
                $data['telat_5'] = $telat_5;
            }
            // else{
            //     $telat_4=$telat_4+1;
            //     $data['telat_4'] = $telat_4;
            // }
        }

        //jumlah status pulang awal
        $data['pulang_awals'] = $this->presensiInfo->where('pin',$data['karyawan_harian']['pin'])
                                    ->whereBetween('scan_date',[$bulan_kemarin,$bulan_sekarang])
                                    ->where(function($query) {
                                        $query->where('status','=',9)
                                            ->orWhere('status','=',10);
                                    })
                                    // ->where('status',9)
                                    // ->where('status',10)
                                    ->get();
        // dd($data['pulang_awals']);
        $pulang_1=0; //pulang awal < 4 jam
		$pulang_2=0; //tpulang awal > 4 jam
        $data['pulang_1'] = $pulang_1;
        $data['pulang_2'] = $pulang_2;
        foreach ($data['pulang_awals'] as $key => $pulang_awal) {
            $explode_pulang_seharusnya=explode("@",$pulang_awal->keterangan);
			$jam_pulang_seharusnya=strtotime($explode_pulang_seharusnya[3]);
			$jam_pulang_awal=strtotime(Carbon::parse($pulang_awal['scan_date'])->format('H:i'));
			// $jam_pulang_awal=strtotime($pulang_awal->jam_pulang_awal);
			$menit_sisa_jam_kerja=(($jam_pulang_seharusnya-$jam_pulang_awal)/60);
			if ($menit_sisa_jam_kerja<=300){
                $pulang_1=$pulang_1+1;
                $data['pulang_1'] = $pulang_1;
            }
			else{
                $pulang_2=$pulang_2+1;
                $data['pulang_2'] = $pulang_2;
            }
        }

        //jumlah ijin saat jam kerja
        $data['keluar_masuks'] = $this->keluarMasuk->where('nik',$data['karyawan_harian']['nik'])
                                    ->whereBetween('tanggal_ijin',[$bulan_kemarin,$bulan_sekarang])
                                    ->get();
        // dd($data['keluar_masuks']);
        $ijin_15=0; //ijin kurang dari 15 menit
		$ijin_k4=0; //ijin > 15menit < 4 jam
		$ijin_l4=0; //ijin > 4 jam
        $data['ijin_15']=$ijin_15;
        $data['ijin_k4']=$ijin_k4;
        $data['ijin_l4']=$ijin_l4;
        foreach ($data['keluar_masuks'] as $key => $keluar_masuk) {
            $jam_keluar=strtotime($keluar_masuk->jam_keluar);
			$jam_datang=strtotime($keluar_masuk->jam_datang);
			$menit_keluar_masuk=(($jam_datang-$jam_keluar)/60);
			if ($menit_keluar_masuk<=15){
                $ijin_15=$ijin_15+1;
                $data['ijin_15']=$ijin_15;
            }
			else if (($menit_keluar_masuk>15)&&($menit_keluar_masuk<=239)){
                $ijin_k4=$ijin_k4+1;
                $data['ijin_k4']=$ijin_k4;
            }
			else {
                $ijin_l4=$ijin_l4+1;
                $data['ijin_l4']=$ijin_l4;
            }
        }

        //lihat tanggal masuk

        // $data['log_posisi'] = $this->logPosisi->select('tanggal')->where('nik',$nik)->first();
		$selisih_tanggal_masuk=strtotime($bulan_kemarin)-strtotime($data['karyawan_harian']['tanggal']);
		if ($selisih_tanggal_masuk>=0)$div_tk=0;else $div_tk=75000;

        $total_potongan_tk=(75000*$data['alpa']->count())+(75000*$data['diliburkan']->count())+(75000*$data['sakit']->count())+(75000*$data['cuti']->count())+(75000*$data['ijin_full']->count())+($data['ijin_15']*25000)+($data['ijin_k4']*40000)+
        ($data['ijin_l4']*75000)+($data['pulang_1']*40000)+($data['pulang_2']*75000)+($data['telat_1']*15000)+($data['telat_2']*25000)+($data['telat_3']*30000)+($data['telat_4']*40000)+$div_tk;
        if ($total_potongan_tk>75000){$total_potongan_tk=75000;}else {}

        // if ($data['pengerjaan_harian_weekly']['tunjangan_kerja'] == 75000) {
        //     $data['total_potongan_tk']=0;
        // }
        // // elseif($data['pengerjaan_harian_weekly']['tunjangan_kerja'] < 75000){
        // //     $data['total_potongan_tk']=$data['pengerjaan_harian_weekly']['tunjangan_kerja'];
        // // }
        // else{
        //     $data['total_potongan_tk']=$total_potongan_tk; 
        // }

        $data['total_potongan_tk']=$total_potongan_tk;

        return view('backend.pengerjaan.harian.ambri.input_hasil_kerja_karyawan',$data);

    }

    public function hasil_kerja_harian_ambri_simpan(Request $request, $id, $kode_pengerjaan, $tanggal)
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

        $input['uang_makan'] = $request->uang_makan;

        $input['plus_1'] = $request->plus_1."|".$request->keterangan_plus_1;
        $input['plus_2'] = $request->plus_2."|".$request->keterangan_plus_2;
        $input['plus_3'] = $request->plus_3."|".$request->keterangan_plus_3;

        $input['minus_1'] = $request->minus_1."|".$request->keterangan_minus_1;
        $input['minus_2'] = $request->minus_2."|".$request->keterangan_minus_2;

        $hitung_lembur = round((($request->upah_dasar*25/173*$request->lembur_1)*1.5)+(($request->upah_dasar*25/173*$request->lembur_2)*2));
        
        $input['lembur'] = $hitung_lembur.'|'.$request->lembur_1.'|'.$request->lembur_2;

        $upah_dasar_weekly = 0;
        $hasil_kerja="";

        for ($i=1; $i <=$request->data_for; $i++) { 
            $var_hasil_kerja="jam_kerja_".$i;
            $hasil_kerja=$hasil_kerja.$request[$var_hasil_kerja]."|";
            $upah_dasar_harian=$request[$var_hasil_kerja]*$request->upah_dasar;
            $upah_dasar_weekly=$upah_dasar_weekly+$upah_dasar_harian;
            // $upah_dasar_harian=$request[$var_hasil_kerja]*($request->upah_dasar-0.1);
            // $upah_dasar_weekly=$upah_dasar_weekly+$upah_dasar_harian+0.2;
        }

        $input['hasil_kerja'] = $hasil_kerja;
        // $input['upah_dasar_harian'] = round($upah_dasar_harian);
        $input['upah_dasar_weekly'] = round($upah_dasar_weekly);
        if ($request->akhir_bulan == 'n') {
            $input['tunjangan_kerja'] = 0;
            $input['tunjangan_kehadiran'] = 0;
        }else{
            $input['tunjangan_kerja'] = $request->tunjangan_kerja;
            $input['tunjangan_kehadiran'] = 75000-$request->pot_tunjangan_kehadiran;
        }
        // $input['tunjangan_kehadiran'] = 75000-$request->pot_tunjangan_kehadiran;
        // $input['tunjangan_kerja'] = $request->tunjangan_kerja;

        if ($request->check_jht == 'on') {
            $input['jht'] = $request->jht;
        }else{
            $input['jht'] = 0;
        }

        if($request->check_bpjs_kesehatan == 'on'){
            $input['bpjs_kesehatan'] = $request->bpjs_kesehatan;
        }else{
            $input['bpjs_kesehatan'] = 0;
        }

        $pengerjaan_harian_karyawan = $this->pengerjaanHarian->where('operator_harian_karyawan_id',$request->operator_harian_karyawan_id)
                                                    ->where('kode_pengerjaan',$kode_pengerjaan)
                                                    // ->update($input)
                                                    ->first()
                                                    ;
        // return 'Success';
        if (!empty($pengerjaan_harian_karyawan)) {
            $pengerjaan_harian_karyawan->update($input);
            return response()->json([
                'success' => true,
                'message_title' => 'Berhasil',
                'message_content' => 'Data Berhasil Disimpan'
            ]);
        }else{
            return response()->json([
                'success' => false,
                'message_title' => 'Gagal',
                'message_content' => 'Data Tidak Berhasil Disimpan'
            ]);
        }
    }

    public function hasil_kerja_harian_umum($id, $kode_pengerjaan)
    {
        $data['id'] = $id;
        $data['kode_pengerjaan'] = $kode_pengerjaan;
        $data['jenis_operator_detail_pekerjaan'] = $this->jenisOperatorDetailPengerjaan->find(17);
        $data['new_data_pengerjaan'] = $this->newDataPengerjaan->where('kode_pengerjaan',$kode_pengerjaan)->first();
        $data['explode_tanggal_pengerjaans'] = explode('#',$data['new_data_pengerjaan']['tanggal']);

        $data['pengerjaan_harians'] = $this->pengerjaanHarian->select([
                                                        'pengerjaan_harian.id as id',
                                                        'operator_harian_karyawan.nik as nik',
                                                        'biodata_karyawan.nama as nama',
                                                        'pengerjaan_harian.operator_harian_karyawan_id as operator_harian_karyawan_id',
                                                        'pengerjaan_harian.uang_makan as uang_makan',
                                                        'pengerjaan_harian.upah_dasar_weekly as upah_dasar_weekly',
                                                        'pengerjaan_harian.plus_1 as plus_1',
                                                        'pengerjaan_harian.plus_2 as plus_2',
                                                        'pengerjaan_harian.plus_3 as plus_3',
                                                        'pengerjaan_harian.minus_1 as minus_1',
                                                        'pengerjaan_harian.minus_2 as minus_2',
                                                        'pengerjaan_harian.jht as jht',
                                                        'pengerjaan_harian.bpjs_kesehatan as bpjs_kesehatan',
                                                        'pengerjaan_harian.lembur as lembur',
                                                        'pengerjaan_harian.hasil_kerja as hasil_kerja',
                                                        'pengerjaan_harian.tunjangan_kerja as tunjangan_kerja',
                                                        'pengerjaan_harian.tunjangan_kehadiran as tunjangan_kehadiran',
                                                        'operator_harian_karyawan.tunjangan_kerja_id as tunjangan_kerja_id',
                                                    ])
                                                    ->leftJoin('operator_harian_karyawan','operator_harian_karyawan.id','=','pengerjaan_harian.operator_harian_karyawan_id')
                                                    ->leftJoin('itic_emp_new.biodata_karyawan','biodata_karyawan.nik','=','operator_harian_karyawan.nik')
                                                    ->where('operator_harian_karyawan.jenis_operator_detail_pekerjaan_id',17)
                                                    ->where('kode_pengerjaan',$kode_pengerjaan)
                                                    ->where('operator_harian_karyawan.status','y')
                                                    ->orderBy('biodata_karyawan.nama','asc')
                                                    ->get();
        return view('backend.pengerjaan.harian.umum.index',$data);
    }

    public function hasil_kerja_harian_umum_view($id, $kode_pengerjaan, $nik, $month, $year)
    {
        $data['id'] = $id;
        $data['nik'] = $nik;
        $data['kode_pengerjaan'] = $kode_pengerjaan;
        $data['month'] = $month;
        $data['year']= $year;

        if($id == 4){
            $kode_jenis_operator_detail = 'H';
        }

        $data['karyawan_harian'] = $this->karyawanOperatorHarian->select([
                                                            'operator_harian_karyawan.id as id',
                                                            'operator_harian_karyawan.nik as nik',
                                                            'biodata_karyawan.nama as nama',
                                                            'biodata_karyawan.pin as pin',
                                                            'biodata_karyawan.tanggal_masuk as tanggal',
                                                            'operator_harian_karyawan.upah_dasar as upah_dasar',
                                                            'pengerjaan_harian.upah_dasar_weekly as upah_dasar_weekly',
                                                            'pengerjaan_harian.hari_kerja as hari_kerja',
                                                            'pengerjaan_harian.hasil_kerja as hasil_kerja',
                                                            'pengerjaan_harian.tunjangan_kerja as tunjangan_kerja',
                                                            'operator_harian_karyawan.tunjangan_kerja_id as tunjangan_kerja_id',
                                                            'pengerjaan_harian.plus_1 as plus_1',
                                                            'pengerjaan_harian.plus_2 as plus_2',
                                                            'pengerjaan_harian.plus_3 as plus_3',
                                                            'pengerjaan_harian.minus_1 as minus_1',
                                                            'pengerjaan_harian.minus_2 as minus_2',
                                                            'pengerjaan_harian.minus_3 as minus_3',
                                                            'pengerjaan_harian.uang_makan as uang_makan',
                                                            'pengerjaan_harian.lembur as lembur',
                                                            'operator_harian_karyawan.jht as jht',
                                                            'operator_harian_karyawan.bpjs as bpjs',
                                                            'operator_harian_karyawan.status as status',
                                                        ])
                                                        ->with('tunjangan_kerja_nominal')
                                                        ->leftJoin('itic_emp_new.biodata_karyawan','biodata_karyawan.nik','=','operator_harian_karyawan.nik')
                                                        ->rightJoin('pengerjaan_harian','pengerjaan_harian.operator_harian_karyawan_id','=','operator_harian_karyawan.id')
                                                        ->where('operator_harian_karyawan.nik',$nik)
                                                        ->where('operator_harian_karyawan.status','y')
                                                        ->where('pengerjaan_harian.kode_pengerjaan',$kode_pengerjaan)
                                                        ->first();
        // dd($data['karyawan_harian']);
        $data['new_data_pengerjaan'] = $this->newDataPengerjaan->where('kode_pengerjaan',$kode_pengerjaan)->first();
        $data['explode_tanggal_pengerjaans'] = explode('#',$data['new_data_pengerjaan']['tanggal']);

        if (empty($data['karyawan_harian']['plus_1'])) {
            $plus_1=null;
            $ket_plus_1=null;
        }else{
            $exp_plus1=explode("|",$data['karyawan_harian']['plus_1']);
            // dd($exp_plus1);
            $plus_1=$exp_plus1[0];
            $ket_plus_1=$exp_plus1[1];
        }

        if (empty($data['karyawan_harian']['plus_2'])) {
            $plus_2=null;
            $ket_plus_2=null;
        }else{
            $exp_plus2=explode("|",$data['karyawan_harian']['plus_2']);
            $plus_2=$exp_plus2[0];
            $ket_plus_2=$exp_plus2[1];
        }

        if (empty($data['karyawan_harian']['plus_3'])) {
            $plus_3=null;
            $ket_plus_3=null;
        }else{
            $exp_plus3=explode("|",$data['karyawan_harian']['plus_3']);
            $plus_3=$exp_plus2[0];
            $ket_plus_3=$exp_plus2[1];
        }

        if (empty($data['karyawan_harian']['minus_1'])) {
            $minus_1=null;
            $ket_minus_1=null;
        }else{
            $exp_minus1=explode("|",$data['karyawan_harian']['minus_1']);
            $minus_1=$exp_minus1[0];
            $ket_minus_1=$exp_minus1[1];
        }

        if (empty($data['karyawan_harian']['minus_2'])) {
            $minus_2=null;
            $ket_minus_2=null;
        }else{
            $exp_minus2=explode("|",$data['karyawan_harian']['minus_2']);
            $minus_2=$exp_minus2[0];
            $ket_minus_2=$exp_minus2[1];
        }

        $data['jhts'] = $this->bpjsJht->where('status','y')->get();
        $data['bpjs_kesehatan'] = $this->bpjsKesehatan->select('nominal')->where('status','y')->first();

        $data['pengerjaan_harian_weekly'] = $this->pengerjaanHarian->where('operator_harian_karyawan_id',$data['karyawan_harian']['id'])
                                                            ->where('kode_payrol',$kode_pengerjaan)
                                                            ->first();
        $exp_lembur = explode("@",$data['pengerjaan_harian_weekly']['lembur']);
        // dd($exp_lembur);
        if(empty($exp_lembur[0])){
            $exp_lembur[0]==null;
        }

        //Hitung Masa Kerja
        // $data['karyawan_masa_kerja'] = DB::connection('emp')->table('log_posisi')
        //                                                     ->select('tanggal')
        //                                                     ->where('nik',$nik)
        //                                                     ->first();
                                                            
        $awal  = new DateTime($data['karyawan_harian']->tanggal);
        $akhir = new DateTime(); // Waktu sekarang
        $diff  = $awal->diff($akhir);
        $data['masa_kerja'] = $diff->y.' Tahun '.$diff->m.' Bulan '.$diff->d.' Hari';
        $data['masa_kerja_tahun'] = $diff->y;
        $data['masa_kerja_hari'] = $diff->d;

        $data['upah_dasar_karyawan'] = [];
        foreach ($data['jhts'] as $key => $jht) {
            if ($data['masa_kerja_tahun'] > 15) {
                if ($jht->urutan == 3) {
                    $data['upah_dasar_karyawan'] = ($data['bpjs_kesehatan']['nominal'] + 100000)/25;
                }
            }
            elseif($data['masa_kerja_tahun'] >= 10 && $data['masa_kerja_tahun'] <= 15 && $data['masa_kerja_hari'] >= 1){
                if ($jht->urutan == 2) {
                    $data['upah_dasar_karyawan'] = ($data['bpjs_kesehatan']['nominal'] + 50000)/25;
                }
            }
            elseif($data['masa_kerja_tahun'] <= 10 || $data['masa_kerja_hari'] >= 1){
                if ($jht->urutan == 1) {
                    $data['upah_dasar_karyawan'] = ($data['bpjs_kesehatan']['nominal'] + 0)/25;
                }
            }
        }

        $tgl_current_active = Carbon::now()->format('d');
        $bln_active = Carbon::now()->format('m');
        $bln_current_active = $month;
        $thn_current_active = $year;

        if($month == 1 && $tgl_current_active <10){
            $thn_prev_active = $year-1;
            $bulan_kemarin="$thn_prev_active-11-26";
            $bulan_sekarang="$thn_prev_active-12-26";
        }elseif($month == 1 && $tgl_current_active>10 or $month == 2 && $tgl_current_active<10 && $bln_active != 3){
            $thn_prev_active = $year-1;
            $bulan_kemarin="$thn_prev_active-12-26";
            $bulan_sekarang="$thn_current_active-01-26";
        }else{
            $bln_prev_active = $month-1; 
            $thn_prev_active = $year;
            $bulan_kemarin="$thn_prev_active-$bln_prev_active-26";
            $bulan_sekarang="$thn_current_active-$bln_current_active-26";
        }

        $data['tunjangan_kerjas'] = $this->tunjanganKerja->select('nominal')->where('id',$data['karyawan_harian']['tunjangan_kerja_id'])->first();
        if(empty($data['tunjangan_kerjas'])){
            $data['tunjangan_kerja'] = 0;
        }else{
            $data['tunjangan_kerja'] = $data['tunjangan_kerjas']['nominal'];
        }

        //jumlah alpa
        $data['alpa'] = $this->presensiInfo->where('pin',$data['karyawan_harian']['pin'])
                                    ->whereBetween('scan_date',[$bulan_kemarin,$bulan_sekarang])
                                    ->where('status',7)
                                    ->get();
        //jumlah diliburkan
        $data['diliburkan'] = $this->presensiInfo->where('pin',$data['karyawan_harian']['pin'])
                                    ->whereBetween('scan_date',[$bulan_kemarin,$bulan_sekarang])
                                    ->where('status',14)
                                    ->get();
        //jumlah cuti
        $data['cuti'] = $this->presensiInfo->where('pin',$data['karyawan_harian']['pin'])
                                    ->whereBetween('scan_date',[$bulan_kemarin,$bulan_sekarang])
                                    ->where('status',13)
                                    ->get();
        //jumlah sakit
        $data['sakit'] = $this->presensiInfo->where('pin',$data['karyawan_harian']['pin'])
                                    ->whereBetween('scan_date',[$bulan_kemarin,$bulan_sekarang])
                                    ->where('status',4)
                                    ->get();
        // dd($data['sakit']);
        //jumlah ijin full
        $data['ijin_full'] = $this->presensiInfo->where('pin',$data['karyawan_harian']['pin'])
                                    ->whereBetween('scan_date',[$bulan_kemarin,$bulan_sekarang])
                                    ->where('status',6)
                                    ->get();
        //jumlah status terlambat
        $data['terlambats'] = $this->presensiInfo->where('pin',$data['karyawan_harian']['pin'])
                                    ->whereBetween('scan_date',[$bulan_kemarin,$bulan_sekarang])
                                    ->where('status',3)
                                    ->get();
        $telat_1=0; //terlambat < 5 menit
        $telat_2=0; //terlambat > 5 menit < 15 menit
        $telat_3=0; //terlambat > 15 menit < 1 jam
        $telat_4=0; //terlambat > 1 jam < 3 jam	
        $telat_5=0; //terlambat > 3 jam	
        $data['telat_1'] = $telat_1;
        $data['telat_2'] = $telat_2;
        $data['telat_3'] = $telat_3;
        $data['telat_4'] = $telat_4;
        $data['telat_5'] = $telat_5;
        foreach ($data['terlambats'] as $key => $terlambat) {
            $explode_keterangan_terlambat = explode("@",$terlambat->keterangan);
            $jam_keterangan_terlambat=strtotime($explode_keterangan_terlambat[1]);
            $jam_datang_terlambat=strtotime(Carbon::parse($terlambat['scan_date'])->format('H:i'));
            // $jam_datang_terlambat=strtotime($terlambat['jam_datang_telat']);
            $menit_telat=(($jam_datang_terlambat-$jam_keterangan_terlambat)/60);
            // dd($menit_telat);
            if($menit_telat <= 4){
                $telat_1=$telat_1+1;
                $data['telat_1'] = $telat_1;
            }
            else if(($menit_telat >= 5) && ($menit_telat <= 15) ){
                $telat_2=$telat_2+1;
                $data['telat_2'] = $telat_2;
            }
            else if(($menit_telat >= 16) && ($menit_telat <= 60)){
                $telat_3=$telat_3+1;
                $data['telat_3'] = $telat_3;
            }
            else if(($menit_telat >= 61) && ($menit_telat <= 179)){
                $telat_4=$telat_4+1;
                $data['telat_4'] = $telat_4;
            }else if($menit_telat > 179){
                $telat_5=$telat_5+1;
                $data['telat_5'] = $telat_5;
            }
            // else{
            //     $telat_4=$telat_4+1;
            //     $data['telat_4'] = $telat_4;
            // }
        }

        // dd($data['terlambats']);

        //jumlah status pulang awal
        $data['pulang_awals'] = $this->presensiInfo->where('pin',$data['karyawan_harian']['pin'])
                                    ->whereBetween('scan_date',[$bulan_kemarin,$bulan_sekarang])
                                    ->where(function($query) {
                                        $query->where('status','=',9)
                                            ->orWhere('status','=',10);
                                    })
                                    // ->where('status',9)
                                    // ->where('status',10)
                                    ->get();
        // dd($data['pulang_awals']);
        $pulang_1=0; //pulang awal < 4 jam
		$pulang_2=0; //tpulang awal > 4 jam
        $data['pulang_1'] = $pulang_1;
        $data['pulang_2'] = $pulang_2;
        foreach ($data['pulang_awals'] as $key => $pulang_awal) {
            $explode_pulang_seharusnya=explode("@",$pulang_awal->keterangan);
			$jam_pulang_seharusnya=strtotime($explode_pulang_seharusnya[3]);
			$jam_pulang_awal=strtotime(Carbon::parse($pulang_awal['scan_date'])->format('H:i'));
			// $jam_pulang_awal=strtotime($pulang_awal->jam_pulang_awal);
			$menit_sisa_jam_kerja=(($jam_pulang_seharusnya-$jam_pulang_awal)/60);
			if ($menit_sisa_jam_kerja<=300){
                $pulang_1=$pulang_1+1;
                $data['pulang_1'] = $pulang_1;
            }
			else{
                $pulang_2=$pulang_2+1;
                $data['pulang_2'] = $pulang_2;
            }
        }

        //jumlah ijin saat jam kerja
        $data['keluar_masuks'] = $this->keluarMasuk->where('nik',$data['karyawan_harian']['nik'])
                                    ->whereBetween('tanggal_ijin',[$bulan_kemarin,$bulan_sekarang])
                                    ->get();
        // dd($data['keluar_masuks']);
        $ijin_15=0; //ijin kurang dari 15 menit
		$ijin_k4=0; //ijin > 15menit < 4 jam
		$ijin_l4=0; //ijin > 4 jam
        $data['ijin_15']=$ijin_15;
        $data['ijin_k4']=$ijin_k4;
        $data['ijin_l4']=$ijin_l4;
        foreach ($data['keluar_masuks'] as $key => $keluar_masuk) {
            $jam_keluar=strtotime($keluar_masuk->jam_keluar);
			$jam_datang=strtotime($keluar_masuk->jam_datang);
			$menit_keluar_masuk=(($jam_datang-$jam_keluar)/60);
			if ($menit_keluar_masuk<=15){
                $ijin_15=$ijin_15+1;
                $data['ijin_15']=$ijin_15;
            }
			else if (($menit_keluar_masuk>15)&&($menit_keluar_masuk<=239)){
                $ijin_k4=$ijin_k4+1;
                $data['ijin_k4']=$ijin_k4;
            }
			else {
                $ijin_l4=$ijin_l4+1;
                $data['ijin_l4']=$ijin_l4;
            }
        }

        //lihat tanggal masuk

        // $data['log_posisi'] = $this->logPosisi->select('tanggal')->where('nik',$nik)->first();
		$selisih_tanggal_masuk=strtotime($bulan_kemarin)-strtotime($data['karyawan_harian']['tanggal']);
		if ($selisih_tanggal_masuk>=0)$div_tk=0;else $div_tk=75000;

        $total_potongan_tk=(75000*$data['alpa']->count())+(75000*$data['diliburkan']->count())+(75000*$data['sakit']->count())+(75000*$data['cuti']->count())+(75000*$data['ijin_full']->count())+($data['ijin_15']*25000)+($data['ijin_k4']*40000)+
        ($data['ijin_l4']*75000)+($data['pulang_1']*40000)+($data['pulang_2']*75000)+($data['telat_1']*15000)+($data['telat_2']*25000)+($data['telat_3']*30000)+($data['telat_4']*40000)+$div_tk;
        if ($total_potongan_tk>75000){$total_potongan_tk=75000;}else {}

        // if ($data['pengerjaan_harian_weekly']['tunjangan_kerja'] == 75000) {
        //     $data['total_potongan_tk']=0;
        // }
        // // elseif($data['pengerjaan_harian_weekly']['tunjangan_kerja'] < 75000){
        // //     $data['total_potongan_tk']=$data['pengerjaan_harian_weekly']['tunjangan_kerja'];
        // // }
        // else{
        //     $data['total_potongan_tk']=$total_potongan_tk; 
        // }

        $data['total_potongan_tk']=$total_potongan_tk;

        return view('backend.pengerjaan.harian.umum.input_hasil_kerja_karyawan',$data);

    }

    public function hasil_kerja_harian_umum_simpan(Request $request, $id, $kode_pengerjaan, $tanggal)
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

        $input['uang_makan'] = $request->uang_makan;

        $input['plus_1'] = $request->plus_1."|".$request->keterangan_plus_1;
        $input['plus_2'] = $request->plus_2."|".$request->keterangan_plus_2;
        $input['plus_3'] = $request->plus_3."|".$request->keterangan_plus_3;

        $input['minus_1'] = $request->minus_1."|".$request->keterangan_minus_1;
        $input['minus_2'] = $request->minus_2."|".$request->keterangan_minus_2;

        $hitung_lembur = round((($request->upah_dasar*25/173*$request->lembur_1)*1.5)+(($request->upah_dasar*25/173*$request->lembur_2)*2));
        
        $input['lembur'] = $hitung_lembur.'|'.$request->lembur_1.'|'.$request->lembur_2;

        $upah_dasar_weekly = 0;
        $hasil_kerja="";

        for ($i=1; $i <=$request->data_for; $i++) { 
            $var_hasil_kerja="jam_kerja_".$i;
            $hasil_kerja=$hasil_kerja.$request[$var_hasil_kerja]."|";
            $upah_dasar_harian=$request[$var_hasil_kerja]*$request->upah_dasar;
            $upah_dasar_weekly=$upah_dasar_weekly+$upah_dasar_harian;
            // $upah_dasar_harian=$request[$var_hasil_kerja]*($request->upah_dasar-0.1);
            // $upah_dasar_weekly=$upah_dasar_weekly+$upah_dasar_harian+0.2;
        }

        $input['hasil_kerja'] = $hasil_kerja;
        // $input['upah_dasar_harian'] = round($upah_dasar_harian);
        $input['upah_dasar_weekly'] = round($upah_dasar_weekly);
        if ($request->akhir_bulan == 'n') {
            $input['tunjangan_kerja'] = 0;
            $input['tunjangan_kehadiran'] = 0;
        }else{
            $input['tunjangan_kerja'] = $request->tunjangan_kerja;
            $input['tunjangan_kehadiran'] = 75000-$request->pot_tunjangan_kehadiran;
        }
        // $input['tunjangan_kehadiran'] = 75000-$request->pot_tunjangan_kehadiran;
        // $input['tunjangan_kerja'] = $request->tunjangan_kerja;

        if ($request->check_jht == 'on') {
            $input['jht'] = $request->jht;
        }else{
            $input['jht'] = 0;
        }

        if($request->check_bpjs_kesehatan == 'on'){
            $input['bpjs_kesehatan'] = $request->bpjs_kesehatan;
        }else{
            $input['bpjs_kesehatan'] = 0;
        }

        $pengerjaan_harian_karyawan = $this->pengerjaanHarian->where('operator_harian_karyawan_id',$request->operator_harian_karyawan_id)
                                                    ->where('kode_pengerjaan',$kode_pengerjaan)
                                                    // ->update($input)
                                                    ->first()
                                                    ;
        // return 'Success';
        if (!empty($pengerjaan_harian_karyawan)) {
            $pengerjaan_harian_karyawan->update($input);
            return response()->json([
                'success' => true,
                'message_title' => 'Berhasil',
                'message_content' => 'Data Berhasil Disimpan'
            ]);
        }else{
            return response()->json([
                'success' => false,
                'message_title' => 'Gagal',
                'message_content' => 'Data Tidak Berhasil Disimpan'
            ]);
        }
    }

    public function hasil_kerja_harian_supir($id, $kode_pengerjaan)
    {
        $data['id'] = $id;
        $data['kode_pengerjaan'] = $kode_pengerjaan;
        $data['jenis_operator_detail_pekerjaan'] = $this->jenisOperatorDetailPengerjaan->find(18);
        $data['new_data_pengerjaan'] = $this->newDataPengerjaan->where('kode_pengerjaan',$kode_pengerjaan)->first();
        $data['explode_tanggal_pengerjaans'] = explode('#',$data['new_data_pengerjaan']['tanggal']);

        $data['pengerjaan_harians'] = $this->pengerjaanHarian->select([
                                                        'pengerjaan_harian.id as id',
                                                        'operator_harian_karyawan.nik as nik',
                                                        'biodata_karyawan.nama as nama',
                                                        'pengerjaan_harian.operator_harian_karyawan_id as operator_harian_karyawan_id',
                                                        'pengerjaan_harian.uang_makan as uang_makan',
                                                        'pengerjaan_harian.upah_dasar_weekly as upah_dasar_weekly',
                                                        'pengerjaan_harian.plus_1 as plus_1',
                                                        'pengerjaan_harian.plus_2 as plus_2',
                                                        'pengerjaan_harian.plus_3 as plus_3',
                                                        'pengerjaan_harian.minus_1 as minus_1',
                                                        'pengerjaan_harian.minus_2 as minus_2',
                                                        'pengerjaan_harian.jht as jht',
                                                        'pengerjaan_harian.bpjs_kesehatan as bpjs_kesehatan',
                                                        'pengerjaan_harian.lembur as lembur',
                                                        'pengerjaan_harian.hasil_kerja as hasil_kerja',
                                                        'pengerjaan_harian.tunjangan_kehadiran as tunjangan_kehadiran',
                                                        'pengerjaan_harian.tunjangan_kerja as tunjangan_kerja'
                                                    ])
                                                    ->leftJoin('operator_harian_karyawan','operator_harian_karyawan.id','=','pengerjaan_harian.operator_harian_karyawan_id')
                                                    ->leftJoin('itic_emp_new.biodata_karyawan','biodata_karyawan.nik','=','operator_harian_karyawan.nik')
                                                    ->where('operator_harian_karyawan.jenis_operator_detail_pekerjaan_id',18)
                                                    ->where('kode_pengerjaan',$kode_pengerjaan)
                                                    ->where('operator_harian_karyawan.status','y')
                                                    ->orderBy('biodata_karyawan.nama','asc')
                                                    ->get();
        return view('backend.pengerjaan.harian.supir.index',$data);
    }

    public function hasil_kerja_harian_supir_view($id, $kode_pengerjaan, $nik, $month, $year)
    {
        $data['id'] = $id;
        $data['nik'] = $nik;
        $data['kode_pengerjaan'] = $kode_pengerjaan;
        $data['month'] = $month;
        $data['year']= $year;

        if($id == 4){
            $kode_jenis_operator_detail = 'H';
        }

        $data['karyawan_harian'] = $this->karyawanOperatorHarian->select([
                                                            'operator_harian_karyawan.id as id',
                                                            'operator_harian_karyawan.nik as nik',
                                                            'biodata_karyawan.nama as nama',
                                                            'biodata_karyawan.pin as pin',
                                                            'biodata_karyawan.tanggal_masuk as tanggal',
                                                            'operator_harian_karyawan.upah_dasar as upah_dasar',
                                                            'pengerjaan_harian.upah_dasar_weekly as upah_dasar_weekly',
                                                            'pengerjaan_harian.hari_kerja as hari_kerja',
                                                            'pengerjaan_harian.hasil_kerja as hasil_kerja',
                                                            'pengerjaan_harian.tunjangan_kerja as tunjangan_kerja',
                                                            'operator_harian_karyawan.tunjangan_kerja_id as tunjangan_kerja_id',
                                                            'pengerjaan_harian.plus_1 as plus_1',
                                                            'pengerjaan_harian.plus_2 as plus_2',
                                                            'pengerjaan_harian.plus_3 as plus_3',
                                                            'pengerjaan_harian.minus_1 as minus_1',
                                                            'pengerjaan_harian.minus_2 as minus_2',
                                                            'pengerjaan_harian.minus_3 as minus_3',
                                                            'pengerjaan_harian.uang_makan as uang_makan',
                                                            'pengerjaan_harian.lembur as lembur',
                                                            'operator_harian_karyawan.jht as jht',
                                                            'operator_harian_karyawan.bpjs as bpjs',
                                                            'operator_harian_karyawan.status as status',
                                                        ])
                                                        ->with('tunjangan_kerja_nominal')
                                                        ->leftJoin('itic_emp_new.biodata_karyawan','biodata_karyawan.nik','=','operator_harian_karyawan.nik')
                                                        ->rightJoin('pengerjaan_harian','pengerjaan_harian.operator_harian_karyawan_id','=','operator_harian_karyawan.id')
                                                        ->where('operator_harian_karyawan.nik',$nik)
                                                        ->where('operator_harian_karyawan.status','y')
                                                        ->where('pengerjaan_harian.kode_pengerjaan',$kode_pengerjaan)
                                                        ->first();
        // dd($data['karyawan_harian']);
        $data['new_data_pengerjaan'] = $this->newDataPengerjaan->where('kode_pengerjaan',$kode_pengerjaan)->first();
        $data['explode_tanggal_pengerjaans'] = explode('#',$data['new_data_pengerjaan']['tanggal']);

        if (empty($data['karyawan_harian']['plus_1'])) {
            $plus_1=null;
            $ket_plus_1=null;
        }else{
            $exp_plus1=explode("|",$data['karyawan_harian']['plus_1']);
            // dd($exp_plus1);
            $plus_1=$exp_plus1[0];
            $ket_plus_1=$exp_plus1[1];
        }

        if (empty($data['karyawan_harian']['plus_2'])) {
            $plus_2=null;
            $ket_plus_2=null;
        }else{
            $exp_plus2=explode("|",$data['karyawan_harian']['plus_2']);
            $plus_2=$exp_plus2[0];
            $ket_plus_2=$exp_plus2[1];
        }

        if (empty($data['karyawan_harian']['plus_3'])) {
            $plus_3=null;
            $ket_plus_3=null;
        }else{
            $exp_plus3=explode("|",$data['karyawan_harian']['plus_3']);
            $plus_3=$exp_plus2[0];
            $ket_plus_3=$exp_plus2[1];
        }

        if (empty($data['karyawan_harian']['minus_1'])) {
            $minus_1=null;
            $ket_minus_1=null;
        }else{
            $exp_minus1=explode("|",$data['karyawan_harian']['minus_1']);
            $minus_1=$exp_minus1[0];
            $ket_minus_1=$exp_minus1[1];
        }

        if (empty($data['karyawan_harian']['minus_2'])) {
            $minus_2=null;
            $ket_minus_2=null;
        }else{
            $exp_minus2=explode("|",$data['karyawan_harian']['minus_2']);
            $minus_2=$exp_minus2[0];
            $ket_minus_2=$exp_minus2[1];
        }

        $data['jhts'] = $this->bpjsJht->where('status','y')->get();
        $data['bpjs_kesehatan'] = $this->bpjsKesehatan->select('nominal')->where('status','y')->first();

        $data['pengerjaan_harian_weekly'] = $this->pengerjaanHarian->where('operator_harian_karyawan_id',$data['karyawan_harian']['id'])
                                                            ->where('kode_payrol',$kode_pengerjaan)
                                                            ->first();
        $exp_lembur = explode("@",$data['pengerjaan_harian_weekly']['lembur']);
        // dd($exp_lembur);
        if(empty($exp_lembur[0])){
            $exp_lembur[0]==null;
        }

        //Hitung Masa Kerja
        // $data['karyawan_masa_kerja'] = DB::connection('emp')->table('log_posisi')
        //                                                     ->select('tanggal')
        //                                                     ->where('nik',$nik)
        //                                                     ->first();
                                                            
        $awal  = new DateTime($data['karyawan_harian']->tanggal);
        $akhir = new DateTime(); // Waktu sekarang
        $diff  = $awal->diff($akhir);
        $data['masa_kerja'] = $diff->y.' Tahun '.$diff->m.' Bulan '.$diff->d.' Hari';
        $data['masa_kerja_tahun'] = $diff->y;
        $data['masa_kerja_hari'] = $diff->d;

        $data['upah_dasar_karyawan'] = [];
        foreach ($data['jhts'] as $key => $jht) {
            if ($data['masa_kerja_tahun'] > 15) {
                if ($jht->urutan == 3) {
                    $data['upah_dasar_karyawan'] = ($data['bpjs_kesehatan']['nominal'] + 100000)/25;
                }
            }
            elseif($data['masa_kerja_tahun'] >= 10 && $data['masa_kerja_tahun'] <= 15 && $data['masa_kerja_hari'] >= 1){
                if ($jht->urutan == 2) {
                    $data['upah_dasar_karyawan'] = ($data['bpjs_kesehatan']['nominal'] + 50000)/25;
                }
            }
            elseif($data['masa_kerja_tahun'] <= 10 || $data['masa_kerja_hari'] >= 1){
                if ($jht->urutan == 1) {
                    $data['upah_dasar_karyawan'] = ($data['bpjs_kesehatan']['nominal'] + 0)/25;
                }
            }
        }

        $tgl_current_active = Carbon::now()->format('d');
        $bln_active = Carbon::now()->format('m');
        $bln_current_active = $month;
        $thn_current_active = $year;

        if($month == 1 && $tgl_current_active <10){
            $thn_prev_active = $year-1;
            $bulan_kemarin="$thn_prev_active-11-26";
            $bulan_sekarang="$thn_prev_active-12-26";
        }elseif($month == 1 && $tgl_current_active>10 or $month == 2 && $tgl_current_active<10 && $bln_active != 3){
            $thn_prev_active = $year-1;
            $bulan_kemarin="$thn_prev_active-12-26";
            $bulan_sekarang="$thn_current_active-01-26";
        }else{
            $bln_prev_active = $month-1; 
            $thn_prev_active = $year;
            $bulan_kemarin="$thn_prev_active-$bln_prev_active-26";
            $bulan_sekarang="$thn_current_active-$bln_current_active-26";
        }

        $data['tunjangan_kerjas'] = $this->tunjanganKerja->select('nominal')->where('id',$data['karyawan_harian']['tunjangan_kerja_id'])->first();
        if(empty($data['tunjangan_kerjas'])){
            $data['tunjangan_kerja'] = 0;
        }else{
            $data['tunjangan_kerja'] = $data['tunjangan_kerjas']['nominal'];
        }

        //jumlah alpa
        $data['alpa'] = $this->presensiInfo->where('pin',$data['karyawan_harian']['pin'])
                                    ->whereBetween('scan_date',[$bulan_kemarin,$bulan_sekarang])
                                    ->where('status',7)
                                    ->get();
        //jumlah diliburkan
        $data['diliburkan'] = $this->presensiInfo->where('pin',$data['karyawan_harian']['pin'])
                                    ->whereBetween('scan_date',[$bulan_kemarin,$bulan_sekarang])
                                    ->where('status',14)
                                    ->get();
        //jumlah cuti
        $data['cuti'] = $this->presensiInfo->where('pin',$data['karyawan_harian']['pin'])
                                    ->whereBetween('scan_date',[$bulan_kemarin,$bulan_sekarang])
                                    ->where('status',13)
                                    ->get();
        //jumlah sakit
        $data['sakit'] = $this->presensiInfo->where('pin',$data['karyawan_harian']['pin'])
                                    ->whereBetween('scan_date',[$bulan_kemarin,$bulan_sekarang])
                                    ->where('status',4)
                                    ->get();
        // dd($data['sakit']);
        //jumlah ijin full
        $data['ijin_full'] = $this->presensiInfo->where('pin',$data['karyawan_harian']['pin'])
                                    ->whereBetween('scan_date',[$bulan_kemarin,$bulan_sekarang])
                                    ->where('status',6)
                                    ->get();
        //jumlah status terlambat
        $data['terlambats'] = $this->presensiInfo->where('pin',$data['karyawan_harian']['pin'])
                                    ->whereBetween('scan_date',[$bulan_kemarin,$bulan_sekarang])
                                    ->where('status',3)
                                    ->get();
        $telat_1=0; //terlambat < 5 menit
        $telat_2=0; //terlambat > 5 menit < 15 menit
        $telat_3=0; //terlambat > 15 menit < 1 jam
        $telat_4=0; //terlambat > 1 jam < 3 jam	
        $telat_5=0; //terlambat > 3 jam	
        $data['telat_1'] = $telat_1;
        $data['telat_2'] = $telat_2;
        $data['telat_3'] = $telat_3;
        $data['telat_4'] = $telat_4;
        $data['telat_5'] = $telat_5;
        foreach ($data['terlambats'] as $key => $terlambat) {
            $explode_keterangan_terlambat = explode("@",$terlambat->keterangan);
            $jam_keterangan_terlambat=strtotime($explode_keterangan_terlambat[1]);
            $jam_datang_terlambat=strtotime(Carbon::parse($terlambat['scan_date'])->format('H:i'));
            // $jam_datang_terlambat=strtotime($terlambat['jam_datang_telat']);
            $menit_telat=(($jam_datang_terlambat-$jam_keterangan_terlambat)/60);
            // dd($menit_telat);
            if($menit_telat <= 4){
                $telat_1=$telat_1+1;
                $data['telat_1'] = $telat_1;
            }
            else if(($menit_telat >= 5) && ($menit_telat <= 15) ){
                $telat_2=$telat_2+1;
                $data['telat_2'] = $telat_2;
            }
            else if(($menit_telat >= 16) && ($menit_telat <= 60)){
                $telat_3=$telat_3+1;
                $data['telat_3'] = $telat_3;
            }
            else if(($menit_telat >= 61) && ($menit_telat <= 179)){
                $telat_4=$telat_4+1;
                $data['telat_4'] = $telat_4;
            }else if($menit_telat > 179){
                $telat_5=$telat_5+1;
                $data['telat_5'] = $telat_5;
            }
            // else{
            //     $telat_4=$telat_4+1;
            //     $data['telat_4'] = $telat_4;
            // }
        }

        //jumlah status pulang awal
        $data['pulang_awals'] = $this->presensiInfo->where('pin',$data['karyawan_harian']['pin'])
                                    ->whereBetween('scan_date',[$bulan_kemarin,$bulan_sekarang])
                                    ->where(function($query) {
                                        $query->where('status','=',9)
                                            ->orWhere('status','=',10);
                                    })
                                    // ->where('status',9)
                                    // ->where('status',10)
                                    ->get();
        // dd($data['pulang_awals']);
        $pulang_1=0; //pulang awal < 4 jam
		$pulang_2=0; //tpulang awal > 4 jam
        $data['pulang_1'] = $pulang_1;
        $data['pulang_2'] = $pulang_2;
        foreach ($data['pulang_awals'] as $key => $pulang_awal) {
            $explode_pulang_seharusnya=explode("@",$pulang_awal->keterangan);
			$jam_pulang_seharusnya=strtotime($explode_pulang_seharusnya[3]);
			$jam_pulang_awal=strtotime(Carbon::parse($pulang_awal['scan_date'])->format('H:i'));
			// $jam_pulang_awal=strtotime($pulang_awal->jam_pulang_awal);
			$menit_sisa_jam_kerja=(($jam_pulang_seharusnya-$jam_pulang_awal)/60);
			if ($menit_sisa_jam_kerja<=300){
                $pulang_1=$pulang_1+1;
                $data['pulang_1'] = $pulang_1;
            }
			else{
                $pulang_2=$pulang_2+1;
                $data['pulang_2'] = $pulang_2;
            }
        }

        //jumlah ijin saat jam kerja
        $data['keluar_masuks'] = $this->keluarMasuk->where('nik',$data['karyawan_harian']['nik'])
                                    ->whereBetween('tanggal_ijin',[$bulan_kemarin,$bulan_sekarang])
                                    ->get();
        // dd($data['keluar_masuks']);
        $ijin_15=0; //ijin kurang dari 15 menit
		$ijin_k4=0; //ijin > 15menit < 4 jam
		$ijin_l4=0; //ijin > 4 jam
        $data['ijin_15']=$ijin_15;
        $data['ijin_k4']=$ijin_k4;
        $data['ijin_l4']=$ijin_l4;
        foreach ($data['keluar_masuks'] as $key => $keluar_masuk) {
            $jam_keluar=strtotime($keluar_masuk->jam_keluar);
			$jam_datang=strtotime($keluar_masuk->jam_datang);
			$menit_keluar_masuk=(($jam_datang-$jam_keluar)/60);
			if ($menit_keluar_masuk<=15){
                $ijin_15=$ijin_15+1;
                $data['ijin_15']=$ijin_15;
            }
			else if (($menit_keluar_masuk>15)&&($menit_keluar_masuk<=239)){
                $ijin_k4=$ijin_k4+1;
                $data['ijin_k4']=$ijin_k4;
            }
			else {
                $ijin_l4=$ijin_l4+1;
                $data['ijin_l4']=$ijin_l4;
            }
        }

        //lihat tanggal masuk

        // $data['log_posisi'] = $this->logPosisi->select('tanggal')->where('nik',$nik)->first();
		$selisih_tanggal_masuk=strtotime($bulan_kemarin)-strtotime($data['karyawan_harian']['tanggal']);
		if ($selisih_tanggal_masuk>=0)$div_tk=0;else $div_tk=75000;

        $total_potongan_tk=(75000*$data['alpa']->count())+(75000*$data['diliburkan']->count())+(75000*$data['sakit']->count())+(75000*$data['cuti']->count())+(75000*$data['ijin_full']->count())+($data['ijin_15']*25000)+($data['ijin_k4']*40000)+
        ($data['ijin_l4']*75000)+($data['pulang_1']*40000)+($data['pulang_2']*75000)+($data['telat_1']*15000)+($data['telat_2']*25000)+($data['telat_3']*30000)+($data['telat_4']*40000)+$div_tk;
        // dd($total_potongan_tk);
        if ($total_potongan_tk>75000){$total_potongan_tk=75000;}else {}

        // if ($data['pengerjaan_harian_weekly']['tunjangan_kerja'] == 75000) {
        //     $data['total_potongan_tk']=0;
        // }
        // // elseif($data['pengerjaan_harian_weekly']['tunjangan_kerja'] < 75000){
        // //     $data['total_potongan_tk']=$data['pengerjaan_harian_weekly']['tunjangan_kerja'];
        // // }
        // else{
        //     $data['total_potongan_tk']=$total_potongan_tk; 
        // }

        $data['total_potongan_tk']=$total_potongan_tk;

        return view('backend.pengerjaan.harian.supir.input_hasil_kerja_karyawan',$data);

    }

    public function hasil_kerja_harian_supir_simpan(Request $request, $id, $kode_pengerjaan, $tanggal)
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

        $input['uang_makan'] = $request->uang_makan;

        $input['plus_1'] = $request->plus_1."|".$request->keterangan_plus_1;
        $input['plus_2'] = $request->plus_2."|".$request->keterangan_plus_2;
        $input['plus_3'] = $request->plus_3."|".$request->keterangan_plus_3;

        $input['minus_1'] = $request->minus_1."|".$request->keterangan_minus_1;
        $input['minus_2'] = $request->minus_2."|".$request->keterangan_minus_2;

        $hitung_lembur = round((($request->upah_dasar*25/173*$request->lembur_1)*1.5)+(($request->upah_dasar*25/173*$request->lembur_2)*2));
        
        $input['lembur'] = $hitung_lembur.'|'.$request->lembur_1.'|'.$request->lembur_2;

        $upah_dasar_weekly = 0;
        $hasil_kerja="";

        for ($i=1; $i <=$request->data_for; $i++) { 
            $var_hasil_kerja="jam_kerja_".$i;
            $hasil_kerja=$hasil_kerja.$request[$var_hasil_kerja]."|";
            $upah_dasar_harian=$request[$var_hasil_kerja]*$request->upah_dasar;
            $upah_dasar_weekly=$upah_dasar_weekly+$upah_dasar_harian;
            // $upah_dasar_harian=$request[$var_hasil_kerja]*($request->upah_dasar-0.1);
            // $upah_dasar_weekly=$upah_dasar_weekly+$upah_dasar_harian+0.2;
        }

        $input['hasil_kerja'] = $hasil_kerja;
        // $input['upah_dasar_harian'] = round($upah_dasar_harian);
        $input['upah_dasar_weekly'] = round($upah_dasar_weekly);
        if ($request->akhir_bulan == 'n') {
            $input['tunjangan_kerja'] = 0;
            $input['tunjangan_kehadiran'] = 0;
        }else{
            $input['tunjangan_kerja'] = $request->tunjangan_kerja;
            $input['tunjangan_kehadiran'] = 75000-$request->pot_tunjangan_kehadiran;
        }
        // $input['tunjangan_kehadiran'] = 75000-$request->pot_tunjangan_kehadiran;
        // $input['tunjangan_kerja'] = $request->tunjangan_kerja;

        if ($request->check_jht == 'on') {
            $input['jht'] = $request->jht;
        }else{
            $input['jht'] = 0;
        }

        if($request->check_bpjs_kesehatan == 'on'){
            $input['bpjs_kesehatan'] = $request->bpjs_kesehatan;
        }else{
            $input['bpjs_kesehatan'] = 0;
        }

        $pengerjaan_harian_karyawan = $this->pengerjaanHarian->where('operator_harian_karyawan_id',$request->operator_harian_karyawan_id)
                                                    ->where('kode_pengerjaan',$kode_pengerjaan)
                                                    // ->update($input)
                                                    ->first()
                                                    ;
        // return 'Success';
        if (!empty($pengerjaan_harian_karyawan)) {
            $pengerjaan_harian_karyawan->update($input);
            return response()->json([
                'success' => true,
                'message_title' => 'Berhasil',
                'message_content' => 'Data Berhasil Disimpan'
            ]);
        }else{
            return response()->json([
                'success' => false,
                'message_title' => 'Gagal',
                'message_content' => 'Data Tidak Berhasil Disimpan'
            ]);
        }
    }

    public function hasil_kerja_harian_satpam($id, $kode_pengerjaan)
    {
        $data['id'] = $id;
        $data['kode_pengerjaan'] = $kode_pengerjaan;
        $data['jenis_operator_detail_pekerjaan'] = $this->jenisOperatorDetailPengerjaan->find(19);
        $data['new_data_pengerjaan'] = $this->newDataPengerjaan->where('kode_pengerjaan',$kode_pengerjaan)->first();
        $data['explode_tanggal_pengerjaans'] = explode('#',$data['new_data_pengerjaan']['tanggal']);

        $data['pengerjaan_harians'] = $this->pengerjaanHarian->select([
                                                        'pengerjaan_harian.id as id',
                                                        'operator_harian_karyawan.nik as nik',
                                                        'biodata_karyawan.nama as nama',
                                                        'pengerjaan_harian.operator_harian_karyawan_id as operator_harian_karyawan_id',
                                                        'pengerjaan_harian.uang_makan as uang_makan',
                                                        'pengerjaan_harian.upah_dasar_weekly as upah_dasar_weekly',
                                                        'pengerjaan_harian.plus_1 as plus_1',
                                                        'pengerjaan_harian.plus_2 as plus_2',
                                                        'pengerjaan_harian.plus_3 as plus_3',
                                                        'pengerjaan_harian.minus_1 as minus_1',
                                                        'pengerjaan_harian.minus_2 as minus_2',
                                                        'pengerjaan_harian.jht as jht',
                                                        'pengerjaan_harian.bpjs_kesehatan as bpjs_kesehatan',
                                                        'pengerjaan_harian.lembur as lembur',
                                                        'pengerjaan_harian.hasil_kerja as hasil_kerja',
                                                        'pengerjaan_harian.tunjangan_kerja as tunjangan_kerja',
                                                        'pengerjaan_harian.tunjangan_kehadiran as tunjangan_kehadiran',
                                                        'operator_harian_karyawan.tunjangan_kerja_id as tunjangan_kerja_id',
                                                    ])
                                                    ->leftJoin('operator_harian_karyawan','operator_harian_karyawan.id','=','pengerjaan_harian.operator_harian_karyawan_id')
                                                    ->leftJoin('itic_emp_new.biodata_karyawan','biodata_karyawan.nik','=','operator_harian_karyawan.nik')
                                                    ->where('operator_harian_karyawan.jenis_operator_detail_pekerjaan_id',19)
                                                    ->where('kode_pengerjaan',$kode_pengerjaan)
                                                    ->where('operator_harian_karyawan.status','y')
                                                    ->orderBy('biodata_karyawan.nama','asc')
                                                    ->get();
        return view('backend.pengerjaan.harian.satpam.index',$data);
    }

    public function hasil_kerja_harian_satpam_view($id, $kode_pengerjaan, $nik, $month, $year)
    {
        $data['id'] = $id;
        $data['nik'] = $nik;
        $data['kode_pengerjaan'] = $kode_pengerjaan;
        $data['month'] = $month;
        $data['year']= $year;

        if($id == 4){
            $kode_jenis_operator_detail = 'H';
        }

        $data['karyawan_harian'] = $this->karyawanOperatorHarian->select([
                                                            'operator_harian_karyawan.id as id',
                                                            'operator_harian_karyawan.nik as nik',
                                                            'biodata_karyawan.nama as nama',
                                                            'biodata_karyawan.pin as pin',
                                                            'biodata_karyawan.tanggal_masuk as tanggal',
                                                            'operator_harian_karyawan.upah_dasar as upah_dasar',
                                                            'pengerjaan_harian.upah_dasar_weekly as upah_dasar_weekly',
                                                            'pengerjaan_harian.hari_kerja as hari_kerja',
                                                            'pengerjaan_harian.hasil_kerja as hasil_kerja',
                                                            'pengerjaan_harian.tunjangan_kerja as tunjangan_kerja',
                                                            'operator_harian_karyawan.tunjangan_kerja_id as tunjangan_kerja_id',
                                                            'pengerjaan_harian.plus_1 as plus_1',
                                                            'pengerjaan_harian.plus_2 as plus_2',
                                                            'pengerjaan_harian.plus_3 as plus_3',
                                                            'pengerjaan_harian.minus_1 as minus_1',
                                                            'pengerjaan_harian.minus_2 as minus_2',
                                                            'pengerjaan_harian.minus_3 as minus_3',
                                                            'pengerjaan_harian.uang_makan as uang_makan',
                                                            'pengerjaan_harian.lembur as lembur',
                                                            'operator_harian_karyawan.jht as jht',
                                                            'operator_harian_karyawan.bpjs as bpjs',
                                                            'operator_harian_karyawan.status as status',
                                                        ])
                                                        ->with('tunjangan_kerja_nominal')
                                                        ->leftJoin('itic_emp_new.biodata_karyawan','biodata_karyawan.nik','=','operator_harian_karyawan.nik')
                                                        ->rightJoin('pengerjaan_harian','pengerjaan_harian.operator_harian_karyawan_id','=','operator_harian_karyawan.id')
                                                        ->where('operator_harian_karyawan.nik',$nik)
                                                        ->where('operator_harian_karyawan.status','y')
                                                        ->where('pengerjaan_harian.kode_pengerjaan',$kode_pengerjaan)
                                                        ->first();
        // dd($data['karyawan_harian']);
        $data['new_data_pengerjaan'] = $this->newDataPengerjaan->where('kode_pengerjaan',$kode_pengerjaan)->first();
        $data['explode_tanggal_pengerjaans'] = explode('#',$data['new_data_pengerjaan']['tanggal']);
        
        if (empty($data['karyawan_harian']['plus_1'])) {
            $plus_1=null;
            $ket_plus_1=null;
        }else{
            $exp_plus1=explode("|",$data['karyawan_harian']['plus_1']);
            // dd($exp_plus1);
            $plus_1=$exp_plus1[0];
            $ket_plus_1=$exp_plus1[1];
        }

        if (empty($data['karyawan_harian']['plus_2'])) {
            $plus_2=null;
            $ket_plus_2=null;
        }else{
            $exp_plus2=explode("|",$data['karyawan_harian']['plus_2']);
            $plus_2=$exp_plus2[0];
            $ket_plus_2=$exp_plus2[1];
        }

        if (empty($data['karyawan_harian']['plus_3'])) {
            $plus_3=null;
            $ket_plus_3=null;
        }else{
            $exp_plus3=explode("|",$data['karyawan_harian']['plus_3']);
            $plus_3=$exp_plus2[0];
            $ket_plus_3=$exp_plus2[1];
        }

        if (empty($data['karyawan_harian']['minus_1'])) {
            $minus_1=null;
            $ket_minus_1=null;
        }else{
            $exp_minus1=explode("|",$data['karyawan_harian']['minus_1']);
            $minus_1=$exp_minus1[0];
            $ket_minus_1=$exp_minus1[1];
        }

        if (empty($data['karyawan_harian']['minus_2'])) {
            $minus_2=null;
            $ket_minus_2=null;
        }else{
            $exp_minus2=explode("|",$data['karyawan_harian']['minus_2']);
            $minus_2=$exp_minus2[0];
            $ket_minus_2=$exp_minus2[1];
        }

        $data['jhts'] = $this->bpjsJht->where('status','y')->get();
        $data['bpjs_kesehatan'] = $this->bpjsKesehatan->select('nominal')->where('status','y')->first();

        $data['pengerjaan_harian_weekly'] = $this->pengerjaanHarian->where('operator_harian_karyawan_id',$data['karyawan_harian']['id'])
                                                            ->where('kode_payrol',$kode_pengerjaan)
                                                            ->first();
        $exp_lembur = explode("@",$data['pengerjaan_harian_weekly']['lembur']);
        // dd($exp_lembur);
        if(empty($exp_lembur[0])){
            $exp_lembur[0]==null;
        }

        //Hitung Masa Kerja
        // $data['karyawan_masa_kerja'] = DB::connection('emp')->table('log_posisi')
        //                                                     ->select('tanggal')
        //                                                     ->where('nik',$nik)
        //                                                     ->first();
                                                            
        $awal  = new DateTime($data['karyawan_harian']->tanggal);
        $akhir = new DateTime(); // Waktu sekarang
        $diff  = $awal->diff($akhir);
        $data['masa_kerja'] = $diff->y.' Tahun '.$diff->m.' Bulan '.$diff->d.' Hari';
        $data['masa_kerja_tahun'] = $diff->y;
        $data['masa_kerja_hari'] = $diff->d;

        $data['upah_dasar_karyawan'] = [];
        foreach ($data['jhts'] as $key => $jht) {
            if ($data['masa_kerja_tahun'] > 15) {
                if ($jht->urutan == 3) {
                    $data['upah_dasar_karyawan'] = ($data['bpjs_kesehatan']['nominal'] + 100000)/25;
                }
            }
            elseif($data['masa_kerja_tahun'] >= 10 && $data['masa_kerja_tahun'] <= 15 && $data['masa_kerja_hari'] >= 1){
                if ($jht->urutan == 2) {
                    $data['upah_dasar_karyawan'] = ($data['bpjs_kesehatan']['nominal'] + 50000)/25;
                }
            }
            elseif($data['masa_kerja_tahun'] <= 10 || $data['masa_kerja_hari'] >= 1){
                if ($jht->urutan == 1) {
                    $data['upah_dasar_karyawan'] = ($data['bpjs_kesehatan']['nominal'] + 0)/25;
                }
            }
        }

        $tgl_current_active = Carbon::now()->format('d');
        $bln_active = Carbon::now()->format('m');
        $bln_current_active = $month;
        $thn_current_active = $year;

        if($month == 1 && $tgl_current_active <10){
            $thn_prev_active = $year-1;
            $bulan_kemarin="$thn_prev_active-11-26";
            $bulan_sekarang="$thn_prev_active-12-26";
        }elseif($month == 1 && $tgl_current_active>10 or $month == 2 && $tgl_current_active<10 && $bln_active != 3){
            $thn_prev_active = $year-1;
            $bulan_kemarin="$thn_prev_active-12-26";
            $bulan_sekarang="$thn_current_active-01-26";
        }else{
            $bln_prev_active = $month-1; 
            $thn_prev_active = $year;
            $bulan_kemarin="$thn_prev_active-$bln_prev_active-26";
            $bulan_sekarang="$thn_current_active-$bln_current_active-26";
        }

        $data['tunjangan_kerjas'] = $this->tunjanganKerja->select('nominal')->where('id',$data['karyawan_harian']['tunjangan_kerja_id'])->first();
        // dd($data['tunjangan_kerjas']);
        if(empty($data['tunjangan_kerjas'])){
            $data['tunjangan_kerja'] = 0;
        }else{
            $data['tunjangan_kerja'] = $data['tunjangan_kerjas']['nominal'];
        }

        //jumlah alpa
        $data['alpa'] = $this->presensiInfo->where('pin',$data['karyawan_harian']['pin'])
                                    ->whereBetween('scan_date',[$bulan_kemarin,$bulan_sekarang])
                                    ->where('status',7)
                                    ->get();
        //jumlah diliburkan
        $data['diliburkan'] = $this->presensiInfo->where('pin',$data['karyawan_harian']['pin'])
                                    ->whereBetween('scan_date',[$bulan_kemarin,$bulan_sekarang])
                                    ->where('status',14)
                                    ->get();
        //jumlah cuti
        $data['cuti'] = $this->presensiInfo->where('pin',$data['karyawan_harian']['pin'])
                                    ->whereBetween('scan_date',[$bulan_kemarin,$bulan_sekarang])
                                    ->where('status',13)
                                    ->get();
        //jumlah sakit
        $data['sakit'] = $this->presensiInfo->where('pin',$data['karyawan_harian']['pin'])
                                    ->whereBetween('scan_date',[$bulan_kemarin,$bulan_sekarang])
                                    ->where('status',4)
                                    ->get();
        // dd($data['sakit']);
        //jumlah ijin full
        $data['ijin_full'] = $this->presensiInfo->where('pin',$data['karyawan_harian']['pin'])
                                    ->whereBetween('scan_date',[$bulan_kemarin,$bulan_sekarang])
                                    ->where('status',6)
                                    ->get();
        //jumlah status terlambat
        $data['terlambats'] = $this->presensiInfo->where('pin',$data['karyawan_harian']['pin'])
                                    ->whereBetween('scan_date',[$bulan_kemarin,$bulan_sekarang])
                                    ->where('status',3)
                                    ->get();
        $telat_1=0; //terlambat < 5 menit
        $telat_2=0; //terlambat > 5 menit < 15 menit
        $telat_3=0; //terlambat > 15 menit < 1 jam
        $telat_4=0; //terlambat > 1 jam < 3 jam	
        $telat_5=0; //terlambat > 3 jam	
        $data['telat_1'] = $telat_1;
        $data['telat_2'] = $telat_2;
        $data['telat_3'] = $telat_3;
        $data['telat_4'] = $telat_4;
        $data['telat_5'] = $telat_5;
        foreach ($data['terlambats'] as $key => $terlambat) {
            $explode_keterangan_terlambat = explode("@",$terlambat->keterangan);
            $jam_keterangan_terlambat=strtotime($explode_keterangan_terlambat[1]);
            $jam_datang_terlambat=strtotime(Carbon::parse($terlambat['scan_date'])->format('H:i'));
            // $jam_datang_terlambat=strtotime($terlambat['jam_datang_telat']);
            $menit_telat=(($jam_datang_terlambat-$jam_keterangan_terlambat)/60);
            // dd($menit_telat);
            if($menit_telat <= 4){
                $telat_1=$telat_1+1;
                $data['telat_1'] = $telat_1;
            }
            else if(($menit_telat >= 5) && ($menit_telat <= 15) ){
                $telat_2=$telat_2+1;
                $data['telat_2'] = $telat_2;
            }
            else if(($menit_telat >= 16) && ($menit_telat <= 60)){
                $telat_3=$telat_3+1;
                $data['telat_3'] = $telat_3;
            }
            else if(($menit_telat >= 61) && ($menit_telat <= 179)){
                $telat_4=$telat_4+1;
                $data['telat_4'] = $telat_4;
            }else if($menit_telat > 179){
                $telat_5=$telat_5+1;
                $data['telat_5'] = $telat_5;
            }
            // else{
            //     $telat_4=$telat_4+1;
            //     $data['telat_4'] = $telat_4;
            // }
        }

        //jumlah status pulang awal
        $data['pulang_awals'] = $this->presensiInfo->where('pin',$data['karyawan_harian']['pin'])
                                    ->whereBetween('scan_date',[$bulan_kemarin,$bulan_sekarang])
                                    ->where(function($query) {
                                        $query->where('status','=',9)
                                            ->orWhere('status','=',10);
                                    })
                                    // ->where('status',9)
                                    // ->where('status',10)
                                    ->get();
        // dd($data['pulang_awals']);
        $pulang_1=0; //pulang awal < 4 jam
		$pulang_2=0; //tpulang awal > 4 jam
        $data['pulang_1'] = $pulang_1;
        $data['pulang_2'] = $pulang_2;
        foreach ($data['pulang_awals'] as $key => $pulang_awal) {
            $explode_pulang_seharusnya=explode("@",$pulang_awal->keterangan);
			$jam_pulang_seharusnya=strtotime($explode_pulang_seharusnya[3]);
			$jam_pulang_awal=strtotime(Carbon::parse($pulang_awal['scan_date'])->format('H:i'));
			// $jam_pulang_awal=strtotime($pulang_awal->jam_pulang_awal);
			$menit_sisa_jam_kerja=(($jam_pulang_seharusnya-$jam_pulang_awal)/60);
			if ($menit_sisa_jam_kerja<=300){
                $pulang_1=$pulang_1+1;
                $data['pulang_1'] = $pulang_1;
            }
			else{
                $pulang_2=$pulang_2+1;
                $data['pulang_2'] = $pulang_2;
            }
        }

        //jumlah ijin saat jam kerja
        $data['keluar_masuks'] = $this->keluarMasuk->where('nik',$data['karyawan_harian']['nik'])
                                    ->whereBetween('tanggal_ijin',[$bulan_kemarin,$bulan_sekarang])
                                    ->get();
        // dd($data['keluar_masuks']);
        $ijin_15=0; //ijin kurang dari 15 menit
		$ijin_k4=0; //ijin > 15menit < 4 jam
		$ijin_l4=0; //ijin > 4 jam
        $data['ijin_15']=$ijin_15;
        $data['ijin_k4']=$ijin_k4;
        $data['ijin_l4']=$ijin_l4;
        foreach ($data['keluar_masuks'] as $key => $keluar_masuk) {
            $jam_keluar=strtotime($keluar_masuk->jam_keluar);
			$jam_datang=strtotime($keluar_masuk->jam_datang);
			$menit_keluar_masuk=(($jam_datang-$jam_keluar)/60);
			if ($menit_keluar_masuk<=15){
                $ijin_15=$ijin_15+1;
                $data['ijin_15']=$ijin_15;
            }
			else if (($menit_keluar_masuk>15)&&($menit_keluar_masuk<=239)){
                $ijin_k4=$ijin_k4+1;
                $data['ijin_k4']=$ijin_k4;
            }
			else {
                $ijin_l4=$ijin_l4+1;
                $data['ijin_l4']=$ijin_l4;
            }
        }

        //lihat tanggal masuk

        // $data['log_posisi'] = $this->logPosisi->select('tanggal')->where('nik',$nik)->first();
		$selisih_tanggal_masuk=strtotime($bulan_kemarin)-strtotime($data['karyawan_harian']['tanggal']);
		if ($selisih_tanggal_masuk>=0)$div_tk=0;else $div_tk=75000;

        $total_potongan_tk=(75000*$data['alpa']->count())+(75000*$data['diliburkan']->count())+(75000*$data['sakit']->count())+(75000*$data['cuti']->count())+(75000*$data['ijin_full']->count())+($data['ijin_15']*25000)+($data['ijin_k4']*40000)+
        ($data['ijin_l4']*75000)+($data['pulang_1']*40000)+($data['pulang_2']*75000)+($data['telat_1']*15000)+($data['telat_2']*25000)+($data['telat_3']*30000)+($data['telat_4']*40000)+$div_tk;
        if ($total_potongan_tk>75000){$total_potongan_tk=75000;}else {}

        // if ($data['pengerjaan_harian_weekly']['tunjangan_kerja'] == 75000) {
        //     $data['total_potongan_tk']=0;
        // }
        // // elseif($data['pengerjaan_harian_weekly']['tunjangan_kerja'] < 75000){
        // //     $data['total_potongan_tk']=$data['pengerjaan_harian_weekly']['tunjangan_kerja'];
        // // }
        // else{
        //     $data['total_potongan_tk']=$total_potongan_tk; 
        // }

        $data['total_potongan_tk']=$total_potongan_tk;

        return view('backend.pengerjaan.harian.satpam.input_hasil_kerja_karyawan',$data);

    }

    public function hasil_kerja_harian_satpam_simpan(Request $request, $id, $kode_pengerjaan, $tanggal)
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

        $input['uang_makan'] = $request->uang_makan;

        $input['plus_1'] = $request->plus_1."|".$request->keterangan_plus_1;
        $input['plus_2'] = $request->plus_2."|".$request->keterangan_plus_2;
        $input['plus_3'] = $request->plus_3."|".$request->keterangan_plus_3;

        $input['minus_1'] = $request->minus_1."|".$request->keterangan_minus_1;
        $input['minus_2'] = $request->minus_2."|".$request->keterangan_minus_2;

        $hitung_lembur = round((($request->upah_dasar*25/173*$request->lembur_1)*1.5)+(($request->upah_dasar*25/173*$request->lembur_2)*2));
        
        $input['lembur'] = $hitung_lembur.'|'.$request->lembur_1.'|'.$request->lembur_2;

        $upah_dasar_weekly = 0;
        $hasil_kerja="";

        for ($i=1; $i <=$request->data_for; $i++) { 
            $var_hasil_kerja="jam_kerja_".$i;
            $hasil_kerja=$hasil_kerja.$request[$var_hasil_kerja]."|";
            $upah_dasar_harian=$request[$var_hasil_kerja]*$request->upah_dasar;
            $upah_dasar_weekly=$upah_dasar_weekly+$upah_dasar_harian;
            // $upah_dasar_harian=$request[$var_hasil_kerja]*($request->upah_dasar-0.1);
            // $upah_dasar_weekly=$upah_dasar_weekly+$upah_dasar_harian+0.2;
        }

        $input['hasil_kerja'] = $hasil_kerja;
        // $input['upah_dasar_harian'] = round($upah_dasar_harian);
        $input['upah_dasar_weekly'] = round($upah_dasar_weekly);
        if ($request->akhir_bulan == 'n') {
            $input['tunjangan_kerja'] = 0;
            $input['tunjangan_kehadiran'] = 0;
        }else{
            $input['tunjangan_kerja'] = $request->tunjangan_kerja;
            $input['tunjangan_kehadiran'] = 75000-$request->pot_tunjangan_kehadiran;
        }
        // $input['tunjangan_kehadiran'] = 75000-$request->pot_tunjangan_kehadiran;
        // $input['tunjangan_kerja'] = $request->tunjangan_kerja;

        if ($request->check_jht == 'on') {
            $input['jht'] = $request->jht;
        }else{
            $input['jht'] = 0;
        }

        if($request->check_bpjs_kesehatan == 'on'){
            $input['bpjs_kesehatan'] = $request->bpjs_kesehatan;
        }else{
            $input['bpjs_kesehatan'] = 0;
        }



        $pengerjaan_harian_karyawan = $this->pengerjaanHarian->where('operator_harian_karyawan_id',$request->operator_harian_karyawan_id)
                                                    ->where('kode_pengerjaan',$kode_pengerjaan)
                                                    // ->update($input)
                                                    ->first()
                                                    ;
        // return 'Success';
        if (!empty($pengerjaan_harian_karyawan)) {
            $pengerjaan_harian_karyawan->update($input);
            return response()->json([
                'success' => true,
                'message_title' => 'Berhasil',
                'message_content' => 'Data Berhasil Disimpan'
            ]);
        }else{
            return response()->json([
                'success' => false,
                'message_title' => 'Gagal',
                'message_content' => 'Data Tidak Berhasil Disimpan'
            ]);
        }
    }

    public function hasil_kerja_supir_rit($kode_pengerjaan){
        $data['kode_pengerjaan'] = $kode_pengerjaan;
        $data['new_data_pengerjaan'] = $this->newDataPengerjaan->where('kode_pengerjaan',$kode_pengerjaan)->first();
        $data['explode_tanggal_pengerjaans'] = explode('#',$data['new_data_pengerjaan']['tanggal']);
        // dd($data['new_data_pengerjaan']);
        $data['pengerjaan_supir_rits'] = $this->pengerjaanRitWeekly->select([
                                                                'pengerjaan_supir_rit_weekly.id as id',
                                                                'operator_supir_rit_karyawan.nik as nik',
                                                                'biodata_karyawan.nama as nama',
                                                                'operator_supir_rit_karyawan.rit_posisi_id as rit_posisi_id',
                                                                'pengerjaan_supir_rit_weekly.karyawan_supir_rit_id as karyawan_supir_rit_id',
                                                                'pengerjaan_supir_rit_weekly.tunjangan_kerja as tunjangan_kerja',
                                                                'pengerjaan_supir_rit_weekly.tunjangan_kehadiran as tunjangan_kehadiran',
                                                                'pengerjaan_supir_rit_weekly.uang_makan as uang_makan',
                                                                'pengerjaan_supir_rit_weekly.plus_1 as plus_1',
                                                                'pengerjaan_supir_rit_weekly.plus_2 as plus_2',
                                                                'pengerjaan_supir_rit_weekly.plus_3 as plus_3',
                                                                'pengerjaan_supir_rit_weekly.minus_1 as minus_1',
                                                                'pengerjaan_supir_rit_weekly.minus_2 as minus_2',
                                                                'pengerjaan_supir_rit_weekly.lembur as lembur',
                                                                'pengerjaan_supir_rit_weekly.jht as jht',
                                                                'pengerjaan_supir_rit_weekly.bpjs_kesehatan as bpjs_kesehatan',
                                                            ])
                                                            ->leftJoin('operator_supir_rit_karyawan','operator_supir_rit_karyawan.id','=','pengerjaan_supir_rit_weekly.karyawan_supir_rit_id')
                                                            ->leftJoin('itic_emp_new.biodata_karyawan','biodata_karyawan.nik','=','operator_supir_rit_karyawan.nik')
                                                            ->where('kode_pengerjaan',$kode_pengerjaan)
                                                            ->where('operator_supir_rit_karyawan.status','y')
                                                            ->orderBy('biodata_karyawan.nama','asc')
                                                            ->get();
                                                            // dd($data['pengerjaan_supir_rits']);
        return view('backend.pengerjaan.supir_rit.index',$data);
        // return $data['pengerjaan_supir_rits'];
    }

    public function hasil_kerja_supir_rit_input($kode_pengerjaan, $tanggal){
        $data['kode_pengerjaan'] = $kode_pengerjaan;
        $data['tanggal'] = $tanggal;
        $data['pengerjaan_supir_rits'] = $this->pengerjaanRitWeekly->select([
                                                                'pengerjaan_supir_rit_weekly.id as id',
                                                                'operator_supir_rit_karyawan.nik as nik',
                                                                'biodata_karyawan.nama as nama',
                                                                'operator_supir_rit_karyawan.rit_posisi_id as rit_posisi_id',
                                                                'pengerjaan_supir_rit_weekly.karyawan_supir_rit_id as karyawan_supir_rit_id'
                                                            ])
                                                            ->leftJoin('operator_supir_rit_karyawan','operator_supir_rit_karyawan.id','=','pengerjaan_supir_rit_weekly.karyawan_supir_rit_id')
                                                            ->leftJoin('itic_emp_new.biodata_karyawan','biodata_karyawan.nik','=','operator_supir_rit_karyawan.nik')
                                                            ->where('pengerjaan_supir_rit_weekly.kode_pengerjaan',$kode_pengerjaan)
                                                            ->where('operator_supir_rit_karyawan.status','y')
                                                            ->orderBy('biodata_karyawan.nama','asc')
                                                            ->get();

        $data['new_data_pengerjaan'] = $this->newDataPengerjaan->where('kode_pengerjaan',$kode_pengerjaan)->first();
        return view('backend.pengerjaan.supir_rit.input',$data);
    }

    public function hasil_kerja_supir_rit_simpan(Request $request, $kode_pengerjaan, $tanggal)
    {
        $data['pengerjaan_supir_rit_dailys'] = $this->pengerjaanRitHarian->select([
                                                                    'pengerjaan_supir_rit.id as id',
                                                                    'operator_supir_rit_karyawan.nik as nik',
                                                                    'pengerjaan_supir_rit.kode_pengerjaan as kode_pengerjaan',
                                                                    'pengerjaan_supir_rit.karyawan_supir_rit_id as karyawan_supir_rit_id',
                                                                    'pengerjaan_supir_rit.tanggal_pengerjaan as tanggal_pengerjaan',
                                                                    'pengerjaan_supir_rit.hasil_kerja_1 as hasil_kerja_1',
                                                                    'pengerjaan_supir_rit.dpb as dpb',
                                                                    'operator_supir_rit_karyawan.rit_posisi_id as rit_posisi_id',
                                                                    'biodata_karyawan.tanggal_masuk as tanggal_masuk',
                                                                ])
                                                                ->leftJoin('operator_supir_rit_karyawan','operator_supir_rit_karyawan.id','=','pengerjaan_supir_rit.karyawan_supir_rit_id')
                                                                ->leftJoin('itic_emp_new.biodata_karyawan','biodata_karyawan.nik','=','operator_supir_rit_karyawan.nik')
                                                                ->where('pengerjaan_supir_rit.tanggal_pengerjaan',$tanggal)
                                                                ->where('pengerjaan_supir_rit.kode_pengerjaan',$kode_pengerjaan)
                                                                ->where('operator_supir_rit_karyawan.status','y')
                                                                ->orderBy('biodata_karyawan.nama','asc')
                                                                ->get();
        
        foreach ($data['pengerjaan_supir_rit_dailys'] as $key => $pengerjaan_supir_rit_daily) {
            // $umk_rit = RitUMK::where('rit_posisi_id', $pengerjaan_supir_rit_daily->rit_posisi_id)->first();
            $operator_karyawan_supir_rit = $this->ritKaryawan->where('nik',$pengerjaan_supir_rit_daily->nik)->where('status','y')->first();
            if ($request->rit[$key]) {
                $hasil_kerja_1 = $request->hasil_kerja_1[$key].'|'.$request->rit[$key];
            }else{
                $hasil_kerja_1 = '0|0';
            }

            if ($request->dpb[$key]) {
                $dpb = $request->dpb[$key];
            }else{
                $dpb = 0;
            }

            $awal = new DateTime($pengerjaan_supir_rit_daily->tanggal_masuk);
            $akhir = new DateTime(); // Waktu sekarang
            $diff = $awal->diff($akhir);
            $data['masa_kerja'] = $diff->y.' Tahun '.$diff->m.' Bulan '.$diff->d.' Hari';
            $data['masa_kerja_tahun'] = $diff->y;
            $data['masa_kerja_hari'] = $diff->d;

            $data['jhts'] = $this->bpjsJht->where('status','y')->get();
            $data['bpjs_kesehatan'] = $this->bpjsKesehatan->select('nominal')->where('status','y')->first();

            $upah_dasar_karyawan = [];
            foreach ($data['jhts'] as $key => $jht) {
                if ($data['masa_kerja_tahun'] > 15) {
                    if ($jht->urutan == 3) {
                        $upah_dasar_karyawan = ($data['bpjs_kesehatan']['nominal'] + 100000)/25;
                    }
                }
                elseif($data['masa_kerja_tahun'] >= 10 && $data['masa_kerja_tahun'] <= 15 && $data['masa_kerja_hari'] >= 1){
                    if ($jht->urutan == 2) {
                        $upah_dasar_karyawan = ($data['bpjs_kesehatan']['nominal'] + 50000)/25;
                    }
                }
                elseif($data['masa_kerja_tahun'] <= 10 || $data['masa_kerja_hari'] >= 1){
                    if ($jht->urutan == 1) {
                        $upah_dasar_karyawan = ($data['bpjs_kesehatan']['nominal'] + 0)/25;
                    }
                }
            }
            // $ritUmk = $this->ritUmk->find($request->hasil_kerja_1[$key]);

            // dd($ritUmk);

            // dd(round($upah_dasar_karyawan));
            if ($dpb == 7) {
                $upah_dasar = round($upah_dasar_karyawan);
            }else{
                $upah_dasar = round($upah_dasar_karyawan)/$request->rit[$key];
            }

            // dd($upah_dasar);

            $pengerjaan_supir_rit_weekly = $this->pengerjaanRitWeekly->where('kode_pengerjaan',$kode_pengerjaan)
                                                            ->where('karyawan_supir_rit_id',$pengerjaan_supir_rit_daily->karyawan_supir_rit_id)
                                                            ->update([
                                                                'total_hasil' => $operator_karyawan_supir_rit->upah_dasar,
                                                            ]);
            
            $pengerjaan_supir_rit_daily->update([
                'hasil_kerja_1' => $hasil_kerja_1,
                'dpb' => $dpb,
                // 'upah_dasar' => $operator_karyawan_supir_rit->upah_dasar*$request->rit[$key],
                'upah_dasar' => $upah_dasar,
            ]);
            // dd($dpb);
        }
        return response()->json([
            'success' => true,
            'message_title' => 'Berhasil',
            'message_content' => 'Data Berhasil Disimpan'
        ]);
        // dd($request->all());
    }
    
    public function hasil_kerja_supir_rit_input_karyawan($kode_pengerjaan, $nik, $month, $year)
    {
        $data['kode_pengerjaan'] = $kode_pengerjaan;
        $data['nik'] = $nik;
        $data['month'] = $month;
        $data['year'] = $year;

        $data['karyawan_supir_rit'] = $this->pengerjaanRitWeekly->select([
                                                          'pengerjaan_supir_rit_weekly.id as id',
                                                          'pengerjaan_supir_rit_weekly.karyawan_supir_rit_id as karyawan_supir_rit_id',
                                                          'operator_supir_rit_karyawan.nik as nik',  
                                                          'operator_supir_rit_karyawan.upah_dasar as upah_dasar',  
                                                          'biodata_karyawan.nama as nama',  
                                                          'biodata_karyawan.pin as pin',
                                                          'biodata_karyawan.tanggal_masuk as tanggal',
                                                          'operator_supir_rit_karyawan.tunjangan_kerja_id as tunjangan_kerja_id',
                                                          'pengerjaan_supir_rit_weekly.karyawan_supir_rit_id as karyawan_supir_rit_id',
                                                          'pengerjaan_supir_rit_weekly.total_hasil as total_hasil',
                                                          'pengerjaan_supir_rit_weekly.tunjangan_kerja as tunjangan_kerja',
                                                          'pengerjaan_supir_rit_weekly.tunjangan_kehadiran as tunjangan_kehadiran',
                                                          'pengerjaan_supir_rit_weekly.uang_makan as uang_makan',
                                                          'pengerjaan_supir_rit_weekly.plus_1 as plus_1',
                                                          'pengerjaan_supir_rit_weekly.plus_2 as plus_2',
                                                          'pengerjaan_supir_rit_weekly.plus_3 as plus_3',
                                                          'pengerjaan_supir_rit_weekly.minus_1 as minus_1',
                                                          'pengerjaan_supir_rit_weekly.minus_2 as minus_2',
                                                          'pengerjaan_supir_rit_weekly.jht as jht',
                                                          'pengerjaan_supir_rit_weekly.lembur as lembur',
                                                          'pengerjaan_supir_rit_weekly.bpjs_kesehatan as bpjs_kesehatan',
                                                          'pengerjaan_supir_rit_weekly.pensiun as pensiun',
                                                        ])
                                                        ->leftJoin('operator_supir_rit_karyawan','operator_supir_rit_karyawan.id','=','pengerjaan_supir_rit_weekly.karyawan_supir_rit_id')
                                                        ->leftJoin('itic_emp_new.biodata_karyawan','biodata_karyawan.nik','=','operator_supir_rit_karyawan.nik')
                                                        ->where('operator_supir_rit_karyawan.nik',$nik)
                                                        ->where('pengerjaan_supir_rit_weekly.kode_pengerjaan',$kode_pengerjaan)
                                                        ->first();

        $data['new_data_pengerjaan'] = $this->newDataPengerjaan->where('kode_pengerjaan',$kode_pengerjaan)->first();
        $data['explode_tanggal_pengerjaans'] = explode("#",$data['new_data_pengerjaan']['tanggal']);
        if (empty($data['karyawan_supir_rit']['plus_1'])) {
            $plus_1=null;
            $ket_plus_1=null;
        }else{
            $exp_plus1=explode("|",$data['karyawan_supir_rit']['plus_1']);
            // dd($exp_plus1);
            $plus_1=$exp_plus1[0];
            $ket_plus_1=$exp_plus1[1];
        }

        if (empty($data['karyawan_supir_rit']['plus_2'])) {
            $plus_2=null;
            $ket_plus_2=null;
        }else{
            $exp_plus2=explode("|",$data['karyawan_supir_rit']['plus_2']);
            $plus_2=$exp_plus2[0];
            $ket_plus_2=$exp_plus2[1];
        }

        if (empty($data['karyawan_supir_rit']['plus_3'])) {
            $plus_3=null;
            $ket_plus_3=null;
        }else{
            $exp_plus3=explode("|",$data['karyawan_supir_rit']['plus_3']);
            $plus_3=$exp_plus2[0];
            $ket_plus_3=$exp_plus2[1];
        }

        if (empty($data['karyawan_supir_rit']['minus_1'])) {
            $minus_1=null;
            $ket_minus_1=null;
        }else{
            $exp_minus1=explode("|",$data['karyawan_supir_rit']['minus_1']);
            $minus_1=$exp_minus1[0];
            $ket_minus_1=$exp_minus1[1];
        }

        if (empty($data['karyawan_supir_rit']['minus_2'])) {
            $minus_2=null;
            $ket_minus_2=null;
        }else{
            $exp_minus2=explode("|",$data['karyawan_supir_rit']['minus_2']);
            $minus_2=$exp_minus2[0];
            $ket_minus_2=$exp_minus2[1];
        }

        $data['jhts'] = $this->bpjsJht->where('status','y')->get();
        $data['bpjs_kesehatan'] = $this->bpjsKesehatan->select('nominal')->where('status','y')->first();

        //Hitung Masa Kerja
        // $data['karyawan_masa_kerja'] = DB::connection('emp')->table('log_posisi')
        //                                                     ->select('tanggal')
        //                                                     ->where('nik',$nik)
        //                                                     ->first();

        $awal  = new DateTime($data['karyawan_supir_rit']->tanggal);
        $akhir = new DateTime(); // Waktu sekarang
        $diff  = $awal->diff($akhir);
        $data['masa_kerja'] = $diff->y.' Tahun '.$diff->m.' Bulan '.$diff->d.' Hari';
        $data['masa_kerja_tahun'] = $diff->y;
        $data['masa_kerja_hari'] = $diff->d;

        $data['upah_dasar_karyawan'] = [];
        foreach ($data['jhts'] as $key => $jht) {
            if ($data['masa_kerja_tahun'] > 15) {
                if ($jht->urutan == 3) {
                    $data['upah_dasar_karyawan'] = ($data['bpjs_kesehatan']['nominal'] + 100000)/25;
                }
            }
            elseif($data['masa_kerja_tahun'] >= 10 && $data['masa_kerja_tahun'] <= 15 && $data['masa_kerja_hari'] >= 1){
                if ($jht->urutan == 2) {
                    $data['upah_dasar_karyawan'] = ($data['bpjs_kesehatan']['nominal'] + 50000)/25;
                }
            }
            elseif($data['masa_kerja_tahun'] <= 10 || $data['masa_kerja_hari'] >= 1){
                if ($jht->urutan == 1) {
                    $data['upah_dasar_karyawan'] = ($data['bpjs_kesehatan']['nominal'] + 0)/25;
                }
            }
        }

        $tgl_current_active = Carbon::now()->format('d');
        $bln_active = Carbon::now()->format('m');
        $bln_current_active = $month;
        $thn_current_active = $year;

        if($month == 1 && $tgl_current_active <10){
            $thn_prev_active = $year-1;
            $bulan_kemarin="$thn_prev_active-11-26";
            $bulan_sekarang="$thn_prev_active-12-26";
        }elseif($month == 1 && $tgl_current_active>10 or $month == 2 && $tgl_current_active<10 && $bln_active != 3){
            $thn_prev_active = $year-1;
            $bulan_kemarin="$thn_prev_active-12-26";
            $bulan_sekarang="$thn_current_active-01-26";
        }else{
            $bln_prev_active = $month-1; 
            $thn_prev_active = $year;
            $bulan_kemarin="$thn_prev_active-$bln_prev_active-26";
            $bulan_sekarang="$thn_current_active-$bln_current_active-26";
        }
        $data['tunjangan_kerjas'] = $this->tunjanganKerja->select('nominal')->where('id',$data['karyawan_supir_rit']['tunjangan_kerja_id'])->first();
        // dd($data['tunjangan_kerjas']);

        if(empty($data['tunjangan_kerjas'])){
            $data['tunjangan_kerja'] = 0;
        }else{
            $data['tunjangan_kerja'] = $data['tunjangan_kerjas']['nominal'];
        }

        //jumlah alpa
        $data['alpa'] = $this->presensiInfo->where('pin',$data['karyawan_supir_rit']['pin'])
                                    ->whereBetween('scan_date',[$bulan_kemarin,$bulan_sekarang])
                                    ->where('status',7)
                                    ->get();
        //jumlah diliburkan
        $data['diliburkan'] = $this->presensiInfo->where('pin',$data['karyawan_supir_rit']['pin'])
                                    ->whereBetween('scan_date',[$bulan_kemarin,$bulan_sekarang])
                                    ->where('status',14)
                                    ->get();
        //jumlah cuti
        $data['cuti'] = $this->presensiInfo->where('pin',$data['karyawan_supir_rit']['pin'])
                                    ->whereBetween('scan_date',[$bulan_kemarin,$bulan_sekarang])
                                    ->where('status',13)
                                    ->get();
        //jumlah sakit
        $data['sakit'] = $this->presensiInfo->where('pin',$data['karyawan_supir_rit']['pin'])
                                    ->whereBetween('scan_date',[$bulan_kemarin,$bulan_sekarang])
                                    ->where('status',4)
                                    ->get();
        // dd($data['sakit']);
        //jumlah ijin full
        $data['ijin_full'] = $this->presensiInfo->where('pin',$data['karyawan_supir_rit']['pin'])
                                    ->whereBetween('scan_date',[$bulan_kemarin,$bulan_sekarang])
                                    ->where('status',6)
                                    ->get();
        //jumlah status terlambat
        $data['terlambats'] = $this->presensiInfo->where('pin',$data['karyawan_supir_rit']['pin'])
                                    ->whereBetween('scan_date',[$bulan_kemarin,$bulan_sekarang])
                                    ->where('status',3)
                                    ->get();
        $telat_1=0; //terlambat < 5 menit
        $telat_2=0; //terlambat > 5 menit < 15 menit
        $telat_3=0; //terlambat > 15 menit < 1 jam
        $telat_4=0; //terlambat > 1 jam < 3 jam	
        $telat_5=0; //terlambat > 3 jam	
        $data['telat_1'] = $telat_1;
        $data['telat_2'] = $telat_2;
        $data['telat_3'] = $telat_3;
        $data['telat_4'] = $telat_4;
        $data['telat_5'] = $telat_5;
        foreach ($data['terlambats'] as $key => $terlambat) {
            $explode_keterangan_terlambat = explode("@",$terlambat->keterangan);
            $jam_keterangan_terlambat=strtotime($explode_keterangan_terlambat[1]);
            $jam_datang_terlambat=strtotime(Carbon::parse($terlambat['scan_date'])->format('H:i'));
            // $jam_datang_terlambat=strtotime($terlambat['jam_datang_telat']);
            $menit_telat=(($jam_datang_terlambat-$jam_keterangan_terlambat)/60);
            // dd($menit_telat);
            if($menit_telat <= 4){
                $telat_1=$telat_1+1;
                $data['telat_1'] = $telat_1;
            }
            else if(($menit_telat >= 5) && ($menit_telat <= 15) ){
                $telat_2=$telat_2+1;
                $data['telat_2'] = $telat_2;
            }
            else if(($menit_telat >= 16) && ($menit_telat <= 60)){
                $telat_3=$telat_3+1;
                $data['telat_3'] = $telat_3;
            }
            else if(($menit_telat >= 61) && ($menit_telat <= 179)){
                $telat_4=$telat_4+1;
                $data['telat_4'] = $telat_4;
            }else if($menit_telat > 179){
                $telat_5=$telat_5+1;
                $data['telat_5'] = $telat_5;
            }
            // else{
            //     $telat_4=$telat_4+1;
            //     $data['telat_4'] = $telat_4;
            // }
        }

        //jumlah status pulang awal
        $data['pulang_awals'] = $this->presensiInfo->where('pin',$data['karyawan_supir_rit']['pin'])
                                    ->whereBetween('scan_date',[$bulan_kemarin,$bulan_sekarang])
                                    ->where(function($query) {
                                        $query->where('status','=',9)
                                            ->orWhere('status','=',10);
                                    })
                                    // ->where('status',9)
                                    // ->where('status',10)
                                    ->get();
                                    // dd($data['pulang_awals']);
        // dd($data['pulang_awals']);
        $pulang_1=0; //pulang awal < 4 jam
		$pulang_2=0; //tpulang awal > 4 jam
        $data['pulang_1'] = $pulang_1;
        $data['pulang_2'] = $pulang_2;
        foreach ($data['pulang_awals'] as $key => $pulang_awal) {
            $explode_pulang_seharusnya=explode("@",$pulang_awal->keterangan);
			$jam_pulang_seharusnya=strtotime($explode_pulang_seharusnya[0]);
			$jam_pulang_awal=strtotime(Carbon::parse($pulang_awal['scan_date'])->format('H:i'));
			// $jam_pulang_awal=strtotime($pulang_awal->jam_pulang_awal);
			$menit_sisa_jam_kerja=(($jam_pulang_seharusnya-$jam_pulang_awal)/60);
			if ($menit_sisa_jam_kerja<=300){
                $pulang_1=$pulang_1+1;
                $data['pulang_1'] = $pulang_1;
            }
			else{
                $pulang_2=$pulang_2+1;
                $data['pulang_2'] = $pulang_2;
            }
        }

        // dd($data['pulang_2']);

        //jumlah ijin saat jam kerja
        $data['keluar_masuks'] = $this->keluarMasuk->where('nik',$data['karyawan_supir_rit']['nik'])
                                    ->whereBetween('tanggal_ijin',[$bulan_kemarin,$bulan_sekarang])
                                    ->get();
        // dd($data['keluar_masuks']);
        $ijin_15=0; //ijin kurang dari 15 menit
		$ijin_k4=0; //ijin > 15menit < 4 jam
		$ijin_l4=0; //ijin > 4 jam
        $data['ijin_15']=$ijin_15;
        $data['ijin_k4']=$ijin_k4;
        $data['ijin_l4']=$ijin_l4;
        foreach ($data['keluar_masuks'] as $key => $keluar_masuk) {
            $jam_keluar=strtotime($keluar_masuk->jam_keluar);
			$jam_datang=strtotime($keluar_masuk->jam_datang);
			$menit_keluar_masuk=(($jam_datang-$jam_keluar)/60);
			if ($menit_keluar_masuk<=15){
                $ijin_15=$ijin_15+1;
                $data['ijin_15']=$ijin_15;
            }
			else if (($menit_keluar_masuk>15)&&($menit_keluar_masuk<=239)){
                $ijin_k4=$ijin_k4+1;
                $data['ijin_k4']=$ijin_k4;
            }
			else {
                $ijin_l4=$ijin_l4+1;
                $data['ijin_l4']=$ijin_l4;
            }
        }

        //lihat tanggal masuk

        // $data['log_posisi'] = $this->logPosisi->select('tanggal')->where('nik',$nik)->first();
        // dd($data['log_posisi']);
		$selisih_tanggal_masuk=strtotime($bulan_kemarin)-strtotime($data['karyawan_supir_rit']->tanggal);
		if ($selisih_tanggal_masuk>=0)$div_tk=0;else $div_tk=75000;

        $total_potongan_tk=(75000*$data['alpa']->count())+(75000*$data['diliburkan']->count())+(75000*$data['sakit']->count())+(75000*$data['cuti']->count())+(75000*$data['ijin_full']->count())+($data['ijin_15']*25000)+($data['ijin_k4']*40000)+
        ($data['ijin_l4']*75000)+($data['pulang_1']*40000)+($data['pulang_2']*75000)+($data['telat_1']*15000)+($data['telat_2']*25000)+($data['telat_3']*30000)+($data['telat_4']*40000)+($data['telat_5']*75000)+$div_tk;
        if ($total_potongan_tk>75000){$total_potongan_tk=75000;}else {}

        // if ($data['karyawan_supir_rit']['tunjangan_kehadiran'] > 0) {
        //     $data['total_potongan_tk']=$data['karyawan_supir_rit']['tunjangan_kehadiran'];
        // }else{
        //     $data['total_potongan_tk']=$total_potongan_tk; 
        // }

        $data['total_potongan_tk']=$total_potongan_tk;

        return view('backend.pengerjaan.supir_rit.input_karyawan',$data);
    }

    public function hasil_kerja_supir_rit_input_karyawan_simpan(Request $request, $kode_pengerjaan, $nik, $month, $year)
    {
        $data['kode_pengerjaan'] = $kode_pengerjaan;
        $data['nik'] = $nik;
        $data['month'] = $month;
        $data['year'] = $year;

        $input['total_hasil'] = $request->upah_dasar;
        $input['uang_makan'] = $request->uang_makan;
        $input['plus_1'] = $request->plus_1."|".$request->keterangan_plus_1;
        $input['plus_2'] = $request->plus_2."|".$request->keterangan_plus_2;
        $input['plus_3'] = $request->plus_3."|".$request->keterangan_plus_3;
        $input['minus_1'] = $request->minus_1."|".$request->keterangan_minus_1;
        $input['minus_2'] = $request->minus_2."|".$request->keterangan_minus_2;

        if ($request->check_jht == 'on') {
            $input['jht'] = $request->jht;
        }else{
            $input['jht'] = 0;
        }

        if($request->check_bpjs_kesehatan == 'on'){
            $input['bpjs_kesehatan'] = $request->bpjs_kesehatan;
        }else{
            $input['bpjs_kesehatan'] = 0;
        }

        $hitung_lembur = round((($request->upah_dasar_karyawan*25/173*$request->lembur_1)*1.5)+(($request->upah_dasar_karyawan*25/173*$request->lembur_2)*2));

        $input['lembur'] = $hitung_lembur.'|'.$request->lembur_1.'|'.$request->lembur_2;
        
        $karyawan_supir_rit = $this->ritKaryawan->where('nik',$nik)->where('status','y')->first();
        if ($request->akhir_bulan == 'n') {
            $input['tunjangan_kerja'] = 0;
            $input['tunjangan_kehadiran'] = 0;
        }else{
            $input['tunjangan_kerja'] = $request->tunjangan_kerja;
            $input['tunjangan_kehadiran'] = 75000-$request->pot_tunjangan_kehadiran;
        }
        // $input['tunjangan_kehadiran'] = 75000-$request->pot_tunjangan_kehadiran;
        // $input['tunjangan_kerja'] = $karyawan_supir_rit->tunjangan_kerja->nominal;
        
        $pengerjaan_supir_rit_weekly = $this->pengerjaanRitWeekly->where('karyawan_supir_rit_id',$request->karyawan_supir_rit_id)
                                                        ->where('kode_pengerjaan',$kode_pengerjaan)
                                                        // ->update($input)
                                                        ->first()
                                                        ;
        // dd($request->karyawan_supir_rit_id);
        if (!empty($pengerjaan_supir_rit_weekly)) {
            $pengerjaan_supir_rit_weekly->update($input);
            return response()->json([
                'success' => true,
                'message_title' => 'Berhasil',
                'message_content' => 'Data Berhasil Disimpan'
            ]);
        }else{
            return response()->json([
                'success' => false,
                'message_title' => 'Gagal',
                'message_content' => 'Data Tidak Berhasil Disimpan'
            ]);
        }
        // $pengerjaan_supir_rit_weekly = PengerjaanRITWeekly::select([
        //                                                     'pengerjaan_supir_rit_weekly.id as id',
        //                                                     'operator_supir_rit_karyawan.nik as nik',  
        //                                                     'biodata_karyawan.nama as nama',  
        //                                                     'biodata_karyawan.pin as pin',  
        //                                                     'pengerjaan_supir_rit_weekly.karyawan_supir_rit_id as karyawan_supir_rit_id',
        //                                                     'pengerjaan_supir_rit_weekly.total_hasil as total_hasil',
        //                                                     'pengerjaan_supir_rit_weekly.tunjangan_kerja as tunjangan_kerja',
        //                                                     'pengerjaan_supir_rit_weekly.tunjangan_kehadiran as tunjangan_kehadiran',
        //                                                     'pengerjaan_supir_rit_weekly.uang_makan as uang_makan',
        //                                                     'pengerjaan_supir_rit_weekly.plus_1 as plus_1',
        //                                                     'pengerjaan_supir_rit_weekly.plus_2 as plus_2',
        //                                                     'pengerjaan_supir_rit_weekly.plus_3 as plus_3',
        //                                                     'pengerjaan_supir_rit_weekly.minus_1 as minus_1',
        //                                                     'pengerjaan_supir_rit_weekly.minus_2 as minus_2',
        //                                                     'pengerjaan_supir_rit_weekly.jht as jht',
        //                                                     'pengerjaan_supir_rit_weekly.bpjs_kesehatan as bpjs_kesehatan',
        //                                                     'pengerjaan_supir_rit_weekly.pensiun as pensiun',
        //                                                 ])
        //                                                 ->leftJoin('operator_supir_rit_karyawan','operator_supir_rit_karyawan.id','=','pengerjaan_supir_rit_weekly.karyawan_supir_rit_id')
        //                                                 ->leftJoin('biodata_karyawan','biodata_karyawan.nik','=','operator_supir_rit_karyawan.nik')
        //                                                 ->where('operator_supir_rit_karyawan.nik',$nik)
        //                                                 ->where('pengerjaan_supir_rit_weekly.kode_pengerjaan',$kode_pengerjaan)
        //                                                 ->update($input);
        
        // return 'Success';
    }

    public function close_periode(){
        $new_data_pengerjaans = $this->newDataPengerjaan->where('status','y')->get();
        foreach ($new_data_pengerjaans as $key => $new_data_pengerjaan) {
            $explode_tanggal_pengerjaans = explode("#",$new_data_pengerjaan->tanggal);
            $data_hasil_tanggal_pengerjaan = [];
            foreach (array_filter($explode_tanggal_pengerjaans) as $keys => $explode_tanggal_pengerjaan) {
                $hasil_tanggal_pengerjaan = Carbon::parse($explode_tanggal_pengerjaan)->isoFormat('D MMMM');
                array_push($data_hasil_tanggal_pengerjaan,$hasil_tanggal_pengerjaan);
            }
            if ($new_data_pengerjaan->status == 'y') {
                $status = '<span class="badge bg-primary">Berjalan</span>';
            }elseif($new_data_pengerjaan->status == 'n'){
                $status = '<span class="badge bg-success">Selesai</span>';
            }
            $data[] = [
                'id' => $new_data_pengerjaan->id,
                'kode_pengerjaan' => $new_data_pengerjaan->kode_pengerjaan,
                'tanggal' => $data_hasil_tanggal_pengerjaan,
                'status' => $status
            ];
        }
        return response()->json([
            'success' => true,
            'data' => $data
        ],200);
    }

    public function close_periode_update()
    {
        $new_data_pengerjaans = $this->newDataPengerjaan->where('status','y')->get();
        foreach ($new_data_pengerjaans as $key => $new_data_pengerjaan) {
            $new_data_pengerjaan->update([
                'status' => 'n'
            ]);
        };
        return response()->json([
            'success' => true,
            'message_title' => 'Close Periode Success',
            'message_content' => 'Periode ini telah ditutup, silahkan buat periode baru'
        ]);
    }
}
