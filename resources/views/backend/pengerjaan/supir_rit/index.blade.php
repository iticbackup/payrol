@extends('layouts.backend.app')
@section('title')
    Pengerjaan - Supir RIT
@endsection
@section('css')
    <style>
        @media screen and (max-width: 1366px) {
            .table-container {
                overflow-x: scroll;
                width: 52%;
            }
        }
        @media screen and (min-width: 1367px) {
            .table-container {
                overflow-x: scroll;
                width: 90%;
            }
        }

        /* @media screen (max-width: 1920px) {

            .table-container {
                overflow-x: scroll;
                width: 80%;
            }
        } */
        /* .table-container {
            overflow-x: scroll;
            width: 64%;
        } */
    </style>
@endsection
@section('content')
    @component('components.breadcrumb')
        @slot('li_1')
            Data Hasil Kerja
        @endslot
        @slot('li_3')
            @yield('title')
        @endslot
        @slot('title')
            @yield('title')
        @endslot
    @endcomponent

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5>Kode ID : {{ $new_data_pengerjaan->kode_pengerjaan }}
                        @if ($new_data_pengerjaan->status == 'n')
                            <i class="far fa-check-circle text-success"></i>
                        @endif
                    </h5>
                </div>
                <div class="card-body">
                    <div class="table-container">
                        <table class="table table-sm table-bordered dt-responsive nowrap"
                            style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                            <thead>
                                @php
                                    $exp_tanggal = array_filter($explode_tanggal_pengerjaans);
                                    $a = count($exp_tanggal);
                                    // var_dump($a);
                                    $exp_tgl_awal = explode('-', $exp_tanggal[1]);
                                    $exp_tgl_akhir = explode('-', $exp_tanggal[$a]);
                                    $explode_posting = explode('-', $new_data_pengerjaan['date']);
                                    // dd($explode_posting);
                                    if ($new_data_pengerjaan['akhir_bulan'] == 'y') {
                                        $merger_plus = 'colspan=7';
                                        $merger_minus = 'colspan=4';
                                    } else {
                                        $merger_plus = 'colspan=5';
                                        $merger_minus = 'colspan=4';
                                    }

                                    $month = \Carbon\Carbon::now()->format('m');
                                    $year = \Carbon\Carbon::now()->format('Y');
                                @endphp
                                <tr>
                                    <th rowspan="3" class="text-center">No</th>
                                    <th rowspan="3" class="text-center">NIK</th>
                                    <th rowspan="3" class="text-center">Nama</th>
                                    <th colspan="{{ $a }}" class="text-center">Tanggal</th>
                                    <th rowspan="3" class="text-center">Upah Dasar</th>
                                    <th {{ $merger_plus }} class="text-center">Plus</th>
                                    <th rowspan="3" class="text-center">Total Gaji</th>
                                    <th {{ $merger_minus }} class="text-center">Potongan</th>
                                    <th rowspan="3" class="text-center">Upah Diterima</th>
                                </tr>
                                <tr>
                                    @foreach ($explode_tanggal_pengerjaans as $key => $explode_tanggal_pengerjaan)
                                        @if ($key != 0)
                                            @if ($new_data_pengerjaan->status == 'n')
                                                <td class="text-center">
                                                    {{ \Carbon\Carbon::parse($explode_tanggal_pengerjaan)->isoFormat('D MMMM') }}
                                                </td>
                                            @else
                                                <th class="text-center"><a href=""
                                                        onclick="window.open('{{ route('hasil_kerja.supir_rit.input', ['kode_pengerjaan' => $kode_pengerjaan, 'tanggal' => $explode_tanggal_pengerjaan]) }}','','width=1200,height=555,left=80,top=80,location=yes, menubar=no, status=no,toolbar=no, scrollbars=yes, resizable=yes')"
                                                        class="text-primary">{{ \Carbon\Carbon::parse($explode_tanggal_pengerjaan)->isoFormat('D MMMM') }}</a>
                                                </th>
                                            @endif
                                        @endif
                                    @endforeach
                                    <th rowspan="2" class="text-center">Plus 1</th>
                                    <th rowspan="2" class="text-center">Plus 2</th>
                                    <th rowspan="2" class="text-center">Plus 3</th>
                                    <th rowspan="2" class="text-center">UM</th>
                                    <th rowspan="2" class="text-center">Lembur</th>
                                    @if ($new_data_pengerjaan['akhir_bulan'] == 'y')
                                        <th rowspan="2" class="text-center">Tunjangan Kerja</th>
                                        <th rowspan="2" class="text-center">Kehadiran</th>
                                    @endif
                                    <th rowspan="2" class="text-center">Minus 1</th>
                                    <th rowspan="2" class="text-center">Minus 2</th>
                                    <th rowspan="2" class="text-center">BPJS Ketenagakerjaan</th>
                                    <th rowspan="2" class="text-center">BPJS Kesehatan</th>
                                    {{-- <th rowspan="2" class="text-center">Pensiun</th> --}}
                                </tr>
                                <tr>
                                    @foreach ($explode_tanggal_pengerjaans as $key => $explode_tanggal_pengerjaan)
                                        @if ($key != 0)
                                            <th style="font-size: 8pt" class="text-center">
                                                <table style="width: 100%">
                                                    <tr>
                                                        <th>KODE</th>
                                                        <th>|</th>
                                                        <th>DPB</th>
                                                    </tr>
                                                </table>
                                            </th>
                                        @endif
                                    @endforeach
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $operator_karyawans = [];
                                    $total_all_upah_dasar = [];
                                    $total_all_plus_1 = [];
                                    $total_all_plus_2 = [];
                                    $total_all_plus_3 = [];
                                    $total_all_uang_makan = [];
                                    $total_all_lembur = [];
                                    $total_all_total_gaji = [];
                                    $total_all_tunjangan_kerja = [];
                                    $total_all_tunjangan_kehadiran = [];
                                    $total_all_minus_1 = [];
                                    $total_all_minus_2 = [];
                                    $total_all_jht = [];
                                    $total_all_bpjs_kesehatan = [];
                                    $total_all_pensiun = [];
                                    $total_all_upah_diterima = [];
                                @endphp
                                @foreach ($pengerjaan_supir_rits as $key => $pengerjaan_supir_rit)
                                    @php
                                        $upah_dasar = [];
                                        array_push($operator_karyawans, $pengerjaan_supir_rit->karyawan_supir_rit_id);
                                    @endphp
                                    <tr>
                                        <td class="text-center">{{ $key + 1 }}</td>
                                        <td class="text-center">{{ $pengerjaan_supir_rit->nik }}</td>
                                        @if ($new_data_pengerjaan->status == 'n')
                                            <td>{{ $pengerjaan_supir_rit->nama }}</td>
                                        @else
                                            <td><a href="javascript:void()"
                                                    onclick="window.open('{{ route('hasil_kerja.supir_rit.view_hasil_karyawan', ['kode_pengerjaan' => $kode_pengerjaan, 'nik' => $pengerjaan_supir_rit->nik, 'month' => $month, 'year' => $year]) }}','','width=1200,height=555,left=80,top=80,location=yes, menubar=no, status=no,toolbar=no, scrollbars=yes, resizable=yes')"
                                                    class="text-primary">{{ $pengerjaan_supir_rit->nama }}</a></td>
                                        @endif
                                        @foreach ($explode_tanggal_pengerjaans as $keys => $explode_tanggal_pengerjaan)
                                            @if ($keys != 0)
                                                @php
                                                    $pengerjaan_supir_rit_daily = \App\Models\PengerjaanRITHarian::where('karyawan_supir_rit_id', $pengerjaan_supir_rit->karyawan_supir_rit_id)
                                                        ->where('kode_pengerjaan', $kode_pengerjaan)
                                                        ->where('tanggal_pengerjaan', $explode_tanggal_pengerjaan)
                                                        ->first();
                                                    if (empty($pengerjaan_supir_rit_daily->hasil_kerja_1)) {
                                                        $hasil_kerja_1 = 0;
                                                        $hasil_umk_rit = 0;
                                                        $tarif_umk = 0;
                                                        $dpb = 0;
                                                    } else {
                                                        $explode_hasil_kerja_1 = explode('|', $pengerjaan_supir_rit_daily->hasil_kerja_1);
                                                        $umk_rit = \App\Models\RitUMK::where('id', $explode_hasil_kerja_1[0])->first();
                                                        if (empty($umk_rit)) {
                                                            $hasil_kerja_1 = 0;
                                                            $hasil_umk_rit = 0;
                                                            $tarif_umk = 0;
                                                            $dpb = 0;
                                                        } else {
                                                            $hasil_kerja_1 = $umk_rit->tarif*$explode_hasil_kerja_1[1];
                                                            $hasil_umk_rit = $umk_rit->kategori_upah;
                                                            $tarif_umk = $umk_rit->tarif;
                                                            $dpb = ($pengerjaan_supir_rit_daily->dpb / 7) * $pengerjaan_supir_rit_daily->upah_dasar;
                                                        }
                                                    }
                                                    $total_upah_dasar = $hasil_kerja_1 + $dpb;
                                                    array_push($upah_dasar, $total_upah_dasar);
                                                @endphp
                                                <td style="font-size: 8pt;">
                                                    <table class="table" style="width: 100%">
                                                        <tr>
                                                            <td><span class="text-danger">{{ $hasil_umk_rit }}</span></td>
                                                            <td><span class="text-primary">{{ 'Rp. '.number_format($hasil_kerja_1,0,',','.') }}</span></td>
                                                            <td>-</td>
                                                            <td><span class="text-primary">Rp.
                                                                    {{ number_format($dpb, 0, ',', '.') }}</td>
                                                        </tr>
                                                    </table>
                                                </td>
                                            @endif
                                        @endforeach
                                        @php
                                            // array_push()
                                            $hasil_upah_dasar = array_sum($upah_dasar);

                                            if (empty($pengerjaan_supir_rit->plus_1)) {
                                                $plus_1 = 0;
                                            } else {
                                                $explode_plus_1 = explode('|', $pengerjaan_supir_rit->plus_1);
                                                $plus_1 = floatval($explode_plus_1[0]);
                                            }

                                            if (empty($pengerjaan_supir_rit->plus_2)) {
                                                $plus_2 = 0;
                                            } else {
                                                $explode_plus_2 = explode('|', $pengerjaan_supir_rit->plus_2);
                                                $plus_2 = floatval($explode_plus_2[0]);
                                            }

                                            if (empty($pengerjaan_supir_rit->plus_3)) {
                                                $plus_3 = 0;
                                            } else {
                                                $explode_plus_3 = explode('|', $pengerjaan_supir_rit->plus_3);
                                                $plus_3 = floatval($explode_plus_3[0]);
                                            }

                                            if (empty($pengerjaan_supir_rit->uang_makan)) {
                                                $uang_makan = 0;
                                            } else {
                                                $uang_makan = $pengerjaan_supir_rit->uang_makan;
                                            }

                                            if (empty($pengerjaan_supir_rit->lembur)) {
                                                $lembur = 0;
                                            } else {
                                                $explode_lembur = explode('|', $pengerjaan_supir_rit->lembur);
                                                $lembur = $explode_lembur[0];
                                            }

                                            if (empty($pengerjaan_supir_rit->tunjangan_kerja)) {
                                                $tunjangan_kerja = 0;
                                            } else {
                                                $tunjangan_kerja = $pengerjaan_supir_rit->tunjangan_kerja;
                                            }

                                            if (empty($pengerjaan_supir_rit->tunjangan_kehadiran)) {
                                                $tunjangan_kehadiran = 0;
                                            } else {
                                                $tunjangan_kehadiran = $pengerjaan_supir_rit->tunjangan_kehadiran;
                                            }

                                            if ($new_data_pengerjaan->akhir_bulan == 'y') {
                                                $plus_tunjangan_kerja = $tunjangan_kerja;
                                                $plus_tunjangan_kehadiran = $tunjangan_kehadiran;
                                            } else {
                                                $plus_tunjangan_kerja = 0;
                                                $plus_tunjangan_kehadiran = 0;
                                            }

                                            // $total_gaji = 0;
                                            $total_gaji = $hasil_upah_dasar + $plus_1 + $plus_2 + $plus_3 + $uang_makan + $lembur + $plus_tunjangan_kerja + $plus_tunjangan_kehadiran;

                                            if (empty($pengerjaan_supir_rit->minus_1)) {
                                                $minus_1 = 0;
                                            } else {
                                                $explode_minus_1 = explode('|', $pengerjaan_supir_rit->minus_1);
                                                $minus_1 = $explode_minus_1[0];
                                            }

                                            if (empty($pengerjaan_supir_rit->minus_2)) {
                                                $minus_2 = 0;
                                            } else {
                                                $explode_minus_2 = explode('|', $pengerjaan_supir_rit->minus_2);
                                                $minus_2 = $explode_minus_2[0];
                                            }

                                            if (empty($pengerjaan_supir_rit->jht)) {
                                                $jht = 0;
                                            } else {
                                                $jht = intval($pengerjaan_supir_rit->jht);
                                            }

                                            if (empty($pengerjaan_supir_rit->bpjs_kesehatan)) {
                                                $bpjs_kesehatan = 0;
                                            } else {
                                                $bpjs_kesehatan = intval($pengerjaan_supir_rit->bpjs_kesehatan);
                                            }
                                            // dd($minus_1);

                                            if (empty($pengerjaan_supir_rit->pensiun)) {
                                                $pensiun = 0;
                                            } else {
                                                $pensiun = $pengerjaan_supir_rit->pensiun;
                                            }

                                            $total_upah_diterima = $total_gaji - $minus_1 - $minus_2 - $jht - $bpjs_kesehatan - $pensiun;
                                            // $total_upah_diterima =$total_gaji;
                                            array_push($total_all_upah_dasar, $hasil_upah_dasar);
                                            array_push($total_all_plus_1, $plus_1);
                                            array_push($total_all_plus_2, $plus_2);
                                            array_push($total_all_plus_3, $plus_3);
                                            array_push($total_all_uang_makan, $uang_makan);
                                            array_push($total_all_lembur, $lembur);
                                            array_push($total_all_total_gaji, $total_gaji);

                                            array_push($total_all_tunjangan_kerja, $tunjangan_kerja);
                                            array_push($total_all_tunjangan_kehadiran, $tunjangan_kehadiran);

                                            array_push($total_all_minus_1, $minus_1);
                                            array_push($total_all_minus_2, $minus_2);
                                            array_push($total_all_jht, $jht);
                                            array_push($total_all_bpjs_kesehatan, $bpjs_kesehatan);
                                            array_push($total_all_pensiun, $pensiun);
                                            array_push($total_all_upah_diterima, $total_upah_diterima);
                                        @endphp
                                        <td style="text-align: right">Rp. {{ number_format($hasil_upah_dasar, 0, ',', '.') }}</td>
                                        <td style="text-align: right">Rp. {{ number_format($plus_1, 0, ',', '.') }}</td>
                                        <td style="text-align: right">Rp. {{ number_format($plus_2, 0, ',', '.') }}</td>
                                        <td style="text-align: right">Rp. {{ number_format($plus_3, 0, ',', '.') }}</td>
                                        <td style="text-align: right">Rp. {{ number_format($uang_makan, 0, ',', '.') }}</td>
                                        <td style="text-align: right">Rp. {{ number_format($lembur, 0, ',', '.') }}</td>
                                        @if ($new_data_pengerjaan['akhir_bulan'] == 'y')
                                            <td style="text-align: right">Rp.
                                                {{ number_format($tunjangan_kerja, 0, ',', '.') }}</td>
                                            <td style="text-align: right">Rp.
                                                {{ number_format($tunjangan_kehadiran, 0, ',', '.') }}</td>
                                        @endif
                                        <td style="text-align: right">Rp. {{ number_format($total_gaji, 0, ',', '.') }}</td>
                                        <td style="text-align: right">Rp. {{ number_format($minus_1, 0, ',', '.') }}</td>
                                        <td style="text-align: right">Rp. {{ number_format($minus_2, 0, ',', '.') }}</td>
                                        <td style="text-align: right">Rp. {{ number_format($jht, 0, ',', '.') }}</td>
                                        <td style="text-align: right">Rp. {{ number_format($bpjs_kesehatan, 0, ',', '.') }}</td>
                                        <td style="text-align: right">Rp. {{ number_format($total_upah_diterima, 0, ',', '.') }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="3" class="text-center" style="font-weight: bold">TOTAL</td>
                                    @foreach ($explode_tanggal_pengerjaans as $keys => $explode_tanggal_pengerjaan)
                                        @if ($keys != 0)
                                            @php
                                                // dd($explode_tanggal_pengerjaan);
                                                $total_hasil_kerja = [];
                                                $total_hasil_dpb = [];
                                                $hasil_pengerjaans = \App\Models\PengerjaanRITHarian::where('kode_pengerjaan', $kode_pengerjaan)
                                                                                                    ->where('tanggal_pengerjaan', $explode_tanggal_pengerjaan)
                                                                                                    ->whereIn('karyawan_supir_rit_id', $operator_karyawans)
                                                                                                    ->get();
                                                foreach ($hasil_pengerjaans as $key => $hasil_pengerjaan) {
                                                    if (empty($hasil_pengerjaan['hasil_kerja_1'])) {
                                                        $jenis_umk = '-';
                                                        $hasil_kerja = 0;
                                                        $dpb = 0;
                                                    } else {
                                                        $explode_hasil_kerja_1 = explode('|', $hasil_pengerjaan->hasil_kerja_1);
                                                        $umk_rit = \App\Models\RitUMK::where('id', $explode_hasil_kerja_1[0])->first();
                                                        if (empty($umk_rit)) {
                                                            $jenis_umk = '-';
                                                            $hasil_kerja = 0;
                                                            $dpb = 0;
                                                        } else {
                                                            $hasil_kerja = $umk_rit->tarif*$explode_hasil_kerja_1[1];
                                                            $dpb = ($hasil_pengerjaan->dpb / 7) * $hasil_pengerjaan->upah_dasar;
                                                        }
                                                    }
                                                    array_push($total_hasil_kerja, $hasil_kerja);
                                                    array_push($total_hasil_dpb, $dpb);
                                                }
                                            @endphp
                                            <td>
                                                <table style="width: 100%">
                                                    <tr>
                                                        <td style="font-size: 8pt; font-weight: bold">KODE</td>
                                                        <td style="font-size: 8pt; font-weight: bold">:</td>
                                                        <td style="font-size: 8pt; font-weight: bold">-</td>
                                                        <td style="text-align: right; font-size: 8pt; font-weight: bold">Rp.
                                                            {{ number_format(array_sum($total_hasil_kerja), 0, ',', '.') }}
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td style="font-size: 8pt; font-weight: bold">DPB</td>
                                                        <td style="font-size: 8pt; font-weight: bold">:</td>
                                                        <td style="font-size: 8pt; font-weight: bold">-</td>
                                                        <td style="text-align: right; font-size: 8pt; font-weight: bold">Rp.
                                                            {{ number_format(array_sum($total_hasil_dpb), 0, ',', '.') }}</td>
                                                    </tr>
                                                </table>
                                            </td>
                                        @endif
                                    @endforeach
                                    <td style="text-align: right; font-weight: bold">Rp.
                                        {{ number_format(array_sum($total_all_upah_dasar), 0, ',', '.') }}</td>
                                    <td style="text-align: right; font-weight: bold">Rp.
                                        {{ number_format(array_sum($total_all_plus_1), 0, ',', '.') }}</td>
                                    <td style="text-align: right; font-weight: bold">Rp.
                                        {{ number_format(array_sum($total_all_plus_2), 0, ',', '.') }}</td>
                                    <td style="text-align: right; font-weight: bold">Rp.
                                        {{ number_format(array_sum($total_all_plus_3), 0, ',', '.') }}</td>
                                    <td style="text-align: right; font-weight: bold">Rp.
                                        {{ number_format(array_sum($total_all_uang_makan), 0, ',', '.') }}</td>
                                    <td style="text-align: right; font-weight: bold">Rp.
                                        {{ number_format(array_sum($total_all_lembur), 0, ',', '.') }}</td>
                                    @if ($new_data_pengerjaan['akhir_bulan'] == 'y')
                                        <td style="text-align: right; font-weight: bold">Rp.
                                            {{ number_format(array_sum($total_all_tunjangan_kerja), 0, ',', '.') }}</td>
                                        <td style="text-align: right; font-weight: bold">Rp.
                                            {{ number_format(array_sum($total_all_tunjangan_kehadiran), 0, ',', '.') }}</td>
                                    @endif
                                    <td style="text-align: right; font-weight: bold">Rp.
                                        {{ number_format(array_sum($total_all_total_gaji), 0, ',', '.') }}</td>
                                    <td style="text-align: right; font-weight: bold">Rp.
                                        {{ number_format(array_sum($total_all_minus_1), 0, ',', '.') }}</td>
                                    <td style="text-align: right; font-weight: bold">Rp.
                                        {{ number_format(array_sum($total_all_minus_2), 0, ',', '.') }}</td>
                                    <td style="text-align: right; font-weight: bold">Rp.
                                        {{ number_format(array_sum($total_all_jht), 0, ',', '.') }}</td>
                                    <td style="text-align: right; font-weight: bold">Rp.
                                        {{ number_format(array_sum($total_all_bpjs_kesehatan), 0, ',', '.') }}</td>
                                    <td style="text-align: right; font-weight: bold">Rp.
                                        {{ number_format(array_sum($total_all_upah_diterima), 0, ',', '.') }}</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
