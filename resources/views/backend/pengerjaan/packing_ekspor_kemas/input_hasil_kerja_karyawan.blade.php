@extends('layouts.backend.app')

@section('title')
    Input Hasil Kerja Weekly Karyawan
@endsection

@section('css')
    <link href="{{ URL::asset('public/assets/css/iziToast.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ URL::asset('public/assets/plugins/sweet-alert2/sweetalert2.min.css') }}" rel="stylesheet" type="text/css">
    <script src="{{ URL::asset('public/assets/js/pages/jquery.sweet-alert.init.js') }}"></script>
@endsection

@section('content')
    @component('components.breadcrumb')
        @slot('li_1')
            Data Hasil Kerja Packing Lokal
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
    ?>
    <div class="row">
        <div class="col-12">
            <div class="card">
                {{-- <form action="{{ route('hasil_kerja.kemasEkspor.view_hasil_karyawan.simpan',['id' => $id, 'kode_pengerjaan' => $kode_pengerjaan,'nik' => $nik]) }}" method="post" enctype="multipart/form-data"> --}}
                <form id="form-simpan" method="post" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="akhir_bulan" value="{{ $new_data_pengerjaan->akhir_bulan }}" id="">
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
                                                <td class="text-center">:</td>
                                                <td>{{ $nik }}</td>
                                            </tr>
                                            <tr>
                                                <td>Nama Karyawan</td>
                                                <td class="text-center">:</td>
                                                <td>{{ $karyawan->nama }}</td>
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
                                                $upah_dasar = array_sum($upah);
                                                ?>
                                                <td>
                                                    Rp. {{ number_format($upah_dasar, 0, ',', '.') }}
                                                    <input type="hidden" name="upah_dasar" value="{{ round($upah_dasar) }}"
                                                        id="">
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>Upah Makan</td>
                                                <td class="text-center">:</td>
                                                <td>
                                                    @php
                                                        if (empty($pengerjaan_weekly->uang_makan)) {
                                                            $uang_makan = 0;
                                                        } else {
                                                            $uang_makan = $pengerjaan_weekly->uang_makan;
                                                        }
                                                    @endphp
                                                    <input type="text" name="uang_makan" class="form-control"
                                                        placeholder="Uang Makan" value="{{ $uang_makan }}" id="">
                                                </td>
                                            </tr>
                                            @if ($new_data_pengerjaan->akhir_bulan == $akhir_bulan)
                                                <tr>
                                                    <td>Tunjangan Kerja</td>
                                                    <td class="text-center">:</td>
                                                    <td>
                                                        <input type="text" class="form-control"
                                                            placeholder="Tunjangan Kerja"
                                                            value="{{ number_format($tunjangan_kerja, 0, ',', '.') }}"
                                                            id="" readonly>
                                                        <input type="hidden" name="tunjangan_kerja"
                                                            value="{{ $tunjangan_kerja }}" id="">
                                                    </td>
                                                </tr>
                                            @endif
                                            <?php
                                            $explode_plus_1 = explode('|', $pengerjaan_weekly->plus_1);
                                            $explode_plus_2 = explode('|', $pengerjaan_weekly->plus_2);
                                            $explode_plus_3 = explode('|', $pengerjaan_weekly->plus_3);
                                            
                                            if (empty($explode_plus_1[0])) {
                                                $plus_1 = null;
                                            } else {
                                                $plus_1 = $explode_plus_1[0];
                                            }
                                            
                                            if (empty($explode_plus_1[1])) {
                                                $keterangan_plus_1 = null;
                                            } else {
                                                $keterangan_plus_1 = $explode_plus_1[1];
                                            }
                                            
                                            if (empty($explode_plus_2[0])) {
                                                $plus_2 = null;
                                            } else {
                                                $plus_2 = $explode_plus_2[0];
                                            }
                                            
                                            if (empty($explode_plus_2[1])) {
                                                $keterangan_plus_2 = null;
                                            } else {
                                                $keterangan_plus_2 = $explode_plus_2[1];
                                            }
                                            
                                            if (empty($explode_plus_3[0])) {
                                                $plus_3 = null;
                                            } else {
                                                $plus_3 = $explode_plus_3[0];
                                            }
                                            
                                            if (empty($explode_plus_3[1])) {
                                                $keterangan_plus_3 = null;
                                            } else {
                                                $keterangan_plus_3 = $explode_plus_3[1];
                                            }
                                            
                                            ?>
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
                                        </table>
                                        {{-- <div class="form-group mb-3">
                                        <label for="">NIK</label>
                                        <input type="text" name="" class="form-control" id="">
                                    </div> --}}
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
                                                                        << /u> 15 Menit</label>
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
                                                                        << /u> 4 Jam</label>
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
                                                                        << /u> 4 Jam Kerja</label>
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
                                                                    value="{{ number_format($pulang_2, 0, ',', '.') }}"
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
                                                                        << /u> 5 Menit</label>
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
                                                                    Terlambar <u>></u> 5 Menit <u>
                                                                        << /u> 15 Menit</label>
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
                                                                        << /u> 1 Jam</label>
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
                                                                        << /u> 3 Jam</label>
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
                                                <?php
                                                
                                                ?>
                                                <tr>
                                                    <td>Potongan T. Kehadiran</td>
                                                    <td>
                                                        <input type="text" class="form-control"
                                                            value="{{ number_format($total_potongan_tk, 0, ',', '.') }}"
                                                            id="" readonly>
                                                        <input type="hidden" name="pot_tunjangan_kehadiran"
                                                            value="{{ $total_potongan_tk }}">
                                                    </td>
                                                </tr>
                                            @endif
                                            <tr>
                                                <td>Jaminan Hari Tua</td>
                                                <td colspan="2">
                                                    <div class="row">
                                                        <div
                                                            class="col-sm-2 form-label align-self-center mb-lg-0 text-center">
                                                            <?php
                                                            if ($pengerjaan_weekly->jht != null && $pengerjaan_weekly->jht != 0) {
                                                                $check_jht = 'checked';
                                                            } else {
                                                                $check_jht = null;
                                                            }
                                                            ?>
                                                            <input type="checkbox" name="check_jht" {{ $check_jht }}
                                                                class="form-check-input" id="">
                                                        </div>
                                                        <div class="col-sm-10">
                                                            <select name="jht" class="form-control" id="">
                                                                <option value="">-- Pilih --</option>
                                                                @if ($masa_kerja_tahun >= 15)
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
                                                                @endif
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
                                                        <div
                                                            class="col-sm-2 form-label align-self-center mb-lg-0 text-center">
                                                            <?php
                                                            if ($pengerjaan_weekly->bpjs_kesehatan != null && $pengerjaan_weekly->bpjs_kesehatan != 0) {
                                                                $check_bpjs_kesehatan = 'checked';
                                                            } else {
                                                                $check_bpjs_kesehatan = null;
                                                            }
                                                            ?>
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
                                            <?php
                                            $explode_minus_1 = explode('|', $pengerjaan_weekly->minus_1);
                                            $explode_minus_2 = explode('|', $pengerjaan_weekly->minus_2);
                                            
                                            if (empty($explode_minus_1[0])) {
                                                $minus_1 = null;
                                            } else {
                                                $minus_1 = $explode_minus_1[0];
                                            }
                                            
                                            if (empty($explode_minus_1[1])) {
                                                $keterangan_minus_1 = null;
                                            } else {
                                                $keterangan_minus_1 = $explode_minus_1[1];
                                            }
                                            
                                            if (empty($explode_minus_2[0])) {
                                                $minus_2 = null;
                                            } else {
                                                $minus_2 = $explode_minus_2[0];
                                            }
                                            
                                            if (empty($explode_minus_2[1])) {
                                                $keterangan_minus_2 = null;
                                            } else {
                                                $keterangan_minus_2 = $explode_minus_2[1];
                                            }
                                            ?>
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
                url: "{{ url('pengerjaan/hasil_kerja/kemas_ekspor/' . $id . '/' . $kode_pengerjaan . '/' . $nik . '/input_hasil_karyawan/simpan') }}",
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
