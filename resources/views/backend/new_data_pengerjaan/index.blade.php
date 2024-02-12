@extends('layouts.backend.app')
@section('title')
    Buat Data Pengerjaan
@endsection

@section('content')
    @component('components.breadcrumb')
    @slot('li_1')
        New Data
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
                <form action="{{ route('pengerjaan.simpan') }}" method="post" enctype="multipart/form-data">
                @csrf
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-2">
                                <div class="mb-2">Tanggal</div>
                                <input type="text" name="tanggal" class="form-control" autocomplete="off" id="datePick">
                            </div>
                            <div class="mb-2">
                                <div class="mb-2">Akhir Bulan</div>
                                <select name="akhir_bulan" class="form-control" id="">
                                    <option>-- Status Akhir Bulan --</option>
                                    <option value="y">Ya</option>
                                    <option value="n">Tidak</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-2">Kode Payrol</div>
                            <table class="table table-bordered dt-responsive nowrap"
                            style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                                <thead>
                                    <tr>
                                        <th class="text-center">No</th>
                                        <th class="text-center">Kode Payrol Sebelum</th>
                                        <th class="text-center">Kode Payrol Selanjutnya</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if ($data_new_pengerjaans->isEmpty())
                                    @foreach ($data_new_pengerjaan_awal as $key => $payrol_new_data_awal)
                                        <tr>
                                            <td class="text-center">{{ $key+1 }}</td>
                                            <td class="text-center">{{ $payrol_new_data_awal['kode_payrol'] }}</td>
                                            <td class="text-center">
                                                {{ substr($payrol_new_data_awal['kode_payrol'],0,3).substr($payrol_new_data_awal['kode_payrol'],3,5).sprintf("%04s", substr($payrol_new_data_awal['kode_payrol'],8)+1) }}
                                                <input type="hidden" name="kode_payrol[]" value="{{ substr($payrol_new_data_awal['kode_payrol'],0,3).substr($payrol_new_data_awal['kode_payrol'],3,5).sprintf("%04s", substr($payrol_new_data_awal['kode_payrol'],8)+1) }}" id="">
                                            </td>
                                        </tr>
                                    @endforeach
                                    @else
                                    @forelse ($data_new_pengerjaans as $key => $payrol_new_data)
                                        <tr>
                                            <td class="text-center">{{ $key+1 }}</td>
                                            <td class="text-center">{{ $payrol_new_data['kode_pengerjaan'] }}</td>
                                            <td class="text-center">
                                                {{ substr($payrol_new_data['kode_pengerjaan'],0,3).substr($payrol_new_data['kode_pengerjaan'],3,5).sprintf("%04s", substr($payrol_new_data['kode_pengerjaan'],8)+1) }}
                                                
                                                {{-- {{ substr($payrol_new_data['kode_payrol'],0,3).substr($payrol_new_data['kode_payrol'],3,11).sprintf("%03s", substr($payrol_new_data['kode_payrol'],3,1)) }} --}}
                                                {{-- {{ substr($payrol_new_data['kode_payrol'],0,3) }}{{ sprintf("%04s",substr($payrol_new_data['kode_payrol'],3)+1) }} --}}
                                                {{-- <input type="hidden" name="id_jenis_payrol_new[]" value="" id="">
                                                <input type="hidden" name="tahun_aktif_umk" value="" id=""> --}}
                                                
                                                <input type="hidden" name="kode_payrol[]" value="{{ substr($payrol_new_data['kode_pengerjaan'],0,3).substr($payrol_new_data['kode_pengerjaan'],3,5).sprintf("%04s", substr($payrol_new_data['kode_pengerjaan'],8)+1) }}" id="">
                                            </td>
                                        </tr>
                                    @empty
                                    @endforelse
                                    @endif
                                    {{-- @foreach ($payrol_new_datas as $key => $payrol_new_data)
                                    <tr>
                                        <td class="text-center">{{ $key+1 }}</td>
                                        <td class="text-center">{{ $payrol_new_data->kode_payrol }}</td>
                                        <td class="text-center">
                                            {{ substr($payrol_new_data->kode_payrol,0,3) }}{{ sprintf("%04s",substr($payrol_new_data->kode_payrol,3)+1) }}
                                            <input type="hidden" name="id_jenis_payrol_new[]" value="{{ $payrol_new_data->id_jenis_payrol }}" id="">
                                            <input type="hidden" name="tahun_aktif_umk" value="{{ $payrol_new_data->tahun_aktif_umk }}" id="">
                                            <input type="text" value="{{ substr($payrol_new_data->kode_payrol,0,3) }}{{ sprintf("%04s",substr($payrol_new_data->kode_payrol,3)+1) }}" id="">
                                        </td>
                                    </tr>
                                    @endforeach --}}
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="card-footer">
                    <button type="submit" class="btn btn-soft-primary btn-sm">Submit</button>
                    <a class="btn btn-soft-secondary btn-sm">Back</a>
                </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('script')
<script src='{{ asset('public/assets/js/datepickers.js') }}'></script>
<script>
    $(document).ready(function () {
        // $('#datePick').multiDatesPicker();
        $('#datePick').datepicker({
            multidate: true,
            format: 'dd-mm-yyyy'
        });
    });
</script>
@endsection
