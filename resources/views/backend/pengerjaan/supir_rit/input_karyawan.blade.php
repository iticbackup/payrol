@extends('layouts.backend.master_no_header')

@section('title')
    Input Hasil Kerja Karyawan Supir RIT
@endsection

@section('css')
    <link href="{{ URL::asset('public/assets/css/iziToast.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ URL::asset('public/assets/plugins/sweet-alert2/sweetalert2.min.css') }}" rel="stylesheet" type="text/css">
    <script src="{{ URL::asset('public/assets/js/pages/jquery.sweet-alert.init.js') }}"></script>
@endsection

@section('content')
    @component('components.breadcrumb')
        @slot('li_1')
            Supir RIT
        @endslot
        @slot('li_3')
            @yield('title')
        @endslot
        @slot('title')
            @yield('title')
        @endslot
    @endcomponent
    <?php
    $akhir_bulan = 'y';
    
    $exp_tanggals = array_filter($explode_tanggal_pengerjaans);
    $a = count($exp_tanggals);
    // dd($exp_tanggal);
    $exp_tgl_awal = explode('-', $exp_tanggals[1]);
    $exp_tgl_akhir = explode('-', $exp_tanggals[$a]);
    $explode_posting = explode('-', $new_data_pengerjaan['date']);
    
    $upah_dasar = [];
    foreach ($exp_tanggals as $key => $exp_tanggal) {
        $pengerjaan_supir_rit_daily = \App\Models\PengerjaanRITHarian::where('karyawan_supir_rit_id', $karyawan_supir_rit->karyawan_supir_rit_id)
            ->where('kode_pengerjaan', $kode_pengerjaan)
            ->where('tanggal_pengerjaan', $exp_tanggal)
            ->first();
        // dd($pengerjaan_supir_rit_daily);
        if (empty($pengerjaan_supir_rit_daily->hasil_kerja_1)) {
            $hasil_kerja_1 = 0;
            $hasil_umk_rit = 0;
            $tarif_umk = 0;
            $dpb = 0;
        } else {
            $explode_hasil_kerja_1 = explode('|', $pengerjaan_supir_rit_daily->hasil_kerja_1);
            $umk_rit = \App\Models\RitUMK::where('id', $explode_hasil_kerja_1[0])
                ->where('status', 'y')
                ->first();
            if (empty($umk_rit)) {
                $hasil_kerja_1 = 0;
                $hasil_umk_rit = 0;
                $tarif_umk = 0;
                $dpb = 0;
            } else {
                $hasil_kerja_1 = 'Rp. ' . number_format($umk_rit->tarif, 0, ',', '.');
                $hasil_umk_rit = $umk_rit->kategori_upah;
                $tarif_umk = $umk_rit->tarif;
                $dpb = ($pengerjaan_supir_rit_daily->dpb / 7) * $pengerjaan_supir_rit_daily->upah_dasar;
            }
        }
        $total_upah_dasar = $tarif_umk + $dpb;
        // dd($total_upah_dasar);
        array_push($upah_dasar, $total_upah_dasar);
    }
    ?>
    <div class="row">
        <div class="col-12">
            <div class="card">
                {{-- <form action="{{ route('hasil_kerja.supir_rit.view_hasil_karyawan.simpan',['kode_pengerjaan' => $kode_pengerjaan, 'nik' => $nik, 'month' => $month, 'year' => $year]) }}" method="post" enctype="multipart/form-data"> --}}
                <form id="form-simpan" method="post" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="karyawan_supir_rit_id"
                        value="{{ $karyawan_supir_rit->karyawan_supir_rit_id }}" id="">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-5">
                                <div class="card">
                                    <div class="card-header">
                                        <h4 class="card-title">Input Gaji Karyawan</h4>
                                    </div>
                                    <div class="card-body">
                                        <table class="table table-bordered">
                                            <tr>
                                                <td>NIK</td>
                                                <td>:</td>
                                                <td>{{ $nik }}</td>
                                            </tr>
                                            <tr>
                                                <td>Nama Karyawan</td>
                                                <td class="text-center">:</td>
                                                <td>{{ $karyawan_supir_rit->operator_supir_rit->biodata_karyawan->nama }}</td>
                                            </tr>
                                            <tr>
                                                <td>Masa Kerja</td>
                                                <td class="text-center">:</td>
                                                <td>{{ $masa_kerja }}</td>
                                            </tr>
                                            <tr>
                                                <td>Upah Dasar</td>
                                                <td class="text-center">:</td>
                                                <?php
                                                $total_upah_dasar = array_sum($upah_dasar);
                                                ?>
                                                <td>
                                                    Rp. {{ number_format($total_upah_dasar, 0, ',', '.') }}
                                                    <input type="hidden" name="upah_dasar"
                                                        value="{{ round($total_upah_dasar) }}" id="">
                                                    <input type="hidden" name="upah_dasar_karyawan"
                                                        value="{{ $karyawan_supir_rit->upah_dasar }}" id="">
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>Uang Makan</td>
                                                <td class="text-center">:</td>
                                                <td>
                                                    @php
                                                        if (empty($karyawan_supir_rit->uang_makan)) {
                                                            $uang_makan = 0;
                                                        } else {
                                                            $uang_makan = $karyawan_supir_rit->uang_makan;
                                                        }
                                                    @endphp
                                                    <input type="text" name="uang_makan" class="form-control"
                                                        placeholder="Uang Makan" value="{{ $uang_makan }}"
                                                        id="">
                                                </td>
                                            </tr>
                                            @if ($new_data_pengerjaan->akhir_bulan == $akhir_bulan)
                                            <tr>
                                                <td>Tunjangan Kerja</td>
                                                <td class="text-center">:</td>
                                                <td>
                                                    <input type="hidden" name="tunjangan_kerja" value="{{ $tunjangan_kerja }}" id="">
                                                    {{ 'Rp. '.number_format($tunjangan_kerja,0,',','.') }}
                                                </td>
                                            </tr>
                                            @endif
                                            @php
                                                if (empty($karyawan_supir_rit->plus_1)) {
                                                    $plus_1 = 0;
                                                    $keterangan_plus_1 = null;
                                                } else {
                                                    $explode_plus_1 = explode('|', $karyawan_supir_rit->plus_1);
                                                    $plus_1 = $explode_plus_1[0];
                                                    $keterangan_plus_1 = $explode_plus_1[1];
                                                }

                                                if (empty($karyawan_supir_rit->plus_2)) {
                                                    $plus_2 = 0;
                                                    $keterangan_plus_2 = null;
                                                } else {
                                                    $explode_plus_2 = explode('|', $karyawan_supir_rit->plus_2);
                                                    $plus_2 = $explode_plus_2[0];
                                                    $keterangan_plus_2 = $explode_plus_2[1];
                                                }

                                                if (empty($karyawan_supir_rit->plus_3)) {
                                                    $plus_3 = 0;
                                                    $keterangan_plus_3 = null;
                                                } else {
                                                    $explode_plus_3 = explode('|', $karyawan_supir_rit->plus_3);
                                                    $plus_3 = $explode_plus_3[0];
                                                    $keterangan_plus_3 = $explode_plus_3[1];
                                                }
                                            @endphp
                                            <tr>
                                                <td style="font-weight: bold">Plus 1</td>
                                                <td class="text-center">:</td>
                                                <td>
                                                    <div class="form-group">
                                                        <input type="text" name="plus_1" class="form-control"
                                                            placeholder="Rp." value="{{ $plus_1 }}" id="">
                                                        <input type="text" name="keterangan_plus_1" class="form-control"
                                                            placeholder="Keterangan" value="{{ $keterangan_plus_1 }}"
                                                            id="">
                                                    </div>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td style="font-weight: bold">Plus 2</td>
                                                <td class="text-center">:</td>
                                                <td>
                                                    <div class="form-group">
                                                        <input type="text" name="plus_2" value="{{ $plus_2 }}"
                                                            class="form-control" placeholder="Rp." id="">
                                                        <input type="text" name="keterangan_plus_2"
                                                            value="{{ $keterangan_plus_2 }}" class="form-control"
                                                            placeholder="Keterangan" id="">
                                                    </div>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td style="font-weight: bold">Plus 3</td>
                                                <td class="text-center">:</td>
                                                <td>
                                                    <div class="form-group">
                                                        <input type="text" name="plus_3" value="{{ $plus_3 }}"
                                                            class="form-control" placeholder="Rp." id="">
                                                        <input type="text" name="keterangan_plus_3"
                                                            value="{{ $keterangan_plus_3 }}" class="form-control"
                                                            placeholder="Keterangan" id="">
                                                    </div>
                                                </td>
                                            </tr>
                                            @php
                                                if (!empty($karyawan_supir_rit->lembur)) {
                                                    $explode_lembur = explode('|', $karyawan_supir_rit->lembur);
                                                    $hasil_nominal_lembur = 0;
                                                    $hasil_lembur_0 = $explode_lembur[0];
                                                    $hasil_lembur_1 = $explode_lembur[1];
                                                    $hasil_lembur_2 = $explode_lembur[2];
                                                } else {
                                                    $hasil_lembur_0 = null;
                                                    $hasil_lembur_1 = null;
                                                    $hasil_lembur_2 = null;
                                                    $hasil_nominal_lembur = 0;
                                                }
                                                // dd($karyawan_supir_rit->lembur);
                                            @endphp
                                            <tr>
                                                <td style="font-weight: bold">Lembur</td>
                                                <td class="text-center">:</td>
                                                <td>
                                                    <div class="row">
                                                        <div class="col-md-5"><input type="text" class="form-control"
                                                                value="{{ $hasil_lembur_1 }}" name="lembur_1"
                                                                id=""></div>
                                                        <div class="col-md-1">-</div>
                                                        <div class="col-md-5"><input type="text" class="form-control"
                                                                value="{{ $hasil_lembur_2 }}" name="lembur_2"
                                                                id=""></div>
                                                    </div>
                                                    <input type="text" name="lembur_0" class="form-control mt-2"
                                                        style="width:132px;text-align:right;"
                                                        value="{{ $hasil_lembur_0 }}" readonly="readonly">
                                                </td>
                                            </tr>
                                        </table>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-7">
                                <div class="card">
                                    <div class="card-header">
                                        <h4 class="card-title">Potongan</h4>
                                    </div>
                                    <div class="card-body">
                                        <table class="table table-bordered">
                                            @if ($new_data_pengerjaan->akhir_bulan == $akhir_bulan)
                                                <tr>
                                                    <td rowspan="15" style="vertical-align: top">Pot. T. Kehadiran</td>
                                                    <td>
                                                        <div class="row">
                                                            <div
                                                                class="col-sm-4 form-label align-self-center mb-lg-0 text-center">
                                                                <input type="text" class="form-control"
                                                                    value="{{ $alpa->count() }}" id="" readonly>
                                                            </div>
                                                            <div class="col-sm-8">
                                                                <label
                                                                    class="col-sm-10 form-label align-self-center mb-lg-0">Hari
                                                                    Alpa</label>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div class="row">
                                                            <div
                                                                class="col-sm-1 form-label align-self-center mb-lg-0 text-center">
                                                                <label
                                                                    class="col-sm-1 form-label align-self-center mb-lg-0">X</label>
                                                            </div>
                                                            <div class="col-sm-5">
                                                                <input type="text" class="form-control" value="75.000"
                                                                    id="" readonly>
                                                            </div>
                                                            <div
                                                                class="col-sm-1 form-label align-self-center mb-lg-0 text-center">
                                                                <label
                                                                    class="col-sm-1 form-label align-self-center mb-lg-0">=</label>
                                                            </div>
                                                            <div class="col-sm-5">
                                                                <input type="text" class="form-control"
                                                                    value="{{ number_format(75000 * $alpa->count(), 0, ',', '.') }}"
                                                                    id="" readonly>
                                                            </div>
                                                        </div>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>
                                                        <div class="row">
                                                            <div
                                                                class="col-sm-4 form-label align-self-center mb-lg-0 text-center">
                                                                <input type="text" class="form-control"
                                                                    value="{{ $diliburkan->count() }}" id=""
                                                                    readonly>
                                                            </div>
                                                            <div class="col-sm-8">
                                                                <label
                                                                    class="col-sm-10 form-label align-self-center mb-lg-0">Hari
                                                                    Diliburkan</label>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div class="row">
                                                            <div
                                                                class="col-sm-1 form-label align-self-center mb-lg-0 text-center">
                                                                <label
                                                                    class="col-sm-1 form-label align-self-center mb-lg-0">X</label>
                                                            </div>
                                                            <div class="col-sm-5">
                                                                <input type="text" class="form-control" value="75.000"
                                                                    id="" readonly>
                                                            </div>
                                                            <div
                                                                class="col-sm-1 form-label align-self-center mb-lg-0 text-center">
                                                                <label
                                                                    class="col-sm-1 form-label align-self-center mb-lg-0">=</label>
                                                            </div>
                                                            <div class="col-sm-5">
                                                                <input type="text" class="form-control"
                                                                    value="{{ number_format(75000 * $diliburkan->count(), 0, ',', '.') }}"
                                                                    id="" readonly>
                                                            </div>
                                                        </div>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>
                                                        <div class="row">
                                                            <div
                                                                class="col-sm-4 form-label align-self-center mb-lg-0 text-center">
                                                                <input type="text" class="form-control"
                                                                    value="{{ $sakit->count() }}" id=""
                                                                    readonly>
                                                            </div>
                                                            <div class="col-sm-8">
                                                                <label
                                                                    class="col-sm-10 form-label align-self-center mb-lg-0">Hari
                                                                    Sakit</label>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div class="row">
                                                            <div
                                                                class="col-sm-1 form-label align-self-center mb-lg-0 text-center">
                                                                <label
                                                                    class="col-sm-1 form-label align-self-center mb-lg-0">X</label>
                                                            </div>
                                                            <div class="col-sm-5">
                                                                <input type="text" class="form-control" value="75.000"
                                                                    id="" readonly>
                                                            </div>
                                                            <div
                                                                class="col-sm-1 form-label align-self-center mb-lg-0 text-center">
                                                                <label
                                                                    class="col-sm-1 form-label align-self-center mb-lg-0">=</label>
                                                            </div>
                                                            <div class="col-sm-5">
                                                                <input type="text" class="form-control"
                                                                    value="{{ number_format(75000 * $sakit->count(), 0, ',', '.') }}"
                                                                    id="" readonly>
                                                            </div>
                                                        </div>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>
                                                        <div class="row">
                                                            <div
                                                                class="col-sm-4 form-label align-self-center mb-lg-0 text-center">
                                                                <input type="text" class="form-control"
                                                                    value="{{ $cuti->count() }}" id="" readonly>
                                                            </div>
                                                            <div class="col-sm-8">
                                                                <label
                                                                    class="col-sm-10 form-label align-self-center mb-lg-0">Hari
                                                                    Cuti</label>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div class="row">
                                                            <div
                                                                class="col-sm-1 form-label align-self-center mb-lg-0 text-center">
                                                                <label
                                                                    class="col-sm-1 form-label align-self-center mb-lg-0">X</label>
                                                            </div>
                                                            <div class="col-sm-5">
                                                                <input type="text" class="form-control" value="75.000"
                                                                    id="" readonly>
                                                            </div>
                                                            <div
                                                                class="col-sm-1 form-label align-self-center mb-lg-0 text-center">
                                                                <label
                                                                    class="col-sm-1 form-label align-self-center mb-lg-0">=</label>
                                                            </div>
                                                            <div class="col-sm-5">
                                                                <input type="text" class="form-control"
                                                                    value="{{ number_format(75000 * $cuti->count(), 0, ',', '.') }}"
                                                                    id="" readonly>
                                                            </div>
                                                        </div>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>
                                                        <div class="row">
                                                            <div
                                                                class="col-sm-4 form-label align-self-center mb-lg-0 text-center">
                                                                <input type="text" class="form-control"
                                                                    value="{{ $ijin_full->count() }}" id=""
                                                                    readonly>
                                                            </div>
                                                            <div class="col-sm-8">
                                                                <label
                                                                    class="col-sm-10 form-label align-self-center mb-lg-0">Hari
                                                                    Izin (Full)</label>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div class="row">
                                                            <div
                                                                class="col-sm-1 form-label align-self-center mb-lg-0 text-center">
                                                                <label
                                                                    class="col-sm-1 form-label align-self-center mb-lg-0">X</label>
                                                            </div>
                                                            <div class="col-sm-5">
                                                                <input type="text" class="form-control" value="75.000"
                                                                    id="" readonly>
                                                            </div>
                                                            <div
                                                                class="col-sm-1 form-label align-self-center mb-lg-0 text-center">
                                                                <label
                                                                    class="col-sm-1 form-label align-self-center mb-lg-0">=</label>
                                                            </div>
                                                            <div class="col-sm-5">
                                                                <input type="text" class="form-control"
                                                                    value="{{ number_format(75000 * $ijin_full->count(), 0, ',', '.') }}"
                                                                    id="" readonly>
                                                            </div>
                                                        </div>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>
                                                        <div class="row">
                                                            <div
                                                                class="col-sm-4 form-label align-self-center mb-lg-0 text-center">
                                                                <input type="text" class="form-control"
                                                                    value="{{ $ijin_15 }}" id="" readonly>
                                                            </div>
                                                            <div class="col-sm-8">
                                                                <label
                                                                    class="col-sm-10 form-label align-self-center mb-lg-0">Hari
                                                                    Izin <u>
                                                                        <</u> 15 Menit</label>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div class="row">
                                                            <div
                                                                class="col-sm-1 form-label align-self-center mb-lg-0 text-center">
                                                                <label
                                                                    class="col-sm-1 form-label align-self-center mb-lg-0">X</label>
                                                            </div>
                                                            <div class="col-sm-5">
                                                                <input type="text" class="form-control" value="25.000"
                                                                    id="" readonly>
                                                            </div>
                                                            <div
                                                                class="col-sm-1 form-label align-self-center mb-lg-0 text-center">
                                                                <label
                                                                    class="col-sm-1 form-label align-self-center mb-lg-0">=</label>
                                                            </div>
                                                            <div class="col-sm-5">
                                                                <input type="text" class="form-control"
                                                                    value="{{ number_format($ijin_15 * 25000, 0, ',', '.') }}"
                                                                    id="" readonly>
                                                            </div>
                                                        </div>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>
                                                        <div class="row">
                                                            <div
                                                                class="col-sm-4 form-label align-self-center mb-lg-0 text-center">
                                                                <input type="text" class="form-control"
                                                                    value="{{ $ijin_k4 }}" id="" readonly>
                                                            </div>
                                                            <div class="col-sm-8">
                                                                <label
                                                                    class="col-sm-10 form-label align-self-center mb-lg-0">Hari
                                                                    Izin <u>></u> 15 Menit <u>
                                                                        <</u> 4 Jam</label>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div class="row">
                                                            <div
                                                                class="col-sm-1 form-label align-self-center mb-lg-0 text-center">
                                                                <label
                                                                    class="col-sm-1 form-label align-self-center mb-lg-0">X</label>
                                                            </div>
                                                            <div class="col-sm-5">
                                                                <input type="text" class="form-control" value="40.000"
                                                                    id="" readonly>
                                                            </div>
                                                            <div
                                                                class="col-sm-1 form-label align-self-center mb-lg-0 text-center">
                                                                <label
                                                                    class="col-sm-1 form-label align-self-center mb-lg-0">=</label>
                                                            </div>
                                                            <div class="col-sm-5">
                                                                <input type="text" class="form-control"
                                                                    value="{{ number_format($ijin_k4 * 40000, 0, ',', '.') }}"
                                                                    id="" readonly>
                                                            </div>
                                                        </div>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>
                                                        <div class="row">
                                                            <div
                                                                class="col-sm-4 form-label align-self-center mb-lg-0 text-center">
                                                                <input type="text" class="form-control"
                                                                    value="{{ $ijin_l4 }}" id="" readonly>
                                                            </div>
                                                            <div class="col-sm-8">
                                                                <label
                                                                    class="col-sm-10 form-label align-self-center mb-lg-0">Hari
                                                                    Izin <u>></u> 4 Jam</label>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div class="row">
                                                            <div
                                                                class="col-sm-1 form-label align-self-center mb-lg-0 text-center">
                                                                <label
                                                                    class="col-sm-1 form-label align-self-center mb-lg-0">X</label>
                                                            </div>
                                                            <div class="col-sm-5">
                                                                <input type="text" class="form-control" value="75.000"
                                                                    id="" readonly>
                                                            </div>
                                                            <div
                                                                class="col-sm-1 form-label align-self-center mb-lg-0 text-center">
                                                                <label
                                                                    class="col-sm-1 form-label align-self-center mb-lg-0">=</label>
                                                            </div>
                                                            <div class="col-sm-5">
                                                                <input type="text" class="form-control"
                                                                    value="{{ number_format($ijin_k4 * 75000, 0, ',', '.') }}"
                                                                    id="" readonly>
                                                            </div>
                                                        </div>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>
                                                        <div class="row">
                                                            <div
                                                                class="col-sm-4 form-label align-self-center mb-lg-0 text-center">
                                                                <input type="text" class="form-control"
                                                                    value="{{ $pulang_1 }}" id="" readonly>
                                                            </div>
                                                            <div class="col-sm-8">
                                                                <label
                                                                    class="col-sm-10 form-label align-self-center mb-lg-0">Hari
                                                                    Pulang Awal <u>
                                                                        <</u> 4 Jam Kerja</label>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div class="row">
                                                            <div
                                                                class="col-sm-1 form-label align-self-center mb-lg-0 text-center">
                                                                <label
                                                                    class="col-sm-1 form-label align-self-center mb-lg-0">X</label>
                                                            </div>
                                                            <div class="col-sm-5">
                                                                <input type="text" class="form-control" value="40.000"
                                                                    id="" readonly>
                                                            </div>
                                                            <div
                                                                class="col-sm-1 form-label align-self-center mb-lg-0 text-center">
                                                                <label
                                                                    class="col-sm-1 form-label align-self-center mb-lg-0">=</label>
                                                            </div>
                                                            <div class="col-sm-5">
                                                                <input type="text" class="form-control"
                                                                    value="{{ number_format($pulang_1 * 40000, 0, ',', '.') }}"
                                                                    id="" readonly>
                                                            </div>
                                                        </div>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>
                                                        <div class="row">
                                                            <div
                                                                class="col-sm-4 form-label align-self-center mb-lg-0 text-center">
                                                                <input type="text" class="form-control"
                                                                    value="{{ $pulang_2 }}" id="" readonly>
                                                            </div>
                                                            <div class="col-sm-8">
                                                                <label
                                                                    class="col-sm-10 form-label align-self-center mb-lg-0">Hari
                                                                    Pulang Awal <u>></u> 4 Jam Kerja</label>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div class="row">
                                                            <div
                                                                class="col-sm-1 form-label align-self-center mb-lg-0 text-center">
                                                                <label
                                                                    class="col-sm-1 form-label align-self-center mb-lg-0">X</label>
                                                            </div>
                                                            <div class="col-sm-5">
                                                                <input type="text" class="form-control" value="75.000"
                                                                    id="" readonly>
                                                            </div>
                                                            <div
                                                                class="col-sm-1 form-label align-self-center mb-lg-0 text-center">
                                                                <label
                                                                    class="col-sm-1 form-label align-self-center mb-lg-0">=</label>
                                                            </div>
                                                            <div class="col-sm-5">
                                                                <input type="text" class="form-control"
                                                                    value="{{ number_format($pulang_2 * 75000, 0, ',', '.') }}"
                                                                    id="" readonly>
                                                            </div>
                                                        </div>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>
                                                        <div class="row">
                                                            <div
                                                                class="col-sm-4 form-label align-self-center mb-lg-0 text-center">
                                                                <input type="text" class="form-control"
                                                                    value="{{ $telat_1 }}" id="" readonly>
                                                            </div>
                                                            <div class="col-sm-8">
                                                                <label
                                                                    class="col-sm-10 form-label align-self-center mb-lg-0">Hari
                                                                    Terlambat <u>
                                                                        <</u> 5 Menit</label>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div class="row">
                                                            <div
                                                                class="col-sm-1 form-label align-self-center mb-lg-0 text-center">
                                                                <label
                                                                    class="col-sm-1 form-label align-self-center mb-lg-0">X</label>
                                                            </div>
                                                            <div class="col-sm-5">
                                                                <input type="text" class="form-control" value="15.000"
                                                                    id="" readonly>
                                                            </div>
                                                            <div
                                                                class="col-sm-1 form-label align-self-center mb-lg-0 text-center">
                                                                <label
                                                                    class="col-sm-1 form-label align-self-center mb-lg-0">=</label>
                                                            </div>
                                                            <div class="col-sm-5">
                                                                <input type="text" class="form-control"
                                                                    value="{{ number_format($telat_1 * 15000, 0, ',', '.') }}"
                                                                    id="" readonly>
                                                            </div>
                                                        </div>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>
                                                        <div class="row">
                                                            <div
                                                                class="col-sm-4 form-label align-self-center mb-lg-0 text-center">
                                                                <input type="text" class="form-control"
                                                                    value="{{ $telat_2 }}" id="" readonly>
                                                            </div>
                                                            <div class="col-sm-8">
                                                                <label
                                                                    class="col-sm-10 form-label align-self-center mb-lg-0">Hari
                                                                    Terlambat <u>></u> 5 Menit <u>
                                                                        <</u> 15 Menit</label>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div class="row">
                                                            <div
                                                                class="col-sm-1 form-label align-self-center mb-lg-0 text-center">
                                                                <label
                                                                    class="col-sm-1 form-label align-self-center mb-lg-0">X</label>
                                                            </div>
                                                            <div class="col-sm-5">
                                                                <input type="text" class="form-control" value="25.000"
                                                                    id="" readonly>
                                                            </div>
                                                            <div
                                                                class="col-sm-1 form-label align-self-center mb-lg-0 text-center">
                                                                <label
                                                                    class="col-sm-1 form-label align-self-center mb-lg-0">=</label>
                                                            </div>
                                                            <div class="col-sm-5">
                                                                <input type="text" class="form-control"
                                                                    value="{{ number_format($telat_2 * 25000, 0, ',', '.') }}"
                                                                    id="" readonly>
                                                            </div>
                                                        </div>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>
                                                        <div class="row">
                                                            <div
                                                                class="col-sm-4 form-label align-self-center mb-lg-0 text-center">
                                                                <input type="text" class="form-control"
                                                                    value="{{ $telat_3 }}" id="" readonly>
                                                            </div>
                                                            <div class="col-sm-8">
                                                                <label
                                                                    class="col-sm-10 form-label align-self-center mb-lg-0">Hari
                                                                    Terlambat <u>></u> 15 Menit <u>
                                                                        <</u> 1 Jam</label>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div class="row">
                                                            <div
                                                                class="col-sm-1 form-label align-self-center mb-lg-0 text-center">
                                                                <label
                                                                    class="col-sm-1 form-label align-self-center mb-lg-0">X</label>
                                                            </div>
                                                            <div class="col-sm-5">
                                                                <input type="text" class="form-control" value="30.000"
                                                                    id="" readonly>
                                                            </div>
                                                            <div
                                                                class="col-sm-1 form-label align-self-center mb-lg-0 text-center">
                                                                <label
                                                                    class="col-sm-1 form-label align-self-center mb-lg-0">=</label>
                                                            </div>
                                                            <div class="col-sm-5">
                                                                <input type="text" class="form-control"
                                                                    value="{{ number_format($telat_3 * 30000, 0, ',', '.') }}"
                                                                    id="" readonly>
                                                            </div>
                                                        </div>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>
                                                        <div class="row">
                                                            <div
                                                                class="col-sm-4 form-label align-self-center mb-lg-0 text-center">
                                                                <input type="text" class="form-control"
                                                                    value="{{ $telat_4 }}" id="" readonly>
                                                            </div>
                                                            <div class="col-sm-8">
                                                                <label
                                                                    class="col-sm-10 form-label align-self-center mb-lg-0">Hari
                                                                    Terlambat <u>></u> 1 Jam <u>
                                                                        <</u> 3 Jam</label>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div class="row">
                                                            <div
                                                                class="col-sm-1 form-label align-self-center mb-lg-0 text-center">
                                                                <label
                                                                    class="col-sm-1 form-label align-self-center mb-lg-0">X</label>
                                                            </div>
                                                            <div class="col-sm-5">
                                                                <input type="text" class="form-control" value="40.000"
                                                                    id="" readonly>
                                                            </div>
                                                            <div
                                                                class="col-sm-1 form-label align-self-center mb-lg-0 text-center">
                                                                <label
                                                                    class="col-sm-1 form-label align-self-center mb-lg-0">=</label>
                                                            </div>
                                                            <div class="col-sm-5">
                                                                <input type="text" class="form-control"
                                                                    value="{{ number_format($telat_4 * 40000, 0, ',', '.') }}"
                                                                    id="" readonly>
                                                            </div>
                                                        </div>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>
                                                        <div class="row">
                                                            <div
                                                                class="col-sm-4 form-label align-self-center mb-lg-0 text-center">
                                                                <input type="text" class="form-control"
                                                                    value="{{ $telat_5 }}" id="" readonly>
                                                            </div>
                                                            <div class="col-sm-8">
                                                                <label
                                                                    class="col-sm-10 form-label align-self-center mb-lg-0">Hari
                                                                    Terlambat <u>></u> 3 Jam</label>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div class="row">
                                                            <div
                                                                class="col-sm-1 form-label align-self-center mb-lg-0 text-center">
                                                                <label
                                                                    class="col-sm-1 form-label align-self-center mb-lg-0">X</label>
                                                            </div>
                                                            <div class="col-sm-5">
                                                                <input type="text" class="form-control" value="75.000"
                                                                    id="" readonly>
                                                            </div>
                                                            <div
                                                                class="col-sm-1 form-label align-self-center mb-lg-0 text-center">
                                                                <label
                                                                    class="col-sm-1 form-label align-self-center mb-lg-0">=</label>
                                                            </div>
                                                            <div class="col-sm-5">
                                                                <input type="text" class="form-control"
                                                                    value="{{ number_format($telat_5 * 75000, 0, ',', '.') }}"
                                                                    id="" readonly>
                                                            </div>
                                                        </div>
                                                    </td>
                                                </tr>
                                                <?php
                                                
                                                ?>
                                                <tr>
                                                    <td>Potongan T. Kehadiran</td>
                                                    <td>
                                                        <input type="text" class="form-control"
                                                            value="{{ number_format($total_potongan_tk, 0, ',', '.') }}"
                                                            id="" readonly>
                                                        <input type="hidden" name="pot_tunjangan_kehadiran"
                                                            value="{{ $total_potongan_tk }}" class="form-control">
                                                    </td>
                                                </tr>
                                            @endif
                                            <tr>
                                                <td>Jaminan Hari Tua</td>
                                                <td colspan="2">
                                                    <div class="row">
                                                        @php
                                                            if ($karyawan_supir_rit->jht != null && $karyawan_supir_rit->jht != 0) {
                                                                $check_jht = 'checked';
                                                            } else {
                                                                $check_jht = null;
                                                            }
                                                        @endphp
                                                        <div
                                                            class="col-sm-2 form-label align-self-center mb-lg-0 text-center">
                                                            <input type="checkbox" name="check_jht" {{ $check_jht }}
                                                                class="form-check-input" id="">
                                                        </div>
                                                        <div class="col-sm-10">
                                                            <select name="jht" class="form-control" id="">
                                                                <option value="">-- Pilih --</option>
                                                                {{-- @if ($masa_kerja_tahun >= 15)
                                                                    <option value="99274">Masa Kerja &nbsp;&nbsp;0 - 10
                                                                        Tahun | Rp. 99.274</option>
                                                                    <option value="100774">Masa Kerja 10 - 15 Tahun | Rp.
                                                                        100.774</option>
                                                                    <option value="102274" selected>Masa Kerja
                                                                        &nbsp;&nbsp;&nbsp;&nbsp;> 15 Tahun | Rp. 102.274
                                                                    </option>
                                                                @elseif($masa_kerja_tahun >= 10 && $masa_kerja_tahun <= 15 && $masa_kerja_hari >= 1)
                                                                    <option value="99274">Masa Kerja &nbsp;&nbsp;0 - 10
                                                                        Tahun | Rp. 99.274</option>
                                                                    <option value="100774" selected>Masa Kerja 10 - 15
                                                                        Tahun | Rp. 100.774</option>
                                                                    <option value="102274">Masa Kerja
                                                                        &nbsp;&nbsp;&nbsp;&nbsp;> 15 Tahun | Rp. 102.274
                                                                    </option>
                                                                @elseif($masa_kerja_tahun <= 10 || $masa_kerja_hari >= 1)
                                                                    <option value="99274" selected>Masa Kerja
                                                                        &nbsp;&nbsp;0 - 10 Tahun | Rp. 99.274</option>
                                                                    <option value="100774">Masa Kerja 10 - 15 Tahun | Rp.
                                                                        100.774</option>
                                                                    <option value="102274">Masa Kerja
                                                                        &nbsp;&nbsp;&nbsp;&nbsp;> 15 Tahun | Rp. 102.274
                                                                    </option>
                                                                @endif --}}
                                                                @foreach ($jhts as $jht)
                                                                    @if ($masa_kerja_tahun > 15)
                                                                    <option value="{{ $jht->nominal }}" {{ $jht->urutan == 3 ? 'selected' : null }}>{{ $jht->keterangan }} | Rp. {{ number_format($jht->nominal,0,',','.') }}</option>
                                                                    @elseif($masa_kerja_tahun >= 10 && $masa_kerja_tahun <= 15 && $masa_kerja_hari >= 1)
                                                                    <option value="{{ $jht->nominal }}" {{ $jht->urutan == 2 ? 'selected' : null }}>{{ $jht->keterangan }} | Rp. {{ number_format($jht->nominal,0,',','.') }}</option>
                                                                    @elseif($masa_kerja_tahun <= 10 || $masa_kerja_hari >= 1)
                                                                    <option value="{{ $jht->nominal }}" {{ $jht->urutan == 1 ? 'selected' : null }}>{{ $jht->keterangan }} | Rp. {{ number_format($jht->nominal,0,',','.') }}</option>
                                                                    @endif
                                                                @endforeach
                                                                {{-- @foreach ($jhts as $jht)
                                                            <option value="{{ $jht->nominal }}" {{ $masa_kerja_tahun >= 15 ?  }}>{{ $jht->keterangan.' | Rp. '.number_format($jht->nominal,0,',','.') }}</option>
                                                            @endforeach --}}
                                                            </select>
                                                        </div>
                                                    </div>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>BPJS Kesehatan</td>
                                                <td colspan="2">
                                                    <div class="row">
                                                        @php
                                                            if ($karyawan_supir_rit->bpjs_kesehatan != null && $karyawan_supir_rit->bpjs_kesehatan != 0) {
                                                                $check_bpjs_kesehatan = 'checked';
                                                            } else {
                                                                $check_bpjs_kesehatan = null;
                                                            }
                                                        @endphp
                                                        <div
                                                            class="col-sm-2 form-label align-self-center mb-lg-0 text-center">
                                                            <input type="checkbox" name="check_bpjs_kesehatan"
                                                                {{ $check_bpjs_kesehatan }} class="form-check-input"
                                                                id="">
                                                        </div>
                                                        <div class="col-sm-10">
                                                            {{ '1% X Rp. ' . number_format($bpjs_kesehatan->nominal, 0, ',', '.') }}
                                                            <input type="hidden" name="bpjs_kesehatan"
                                                                value="{{ round((1 / 100) * $bpjs_kesehatan->nominal) }}"
                                                                id="">
                                                        </div>
                                                    </div>
                                                </td>
                                            </tr>
                                            {{-- @php
                                            $explode_minus_1 = explode("|",$karyawan_supir_rit->minus_1);
                                            $explode_minus_2 = explode("|",$karyawan_supir_rit->minus_2);

                                            if(empty($explode_minus_1[0])){
                                                $minus_1 = null;
                                            }else{
                                                $minus_1 = $explode_minus_1[0];
                                            }

                                            if(empty($explode_minus_1[1])){
                                                $keterangan_minus_1 = null;
                                            }else{
                                                $keterangan_minus_1 = $explode_minus_1[1];
                                            }

                                            if(empty($explode_minus_2[0])){
                                                $minus_2 = null;
                                            }else{
                                                $minus_2 = $explode_minus_2[0];
                                            }

                                            if(empty($explode_minus_2[1])){
                                                $keterangan_minus_2 = null;
                                            }else{
                                                $keterangan_minus_2 = $explode_minus_2[1];
                                            }
                                        @endphp --}}
                                            @php
                                                if (empty($karyawan_supir_rit->minus_1)) {
                                                    $minus_1 = 0;
                                                    $keterangan_minus_1 = null;
                                                } else {
                                                    $explode_minus_1 = explode('|', $karyawan_supir_rit->minus_1);
                                                    $minus_1 = $explode_minus_1[0];
                                                    $keterangan_minus_1 = $explode_minus_1[1];
                                                }

                                                if (empty($karyawan_supir_rit->minus_2)) {
                                                    $minus_2 = 0;
                                                    $keterangan_minus_2 = null;
                                                } else {
                                                    $explode_minus_2 = explode('|', $karyawan_supir_rit->minus_2);
                                                    $minus_2 = $explode_minus_2[0];
                                                    $keterangan_minus_2 = $explode_minus_2[1];
                                                }
                                            @endphp
                                            <tr>
                                                <td style="font-weight: bold">Minus 1</td>
                                                <td colspan="2">
                                                    <input type="text" name="minus_1" value="{{ $minus_1 }}"
                                                        class="form-control" placeholder="Rp" id="">
                                                    <input type="text" name="keterangan_minus_1"
                                                        value="{{ $keterangan_minus_1 }}" class="form-control"
                                                        placeholder="Keterangan" id="">
                                                </td>
                                            </tr>
                                            <tr>
                                                <td style="font-weight: bold">Minus 2</td>
                                                <td colspan="2">
                                                    <input type="text" name="minus_2" value="{{ $minus_2 }}"
                                                        class="form-control" placeholder="Rp" id="">
                                                    <input type="text" name="keterangan_minus_2"
                                                        value="{{ $keterangan_minus_2 }}" class="form-control"
                                                        placeholder="Keterangan" id="">
                                                </td>
                                            </tr>
                                        </table>
                                    </div>
                                    <div class="card-footer">
                                        <button type="submit" class="btn btn-outline-success">Submit</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
