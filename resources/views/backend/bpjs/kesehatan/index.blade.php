@extends('layouts.backend.app')

@section('title')
    BPJS Kesehatan
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
@endsection

@section('content')
    @component('components.breadcrumb')
        @slot('li_1')
            BPJS
        @endslot
        @slot('li_3')
            @yield('title')
        @endslot
        @slot('title')
            @yield('title')
        @endslot
    @endcomponent

    @include('backend.bpjs.kesehatan.modalBuat')
    @include('backend.bpjs.kesehatan.modalEdit')

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <button type="button" class="btn btn-primary" onclick="buat()" data-bs-toggle="modal"
                    data-bs-target="#exampleModalCenter"><i class="fas fa-plus"></i> Tambah Data</button>
                    <button type="button" class="btn btn-primary" onclick="reload()"><i class="fas fa-undo"></i>
                        Refresh</button>
                </div>
                <div class="card-body">
                    <table id="datatables" class="table table-bordered dt-responsive nowrap"
                        style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                        <thead>
                            <tr>
                                <th>Keterangan</th>
                                <th>Nominal</th>
                                <th>Tahun</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
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
            ajax: "{{ route('bpjs.kesehatan') }}",
            columns: [
                {
                    data: 'keterangan',
                    name: 'keterangan'
                },
                {
                    data: 'nominal',
                    name: 'nominal'
                },
                {
                    data: 'tahun',
                    name: 'tahun'
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
            ]
        });

        function buat() {
            $('.modalBuat').modal();
        }

        function reload() {
            table.ajax.reload();
        }

        function edit(id) {
            var url = '{{ route('bpjs.kesehatan.detail', ':id') }}';
            url = url.replace(':id', id);

            $.ajax({
                type: 'GET',
                url: url,
                contentType: "application/json;  charset=utf-8",
                cache: false,
                success: (result) => {
                    // alert(result);
                    $('#edit_id').val(result.data.id);
                    $('#edit_keterangan').val(result.data.keterangan);
                    $('#edit_nominal').val(result.data.nominal);
                    $('#edit_tahun').val(result.data.tahun);
                    $('#edit_status').val(result.data.status);

                    $('.modalEdit').modal('show');
                },
                error: function(request, status, error) {
                    iziToast.error({
                        title: 'Error',
                        message: error,
                    });
                }
            });
        }

        function hapus(id) {
            var url = '{{ route('bpjs.kesehatan.delete', ':id') }}';
            url = url.replace(':id', id);

            Swal.fire({
                title: "Apa kamu yakin?",
                text: "Anda tidak akan dapat mengembalikan ini!",
                type: "warning",
                showCancelButton: !0,
                confirmButtonColor: "$success",
                cancelButtonColor: "$danger",
                confirmButtonText: "Yes, delete it!"
            }).then(function(t) {
                t.value && 
                // Swal.fire("Deleted!", "Your file has been deleted.", "success")
                $.ajax({
                    type: 'GET',
                    url: url,
                    contentType: "application/json;  charset=utf-8",
                    cache: false,
                    success: (result) => {
                        if (result.success != false) {
                            iziToast.success({
                                title: result.message_title,
                                message: result.message_content
                            });
                            table.ajax.reload();
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
            })
        }

        $('#form-simpan').submit(function(e) {
            e.preventDefault();
            let formData = new FormData(this);
            // $('#image-input-error').text('');
            $.ajax({
                type: 'POST',
                url: "{{ route('bpjs.kesehatan.simpan') }}",
                data: formData,
                contentType: false,
                processData: false,
                success: (result) => {
                    if (result.success != false) {
                        iziToast.success({
                            title: result.message_title,
                            message: result.message_content
                        });
                        this.reset();
                        table.ajax.reload();
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

        $('#form-update').submit(function(e) {
            e.preventDefault();
            let formData = new FormData(this);
            // $('#image-input-error').text('');
            $.ajax({
                type: 'POST',
                url: "{{ route('bpjs.kesehatan.update') }}",
                data: formData,
                contentType: false,
                processData: false,
                success: (result) => {
                    if (result.success != false) {
                        iziToast.success({
                            title: result.message_title,
                            message: result.message_content
                        });
                        $('.modalEdit').modal('hide');
                        table.ajax.reload();
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
