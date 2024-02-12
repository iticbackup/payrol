@extends('layouts.backend.app')
@section('title')
    UMK Borongan Lokal
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
            UMK Borongan
        @endslot
        @slot('li_3')
            @yield('title')
        @endslot
        @slot('title')
            @yield('title')
        @endslot
    @endcomponent

    @include('backend.jenis_umk_borongan.lokal.modalBuat')
    @include('backend.jenis_umk_borongan.lokal.modalEdit')

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <button type="button" class="btn btn-primary" onclick="buat()" data-bs-toggle="modal"
                        data-bs-target="#exampleModalCenter"><i class="fas fa-plus"></i> Tambah Data</button>
                    {{-- <a href="{{ route('operator_karyawan.create') }}" class="btn btn-primary"><i class="fas fa-plus"></i> Tambah Data</a> --}}
                    <button type="button" class="btn btn-primary" onclick="reload()"><i class="fas fa-undo"></i>
                        Refresh</button>
                </div>
                <div class="card-body">
                    <table id="datatables" class="table table-bordered dt-responsive nowrap"
                        style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                        <thead>
                            <tr>
                                <th>Jenis Produk</th>
                                <th>UMK Packing</th>
                                <th>UMK Bandrol</th>
                                <th>UMK Inner</th>
                                <th>UMK Outer</th>
                                <th>Tahun Aktif</th>
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
            ajax: "{{ route('jenis_umk_borongan.lokal') }}",
            columns: [{
                    data: 'jenis_produk',
                    name: 'jenis_produk'
                },
                {
                    data: 'umk_packing',
                    name: 'umk_packing'
                },
                {
                    data: 'umk_bandrol',
                    name: 'umk_bandrol'
                },
                {
                    data: 'umk_inner',
                    name: 'umk_inner'
                },
                {
                    data: 'umk_outer',
                    name: 'umk_outer'
                },
                {
                    data: 'tahun_aktif',
                    name: 'tahun_aktif'
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
            var url = '{{ route('jenis_umk_borongan.lokal.detail', ':id') }}';
            url = url.replace(':id', id);

            $.ajax({
                type: 'GET',
                url: url,
                contentType: "application/json;  charset=utf-8",
                cache: false,
                success: (result) => {
                    // alert(result);
                    $('#edit_id').val(result.data.id);
                    $('#edit_jenis_produk').val(result.data.jenis_produk);
                    $('#edit_umk_packing').val(result.data.umk_packing);
                    $('#edit_umk_bandrol').val(result.data.umk_bandrol);
                    $('#edit_umk_inner').val(result.data.umk_inner);
                    $('#edit_umk_outer').val(result.data.umk_outer);
                    $('#edit_tahun_aktif').val(result.data.tahun_aktif);
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
            var url = '{{ route('jenis_umk_borongan.lokal.delete', ':id') }}';
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
                url: "{{ route('jenis_umk_borongan.lokal.simpan') }}",
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
                url: "{{ route('jenis_umk_borongan.lokal.update') }}",
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
