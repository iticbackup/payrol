<!DOCTYPE html>
<html lang="en">

<head>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    {{-- <meta charset="UTF-8"> --}}
    {{-- <meta name="viewport" content="width=device-width, initial-scale=1.0"> --}}
    {{-- <meta http-equiv="X-UA-Compatible" content="ie=edge"> --}}
    {{-- <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous"> --}}
    <title>Slip Gaji</title>
    <style>
        html {
            font-family: Arial, Helvetica, sans-serif
        }

        /* table,
        td,
        th {
            border: 1px solid;
        } */

        table {
            width: 100%;
            border: 1px solid;
            border-collapse: collapse;
            font-size: 10pt;
        }

        .flex-container {
            display: flex;
            flex-wrap: wrap;
        }

        .flex-item {
            width: calc(100% / 2);
        }

        .grid {
            /* background-color: #FFF; */
            overflow: hidden !important;
            display: grid !important;
            grid-template-rows: 190px minmax(10px, 246px) auto 212px 1fr !important;
            grid-row-gap: 1em !important;
        }
    </style>
</head>

<body>
    {{-- @foreach ($operator_borongans as $operator_borongan)
        <h5>1</h5>
    @endforeach --}}
    @foreach ($operator_borongans as $operator_borongan)
    <table>
        <tr>
            <td style="width: 150px; font-weight: bold">Tanggal Gaji</td>
            <td style="font-weight: bold">:</td>
            <td colspan="3" style="width: 150px"></td>
        </tr>
        <tr>
            <td style="font-weight: bold">Nama</td>
            <td style="font-weight: bold">:</td>
            <td colspan="3"></td>
        </tr>
        {{-- <tr>
            <td>Gaji</td>
            <td>:</td>
            <td></td>
            <td>Rp.</td>
            <td style="text-align: right">0</td>
        </tr>
        <tr>
            <td>Lembur</td>
            <td>:</td>
            <td></td>
            <td>Rp.</td>
            <td style="text-align: right">0</td>
        </tr>
        <tr>
            <td>Insentif Kehadiran</td>
            <td>:</td>
            <td></td>
            <td>Rp.</td>
            <td style="text-align: right">0</td>
        </tr>
        <tr>
            <td>Tunjangan Kerja</td>
            <td>:</td>
            <td></td>
            <td>Rp.</td>
            <td style="text-align: right">0</td>
        </tr>
        <tr>
            <td style="vertical-align: top">Plus</td>
            <td style="vertical-align: top">:</td>
            <td style="vertical-align: top">
                <div>()</div>
                <div>()</div>
                <div>()</div>
            </td>
            <td>
                <div>Rp. </div>
                <div>Rp. </div>
                <div>Rp. </div>
            </td>
            <td style="text-align: right">
                <div>0</div>
                <div>0</div>
                <div>0</div>
            </td>
        </tr>
        <tr>
            <td>Uang Makan</td>
            <td>:</td>
            <td></td>
            <td>Rp.</td>
            <td style="text-align: right">0</td>
        </tr>
        <tr>
            <td style="font-weight: bold">POTONGAN</td>
            <td style="font-weight: bold">:</td>
            <td style="font-weight: bold">
                <div>BPJS > JHT + JP</div>
                <div>BPJS Kesehatan</div>
                <div>()</div>
                <div>()</div>
            </td>
            <td>
                <div>Rp. </div>
                <div>Rp. </div>
                <div>Rp. </div>
                <div>Rp. </div>
            </td>
            <td style="text-align: right">
                <div>0</div>
                <div>0</div>
                <div>0</div>
                <div>0</div>
            </td>
        </tr>
        <tr>
            <td style="font-weight: bold">TOTAL DITERIMA</td>
            <td style="font-weight: bold">:</td>
            <td style="font-weight: bold"></td>
            <td style="font-weight: bold">Rp.</td>
            <td style="font-weight: bold; text-align: right">0</td>
        </tr> --}}
    </table>
    @endforeach
    {{-- <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script> --}}
</body>

</html>
