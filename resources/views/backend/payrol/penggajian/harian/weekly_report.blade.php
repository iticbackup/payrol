<html>

<head>
    <title> Report Harian Weekly </title>
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
        <b>Total Daftar Gaji Karyawan Harian</b> <br>
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
                <td style="font-size: 10pt; height: 5%; text-align: center; background-color: grey; font-weight: bold"><span style="color: white">UPAH <br>DASAR</span></td>
                <td style="font-size: 10pt; height: 5%; text-align: center; background-color: grey; font-weight: bold"><span style="color: white">TUNJ.KERJA</span></td>
                <td style="font-size: 10pt; height: 5%; text-align: center; background-color: grey; font-weight: bold"><span style="color: white">TUNJ. <br>KEHADIRAN</span></td>
                <td style="font-size: 10pt; height: 5%; text-align: center; background-color: grey; font-weight: bold"><span style="color: white">UANG <br>MAKAN</span></td>
                <td style="font-size: 10pt; height: 5%; text-align: center; background-color: grey; font-weight: bold"><span style="color: white">PLUS</span></td>
                <td style="font-size: 10pt; height: 5%; text-align: center; background-color: grey; font-weight: bold"><span style="color: white">MINUS</span></td>
                <td style="font-size: 10pt; height: 5%; text-align: center; background-color: grey; font-weight: bold"><span style="color: white">POT. <br>JHT</span></td>
                <td style="font-size: 10pt; height: 5%; text-align: center; background-color: grey; font-weight: bold"><span style="color: white">POT. <br>BPJS</span></td>
                <td style="font-size: 10pt; height: 5%; text-align: center; background-color: grey; font-weight: bold"><span style="color: white">POT. <br>PENSIUN</span></td>
                <td style="font-size: 10pt; height: 5%; text-align: center; background-color: grey; font-weight: bold"><span style="color: white">UPAH  <br>DITERIMA</span></td>
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
                $operator_harian_karyawans = \App\Models\KaryawanOperatorHarian::select([
                                                                                'pengerjaan_harian.operator_harian_karyawan_id as operator_harian_karyawan_id',
                                                                                'pengerjaan_harian.upah_dasar as upah_dasar',
                                                                                'pengerjaan_harian.upah_dasar_weekly as upah_dasar_weekly',
                                                                                'pengerjaan_harian.hari_kerja as hari_kerja',
                                                                                'pengerjaan_harian.hasil_kerja as hasil_kerja',
                                                                                'pengerjaan_harian.tunjangan_kerja as tunjangan_kerja',
                                                                                'pengerjaan_harian.tunjangan_kehadiran as tunjangan_kehadiran',
                                                                                'pengerjaan_harian.plus_1 as plus_1',
                                                                                'pengerjaan_harian.plus_2 as plus_2',
                                                                                'pengerjaan_harian.plus_3 as plus_3',
                                                                                'pengerjaan_harian.minus_1 as minus_1',
                                                                                'pengerjaan_harian.minus_2 as minus_2',
                                                                                'pengerjaan_harian.uang_makan as uang_makan',
                                                                                'pengerjaan_harian.lembur as lembur',
                                                                                'pengerjaan_harian.jht as jht',
                                                                                'pengerjaan_harian.bpjs_kesehatan as bpjs_kesehatan',
                                                                            ])
                                                                            ->leftJoin('pengerjaan_harian','pengerjaan_harian.operator_harian_karyawan_id','=','operator_harian_karyawan.id')
                                                                            ->where('operator_harian_karyawan.jenis_operator_detail_id',$jenis_operator_detail_pengerjaan->jenis_operator_detail_id)
                                                                            ->where('operator_harian_karyawan.jenis_operator_detail_pekerjaan_id',$jenis_operator_detail_pengerjaan->id)
                                                                            ->get();
                // dd($operator_harian_karyawans);
                foreach ($operator_harian_karyawans as $keys => $operator_harian_karyawan) {
                    $total_keseluruhan_upah_diterima = [];
                    $pengerjaan_harians = \App\Models\PengerjaanHarian::where('kode_pengerjaan',$kode_pengerjaan)
                                                                    ->where('operator_harian_karyawan_id',$operator_harian_karyawan->operator_harian_karyawan_id)
                                                                    ->get();

                    foreach ($pengerjaan_harians as $ph => $pengerjaan_harian) {
                        
                        $upah_dasar_weekly = $pengerjaan_harian->upah_dasar_weekly;

                        if (empty($pengerjaan_harian->plus_1)) {
                            $plus_1 = 0;
                        }else{
                            $explode_plus_1 = explode("|",$pengerjaan_harian->plus_1);
                            if (empty($explode_plus_1[0])) {
                                $plus_1 = 0;
                            }else{
                                $plus_1 = $explode_plus_1[0];
                            }
                        }

                        if (empty($pengerjaan_harian->plus_2)) {
                            $plus_2 = 0;
                        }else{
                            $explode_plus_2 = explode("|",$pengerjaan_harian->plus_2);
                            if (empty($explode_plus_2[0])) {
                                $plus_2 = 0;
                            }else{
                                $plus_2 = $explode_plus_2[0];
                            }
                        }

                        if (empty($pengerjaan_harian->plus_3)) {
                            $plus_3 = 0;
                        }else{
                            $explode_plus_3 = explode("|",$pengerjaan_harian->plus_3);
                            if (empty($explode_plus_3[0])) {
                                $plus_3 = 0;
                            }else{
                                $plus_3 = $explode_plus_3[0];
                            }
                        }

                        if (empty($pengerjaan_harian->lembur)) {
                            $lembur = 0;
                        }else{
                            $explode_lembur = explode("|",$pengerjaan_harian->lembur);
                            if (empty($explode_lembur[0])) {
                                $lembur = 0;
                            }else{
                                $lembur = $explode_lembur[0];
                            }
                        }

                        if (empty($pengerjaan_harian->minus_1)) {
                            $minus_1 = 0;
                        }else{
                            $explode_minus_1 = explode("|",$pengerjaan_harian->minus_1);
                            if (empty($explode_minus_1[0])) {
                                $minus_1 = 0;
                            }else{
                                $minus_1 = $explode_minus_1[0];
                            }
                        }

                        if (empty($pengerjaan_harian->minus_2)) {
                            $minus_2 = 0;
                        }else{
                            $explode_minus_2 = explode("|",$pengerjaan_harian->minus_2);
                            if (empty($explode_minus_2[0])) {
                                $minus_2 = 0;
                            }else{
                                $minus_2 = $explode_minus_2[0];
                            }
                        }

                        $total_all_plus = $plus_1+$plus_2+$plus_3;
                        array_push($total_plus,$total_all_plus);

                        $total_all_minus = $minus_1+$minus_2;
                        array_push($total_minus,$total_all_minus);
                        
                        array_push($total_tunjangan_kerja,$pengerjaan_harian->tunjangan_kerja);
                        array_push($total_tunjangan_kehadiran,$pengerjaan_harian->tunjangan_kehadiran);
                        array_push($total_uang_makan,$pengerjaan_harian->uang_makan);

                        array_push($total_upah_dasar,$upah_dasar_weekly);
                        array_push($total_upah_lembur,$lembur);

                        array_push($total_jht,$pengerjaan_harian->jht);
                        array_push($total_bpjs_kesehatan,$pengerjaan_harian->bpjs_kesehatan);
                        // echo json_encode($total_all_plus);
                        // echo json_encode($pengerjaan_harian);
                        $upah_diterima = ($upah_dasar_weekly+$total_all_plus+$pengerjaan_harian->tunjangan_kerja+$pengerjaan_harian->tunjangan_kehadiran+$pengerjaan_harian->uang_makan+$lembur)
                                        -
                                        ($pengerjaan_harian->jht+$pengerjaan_harian->bpjs_kesehatan+$total_all_minus)
                                        ;
                        array_push($total_jumlah_upah_diterima,$upah_diterima);
                    }
                }

                array_push($total_keseluruhan_upah_dasar,array_sum($total_upah_dasar));
                array_push($total_keseluruhan_tunjangan_kerja,array_sum($total_tunjangan_kerja));
                array_push($total_keseluruhan_tunjangan_kehadiran,array_sum($total_tunjangan_kehadiran));
                array_push($total_keseluruhan_uang_makan,array_sum($total_uang_makan));
                array_push($total_keseluruhan_plus,array_sum($total_plus));
                array_push($total_keseluruhan_minus,array_sum($total_minus));
                array_push($total_keseluruhan_pot_jht,array_sum($total_jht));
                array_push($total_keseluruhan_pot_kesehatan,array_sum($total_bpjs_kesehatan));
                array_push($total_keseluruhan_pot_pensiun,0);
                array_push($total_keseluruhan_upah_diterimas,array_sum($total_jumlah_upah_diterima));
            @endphp
            <tr>
                <td style="font-size: 10pt; text-align: center">{{ $key+1 }}</td>
                <td style="font-size: 10pt">{{ $jenis_operator_detail_pengerjaan->jenis_posisi_pekerjaan }}</td>
                <td style="font-size: 10pt; text-align: right">{{ number_format(array_sum($total_upah_dasar),0,',','.') }}</td>
                <td style="font-size: 10pt; text-align: right">{{ number_format(array_sum($total_tunjangan_kerja),0,',','.') }}</td>
                <td style="font-size: 10pt; text-align: right">{{ number_format(array_sum($total_tunjangan_kehadiran),0,',','.') }}</td>
                <td style="font-size: 10pt; text-align: right">{{ number_format(array_sum($total_uang_makan),0,',','.') }}</td>
                <td style="font-size: 10pt; text-align: right">{{ number_format(array_sum($total_plus),0,',','.') }}</td>
                <td style="font-size: 10pt; text-align: right">{{ number_format(array_sum($total_minus),0,',','.') }}</td>
                <td style="font-size: 10pt; text-align: right">{{ number_format(array_sum($total_jht),0,',','.') }}</td>
                <td style="font-size: 10pt; text-align: right">{{ number_format(array_sum($total_bpjs_kesehatan),0,',','.') }}</td>
                <td style="font-size: 10pt; text-align: right">0</td>
                <td style="font-size: 10pt; text-align: right">{{ number_format(array_sum($total_jumlah_upah_diterima),0,',','.') }}</td>
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
