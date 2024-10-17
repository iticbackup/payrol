@extends('layouts.backend.app')

@section('title')
    Cek Kirim Gaji Payrol Supir Rit
@endsection

@section('css')
    <link href="{{ URL::asset('public/assets/plugins/datatables/dataTables.bootstrap5.min.css') }}" rel="stylesheet"
        type="text/css" />
    <link href="{{ URL::asset('public/assets/plugins/datatables/buttons.bootstrap5.min.css') }}" rel="stylesheet"
        type="text/css" />
    <link href="{{ URL::asset('public/assets/plugins/datatables/responsive.bootstrap4.min.css') }}" rel="stylesheet"
        type="text/css" />
    <link href="{{ URL::asset('public/assets/css/iziToast.css') }}" rel="stylesheet" type="text/css" />
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
            <div class="card">
                <div class="card-header">
                    <h5>Kode ID : {{ $new_data_pengerjaan->kode_pengerjaan }}
                        @if ($new_data_pengerjaan->status == 'n')
                            <i class="far fa-check-circle text-success"></i>
                        @endif
                    </h5>
                </div>
                <div class="card-body">
                    <table id="datatables" class="table table-bordered dt-responsive nowrap"
                        style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Kode Payrol</th>
                                <th>Nama Karyawan</th>
                                <th>Jenis Pengerjaan</th>
                                <th>Nominal Gaji</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($check_kirim_gajis as $key => $check_kirim_gaji)
                                <tr>
                                    <td>{{ $key+1 }}</td>
                                    <td>{{ $check_kirim_gaji->kode_payrol }}</td>
                                    <td>{{ $check_kirim_gaji->nik.' - '.$check_kirim_gaji->nama_karyawan }}</td>
                                    <td>{{ 'Supir Rit - '.$check_kirim_gaji->karyawan_operator_supir_rit->rit_posisi->nama_posisi }}</td>
                                    <td>{{ 'Rp. '.number_format($check_kirim_gaji->nominal_gaji,0,',','.') }}</td>
                                    <td>
                                        @switch($check_kirim_gaji->status)
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
                                    <td>
                                        <button class="btn btn-primary" onclick="resend_mail(`{{ $new_data_pengerjaan->kode_pengerjaan }}`,{{ $check_kirim_gaji->id }})">
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
        function resend_mail(kode_pengerjaan,id)
        {
            $.ajax({
                type: 'GET',
                url: "{{ url('payrol/supir_rit/') }}"+'/'+kode_pengerjaan+'/detail_kirim_email/'+id+'/kirim_ulang',
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