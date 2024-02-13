@extends('layouts.backend.app')

@section('title')
    Hasil Kerja
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
            Data Hasil Kerja
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
                <div class="card-body">
                    <button type="button" class="btn btn-info mb-2" onclick="reload()"><i class="fas fa-undo"></i> Refresh
                        Table</button>
                    <div class="card">
                        <div class="card-body">
                            <div class="card-title">Hasil Kerja Packing</div>
                            <table id="datatables2" class="table table-bordered dt-responsive nowrap"
                                style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                                <thead>
                                    <tr>
                                        <th>Kode Pengerjaan</th>
                                        <th>Periode Penggajian</th>
                                        <th>Status</th>
                                        <th>Jenis Pekerja</th>
                                    </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                    <div class="card">
                        <div class="card-body">
                            <div class="card-title">Hasil Kerja Harian</div>
                            <table id="datatables" class="table table-bordered dt-responsive nowrap"
                                style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                                <thead>
                                    <tr>
                                        <th>Kode Pengerjaan</th>
                                        <th>Periode Penggajian</th>
                                        <th>Status</th>
                                        <th>Jenis Pekerja</th>
                                    </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                    <div class="card">
                        <div class="card-body">
                            <div class="card-title">Hasil Kerja Supir</div>
                            <table id="datatables3" class="table table-bordered dt-responsive nowrap"
                                style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                                <thead>
                                    <tr>
                                        <th>Kode Pengerjaan</th>
                                        <th>Periode Penggajian</th>
                                        <th>Status</th>
                                        <th>Jenis Pekerja</th>
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
            ajax: "{{ route('pengerjaan.b_hasil_kerja_harian') }}",
            columns: [{
                    data: 'kode_pengerjaan',
                    name: 'kode_pengerjaan'
                },
                {
                    data: 'tanggal_pengerjaan',
                    name: 'tanggal_pengerjaan'
                },
                {
                    data: 'status',
                    name: 'status'
                },
                {
                    data: 'jenis_kerja',
                    name: 'jenis_kerja'
                },
            ],
            order: [
                [0, 'desc']
            ]
        });

        var table2 = $('#datatables2').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route('pengerjaan.b_hasil_kerja_packing') }}",
            columns: [{
                    data: 'kode_pengerjaan',
                    name: 'kode_pengerjaan'
                },
                {
                    data: 'tanggal_pengerjaan',
                    name: 'tanggal_pengerjaan'
                },
                {
                    data: 'status',
                    name: 'status'
                },
                {
                    data: 'jenis_kerja',
                    name: 'jenis_kerja'
                },
            ],
            order: [
                [0, 'desc']
            ]
        });

        var table3 = $('#datatables3').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route('pengerjaan.b_hasil_kerja_supir') }}",
            columns: [{
                    data: 'kode_pengerjaan',
                    name: 'kode_pengerjaan'
                },
                {
                    data: 'tanggal_pengerjaan',
                    name: 'tanggal_pengerjaan'
                },
                {
                    data: 'status',
                    name: 'status'
                },
                {
                    data: 'jenis_kerja',
                    name: 'jenis_kerja'
                },
            ],
            order: [
                [0, 'desc']
            ]
        });

        function reload() {
            table.ajax.reload();
            table2.ajax.reload();
            table3.ajax.reload();
        }
    </script>
@endsection
