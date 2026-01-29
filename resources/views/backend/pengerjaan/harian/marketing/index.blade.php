@extends('layouts.backend.app')
@section('title')
    Pengerjaan - Harian {{ $jenis_operator_detail_pekerjaan->jenis_posisi_pekerjaan }}
@endsection
@section('css')
    <style>
        @media (max-width: 1518px) {

            .table-container { 
                /* width: 100% !important; */
                overflow-x: scroll; 
                width: 90%;
            }

            /* .layouts {
                margin-left: 26%; 
                margin-right: 9%
            } */

            /* th, td {min-width: 200px; } */
        }
    </style>
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
                <div class="card-header">
                    <h5>Kode ID : {{ $new_data_pengerjaan->kode_pengerjaan }}
                        @if ($new_data_pengerjaan->status == 'n')
                        <i class="far fa-check-circle text-success"></i>
                        @endif
                    </h5>
                    @if ($new_data_pengerjaan->status == 'y')
                    <a href="{{ route('hasil_kerja.marketing.tambah_karyawan',['id' => $jenis_operator_detail_pekerjaan->jenis_operator_detail_id, 'kode_pengerjaan' => $new_data_pengerjaan->kode_pengerjaan ]) }}" class="btn btn-primary">Tambah Karyawan</a>
                    @endif
                </div>
                <div class="card-body">
                    <div class="table-container">
                        <table class="table table-sm table-bordered dt-responsive nowrap"
                            style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                            @php
                                $exp_tanggal = array_filter(explode("#",$new_data_pengerjaan['tanggal']));
                                // dd($exp_tanggal);
                                $a = count($exp_tanggal);
                                // var_dump($a);
                                $exp_tgl_awal = explode("-",$exp_tanggal[1]);
                                $exp_tgl_akhir = explode("-",$exp_tanggal[$a]);
                                $explode_posting = explode("-",$new_data_pengerjaan['date']);
                                // dd($exp_tgl_awal[2]);
    
                                if ($new_data_pengerjaan['akhir_bulan'] == 'y') {
                                    $merger_plus="colspan=7";
                                    $merger_minus="colspan=4";
                                }else{
                                    $merger_plus="colspan=5";
                                    $merger_minus="colspan=4";
                                }
    
                                if ($exp_tgl_awal[2]<10) {
                                    $bulan_explode=$exp_tgl_awal[1];
                                }else{
                                    $bulan_explode=$exp_tgl_awal[1];
                                }
    
                                if ($bulan_explode==$explode_posting[1]) {
                                    $get_bulan=$bulan_explode;
                                    $get_tahun=$explode_posting[0];
                                }
                                else{
                                    if ($explode_posting[1]=="01"){
                                        $get_bulan="12";
                                        $get_tahun=$explode_posting[0]-1;			
                                    }
                                    else{
                                        $get_bulan=$bulan_explode;
                                        $get_tahun=$explode_posting[0];
                                    }
                                }
                                
                                $total_all_upah_dasar = [];
                                $total_all_plus_1 = [];
                                $total_all_plus_2 = [];
                                $total_all_plus_3 = [];
                                $total_all_uang_makan = [];
                                $total_all_lembur = [];
                                $total_all_tj_kerja = [];
                                $total_all_tj_kehadiran = [];
                                $total_all_gaji = [];
                                $total_all_minus_1 = [];
                                $total_all_minus_2 = [];
                                $total_all_jht = [];
                                $total_all_bpjs_kesehatan = [];
                                $total_all_diterima = [];
                            @endphp
                            <thead>
                                <tr>
                                    <th rowspan="2" class="text-center">No</th>
                                    <th rowspan="2" class="text-center">NIK</th>
                                    <th rowspan="2" class="text-center">Nama Karyawan</th>
                                    <th colspan="{{ $a }}" class="text-center">Tanggal</th>
                                    <th rowspan="2" class="text-center">Total Upah Dasar</th>
                                    <th {{ $merger_plus }} class="text-center">PLUS</th>
                                    <th rowspan="2" class="text-center">Total Gaji</th>
                                    <th {{ $merger_minus }} class="text-center">POTONGAN</th>
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
                                    @if ($new_data_pengerjaan['akhir_bulan'] == 'y')
                                    <th class="text-center">Tunj. Kerja</th>
                                    <th class="text-center">Kehadiran</th>
                                    @endif
                                    <th class="text-center">Minus 1</th>
                                    <th class="text-center">Minus 2</th>
                                    <th class="text-center">BPJS Ketenagakerjaan</th>
                                    <th class="text-center">BPJS Kesehatan</th>
                                </tr>
                            </thead>
                            <tbody id="table">
                                @php
                                    $operator_karyawans = [];
                                @endphp
                                @foreach ($pengerjaan_harians as $key => $pengerjaan_harian)
                                @php
                                    array_push($operator_karyawans,$pengerjaan_harian->operator_harian_karyawan_id);
                                    $explode_hasil_kerjas = explode("|",$pengerjaan_harian->hasil_kerja);
                                @endphp
                                <tr>
                                    <td class="text-center">{{ $key+1 }}</td>
                                    <td class="text-center">{{ $pengerjaan_harian->nik }}</td>
                                    @if ($new_data_pengerjaan->status == 'n')
                                    <td>{{ $pengerjaan_harian->nama }}</td>
                                    @else
                                    <td><a href="javascript:void(0)" onclick="window.open('{{ route('hasil_kerja.marketing.view',['id' => $id, 'kode_pengerjaan' => $kode_pengerjaan, 'nik' => $pengerjaan_harian->nik, 'month' => $get_bulan, 'year' => $get_tahun]) }}','','width=1200,height=555,left=80,top=80,location=yes, menubar=no, status=no,toolbar=no, scrollbars=yes, resizable=yes')" class="text-primary">{{ $pengerjaan_harian->nama }}</a></td>
                                    @endif
                                    @foreach ($explode_hasil_kerjas as $keys => $explode_hasil_kerja)
                                    @if (empty($pengerjaan_harian->hasil_kerja))
                                        @foreach ($explode_tanggal_pengerjaans as $keys => $explode_tanggal_pengerjaan)
                                            @if ($keys != 0)
                                            <td class="text-center">-</td>
                                            @endif
                                        @endforeach
                                    @else
                                        @if ($keys != $a)
                                        <td class="text-center">{{ $explode_hasil_kerja }}</td>
                                        @endif
                                    @endif
                                    @endforeach
                                    {{-- @foreach ($explode_tanggal_pengerjaans as $keys => $explode_tanggal_pengerjaan)
                                        <td class="text-center">-</td>
                                    @endforeach --}}
                                    <td style="text-align: right">Rp. {{ number_format($pengerjaan_harian->upah_dasar_weekly,0,',','.') }}</td>
                                    @php
                                        if(empty($pengerjaan_harian->plus_1)){
                                            $hasil_plus_1 = 0;
                                        }else{
                                            $explode_plus_1 = explode("|",$pengerjaan_harian->plus_1);
                                            if ($explode_plus_1[0] == "") {
                                                $hasil_plus_1 = 0;
                                            }else{
                                                $hasil_plus_1 = $explode_plus_1[0];
                                            }
                                        }
    
                                        if(empty($pengerjaan_harian->plus_2)){
                                            $hasil_plus_2 = 0;
                                        }else{
                                            $explode_plus_2 = explode("|",$pengerjaan_harian->plus_2);
                                            if ($explode_plus_2[0] == "") {
                                                $hasil_plus_2 = 0;
                                            }else{
                                                $hasil_plus_2 = $explode_plus_2[0];
                                            }
                                        }
    
                                        if(empty($pengerjaan_harian->plus_3)){
                                            $hasil_plus_3 = 0;
                                        }else{
                                            $explode_plus_3 = explode("|",$pengerjaan_harian->plus_3);
                                            if ($explode_plus_3[0] == "") {
                                                $hasil_plus_3 = 0;
                                            }else{
                                                $hasil_plus_3 = $explode_plus_3[0];
                                            }
                                        }
    
                                        if (empty($pengerjaan_harian->uang_makan)) {
                                            $hasil_uang_makan = 0;
                                        }else{
                                            $hasil_uang_makan = $pengerjaan_harian->uang_makan;
                                        }
    
                                        if (empty($pengerjaan_harian->lembur)) {
                                            $hasil_lembur = 0;
                                        }else{
                                            $explode_lembur = explode("|",$pengerjaan_harian->lembur);
                                            // dd($explode_lembur);
                                            $hasil_lembur = $explode_lembur[0];
                                        }
    
                                        if (empty($pengerjaan_harian->tunjangan_kerja)) {
                                            $hasil_tunjangan_kerja = 0;
                                        }else{
                                            $hasil_tunjangan_kerja = $pengerjaan_harian->tunjangan_kerja;
                                        }
    
                                        if (empty($pengerjaan_harian->tunjangan_kehadiran)) {
                                            $hasil_tunjangan_kehadiran = 0;
                                        }else{
                                            $hasil_tunjangan_kehadiran = $pengerjaan_harian->tunjangan_kehadiran;
                                        }
    
                                        $total_gaji = $pengerjaan_harian->upah_dasar_weekly+$hasil_plus_1+$hasil_plus_2+$hasil_plus_3+$hasil_uang_makan+$hasil_lembur+$hasil_tunjangan_kerja+$hasil_tunjangan_kehadiran;
    
                                        if(empty($pengerjaan_harian->minus_1)){
                                            $hasil_minus_1 = 0;
                                        }else{
                                            $explode_minus_1 = explode("|",$pengerjaan_harian->minus_1);
                                            if ($explode_minus_1[0] == "") {
                                                $hasil_minus_1 = 0;
                                            }else{
                                                $hasil_minus_1 = $explode_minus_1[0];
                                            }
                                        }
    
                                        if(empty($pengerjaan_harian->minus_2)){
                                            $hasil_minus_2 = 0;
                                        }else{
                                            $explode_minus_2 = explode("|",$pengerjaan_harian->minus_2);
                                            if ($explode_minus_2[0] == "") {
                                                $hasil_minus_2 = 0;
                                            }else{
                                                $hasil_minus_2 = $explode_minus_2[0];
                                            }
                                        }
    
                                        $total_potongan = $hasil_minus_1+$hasil_minus_2+$pengerjaan_harian->jht+$pengerjaan_harian->bpjs_kesehatan;
    
                                        $total_gaji_diterima = $total_gaji-$total_potongan;

                                        array_push($total_all_upah_dasar,$pengerjaan_harian->upah_dasar_weekly);
                                        array_push($total_all_plus_1,$hasil_plus_1);
                                        array_push($total_all_plus_2,$hasil_plus_2);
                                        array_push($total_all_plus_3,$hasil_plus_3);

                                        array_push($total_all_uang_makan,$hasil_uang_makan);
                                        array_push($total_all_lembur,$hasil_lembur);
                                        array_push($total_all_tj_kerja,$hasil_tunjangan_kerja);
                                        array_push($total_all_tj_kehadiran,$hasil_tunjangan_kehadiran);
                                        array_push($total_all_gaji,$total_gaji);

                                        array_push($total_all_minus_1,$hasil_minus_1);
                                        array_push($total_all_minus_2,$hasil_minus_2);
                                        array_push($total_all_jht,$pengerjaan_harian->jht);
                                        array_push($total_all_bpjs_kesehatan,$pengerjaan_harian->bpjs_kesehatan);
                                        array_push($total_all_diterima,$total_gaji_diterima);
                                    @endphp
                                    <td style="text-align: right;">Rp. {{ number_format($hasil_plus_1,0,',','.') }}</td>
                                    <td style="text-align: right;">Rp. {{ number_format($hasil_plus_2,0,',','.') }}</td>
                                    <td style="text-align: right;">Rp. {{ number_format($hasil_plus_3,0,',','.') }}</td>
                                    <td style="text-align: right;">Rp. {{ number_format($hasil_uang_makan,0,',','.') }}</td>
                                    <td style="text-align: right;">Rp. {{ number_format($hasil_lembur,0,',','.') }}</td>
                                    @if ($new_data_pengerjaan['akhir_bulan'] == 'y')
                                    <td style="text-align: right;">Rp. {{ number_format($hasil_tunjangan_kerja,0,',','.') }}</td>
                                    <td style="text-align: right;">Rp. {{ number_format($hasil_tunjangan_kehadiran,0,',','.') }}</td>
                                    @endif
                                    <td style="text-align: right;">Rp. {{ number_format($total_gaji,0,',','.') }}</td>
                                    <td style="text-align: right;">Rp. {{ number_format($hasil_minus_1,0,',','.') }}</td>
                                    <td style="text-align: right;">Rp. {{ number_format($hasil_minus_2,0,',','.') }}</td>
                                    <td style="text-align: right;">Rp. {{ number_format($pengerjaan_harian->jht,0,',','.') }}</td>
                                    <td style="text-align: right;">Rp. {{ number_format($pengerjaan_harian->bpjs_kesehatan,0,',','.') }}</td>
                                    <td style="text-align: right;">Rp. {{ number_format($total_gaji_diterima,0,',','.') }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="3" style="text-align: center; font-weight: bold">TOTAL</td>
                                    @for ($i = 0; $i < $a; $i++)
                                    @php
                                        $total_hari_jam_kerja = [];
                                        $pengerjaan_harians = \App\Models\PengerjaanHarian::whereIn('operator_harian_karyawan_id',$operator_karyawans)
                                                                                            ->where('kode_pengerjaan',$kode_pengerjaan)
                                                                                            ->get();
                                        foreach ($pengerjaan_harians as $key => $pengerjaan_harian) {
                                            if (!empty($pengerjaan_harian->hasil_kerja)) {
                                                $explode_hasil_kerja = explode("|",$pengerjaan_harian->hasil_kerja);
                                                $hasil_kerja = array_push($total_hari_jam_kerja,$explode_hasil_kerja[$i]);
                                            }
                                            // else{

                                            // }
                                        }
                                                                                            // dd($pengerjaan_harians);
                                    @endphp
                                    <td class="text-center" style="font-weight: bold" id="">{{ array_sum($total_hari_jam_kerja) }}</td>
                                    @endfor
                                    {{-- @foreach ($hasil_harian_tanggal_pengerjaan as $key => $htp)
                                    @php
                                        $total_hari_jam_kerja = [];
                                        $pengerjaan_harians = \App\Models\PengerjaanHarian::whereIn('operator_harian_karyawan_id',$operator_karyawans)
                                                                                            ->where('kode_pengerjaan',$kode_pengerjaan)
                                                                                            ->get();
                                        foreach ($pengerjaan_harians as $key => $pengerjaan_harian) {
                                            $explode_hasil_kerja = explode("|",$pengerjaan_harian->hasil_kerja);
                                            $hasil_kerja = array_push($total_hari_jam_kerja,$explode_hasil_kerja[$key]);
                                        }
                                                                                            // dd($pengerjaan_harians);
                                    @endphp
                                    @if ($key != 0)
                                    @endif
                                    <td class="text-center" style="font-weight: bold" id="">{{ array_sum($total_hari_jam_kerja) }}</td>
                                    @endforeach --}}
    
                                    {{-- <td id="val"></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td> --}}
    
                                    <td style="text-align: right; font-weight: bold">Rp. {{ number_format(array_sum($total_all_upah_dasar),0,',','.') }}</td>
                                    <td style="text-align: right; font-weight: bold">Rp. {{ number_format(array_sum($total_all_plus_1),0,',','.') }}</td>
                                    <td style="text-align: right; font-weight: bold">Rp. {{ number_format(array_sum($total_all_plus_2),0,',','.') }}</td>
                                    <td style="text-align: right; font-weight: bold">Rp. {{ number_format(array_sum($total_all_plus_3),0,',','.') }}</td>
                                    <td style="text-align: right; font-weight: bold">Rp. {{ number_format(array_sum($total_all_uang_makan),0,',','.') }}</td>
                                    <td style="text-align: right; font-weight: bold">Rp. {{ number_format(array_sum($total_all_lembur),0,',','.') }}</td>
                                    @if ($new_data_pengerjaan['akhir_bulan'] == 'y')
                                    <td style="text-align: right; font-weight: bold">Rp. {{ number_format(array_sum($total_all_tj_kerja),0,',','.') }}</td>
                                    <td style="text-align: right; font-weight: bold">Rp. {{ number_format(array_sum($total_all_tj_kehadiran),0,',','.') }}</td>
                                    @endif
                                    <td style="text-align: right; font-weight: bold">Rp. {{ number_format(array_sum($total_all_gaji),0,',','.') }}</td>
                                    <td style="text-align: right; font-weight: bold">Rp. {{ number_format(array_sum($total_all_minus_1),0,',','.') }}</td>
                                    <td style="text-align: right; font-weight: bold">Rp. {{ number_format(array_sum($total_all_minus_2),0,',','.') }}</td>
                                    <td style="text-align: right; font-weight: bold">Rp. {{ number_format(array_sum($total_all_jht),0,',','.') }}</td>
                                    <td style="text-align: right; font-weight: bold">Rp. {{ number_format(array_sum($total_all_bpjs_kesehatan),0,',','.') }}</td>
                                    <td style="text-align: right; font-weight: bold">Rp. {{ number_format(array_sum($total_all_diterima),0,',','.') }}</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @php
    // array_push($total_all_hasil_kerja,array_sum($all_hasil_kerja));
        // dd($total_all_hasil_kerja);
    @endphp
@endsection

@section('script')
<script>
            
    var table = document.getElementById("table");
    // var sumVal = 0;
    var sumVal1 = 0;
    var sumVal2 = 0;
    var sumVal3 = 0;
    var sumVal4 = 0;
    var sumVal5 = 0;
    var sumVal6 = 0;
    var sumVal7 = 0;

    // console.log(table.rows[1].cells[3]);
    
    for(var i = 0; i < table.rows.length; i++)
    {
        // sumVal1 = sumVal1 + parseInt(table.rows[i].cells[3].innerHTML);
        sumVal1 = sumVal1 + parseInt(table.rows[i].cells[3].innerHTML);
        sumVal2 = sumVal2 + parseInt(table.rows[i].cells[4].innerHTML);
        sumVal3 = sumVal3 + parseInt(table.rows[i].cells[5].innerHTML);
        sumVal4 = sumVal4 + parseInt(table.rows[i].cells[6].innerHTML);
        sumVal5 = sumVal5 + parseInt(table.rows[i].cells[7].innerHTML);
        sumVal6 = sumVal6 + parseInt(table.rows[i].cells[8].innerHTML);
        sumVal7 = sumVal7 + parseInt(table.rows[i].cells[9].innerHTML);

        // sumVal2 = sumVal2 + parseInt(table.rows[i].cells[3].innerHTML);
        document.getElementById('val1').innerHTML = sumVal1;
        document.getElementById('val2').innerHTML = sumVal2;
        document.getElementById('val3').innerHTML = sumVal3;
        document.getElementById('val4').innerHTML = sumVal4;
        document.getElementById('val5').innerHTML = sumVal5;
        document.getElementById('val6').innerHTML = sumVal6;
        document.getElementById('val7').innerHTML = sumVal7;
    }
    
    // for (var j = 1; j <= {{ $a }}; j++) {
    //     var sumValNew = 'val'+j;
    //     document.getElementById(sumValNew).innerHTML = sumVal;
    //     // console.log(sumValNew);
    // }

    // console.log({{ $a }});
    // document.getElementById("val2").innerHTML = "Sum Value = " + sumVal2;
    // console.log(sumVal);
    
</script>
@endsection