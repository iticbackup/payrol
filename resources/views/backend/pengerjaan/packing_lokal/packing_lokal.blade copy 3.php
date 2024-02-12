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
                                <th rowspan="2" class="text-center">TG</th>
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
                            @foreach ($pengerjaans as $key => $pengerjaan)
                            <?php 
                                $hasil_pengerjaans = \App\Models\Pengerjaan::select([
                                                                                'hasil_kerja_1',
                                                                                'hasil_kerja_2',
                                                                                'hasil_kerja_3',
                                                                                'hasil_kerja_4',
                                                                                'hasil_kerja_5',
                                                                                'total_jam_kerja_1',
                                                                                'total_jam_kerja_2',
                                                                                'total_jam_kerja_3',
                                                                                'total_jam_kerja_4',
                                                                                'total_jam_kerja_5',
                                                                            ])
                                                                            ->where('operator_karyawan_id',$pengerjaan->operator_karyawan_id)->get();
                                                                            // dd($hasil_pengerjaans);
                            ?>
                                <tr>
                                    <td class="text-center">{{ $key+1 }}</td>
                                    <td class="text-center">{{ $pengerjaan->nik }}</td>
                                    <td>{{ $pengerjaan->nama }}</td>
                                    <?php 
                                        $upah = array();
                                    ?>
                                    @foreach ($hasil_pengerjaans as $hasil_pengerjaan)
                                    <?php 
                                        $explode_hasil_kerja_1 = explode("|",$hasil_pengerjaan->hasil_kerja_1);
                                        $umk_borongan_lokal_1 = \App\Models\UMKBoronganLokal::select('id','jenis_produk','umk_packing','umk_bandrol','umk_inner','umk_outer')->where('id',$explode_hasil_kerja_1[0])->where('status','Y')->first();
                                        // dd($umk_borongan_lokal_1);
                                        // dd($umk_borongan_lokal_1->umk_packing);
                                        // dd($explode_hasil_kerja_1[0]);
                                        if(empty($umk_borongan_lokal_1)){
                                            $jenis_produk_1 = '-';
                                            $hasil_kerja_1 = null;
                                            $data_explode_hasil_kerja_1 = '-';
                                        }else{
                                            $jenis_produk_1 = $umk_borongan_lokal_1->jenis_produk;
                                            $hasil_kerja_1 = $explode_hasil_kerja_1[1]*$umk_borongan_lokal_1->umk_packing;
                                            $data_explode_hasil_kerja_1 = $explode_hasil_kerja_1[1];
                                            // dd($umk_borongan_lokal_1->umk_packing);
                                        }

                                        $explode_hasil_kerja_2 = explode("|",$hasil_pengerjaan->hasil_kerja_2);
                                        $umk_borongan_lokal_2 = \App\Models\UMKBoronganLokal::select('id','jenis_produk','umk_packing','umk_bandrol','umk_inner','umk_outer')->where('id',$explode_hasil_kerja_2[0])->where('status','Y')->first();
                                        if(empty($umk_borongan_lokal_2)){
                                            $jenis_produk_2 = '-';
                                            $hasil_kerja_2 = null;
                                            $data_explode_hasil_kerja_2 = '-';
                                        }else{
                                            $jenis_produk_2 = $umk_borongan_lokal_2->jenis_produk;
                                            $hasil_kerja_2 = $explode_hasil_kerja_2[1]*$umk_borongan_lokal_2->umk_packing;
                                            $data_explode_hasil_kerja_2 = $explode_hasil_kerja_2[1];
                                        }

                                        $explode_hasil_kerja_3 = explode("|",$hasil_pengerjaan->hasil_kerja_3);
                                        $umk_borongan_lokal_3 = \App\Models\UMKBoronganLokal::select('id','jenis_produk','umk_packing','umk_bandrol','umk_inner','umk_outer')->where('id',$explode_hasil_kerja_3[0])->where('status','Y')->first();
                                        if(empty($umk_borongan_lokal_3)){
                                            $jenis_produk_3 = '-';
                                            $hasil_kerja_3 = null;
                                            $data_explode_hasil_kerja_3 = '-';
                                        }else{
                                            $jenis_produk_3 = $umk_borongan_lokal_3->jenis_produk;
                                            $hasil_kerja_3 = $explode_hasil_kerja_3[1]*$umk_borongan_lokal_3->umk_packing;
                                            $data_explode_hasil_kerja_3 = $explode_hasil_kerja_3[1];
                                        }

                                        $explode_hasil_kerja_4 = explode("|",$hasil_pengerjaan->hasil_kerja_4);
                                        $umk_borongan_lokal_4 = \App\Models\UMKBoronganLokal::select('id','jenis_produk','umk_packing','umk_bandrol','umk_inner','umk_outer')->where('id',$explode_hasil_kerja_4[0])->where('status','Y')->first();
                                        if(empty($umk_borongan_lokal_4)){
                                            $jenis_produk_4 = '-';
                                            $hasil_kerja_4 = null;
                                            $data_explode_hasil_kerja_4 = '-';
                                        }else{
                                            $jenis_produk_4 = $umk_borongan_lokal_4->jenis_produk;
                                            $hasil_kerja_4 = $explode_hasil_kerja_4[1]*$umk_borongan_lokal_4->umk_packing;
                                            $data_explode_hasil_kerja_4 = $explode_hasil_kerja_4[1];
                                        }

                                        $explode_hasil_kerja_5 = explode("|",$hasil_pengerjaan->hasil_kerja_5);
                                        $umk_borongan_lokal_5 = \App\Models\UMKBoronganLokal::select('id','jenis_produk','umk_packing','umk_bandrol','umk_inner','umk_outer')->where('id',$explode_hasil_kerja_5[0])->where('status','Y')->first();
                                        if(empty($umk_borongan_lokal_5)){
                                            $jenis_produk_5 = '-';
                                            $hasil_kerja_5 = null;
                                            $data_explode_hasil_kerja_5 = '-';
                                        }else{
                                            $jenis_produk_5 = $umk_borongan_lokal_5->jenis_produk;
                                            $hasil_kerja_5 = $explode_hasil_kerja_5[1]*$umk_borongan_lokal_5->umk_packing;
                                            $data_explode_hasil_kerja_5 = $explode_hasil_kerja_5[1];
                                        }

                                        $hasil_upah = $hasil_kerja_1+$hasil_kerja_2+$hasil_kerja_3+$hasil_kerja_4+$hasil_kerja_5;
                                        // dd($hasil_upah);
                                        array_push($upah,$hasil_upah);
                                        // dd($upah);
                                    ?>
                                        <td>
                                            <span style="font-size: 8pt" class="text-danger">{{ $jenis_produk_1 }}</span> - <span style="font-size: 8pt" class="text-primary">{{ $data_explode_hasil_kerja_1 }}</span> |
                                            <span style="font-size: 8pt" class="text-danger">{{ $jenis_produk_2 }}</span> - <span style="font-size: 8pt" class="text-primary">{{ $data_explode_hasil_kerja_2 }}</span> |
                                            <span style="font-size: 8pt" class="text-danger">{{ $jenis_produk_3 }}</span> - <span style="font-size: 8pt" class="text-primary">{{ $data_explode_hasil_kerja_3 }}</span> |
                                            <span style="font-size: 8pt" class="text-danger">{{ $jenis_produk_4 }}</span> - <span style="font-size: 8pt" class="text-primary">{{ $data_explode_hasil_kerja_4 }}</span> |
                                            <span style="font-size: 8pt" class="text-danger">{{ $jenis_produk_5 }}</span> - <span style="font-size: 8pt" class="text-primary">{{ $data_explode_hasil_kerja_5 }}</span>
                                        </td>
                                    @endforeach
                                    <?php 
                                        $total_upah = array_sum($upah);
                                    ?>
                                    <td style="text-align: right">{{ number_format($total_upah,0,',','.') }}</td>
                                    <td style="text-align: right">{{ number_format($pengerjaan->plus_1,0,',','.') }}</td>
                                    <td style="text-align: right">{{ number_format($pengerjaan->plus_2,0,',','.') }}</td>
                                    <td style="text-align: right">{{ number_format($pengerjaan->plus_3,0,',','.') }}</td>
                                    <td style="text-align: right">{{ number_format($pengerjaan->uang_makan,0,',','.') }}</td>
                                    <td style="text-align: right">{{ number_format($pengerjaan->tunjangan_kerja,0,',','.') }}</td>
                                    <td style="text-align: right">{{ number_format($pengerjaan->tunjangan_kehadiran,0,',','.') }}</td>
                                    <?php 
                                        $total_hasil_tg = $total_upah+$pengerjaan->plus_1+$pengerjaan->plus_2+$pengerjaan->plus_3+$pengerjaan->uang_makan+$pengerjaan->tunjangan_kerja+$pengerjaan->tunjangan_kehadiran;
                                    ?>
                                    <td style="text-align: right">{{ number_format($total_hasil_tg,0,',','.') }}</td>
                                    <?php 
                                        $total_gaji_diterima = $total_hasil_tg-($pengerjaan->minus_1+$pengerjaan->minus_2+$pengerjaan->jht+$pengerjaan->bpjs_kesehatan);
                                    ?>
                                    <td style="text-align: right">{{ number_format($pengerjaan->minus_1,0,',','.') }}</td>
                                    <td style="text-align: right">{{ number_format($pengerjaan->minus_2,0,',','.') }}</td>
                                    <td style="text-align: right">{{ number_format($pengerjaan->jht,0,',','.') }}</td>
                                    <td style="text-align: right">{{ number_format($pengerjaan->bpjs_kesehatan,0,',','.') }}</td>
                                    <td style="text-align: right">{{ number_format($total_gaji_diterima,0,',','.') }}</td>
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