@section('script')
    <script src="{{ URL::asset('public/assets/js/iziToast.min.js') }}"></script>
    <script src="{{ URL::asset('public/assets/plugins/sweet-alert2/sweetalert2.min.js') }}"></script>
    <script>
        $('#form-simpan').submit(function(e) {
            e.preventDefault();
            let formData = new FormData(this);
            $('#image-input-error').text('');
            $.ajax({
                type: 'POST',
                url: "{{ url('pengerjaan/hasil_kerja/supir_rit/' . $kode_pengerjaan . '/' . $nik . '/input_hasil_karyawan' . '/' . $month . '/' . $year . '/simpan') }}",
                data: formData,
                contentType: false,
                processData: false,
                success: (result) => {
                    if (result.success != false) {
                        var timerInterval
                        Swal.fire({
                            title: result.message_title + ' - ' + result.message_content,
                            html: 'I will close in <strong></strong> seconds.',
                            timer: 3000,
                            onBeforeOpen: function() {
                                Swal.showLoading()
                                timerInterval = setInterval(function() {
                                    Swal.getContent().querySelector('strong')
                                        .textContent = Swal.getTimerLeft()
                                }, 100)
                            },
                            onClose: function() {
                                clearInterval(timerInterval)
                            }
                        }).then(function(result) {
                            if (result.dismiss === Swal.DismissReason.timer) {
                                window.close();
                            }
                        })
                    } else {
                        iziToast.error({
                            title: result.success,
                            message: result.error
                        });
                    }
                },
                error: function(request, status, error) {
                    iziToast.error({
                        title: 'Error',
                        message: error,
                    });
                }
            });
        });
    </script>
@endsection
