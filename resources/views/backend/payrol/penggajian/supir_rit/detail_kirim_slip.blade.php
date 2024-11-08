@extends('layouts.backend.app')

@section('title')
    Detail Payrol Supir Rit
@endsection

@section('css')
    <link href="{{ URL::asset('public/assets/plugins/sweet-alert2/sweetalert2.min.css') }}" rel="stylesheet" type="text/css">
    <script src="{{ URL::asset('public/assets/js/pages/jquery.sweet-alert.init.js') }}"></script>
@endsection

@section('content')
    @component('components.breadcrumb')
        @slot('li_1')
            Payrol
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
            <div class="alert alert-danger border-0" role="alert">
                <strong>Informasi!</strong> Sistem Slip Gaji Elektronik dilakukan mengirim email dari sistem Payroll maksimal <b>50</b> ke penerima dan diberi waktu jeda maksimal 15 Menit agar tidak terjadi overload.
                Jika Sudah lebih dari 15 menit bisa dilakukan pengiriman kembali.
            </div>
            <form id="kirim_slip" method="POST" enctype="multipart/form-data">
                @csrf
            <div class="card">
                <div class="card-header">
                    <h5>Kode ID : {{ $new_data_pengerjaan->kode_pengerjaan }}
                        @if ($new_data_pengerjaan->status == 'n')
                            <i class="far fa-check-circle text-success"></i>
                        @endif
                        <button type="submit" class="btn btn-primary">
                            <svg xmlns="http://www.w3.org/2000/svg" width="1em" height="1em" viewBox="0 0 512 512">
                                <path fill="currentColor"
                                    d="m476.59 227.05l-.16-.07L49.35 49.84A23.56 23.56 0 0 0 27.14 52A24.65 24.65 0 0 0 16 72.59v113.29a24 24 0 0 0 19.52 23.57l232.93 43.07a4 4 0 0 1 0 7.86L35.53 303.45A24 24 0 0 0 16 327v113.31A23.57 23.57 0 0 0 26.59 460a23.94 23.94 0 0 0 13.22 4a24.55 24.55 0 0 0 9.52-1.93L476.4 285.94l.19-.09a32 32 0 0 0 0-58.8" />
                            </svg> Kirim Gaji
                        </button>
                        <button type="button" class="btn" style="background-color: #FD8B51; color: black"
                            onclick="window.location.href='{{ route('payrol.supir_rit.supir_rit_cek_email_slip_gaji', ['kode_pengerjaan' => $new_data_pengerjaan->kode_pengerjaan]) }}'">
                            <svg xmlns="http://www.w3.org/2000/svg" width="1.5em" height="1.5em" viewBox="0 0 512 512">
                                <path fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                    stroke-width="32"
                                    d="M320 96H88a40 40 0 0 0-40 40v240a40 40 0 0 0 40 40h334.73a40 40 0 0 0 40-40V239" />
                                <path fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                    stroke-width="32" d="m112 160l144 112l87-65.67" />
                                <circle cx="431.95" cy="128.05" r="47.95" fill="currentColor" />
                                <path fill="currentColor"
                                    d="M432 192a63.95 63.95 0 1 1 63.95-63.95A64 64 0 0 1 432 192m0-95.9a32 32 0 1 0 31.95 32a32 32 0 0 0-31.95-32" />
                            </svg>
                            Cek Email
                        </button>
                    </h5>
                </div>
                <div class="card-body">
                    <table id="datatables" class="table table-bordered dt-responsive nowrap"
                        style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Nama Karyawan</th>
                                <th>Email Karyawan</th>
                                <th>Jenis Pengerjaan</th>
                                <th>Nominal Gaji</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $total_all_gaji = [];
                            @endphp
                            @foreach ($pengerjaan_rit_weeklys as $key => $pengerjaan_rit_weekly)
                                @php
                                    $explode_tanggal_pengerjaans = explode('#', $new_data_pengerjaan['tanggal']);
                                    $exp_tanggals = array_filter($explode_tanggal_pengerjaans);
                                    $a = count($exp_tanggals);
                                    $exp_tgl_awal = explode('-', $exp_tanggals[1]);
                                    $exp_tgl_akhir = explode('-', $exp_tanggals[$a]);

                                    // $pengerjaan_rits
                                    $upah_dasar = [];

                                    for ($i = 0; $i < $a; $i++) {
                                        $pengerjaan_rits = \App\Models\PengerjaanRITHarian::where(
                                            'kode_pengerjaan',
                                            $new_data_pengerjaan['kode_pengerjaan'],
                                        )
                                            ->where(
                                                'karyawan_supir_rit_id',
                                                $pengerjaan_rit_weekly->karyawan_supir_rit_id,
                                            )
                                            ->get();
                                        if (empty($pengerjaan_rits[$i]->hasil_kerja_1)) {
                                            $tanggal_pengerjaan = 0;
                                            $hasil_kerja_1 = 0;
                                            $hasil_umk_rit = 0;
                                            $tarif_umk = 0;
                                            $dpb = 0;
                                            $jenis_umk = '-';
                                        } else {
                                            $explode_hasil_kerja_1 = explode('|', $pengerjaan_rits[$i]->hasil_kerja_1);
                                            $umk_rit = \App\Models\RitUMK::where(
                                                'id',
                                                $explode_hasil_kerja_1[0],
                                            )->first();
                                            if (empty($umk_rit)) {
                                                $hasil_kerja_1 = 0;
                                                $hasil_umk_rit = 0;
                                                $tarif_umk = 0;
                                                $dpb = 0;
                                                $jenis_umk = '-';
                                            } else {
                                                $hasil_kerja_1 = $umk_rit->tarif * $explode_hasil_kerja_1[1];
                                                $hasil_umk_rit = $umk_rit->kategori_upah;
                                                $tarif_umk = $umk_rit->tarif;
                                                $dpb =
                                                    ($pengerjaan_rits[$i]->dpb / 7) * $pengerjaan_rits[$i]->upah_dasar;
                                                if (empty($umk_rit->rit_tujuan)) {
                                                    $jenis_umk = '-';
                                                } else {
                                                    $jenis_umk =
                                                        $umk_rit->rit_tujuan->tujuan .
                                                        ' - ' .
                                                        $umk_rit->rit_kendaraan->jenis_kendaraan;
                                                }
                                                $total_upah_dasar = $hasil_kerja_1 + $dpb;
                                                array_push($upah_dasar, $total_upah_dasar);
                                            }
                                        }
                                    }

                                    $hasil_upah_dasar = array_sum($upah_dasar);

                                    if (empty($pengerjaan_rit_weekly->lembur)) {
                                        $lembur_1 = 0;
                                        $lembur_2 = 0;
                                        $hasil_lembur = 0;
                                    } else {
                                        $explode_lembur = explode('|', $pengerjaan_rit_weekly->lembur);
                                        $lembur_1 = $explode_lembur[1];
                                        $lembur_2 = $explode_lembur[2];
                                        $hasil_lembur = $explode_lembur[0];
                                    }

                                    $total_jam_lembur = floatval($lembur_1) + floatval($lembur_2);

                                    if ($new_data_pengerjaan['akhir_bulan'] == 'y') {
                                        if (empty($pengerjaan_rit_weekly->tunjangan_kehadiran)) {
                                            $tunjangan_kehadiran = 0;
                                        } else {
                                            $tunjangan_kehadiran = $pengerjaan_rit_weekly->tunjangan_kehadiran;
                                        }
                                    } else {
                                        $tunjangan_kehadiran = 0;
                                    }

                                    if ($new_data_pengerjaan['akhir_bulan'] == 'y') {
                                        if (empty($pengerjaan_rit_weekly->tunjangan_kerja)) {
                                            $tunjangan_kerja = 0;
                                        } else {
                                            $tunjangan_kerja = $pengerjaan_rit_weekly->tunjangan_kerja;
                                        }
                                    } else {
                                        $tunjangan_kerja = 0;
                                    }

                                    if (empty($pengerjaan_rit_weekly->uang_makan)) {
                                        $uang_makan = 0;
                                    } else {
                                        $uang_makan = $pengerjaan_rit_weekly->uang_makan;
                                    }

                                    if (empty($pengerjaan_rit_weekly->plus_1)) {
                                        $plus_1 = 0;
                                        $keterangan_plus_1 = '';
                                    } else {
                                        $explode_plus_1 = explode('|', $pengerjaan_rit_weekly->plus_1);
                                        $plus_1 = floatval($explode_plus_1[0]);
                                        $keterangan_plus_1 = $explode_plus_1[1];
                                    }

                                    if (empty($pengerjaan_rit_weekly->plus_2)) {
                                        $plus_2 = 0;
                                        $keterangan_plus_2 = '';
                                    } else {
                                        $explode_plus_2 = explode('|', $pengerjaan_rit_weekly->plus_2);
                                        $plus_2 = floatval($explode_plus_2[0]);
                                        $keterangan_plus_2 = $explode_plus_2[1];
                                    }

                                    if (empty($pengerjaan_rit_weekly->plus_3)) {
                                        $plus_3 = 0;
                                        $keterangan_plus_3 = '';
                                    } else {
                                        $explode_plus_3 = explode('|', $pengerjaan_rit_weekly->plus_3);
                                        $plus_3 = floatval($explode_plus_3[0]);
                                        $keterangan_plus_3 = $explode_plus_3[1];
                                    }

                                    $total_gaji =
                                        $hasil_upah_dasar +
                                        $plus_1 +
                                        $plus_2 +
                                        $plus_3 +
                                        $uang_makan +
                                        $hasil_lembur +
                                        $tunjangan_kerja +
                                        $tunjangan_kehadiran;

                                    if (empty($pengerjaan_rit_weekly->minus_1)) {
                                        $minus_1 = 0;
                                        $keterangan_minus_1 = '';
                                    } else {
                                        $explode_minus_1 = explode('|', $pengerjaan_rit_weekly->minus_1);
                                        $minus_1 = $explode_minus_1[0];
                                        $keterangan_minus_1 = $explode_minus_1[1];
                                    }

                                    if (empty($pengerjaan_rit_weekly->minus_2)) {
                                        $minus_2 = 0;
                                        $keterangan_minus_2 = '';
                                    } else {
                                        $explode_minus_2 = explode('|', $pengerjaan_rit_weekly->minus_2);
                                        $minus_2 = $explode_minus_2[0];
                                        $keterangan_minus_2 = $explode_minus_2[1];
                                    }

                                    if (empty($pengerjaan_rit_weekly->jht)) {
                                        $jht = 0;
                                    } else {
                                        $jht = $pengerjaan_rit_weekly->jht;
                                    }

                                    if (empty($pengerjaan_rit_weekly->bpjs_kesehatan)) {
                                        $bpjs_kesehatan = 0;
                                    } else {
                                        $bpjs_kesehatan = $pengerjaan_rit_weekly->bpjs_kesehatan;
                                    }

                                    if (empty($pengerjaan_rit_weekly->pensiun)) {
                                        $pensiun = 0;
                                    } else {
                                        $pensiun = $pengerjaan_rit_weekly->pensiun;
                                    }

                                    $total_upah_diterima =
                                        $total_gaji - $minus_1 - $minus_2 - $jht - $bpjs_kesehatan - $pensiun;
                                    
                                    $kirim_gaji = \App\Models\KirimGaji::where('pengerjaan_id',$pengerjaan_rit_weekly->id)
                                                                    ->where('kode_pengerjaan',$new_data_pengerjaan->kode_pengerjaan)
                                                                    ->first();
                                @endphp
                                <tr>
                                    <td>
                                        {{ $key + 1 }}
                                        <input type="hidden" name="id[]" value="{{ $pengerjaan_rit_weekly->id }}">
                                        <input type="hidden" name="nominal_gaji[]" value="{{ $total_upah_diterima }}">
                                    </td>
                                    <td>{{ $pengerjaan_rit_weekly->operator_supir_rit->nik . ' - ' . $pengerjaan_rit_weekly->operator_supir_rit->biodata_karyawan->nama }}
                                    </td>
                                    <td>{{ $pengerjaan_rit_weekly->operator_supir_rit->biodata_karyawan->email }}</td>
                                    <td>{{ 'Supir Rit - ' . $pengerjaan_rit_weekly->operator_supir_rit->rit_posisi->nama_posisi }}
                                    </td>
                                    <td>{{ 'Rp. ' . number_format($total_upah_diterima, 0, ',', '.') }}</td>
                                    <td>
                                        @if (empty($kirim_gaji))
                                            <span class="badge bg-primary">Belum Terkirim</span>
                                        @elseif($kirim_gaji->status == 'terkirim')
                                            <span class="badge bg-success">Terkirim</span>
                                        @else
                                            <span class="badge bg-danger">Gagal Terkirim</span>
                                        @endif
                                    </td>
                                    <td>
                                        <a href="{{ route('payrol.supir_rit.supir_rit_cek_slip_gaji', ['kode_pengerjaan' => $new_data_pengerjaan->kode_pengerjaan, 'id' => $pengerjaan_rit_weekly->id]) }}"
                                            class="btn btn-primary" target="_blank">Cek Gaji</a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            </form>
        </div>
    </div>
