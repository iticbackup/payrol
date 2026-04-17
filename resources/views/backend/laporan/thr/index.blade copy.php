@extends('layouts.backend.app')
@section('title', 'THR')

@section('content')
    @component('components.breadcrumb')
        @slot('li_1')
            Laporan
        @endslot
        @slot('li_3')
            @yield('title')
        @endslot
        @slot('title')
            @yield('title')
        @endslot
    @endcomponent
    @php
        $kategori_pengerjaans = [
            [
                'icon' => asset('public/assets/images/money.png'),
                'name' => 'Borongan',
                'link' => route('laporan.thr.pengerjaan', ['kode_pengerjaan' => 'PB']),
            ],
            [
                'icon' => asset('public/assets/images/money.png'),
                'name' => 'Harian',
                'link' => route('laporan.thr.pengerjaan', ['kode_pengerjaan' => 'PH']),
            ],
            [
                'icon' => asset('public/assets/images/money.png'),
                'name' => 'Supir RIT',
                'link' => route('laporan.thr.pengerjaan', ['kode_pengerjaan' => 'PS']),
            ],
        ];
    @endphp
    <div class="row">
        @foreach ($kategori_pengerjaans as $item)
            <div class="col-md-4">
                <a href="{{ $item['link'] }}" class="card">
                    <div class="text-center mt-4 mb-4">
                        <img src="{{ $item['icon'] }}" class="card-img-top text-center" style="width: 100px">
                        <h5 class="card-title fs-4 fw-bold">{{ $item['name'] }}</h5>
                    </div>
                </a>
            </div>
        @endforeach
    </div>
@endsection
