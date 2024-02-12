@extends('layouts.backend.app')

@section('title')
    Laporan Borongan
@endsection

@section('css')
    <link href="{{ URL::asset('public/assets/plugins/datatables/dataTables.bootstrap5.min.css') }}" rel="stylesheet"
        type="text/css" />
    <link href="{{ URL::asset('public/assets/plugins/datatables/buttons.bootstrap5.min.css') }}" rel="stylesheet"
        type="text/css" />
    <link href="{{ URL::asset('public/assets/plugins/datatables/responsive.bootstrap4.min.css') }}" rel="stylesheet"
        type="text/css" />
    <link href="{{ URL::asset('public/assets/css/iziToast.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ URL::asset('public/assets/plugins/sweet-alert2/sweetalert2.min.css') }}" rel="stylesheet"
        type="text/css">
    <script src="{{ URL::asset('public/assets/js/pages/jquery.sweet-alert.init.js') }}"></script>
    <style>
        @media (max-width: 1518px) {

            .table-container {
                /* width: 100% !important; */
                overflow-x: scroll;
                width: 65%;
            }

            /* .layouts {
        margin-left: 26%;
        margin-right: 9%
    } */

            /* th, td {min-width: 200px; } */
        }
    </style>
@endsection

@section('content')
    @component('components.breadcrumb')
        @slot('li_1')
            Laporan
        @endslot
        @slot('li_3')
            @yield('title')
        @endslot
        @slot('title')
            @yield('title')
        @endslot
    @endcomponent

    {{-- @include('backend.laporan.borongan.loading') --}}

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Hasil Laporan Payrol</h4>
                    <button type="button" class="btn btn-primary mt-2 mb-1" onclick="reload()"><i class="fas fa-undo"></i>
                        Refresh</button>
                </div>
                <div class="card-body">
                    <div class="table-container">
                        <div class="table-responsive">
                            <table id="datatables" class="table table-bordered dt-responsive nowrap"
                                style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                                <thead>
                                    <tr>
                                        <th class="text-center" style="width: 10%">Kode Payrol</th>
                                        <th class="text-center" style="width: 25%">Tanggal Penggajian</th>
                                        <th class="text-center" style="width: 10%">Status</th>
                                        <th class="text-center">Action</th>
                                    </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
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
    <script src="{{ URL::asset('public/assets/js/iziToast.min.js') }}"></script>
    <script src="{{ URL::asset('public/assets/plugins/sweet-alert2/sweetalert2.min.js') }}"></script>

    <script>
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        var table = $('#datatables').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route('laporan.borongan') }}",
            columns: [{
                    data: 'kode_pengerjaan',
                    name: 'kode_pengerjaan'
                },
                {
                    data: 'tanggal_penggajian',
                    name: 'tanggal_penggajian'
                },
                {
                    data: 'status',
                    name: 'status'
                },
                {
                    data: 'action',
                    name: 'action',
                    orderable: false,
                    searchable: false
                },
            ],
            columnDefs: [{
                    className: 'text-center',
                    targets: [0, 2]
                },
                // { className: 'text-center', targets: [5] },
            ],
            order: [
                [0, 'desc']
            ]
        });

        function reload() {
            table.ajax.reload();
        }

        var i = 0;

        function download_excel(id, kode_pengerjaan) {
            // alert(id+','+kode_pengerjaan);
            var url = '{{ url('laporan/borongan/export') }}' + "/" + id + "/" + kode_pengerjaan;
            // alert(url);
            // var url = '{{ route('export', ':id') }}';
            // url = url.replace(':id', id);
            $.ajax({
                type: 'GET',
                url: url,
                contentType: "application/xml;  charset=utf-8",
                cache: false,
                beforeSend: function() {
                    // setting a timeout
                    // $(placeholder).addClass('loading');
                    // $('.modalLoading').modal('show');
                    Swal.fire({
                        title: 'Laporan Sedang didownload, silahkan tunggu',
                        allowEscapeKey: false,
                        allowOutsideClick: false,
                        onOpen: () => {
                            swal.showLoading();
                        }
                    })
                    i++;
                },
                complete: function() {
                    // i--;
                    // if (i <= 0) {
                    //     // const myModalEl = document.getElementById('staticBackdrop')
                    //     // myModalEl.addEventListener('hidden.bs.modal', function (event) {
                    //     // // do something...
                    //     // })
                    //     // $('.modalLoading').hide();
                    //     Swall.fire({
                    //         title: "Success",
                    //         type: "success",
                    //         timer: 2000,
                    //         showConfirmButton: false
                    //     })
                    // }
                    Swall.fire({
                        title: "Success",
                        type: "success",
                        timer: 2000,
                        showConfirmButton: false
                    })
                },
                success: (result) => {
                    console.log(result);
                    Swall.fire({
                        title: "Success",
                        type: "success",
                        timer: 2000,
                        showConfirmButton: false
                    })
                    // $('#edit_id').val(result.data.id);
                    // $('#edit_keterangan').val(result.data.keterangan);
                    // $('#edit_nominal').val(result.data.nominal);
                    // $('#edit_masa_kerja').val(result.data.masa_kerja);
                    // $('#edit_tahun').val(result.data.tahun);
                    // $('#edit_status').val(result.data.status);

                    // $('.modalEdit').modal('show');
                },
                error: function(request, status, error) {
                    // iziToast.error({
                    //     title: 'Error',
                    //     message: error,
                    // });
                    alert(error);
                },
            });
        }
    </script>
@endsection
