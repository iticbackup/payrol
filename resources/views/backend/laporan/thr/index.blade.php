@extends('layouts.backend.app')
@section('title', 'THR')

@section('content')
    @component('components.breadcrumb')
        @slot('li_1')
            Laporan THR
        @endslot
        @slot('li_3')
            @yield('title')
        @endslot
        @slot('title')
            @yield('title')
        @endslot
    @endcomponent
    <div class="row">
        @foreach ($periode_thrs as $item)
            <div class="col-md-2">
                <a href="{{ route('laporan.thr.periode',['periode' => $item->tahun]) }}" class="card">
                    <div class="text-center mt-4 mb-4">
                        <img src="{{ asset('public/assets/images/money.png') }}" class="card-img-top text-center" style="width: 100px">
                        <h5 class="card-title fs-4 fw-bold mb-2">{{ $item['tahun'] }}</h5>
                        <div><span class="fw-bold">Periode :</span> {{ $item['periode'] }}</div>
                        <div><span class="fw-bold">Cut Off :</span> {{ $item['cut_off'] }}</div>
                    </div>
                </a>
            </div>
        @endforeach
    </div>
@endsection
