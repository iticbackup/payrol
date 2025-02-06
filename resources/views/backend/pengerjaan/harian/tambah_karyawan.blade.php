@extends('layouts.backend.app')
@section('title')
    Pengerjaan - Tambah Karyawan Harian {{ $jenis_operator_detail_pekerjaan->jenis_posisi_pekerjaan }}
@endsection
@section('css')
    <link href="{{ URL::asset('public/assets/css/iziToast.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ URL::asset('public/assets/css/iziToast.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ URL::asset('public/assets/plugins/sweet-alert2/sweetalert2.min.css') }}" rel="stylesheet" type="text/css">
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
                <div class="card-header">
                    <h5>Tambah Karyawan Harian {{ $jenis_operator_detail_pekerjaan->jenis_posisi_pekerjaan }}</h5>
                    <div class="alert alert-light mb-0" role="alert">
                        <h4 class="alert-heading font-18">Informasi!</h4>
                        <p>
                            Dipastikan untuk inputan karyawan harian sesuai dengan kategori pengerjaan harian dan kode pengerjaan.
                            <div class="checkbox checkbox-primary">
                                <input type="checkbox" disabled checked>
                                <label for="checkbox">NIK - Nama</label> : Telah Diinput
                            </div>
                            <div class="checkbox checkbox-primary">
                                <input type="checkbox">
                                <label for="checkbox">NIK - Nama</label> : Belum Diinput
                            </div>
                        </p>
                    </div>
                </div>
                <form id="form-simpan" method="POST">
                    @csrf
                    <div class="card-body">
                        <table class="table table-responsive">
                            <tbody>
                                <tr>
                                    <td>Kode Pengerjaan</td>
                                    <td>:</td>
                                    <td>{{ $kode_pengerjaan }}</td>
                                </tr>
                                <tr>
                                    <td style="vertical-align: top">Nama Karyawan</td>
                                    <td style="vertical-align: top">:</td>
                                    <td>
                                        @foreach ($karyawan_operator_harians as $karyawan_operator_harian)
                                            @php
                                                $checklist_karyawan = \App\Models\PengerjaanHarian::where(
                                                    'operator_harian_karyawan_id',
                                                    $karyawan_operator_harian->id,
                                                )
                                                    ->where('kode_pengerjaan', $kode_pengerjaan)
                                                    ->first();
                                                // dd($checklist_karyawan);
                                            @endphp
                                            @if (empty($checklist_karyawan))
                                                <input type="hidden" {{-- name="pegawai_{{ $karyawan_operator_harian->id }}" --}}
                                                    value="{{ $karyawan_operator_harian->id }}" id="">
                                                <div class="checkbox checkbox-primary">
                                                    <input id="checkbox_{{ $karyawan_operator_harian->id }}" type="checkbox"
                                                        name="checkbox_{{ $jenis_operator_detail_pekerjaan->id }}[]"
                                                        value="{{ $karyawan_operator_harian->id }}">
                                                    <label for="checkbox_{{ $karyawan_operator_harian->id }}">
                                                        {{ $karyawan_operator_harian->nik . ' - ' . $karyawan_operator_harian->nama }}
                                                    </label>
                                                </div>
                                            @else
                                                <div class="checkbox checkbox-primary">
                                                    <input id="checkbox_{{ $karyawan_operator_harian->id }}"
                                                        type="checkbox" disabled checked>
                                                    <label for="checkbox_{{ $karyawan_operator_harian->id }}">
                                                        {{ $karyawan_operator_harian->nik . ' - ' . $karyawan_operator_harian->nama }}
                                                    </label>
                                                </div>
                                            @endif
                                        @endforeach
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                        {{-- <div class="mb-3">
                            <label for="">Nama Karyawan</label>
                        </div> --}}
                    </div>
                    <div class="card-footer">
                        <button type="submit" class="btn btn-success">Submit</button>
                        <a href="{{ url()->previous() }}" class="btn btn-secondary">Back</a>
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
        $('#form-simpan').submit(function(e) {
        e.preventDefault();
        let formData = new FormData(this);
        $('#image-input-error').text('');
        Swal.fire({
            title: 'Apakah Anda yakin?',
            text: "Periksa kembali dan inputan tidak bisa dikembalikan setelah disubmit.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '$success',
            cancelButtonColor: '$danger',
            confirmButtonText: 'Yes, Submit!'
            }).then(function(result) {
            if (result.value) {
                $.ajax({
                    type: 'POST',
                    url: "{{ route('hasil_kerja.harian.tambah_karyawan.simpan', ['kode_pengerjaan' => $kode_pengerjaan, 'id' => $jenis_operator_detail_pekerjaan->jenis_operator_detail_id]) }}",
                    data: formData,
                    contentType: false,
                    processData: false,
                    beforeSend: function() {
                        $('.modalLoading').modal('show');
                    },
                    success: (result) => {
                        if (result.success != false) {
                            iziToast.success({
                                title: result.success,
                                message: result.message_title
                            });
                            setTimeout(() => {
                                $('.modalLoading').modal('hide');
                                window.location.href = "{{ url()->previous() }}";
                            }, 3000);
                        } else {}
                    },
                    error: function(request, status, error) {
                        iziToast.error({
                            title: 'Error',
                            message: error,
                        });
                    }
                });
            }
        });

        });
    </script>
@endsection
