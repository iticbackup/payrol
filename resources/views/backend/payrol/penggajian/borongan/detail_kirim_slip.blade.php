@extends('layouts.backend.app')

@section('title')
    Detail Payrol Borongan
@endsection

@section('css')
    {{-- <link href="{{ URL::asset('public/assets/plugins/datatables/dataTables.bootstrap5.min.css') }}" rel="stylesheet"
    type="text/css" />
    <link href="{{ URL::asset('public/assets/plugins/datatables/buttons.bootstrap5.min.css') }}" rel="stylesheet"
    type="text/css" />
    <link href="{{ URL::asset('public/assets/plugins/datatables/responsive.bootstrap4.min.css') }}" rel="stylesheet"
    type="text/css" /> --}}
    <link href="{{ URL::asset('public/assets/plugins/sweet-alert2/sweetalert2.min.css') }}" rel="stylesheet" type="text/css">
    <script src="{{ URL::asset('public/assets/js/pages/jquery.sweet-alert.init.js') }}"></script>
@endsection

@section('content')
    @component('components.breadcrumb')
        @slot('li_1')
            Payrol
        @endslot
        @slot('li_3')
            @yield('title')
        @endslot
        @slot('title')
            @yield('title')
        @endslot
    @endcomponent

    <div class="row">
        <div class="col-12">
            <form id="kirim_slip" method="POST" class="card" enctype="multipart/form-data">
                @csrf
                <div class="card-header">
                    <h5>Kode ID : {{ $new_data_pengerjaan->kode_pengerjaan }}
                        @if ($new_data_pengerjaan->status == 'n')
                        <i class="far fa-check-circle text-success"></i>
                        @endif
                        <button type="submit" class="btn btn-primary">
                            <svg xmlns="http://www.w3.org/2000/svg" width="1em" height="1em" viewBox="0 0 512 512">
                                <path fill="currentColor" d="m476.59 227.05l-.16-.07L49.35 49.84A23.56 23.56 0 0 0 27.14 52A24.65 24.65 0 0 0 16 72.59v113.29a24 24 0 0 0 19.52 23.57l232.93 43.07a4 4 0 0 1 0 7.86L35.53 303.45A24 24 0 0 0 16 327v113.31A23.57 23.57 0 0 0 26.59 460a23.94 23.94 0 0 0 13.22 4a24.55 24.55 0 0 0 9.52-1.93L476.4 285.94l.19-.09a32 32 0 0 0 0-58.8" />
                            </svg> Kirim Gaji
                        </button>
                        <button type="button" class="btn" style="background-color: #FD8B51; color: black" onclick="window.location.href='{{ route('payrol.borongan.borongan_cek_email_slip_gaji',['kode_pengerjaan' => $new_data_pengerjaan->kode_pengerjaan ]) }}'">
                            <svg xmlns="http://www.w3.org/2000/svg" width="1.5em" height="1.5em" viewBox="0 0 512 512">
                                <path fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="32" d="M320 96H88a40 40 0 0 0-40 40v240a40 40 0 0 0 40 40h334.73a40 40 0 0 0 40-40V239" />
                                <path fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="32" d="m112 160l144 112l87-65.67" />
                                <circle cx="431.95" cy="128.05" r="47.95" fill="currentColor" />
                                <path fill="currentColor" d="M432 192a63.95 63.95 0 1 1 63.95-63.95A64 64 0 0 1 432 192m0-95.9a32 32 0 1 0 31.95 32a32 32 0 0 0-31.95-32" />
                            </svg>
                            Cek Email
                        </button>
                    </h5>
                </div>
                <div class="card-body">
                    <table id="datatables" class="table table-bordered dt-responsive nowrap"
                        style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Nama Karyawan</th>
                                <th>Jenis Pengerjaan</th>
                                <th>Nominal Gaji</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $total_all_gaji = [];
                            @endphp
                            @foreach ($pengerjaan_weeklys as $key => $pengerjaan_weekly)
                            @php
                                $explode_tanggal_pengerjaans = explode('#',$new_data_pengerjaan['tanggal']);
                                $exp_tanggals = array_filter($explode_tanggal_pengerjaans);
                                $a = count($exp_tanggals);
                                $exp_tgl_awal = explode('-', $exp_tanggals[1]);
                                $exp_tgl_akhir = explode('-', $exp_tanggals[$a]);
                                // $jenis_operator_id = [];

                                $pengerjaans = \App\Models\Pengerjaan::where('operator_karyawan_id',$pengerjaan_weekly->operator_karyawan_id)
                                                                    ->where('kode_pengerjaan',$new_data_pengerjaan->kode_pengerjaan)
                                                                    ->get();
                                $total_upah_hasil_kerja = [];
                                $total_lembur_kerja = [];

                                foreach ($pengerjaans as $pengerjaan) {
                                    #Borongan Packing
                                    if ($pengerjaan->operator_karyawan->jenis_operator_detail_pekerjaan_id == 1) {
                                        for ($i=1; $i <= 5 ; $i++) { 
                                            ${"explode_hasil_kerja_".$i} = explode("|",$pengerjaan['hasil_kerja_'.$i]);
                                            ${"umk_borongan_lokal_".$i} = \App\Models\UMKBoronganLokal::where('id',${"explode_hasil_kerja_".$i}[0])
                                                                                                    ->first();
                                            if (empty(${"umk_borongan_lokal_".$i})) {
                                                ${"jenis_produk_".$i} = '-';
                                                ${"hasil_kerja_".$i} = null;
                                                ${"data_explode_hasil_kerja_".$i} = '-';
                                                ${"lembur_".$i} = 1;
                                                ${"total_hasil_".$i} = 0;
                                            }else{
                                                ${"jenis_produk_".$i} = ${"umk_borongan_lokal_".$i}['jenis_produk'];
                                                ${"hasil_kerja_".$i} = ${"explode_hasil_kerja_".$i}[1]*${"umk_borongan_lokal_".$i}['umk_packing'];
                                                ${"data_explode_hasil_kerja_".$i} = ${"explode_hasil_kerja_".$i}[1];
                                                ${"explode_lembur_".$i} = explode("|",$pengerjaan['lembur']);
                                                ${"explode_status_lembur_".$i} = explode("-",${"explode_lembur_".$i}[$i]);
                                                if(${"explode_status_lembur_".$i}[1] == 'y'){
                                                    ${"lembur_".$i} = 1.5;
                                                }else{
                                                    ${"lembur_".$i} = 1;
                                                }
                                            }
                                        }
                                    }

                                    #Borongan Bandrol
                                    if ($pengerjaan->operator_karyawan->jenis_operator_detail_pekerjaan_id == 2) {
                                        for ($i=1; $i <= 5 ; $i++) { 
                                            ${"explode_hasil_kerja_".$i} = explode("|",$pengerjaan['hasil_kerja_'.$i]);
                                            ${"umk_borongan_lokal_".$i} = \App\Models\UMKBoronganLokal::where('id',${"explode_hasil_kerja_".$i}[0])
                                                                                                    ->first();
                                            if (empty(${"umk_borongan_lokal_".$i})) {
                                                ${"jenis_produk_".$i} = '-';
                                                ${"hasil_kerja_".$i} = null;
                                                ${"data_explode_hasil_kerja_".$i} = '-';
                                                ${"lembur_".$i} = 1;
                                                ${"total_hasil_".$i} = 0;
                                            }else{
                                                ${"jenis_produk_".$i} = ${"umk_borongan_lokal_".$i}['jenis_produk'];
                                                ${"hasil_kerja_".$i} = ${"explode_hasil_kerja_".$i}[1]*${"umk_borongan_lokal_".$i}['umk_bandrol'];
                                                ${"data_explode_hasil_kerja_".$i} = ${"explode_hasil_kerja_".$i}[1];
                                                ${"explode_lembur_".$i} = explode("|",$pengerjaan['lembur']);
                                                ${"explode_status_lembur_".$i} = explode("-",${"explode_lembur_".$i}[$i]);
                                                if(${"explode_status_lembur_".$i}[1] == 'y'){
                                                    ${"lembur_".$i} = 1.5;
                                                }else{
                                                    ${"lembur_".$i} = 1;
                                                }
                                            }
                                        }
                                    }

                                    #Borongan Inner
                                    if ($pengerjaan->operator_karyawan->jenis_operator_detail_pekerjaan_id == 3) {
                                        for ($i=1; $i <= 5 ; $i++) { 
                                            ${"explode_hasil_kerja_".$i} = explode("|",$pengerjaan['hasil_kerja_'.$i]);
                                            ${"umk_borongan_lokal_".$i} = \App\Models\UMKBoronganLokal::where('id',${"explode_hasil_kerja_".$i}[0])
                                                                                                    ->first();
                                            if (empty(${"umk_borongan_lokal_".$i})) {
                                                ${"jenis_produk_".$i} = '-';
                                                ${"hasil_kerja_".$i} = null;
                                                ${"data_explode_hasil_kerja_".$i} = '-';
                                                ${"lembur_".$i} = 1;
                                                ${"total_hasil_".$i} = 0;
                                            }else{
                                                ${"jenis_produk_".$i} = ${"umk_borongan_lokal_".$i}['jenis_produk'];
                                                ${"hasil_kerja_".$i} = ${"explode_hasil_kerja_".$i}[1]*${"umk_borongan_lokal_".$i}['umk_inner'];
                                                ${"data_explode_hasil_kerja_".$i} = ${"explode_hasil_kerja_".$i}[1];
                                                ${"explode_lembur_".$i} = explode("|",$pengerjaan['lembur']);
                                                ${"explode_status_lembur_".$i} = explode("-",${"explode_lembur_".$i}[$i]);
                                                if(${"explode_status_lembur_".$i}[1] == 'y'){
                                                    ${"lembur_".$i} = 1.5;
                                                }else{
                                                    ${"lembur_".$i} = 1;
                                                }
                                            }
                                        }
                                    }

                                    #Borongan Outer
                                    if ($pengerjaan->operator_karyawan->jenis_operator_detail_pekerjaan_id == 4) {
                                        for ($i=1; $i <= 5 ; $i++) { 
                                            ${"explode_hasil_kerja_".$i} = explode("|",$pengerjaan['hasil_kerja_'.$i]);
                                            ${"umk_borongan_lokal_".$i} = \App\Models\UMKBoronganLokal::where('id',${"explode_hasil_kerja_".$i}[0])
                                                                                                    ->first();
                                            if (empty(${"umk_borongan_lokal_".$i})) {
                                                ${"jenis_produk_".$i} = '-';
                                                ${"hasil_kerja_".$i} = null;
                                                ${"data_explode_hasil_kerja_".$i} = '-';
                                                ${"lembur_".$i} = 1;
                                                ${"total_hasil_".$i} = 0;
                                            }else{
                                                ${"jenis_produk_".$i} = ${"umk_borongan_lokal_".$i}['jenis_produk'];
                                                ${"hasil_kerja_".$i} = ${"explode_hasil_kerja_".$i}[1]*${"umk_borongan_lokal_".$i}['umk_outer'];
                                                ${"data_explode_hasil_kerja_".$i} = ${"explode_hasil_kerja_".$i}[1];
                                                ${"explode_lembur_".$i} = explode("|",$pengerjaan['lembur']);
                                                ${"explode_status_lembur_".$i} = explode("-",${"explode_lembur_".$i}[$i]);
                                                if(${"explode_status_lembur_".$i}[1] == 'y'){
                                                    ${"lembur_".$i} = 1.5;
                                                }else{
                                                    ${"lembur_".$i} = 1;
                                                }
                                            }
                                        }
                                    }

                                    #Borongan Ekspor Packing
                                    if ($pengerjaan->operator_karyawan->jenis_operator_detail_pekerjaan_id == 5) {
                                        for ($i=1; $i <= 5 ; $i++) { 
                                            ${"explode_hasil_kerja_".$i} = explode("|",$pengerjaan['hasil_kerja_'.$i]);
                                            ${"umk_borongan_lokal_".$i} = \App\Models\UMKBoronganEkspor::where('id',${"explode_hasil_kerja_".$i}[0])
                                                                                                    ->first();
                                            if (empty(${"umk_borongan_lokal_".$i})) {
                                                ${"jenis_produk_".$i} = '-';
                                                ${"hasil_kerja_".$i} = null;
                                                ${"data_explode_hasil_kerja_".$i} = '-';
                                                ${"lembur_".$i} = 1;
                                                ${"total_hasil_".$i} = 0;
                                            }else{
                                                ${"jenis_produk_".$i} = ${"umk_borongan_lokal_".$i}['jenis_produk'];
                                                ${"hasil_kerja_".$i} = ${"explode_hasil_kerja_".$i}[1]*${"umk_borongan_lokal_".$i}['umk_packing'];
                                                ${"data_explode_hasil_kerja_".$i} = ${"explode_hasil_kerja_".$i}[1];
                                                ${"explode_lembur_".$i} = explode("|",$pengerjaan['lembur']);
                                                ${"explode_status_lembur_".$i} = explode("-",${"explode_lembur_".$i}[$i]);
                                                if(${"explode_status_lembur_".$i}[1] == 'y'){
                                                    ${"lembur_".$i} = 1.5;
                                                }else{
                                                    ${"lembur_".$i} = 1;
                                                }
                                            }
                                        }
                                    }

                                    #Borongan Ekspor Kemas
                                    if ($pengerjaan->operator_karyawan->jenis_operator_detail_pekerjaan_id == 6) {
                                        for ($i=1; $i <= 5 ; $i++) { 
                                            ${"explode_hasil_kerja_".$i} = explode("|",$pengerjaan['hasil_kerja_'.$i]);
                                            ${"umk_borongan_lokal_".$i} = \App\Models\UMKBoronganEkspor::where('id',${"explode_hasil_kerja_".$i}[0])
                                                                                                    ->first();
                                            if (empty(${"umk_borongan_lokal_".$i})) {
                                                ${"jenis_produk_".$i} = '-';
                                                ${"hasil_kerja_".$i} = null;
                                                ${"data_explode_hasil_kerja_".$i} = '-';
                                                ${"lembur_".$i} = 1;
                                                ${"total_hasil_".$i} = 0;
                                            }else{
                                                ${"jenis_produk_".$i} = ${"umk_borongan_lokal_".$i}['jenis_produk'];
                                                ${"hasil_kerja_".$i} = ${"explode_hasil_kerja_".$i}[1]*${"umk_borongan_lokal_".$i}['umk_kemas'];
                                                ${"data_explode_hasil_kerja_".$i} = ${"explode_hasil_kerja_".$i}[1];
                                                ${"explode_lembur_".$i} = explode("|",$pengerjaan['lembur']);
                                                ${"explode_status_lembur_".$i} = explode("-",${"explode_lembur_".$i}[$i]);
                                                if(${"explode_status_lembur_".$i}[1] == 'y'){
                                                    ${"lembur_".$i} = 1.5;
                                                }else{
                                                    ${"lembur_".$i} = 1;
                                                }
                                            }
                                        }
                                    }

                                    #Borongan Ekspor Gagang
                                    if ($pengerjaan->operator_karyawan->jenis_operator_detail_pekerjaan_id == 7) {
                                        for ($i=1; $i <= 5 ; $i++) { 
                                            ${"explode_hasil_kerja_".$i} = explode("|",$pengerjaan['hasil_kerja_'.$i]);
                                            ${"umk_borongan_lokal_".$i} = \App\Models\UMKBoronganEkspor::where('id',${"explode_hasil_kerja_".$i}[0])
                                                                                                    ->first();
                                            if (empty(${"umk_borongan_lokal_".$i})) {
                                                ${"jenis_produk_".$i} = '-';
                                                ${"hasil_kerja_".$i} = null;
                                                ${"data_explode_hasil_kerja_".$i} = '-';
                                                ${"lembur_".$i} = 1;
                                                ${"total_hasil_".$i} = 0;
                                            }else{
                                                ${"jenis_produk_".$i} = ${"umk_borongan_lokal_".$i}['jenis_produk'];
                                                ${"hasil_kerja_".$i} = ${"explode_hasil_kerja_".$i}[1]*${"umk_borongan_lokal_".$i}['umk_pilih_gagang'];
                                                ${"data_explode_hasil_kerja_".$i} = ${"explode_hasil_kerja_".$i}[1];
                                                ${"explode_lembur_".$i} = explode("|",$pengerjaan['lembur']);
                                                ${"explode_status_lembur_".$i} = explode("-",${"explode_lembur_".$i}[$i]);
                                                if(${"explode_status_lembur_".$i}[1] == 'y'){
                                                    ${"lembur_".$i} = 1.5;
                                                }else{
                                                    ${"lembur_".$i} = 1;
                                                }
                                            }
                                        }
                                    }

                                    #Borongan Ambri Isi Etiket
                                    if ($pengerjaan->operator_karyawan->jenis_operator_detail_pekerjaan_id == 8) {
                                        for ($i=1; $i <= 5 ; $i++) { 
                                            ${"explode_hasil_kerja_".$i} = explode("|",$pengerjaan['hasil_kerja_'.$i]);
                                            ${"umk_borongan_lokal_".$i} = \App\Models\UMKBoronganAmbri::where('id',${"explode_hasil_kerja_".$i}[0])
                                                                                                    ->first();
                                            if (empty(${"umk_borongan_lokal_".$i})) {
                                                ${"jenis_produk_".$i} = '-';
                                                ${"hasil_kerja_".$i} = null;
                                                ${"data_explode_hasil_kerja_".$i} = '-';
                                                ${"lembur_".$i} = 1;
                                                ${"total_hasil_".$i} = 0;
                                            }else{
                                                ${"jenis_produk_".$i} = ${"umk_borongan_lokal_".$i}['jenis_produk'];
                                                ${"hasil_kerja_".$i} = ${"explode_hasil_kerja_".$i}[1]*${"umk_borongan_lokal_".$i}['umk_etiket'];
                                                ${"data_explode_hasil_kerja_".$i} = ${"explode_hasil_kerja_".$i}[1];
                                                ${"explode_lembur_".$i} = explode("|",$pengerjaan['lembur']);
                                                ${"explode_status_lembur_".$i} = explode("-",${"explode_lembur_".$i}[$i]);
                                                if(${"explode_status_lembur_".$i}[1] == 'y'){
                                                    ${"lembur_".$i} = 1.5;
                                                }else{
                                                    ${"lembur_".$i} = 1;
                                                }
                                            }
                                        }
                                    }

                                    #Borongan Ambri Las Tepi
                                    if ($pengerjaan->operator_karyawan->jenis_operator_detail_pekerjaan_id == 9) {
                                        for ($i=1; $i <= 5 ; $i++) { 
                                            ${"explode_hasil_kerja_".$i} = explode("|",$pengerjaan['hasil_kerja_'.$i]);
                                            ${"umk_borongan_lokal_".$i} = \App\Models\UMKBoronganAmbri::where('id',${"explode_hasil_kerja_".$i}[0])
                                                                                                    ->first();
                                            if (empty(${"umk_borongan_lokal_".$i})) {
                                                ${"jenis_produk_".$i} = '-';
                                                ${"hasil_kerja_".$i} = null;
                                                ${"data_explode_hasil_kerja_".$i} = '-';
                                                ${"lembur_".$i} = 1;
                                                ${"total_hasil_".$i} = 0;
                                            }else{
                                                ${"jenis_produk_".$i} = ${"umk_borongan_lokal_".$i}['jenis_produk'];
                                                ${"hasil_kerja_".$i} = ${"explode_hasil_kerja_".$i}[1]*${"umk_borongan_lokal_".$i}['umk_las_tepi'];
                                                ${"data_explode_hasil_kerja_".$i} = ${"explode_hasil_kerja_".$i}[1];
                                                ${"explode_lembur_".$i} = explode("|",$pengerjaan['lembur']);
                                                ${"explode_status_lembur_".$i} = explode("-",${"explode_lembur_".$i}[$i]);
                                                if(${"explode_status_lembur_".$i}[1] == 'y'){
                                                    ${"lembur_".$i} = 1.5;
                                                }else{
                                                    ${"lembur_".$i} = 1;
                                                }
                                            }
                                        }
                                    }

                                    #Borongan Ambri Isi Ambri
                                    if ($pengerjaan->operator_karyawan->jenis_operator_detail_pekerjaan_id == 11) {
                                        for ($i=1; $i <= 5 ; $i++) { 
                                            ${"explode_hasil_kerja_".$i} = explode("|",$pengerjaan['hasil_kerja_'.$i]);
                                            ${"umk_borongan_lokal_".$i} = \App\Models\UMKBoronganAmbri::where('id',${"explode_hasil_kerja_".$i}[0])
                                                                                                    ->first();
                                            if (empty(${"umk_borongan_lokal_".$i})) {
                                                ${"jenis_produk_".$i} = '-';
                                                ${"hasil_kerja_".$i} = null;
                                                ${"data_explode_hasil_kerja_".$i} = '-';
                                                ${"lembur_".$i} = 1;
                                                ${"total_hasil_".$i} = 0;
                                            }else{
                                                ${"jenis_produk_".$i} = ${"umk_borongan_lokal_".$i}['jenis_produk'];
                                                ${"hasil_kerja_".$i} = ${"explode_hasil_kerja_".$i}[1]*${"umk_borongan_lokal_".$i}['umk_ambri'];
                                                ${"data_explode_hasil_kerja_".$i} = ${"explode_hasil_kerja_".$i}[1];
                                                ${"explode_lembur_".$i} = explode("|",$pengerjaan['lembur']);
                                                ${"explode_status_lembur_".$i} = explode("-",${"explode_lembur_".$i}[$i]);
                                                if(${"explode_status_lembur_".$i}[1] == 'y'){
                                                    ${"lembur_".$i} = 1.5;
                                                }else{
                                                    ${"lembur_".$i} = 1;
                                                }
                                            }
                                        }
                                    }

                                    $total_hasil_kerja = (round(($hasil_kerja_1*$lembur_1)+($hasil_kerja_2*$lembur_2)+($hasil_kerja_3*$lembur_3)+($hasil_kerja_4*$lembur_4)+($hasil_kerja_5*$lembur_5)))-$pengerjaan['uang_lembur'];
                                    $total_lembur = $pengerjaan['uang_lembur'];

                                    array_push($total_upah_hasil_kerja,$total_hasil_kerja);
                                    array_push($total_lembur_kerja,$total_lembur);

                                    if ($new_data_pengerjaan['akhir_bulan'] == 'y') {
                                        if (empty($pengerjaan_weekly->tunjangan_kerja)) {
                                            $tunjangan_kerja = 0;
                                        }else{
                                            $tunjangan_kerja = $pengerjaan_weekly->tunjangan_kerja;
                                        }
                                    }else{
                                        $tunjangan_kerja = 0;
                                    }

                                    if (empty($pengerjaan_weekly->tunjangan_kehadiran)) {
                                        $tunjangan_kehadiran = 0;
                                    }else{
                                        $tunjangan_kehadiran = $pengerjaan_weekly->tunjangan_kehadiran;
                                    }

                                    if (empty($pengerjaan_weekly->uang_makan)) {
                                        $uang_makan = 0;
                                    }else{
                                        $uang_makan = $pengerjaan_weekly->uang_makan;
                                    }

                                    if (empty($pengerjaan_weekly->plus_1)) {
                                        $plus_1 = 0;
                                        $ket_plus_1 = null;
                                    }else{
                                        $explode_plus_1 = explode("|",$pengerjaan_weekly->plus_1);
                                        $plus_1 = floatval($explode_plus_1[0]);
                                        $ket_plus_1 = $explode_plus_1[1];
                                    }

                                    if (empty($pengerjaan_weekly->plus_2)) {
                                        $plus_2 = 0;
                                        $ket_plus_2 = null;
                                    }else{
                                        $explode_plus_2 = explode("|",$pengerjaan_weekly->plus_2);
                                        $plus_2 = floatval($explode_plus_2[0]);
                                        $ket_plus_2 = $explode_plus_2[1];
                                    }

                                    if (empty($pengerjaan_weekly->plus_3)) {
                                        $plus_3 = 0;
                                        $ket_plus_3 = null;
                                    }else{
                                        $explode_plus_3 = explode("|",$pengerjaan_weekly->plus_3);
                                        $plus_3 = floatval($explode_plus_3[0]);
                                        $ket_plus_3 = $explode_plus_3[1];
                                    }

                                    if (empty($pengerjaan_weekly->jht)) {
                                        $jht = 0;
                                    }else{
                                        $jht = $pengerjaan_weekly->jht;
                                    }

                                    if (empty($pengerjaan_weekly->bpjs_kesehatan)) {
                                        $bpjs_kesehatan = 0;
                                    }else{
                                        $bpjs_kesehatan = $pengerjaan_weekly->bpjs_kesehatan;
                                    }

                                    if (empty($pengerjaan_weekly->minus_1)) {
                                        $minus_1 = '0';
                                        $ket_minus_1 = null;
                                    }else{
                                        $explode_minus_1 = explode("|",$pengerjaan_weekly->minus_1);
                                        $minus_1 = floatval($explode_minus_1[0]);
                                        $ket_minus_1 = $explode_minus_1[1];
                                    }

                                    if (empty($pengerjaan_weekly->minus_2)) {
                                        $minus_2 = 0;
                                        $ket_minus_2 = null;
                                    }else{
                                        $explode_minus_2 = explode("|",$pengerjaan_weekly->minus_2);
                                        $minus_2 = floatval($explode_minus_2[0]);
                                        $ket_minus_2 = $explode_minus_2[1];
                                    }

                                    $total_gaji_diterima = (array_sum($total_upah_hasil_kerja)
                                                                +array_sum($total_lembur_kerja)
                                                                +$tunjangan_kerja
                                                                +$tunjangan_kehadiran
                                                                +$uang_makan
                                                                +$plus_1
                                                                +$plus_2
                                                                +$plus_3
                                                                )
                                                                -
                                                                ($jht+$bpjs_kesehatan+$minus_1+$minus_2)
                                                                ;
                                }

                                array_push($total_all_gaji,$total_gaji_diterima);
                            @endphp
                                <tr>
                                    <td>
                                        {{ $key+1 }}
                                        <input type="hidden" name="id[]" value="{{ $pengerjaan_weekly->id }}">
                                        <input type="hidden" name="nominal_gaji[]" value="{{ $total_gaji_diterima }}">
                                    </td>
                                    {{-- <td>{{ ($pengerjaan_weeklys->perPage() * ($pengerjaan_weeklys->currentPage() - 1)) + $loop->iteration }}</td> --}}
                                    <td>{{ $pengerjaan_weekly->operator_karyawan->biodata_karyawan->nik.' - '.$pengerjaan_weekly->operator_karyawan->biodata_karyawan->nama }}</td>
                                    <td>{{ $pengerjaan_weekly->operator_karyawan->jenis_operator->jenis_operator.' - '.$pengerjaan_weekly->operator_karyawan->jenis_operator_detail_pengerjaan->jenis_posisi_pekerjaan }}</td>
                                    <td>{{ 'Rp. '.number_format($total_gaji_diterima,0,',','.') }}</td>
                                    <td>
                                        {{-- <form enctype="multipart/form-data" onsubmit="send_slip($new_data_pengerjaan->kode_pengerjaan,$pengerjaan_weekly->id)"> --}}
                                        <a href="{{ route('payrol.borongan.borongan_cek_slip_gaji',['kode_pengerjaan' => $new_data_pengerjaan->kode_pengerjaan, 'id' => $pengerjaan_weekly->id]) }}" class="btn btn-primary" target="_blank">Cek Gaji</a>
                                        {{-- @csrf --}}
                                        {{-- <button class="btn btn-info">Kirim Gaji</button> --}}
                                        {{-- </form> --}}
                                    </td>
                                </tr>
                            @endforeach
                                <tr>
                                    <td colspan="3" class="text-center" style="font-weight: bold">Total Gaji</td>
                                    <td style="font-weight: bold">{{ 'Rp. '.number_format(array_sum($total_all_gaji),0,',','.') }}</td>
                                </tr>
                        </tbody>
                    </table>
                    {{-- {{ $pengerjaan_weeklys->links('vendor.pagination.paginate_custom1') }} --}}
                </div>
            </form>
        </div>
    </div>
