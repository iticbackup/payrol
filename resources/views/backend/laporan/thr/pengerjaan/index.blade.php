@extends('layouts.backend.app')
@section('title', 'THR - '.$nama_pengerjaan)
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
                    <h4 class="card-title">Periode</h4>
                </div>
                <div class="card-body">
                    <table class="table table-bordered dt-responsive nowrap" style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Periode</th>
                                <th>Tahun</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($periods as $key => $periode)
                                <tr>
                                    <td>{{ $key+1 }}</td>
                                    <td>{{ $periode->periode }}</td>
                                    <td>{{ $periode->tahun }}</td>
                                    <td>
                                        @switch($periode->status)
                                            @case('Y')
                                                Aktif
                                                @break
                                            @case('T')
                                                Tidak Aktif
                                                @break
                                            @default
                                                
                                        @endswitch
                                    </td>
                                    <td>
                                        <a href="{{ route('laporan.thr.pengerjaan.periode',['kode_pengerjaan' => $kode_pengerjaan, 'periode' => $periode->tahun]) }}" class="btn btn-success">Detail</a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection