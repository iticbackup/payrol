@extends('layouts.backend.app')
@section('title')
    Pengerjaan - Harian {{ $jenis_operator_detail_pekerjaan->jenis_posisi_pekerjaan }}
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
                <div class="card-header"></div>
                <div class="card-body">
                    <table id="datatables" class="table table-bordered dt-responsive nowrap"
                        style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                        <thead>
                            <tr>
                                <th rowspan="2" class="text-center">No</th>
                                <th rowspan="2" class="text-center">NIK</th>
                                <th rowspan="2" class="text-center">Nama Karyawan</th>
                                <th colspan="6" class="text-center">Tanggal</th>
                                <th rowspan="2" class="text-center">Total Upah Dasar</th>
                                <th colspan="5" class="text-center">PLUS</th>
                                <th rowspan="2" class="text-center">Total Gaji</th>
                                <th colspan="4" class="text-center">POTONGAN</th>
                                <th rowspan="2" class="text-center">DITERIMA</th>
                            </tr>
                            <tr>
                                @foreach ($explode_tanggal_pengerjaans as $key => $explode_tanggal_pengerjaan)
                                @if ($key != 0)
                                <th class="text-center">{{ \Carbon\Carbon::parse($explode_tanggal_pengerjaan)->isoFormat('D MMMM') }}</th>
                                @endif
                                @endforeach
                                <th class="text-center">Plus 1</th>
                                <th class="text-center">Plus 2</th>
                                <th class="text-center">Plus 3</th>
                                <th class="text-center">Uang Makan</th>
                                <th class="text-center">Lembur</th>
                                <th class="text-center">Minus 1</th>
                                <th class="text-center">Minus 2</th>
                                <th class="text-center">BPJS Ketenagakerjaan</th>
                                <th class="text-center">BPJS Kesehatan</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($karyawan_operator_harians as $key => $karyawan_operator_harian)
                                @php
                                    $hasil_pengerjaan_harians = \App\Models\PengerjaanHarian::where('operator_harian_karyawan_id',$karyawan_operator_harian->id)->first();
                                    if (empty($hasil_pengerjaan_harians)) {
                                        $hasil_kerja = '-';
                                    }else{
                                        $explode_hasil_kerja = explode('|',$hasil_pengerjaan_harians->hasil_kerja);
                                        $hasil_kerja = $explode_hasil_kerja[$key];
                                    }

                                    $month = \Carbon\Carbon::now()->format('m');
                                    $year = \Carbon\Carbon::now()->format('Y');
                                @endphp
                                <tr>
                                    <td class="text-center">{{ $key+1 }}</td>
                                    <td class="text-center">{{ $karyawan_operator_harian->nik }}</td>
                                    <td><a href="javascript:void(0)" onclick="window.open('{{ route('hasil_kerja.marketing.view',['id' => $id, 'kode_pengerjaan' => $kode_pengerjaan, 'nik' => $karyawan_operator_harian->nik, 'month' => $month, 'year' => $year]) }}','','width=1200,height=555,left=80,top=80,location=yes, menubar=no, status=no,toolbar=no, scrollbars=yes, resizable=yes')" class="text-primary">{{ $karyawan_operator_harian->nama }}</a></td>
                                    {{-- <td><a href="{{ route('hasil_kerja.marketing.view',['id' => $id, 'kode_pengerjaan' => $kode_pengerjaan, 'id_nik' => $karyawan_operator_harian->id]) }}" class="text-primary">{{ $karyawan_operator_harian->nama }}</a></td> --}}
                                    @foreach ($explode_tanggal_pengerjaans as $keys => $explode_tanggal_pengerjaan)
                                        @if ($keys !=0)
                                        <td class="text-center">{{ $hasil_kerja }}</td>
                                        @endif
                                    @endforeach
                                    <td class="text-center">-</td>{{-- total_upah_dasar --}}
                                    <td>-</td>
                                    <td>-</td>
                                    <td>-</td>
                                    <td>-</td>
                                    <td>-</td>
                                    <td>-</td>{{-- total_gaji --}}
                                    <td>-</td>
                                    <td>-</td>
                                    <td>-</td>
                                    <td>-</td>
                                    <td>-</td>{{-- diterima --}}
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
