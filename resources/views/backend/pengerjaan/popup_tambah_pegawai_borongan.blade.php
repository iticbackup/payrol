@extends('layouts.backend.app')
@section('css')
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
    <div class="row">
        <div class="col-12">
            <div class="card">
                {{-- <form action="{{ route('pengerjaan.popup_tambah_pegawai_simpan',['id' => $id, 'kode_pengerjaan' => $kode_pengerjaan, 'jenis_pekerja_id' => $jenis_pekerjaan_id]) }}" method="post" enctype="multipart/form-data"> --}}
                <form id="form-simpan" method="post" enctype="multipart/form-data">
                    @csrf
                <div class="card-header">
                    <h4 class="card-title text-center">Pegawai Diperbantukan</h4>
                </div>
                <div class="card-body">
                    <div class="row">
                        {{-- <input type="text" name="" value="{{ $jenis_pekerjaan_id }}" id=""> --}}
                        @foreach ($jenis_operator_detail_pekerjaans as $jenis_operator_detail_pekerjaan)
                            @php
                                $karyawan_operators = \App\Models\KaryawanOperator::where('jenis_operator_detail_pekerjaan_id',$jenis_operator_detail_pekerjaan->id)->where('status','Y')->get();
                            @endphp
                            <div class="col-md-3">
                                <table class="table table-bordered dt-responsive nowrap"
                                style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                                    <thead>
                                        <tr>
                                            <th colspan="5" class="text-center">Pegawai {{ $jenis_operator_detail_pekerjaan->jenis_posisi_pekerjaan }}</th>
                                        </tr>
                                        <tr>
                                            <th class="text-center">No</th>
                                            <th class="text-center">NIK</th>
                                            <th class="text-center">Nama</th>
                                            <th class="text-center">Status Karyawan</th>
                                            <th class="text-center"><input type="checkbox" name="toggle" onclick="toggle_{{ $jenis_operator_detail_pekerjaan->id }}(this)" class="form-check-input"></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($karyawan_operators as $key => $karyawan_operator)
                                            @php
                                                $biodata_karyawan = \App\Models\BiodataKaryawan::where('nik',$karyawan_operator->nik)->first();
                                            @endphp
                                            <tr>
                                                <td class="text-center">{{ $key+1 }}</td>
                                                <td class="text-center">
                                                    {{ $karyawan_operator->nik }}
                                                    {{-- <input type="text" name="" value="{{ $karyawan_operator->nik }}" id="">
                                                    <input type="text" name="" value="{{ $karyawan_operator->jenis_operator_id }}" id="">
                                                    <input type="text" name="" value="{{ $karyawan_operator->jenis_operator_detail_id }}" id="">
                                                    <input type="text" name="" value="{{ $karyawan_operator->jenis_operator_detail_pekerjaan_id }}" id=""> --}}
                                                </td>
                                                <td class="text-left">{{ $biodata_karyawan->nama }}</td>
                                                <td class="text-center">
                                                    <div data-bs-toggle="tooltip" data-bs-placement="top" title="ID: {{ $karyawan_operator->id }}">
                                                        {{ $karyawan_operator->status == 'Y' ? 'Aktif' : 'Tidak Aktif' }}
                                                    </div>
                                                </td>
                                                <td>
                                                    {{-- <input type="text" name="key" value="{{ $key+1 }}" id=""> --}}
                                                    <input type="hidden" name="pegawai_{{ $jenis_operator_detail_pekerjaan->id }}" value="{{ $jenis_operator_detail_pekerjaan->id }}" id="">
                                                    <input type="checkbox" name="checkbox_{{ $jenis_operator_detail_pekerjaan->id }}[]" value="{{ $karyawan_operator->nik }}" class="form-check-input" id="">
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @endforeach
                    </div>
                </div>
                <div class="card-footer text-center">
                    <button type="submit" class="btn btn-xs btn-primary">Submit</button>
                </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('script')
<script src="{{ URL::asset('public/assets/js/iziToast.min.js') }}"></script>
<script src="{{ URL::asset('public/assets/plugins/sweet-alert2/sweetalert2.min.js') }}"></script>
<script>
    function toggle_1(source) {
        checkboxes = document.getElementsByName('checkbox_1[]');
        for (var i = 0, n = checkboxes.length; i < n; i++) {
            checkboxes[i].checked = source.checked;
        }
    }
    function toggle_2(source) {
        checkboxes = document.getElementsByName('checkbox_2[]');
        for (var i = 0, n = checkboxes.length; i < n; i++) {
            checkboxes[i].checked = source.checked;
        }
    }
    function toggle_3(source) {
        checkboxes = document.getElementsByName('checkbox_3[]');
        for (var i = 0, n = checkboxes.length; i < n; i++) {
            checkboxes[i].checked = source.checked;
        }
    }
    function toggle_4(source) {
        checkboxes = document.getElementsByName('checkbox_4[]');
        for (var i = 0, n = checkboxes.length; i < n; i++) {
            checkboxes[i].checked = source.checked;
        }
    }

    $('#form-simpan').submit(function(e) {
        e.preventDefault();
        let formData = new FormData(this);
        $('#image-input-error').text('');
        $.ajax({
            type: 'POST',
            url: "{{ route('pengerjaan.popup_tambah_pegawai_simpan',['id' => $id, 'kode_pengerjaan' => $kode_pengerjaan, 'jenis_pekerja_id' => $jenis_pekerjaan_id]) }}",
            data: formData,
            contentType: false,
            processData: false,
            beforeSend: function() {
                $('.modalLoading').modal('show');
            },
            success: (result) => {
                if (result.success != false) {
                    // iziToast.success({
                    //     title: result.success,
                    //     message: result.message_title
                    // });

                    // setTimeout(() => {
                    //     $('.modalLoading').modal('hide');
                    //     window.location.href = "{{ route('pengerjaan.hasil_kerja') }}";
                    // }, 3000);

                    var timerInterval
                    Swal.fire({
                    title: result.message_title+' - '+result.message_content,
                    html: 'I will close in <strong></strong> seconds.',
                    timer: 3000,
                    onBeforeOpen: function() {
                        Swal.showLoading()
                        timerInterval = setInterval(function() {
                        Swal.getContent().querySelector('strong')
                            .textContent = Swal.getTimerLeft()
                        }, 100)
                    },
                    onClose: function() {
                        clearInterval(timerInterval)
                    }
                    }).then(function(result) {
                        if (result.dismiss === Swal.DismissReason.timer) {
                            window.close();
                        }
                    })
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