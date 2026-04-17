@extends('layouts.backend.app')
@section('title', 'THR - Periode '.$periode)

@section('content')
    @component('components.breadcrumb')
        @slot('li_1')
            Laporan
        @endslot
        @slot('li_3')
            THR / {{ $nama_pengerjaan }} / @yield('title')
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
                    <div class="table-responsive">
                        <table class="table">
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
                                    {{-- @if (!empty($item->karyawan_operator->biodata_karyawan))
                                        @if ($item->karyawan_operator->biodata_karyawan->status_karyawan != 'R')
                                            @php
                                                $awal = new DateTime($item->karyawan_operator->biodata_karyawan->tanggal_masuk);
                                                $akhir = new DateTime();
                                                $diff  = $awal->diff($akhir);

                                                array_push($total_gaji,$item->nominal_gaji);
                                                array_push($total_rata_gaji,$item->nominal_gaji/$totalBulan);
                                            @endphp
                                            <tr>
                                                <td class="text-center">{{ $key+1 }}</td>
                                                <td class="text-center">{{ $item->nik }}</td>
                                                <td >{{ $item->karyawan_operator->biodata_karyawan->nama }}</td>
                                                <td class="text-center">{{ empty($item->karyawan_operator->biodata_karyawan->departemen_bagian) ? '-' : $item->karyawan_operator->biodata_karyawan->departemen_dept }}</td>
                                                <td class="text-center">
                                                    @if (empty($item->karyawan_operator->biodata_karyawan->status_karyawan))
                                                        -
                                                    @else
                                                        @switch($item->karyawan_operator->biodata_karyawan->status_karyawan)
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
                                                <td class="text-end">{{ 'Rp. '.number_format($bpjsKesehatan->nominal,2,',','.') }}</td>
                                                <td class="text-end">{{ 'Rp. '.number_format($item->nominal_gaji,2,',','.') }}</td>
                                                <td class="text-end">{{ 'Rp. '.number_format($item->nominal_gaji/$totalBulan,2,',','.') }}</td>
                                            </tr>
                                        @endif
                                    @else
                                        <tr>
                                            <td></td>
                                        </tr>
                                    @endif --}}
                                    {{-- @if ($item->karyawan_operator->biodata_karyawan->status_karyawan != 'R')
                                        @php
                                            $awal = new DateTime($item->karyawan_operator->biodata_karyawan->tanggal_masuk);
                                            $akhir = new DateTime();
                                            $diff  = $awal->diff($akhir);

                                            array_push($total_gaji,$item->nominal_gaji);
                                            array_push($total_rata_gaji,$item->nominal_gaji/$totalBulan);
                                        @endphp
                                        <tr>
                                            <td class="text-center">{{ $key+1 }}</td>
                                            <td class="text-center">{{ $item->nik }}</td>
                                            <td>
                                                {{ $kode_pengerjaan == 'PB' ?
                                                    $item->karyawan_operator->biodata_karyawan->nama :
                                                    $kode_pengerjaan == 'PH' ? 
                                                    $item->karyawan_operator_harian->biodata_karyawan->nama : 
                                                    $item->karyawan_operator_supir_rit->biodata_karyawan->nama
                                                }}
                                            </td>
                                            <td class="text-center">{{ empty($item->karyawan_operator->biodata_karyawan->departemen_bagian) ? '-' : $item->karyawan_operator->biodata_karyawan->departemen_dept }}</td>
                                            <td class="text-center">
                                                @if (empty(
                                                    $kode_pengerjaan == 'PB' ? 
                                                    $item->karyawan_operator->biodata_karyawan->status_karyawan : 
                                                    $kode_pengerjaan == 'PH' ? 
                                                    $item->karyawan_operator_harian->biodata_karyawan->status_karyawan : 
                                                    $item->karyawan_operator_supir_rit->biodata_karyawan->status_karyawan
                                                    ))
                                                    -
                                                @else
                                                    @switch(
                                                        $kode_pengerjaan == 'PB' ? 
                                                        $item->karyawan_operator->biodata_karyawan->status_karyawan : 
                                                        $kode_pengerjaan == 'PH' ? 
                                                        $item->karyawan_operator_harian->biodata_karyawan->status_karyawan : 
                                                        $item->karyawan_operator_supir_rit->biodata_karyawan->status_karyawan
                                                    )
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
                                            <td class="text-end">{{ 'Rp. '.number_format($bpjsKesehatan->nominal,2,',','.') }}</td>
                                            <td class="text-end">{{ 'Rp. '.number_format($item->nominal_gaji,2,',','.') }}</td>
                                            <td class="text-end">{{ 'Rp. '.number_format($item->nominal_gaji/$totalBulan,2,',','.') }}</td>
                                        </tr>
                                    @endif --}}

                                    {{-- ---- OK ---- --}}
                                    {{-- @php
                                        $awal = new DateTime(
                                            $kode_pengerjaan == 'PB' ? 
                                            $item->karyawan_operator->biodata_karyawan->tanggal_masuk : 
                                            $kode_pengerjaan == 'PH' ? 
                                            $item->karyawan_operator_harian->biodata_karyawan->tanggal_masuk : 
                                            $item->karyawan_operator_supir_rit->biodata_karyawan->tanggal_masuk
                                            );
                                        $akhir = new DateTime();
                                        $diff  = $awal->diff($akhir);

                                        array_push($total_gaji,$item->nominal_gaji);
                                        array_push($total_rata_gaji,$item->nominal_gaji/$totalBulan);

                                        if ($item->nominal_gaji/$totalBulan < $bpjsKesehatan->nominal) {
                                            $rata_rata_gaji = $bpjsKesehatan->nominal;
                                        }else{
                                            $rata_rata_gaji = $item->nominal_gaji/$totalBulan;    
                                        }
                                    @endphp
                                    <tr>
                                        <td class="text-center">{{ $key+1 }}</td>
                                        <td class="text-center">{{ $item->nik }}</td>
                                        <td>
                                            {{ $kode_pengerjaan == 'PB' ?
                                                $item->karyawan_operator->biodata_karyawan->nama :
                                                $kode_pengerjaan == 'PH' ? 
                                                $item->karyawan_operator_harian->biodata_karyawan->nama : 
                                                $item->karyawan_operator_supir_rit->biodata_karyawan->nama
                                            }}
                                        </td>
                                        <td class="text-center">{{ empty($item->karyawan_operator->biodata_karyawan->departemen_bagian) ? '-' : $item->karyawan_operator->biodata_karyawan->departemen_dept }}</td>
                                        <td class="text-center">
                                            @if (empty(
                                                $kode_pengerjaan == 'PB' ? 
                                                $item->karyawan_operator->biodata_karyawan->status_karyawan : 
                                                $kode_pengerjaan == 'PH' ? 
                                                $item->karyawan_operator_harian->biodata_karyawan->status_karyawan : 
                                                $item->karyawan_operator_supir_rit->biodata_karyawan->status_karyawan
                                                ))
                                                -
                                            @else
                                                @switch(
                                                    $kode_pengerjaan == 'PB' ? 
                                                    $item->karyawan_operator->biodata_karyawan->status_karyawan : 
                                                    $kode_pengerjaan == 'PH' ? 
                                                    $item->karyawan_operator_harian->biodata_karyawan->status_karyawan : 
                                                    $item->karyawan_operator_supir_rit->biodata_karyawan->status_karyawan
                                                )
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
                                        <td class="text-end">{{ 'Rp. '.number_format($rata_rata_gaji,2,',','.') }}</td>
                                        <td class="text-end">{{ 'Rp. '.number_format($item->nominal_gaji,2,',','.') }}</td>
                                        <td class="text-end">{{ 'Rp. '.number_format($item->nominal_gaji/$totalBulan,2,',','.') }}</td>
                                    </tr> --}}
                                    {{-- ---- OK ---- --}}
                                    @if ($kode_pengerjaan == 'PB')
                                        @php
                                            $awal = new DateTime($item->karyawan_operator->biodata_karyawan->tanggal_masuk);
                                            $akhir = new DateTime();
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

                                            array_push($total_thr_diterima,$hasil_percentase);
                                        @endphp
                                        <tr>
                                            <td class="text-center">{{ $key+1 }}</td>
                                            <td class="text-center">{{ $item->nik }}</td>
                                            <td>
                                                {{ $item->karyawan_operator->biodata_karyawan->nama }}
                                            </td>
                                            <td class="text-center">{{ empty($item->karyawan_operator->biodata_karyawan->departemen_bagian) ? '-' : $item->karyawan_operator->biodata_karyawan->departemen_dept }}</td>
                                            <td class="text-center">
                                                @if (empty($item->karyawan_operator->biodata_karyawan->status_karyawan))
                                                    -
                                                @else
                                                    @switch($item->karyawan_operator->biodata_karyawan->status_karyawan)
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
                                            <td class="text-center">{{ number_format($percentase,2,',','.') }}%</td>
                                            <td class="text-end">{{ 'Rp. '.number_format($item->nominal_gaji,2,',','.') }}</td>
                                            <td class="text-end">{{ 'Rp. '.number_format($item->nominal_gaji/$totalBulan,2,',','.') }}</td>
                                            <td class="text-end">{{ 'Rp. '.number_format($hasil_percentase,0,',','.') }}</td>
                                        </tr>
                                    @elseif($kode_pengerjaan == 'PH' || $kode_pengerjaan == 'PS')
                                    @php
                                        $awal = new DateTime(
                                            $kode_pengerjaan == 'PH' ? 
                                            $item->karyawan_operator_harian->biodata_karyawan->tanggal_masuk : 
                                            $item->karyawan_operator_supir_rit->biodata_karyawan->tanggal_masuk
                                            );
                                            
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

                                        array_push($total_thr_diterima,$hasil_percentase);

                                    @endphp
                                    <tr>
                                        <td class="text-center">{{ $key+1 }}</td>
                                        <td class="text-center">{{ $item->nik }}</td>
                                        <td>
                                            {{ $kode_pengerjaan == 'PH' ? 
                                                $item->karyawan_operator_harian->biodata_karyawan->nama : 
                                                $item->karyawan_operator_supir_rit->biodata_karyawan->nama
                                            }}
                                        </td>
                                        <td class="text-center">
                                            @if ($kode_pengerjaan == 'PH')
                                                @php
                                                    empty($item->karyawan_operator_harian->biodata_karyawan->departemen_bagian) ? '-' : 
                                                    $item->karyawan_operator_harian->biodata_karyawan->departemen_dept
                                                @endphp
                                            @else
                                                @php
                                                    empty($item->karyawan_operator_supir_rit->biodata_karyawan->departemen_bagian) ? '-' : 
                                                    $item->karyawan_operator_supir_rit->biodata_karyawan->departemen_dept
                                                @endphp
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            @if (empty(
                                                $kode_pengerjaan == 'PH' ? 
                                                $item->karyawan_operator_harian->biodata_karyawan->status_karyawan : 
                                                $item->karyawan_operator_supir_rit->biodata_karyawan->status_karyawan
                                                ))
                                                -
                                            @else
                                                @switch(
                                                    $kode_pengerjaan == 'PH' ? 
                                                    $item->karyawan_operator_harian->biodata_karyawan->status_karyawan : 
                                                    $item->karyawan_operator_supir_rit->biodata_karyawan->status_karyawan
                                                )
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
                                        <td class="text-center">{{ number_format($percentase,2,',','.') }}%</td>
                                        <td class="text-end">{{ 'Rp. '.number_format($item->nominal_gaji,2,',','.') }}</td>
                                        <td class="text-end">{{ 'Rp. '.number_format($item->nominal_gaji/$totalBulan,2,',','.') }}</td>
                                        <td class="text-end">{{ 'Rp. '.number_format($hasil_percentase,0,',','.') }}</td>
                                    </tr>
                                    @endif
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th colspan="7" class="text-end">Total</th>
                                    <th></th>
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