@extends('layouts.backend.app')

@section('title')
    Karyawan Pengerjaan - {{ $jenis_operators->jenis_operator }}
@endsection

@section('css')
    <link href="{{ URL::asset('public/assets/css/iziToast.css') }}" rel="stylesheet" type="text/css" />
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
            Karyawan Pengerjaan
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
                <form id="form-simpan" method="post" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="akhir_bulan" value="{{ $new_data_pengerjaan->akhir_bulan }}" id="">
                    <div class="card-body">
                        <h5>
                            Kode Payrol : {{ $kode_payrol }}
                            @if ($new_data_pengerjaan->status == 'n')
                                <i class="far fa-check-circle text-success"></i>
                            @endif
                            <input type="hidden" name="kode_pengerjaan"
                                value="{{ $new_data_pengerjaan->kode_pengerjaan }}">
                        </h5>
                        <div>Tanggal Buat : {{ \Carbon\Carbon::parse($new_data_pengerjaan->date)->isoFormat('LLL') }}</div>
                        @if ($new_data_pengerjaan->status == 'y')
                            <div class="text-center"><button type="submit"
                                    class="btn btn-xs btn-outline-primary">Submit</button></div>
                        @endif
                        <br>
                        <div class="row">
                            @foreach ($jenis_operator_detail_pekerjaans as $keys => $jenis_operator_detail_pekerjaan)
                                <?php
                                // dd($jenis_operator_detail_pekerjaasn->id);
                                $jenis_pekerja_id = [];
                                $karyawan_operators = \App\Models\KaryawanOperator::select([
                                                        'operator_karyawan.id as id',
                                                        'operator_karyawan.nik as nik',
                                                        'operator_karyawan.jenis_operator_id as jenis_operator_id',
                                                        'operator_karyawan.jenis_operator_detail_id as jenis_operator_detail_id',
                                                        'operator_karyawan.jenis_operator_detail_pekerjaan_id as jenis_operator_detail_pekerjaan_id',
                                                        'operator_karyawan.tunjangan_kerja_id as tunjangan_kerja_id',
                                                        'operator_karyawan.jht as jht',
                                                        'operator_karyawan.bpjs as bpjs',
                                                        'operator_karyawan.training as training',
                                                        'operator_karyawan.status as status',
                                                        'biodata_karyawan.nama as nama',
                                                    ])
                                                    ->leftJoin('itic_emp.biodata_karyawan','biodata_karyawan.nik','=','operator_karyawan.nik')
                                                    ->where('operator_karyawan.jenis_operator_detail_pekerjaan_id', $jenis_operator_detail_pekerjaan->id)
                                                    ->where('operator_karyawan.status', 'Y')
                                                    // ->orderBy('operator_karyawan.jenis_operator_detail_pekerjaan_id','asc')
                                                    ->orderBy('biodata_karyawan.nama','asc')
                                                    ->get();
                                array_push($jenis_pekerja_id, $jenis_operator_detail_pekerjaan->id);
                                // dd(array_push($jenis_pekerja_id,$jenis_operator_detail_pekerjaan->id));
                                ?>
                                <div class="col-md-3">
                                    <table class="table table-bordered dt-responsive nowrap"
                                        style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                                        <thead>
                                            <tr>
                                                <th colspan="5" class="text-center">Pegawai
                                                    {{ $jenis_operator_detail_pekerjaan->jenis_posisi_pekerjaan }}</th>
                                            </tr>
                                            <tr>
                                                <th class="text-center">No</th>
                                                <th class="text-center">NIK</th>
                                                <th class="text-center">Nama</th>
                                                <th class="text-center">Status Karyawan</th>
                                                <th class="text-center"><input type="checkbox"
                                                        {{ $new_data_pengerjaan->status == 'n' ? 'disabled' : null }}
                                                        name="toggle"
                                                        onclick="toggle_{{ $jenis_operator_detail_pekerjaan->id }}(this)"
                                                        class="form-check-input"></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($karyawan_operators as $key => $karyawan_operator)
                                                <?php
                                                $biodata_karyawan = \App\Models\BiodataKaryawan::where('nik', $karyawan_operator->nik)->first();
                                                $checklist_karyawan = \App\Models\PengerjaanWeekly::where('operator_karyawan_id',$karyawan_operator->id)
                                                                                                ->where('kode_pengerjaan',$new_data_pengerjaan->kode_pengerjaan)
                                                                                                ->first();
                                                                                                // dd($new_data_pengerjaan->kode_pengerjaan);
                                                ?>
                                                <tr>
                                                    <td class="text-center">{{ $key + 1 }}</td>
                                                    <td class="text-center">{{ $karyawan_operator->nik }}</td>
                                                    <td class="text-left">{{ $biodata_karyawan->nama }}</td>
                                                    <td class="text-center">{{ $karyawan_operator->status == 'Y' ? 'Aktif' : 'Tidak Aktif' }}</td>
                                                    <td>
                                                        @if ($new_data_pengerjaan->status == 'n')
                                                            <input type="checkbox" disabled checked class="form-check-input"
                                                                id="">
                                                        @else
                                                            @if (empty($checklist_karyawan))
                                                            <input type="hidden"
                                                                name="pegawai_{{ $jenis_operator_detail_pekerjaan->id }}"
                                                                value="{{ $jenis_operator_detail_pekerjaan->id }}"
                                                                id="">
                                                            {{-- <input type="hidden"
                                                                value="{{ $karyawan_operator->id }}"
                                                                id=""> --}}
                                                            <input type="checkbox"
                                                                name="checkbox_{{ $jenis_operator_detail_pekerjaan->id }}[]"
                                                                value="{{ $karyawan_operator->id }}"
                                                                class="form-check-input" id="">
                                                            @else
                                                            <input type="checkbox"
                                                                class="form-check-input" disabled checked id="">
                                                            @endif
                                                        @endif
                                                    </td>
                                                </tr>
                                            @endforeach
                                            @if ($new_data_pengerjaan->status == 'y')
                                                <tr>
                                                    {{-- {{ dd($jenis_pekerja_id) }} --}}
                                                    <td class="text-center" colspan="4"><button type="button"
                                                            class="btn btn-primary btn-md"
                                                            onclick="window.open('{{ route('pengerjaan.popup_tambah_pegawai', ['id' => $id, 'kode_pengerjaan' => $kode_pengerjaan, 'jenis_pekerja_id' => $jenis_pekerja_id[0]]) }}','','width=1200,height=555,left=80,top=80,location=yes, menubar=no, status=no,toolbar=no, scrollbars=yes, resizable=yes')"><i
                                                                class="fas fa-plus"></i> Tambah</button></td>
                                                </tr>
                                            @endif
                                        </tbody>
                                    </table>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('script')
