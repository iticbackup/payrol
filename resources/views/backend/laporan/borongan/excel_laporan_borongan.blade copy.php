<style>
    table,td,th {
        border: 1px solid;
    }

    td{
        vertical-align: middle;
    }

    table {
        width: 100%;
        border-collapse: collapse;
    }
</style>

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
            @foreach ($explode_tanggal_pengerjaans as $key => $explode_tanggal_pengerjaan)
            @if ($key != 0)
                <td colspan="4" style="text-align: center">{{ \Carbon\Carbon::parse($explode_tanggal_pengerjaan)->isoFormat('D MMMM YYYY') }}</td>
            @endif
            @endforeach
            {{-- <td colspan="4" style="text-align: center">Tanggal</td> --}}
            <td rowspan="2" style="text-align: center">Upah Hasil Kerja</td>
            <td colspan="7" style="text-align: center">PLUS</td>
            {{-- <tr>
                <td style="text-align: center">Plus 1</td>
                <td style="text-align: center">Plus 2</td>
                <td style="text-align: center">Plus 3</td>
                <td style="text-align: center">Uang Makan</td>
                <td style="text-align: center">Tunj.Kerja</td>
                <td style="text-align: center">Kehadiran</td>
                <td style="text-align: center">Total Plus</td>
            </tr> --}}
            <td rowspan="2" style="text-align: center">Total Gaji</td>
            <td colspan="6" style="text-align: center">Potongan</td>
            <td rowspan="2" style="text-align: center">Upah Diterima</td>
        </tr>
        <tr>
            @foreach ($explode_tanggal_pengerjaans as $key => $explode_tanggal_pengerjaan)
            @if ($key != 0)
            <td style="text-align: center">Jenis</td>
            <td style="text-align: center">Hasil Kerja</td>
            <td style="text-align: center">Total Nominal</td>
            <td style="text-align: center">Total Jam</td>
            @endif
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
    {{-- <tr>
        <td style="text-align: center">Jenis</td>
        <td style="text-align: center">Hasil Kerja</td>
        <td style="text-align: center">Total Nominal</td>
        <td style="text-align: center">Total Jam</td>
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
    </tr> --}}
    <tbody>
        @foreach ($pengerjaan_borongan_weeklys as $key => $pengerjaan_borongan_weekly)
        @php
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
                                                        ->where('operator_karyawan_id',$pengerjaan_borongan_weekly->operator_karyawan_id)
                                                        ->get();
            // dd($hasil_pengerjaans);
            $upah = array();
        @endphp
        <tr>
            <td style="text-align: center">{{ $key+1 }}</td>
            <td style="text-align: center">{{ $pengerjaan_borongan_weekly->nik }}</td>
            <td>{{ $pengerjaan_borongan_weekly->nama }}</td>
            {{-- @foreach ($hasil_pengerjaans as $hasil_pengerjaan)
                @php
                    $explode_hasil_kerja_1 = explode("|",$hasil_pengerjaan->hasil_kerja_1);
                    $umk_borongan_lokal_1 = \App\Models\UMKBoronganLokal::select('id','jenis_produk','umk_packing','umk_bandrol','umk_inner','umk_outer')->where('id',$explode_hasil_kerja_1[0])->where('status','Y')->first();

                    if(empty($umk_borongan_lokal_1)){
                        $jenis_produk_1 = '-';
                        $hasil_kerja_1 = 0;
                        $data_explode_hasil_kerja_1 = '-';
                        $lembur_1 = 1;
                    }else{
                        $jenis_produk_1 = $umk_borongan_lokal_1->jenis_produk;
                        $hasil_kerja_1 = $explode_hasil_kerja_1[1]*$umk_borongan_lokal_1->umk_packing;
                        $data_explode_hasil_kerja_1 = $explode_hasil_kerja_1[1];

                        $explode_lembur_1 = explode("|",$hasil_pengerjaan->lembur);
                        // dd($explode_lembur_1[1]);
                        $explode_status_lembur_1 = explode("-",$explode_lembur_1[1]);

                        if($explode_status_lembur_1[1] == 'y'){
                            $lembur_1 = 1.5;
                        }else{
                            $lembur_1 = 1;
                        }
                    }

                    $explode_hasil_kerja_2 = explode("|",$hasil_pengerjaan->hasil_kerja_2);
                    $umk_borongan_lokal_2 = \App\Models\UMKBoronganLokal::select('id','jenis_produk','umk_packing','umk_bandrol','umk_inner','umk_outer')->where('id',$explode_hasil_kerja_2[0])->where('status','Y')->first();
                    if(empty($umk_borongan_lokal_2)){
                        $jenis_produk_2 = '-';
                        $hasil_kerja_2 = 0;
                        $data_explode_hasil_kerja_2 = '-';
                        $lembur_2 = 1;
                    }else{
                        $jenis_produk_2 = $umk_borongan_lokal_2->jenis_produk;
                        $hasil_kerja_2 = $explode_hasil_kerja_2[1]*$umk_borongan_lokal_2->umk_packing;
                        $data_explode_hasil_kerja_2 = $explode_hasil_kerja_2[1];

                        $explode_lembur_2 = explode("|",$hasil_pengerjaan->lembur);
                        // dd($explode_lembur_2[2]);
                        $explode_status_lembur_2 = explode("-",$explode_lembur_2[2]);

                        if($explode_status_lembur_2[1] == 'y'){
                            $lembur_2 = 1.5;
                        }else{
                            $lembur_2 = 1;
                        }

                        // dd($hasil_kerja_2+$lembur_2);
                    }

                    $explode_hasil_kerja_3 = explode("|",$hasil_pengerjaan->hasil_kerja_3);
                    $umk_borongan_lokal_3 = \App\Models\UMKBoronganLokal::select('id','jenis_produk','umk_packing','umk_bandrol','umk_inner','umk_outer')->where('id',$explode_hasil_kerja_3[0])->where('status','Y')->first();
                    if(empty($umk_borongan_lokal_3)){
                        $jenis_produk_3 = '-';
                        $hasil_kerja_3 = 0;
                        $data_explode_hasil_kerja_3 = '-';
                        $lembur_3 = 1;
                    }else{
                        $jenis_produk_3 = $umk_borongan_lokal_3->jenis_produk;
                        $hasil_kerja_3 = $explode_hasil_kerja_3[1]*$umk_borongan_lokal_3->umk_packing;
                        $data_explode_hasil_kerja_3 = $explode_hasil_kerja_3[1];

                        $explode_lembur_3 = explode("|",$hasil_pengerjaan->lembur);
                        $explode_status_lembur_3 = explode("-",$explode_lembur_3[3]);

                        if($explode_status_lembur_3[1] == 'y'){
                            $lembur_3 = 1.5;
                        }else{
                            $lembur_3 = 1;
                        }
                    }

                    $explode_hasil_kerja_4 = explode("|",$hasil_pengerjaan->hasil_kerja_4);
                    $umk_borongan_lokal_4 = \App\Models\UMKBoronganLokal::select('id','jenis_produk','umk_packing','umk_bandrol','umk_inner','umk_outer')->where('id',$explode_hasil_kerja_4[0])->where('status','Y')->first();
                    if(empty($umk_borongan_lokal_4)){
                        $jenis_produk_4 = '-';
                        $hasil_kerja_4 = 0;
                        $data_explode_hasil_kerja_4 = '-';
                        $lembur_4 = 1;
                    }else{
                        $jenis_produk_4 = $umk_borongan_lokal_4->jenis_produk;
                        $hasil_kerja_4 = $explode_hasil_kerja_4[1]*$umk_borongan_lokal_4->umk_packing;
                        $data_explode_hasil_kerja_4 = $explode_hasil_kerja_4[1];

                        $explode_lembur_4 = explode("|",$hasil_pengerjaan->lembur);
                        $explode_status_lembur_4 = explode("-",$explode_lembur_4[4]);

                        if($explode_status_lembur_4[1] == 'y'){
                            $lembur_4 = 1.5;
                        }else{
                            $lembur_4 = 1;
                        }
                    }

                    $explode_hasil_kerja_5 = explode("|",$hasil_pengerjaan->hasil_kerja_5);
                    $umk_borongan_lokal_5 = \App\Models\UMKBoronganLokal::select('id','jenis_produk','umk_packing','umk_bandrol','umk_inner','umk_outer')->where('id',$explode_hasil_kerja_5[0])->where('status','Y')->first();
                    if(empty($umk_borongan_lokal_5)){
                        $jenis_produk_5 = '-';
                        $hasil_kerja_5 = 0;
                        $data_explode_hasil_kerja_5 = '-';
                        $lembur_5 = 1;
                    }else{
                        $jenis_produk_5 = $umk_borongan_lokal_5->jenis_produk;
                        $hasil_kerja_5 = $explode_hasil_kerja_5[1]*$umk_borongan_lokal_5->umk_packing;
                        $data_explode_hasil_kerja_5 = $explode_hasil_kerja_5[1];

                        $explode_lembur_5 = explode("|",$hasil_pengerjaan->lembur);
                        $explode_status_lembur_5 = explode("-",$explode_lembur_5[5]);

                        if($explode_status_lembur_5[1] == 'y'){
                            $lembur_5 = 1.5;
                            $label_lembur_5 = 'L';
                            // $label_lembur_5 = '<span class="badge bg-primary">L</span>';
                        }else{
                            $lembur_5 = 1;
                            $label_lembur_5 = 'NL';
                            // $label_lembur_5 = null;
                        }
                        // $label_lembur_5 = '<span class="badge bg-primary">L</span>';
                    }

                    $hasil_upah = ($hasil_kerja_1*$lembur_1)+($hasil_kerja_2*$lembur_2)+($hasil_kerja_3*$lembur_3)+($hasil_kerja_4*$lembur_4)+($hasil_kerja_5*$lembur_5);
                    array_push($upah,$hasil_upah);
                @endphp
                <td>{{ $jenis_produk_1 }}</td>
                <td>{{ $data_explode_hasil_kerja_1 }}</td>
                <td></td>
                <td>{{ $hasil_pengerjaan->total_jam_kerja_1 }}</td>
            @endforeach --}}
        </tr>
        @endforeach
    </tbody>
</table>
