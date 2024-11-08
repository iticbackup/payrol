@extends('layouts.backend.app')

@section('title')
    Detail Payrol Harian
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
            <form id="kirim_slip" method="post" class="card" enctype="multipart/form-data">
                @csrf
                <div class="card-header">
                    <h5>
                        Kode ID : {{ $new_data_pengerjaan->kode_pengerjaan }}
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
                            onclick="window.location.href='{{ route('payrol.harian.harian_cek_email_slip_gaji', ['kode_pengerjaan' => $new_data_pengerjaan->kode_pengerjaan]) }}'">
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
                            @foreach ($pengerjaan_harians as $key => $pengerjaan_harian)
                                @php
                                    $explode_tanggal_pengerjaans = explode('#',$new_data_pengerjaan->tanggal);
                                    $exp_tanggals = array_filter($explode_tanggal_pengerjaans);
                                    $a = count($exp_tanggals);

                                    $exp_tgl_awal = explode('-', $exp_tanggals[1]);
                                    $exp_tgl_akhir = explode('-', $exp_tanggals[$a]);

                                    if (empty($pengerjaan_harian->lembur)) {
                                        $hasil_lembur = 0;
                                        $lembur_1 = 0;
                                        $lembur_2 = 0;
                                    }else{
                                        $exlode_lembur = explode("|",$pengerjaan_harian->lembur);
                                        if (empty($exlode_lembur)) {
                                            $hasil_lembur = 0;
                                            $lembur_1 = 0;
                                            $lembur_2 = 0;
                                        }else{
                                            $hasil_lembur = $exlode_lembur[0];
                                            $lembur_1 = $exlode_lembur[1];
                                            $lembur_2 = $exlode_lembur[2];
                                        }
                                    }

                                    $total_jam_lembur = floatval($lembur_1)+floatval($lembur_2);

                                    if (empty($pengerjaan_harian->upah_dasar_weekly)) {
                                        $upah_dasar_weekly = 0;
                                    }else{
                                        $upah_dasar_weekly = $pengerjaan_harian->upah_dasar_weekly;
                                    }

                                    if($new_data_pengerjaan['akhir_bulan'] == 'y'){
                                        if (empty($pengerjaan_harian->tunjangan_kehadiran)) {
                                            $tunjangan_kehadiran = 0;
                                        }else{
                                            $tunjangan_kehadiran = $pengerjaan_harian->tunjangan_kehadiran;
                                        }
                                    }else{
                                        $tunjangan_kehadiran = 0;
                                    }

                                    if ($new_data_pengerjaan['akhir_bulan'] == 'y') {
                                        if (empty($pengerjaan_harian->tunjangan_kerja)) {
                                            $tunjangan_kerja = 0;
                                        }else{
                                            $tunjangan_kerja = $pengerjaan_harian->tunjangan_kerja;
                                        }
                                    }else{
                                        $tunjangan_kerja = 0;
                                    }

                                    if (empty($pengerjaan_harian->uang_makan)) {
                                        $uang_makan = 0;
                                    }else{
                                        $uang_makan = $pengerjaan_harian->uang_makan;
                                    }

                                    if (empty($pengerjaan_harian->plus_1)) {
                                        $plus_1 = 0;
                                        $ket_plus_1 = "";
                                    }else{
                                        $explode_plus_1 = explode("|",$pengerjaan_harian->plus_1);
                                        $plus_1 = intval($explode_plus_1[0]);
                                        $ket_plus_1 = $explode_plus_1[1];
                                    }

                                    if (empty($pengerjaan_harian->plus_2)) {
                                        $plus_2 = 0;
                                        $ket_plus_2 = "";
                                    }else{
                                        $explode_plus_2 = explode("|",$pengerjaan_harian->plus_2);
                                        $plus_2 = intval($explode_plus_2[0]);
                                        $ket_plus_2 = $explode_plus_2[1];
                                    }

                                    if (empty($pengerjaan_harian->plus_3)) {
                                        $plus_3 = 0;
                                        $ket_plus_3 = "";
                                    }else{
                                        $explode_plus_3 = explode("|",$pengerjaan_harian->plus_3);
                                        $plus_3 = intval($explode_plus_3[0]);
                                        $ket_plus_3 = $explode_plus_3[1];
                                    }

                                    if (empty($pengerjaan_harian->minus_1)) {
                                        $minus_1 = 0;
                                        $ket_minus_1 = "";
                                    }else{
                                        $explode_minus_1 = explode("|",$pengerjaan_harian->minus_1);
                                        if (empty($explode_minus_1[0])) {
                                            $minus_1 = 0;
                                        }else{
                                            $minus_1 = intval($explode_minus_1[0]);
                                        }
                                        $ket_minus_1 = $explode_minus_1[1];
                                    }

                                    if (empty($pengerjaan_harian->minus_2)) {
                                        $minus_2 = 0;
                                        $ket_minus_2 = "";
                                    }else{
                                        $explode_minus_2 = explode("|",$pengerjaan_harian->minus_2);
                                        if (empty($explode_minus_2[0])) {
                                            $minus_2 = 0;
                                        }else{
                                            $minus_2 = intval($explode_minus_2[0]);
                                        }
                                        $ket_minus_2 = $explode_minus_2[1];
                                    }

                                    if (empty($pengerjaan_harian->jht)) {
                                        $jht = 0;
                                    }else{
                                        $jht = intval($pengerjaan_harian->jht);
                                    }

                                    if (empty($pengerjaan_harian->bpjs_kesehatan)) {
                                        $bpjs_kesehatan = 0;
                                    }else{
                                        $bpjs_kesehatan = intval($pengerjaan_harian->bpjs_kesehatan);
                                    }

                                    $total_gaji_diterima = ($pengerjaan_harian->upah_dasar_weekly+$hasil_lembur+$tunjangan_kehadiran+$tunjangan_kerja+
                                                            $plus_1+$plus_2+$plus_3+$pengerjaan_harian->uang_makan)-
                                                            ($jht+$bpjs_kesehatan+$minus_1+$minus_2);
                                    $kirim_gaji = \App\Models\KirimGaji::where('pengerjaan_id',$pengerjaan_harian->id)
                                                                        ->where('kode_pengerjaan',$new_data_pengerjaan->kode_pengerjaan)
                                                                        ->first();
                                @endphp
                                <tr>
                                    <td>
                                        {{ $loop->iteration + $pengerjaan_harians->firstItem() - 1 }}
                                        <input type="hidden" name="id[]" value="{{ $pengerjaan_harian->id }}">
                                        <input type="hidden" name="nominal_gaji[]" value="{{ $total_gaji_diterima }}">
                                    </td>
                                    <td>{{ $pengerjaan_harian->operator_karyawan->biodata_karyawan->nik.' - '.$pengerjaan_harian->operator_karyawan->biodata_karyawan->nama }}</td>
                                    <td>{{ $pengerjaan_harian->operator_karyawan->biodata_karyawan->email }}</td>
                                    <td>{{ $pengerjaan_harian->operator_karyawan->jenis_operator->jenis_operator.' - '.$pengerjaan_harian->operator_karyawan->jenis_operator_detail_pengerjaan->jenis_posisi_pekerjaan }}</td>
                                    <td>{{ 'Rp. '.number_format($total_gaji_diterima,0,',','.') }}</td>
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
                                        <a href="{{ route('payrol.harian.harian_cek_slip_gaji',['kode_pengerjaan' => $new_data_pengerjaan->kode_pengerjaan, 'id' => $pengerjaan_harian->id]) }}" class="btn btn-primary" target="_blank">Cek Gaji</a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    {{ $pengerjaan_harians->links('vendor.pagination.paginate_custom1') }}
                </div>
            </form>
        </div>
    </div>
@endsection
@section('script')
<script src="{{ URL::asset('public/assets/plugins/sweet-alert2/sweetalert2.min.js') }}"></script>
    <script>
        $('#kirim_slip').submit(function(e) {
            e.preventDefault();
            let formData = new FormData(this);
            $.ajax({
                type: 'POST',
                url: "{{ route('payrol.harian.harian_kirim_slip_gaji',['kode_pengerjaan' => $new_data_pengerjaan->kode_pengerjaan]) }}",
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