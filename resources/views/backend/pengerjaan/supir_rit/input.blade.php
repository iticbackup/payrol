@extends('layouts.backend.master_no_header')

@section('css')
    <link href="{{ URL::asset('public/assets/css/iziToast.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ URL::asset('public/assets/plugins/sweet-alert2/sweetalert2.min.css') }}" rel="stylesheet"
        type="text/css">
    <script src="{{ URL::asset('public/assets/js/pages/jquery.sweet-alert.init.js') }}"></script>
@endsection

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                {{-- <form action="{{ route('hasil_kerja.supir_rit.simpan',['kode_pengerjaan' => $kode_pengerjaan, 'tanggal' => $tanggal]) }}" method="post" enctype="multipart/form-data"> --}}
                <form id="form-simpan" method="post" enctype="multipart/form-data">
                    @csrf
                <div class="card-body">
                    <table class="table table-bordered dt-responsive nowrap"
                    style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                    <thead>
                        <tr>
                            <th class="text-center" rowspan="2" style="width: 50px">No</th>
                            <th class="text-center" rowspan="2" style="width: 100px">NIK</th>
                            <th class="text-center" rowspan="2">Nama</th>
                            <th class="text-center" colspan="2">Hasil Kerja</th>
                        </tr>
                        <tr>
                            <th class="text-center" style="width: 350px">KODE</th>
                            <th class="text-center" style="width: 150px">DPB</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($pengerjaan_supir_rits as $key => $pengerjaan_supir_rit)
                        @php
                            $pengerjaan_supir_rit_daily = \App\Models\PengerjaanRITHarian::where('tanggal_pengerjaan',$tanggal)
                                                                                        ->where('karyawan_supir_rit_id', $pengerjaan_supir_rit->karyawan_supir_rit_id)
                                                                                        ->where('kode_pengerjaan',$kode_pengerjaan)
                                                                                        ->first();
                            // dd($pengerjaan_supir_rit_daily);
                            $rit_umks = \App\Models\RitUMK::where('rit_posisi_id',$pengerjaan_supir_rit->rit_posisi_id)->where('status','y')->orderBy('kategori_upah','asc')->get();
                            // dd($pengerjaan_supir_rit_daily);
                            if (empty($pengerjaan_supir_rit_daily->hasil_kerja_1)) {
                                $hasil_kerja_1 = 0;
                                $rit = 1;
                            }else{
                                $explode_hasil_kerja_1 = explode("|",$pengerjaan_supir_rit_daily->hasil_kerja_1);
                                $hasil_kerja_1 = $explode_hasil_kerja_1[0];
                                $rit = $explode_hasil_kerja_1[1];
                            }

                            if (empty($pengerjaan_supir_rit_daily->dpb)) {
                                $dpb = 0;
                            }else{
                                $dpb = $pengerjaan_supir_rit_daily->dpb;
                            }
                            // echo json_encode($hasil_kerja_1);
                        @endphp
                        <tr>
                            <td class="text-center">{{ $key+1 }}</td>
                            <td class="text-center">{{ $pengerjaan_supir_rit->nik }}</td>
                            <td>{{ $pengerjaan_supir_rit->nama }}</td>
                            <td class="text-center">
                                <div class="row">
                                    <div class="col-md-5">
                                        <select name="hasil_kerja_1[]" class="form-control" id="">
                                            @foreach ($rit_umks as $rit_umk)
                                            <option value="{{ $rit_umk->id }}" {{ $rit_umk->id==$hasil_kerja_1 ? 'selected' : null }}>{{ $rit_umk->kategori_upah.' - '.'Rp. '.number_format($rit_umk->tarif,0,',','.') }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-2">|</div>
                                    <div class="col-md-5">
                                        <input type="text" name="rit[]" class="form-control text-center" value="{{ $rit }}" id="">
                                    </div>
                                </div>
                            </td>
                            <td><input type="text" name="dpb[]" class="form-control text-center" value="{{ $dpb }}" id=""></td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="5" class="text-center">
                                <button type="submit" class="btn btn-success btn-xs">Submit</button>
                            </td>
                        </tr>
                    </tfoot>
                    </table>
                </div>
                </form>
            </div>
        </div>
    </div>
@endsection
@section('script')
<script src="{{ URL::asset('public/assets/js/iziToast.min.js') }}"></script>
<script src="{{ URL::asset('public/assets/plugins/sweet-alert2/sweetalert2.min.js') }}"></script>
<script>
    $('#form-simpan').submit(function(e) {
        e.preventDefault();
        let formData = new FormData(this);
        $('#image-input-error').text('');
        $.ajax({
            type:'POST',
            url:"{{ url('pengerjaan/hasil_kerja/supir_rit/'.$kode_pengerjaan.'/'.$tanggal.'/simpan') }}",
            data: formData,
            contentType: false,
            processData: false,
            success: (result) => {
                if(result.success != false){
                    var timerInterval
                    Swal.fire({
                    title: result.message_title+' - '+result.message_content,
                    html: 'I will close in <strong></strong> seconds.',
                    timer: 3000,
                    onBeforeOpen: function() {
                        Swal.showLoading()
                        timerInterval = setInterval(function() {
                        Swal.getContent().querySelector('strong')
                            .textContent = Swal.getTimerLeft()
                        }, 100)
                    },
                    onClose: function() {
                        clearInterval(timerInterval)
                    }
                    }).then(function(result) {
                        if (result.dismiss === Swal.DismissReason.timer) {
                            window.close();
                        }
                    })
                }else{
                    iziToast.error({
                        title: result.success,
                        message: result.error
                    });
                }
            },
            error: function (request, status, error) {
                iziToast.error({
                    title: 'Error',
                    message: error,
                });
            }
        });
    });
</script>
@endsection