<script src="{{ URL::asset('public/assets/js/iziToast.min.js') }}"></script>
    <script>
        function toggle_1(source) {
            checkboxes = document.getElementsByName('checkbox_1[]');
            for (var i = 0, n = checkboxes.length; i < n; i++) {
                checkboxes[i].checked = source.checked;
            }
        }

        function toggle_2(source) {
            checkboxes = document.getElementsByName('checkbox_2[]');
            for (var i = 0, n = checkboxes.length; i < n; i++) {
                checkboxes[i].checked = source.checked;
            }
        }

        function toggle_3(source) {
            checkboxes = document.getElementsByName('checkbox_3[]');
            for (var i = 0, n = checkboxes.length; i < n; i++) {
                checkboxes[i].checked = source.checked;
            }
        }

        function toggle_4(source) {
            checkboxes = document.getElementsByName('checkbox_4[]');
            for (var i = 0, n = checkboxes.length; i < n; i++) {
                checkboxes[i].checked = source.checked;
            }
        }

        function toggle_5(source) {
            checkboxes = document.getElementsByName('checkbox_5[]');
            for (var i = 0, n = checkboxes.length; i < n; i++) {
                checkboxes[i].checked = source.checked;
            }
        }

        function toggle_6(source) {
            checkboxes = document.getElementsByName('checkbox_6[]');
            for (var i = 0, n = checkboxes.length; i < n; i++) {
                checkboxes[i].checked = source.checked;
            }
        }

        function toggle_7(source) {
            checkboxes = document.getElementsByName('checkbox_7[]');
            for (var i = 0, n = checkboxes.length; i < n; i++) {
                checkboxes[i].checked = source.checked;
            }
        }

        function toggle_8(source) {
            checkboxes = document.getElementsByName('checkbox_8[]');
            for (var i = 0, n = checkboxes.length; i < n; i++) {
                checkboxes[i].checked = source.checked;
            }
        }

        function toggle_9(source) {
            checkboxes = document.getElementsByName('checkbox_9[]');
            for (var i = 0, n = checkboxes.length; i < n; i++) {
                checkboxes[i].checked = source.checked;
            }
        }

        function toggle_10(source) {
            checkboxes = document.getElementsByName('checkbox_10[]');
            for (var i = 0, n = checkboxes.length; i < n; i++) {
                checkboxes[i].checked = source.checked;
            }
        }

        function toggle_11(source) {
            checkboxes = document.getElementsByName('checkbox_11[]');
            for (var i = 0, n = checkboxes.length; i < n; i++) {
                checkboxes[i].checked = source.checked;
            }
        }
    </script>
    <script>
        $('#form-simpan').submit(function(e) {
            e.preventDefault();
            let formData = new FormData(this);
            $('#image-input-error').text('');
            $.ajax({
                type: 'POST',
                url: "{{ route('pengerjaan.karyawan.simpan', ['kode_pengerjaan' => $kode_pengerjaan, 'id' => $id, 'kode_payrol' => $kode_payrol]) }}",
                data: formData,
                contentType: false,
                processData: false,
                beforeSend: function() {
                    $('.modalLoading').modal('show');
                },
                success: (result) => {
                    if (result.success != false) {
                        iziToast.success({
                            title: result.success,
                            message: result.message_title
                        });
                        setTimeout(() => {
                            $('.modalLoading').modal('hide');
                            window.location.href = "{{ route('pengerjaan.hasil_kerja') }}";
                        }, 3000);
                    } else {}
                },
                error: function(request, status, error) {
                    iziToast.error({
                        title: 'Error',
                        message: error,
                    });
                }
            });
        });
    </script>
@endsection
