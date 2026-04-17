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