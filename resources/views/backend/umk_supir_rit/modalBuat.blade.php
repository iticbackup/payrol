<div class="modal fade modalBuat" id="exampleModalCenter" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title m-0" id="exampleModalCenterTitle">Buat UMK RIT</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="form-simpan" method="post" enctype="multipart/form-data">
                @csrf
                @php
                    $currentYear = date('Y');
                    $startYear = $currentYear - 5;
                    $endYear = $currentYear + 5;
                @endphp
                <div class="modal-body">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th class="text-center">No</th>
                                <th class="text-center">Kategori Upah</th>
                                <th class="text-center">RIT Posisi</th>
                                <th class="text-center">RIT Kendaraan</th>
                                <th class="text-center">RIT Tujuan</th>
                                <th class="text-center">Tarif</th>
                                <th class="text-center">Tahun Aktif</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($rit_umks as $key => $item)
                                <tr>
                                    @switch($item->rit_posisi_id)
                                        @case(1)
                                            <td class="text-center">
                                                {{ $key+1 }}
                                                <input type="hidden" name="no[]" value="{{ $key }}" id="">
                                            </td>
                                            <td>
                                                <input type="text" name="kategori_upah[]" class="form-control" style="background-color: #A3DC9A; color: black" value="{{ $item->kategori_upah }}">
                                                {{-- {{ $item->kategori_upah }} --}}
                                            </td>
                                            <td>
                                                <select name="rit_posisi_id[]" class="form-control" id="">
                                                    <option value="">-- Pilih Rit Posisi --</option>
                                                    <option value="0">Tidak Ada</option>
                                                    @foreach ($rit_posisis as $rit_posisi)
                                                    <option value="{{ $rit_posisi->id }}" {{ $rit_posisi->id == $item->rit_posisi_id ? 'selected' : null }}>{{ $rit_posisi->nama_posisi }}</option>
                                                    @endforeach
                                                </select>
                                            </td>
                                            <td>
                                                <select name="rit_kendaraan_id[]" class="form-control" id="">
                                                    <option value="">-- Pilih Rit Kendaraan --</option>
                                                    <option value="0">Tidak Ada</option>
                                                    @foreach ($rit_kendaraans as $rit_kendaraan)
                                                    <option value="{{ $rit_kendaraan->id }}" {{ $rit_kendaraan->id == $item->rit_kendaraan_id ? 'selected' : null }}>{{ $rit_kendaraan->jenis_kendaraan }}</option>
                                                    @endforeach
                                                </select>
                                            </td>
                                            <td>
                                                <select name="rit_tujuan_id[]" class="form-control" id="">
                                                    <option value="0">-- Pilih Rit Tujuan --</option>
                                                    <option value="0">Tidak Ada</option>
                                                    @foreach ($rit_tujuans as $rit_tujuan)
                                                    <option value="{{ $rit_tujuan->id }}" {{ $rit_tujuan->id == $item->rit_tujuan_id ? 'selected' : null }}>{{ $rit_tujuan->kode_tujuan.' - '.$rit_tujuan->tujuan }}</option>
                                                    @endforeach
                                                </select>
                                            </td>
                                            <td>
                                                <input type="text" name="tarif[]" class="form-control" style="background-color: #A3DC9A; color: black" placeholder="Tarif" id="">
                                            </td>
                                            <td>
                                                <select name="tahun_aktif[]" class="form-control" id="">
                                                    <option value="">-- Pilih Tahun --</option>
                                                    @for ($i = $startYear; $i <= $endYear; $i++)
                                                        <option value="{{ $i }}" {{ $i == date('Y') ? 'selected' : null }}>{{ $i }}</option>
                                                    @endfor
                                                </select>
                                            </td>
                                            @break
                                        @case(2)
                                            <td class="text-center">
                                                {{ $key+1 }}
                                                <input type="hidden" name="no[]" value="{{ $key }}" id="">
                                            </td>
                                            <td>
                                                <input type="text" name="kategori_upah[]" class="form-control" style="background-color: #FFD93D; color: black" value="{{ $item->kategori_upah }}">
                                                {{-- {{ $item->kategori_upah }} --}}
                                            </td>
                                            <td>
                                                <select name="rit_posisi_id[]" class="form-control" id="">
                                                    <option value="">-- Pilih Rit Posisi --</option>
                                                    <option value="0">Tidak Ada</option>
                                                    @foreach ($rit_posisis as $rit_posisi)
                                                    <option value="{{ $rit_posisi->id }}" {{ $rit_posisi->id == $item->rit_posisi_id ? 'selected' : null }}>{{ $rit_posisi->nama_posisi }}</option>
                                                    @endforeach
                                                </select>
                                            </td>
                                            <td>
                                                <select name="rit_kendaraan_id[]" class="form-control" id="">
                                                    <option value="">-- Pilih Rit Kendaraan --</option>
                                                    <option value="0">Tidak Ada</option>
                                                    @foreach ($rit_kendaraans as $rit_kendaraan)
                                                    <option value="{{ $rit_kendaraan->id }}" {{ $rit_kendaraan->id == $item->rit_kendaraan_id ? 'selected' : null }}>{{ $rit_kendaraan->jenis_kendaraan }}</option>
                                                    @endforeach
                                                </select>
                                            </td>
                                            <td>
                                                <select name="rit_tujuan_id[]" class="form-control" id="">
                                                    <option value="0">-- Pilih Rit Tujuan --</option>
                                                    <option value="0">Tidak Ada</option>
                                                    @foreach ($rit_tujuans as $rit_tujuan)
                                                    <option value="{{ $rit_tujuan->id }}" {{ $rit_tujuan->id == $item->rit_tujuan_id ? 'selected' : null }}>{{ $rit_tujuan->kode_tujuan.' - '.$rit_tujuan->tujuan }}</option>
                                                    @endforeach
                                                </select>
                                            </td>
                                            <td>
                                                <input type="text" name="tarif[]" class="form-control" placeholder="Tarif" style="background-color: #FFD93D; color: black" id="">
                                            </td>
                                            <td>
                                                <select name="tahun_aktif[]" class="form-control" id="">
                                                    <option value="">-- Pilih Tahun --</option>
                                                    @for ($i = $startYear; $i <= $endYear; $i++)
                                                        <option value="{{ $i }}" {{ $i == date('Y') ? 'selected' : null }}>{{ $i }}</option>
                                                    @endfor
                                                </select>
                                            </td>
                                            @break
                                        @default
                                            
                                    @endswitch
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-soft-primary btn-sm">Submit</button>
                    <button type="button" class="btn btn-soft-secondary btn-sm" data-bs-dismiss="modal">Close</button>
                </div>
            </form>
        </div>
    </div>
</div>
