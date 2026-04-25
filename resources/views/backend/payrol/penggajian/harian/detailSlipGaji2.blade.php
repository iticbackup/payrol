@extends('layouts.backend.app')

@section('title')
    Detail Slip Gaji Harian - {{ $new_data_pengerjaan->kode_pengerjaan }}
@endsection
@section('css')
    <link href="{{ URL::asset('public/assets/plugins/datatables/dataTables.bootstrap5.min.css') }}" rel="stylesheet"
        type="text/css" />
    <link href="{{ URL::asset('public/assets/plugins/datatables/buttons.bootstrap5.min.css') }}" rel="stylesheet"
        type="text/css" />
    <link href="{{ URL::asset('public/assets/plugins/datatables/responsive.bootstrap4.min.css') }}" rel="stylesheet"
        type="text/css" />
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
            <div class="alert alert-info border-0" role="alert">
                <strong>Informasi!</strong> Sistem Slip Gaji Elektronik Dikirim Secara Otomatis Oleh Sistem dan Akan Dikirim Setelah Melakukan Close Period.
            </div>
            <div class="card">
                <div class="card-header">
                    <h5>Kode ID : {{ $new_data_pengerjaan->kode_pengerjaan }}
                        @if ($new_data_pengerjaan->status == 'n')
                        <i class="far fa-check-circle text-success"></i>
                        @endif
                        <button type="button" onclick="kirim_email_ulang()" class="btn btn-primary">
                            <svg xmlns="http://www.w3.org/2000/svg" width="1em" height="1em" viewBox="0 0 512 512">
                                <path fill="currentColor" d="m476.59 227.05l-.16-.07L49.35 49.84A23.56 23.56 0 0 0 27.14 52A24.65 24.65 0 0 0 16 72.59v113.29a24 24 0 0 0 19.52 23.57l232.93 43.07a4 4 0 0 1 0 7.86L35.53 303.45A24 24 0 0 0 16 327v113.31A23.57 23.57 0 0 0 26.59 460a23.94 23.94 0 0 0 13.22 4a24.55 24.55 0 0 0 9.52-1.93L476.4 285.94l.19-.09a32 32 0 0 0 0-58.8" />
                            </svg> Kirim Gaji
                        </button>
                        <button type="button" class="btn" style="background-color: #FD8B51; color: black" onclick="window.location.href='{{ route('payrol.harian.harian_cek_email_slip_gaji',['kode_pengerjaan' => $new_data_pengerjaan->kode_pengerjaan ]) }}'">
                            <svg xmlns="http://www.w3.org/2000/svg" width="1.5em" height="1.5em" viewBox="0 0 512 512">
                                <path fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="32" d="M320 96H88a40 40 0 0 0-40 40v240a40 40 0 0 0 40 40h334.73a40 40 0 0 0 40-40V239" />
                                <path fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="32" d="m112 160l144 112l87-65.67" />
                                <circle cx="431.95" cy="128.05" r="47.95" fill="currentColor" />
                                <path fill="currentColor" d="M432 192a63.95 63.95 0 1 1 63.95-63.95A64 64 0 0 1 432 192m0-95.9a32 32 0 1 0 31.95 32a32 32 0 0 0-31.95-32" />
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
                                <th class="text-center">No</th>
                                <th class="text-center">ID</th>
                                <th class="text-center">Nama Karyawan</th>
                                <th class="text-center">Email Karyawan</th>
                                <th class="text-center">Jenis Pengerjaan</th>
                                <th class="text-center">Nominal Gaji</th>
                                <th class="text-center">Status</th>
                                <th class="text-center">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($slip_gaji_borongans as $item)
                                <tr>
                                    <td class="text-center">{{ $loop->iteration }}</td>
                                    <td class="text-center">{{ $item->pengerjaan_id }}</td>
                                    <td>{{ $item->nama_karyawan }}</td>
                                    <td class="text-center">{{ $item->biodata_karyawan->email }}</td>
                                    <td class="text-center">
                                        @if (empty($item->karyawan_operator_harian->jenis_operator_detail_pengerjaan->jenis_posisi_pekerjaan))
                                            -
                                        @else
                                        {{ $item->karyawan_operator_harian->jenis_operator_detail->jenis_posisi }}
                                        @endif
                                    </td>
                                    <td class="text-center">{{ 'Rp. '.number_format($item->nominal_gaji,0,',','.') }}</td>
                                    <td class="text-center">
                                        @switch($item->status)
                                            @case('menunggu')
                                                <span class="badge bg-warning">
                                                    <i class="mdi mdi-sync"></i> Menunggu Dikirim
                                                </span>
                                                @break
                                            @case('terkirim')
                                                <span class="badge bg-success">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="1em" height="1em" viewBox="0 0 512 512">
                                                        <path fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="32" d="M464 128L240 384l-96-96m0 96l-96-96m320-160L232 284" />
                                                    </svg> Terkirim
                                                </span>
                                                @break
                                            @case('gagal terkirim')
                                                <span class="badge bg-danger">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="1em" height="1em" viewBox="0 0 512 512">
                                                        <path fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="32" d="M464 128L240 384l-96-96m0 96l-96-96m320-160L232 284" />
                                                    </svg> Gagal Terkirim
                                                </span>
                                                @break
                                            @default
                                                
                                        @endswitch
                                    </td>
                                    <td class="text-center">
                                        <button class="btn btn-primary" onclick="resend_mail(`{{ $new_data_pengerjaan->kode_pengerjaan }}`,{{ $item->id }})">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="1em" height="1em" viewBox="0 0 512 512">
                                                <path fill="currentColor" d="m16 464l480-208L16 48v160l320 48l-320 48Z" />
                                            </svg> Kirim Email Ulang
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('script')
    <script src="{{ URL::asset('public/assets/plugins/datatables/jquery.dataTables.min.js') }}"></script>
    <script src="{{ URL::asset('public/assets/plugins/datatables/dataTables.bootstrap5.min.js') }}"></script>
    <script src="{{ URL::asset('public/assets/plugins/datatables/dataTables.buttons.min.js') }}"></script>
    <script src="{{ URL::asset('public/assets/plugins/datatables/buttons.bootstrap5.min.js') }}"></script>
    <script src="{{ URL::asset('public/assets/plugins/datatables/jszip.min.js') }}"></script>
    <script src="{{ URL::asset('public/assets/plugins/datatables/pdfmake.min.js') }}"></script>
    <script src="{{ URL::asset('public/assets/plugins/datatables/vfs_fonts.js') }}"></script>
    <script src="{{ URL::asset('public/assets/plugins/datatables/buttons.html5.min.js') }}"></script>
    <script src="{{ URL::asset('public/assets/plugins/datatables/buttons.print.min.js') }}"></script>
    <script src="{{ URL::asset('public/assets/plugins/datatables/buttons.colVis.min.js') }}"></script>
    <script src="{{ URL::asset('public/assets/plugins/datatables/dataTables.responsive.min.js') }}"></script>
    <script src="{{ URL::asset('public/assets/plugins/datatables/responsive.bootstrap4.min.js') }}"></script>
    <script src="{{ URL::asset('public/assets/js/pages/jquery.datatable.init.js') }}"></script>

    <script src="{{ URL::asset('public/assets/plugins/sweet-alert2/sweetalert2.min.js') }}"></script>

    <script>
        $('#datatables').DataTable();

        function kirim_email_ulang()
        {
            $.ajax({
                type: 'GET',
                url: "{{ route('payrol.harian.harian_kirim_slip_gaji',['kode_pengerjaan' => $new_data_pengerjaan->kode_pengerjaan]) }}",
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
        }

        function resend_mail(kode_pengerjaan,id)
        {
            $.ajax({
                type: 'GET',
                url: "{{ url('payrol/harian/') }}"+'/'+kode_pengerjaan+'/detail_kirim_email/'+id+'/kirim_ulang',
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
        }
    </script>
@endsection