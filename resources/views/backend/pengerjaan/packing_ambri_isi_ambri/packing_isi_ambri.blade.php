@extends('layouts.backend.app')

@section('title')
    Pengerjaan - {{ $jenis_operator_detail_pekerjaan->jenis_posisi_pekerjaan }}
@endsection

@section('css')
    <link href="{{ URL::asset('public/assets/plugins/datatables/dataTables.bootstrap5.min.css') }}" rel="stylesheet"
        type="text/css" />
    <link href="{{ URL::asset('public/assets/plugins/datatables/buttons.bootstrap5.min.css') }}" rel="stylesheet"
        type="text/css" />
    <link href="{{ URL::asset('public/assets/plugins/datatables/responsive.bootstrap4.min.css') }}" rel="stylesheet"
        type="text/css" />
    <link href="{{ URL::asset('public/assets/css/iziToast.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ URL::asset('public/assets/plugins/sweet-alert2/sweetalert2.min.css') }}" rel="stylesheet"
        type="text/css">
    <script src="{{ URL::asset('public/assets/js/pages/jquery.sweet-alert.init.js') }}"></script>

    <style>
        @media (max-width: 1518px) {

            .table-container { 
                /* width: 100% !important; */
                overflow-x: scroll; 
                width: 68%;
                /* width: 85%; */
            }

            /* .layouts {
                margin-left: 26%; 
                margin-right: 9%
            } */

            /* th, td {min-width: 200px; } */
        }
    </style>
@endsection

