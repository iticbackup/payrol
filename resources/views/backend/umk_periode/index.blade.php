@extends('layouts.backend.app')
@section('title')
    UMK Periode
@endsection

@section('css')
    <link href="{{ URL::asset('public/assets/plugins/datatables/dataTables.bootstrap5.min.css') }}" rel="stylesheet"
        type="text/css" />
    <link href="{{ URL::asset('public/assets/plugins/datatables/buttons.bootstrap5.min.css') }}" rel="stylesheet"
        type="text/css" />
    <link href="{{ URL::asset('public/assets/plugins/datatables/responsive.bootstrap4.min.css') }}" rel="stylesheet"
        type="text/css" />
    <link href="{{ URL::asset('public/assets/css/iziToast.css') }}" rel="stylesheet" type="text/css" />
@endsection

@section('content')
    @component('components.breadcrumb')
        @slot('li_1')
            UMK
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
                    @if ($message = Session::get('success'))
                    <div class="alert alert-success alert-block">
                        {{-- <button type="button" class="close" data-dismiss="alert">×</button>	 --}}
                        <strong>{{ $message }}</strong>
                    </div>
                    @endif

                    @if ($message = Session::get('error'))
                    <div class="alert alert-danger alert-block">
                        {{-- <button type="button" class="close" data-dismiss="alert">×</button>	 --}}
                        <strong>{{ $message }}</strong>
                    </div>
                    @endif

                    @if ($message = Session::get('warning'))
                    <div class="alert alert-warning alert-block">
                        {{-- <button type="button" class="close" data-dismiss="alert">×</button>	 --}}
                        <strong>{{ $message }}</strong>
                    </div>
                    @endif

                    @if ($message = Session::get('info'))
                    <div class="alert alert-info alert-block">
                        {{-- <button type="button" class="close" data-dismiss="alert">×</button>	 --}}
                        <strong>{{ $message }}</strong>
                    </div>
                    @endif

                    @if ($errors->any())
                    <div class="alert alert-danger">
                        {{-- <button type="button" class="close" data-dismiss="alert">×</button>	 --}}
                        Please check the form below for errors
                    </div>
                    @endif

                    <div class="mb-3">
                        <div class="card-title">UMK Borongan</div>
                        <hr>
                        <div class="accordion accordion-flush" id="accordionFlushExample">
                            <div class="row">
                                <div class="col-md-4">
                                    @for ($i = 2023; $i <= $tahun_berjalan; $i++)
                                    @php
                                        $umk_borongan_lokals = \App\Models\UMKBoronganLokal::where('tahun_aktif',$i)->get();                                    // dd($umk_borongan_ekspors);
                                    @endphp
                                    <div class="accordion-item">
                                        <h5 class="accordion-header m-0" id="flush-borongan-lokal-heading{{ $i }}">
                                            <button class="accordion-button collapsed fw-semibold" type="button" data-bs-toggle="collapse" data-bs-target="#flush-borongan-lokal-collapse{{ $i }}" aria-expanded="false" aria-controls="flush-borongan-lokal-collapse{{ $i }}">
                                                Borongan Lokal - Tahun {{ $i }} &nbsp; 
                                                @if ($i == \Carbon\Carbon::now()->format('Y'))
                                                <span class="badge bg-warning text-dark">Sedang Berjalan</span>
                                                @elseif($i <= \Carbon\Carbon::now()->format('Y'))
                                                <span class="badge bg-success text-dark"><i class="fas fa-check"></i> Selesai</span>
                                                @else
                                                <span class="badge bg-primary">Belum Tersedia</span>
                                                @endif
                                            </button>
                                        </h5>
                                        <div id="flush-borongan-lokal-collapse{{ $i }}" class="accordion-collapse collapse" aria-labelledby="flush-borongan-lokal-heading{{ $i }}" data-bs-parent="#accordionFlushExample">
                                            <div class="accordion-body">
                                                <form action="{{ route('umk_periode.lokal_umk_periode_simpan',['tahun_aktif' => $i]) }}" method="post" enctype="multipart/form-data">
                                                    @csrf
                                                <table id="datatables" class="table table-bordered dt-responsive nowrap"
                                                    style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                                                    <thead>
                                                        <tr>
                                                            <th class="text-center">Jenis Produk</th>
                                                            <th class="text-center">UMK Packing</th>
                                                            <th class="text-center">UMK Bandrol</th>
                                                            <th class="text-center">UMK Inner</th>
                                                            <th class="text-center">UMK Outer</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @php
                                                            $tanggal_berjalan = \Carbon\Carbon::now()->format('Y-m');
                                                        @endphp
                                                        @if ($umk_borongan_lokals->isEmpty())
                                                        @php
                                                            $umk_borongan_lokal_periodes = \App\Models\UMKBoronganLokal::where('tahun_aktif',$i-1)->get();
                                                        @endphp
    
                                                        @if ($tanggal_berjalan == $i.'-'.'01')
                                                        <div class="mb-3">
                                                            <div class="alert alert-danger border-0">UMK Borongan Lokal Diinput mulai bulan {{ \Carbon\Carbon::create($i,01)->isoFormat('MMMM YYYY') }}, untuk bulan selanjutnya akan ditutup inputan secara otomatis oleh sistem</div>
                                                        </div>
                                                        
                                                        @foreach ($umk_borongan_lokal_periodes as $umk_borongan_lokal)
                                                            <tr>
                                                                <td class="text-center">{{ $umk_borongan_lokal['jenis_produk'] }}
                                                                    <input type="hidden" name="tahun_aktif[]" class="form-control" value="{{ $i }}">
                                                                    <input type="hidden" name="jenis_produk[]" class="form-control" value="{{ $umk_borongan_lokal['jenis_produk'] }}">
                                                                </td>
                                                                <td><input type="text" name="umk_packing[]" class="form-control" placeholder="UMK Packing"></td>
                                                                <td><input type="text" name="umk_bandrol[]" class="form-control" placeholder="UMK Bandrol"></td>
                                                                <td><input type="text" name="umk_inner[]" class="form-control" placeholder="UMK Inner"></td>
                                                                <td><input type="text" name="umk_outer[]" class="form-control" placeholder="UMK Outer"></td>
                                                            </tr>
                                                        @endforeach
                                                            <tr>
                                                                <td colspan="5"><button type="submit" class="btn btn-primary">Submit</button></td>
                                                            </tr>
                                                        @endif
    
                                                        @else
                                                        @foreach ($umk_borongan_lokals as $umk_borongan_lokal)
                                                        <tr>
                                                            <td class="text-center">{{ $umk_borongan_lokal->jenis_produk }}</td>
                                                            <td class="text-center">{{ $umk_borongan_lokal->umk_packing }}</td>
                                                            <td class="text-center">{{ $umk_borongan_lokal->umk_bandrol }}</td>
                                                            <td class="text-center">{{ $umk_borongan_lokal->umk_inner }}</td>
                                                            <td class="text-center">{{ $umk_borongan_lokal->umk_outer }}</td>
                                                        </tr>
                                                        @endforeach
                                                        @endif
                                                    </tbody>
                                                </table>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                    @endfor
                                </div>
                                <div class="col-md-4">
                                    @for ($i = 2023; $i <= $tahun_berjalan; $i++)
                                    @php
                                        $umk_borongan_ekspors = \App\Models\UMKBoronganEkspor::where('tahun_aktif',$i)->get();
                                    @endphp
                                    <div class="accordion-item">
                                        <h5 class="accordion-header m-0" id="flush-borongan-ekspor-heading{{ $i }}">
                                            <button class="accordion-button collapsed fw-semibold" type="button" data-bs-toggle="collapse" data-bs-target="#flush-borongan-ekspor-collapse{{ $i }}" aria-expanded="false" aria-controls="flush-borongan-ekspor-collapse{{ $i }}">
                                                Borongan Ekspor - Tahun {{ $i }} &nbsp;
                                                @if ($i == \Carbon\Carbon::now()->format('Y'))
                                                <span class="badge bg-warning text-dark">Sedang Berjalan</span>
                                                @elseif($i <= \Carbon\Carbon::now()->format('Y'))
                                                <span class="badge bg-success text-dark"><i class="fas fa-check"></i> Selesai</span>
                                                @else
                                                <span class="badge bg-primary">Belum Tersedia</span>
                                                @endif
                                            </button>
                                        </h5>
                                        <div id="flush-borongan-ekspor-collapse{{ $i }}" class="accordion-collapse collapse" aria-labelledby="flush-borongan-ekspor-heading{{ $i }}" data-bs-parent="#accordionFlushExample">
                                            <div class="accordion-body">
                                                <form action="{{ route('umk_periode.umk_borongan_ekspor_simpan',['tahun_aktif' => $i]) }}" method="post" enctype="multipart/form-data">
                                                @csrf
                                                <table class="table table-bordered dt-responsive nowrap"
                                                style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                                                    <thead>
                                                        <tr>
                                                            <th class="text-center">Jenis Produk</th>
                                                            <th class="text-center">UMK Packing</th>
                                                            <th class="text-center">UMK Kemas</th>
                                                            <th class="text-center">UMK Pilih Gagang</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @php
                                                            $tanggal_berjalan = \Carbon\Carbon::now()->format('Y-m');
                                                        @endphp
                                                        @if ($umk_borongan_ekspors->isEmpty())
                                                        @php
                                                            $umk_borongan_ekpor_periodes = \App\Models\UMKBoronganEkspor::where('tahun_aktif',$i-1)->get();
                                                        @endphp
    
                                                        @if ($tanggal_berjalan == $i.'-'.'01')
                                                        @foreach ($umk_borongan_ekpor_periodes as $umk_borongan_ekspor)
                                                            <tr>
                                                                <td class="text-center">{{ $umk_borongan_ekspor['jenis_produk'] }} <input type="hidden" name="jenis_produk[]" class="form-control" value="{{ $umk_borongan_ekspor['jenis_produk'] }}"></td>
                                                                <td><input type="text" name="umk_packing[]" class="form-control" placeholder="UMK Packing" required></td>
                                                                <td><input type="text" name="umk_kemas[]" class="form-control" placeholder="UMK Kemas" required></td>
                                                                <td><input type="text" name="umk_pilih_gagang[]" class="form-control" placeholder="UMK Pilih Gagang" required></td>
                                                            </tr>
                                                        @endforeach
                                                            <tr>
                                                                <td colspan="5"><button type="submit" class="btn btn-primary">Submit</button></td>
                                                            </tr>
                                                        @endif
    
                                                        @else
                                                        @foreach ($umk_borongan_ekspors as $umk_borongan_ekspor)
                                                        <tr>
                                                            <td class="text-center">{{ $umk_borongan_ekspor->jenis_produk }}</td>
                                                            <td class="text-center">{{ $umk_borongan_ekspor->umk_packing }}</td>
                                                            <td class="text-center">{{ $umk_borongan_ekspor->umk_kemas }}</td>
                                                            <td class="text-center">{{ $umk_borongan_ekspor->umk_pilih_gagang }}</td>
                                                        </tr>
                                                        @endforeach
                                                        @endif
                                                    </tbody>
                                                </table>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                    @endfor
                                </div>
                                <div class="col-md-4">
                                    @for ($i = 2023; $i <= $tahun_berjalan; $i++)
                                    @php
                                        $umk_borongan_ambris = \App\Models\UMKBoronganAmbri::where('tahun_aktif',$i)->get();
                                    @endphp
                                    <div class="accordion-item">
                                        <h5 class="accordion-header m-0" id="flush-borongan-ambri-heading{{ $i }}">
                                            <button class="accordion-button collapsed fw-semibold" type="button" data-bs-toggle="collapse" data-bs-target="#flush-borongan-ambri-collapse{{ $i }}" aria-expanded="false" aria-controls="flush-borongan-ambri-collapse{{ $i }}">
                                                Borongan Ambri - Tahun {{ $i }} &nbsp; 
                                                @if ($i == \Carbon\Carbon::now()->format('Y'))
                                                <span class="badge bg-warning text-dark">Sedang Berjalan</span>
                                                @elseif($i <= \Carbon\Carbon::now()->format('Y'))
                                                <span class="badge bg-success text-dark"><i class="fas fa-check"></i> Selesai</span>
                                                @else
                                                <span class="badge bg-primary">Belum Tersedia</span>
                                                @endif
                                            </button>
                                        </h5>
                                        <div id="flush-borongan-ambri-collapse{{ $i }}" class="accordion-collapse collapse" aria-labelledby="flush-borongan-ambri-heading{{ $i }}" data-bs-parent="#accordionFlushExample">
                                            <div class="accordion-body">
                                                <form action="{{ route('umk_periode.umk_borongan_ambri_simpan',['tahun_aktif' => $i]) }}" method="post" enctype="multipart/form-data">
                                                @csrf
                                                <table class="table table-bordered dt-responsive nowrap"
                                                style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                                                    <thead>
                                                        <tr>
                                                            <th class="text-center">Jenis Produk</th>
                                                            <th class="text-center">UMK Etiket</th>
                                                            <th class="text-center">UMK Las Tepi</th>
                                                            <th class="text-center">UMK Las Pojok</th>
                                                            <th class="text-center">UMK Ambri</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @php
                                                            $tanggal_berjalan = \Carbon\Carbon::now()->format('Y-m');
                                                        @endphp
                                                        @if ($umk_borongan_ambris->isEmpty())
                                                        @php
                                                            $umk_borongan_ambri_periodes = \App\Models\UMKBoronganAmbri::where('tahun_aktif',$i-1)->get();
                                                        @endphp
                                                        @if ($tanggal_berjalan == $i.'-'.'01')
                                                        <div class="mb-3">
                                                            <div class="alert alert-danger border-0">UMK Borongan Lokal Diinput mulai bulan {{ \Carbon\Carbon::create($i,01)->isoFormat('MMMM YYYY') }}, untuk bulan selanjutnya akan ditutup inputan secara otomatis oleh sistem</div>
                                                        </div>
                                                        @foreach ($umk_borongan_ambri_periodes as $umk_borongan_ambri)
                                                            <tr>
                                                                <td class="text-center">{{ $umk_borongan_ambri['jenis_produk'] }} <input type="hidden" name="jenis_produk[]" class="form-control" value="{{ $umk_borongan_ambri['jenis_produk'] }}"></td>
                                                                <td><input type="text" name="umk_etiket[]" class="form-control" placeholder="UMK Etiket" required></td>
                                                                <td><input type="text" name="umk_las_tepi[]" class="form-control" placeholder="UMK Las Tepi" required></td>
                                                                <td><input type="text" name="umk_las_pojok[]" class="form-control" placeholder="UMK Las Pojok" required></td>
                                                                <td><input type="text" name="umk_ambri[]" class="form-control" placeholder="UMK Ambri" required></td>
                                                            </tr>
                                                        @endforeach
                                                            <tr>
                                                                <td colspan="5"><button type="submit" class="btn btn-primary">Submit</button></td>
                                                            </tr>
                                                        @endif
                                                        
                                                        @else
                                                        @foreach ($umk_borongan_ambris as $umk_borongan_ambri)
                                                            <tr>
                                                                <td class="text-center">{{ $umk_borongan_ambri->jenis_produk }}</td>
                                                                <td class="text-center">{{ $umk_borongan_ambri->umk_etiket }}</td>
                                                                <td class="text-center">{{ $umk_borongan_ambri->umk_las_tepi }}</td>
                                                                <td class="text-center">{{ $umk_borongan_ambri->umk_las_pojok }}</td>
                                                                <td class="text-center">{{ $umk_borongan_ambri->umk_ambri }}</td>
                                                            </tr>
                                                        @endforeach
                                                        @endif
                                                    </tbody>
                                                </table>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                    @endfor
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <div class="card-title">UMK Supir RIT</div>
                        <hr>
                    </div>
                    @for ($i = 2023; $i <= $tahun_berjalan; $i++)
                    @php
                        $umk_supir_rits = \App\Models\RitUMK::where('tahun_aktif',$i)->get();
                    @endphp
                    <div class="accordion-item">
                        <h5 class="accordion-header m-0" id="flush-supir-rit-heading{{ $i }}">
                            <button class="accordion-button collapsed fw-semibold" type="button" data-bs-toggle="collapse" data-bs-target="#flush-supir-rit-collapse{{ $i }}" aria-expanded="false" aria-controls="flush-supir-rit-collapse{{ $i }}">
                                Supir RIT - Tahun {{ $i }} &nbsp;
                                @if ($i == \Carbon\Carbon::now()->format('Y'))
                                <span class="badge bg-warning text-dark">Sedang Berjalan</span>
                                @elseif($i <= \Carbon\Carbon::now()->format('Y'))
                                <span class="badge bg-success text-dark"><i class="fas fa-check"></i> Selesai</span>
                                @else
                                <span class="badge bg-primary">Belum Tersedia</span>
                                @endif
                            </button>
                        </h5>
                        <div id="flush-supir-rit-collapse{{ $i }}" class="accordion-collapse collapse" aria-labelledby="flush-supir-rit-heading{{ $i }}" data-bs-parent="#accordionFlushExample">
                            <div class="accordion-body">
                                <form action="{{ route('umk_periode.umk_supir_rit_simpan',['tahun_aktif' => $i]) }}" method="post" enctype="multipart/form-data">
                                    @csrf
                                <table class="table table-bordered dt-responsive nowrap"
                                style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                                    <thead>
                                        <tr>
                                            <th class="text-center">Kategori Upah</th>
                                            <th class="text-center">RIT Posisi</th>
                                            <th class="text-center">RIT Kendaraan</th>
                                            <th class="text-center">RIT Tujuan</th>
                                            <th class="text-center">Tarif</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php
                                            $tanggal_berjalan = \Carbon\Carbon::now()->format('Y-m');
                                        @endphp
                                        @if ($umk_supir_rits->isEmpty())
                                            @php
                                                $umk_supir_rit_periodes = \App\Models\RitUMK::where('tahun_aktif',$i-1)->get();
                                            @endphp

                                            @if ($tanggal_berjalan == $i.'-'.'01')
                                            <div class="mb-3">
                                                <div class="alert alert-danger border-0">UMK Supir RIT Diinput mulai bulan {{ \Carbon\Carbon::create($i,01)->isoFormat('MMMM YYYY') }}, untuk bulan selanjutnya akan ditutup inputan secara otomatis oleh sistem</div>
                                            </div>
                                            @foreach ($umk_supir_rit_periodes as $umk_supir_rit_periode)
                                                <tr>
                                                    <td class="text-center">{{ $umk_supir_rit_periode->kategori_upah }}
                                                        <input type="hidden" name="kategori_upah[]" class="form-control" value="{{ $umk_supir_rit_periode->kategori_upah }}">
                                                    </td>
                                                    @php
                                                        if ($umk_supir_rit_periode->rit_posisi_id == 0) {
                                                            $rit_posisi = '-';
                                                        }else{
                                                            $rit_posisi = $umk_supir_rit_periode->rit_posisi->nama_posisi;
                                                        }
                                                        
                                                        if ($umk_supir_rit_periode->rit_kendaraan_id == 0) {
                                                            $rit_kendaraan = '-';
                                                        }else{
                                                            $rit_kendaraan = $umk_supir_rit_periode->rit_kendaraan->jenis_kendaraan;
                                                        }

                                                        if ($umk_supir_rit_periode->rit_tujuan_id == 0) {
                                                            $rit_tujuan = '-';
                                                        }else{
                                                            $rit_tujuan = $umk_supir_rit_periode->rit_tujuan->tujuan;
                                                        }
                                                    @endphp
                                                    <td class="text-center">{{ $rit_posisi }}
                                                        <input type="hidden" name="rit_posisi_id[]" class="form-control" value="{{ $umk_supir_rit_periode->rit_posisi_id }}">
                                                    </td>
                                                    <td class="text-center">{{ $rit_kendaraan }}
                                                        <input type="hidden" name="rit_kendaraan_id[]" class="form-control" value="{{ $umk_supir_rit_periode->rit_kendaraan_id }}">
                                                    </td>
                                                    <td class="text-center">{{ $rit_tujuan }}
                                                        <input type="hidden" name="rit_tujuan_id[]" class="form-control" value="{{ $umk_supir_rit_periode->rit_tujuan_id }}">
                                                    </td>
                                                    <td class="text-center">
                                                        <input type="text" name="tarif[]" class="form-control" placeholder="Tarif" autocomplete="off" required>
                                                        <input type="hidden" name="tahun_aktif[]" class="form-control" value="{{ $i }}">
                                                    </td>
                                                </tr>
                                            @endforeach
                                                <tr>
                                                    <td colspan="5"><button type="submit" class="btn btn-primary">Submit</button></td>
                                                </tr>
                                            @endif
                                        @else
                                            @foreach ($umk_supir_rits as $umk_supir_rit)
                                            <tr>
                                                <td class="text-center">{{ $umk_supir_rit->kategori_upah }}</td>
                                                @php
                                                    if ($umk_supir_rit->rit_posisi_id == 0) {
                                                        $rit_posisi = '-';
                                                    }else{
                                                        $rit_posisi = $umk_supir_rit->rit_posisi->nama_posisi;
                                                    }
                                                    
                                                    if ($umk_supir_rit->rit_kendaraan_id == 0) {
                                                        $rit_kendaraan = '-';
                                                    }else{
                                                        $rit_kendaraan = $umk_supir_rit->rit_kendaraan->jenis_kendaraan;
                                                    }

                                                    if ($umk_supir_rit->rit_tujuan_id == 0) {
                                                        $rit_tujuan = '-';
                                                    }else{
                                                        $rit_tujuan = $umk_supir_rit->rit_tujuan->tujuan;
                                                    }
                                                @endphp
                                                <td class="text-center">{{ $rit_posisi }}</td>
                                                <td class="text-center">{{ $rit_kendaraan }}</td>
                                                <td class="text-center">{{ $rit_tujuan }}</td>
                                                <td class="text-center">{{ 'Rp. '.number_format($umk_supir_rit->tarif,0,',','.') }}</td>
                                            </tr>
                                            @endforeach
                                        @endif
                                    </tbody>
                                </table>
                                </form>
                            </div>
                        </div>
                    </div>
                    @endfor
                </div>
            </div>
        </div>
    </div>
@endsection
@section('script')
@endsection