@extends('layouts.backend.app')

@section('title')
    Dashboard
@endsection

@section('content')
    @component('components.breadcrumb')
        @slot('li_1')
            Dashboard
        @endslot
        @slot('li_3')
            @yield('title')
        @endslot
        @slot('title')
            @yield('title')
        @endslot
    @endcomponent

    @php
        if (env('IS_PRODUCTION') == 'LIVE') {
            $color = 'success';
            $icon = 'mdi mdi-briefcase-check';
            $message_alert_title = 'PAYROL MODE LIVE';
            $message_alert = 'Aplikasi Payrol sekarang adalah mode <b>LIVE</b>. Gunakan aplikasi sebaik mungkin agar tidak ada kesalahan saat input data.';
        }elseif(env('IS_PRODUCTION') == 'TESTING'){
            $color = 'warning';
            $icon = 'mdi mdi-briefcase-search';
            $message_alert_title = 'PAYROL MODE TESTING';
            $message_alert = 'Aplikasi Payrol sekarang adalah mode <b>TESTING</b>. Silahkan input data dummy bila tidak sesuai dengan data tersebut.';
        }
    @endphp

    <div class="row">
        <div class="col-12">
            <div class="alert custom-alert custom-alert-{{ $color }} icon-custom-alert shadow-sm fade show d-flex justify-content-between" role="alert">  
                <div class="media">
                    <i class="{{ $icon }} alert-icon text-{{ $color }} align-self-center font-30 me-3"></i>
                    <div class="media-body align-self-center">
                        <h5 class="mb-1 fw-bold mt-0">{{ $message_alert_title }}</h5>
                        <span>{!! $message_alert !!}</span>
                    </div>
                </div>                                  
                {{-- <button type="button" class="btn-close align-self-center" data-bs-dismiss="alert" aria-label="Close"></button> --}}
            </div>
        </div>
        <div class="col-12 col-lg-6 col-xl-3"> 
            <div class="card">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col text-center">                                                                        
                            <span class="h4">{{ $total_all_karyawan_operator_borongan+$total_all_karyawan_operator_harian+$total_all_karyawan_rit }}</span>      
                            <h6 class="text-uppercase text-muted mt-2 m-0">Total All Karyawan Operator</h6>                
                        </div>
                    </div>
                </div>
            </div>
        </div>  
        <div class="col-12 col-lg-6 col-xl-3"> 
            <div class="card">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col text-center">                                                                        
                            <span class="h4">{{ $total_all_karyawan_operator_borongan }}</span>      
                            <h6 class="text-uppercase text-muted mt-2 m-0">Total Karyawan Borongan</h6>                
                        </div>
                    </div>
                </div>
            </div>                     
        </div>
        <div class="col-12 col-lg-6 col-xl-3"> 
            <div class="card">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col text-center">                                                                        
                            <span class="h4">{{ $total_all_karyawan_operator_harian }}</span>      
                            <h6 class="text-uppercase text-muted mt-2 m-0">Total Karyawan Harian</h6>                
                        </div>
                    </div>
                </div>
            </div>                     
        </div>
        <div class="col-12 col-lg-6 col-xl-3"> 
            <div class="card">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col text-center">                                                                        
                            <span class="h4">{{ $total_all_karyawan_rit }}</span>      
                            <h6 class="text-uppercase text-muted mt-2 m-0">Total Karyawan Supir RIT</h6>                
                        </div>
                    </div>
                </div>
            </div>                     
        </div>
        <div class="col-12 col-lg-12 col-xl-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Periode 2024</h4>
                </div>
                <div class="card-body">
                    <div id="total_gaji_karyawan_periode" class="apex-charts"></div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('script')
<script src="{{ URL::asset('public/assets/plugins/apex-charts/apexcharts.min.js') }}"></script>
<script>
    
    const rupiah = (number)=>{
        return new Intl.NumberFormat("id-ID", {
        style: "currency",
        currency: "IDR"
        }).format(number);
    }

    var options = {
        chart: {
            height: 320,
            type: 'bar',
            stacked: false,
            toolbar: {
                show: true,
                autoSelected: 'zoom'
            },
        },
        colors: ['#31511E', '#FF6600', '#6256CA'],
        dataLabels: {
            enabled: false
        },
        stroke: {
            curve: 'smooth',
            width: [1.5, 1.5],
            // dashArray: [0, 4],
            lineCap: 'round',
        },
        grid: {
            padding: {
                left: 0,
                right: 0
            },
            strokeDashArray: 3,
        },
        markers: {
            size: 0,
            hover: {
                size: 0
            }
        },
        series: [
            {
                name: 'Laporan Gaji Bulanan Borongan',
                data: @json($total_gaji_karyawan_borongan)
            },
            {
                name: 'Laporan Gaji Bulanan Harian',
                data: @json($total_gaji_karyawan_harian)
            },
            {
                name: 'Laporan Gaji Bulanan Supir RIT',
                data: @json($total_gaji_karyawan_supir_rit)
            },
        ],

        xaxis: {
            type: 'month',
            categories: ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep',
                'Okt', 'Nov', 'Des'
            ],
            axisBorder: {
                show: true,
            },
            axisTicks: {
                show: true,
            },
        },
        yaxis: {
            labels: {
                formatter: function (value) {
                return rupiah(value);
                }
            },
        },
        fill: {
            type: "gradient",
            gradient: {
                shadeIntensity: 1,
                opacityFrom: 0.4,
                opacityTo: 0.3,
                stops: [0, 90, 100]
            }
        },

        tooltip: {
            x: {
                format: 'dd/MM/yy HH:mm'
            },
        },
        legend: {
            position: 'top',
            horizontalAlign: 'right'
        },
    }

    var chart = new ApexCharts(
        document.querySelector("#total_gaji_karyawan_periode"),
        options
    );

    chart.render();
</script>
@endsection

{{-- @section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('Dashboard') }}</div>

                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif

                    {{ __('You are logged in!') }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection --}}
