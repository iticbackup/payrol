@extends('layouts.backend.app')
@section('title')
    Karyawan Operator Supir RIT
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
            Data Karyawan
        @endslot
        @slot('li_3')
            @yield('title')
        @endslot
        @slot('title')
            @yield('title')
        @endslot
    @endcomponent

    @include('backend.payrol.operator_karyawan_supir_rit.modalBuat')
    @include('backend.payrol.operator_karyawan_supir_rit.modalEdit')

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    @if ($user_management->c == 'Y')
                        <button type="button" class="btn btn-primary" onclick="buat()" data-bs-toggle="modal"
                            data-bs-target="#exampleModalCenter"><i class="fas fa-plus"></i> Tambah Data</button>
                    @endif
                    {{-- <a href="{{ route('operator_karyawan.create') }}" class="btn btn-primary"><i class="fas fa-plus"></i> Tambah Data</a> --}}
                    <button type="button" class="btn btn-primary" onclick="reload()"><i class="fas fa-undo"></i>
                        Refresh</button>
                </div>
                <div class="card-body">
                    <table id="datatables" class="table table-bordered dt-responsive nowrap"
                        style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>NIK</th>
                                <th>Nama Karyawan</th>
                                <th>RIT Posisi</th>
                                <th>Golongan Tunjangan Kerja</th>
                                <th>Nominal Tunjangan Kerja</th>
                                <th>Upah Dasar</th>
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
    <script src="https://cdnjs.cloudflare.com/ajax/libs/axios/1.4.0/axios.min.js"
        integrity="sha512-uMtXmF28A2Ab/JJO2t/vYhlaa/3ahUOgj1Zf27M5rOo8/+fcTUVH0/E0ll68njmjrLqOBjXM3V9NiPFL5ywWPQ=="
        crossorigin="anonymous" referrerpolicy="no-referrer"></script>

    <script>
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        var table = $('#datatables').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route('operator_karyawan_supir_rit') }}",
            columns: [
                {
                    data: 'id',
                    name: 'id'
                },
                {
                    data: 'nik',
                    name: 'nik'
                },
                {
                    data: 'nama_karyawan',
                    name: 'nama_karyawan'
                },
                {
                    data: 'rit_posisi_id',
                    name: 'rit_posisi_id'
                },
                {
                    data: 'tunjangan_kerja_id',
                    name: 'tunjangan_kerja_id'
                },
                {
                    data: 'nominal_tunjangan_kerja',
                    name: 'nominal_tunjangan_kerja'
                },
                {
                    data: 'upah_dasar',
                    name: 'upah_dasar'
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
            table.ajax.reload(null, false);
        }

        $('.buat_nik_karyawan').on('change', function() {
            axios.post('{{ route('operator_karyawan.select_biodata_karyawan') }}', {
                    nik: $(this).val()
                })
                .then(function(response) {
                    if (response.data.success == false) {
                        $('.buat_nama_karyawan').val(response.data.data);
                    }else{
                        $('.buat_nama_karyawan').val(response.data.data.nama);
                    }
                });
        });

        $('#form-simpan').submit(function(e) {
            e.preventDefault();
            let formData = new FormData(this);
            // $('#image-input-error').text('');
            $.ajax({
                type: 'POST',
                url: "{{ route('operator_karyawan_supir_rit_simpan') }}",
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
                        table.ajax.reload(null, false);
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

        function edit(id) {
            var url = '{{ route('operator_karyawan_supir_rit_detail', ':id') }}';
            url = url.replace(':id', id);

            $.ajax({
                type: 'GET',
                url: url,
                contentType: "application/json;  charset=utf-8",
                cache: false,
                success: (result) => {
                    // alert(result);
                    $('#edit_id').val(result.data.id);
                    $('.edit_nik').val(result.data.nik);
                    $('#edit_rekening').val(result.data.rekening);
                    $('#edit_jht').val(result.data.jht);
                    $('#edit_bpjs').val(result.data.bpjs);
                    $('#edit_rit_posisi_id').val(result.data.rit_posisi_id);
                    $('#edit_upah_dasar').val(result.data.upah_dasar);
                    $('#edit_tunjangan_kerja_id').val(result.data.tunjangan_kerja_id);
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

        $('#form-update').submit(function(e) {
            e.preventDefault();
            let formData = new FormData(this);
            // $('#image-input-error').text('');
            $.ajax({
                type: 'POST',
                url: "{{ route('operator_karyawan_supir_rit_update') }}",
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
                        table.ajax.reload(null, false);
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

        function hapus(id) {
            swal.fire({
                title: 'Apakah anda yakin?',
                type: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes, Submit',
                cancelButtonText: 'No, cancel!',
                reverseButtons: true
            }).then(function(result) {
                if (result.value) {
                    // e.preventDefault();
                    // let formData = new FormData(this);
                    // $('#image-input-error').text('');
                    var url = '{{ route('operator_karyawan_supir_rit_hapus', ':id') }}';
                    url = url.replace(':id', id);
                    $.ajax({
                        type:'GET',
                        url: url,
                        contentType: "application/json;  charset=utf-8",
                        cache: false,
                        success: (result) => {
                            if(result.success != false){
                                iziToast.success({
                                    title: result.message_title,
                                    message: result.message_content
                                });
                                table.ajax.reload(null, false);
                            }else{
                                iziToast.error({
                                    title: result.success,
                                });
                            }
                        },
                        error: function (request, status, error) {
                            iziToast.error({
                                title: 'Error',
                                message: error,
                            });
                        }
                    });
                }
            })
        }
    </script>
@endsection
