<html>

<head>
    <title>Bank {{ $kode_pengerjaan }}</title>
</head>
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
    .container{
        display: flex;
        flex-direction: row;
    }
</style>

<body>
    @php
        $total_nominal = [];
        $explode_tanggal_pengerjaans = explode('#',$new_data_pengerjaan['tanggal']);
        $exp_tanggals = array_filter($explode_tanggal_pengerjaans);
        $a = count($exp_tanggals);
        $exp_tgl_awal = explode('-', $exp_tanggals[1]);
        $exp_tgl_akhir = explode('-', $exp_tanggals[$a]);
    @endphp
    {{-- <div style="float: left;">
        <img src="{{ public_path('itic/logo_itic.png') }}" width="50">
        PT Indonesian Tobacco Tbk.
        Rekap Gaji Karyawan Borongan PT Indonesian Tobacco Tbk. <br>
        Tanggal :
    </div> --}}
    <p style="vertical-align: middle; margin-top: -2.5%">
        <img style="float: left" width="50" src="{{ public_path('itic/logo_itic.png') }}">
        <div>
            <b>Rekap Gaji Karyawan Borongan PT Indonesian Tobacco Tbk.</b> <br>
            <b>Tanggal : {{ \Carbon\Carbon::parse($exp_tgl_awal[0] . '-' . $exp_tgl_awal[1] . '-' . $exp_tgl_awal[2])->isoFormat('D MMMM').' s/d '.\Carbon\Carbon::parse($exp_tgl_akhir[0] . '-' . $exp_tgl_akhir[1] . '-' . $exp_tgl_akhir[2])->isoFormat('D MMMM YYYY') }}</b>
        </div>
    </p>
    <br>
    {{-- <div style="margin-top: 10%; margin-bottom: 2.5%">
        <div style="font-weight: bold">Rekap Gaji Karyawan Borongan PT Indonesian Tobacco Tbk.</div>
        <div style="font-weight: bold">Tanggal : </div>
    </div> --}}
    {{-- <div class="container">
        <img src="{{ public_path('itic/logo_itic.png') }}" width="50">
        <div style="font-weight: bold">Rekap Gaji Karyawan Borongan PT Indonesian Tobacco Tbk.</div>
        <div style="font-weight: bold">Tanggal : </div>
    </div> --}}

    <table>
        <tr>
            <th style="height: 4%; background-color: grey;"><span style="color: white">NO</span></th>
            <th style="height: 4%; background-color: grey;"><span style="color: white">NIK</span></th>
            <th style="height: 4%; background-color: grey;"><span style="color: white">NAMA</span></th>
            <th style="height: 4%; background-color: grey;"><span style="color: white">REKENING</span></th>
            <th style="height: 4%; background-color: grey;"><span style="color: white">NOMINAL</span></th>
            <th style="height: 4%; background-color: grey;"><span style="color: white">KET</span></th>
        </tr>
        @foreach ($karyawan_operators as $key => $karyawan_operator)
        @php
            array_push($total_nominal,$karyawan_operator->upah_dasar);
        @endphp
        <tr>
            <td style="text-align: center">{{ $key+1 }}</td>
            <td style="text-align: center">{{ $karyawan_operator->nik }}</td>
            <td>{{ $karyawan_operator->nama }}</td>
            <td style="text-align: center">{{ $karyawan_operator->rekening }}</td>
            <td style="text-align: right">{{ number_format($karyawan_operator->upah_dasar,0,',','.') }}</td>
            <td style="text-align: center">Gaji</td>
        </tr>
        @endforeach
        {{-- @php
            echo array_sum($total_nominal);
        @endphp --}}
        <tr>
            <td colspan="4" style="text-align: center">TOTAL</td>
            <td style="text-align: right">{{ number_format(array_sum($total_nominal),0,',','.') }}</td>
            <td></td>
        </tr>
    </table>
    {{-- <br style="margin-top: 15%"> --}}
    <table style="width: 80%; margin-top: 10%">
        <tr>
            <td style="text-align: center">Dibuat Oleh</td>
            <td style="text-align: center">Disetujui Oleh</td>
            <td colspan="2" style="text-align: center">Diketahui Oleh</td>
        </tr>
        <tr>
            <td style="height: 8%"></td>
            <td style="height: 8%"></td>
            <td style="height: 8%"></td>
            <td style="height: 8%"></td>
        </tr>
        <tr>
            <td style="text-align: center; padding-left: 10%; padding-right: 10%">Staff Payroll</td>
            <td style="text-align: center; padding-left: 10%; padding-right: 10%">Manager HRD</td>
            <td style="text-align: center; padding-left: 10%; padding-right: 10%">Kepala Bagian</td>
            <td style="text-align: center; padding-left: 10%; padding-right: 10%">Manager Keuangan</td>
        </tr>
    </table>
</body>

</html>
