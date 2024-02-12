<!DOCTYPE html>
<html lang="en">
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
        $a = count($exp_tanggals);
        $exp_tgl_awal = explode('-', $exp_tanggals[1]);
        $exp_tgl_akhir = explode('-', $exp_tanggals[$a]);
    @endphp

    <table>
        <thead>
            <tr>
                <td colspan="12">Daftar Gaji Supir RIT PT. Indonesian Tobacco Tbk.</td>
            </tr>
            <tr>
                <td colspan="12">Tanggal :
                    {{ \Carbon\Carbon::parse($exp_tgl_awal[0] . '-' . $exp_tgl_awal[1] . '-' . $exp_tgl_awal[2])->isoFormat('D MMMM') }}
                    -
                    {{ \Carbon\Carbon::parse($exp_tgl_akhir[0] . '-' . $exp_tgl_akhir[1] . '-' . $exp_tgl_akhir[2])->isoFormat('D MMMM YYYY') }}
                </td>
            </tr>
            <tr>
                <td></td>
            </tr>
            <tr>
                <td></td>
            </tr>
            <tr>
                <td rowspan="2" style="text-align: center; border: 1px solid black; background-color: #EEEEEE; font-weight: bold; vertical-align: middle">NO</td>
                <td rowspan="2" style="text-align: center; border: 1px solid black; background-color: #EEEEEE; font-weight: bold; vertical-align: middle">NIK</td>
                <td rowspan="2" style="text-align: center; border: 1px solid black; background-color: #EEEEEE; font-weight: bold; vertical-align: middle">NAMA</td>
                @foreach ($exp_tanggals as $exp_tanggal)
                <td colspan="6" style="text-align: center; border: 1px solid black; background-color: #EEEEEE; font-weight: bold; vertical-align: middle">{{ \Carbon\Carbon::parse($exp_tanggal)->format('d M') }}</td>
                {{-- <td colspan="6" style="text-align: center; border: 1px solid black; background-color: #EEEEEE; font-weight: bold; vertical-align: middle">{{ \Carbon\Carbon::parse($exp_tanggal)->isoFormat('D MMMM') }}</td> --}}
                @endforeach
                <td rowspan="2" style="text-align: center; border: 1px solid black; background-color: #EEEEEE; font-weight: bold; vertical-align: middle">Upah Dasar</td>
                @if ($new_data_pengerjaan->akhir_bulan == $akhir_bulan)
                <td colspan="7" style="text-align: center; border: 1px solid black; background-color: #EEEEEE; font-weight: bold; vertical-align: middle">PLUS</td>
                @else
                <td colspan="5" style="text-align: center; border: 1px solid black; background-color: #EEEEEE; font-weight: bold; vertical-align: middle">PLUS</td>
                @endif
                <td colspan="3" style="text-align: center; border: 1px solid black; background-color: #EEEEEE; font-weight: bold; vertical-align: middle">Lembur</td>
                <td rowspan="2" style="text-align: center; border: 1px solid black; background-color: #EEEEEE; font-weight: bold; vertical-align: middle">Total Gaji</td>
                <td colspan="5" style="text-align: center; border: 1px solid black; background-color: #EEEEEE; font-weight: bold; vertical-align: middle">Potongan</td>
                <td rowspan="2" style="text-align: center; border: 1px solid black; background-color: #EEEEEE; font-weight: bold; vertical-align: middle">Diterima</td>
            </tr>
            <tr>
                @foreach ($exp_tanggals as $exp_tanggal)
                <td style="text-align: center; border: 1px solid black; background-color: #EEEEEE;">Kode</td>
                <td style="text-align: center; border: 1px solid black; background-color: #EEEEEE;">Hari</td>
                <td style="text-align: center; border: 1px solid black; background-color: #EEEEEE;">Rp</td>
                <td style="text-align: center; border: 1px solid black; background-color: #EEEEEE;">DPB</td>
                <td style="text-align: center; border: 1px solid black; background-color: #EEEEEE;">Hari</td>
                <td style="text-align: center; border: 1px solid black; background-color: #EEEEEE;">Rp</td>
                @endforeach
                <td style="text-align: center; border: 1px solid black; background-color: #EEEEEE;">Plus 1</td>
                <td style="text-align: center; border: 1px solid black; background-color: #EEEEEE;">Plus 2</td>
                <td style="text-align: center; border: 1px solid black; background-color: #EEEEEE;">Plus 3</td>
                <td style="text-align: center; border: 1px solid black; background-color: #EEEEEE;">Uang Makan</td>
                @if ($new_data_pengerjaan->akhir_bulan == $akhir_bulan)
                <td style="text-align: center; border: 1px solid black; background-color: #EEEEEE;">Tunj. Kerja</td>
                <td style="text-align: center; border: 1px solid black; background-color: #EEEEEE;">Tunj. Kehadiran</td>
                @endif
                <td style="text-align: center; border: 1px solid black; background-color: #EEEEEE;">Total Plus</td>
                <td style="text-align: center; border: 1px solid black; background-color: #EEEEEE;">Jam I</td>
                <td style="text-align: center; border: 1px solid black; background-color: #EEEEEE;">Jam II</td>
                <td style="text-align: center; border: 1px solid black; background-color: #EEEEEE;">Rp Lembur</td>
                <td style="text-align: center; border: 1px solid black; background-color: #EEEEEE;">BPJS Ketenagakerjaan</td>
                <td style="text-align: center; border: 1px solid black; background-color: #EEEEEE;">BPJS Kesehatan</td>
                {{-- <td style="text-align: center; border: 1px solid black; background-color: #EEEEEE;">Pensiun</td> --}}
                <td style="text-align: center; border: 1px solid black; background-color: #EEEEEE;">Minus 1</td>
                <td style="text-align: center; border: 1px solid black; background-color: #EEEEEE;">Minus 2</td>
                <td style="text-align: center; border: 1px solid black; background-color: #EEEEEE;">Total Potongan</td>
            </tr>
        </thead>
        <tbody>
            @php
                $total_all_upah_dasar = [];
                $total_all_plus_1 = [];
                $total_all_plus_2 = [];
                $total_all_plus_3 = [];
                $total_all_uang_makan = [];
                $total_all_tunjangan_kerja = [];
                $total_all_tunjangan_kehadiran = [];
                $total_all_total_plus = [];
                $total_all_jam_lembur_1 = [];
                $total_all_jam_lembur_2 = [];
                $total_all_lembur = [];
                $total_all_gaji = [];
                $total_all_jht = [];
                $total_all_bpjs_kesehatan = [];
                $total_all_pensiun = [];
                $total_all_minus_1 = [];
                $total_all_minus_2 = [];
                $total_all_total_potongan = [];
                $total_all_diterima = [];
            @endphp
            @foreach ($pengerjaan_supir_rits as $psr => $pengerjaan_supir_rit)
                @php
                    $upah_dasar = [];
                @endphp
                <tr>
                    <td style="text-align: center; border: 1px solid black;">{{ $psr+1 }}</td>
                    <td style="text-align: center; border: 1px solid black;">{{ $pengerjaan_supir_rit->nik }}</td>
                    <td style="text-align: left; border: 1px solid black;">{{ $pengerjaan_supir_rit->nama }}</td>
                    @foreach ($exp_tanggals as $exp_tanggal)
                    @php
                        $hasil_pengerjaan = \App\Models\PengerjaanRITHarian::where('karyawan_supir_rit_id',$pengerjaan_supir_rit->karyawan_supir_rit_id)
                                                                            ->where('tanggal_pengerjaan',$exp_tanggal)
                                                                            ->where('kode_pengerjaan',$kode_pengerjaan)
                                                                            ->first();
                        if (empty($hasil_pengerjaan->hasil_kerja_1)) {
                            $jenis_umk = '-';
                            $hari = 0;
                            $hasil_kerja_1 = 0;
                            $hasil_umk_rit = 0;
                            $tarif_umk = 0;
                            $hari_dpb = 0;
                            $dpb = 0;
                        }else{
                            $explode_hasil_kerja_1 = explode("|",$hasil_pengerjaan->hasil_kerja_1);
                            $umk_rit = \App\Models\RitUMK::where('id',$explode_hasil_kerja_1[0])->first();
                            // dd($umk_rit);
                            if (empty($umk_rit)) {
                                $jenis_umk = '-';
                                $hari = 0;
                                $hasil_kerja_1 = 0;
                                $hasil_umk_rit = 0;
                                $tarif_umk = 0;
                                $hari_dpb = 0;
                                $dpb = 0;
                            }else{
                                $jenis_umk = $umk_rit->kategori_upah;
                                $hari = $explode_hasil_kerja_1[1];
                                $hasil_kerja_1 = $umk_rit->tarif*$explode_hasil_kerja_1[1];
                                $tarif_umk = $umk_rit->tarif;
                                $hari_dpb = $hasil_pengerjaan->dpb;
                                $dpb = $hasil_pengerjaan->dpb/7*$hasil_pengerjaan->upah_dasar;
                            }
                        }
                        // $total_upah_dasar = $tarif_umk+$dpb;
                        $total_upah_dasar = $hasil_kerja_1+$dpb;
                        array_push($upah_dasar,$total_upah_dasar);
                    @endphp
                    <td style="text-align: center; border: 1px solid black;">{{ $jenis_umk }}</td>
                    <td style="text-align: center; border: 1px solid black;">{{ $hari }}</td>
                    <td style="text-align: right; border: 1px solid black;">{{ $hasil_kerja_1 }}</td>
                    <td style="text-align: center; border: 1px solid black;">-</td>
                    <td style="text-align: center; border: 1px solid black;">{{ $hari_dpb }}</td>
                    <td style="text-align: right; border: 1px solid black;">{{ round($dpb) }}</td>
                    @endforeach
                    @php
                        $hasil_upah_dasar = round(array_sum($upah_dasar));

                        if (empty($pengerjaan_supir_rit->plus_1)) {
                            $plus_1 = 0;
                            $keterangan_1 = null;
                        }else{
                            $explode_plus_1 = explode("|",$pengerjaan_supir_rit->plus_1);
                            $plus_1 = floatval($explode_plus_1[0]);
                            $keterangan_1 = $explode_plus_1[1];
                        }

                        if (empty($pengerjaan_supir_rit->plus_2)) {
                            $plus_2 = 0;
                            $keterangan_2 = null;
                        }else{
                            $explode_plus_2 = explode("|",$pengerjaan_supir_rit->plus_2);
                            $plus_2 = floatval($explode_plus_2[0]);
                            $keterangan_2 = $explode_plus_2[1];
                        }

                        if (empty($pengerjaan_supir_rit->plus_3)) {
                            $plus_3 = 0;
                            $keterangan_3 = null;
                        }else{
                            $explode_plus_3 = explode("|",$pengerjaan_supir_rit->plus_3);
                            $plus_3 = floatval($explode_plus_3[0]);
                            $keterangan_3 = $explode_plus_3[1];
                        }

                        if (empty($pengerjaan_supir_rit->uang_makan)) {
                            $uang_makan = 0;
                        }else{
                            $uang_makan = $pengerjaan_supir_rit->uang_makan;
                        }

                        if (empty($pengerjaan_supir_rit->lembur)) {
                            $lembur = 0;
                            $jam_lembur_1 = 0;
                            $jam_lembur_2 = 0;
                        }else{
                            $explode_lembur = explode("|",$pengerjaan_supir_rit->lembur);
                            $lembur = $explode_lembur[0];
                            $jam_lembur_1 = $explode_lembur[1];
                            $jam_lembur_2 = $explode_lembur[2];
                        }

                        if ($new_data_pengerjaan->akhir_bulan == $akhir_bulan){
                            if (empty($pengerjaan_supir_rit->tunjangan_kerja)) {
                                $tunjangan_kerja = 0;
                            }else{
                                $tunjangan_kerja = $pengerjaan_supir_rit->tunjangan_kerja;
                            }
    
                            if (empty($pengerjaan_supir_rit->tunjangan_kehadiran)) {
                                $tunjangan_kehadiran = 0;
                            }else{
                                $tunjangan_kehadiran = $pengerjaan_supir_rit->tunjangan_kehadiran;
                            }
    
                            $total_gaji = $hasil_upah_dasar+$plus_1+$plus_2+$plus_3+$uang_makan+$lembur+$tunjangan_kerja+$tunjangan_kehadiran;
                        }else{
                            $total_gaji = $hasil_upah_dasar+$plus_1+$plus_2+$plus_3+$uang_makan+$lembur;
                        }


                        if (empty($pengerjaan_supir_rit->minus_1)) {
                            $minus_1 = 0;
                        }else{
                            $explode_minus_1 = explode("|",$pengerjaan_supir_rit->minus_1);
                            $minus_1 = $explode_minus_1[0];
                        }

                        if (empty($pengerjaan_supir_rit->minus_2)) {
                            $minus_2 = 0;
                        }else{
                            $explode_minus_2 = explode("|",$pengerjaan_supir_rit->minus_2);
                            $minus_2 = $explode_minus_2[0];
                        }

                        if (empty($pengerjaan_supir_rit->jht)) {
                            $jht = 0;
                        }else{
                            $jht = intval($pengerjaan_supir_rit->jht);
                        }

                        if (empty($pengerjaan_supir_rit->bpjs_kesehatan)) {
                            $bpjs_kesehatan = 0;
                        }else{
                            $bpjs_kesehatan = intval($pengerjaan_supir_rit->bpjs_kesehatan);
                        }
                        // dd($minus_1);

                        if (empty($pengerjaan_supir_rit->pensiun)) {
                            $pensiun = 0;
                        }else{
                            $pensiun = $pengerjaan_supir_rit->pensiun;
                        }

                        if ($new_data_pengerjaan->akhir_bulan == $akhir_bulan){
                            $total_plus = $plus_1+$plus_2+$plus_3+$uang_makan+$tunjangan_kerja+$tunjangan_kehadiran;
                            $total_potongan = $minus_1+$minus_2+$jht+$bpjs_kesehatan+$pensiun;
                            $total_upah_diterima = $total_gaji-$minus_1-$minus_2-$jht-$bpjs_kesehatan-$pensiun;
                        }else{
                            $total_plus = $plus_1+$plus_2+$plus_3+$uang_makan;
                            $total_potongan = $minus_1+$minus_2+$jht+$bpjs_kesehatan+$pensiun;
                            $total_upah_diterima = $total_gaji-$minus_1-$minus_2-$jht-$bpjs_kesehatan-$pensiun;
                        }

                        array_push($total_all_upah_dasar,round(array_sum($upah_dasar)));
                        array_push($total_all_plus_1,round($plus_1));
                        array_push($total_all_plus_2,round($plus_2));
                        array_push($total_all_plus_3,round($plus_3));
                        array_push($total_all_uang_makan,round($uang_makan));

                        if ($new_data_pengerjaan->akhir_bulan == $akhir_bulan){
                            array_push($total_all_tunjangan_kerja,round($tunjangan_kerja));
                            array_push($total_all_tunjangan_kehadiran,round($tunjangan_kehadiran));
                        }

                        array_push($total_all_total_plus,round($total_plus));
                        array_push($total_all_jam_lembur_1,$jam_lembur_1);
                        array_push($total_all_jam_lembur_2,$jam_lembur_2);
                        array_push($total_all_lembur,round($lembur));
                        array_push($total_all_gaji,round($total_gaji));
                        array_push($total_all_jht,round($jht));
                        array_push($total_all_bpjs_kesehatan,round($bpjs_kesehatan));
                        array_push($total_all_pensiun,round($pensiun));
                        array_push($total_all_minus_1,round($minus_1));
                        array_push($total_all_minus_2,round($minus_2));
                        array_push($total_all_total_potongan,round($total_potongan));
                        array_push($total_all_diterima,round($total_upah_diterima));
                    @endphp
                    <td style="text-align: right; border: 1px solid black;">{{ $hasil_upah_dasar }}</td>
                    <td style="text-align: right; border: 1px solid black;">{{ $plus_1 }}</td>
                    <td style="text-align: right; border: 1px solid black;">{{ $plus_2 }}</td>
                    <td style="text-align: right; border: 1px solid black;">{{ $plus_3 }}</td>
                    <td style="text-align: right; border: 1px solid black;">{{ $uang_makan }}</td>
                    @if ($new_data_pengerjaan->akhir_bulan == $akhir_bulan)
                    <td style="text-align: right; border: 1px solid black;">{{ $tunjangan_kerja }}</td>
                    <td style="text-align: right; border: 1px solid black;">{{ $tunjangan_kehadiran }}</td>
                    @endif
                    <td style="text-align: right; border: 1px solid black;">{{ $total_plus }}</td>
                    <td style="text-align: right; border: 1px solid black;">{{ $jam_lembur_1 }}</td>
                    <td style="text-align: right; border: 1px solid black;">{{ $jam_lembur_2 }}</td>
                    <td style="text-align: right; border: 1px solid black;">{{ $lembur }}</td>
                    <td style="text-align: right; border: 1px solid black;">{{ $total_gaji }}</td>
                    <td style="text-align: right; border: 1px solid black;">{{ $jht }}</td>
                    <td style="text-align: right; border: 1px solid black;">{{ $bpjs_kesehatan }}</td>
                    {{-- <td style="text-align: right; border: 1px solid black;">{{ $pensiun }}</td> --}}
                    <td style="text-align: right; border: 1px solid black;">{{ $minus_1 }}</td>
                    <td style="text-align: right; border: 1px solid black;">{{ $minus_2 }}</td>
                    <td style="text-align: right; border: 1px solid black;">{{ $total_potongan }}</td>
                    <td style="text-align: right; border: 1px solid black;">{{ $total_upah_diterima }}</td>
                    <td style="text-align: right;">{{ $keterangan_1 }}</td>
                    <td style="text-align: right;">{{ $keterangan_2 }}</td>
                    <td style="text-align: right;">{{ $keterangan_3 }}</td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <td colspan="3" style="text-align: center; border: 1px solid black; font-weight: bold">TOTAL</td>
                @foreach ($exp_tanggals as $exp_tanggal)
                @php
                    $total_hari = [];
                    $total_tarif = [];
                    $total_hari_dpb = [];
                    $total_dpb = [];

                    $hasil_pengerjaans = \App\Models\PengerjaanRITHarian::where('tanggal_pengerjaan',$exp_tanggal)
                                                                        ->where('kode_pengerjaan',$kode_pengerjaan)
                                                                        ->get();
                    foreach ($hasil_pengerjaans as $key => $hasil_pengerjaan) {
                        if (empty($hasil_pengerjaan->hasil_kerja_1)) {
                            $hari = 0;
                            $tarif_umk = 0;
                            $hari_dpb = 0;
                            $dpb = 0;
                            $hasil_kerja_1 = 0;
                        }else{
                            $explode_hari = explode("|",$hasil_pengerjaan->hasil_kerja_1);
                            $umk_rit = \App\Models\RitUMK::where('id',$explode_hari[0])->first();
                            if (empty($umk_rit)) {
                                $hari = 0;
                                $tarif_umk = 0;
                                $hari_dpb = 0;
                                $dpb = 0;
                                $hasil_kerja_1 = 0;
                            }else{
                                $hari = $explode_hari[1];
                                $hari_dpb = $hasil_pengerjaan->dpb;
                                $tarif_umk = $umk_rit->tarif;
                                $dpb = $dpb = $hasil_pengerjaan->dpb/7*$hasil_pengerjaan->upah_dasar;
                                $hasil_kerja_1 = $umk_rit->tarif*$hari;
                            }
                        }
                        array_push($total_hari,$hari);
                        array_push($total_tarif,$hasil_kerja_1);
                        array_push($total_hari_dpb,$hari_dpb);
                        array_push($total_dpb,round($dpb));
                    }
                @endphp
                <td style="text-align: center; border: 1px solid black; font-weight: bold"></td>
                <td style="text-align: center; border: 1px solid black; font-weight: bold;">{{ array_sum($total_hari) }}</td>
                <td style="text-align: right; border: 1px solid black; font-weight: bold;">{{ array_sum($total_tarif) }}</td>
                <td style="text-align: center; border: 1px solid black; font-weight: bold">-</td>
                <td style="text-align: center; border: 1px solid black; font-weight: bold">{{ array_sum($total_hari_dpb) }}</td>
                <td style="text-align: right; border: 1px solid black; font-weight: bold">{{ array_sum($total_dpb) }}</td>
                @endforeach
                {{-- <td style="text-align: right; border: 1px solid black; font-weight: bold">{{ array_sum($total_all_upah_dasar) }}</td> --}}
                <td style="text-align: right; border: 1px solid black; font-weight: bold">{{ array_sum($total_all_upah_dasar) }}</td>
                <td style="text-align: right; border: 1px solid black; font-weight: bold">{{ array_sum($total_all_plus_1) }}</td>
                <td style="text-align: right; border: 1px solid black; font-weight: bold">{{ array_sum($total_all_plus_2) }}</td>
                <td style="text-align: right; border: 1px solid black; font-weight: bold">{{ array_sum($total_all_plus_3) }}</td>
                <td style="text-align: right; border: 1px solid black; font-weight: bold">{{ array_sum($total_all_uang_makan) }}</td>
                @if ($new_data_pengerjaan->akhir_bulan == $akhir_bulan)
                <td style="text-align: right; border: 1px solid black; font-weight: bold">{{ array_sum($total_all_tunjangan_kerja) }}</td>
                <td style="text-align: right; border: 1px solid black; font-weight: bold">{{ array_sum($total_all_tunjangan_kehadiran) }}</td>
                @endif
                <td style="text-align: right; border: 1px solid black; font-weight: bold">{{ array_sum($total_all_total_plus) }}</td>
                <td style="text-align: right; border: 1px solid black; font-weight: bold">{{ array_sum($total_all_jam_lembur_1) }}</td>
                <td style="text-align: right; border: 1px solid black; font-weight: bold">{{ array_sum($total_all_jam_lembur_2) }}</td>
                <td style="text-align: right; border: 1px solid black; font-weight: bold">{{ array_sum($total_all_lembur) }}</td>
                <td style="text-align: right; border: 1px solid black; font-weight: bold">{{ array_sum($total_all_gaji) }}</td>
                <td style="text-align: right; border: 1px solid black; font-weight: bold">{{ array_sum($total_all_jht) }}</td>
                <td style="text-align: right; border: 1px solid black; font-weight: bold">{{ array_sum($total_all_bpjs_kesehatan) }}</td>
                {{-- <td style="text-align: right; border: 1px solid black; font-weight: bold">{{ array_sum($total_all_pensiun) }}</td> --}}
                <td style="text-align: right; border: 1px solid black; font-weight: bold">{{ array_sum($total_all_minus_1) }}</td>
                <td style="text-align: right; border: 1px solid black; font-weight: bold">{{ array_sum($total_all_minus_2) }}</td>
                <td style="text-align: right; border: 1px solid black; font-weight: bold">{{ array_sum($total_all_total_potongan) }}</td>
                <td style="text-align: right; border: 1px solid black; font-weight: bold">{{ array_sum($total_all_diterima) }}</td>
            </tr>
            <tr></tr>
            <tr>
                <td colspan="3"></td>
                @foreach ($exp_tanggals as $exp_tanggal)
                <td style="text-align: center"></td>
                <td style="text-align: center"></td>
                <td style="text-align: center"></td>
                <td style="text-align: center"></td>
                <td style="text-align: center"></td>
                <td style="text-align: center"></td>
                @endforeach
                {{-- @for ($t = 1; $t <= 13; $t++)
                <td></td>
                @endfor --}}
                @if ($new_data_pengerjaan->akhir_bulan == $akhir_bulan)
                <td colspan="18" style="border: 1px solid black; font-weight: bold; text-align: right; font-size: 18pt">{{ number_format(array_sum($total_all_diterima),0,',','.') }}</td>
                @else
                <td colspan="16" style="border: 1px solid black; font-weight: bold; text-align: right; font-size: 18pt">{{ number_format(array_sum($total_all_diterima),0,',','.') }}</td>
                @endif
            </tr>
        </tfoot>
    </table>
</body>
</html>