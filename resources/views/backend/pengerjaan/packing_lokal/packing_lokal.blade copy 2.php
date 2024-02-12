@extends('layouts.backend.app')

@section('title')
    Pengerjaan - Packing Lokal
@endsection

@section('css')
    <link href="{{ URL::asset('public/assets/plugins/datatables/dataTables.bootstrap5.min.css') }}" rel="stylesheet"
        type="text/css" />
    <link href="{{ URL::asset('public/assets/plugins/datatables/buttons.bootstrap5.min.css') }}" rel="stylesheet"
        type="text/css" />
    <link href="{{ URL::asset('public/assets/plugins/datatables/responsive.bootstrap4.min.css') }}" rel="stylesheet"
        type="text/css" />
    <link href="{{ URL::asset('public/assets/css/iziToast.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ URL::asset('public/assets/plugins/sweet-alert2/sweetalert2.min.css') }}" rel="stylesheet"
        type="text/css">
    <script src="{{ URL::asset('public/assets/js/pages/jquery.sweet-alert.init.js') }}"></script>
@endsection

@section('content')
    @component('components.breadcrumb')
    @slot('li_1')
        Data Hasil Kerja Packing Lokal
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
                <div class="card-header">
                    <h5>Kode ID : {{ $new_data_pengerjaan->kode_pengerjaan }}</h5>
                </div>
                <div class="card-body">
                    <table id="datatables" class="table table-bordered dt-responsive nowrap"
                        style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                        <thead>
                            <tr>
                                <th rowspan="2" class="text-center">No</th>
                                <th rowspan="2" class="text-center">NIK</th>
                                <th rowspan="2" class="text-center">Nama Karyawan</th>
                                <th colspan="6" class="text-center">Tanggal</th>
                                <th rowspan="2" class="text-center">Upah</th>
                                <th colspan="6" class="text-center">PLUS</th>
                                <th colspan="4" class="text-center">POTONGAN</th>
                                <th rowspan="2" class="text-center">DITERIMA</th>
                            </tr>
                            <tr>
                                @foreach ($explode_tanggal_pengerjaans as $key => $explode_tanggal_pengerjaan)
                                @if ($key != 0)
                                <th class="text-center"><a href="javascript:void()" onclick="window.open('{{ route('hasil_kerja.packingLokal.view_hasil',['id' => $id, 'kode_pengerjaan' => $kode_pengerjaan, 'tanggal' => $explode_tanggal_pengerjaan]) }}','','width=1200,height=555,left=80,top=80,location=yes, menubar=no, status=no,toolbar=no, scrollbars=yes, resizable=yes')" class="text-primary">{{ \Carbon\Carbon::parse($explode_tanggal_pengerjaan)->isoFormat('D MMMM') }}</a></th>
                                @endif
                                @endforeach
                                <th class="text-center">PLUS 1</th>
                                <th class="text-center">PLUS 2</th>
                                <th class="text-center">PLUS 3</th>
                                <th class="text-center">U Makan</th>
                                <th class="text-center">T Kerja</th>
                                <th class="text-center">Kehadiran</th>
                                <th class="text-center">MIN 1</th>
                                <th class="text-center">MIN 2</th>
                                <th class="text-center">JHT</th>
                                <th class="text-center">BPJS</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($packing_lokals as $key => $packing_lokal)
                                <?php
                                    $pengerjaans = \App\Models\Pengerjaan::where('operator_karyawan_id',$packing_lokal->id)->get();
                                    $pengerjaan_weekly = \App\Models\PengerjaanWeekly::where('operator_karyawan_id',$packing_lokal->id)->first();
                                    // dd($pengerjaans);
                                ?>
                                <tr>
                                    <td>{{ $key+1 }}</td>
                                    <td>{{ $packing_lokal->nik }}</td>
                                    <td>{{ $packing_lokal->nama }}</td>
                                    @foreach ($pengerjaans as $keys => $pengerjaan)
                                    <?php 
                                        $explode_hasil_kerja_1 = explode("|","$pengerjaan->hasil_kerja_1");
                                        $umk_borongan_lokal = \App\Models\UMKBoronganLokal::select('id','jenis_produk')->where('id',$explode_hasil_kerja_1[0])->first();
                                        if(empty($umk_borongan_lokal)){
                                            $jenis_produk = '-';
                                            $hasil_kerja_1 = null;
                                        }else{
                                            $jenis_produk = $umk_borongan_lokal->jenis_produk;
                                            $hasil_kerja_1 = $explode_hasil_kerja_1[1];
                                        }
                                    ?>
                                    <td>
                                        <span style="font-size: 8pt">{{ $pengerjaan->hasil_kerja_1 }}</span>
                                        {{-- <span style="font-size: 8pt">{{ $jenis_produk }} {{ 'Rp. '.number_format($hasil_kerja_1,0,',','.') }} - {{ $pengerjaan->hasil_kerja_2 }} - {{ $pengerjaan->hasil_kerja_3 }} - {{ $pengerjaan->hasil_kerja_4 }} - {{ $pengerjaan->hasil_kerja_5 }}</span> --}}
                                    </td>
                                    @endforeach
                                    <td>-</td>
                                    {{-- <td>Rp. {{ number_format($pengerjaan_weekly->plus_1,2,',','.') }}</td>
                                    <td>Rp. {{ number_format($pengerjaan_weekly->plus_2,2,',','.') }}</td>
                                    <td>Rp. {{ number_format($pengerjaan_weekly->plus_3,2,',','.') }}</td>
                                    <td>Rp. {{ number_format($pengerjaan_weekly->uang_makan,2,',','.') }}</td>
                                    <td>Rp. {{ number_format($pengerjaan_weekly->tunjangan_kerja,2,',','.') }}</td>
                                    <td>Rp. {{ number_format($pengerjaan_weekly->tunjangan_kehadiran,2,',','.') }}</td>
                                    <td>Rp. {{ number_format($pengerjaan_weekly->minus_1,2,',','.') }}</td>
                                    <td>Rp. {{ number_format($pengerjaan_weekly->minus_2,2,',','.') }}</td>
                                    <td>Rp. {{ number_format($pengerjaan_weekly->jht,2,',','.') }}</td>
                                    <td>Rp. {{ number_format($pengerjaan_weekly->bpjs_kesehatan,2,',','.') }}</td> --}}
                                    {{-- <td>Rp. {{ number_format($pengerjaans->sum('plus_1'),2,',','.') }}</td>
                                    <td>Rp. {{ number_format($pengerjaans->sum('plus_2'),2,',','.') }}</td>
                                    <td>Rp. {{ number_format($pengerjaans->sum('plus_3'),2,',','.') }}</td>
                                    <td>Rp. {{ number_format($pengerjaans->sum('uang_makan'),2,',','.') }}</td>
                                    <td>Rp. {{ number_format($pengerjaans->sum('tunjangan_kerja'),2,',','.') }}</td>
                                    <td>Rp. {{ number_format($pengerjaans->sum('tunjangan_kerja'),2,',','.') }}</td> --}}
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
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
    <script src="{{ URL::asset('public/assets/js/iziToast.min.js') }}"></script>

    <script>
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
    </script>
@endsection
