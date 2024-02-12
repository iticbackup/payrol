<html>

<head>
    <title>Report Supir RIT Weekly</title>
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
        $total_rit_upah_dasar = [];
        $explode_tanggal_pengerjaans = explode('#', $new_data_pengerjaan['tanggal']);
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
        <b>Total Daftar Gaji Karyawan Supir RIT</b> <br>
        <b>Tanggal :
            {{ \Carbon\Carbon::parse($exp_tgl_awal[0] . '-' . $exp_tgl_awal[1] . '-' . $exp_tgl_awal[2])->isoFormat('D MMMM') . ' s/d ' . \Carbon\Carbon::parse($exp_tgl_akhir[0] . '-' . $exp_tgl_akhir[1] . '-' . $exp_tgl_akhir[2])->isoFormat('D MMMM YYYY') }}</b>
    </div>
    </p>
    <br>

    <table>
        <tbody>
            <tr>
                <td style="font-size: 10pt; height: 5%; text-align: center; background-color: grey; font-weight: bold"><span style="color: white">NO</span></td>
                <td style="font-size: 10pt; height: 5%; text-align: center; background-color: grey; font-weight: bold"><span style="color: white">UNIT KERJA</span></td>
                <td style="font-size: 10pt; height: 5%; text-align: center; background-color: grey; font-weight: bold"><span style="color: white">UPAH  <br>DASAR</span></td>
                <td style="font-size: 10pt; height: 5%; text-align: center; background-color: grey; font-weight: bold"><span style="color: white">TUNJ.KERJA</span></td>
                <td style="font-size: 10pt; height: 5%; text-align: center; background-color: grey; font-weight: bold"><span style="color: white">TUNJ. <br>KEHADIRAN</span></td>
                <td style="font-size: 10pt; height: 5%; text-align: center; background-color: grey; font-weight: bold"><span style="color: white">UANG  <br>MAKAN</span></td>
                <td style="font-size: 10pt; height: 5%; text-align: center; background-color: grey; font-weight: bold"><span style="color: white">PLUS</span></td>
                <td style="font-size: 10pt; height: 5%; text-align: center; background-color: grey; font-weight: bold"><span style="color: white">MINUS</span></td>
                <td style="font-size: 10pt; height: 5%; text-align: center; background-color: grey; font-weight: bold"><span style="color: white">POT. <br>JHT</span></td>
                <td style="font-size: 10pt; height: 5%; text-align: center; background-color: grey; font-weight: bold"><span style="color: white">POT. <br>BPJS</span></td>
                <td style="font-size: 10pt; height: 5%; text-align: center; background-color: grey; font-weight: bold"><span style="color: white">POT. <br>PENSIUN</span></td>
                <td style="font-size: 10pt; height: 5%; text-align: center; background-color: grey; font-weight: bold"><span style="color: white">UPAH  <br>DITERIMA</span></td>
            </tr>
            @foreach ($rit_posisis as $key => $rit_posisi)
            @php
                $upah_yang_diterima = [];
                $upah_dasar = [];
                $tunjangan_kerja = [];
                $tunjangan_kehadiran = [];
                $uang_makan = [];
                $plus = [];
                $minus = [];
                $pot_jht = [];
                $pot_bpjs_kesehatan = [];
                $pot_pensiun = [];
                $supir_rit_karyawans = \App\Models\RitKaryawan::select([
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
                                                                'pengerjaan_supir_rit_weekly.bpjs_kesehatan as bpjs_kesehatan',
                                                            ])
                                                            ->leftJoin('pengerjaan_supir_rit_weekly','pengerjaan_supir_rit_weekly.karyawan_supir_rit_id','=','operator_supir_rit_karyawan.id')
                                                            ->where('operator_supir_rit_karyawan.rit_posisi_id',$rit_posisi->id)
                                                            ->where('pengerjaan_supir_rit_weekly.kode_pengerjaan',$kode_pengerjaan)
                                                            ->get();

                foreach ($supir_rit_karyawans as $keys => $supir_rit_karyawan) {
                    // array_push($total_rit_upah_dasar,array_sum($upah_dasar));
                    $pengerjaan_supir_rits = \App\Models\PengerjaanRITHarian::where('kode_pengerjaan',$kode_pengerjaan)
                                                                            ->where('karyawan_supir_rit_id',$supir_rit_karyawan->karyawan_supir_rit_id)
                                                                            ->get();
                    $total_upah_dasars = [];
                    foreach ($pengerjaan_supir_rits as $keyss => $pengerjaan_supir_rit) {
                        if (empty($pengerjaan_supir_rit->hasil_kerja_1)) {
                            $hasil_kerja_1 = 0;
                            $tarif_umk = 0;
                            $dpb = 0;
                        }else{
                            $explode_hasil_kerja_1 = explode("|",$pengerjaan_supir_rit->hasil_kerja_1);
                            if ($explode_hasil_kerja_1[0] == 0 && $explode_hasil_kerja_1[1] == 0) {
                                $hasil_kerja_1 = 0;
                                $tarif_umk = 0;
                                $dpb = 0;
                            }else{
                                $jenis_umk_rit = \App\Models\RitUMK::where('id',$explode_hasil_kerja_1[0])->first();
                                $hasil_kerja_1 = $explode_hasil_kerja_1[1];
                                $tarif_umk = $jenis_umk_rit->tarif;
                                $dpb = $pengerjaan_supir_rit->dpb/7*$pengerjaan_supir_rit->upah_dasar;
                            }
                        }

                        if (empty($pengerjaan_supir_rit->upah_dasar)) {
                            $upah_dasar_pengerjaan_supir_rit = 0;
                        }else{
                            $upah_dasar_pengerjaan_supir_rit = $pengerjaan_supir_rit->upah_dasar;
                        }

                        $total_upah_dasar = $tarif_umk+$dpb;
                        array_push($upah_dasar,$total_upah_dasar);
                        array_push($total_upah_dasars,$total_upah_dasar);
                    }

                    if (empty($supir_rit_karyawan->plus_1)) {
                        $plus_1 = 0;
                    }else{
                        $explode_plus_1 = explode("|",$supir_rit_karyawan->plus_1);
                        if ($explode_plus_1[0] == 0) {
                            $plus_1 = 0;
                        }else{
                            $plus_1 = $explode_plus_1[0];
                        }
                    }

                    if (empty($supir_rit_karyawan->plus_2)) {
                        $plus_2 = 0;
                    }else{
                        $explode_plus_2 = explode("|",$supir_rit_karyawan->plus_2);
                        if ($explode_plus_2[0] == 0) {
                            $plus_2 = 0;
                        }else{
                            $plus_2 = $explode_plus_2[0];
                        }
                    }

                    if (empty($supir_rit_karyawan->plus_3)) {
                        $plus_3 = 0;
                    }else{
                        $explode_plus_3 = explode("|",$supir_rit_karyawan->plus_3);
                        if ($explode_plus_3[0] == 0) {
                            $plus_3 = 0;
                        }else{
                            $plus_3 = $explode_plus_3[0];
                        }
                    }

                    if (empty($supir_rit_karyawan->minus_1)) {
                        $minus_1 = 0;
                    }else{
                        $explode_minus_1 = explode("|",$supir_rit_karyawan->minus_1);
                        if ($explode_minus_1[0] == 0) {
                            $minus_1 = 0;
                        }else{
                            $minus_1 = $explode_plus_1[0];
                        }
                    }

                    if (empty($supir_rit_karyawan->minus_2)) {
                        $minus_2 = 0;
                    }else{
                        $explode_minus_2 = explode("|",$supir_rit_karyawan->minus_2);
                        if ($explode_minus_2[0] == 0) {
                            $minus_2 = 0;
                        }else{
                            $minus_2 = $explode_minus_2[0];
                        }
                    }

                    $total_plus = $plus_1+$plus_2+$plus_3;
                    $total_minus = $minus_1+$minus_2;

                    array_push($plus,$total_plus);
                    array_push($minus,$total_minus);
                    array_push($tunjangan_kerja,$supir_rit_karyawan->tunjangan_kerja);
                    array_push($tunjangan_kehadiran,$supir_rit_karyawan->tunjangan_kehadiran);
                    array_push($uang_makan,$supir_rit_karyawan->uang_makan);
                    array_push($pot_jht,$supir_rit_karyawan->jht);
                    array_push($pot_bpjs_kesehatan,$supir_rit_karyawan->bpjs_kesehatan);
                    array_push($pot_pensiun,0);

                    $upah_diterima = (array_sum($total_upah_dasars)+$supir_rit_karyawan->tunjangan_kerja+$supir_rit_karyawan->tunjangan_kehadiran+$supir_rit_karyawan->uang_makan+$total_plus)
                                    -
                                    ($total_minus+$supir_rit_karyawan->jht+$supir_rit_karyawan->bpjs_kesehatan+0);
                    array_push($upah_yang_diterima,$upah_diterima);
                }

                array_push($total_keseluruhan_upah_dasar,array_sum($upah_dasar));
                array_push($total_keseluruhan_tunjangan_kerja,array_sum($tunjangan_kerja));
                array_push($total_keseluruhan_tunjangan_kehadiran,array_sum($tunjangan_kehadiran));
                array_push($total_keseluruhan_uang_makan,array_sum($uang_makan));
                array_push($total_keseluruhan_plus,array_sum($plus));
                array_push($total_keseluruhan_minus,array_sum($minus));
                array_push($total_keseluruhan_pot_jht,array_sum($pot_jht));
                array_push($total_keseluruhan_pot_kesehatan,array_sum($pot_bpjs_kesehatan));
                array_push($total_keseluruhan_pot_pensiun,array_sum($pot_pensiun));
                array_push($total_keseluruhan_upah_diterimas,array_sum($upah_yang_diterima));
            @endphp
            <tr>
                <td style="font-size: 10pt; text-align: center">{{ $key+1 }}</td>
                <td style="font-size: 10pt">{{ $rit_posisi->nama_posisi }}</td>
                <td style="font-size: 10pt; text-align: right">{{ number_format(array_sum($upah_dasar),0,',','.') }}</td>
                <td style="font-size: 10pt; text-align: right">{{ number_format(array_sum($tunjangan_kerja),0,',','.') }}</td>
                <td style="font-size: 10pt; text-align: right">{{ number_format(array_sum($tunjangan_kehadiran),0,',','.') }}</td>
                <td style="font-size: 10pt; text-align: right">{{ number_format(array_sum($uang_makan),0,',','.') }}</td>
                <td style="font-size: 10pt; text-align: right">{{ number_format(array_sum($plus),0,',','.') }}</td>
                <td style="font-size: 10pt; text-align: right">{{ number_format(array_sum($minus),0,',','.') }}</td>
                <td style="font-size: 10pt; text-align: right">{{ number_format(array_sum($pot_jht),0,',','.') }}</td>
                <td style="font-size: 10pt; text-align: right">{{ number_format(array_sum($pot_bpjs_kesehatan),0,',','.') }}</td>
                <td style="font-size: 10pt; text-align: right">{{ number_format(array_sum($pot_pensiun),0,',','.') }}</td>
                <td style="font-size: 10pt; text-align: right">{{ number_format(array_sum($upah_yang_diterima),0,',','.') }}</td>
            </tr>
            @endforeach
        </tbody>
        <tr>
            <td style="font-size: 10pt; text-align: center" colspan="2">TOTAL</td>
            <td style="font-size: 10pt; text-align: right">{{ number_format(array_sum($total_keseluruhan_upah_dasar),0,',','.') }}</td>
            <td style="font-size: 10pt; text-align: right">{{ number_format(array_sum($total_keseluruhan_tunjangan_kerja),0,',','.') }}</td>
            <td style="font-size: 10pt; text-align: right">{{ number_format(array_sum($total_keseluruhan_tunjangan_kehadiran),0,',','.') }}</td>
            <td style="font-size: 10pt; text-align: right">{{ number_format(array_sum($total_keseluruhan_uang_makan),0,',','.') }}</td>
            <td style="font-size: 10pt; text-align: right">{{ number_format(array_sum($total_keseluruhan_plus),0,',','.') }}</td>
            <td style="font-size: 10pt; text-align: right">{{ number_format(array_sum($total_keseluruhan_minus),0,',','.') }}</td>
            <td style="font-size: 10pt; text-align: right">{{ number_format(array_sum($total_keseluruhan_pot_jht),0,',','.') }}</td>
            <td style="font-size: 10pt; text-align: right">{{ number_format(array_sum($total_keseluruhan_pot_kesehatan),0,',','.') }}</td>
            <td style="font-size: 10pt; text-align: right">{{ number_format(array_sum($total_keseluruhan_pot_pensiun),0,',','.') }}</td>
            <td style="font-size: 10pt; text-align: right">{{ number_format(array_sum($total_keseluruhan_upah_diterimas),0,',','.') }}</td>
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
