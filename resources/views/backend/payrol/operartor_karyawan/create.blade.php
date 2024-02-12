@extends('layouts.backend.app')

@section('title')
    Buat Data Karyawan Operator
@endsection

@section('content')
    @component('components.breadcrumb')
        @slot('li_1')
            Data Karyawan Operator
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
                    <div class="row">
                        <div class="col-md-2">
                            <div class="mb-3">
                                <label class="form-label">NIK</label>
                                <input type="text" class="form-control" name="nik" placeholder="NIK Karyawan">
                            </div>
                        </div>
                        <div class="col-md-10">
                            <div class="mb-3">
                                <label class="form-label">Nama Karyawan</label>
                                <input type="text" class="form-control" name="nama_karyawan" placeholder="Nama Karyawan">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Posisi</label>
                                <select name="jenis_operator_id" class="form-control" id="posisi">
                                    <option>-- Pilih Posisi --</option>
                                    @foreach ($jenis_operators as $jenis_operator)
                                        <option value="{{ $jenis_operator->id }}">{{ $jenis_operator->jenis_operator }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Posisi Pengerjaan</label>
                                <select name="jenis_operator_detail_id" class="form-control" id="posisi_pengerjaan">
                                    <option>-- Pilih Posisi Pengerjaan --</option>
                                    {{-- @foreach ($jenis_operators as $jenis_operator)
                                        <option value="{{ $jenis_operator->id }}">{{ $jenis_operator->jenis_operator }}</option>
                                    @endforeach --}}
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label">JHT</label>
                                <select name="jht" class="form-control" id="">
                                    <option>-- Pilih JHT --</option>
                                    <option value="Y">Ya</option>
                                    <option value="T">Tidak</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label">BPJS</label>
                                <select name="bpjs" class="form-control" id="">
                                    <option>-- Pilih BPJS --</option>
                                    <option value="Y">Ya</option>
                                    <option value="T">Tidak</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label">Training</label>
                                <select name="training" class="form-control" id="">
                                    <option>-- Pilih Training --</option>
                                    <option value="Y">Ya</option>
                                    <option value="T">Tidak</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-footer">
                    <button type="submit" class="btn btn-soft-primary btn-sm">Submit</button>
                    <a class="btn btn-soft-secondary btn-sm">Back</a>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/axios/1.4.0/axios.min.js"
        integrity="sha512-uMtXmF28A2Ab/JJO2t/vYhlaa/3ahUOgj1Zf27M5rOo8/+fcTUVH0/E0ll68njmjrLqOBjXM3V9NiPFL5ywWPQ=="
        crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script>
        $('#posisi').on('change', function() {
            axios.post('{{ route('operator_karyawan.select_jenis_operator_detail') }}', {
                    id: $(this).val()
                })
                .then(function(response) {
                    $('#posisi_pengerjaan').empty();

                    $.each(response.data, function(id, jenis_posisi) {
                        // alert(nama);
                        $('#posisi_pengerjaan').append(new Option(jenis_posisi, id));
                    })
                });
        });
    </script>
@endsection
