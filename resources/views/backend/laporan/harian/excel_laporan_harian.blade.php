<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <style>
        html{
            font-family: Arial, Helvetica, sans-serif;
        }
        td {
            vertical-align: middle;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
    </style>
</head>
<body>
    @php
        $akhir_bulan = 'y';
        $exp_tanggals = array_filter($explode_tanggal_pengerjaans);
        // dd($exp_tanggals);
        // $exp_tanggals = array_filter($explode_tanggal_pengerjaans);
        $a = count($exp_tanggals);
        // dd($a);
        $exp_tgl_awal = explode('-', $exp_tanggals[1]);
        $exp_tgl_akhir = explode('-', $exp_tanggals[$a]);

        $total_hasil_kerja = [];

        $total_hari_kerja = [];
        $total_upah_dasar = [];
        $total_gaji_pokok = [];
        $total_plus_1 = [];
        $total_plus_2 = [];
        $total_plus_3 = [];
        $total_uang_makan = [];
        $total_tunjangan_kerja = [];
        $total_tunjangan_kehadiran = [];
        $total_all_plus = [];

        $total_lembur_ke_1 = [];
        $total_lembur_ke_2 = [];
        $total_lembur = [];
        $total_all_gaji = [];

        $total_jht = [];
        $total_bpjs_kesehatan = [];
        $total_pensiun = [];
        $total_minus_1 = [];
        $total_minus_2 = [];
        $total_all_potongan = [];
        $total_gaji_diterima = [];
    @endphp

    <table>
        <thead>
            <tr >
                <td colspan="3">Daftar Gaji Harian PT. Indonesian Tobacco Tbk.</td>
            </tr>
            <tr>
                <td colspan="3">Harian: {{ $jenis_operator_detail_pengerjaan->jenis_posisi_pekerjaan }}</td>
            </tr>
            <tr>
                <td colspan="3">Tanggal :
                    {{ \Carbon\Carbon::parse($exp_tgl_awal[0] . '-' . $exp_tgl_awal[1] . '-' . $exp_tgl_awal[2])->isoFormat('D MMMM') }}
                    {{-- {{ \Carbon\Carbon::parse($exp_tgl_awal[0] . '-' . $exp_tgl_awal[1] . '-' . $exp_tgl_awal[2])->isoFormat('D MMMM') }} --}}
                    -
                    {{ \Carbon\Carbon::parse($exp_tgl_akhir[0] . '-' . $exp_tgl_akhir[1] . '-' . $exp_tgl_akhir[2])->isoFormat('D MMMM YYYY') }}
                    {{-- {{ \Carbon\Carbon::parse($exp_tgl_akhir[0] . '-' . $exp_tgl_akhir[1] . '-' . $exp_tgl_akhir[2])->isoFormat('D MMMM YYYY') }} --}}
                </td>
            </tr>
            <tr>
                <td></td>
            </tr>
            <tr>
                <td></td>
            </tr>
            <tr>
                <td rowspan="2" style="text-align: center; border: 1px solid black; background-color: #EEEEEE; font-weight: bold">NO</td>
                <td rowspan="2" style="text-align: center; border: 1px solid black; background-color: #EEEEEE; font-weight: bold">NIK</td>
                <td rowspan="2" style="text-align: center; border: 1px solid black; background-color: #EEEEEE; font-weight: bold">NAMA</td>
                @foreach ($exp_tanggals as $exp_tanggal)
                <td rowspan="2" style="text-align: center; border: 1px solid black; background-color: #EEEEEE; font-weight: bold">{{ \Carbon\Carbon::parse($exp_tanggal)->format('d M') }}</td>
                {{-- <td rowspan="2" style="text-align: center; border: 1px solid black; background-color: #EEEEEE; font-weight: bold">{{ \Carbon\Carbon::parse($exp_tanggal)->isoFormat('D MMMM') }}</td> --}}
                @endforeach
                <td rowspan="2" style="text-align: center; border: 1px solid black; background-color: #EEEEEE; font-weight: bold">Tot. Hari</td>
                <td rowspan="2" style="text-align: center; border: 1px solid black; background-color: #EEEEEE; font-weight: bold">Upah</td>
                <td rowspan="2" style="text-align: center; border: 1px solid black; background-color: #EEEEEE; font-weight: bold">Gaji Pokok</td>
                @if ($new_data_pengerjaan->akhir_bulan == $akhir_bulan)
                <td colspan="7" style="text-align: center; border: 1px solid black; background-color: #EEEEEE; font-weight: bold">PLUS</td>
                @else
                <td colspan="5" style="text-align: center; border: 1px solid black; background-color: #EEEEEE; font-weight: bold">PLUS</td>
                @endif
                <td colspan="3" style="text-align: center; border: 1px solid black; background-color: #EEEEEE; font-weight: bold">Lembur</td>
                <td rowspan="2" style="text-align: center; border: 1px solid black; background-color: #EEEEEE; font-weight: bold">Total Gaji</td>
                <td colspan="5" style="text-align: center; border: 1px solid black; background-color: #EEEEEE; font-weight: bold">Potongan</td>
                <td rowspan="2" style="text-align: center; border: 1px solid black; background-color: #EEEEEE; font-weight: bold">Diterima</td>
            </tr>
            <tr>
                <td style="text-align: center; border: 1px solid black; background-color: #EEEEEE; font-weight: bold">Plus 1</td>
                <td style="text-align: center; border: 1px solid black; background-color: #EEEEEE; font-weight: bold">Plus 2</td>
                <td style="text-align: center; border: 1px solid black; background-color: #EEEEEE; font-weight: bold">Plus 3</td>
                <td style="text-align: center; border: 1px solid black; background-color: #EEEEEE; font-weight: bold">Uang Makan</td>
                @if ($new_data_pengerjaan->akhir_bulan == $akhir_bulan)
                <td style="text-align: center; border: 1px solid black; background-color: #EEEEEE; font-weight: bold">Tunj. Kerja</td>
                <td style="text-align: center; border: 1px solid black; background-color: #EEEEEE; font-weight: bold">Tunj. Kehadiran</td>
                @endif
                <td style="text-align: center; border: 1px solid black; background-color: #EEEEEE; font-weight: bold">Total Plus</td>
                <td style="text-align: center; border: 1px solid black; background-color: #EEEEEE; font-weight: bold">Jam I</td>
                <td style="text-align: center; border: 1px solid black; background-color: #EEEEEE; font-weight: bold">Jam II</td>
                <td style="text-align: center; border: 1px solid black; background-color: #EEEEEE; font-weight: bold">Rp. Lembur</td>
                <td style="text-align: center; border: 1px solid black; background-color: #EEEEEE; font-weight: bold">Ketenagakerjaan</td>
                <td style="text-align: center; border: 1px solid black; background-color: #EEEEEE; font-weight: bold">BPJS Kesehatan</td>
                {{-- <td style="text-align: center; border: 1px solid black; background-color: #EEEEEE; font-weight: bold">Pensiun</td> --}}
                <td style="text-align: center; border: 1px solid black; background-color: #EEEEEE; font-weight: bold">Minus 1</td>
                <td style="text-align: center; border: 1px solid black; background-color: #EEEEEE; font-weight: bold">Minus 2</td>
                <td style="text-align: center; border: 1px solid black; background-color: #EEEEEE; font-weight: bold">Total Potongan</td>
            </tr>
        </thead>
        <tbody>
            @php
                $operator_karyawan_id = [];
                $cutOff = $cut_off->select('id','periode','tanggal')->where('periode',explode('_',$kode_pengerjaan)[1])->first();
                // dd($tahun_pengerjaan);
            @endphp
            @foreach ($pengerjaan_harians as $ph=> $pengerjaan_harian)
            @php
                array_push($operator_karyawan_id,$pengerjaan_harian->operator_harian_karyawan_id);
                if(empty($pengerjaan_harian->hasil_kerja)){
                    $hasil_kerjas = [];
                }else{
                    $explode_hasil_kerja = explode("|",$pengerjaan_harian->hasil_kerja);
                    $hasil_kerjas = $explode_hasil_kerja;
                    // dd(count($explode_hasil_kerja));
                }
            @endphp
                <tr>
                    <td style="text-align: center; border: 1px solid black;">{{ $ph+1 }}</td>
                    <td style="text-align: center; border: 1px solid black;">{{ $pengerjaan_harian->nik }}</td>
                    <td style="text-align: left; border: 1px solid black;">{{ $pengerjaan_harian->nama }}</td>
                    @foreach ($hasil_kerjas as $hasil_kerja)
                    @if ($hasil_kerja != null)
                    @php
                        array_push($total_hasil_kerja,$hasil_kerja);
                        // dd(array_push($total_hasil_kerja,$hasil_kerja));
                    @endphp
                    <td style="text-align: center; border: 1px solid black;">{{ $hasil_kerja }}</td>
                    @endif
                    @endforeach
                    @php
                        if (empty($pengerjaan_harian->plus_1)) {
                            $plus_1 = 0;
                            $keterangan_1 = null;
                        }else{
                            $explode_plus_1 = explode("|",$pengerjaan_harian->plus_1);
                            if ($explode_plus_1[0] == 0) {
                                $plus_1 = 0;
                                $keterangan_1 = null;
                            }else{
                                $plus_1 = $explode_plus_1[0];
                                $keterangan_1 = $explode_plus_1[1];
                            }
                        }

                        if (empty($pengerjaan_harian->plus_2)) {
                            $plus_2 = 0;
                            $keterangan_2 = null;
                        }else{
                            $explode_plus_2 = explode("|",$pengerjaan_harian->plus_2);
                            if ($explode_plus_2[0] == null || $explode_plus_2[0] == 0) {
                                $plus_2 = 0;
                                $keterangan_2 = null;
                            }else{
                                $plus_2 = $explode_plus_2[0];
                                $keterangan_2 = $explode_plus_2[1];
                            }
                        }

                        if (empty($pengerjaan_harian->plus_3)) {
                            $plus_3 = 0;
                            $keterangan_3 = null;
                        }else{
                            $explode_plus_3 = explode("|",$pengerjaan_harian->plus_3);
                            if ($explode_plus_3[0] == 0) {
                                $plus_3 = 0;
                                $keterangan_3 = null;
                            }else{
                                $plus_3 = $explode_plus_3[0];
                                $keterangan_3 = $explode_plus_3[1];
                            }
                        }

                        if (empty($pengerjaan_harian->uang_makan)) {
                            $uang_makan = 0;
                        }else{
                            $uang_makan = $pengerjaan_harian->uang_makan;
                        }

                        if ($new_data_pengerjaan->akhir_bulan == $akhir_bulan) {
                            if (empty($pengerjaan_harian->tunjangan_kerja)) {
                                $tunjangan_kerja = 0;
                            }else{
                                $tunjangan_kerja = $pengerjaan_harian->tunjangan_kerja;
                            }

                            if (empty($pengerjaan_harian->tunjangan_kehadiran)) {
                                $tunjangan_kehadiran = 0;
                            }else{
                                $tunjangan_kehadiran = $pengerjaan_harian->tunjangan_kehadiran;
                            }

                            $total_plus = (int)$plus_1+(int)$plus_2+(int)$plus_3+$uang_makan+$tunjangan_kerja+$tunjangan_kehadiran;
                        }else{
                            $total_plus = (int)$plus_1+(int)$plus_2+(int)$plus_3+$uang_makan;
                            // dd((int)$plus_3);
                        }

                        if (empty($pengerjaan_harian->lembur)) {
                            $lembur = 0;
                            $lembur_ke_1 = 0;
                            $lembur_ke_2 = 0;
                        }else{
                            $explode_lembur = explode("|",$pengerjaan_harian->lembur);
                            if (empty($explode_lembur[0])) {
                                $lembur = 0;
                                $lembur_ke_1 = 0;
                                $lembur_ke_2 = 0;
                            }else{
                                $lembur = $explode_lembur[0];
                                $lembur_ke_1 = $explode_lembur[1];
                                $lembur_ke_2 = $explode_lembur[2];
                            }
                        }

                        $total_gaji = $pengerjaan_harian->upah_dasar_weekly+$total_plus+$lembur;
                        // dd($total_plus);
                        // $total_gaji = $pengerjaan_harian->upah_dasar_weekly+$total_plus+$lembur;

                        if (empty($pengerjaan_harian->jht)) {
                            $jht = 0;
                        }else{
                            $jht = $pengerjaan_harian->jht;
                        }

                        if (empty($pengerjaan_harian->bpjs_kesehatan)) {
                            $bpjs_kesehatan = 0;
                        }else{
                            $bpjs_kesehatan = $pengerjaan_harian->bpjs_kesehatan;
                        }

                        if (empty($pengerjaan_harian->pensiun)) {
                            $pensiun = 0;
                        }else{
                            $pensiun = $pengerjaan_harian->pensiun;
                        }

                        if (empty($pengerjaan_harian->minus_1)) {
                            $minus_1 = 0;
                        }else{
                            $explode_minus_1 = explode("|",$pengerjaan_harian->minus_1);
                            if ($explode_minus_1[0] == 0 || $explode_minus_1[0] == "") {
                                $minus_1 = 0;
                            }else{
                                $minus_1 = $explode_minus_1[0];
                            }
                        }

                        if (empty($pengerjaan_harian->minus_2)) {
                            $minus_2 = 0;
                        }else{
                            $explode_minus_2 = explode("|",$pengerjaan_harian->minus_2);
                            if ($explode_minus_2[0] == 0 || $explode_minus_2[0] == "") {
                                $minus_2 = 0;
                            }else{
                                $minus_2 = $explode_minus_2[0];
                            }
                        }

                        $total_potongan = $jht+$bpjs_kesehatan+$pensiun+$minus_1+$minus_2;

                        $total_diterima = $total_gaji-$total_potongan;

                        // array_push($total_hari_kerja,$pengerjaan_harian->hari_kerja);
                        // array_push($total_upah_dasar,$pengerjaan_harian->upah_dasar);
                        // array_push($total_upah_dasar,$pengerjaan_harian->upah_dasar);
                        // array_push($total_gaji_pokok,$pengerjaan_harian->upah_dasar_weekly);

                        array_push($total_plus_1,$plus_1);
                        array_push($total_plus_2,$plus_2);
                        array_push($total_plus_3,$plus_3);
                        array_push($total_uang_makan,$uang_makan);

                        if ($new_data_pengerjaan->akhir_bulan == $akhir_bulan) {
                            array_push($total_tunjangan_kerja,$tunjangan_kerja);
                            array_push($total_tunjangan_kehadiran,$tunjangan_kehadiran);
                        }

                        array_push($total_all_plus,$total_plus);

                        array_push($total_lembur_ke_1,$lembur_ke_1);
                        array_push($total_lembur_ke_2,$lembur_ke_2);
                        array_push($total_lembur,$lembur);
                        array_push($total_all_gaji,$total_gaji);

                        array_push($total_jht,$jht);
                        array_push($total_bpjs_kesehatan,$bpjs_kesehatan);
                        array_push($total_pensiun,$pensiun);

                        array_push($total_minus_1,$minus_1);
                        array_push($total_minus_2,$minus_2);
                        array_push($total_all_potongan,$total_potongan);
                        array_push($total_gaji_diterima,$total_diterima);

                        if (empty($pengerjaan_harian->hasil_kerja)) {
                            $total_all_hasil_kerja = 0;
                        }else{
                            $total_hasil_kerja = [];
                            $explode_hasil_kerja = explode("|",$pengerjaan_harian->hasil_kerja);
                            $array_hasil_kerja = array_push($total_hasil_kerja,array_filter($explode_hasil_kerja));
                            $total_all_hasil_kerja = array_sum($total_hasil_kerja[0]);
                            // dd($total_hasil_kerja);
                        }
                        array_push($total_hari_kerja,$total_all_hasil_kerja);

                        $data['jhtss'] = \App\Models\BPJSJHT::where('status','y')->get();
                        $data['bpjs_kesehatan'] = \App\Models\BPJSKesehatan::select('nominal')->where('status','y')->first();

                        $awal  = new DateTime($pengerjaan_harian->tanggal);
                        $akhir = new DateTime(); // Waktu sekarang
                        $diff  = $awal->diff($akhir);
                        $data['masa_kerja'] = $diff->y.' Tahun '.$diff->m.' Bulan '.$diff->d.' Hari';
                        $data['masa_kerja_tahun'] = $diff->y;
                        $data['masa_kerja_hari'] = $diff->d;

                        $data['upah_dasar_karyawan'] = [];
                        foreach ($data['jhtss'] as $key => $jhts) {
                            if ($data['masa_kerja_tahun'] > 15) {
                                if ($jhts->urutan == 3) {
                                    $data['upah_dasar_karyawan'] = ($data['bpjs_kesehatan']['nominal'] + 100000)/$cutOff->tanggal;
                                }
                            }
                            elseif($data['masa_kerja_tahun'] >= 10 && $data['masa_kerja_tahun'] <= 15){
                                if ($jhts->urutan == 2) {
                                    $data['upah_dasar_karyawan'] = ($data['bpjs_kesehatan']['nominal'] + 50000)/$cutOff->tanggal;
                                }
                            }
                            elseif($data['masa_kerja_tahun'] <= 10 || $data['masa_kerja_hari'] >= 1){
                                if ($jhts->urutan == 1) {
                                    $data['upah_dasar_karyawan'] = ($data['bpjs_kesehatan']['nominal'] + 0)/$cutOff->tanggal;
                                }
                            }
                        }

                        array_push($total_upah_dasar,$pengerjaan_harian->upah_dasar_weekly);
                        array_push($total_gaji_pokok,$pengerjaan_harian->upah_dasar);

                    @endphp
                    {{-- <td style="text-align: center; border: 1px solid black;">-</td> --}}
                    <td style="text-align: center; border: 1px solid black;">{{ $total_all_hasil_kerja }}</td>
                    {{-- <td style="text-align: right; border: 1px solid black;">{{ $pengerjaan_harian->upah_dasar_weekly }}</td> --}}
                    <td style="text-align: right; border: 1px solid black;">{{ round($pengerjaan_harian->upah_dasar_weekly) }}</td>
                    <td style="text-align: right; border: 1px solid black;">{{ round($pengerjaan_harian->upah_dasar) }}</td>
                    <td style="text-align: right; border: 1px solid black;">{{ $plus_1 }}</td>
                    <td style="text-align: right; border: 1px solid black;">{{ $plus_2 }}</td>
                    <td style="text-align: right; border: 1px solid black;">{{ $plus_3 }}</td>
                    <td style="text-align: right; border: 1px solid black;">{{ $uang_makan }}</td>
                    @if ($new_data_pengerjaan->akhir_bulan == $akhir_bulan)
                    <td style="text-align: right; border: 1px solid black;">{{ $tunjangan_kerja }}</td>
                    <td style="text-align: right; border: 1px solid black;">{{ $tunjangan_kehadiran }}</td>
                    @endif
                    <td style="text-align: right; border: 1px solid black;">{{ $total_plus }}</td>
                    <td style="text-align: center; border: 1px solid black;">{{ $lembur_ke_1 }}</td>
                    <td style="text-align: center; border: 1px solid black;">{{ $lembur_ke_2 }}</td>
                    <td style="text-align: right; border: 1px solid black;">{{ $lembur }}</td>
                    <td style="text-align: right; border: 1px solid black;">{{ $total_gaji }}</td>
                    <td style="text-align: right; border: 1px solid black;">{{ $jht }}</td>
                    <td style="text-align: right; border: 1px solid black;">{{ $bpjs_kesehatan }}</td>
                    <td style="text-align: right; border: 1px solid black;">{{ $minus_1 }}</td>
                    <td style="text-align: right; border: 1px solid black;">{{ $minus_2 }}</td>
                    <td style="text-align: right; border: 1px solid black;">{{ $total_potongan }}</td>
                    <td style="text-align: right; border: 1px solid black;">{{ $total_diterima }}</td>
                    <td style="text-align: right;">{{ $keterangan_1 }}</td>
                    <td style="text-align: right;">{{ $keterangan_2 }}</td>
                    <td style="text-align: right;">{{ $keterangan_3 }}</td>
                </tr>
            @endforeach
        </tbody>
        @php
            $total_penjumlahan_hasil_kerja = [];
            // dd($total_gaji_pokok);
        @endphp
        <tfoot>
            <tr>
                <td colspan="3" style="text-align: center; border: 1px solid black; font-weight: bold">TOTAL</td>
                @for ($i = 0; $i < $a; $i++)
                @php
                    $total_hari_jam_kerja = [];
                    $pengerjaan_harians = \App\Models\PengerjaanHarian::whereIn('operator_harian_karyawan_id',$operator_karyawan_id)
                                                                    ->where('kode_pengerjaan',$kode_pengerjaan)
                                                                    ->get();
                    foreach ($pengerjaan_harians as $key => $pengerjaan_harian) {
                        $explode_hasil_kerja = explode("|",$pengerjaan_harian->hasil_kerja);
                        $hasil_kerja = array_push($total_hari_jam_kerja,$explode_hasil_kerja[$i]);
                    }
                @endphp
                <td style="text-align: center; border: 1px solid black; font-weight: bold">{{ array_sum($total_hari_jam_kerja) }}</td>
                @endfor
                <td style="text-align: center; border: 1px solid black; font-weight: bold">{{ array_sum($total_hari_kerja) }}</td>
                <td style="text-align: right; border: 1px solid black; font-weight: bold">{{ round(array_sum($total_upah_dasar)) }}</td>
                <td style="text-align: right; border: 1px solid black; font-weight: bold">{{ round(array_sum($total_gaji_pokok)) }}</td>
                <td style="text-align: right; border: 1px solid black; font-weight: bold">{{ array_sum($total_plus_1) }}</td>
                <td style="text-align: right; border: 1px solid black; font-weight: bold">{{ array_sum($total_plus_2) }}</td>
                <td style="text-align: right; border: 1px solid black; font-weight: bold">{{ array_sum($total_plus_3) }}</td>
                <td style="text-align: right; border: 1px solid black; font-weight: bold">{{ array_sum($total_uang_makan) }}</td>
                @if ($new_data_pengerjaan->akhir_bulan == $akhir_bulan)
                <td style="text-align: right; border: 1px solid black; font-weight: bold">{{ array_sum($total_tunjangan_kerja) }}</td>
                <td style="text-align: right; border: 1px solid black; font-weight: bold">{{ array_sum($total_tunjangan_kehadiran) }}</td>
                @endif
                <td style="text-align: right; border: 1px solid black; font-weight: bold">{{ array_sum($total_all_plus) }}</td>
                <td style="text-align: center; border: 1px solid black; font-weight: bold">{{ array_sum($total_lembur_ke_1) }}</td>
                <td style="text-align: center; border: 1px solid black; font-weight: bold">{{ array_sum($total_lembur_ke_2) }}</td>
                <td style="text-align: right; border: 1px solid black; font-weight: bold">{{ array_sum($total_lembur) }}</td>
                <td style="text-align: right; border: 1px solid black; font-weight: bold">{{ array_sum($total_all_gaji) }}</td>
                <td style="text-align: right; border: 1px solid black; font-weight: bold">{{ array_sum($total_jht) }}</td>
                <td style="text-align: right; border: 1px solid black; font-weight: bold">{{ array_sum($total_bpjs_kesehatan) }}</td>
                {{-- <td style="text-align: right; border: 1px solid black; font-weight: bold">{{ array_sum($total_pensiun) }}</td> --}}
                <td style="text-align: right; border: 1px solid black; font-weight: bold">{{ array_sum($total_minus_1) }}</td>
                <td style="text-align: right; border: 1px solid black; font-weight: bold">{{ array_sum($total_minus_2) }}</td>
                <td style="text-align: right; border: 1px solid black; font-weight: bold">{{ array_sum($total_all_potongan) }}</td>
                <td style="text-align: right; border: 1px solid black; font-weight: bold">{{ array_sum($total_gaji_diterima) }}</td>
            </tr>
            <tr></tr>
            <tr>
                <td colspan="3"></td>
                @foreach ($exp_tanggals as $et => $exp_tanggal)
                <td style="text-align: center"></td>
                @endforeach
                @for ($t = 1; $t <= 13; $t++)
                <td></td>
                @endfor
                @if ($new_data_pengerjaan->akhir_bulan == $akhir_bulan)
                <td colspan="7" style="border: 1px solid black; font-weight: bold; text-align: right; font-size: 18pt">{{ number_format(array_sum($total_gaji_diterima),0,',','.') }}</td>
                @else
                <td colspan="5" style="border: 1px solid black; font-weight: bold; text-align: right; font-size: 18pt">{{ number_format(array_sum($total_gaji_diterima),0,',','.') }}</td>
                @endif
            </tr>
        </tfoot>
    </table>
</body>
</html>