@endsection
@section('script')
    {{-- <script src="{{ URL::asset('public/assets/plugins/datatables/jquery.dataTables.min.js') }}"></script>
    <script src="{{ URL::asset('public/assets/plugins/datatables/dataTables.bootstrap5.min.js') }}"></script>
    <script src="{{ URL::asset('public/assets/plugins/datatables/dataTables.buttons.min.js') }}"></script>
    <script src="{{ URL::asset('public/assets/plugins/datatables/buttons.bootstrap5.min.js') }}"></script>
    <script src="{{ URL::asset('public/assets/plugins/datatables/jszip.min.js') }}"></script>
    <script src="{{ URL::asset('public/assets/plugins/datatables/pdfmake.min.js') }}"></script>
    <script src="{{ URL::asset('public/assets/plugins/datatables/vfs_fonts.js') }}"></script>
    <script src="{{ URL::asset('public/assets/plugins/datatables/buttons.html5.min.js') }}"></script>
    <script src="{{ URL::asset('public/assets/plugins/datatables/buttons.print.min.js') }}"></script>
    <script src="{{ URL::asset('public/assets/plugins/datatables/buttons.colVis.min.js') }}"></script>
    <script src="{{ URL::asset('public/assets/plugins/datatables/dataTables.responsive.min.js') }}"></script>
    <script src="{{ URL::asset('public/assets/plugins/datatables/responsive.bootstrap4.min.js') }}"></script>
    <script src="{{ URL::asset('public/assets/js/pages/jquery.datatable.init.js') }}"></script> --}}
    <script src="{{ URL::asset('public/assets/plugins/sweet-alert2/sweetalert2.min.js') }}"></script>
    <script>
        // var table = $('#datatables').DataTable(
            
        // );

        function send_slip(kode_pengerjaan,id)
        {
            alert(kode_pengerjaan);
        }

        $('#kirim_slip').submit(function(e) {
            e.preventDefault();
            let formData = new FormData(this);
            $.ajax({
                type: 'POST',
                url: "{{ route('payrol.borongan.borongan_kirim_slip_gaji',['kode_pengerjaan' => $new_data_pengerjaan->kode_pengerjaan]) }}",
                data: formData,
                contentType: false,
                processData: false,
                beforeSend: () => {
                    let timerInterval;
                    Swal.fire({
                        title: "Sedang Proses Dikirim!",
                        // timer: 2000,
                        timerProgressBar: true,
                        showConfirmButton: false,
                        didOpen: () => {
                            Swal.showLoading();
                            const timer = Swal.getPopup().querySelector("b");
                            timerInterval = setInterval(() => {
                            timer.textContent = `${Swal.getTimerLeft()}`;
                            }, 100);
                        },
                        willClose: () => {
                            clearInterval(timerInterval);
                        }
                        }).then((result) => {
                        /* Read more about handling dismissals below */
                        if (result.dismiss === Swal.DismissReason.timer) {
                            console.log("I was closed by the timer");
                        }
                    });
                },
                success: (result) => {
                    if (result.success != false) {
                        Swal.fire({
                            title: result.message_title,
                            text: result.message_content,
                            icon: "success"
                        });
                    } else {
                        Swal.fire({
                            title: 'Gagal Terkirim',
                            icon: "error"
                        });
                    }
                    console.log(result);
                },
                error: function(request, status, error) {
                    Swal.fire({
                        title: 'Error',
                        text: error,
                        icon: "error"
                    });
                }
            });
        });
    </script>
@endsection