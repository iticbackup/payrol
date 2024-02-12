@extends('layouts.backend.app')
@section('title') User Management @endsection
@section('css')
    {{-- <link href="{{ URL::asset('assets/plugins/select2/select2.min.css') }}" rel="stylesheet" type="text/css" /> --}}
    <link href="{{ URL::asset('public/assets/plugins/datatables/dataTables.bootstrap5.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ URL::asset('public/assets/plugins/datatables/buttons.bootstrap5.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ URL::asset('public/assets/plugins/datatables/responsive.bootstrap4.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ URL::asset('public/assets/css/iziToast.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ URL::asset('public/assets/plugins/sweet-alert2/sweetalert2.min.css') }}" rel="stylesheet" type="text/css">
    <script src="{{ URL::asset('public/assets/js/pages/jquery.sweet-alert.init.js') }}"></script>
    {{-- <link href="{{ URL::asset('assets/plugins/huebee/huebee.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ URL::asset('assets/plugins/timepicker/bootstrap-material-datetimepicker.css') }}" rel="stylesheet">
    <link href="{{ URL::asset('assets/plugins/bootstrap-touchspin/css/jquery.bootstrap-touchspin.min.css') }}" rel="stylesheet" /> --}}
@endsection

@section('content')
    @component('components.breadcrumb')
    @slot('li_1') User Management @endslot
    @slot('li_3') @yield('title') @endslot
    @slot('title') @yield('title') @endslot
    @endcomponent

    @include('backend.users.management.modalBuat')
    @include('backend.users.management.modalEdit')

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    {{-- <h4 class="card-title">Users</h4> --}}
                    <button type="button" class="btn btn-primary" onclick="buat()" data-bs-toggle="modal" data-bs-target="#exampleModalCenter"><i class="fas fa-plus"></i> Tambah</button>
                    <button type="button" class="btn btn-primary" onclick="reload()"><i class="fas fa-undo"></i> Refresh</button>
                </div>
                <div class="card-body">
                    <table id="datatables" class="table table-bordered dt-responsive nowrap" style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                        <thead>
                            <tr>
                                <th>User</th>
                                <th>Create</th>
                                <th>Read</th>
                                <th>Update</th>
                                <th>Delete</th>
                                <th>Tanggal Dibuat</th>
                                <th>Tanggal Diubah</th>
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
    {{-- <script src="{{ URL::asset('assets/plugins/select2/select2.min.js') }}"></script> --}}
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
    <script src="{{ URL::asset('public/assets/js/app.js') }}"></script>
    <script src="{{ URL::asset('public/assets/js/iziToast.min.js') }}"></script>
    <script src="{{ URL::asset('public/assets/plugins/sweet-alert2/sweetalert2.min.js') }}"></script>

    {{-- <script src="{{ URL::asset('assets/plugins/huebee/huebee.pkgd.min.js') }}"></script>
    <script src="{{ URL::asset('assets/plugins/timepicker/bootstrap-material-datetimepicker.js') }}"></script>
    <script src="{{ URL::asset('assets/plugins/bootstrap-maxlength/bootstrap-maxlength.min.js') }}"></script>
    <script src="{{ URL::asset('assets/plugins/bootstrap-touchspin/js/jquery.bootstrap-touchspin.min.js') }}"></script> --}}
    <script src="{{ URL::asset('public/assets/js/pages/jquery.forms-advanced.js') }}"></script>
    
    <script>
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        var table = $('#datatables').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route('user_management') }}",
            columns: [
                {
                    data: 'user',
                    name: 'user'
                },
                {
                    data: 'c',
                    name: 'c'
                },
                {
                    data: 'r',
                    name: 'r'
                },
                {
                    data: 'u',
                    name: 'u'
                },
                {
                    data: 'd',
                    name: 'd'
                },
                {
                    data: 'created_at',
                    name: 'created_at'
                },
                {
                    data: 'updated_at',
                    name: 'updated_at'
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
            // alert(id);
            var ids = id;
            $.ajax({
                type:'GET',
                url: "{{ url('management-user/') }}"+'/'+id+'/edit',
                contentType: "application/json;  charset=utf-8",
                cache: false,
                success: (result) => {
                    // alert(result);
                    $('.modalEdit').modal('show');
                    // document.getElementById('edit_pengguna').innerHTML = 'Edit - '+result.data.name;
                    $('#edit_id').val(result.data.id);
                    $('#edit_user_id').val(result.data.user_id);
                    if(result.data.c == 'Y'){
                        $(".edit_c").prop("checked", true);
                    }
                    if(result.data.r == 'Y'){
                        $(".edit_r").prop("checked", true);
                    }
                    if(result.data.u == 'Y'){
                        $(".edit_u").prop("checked", true);
                    }
                    if(result.data.d == 'Y'){
                        $(".edit_d").prop("checked", true);
                    }
                    // $('#edit_c').val(result.data.c);
                    // $('#edit_r').val(result.data.r);
                    // $('#edit_u').val(result.data.u);
                    // $('#edit_d').val(result.data.d);
                },
                error: function (request, status, error) {
                    iziToast.error({
                        title: 'Error',
                        message: error,
                    });
                }
            });
        }

        function hapus(id) {
            // alert(id);
            iziToast.show({
                theme: 'dark',
                icon: 'icon-person',
                title: 'Hallo',
                message: 'Apakah anda yakin menghapus ini?',
                position: 'center', // bottomRight, bottomLeft, topRight, topLeft, topCenter, bottomCenter
                progressBarColor: 'rgb(0, 255, 184)',
                buttons: [
                    ['<button>Ya</button>', function (instance, toast) {
                        // alert("Hello world!");
                        $.ajax({
                            type:'GET',
                            url: "{{ url('users/') }}"+'/'+id+'/delete',
                            contentType: "application/json;  charset=utf-8",
                            cache: false,
                            success: (result) => {
                                if(result.success == true){
                                    // iziToast.success({
                                    //     title: result.message_title,
                                    //     message: result.message_content
                                    // });
                                    alert(result.message_content);
                                    table.ajax.reload();
                                }else{
                                    iziToast.error({
                                        title: result.message_title,
                                        message: result.message_content
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
                    }, true], // true to focus
                    ['<button>Tidak</button>', function (instance, toast) {
                        instance.hide({
                            transitionOut: 'fadeOutUp',
                            onClosing: function(instance, toast, closedBy){
                                console.info('closedBy: ' + closedBy); // The return will be: 'closedBy: buttonName'
                            }
                        }, toast, 'buttonName');
                    }]
                ],
                onOpening: function(instance, toast){
                    console.info('callback abriu!');
                },
                onClosing: function(instance, toast, closedBy){
                    console.info('closedBy: ' + closedBy); // tells if it was closed by 'drag' or 'button'
                }
            });
            // $.ajax({
            //     type:'GET',
            //     url: "{{ url('users/') }}"+'/'+id+'/edit',
            //     contentType: "application/json;  charset=utf-8",
            //     cache: false,
            //     success: (result) => {
            //         // alert(result);
            //         $('.modalEdit').modal('show');
            //         // document.getElementById('edit_pengguna').innerHTML = 'Edit - '+result.data.name;
            //         $('#edit_id').val(result.data.id);
            //         $('#edit_username').val(result.data.username);
            //         $('#edit_name').val(result.data.name);
            //         $('#edit_email').val(result.data.email);
            //         $('#edit_roles').val(result.data.roles);
            //     },
            //     error: function (request, status, error) {
            //         iziToast.error({
            //             title: 'Error',
            //             message: error,
            //         });
            //     }
            // });
        }

        $('#form-simpan').submit(function(e) {
            e.preventDefault();
            let formData = new FormData(this);
            $('#image-input-error').text('');
            $.ajax({
                type:'POST',
                url: "{{ route('user_management.simpan') }}",
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
                        // $('.modalBuat').hide();
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

        $('#form-update').submit(function(e) {
            e.preventDefault();
            let formData = new FormData(this);
            $('#image-input-error').text('');
            $.ajax({
                type:'POST',
                url: "{{ route('user_management.update') }}",
                data: formData,
                contentType: false,
                processData: false,
                success: (result) => {
                    if(result.success != false){
                        iziToast.success({
                            title: result.message_title,
                            message: result.message_content
                        });
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
    </script>
@endsection