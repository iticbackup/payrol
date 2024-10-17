@extends('layouts.backend.app')

@section('title')
Payrol Borongan
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
        type="text/css">
    <script src="{{ URL::asset('public/assets/js/pages/jquery.sweet-alert.init.js') }}"></script> --}}
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

    @include('backend.payrol.penggajian.borongan.modalSlipGajiView')

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <button type="button" class="btn btn-primary mb-2" onclick="reload()"><i class="fas fa-undo"></i>
                        Refresh Table</button>
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
    {{-- <script src="{{ URL::asset('public/assets/plugins/sweet-alert2/sweetalert2.min.js') }}"></script> --}}
    <script>
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        var table = $('#datatables').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route('payrol.borongan') }}",
            columns: [
                {
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
            columnDefs: [
                { className: 'text-center', targets: [0,2] },
                // { className: 'text-center', targets: [5] },
            ],
            order: [[0, 'desc']]
        });

        function reload() {
            table.ajax.reload();
        }

        function slip_gaji_view(kode_pengerjaan) {
            $.ajax({
                type:'GET',
                // url: "{{ url('payrol/borongan/') }}"+'/'+kode_pengerjaan+'/'+'slip_gaji/view',
                url: "{{ url('payrol/borongan/') }}"+'/'+kode_pengerjaan+'/'+'slip_gaji',
                contentType: "application/json;  charset=utf-8",
                cache: false,
                beforeSend: function() {
                    $('.modalLoading').modal('show');
                },
                success: (result) => {
                    // alert(result);
                    // if (result.success == true) {
                    // }
                    $('.modalLoading').modal('hide');
                    $('.modalViewSlipGaji').modal('show');
                    document.getElementById('slip_gaji').innerHTML = '<object data='+result+' type="application/pdf">'+
                        '<iframe src="'+result+'"></iframe>'+
                    '</object>';
                    // setTimeout(() => {
                    //     $('.modalLoading').modal('hide');
                    //     $('.modalViewSlipGaji').modal('show');
                    //     document.getElementById('slip_gaji').innerHTML = '<iframe src="'+result+'"></iframe>';
                    //     // window.location.href = "{{ route('pengerjaan.hasil_kerja') }}";
                    // }, 2000);
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
                    // document.getElementById("data_periode").innerHTML = text;
                    // $('.modalBuat').modal('show');
                },
                error: function (request, status, error) {
                    iziToast.error({
                        title: 'Error',
                        message: error,
                    });
                }
            });
        }
    </script>
@endsection
