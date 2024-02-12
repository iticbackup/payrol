@extends('layouts.backend.app')

@section('title')

@endsection

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
                <div class="card-header">

                </div>
                {{-- <form action="{{ route('hasil_kerja.outerLokal.view_hasil.simpan',['id' => $id, 'kode_pengerjaan' => $kode_pengerjaan, 'tanggal' => $tanggal]) }}" method="post" enctype="multipart/form-data"> --}}
                <form id="form-simpan" method="post" enctype="multipart/form-data">
                @csrf
                <div class="card-body">
                    <table class="table table-bordered dt-responsive nowrap"
                    style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                        <thead>
                            <tr>
                                <th class="text-center" rowspan="3">No</th>
                                <th class="text-center" rowspan="3" style="width: 5%">NIK</th>
                                <th class="text-center" rowspan="3" style="width: 15%">Nama</th>
                                <th class="text-center" colspan="2">Hasil Kerja 1</th>
                                <th class="text-center" colspan="2">Hasil Kerja 2</th>
                                <th class="text-center" colspan="2">Hasil Kerja 3</th>
                                <th class="text-center" colspan="2">Hasil Kerja 4</th>
                                <th class="text-center" colspan="2">Hasil Kerja 5</th>
                            </tr>
                            <?php
                                // $umk_karyawan_bandrol = \App\Models\KaryawanOperator::where('jenis_operator_detail_pekerjaan_id',2)->first();
                                // dd($umk_karyawan_bandrol);
                                $umk_borongan_lokal_kerja = \App\Models\Pengerjaan::select([
                                                                                    'pengerjaan.hasil_kerja_1 as hasil_kerja_1',
                                                                                    'pengerjaan.hasil_kerja_2 as hasil_kerja_2',
                                                                                    'pengerjaan.hasil_kerja_3 as hasil_kerja_3',
                                                                                    'pengerjaan.hasil_kerja_4 as hasil_kerja_4',
                                                                                    'pengerjaan.hasil_kerja_5 as hasil_kerja_5',
                                                                                    'pengerjaan.lembur as lembur'
                                                                                ])
                                                                                ->leftJoin('operator_karyawan','operator_karyawan.id','=','pengerjaan.operator_karyawan_id')
                                                                                ->where('pengerjaan.tanggal_pengerjaan',$tanggal)
                                                                                ->where('operator_karyawan.jenis_operator_detail_pekerjaan_id',4)
                                                                                // ->with('operator_karyawan', function($query){
                                                                                //     $query->where('jenis_operator_detail_pekerjaan_id',2);
                                                                                // })
                                                                                // ->with('operator_karyawan')
                                                                                ->first();
                                // dd($umk_borongan_lokal_kerja);
                                if(empty($umk_borongan_lokal_kerja)){
                                    $explode_select_umk_borongan_lokal_1 = null;
                                    $explode_select_umk_borongan_lokal_2 = null;
                                    $explode_select_umk_borongan_lokal_3 = null;
                                    $explode_select_umk_borongan_lokal_4 = null;
                                    $explode_select_umk_borongan_lokal_5 = null;

                                    $select_umk_borongan_lokal_1 = null;
                                    $select_umk_borongan_lokal_2 = null;
                                    $select_umk_borongan_lokal_3 = null;
                                    $select_umk_borongan_lokal_4 = null;
                                    $select_umk_borongan_lokal_5 = null;

                                    $lembur_1 = null;
                                    $lembur_2 = null;
                                    $lembur_3 = null;
                                    $lembur_4 = null;
                                    $lembur_5 = null;

                                }else{
                                    $explode_select_umk_borongan_lokal_1 = explode("|",$umk_borongan_lokal_kerja->hasil_kerja_1);
                                    $explode_select_umk_borongan_lokal_2 = explode("|",$umk_borongan_lokal_kerja->hasil_kerja_2);
                                    $explode_select_umk_borongan_lokal_3 = explode("|",$umk_borongan_lokal_kerja->hasil_kerja_3);
                                    $explode_select_umk_borongan_lokal_4 = explode("|",$umk_borongan_lokal_kerja->hasil_kerja_4);
                                    $explode_select_umk_borongan_lokal_5 = explode("|",$umk_borongan_lokal_kerja->hasil_kerja_5);

                                    $select_umk_borongan_lokal_1 = $explode_select_umk_borongan_lokal_1[0];
                                    $select_umk_borongan_lokal_2 = $explode_select_umk_borongan_lokal_2[0];
                                    $select_umk_borongan_lokal_3 = $explode_select_umk_borongan_lokal_3[0];
                                    $select_umk_borongan_lokal_4 = $explode_select_umk_borongan_lokal_4[0];
                                    $select_umk_borongan_lokal_5 = $explode_select_umk_borongan_lokal_5[0];

                                    // dd($select_umk_borongan_lokal_1);
                                    if($umk_borongan_lokal_kerja->lembur == null){
                                        $checked_lembur_1 = null;
                                        $checked_lembur_2 = null;
                                        $checked_lembur_3 = null;
                                        $checked_lembur_4 = null;
                                        $checked_lembur_5 = null;
                                    }else{
                                        $explode_lembur = explode("|",$umk_borongan_lokal_kerja->lembur);
                                        // dd(explode("-",$explode_lembur[1]));
                                        $explode_lembur_1 = explode("-",$explode_lembur[1]);
                                        if($explode_lembur_1[1] == 'y'){
                                            $checked_lembur_1 = 'y';
                                            $lembur_1 = 1.5;
                                        }else{
                                            $checked_lembur_1 = null;
                                            $lembur_1 = 1;
                                        }
    
                                        $explode_lembur_2 = explode("-",$explode_lembur[2]);
                                        if($explode_lembur_2[1] == 'y'){
                                            $checked_lembur_2 = 'y';
                                            $lembur_2 = 1.5;
                                        }else{
                                            $checked_lembur_2 = null;
                                            $lembur_2 = 1;
                                        }
    
                                        $explode_lembur_3 = explode("-",$explode_lembur[3]);
                                        if($explode_lembur_3[1] == 'y'){
                                            $checked_lembur_3 = 'y';
                                            $lembur_3 = 1.5;
                                        }else{
                                            $checked_lembur_3 = null;
                                            $lembur_3 = 1;
                                        }
    
                                        $explode_lembur_4 = explode("-",$explode_lembur[4]);
                                        if($explode_lembur_4[1] == 'y'){
                                            $checked_lembur_4 = 'y';
                                            $lembur_4 = 1.5;
                                        }else{
                                            $checked_lembur_4 = null;
                                            $lembur_4 = 1;
                                        }
    
                                        $explode_lembur_5 = explode("-",$explode_lembur[5]);
                                        if($explode_lembur_5[1] == 'y'){
                                            $checked_lembur_5 = 'y';
                                            $lembur_5 = 1.5;
                                        }else{
                                            $checked_lembur_5 = null;
                                            $lembur_5 = 1;
                                        }

                                        // dd($select_umk_borongan_lokal_2);
                                    }
                                }
                            ?>
                            <tr>
                                <th class="text-center" style="width: 10%">
                                    {{-- {{ dd($select_umk_borongan_lokal_1) }} --}}
                                    <select name="umk_borongan_lokal_kerja_1" class="form-control" id="">
                                        <option value="">-- Pilih --</option>
                                        @foreach ($umk_borongan_lokals as $umk_borongan_lokal)
                                            <option value="{{ $umk_borongan_lokal->id }}" {{ $umk_borongan_lokal->id == $select_umk_borongan_lokal_1 ? 'selected' : null }}>{{ $umk_borongan_lokal->jenis_produk }} - Rp {{ $umk_borongan_lokal->umk_outer }}</option>
                                        @endforeach
                                    </select>
                                </th>
                                <th class="text-center">
                                    <input type="checkbox" name="lembur_kerja_1" {{ $checked_lembur_1 == 'y' ? 'checked' : null }} class="form-check-input" id=""> <br>Lembur
                                </th>
                                <th class="text-center" style="width: 10%">
                                    <select name="umk_borongan_lokal_kerja_2" class="form-control" id="">
                                        <option value="">-- Pilih --</option>
                                        @foreach ($umk_borongan_lokals as $umk_borongan_lokal)
                                        {{-- {{ dd($umk_borongan_lokal->id) }} --}}
                                            <option value="{{ $umk_borongan_lokal->id }}" {{ $umk_borongan_lokal->id == $select_umk_borongan_lokal_2 ? 'selected' : null }}>{{ $umk_borongan_lokal->jenis_produk }} - Rp {{ $umk_borongan_lokal->umk_outer }}</option>
                                        @endforeach
                                    </select>
                                </th>
                                <th class="text-center">
                                    <input type="checkbox" name="lembur_kerja_2" {{ $checked_lembur_2 == 'y' ? 'checked' : null }} class="form-check-input" id=""> <br>Lembur
                                </th>
                                <th class="text-center" style="width: 10%">
                                    <select name="umk_borongan_lokal_kerja_3" class="form-control" id="">
                                        <option value="">-- Pilih --</option>
                                        @foreach ($umk_borongan_lokals as $umk_borongan_lokal)
                                            <option value="{{ $umk_borongan_lokal->id }}" {{ $umk_borongan_lokal->id == $select_umk_borongan_lokal_3 ? 'selected' : null }}>{{ $umk_borongan_lokal->jenis_produk }} - Rp {{ $umk_borongan_lokal->umk_outer }}</option>
                                        @endforeach
                                    </select>
                                </th>
                                <th class="text-center">
                                    <input type="checkbox" name="lembur_kerja_3" {{ $checked_lembur_3 == 'y' ? 'checked' : null }} class="form-check-input" id=""> <br>Lembur
                                </th>
                                <th class="text-center" style="width: 10%">
                                    <select name="umk_borongan_lokal_kerja_4" class="form-control" id="">
                                        <option value="">-- Pilih --</option>
                                        @foreach ($umk_borongan_lokals as $umk_borongan_lokal)
                                            <option value="{{ $umk_borongan_lokal->id }}" {{ $umk_borongan_lokal->id == $select_umk_borongan_lokal_4 ? 'selected' : null }}>{{ $umk_borongan_lokal->jenis_produk }} - Rp {{ $umk_borongan_lokal->umk_outer }}</option>
                                        @endforeach
                                    </select>
                                </th>
                                <th class="text-center">
                                    <input type="checkbox" name="lembur_kerja_4" {{ $checked_lembur_4 == 'y' ? 'checked' : null }} class="form-check-input" id=""> <br>Lembur
                                </th>
                                <th class="text-center" style="width: 10%">
                                    <select name="umk_borongan_lokal_kerja_5" class="form-control" id="">
                                        <option value="">-- Pilih --</option>
                                        @foreach ($umk_borongan_lokals as $umk_borongan_lokal)
                                            <option value="{{ $umk_borongan_lokal->id }}" {{ $umk_borongan_lokal->id == $select_umk_borongan_lokal_5 ? 'selected' : null }}>{{ $umk_borongan_lokal->jenis_produk }} - Rp {{ $umk_borongan_lokal->umk_outer }}</option>
                                        @endforeach
                                    </select>
                                </th>
                                <th class="text-center">
                                    <input type="checkbox" name="lembur_kerja_5" {{ $checked_lembur_5 == 'y' ? 'checked' : null }} class="form-check-input" id=""> <br>Lembur
                                </th>
                            </tr>
                            <tr>
                                <th class="text-center">Hasil</th>
                                <th class="text-center">Jam</th>
                                <th class="text-center">Hasil</th>
                                <th class="text-center">Jam</th>
                                <th class="text-center">Hasil</th>
                                <th class="text-center">Jam</th>
                                <th class="text-center">Hasil</th>
                                <th class="text-center">Jam</th>
                                <th class="text-center">Hasil</th>
                                <th class="text-center">Jam</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($packing_lokals as $key=> $packing_lokal)
                                {{-- <?php 
                                    $explode_hasil_kerja_1 = explode("|",$packing_lokal->hasil_kerja_1);
                                    if($explode_hasil_kerja_1[1] == '0'){
                                        $hasil_kerja_1 = 0;
                                    }else{
                                        $hasil_kerja_1 = $explode_hasil_kerja_1[1];
                                    }
                                ?>
                                <tr>
                                    <td class="text-center">{{ $key+1 }}</td>
                                    <td>
                                        {{ $packing_lokal->nik }}
                                    </td>
                                    <td>{{ $packing_lokal->nama }}</td>
                                    <td style="width: 5%"><input type="text" name="hasil_kerja_1[]" value="{{ $hasil_kerja_1 }}" class="form-control" id="" placeholder="Hasil Kerja 1"></td>
                                    <td style="width: 5%">
                                        <div class="input-group">
                                            <input type="text" name="total_jam_1[]" class="form-control">
                                            <span class="input-group-text" id="basic-addon2">Jam</span>
                                        </div>
                                    </td>
                                    <td style="width: 5%"><input type="text" name="hasil_kerja_2[]" class="form-control" id="" placeholder="Hasil Kerja 2"></td>
                                    <td style="width: 5%">
                                        <div class="input-group">
                                            <input type="text" name="total_jam_2[]" class="form-control">
                                            <span class="input-group-text" id="basic-addon2">Jam</span>
                                        </div>
                                    </td>
                                    <td style="width: 5%"><input type="text" name="hasil_kerja_3[]" class="form-control" id="" placeholder="Hasil Kerja 3"></td>
                                    <td style="width: 5%">
                                        <div class="input-group">
                                            <input type="text" name="total_jam_3[]" class="form-control">
                                            <span class="input-group-text" id="basic-addon2">Jam</span>
                                        </div>
                                    </td>
                                    <td style="width: 5%"><input type="text" name="hasil_kerja_4[]" class="form-control" id="" placeholder="Hasil Kerja 4"></td>
                                    <td style="width: 5%">
                                        <div class="input-group">
                                            <input type="text" name="total_jam_4[]" class="form-control">
                                            <span class="input-group-text" id="basic-addon2">Jam</span>
                                        </div>
                                    </td>
                                    <td style="width: 5%"><input type="text" name="hasil_kerja_5[]" class="form-control" id="" placeholder="Hasil Kerja 5"></td>
                                    <td style="width: 5%">
                                        <div class="input-group">
                                            <input type="text" name="total_jam_5[]" class="form-control">
                                            <span class="input-group-text" id="basic-addon2">Jam</span>
                                        </div>
                                    </td>
                                </tr> --}}
                                <?php 
                                    if(empty($packing_lokal->hasil_kerja_1)){
                                        $hasil_kerja_1 = null;
                                    }else{
                                        $explode_hasil_kerja_1 = explode("|",$packing_lokal->hasil_kerja_1);
                                        $hasil_kerja_1 = $explode_hasil_kerja_1[1];
                                    }

                                    if(empty($packing_lokal->hasil_kerja_2)){
                                        $hasil_kerja_2 = null;
                                    }else{
                                        $explode_hasil_kerja_2 = explode("|",$packing_lokal->hasil_kerja_2);
                                        $hasil_kerja_2 = $explode_hasil_kerja_2[1];
                                    }

                                    if(empty($packing_lokal->hasil_kerja_3)){
                                        $hasil_kerja_3 = null;
                                    }else{
                                        $explode_hasil_kerja_3 = explode("|",$packing_lokal->hasil_kerja_3);
                                        $hasil_kerja_3 = $explode_hasil_kerja_3[1];
                                    }

                                    if(empty($packing_lokal->hasil_kerja_4)){
                                        $hasil_kerja_4 = null;
                                    }else{
                                        $explode_hasil_kerja_4 = explode("|",$packing_lokal->hasil_kerja_4);
                                        $hasil_kerja_4 = $explode_hasil_kerja_4[1];
                                    }

                                    if(empty($packing_lokal->hasil_kerja_5)){
                                        $hasil_kerja_5 = null;
                                    }else{
                                        $explode_hasil_kerja_5 = explode("|",$packing_lokal->hasil_kerja_5);
                                        $hasil_kerja_5 = $explode_hasil_kerja_5[1];
                                    }
                                ?>
                                <tr>
                                    <td class="text-center">{{ $key+1 }}</td>
                                    <td>{{ $packing_lokal->nik }}</td>
                                    <td>{{ $packing_lokal->nama }}</td>
                                    <td style="width: 5%"><input type="text" name="hasil_kerja_1[]" style="text-align: center" value="{{ $hasil_kerja_1 }}" class="form-control" id="" placeholder="Hasil Kerja 1"></td>
                                    <td style="width: 5%">
                                        <div class="input-group">
                                            <input type="text" name="total_jam_1[]" value="{{ $packing_lokal->total_jam_kerja_1 }}" style="background-color: #E1ECC8; text-align: center" class="form-control">
                                            {{-- <span class="input-group-text" id="basic-addon2">Jam</span> --}}
                                        </div>
                                    </td>
                                    <td style="width: 5%"><input type="text" name="hasil_kerja_2[]" style="text-align: center" value="{{ $hasil_kerja_2 }}" class="form-control" id="" placeholder="Hasil Kerja 2"></td>
                                    <td style="width: 5%">
                                        <div class="input-group">
                                            <input type="text" name="total_jam_2[]" value="{{ $packing_lokal->total_jam_kerja_2 }}" style="background-color: #E1ECC8; text-align: center" class="form-control">
                                            {{-- <span class="input-group-text" id="basic-addon2">Jam</span> --}}
                                        </div>
                                    </td>
                                    <td style="width: 5%"><input type="text" name="hasil_kerja_3[]" style="text-align: center" value="{{ $hasil_kerja_3 }}" class="form-control" id="" placeholder="Hasil Kerja 3"></td>
                                    <td style="width: 5%">
                                        <div class="input-group">
                                            <input type="text" name="total_jam_3[]" value="{{ $packing_lokal->total_jam_kerja_3 }}" style="background-color: #E1ECC8; text-align: center" class="form-control">
                                            {{-- <span class="input-group-text" id="basic-addon2">Jam</span> --}}
                                        </div>
                                    </td>
                                    <td style="width: 5%"><input type="text" name="hasil_kerja_4[]" style="text-align: center" value="{{ $hasil_kerja_4 }}" class="form-control" id="" placeholder="Hasil Kerja 4"></td>
                                    <td style="width: 5%">
                                        <div class="input-group">
                                            <input type="text" name="total_jam_4[]" value="{{ $packing_lokal->total_jam_kerja_4 }}" style="background-color: #E1ECC8; text-align: center" class="form-control">
                                            {{-- <span class="input-group-text" id="basic-addon2">Jam</span> --}}
                                        </div>
                                    </td>
                                    <td style="width: 5%"><input type="text" name="hasil_kerja_5[]" style="text-align: center" value="{{ $hasil_kerja_5 }}" class="form-control" id="" placeholder="Hasil Kerja 5"></td>
                                    <td style="width: 5%">
                                        <div class="input-group">
                                            <input type="text" name="total_jam_5[]" value="{{ $packing_lokal->total_jam_kerja_5 }}" style="background-color: #E1ECC8; text-align: center" class="form-control">
                                            {{-- <span class="input-group-text" id="basic-addon2">Jam</span> --}}
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="card-footer">
                    <button type="submit" class="btn btn-primary">Submit</button>
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
    $(document).ready(function(){
        $('input').keydown(function(e){
            if(e.keyCode==13){
            if($(':input:eq(' + ($(':input').index(this) + 1) + ')').attr('type')=='submit'){
            // check for submit button and submit form on enter press
                return true;
            }

            // $(':input:eq(' + ($(':input').index(this) + 1) + ')').focus();
            // alert($(':input').index(this) * 10);
            if(($(':input').index(this)+1)%2 == 0){
                $(':input:eq(' + ($(':input').index(this) + 10) + ')').focus();
            }
            else{
                $(':input:eq(' + ($(':input').index(this) + 10) + ')').focus();
            }
            return false;
            }

        });
    });

    $('#form-simpan').submit(function(e) {
        e.preventDefault();
        let formData = new FormData(this);
        $('#image-input-error').text('');
        $.ajax({
            type:'POST',
            url:"{{ url('pengerjaan/hasil_kerja/outer_lokal/'.$id.'/'.$kode_pengerjaan.'/'.$tanggal.'/input_hasil/simpan') }}",
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
