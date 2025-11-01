@extends('layouts.backend.app')

@section('title')
    Cutt Off
@endsection

@section('css')
    <link href="{{ URL::asset('public/assets/plugins/datatables/dataTables.bootstrap5.min.css') }}" rel="stylesheet"
        type="text/css" />
    <link href="{{ URL::asset('public/assets/plugins/datatables/buttons.bootstrap5.min.css') }}" rel="stylesheet"
        type="text/css" />
    <link href="{{ URL::asset('public/assets/plugins/datatables/responsive.bootstrap4.min.css') }}" rel="stylesheet"
        type="text/css" />
    <link href="{{ URL::asset('public/assets/css/iziToast.css') }}" rel="stylesheet" type="text/css" />
    {{-- <link href="{{ URL::asset('public/assets/plugins/sweet-alert2/sweetalert2.min.css') }}" rel="stylesheet"
        type="text/css"> --}}
    <link href="{{ URL::asset('public/assets/plugins/sweet-alert2/sweetalert2.min.css') }}" rel="stylesheet"
        type="text/css">
    <script src="{{ URL::asset('public/assets/js/pages/jquery.sweet-alert.init.js') }}"></script>
@endsection

@section('content')
    @component('components.breadcrumb')
        @slot('li_1')
            Cut Off
        @endslot
        @slot('li_3')
            @yield('title')
        @endslot
        @slot('title')
            @yield('title')
        @endslot
    @endcomponent

    @include('backend.cutoff.modalBuat')
    @include('backend.cutoff.modalEdit')

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <button type="button" class="btn btn-primary mb-2" onclick="buat()"><i class="fas fa-plus"></i> Buat Baru</button>
                    <button type="button" class="btn btn-info mb-2" onclick="reload()"><i class="fas fa-undo"></i> Refresh
                        Table</button>
                    <div class="card">
                        <div class="card-body">
                            <table id="datatables" class="table table-bordered dt-responsive nowrap"
                                style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                                <thead>
                                    <tr>
                                        <th>Periode</th>
                                        <th>Pertanggal</th>
                                        <th>Status</th>
                                        <th>Actions</th>
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
            ajax: "{{ route('cut_off') }}",
            columns: [
                {
                    data: 'periode',
                    name: 'periode'
                },
                {
                    data: 'tanggal',
                    name: 'tanggal'
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
                [0, 'desc']
            ]
        });

        function buat()
        {
            $('.modalBuat').modal('show');
        }

        function reload()
        {
            table.ajax.reload(null, false);
        }

        $('#form-simpan').submit(function(e) {
            e.preventDefault();
            let formData = new FormData(this);
            // $('#image-input-error').text('');
            $.ajax({
                type:'POST',
                url: "{{ route('cut_off.simpan') }}",
                data: formData,
                contentType: false,
                processData: false,
                success: (result) => {
                    if(result.success != false){
                        iziToast.success({
                            title: result.message_title,
                            message: result.message_content
                        });
                        this.reset();
                        table.ajax.reload();
                    }else{
                        iziToast.error({
                            title: result.success,
                            message: result.error
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
        });

        function edit(id)
        {
            $.ajax({
                type: 'GET',
                url: "{{ url('cut_off/') }}"+'/'+id,
                contentType: "application/json;  charset=utf-8",
                cache: false,
                beforeSend: function() {
                },
                success: (result) => {
                    $('#edit_id').val(result.data.id);
                    $('#edit_periode').val(result.data.periode);
                    $('#edit_tanggal').val(result.data.tanggal);
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

        $('#form-update').submit(function(e) {
            e.preventDefault();
            let formData = new FormData(this);
            // $('#image-input-error').text('');
            $.ajax({
                type:'POST',
                url: "{{ route('cut_off.update') }}",
                data: formData,
                contentType: false,
                processData: false,
                success: (result) => {
                    if(result.success != false){
                        iziToast.success({
                            title: result.message_title,
                            message: result.message_content
                        });
                        this.reset();
                        table.ajax.reload();
                        $('.modalEdit').modal('hide');
                    }else{
                        iziToast.error({
                            title: result.success,
                            message: result.error
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
        });
    </script>
@endsection