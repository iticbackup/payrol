<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <style>
        table,
        td,
        th {
            /* border: 1px solid; */
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
    {{-- @php
        $total_hasil_kerjas_1 = [];
    @endphp --}}
    @php
        $akhir_bulan = 'y';
        $exp_tanggals = array_filter($explode_tanggal_pengerjaans);
        $a = count($exp_tanggals);
        $exp_tgl_awal = explode('-', $exp_tanggals[1]);
        $exp_tgl_akhir = explode('-', $exp_tanggals[$a]);
    @endphp
    {{-- <p>Daftar Gaji Borongan PT. Indonesian Tobacco Tbk.</p>
    <p>Produksi B: </p>
    <p>Tanggal : {{ \Carbon\Carbon::parse($exp_tgl_awal[0].'-'.$exp_tgl_awal[1].'-'.$exp_tgl_awal[2])->isoFormat('D MMMM') }} - {{ \Carbon\Carbon::parse($exp_tgl_akhir[0].'-'.$exp_tgl_akhir[1].'-'.$exp_tgl_akhir[2])->isoFormat('D MMMM YYYY') }}</p>
    <br> --}}
    <table>
        <thead>
            <tr >
                <td colspan="3">Daftar Gaji Borongan PT. Indonesian Tobacco Tbk.</td>
            </tr>
            <tr>
                <td colspan="3">Produksi B: {{ $jenis_operator_detail_pengerjaan->jenis_posisi_pekerjaan }}</td>
            </tr>
            <tr>
                <td colspan="3">Tanggal :
                    {{ \Carbon\Carbon::parse($exp_tgl_awal[0] . '-' . $exp_tgl_awal[1] . '-' . $exp_tgl_awal[2])->isoFormat('D MMMM') }}
                    -
                    {{ \Carbon\Carbon::parse($exp_tgl_akhir[0] . '-' . $exp_tgl_akhir[1] . '-' . $exp_tgl_akhir[2])->isoFormat('D MMMM YYYY') }}
                </td>
            </tr>
            <tr>
                <td></td>
            </tr>
            <tr>
                <td rowspan="2" style="text-align: center; border: 1px solid black; background-color: #EEEEEE; font-weight: bold" valign="middle">No</td>
                <td rowspan="2" style="text-align: center; border: 1px solid black; background-color: #EEEEEE; font-weight: bold" valign="middle">NIK</td>
                <td rowspan="2" style="text-align: center; border: 1px solid black; background-color: #EEEEEE; font-weight: bold" valign="middle">Nama</td>
                @foreach ($exp_tanggals as $exp_tanggal)
                    @for ($b = 1; $b <= $a; $b++)
                        <td colspan="4" style="text-align: center; border: 1px solid black; background-color: #EEEEEE; font-weight: bold">
                            {{-- {{ \Carbon\Carbon::parse($exp_tanggal)->isoFormat('D MMMM YYYY') }} --}}
                            {{ \Carbon\Carbon::parse($exp_tanggal)->format('d M Y') }}
                        </td>
                    @endfor
                @endforeach
                <td rowspan="2" style="text-align: center; border: 1px solid black; background-color: #EEEEEE; font-weight: bold" valign="middle">Upah Hasil Kerja</td>
                <td colspan="7" style="text-align: center; border: 1px solid black; background-color: #EEEEEE; font-weight: bold">PLUS</td>
                <td rowspan="2" style="text-align: center; border: 1px solid black; background-color: #EEEEEE; font-weight: bold" valign="middle">Total Gaji</td>
                <td colspan="5" style="text-align: center; border: 1px solid black; background-color: #EEEEEE; font-weight: bold">Potongan</td>
                <td rowspan="2" style="text-align: center; border: 1px solid black; background-color: #EEEEEE; font-weight: bold" valign="middle">Upah Diterima</td>
            </tr>
            <tr>
                @foreach ($exp_tanggals as $exp_tanggal)
                    @for ($j = 1; $j <= $a; $j++)
                        <td style="text-align: center; border: 1px solid black; background-color: #EEEEEE; font-weight: bold">Jenis</td>
                        <td style="text-align: center; border: 1px solid black; background-color: #EEEEEE; font-weight: bold">Hasil</td>
                        <td style="text-align: center; border: 1px solid black; background-color: #EEEEEE; font-weight: bold">Nominal</td>
                        <td style="text-align: center; border: 1px solid black; background-color: #EEEEEE; font-weight: bold">Jam</td>
                    @endfor
                @endforeach
                <td style="text-align: center; border: 1px solid black; background-color: #EEEEEE; font-weight: bold">Plus 1</td>
                <td style="text-align: center; border: 1px solid black; background-color: #EEEEEE; font-weight: bold">Plus 2</td>
                <td style="text-align: center; border: 1px solid black; background-color: #EEEEEE; font-weight: bold">Plus 3</td>
                <td style="text-align: center; border: 1px solid black; background-color: #EEEEEE; font-weight: bold">Uang Makan</td>
                <td style="text-align: center; border: 1px solid black; background-color: #EEEEEE; font-weight: bold">Tunj.Kerja</td>
                <td style="text-align: center; border: 1px solid black; background-color: #EEEEEE; font-weight: bold">Kehadiran</td>
                <td style="text-align: center; border: 1px solid black; background-color: #EEEEEE; font-weight: bold">Total Plus</td>
                <td style="text-align: center; border: 1px solid black; background-color: #EEEEEE; font-weight: bold">BPJS Ketenagakerjaan</td>
                <td style="text-align: center; border: 1px solid black; background-color: #EEEEEE; font-weight: bold">BPJS Kesehatan</td>
                {{-- <td style="text-align: center; border: 1px solid black; background-color: #EEEEEE; font-weight: bold">Pensiun</td> --}}
                <td style="text-align: center; border: 1px solid black; background-color: #EEEEEE; font-weight: bold">Minus 1</td>
                <td style="text-align: center; border: 1px solid black; background-color: #EEEEEE; font-weight: bold">Minus 2</td>
                <td style="text-align: center; border: 1px solid black; background-color: #EEEEEE; font-weight: bold">Total Potongan</td>
            </tr>
        </thead>
        <tbody id="myTable">
            @php
                $total_jumlah_kerja_1 = [];
                $total_nominal_kerja_1 = [];
                $total_jam_kerja_1 = [];
                
                $operator_karyawan_id = [];
                $total_hasil_karyawan_kerja = [];
                // $total_all_hasil_kerja = [];
                $total_row_upah_hasil_kerja = [];
                $total_row_plus_1 = [];
                $total_row_plus_2 = [];
                $total_row_plus_3 = [];
                $total_row_uang_makan = [];
                $total_row_tunj_kerja = [];
                $total_row_tunj_kehadiran = [];
                $total_row_all_plus = [];
                $total_row_gaji = [];
                $total_row_jht = [];
                $total_row_bpjs_kesehatan = [];
                $total_row_pensiun = [];
                $total_row_minus_1 = [];
                $total_row_minus_2 = [];
                $total_row_all_potongan = [];
                $total_row_upah_diterima = [];
            @endphp
            @foreach ($pengerjaan_borongan_weeklys as $key => $pengerjaan_borongan_weekly)
                @php
                    $upah_hasil_kerja = [];
                    $total_kerja_hasil = [];
                    array_push($total_hasil_karyawan_kerja,$key);
                    array_push($operator_karyawan_id, $pengerjaan_borongan_weekly->operator_karyawan_id);
                    
                    if ($kode_id == 1) {
                        $kode_jenis_operator_detail = 'L';
                    } elseif ($kode_id == 2) {
                        $kode_jenis_operator_detail = 'E';
                    } elseif ($kode_id == 3) {
                        $kode_jenis_operator_detail = 'A';
                    }
                    // $jenis_operator_detail_pekerjaan = \App\Models\JenisOperatorDetailPengerjaan::where('id',$pengerjaan_borongan_weekly->operator_karyawan->jenis_operator_detail_pekerjaan_id)->first();
                    // dd($jenis_operator_detail_pekerjaan);
                    $hasil_pengerjaans = \App\Models\Pengerjaan::where('kode_payrol', substr($pengerjaan_borongan_weekly->kode_payrol, 0, 2) . $kode_jenis_operator_detail . substr($pengerjaan_borongan_weekly->kode_payrol, 3))
                        ->where('operator_karyawan_id', $pengerjaan_borongan_weekly->operator_karyawan_id)
                        ->get();
                    // dd($hasil_pengerjaans);
                @endphp
                <tr>
                    <td style="text-align: center; border: 1px solid black">{{ $key + 1 }}</td>
                    <td style="text-align: center; border: 1px solid black">{{ $pengerjaan_borongan_weekly->nik }}</td>
                    <td style="border: 1px solid black">{{ $pengerjaan_borongan_weekly->nama }}</td>
                    @foreach ($hasil_pengerjaans as $keys_hasil_pengerjaan => $hasil_pengerjaan)
                        @php
                            $column_total = [];
                            // $total_hasil_kerjas_1 = [];
                            // dd($total_all_hasil_kerja);
                            // array_push($total_hasil_karyawan_kerja,$keys_hasil_pengerjaan);
                        @endphp
                        @foreach ($exp_tanggals as $keys => $exp_tanggal)
                            @php
                                array_push($column_total, $keys);
                            @endphp
                            @php
                                $explode_hasil_kerja = explode('|', $hasil_pengerjaan['hasil_kerja_' . $keys]);
                                if ($kode_id == 1) {
                                    $umk_borongan_lokal = \App\Models\UMKBoronganLokal::select('id', 'jenis_produk', 'umk_packing', 'umk_bandrol', 'umk_inner', 'umk_outer')
                                                                                    ->where('id', $explode_hasil_kerja[0])
                                                                                    ->first();
                                    if (empty($umk_borongan_lokal)) {
                                        $jenis_produk = '-';
                                        $hasil_kerja = 0;
                                        $data_explode_hasil_kerja = '-';
                                        $lembur = 1;
                                        $total_hasil_kerja = 0;
                                        $total_jam = 0;
                                    } else {
                                        if ($hasil_pengerjaan->operator_karyawan->jenis_operator_detail_pekerjaan_id == 1) {
                                            $jenis_produk = $umk_borongan_lokal->jenis_produk;
                                            $hasil_kerja = $explode_hasil_kerja[1];
                                            // dd($hasil_kerja);
                                            // $total_hasil_kerja = $explode_hasil_kerja[1]*$umk_borongan_lokal->umk_packing;
                                            $total_jam = $hasil_pengerjaan['total_jam_kerja_' . $keys];
                                    
                                            // // $data_explode_hasil_kerja = $explode_hasil_kerja[1];
                                            $explode_lembur = explode('|', $hasil_pengerjaan->lembur);
                                            $explode_status_lembur = explode('-', $explode_lembur[$keys]);
                                            // echo json_encode($hs);
                                            if ($explode_status_lembur[1] == 'y') {
                                                $lembur = 1.5;
                                            } else {
                                                $lembur = 1;
                                            }
    
                                            $total_hasil_kerja = $explode_hasil_kerja[1] * $umk_borongan_lokal->umk_packing*$lembur;
                                        }
                                        
                                        if ($hasil_pengerjaan->operator_karyawan->jenis_operator_detail_pekerjaan_id == 2) {
                                            $jenis_produk = $umk_borongan_lokal->jenis_produk;
                                            $hasil_kerja = $explode_hasil_kerja[1];
                                    
                                            $total_jam = $hasil_pengerjaan['total_jam_kerja_' . $keys];
                                    
                                            $explode_lembur = explode('|', $hasil_pengerjaan->lembur);
                                            $explode_status_lembur = explode('-', $explode_lembur[$keys]);
                                    
                                            if ($explode_status_lembur[1] == 'y') {
                                                $lembur = 1.5;
                                            } else {
                                                $lembur = 1;
                                            }
    
                                            $total_hasil_kerja = $explode_hasil_kerja[1] * $umk_borongan_lokal->umk_bandrol*$lembur;
    
                                        }
                                        
                                        if ($hasil_pengerjaan->operator_karyawan->jenis_operator_detail_pekerjaan_id == 3) {
                                            $jenis_produk = $umk_borongan_lokal->jenis_produk;
                                            $hasil_kerja = $explode_hasil_kerja[1];
                                    
                                            $total_jam = $hasil_pengerjaan['total_jam_kerja_' . $keys];
                                    
                                            $explode_lembur = explode('|', $hasil_pengerjaan->lembur);
                                            $explode_status_lembur = explode('-', $explode_lembur[$keys]);
                                    
                                            if ($explode_status_lembur[1] == 'y') {
                                                $lembur = 1.5;
                                            } else {
                                                $lembur = 1;
                                            }
    
                                            $total_hasil_kerja = $explode_hasil_kerja[1] * $umk_borongan_lokal->umk_inner*$lembur;
    
                                        }
                                        
                                        if ($hasil_pengerjaan->operator_karyawan->jenis_operator_detail_pekerjaan_id == 4) {
                                            $jenis_produk = $umk_borongan_lokal->jenis_produk;
                                            $hasil_kerja = $explode_hasil_kerja[1];
                                    
                                            $total_jam = $hasil_pengerjaan['total_jam_kerja_' . $keys];
                                    
                                            $explode_lembur = explode('|', $hasil_pengerjaan->lembur);
                                            $explode_status_lembur = explode('-', $explode_lembur[$keys]);
                                    
                                            if ($explode_status_lembur[1] == 'y') {
                                                $lembur = 1.5;
                                            } else {
                                                $lembur = 1;
                                            }
    
                                            $total_hasil_kerja = $explode_hasil_kerja[1] * $umk_borongan_lokal->umk_outer*$lembur;
    
                                        }

                                        if ($hasil_pengerjaan->operator_karyawan->jenis_operator_detail_pekerjaan_id == 25) {
                                            $umk_borongan_lokal_stempel = \App\Models\UMKBoronganStempel::select('id', 'jenis_produk', 'nominal_umk')
                                                                                                ->where('id', $explode_hasil_kerja[0])
                                                                                                ->first();

                                            if (empty($umk_borongan_lokal_stempel)) {
                                                $jenis_produk = '-';
                                                $hasil_kerja = 0;
                                                $data_explode_hasil_kerja = '-';
                                                $lembur = 1;
                                                $total_hasil_kerja = 0;
                                                $total_jam = 0;
                                            }else{
                                                $jenis_produk = $umk_borongan_lokal_stempel->jenis_produk;
                                                $hasil_kerja = $explode_hasil_kerja[1];
                                                $total_jam = $hasil_pengerjaan['total_jam_kerja_' . $keys];
                                                $explode_lembur = explode('|', $hasil_pengerjaan->lembur);
                                                $explode_status_lembur = explode('-', $explode_lembur[$keys]);
                                        
                                                if ($explode_status_lembur[1] == 'y') {
                                                    $lembur = 1.5;
                                                } else {
                                                    $lembur = 1;
                                                }

                                                $total_hasil_kerja = $explode_hasil_kerja[1] * $umk_borongan_lokal_stempel->nominal_umk*$lembur;
                                            }
                                        }
                                        
                                    }

                                }elseif($kode_id == 2){
                                    $umk_borongan_ekspor = \App\Models\UMKBoronganEkspor::select('id', 'jenis_produk', 'umk_packing', 'umk_kemas', 'umk_pilih_gagang')
                                                                                    ->where('id', $explode_hasil_kerja[0])
                                                                                    ->first();
                                    if (empty($umk_borongan_ekspor)) {
                                        $jenis_produk = '-';
                                        $hasil_kerja = 0;
                                        $data_explode_hasil_kerja = '-';
                                        $lembur = 1;
                                        $total_hasil_kerja = 0;
                                        $total_jam = 0;
                                    }else{
                                        if ($hasil_pengerjaan->operator_karyawan->jenis_operator_detail_pekerjaan_id == 5) {
                                            $jenis_produk = $umk_borongan_ekspor->jenis_produk;
                                            $hasil_kerja = $explode_hasil_kerja[1];
                                            $total_jam = $hasil_pengerjaan['total_jam_kerja_' . $keys];
                                            $explode_lembur = explode('|', $hasil_pengerjaan->lembur);
                                            $explode_status_lembur = explode('-', $explode_lembur[$keys]);
                                            if ($explode_status_lembur[1] == 'y') {
                                                $lembur = 1.5;
                                            } else {
                                                $lembur = 1;
                                            }
                                            $total_hasil_kerja = $explode_hasil_kerja[1] * $umk_borongan_ekspor->umk_packing*$lembur;
                                        }

                                        if ($hasil_pengerjaan->operator_karyawan->jenis_operator_detail_pekerjaan_id == 6) {
                                            $jenis_produk = $umk_borongan_ekspor->jenis_produk;
                                            $hasil_kerja = $explode_hasil_kerja[1];
                                            $total_jam = $hasil_pengerjaan['total_jam_kerja_' . $keys];
                                            $explode_lembur = explode('|', $hasil_pengerjaan->lembur);
                                            $explode_status_lembur = explode('-', $explode_lembur[$keys]);
                                            if ($explode_status_lembur[1] == 'y') {
                                                $lembur = 1.5;
                                            } else {
                                                $lembur = 1;
                                            }
                                            $total_hasil_kerja = $explode_hasil_kerja[1] * $umk_borongan_ekspor->umk_kemas*$lembur;
                                        }

                                        if ($hasil_pengerjaan->operator_karyawan->jenis_operator_detail_pekerjaan_id == 7) {
                                            $jenis_produk = $umk_borongan_ekspor->jenis_produk;
                                            $hasil_kerja = $explode_hasil_kerja[1];
                                            $total_jam = $hasil_pengerjaan['total_jam_kerja_' . $keys];
                                            $explode_lembur = explode('|', $hasil_pengerjaan->lembur);
                                            $explode_status_lembur = explode('-', $explode_lembur[$keys]);
                                            if ($explode_status_lembur[1] == 'y') {
                                                $lembur = 1.5;
                                            } else {
                                                $lembur = 1;
                                            }
                                            $total_hasil_kerja = $explode_hasil_kerja[1] * $umk_borongan_ekspor->umk_pilih_gagang*$lembur;
                                        }
                                    }
                                }elseif ($kode_id == 3) {
                                    // dd($kode_id);
                                    $umk_borongan_ambri = \App\Models\UMKBoronganAmbri::select('id', 'jenis_produk', 'umk_etiket', 'umk_las_tepi', 'umk_las_pojok','umk_ambri')
                                                                                    ->where('id', $explode_hasil_kerja[0])
                                                                                    ->first();
                                    // dd($umk_borongan_ambri);
                                    if (empty($umk_borongan_ambri)) {
                                        $jenis_produk = '-';
                                        $hasil_kerja = 0;
                                        $data_explode_hasil_kerja = '-';
                                        $lembur = 1;
                                        $total_hasil_kerja = 0;
                                        $total_jam = 0;
                                        // dd($jenis_produk);
                                    }else{
                                        if ($hasil_pengerjaan->operator_karyawan->jenis_operator_detail_pekerjaan_id == 8) {
                                            $jenis_produk = $umk_borongan_ambri->jenis_produk;
                                            $hasil_kerja = $explode_hasil_kerja[1];
                                            $total_jam = $hasil_pengerjaan['total_jam_kerja_' . $keys];
                                            $explode_lembur = explode('|', $hasil_pengerjaan->lembur);
                                            $explode_status_lembur = explode('-', $explode_lembur[$keys]);
                                            if ($explode_status_lembur[1] == 'y') {
                                                $lembur = 1.5;
                                            } else {
                                                $lembur = 1;
                                            }
                                            $total_hasil_kerja = $explode_hasil_kerja[1] * $umk_borongan_ambri->umk_etiket*$lembur;
                                        }

                                        if ($hasil_pengerjaan->operator_karyawan->jenis_operator_detail_pekerjaan_id == 9) {
                                            $jenis_produk = $umk_borongan_ambri->jenis_produk;
                                            $hasil_kerja = $explode_hasil_kerja[1];
                                            $total_jam = $hasil_pengerjaan['total_jam_kerja_' . $keys];
                                            $explode_lembur = explode('|', $hasil_pengerjaan->lembur);
                                            $explode_status_lembur = explode('-', $explode_lembur[$keys]);
                                            if ($explode_status_lembur[1] == 'y') {
                                                $lembur = 1.5;
                                            } else {
                                                $lembur = 1;
                                            }
                                            $total_hasil_kerja = $explode_hasil_kerja[1] * $umk_borongan_ambri->umk_las_tepi*$lembur;
                                        }

                                        if ($hasil_pengerjaan->operator_karyawan->jenis_operator_detail_pekerjaan_id == 10) {
                                            $jenis_produk = $umk_borongan_ambri->jenis_produk;
                                            $hasil_kerja = $explode_hasil_kerja[1];
                                            $total_jam = $hasil_pengerjaan['total_jam_kerja_' . $keys];
                                            $explode_lembur = explode('|', $hasil_pengerjaan->lembur);
                                            $explode_status_lembur = explode('-', $explode_lembur[$keys]);
                                            if ($explode_status_lembur[1] == 'y') {
                                                $lembur = 1.5;
                                            } else {
                                                $lembur = 1;
                                            }
                                            $total_hasil_kerja = $explode_hasil_kerja[1] * $umk_borongan_ambri->umk_las_pojok*$lembur;
                                        }

                                        if ($hasil_pengerjaan->operator_karyawan->jenis_operator_detail_pekerjaan_id == 11) {
                                            $jenis_produk = $umk_borongan_ambri->jenis_produk;
                                            $hasil_kerja = $explode_hasil_kerja[1];
                                            $total_jam = $hasil_pengerjaan['total_jam_kerja_' . $keys];
                                            $explode_lembur = explode('|', $hasil_pengerjaan->lembur);
                                            $explode_status_lembur = explode('-', $explode_lembur[$keys]);
                                            if ($explode_status_lembur[1] == 'y') {
                                                $lembur = 1.5;
                                            } else {
                                                $lembur = 1;
                                            }
                                            $total_hasil_kerja = $explode_hasil_kerja[1]*$umk_borongan_ambri->umk_ambri*$lembur;
                                        }
                                    }
                                }
                                // $hasil_upah = $total_hasil_kerja * $lembur;
                                // dd($total_hasil_kerja);
                                // $hasil_upah = round($total_hasil_kerja);
                                $hasil_upah = $total_hasil_kerja;
                                array_push($upah_hasil_kerja, round($hasil_upah));
                                // array_push($total_hasil_kerjas_1,$hasil_kerja);
                                // dd($total_hasil_kerjas_1);
                                // echo json_encode($hasil_kerja);
                                // array_push($total_kerja_hasil,$hasil_kerja);
                                // $explode_hasil_kerja_1 = explode('|', $hasil_pengerjaan['hasil_kerja_1']);
                                // if (empty($explode_hasil_kerja_1)) {
                                //     $total_hasil_kerja_1 = 0;
                                // } else {
                                //     if (empty($explode_hasil_kerja_1[1])) {
                                //         $total_hasil_kerja_1 = 0;
                                //     } else {
                                //         $total_hasil_kerja_1 = $explode_hasil_kerja_1[1];
                                //     }
                                // }
                                // array_push($total_hasil_kerjas_1,$total_hasil_kerja_1);
                                // dd($explode_hasil_kerja_1);
                                // $total_hasil_kerjas_1 = $total_hasil_kerja_1;
                                // dd($total_hasil_kerjas_1);
                                // array_push($total_hasil_kerjas_1,$total_hasil_kerja_1);
                                // dd($total_hasil_kerjas_1);

                                // array_push($total_all_hasil_kerja,$hasil_kerja);
                            @endphp
                            <td style="text-align: center; border: 1px solid black">{{ $jenis_produk }}</td>
                            <td class="td_hasil_kerja" style="text-align: right; border: 1px solid black">{{ $hasil_kerja }}</td>
                            <td class="td_hasil_kerja" style="text-align: right; border: 1px solid black">{{ round($total_hasil_kerja) }}</td>
                            {{-- <td class="td_hasil_kerja" style="text-align: right; border: 1px solid black">{{ round($total_hasil_kerja) }}</td> --}}
                            <td class="td_hasil_kerja" style="text-align: center; border: 1px solid black">{{ $total_jam }}</td>
                        @endforeach
                    @endforeach
                    @php
                        // echo json_encode($column_total);
                        $hasil_total_upah = array_sum($upah_hasil_kerja);

                        // dd($hasil_total_upah);
                        // array_push($total_all_hasil_kerja,$hasil_total_upah);
                        // echo json_encode(array_sum($total_kerja_hasil));
                        if (empty($pengerjaan_borongan_weekly->uang_makan)) {
                            $uang_makan = 0;
                        } else {
                            $uang_makan = $pengerjaan_borongan_weekly->uang_makan;
                        }
                        
                        // if (empty($pengerjaan_borongan_weekly->tunjangan_kerja)) {
                        //     $tunjangan_kerja = 0;
                        // } else {
                        //     $tunjangan_kerja = $pengerjaan_borongan_weekly->tunjangan_kerja;
                        // }
                        $tunjangan_kerja = 0;
                        
                        if (empty($pengerjaan_borongan_weekly->tunjangan_kehadiran)) {
                            $tunjangan_kehadiran = 0;
                        } else {
                            $tunjangan_kehadiran = $pengerjaan_borongan_weekly->tunjangan_kehadiran;
                        }
                        
                        $explode_plus_1 = explode('|', $pengerjaan_borongan_weekly->plus_1);
                        $explode_plus_2 = explode('|', $pengerjaan_borongan_weekly->plus_2);
                        $explode_plus_3 = explode('|', $pengerjaan_borongan_weekly->plus_3);
                        
                        if ($explode_plus_1[0] == '') {
                            $plus_1 = 0;
                            $keterangan_1 = null;
                        } else {
                            $plus_1 = $explode_plus_1[0];
                            $keterangan_1 = $explode_plus_1[1];
                        }
                        // dd($plus_1);
                        
                        if ($explode_plus_2[0] == '') {
                            $plus_2 = 0;
                            $keterangan_2 = null;
                        } else {
                            $plus_2 = $explode_plus_2[0];
                            $keterangan_2 = $explode_plus_2[1];
                        }
                        
                        if ($explode_plus_3[0] == '') {
                            $plus_3 = 0;
                            $keterangan_3 = null;
                        } else {
                            $plus_3 = $explode_plus_3[0];
                            $keterangan_3 = $explode_plus_3[1];
                        }

                        // $keterangan_plus = $keterangan_1.'|'.$keterangan_2.'|'.$keterangan_3;

                        if ($new_data_pengerjaan->akhir_bulan == 'y') {
                            $plus_tunjangan_kerja = $tunjangan_kerja;
                            $plus_tunjangan_kehadiran = $tunjangan_kehadiran;
                        }else{
                            $plus_tunjangan_kerja = 0;
                            $plus_tunjangan_kehadiran = 0;
                        }
                        
                        $total_plus = $plus_1 + $plus_2 + $plus_3 + $uang_makan + $plus_tunjangan_kerja + $plus_tunjangan_kehadiran;
                        
                        $total_gaji = $hasil_total_upah + $total_plus;
                        
                        $explode_minus_1 = explode('|', $pengerjaan_borongan_weekly->minus_1);
                        $explode_minus_2 = explode('|', $pengerjaan_borongan_weekly->minus_2);
                        
                        if ($explode_minus_1[0] == '') {
                            $minus_1 = 0;
                        } else {
                            $minus_1 = $explode_minus_1[0];
                        }
                        
                        if ($explode_minus_2[0] == '') {
                            $minus_2 = 0;
                        } else {
                            $minus_2 = $explode_minus_2[0];
                        }
                        
                        if (empty($pengerjaan_borongan_weekly->jht)) {
                            $jht = 0;
                        } else {
                            $jht = $pengerjaan_borongan_weekly->jht;
                        }
                        
                        if (empty($pengerjaan_borongan_weekly->bpjs_kesehatan)) {
                            $bpjs_kesehatan = 0;
                        } else {
                            $bpjs_kesehatan = $pengerjaan_borongan_weekly->bpjs_kesehatan;
                        }
                        
                        $total_potongan = $bpjs_kesehatan + $jht + $minus_1 + $minus_2;
                        
                        $upah_dterima = $total_gaji - $total_potongan;
                        
                        array_push($total_row_upah_hasil_kerja,round($hasil_total_upah));
                        array_push($total_row_plus_1,round($plus_1));
                        array_push($total_row_plus_2,round($plus_2));
                        array_push($total_row_plus_3,round($plus_3));
                        array_push($total_row_uang_makan,round($uang_makan));
                        array_push($total_row_tunj_kerja,round($tunjangan_kerja));
                        array_push($total_row_tunj_kehadiran,round($tunjangan_kehadiran));
                        array_push($total_row_all_plus,round($total_plus));
                        array_push($total_row_gaji,round($total_gaji));
                        array_push($total_row_jht,round($jht));
                        array_push($total_row_bpjs_kesehatan,round($bpjs_kesehatan));
                        array_push($total_row_pensiun,0);
                        array_push($total_row_minus_1,round($minus_1));
                        array_push($total_row_minus_2,round($minus_2));
                        array_push($total_row_all_potongan,round($total_potongan));
                        array_push($total_row_upah_diterima,round($upah_dterima));
                        // array_push($total_row_upah_diterima,$upah_dterima);
                        // echo json_encode(array_sum($upah_hasil_kerja));
                        
                    @endphp
                    <td style="text-align: right; border: 1px solid black">{{ round($hasil_total_upah) }}</td>
                    <td style="text-align: right; border: 1px solid black">{{ round($plus_1) }}</td>
                    <td style="text-align: right; border: 1px solid black">{{ round($plus_2) }}</td>
                    <td style="text-align: right; border: 1px solid black">{{ round($plus_3) }}</td>
                    <td style="text-align: right; border: 1px solid black">{{ round($uang_makan) }}</td>
                    <td style="text-align: right; border: 1px solid black">{{ round($tunjangan_kerja) }}</td>
                    <td style="text-align: right; border: 1px solid black">{{ round($tunjangan_kehadiran) }}</td>
                    <td style="text-align: right; border: 1px solid black">{{ round($total_plus) }}</td>
                    <td style="text-align: right; border: 1px solid black">{{ round($total_gaji) }}</td>
                    <td style="text-align: right; border: 1px solid black">{{ round($jht) }}</td>
                    <td style="text-align: right; border: 1px solid black">{{ round($bpjs_kesehatan) }}</td>
                    {{-- <td style="text-align: right; border: 1px solid black">Rp. 0</td> --}}
                    <td style="text-align: right; border: 1px solid black">{{ round($minus_1) }}</td>
                    <td style="text-align: right; border: 1px solid black">{{ round($minus_2) }}</td>
                    <td style="text-align: right; border: 1px solid black">{{ round($total_potongan) }}</td>
                    {{-- <td style="text-align: right; border: 1px solid black">{{ $upah_dterima }}</td> --}}
                    <td style="text-align: right; border: 1px solid black">{{ round($upah_dterima) }}</td>
                    <td style="text-align: right;">{{ $keterangan_1 }}</td>
                    <td style="text-align: right;">{{ $keterangan_2 }}</td>
                    <td style="text-align: right;">{{ $keterangan_3 }}</td>
                </tr>
            @endforeach
            {{-- <tr></tr>
            <tr>
                <td colspan="8" style="position: relative"><img src="{{ public_path('itic/Tanda_Tangan_Payroll.jpg') }}" style="position: absolute" width="700" alt="" srcset=""></td>
            </tr> --}}
        </tbody>
        @php
        // dd($operator_karyawan_id);
        @endphp
        <tfoot>
            <tr>
                <td colspan="3" style="text-align: center; border: 1px solid black; font-weight: bold">TOTAL</td>
                @php
                    if ($kode_id == 1) {
                        $kode_jenis_operator_detail = 'L';
                    } elseif ($kode_id == 2) {
                        $kode_jenis_operator_detail = 'E';
                    } elseif ($kode_id == 3) {
                        $kode_jenis_operator_detail = 'A';
                    }
                @endphp

                @foreach ($exp_tanggals as $et => $exp_tanggal)
                @for ($t = 1; $t <= $a; $t++)

                @php
                    $hasil_pengerjaans = \App\Models\Pengerjaan::whereIn('operator_karyawan_id',$operator_karyawan_id)
                                                                ->where('tanggal_pengerjaan',$exp_tanggal)
                                                                ->where('kode_payrol', substr($kode_pengerjaan, 0, 2) . $kode_jenis_operator_detail .'_'. substr($kode_pengerjaan, 3))
                                                                ->get();
                    // dd($hasil_pengerjaans);
                    $jenis_row = [];
                    $total_row_hasil_kerja = [];
                    $total_row_nominal = [];
                    $total_row_jam = [];
                    foreach ($hasil_pengerjaans as $hasil_pengerjaan) {
                        $explode_hasil_kerja = explode('|', $hasil_pengerjaan['hasil_kerja_'.$t]);
                        if ($kode_id == 1) {
                            $umk_borongan_lokal = \App\Models\UMKBoronganLokal::select('id', 'jenis_produk', 'umk_packing', 'umk_bandrol', 'umk_inner', 'umk_outer')
                                                                                ->where('id', $explode_hasil_kerja[0])
                                                                                ->first();
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
                                    // dd($hasil_kerja);
                                    $total_jam = $hasil_pengerjaan['total_jam_kerja_' . $t];
                                    $explode_lembur = explode('|', $hasil_pengerjaan->lembur);
                                    $explode_status_lembur = explode('-', $explode_lembur[$t]);
                                    if ($explode_status_lembur[1] == 'y') {
                                        $lembur = 1.5;
                                    } else {
                                        $lembur = 1;
                                    }
                                    $total_hasil_kerja = $hasil_kerja*$umk_borongan_lokal->umk_packing*$lembur;
                                    // dd($total_hasil_kerja);
                                    // $total_hasil_kerja = $explode_hasil_kerja[1] * $umk_borongan_lokal->umk_packing*$lembur;
                                }
    
                                if ($hasil_pengerjaan->operator_karyawan->jenis_operator_detail_pekerjaan_id == 2) {
                                    $jenis_produk = $umk_borongan_lokal->jenis_produk;
                                    $hasil_kerja = $explode_hasil_kerja[1];
                                    $total_jam = $hasil_pengerjaan['total_jam_kerja_' . $t];
                                    $explode_lembur = explode('|', $hasil_pengerjaan->lembur);
                                    $explode_status_lembur = explode('-', $explode_lembur[$t]);
                                    if ($explode_status_lembur[1] == 'y') {
                                        $lembur = 1.5;
                                    } else {
                                        $lembur = 1;
                                    }
                                    $total_hasil_kerja = $explode_hasil_kerja[1] * $umk_borongan_lokal->umk_bandrol*$lembur;
                                }
    
                                if ($hasil_pengerjaan->operator_karyawan->jenis_operator_detail_pekerjaan_id == 3) {
                                    $jenis_produk = $umk_borongan_lokal->jenis_produk;
                                    $hasil_kerja = $explode_hasil_kerja[1];
                                    $total_jam = $hasil_pengerjaan['total_jam_kerja_' . $t];
                                    $explode_lembur = explode('|', $hasil_pengerjaan->lembur);
                                    $explode_status_lembur = explode('-', $explode_lembur[$t]);
                                    if ($explode_status_lembur[1] == 'y') {
                                        $lembur = 1.5;
                                    } else {
                                        $lembur = 1;
                                    }
                                    $total_hasil_kerja = $explode_hasil_kerja[1] * $umk_borongan_lokal->umk_inner*$lembur;
                                }
    
                                if ($hasil_pengerjaan->operator_karyawan->jenis_operator_detail_pekerjaan_id == 4) {
                                    $jenis_produk = $umk_borongan_lokal->jenis_produk;
                                    $hasil_kerja = $explode_hasil_kerja[1];
                                    $total_jam = $hasil_pengerjaan['total_jam_kerja_' . $t];
                                    $explode_lembur = explode('|', $hasil_pengerjaan->lembur);
                                    $explode_status_lembur = explode('-', $explode_lembur[$t]);
                                    if ($explode_status_lembur[1] == 'y') {
                                        $lembur = 1.5;
                                    } else {
                                        $lembur = 1;
                                    }
                                    $total_hasil_kerja = $explode_hasil_kerja[1] * $umk_borongan_lokal->umk_outer*$lembur;
                                }

                                if ($hasil_pengerjaan->operator_karyawan->jenis_operator_detail_pekerjaan_id == 25) {
                                    $umk_borongan_lokal_stempel = \App\Models\UMKBoronganStempel::select('id', 'jenis_produk', 'nominal_umk')
                                                                                                ->where('id', $explode_hasil_kerja[0])
                                                                                                ->first();
                                                                                                
                                    if (empty($umk_borongan_lokal_stempel)) {
                                        $jenis_produk = '-';
                                        $hasil_kerja = 0;
                                        $data_explode_hasil_kerja = '-';
                                        $lembur = 1;
                                        $total_hasil_kerja = 0;
                                        $total_jam = 0;
                                    }else{
                                        $jenis_produk = $umk_borongan_lokal_stempel->jenis_produk;
                                        $hasil_kerja = $explode_hasil_kerja[1];
                                        $total_jam = $hasil_pengerjaan['total_jam_kerja_' . $t];
                                        $explode_lembur = explode('|', $hasil_pengerjaan->lembur);
                                        $explode_status_lembur = explode('-', $explode_lembur[$t]);
                                
                                        if ($explode_status_lembur[1] == 'y') {
                                            $lembur = 1.5;
                                        } else {
                                            $lembur = 1;
                                        }

                                        $total_hasil_kerja = $hasil_kerja * $umk_borongan_lokal_stempel->nominal_umk*$lembur;
                                    }
                                }
                            }

                        }

                        if ($kode_id == 2) {
                            $umk_borongan_ekspor = \App\Models\UMKBoronganEkspor::select('id', 'jenis_produk', 'umk_packing', 'umk_kemas', 'umk_pilih_gagang')
                                                                                ->where('id', $explode_hasil_kerja[0])
                                                                                ->first();
                            if (empty($umk_borongan_ekspor)) {
                                $jenis_produk = '-';
                                $hasil_kerja = 0;
                                $data_explode_hasil_kerja = '-';
                                $lembur = 1;
                                $total_hasil_kerja = 0;
                                $total_jam = 0;
                            }else{
                                if ($hasil_pengerjaan->operator_karyawan->jenis_operator_detail_pekerjaan_id == 5) {
                                    $jenis_produk = $umk_borongan_ekspor->jenis_produk;
                                    $hasil_kerja = $explode_hasil_kerja[1];
                                    $total_jam = $hasil_pengerjaan['total_jam_kerja_' . $t];
                                    $explode_lembur = explode('|', $hasil_pengerjaan->lembur);
                                    $explode_status_lembur = explode('-', $explode_lembur[$t]);
                                    if ($explode_status_lembur[1] == 'y') {
                                        $lembur = 1.5;
                                    } else {
                                        $lembur = 1;
                                    }
                                    $total_hasil_kerja = $explode_hasil_kerja[1] * $umk_borongan_ekspor->umk_packing*$lembur;
                                }

                                if ($hasil_pengerjaan->operator_karyawan->jenis_operator_detail_pekerjaan_id == 6) {
                                    $jenis_produk = $umk_borongan_ekspor->jenis_produk;
                                    $hasil_kerja = $explode_hasil_kerja[1];
                                    $total_jam = $hasil_pengerjaan['total_jam_kerja_' . $t];
                                    $explode_lembur = explode('|', $hasil_pengerjaan->lembur);
                                    $explode_status_lembur = explode('-', $explode_lembur[$t]);
                                    if ($explode_status_lembur[1] == 'y') {
                                        $lembur = 1.5;
                                    } else {
                                        $lembur = 1;
                                    }
                                    $total_hasil_kerja = $explode_hasil_kerja[1] * $umk_borongan_ekspor->umk_kemas*$lembur;
                                }

                                if ($hasil_pengerjaan->operator_karyawan->jenis_operator_detail_pekerjaan_id == 7) {
                                    $jenis_produk = $umk_borongan_ekspor->jenis_produk;
                                    $hasil_kerja = $explode_hasil_kerja[1];
                                    $total_jam = $hasil_pengerjaan['total_jam_kerja_' . $t];
                                    $explode_lembur = explode('|', $hasil_pengerjaan->lembur);
                                    $explode_status_lembur = explode('-', $explode_lembur[$t]);
                                    if ($explode_status_lembur[1] == 'y') {
                                        $lembur = 1.5;
                                    } else {
                                        $lembur = 1;
                                    }
                                    $total_hasil_kerja = $explode_hasil_kerja[1] * $umk_borongan_ekspor->umk_pilih_gagang*$lembur;
                                }
                            }
                        }

                        if ($kode_id == 3) {
                            $umk_borongan_ambri = \App\Models\UMKBoronganAmbri::select('id', 'jenis_produk', 'umk_etiket', 'umk_las_tepi', 'umk_las_pojok','umk_ambri')
                                                                                ->where('id', $explode_hasil_kerja[0])
                                                                                ->first();
                            if (empty($umk_borongan_ambri)) {
                                $jenis_produk = '-';
                                $hasil_kerja = 0;
                                $data_explode_hasil_kerja = '-';
                                $lembur = 1;
                                $total_hasil_kerja = 0;
                                $total_jam = 0;
                            }else{
                                if ($hasil_pengerjaan->operator_karyawan->jenis_operator_detail_pekerjaan_id == 8) {
                                    $jenis_produk = $umk_borongan_ambri->jenis_produk;
                                    $hasil_kerja = $explode_hasil_kerja[1];
                                    $total_jam = $hasil_pengerjaan['total_jam_kerja_' . $t];
                                    $explode_lembur = explode('|', $hasil_pengerjaan->lembur);
                                    $explode_status_lembur = explode('-', $explode_lembur[$t]);
                                    if ($explode_status_lembur[1] == 'y') {
                                        $lembur = 1.5;
                                    } else {
                                        $lembur = 1;
                                    }
                                    $total_hasil_kerja = $explode_hasil_kerja[1] * $umk_borongan_ambri->umk_etiket*$lembur;
                                }

                                if ($hasil_pengerjaan->operator_karyawan->jenis_operator_detail_pekerjaan_id == 9) {
                                    $jenis_produk = $umk_borongan_ambri->jenis_produk;
                                    $hasil_kerja = $explode_hasil_kerja[1];
                                    $total_jam = $hasil_pengerjaan['total_jam_kerja_' . $t];
                                    $explode_lembur = explode('|', $hasil_pengerjaan->lembur);
                                    $explode_status_lembur = explode('-', $explode_lembur[$t]);
                                    if ($explode_status_lembur[1] == 'y') {
                                        $lembur = 1.5;
                                    } else {
                                        $lembur = 1;
                                    }
                                    $total_hasil_kerja = $explode_hasil_kerja[1] * $umk_borongan_ambri->umk_las_tepi*$lembur;
                                }

                                if ($hasil_pengerjaan->operator_karyawan->jenis_operator_detail_pekerjaan_id == 10) {
                                    $jenis_produk = $umk_borongan_ambri->jenis_produk;
                                    $hasil_kerja = $explode_hasil_kerja[1];
                                    $total_jam = $hasil_pengerjaan['total_jam_kerja_' . $t];
                                    $explode_lembur = explode('|', $hasil_pengerjaan->lembur);
                                    $explode_status_lembur = explode('-', $explode_lembur[$t]);
                                    if ($explode_status_lembur[1] == 'y') {
                                        $lembur = 1.5;
                                    } else {
                                        $lembur = 1;
                                    }
                                    $total_hasil_kerja = $explode_hasil_kerja[1] * $umk_borongan_ambri->umk_las_pojok*$lembur;
                                }

                                if ($hasil_pengerjaan->operator_karyawan->jenis_operator_detail_pekerjaan_id == 11) {
                                    $jenis_produk = $umk_borongan_ambri->jenis_produk;
                                    $hasil_kerja = $explode_hasil_kerja[1];
                                    $total_jam = $hasil_pengerjaan['total_jam_kerja_' . $t];
                                    $explode_lembur = explode('|', $hasil_pengerjaan->lembur);
                                    $explode_status_lembur = explode('-', $explode_lembur[$t]);
                                    if ($explode_status_lembur[1] == 'y') {
                                        $lembur = 1.5;
                                    } else {
                                        $lembur = 1;
                                    }
                                    $total_hasil_kerja = $explode_hasil_kerja[1] * $umk_borongan_ambri->umk_ambri*$lembur;
                                }
                            }
                        }

                        $jenis = $jenis_produk;
                        $hasil_upah = $total_hasil_kerja;
                        // dd($hasil_upah);
                        $total_jam_kerja = $total_jam;

                        array_push($jenis_row, $jenis);
                        array_push($total_row_hasil_kerja, $hasil_kerja);
                        array_push($total_row_nominal, $hasil_upah);
                        array_push($total_row_jam, $total_jam_kerja);
                    }

                    // if (empty($jenis_row)) {
                    //     $data_jenis_row = '-';
                    // }else{
                    //     $data_jenis_row = $jenis_row[0];
                    // }
                    // dd(array_sum($total_row_nominal));
                @endphp

                <td style="border: 1px solid black; font-weight: bold; text-align: center">{{ $jenis_row[0] }}</td>
                {{-- <td style="border: 1px solid black; font-weight: bold; text-align: center">{{ $data_jenis_row }}</td> --}}
                {{-- <td style="border: 1px solid black; font-weight: bold; text-align: center">{{ '+' }}</td> --}}
                <td style="border: 1px solid black; font-weight: bold; text-align: right">{{ round(array_sum($total_row_hasil_kerja)) }}</td>
                {{-- <td style="border: 1px solid black; font-weight: bold; text-align: right">{{ 1 }}</td> --}}

                {{-- <td style="border: 1px solid black; font-weight: bold; text-align: right">{{ round(array_sum($total_row_nominal)) }}</td> --}}
                <td style="border: 1px solid black; font-weight: bold; text-align: right">{{ round(array_sum($total_row_nominal)) }}</td>
                <td style="border: 1px solid black; font-weight: bold; text-align: center">{{ array_sum($total_row_jam) }}</td>
                
                @endfor
                @endforeach
                <td style="border: 1px solid black; font-weight: bold; text-align: right">{{ round(array_sum($total_row_upah_hasil_kerja)) }}</td>
                <td style="border: 1px solid black; font-weight: bold; text-align: right">{{ round(array_sum($total_row_plus_1)) }}</td>
                <td style="border: 1px solid black; font-weight: bold; text-align: right">{{ round(array_sum($total_row_plus_2)) }}</td>
                <td style="border: 1px solid black; font-weight: bold; text-align: right">{{ round(array_sum($total_row_plus_3)) }}</td>
                <td style="border: 1px solid black; font-weight: bold; text-align: right">{{ round(array_sum($total_row_uang_makan)) }}</td>
                <td style="border: 1px solid black; font-weight: bold; text-align: right">{{ round(array_sum($total_row_tunj_kerja)) }}</td>
                <td style="border: 1px solid black; font-weight: bold; text-align: right">{{ round(array_sum($total_row_tunj_kehadiran)) }}</td>
                <td style="border: 1px solid black; font-weight: bold; text-align: right">{{ round(array_sum($total_row_all_plus)) }}</td>
                <td style="border: 1px solid black; font-weight: bold; text-align: right">{{ round(array_sum($total_row_gaji)) }}</td>
                <td style="border: 1px solid black; font-weight: bold; text-align: right">{{ round(array_sum($total_row_jht)) }}</td>
                <td style="border: 1px solid black; font-weight: bold; text-align: right">{{ round(array_sum($total_row_bpjs_kesehatan)) }}</td>
                {{-- <td style="border: 1px solid black; font-weight: bold; text-align: right">{{ round(array_sum($total_row_pensiun)) }}</td> --}}
                <td style="border: 1px solid black; font-weight: bold; text-align: right">{{ round(array_sum($total_row_minus_1)) }}</td>
                <td style="border: 1px solid black; font-weight: bold; text-align: right">{{ round(array_sum($total_row_minus_2)) }}</td>
                <td style="border: 1px solid black; font-weight: bold; text-align: right">{{ round(array_sum($total_row_all_potongan)) }}</td>
                <td style="border: 1px solid black; font-weight: bold; text-align: right">{{ round(array_sum($total_row_upah_diterima)) }}</td>
            </tr>
            <tr></tr>
            <tr>
                <td colspan="3"></td>
                @foreach ($exp_tanggals as $et => $exp_tanggal)
                @for ($t = 1; $t <= $a; $t++)
                <td colspan="4" style="text-align: center"></td>
                @endfor
                @endforeach
                <td colspan="15" style="border: 1px solid black; font-weight: bold; text-align: right; font-size: 18pt">{{ number_format(array_sum($total_row_upah_diterima),0,',','.') }}</td>
            </tr>
        </tfoot>
    </table>
</body>
{{-- <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.5.1/jquery.min.js"
    integrity="sha512-bLT0Qm9VnAYZDflyKcBaQ2gg0hSYNQrJ8RilYldYQ1FxQYoCLtUjuuRuZo+fjqhx/qtq/1itJ0C2ejDxltZVFg=="
    crossorigin="anonymous" referrerpolicy="no-referrer"></script> --}}
</html>