@section('content')
    @component('components.breadcrumb')
    @slot('li_1')
        Data Hasil Kerja
    @endslot
    @slot('li_3')
        @yield('title')
    @endslot
    @slot('title')
        @yield('title')
    @endslot
    @endcomponent

    @php
        if($id == 1){
            $kode_jenis_operator_detail = 'L';
        }
        elseif($id == 2){
            $kode_jenis_operator_detail = 'E';
        }
        elseif($id == 3){
            $kode_jenis_operator_detail = 'A';
        }

        $akhir_bulan = 'y';

        if ($new_data_pengerjaan->akhir_bulan == $akhir_bulan) {
            $colspan_plus = 6;
        }else{
            $colspan_plus = 4;
        }
    @endphp

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5>Kode ID : {{ $new_data_pengerjaan->kode_pengerjaan }}
                    @if ($new_data_pengerjaan->status == 'n')
                    <i class="far fa-check-circle text-success"></i>
                    @endif
                    </h5>
                </div>
                <div class="card-body">
                    <div class="table-container">
                        <table id="datatables" class="table table-sm table-bordered dt-responsive nowrap"
                            style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                            <thead>
                                <tr>
                                    <th rowspan="3" class="text-center">No</th>
                                    <th rowspan="3" class="text-center">NIK</th>
                                    <th rowspan="3" class="text-center">Nama Karyawan</th>
                                    <th colspan="{{ $count_tanggal }}" class="text-center">Tanggal</th>
                                    <th rowspan="3" class="text-center">Upah</th>
                                    <th colspan="{{ $colspan_plus }}" class="text-center">PLUS</th>
                                    <th rowspan="3" class="text-center">TG</th>
                                    <th colspan="4" class="text-center">POTONGAN</th>
                                    <th rowspan="3" class="text-center">DITERIMA</th>
                                </tr>
                                <tr>
                                    @foreach ($explode_tanggal_pengerjaans as $key => $explode_tanggal_pengerjaan)
                                    @if ($key != 0)
                                        @if ($new_data_pengerjaan->status == 'n')
                                        <th class="text-center">{{ \Carbon\Carbon::parse($explode_tanggal_pengerjaan)->isoFormat('D MMMM') }}</th>
                                        @else
                                        <th class="text-center"><a href="javascript:void()" onclick="window.open('{{ route('hasil_kerja.isiAmbri.view_hasil',['id' => $id, 'kode_pengerjaan' => $kode_pengerjaan, 'tanggal' => $explode_tanggal_pengerjaan]) }}','','width=1200,height=555,left=80,top=80,location=yes, menubar=no, status=no,toolbar=no, scrollbars=yes, resizable=yes')" class="text-primary">{{ \Carbon\Carbon::parse($explode_tanggal_pengerjaan)->isoFormat('D MMMM') }}</a></th>
                                        @endif
                                    @endif
                                    @endforeach
                                    <th rowspan="2" class="text-center">PLUS 1</th>
                                    <th rowspan="2" class="text-center">PLUS 2</th>
                                    <th rowspan="2" class="text-center">PLUS 3</th>
                                    <th rowspan="2" class="text-center">U Makan</th>
                                    @if ($new_data_pengerjaan->akhir_bulan == $akhir_bulan)
                                    <th rowspan="2" class="text-center">T Kerja</th>
                                    <th rowspan="2" class="text-center">Kehadiran</th>
                                    @endif
                                    <th rowspan="2" class="text-center">MIN 1</th>
                                    <th rowspan="2" class="text-center">MIN 2</th>
                                    <th rowspan="2" class="text-center">JHT</th>
                                    <th rowspan="2" class="text-center">BPJS</th>
                                </tr>
                                <tr>
                                    @foreach ($explode_tanggal_pengerjaans as $key => $explode_tanggal_pengerjaan)
                                    @if ($key != 0)
                                    <th style="font-size: 8pt" class="text-center">
                                        <table style="width: 100%">
                                            <tr>
                                                <th>Jenis</th>
                                                <th>Jumlah</th>
                                                <th>Jam</th>
                                            </tr>
                                        </table>
                                    </th>
                                    @endif
                                    @endforeach
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $nama_jenis_produk_1 = [];
                                    $nama_jenis_produk_2 = [];
                                    $nama_jenis_produk_3 = [];
                                    $nama_jenis_produk_4 = [];
                                    $nama_jenis_produk_5 = [];
    
                                    $total_all_upah = [];
                                    $total_all_plus_1 = [];
                                    $total_all_plus_2 = [];
                                    $total_all_plus_3 = [];
                                    $total_all_uang_makan = [];
                                    $total_all_tunjangan_kerja = [];
                                    $total_all_tunjangan_kehadiran = [];
                                    $total_all_tg = [];
                                    $total_all_minus_1 = [];
                                    $total_all_minus_2 = [];
                                    $total_all_jht = [];
                                    $total_all_bpjs_kesehatan = [];
                                    $total_all_gaji_diterima = [];
    
                                    $operator_karyawans = [];
                                @endphp
                                @foreach ($pengerjaans as $key => $pengerjaan)
                                <?php 
                                // dd($pengerjaan);
                                array_push($operator_karyawans,$pengerjaan->operator_karyawan_id);
                                    $hasil_pengerjaans = \App\Models\Pengerjaan::select([
                                                                                    'kode_payrol',
                                                                                    'tanggal_pengerjaan',
                                                                                    'operator_karyawan_id',
                                                                                    'hasil_kerja_1',
                                                                                    'hasil_kerja_2',
                                                                                    'hasil_kerja_3',
                                                                                    'hasil_kerja_4',
                                                                                    'hasil_kerja_5',
                                                                                    'total_jam_kerja_1',
                                                                                    'total_jam_kerja_2',
                                                                                    'total_jam_kerja_3',
                                                                                    'total_jam_kerja_4',
                                                                                    'total_jam_kerja_5',
                                                                                    'lembur'
                                                                                ])
                                                                                ->where('operator_karyawan_id',$pengerjaan->operator_karyawan_id)
                                                                                ->where('kode_payrol',substr($kode_pengerjaan,0,2).$kode_jenis_operator_detail.'_'.substr($kode_pengerjaan,3))
                                                                                ->orderBy('tanggal_pengerjaan','asc')
                                                                                ->get();
                                                                                // dd($hasil_pengerjaans);
                                    // dd($pengerjaan->operator_karyawan->jenis_operator_detail_pengerjaan);
                                    // dd($pengerjaan->pengerjaan);
                                    // dd('id = '.$pengerjaan->operator_karyawan->jenis_operator_detail_pengerjaan->id.','.' kode_pengerjaan = '.$kode_pengerjaan.' tanggal = '.$hasil_pengerjaans[1]['tanggal_pengerjaan']);
                                    $month = \Carbon\Carbon::now()->format('m');
                                    $year = \Carbon\Carbon::now()->format('Y');
                                ?>
                                    <tr>
                                        <td class="text-center">{{ $key+1 }}</td>
                                        <td class="text-center">{{ $pengerjaan->nik }}</td>
                                        {{-- <td><a href="{{ route('hasil_kerja.bandrolLokal.view_hasil_karyawan',['id' => $pengerjaan->operator_karyawan->jenis_operator_detail_pengerjaan->id, 'kode_pengerjaan' => $kode_pengerjaan, 'nik' => $pengerjaan->nik]) }}" class="text-primary">{{ $pengerjaan->nama }}</a></td> --}}
                                        @if ($new_data_pengerjaan->status == 'n')
                                        <td>{{ $pengerjaan->nama }}</td>
                                        @else
                                        <td><a href="javascript:void(0)" onclick="window.open('{{ route('hasil_kerja.isiAmbri.view_hasil_karyawan',['id' => $pengerjaan->operator_karyawan->jenis_operator_detail_pengerjaan->jenis_operator_detail_id, 'kode_pengerjaan' => $kode_pengerjaan, 'nik' => $pengerjaan->nik, 'month' => $month, 'year' => $year]) }}','','width=1200,height=555,left=80,top=80,location=yes, menubar=no, status=no,toolbar=no, scrollbars=yes, resizable=yes')" class="text-primary">{{ $pengerjaan->nama }}</a></td>
                                        @endif
                                        <?php 
                                            $upah = array();
                                        ?>
                                        @foreach ($hasil_pengerjaans as $hasil_pengerjaan)
                                        <?php 
                                            $explode_hasil_kerja_1 = explode("|",$hasil_pengerjaan->hasil_kerja_1);
                                            $umk_borongan_lokal_1 = \App\Models\UMKBoronganAmbri::select('id','jenis_produk','umk_etiket','umk_las_tepi','umk_las_pojok','umk_ambri')->where('id',$explode_hasil_kerja_1[0])->where('status','Y')->first();
                                            if(empty($umk_borongan_lokal_1)){
                                                $jenis_produk_1 = '-';
                                                $hasil_kerja_1 = null;
                                                $data_explode_hasil_kerja_1 = '-';
                                                $lembur_1 = 1;
                                                $icon_lembur_1 = null;
                                            }else{
                                                $jenis_produk_1 = $umk_borongan_lokal_1->jenis_produk;
                                                $hasil_kerja_1 = $explode_hasil_kerja_1[1]*$umk_borongan_lokal_1->umk_ambri;
                                                $data_explode_hasil_kerja_1 = $explode_hasil_kerja_1[1];
    
                                                $explode_lembur_1 = explode("|",$hasil_pengerjaan->lembur);
                                                $explode_status_lembur_1 = explode("-",$explode_lembur_1[1]);
    
                                                if($explode_status_lembur_1[1] == 'y'){
                                                    $lembur_1 = 1.5;
                                                    $icon_lembur_1 = "<span class='badge badge-soft-success'>L</span>";
                                                }else{
                                                    $lembur_1 = 1;
                                                    $icon_lembur_1 = null;
                                                }
                                                // dd($explode_status_lembur);
                                                // dd($hasil_pengerjaan->lembur);
                                            }
    
                                            $explode_hasil_kerja_2 = explode("|",$hasil_pengerjaan->hasil_kerja_2);
                                            $umk_borongan_lokal_2 = \App\Models\UMKBoronganAmbri::select('id','jenis_produk','umk_etiket','umk_las_tepi','umk_las_pojok','umk_ambri')->where('id',$explode_hasil_kerja_2[0])->where('status','Y')->first();
                                            if(empty($umk_borongan_lokal_2)){
                                                $jenis_produk_2 = '-';
                                                $hasil_kerja_2 = null;
                                                $data_explode_hasil_kerja_2 = '-';
                                                $lembur_2 = 1;
                                                $icon_lembur_2 = null;
                                            }else{
                                                $jenis_produk_2 = $umk_borongan_lokal_2->jenis_produk;
                                                $hasil_kerja_2 = $explode_hasil_kerja_2[1]*$umk_borongan_lokal_2->umk_ambri;
                                                $data_explode_hasil_kerja_2 = $explode_hasil_kerja_2[1];
    
                                                $explode_lembur_2 = explode("|",$hasil_pengerjaan->lembur);
                                                $explode_status_lembur_2 = explode("-",$explode_lembur_2[2]);
    
                                                if($explode_status_lembur_2[1] == 'y'){
                                                    $lembur_2 = 1.5;
                                                    $icon_lembur_2 = "<span class='badge badge-soft-success'>L</span>";
                                                }else{
                                                    $lembur_2 = 1;
                                                    $icon_lembur_2 = null;
                                                }
                                            }
    
                                            $explode_hasil_kerja_3 = explode("|",$hasil_pengerjaan->hasil_kerja_3);
                                            $umk_borongan_lokal_3 = \App\Models\UMKBoronganAmbri::select('id','jenis_produk','umk_etiket','umk_las_tepi','umk_las_pojok','umk_ambri')->where('id',$explode_hasil_kerja_3[0])->where('status','Y')->first();
                                            if(empty($umk_borongan_lokal_3)){
                                                $jenis_produk_3 = '-';
                                                $hasil_kerja_3 = null;
                                                $data_explode_hasil_kerja_3 = '-';
                                                $lembur_3 = 1;
                                                $icon_lembur_3 = null;
                                            }else{
                                                $jenis_produk_3 = $umk_borongan_lokal_3->jenis_produk;
                                                $hasil_kerja_3 = $explode_hasil_kerja_3[1]*$umk_borongan_lokal_3->umk_ambri;
                                                $data_explode_hasil_kerja_3 = $explode_hasil_kerja_3[1];
    
                                                $explode_lembur_3 = explode("|",$hasil_pengerjaan->lembur);
                                                $explode_status_lembur_3 = explode("-",$explode_lembur_3[3]);
    
                                                if($explode_status_lembur_3[1] == 'y'){
                                                    $lembur_3 = 1.5;
                                                    $icon_lembur_3 = "<span class='badge badge-soft-success'>L</span>";
                                                }else{
                                                    $lembur_3 = 1;
                                                    $icon_lembur_3 = null;
                                                }
                                            }
    
                                            $explode_hasil_kerja_4 = explode("|",$hasil_pengerjaan->hasil_kerja_4);
                                            $umk_borongan_lokal_4 = \App\Models\UMKBoronganAmbri::select('id','jenis_produk','umk_etiket','umk_las_tepi','umk_las_pojok','umk_ambri')->where('id',$explode_hasil_kerja_4[0])->where('status','Y')->first();
                                            if(empty($umk_borongan_lokal_4)){
                                                $jenis_produk_4 = '-';
                                                $hasil_kerja_4 = null;
                                                $data_explode_hasil_kerja_4 = '-';
                                                $lembur_4 = 1;
                                                $icon_lembur_4 = null;
                                            }else{
                                                $jenis_produk_4 = $umk_borongan_lokal_4->jenis_produk;
                                                $hasil_kerja_4 = $explode_hasil_kerja_4[1]*$umk_borongan_lokal_4->umk_ambri;
                                                $data_explode_hasil_kerja_4 = $explode_hasil_kerja_4[1];
    
                                                $explode_lembur_4 = explode("|",$hasil_pengerjaan->lembur);
                                                $explode_status_lembur_4 = explode("-",$explode_lembur_4[4]);
    
                                                if($explode_status_lembur_4[1] == 'y'){
                                                    $lembur_4 = 1.5;
                                                    $icon_lembur_4 = "<span class='badge badge-soft-success'>L</span>";
                                                }else{
                                                    $lembur_4 = 1;
                                                    $icon_lembur_4 = null;
                                                }
                                            }
    
                                            $explode_hasil_kerja_5 = explode("|",$hasil_pengerjaan->hasil_kerja_5);
                                            $umk_borongan_lokal_5 = \App\Models\UMKBoronganAmbri::select('id','jenis_produk','umk_etiket','umk_las_tepi','umk_las_pojok','umk_ambri')->where('id',$explode_hasil_kerja_5[0])->where('status','Y')->first();
                                            if(empty($umk_borongan_lokal_5)){
                                                $jenis_produk_5 = '-';
                                                $hasil_kerja_5 = null;
                                                $data_explode_hasil_kerja_5 = '-';
                                                $lembur_5 = 1;
                                                $icon_lembur_5 = null;
                                            }else{
                                                $jenis_produk_5 = $umk_borongan_lokal_5->jenis_produk;
                                                $hasil_kerja_5 = $explode_hasil_kerja_5[1]*$umk_borongan_lokal_5->umk_ambri;
                                                $data_explode_hasil_kerja_5 = $explode_hasil_kerja_5[1];
    
                                                $explode_lembur_5 = explode("|",$hasil_pengerjaan->lembur);
                                                $explode_status_lembur_5 = explode("-",$explode_lembur_5[5]);
    
                                                if($explode_status_lembur_5[1] == 'y'){
                                                    $lembur_5 = 1.5;
                                                    $icon_lembur_5 = "<span class='badge badge-soft-success'>L</span>";
                                                }else{
                                                    $lembur_5 = 1;
                                                    $icon_lembur_5 = null;
                                                }
                                            }
    
                                            $hasil_upah = round(($hasil_kerja_1*$lembur_1)+($hasil_kerja_2*$lembur_2)+($hasil_kerja_3*$lembur_3)+($hasil_kerja_4*$lembur_4)+($hasil_kerja_5*$lembur_5));
                                            // $hasil_upah = ($hasil_kerja_1*$lembur_1)+($hasil_kerja_2*$lembur_2)+($hasil_kerja_3*$lembur_3)+($hasil_kerja_4*$lembur_4)+($hasil_kerja_5*$lembur_5);
                                            // dd($hasil_upah);
                                            array_push($upah,$hasil_upah);
                                            // dd($upah);
                                            array_push($nama_jenis_produk_1,$jenis_produk_1);
                                            array_push($nama_jenis_produk_2,$jenis_produk_2);
                                            array_push($nama_jenis_produk_3,$jenis_produk_3);
                                            array_push($nama_jenis_produk_4,$jenis_produk_4);
                                            array_push($nama_jenis_produk_5,$jenis_produk_5);
                                        ?>
                                            <td>
                                                <table class="table table-bordered" style="width: 100%">
                                                    <tr>
                                                        <td style="font-size: 8pt; " class="text-danger">{{ $jenis_produk_1 }} {!! $icon_lembur_1 !!}</td>
                                                        <td style="font-size: 8pt; text-align: right" class="text-primary">{{ $data_explode_hasil_kerja_1 }}</td>
                                                        <td style="font-size: 8pt; text-align: right">{{ $hasil_pengerjaan->total_jam_kerja_1 }}</td>
                                                    </tr>
                                                    <tr>
                                                        <td style="font-size: 8pt; " class="text-danger">{{ $jenis_produk_2 }} {!! $icon_lembur_2 !!}</td>
                                                        <td style="font-size: 8pt; text-align: right" class="text-primary">{{ $data_explode_hasil_kerja_2 }}</td>
                                                        <td style="font-size: 8pt; text-align: right">{{ $hasil_pengerjaan->total_jam_kerja_2 }}</td>
                                                    </tr>
                                                    <tr>
                                                        <td style="font-size: 8pt; " class="text-danger">{{ $jenis_produk_3 }} {!! $icon_lembur_3 !!}</td>
                                                        <td style="font-size: 8pt; text-align: right" class="text-primary">{{ $data_explode_hasil_kerja_3 }}</td>
                                                        <td style="font-size: 8pt; text-align: right">{{ $hasil_pengerjaan->total_jam_kerja_3 }}</td>
                                                    </tr>
                                                    <tr>
                                                        <td style="font-size: 8pt; " class="text-danger">{{ $jenis_produk_4 }} {!! $icon_lembur_4 !!}</td>
                                                        <td style="font-size: 8pt; text-align: right" class="text-primary">{{ $data_explode_hasil_kerja_4 }}</td>
                                                        <td style="font-size: 8pt; text-align: right">{{ $hasil_pengerjaan->total_jam_kerja_4 }}</td>
                                                    </tr>
                                                    <tr>
                                                        <td style="font-size: 8pt; " class="text-danger">{{ $jenis_produk_5 }} {!! $icon_lembur_5 !!}</td>
                                                        <td style="font-size: 8pt; text-align: right" class="text-primary">{{ $data_explode_hasil_kerja_5 }}</td>
                                                        <td style="font-size: 8pt; text-align: right">{{ $hasil_pengerjaan->total_jam_kerja_5 }}</td>
                                                    </tr>
                                                </table>
                                                {{-- <span style="font-size: 8pt" class="text-danger">{{ $jenis_produk_1 }}</span> - <span style="font-size: 8pt" class="text-primary">{{ $data_explode_hasil_kerja_1 }}</span> |
                                                <span style="font-size: 8pt" class="text-danger">{{ $jenis_produk_2 }}</span> - <span style="font-size: 8pt" class="text-primary">{{ $data_explode_hasil_kerja_2 }}</span> |
                                                <span style="font-size: 8pt" class="text-danger">{{ $jenis_produk_3 }}</span> - <span style="font-size: 8pt" class="text-primary">{{ $data_explode_hasil_kerja_3 }}</span> |
                                                <span style="font-size: 8pt" class="text-danger">{{ $jenis_produk_4 }}</span> - <span style="font-size: 8pt" class="text-primary">{{ $data_explode_hasil_kerja_4 }}</span> |
                                                <span style="font-size: 8pt" class="text-danger">{{ $jenis_produk_5 }}</span> - <span style="font-size: 8pt" class="text-primary">{{ $data_explode_hasil_kerja_5 }}</span> --}}
                                            </td>
                                        @endforeach
                                        <?php 
                                            $total_upah = array_sum($upah);
                                            array_push($total_all_upah,$total_upah);
                                            // dd($total_all_upah);
    
                                            if(empty($pengerjaan->uang_makan)){
                                                $uang_makan = 0;
                                            }else{
                                                $uang_makan = $pengerjaan->uang_makan;
                                            }
                                            
                                            // if(empty($pengerjaan->tunjangan_kerja)){
                                            //     $tunjangan_kerja = 0;
                                            // }else{
                                            //     $tunjangan_kerja = number_format($pengerjaan->tunjangan_kerja,0,',','.');
                                            // }
                                            if(empty($pengerjaan->tunjangan_kerja)){
                                                $tunjangan_kerja = 0;
                                            }else{
                                                $tunjangan_kerja = $pengerjaan->tunjangan_kerja;
                                            }
    
                                            if(empty($pengerjaan->tunjangan_kehadiran)){
                                                $tunjangan_kehadiran = 0;
                                            }else{
                                                $tunjangan_kehadiran = $pengerjaan->tunjangan_kehadiran;
                                            }
    
                                            $explode_plus_1 = explode("|",$pengerjaan->plus_1);
                                            $explode_plus_2 = explode("|",$pengerjaan->plus_2);
                                            $explode_plus_3 = explode("|",$pengerjaan->plus_3);
    
                                            if($explode_plus_1[0] == ""){
                                                $plus_1 = 0;
                                            }else{
                                                $plus_1 = $explode_plus_1[0];
                                            }
    
                                            if($explode_plus_2[0] == ""){
                                                $plus_2 = 0;
                                            }else{
                                                $plus_2 = $explode_plus_2[0];
                                            }
    
                                            if($explode_plus_3[0] == ""){
                                                $plus_3 = 0;
                                            }else{
                                                $plus_3 = $explode_plus_3[0];
                                            }
    
                                            array_push($total_all_plus_1,$plus_1);
                                            array_push($total_all_plus_2,$plus_2);
                                            array_push($total_all_plus_3,$plus_3);
                                            array_push($total_all_uang_makan,$uang_makan);
                                            array_push($total_all_tunjangan_kerja,$tunjangan_kerja);
                                            array_push($total_all_tunjangan_kehadiran,$tunjangan_kehadiran);
    
                                            $explode_minus_1 = explode("|",$pengerjaan->minus_1);
                                            $explode_minus_2 = explode("|",$pengerjaan->minus_2);
    
                                            if($explode_minus_1[0] == ""){
                                                $minus_1 = 0;
                                            }else{
                                                $minus_1 = $explode_minus_1[0];
                                            }
    
                                            if($explode_minus_2[0] == ""){
                                                $minus_2 = 0;
                                            }else{
                                                $minus_2 = $explode_minus_2[0];
                                            }
    
                                            array_push($total_all_minus_1,$minus_1);
                                            array_push($total_all_minus_2,$minus_2);
    
                                            array_push($total_all_jht,$pengerjaan->jht);
                                            array_push($total_all_bpjs_kesehatan,$pengerjaan->bpjs_kesehatan);
                                        ?>
                                        {{-- <td style="text-align: right">{{ number_format($total_upah,0,',','.') }}</td> --}}
                                        <td style="text-align: right">{{ number_format($total_upah,0,',','.') }}</td>
                                        <td style="text-align: right">{{ number_format($plus_1,0,',','.') }}</td>
                                        <td style="text-align: right">{{ number_format($plus_2,0,',','.') }}</td>
                                        <td style="text-align: right">{{ number_format($plus_3,0,',','.') }}</td>
                                        <td style="text-align: right">{{ number_format($uang_makan,0,',','.') }}</td>
                                        @if ($new_data_pengerjaan->akhir_bulan == $akhir_bulan)
                                        <td style="text-align: right">{{ number_format($tunjangan_kerja,0,',','.') }}</td>
                                        <td style="text-align: right">{{ number_format($tunjangan_kehadiran,0,',','.') }}</td>
                                        @endif
                                        <?php 
                                            $total_hasil_tg = $total_upah+$plus_1+$plus_2+$plus_3+$uang_makan+$tunjangan_kerja+$tunjangan_kehadiran;
                                            array_push($total_all_tg,$total_hasil_tg);
                                        ?>
                                        <td style="text-align: right">{{ number_format($total_hasil_tg,0,',','.') }}</td>
                                        <?php 
                                            $total_gaji_diterima = $total_hasil_tg-($minus_1+$minus_2+$pengerjaan->jht+$pengerjaan->bpjs_kesehatan);
                                            array_push($total_all_gaji_diterima,round($total_gaji_diterima));
                                            // array_push($total_all_gaji_diterima,$total_gaji_diterima);
                                        ?>
                                        <td style="text-align: right">{{ number_format($minus_1,0,',','.') }}</td>
                                        <td style="text-align: right">{{ number_format($minus_2,0,',','.') }}</td>
                                        <td style="text-align: right">{{ number_format($pengerjaan->jht,0,',','.') }}</td>
                                        <td style="text-align: right">{{ number_format($pengerjaan->bpjs_kesehatan,0,',','.') }}</td>
                                        <td style="text-align: right">{{ number_format($total_gaji_diterima,0,',','.') }}</td>
                                        {{-- <?php 
                                            $total_hasil_tg = $total_upah+$pengerjaan->plus_1+$pengerjaan->plus_2+$pengerjaan->plus_3+$pengerjaan->uang_makan+$pengerjaan->tunjangan_kerja+$pengerjaan->tunjangan_kehadiran;
                                        ?>
                                        <td style="text-align: right">{{ number_format($total_hasil_tg,0,',','.') }}</td>
                                        <?php 
                                            $total_gaji_diterima = $total_hasil_tg-($minus_1+$minus_2+$pengerjaan->jht+$pengerjaan->bpjs_kesehatan);
                                        ?>
                                        <td style="text-align: right">{{ number_format($pengerjaan->minus_1,0,',','.') }}</td>
                                        <td style="text-align: right">{{ number_format($pengerjaan->minus_2,0,',','.') }}</td>
                                        <td style="text-align: right">{{ number_format($pengerjaan->jht,0,',','.') }}</td>
                                        <td style="text-align: right">{{ number_format($pengerjaan->bpjs_kesehatan,0,',','.') }}</td>
                                        <td style="text-align: right">{{ number_format($total_gaji_diterima,0,',','.') }}</td> --}}
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                @php
                                    // $total = [];
                                @endphp
                                <tr>
                                    <td colspan="3" style="text-align: center; font-weight: bold">TOTAL</td>
                                    @foreach ($explode_tanggal_pengerjaans as $key => $explode_tanggal_pengerjaan)
                                    @if ($key != 0)
                                    @php
                                        if($id == 1){
                                            $kode_jenis_operator_detail = 'L';
                                        }
                                        elseif($id == 2){
                                            $kode_jenis_operator_detail = 'E';
                                        }
                                        elseif($id == 3){
                                            $kode_jenis_operator_detail = 'A';
                                        }
                                        $list_jenis_umk_1 = [];
                                        $list_jenis_umk_2 = [];
                                        $list_jenis_umk_3 = [];
                                        $list_jenis_umk_4 = [];
                                        $list_jenis_umk_5 = [];
                                        $total_all_hasil_kerja_1 = [];
                                        $total_all_hasil_kerja_2 = [];
                                        $total_all_hasil_kerja_3 = [];
                                        $total_all_hasil_kerja_4 = [];
                                        $total_all_hasil_kerja_5 = [];
                                        $total_all_jam_kerja_1 = [];
                                        $total_all_jam_kerja_2 = [];
                                        $total_all_jam_kerja_3 = [];
                                        $total_all_jam_kerja_4 = [];
                                        $total_all_jam_kerja_5 = [];
    
                                        $hasil_pengerjaans = \App\Models\Pengerjaan::where('kode_pengerjaan',$kode_pengerjaan)
                                                                                ->where('kode_payrol',substr($kode_pengerjaan,0,2).$kode_jenis_operator_detail.'_'.substr($kode_pengerjaan,3))
                                                                                ->where('tanggal_pengerjaan',$explode_tanggal_pengerjaan)
                                                                                ->whereIn('operator_karyawan_id',$operator_karyawans)
                                                                                ->get();
                                                                                // dd($hasil_pengerjaans);
                                                                                // dd($operator_karyawans);
                                        foreach ($hasil_pengerjaans as $hp => $hasil_pengerjaan) {
                                            if (empty($hasil_pengerjaan['hasil_kerja_1'])) {
                                                $jenis_umk_1 = '-';
                                                $total_hasil_kerja_1 = '0';
                                                $total_jam_1 = '0';
                                            }else{
                                                $explode_total_hasil_kerja_1 = explode("|",$hasil_pengerjaan['hasil_kerja_1']);
                                                if ($explode_total_hasil_kerja_1[0]==0) {
                                                    $jenis_umk_1 = '-';
                                                    $total_hasil_kerja_1 = 0;
                                                    $total_jam_1 = 0;
                                                }else{
                                                    $list_umk_1 = \App\Models\UMKBoronganAmbri::where('id',$explode_total_hasil_kerja_1[0])->first();
                                                    $jenis_umk_1 = $list_umk_1->jenis_produk;
                                                    $total_hasil_kerja_1 = $explode_total_hasil_kerja_1[1];
                                                    $total_jam_1 = $hasil_pengerjaan->total_jam_kerja_1;
                                                }
                                            }
                                            array_push($list_jenis_umk_1,$jenis_umk_1);
                                            array_push($total_all_hasil_kerja_1,$total_hasil_kerja_1);
                                            array_push($total_all_jam_kerja_1,$total_jam_1);
                                            
                                            if (empty($hasil_pengerjaan['hasil_kerja_2'])) {
                                                $jenis_umk_2 = '-';
                                                $total_hasil_kerja_2 = '0';
                                                $total_jam_2 = '0';
                                            }else{
                                                $explode_total_hasil_kerja_2 = explode("|",$hasil_pengerjaan['hasil_kerja_2']);
                                                if ($explode_total_hasil_kerja_2[0]==0) {
                                                    $jenis_umk_2 = '-';
                                                    $total_hasil_kerja_2 = 0;
                                                    $total_jam_2 = 0;
                                                }else{
                                                    $list_umk_2 = \App\Models\UMKBoronganAmbri::where('id',$explode_total_hasil_kerja_2[0])->first();
                                                    $jenis_umk_2 = $list_umk_2->jenis_produk;
                                                    $total_hasil_kerja_2 = $explode_total_hasil_kerja_2[1];
                                                    $total_jam_2 = $hasil_pengerjaan->total_jam_kerja_2;
                                                }
                                            }
                                            array_push($list_jenis_umk_2,$jenis_umk_2);
                                            array_push($total_all_hasil_kerja_2,$total_hasil_kerja_2);
                                            array_push($total_all_jam_kerja_2,$total_jam_2);
    
                                            if (empty($hasil_pengerjaan['hasil_kerja_3'])) {
                                                $jenis_umk_3 = '-';
                                                $total_hasil_kerja_3 = '0';
                                                $total_jam_3 = '0';
                                            }else{
                                                $explode_total_hasil_kerja_3 = explode("|",$hasil_pengerjaan['hasil_kerja_3']);
                                                if ($explode_total_hasil_kerja_3[0]==0) {
                                                    $jenis_umk_3 = '-';
                                                    $total_hasil_kerja_3 = 0;
                                                    $total_jam_3 = 0;
                                                }else{
                                                    $list_umk_3 = \App\Models\UMKBoronganAmbri::where('id',$explode_total_hasil_kerja_3[0])->first();
                                                    $jenis_umk_3 = $list_umk_3->jenis_produk;
                                                    $total_hasil_kerja_3 = $explode_total_hasil_kerja_3[1];
                                                    $total_jam_3 = $hasil_pengerjaan->total_jam_kerja_3;
                                                }
                                            }
                                            array_push($list_jenis_umk_3,$jenis_umk_3);
                                            array_push($total_all_hasil_kerja_3,$total_hasil_kerja_3);
                                            array_push($total_all_jam_kerja_3,$total_jam_3);
    
                                            if (empty($hasil_pengerjaan['hasil_kerja_4'])) {
                                                $jenis_umk_4 = '-';
                                                $total_hasil_kerja_4 = '0';
                                                $total_jam_4 = '0';
                                            }else{
                                                $explode_total_hasil_kerja_4 = explode("|",$hasil_pengerjaan['hasil_kerja_4']);
                                                if ($explode_total_hasil_kerja_4[0]==0) {
                                                    $jenis_umk_4 = '-';
                                                    $total_hasil_kerja_4 = 0;
                                                    $total_jam_4 = 0;
                                                }else{
                                                    $list_umk_4 = \App\Models\UMKBoronganAmbri::where('id',$explode_total_hasil_kerja_4[0])->first();
                                                    $jenis_umk_4 = $list_umk_4->jenis_produk;
                                                    $total_hasil_kerja_4 = $explode_total_hasil_kerja_4[1];
                                                    $total_jam_4 = $hasil_pengerjaan->total_jam_kerja_4;
                                                }
                                            }
                                            array_push($list_jenis_umk_4,$jenis_umk_4);
                                            array_push($total_all_hasil_kerja_4,$total_hasil_kerja_4);
                                            array_push($total_all_jam_kerja_4,$total_jam_4);
    
                                            if (empty($hasil_pengerjaan['hasil_kerja_5'])) {
                                                $jenis_umk_5 = '-';
                                                $total_hasil_kerja_5 = '0';
                                                $total_jam_5 = '0';
                                            }else{
                                                $explode_total_hasil_kerja_5 = explode("|",$hasil_pengerjaan['hasil_kerja_5']);
                                                if ($explode_total_hasil_kerja_5[0]==0) {
                                                    $jenis_umk_5 = '-';
                                                    $total_hasil_kerja_5 = 0;
                                                    $total_jam_5 = 0;
                                                }else{
                                                    $list_umk_5 = \App\Models\UMKBoronganAmbri::where('id',$explode_total_hasil_kerja_5[0])->first();
                                                    $jenis_umk_5 = $list_umk_5->jenis_produk;
                                                    $total_hasil_kerja_5 = $explode_total_hasil_kerja_5[1];
                                                    $total_jam_5 = $hasil_pengerjaan->total_jam_kerja_5;
                                                }
                                            }
                                            array_push($list_jenis_umk_5,$jenis_umk_5);
                                            array_push($total_all_hasil_kerja_5,$total_hasil_kerja_5);
                                            array_push($total_all_jam_kerja_5,$total_jam_5);
                                        }
                                        if (empty($list_jenis_umk_1)) {
                                            $data_list_jenis_umk_1 = '-';
                                        }else{
                                            $data_list_jenis_umk_1 = $list_jenis_umk_1[0];
                                        }
    
                                        if (empty($list_jenis_umk_2)) {
                                            $data_list_jenis_umk_2 = '-';
                                        }else{
                                            $data_list_jenis_umk_2 = $list_jenis_umk_2[0];
                                        }
    
                                        if (empty($list_jenis_umk_3)) {
                                            $data_list_jenis_umk_3 = '-';
                                        }else{
                                            $data_list_jenis_umk_3 = $list_jenis_umk_3[0];
                                        }
    
                                        if (empty($list_jenis_umk_4)) {
                                            $data_list_jenis_umk_4 = '-';
                                        }else{
                                            $data_list_jenis_umk_4 = $list_jenis_umk_4[0];
                                        }
    
                                        if (empty($list_jenis_umk_5)) {
                                            $data_list_jenis_umk_5 = '-';
                                        }else{
                                            $data_list_jenis_umk_5 = $list_jenis_umk_5[0];
                                        }
                                    @endphp
                                        <td>
                                            <table class="table table-bordered" style="width: 100%">
                                                <tr>
                                                    <td style="font-size: 8pt; font-weight: bold">{{ $data_list_jenis_umk_1 }}</td>
                                                    <td style="text-align: right; font-weight: bold; font-size: 8pt">{{ array_sum($total_all_hasil_kerja_1) }}</td>
                                                    <td style="text-align: right; font-weight: bold; font-size: 8pt">{{ array_sum($total_all_jam_kerja_1) }}</td>
                                                </tr>
                                                <tr>
                                                    <td style="font-size: 8pt; font-weight: bold">{{ $data_list_jenis_umk_2 }}</td>
                                                    <td style="text-align: right; font-weight: bold; font-size: 8pt">{{ array_sum($total_all_hasil_kerja_2) }}</td>
                                                    <td style="text-align: right; font-weight: bold; font-size: 8pt">{{ array_sum($total_all_jam_kerja_2) }}</td>
                                                </tr>
                                                <tr>
                                                    <td style="font-size: 8pt; font-weight: bold">{{ $data_list_jenis_umk_3 }}</td>
                                                    <td style="text-align: right; font-weight: bold; font-size: 8pt">{{ array_sum($total_all_hasil_kerja_3) }}</td>
                                                    <td style="text-align: right; font-weight: bold; font-size: 8pt">{{ array_sum($total_all_jam_kerja_3) }}</td>
                                                </tr>
                                                <tr>
                                                    <td style="font-size: 8pt; font-weight: bold">{{ $data_list_jenis_umk_4 }}</td>
                                                    <td style="text-align: right; font-weight: bold; font-size: 8pt">{{ array_sum($total_all_hasil_kerja_4) }}</td>
                                                    <td style="text-align: right; font-weight: bold; font-size: 8pt">{{ array_sum($total_all_jam_kerja_4) }}</td>
                                                </tr>
                                                <tr>
                                                    <td style="font-size: 8pt; font-weight: bold">{{ $data_list_jenis_umk_5 }}</td>
                                                    <td style="text-align: right; font-weight: bold; font-size: 8pt">{{ array_sum($total_all_hasil_kerja_5) }}</td>
                                                    <td style="text-align: right; font-weight: bold; font-size: 8pt">{{ array_sum($total_all_jam_kerja_5) }}</td>
                                                </tr>
                                            </table>
                                        </td>
                                    @endif
                                    @endforeach
                                    <td style="text-align: right; font-weight: bold">
                                        {{ number_format(array_sum($total_all_upah),0,',','.') }}
                                    </td>
                                    <td style="text-align: right; font-weight: bold">
                                        {{ number_format(array_sum($total_all_plus_1),0,',','.') }}
                                    </td>
                                    <td style="text-align: right; font-weight: bold">
                                        {{ number_format(array_sum($total_all_plus_2),0,',','.') }}
                                    </td>
                                    <td style="text-align: right; font-weight: bold">
                                        {{ number_format(array_sum($total_all_plus_3),0,',','.') }}
                                    </td>
                                    <td style="text-align: right; font-weight: bold">
                                        {{ number_format(array_sum($total_all_uang_makan),0,',','.') }}
                                    </td>
                                    @if ($new_data_pengerjaan->akhir_bulan == $akhir_bulan)
                                    <td style="text-align: right; font-weight: bold">
                                        {{ number_format(array_sum($total_all_tunjangan_kerja),0,',','.') }}
                                    </td>
                                    <td style="text-align: right; font-weight: bold">
                                        {{ number_format(array_sum($total_all_tunjangan_kehadiran),0,',','.') }}
                                    </td>
                                    @endif
                                    <td style="text-align: right; font-weight: bold">
                                        {{ number_format(array_sum($total_all_tg),0,',','.') }}
                                    </td>
                                    <td style="text-align: right; font-weight: bold">
                                        {{ number_format(array_sum($total_all_minus_1),0,',','.') }}
                                    </td>
                                    <td style="text-align: right; font-weight: bold">
                                        {{ number_format(array_sum($total_all_minus_2),0,',','.') }}
                                    </td>
                                    <td style="text-align: right; font-weight: bold">
                                        {{ number_format(array_sum($total_all_jht),0,',','.') }}
                                    </td>
                                    <td style="text-align: right; font-weight: bold">
                                        {{ number_format(array_sum($total_all_bpjs_kesehatan),0,',','.') }}
                                    </td>
                                    <td style="text-align: right; font-weight: bold">
                                        {{ number_format(array_sum($total_all_gaji_diterima),0,',','.') }}
                                    </td>
                                    @php
                                        // dd($total_all_upah);
                                    @endphp
                                    {{-- <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td> --}}
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script src="{{ URL::asset('public/assets/plugins/datatables/jquery.dataTables.min.js') }}"></script>
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
    <script src="{{ URL::asset('public/assets/js/pages/jquery.datatable.init.js') }}"></script>
    <script src="{{ URL::asset('public/assets/js/iziToast.min.js') }}"></script>

    <script>
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
    </script>
@endsection
