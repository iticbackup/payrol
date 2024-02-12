<style>
    table,
    td,
    th {
        border: 1px solid;
    }

    td {
        vertical-align: middle;
    }

    table {
        width: 100%;
        border-collapse: collapse;
    }
</style>
@php
    $akhir_bulan = 'y';
    $exp_tanggals = array_filter($explode_tanggal_pengerjaans);
    $a = count($exp_tanggals);
    $exp_tgl_awal = explode('-', $exp_tanggals[1]);
    $exp_tgl_akhir = explode('-', $exp_tanggals[$a]);
    
    // dd($exp_tgl_awal);
    // $hasil_pengerjaan = \App\Models\Pengerjaan::where()
    
@endphp
<p>Daftar Gaji Borongan PT. Indonesian Tobacco Tbk.</p>
<p>Produksi B: </p>
<p>Tanggal :</p>
<br>
<table>
    <thead>
        <tr>
            <td rowspan="2" style="text-align: center">No</td>
            <td rowspan="2" style="text-align: center">NIK</td>
            <td rowspan="2" style="text-align: center">Nama</td>
            @foreach ($exp_tanggals as $exp_tanggal)
            @for ($b=1;$b<=$a;$b++)
                <td colspan="4" style="text-align: center">{{ \Carbon\Carbon::parse($exp_tanggal)->isoFormat('D MMMM YYYY') }}</td>
            @endfor
            @endforeach
            {{-- @foreach ($exp_tanggals as $exp_tanggal)
                @for ($i=1;$i<=$a;$i++)
                    <td colspan="4" style="text-align: center">
                        {{ \Carbon\Carbon::parse($exp_tanggal)->isoFormat('D MMMM YYYY') }}</td>
                @endfor
            @endforeach --}}
            
            {{-- @foreach ($explode_tanggal_pengerjaans as $key => $explode_tanggal_pengerjaan)
            @if ($key != 0)
                <td colspan="4" style="text-align: center">{{ \Carbon\Carbon::parse($explode_tanggal_pengerjaan)->isoFormat('D MMMM YYYY') }}</td>
            @endif
            @endforeach --}}
            <td rowspan="2" style="text-align: center">Upah Hasil Kerja</td>
            <td colspan="7" style="text-align: center">PLUS</td>
            <td rowspan="2" style="text-align: center">Total Gaji</td>
            <td colspan="6" style="text-align: center">Potongan</td>
            <td rowspan="2" style="text-align: center">Upah Diterima</td>
        </tr>
        <tr>
            @foreach ($exp_tanggals as $exp_tanggal)
                @for ($j= 1;$j<=$a;$j++)
                    <td style="text-align: center">Jenis</td>
                    <td style="text-align: center">Hasil Kerja</td>
                    <td style="text-align: center">Total Nominal</td>
                    <td style="text-align: center">Total Jam</td>
                @endfor
            @endforeach
            <td style="text-align: center">Plus 1</td>
            <td style="text-align: center">Plus 2</td>
            <td style="text-align: center">Plus 3</td>
            <td style="text-align: center">Uang Makan</td>
            <td style="text-align: center">Tunj.Kerja</td>
            <td style="text-align: center">Kehadiran</td>
            <td style="text-align: center">Total Plus</td>
            <td style="text-align: center">BPJS Ketenagakerjaan</td>
            <td style="text-align: center">BPJS Kesehatan</td>
            <td style="text-align: center">Pensiun</td>
            <td style="text-align: center">Minus 1</td>
            <td style="text-align: center">Minus 2</td>
            <td style="text-align: center">Total Potongan</td>
        </tr>
    </thead>
    <tbody>
        @php
            $upah_hasil_kerja = array();

            $total_jumlah_kerja_1 = array();
            $total_nominal_kerja_1 = array();
            $total_jam_kerja_1 = array();
        @endphp
        @foreach ($pengerjaan_borongan_weeklys as $key => $pengerjaan_borongan_weekly)
        @php
            if($kode_id == 1){
                $kode_jenis_operator_detail = 'L';
            }
            elseif($kode_id == 2){
                $kode_jenis_operator_detail = 'E';
            }
            elseif($kode_id == 3){
                $kode_jenis_operator_detail = 'A';
            }
            // $jenis_operator_detail_pekerjaan = \App\Models\JenisOperatorDetailPengerjaan::where('id',$pengerjaan_borongan_weekly->operator_karyawan->jenis_operator_detail_pekerjaan_id)->first();
            // dd($jenis_operator_detail_pekerjaan);
            $hasil_pengerjaans = \App\Models\Pengerjaan::where('kode_payrol',substr($pengerjaan_borongan_weekly->kode_payrol,0,2).$kode_jenis_operator_detail.substr($pengerjaan_borongan_weekly->kode_payrol,3))
                                                    ->where('operator_karyawan_id',$pengerjaan_borongan_weekly->operator_karyawan_id)
                                                    ->get();
        @endphp
        <tr>
            <td style="text-align: center">{{ $key+1 }}</td>
            <td style="text-align: center">{{ $pengerjaan_borongan_weekly->nik }}</td>
            <td>{{ $pengerjaan_borongan_weekly->nama }}</td>
            @foreach ($hasil_pengerjaans as $keys => $hasil_pengerjaan)
            @for ($hs = 1; $hs <= $a; $hs++)
            @php
                $explode_hasil_kerja = explode("|",$hasil_pengerjaan['hasil_kerja_'.$hs]);
                // $explode_hasil_kerja = explode("|",$hasil_pengerjaan->hasil_kerja_.$hs);
                // echo json_encode($explode_hasil_kerja);
                $umk_borongan_lokal = \App\Models\UMKBoronganLokal::select('id','jenis_produk','umk_packing','umk_bandrol','umk_inner','umk_outer')->where('id',$explode_hasil_kerja[0])->first();
                // dd($umk_borongan_lokal);
                if (empty($umk_borongan_lokal)) {
                    $jenis_produk = '-';
                    $hasil_kerja = 0;
                    $data_explode_hasil_kerja = '-';
                    $lembur = 1;
                    $total_hasil_kerja = 0;
                    $total_jam = 0;
                }else{
                    if ($hasil_pengerjaan->operator_karyawan->jenis_operator_detail_pekerjaan_id == 1) {
                        $jenis_produk = $umk_borongan_lokal->jenis_produk;
                        $hasil_kerja = $explode_hasil_kerja[1];
                        $total_hasil_kerja = $explode_hasil_kerja[1]*$umk_borongan_lokal->umk_packing;
                        // dd($hasil_kerja);
                        // $total_hasil_kerja = $explode_hasil_kerja[1]*$umk_borongan_lokal->umk_packing;
                        $total_jam = $hasil_pengerjaan['total_jam_kerja_'.$hs];
    
                        // // $data_explode_hasil_kerja = $explode_hasil_kerja[1];
                        $explode_lembur = explode("|",$hasil_pengerjaan->lembur);
                        $explode_status_lembur = explode("-",$explode_lembur[$hs]);
                        // echo json_encode($hs);
                        if($explode_status_lembur[1] == 'y'){
                            $lembur = 1.5;
                        }else{
                            $lembur = 1;
                        }
                    }elseif($hasil_pengerjaan->operator_karyawan->jenis_operator_detail_pekerjaan_id == 2){
                        $jenis_produk = $umk_borongan_lokal->jenis_produk;
                        $hasil_kerja = $explode_hasil_kerja[1];
                        $total_hasil_kerja = $explode_hasil_kerja[1]*$umk_borongan_lokal->umk_bandrol;

                        $total_jam = $hasil_pengerjaan['total_jam_kerja_'.$hs];
    
                        $explode_lembur = explode("|",$hasil_pengerjaan->lembur);
                        $explode_status_lembur = explode("-",$explode_lembur[$hs]);

                        if($explode_status_lembur[1] == 'y'){
                            $lembur = 1.5;
                        }else{
                            $lembur = 1;
                        }
                    }elseif($hasil_pengerjaan->operator_karyawan->jenis_operator_detail_pekerjaan_id == 3){
                        $jenis_produk = $umk_borongan_lokal->jenis_produk;
                        $hasil_kerja = $explode_hasil_kerja[1];
                        $total_hasil_kerja = $explode_hasil_kerja[1]*$umk_borongan_lokal->umk_inner;

                        $total_jam = $hasil_pengerjaan['total_jam_kerja_'.$hs];
    
                        $explode_lembur = explode("|",$hasil_pengerjaan->lembur);
                        $explode_status_lembur = explode("-",$explode_lembur[$hs]);

                        if($explode_status_lembur[1] == 'y'){
                            $lembur = 1.5;
                        }else{
                            $lembur = 1;
                        }
                    }elseif($hasil_pengerjaan->operator_karyawan->jenis_operator_detail_pekerjaan_id == 4){
                        $jenis_produk = $umk_borongan_lokal->jenis_produk;
                        $hasil_kerja = $explode_hasil_kerja[1];
                        $total_hasil_kerja = $explode_hasil_kerja[1]*$umk_borongan_lokal->umk_outer;

                        $total_jam = $hasil_pengerjaan['total_jam_kerja_'.$hs];
    
                        $explode_lembur = explode("|",$hasil_pengerjaan->lembur);
                        $explode_status_lembur = explode("-",$explode_lembur[$hs]);

                        if($explode_status_lembur[1] == 'y'){
                            $lembur = 1.5;
                        }else{
                            $lembur = 1;
                        }
                    }
                }

                $hasil_upah = $total_hasil_kerja*$lembur;
                // dd($hasil_upah)
                array_push($upah_hasil_kerja, $hasil_upah);
                // array_push($upah_hasil_kerja_1, $hasil_upah);
            @endphp
            <td style="text-align: center">{{ $jenis_produk }}</td>
            <td style="text-align: right">{{ $hasil_kerja }}</td>
            <td style="text-align: right">{{ 'Rp.'.number_format($total_hasil_kerja,0,',','.') }}</td>
            <td style="text-align: center">{{ $total_jam }}</td>
            @endfor
            @endforeach
            @php
                $hasil_total_upah = array_sum($upah_hasil_kerja);
                // echo json_encode($upah_hasil_kerja);
                if (empty($pengerjaan_borongan_weekly->uang_makan)) {
                    $uang_makan = 0;
                }else{
                    $uang_makan = $pengerjaan_borongan_weekly->uang_makan;
                }

                if(empty($pengerjaan_borongan_weekly->tunjangan_kerja)){
                    $tunjangan_kerja = 0;
                }else{
                    $tunjangan_kerja = number_format($pengerjaan_borongan_weekly->tunjangan_kerja,0,',','.');
                }

                if(empty($pengerjaan_borongan_weekly->tunjangan_kehadiran)){
                    $tunjangan_kehadiran = 0;
                }else{
                    $tunjangan_kehadiran = number_format($pengerjaan_borongan_weekly->tunjangan_kehadiran,0,',','.');
                }

                $explode_plus_1 = explode("|",$pengerjaan_borongan_weekly->plus_1);
                $explode_plus_2 = explode("|",$pengerjaan_borongan_weekly->plus_2);
                $explode_plus_3 = explode("|",$pengerjaan_borongan_weekly->plus_3);

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

                $total_plus = $plus_1+$plus_2+$plus_3+$uang_makan+$tunjangan_kerja+$tunjangan_kehadiran;

                $total_gaji = $hasil_total_upah+$total_plus;

                $explode_minus_1 = explode("|",$pengerjaan_borongan_weekly->minus_1);
                $explode_minus_2 = explode("|",$pengerjaan_borongan_weekly->minus_2);

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

                if(empty($pengerjaan_borongan_weekly->jht)){
                    $jht = 0;
                }else{
                    $jht = $pengerjaan_borongan_weekly->jht;
                }

                if(empty($pengerjaan_borongan_weekly->bpjs_kesehatan)){
                    $bpjs_kesehatan = 0;
                }else{
                    $bpjs_kesehatan = $pengerjaan_borongan_weekly->bpjs_kesehatan;
                }

                $total_potongan = $bpjs_kesehatan+$jht+$minus_1+$minus_2;

            @endphp
            <td style="text-align: right">{{ 'Rp. '.number_format($hasil_total_upah,0,',','.') }}</td>
            <td style="text-align: right">{{ 'Rp. '.number_format($plus_1,0,',','.') }}</td>
            <td style="text-align: right">{{ 'Rp. '.number_format($plus_2,0,',','.') }}</td>
            <td style="text-align: right">{{ 'Rp. '.number_format($plus_3,0,',','.') }}</td>
            <td style="text-align: right">{{ 'Rp. '.number_format($uang_makan,0,',','.') }}</td>
            <td style="text-align: right">{{ 'Rp. '.number_format($tunjangan_kehadiran,0,',','.') }}</td>
            <td style="text-align: right">{{ 'Rp. '.number_format($tunjangan_kerja,0,',','.') }}</td>
            <td style="text-align: right">{{ 'Rp. '.number_format($total_plus,0,',','.') }}</td>
            <td style="text-align: right">{{ 'Rp. '.number_format($total_gaji,0,',','.') }}</td>
            <td style="text-align: right">{{ 'Rp. '.number_format($jht,0,',','.') }}</td>
            <td style="text-align: right">{{ 'Rp. '.number_format($bpjs_kesehatan,0,',','.') }}</td>
            <td style="text-align: right">0</td>
            <td style="text-align: right">{{ 'Rp. '.number_format($minus_1,0,',','.') }}</td>
            <td style="text-align: right">{{ 'Rp. '.number_format($minus_2,0,',','.') }}</td>
            <td style="text-align: right">{{ 'Rp. '.number_format($total_potongan,0,',','.') }}</td>
            <td style="text-align: right">+</td>
        </tr>
        @endforeach
    </tbody>
</table>
