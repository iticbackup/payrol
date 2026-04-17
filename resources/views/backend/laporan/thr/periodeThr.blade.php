@extends('layouts.backend.app')
@section('title', 'THR - Periode '.$periode)

@section('css')
    <link href="{{ URL::asset('public/assets/plugins/datatables/dataTables.bootstrap5.min.css') }}" rel="stylesheet"
        type="text/css" />
    <link href="{{ URL::asset('public/assets/plugins/datatables/buttons.bootstrap5.min.css') }}" rel="stylesheet"
        type="text/css" />
    <link href="{{ URL::asset('public/assets/plugins/datatables/responsive.bootstrap4.min.css') }}" rel="stylesheet"
        type="text/css" />
@endsection

@section('content')
    @component('components.breadcrumb')
        @slot('li_1')
            Laporan
        @endslot
        @slot('li_3')
            THR / @yield('title')
        @endslot
        @slot('title')
            @yield('title')
        @endslot
    @endcomponent

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Perhitungan THR Per {{ $totalBulan }} Bulan Terakhir</h4>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <div class="fw-bold">Update Masa Kerja Cut Off Pertanggal : {{ $cut_off->format('d-m-Y') }}</div>
                        <div class="fw-bold">Total Record : {{ $list_karyawans->count() }} Data</div>
                    </div>
                    <div class="mb-3">
                        <a href="{{ route('laporan.thr.slip_gaji',['periode' => $periode]) }}" class="btn btn-primary">Print Slip Gaji</a>
                    </div>
                    <div class="table-responsive">
                        <table class="table" id="datatables">
                            <thead>
                                <tr>
                                    <th class="text-center">No</th>
                                    <th class="text-center">NIK</th>
                                    <th class="text-center">Nama Karyawan</th>
                                    <th class="text-center">Bagian</th>
                                    <th class="text-center">Status</th>
                                    <th class="text-center">Masa Kerja</th>
                                    <th class="text-center">Minimal UMK</th>
                                    <th class="text-center">Persen %</th>
                                    <th class="text-center">Total Gaji</th>
                                    <th class="text-center">Rata-Rata Gaji</th>
                                    <th class="text-center">THR Diterima</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $total_gaji = [];
                                    $total_rata_gaji = [];
                                    $total_thr_diterima = [];
                                @endphp
                                @foreach ($list_karyawans as $key => $item)
                                    {{-- Borongan --}}
                                    @if ($item->id_departemen_bagian >= 12 && $item->id_departemen_bagian <= 17 || $item->id_departemen_bagian == 35)
                                    @php
                                        $awal = new DateTime($item->biodata_karyawan->tanggal_masuk);
                                        $akhir = $cut_off;
                                        $diff  = $awal->diff($akhir);

                                        array_push($total_gaji,$item->nominal_gaji);
                                        array_push($total_rata_gaji,$item->nominal_gaji/$totalBulan);

                                        if ($item->nominal_gaji/$totalBulan < $bpjsKesehatan->nominal) {
                                            $gaji_minimum_umk = $bpjsKesehatan->nominal;
                                        }else{
                                            $gaji_minimum_umk = $item->nominal_gaji/$totalBulan;    
                                        }

                                        if ($diff->y >= 20) {
                                            $percentase = 205;
                                            $hasil_percentase = $gaji_minimum_umk*2.05;
                                        }
                                        elseif($diff->y >= 15 && $diff->y < 20) {
                                            $percentase = 180;
                                            $hasil_percentase = $gaji_minimum_umk*1.80;
                                        }
                                        elseif($diff->y >= 10 && $diff->y < 15) {
                                            $percentase = 155;
                                            $hasil_percentase = $gaji_minimum_umk*1.55;
                                        }
                                        elseif($diff->y >= 5 && $diff->y < 10) {
                                            $percentase = 130;
                                            $hasil_percentase = $gaji_minimum_umk*1.30;
                                        }
                                        elseif($diff->y >= 2 && $diff->y < 5) {
                                            $percentase = 120;
                                            $hasil_percentase = $gaji_minimum_umk*1.20;
                                        }
                                        elseif($diff->y >= 1 && $diff->y < 2) {
                                            $percentase = 100;
                                            $hasil_percentase = $gaji_minimum_umk*1;
                                        }
                                        else{
                                            if ($diff->m >= 1) {
                                                $percentase = $diff->m/12;
                                                $hasil_percentase = $gaji_minimum_umk*$percentase;
                                            }else{
                                                $percentase = 0;
                                                $hasil_percentase = 0;
                                            }
                                        }

                                        if ($percentase >= 100) {
                                            $nilai_percentase = $percentase;
                                        }else{
                                            $nilai_percentase = number_format($percentase*100,0,',','.');   
                                        }

                                        array_push($total_thr_diterima,$hasil_percentase);
                                    @endphp

                                    {{-- harian --}}
                                    @elseif($item->id_departemen_bagian >= 0 && $item->id_departemen_bagian <= 11 || $item->id_departemen_bagian >= 18 && $item->id_departemen_bagian <= 34)
                                    @php
                                        $awal = new DateTime($item->biodata_karyawan->tanggal_masuk);
                                        $akhir = $cut_off;
                                        $diff  = $awal->diff($akhir);

                                        array_push($total_gaji,$item->nominal_gaji);
                                        array_push($total_rata_gaji,$item->nominal_gaji/$totalBulan);

                                        if ($diff->y >= 15) {
                                            $gaji_minimum_umk = $bpjsKesehatan->nominal+100000;
                                        }elseif($diff->y >= 10 && $diff->y <= 14) {
                                            $gaji_minimum_umk = $bpjsKesehatan->nominal+50000;
                                        }elseif($diff->y < 10) {
                                            $gaji_minimum_umk = $bpjsKesehatan->nominal;
                                        }

                                        if ($diff->y >= 20) {
                                            $percentase = 205;
                                            $hasil_percentase = $gaji_minimum_umk*2.05;
                                        }
                                        elseif($diff->y >= 15 && $diff->y < 20) {
                                            $percentase = 180;
                                            $hasil_percentase = $gaji_minimum_umk*1.80;
                                        }
                                        elseif($diff->y >= 10 && $diff->y < 15) {
                                            $percentase = 155;
                                            $hasil_percentase = $gaji_minimum_umk*1.55;
                                        }
                                        elseif($diff->y >= 5 && $diff->y < 10) {
                                            $percentase = 130;
                                            $hasil_percentase = $gaji_minimum_umk*1.30;
                                        }
                                        elseif($diff->y >= 2 && $diff->y < 5) {
                                            $percentase = 120;
                                            $hasil_percentase = $gaji_minimum_umk*1.20;
                                        }
                                        elseif($diff->y >= 1 && $diff->y < 2) {
                                            $percentase = 100;
                                            $hasil_percentase = $gaji_minimum_umk*1;
                                        }
                                        else{
                                            if ($diff->m >= 1) {
                                                $percentase = $diff->m/12;
                                                $hasil_percentase = $gaji_minimum_umk*$percentase;
                                            }else{
                                                $percentase = 0;
                                                $hasil_percentase = 0;
                                            }
                                        }

                                        if ($percentase >= 100) {
                                            $nilai_percentase = $percentase;
                                        }else{
                                            $nilai_percentase = number_format($percentase*100,0,',','.');    
                                        }

                                        array_push($total_thr_diterima,$hasil_percentase);
                                    @endphp
                                    @endif
                                    <tr>
                                        <td class="text-center">{{ $key+1 }}</td>
                                        <td class="text-center">{{ $item->nik }}</td>
                                        <td>{{ $item->biodata_karyawan->nama }}</td>
                                        <td>{{ empty($item->biodata_karyawan->departemen_bagian) ? '-' : $item->biodata_karyawan->departemen_dept.' - '.$item->biodata_karyawan->departemen_bagian }}</td>
                                        <td class="text-center">
                                            @if (empty($item->biodata_karyawan->status_karyawan))
                                                -
                                            @else
                                                @switch($item->biodata_karyawan->status_karyawan)
                                                    @case('A')
                                                        <span class="badge bg-success">Tetap</span>
                                                        @break
                                                    @case('R')
                                                        <span class="badge bg-danger">Resign</span>
                                                        @break
                                                    @case('K')
                                                        <span class="badge bg-warning">Kontrak</span>
                                                        @break
                                                    @default
                                                        
                                                @endswitch
                                            @endif
                                        </td>
                                        <td class="text-center">{{ $diff->y.' Tahun '.$diff->m.' Bulan '.$diff->d.' Hari' }}</td>
                                        <td class="text-end">{{ 'Rp. '.number_format($gaji_minimum_umk,2,',','.') }}</td>
                                        {{-- <td class="text-center">{{ number_format($percentase,2,',','.') }}%</td> --}}
                                        <td class="text-center">{{ $nilai_percentase }}%</td>
                                        <td class="text-end">{{ 'Rp. '.number_format($item->nominal_gaji,2,',','.') }}</td>
                                        <td class="text-end">{{ 'Rp. '.number_format($item->nominal_gaji/$totalBulan,2,',','.') }}</td>
                                        <td class="text-end">{{ 'Rp. '.number_format($hasil_percentase,0,',','.') }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th colspan="8" class="text-end">Total</th>
                                    <th class="text-end">{{ 'Rp. '.number_format(array_sum($total_gaji),0,',','.') }}</th>
                                    <th class="text-end">{{ 'Rp. '.number_format(array_sum($total_rata_gaji),0,',','.') }}</th>
                                    <th class="text-end">{{ 'Rp. '.number_format(array_sum($total_thr_diterima),0,',','.') }}</th>
                                </tr>
                            </tfoot>
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

    <script>
        $('#datatables').DataTable();
    </script>
@endsection