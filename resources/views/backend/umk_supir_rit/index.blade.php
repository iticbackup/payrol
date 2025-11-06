@extends('layouts.backend.app')
@section('title')
    UMK Supir RIT
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
            UMK Supir RIT
        @endslot
        @slot('li_3')
            @yield('title')
        @endslot
        @slot('title')
            @yield('title')
        @endslot
    @endcomponent

    @include('backend.umk_supir_rit.modalBuat')
    @include('backend.umk_supir_rit.modalEdit')

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <button type="button" class="btn btn-primary" onclick="buat()" data-bs-toggle="modal"
                        data-bs-target="#exampleModalCenterBuat"><i class="fas fa-plus"></i> Tambah Data</button>
                    <button type="button" class="btn btn-primary" onclick="reload()"><i class="fas fa-undo"></i>
                        Refresh</button>
                </div>
                <div class="card-body">
                    <table id="datatables" class="table table-bordered dt-responsive nowrap"
                        style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                        <thead>
                            <tr>
                                <th>Kategori Upah</th>
                                <th>Rit Posisi</th>
                                <th>Rit Kendaraan</th>
                                <th>Rit Tujuan</th>
                                <th>Tarif</th>
                                <th>Tahun Aktif</th>
                                <th>Status</th>
                                <th>Actions</th>
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
            ajax: "{{ route('umk_supir_rit.index') }}",
            columns: [{
                    data: 'kategori_upah',
                    name: 'kategori_upah'
                },
                {
                    data: 'rit_posisi',
                    name: 'rit_posisi'
                },
                {
                    data: 'rit_kendaraan',
                    name: 'rit_kendaraan'
                },
                {
                    data: 'rit_tujuan',
                    name: 'rit_tujuan'
                },
                {
                    data: 'tarif',
                    name: 'tarif'
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
            ],
            order: [
                [0, 'asc'],
                [5, 'desc'],
            ]
        });

        function reload() {
            table.ajax.reload(null, false);
        }

        function buat() {
            $('.modalBuat').modal('show');
        }

        $('#form-simpan').submit(function(e) {
            e.preventDefault();
            let formData = new FormData(this);
            // $('#image-input-error').text('');
            $.ajax({
                type: 'POST',
                url: "{{ route('umk_supir_rit.simpan') }}",
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
            // $('.modalBuat').modal('show');
            $.ajax({
                type:'GET',
                url: "{{ url('umk_supir_rit/') }}"+"/"+id,
                contentType: "application/json;  charset=utf-8",
                cache: false,
                success: (result) => {
                    // alert(result);
                    // let text = "";
                    // const data = result.data;
                    // data.forEach(periode);

                    // function periode(item, index) {
                    //     text += '<tr>';
                    //     text += '<td class="text-center">'+item.kode_pengerjaan+'</td>';
                    //     text += '<td class="text-center">'+item.tanggal+'</td>';
                    //     text += '<td class="text-center">'+item.status+'</td>';
                    //     text += '</tr>';
                    // }
                    if (result.success == true) {
                        $('#edit_id').val(result.data.id);
                        $('#edit_kategori_upah').val(result.data.kategori_upah);
                        $('#edit_rit_posisi_id').val(result.data.rit_posisi_id);
                        $('#edit_rit_kendaraan_id').val(result.data.rit_kendaraan_id);
                        $('#edit_rit_tujuan_id').val(result.data.rit_tujuan_id);
                        $('#edit_tarif').val(result.data.tarif);
                        $('#edit_tahun_aktif').val(result.data.tahun_aktif);
                        $('#edit_status').val(result.data.status);
                        $('.modalEdit').modal('show');
                    }else{
                        iziToast.error({
                            title: result.success,
                            message: result.error
                        });
                    }
                    // document.getElementById("data_periode").innerHTML = text;
                },
                error: function (request, status, error) {
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
                url: "{{ route('umk_supir_rit.update') }}",
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
                        $('.modalEdit').modal('hide');
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

        function hapus(id)
        {
            var url = '{{ route('umk_supir_rit.delete', ':id') }}';
            url = url.replace(':id', id);

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
        }
    </script>
@endsection