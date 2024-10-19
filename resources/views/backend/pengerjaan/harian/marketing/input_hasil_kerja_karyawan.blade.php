@extends('layouts.backend.master_no_header')

@section('css')
    <link href="{{ URL::asset('public/assets/css/iziToast.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ URL::asset('public/assets/plugins/sweet-alert2/sweetalert2.min.css') }}" rel="stylesheet" type="text/css">
    <script src="{{ URL::asset('public/assets/js/pages/jquery.sweet-alert.init.js') }}"></script>
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
    <?php
    $akhir_bulan = 'y';
    ?>
    <div class="row">
        <div class="col-12">
            <div class="card">
                {{-- <form
                    action="{{ route('hasil_kerja.marketing.simpan', ['id' => $id, 'nik' => $nik, 'kode_pengerjaan' => $kode_pengerjaan, 'month' => $month, 'year' => $year]) }}"
                    method="post" enctype="multipart/form-data"> --}}
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
                                                <td>
                                                    <input type="hidden" name="operator_harian_karyawan_id"
                                                        value="{{ $karyawan_harian->id }}">
                                                    {{ $karyawan_harian->nik }}
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>Nama Karyawan</td>
                                                <td class="text-center">:</td>
                                                <td>{{ $karyawan_harian->biodata_karyawan->nama }}</td>
                                            </tr>
                                            <tr>
                                                <td>Masa Kerja</td>
                                                <td class="text-center">:</td>
                                                <td>{{ $masa_kerja }}</td>
                                            </tr>
                                            <tr>
                                                <td>Upah 1 Hari</td>
                                                <td class="text-center">:</td>
                                                <td>
                                                    Rp. {{ number_format($karyawan_harian->upah_dasar, 0, ',', '.') }}
                                                    <input type="hidden" name="upah_dasar"
                                                        value="{{ $karyawan_harian->upah_dasar }}" id="">
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>Upah Dasar</td>
                                                <td class="text-center">:</td>
                                                <td>Rp.
                                                    {{ number_format($karyawan_harian->upah_dasar_weekly, 0, ',', '.') }}
                                                </td>
                                                <input type="hidden" name="tunjangan_kerja"
                                                    value="{{ $karyawan_harian->tunjangan_kerja_nominal->nominal }}">
                                            </tr>
                                            @php
                                                if (!empty($karyawan_harian->plus_1)) {
                                                    $explode_plus_1 = explode('|', $karyawan_harian->plus_1);
                                                    $hasil_plus_1 = $explode_plus_1[0];
                                                    $keterangan_plus_1 = $explode_plus_1[1];
                                                } else {
                                                    $hasil_plus_1 = 0;
                                                    $keterangan_plus_1 = null;
                                                }
                                                
                                                if (!empty($karyawan_harian->plus_2)) {
                                                    $explode_plus_2 = explode('|', $karyawan_harian->plus_2);
                                                    $hasil_plus_2 = $explode_plus_2[0];
                                                    $keterangan_plus_2 = $explode_plus_2[1];
                                                } else {
                                                    $hasil_plus_2 = 0;
                                                    $keterangan_plus_2 = null;
                                                }
                                                
                                                if (!empty($karyawan_harian->plus_3)) {
                                                    $explode_plus_3 = explode('|', $karyawan_harian->plus_3);
                                                    $hasil_plus_3 = $explode_plus_3[0];
                                                    $keterangan_plus_3 = $explode_plus_3[1];
                                                } else {
                                                    $hasil_plus_3 = 0;
                                                    $keterangan_plus_3 = null;
                                                }
                                                
                                            @endphp
                                            <tr>
                                                <td style="font-weight: bold">PLUS 1</td>
                                                <td class="text-center">:</td>
                                                <td>
                                                    <div class="form-group">
                                                        <input type="text" name="plus_1" class="form-control"
                                                            placeholder="Rp." value="{{ $hasil_plus_1 }}" id="">
                                                        <input type="text" name="keterangan_plus_1" class="form-control"
                                                            placeholder="Keterangan" value="{{ $keterangan_plus_1 }}"
                                                            id="">
                                                    </div>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td style="font-weight: bold">PLUS 2</td>
                                                <td class="text-center">:</td>
                                                <td>
                                                    <div class="form-group">
                                                        <input type="text" name="plus_2" value="{{ $hasil_plus_2 }}"
                                                            class="form-control" placeholder="Rp." id="">
                                                        <input type="text" name="keterangan_plus_2"
                                                            value="{{ $keterangan_plus_2 }}" class="form-control"
                                                            placeholder="Keterangan" id="">
                                                    </div>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td style="font-weight: bold">PLUS 3</td>
                                                <td class="text-center">:</td>
                                                <td>
                                                    <div class="form-group">
                                                        <input type="text" name="plus_3" value="{{ $hasil_plus_3 }}"
                                                            class="form-control" placeholder="Rp." id="">
                                                        <input type="text" name="keterangan_plus_3"
                                                            value="{{ $keterangan_plus_3 }}" class="form-control"
                                                            placeholder="Keterangan" id="">
                                                    </div>
                                                </td>
                                            </tr>
                                            @php
                                                if (!empty($karyawan_harian->lembur)) {
                                                    $explode_lembur = explode('|', $karyawan_harian->lembur);
                                                    $hasil_lembur_1 = $explode_lembur[1];
                                                    $hasil_lembur_2 = $explode_lembur[2];
                                                } else {
                                                    $hasil_lembur_1 = null;
                                                    $hasil_lembur_2 = null;
                                                }
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
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>Uang Makan</td>
                                                <td class="text-center">:</td>
                                                <td>
                                                    <input type="text" name="uang_makan" class="form-control"
                                                        placeholder="Uang Makan"
                                                        value="{{ $karyawan_harian->uang_makan }}" id="">
                                                </td>
                                            </tr>
                                            {{-- @php
                                                $absensi = DB::connection('absensi')->table('att_presensi_info')->where('pin',973)->get();
                                                    dd($absensi);
                                            @endphp --}}
                                            @php
                                                $exp_tanggal = array_filter(explode('#', $new_data_pengerjaan['tanggal']));
                                                // dd($exp_tanggal);
                                                $a = count($exp_tanggal);
                                                // var_dump($a);
                                                $exp_tgl_awal = explode('-', $exp_tanggal[1]);
                                                $exp_tgl_akhir = explode('-', $exp_tanggal[$a]);
                                                $explode_posting = explode('-', $new_data_pengerjaan['date']);
                                                
                                            @endphp
                                            @for ($b = 1; $b <= $a; $b++)
                                                @php
                                                    $exp_per_tanggal = explode('-', $exp_tanggal[$b]);
                                                    if ($exp_per_tanggal[2] <= 9) {
                                                        $d_date = $exp_per_tanggal[2];
                                                    } else {
                                                        $d_date = $exp_per_tanggal[2];
                                                    }
                                                    
                                                    if ($exp_per_tanggal[1] <= 9) {
                                                        $m_date = $exp_per_tanggal[1];
                                                    } else {
                                                        $m_date = $exp_per_tanggal[1];
                                                    }
                                                    
                                                    if ($exp_tgl_awal[2] != $exp_tgl_akhir[2] && $exp_per_tanggal[2] == 1) {
                                                        if ($explode_posting[1] == 12) {
                                                            $y_date = $explode_posting[2] + 1;
                                                        } else {
                                                            $y_date = $explode_posting[2];
                                                        }
                                                    } else {
                                                        if ($explode_posting[1] == 1 && $exp_per_tanggal[2] <= 9) {
                                                            $y_date = $explode_posting[0] - 1;
                                                        } else {
                                                            $y_date = $explode_posting[0];
                                                        }
                                                    }
                                                    $tgl_digunakan = $y_date . '-' . $m_date . '-' . $d_date;
                                                    // dd($tgl_digunakan);
                                                    // var_dump($exp_per_tanggal);
                                                    
                                                    //Absen Terlambat Pribadi
                                                    $absensi_terlambat = \App\Models\PresensiInfo::where('scan_date', 'LIKE', "%$tgl_digunakan%")
                                                        ->where('pin', $karyawan_harian->pin)
                                                        ->where('status', 3)
                                                        ->first();
                                                    if (empty($absensi_terlambat)) {
                                                        $jam_terlambat = 0;
                                                        $menit_terlambat = 0;
                                                    } else {
                                                        // $jam_kerja = 7;
                                                        $exp_keterangan_terlambat = explode('@', $absensi_terlambat->keterangan);
                                                        if (empty($exp_keterangan_terlambat[1]) && empty($exp_keterangan_terlambat[2]) && empty($exp_keterangan_terlambat[3])) {
                                                            $jam_terlambat = 0;
                                                            $menit_terlambat = 0;
                                                        } else {
                                                            $jam_ket_terlambat = strtotime($exp_keterangan_terlambat[1]);
                                                            $jam_dtg_terlambat = strtotime($absensi_terlambat->jam_datang_telat);
                                                            $menit_telat_per_tanggal = ($jam_dtg_terlambat - $jam_ket_terlambat) / 60;
                                                            $jam_terlambat = floor($menit_telat_per_tanggal / 60);
                                                            $menit_terlambat = (($menit_telat_per_tanggal - $jam_terlambat * 60) / 100) * 1.666;
                                                        }
                                                        // dd($jam_terlambat);
                                                        // dd($jam_dtg_terlambat);
                                                    }
                                                    $total_absensi_terlambat = $jam_terlambat + $menit_terlambat;
                                                    // dd($total_absensi_terlambat);
                                                    
                                                    //9 Absen Pulang Awal Pribadi
                                                    $get_pulang_awal_pribadi = \App\Models\PresensiInfo::where('scan_date', 'LIKE', "%$tgl_digunakan%")
                                                        ->where('pin', $karyawan_harian->pin)
                                                        ->where('status', 9)
                                                        ->orderBy('scan_date', 'desc')
                                                        ->first();
                                                    // dd($get_pulang_awal_pribadi);
                                                    if (empty($get_pulang_awal_pribadi)) {
                                                        $jam_plg_awal_pribadi = 0;
                                                        $menit_plg_awal_pribadi = 0;
                                                    } else {
                                                        $jam_pulang_awal_pribadi = strtotime($get_pulang_awal_pribadi->scan_date);
                                                        if (mb_ereg('@', $get_pulang_awal_pribadi['keterangan'])) {
                                                            $split_keterangan_pulang_awal_pribadi = explode('@', $get_pulang_awal_pribadi['keterangan']);
                                                            $penyesuaian_istirahat_pulang_awal_pribadi = explode(':', $split_keterangan_pulang_awal_pribadi[2]);
                                                            $penyesuaian_pulang_awal_pribadi = explode(':', $split_keterangan_pulang_awal_pribadi[3]);
                                                    
                                                            $jam_istirahat_pulang_awal_pribadi = $split_keterangan_pulang_awal_pribadi[2];
                                                            $jam_istirahat_pulang_awal_pribadi = strtotime($jam_istirahat_pulang_awal_pribadi);
                                                    
                                                            $max_pulang_awal_pribadi = strtotime($split_keterangan_pulang_awal_pribadi[3]);
                                                        } else {
                                                            $max_pulang_awal_pribadi = strtotime('17:00');
                                                            $jam_istirahat_awal_pribadi = strtotime('12:00');
                                                        }
                                                    
                                                        $pulang_dijam_istirahat_pulang_awal_pribadi = ($jam_pulang_awal_pribadi - $jam_istirahat_pulang_awal_pribadi) / 60;
                                                    
                                                        if ($jam_pulang_awal_pribadi <= $jam_istirahat_pulang_awal_pribadi || $pulang_dijam_istirahat_pulang_awal_pribadi <= 60) {
                                                            $durasi_pulang_awal_pribadi = ($max_pulang_awal_pribadi - $jam_pulang_awal_pribadi) / 60 - 60;
                                                        } else {
                                                            $durasi_pulang_awal_pribadi = ($max_pulang_awal_pribadi - $jam_pulang_awal_pribadi) / 60;
                                                        }
                                                    
                                                        $jam_plg_awal_pribadi = floor($durasi_pulang_awal_pribadi / 60);
                                                        $menit_plg_awal_pribadi = (($durasi_pulang_awal_pribadi - $jam_plg_awal_pribadi * 60) / 100) * 1.666;
                                                    }
                                                    $total_plg_awal_pribadi = $jam_plg_awal_pribadi + $menit_plg_awal_pribadi;
                                                    // dd($total_plg_awal_pribadi);
                                                    
                                                    //10 Absen Pulang Awal - Sakit
                                                    $get_pulang_awal_sakit = \App\Models\PresensiInfo::where('scan_date', 'LIKE', "%$tgl_digunakan%")
                                                        ->where('pin', $karyawan_harian->pin)
                                                        ->where('status', 10)
                                                        ->orderBy('scan_date', 'desc')
                                                        ->first();
                                                    
                                                    if (empty($get_pulang_awal_sakit)) {
                                                        $jam_plg_awal_sakit = 0;
                                                        $menit_plg_awal_sakit = 0;
                                                    } else {
                                                        $jam_pulang_awal_sakit = strtotime($get_pulang_awal_sakit->scan_date);
                                                        if (mb_ereg('@', $get_pulang_awal_sakit['keterangan'])) {
                                                            $split_keterangan_pulang_awal_sakit = explode('@', $get_pulang_awal_sakit['keterangan']);
                                                            $penyesuaian_istirahat_pulang_awal_sakit = explode(':', $split_keterangan_pulang_awal_sakit[2]);
                                                            $penyesuaian_pulang_awal_sakit = explode(':', $split_keterangan_pulang_awal_sakit[3]);
                                                    
                                                            $jam_istirahat_pulang_awal_sakit = $split_keterangan_pulang_awal_sakit[2];
                                                            $jam_istirahat_pulang_awal_sakit = strtotime($jam_istirahat_pulang_awal_sakit);
                                                    
                                                            $max_pulang_awal_sakit = strtotime($split_keterangan_pulang_awal_sakit[3]);
                                                        } else {
                                                            $max_pulang_awal_sakit = strtotime('17:00');
                                                            $jam_istirahat_awal_sakit = strtotime('12:00');
                                                        }
                                                    
                                                        $pulang_dijam_istirahat_pulang_awal_sakit = ($jam_pulang_awal_sakit - $jam_istirahat_pulang_awal_sakit) / 60;
                                                    
                                                        if ($jam_pulang_awal_sakit <= $jam_istirahat_pulang_awal_sakit || $pulang_dijam_istirahat_pulang_awal_sakit <= 60) {
                                                            $durasi_pulang_awal_sakit = ($max_pulang_awal_sakit - $jam_pulang_awal_sakit) / 60 - 60;
                                                        } else {
                                                            $durasi_pulang_awal_sakit = ($max_pulang_awal_sakit - $jam_pulang_awal_sakit) / 60;
                                                        }
                                                    
                                                        $jam_plg_awal_sakit = floor($durasi_pulang_awal_sakit / 60);
                                                        $menit_plg_awal_sakit = (($durasi_pulang_awal_sakit - $jam_plg_awal_sakit * 60) / 100) * 1.666;
                                                    }
                                                    $total_plg_awal_sakit = $jam_plg_awal_sakit + $menit_plg_awal_sakit;
                                                    
                                                    $total_jumlah_plg_awal_pribadi_plg_awal_sakit = $total_plg_awal_pribadi + $total_plg_awal_sakit;
                                                    // dd($total_jumlah_plg_awal_pribadi_plg_awal_sakit);
                                                    
                                                    // Ijin Keluar Masuk
                                                    $get_ikm = \App\Models\KeluarMasuk::where('tanggal_ijin', $tgl_digunakan)
                                                        ->where('nik', $karyawan_harian->nik)
                                                        ->first();
                                                    // dd($get_ikm);
                                                    if (empty($get_ikm)) {
                                                        $jam_ijin = 0;
                                                        $menit_ijin = 0;
                                                    } else {
                                                        $jk_per_nik = strtotime($get_ikm['jam_keluar']);
                                                        $jd_per_nik = strtotime($get_ikm['jam_datang']);
                                                        $ist_awal_per_nik = strtotime($get_ikm['jam_istirahat']);
                                                        $ist_akhir_per_nik = strtotime($get_ikm['jam_istirahat']);
                                                        $assembly_istirahat_akhir = explode(':', $get_ikm['jam_istirahat']);
                                                        $ist_akhir_per_nik = strtotime($assembly_istirahat_akhir[0] . ':59:59');
                                                        if ($jk_per_nik >= $ist_awal_per_nik && $jk_per_nik <= $ist_akhir_per_nik) {
                                                            $jk_per_nik = strtotime('13:00:00');
                                                        }
                                                        if ($jd_per_nik >= $ist_awal_per_nik && $jd_per_nik <= $ist_akhir_per_nik) {
                                                            $jd_per_nik = strtotime('11:59:59');
                                                        }
                                                        if ($jk_per_nik < $ist_awal_per_nik && $jd_per_nik > $ist_akhir_per_nik) {
                                                            $durasi_ijin = ($jd_per_nik - $jk_per_nik) / 60 - 1;
                                                        } else {
                                                            $durasi_ijin = ($jd_per_nik - $jk_per_nik) / 60;
                                                        }
                                                        $jam_ijin = floor($durasi_ijin / 60);
                                                        //ket : 1 menit normal dihitung 1.666 menit (1 jam = 100 menit)
                                                        $menit_ijin = (($durasi_ijin - $jam_ijin * 60) / 100) * 1.666;
                                                    }
                                                    $total_ijin_km = $jam_ijin + $menit_ijin;
                                                    // dd($total_ijin_km);
                                                    
                                                    $checklog = \App\Models\FtmAttLog::where('scan_date', 'LIKE', "%$tgl_digunakan%")->where('pin', $karyawan_harian->pin);
                                                    $presensi = \App\Models\PresensiInfo::where('scan_date', 'LIKE', "%$tgl_digunakan%")
                                                        ->where('pin', $karyawan_harian->pin)
                                                        ->where(function ($query) {
                                                            $query
                                                                ->where('status', 1)
                                                                ->orWhere('status', 2)
                                                                ->orWhere('status', 3)
                                                                ->orWhere('status', 5)
                                                                ->orWhere('status', 8)
                                                                ->orWhere('status', 9)
                                                                ->orWhere('status', 10);
                                                        });
                                                    // dd($presensi->first());
                                                    if ($checklog->count() > 0 || $presensi->count() > 0) {
                                                        if ($karyawan_harian['hari_kerja'] == 5) {
                                                            $jam_kerja_seharusnya = 8;
                                                        } else {
                                                            $jam_kerja_seharusnya = 7;
                                                        }
                                                        //##lihat jam kerja per nik per hari
                                                        $jam_kerja = $jam_kerja_seharusnya - $total_ijin_km - $total_absensi_terlambat - $total_jumlah_plg_awal_pribadi_plg_awal_sakit;
                                                        // $exp_menit=explode(".",$jam_kerja);
                                                        // echo json_encode($exp_menit);
                                                        // $menit_kerja=(("0.".$exp_menit[1])*60);
                                                        // echo json_encode($menit_kerja);
                                                        // var_dump($jam_kerja_seharusnya);
                                                        // dd($exp_menit);

                                                        // ok
                                                        if (empty($karyawan_harian->hasil_kerja)) {
                                                            $post_jam_kerja = $jam_kerja / $jam_kerja_seharusnya;
                                                        } else {
                                                            $array_b = $b - 1;
                                                            $explode_isi_jam_kerja = explode('|', $karyawan_harian->hasil_kerja);
                                                            $post_jam_kerja = $explode_isi_jam_kerja[$array_b];
                                                        }
                                                        // end ok

                                                        // $post_jam_kerja = $jam_kerja / $jam_kerja_seharusnya;

                                                        // $post_jam_kerja = $jam_kerja / $jam_kerja_seharusnya;
                                                        // dd($post_jam_kerja);
                                                    } else {
                                                        // ok
                                                        if (empty($karyawan_harian->hasil_kerja)) {
                                                            $jam_kerja = 0;
                                                            $menit_kerja = 0;
                                                            if ($b == $a) {
                                                                $post_jam_kerja = 1;
                                                            } else {
                                                                $post_jam_kerja = 0;
                                                            }
                                                        } else {
                                                            $jam_kerja = 0;
                                                            $menit_kerja = 0;
                                                            $array_b = $b - 1;
                                                            $explode_isi_jam_kerja = explode('|', $karyawan_harian->hasil_kerja);
                                                            $post_jam_kerja = $explode_isi_jam_kerja[$array_b];
                                                        }
                                                        // end ok
                                                        // dd($post_jam_kerja);
                                                    }

                                                    // dd($post_jam_kerja);
                                                    
                                                @endphp
                                                <tr>
                                                    <td style="font-weight: bold">
                                                        {{ \Carbon\Carbon::parse($tgl_digunakan)->format('d-m-Y') }}</td>
                                                    <td class="text-center">:</td>
                                                    <td>
                                                        <div class="row">
                                                            <div class="col-md-4">
                                                                <div class="form-floating">
                                                                    <input type="text" class="form-control text-center"
                                                                        value="{{ floor($jam_kerja) }}" readonly
                                                                        id="floatingInput">
                                                                    <label for="floatingInput">Jam</label>
                                                                </div>
                                                            </div>
                                                            {{-- <div class="col-md-3">
                                                                <div class="form-floating">
                                                                    <input type="text" class="form-control text-center" value="" readonly id="floatingInput">
                                                                    <label for="floatingInput">Menit</label>
                                                                </div>
                                                            </div> --}}
                                                            <div class="col-md-4">
                                                                <div class="form-floating">
                                                                    <input type="text" class="form-control text-center"
                                                                        readonly value="=" id="">
                                                                    <label for="floatingInput"></label>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-4">
                                                                <div class="form-floating">
                                                                    <input type="text" class="form-control text-center"
                                                                        name="jam_kerja_{{ $b }}"
                                                                        value="{{ round($post_jam_kerja, 4) }}"
                                                                        id="">
                                                                    {{-- <input type="text" class="form-control text-center" value="" id=""> --}}
                                                                    <label for="floatingInput">Hasil</label>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </td>
                                                </tr>
                                            @endfor
                                        </table>
                                        <input type="hidden" name="data_for" value="{{ $a }}">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-7">
                                <div class="card">
                                    <div class="card-header">
                                        <div class="card-title">Potongan</div>
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
                                                                    value="{{ number_format($pulang_2*75000, 0, ',', '.') }}"
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
                                                                    value="{{ number_format($telat_5 * 75000, 0, ',', '.') }}"
                                                                    id="" readonly>
                                                            </div>
                                                        </div>
                                                    </td>
                                                </tr>
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
                                                <td>BPJS Ketenagakerjaan</td>
                                                <td colspan="2">
                                                    <div class="row">
                                                        <div
                                                            class="col-sm-2 form-label align-self-center mb-lg-0 text-center">
                                                            <?php
                                                            if ($karyawan_harian->jht == 'y') {
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
                                                                {{-- @if ($masa_kerja_tahun >= 15)
                                                                    <option value="95823">Masa Kerja &nbsp;&nbsp;0 - 10
                                                                        Tahun | Rp. 95.823</option>
                                                                    <option value="97323">Masa Kerja 10 - 15 Tahun | Rp.
                                                                        97.323</option>
                                                                    <option value="98823" selected>Masa Kerja
                                                                        &nbsp;&nbsp;&nbsp;&nbsp;> 15 Tahun | Rp. 98.823
                                                                    </option>
                                                                @elseif($masa_kerja_tahun >= 10 && $masa_kerja_tahun <= 15 && $masa_kerja_hari >= 1)
                                                                    <option value="95823">Masa Kerja &nbsp;&nbsp;0 - 10
                                                                        Tahun | Rp. 95.823</option>
                                                                    <option value="97323" selected>Masa Kerja 10 - 15
                                                                        Tahun | Rp. 97.323</option>
                                                                    <option value="98823">Masa Kerja
                                                                        &nbsp;&nbsp;&nbsp;&nbsp;> 15 Tahun | Rp. 98.823
                                                                    </option>
                                                                @elseif($masa_kerja_tahun <= 10 || $masa_kerja_hari >= 1)
                                                                    <option value="95823" selected>Masa Kerja
                                                                        &nbsp;&nbsp;0 - 10 Tahun | Rp. 95.823</option>
                                                                    <option value="97323">Masa Kerja 10 - 15 Tahun | Rp.
                                                                        97.323</option>
                                                                    <option value="98823">Masa Kerja
                                                                        &nbsp;&nbsp;&nbsp;&nbsp;> 15 Tahun | Rp. 98.823
                                                                    </option>
                                                                @endif --}}
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
                                                            if ($karyawan_harian->bpjs == 'y') {
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
                                            @php
                                                if (!empty($karyawan_harian->minus_1)) {
                                                    $explode_minus_1 = explode('|', $karyawan_harian->minus_1);
                                                    $hasil_minus_1 = $explode_minus_1[0];
                                                    $keterangan_minus_1 = $explode_minus_1[1];
                                                } else {
                                                    $hasil_minus_1 = null;
                                                    $keterangan_minus_1 = null;
                                                }
                                                
                                                if (!empty($karyawan_harian->minus_2)) {
                                                    $explode_minus_2 = explode('|', $karyawan_harian->minus_2);
                                                    $hasil_minus_2 = $explode_minus_2[0];
                                                    $keterangan_minus_2 = $explode_minus_2[1];
                                                } else {
                                                    $hasil_minus_2 = null;
                                                    $keterangan_minus_2 = null;
                                                }
                                                
                                            @endphp
                                            <tr>
                                                <td style="font-weight: bold">Minus 1</td>
                                                <td colspan="2">
                                                    <input type="text" name="minus_1" value="{{ $hasil_minus_1 }}"
                                                        class="form-control" placeholder="Rp" id="">
                                                    <input type="text" name="keterangan_minus_1"
                                                        value="{{ $keterangan_minus_1 }}" class="form-control"
                                                        placeholder="Keterangan" id="">
                                                </td>
                                            </tr>
                                            <tr>
                                                <td style="font-weight: bold">Minus 2</td>
                                                <td colspan="2">
                                                    <input type="text" name="minus_2" value="{{ $hasil_minus_2 }}"
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
                url: "{{ url('pengerjaan/hasil_kerja/marketing/' . $id. '/' . $kode_pengerjaan . '/' .$nik. '/' .$month. '/' .$year . '/input_hasil/simpan') }}",
                data: formData,
                contentType: false,
                processData: false,
                success: (result) => {
                    if (result.success != false) {
                        // iziToast.success({
                        //     title: result.message_title,
                        //     message: result.message_content
                        // });
                        // setTimeOut(window.close(),3000);
                        // setTimeout(function(){
                        //     window.close();
                        // }, 3000);
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
