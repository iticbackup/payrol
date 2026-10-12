@extends('layouts.backend.app')

@section('title')
    Estimasi Pengerjaan Borongan Packing
@endsection

@section('content')
    @component('components.breadcrumb')
        @slot('li_1')
            Estimasi
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
                    <div class="card-title mb-2">Kode Pengerjaan : {{ $kode_pengerjaan }}</div>
                    <div class="card-title mb-2">Estimasi Borongan Packing</div>
                    <div class="mb-2">
                        <a href="{{ route('estimasi.pengerjaan.borongan.index') }}" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Back</a>
                    </div>
                    @php
                        $totalEstimasiAll = [];
                    @endphp
                    @foreach ($explode_tanggal_pengerjaans as $explode_tanggal_pengerjaan)
                    <div class="card">
                        <div class="card-body">
                            @php
                                $estimasiRkhBorongans = \App\Models\EstimasiRkhBorongan::where('work_date',$explode_tanggal_pengerjaan)->get();
                                // dd($estimasiRkhBorongans);
                                $products = [
                                    [
                                        'date' => $explode_tanggal_pengerjaan,
                                        'rkh' => 'AGK',
                                        'mc' => 150,
                                        'pouch' => 400
                                    ]
                                ];

                                $totalMc = [];
                                $totalPouch = [];
                                $totalPricePackingLokal = [];
                                $totalPriceBandrolLokal = [];
                                $totalPriceInnerLokal = [];
                                $totalPriceOuterLokal = [];
                                $totalPriceEtiketLokal = [];
                                $totalPricePackingExport = [];
                                $totalPriceKemasExport = [];
                            @endphp
                            <table class="table table-bordered dt-responsive nowrap mt-2"
                                style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                                <thead>
                                    <tr>
                                        <th colspan="11" class="text-center bg-dark text-white">Rencana Kerja Harian Produksi Packing Borongan {{ \Carbon\Carbon::create($explode_tanggal_pengerjaan)->isoFormat('dddd, D MMMM YYYY') }}</th>
                                    </tr>
                                    <tr>
                                        <th class="text-center" rowspan="2" style="background-color: #FDF0D5">RKH</th>
                                        <th class="text-center" rowspan="2" style="background-color: #FDF0D5">MC</th>
                                        <th class="text-center" rowspan="2" style="background-color: #FDF0D5">POUCH</th>
                                        <th class="text-center" colspan="4" style="background-color: #FDF0D5">RKH PRODUKSI LOKAL</th>
                                        <th class="text-center" rowspan="2" style="background-color: #FDF0D5">ETIKET</th>
                                        <th class="text-center" colspan="2" style="background-color: #FDF0D5">RKH PRODUKSI EKSPOR</th>
                                    </tr>
                                    <tr>
                                        <th class="text-center" style="background-color: #FDF0D5">PACKING</th>
                                        <th class="text-center" style="background-color: #FDF0D5">BANDROL</th>
                                        <th class="text-center" style="background-color: #FDF0D5">INNER</th>
                                        <th class="text-center" style="background-color: #FDF0D5">OUTER</th>
                                        <th class="text-center" style="background-color: #FDF0D5">PACKING</th>
                                        <th class="text-center" style="background-color: #FDF0D5">KEMAS</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($estimasiRkhBorongans as $estimasiRkhBorongan)
                                    @php
                                        $pricePackingLokal = ($estimasiRkhBorongan->master_case*$estimasiRkhBorongan->pouch)*$estimasiRkhBorongan->umk_packing;
                                        $priceBandrolLokal = ($estimasiRkhBorongan->master_case*$estimasiRkhBorongan->pouch)*$estimasiRkhBorongan->umk_bandrol;
                                        $priceInnerLokal = ($estimasiRkhBorongan->master_case*$estimasiRkhBorongan->pouch)*$estimasiRkhBorongan->umk_inner;
                                        $priceOuterLokal = ($estimasiRkhBorongan->master_case*$estimasiRkhBorongan->pouch)*$estimasiRkhBorongan->umk_outer;
                                        $priceEtiketLokal = $estimasiRkhBorongan->etiket*$estimasiRkhBorongan->umk_etiket;

                                        $pricePackingExport = $estimasiRkhBorongan->etiket*$estimasiRkhBorongan->umk_export_packing;
                                        $priceKemasExport = $estimasiRkhBorongan->etiket*$estimasiRkhBorongan->umk_export_kemas;

                                        array_push($totalMc,$estimasiRkhBorongan->master_case);
                                        array_push($totalPouch,$estimasiRkhBorongan->master_case*$estimasiRkhBorongan->pouch);

                                        array_push($totalPricePackingLokal,$pricePackingLokal);
                                        array_push($totalPriceBandrolLokal,$priceBandrolLokal);
                                        array_push($totalPriceInnerLokal,$priceInnerLokal);
                                        array_push($totalPriceOuterLokal,$priceOuterLokal);
                                        array_push($totalPriceEtiketLokal,$priceEtiketLokal);
                                        array_push($totalPricePackingExport,$pricePackingExport);
                                        array_push($totalPriceKemasExport,$priceKemasExport);
                                    @endphp
                                    <tr>
                                        <td class="text-center">{{ $estimasiRkhBorongan->rkh }}</td>
                                        <td class="text-center">{{ $estimasiRkhBorongan->master_case }}</td>
                                        <td class="text-center">{{ number_format($estimasiRkhBorongan->master_case*$estimasiRkhBorongan->pouch,0,',','.') }}</td>
                                        <td class="text-end">{{ 'Rp. '.number_format($pricePackingLokal,0,',','.') }}</td>
                                        <td class="text-end">{{ 'Rp. '.number_format($priceBandrolLokal,0,',','.') }}</td>
                                        <td class="text-end">{{ 'Rp. '.number_format($priceInnerLokal,0,',','.') }}</td>
                                        <td class="text-end">{{ 'Rp. '.number_format($priceOuterLokal,0,',','.') }}</td>
                                        <td class="text-end">{{ 'Rp. '.number_format($priceEtiketLokal,0,',','.') }}</td>
                                        <td class="text-end">{{ 'Rp. '.number_format($pricePackingExport,0,',','.') }}</td>
                                        <td class="text-end">{{ 'Rp. '.number_format($priceKemasExport,0,',','.') }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                                @php
                                    $totalEstimasi = array_sum($totalPricePackingLokal)+
                                                    array_sum($totalPriceBandrolLokal)+
                                                    array_sum($totalPriceInnerLokal)+
                                                    array_sum($totalPriceOuterLokal)+
                                                    array_sum($totalPriceEtiketLokal)+
                                                    array_sum($totalPricePackingExport)+
                                                    array_sum($totalPriceKemasExport)+
                                                    array_sum($totalPriceKemasExport);

                                    array_push($totalEstimasiAll,$totalEstimasi);
                                @endphp
                                <tfoot>
                                    <tr>
                                        <td class="text-center fw-bold">Total</td>
                                        <td class="text-center fw-bold">{{ number_format(array_sum($totalMc),0,',','.') }}</td>
                                        <td class="text-center fw-bold">{{ number_format(array_sum($totalPouch),0,',','.') }}</td>
                                        <td class="text-end fw-bold">{{ 'Rp. '.number_format(array_sum($totalPricePackingLokal),0,',','.') }}</td>
                                        <td class="text-end fw-bold">{{ 'Rp. '.number_format(array_sum($totalPriceBandrolLokal),0,',','.') }}</td>
                                        <td class="text-end fw-bold">{{ 'Rp. '.number_format(array_sum($totalPriceInnerLokal),0,',','.') }}</td>
                                        <td class="text-end fw-bold">{{ 'Rp. '.number_format(array_sum($totalPriceOuterLokal),0,',','.') }}</td>
                                        <td class="text-end fw-bold">{{ 'Rp. '.number_format(array_sum($totalPriceEtiketLokal),0,',','.') }}</td>
                                        <td class="text-end fw-bold">{{ 'Rp. '.number_format(array_sum($totalPricePackingExport),0,',','.') }}</td>
                                        <td class="text-end fw-bold">{{ 'Rp. '.number_format(array_sum($totalPriceKemasExport),0,',','.') }}</td>
                                        <td class="text-end fw-bold">{{ 'Rp. '.number_format($totalEstimasi,0,',','.') }}</td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                        <div class="card-footer">
                            <a href="{{ route('estimasi.pengerjaan.borongan.packing.buat',['kode_pengerjaan' => $kode_pengerjaan, 'date' => $explode_tanggal_pengerjaan]) }}" class="btn btn-primary"><i class="fas fa-plus"></i> Tambah / Edit</a>
                        </div>
                    </div>
                    @endforeach
                    <div class="row align-items-center bg-dark mb-3">
                        <div class="col">
                            <div class="card-title mt-2 mb-2 text-white">Grand Total Estimasi</div>
                        </div>
                        <div class="col-auto">
                            <div class="fw-bold mt-2 mb-2 text-success fs-4">{{ 'Rp. '.number_format(array_sum($totalEstimasiAll),0,',','.') }}</div>
                        </div>
                    </div>
                    <div class="card">
                        <div class="card-body">
                            <table class="table table-bordered dt-responsive nowrap"
                            style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                            <thead>
                                <tr>
                                    <th colspan="6" class="text-center bg-dark text-white">Master Case (MC)</th>
                                </tr>
                                <tr>
                                    <th rowspan="2" class="text-center bg-dark text-white">No</th>
                                    <th rowspan="2" class="text-center bg-dark text-white">Item</th>
                                    <th colspan="4" class="text-center bg-dark text-white">Pouch</th>
                                </tr>
                                <tr>
                                    <th class="text-center bg-dark text-white">Packing</th>
                                    <th class="text-center bg-dark text-white">Bandrol</th>
                                    <th class="text-center bg-dark text-white">Inner</th>
                                    <th class="text-center bg-dark text-white">Outer</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($masterCases as $key => $masterCase)
                                    <tr>
                                        <td class="text-center">{{ $key+1 }}</td>
                                        <td class="text-center">{{ $masterCase->name }}</td>
                                        <td class="text-center">{{ $masterCase->pouch_packing }}</td>
                                        <td class="text-center">{{ $masterCase->pouch_bandrol }}</td>
                                        <td class="text-center">{{ $masterCase->pouch_inner }}</td>
                                        <td class="text-center">{{ $masterCase->pouch_outer }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection