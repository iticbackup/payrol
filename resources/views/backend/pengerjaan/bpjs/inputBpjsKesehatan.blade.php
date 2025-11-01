@extends('layouts.backend.app')

@section('title')
    BPJS Kesehatan
@endsection

@section('css')
    <link href="{{ URL::asset('public/assets/css/iziToast.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ URL::asset('public/assets/plugins/sweet-alert2/sweetalert2.min.css') }}" rel="stylesheet" type="text/css">
    <script src="{{ URL::asset('public/assets/js/pages/jquery.sweet-alert.init.js') }}"></script>
@endsection

@section('content')
    @component('components.breadcrumb')
        @slot('li_1')
            BPJS Kesehatan
        @endslot
        @slot('li_3')
            @yield('title')
        @endslot
        @slot('title')
            @yield('title')
        @endslot
    @endcomponent

    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <form id="form-simpan" method="post" enctype="multipart/form-data">
                    @csrf
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col">
                                <h5>Kode Pengerjaan : {{ $kode_pengerjaan }}</h5>
                            </div>
                        </div>
                        <table class="table table-bordered dt-responsive nowrap" style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                            <thead>
                                <tr>
                                    <th class="text-center">No</th>
                                    <th class="text-center">NIK</th>
                                    <th class="text-center">Nama Karyawan</th>
                                    <th class="text-center">Nominal</th>
                                    <th class="text-center">Checklist
                                        <input type="checkbox" name="toggle" onclick="toggle_{{ $id }}(this)" class="form-check-input" id="">
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($karyawans as $key => $karyawan)
                                    <tr>
                                        {{-- <td class="text-center">{{ $key+1 }}</td> --}}
                                        <td class="text-center">
                                            {{ $karyawan->id }}
                                            <input type="text" name="operator_karyawan_id[]" value="{{ $karyawan->id }}">
                                        </td>
                                        <td class="text-center">{{ $karyawan->nik }}</td>
                                        <td>{{ $karyawan->nama }}</td>
                                        <td class="text-center">
                                            {{ '1% X Rp. ' . number_format($bpjs_kesehatan->nominal, 0, ',', '.') }}
                                        </td>
                                        <td class="text-center">
                                            {{-- <div class="radio radio-info form-check-inline">
                                                <input type="radio" id="checkbox[{{ $key }}]={{ $id }}" value="checkbox_{{ $id }}[]" name="checkbox[{{ $key }}]={{ $id }}">
                                                <label for="checkbox[{{ $key }}]={{ $id }}"> Ya </label>
                                            </div>
                                            <div class="radio form-check-inline">
                                                <input type="radio" id="checkbox[{{ $key }}]={{ $id+$key+1 }}" value="checkbox_{{ $id }}[]" name="checkbox[{{ $key }}]={{ $id }}">
                                                <label for="checkbox[{{ $key }}]={{ $id+$key+1 }}"> Tidak </label>
                                            </div> --}}
                                            @if (empty($karyawan->bpjs_kesehatan))
                                            <input type="checkbox" class="form-check-input" name="checkbox_{{ $id }}[]" value="{{ $karyawan->id }}" id="">
                                            {{-- <input type="text" name="bpjs_kesehatan[]" value="{{ round((1/100)*$bpjs_kesehatan->nominal) }}"> --}}
                                            {{-- @elseif($karyawan->bpjs_kesehatan == 0)
                                            <input type="checkbox" class="form-check-input" name="checkbox_{{ $id }}[]" value="{{ $karyawan->id }}" id=""> --}}
                                            @else
                                            <input type="checkbox" class="form-check-input" name="checkbox_{{ $id }}[]" value="{{ $karyawan->id }}" checked id="">
                                            {{-- <input type="text" name="bpjs_kesehatan[]" value="0"> --}}
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="card-footer">
                        <a href="{{ url()->previous() }}" class="btn btn-dark">Back</a>
                        <button type="submit" class="btn btn-success">Submit</button>
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

        $('#form-simpan').submit(function(e) {
            e.preventDefault();
            let formData = new FormData(this);
            $('#image-input-error').text('');
            $.ajax({
                type: 'POST',
                url: "{{ route('hasil_kerja.input_bpjs_kesehatan.simpan',['id' => $id, 'kode_pengerjaan' => $kode_pengerjaan]) }}",
                data: formData,
                contentType: false,
                processData: false,
                success: (result) => {
                    if (result.success != false) {
                        var timerInterval
                        Swal.fire({
                            title: result.message_title + ' - ' + result.message_content,
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
                                // window.close();
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