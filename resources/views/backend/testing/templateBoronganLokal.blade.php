<body>
    <table>
        <tr>
            <td colspan="3" style="font-weight: bold">PT Indonesian Tobacco Tbk.</td>
        </tr>
        <tr>
            <td colspan="3" style="font-weight: bold">Jl. Letjen S. Parman No. 92 Malang</td>
        </tr>
    </table>
    <table>
        <tr>
            <td colspan="8" style="font-weight: bold">Cara Input Hasil Kerja :</td>
        </tr>
        <tr>
            <td colspan="8" style="color: blue">1. Kode Pengerjaan : MNA40, PSM, BSK, SVL, KTM, KTM, PSS, PSE, RTB, KTB, LLL, AGK, LIBUR</td>
        </tr>
        <tr>
            <td colspan="8" style="color: blue">2. Format layout ini tidak boleh dirubah sesuai dengan format yang telah ditentukan dari sistem.</td>
        </tr>
        <tr>
            <td colspan="8" style="color: blue">3. Jika kategori pengerjaan terdapat kosong maka diinput sesuai dengan katergori pengerjaan.</td>
        </tr>
        <tr>
            <td colspan="8" style="color: blue">4. Jika hasil pengerjaan terdapat kosong maka diinput dengan angka 0</td>
        </tr>
        <tr>
            <td colspan="8" style="color: blue">5. Jika total jam pengerjaan terdapat kosong maka diinput dengan angka 0</td>
        </tr>
    </table>
    <table>
        <tr>
            <td colspan="3" style="font-weight: bold">Borongan Packing</td>
        </tr>
        <tr>
            <td colspan="3" style="font-weight: bold">Tanggal Pengerjaan : {{ $tanggal }}</td>
        </tr>
    </table>
    <table>
        <thead>
            <tr>
                <th rowspan="3" style="vertical-align: middle; text-align: center; border: 1px solid black; font-weight: bold">No</th>
                <th rowspan="3" style="vertical-align: middle; text-align: center; border: 1px solid black; font-weight: bold">NIK</th>
                <th rowspan="3" style="vertical-align: middle; text-align: center; border: 1px solid black; font-weight: bold">Nama</th>
                <th colspan="15" style="vertical-align: middle; text-align: center; border: 1px solid black; font-weight: bold">Hasil Kerja</th>
            </tr>
            <tr>
                <th colspan="3" style="vertical-align: middle; text-align: center; border: 1px solid black; font-weight: bold">H1</th>
                <th colspan="3" style="vertical-align: middle; text-align: center; border: 1px solid black; font-weight: bold">H2</th>
                <th colspan="3" style="vertical-align: middle; text-align: center; border: 1px solid black; font-weight: bold">H3</th>
                <th colspan="3" style="vertical-align: middle; text-align: center; border: 1px solid black; font-weight: bold">H4</th>
                <th colspan="3" style="vertical-align: middle; text-align: center; border: 1px solid black; font-weight: bold">H5</th>
            </tr>
            <tr>
                <th style="text-align: center; border: 1px solid black; background-color: greenyellow; font-weight: bold">K.Pengerjaan 1</th>
                <th style="text-align: center; border: 1px solid black; background-color: #ffc12f; font-weight: bold">H.Pengerjaan 1</th>
                <th style="text-align: center; border: 1px solid black; font-weight: bold">Total Jam (menit)</th>
                <th style="text-align: center; border: 1px solid black; background-color: greenyellow; font-weight: bold">K.Pengerjaan 2</th>
                <th style="text-align: center; border: 1px solid black; background-color: #ffc12f; font-weight: bold">H.Pengerjaan 2</th>
                <th style="text-align: center; border: 1px solid black; font-weight: bold">Total Jam (menit)</th>
                <th style="text-align: center; border: 1px solid black; background-color: greenyellow; font-weight: bold">K.Pengerjaan 3</th>
                <th style="text-align: center; border: 1px solid black; background-color: #ffc12f; font-weight: bold">H.Pengerjaan 3</th>
                <th style="text-align: center; border: 1px solid black; font-weight: bold">Total Jam (menit)</th>
                <th style="text-align: center; border: 1px solid black; background-color: greenyellow; font-weight: bold">K.Pengerjaan 4</th>
                <th style="text-align: center; border: 1px solid black; background-color: #ffc12f; font-weight: bold">H.Pengerjaan 4</th>
                <th style="text-align: center; border: 1px solid black; font-weight: bold">Total Jam (menit)</th>
                <th style="text-align: center; border: 1px solid black; background-color: greenyellow; font-weight: bold">K.Pengerjaan 5</th>
                <th style="text-align: center; border: 1px solid black; background-color: #ffc12f; font-weight: bold">H.Pengerjaan 5</th>
                <th style="text-align: center; border: 1px solid black; font-weight: bold">Total Jam (menit)</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($pengerjaanBorongans as $key => $pengerjaanBorongan)
                <tr>
                    <td style="border: 1px solid black; text-align: center">{{ $key+1 }}</td>
                    <td style="border: 1px solid black; text-align: center">{{ $pengerjaanBorongan->operator_karyawan->nik }}</td>
                    <td style="border: 1px solid black">{{ $pengerjaanBorongan->operator_karyawan->biodata_karyawan->nama }}</td>
                    <td style="border: 1px solid black"></td>
                    <td style="border: 1px solid black"></td>
                    <td style="border: 1px solid black"></td>
                    <td style="border: 1px solid black"></td>
                    <td style="border: 1px solid black"></td>
                    <td style="border: 1px solid black"></td>
                    <td style="border: 1px solid black"></td>
                    <td style="border: 1px solid black"></td>
                    <td style="border: 1px solid black"></td>
                    <td style="border: 1px solid black"></td>
                    <td style="border: 1px solid black"></td>
                    <td style="border: 1px solid black"></td>
                    <td style="border: 1px solid black"></td>
                    <td style="border: 1px solid black"></td>
                    <td style="border: 1px solid black"></td>
                </tr>
            @endforeach
        </tbody>

    </table>
</body>