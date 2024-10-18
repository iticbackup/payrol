<style>
    * {
        font-family: Arial, Helvetica, sans-serif
    }

    table{
        border: 1px solid;
    }

    table {
        width: 100%;
        border-collapse: collapse;
    }
</style>
@php
    $new_data_pengerjaan = \App\Models\NewDataPengerjaan::where('kode_pengerjaan', $kode_pengerjaan)->first();
    $explode_tanggal_pengerjaans = explode('#',$new_data_pengerjaan['tanggal']);
    $exp_tanggals = array_filter($explode_tanggal_pengerjaans);
    $a = count($exp_tanggals);
    // dd($a);
    $exp_tgl_awal = explode('-', $exp_tanggals[1]);
    $exp_tgl_akhir = explode('-', $exp_tanggals[$a]);

    $pengerjaan_harian = \App\Models\PengerjaanHarian::where('id',$id)
                                                            ->where('kode_pengerjaan',$kode_pengerjaan)
                                                            ->first();

    if (empty($pengerjaan_harian->lembur)) {
        $hasil_lembur = 0;
        $lembur_1 = 0;
        $lembur_2 = 0;
    }else{
        $exlode_lembur = explode("|",$pengerjaan_harian->lembur);
        if (empty($exlode_lembur)) {
            $hasil_lembur = 0;
            $lembur_1 = 0;
            $lembur_2 = 0;
        }else{
            $hasil_lembur = $exlode_lembur[0];
            $lembur_1 = $exlode_lembur[1];
            $lembur_2 = $exlode_lembur[2];
        }
    }

    $total_jam_lembur = floatval($lembur_1)+floatval($lembur_2);

    if (empty($pengerjaan_harian->upah_dasar_weekly)) {
        $upah_dasar_weekly = 0;
    }else{
        $upah_dasar_weekly = $pengerjaan_harian->upah_dasar_weekly;
    }

    if($new_data_pengerjaan['akhir_bulan'] == 'y'){
        if (empty($pengerjaan_harian->tunjangan_kehadiran)) {
            $tunjangan_kehadiran = 0;
        }else{
            $tunjangan_kehadiran = $pengerjaan_harian->tunjangan_kehadiran;
        }
    }else{
        $tunjangan_kehadiran = 0;
    }

    if ($new_data_pengerjaan['akhir_bulan'] == 'y') {
        if (empty($pengerjaan_harian->tunjangan_kerja)) {
            $tunjangan_kerja = 0;
        }else{
            $tunjangan_kerja = $pengerjaan_harian->tunjangan_kerja;
        }
    }else{
        $tunjangan_kerja = 0;
    }

    if (empty($pengerjaan_harian->uang_makan)) {
        $uang_makan = 0;
    }else{
        $uang_makan = $pengerjaan_harian->uang_makan;
    }

    if (empty($pengerjaan_harian->plus_1)) {
        $plus_1 = 0;
        $ket_plus_1 = "";
    }else{
        $explode_plus_1 = explode("|",$pengerjaan_harian->plus_1);
        $plus_1 = intval($explode_plus_1[0]);
        $ket_plus_1 = $explode_plus_1[1];
    }

    if (empty($pengerjaan_harian->plus_2)) {
        $plus_2 = 0;
        $ket_plus_2 = "";
    }else{
        $explode_plus_2 = explode("|",$pengerjaan_harian->plus_2);
        $plus_2 = intval($explode_plus_2[0]);
        $ket_plus_2 = $explode_plus_2[1];
    }

    if (empty($pengerjaan_harian->plus_3)) {
        $plus_3 = 0;
        $ket_plus_3 = "";
    }else{
        $explode_plus_3 = explode("|",$pengerjaan_harian->plus_3);
        $plus_3 = intval($explode_plus_3[0]);
        $ket_plus_3 = $explode_plus_3[1];
    }

    if (empty($pengerjaan_harian->minus_1)) {
        $minus_1 = 0;
        $ket_minus_1 = "";
    }else{
        $explode_minus_1 = explode("|",$pengerjaan_harian->minus_1);
        if (empty($explode_minus_1[0])) {
            $minus_1 = 0;
        }else{
            $minus_1 = intval($explode_minus_1[0]);
        }
        $ket_minus_1 = $explode_minus_1[1];
    }

    if (empty($pengerjaan_harian->minus_2)) {
        $minus_2 = 0;
        $ket_minus_2 = "";
    }else{
        $explode_minus_2 = explode("|",$pengerjaan_harian->minus_2);
        if (empty($explode_minus_2[0])) {
            $minus_2 = 0;
        }else{
            $minus_2 = intval($explode_minus_2[0]);
        }
        $ket_minus_2 = $explode_minus_2[1];
    }

    if (empty($pengerjaan_harian->jht)) {
        $jht = 0;
    }else{
        $jht = intval($pengerjaan_harian->jht);
    }

    if (empty($pengerjaan_harian->bpjs_kesehatan)) {
        $bpjs_kesehatan = 0;
    }else{
        $bpjs_kesehatan = intval($pengerjaan_harian->bpjs_kesehatan);
    }

    $total_gaji_diterima = ($pengerjaan_harian->upah_dasar_weekly+$hasil_lembur+$tunjangan_kehadiran+$tunjangan_kerja+
                            $plus_1+$plus_2+$plus_3+$pengerjaan_harian->uang_makan)-
                            ($jht+$bpjs_kesehatan+$minus_1+$minus_2);
