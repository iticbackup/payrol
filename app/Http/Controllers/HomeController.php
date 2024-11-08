<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\KaryawanOperator;
use App\Models\KaryawanOperatorHarian;
use App\Models\RitKaryawan;
use App\Models\KirimGaji;
use \Carbon\Carbon;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(
        KaryawanOperator $karyawan_operator,
        KaryawanOperatorHarian $karyawan_operator_harian,
        RitKaryawan $rit_karyawan,
        KirimGaji $kirim_gaji,
    )
    {
        $this->middleware('auth');
        $this->karyawan_operator = $karyawan_operator;
        $this->karyawan_operator_harian = $karyawan_operator_harian;
        $this->rit_karyawan = $rit_karyawan;
        $this->kirim_gaji = $kirim_gaji;
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $data['total_all_karyawan_operator_borongan'] = $this->karyawan_operator->where('status','y')->where('jenis_operator_id',1)->count();
        $data['total_all_karyawan_operator_harian'] = $this->karyawan_operator_harian->where('status','y')->where('jenis_operator_id',2)->count();
        $data['total_all_karyawan_rit'] = $this->rit_karyawan->where('status','y')->count();

        $data['start_year'] = Carbon::now()->startOfYear()->format('Y-m');
        $data['end_year'] = Carbon::now()->endOfYear()->format('Y-m');
        
        $data['total_gaji_karyawan_borongan'] = [];
        $data['total_gaji_karyawan_harian'] = [];
        $data['total_gaji_karyawan_supir_rit'] = [];
        for ($i=$data['start_year']; $i <= $data['end_year'] ; $i++) { 
            $total_gaji_borongan = $this->kirim_gaji->where('created_at','like','%'.$i.'%')
                                        ->where('kode_pengerjaan','like','%PB%')
                                        ->sum('nominal_gaji');
            $data['total_gaji_karyawan_borongan'][] = $total_gaji_borongan;

            $total_gaji_harian = $this->kirim_gaji->where('created_at','like','%'.$i.'%')
                                                ->where('kode_pengerjaan','like','%PH%')
                                                ->sum('nominal_gaji');
            $data['total_gaji_karyawan_harian'][] = $total_gaji_harian;
            
            $total_gaji_supir_rit = $this->kirim_gaji->where('created_at','like','%'.$i.'%')
                                                ->where('kode_pengerjaan','like','%PS%')
                                                ->sum('nominal_gaji');
            $data['total_gaji_karyawan_supir_rit'][] = $total_gaji_supir_rit;
        }

        // dd($data);
        
        return view('home',$data);
    }
}
