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
                <form id="" action="{{ route('estimasi.pengerjaan.borongan.packing.simpan',['kode_pengerjaan' => $kode_pengerjaan, 'date' => $date]) }}" class="repeater" method="post">
                    @csrf
                    <div class="card-body">
                        {{-- <div class="card" data-repeater-list="ksi_monitoring">
                            <div class="card-header">
                                Rencana Kerja Harian Produksi Packing Borongan
                                        {{ \Carbon\Carbon::create($date)->isoFormat('dddd, D MMMM YYYY') }}
                            </div>
                            @foreach ($estimasiRkhBorongans as $estimasiRkhBorongan)
                            <div class="card-body" data-repeater-item>
                                <div class="row">
                                    <div class="col-md-1">
                                        <label for="">RKH</label>
                                        <select name="rkh[]" class="form-control rkh-select">
                                            <option value="">-- Pilih RKH --</option>
                                            @foreach ($umkBoronganLokals as $umkBoronganLokal)
                                                <!-- Gunakan data-* attribute untuk menyimpan harga UMK -->
                                                <option value="{{ $umkBoronganLokal->jenis_produk }}" 
                                                    data-packing="{{ $umkBoronganLokal->umk_packing }}"
                                                    data-bandrol="{{ $umkBoronganLokal->umk_bandrol }}"
                                                    data-inner="{{ $umkBoronganLokal->umk_inner }}"
                                                    data-outer="{{ $umkBoronganLokal->umk_outer }}"
                                                    data-packing_export="{{ $umkBoronganLokal->umk_packing_export }}"
                                                    data-kemas_export="{{ $umkBoronganLokal->umk_kemas_export }}"
                                                    data-pouch_packing="{{ $umkBoronganLokal->pouch_packing }}"
                                                    data-pouch_bandrol="{{ $umkBoronganLokal->pouch_bandrol }}"
                                                    data-pouch_inner="{{ $umkBoronganLokal->pouch_inner }}"
                                                    data-pouch_outer="{{ $umkBoronganLokal->pouch_outer }}"
                                                    {{ $umkBoronganLokal->jenis_produk == $estimasiRkhBorongan->rkh ? 'selected' : '' }}>
                                                    {{ $umkBoronganLokal->jenis_produk }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-1">
                                        <label for="">MC</label>
                                        <input type="number" class="form-control input-mc" name="mc[]" placeholder="MC" value="{{ $estimasiRkhBorongan->master_case }}">
                                    </div>
                                    <div class="col-md-1">
                                        <label for="">POUCH</label>
                                        <input type="number" class="form-control input-pouch" name="pouch[]" placeholder="Pouch" value="{{ $estimasiRkhBorongan->pouch }}" readonly>
                                    </div>
                                    <div class="col-md-1">
                                        <label for="">PACKING LOKAL</label>
                                        <input type="text" class="form-control nominal-packing-lokal" readonly>
                                    </div>
                                    <div class="col-md-1">
                                        <label for="">BANDROL LOKAL</label>
                                        <input type="text" class="form-control nominal-bandrol-lokal" readonly>
                                    </div>
                                    <div class="col-md-1">
                                        <label for="">INNER LOKAL</label>
                                        <input type="text" class="form-control nominal-inner-lokal" readonly>
                                    </div>
                                    <div class="col-md-1">
                                        <label for="">OUTER LOKAL</label>
                                        <input type="text" class="form-control nominal-outer-lokal" readonly>
                                    </div>
                                    <div class="col-md-1">
                                        <label for="">ETIKETL</label>
                                        <input type="number" class="form-control input-jml-etiket" name="jml_etiket[]">
                                        <input type="text" class="form-control nominal-etiket" readonly>
                                    </div>
                                    <div class="col-md-1">
                                        <label for="">PACKING EKSPOR</label>
                                        <input type="text" class="form-control nominal-packing-export" readonly>
                                    </div>
                                    <div class="col-md-1">
                                        <label for="">KEMAS EKSPOR</label>
                                        <input type="text" class="form-control nominal-kemas-export" readonly>
                                    </div>
                                    <div class="col-md-1">
                                        <button type="button" class="btn btn-danger" data-repeater-delete><i
                                                class="fas fa-trash"></i></button>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div> --}}
                        <table class="table table-bordered dt-responsive nowrap mt-2"
                            style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                            <thead>
                                <tr>
                                    <th colspan="12" class="text-center bg-dark text-white">
                                        Rencana Kerja Harian Produksi Packing Borongan
                                        {{ \Carbon\Carbon::create($date)->isoFormat('dddd, D MMMM YYYY') }}
                                    </th>
                                </tr>
                                <tr>
                                    <th class="text-center" rowspan="2">RKH</th>
                                    <th class="text-center" rowspan="2">MC</th>
                                    <th class="text-center" rowspan="2">POUCH</th>
                                    <th class="text-center" colspan="4">RKH PRODUKSI LOKAL</th>
                                    <th class="text-center" colspan="2">ETIKET</th>
                                    <th class="text-center" colspan="2">RKH PRODUKSI EKSPOR</th>
                                    <th class="text-center" rowspan="2">
                                        <a type="button" class="btn btn-primary addRow" data-repeater-create><i
                                                class="fas fa-plus"></i></a>
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
                            <tbody data-repeater-list="ksi_monitoring">
                                @php
                                    // dd($kode_pengerjaan);
                                    $explodeTahunLibur = explode('_',$kode_pengerjaan)[1];

                                    $tahunLibur = \Carbon\Carbon::create($explodeTahunLibur)->isoFormat('YY');
                                @endphp
                                @foreach ($estimasiRkhBorongans as $estimasiRkhBorongan)
                                    <tr data-repeater-item>
                                        <td>
                                            <!-- Gunakan class, bukan ID -->
                                            {{-- <input type="text" name="id[]" value="{{ $estimasiRkhBorongan->id }}" id=""> --}}
                                            <select name="rkh[]" class="form-control rkh-select">
                                                <option value="">-- Pilih RKH --</option>
                                                @foreach ($umkBoronganLokals as $umkBoronganLokal)
                                                    <!-- Gunakan data-* attribute untuk menyimpan harga UMK -->
                                                    {{-- @if ($umkBoronganLokal['jenis_produk'][0] == 'Libur-'.$tahunLibur)
                                                    <option value="{{ $umkBoronganLokal->jenis_produk }}"
                                                        data-packing="{{ $umkBoronganLokal->umk_packing }}"
                                                        data-bandrol="{{ $umkBoronganLokal->umk_bandrol }}"
                                                        data-inner="{{ $umkBoronganLokal->umk_inner }}"
                                                        data-outer="{{ $umkBoronganLokal->umk_outer }}"
                                                        data-packing_export="{{ $umkBoronganLokal->umk_packing }}"
                                                        data-kemas_export="{{ $umkBoronganLokal->umk_packing }}"
                                                        data-pouch_packing="{{ $umkBoronganLokal->pouch_packing }}"
                                                        data-pouch_bandrol="{{ $umkBoronganLokal->pouch_bandrol }}"
                                                        data-pouch_inner="{{ $umkBoronganLokal->pouch_inner }}"
                                                        data-pouch_outer="{{ $umkBoronganLokal->pouch_outer }}"
                                                        {{ $umkBoronganLokal->jenis_produk == $estimasiRkhBorongan->rkh ? 'selected' : '' }}>
                                                        {{ $umkBoronganLokal->jenis_produk }}
                                                    </option>
                                                    @else
                                                    @endif --}}
                                                    <option value="{{ $umkBoronganLokal->jenis_produk }}"
                                                        data-packing="{{ $umkBoronganLokal->umk_packing }}"
                                                        data-bandrol="{{ $umkBoronganLokal->umk_bandrol }}"
                                                        data-inner="{{ $umkBoronganLokal->umk_inner }}"
                                                        data-outer="{{ $umkBoronganLokal->umk_outer }}"
                                                        data-packing_export="{{ $umkBoronganLokal->umk_packing_export }}"
                                                        data-kemas_export="{{ $umkBoronganLokal->umk_kemas_export }}"
                                                        data-pouch_packing="{{ $umkBoronganLokal->pouch_packing }}"
                                                        data-pouch_bandrol="{{ $umkBoronganLokal->pouch_bandrol }}"
                                                        data-pouch_inner="{{ $umkBoronganLokal->pouch_inner }}"
                                                        data-pouch_outer="{{ $umkBoronganLokal->pouch_outer }}"
                                                        {{ $umkBoronganLokal->jenis_produk == $estimasiRkhBorongan->rkh ? 'selected' : '' }}>
                                                        {{ $umkBoronganLokal->jenis_produk }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </td>
                                        <td>
                                            <input type="number" class="form-control input-mc" name="mc[]"
                                                placeholder="MC" value="{{ $estimasiRkhBorongan->master_case }}">
                                        </td>
                                        <td>
                                            <input type="number" class="form-control input-pouch" name="pouch[]"
                                                placeholder="Pouch" value="{{ $estimasiRkhBorongan->pouch }}" readonly>
                                        </td>
                                        <td>
                                            <input type="text" class="form-control umk-packing-lokal" name="umk_packing[]" readonly>
                                            <input type="text" class="form-control nominal-packing-lokal" name="price_packing[]" readonly>
                                        </td>
                                        <td>
                                            <input type="text" class="form-control umk-bandrol-lokal" name="umk_bandrol[]" readonly>
                                            <input type="text" class="form-control nominal-bandrol-lokal" name="price_bandrol[]" readonly>
                                        </td>
                                        <td>
                                            <input type="text" class="form-control umk-inner-lokal" name="umk_inner[]" readonly>
                                            <input type="text" class="form-control nominal-inner-lokal" name="price_inner[]" readonly>
                                        </td>
                                        <td>
                                            <input type="text" class="form-control umk-outer-lokal" name="umk_outer[]" readonly>
                                            <input type="text" class="form-control nominal-outer-lokal" name="price_outer[]" readonly>
                                        </td>
                                        <td>
                                            <input type="number" class="form-control input-jml-etiket" name="jml_etiket[]" value="{{ $estimasiRkhBorongan->etiket }}">
                                        </td>
                                        <td>
                                            <input type="text" class="form-control umk-etiket" name="umk_etiket[]" readonly>
                                            <input type="text" class="form-control nominal-etiket" name="price_etiket[]" readonly>
                                        </td>
                                        <td>
                                            <input type="text" class="form-control umk-packing-export" name="umk_export_packing[]" readonly>
                                            <input type="text" class="form-control nominal-packing-export" name="price_export_packing[]" readonly>
                                        </td>
                                        <td>
                                            <input type="text" class="form-control umk-kemas-export" name="umk_export_kemas[]" readonly>
                                            <input type="text" class="form-control nominal-kemas-export" name="price_export_kemas[]" readonly>
                                        </td>
                                        <td class="text-center"><button type="button" class="btn btn-danger deleteRow"
                                                data-repeater-delete><i class="fas fa-trash"></i></button></td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="card-footer">
                        <a href="{{ route('estimasi.pengerjaan.borongan.packing',['kode_pengerjaan' => $kode_pengerjaan]) }}" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Back</a>
                        <button type="submit" class="btn btn-success">Submit</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('script')
    {{-- <script src="{{ asset('public/assets/plugins/repeater/jquery.repeater.min.js') }}"></script> --}}
    <script>
        $(document).ready(function() {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            // Tarik rate BPJS sekali saja dari PHP ke variabel JS
            const rateEtiket = {{ $bpjsKesehatan->nominal / 25 }};

            // Fungsi untuk menghitung kalkulasi per baris
            function hitungRow(tr) {
                // Ambil elemen select yang sedang aktif di baris tersebut
                let rkhSelect = tr.find('.rkh-select option:selected');

                // Ambil nilai MC (Gunakan fallback 0 jika kosong untuk cegah NaN)
                let mc = parseFloat(tr.find('.input-mc').val()) || 0;
                let mcNew = parseFloat(rkhSelect.data('pouch_packing')) || parseFloat(rkhSelect.data(
                    'pouch_bandrol')) || parseFloat(rkhSelect.data('pouch_inner')) || parseFloat(rkhSelect.data(
                    'pouch_outer')) || 0;
                // let mcPacking   = parseFloat(rkhSelect.data('mc_packing')) || 0;
                // let mcBandrol   = parseFloat(rkhSelect.data('mc_bandrol')) || 0;
                // let mcInner   = parseFloat(rkhSelect.data('mc_inner')) || 0;
                // let mcOuter   = parseFloat(rkhSelect.data('mc_outer')) || 0;

                let multiplier = mc * mcNew;

                // --- PERBAIKAN: Masukkan nilai multiplier (MC * 400) ke input Pouch ---
                tr.find('.input-pouch').val(multiplier);

                // Ambil harga-harga dari atribut data-*
                let pLokal = parseFloat(rkhSelect.data('packing')) || 0;
                let bLokal = parseFloat(rkhSelect.data('bandrol')) || 0;
                let iLokal = parseFloat(rkhSelect.data('inner')) || 0;
                let oLokal = parseFloat(rkhSelect.data('outer')) || 0;
                let pEkspor = parseFloat(rkhSelect.data('packing_export')) || 0;
                let kEkspor = parseFloat(rkhSelect.data('kemas_export')) || 0;

                // Hitung dan masukkan hasilnya ke input masing-masing di baris (tr) yang sama
                tr.find('.umk-packing-lokal').val(pLokal);
                tr.find('.umk-bandrol-lokal').val(bLokal);
                tr.find('.umk-inner-lokal').val(iLokal);
                tr.find('.umk-outer-lokal').val(oLokal);
                tr.find('.umk-etiket').val(rateEtiket);
                tr.find('.umk-packing-export').val(pEkspor);
                tr.find('.umk-kemas-export').val(kEkspor);

                tr.find('.nominal-packing-lokal').val(multiplier * pLokal);
                tr.find('.nominal-bandrol-lokal').val(multiplier * bLokal);
                tr.find('.nominal-inner-lokal').val(multiplier * iLokal);
                tr.find('.nominal-outer-lokal').val(multiplier * oLokal);
                tr.find('.nominal-packing-export').val(multiplier * pEkspor);
                tr.find('.nominal-kemas-export').val(multiplier * kEkspor);

                tr.find('.nominal-etiket').val(tr.find('.input-jml-etiket').val() * rateEtiket);
            }

            // EVENT: Ketika MC atau Pilihan RKH berubah (gunakan input agar realtime saat diketik)
            $(document).on('input change', '.input-mc, .rkh-select', function() {
                let tr = $(this).closest('tr'); // Cari baris (table row) tempat elemen ini berada
                hitungRow(tr);
            });

            // EVENT: Ketika Jumlah Etiket berubah
            $(document).on('input change', '.input-jml-etiket', function() {
                let tr = $(this).closest('tr');
                let jmlEtiket = parseFloat($(this).val()) || 0;

                tr.find('.nominal-etiket').val(jmlEtiket * rateEtiket);
            });

            // TRIGGER OTOMATIS: Saat halaman dimuat, hitung otomatis data yang sudah ada dari database
            $('.input-mc').each(function() {
                let tr = $(this).closest('tr');
                hitungRow(tr);
            });
        });
    </script>

    <script>
        $('thead').on('click','.addRow', function(){
            var tableRow = "<tr>"+
                            "<td>"+
                                "<select name='rkh[]' class='form-control rkh-select'>"+
                                "<option value=''>-- Pilih RKH --</option>"+
                                "@foreach($umkBoronganLokals as $umkBoronganLokal)"+
                                    "<option value='{{ $umkBoronganLokal->jenis_produk }}' data-packing='{{ $umkBoronganLokal->umk_packing }}' data-bandrol='{{ $umkBoronganLokal->umk_bandrol }}' data-inner='{{ $umkBoronganLokal->umk_inner }}' data-outer='{{ $umkBoronganLokal->umk_outer }}' data-packing_export='{{ $umkBoronganLokal->umk_packing_export }}' data-kemas_export='{{ $umkBoronganLokal->umk_kemas_export }}' data-pouch_packing='{{ $umkBoronganLokal->pouch_packing }}' data-pouch_bandrol='{{ $umkBoronganLokal->pouch_bandrol }}' data-pouch_inner='{{ $umkBoronganLokal->pouch_inner }}' data-pouch_outer='{{ $umkBoronganLokal->pouch_outer }}'>{{ $umkBoronganLokal->jenis_produk }}</option>"+
                                "@endforeach"+
                                "</select>"+
                            "</td>"+
                            "<td><input type='number' class='form-control input-mc' name='mc[]' placeholder='MC'></td>"+
                            "<td><input type='number' class='form-control input-pouch' name='pouch[]' placeholder='Pouch'></td>"+
                            "<td><input type='text' class='form-control umk-packing-lokal' name='umk_packing[]' readonly><input type='text' class='form-control nominal-packing-lokal' name='price_packing[]' readonly></td>"+
                            "<td><input type='text' class='form-control umk-bandrol-lokal' name='umk_bandrol[]' readonly><input type='text' class='form-control nominal-bandrol-lokal' name='price_bandrol[]' readonly></td>"+
                            "<td><input type='text' class='form-control umk-inner-lokal' name='umk_inner[]' readonly><input type='text' class='form-control nominal-inner-lokal' name='price_inner[]' readonly></td>"+
                            "<td><input type='text' class='form-control umk-outer-lokal' name='umk_outer[]' readonly><input type='text' class='form-control nominal-outer-lokal' name='price_outer[]' readonly></td>"+
                            "<td><input type='number' class='form-control input-jml-etiket' name='jml_etiket[]'></td>"+
                            "<td><input type=text class='form-control umk-etiket' name='umk_etiket[]' readonly><input type='text' class='form-control nominal-etiket' name='price_etiket[]' readonly></td>"+
                            "<td><input type='text' class='form-control umk-packing-export' name='umk_export_packing[]' readonly><input type='text' class='form-control nominal-packing-export' name='price_export_packing[]' readonly></td>"+
                            "<td><input type='text' class='form-control umk-kemas-export' name='umk_export_kemas[]' readonly><input type='text' class='form-control nominal-kemas-export' name='price_export_kemas[]' readonly></td>"+
                            "<td><button type='button' class='btn btn-danger deleteRow'><i class='fas fa-trash'></i></button></td>"+
                            "</tr>";

            $('tbody').append(tableRow);
        });

        $('tbody').on('click', '.deleteRow', function(){
            $(this).parent().parent().remove();
        });
    </script>

    <script>
        $(document).ready(function() {
            $('.repeater').repeater({
                initEmpty: false,
                defaultValues: {
                    'text-input': 'foo'
                },
                show: function() {
                    $(this).slideDown();
                    // --- PERBAIKAN: Kosongkan nilai MC dan Pouch saat baris baru ditambahkan ---
                    $(this).find('.input-mc').val('');
                    $(this).find('.input-pouch').val('');
                    // ---
                },
                hide: function(deleteElement) {
                    if (confirm('Are you sure you want to delete this element?')) {
                        $(this).slideUp(deleteElement);
                    }
                }
            })
        });
    </script>
@endsection
