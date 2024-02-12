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
    <style>
        @media (max-width: 1518px) {
            .table-container {
                overflow-x: scroll; 
                width: 99%;
            }
        }
    </style>
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

    @include('backend.pengerjaan.modalClosePeriode')

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    {{-- @if ($new_data_pengerjaan)
                    <a href="{{ route('pengerjaan') }}" class="btn btn-primary mb-2"><i class="fas fa-plus"></i> Buat Periode Baru</a>
                    @endif --}}
                    <button type="button" class="btn btn-info mb-2" onclick="reload()"><i class="fas fa-undo"></i> Refresh Table</button>
                    <button type="button" class="btn btn-primary mb-2" onclick="periode()"><i class="fas fa-upload"></i> Close Periode</button>
                    <div class="table-container">
                        <table id="datatables" class="table table-bordered dt-responsive nowrap"
                            style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                            <thead>
                                <tr>
                                    {{-- <th>ID</th> --}}
                                    <th>Kode Pengerjaan</th>
                                    <th>Periode Penggajian</th>
                                    {{-- <th>Tanggal Pengerjaan</th> --}}
                                    <th>Status</th>
                                    <th>Jenis Pekerja</th>
                                    {{-- <th>Action</th> --}}
                                </tr>
                            </thead>
                        </table>
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
    {{-- <script src="{{ URL::asset('public/assets/js/pages/jquery.sweet-alert.init.js') }}"></script> --}}

    <script>
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        var table = $('#datatables').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route('pengerjaan.hasil_kerja') }}",
            columns: [
                // {
                //     data: 'id',
                //     name: 'id'
                // },
                {
                    data: 'kode_pengerjaan',
                    name: 'kode_pengerjaan'
                },
                {
                    data: 'tanggal_pengerjaan',
                    name: 'tanggal_pengerjaan'
                },
                // {
                //     data: 'tanggal',
                //     name: 'tanggal'
                // },
                {
                    data: 'status',
                    name: 'status'
                },
                {
                    data: 'jenis_kerja',
                    name: 'jenis_kerja'
                },
                // {
                //     data: 'action',
                //     name: 'action',
                //     orderable: false,
                //     searchable: false
                // },
            ]
        });

        function reload() {
            table.ajax.reload();
        }

        function periode() {
            // $('.modalBuat').modal('show');
            $.ajax({
                type:'GET',
                url: "{{ route('periode.close_periode') }}",
                contentType: "application/json;  charset=utf-8",
                cache: false,
                success: (result) => {
                    // alert(result);
                    let text = "";
                    const data = result.data;
                    data.forEach(periode);

                    function periode(item, index) {
                        text += '<tr>';
                        text += '<td class="text-center">'+item.kode_pengerjaan+'</td>';
                        text += '<td class="text-center">'+item.tanggal+'</td>';
                        text += '<td class="text-center">'+item.status+'</td>';
                        text += '</tr>';
                    }
                    document.getElementById("data_periode").innerHTML = text;
                    $('.modalBuat').modal('show');
                },
                error: function (request, status, error) {
                    iziToast.error({
                        title: 'Error',
                        message: error,
                    });
                }
            });
        }

        function periode_submit() {
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
                    $.ajax({
                        type:'GET',
                        url: "{{ route('periode.close_periode.submit') }}",
                        // data: formData,
                        // contentType: false,
                        // processData: false,
                        contentType: "application/json;  charset=utf-8",
                        cache: false,
                        success: (result) => {
                            if(result.success != false){
                                iziToast.success({
                                    title: result.message_title,
                                    message: result.message_content
                                });
                                table.ajax.reload();
                                $('.modalBuat').modal('hide');
                            }else{
                                iziToast.error({
                                    title: result.success,
                                    // message: result.error
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

        $('#form-simpan').submit(function(e) {
            
        });
    </script>
@endsection
