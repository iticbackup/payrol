<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\KaryawanOperator;
use App\Models\KaryawanOperatorHarian;
use App\Models\RitKaryawan;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $data['total_all_karyawan_operator_borongan'] = KaryawanOperator::where('status','y')->where('jenis_operator_id',1)->count();
        $data['total_all_karyawan_operator_harian'] = KaryawanOperatorHarian::where('status','y')->where('jenis_operator_id',2)->count();
        $data['total_all_karyawan_rit'] = RitKaryawan::where('status','y')->count();
        return view('home',$data);
    }
}