@endsection
@section('script')
    <script src="{{ URL::asset('public/assets/plugins/sweet-alert2/sweetalert2.min.js') }}"></script>
    <script>
        $('#kirim_slip').on('submit',function(e) {
            e.preventDefault();
            let formData = new FormData(this);
            $.ajax({
                type: 'POST',
                url: "{{ route('payrol.supir_rit.supir_rit_kirim_slip_gaji', ['kode_pengerjaan' => $new_data_pengerjaan->kode_pengerjaan]) }}",
                data: formData,
                contentType: false,
                processData: false,
                beforeSend: () => {
                    let timerInterval;
                    Swal.fire({
                        title: "Sedang Proses Dikirim!",
                        // timer: 2000,
                        timerProgressBar: true,
                        showConfirmButton: false,
                        didOpen: () => {
                            Swal.showLoading();
                            const timer = Swal.getPopup().querySelector("b");
                            timerInterval = setInterval(() => {
                            timer.textContent = `${Swal.getTimerLeft()}`;
                            }, 100);
                        },
                        willClose: () => {
                            clearInterval(timerInterval);
                        }
                        }).then((result) => {
                        /* Read more about handling dismissals below */
                        if (result.dismiss === Swal.DismissReason.timer) {
                            console.log("I was closed by the timer");
                        }
                    });
                },
                success: (result) => {
                    if (result.success != false) {
                        Swal.fire({
                            title: result.message_title,
                            text: result.message_content,
                            icon: "success"
                        });
                    } else {
                        Swal.fire({
                            title: 'Gagal Terkirim',
                            icon: "error"
                        });
                    }
                    console.log(result);
                },
                error: function(request, status, error) {
                    Swal.fire({
                        title: 'Error',
                        text: error,
                        icon: "error"
                    });
                }
            });
        });
    </script>
@endsection
