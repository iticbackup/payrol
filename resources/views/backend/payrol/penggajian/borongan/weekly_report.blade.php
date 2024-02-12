<html>

<head>
    <title>Report Borongan Weekly</title>
    <style>
        html {
            font-family: Arial, Helvetica, sans-serif
        }

        table,
        td,
        th {
            border: 1px solid;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }
    </style>
</head>

<body>
    @php
        $total_nominal = [];
        $explode_tanggal_pengerjaans = explode('#',$new_data_pengerjaan['tanggal']);
        $exp_tanggals = array_filter($explode_tanggal_pengerjaans);
        $a = count($exp_tanggals);
        $exp_tgl_awal = explode('-', $exp_tanggals[1]);
        $exp_tgl_akhir = explode('-', $exp_tanggals[$a]);

        $total_keseluruhan_upah_dasar = [];
        $total_keseluruhan_tunjangan_kerja = [];
        $total_keseluruhan_tunjangan_kehadiran = [];
        $total_keseluruhan_uang_makan = [];
        $total_keseluruhan_plus = [];
        $total_keseluruhan_minus = [];
        $total_keseluruhan_pot_jht = [];
        $total_keseluruhan_pot_kesehatan = [];
        $total_keseluruhan_pot_pensiun = [];
        $total_keseluruhan_upah_diterimas = [];
    @endphp
    <p style="vertical-align: middle; margin-top: -2.5%">
        <img style="float: left" width="50" src="{{ public_path('itic/logo_itic.png') }}">
        <div>
            <b>Total Daftar Gaji Karyawan Borongan Lokal</b> <br>
            <b>Tanggal : {{ \Carbon\Carbon::parse($exp_tgl_awal[0] . '-' . $exp_tgl_awal[1] . '-' . $exp_tgl_awal[2])->isoFormat('D MMMM').' s/d '.\Carbon\Carbon::parse($exp_tgl_akhir[0] . '-' . $exp_tgl_akhir[1] . '-' . $exp_tgl_akhir[2])->isoFormat('D MMMM YYYY') }}</b>
        </div>
    </p>
    <br>
    <table>
        <tbody>
            <tr>
                <td style="font-size: 10pt; height: 5%; text-align: center; background-color: grey; font-weight: bold"><span style="color: white">NO</span></td>
                <td style="font-size: 10pt; height: 5%; text-align: center; background-color: grey; font-weight: bold"><span style="color: white">UNIT KERJA</span></td>
                <td style="font-size: 10pt; height: 5%; text-align: center; background-color: grey; font-weight: bold"><span style="color: white">UPAH <br>DASAR</span></td>
                <td style="font-size: 10pt; height: 5%; text-align: center; background-color: grey; font-weight: bold"><span style="color: white">TUNJ.KERJA</span></td>
                <td style="font-size: 10pt; height: 5%; text-align: center; background-color: grey; font-weight: bold"><span style="color: white">TUNJ. <br>KEHADIRAN</span></td>
                <td style="font-size: 10pt; height: 5%; text-align: center; background-color: grey; font-weight: bold"><span style="color: white">UANG <br>MAKAN</span></td>
                <td style="font-size: 10pt; height: 5%; text-align: center; background-color: grey; font-weight: bold"><span style="color: white">PLUS</span></td>
                <td style="font-size: 10pt; height: 5%; text-align: center; background-color: grey; font-weight: bold"><span style="color: white">MINUS</span></td>
                <td style="font-size: 10pt; height: 5%; text-align: center; background-color: grey; font-weight: bold"><span style="color: white">POT. <br>JHT</span></td>
                <td style="font-size: 10pt; height: 5%; text-align: center; background-color: grey; font-weight: bold"><span style="color: white">POT. <br>BPJS</span></td>
                <td style="font-size: 10pt; height: 5%; text-align: center; background-color: grey; font-weight: bold"><span style="color: white">POT. <br>PENSIUN</span></td>
                <td style="font-size: 10pt; height: 5%; text-align: center; background-color: grey; font-weight: bold"><span style="color: white">UPAH <br>DITERIMA</span></td>
            </tr>
            @foreach ($jenis_operator_detail_pengerjaans as $key => $jenis_operator_detail_pengerjaan)
            @php
                $total_upah_dasar = [];
                $total_tunjangan_kerja = [];
                $total_tunjangan_kehadiran = [];
                $total_uang_makan = [];
    
                $total_plus = [];
                $total_minus = [];
                $total_jht = [];
                $total_bpjs_kesehatan = [];
                $total_pensiun = [];
                $total_jumlah_upah_diterima = [];
    
                $total_upah_lembur = [];
    
                $operator_karyawans = \App\Models\KaryawanOperator::select([
                                                                    'pengerjaan_weekly.operator_karyawan_id as operator_karyawan_id',
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
                                                                ->leftJoin('pengerjaan_weekly','pengerjaan_weekly.operator_karyawan_id','=','operator_karyawan.id')
                                                                ->where('operator_karyawan.jenis_operator_detail_id',$jenis_operator_detail_pengerjaan->jenis_operator_detail_id)
                                                                ->where('operator_karyawan.jenis_operator_detail_pekerjaan_id',$jenis_operator_detail_pengerjaan->id)
                                                                ->get();
                foreach ($operator_karyawans as $keys => $operator_karyawan) {
                    $total_keseluruhan_upah_diterima = [];
                    $pengerjaans = \App\Models\Pengerjaan::where('kode_pengerjaan',$kode_pengerjaan)
                                                        ->where('operator_karyawan_id',$operator_karyawan->operator_karyawan_id)
                                                        ->get();
                    foreach ($pengerjaans as $pengerjaan) {
                        if ($pengerjaan->operator_karyawan->jenis_operator_detail_pekerjaan_id == 1) {
                            $explode_hasil_kerja_1 = explode("|",$pengerjaan->hasil_kerja_1);
                            $umk_borongan_lokal_1 = \App\Models\UMKBoronganLokal::select('id','jenis_produk','umk_packing','umk_bandrol','umk_inner','umk_outer')->where('id',$explode_hasil_kerja_1[0])->first();
                            if(empty($umk_borongan_lokal_1)){
                                $jenis_produk_1 = '-';
                                $hasil_kerja_1 = null;
                                $data_explode_hasil_kerja_1 = '-';
                                $lembur_1 = 1;
                                $data_lembur_1 = 0;
                            }else{
                                $jenis_produk_1 = $umk_borongan_lokal_1->jenis_produk;
                                $hasil_kerja_1 = $explode_hasil_kerja_1[1]*$umk_borongan_lokal_1->umk_packing;
    
                                $explode_lembur_1 = explode("|",$pengerjaan->lembur);
                                $explode_status_lembur_1 = explode("-",$explode_lembur_1[1]);
                                if($explode_status_lembur_1[1] == 'y'){
                                    $lembur_1 = 1.5;
                                    $hasil_lembur_1 = $hasil_kerja_1*1.5;
                                    $data_lembur_1 = $hasil_lembur_1;
                                }else{
                                    $lembur_1 = 1;
                                    $hasil_lembur_1 = $hasil_kerja_1*0;
                                    $data_lembur_1 = 0;
                                }
                            }
    
                            $explode_hasil_kerja_2 = explode("|",$pengerjaan->hasil_kerja_2);
                            $umk_borongan_lokal_2 = \App\Models\UMKBoronganLokal::select('id','jenis_produk','umk_packing','umk_bandrol','umk_inner','umk_outer')->where('id',$explode_hasil_kerja_2[0])->where('status','Y')->first();
                            if(empty($umk_borongan_lokal_2)){
                                $jenis_produk_2 = '-';
                                $hasil_kerja_2 = 0;
                                $data_explode_hasil_kerja_2 = '-';
                                $lembur_2 = 1;
                                $data_lembur_2 = 0;
                            }else{
                                $jenis_produk_2 = $umk_borongan_lokal_2->jenis_produk;
                                $hasil_kerja_2 = $explode_hasil_kerja_2[1]*$umk_borongan_lokal_2->umk_packing;
                                $data_explode_hasil_kerja_2 = $explode_hasil_kerja_2[1];
    
                                $explode_lembur_2 = explode("|",$pengerjaan->lembur);
                                $explode_status_lembur_2 = explode("-",$explode_lembur_2[2]);
                                if($explode_status_lembur_2[1] == 'y'){
                                    $lembur_2 = 1.5;
                                    $hasil_lembur_2 = $hasil_kerja_2*1.5;
                                    $data_lembur_2 = $hasil_lembur_2;
                                }else{
                                    $lembur_2 = 1;
                                    $hasil_lembur_2 = $hasil_kerja_2*0;
                                    $data_lembur_2 = 0;
                                }
                            }
    
                            $explode_hasil_kerja_3 = explode("|",$pengerjaan->hasil_kerja_3);
                            $umk_borongan_lokal_3 = \App\Models\UMKBoronganLokal::select('id','jenis_produk','umk_packing','umk_bandrol','umk_inner','umk_outer')->where('id',$explode_hasil_kerja_3[0])->where('status','Y')->first();
                            if(empty($umk_borongan_lokal_3)){
                                $jenis_produk_3 = '-';
                                $hasil_kerja_3 = 0;
                                $data_explode_hasil_kerja_3 = '-';
                                $lembur_3 = 1;
                                $data_lembur_3 = 0;
                            }else{
                                $jenis_produk_3 = $umk_borongan_lokal_3->jenis_produk;
                                $hasil_kerja_3 = $explode_hasil_kerja_3[1]*$umk_borongan_lokal_3->umk_packing;
                                $data_explode_hasil_kerja_3 = $explode_hasil_kerja_3[1];
    
                                $explode_lembur_3 = explode("|",$pengerjaan->lembur);
                                $explode_status_lembur_3 = explode("-",$explode_lembur_3[3]);
                                if($explode_status_lembur_3[1] == 'y'){
                                    $lembur_3 = 1.5;
                                    $hasil_lembur_3 = $hasil_kerja_3*1.5;
                                    $data_lembur_3 = $hasil_lembur_3;
                                }else{
                                    $lembur_3 = 1;
                                    $hasil_lembur_3 = $hasil_kerja_3*0;
                                    $data_lembur_3 = 0;
                                }
                            }
    
                            $explode_hasil_kerja_4 = explode("|",$pengerjaan->hasil_kerja_4);
                            $umk_borongan_lokal_4 = \App\Models\UMKBoronganLokal::select('id','jenis_produk','umk_packing','umk_bandrol','umk_inner','umk_outer')->where('id',$explode_hasil_kerja_4[0])->where('status','Y')->first();
                            if(empty($umk_borongan_lokal_4)){
                                $jenis_produk_4 = '-';
                                $hasil_kerja_4 = 0;
                                $data_explode_hasil_kerja_4 = '-';
                                $lembur_4 = 1;
                                $data_lembur_4 = 0;
                            }else{
                                $jenis_produk_4 = $umk_borongan_lokal_4->jenis_produk;
                                $hasil_kerja_4 = $explode_hasil_kerja_4[1]*$umk_borongan_lokal_4->umk_packing;
                                $data_explode_hasil_kerja_4 = $explode_hasil_kerja_4[1];
    
                                $explode_lembur_4 = explode("|",$pengerjaan->lembur);
                                $explode_status_lembur_4 = explode("-",$explode_lembur_4[4]);
                                if($explode_status_lembur_4[1] == 'y'){
                                    $lembur_4 = 1.5;
                                    $hasil_lembur_4 = $hasil_kerja_4*1.5;
                                    $data_lembur_4 = $hasil_lembur_4;
                                }else{
                                    $lembur_4 = 1;
                                    $hasil_lembur_4 = $hasil_kerja_4*0;
                                    $data_lembur_4 = 0;
                                }
                            }
    
                            $explode_hasil_kerja_5 = explode("|",$pengerjaan->hasil_kerja_5);
                            $umk_borongan_lokal_5 = \App\Models\UMKBoronganLokal::select('id','jenis_produk','umk_packing','umk_bandrol','umk_inner','umk_outer')->where('id',$explode_hasil_kerja_5[0])->where('status','Y')->first();
                            if(empty($umk_borongan_lokal_5)){
                                $jenis_produk_5 = '-';
                                $hasil_kerja_5 = 0;
                                $data_explode_hasil_kerja_5 = '-';
                                $lembur_5 = 1;
                                $data_lembur_5 = 0;
                            }else{
                                $jenis_produk_5 = $umk_borongan_lokal_5->jenis_produk;
                                $hasil_kerja_5 = $explode_hasil_kerja_5[1]*$umk_borongan_lokal_5->umk_packing;
                                $data_explode_hasil_kerja_5 = $explode_hasil_kerja_5[1];
    
                                $explode_lembur_5 = explode("|",$pengerjaan->lembur);
                                $explode_status_lembur_5 = explode("-",$explode_lembur_5[5]);
                                if($explode_status_lembur_5[1] == 'y'){
                                    $lembur_5 = 1.5;
                                    $hasil_lembur_5 = $hasil_kerja_5*1.5;
                                    $data_lembur_5 = $hasil_lembur_5;
                                }else{
                                    $lembur_5 = 1;
                                    $hasil_lembur_5 = $hasil_kerja_5*0;
                                    $data_lembur_5 = 0;
                                }
                            }
                        }
    
                        if ($pengerjaan->operator_karyawan->jenis_operator_detail_pekerjaan_id == 2) {
                            $explode_hasil_kerja_1 = explode("|",$pengerjaan->hasil_kerja_1);
                            $umk_borongan_lokal_1 = \App\Models\UMKBoronganLokal::select('id','jenis_produk','umk_packing','umk_bandrol','umk_inner','umk_outer')->where('id',$explode_hasil_kerja_1[0])->first();
                            if(empty($umk_borongan_lokal_1)){
                                $jenis_produk_1 = '-';
                                $hasil_kerja_1 = null;
                                $data_explode_hasil_kerja_1 = '-';
                                $lembur_1 = 1;
                                $data_lembur_1 = 0;
                            }else{
                                $jenis_produk_1 = $umk_borongan_lokal_1->jenis_produk;
                                $hasil_kerja_1 = $explode_hasil_kerja_1[1]*$umk_borongan_lokal_1->umk_bandrol;
    
                                $explode_lembur_1 = explode("|",$pengerjaan->lembur);
                                $explode_status_lembur_1 = explode("-",$explode_lembur_1[1]);
                                if($explode_status_lembur_1[1] == 'y'){
                                    $lembur_1 = 1.5;
                                    $hasil_lembur_1 = $hasil_kerja_1*1.5;
                                    $data_lembur_1 = $hasil_lembur_1;
                                }else{
                                    $lembur_1 = 1;
                                    $hasil_lembur_1 = $hasil_kerja_1*0;
                                    $data_lembur_1 = 0;
                                }
                            }
    
                            $explode_hasil_kerja_2 = explode("|",$pengerjaan->hasil_kerja_2);
                            $umk_borongan_lokal_2 = \App\Models\UMKBoronganLokal::select('id','jenis_produk','umk_packing','umk_bandrol','umk_inner','umk_outer')->where('id',$explode_hasil_kerja_2[0])->where('status','Y')->first();
                            if(empty($umk_borongan_lokal_2)){
                                $jenis_produk_2 = '-';
                                $hasil_kerja_2 = 0;
                                $data_explode_hasil_kerja_2 = '-';
                                $lembur_2 = 1;
                                $data_lembur_2 = 0;
                            }else{
                                $jenis_produk_2 = $umk_borongan_lokal_2->jenis_produk;
                                $hasil_kerja_2 = $explode_hasil_kerja_2[1]*$umk_borongan_lokal_2->umk_bandrol;
                                $data_explode_hasil_kerja_2 = $explode_hasil_kerja_2[1];
    
                                $explode_lembur_2 = explode("|",$pengerjaan->lembur);
                                $explode_status_lembur_2 = explode("-",$explode_lembur_2[2]);
                                if($explode_status_lembur_2[1] == 'y'){
                                    $lembur_2 = 1.5;
                                    $hasil_lembur_2 = $hasil_kerja_2*1.5;
                                    $data_lembur_2 = $hasil_lembur_2;
                                }else{
                                    $lembur_2 = 1;
                                    $hasil_lembur_2 = $hasil_kerja_2*0;
                                }
                            }
    
                            $explode_hasil_kerja_3 = explode("|",$pengerjaan->hasil_kerja_3);
                            $umk_borongan_lokal_3 = \App\Models\UMKBoronganLokal::select('id','jenis_produk','umk_packing','umk_bandrol','umk_inner','umk_outer')->where('id',$explode_hasil_kerja_3[0])->where('status','Y')->first();
                            if(empty($umk_borongan_lokal_3)){
                                $jenis_produk_3 = '-';
                                $hasil_kerja_3 = 0;
                                $data_explode_hasil_kerja_3 = '-';
                                $lembur_3 = 1;
                                $data_lembur_3 = 0;
                            }else{
                                $jenis_produk_3 = $umk_borongan_lokal_3->jenis_produk;
                                $hasil_kerja_3 = $explode_hasil_kerja_3[1]*$umk_borongan_lokal_3->umk_bandrol;
                                $data_explode_hasil_kerja_3 = $explode_hasil_kerja_3[1];
    
                                $explode_lembur_3 = explode("|",$pengerjaan->lembur);
                                $explode_status_lembur_3 = explode("-",$explode_lembur_3[3]);
                                if($explode_status_lembur_3[1] == 'y'){
                                    $lembur_3 = 1.5;
                                    $hasil_lembur_3 = $hasil_kerja_3*1.5;
                                    $data_lembur_3 = $hasil_lembur_3;
                                }else{
                                    $lembur_3 = 1;
                                    $hasil_lembur_3 = $hasil_kerja_3*0;
                                    $data_lembur_3 = 0;
                                }
                            }
    
                            $explode_hasil_kerja_4 = explode("|",$pengerjaan->hasil_kerja_4);
                            $umk_borongan_lokal_4 = \App\Models\UMKBoronganLokal::select('id','jenis_produk','umk_packing','umk_bandrol','umk_inner','umk_outer')->where('id',$explode_hasil_kerja_4[0])->where('status','Y')->first();
                            if(empty($umk_borongan_lokal_4)){
                                $jenis_produk_4 = '-';
                                $hasil_kerja_4 = 0;
                                $data_explode_hasil_kerja_4 = '-';
                                $lembur_4 = 1;
                                $data_lembur_4 = 0;
                            }else{
                                $jenis_produk_4 = $umk_borongan_lokal_4->jenis_produk;
                                $hasil_kerja_4 = $explode_hasil_kerja_4[1]*$umk_borongan_lokal_4->umk_bandrol;
                                $data_explode_hasil_kerja_4 = $explode_hasil_kerja_4[1];
    
                                $explode_lembur_4 = explode("|",$pengerjaan->lembur);
                                $explode_status_lembur_4 = explode("-",$explode_lembur_4[4]);
                                if($explode_status_lembur_4[1] == 'y'){
                                    $lembur_4 = 1.5;
                                    $hasil_lembur_4 = $hasil_kerja_4*1.5;
                                    $data_lembur_4 = $hasil_lembur_4;
                                }else{
                                    $lembur_4 = 1;
                                    $hasil_lembur_4 = $hasil_kerja_4*0;
                                    $data_lembur_4 = 0;
                                }
                            }
    
                            $explode_hasil_kerja_5 = explode("|",$pengerjaan->hasil_kerja_5);
                            $umk_borongan_lokal_5 = \App\Models\UMKBoronganLokal::select('id','jenis_produk','umk_packing','umk_bandrol','umk_inner','umk_outer')->where('id',$explode_hasil_kerja_5[0])->where('status','Y')->first();
                            if(empty($umk_borongan_lokal_5)){
                                $jenis_produk_5 = '-';
                                $hasil_kerja_5 = 0;
                                $data_explode_hasil_kerja_5 = '-';
                                $lembur_5 = 1;
                                $data_lembur_5 = 0;
                            }else{
                                $jenis_produk_5 = $umk_borongan_lokal_5->jenis_produk;
                                $hasil_kerja_5 = $explode_hasil_kerja_5[1]*$umk_borongan_lokal_5->umk_bandrol;
                                $data_explode_hasil_kerja_5 = $explode_hasil_kerja_5[1];
    
                                $explode_lembur_5 = explode("|",$pengerjaan->lembur);
                                $explode_status_lembur_5 = explode("-",$explode_lembur_5[5]);
                                if($explode_status_lembur_5[1] == 'y'){
                                    $lembur_5 = 1.5;
                                    $hasil_lembur_5 = $hasil_kerja_5*1.5;
                                    $data_lembur_5 = $hasil_lembur_5;
                                }else{
                                    $lembur_5 = 1;
                                    $hasil_lembur_5 = $hasil_kerja_5*0;
                                    $data_lembur_5 = 0;
                                }
                            }
                        }
    
                        if ($pengerjaan->operator_karyawan->jenis_operator_detail_pekerjaan_id == 3) {
                            $explode_hasil_kerja_1 = explode("|",$pengerjaan->hasil_kerja_1);
                            $umk_borongan_lokal_1 = \App\Models\UMKBoronganLokal::select('id','jenis_produk','umk_packing','umk_bandrol','umk_inner','umk_outer')->where('id',$explode_hasil_kerja_1[0])->first();
                            if(empty($umk_borongan_lokal_1)){
                                $jenis_produk_1 = '-';
                                $hasil_kerja_1 = null;
                                $data_explode_hasil_kerja_1 = '-';
                                $lembur_1 = 1;
                                $data_lembur_1 = 0;
                            }else{
                                $jenis_produk_1 = $umk_borongan_lokal_1->jenis_produk;
                                $hasil_kerja_1 = $explode_hasil_kerja_1[1]*$umk_borongan_lokal_1->umk_inner;
    
                                $explode_lembur_1 = explode("|",$pengerjaan->lembur);
                                $explode_status_lembur_1 = explode("-",$explode_lembur_1[1]);
                                if($explode_status_lembur_1[1] == 'y'){
                                    $lembur_1 = 1.5;
                                    $hasil_lembur_1 = $hasil_kerja_1*1.5;
                                    $data_lembur_1 = $hasil_lembur_1;
                                }else{
                                    $lembur_1 = 1;
                                    $hasil_lembur_1 = $hasil_kerja_1*0;
                                    $data_lembur_1 = 0;
                                }
                            }
    
                            $explode_hasil_kerja_2 = explode("|",$pengerjaan->hasil_kerja_2);
                            $umk_borongan_lokal_2 = \App\Models\UMKBoronganLokal::select('id','jenis_produk','umk_packing','umk_bandrol','umk_inner','umk_outer')->where('id',$explode_hasil_kerja_2[0])->where('status','Y')->first();
                            if(empty($umk_borongan_lokal_2)){
                                $jenis_produk_2 = '-';
                                $hasil_kerja_2 = 0;
                                $data_explode_hasil_kerja_2 = '-';
                                $lembur_2 = 1;
                                $data_lembur_2 = 0;
                            }else{
                                $jenis_produk_2 = $umk_borongan_lokal_2->jenis_produk;
                                $hasil_kerja_2 = $explode_hasil_kerja_2[1]*$umk_borongan_lokal_2->umk_inner;
                                $data_explode_hasil_kerja_2 = $explode_hasil_kerja_2[1];
    
                                $explode_lembur_2 = explode("|",$pengerjaan->lembur);
                                $explode_status_lembur_2 = explode("-",$explode_lembur_2[2]);
                                if($explode_status_lembur_2[1] == 'y'){
                                    $lembur_2 = 1.5;
                                    $hasil_lembur_2 = $hasil_kerja_2*1.5;
                                    $data_lembur_2 = $hasil_lembur_2;
                                }else{
                                    $lembur_2 = 1;
                                    $hasil_lembur_2 = $hasil_kerja_2*0;
                                    $data_lembur_2 = 0;
                                }
                            }
    
                            $explode_hasil_kerja_3 = explode("|",$pengerjaan->hasil_kerja_3);
                            $umk_borongan_lokal_3 = \App\Models\UMKBoronganLokal::select('id','jenis_produk','umk_packing','umk_bandrol','umk_inner','umk_outer')->where('id',$explode_hasil_kerja_3[0])->where('status','Y')->first();
                            if(empty($umk_borongan_lokal_3)){
                                $jenis_produk_3 = '-';
                                $hasil_kerja_3 = 0;
                                $data_explode_hasil_kerja_3 = '-';
                                $lembur_3 = 1;
                                $data_lembur_3 = 0;
                            }else{
                                $jenis_produk_3 = $umk_borongan_lokal_3->jenis_produk;
                                $hasil_kerja_3 = $explode_hasil_kerja_3[1]*$umk_borongan_lokal_3->umk_inner;
                                $data_explode_hasil_kerja_3 = $explode_hasil_kerja_3[1];
    
                                $explode_lembur_3 = explode("|",$pengerjaan->lembur);
                                $explode_status_lembur_3 = explode("-",$explode_lembur_3[3]);
                                if($explode_status_lembur_3[1] == 'y'){
                                    $lembur_3 = 1.5;
                                    $hasil_lembur_3 = $hasil_kerja_3*1.5;
                                    $data_lembur_3 = $hasil_lembur_3;
                                }else{
                                    $lembur_3 = 1;
                                    $hasil_lembur_3 = $hasil_kerja_3*0;
                                    $data_lembur_3 = 0;
                                }
                            }
    
                            $explode_hasil_kerja_4 = explode("|",$pengerjaan->hasil_kerja_4);
                            $umk_borongan_lokal_4 = \App\Models\UMKBoronganLokal::select('id','jenis_produk','umk_packing','umk_bandrol','umk_inner','umk_outer')->where('id',$explode_hasil_kerja_4[0])->where('status','Y')->first();
                            if(empty($umk_borongan_lokal_4)){
                                $jenis_produk_4 = '-';
                                $hasil_kerja_4 = 0;
                                $data_explode_hasil_kerja_4 = '-';
                                $lembur_4 = 1;
                                $data_lembur_4 = 0;
                            }else{
                                $jenis_produk_4 = $umk_borongan_lokal_4->jenis_produk;
                                $hasil_kerja_4 = $explode_hasil_kerja_4[1]*$umk_borongan_lokal_4->umk_inner;
                                $data_explode_hasil_kerja_4 = $explode_hasil_kerja_4[1];
    
                                $explode_lembur_4 = explode("|",$pengerjaan->lembur);
                                $explode_status_lembur_4 = explode("-",$explode_lembur_4[4]);
                                if($explode_status_lembur_4[1] == 'y'){
                                    $lembur_4 = 1.5;
                                    $hasil_lembur_4 = $hasil_kerja_4*1.5;
                                    $data_lembur_4 = 0;
                                }else{
                                    $lembur_4 = 1;
                                    $hasil_lembur_4 = $hasil_kerja_4*0;
                                    $data_lembur_4 = 0;
                                }
                            }
    
                            $explode_hasil_kerja_5 = explode("|",$pengerjaan->hasil_kerja_5);
                            $umk_borongan_lokal_5 = \App\Models\UMKBoronganLokal::select('id','jenis_produk','umk_packing','umk_bandrol','umk_inner','umk_outer')->where('id',$explode_hasil_kerja_5[0])->where('status','Y')->first();
                            if(empty($umk_borongan_lokal_5)){
                                $jenis_produk_5 = '-';
                                $hasil_kerja_5 = 0;
                                $data_explode_hasil_kerja_5 = '-';
                                $lembur_5 = 1;
                                $data_lembur_5 = 0;
                            }else{
                                $jenis_produk_5 = $umk_borongan_lokal_5->jenis_produk;
                                $hasil_kerja_5 = $explode_hasil_kerja_5[1]*$umk_borongan_lokal_5->umk_inner;
                                $data_explode_hasil_kerja_5 = $explode_hasil_kerja_5[1];
    
                                $explode_lembur_5 = explode("|",$pengerjaan->lembur);
                                $explode_status_lembur_5 = explode("-",$explode_lembur_5[5]);
                                if($explode_status_lembur_5[1] == 'y'){
                                    $lembur_5 = 1.5;
                                    $hasil_lembur_5 = $hasil_kerja_5*1.5;
                                    $data_lembur_5 = $hasil_lembur_5;
                                }else{
                                    $lembur_5 = 1;
                                    $hasil_lembur_5 = $hasil_kerja_5*0;
                                    $data_lembur_5 = 0;
                                }
                            }
                        }
    
                        if ($pengerjaan->operator_karyawan->jenis_operator_detail_pekerjaan_id == 4) {
                            $explode_hasil_kerja_1 = explode("|",$pengerjaan->hasil_kerja_1);
                            $umk_borongan_lokal_1 = \App\Models\UMKBoronganLokal::select('id','jenis_produk','umk_packing','umk_bandrol','umk_inner','umk_outer')->where('id',$explode_hasil_kerja_1[0])->first();
                            if(empty($umk_borongan_lokal_1)){
                                $jenis_produk_1 = '-';
                                $hasil_kerja_1 = null;
                                $data_explode_hasil_kerja_1 = '-';
                                $lembur_1 = 1;
                                $data_lembur_1 = 0;
                            }else{
                                $jenis_produk_1 = $umk_borongan_lokal_1->jenis_produk;
                                $hasil_kerja_1 = $explode_hasil_kerja_1[1]*$umk_borongan_lokal_1->umk_outer;
    
                                $explode_lembur_1 = explode("|",$pengerjaan->lembur);
                                $explode_status_lembur_1 = explode("-",$explode_lembur_1[1]);
                                if($explode_status_lembur_1[1] == 'y'){
                                    $lembur_1 = 1.5;
                                    $hasil_lembur_1 = $hasil_kerja_1*1.5;
                                    $data_lembur_1 = $hasil_lembur_1;
                                }else{
                                    $lembur_1 = 1;
                                    $hasil_lembur_1 = $hasil_kerja_1*0;
                                    $data_lembur_1 = 0;
                                }
                            }
    
                            $explode_hasil_kerja_2 = explode("|",$pengerjaan->hasil_kerja_2);
                            $umk_borongan_lokal_2 = \App\Models\UMKBoronganLokal::select('id','jenis_produk','umk_packing','umk_bandrol','umk_inner','umk_outer')->where('id',$explode_hasil_kerja_2[0])->where('status','Y')->first();
                            if(empty($umk_borongan_lokal_2)){
                                $jenis_produk_2 = '-';
                                $hasil_kerja_2 = 0;
                                $data_explode_hasil_kerja_2 = '-';
                                $lembur_2 = 1;
                                $data_lembur_2 = 0;
                            }else{
                                $jenis_produk_2 = $umk_borongan_lokal_2->jenis_produk;
                                $hasil_kerja_2 = $explode_hasil_kerja_2[1]*$umk_borongan_lokal_2->umk_outer;
                                $data_explode_hasil_kerja_2 = $explode_hasil_kerja_2[1];
    
                                $explode_lembur_2 = explode("|",$pengerjaan->lembur);
                                $explode_status_lembur_2 = explode("-",$explode_lembur_2[2]);
                                if($explode_status_lembur_2[1] == 'y'){
                                    $lembur_2 = 1.5;
                                    $hasil_lembur_2 = $hasil_kerja_2*1.5;
                                    $data_lembur_2 = $hasil_lembur_2;
                                }else{
                                    $lembur_2 = 1;
                                    $hasil_lembur_2 = $hasil_kerja_2*0;
                                    $data_lembur_2 = 0;
                                }
                            }
    
                            $explode_hasil_kerja_3 = explode("|",$pengerjaan->hasil_kerja_3);
                            $umk_borongan_lokal_3 = \App\Models\UMKBoronganLokal::select('id','jenis_produk','umk_packing','umk_bandrol','umk_inner','umk_outer')->where('id',$explode_hasil_kerja_3[0])->where('status','Y')->first();
                            if(empty($umk_borongan_lokal_3)){
                                $jenis_produk_3 = '-';
                                $hasil_kerja_3 = 0;
                                $data_explode_hasil_kerja_3 = '-';
                                $lembur_3 = 1;
                                $data_lembur_3 = 0;
                            }else{
                                $jenis_produk_3 = $umk_borongan_lokal_3->jenis_produk;
                                $hasil_kerja_3 = $explode_hasil_kerja_3[1]*$umk_borongan_lokal_3->umk_outer;
                                $data_explode_hasil_kerja_3 = $explode_hasil_kerja_3[1];
    
                                $explode_lembur_3 = explode("|",$pengerjaan->lembur);
                                $explode_status_lembur_3 = explode("-",$explode_lembur_3[3]);
                                if($explode_status_lembur_3[1] == 'y'){
                                    $lembur_3 = 1.5;
                                    $hasil_lembur_3 = $hasil_kerja_3*1.5;
                                    $data_lembur_3 = $hasil_lembur_3;
                                }else{
                                    $lembur_3 = 1;
                                    $hasil_lembur_3 = $hasil_kerja_3*0;
                                    $data_lembur_3 = 0;
                                }
                            }
    
                            $explode_hasil_kerja_4 = explode("|",$pengerjaan->hasil_kerja_4);
                            $umk_borongan_lokal_4 = \App\Models\UMKBoronganLokal::select('id','jenis_produk','umk_packing','umk_bandrol','umk_inner','umk_outer')->where('id',$explode_hasil_kerja_4[0])->where('status','Y')->first();
                            if(empty($umk_borongan_lokal_4)){
                                $jenis_produk_4 = '-';
                                $hasil_kerja_4 = 0;
                                $data_explode_hasil_kerja_4 = '-';
                                $lembur_4 = 1;
                                $data_lembur_4 = 0;
                            }else{
                                $jenis_produk_4 = $umk_borongan_lokal_4->jenis_produk;
                                $hasil_kerja_4 = $explode_hasil_kerja_4[1]*$umk_borongan_lokal_4->umk_outer;
                                $data_explode_hasil_kerja_4 = $explode_hasil_kerja_4[1];
    
                                $explode_lembur_4 = explode("|",$pengerjaan->lembur);
                                $explode_status_lembur_4 = explode("-",$explode_lembur_4[4]);
                                if($explode_status_lembur_4[1] == 'y'){
                                    $lembur_4 = 1.5;
                                    $hasil_lembur_4 = $hasil_kerja_4*1.5;
                                    $data_lembur_4 = $hasil_lembur_4;
                                }else{
                                    $lembur_4 = 1;
                                    $hasil_lembur_4 = $hasil_kerja_4*0;
                                    $data_lembur_4 = 0;
                                }
                            }
    
                            $explode_hasil_kerja_5 = explode("|",$pengerjaan->hasil_kerja_5);
                            $umk_borongan_lokal_5 = \App\Models\UMKBoronganLokal::select('id','jenis_produk','umk_packing','umk_bandrol','umk_inner','umk_outer')->where('id',$explode_hasil_kerja_5[0])->where('status','Y')->first();
                            if(empty($umk_borongan_lokal_5)){
                                $jenis_produk_5 = '-';
                                $hasil_kerja_5 = 0;
                                $data_explode_hasil_kerja_5 = '-';
                                $lembur_5 = 1;
                                $data_lembur_5 = 0;
                            }else{
                                $jenis_produk_5 = $umk_borongan_lokal_5->jenis_produk;
                                $hasil_kerja_5 = $explode_hasil_kerja_5[1]*$umk_borongan_lokal_5->umk_outer;
                                $data_explode_hasil_kerja_5 = $explode_hasil_kerja_5[1];
    
                                $explode_lembur_5 = explode("|",$pengerjaan->lembur);
                                $explode_status_lembur_5 = explode("-",$explode_lembur_5[5]);
                                if($explode_status_lembur_5[1] == 'y'){
                                    $lembur_5 = 1.5;
                                    $hasil_lembur_5 = $hasil_kerja_5*1.5;
                                    $data_lembur_5 = $hasil_lembur_5;
                                }else{
                                    $lembur_5 = 1;
                                    $hasil_lembur_5 = $hasil_kerja_5*0;
                                    $data_lembur_5 = 0;
                                }
                            }
                        }
    
                        // $total_all_upah_diterima = ($hasil_kerja_1-($hasil_kerja_1*$data_lembur_1))+
                        //                             ($hasil_kerja_2-($hasil_kerja_2*$data_lembur_2))+
                        //                             ($hasil_kerja_3-($hasil_kerja_3*$data_lembur_3))+
                        //                             ($hasil_kerja_4-($hasil_kerja_4*$data_lembur_4))+
                        //                             ($hasil_kerja_5-($hasil_kerja_2*$data_lembur_5))
                        //                             ;
                        // array_push($total_upah_lembur,$pengerjaan->uang_lembur);
                        // dd($total_upah_lembur);
                        $total_all_upah_diterima = $hasil_kerja_1+$hasil_kerja_2+$hasil_kerja_3+$hasil_kerja_4+$hasil_kerja_5+$pengerjaan->uang_lembur;
                        // $total_all_upah_diterima = ($hasil_kerja_1)+($hasil_kerja_2)+($hasil_kerja_3)+($hasil_kerja_4)+($hasil_kerja_5);
                        array_push($total_jumlah_upah_diterima,$total_all_upah_diterima);
                    }
                    // dd($pengerjaans);
                    array_push($total_upah_dasar,$operator_karyawan->upah_dasar);
                    array_push($total_tunjangan_kerja,$operator_karyawan->tunjangan_kerja);
                    array_push($total_tunjangan_kehadiran,$operator_karyawan->tunjangan_kehadiran);
                    array_push($total_uang_makan,$operator_karyawan->uang_makan);
    
                    if (empty($operator_karyawan->plus_1)) {
                        $plus_1 = 0;
                    }else{
                        $explode_plus_1 = explode("|",$operator_karyawan->plus_1);
                        if (empty($explode_plus_1[0])) {
                            $plus_1 = 0;
                        }else{
                            $plus_1 = $explode_plus_1[0];
                        }
                    }
    
                    if (empty($operator_karyawan->plus_2)) {
                        $plus_2 = 0;
                    }else{
                        $explode_plus_2 = explode("|",$operator_karyawan->plus_2);
                        if (empty($explode_plus_2[0])) {
                            $plus_2 = 0;
                        }else{
                            $plus_2 = $explode_plus_2[0];
                        }
                    }
    
                    if (empty($operator_karyawan->plus_3)) {
                        $plus_3 = 0;
                    }else{
                        $explode_plus_3 = explode("|",$operator_karyawan->plus_3);
                        if (empty($explode_plus_3[0])) {
                            $plus_3 = 0;
                        }else{
                            $plus_3 = $explode_plus_3[0];
                        }
                    }
                    
                    $total_all_plus = $plus_1+$plus_2+$plus_3;
                    array_push($total_plus,$total_all_plus);
    
                    if (empty($operator_karyawan->minus_1)) {
                        $minus_1 = 0;
                    }else{
                        $explode_minus_1 = explode("|",$operator_karyawan->minus_1);
                        if (empty($explode_minus_1[0])) {
                            $minus_1 = 0;
                        }else{
                            $minus_1 = $explode_minus_1[0];
                        }
                    }
    
                    if (empty($operator_karyawan->minus_2)) {
                        $minus_2 = 0;
                    }else{
                        $explode_minus_2 = explode("|",$operator_karyawan->minus_2);
                        if (empty($explode_minus_2[0])) {
                            $minus_2 = 0;
                        }else{
                            $minus_2 = $explode_minus_2[0];
                        }
                    }
    
                    $total_all_minus = $minus_1+$minus_2;
    
                    array_push($total_minus,$total_all_minus);
    
                    array_push($total_jht,$operator_karyawan->jht);
                    array_push($total_bpjs_kesehatan,$operator_karyawan->bpjs_kesehatan);
                    array_push($total_pensiun,0);
    
                    // $total_upah_diterima = (array_sum($total_jumlah_upah_diterima)
                    //                         // +$total_all_plus+
                    //                         // $operator_karyawan->tunjangan_kerja+$operator_karyawan->tunjangan_kerja+
                    //                         // $operator_karyawan->tunjangan_kehadiran
                    //                         )
                    //                         // -($total_all_minus-$operator_karyawan->jht-$operator_karyawan->bpjs_kesehatan-0)
                    //                         ;
                    $total_upah_diterima = (array_sum($total_jumlah_upah_diterima)+array_sum($total_plus)+array_sum($total_tunjangan_kerja)+array_sum($total_tunjangan_kehadiran)+array_sum($total_uang_makan))
                                            -
                                            (array_sum($total_minus)+array_sum($total_jht)+array_sum($total_bpjs_kesehatan)+array_sum($total_pensiun))
                                            ;
                    array_push($total_keseluruhan_upah_diterima,$total_upah_diterima);
                    // $pengerjaans = \App\Models\Pengerjaan::where('operator_karyawan_id',$operator_karyawan->operator_karyawan_id)
                    //                                     ->where('kode_pengerjaan',$kode_pengerjaan)
                    //                                     ->get();
    
                    // foreach ($pengerjaans as $key => $pengerjaan) {
                        
                    // }
                    // dd($pengerjaans);
                }

                array_push($total_keseluruhan_upah_dasar,array_sum($total_upah_dasar));
                array_push($total_keseluruhan_tunjangan_kerja,array_sum($total_tunjangan_kerja));
                array_push($total_keseluruhan_tunjangan_kehadiran,array_sum($total_tunjangan_kehadiran));
                array_push($total_keseluruhan_uang_makan,array_sum($total_uang_makan));
                array_push($total_keseluruhan_plus,array_sum($total_plus));
                array_push($total_keseluruhan_minus,array_sum($total_minus));
                array_push($total_keseluruhan_pot_jht,array_sum($total_jht));
                array_push($total_keseluruhan_pot_kesehatan,array_sum($total_bpjs_kesehatan));
                array_push($total_keseluruhan_pot_pensiun,$total_pensiun);
                array_push($total_keseluruhan_upah_diterimas,$total_upah_diterima);
                // dd($total_upah_diterima);
                // if($jenis_operator_detail_pengerjaan->id == 1){
                //     dd($operator_karyawan);
                // }
            @endphp
            <tr>
                <td style="font-size: 10pt; text-align: center">{{ $key+1 }}</td>
                <td style="font-size: 10pt">{{ $jenis_operator_detail_pengerjaan->jenis_posisi_pekerjaan }}</td>
                {{-- <td>{{ array_sum($total_upah_dasar) }}</td> --}}
                <td style="font-size: 10pt; text-align: right">Rp. {{ number_format(array_sum($total_upah_dasar),0,',','.') }}</td>
                <td style="font-size: 10pt; text-align: right">Rp. {{ number_format(array_sum($total_tunjangan_kerja),0,',','.') }}</td>
                <td style="font-size: 10pt; text-align: right">Rp. {{ number_format(array_sum($total_tunjangan_kehadiran),0,',','.') }}</td>
                <td style="font-size: 10pt; text-align: right">Rp. {{ number_format(array_sum($total_uang_makan),0,',','.') }}</td>
                <td style="font-size: 10pt; text-align: right">Rp. {{ number_format(array_sum($total_plus),0,',','.') }}</td>
                <td style="font-size: 10pt; text-align: right">Rp. {{ number_format(array_sum($total_minus),0,',','.') }}</td>
                <td style="font-size: 10pt; text-align: right">Rp. {{ number_format(array_sum($total_jht),0,',','.') }}</td>
                <td style="font-size: 10pt; text-align: right">Rp. {{ number_format(array_sum($total_bpjs_kesehatan),0,',','.') }}</td>
                <td style="font-size: 10pt; text-align: right">Rp. {{ number_format(array_sum($total_pensiun),0,',','.') }}</td>
                <td style="font-size: 10pt; text-align: right">Rp. {{ number_format(array_sum($total_keseluruhan_upah_diterima),0,',','.') }}</td>
            </tr>
            @endforeach
        </tbody>
        <tr>
            <td style="font-size: 10pt; text-align: center" colspan="2">TOTAL</td>
            <td style="font-size: 10pt; text-align: right">Rp. {{ number_format(array_sum($total_keseluruhan_upah_dasar),0,',','.') }}</td>
            <td style="font-size: 10pt; text-align: right">Rp. {{ number_format(array_sum($total_keseluruhan_tunjangan_kerja),0,',','.') }}</td>
            <td style="font-size: 10pt; text-align: right">Rp. {{ number_format(array_sum($total_keseluruhan_tunjangan_kehadiran),0,',','.') }}</td>
            <td style="font-size: 10pt; text-align: right">Rp. {{ number_format(array_sum($total_keseluruhan_uang_makan),0,',','.') }}</td>
            <td style="font-size: 10pt; text-align: right">Rp. {{ number_format(array_sum($total_keseluruhan_plus),0,',','.') }}</td>
            <td style="font-size: 10pt; text-align: right">Rp. {{ number_format(array_sum($total_keseluruhan_minus),0,',','.') }}</td>
            <td style="font-size: 10pt; text-align: right">Rp. {{ number_format(array_sum($total_keseluruhan_pot_jht),0,',','.') }}</td>
            <td style="font-size: 10pt; text-align: right">Rp. {{ number_format(array_sum($total_keseluruhan_pot_kesehatan),0,',','.') }}</td>
            <td style="font-size: 10pt; text-align: right">Rp. {{ number_format(array_sum($total_keseluruhan_pot_pensiun),0,',','.') }}</td>
            <td style="font-size: 10pt; text-align: right">Rp. {{ number_format(array_sum($total_keseluruhan_upah_diterimas),0,',','.') }}</td>
        </tr>
    </table>
    <br>

    <table style="width: 60%">
        <tr>
            <td style="text-align: center; font-size: 10pt">Dibuat Oleh</td>
            <td style="text-align: center; font-size: 10pt">Disetujui Oleh</td>
            <td colspan="2" style="text-align: center; font-size: 10pt">Diketahui Oleh</td>
        </tr>
        <tr>
            <td style="height: 10%"></td>
            <td style="height: 10%"></td>
            <td style="height: 10%"></td>
            <td style="height: 10%"></td>
        </tr>
        <tr>
            <td style="text-align: center; padding-left: 10%; padding-right: 10%; font-size: 10pt">Staff Payroll</td>
            <td style="text-align: center; padding-left: 10%; padding-right: 10%; font-size: 10pt">Manager HRD</td>
            <td style="text-align: center; padding-left: 10%; padding-right: 10%; font-size: 10pt">Kepala Bagian</td>
            <td style="text-align: center; padding-left: 10%; padding-right: 10%; font-size: 10pt">Manager Keuangan</td>
        </tr>
    </table>
</body>

</html>
