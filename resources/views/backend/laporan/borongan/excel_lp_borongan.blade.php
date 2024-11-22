<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
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
    <table>
        <thead>
            <tr >
                <td colspan="3">Daftar Gaji Borongan PT. Indonesian Tobacco Tbk.</td>
            </tr>
            <tr>
                <td colspan="3">Produksi B: </td>
            </tr>
            <tr>
                <td colspan="3">Tanggal :
                    {{-- {{ \Carbon\Carbon::parse($exp_tgl_awal[0] . '-' . $exp_tgl_awal[1] . '-' . $exp_tgl_awal[2])->isoFormat('D MMMM') }}
                    -
                    {{ \Carbon\Carbon::parse($exp_tgl_akhir[0] . '-' . $exp_tgl_akhir[1] . '-' . $exp_tgl_akhir[2])->isoFormat('D MMMM YYYY') }} --}}
                </td>
            </tr>
            <tr>
                <td></td>
            </tr>
            <tr>
                <td rowspan="2" style="text-align: center; border: 1px solid black; background-color: #EEEEEE; font-weight: bold" valign="middle">No</td>
                <td rowspan="2" style="text-align: center; border: 1px solid black; background-color: #EEEEEE; font-weight: bold" valign="middle">NIK</td>
                <td rowspan="2" style="text-align: center; border: 1px solid black; background-color: #EEEEEE; font-weight: bold" valign="middle">Nama</td>
                {{-- @foreach ($exp_tanggals as $exp_tanggal)
                    @for ($b = 1; $b <= $a; $b++)
                        <td colspan="4" style="text-align: center; border: 1px solid black; background-color: #EEEEEE; font-weight: bold">
                            {{ \Carbon\Carbon::parse($exp_tanggal)->format('d M Y') }}
                        </td>
                    @endfor
                @endforeach --}}
                <td rowspan="2" style="text-align: center; border: 1px solid black; background-color: #EEEEEE; font-weight: bold" valign="middle">Upah Hasil Kerja</td>
                <td colspan="7" style="text-align: center; border: 1px solid black; background-color: #EEEEEE; font-weight: bold">PLUS</td>
                <td rowspan="2" style="text-align: center; border: 1px solid black; background-color: #EEEEEE; font-weight: bold" valign="middle">Total Gaji</td>
                <td colspan="5" style="text-align: center; border: 1px solid black; background-color: #EEEEEE; font-weight: bold">Potongan</td>
                <td rowspan="2" style="text-align: center; border: 1px solid black; background-color: #EEEEEE; font-weight: bold" valign="middle">Upah Diterima</td>
            </tr>
        </thead>
    </table>
</body>
</html>