@endphp
<table>
    <tr>
        <td style="font-weight: bold">Tanggal Gaji</td>
        <td colspan="4">{{ \Carbon\Carbon::parse($exp_tgl_awal[0] . '-' . $exp_tgl_awal[1] . '-' . $exp_tgl_awal[2])->isoFormat('D MMMM').' s/d '.\Carbon\Carbon::parse($exp_tgl_akhir[0] . '-' . $exp_tgl_akhir[1] . '-' . $exp_tgl_akhir[2])->isoFormat('D MMMM YYYY') }}</td>
    </tr>
    <tr>
        <td style="font-weight: bold">Nama</td>
        <td colspan="4" style="font-weight: bold">{{ strtoupper($pengerjaan_harian->operator_karyawan->biodata_karyawan->nama).' ('.$pengerjaan_harian->operator_karyawan->biodata_karyawan->nik.')' }}</td>
    </tr>
    <tr>
        <td style="font-weight: bold">Departemen</td>
        <td colspan="4" style="font-weight: bold">{{ strtoupper($pengerjaan_harian->operator_karyawan->jenis_operator_detail_pengerjaan->jenis_posisi_pekerjaan) }}</td>
    </tr>
    <tr>
        <td>Gaji</td>
        <td>{{ $a.' HARI' }}</td>
        <td></td>
        <td>RP</td>
        <td style="text-align: right">{{ number_format($upah_dasar_weekly,0,',','.') }}</td>
    </tr>
    <tr>
        <td>Lembur ({{ $total_jam_lembur }} Jam)</td>
        <td></td>
        <td></td>
        <td>RP</td>
        <td style="text-align: right">{{ number_format($hasil_lembur,0,',','.') }}</td>
    </tr>
    <tr>
        <td>Insentif Kehadiran</td>
        <td></td>
        <td></td>
        <td>RP</td>
        <td style="text-align: right">{{ number_format($tunjangan_kehadiran,0,',','.') }}</td>
    </tr>
    <tr>
        <td>Tunjangan Kerja</td>
        <td></td>
        <td></td>
        <td>RP</td>
        <td style="text-align: right">{{ number_format($tunjangan_kerja,0,',','.') }}</td>
    </tr>
    <tr>
        <td>Plus</td>
        <td>({{ $ket_plus_1 }})</td>
        <td></td>
        <td>RP</td>
        <td style="text-align: right">{{ number_format($plus_1,0,',','.') }}</td>
    </tr>
    <tr>
        <td></td>
        <td>({{ $ket_plus_2 }})</td>
        <td></td>
        <td>RP</td>
        <td style="text-align: right">{{ number_format($plus_2,0,',','.') }}</td>
    </tr>
    <tr>
        <td></td>
        <td>({{ $ket_plus_3 }})</td>
        <td></td>
        <td>RP</td>
        <td style="text-align: right">{{ number_format($plus_3,0,',','.') }}</td>
    </tr>
    <tr>
        <td>Uang Makan</td>
        <td></td>
        <td></td>
        <td>RP</td>
        <td style="text-align: right">{{ number_format($uang_makan,0,',','.') }}</td>
    </tr>
    <tr>
        <td style="font-weight: bold">Potongan</td>
        <td style="font-weight: bold">BPJS > JHT + JP</td>
        <td></td>
        <td style="font-weight: bold">RP</td>
        <td style="text-align: right; font-weight: bold">{{ number_format($jht,0,',','.') }}</td>
    </tr>
    <tr>
        <td></td>
        <td style="font-weight: bold">BPJS Kesehatan</td>
        <td></td>
        <td style="font-weight: bold">RP</td>
        <td style="text-align: right; font-weight: bold">{{ number_format($bpjs_kesehatan,0,',','.') }}</td>
    </tr>
    <tr>
        <td></td>
        <td style="font-weight: bold">({{ $ket_minus_1 }})</td>
        <td></td>
        <td style="font-weight: bold">RP</td>
        <td style="text-align: right; font-weight: bold">{{ number_format($minus_1,0,',','.') }}</td>
    </tr>
    <tr>
        <td></td>
        <td style="font-weight: bold">({{ $ket_minus_2 }})</td>
        <td></td>
        <td style="font-weight: bold">RP</td>
        <td style="text-align: right; font-weight: bold">{{ number_format($minus_2,0,',','.') }}</td>
    </tr>
    <tr>
        <td colspan="2" style="font-weight: bold">Total Diterima</td>
        <td></td>
        <td style="font-weight: bold">RP</td>
        <td style="text-align: right; font-weight: bold">{{ number_format($total_gaji_diterima,0,',','.') }}</td>
    </tr>
</table>
<div style="margin-top: 5%">
    <strong style="font-size: 10pt;">Note:</strong>
    <div style="font-size: 10pt">Slip Gaji ini bersifat rahasia digunakan untuk pribadi dan tidak boleh diketahui oleh pihak lain.</div>
</div>