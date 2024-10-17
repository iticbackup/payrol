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
$explode_tanggal_pengerjaans = explode('#', $new_data_pengerjaan['tanggal']);
$exp_tanggals = array_filter($explode_tanggal_pengerjaans);
$a = count($exp_tanggals);
$exp_tgl_awal = explode('-', $exp_tanggals[1]);
$exp_tgl_akhir = explode('-', $exp_tanggals[$a]);

$upah_dasar = array();

$pengerjaan_rit_weekly = \App\Models\PengerjaanRITWeekly::where('kode_pengerjaan', $kode_pengerjaan)->where('id', $id)->first();
@endphp

<table>
    <tr>
        <td colspan="4" style="text-align: center; border: 1px solid">
            <div style="text-transform: uppercase; font-weight: bold">Gaji Rit-Ritan {{ $pengerjaan_rit_weekly->operator_supir_rit->biodata_karyawan->nama }}</div>
            <div style="font-weight: bold">Tanggal {{ \Carbon\Carbon::parse($exp_tgl_awal[0] . '-' . $exp_tgl_awal[1] . '-' . $exp_tgl_awal[2])->isoFormat('D MMMM').' s/d '.\Carbon\Carbon::parse($exp_tgl_akhir[0] . '-' . $exp_tgl_akhir[1] . '-' . $exp_tgl_akhir[2])->isoFormat('D MMMM YYYY') }}</div>
        </td>
    </tr>
    <tr>
        <td style="text-align: center; font-weight: bold; border: 1px solid; width: 20%">TGL</td>
        <td style="text-align: center; font-weight: bold; border: 1px solid" colspan="2">KETERANGAN</td>
        <td style="text-align: center; font-weight: bold; border: 1px solid; width: 20%">Rp</td>
    </tr>
    @for ($i=0;$i<$a;$i++)
    @php
        $pengerjaan_rits = \App\Models\PengerjaanRITHarian::where('kode_pengerjaan',$new_data_pengerjaan['kode_pengerjaan'])
                                                        ->where('karyawan_supir_rit_id',$pengerjaan_rit_weekly->karyawan_supir_rit_id)
                                                        ->get();
                                                        // dd($pengerjaan_rits);
        if (empty($pengerjaan_rits[$i]->hasil_kerja_1)) {
            $tanggal_pengerjaan = 0;
            $hasil_kerja_1 = 0;
            $hasil_umk_rit = 0;
            $tarif_umk = 0;
            $dpb = 0;
            $jenis_umk = '-';
        }else{
            $tanggal_pengerjaan = \Carbon\Carbon::create($pengerjaan_rits[$i]->tanggal_pengerjaan)->isoFormat('D MMM');
            $explode_hasil_kerja_1 = explode("|",$pengerjaan_rits[$i]->hasil_kerja_1);
            $umk_rit = \App\Models\RitUMK::where('id',$explode_hasil_kerja_1[0])->first();
            if (empty($umk_rit)) {
                $hasil_kerja_1 = 0;
                $hasil_umk_rit = 0;
                $tarif_umk = 0;
                $dpb = 0;
                $jenis_umk = '-';
            }else{
                $hasil_kerja_1 = $umk_rit->tarif*$explode_hasil_kerja_1[1];
                $hasil_umk_rit = $umk_rit->kategori_upah;
                $tarif_umk = $umk_rit->tarif;
                $dpb = $pengerjaan_rits[$i]->dpb/7*$pengerjaan_rits[$i]->upah_dasar;
                if (empty($umk_rit->rit_tujuan)) {
                    $jenis_umk = '-';
                }else{
                    $jenis_umk = $umk_rit->rit_tujuan->tujuan.' - '.$umk_rit->rit_kendaraan->jenis_kendaraan;
                }
                $total_upah_dasar = $hasil_kerja_1+$dpb;
                array_push($upah_dasar,$total_upah_dasar);
            }
        }
    @endphp
    <tr>
        <td style="font-size: 10pt; text-align: center; border: 1px solid">{{ $tanggal_pengerjaan }}</td>
        <td colspan="2" style="border: 1px solid">
            <div style="font-size: 10pt">DPB</div>
            <div style="font-size: 8pt">{{ $jenis_umk }}</div>
        </td>
        <td style="border: 1px solid">
            <div style="font-size: 10pt; text-align: right">{{ number_format($dpb,0,',','.') }}</div>
            <div style="font-size: 10pt; text-align: right">{{ number_format($hasil_kerja_1,0,',','.') }}</div>
        </td>
    </tr>
    @endfor
    @php
        $hasil_upah_dasar = array_sum($upah_dasar);

        if (empty($pengerjaan_rit_weekly->lembur)) {
            $lembur_1 = 0;
            $lembur_2 = 0;
            $hasil_lembur = 0;
        }else{
            $explode_lembur = explode("|",$pengerjaan_rit_weekly->lembur);
            $lembur_1 = $explode_lembur[1];
            $lembur_2 = $explode_lembur[2];
            $hasil_lembur = $explode_lembur[0];
        }

        $total_jam_lembur = floatval($lembur_1)+floatval($lembur_2);

        if ($new_data_pengerjaan['akhir_bulan'] == 'y') {
            if (empty($pengerjaan_rit_weekly->tunjangan_kehadiran)) {
                $tunjangan_kehadiran = 0;
            }else{
                $tunjangan_kehadiran = $pengerjaan_rit_weekly->tunjangan_kehadiran;
            }
        }else{
            $tunjangan_kehadiran = 0;
        }

        if ($new_data_pengerjaan['akhir_bulan'] == 'y') {
            if (empty($pengerjaan_rit_weekly->tunjangan_kerja)) {
                $tunjangan_kerja = 0;
            }else{
                $tunjangan_kerja = $pengerjaan_rit_weekly->tunjangan_kerja;
            }
        }else{
            $tunjangan_kerja = 0;
        }

        if (empty($pengerjaan_rit_weekly->uang_makan)) {
            $uang_makan = 0;
        }else{
            $uang_makan = $pengerjaan_rit_weekly->uang_makan;
        }

        if (empty($pengerjaan_rit_weekly->plus_1)) {
            $plus_1 = 0;
            $keterangan_plus_1 = '';
        }else{
            $explode_plus_1 = explode("|",$pengerjaan_rit_weekly->plus_1);
            $plus_1 = floatval($explode_plus_1[0]);
            $keterangan_plus_1 = $explode_plus_1[1];
        }

        if (empty($pengerjaan_rit_weekly->plus_2)) {
            $plus_2 = 0;
            $keterangan_plus_2 = '';
        }else{
            $explode_plus_2 = explode("|",$pengerjaan_rit_weekly->plus_2);
            $plus_2 = floatval($explode_plus_2[0]);
            $keterangan_plus_2 = $explode_plus_2[1];
        }

        if (empty($pengerjaan_rit_weekly->plus_3)) {
            $plus_3 = 0;
            $keterangan_plus_3 = '';
        }else{
            $explode_plus_3 = explode("|",$pengerjaan_rit_weekly->plus_3);
            $plus_3 = floatval($explode_plus_3[0]);
            $keterangan_plus_3 = $explode_plus_3[1];
        }

        $total_gaji = $hasil_upah_dasar+$plus_1+$plus_2+$plus_3+$uang_makan+$hasil_lembur+$tunjangan_kerja+$tunjangan_kehadiran;

        if (empty($pengerjaan_rit_weekly->minus_1)) {
            $minus_1 = 0;
            $keterangan_minus_1 = '';
        }else{
            $explode_minus_1 = explode("|",$pengerjaan_rit_weekly->minus_1);
            $minus_1 = $explode_minus_1[0];
            $keterangan_minus_1 = $explode_minus_1[1];
        }

        if (empty($pengerjaan_rit_weekly->minus_2)) {
            $minus_2 = 0;
            $keterangan_minus_2 = '';
        }else{
            $explode_minus_2 = explode("|",$pengerjaan_rit_weekly->minus_2);
            $minus_2 = $explode_minus_2[0];
            $keterangan_minus_2 = $explode_minus_2[1];
        }

        if (empty($pengerjaan_rit_weekly->jht)) {
            $jht = 0;
        }else{
            $jht = $pengerjaan_rit_weekly->jht;
        }

        if (empty($pengerjaan_rit_weekly->bpjs_kesehatan)) {
            $bpjs_kesehatan = 0;
        }else{
            $bpjs_kesehatan = $pengerjaan_rit_weekly->bpjs_kesehatan;
        }

        if (empty($pengerjaan_rit_weekly->pensiun)) {
            $pensiun = 0;
        }else{
            $pensiun = $pengerjaan_rit_weekly->pensiun;
        }

        $total_upah_diterima = $total_gaji-$minus_1-$minus_2-$jht-$bpjs_kesehatan-$pensiun;
    @endphp
    <tr>
        <td colspan="2" style="font-size: 10pt; ">Lembur ({{ $total_jam_lembur }} Jam)</td>
        <td style="font-size: 10pt; text-align: right">Rp</td>
        <td style="font-size: 10pt; text-align: right">{{ number_format($hasil_lembur,0,',','.') }}</td>
    </tr>
    <tr>
        <td colspan="2" style="font-size: 10pt; ">Insentif Kehadiran</td>
        <td style="font-size: 10pt; text-align: right">Rp</td>
        <td style="font-size: 10pt; text-align: right">{{ number_format($tunjangan_kehadiran,0,',','.') }}</td>
    </tr>
    <tr>
        <td colspan="2" style="font-size: 10pt; ">Tunjangan Kerja</td>
        <td style="font-size: 10pt; text-align: right">Rp</td>
        <td style="font-size: 10pt; text-align: right">{{ number_format($tunjangan_kerja,0,',','.') }}</td>
    </tr>
    <tr>
        <td style="font-size: 10pt; ">Plus</td>
        <td style="font-size: 10pt;">( {{ $keterangan_plus_1 }} )</td>
        <td style="font-size: 10pt; text-align: right">Rp</td>
        <td style="font-size: 10pt; text-align: right">{{ number_format($plus_1,0,',','.') }}</td>
    </tr>
    <tr>
        <td style="font-size: 10pt; "></td>
        <td style="font-size: 10pt;">( {{ $keterangan_plus_2 }} )</td>
        <td style="font-size: 10pt; text-align: right">Rp</td>
        <td style="font-size: 10pt; text-align: right">{{ number_format($plus_2,0,',','.') }}</td>
    </tr>
    <tr>
        <td style="font-size: 10pt; "></td>
        <td style="font-size: 10pt;">( {{ $keterangan_plus_3 }} )</td>
        <td style="font-size: 10pt; text-align: right">Rp</td>
        <td style="font-size: 10pt; text-align: right">{{ number_format($plus_3,0,',','.') }}</td>
    </tr>
    <tr>
        <td style="font-size: 10pt; ">Uang Makan</td>
        <td style="font-size: 10pt;"></td>
        <td style="font-size: 10pt; text-align: right">Rp</td>
        <td style="font-size: 10pt; text-align: right">{{ number_format($uang_makan,0,',','.') }}</td>
    </tr>
    <tr>
        <td style="font-size: 10pt; font-weight: bold; ">POTONGAN</td>
        <td style="font-size: 10pt; font-weight: bold;">BPJS > JHT + JP</td>
        <td style="font-size: 10pt; font-weight: bold; text-align: right">Rp</td>
        <td style="font-size: 10pt; font-weight: bold; text-align: right">{{ number_format($jht+$pensiun,0,',','.') }}</td>
    </tr>
    <tr>
        <td style="font-size: 10pt; font-weight: bold; "></td>
        <td style="font-size: 10pt; font-weight: bold;">BPJS Kesehatan</td>
        <td style="font-size: 10pt; font-weight: bold; text-align: right">Rp</td>
        <td style="font-size: 10pt; font-weight: bold; text-align: right">{{ number_format($bpjs_kesehatan,0,',','.') }}</td>
    </tr>
    <tr>
        <td style="font-size: 10pt; font-weight: bold; "></td>
        <td style="font-size: 10pt; font-weight: bold;">( {{ $keterangan_minus_1 }} )</td>
        <td style="font-size: 10pt; font-weight: bold; text-align: right">Rp</td>
        <td style="font-size: 10pt; font-weight: bold; text-align: right">{{ number_format($minus_1,0,',','.') }}</td>
    </tr>
    <tr>
        <td style="font-size: 10pt; font-weight: bold; "></td>
        <td style="font-size: 10pt; font-weight: bold;">( {{ $keterangan_minus_2 }} )</td>
        <td style="font-size: 10pt; font-weight: bold; text-align: right">Rp</td>
        <td style="font-size: 10pt; font-weight: bold; text-align: right">{{ number_format($minus_2,0,',','.') }}</td>
    </tr>
    <tr>
        <td colspan="2" style="font-size: 10pt; font-weight: bold;">TOTAL DITERIMA</td>
        <td style="font-size: 10pt; font-weight: bold; text-align: right">Rp</td>
        <td style="font-size: 10pt; font-weight: bold; text-align: right">{{ number_format($total_upah_diterima,0,',','.') }}</td>
    </tr>
</table>
<div style="margin-top: 5%">
    <strong style="font-size: 10pt;">Note:</strong>
    <div style="font-size: 10pt">Slip Gaji ini bersifat rahasia digunakan untuk pribadi dan tidak boleh diketahui oleh pihak lain.</div>
</div>