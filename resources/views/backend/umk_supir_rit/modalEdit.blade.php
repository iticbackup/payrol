<div class="modal fade modalEdit" id="exampleModalCenter" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title m-0" id="exampleModalCenterTitle">Edit UMK RIT</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="form-update" method="post" enctype="multipart/form-data">
                @csrf
                @php
                    $currentYear = date('Y');
                    $startYear = $currentYear - 5;
                    $endYear = $currentYear + 5;
                @endphp
                <input type="hidden" name="id" id="edit_id">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="">Kategori Upah</label>
                                <input type="text" name="kategori_upah" class="form-control" placeholder="Kategori Upah" id="edit_kategori_upah">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="">Rit Posisi</label>
                                <select name="rit_posisi_id" class="form-control" id="edit_rit_posisi_id">
                                    <option value="">-- Pilih Rit Posisi --</option>
                                    <option value="0">Tidak Ada</option>
                                    @foreach ($rit_posisis as $rit_posisi)
                                    <option value="{{ $rit_posisi->id }}">{{ $rit_posisi->nama_posisi }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="">Rit Kendaraan</label>
                                <select name="rit_kendaraan_id" class="form-control" id="edit_rit_kendaraan_id">
                                    <option value="">-- Pilih Rit Kendaraan --</option>
                                    <option value="0">Tidak Ada</option>
                                    @foreach ($rit_kendaraans as $rit_kendaraan)
                                    <option value="{{ $rit_kendaraan->id }}">{{ $rit_kendaraan->jenis_kendaraan }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="">Rit Tujuan</label>
                                <select name="rit_tujuan_id" class="form-control" id="edit_rit_tujuan_id">
                                    <option value="0">-- Pilih Rit Tujuan --</option>
                                    <option value="0">Tidak Ada</option>
                                    @foreach ($rit_tujuans as $rit_tujuan)
                                    <option value="{{ $rit_tujuan->id }}">{{ $rit_tujuan->kode_tujuan.' - '.$rit_tujuan->tujuan }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <label for="">Tarif</label>
                            <input type="text" name="tarif" class="form-control" placeholder="Tarif" id="edit_tarif">
                        </div>
                        <div class="col-md-4">
                            <label for="">Tahun Aktif</label>
                            <select name="tahun_aktif" class="form-control" id="edit_tahun_aktif">
                                <option value="">-- Pilih Tahun --</option>
                                @for ($i = $startYear; $i <= $endYear; $i++)
                                    <option value="{{ $i }}" {{ $i == date('Y') ? 'selected' : null }}>{{ $i }}</option>
                                @endfor
                            </select>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="">Status</label>
                                <select name="status" class="form-control" id="edit_status">
                                    <option value="">-- Pilih Status --</option>
                                    <option value="y">Aktif</option>
                                    <option value="t">Non Aktif</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-success">Submit</button>
                </div>
            </form>
        </div>
    </div>
</div>