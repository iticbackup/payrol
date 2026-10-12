@extends('layouts.backend.app')
@section('title')
    Estimasi Pengerjaan Borongan
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
                <div class="card-header">
                    <div class="card-title">Buat Estimasi Borongan</div>
                </div>
                <div class="card-body">
                    <table class="table table-bordered dt-responsive nowrap mt-2"
                        style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                        <thead>
                            <tr>
                                <th colspan="11" class="text-center bg-dark text-white">Rencana Kerja Harian Produksi
                                    Packing Borongan
                                    {{ \Carbon\Carbon::create($date)->isoFormat('dddd, D MMMM YYYY') }}
                                </th>
                            </tr>
                            <tr>
                                <th class="text-center" rowspan="2">RKH</th>
                                <th class="text-center" rowspan="2">MC</th>
                                <th class="text-center" rowspan="2">POUCH</th>
                                <th class="text-center" colspan="4">RKH PRODUKSI LOKAL
                                </th>
                                <th class="text-center" colspan="2">ETIKET</th>
                                <th class="text-center" colspan="2">RKH PRODUKSI EKSPOR
                                </th>
                            </tr>
                            <tr>
                                <th class="text-center">PACKING</th>
                                <th class="text-center">BANDROL</th>
                                <th class="text-center">INNER</th>
                                <th class="text-center">OUTER</th>
                                <th class="text-center">JML</th>
                                <th class="text-center">NOMINAL</th>
                                <th class="text-center">PACKING</th>
                                <th class="text-center">KEMAS</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($estimasiRkhBorongans as $estimasiRkhBorongan)
                                <tr>
                                    <td>
                                        <select name="rkh[]" class="form-control" id="rkh">
                                            <option value="">-- Pilih RKH --</option>
                                            @foreach ($umkBoronganLokals as $umkBoronganLokal)
                                                <option value="{{ 
                                                $umkBoronganLokal->jenis_produk.'|'.$umkBoronganLokal->umk_packing.'|'.$umkBoronganLokal->umk_bandrol.'|'.$umkBoronganLokal->umk_inner.'|'.$umkBoronganLokal->umk_outer.'|'.$umkBoronganLokal->umk_packing_export.'|'.$umkBoronganLokal->umk_kemas_export
                                                 }}" {{ $umkBoronganLokal->jenis_produk == $estimasiRkhBorongan->rkh ? 'selected' : null }}>{{ $umkBoronganLokal->jenis_produk }}</option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td>
                                        <input type="text" class="form-control" placeholder="MC" value="{{ $estimasiRkhBorongan->master_case }}" id="mc">
                                    </td>
                                    <td>
                                        <input type="text" class="form-control" placeholder="Pouch" value="{{ $estimasiRkhBorongan->pouch }}" id="pouch">
                                    </td>
                                    <td>
                                        <input type="text" class="form-control" id="nominal_packing_lokal">
                                    </td>
                                    <td>
                                        <input type="text" class="form-control" id="nominal_bandrol_lokal">
                                    </td>
                                    <td>
                                        <input type="text" class="form-control" id="nominal_inner_lokal">
                                    </td>
                                    <td>
                                        <input type="text" class="form-control" id="nominal_outer_lokal">
                                    </td>
                                    <td>
                                        <input type="text" class="form-control" id="jml_etiket">
                                    </td>
                                    <td>
                                        <input type="text" class="form-control" id="nominal_etiket">
                                    </td>
                                    <td>
                                        <input type="text" class="form-control" id="nominal_packing_export">
                                    </td>
                                    <td>
                                        <input type="text" class="form-control" id="nominal_kemas_export">
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
@section('script')
    <script>
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $('#mc').on('change', function(){
            var jml_packing_lokal = ($('#mc').val()*400)*$('#rkh').val().split('|')[1];
            var jml_bandrol_lokal = ($('#mc').val()*400)*$('#rkh').val().split('|')[2];
            var jml_inner_lokal = ($('#mc').val()*400)*$('#rkh').val().split('|')[3];
            var jml_outer_lokal = ($('#mc').val()*400)*$('#rkh').val().split('|')[4];
            var jml_packing_export = ($('#mc').val()*400)*$('#rkh').val().split('|')[5];
            var jml_kemas_export = ($('#mc').val()*400)*$('#rkh').val().split('|')[6];
            
            
            $('#nominal_packing_lokal').val(jml_packing_lokal);
            $('#nominal_bandrol_lokal').val(jml_bandrol_lokal);
            $('#nominal_inner_lokal').val(jml_inner_lokal);
            $('#nominal_outer_lokal').val(jml_outer_lokal);
            $('#nominal_packing_export').val(jml_packing_export);
            $('#nominal_kemas_export').val(jml_kemas_export);
        });
        
        $('#jml_etiket').on('change', function(){
            var nominal_etiket = $('#jml_etiket').val()*{{ $bpjsKesehatan->nominal/25 }};
            $('#nominal_etiket').val(nominal_etiket);
        });
    </script>
@endsection