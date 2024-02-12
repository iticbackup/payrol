<div class="modal fade modalEdit" id="exampleModalCenter" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title m-0" id="exampleModalCenterTitle">Buat Data Karyawan Operator Supir RIT</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="form-update" method="post">
                @csrf
                <input type="hidden" name="edit_id" id="edit_id">
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label class="form-label">NIK</label>
                            <input type="text" class="form-control buat_nik_karyawan edit_nik" name="edit_nik" placeholder="NIK Karyawan">
                        </div>
                    </div>
                    <div class="col-md-5">
                        <div class="mb-3">
                            <label class="form-label">Nama Karyawan</label>
                            <input type="text" class="form-control buat_nama_karyawan" name="edit_nama_karyawan" placeholder="Nama Karyawan" readonly>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="mb-3">
                            <label class="form-label">Upah Dasar</label>
                            <input type="text" class="form-control" name="edit_upah_dasar" id="edit_upah_dasar" placeholder="Upah Dasar">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="mb-3">
                            <label class="form-label">RIT Posisi</label>
                            <select name="edit_rit_posisi_id" class="form-control" id="edit_rit_posisi_id">
                                <option>-- Pilih Posisi --</option>
                                @foreach ($rit_posisis as $rit_posisi)
                                    <option value="{{ $rit_posisi->id }}">{{ $rit_posisi->kode_posisi.' - '.$rit_posisi->nama_posisi }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="mb-3">
                            <label class="form-label">Gol.Tunj.Kerja</label>
                            <select name="edit_tunjangan_kerja_id" class="form-control" id="edit_tunjangan_kerja_id">
                                <option value="">-- Pilih Golongan Tunjangan Kerja --</option>
                                @foreach ($tunjangan_kerjas as $tunjangan_kerja)
                                    <option value="{{ $tunjangan_kerja->id }}">{{ $tunjangan_kerja->golongan.' - Rp. '.number_format($tunjangan_kerja->nominal,0,',','.') }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="mb-3">
                            <label class="form-label">JHT</label>
                            <select name="edit_jht" class="form-control" id="edit_jht">
                                <option>-- Pilih JHT --</option>
                                <option value="y">Ya</option>
                                <option value="n">Tidak</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="mb-3">
                            <label class="form-label">BPJS</label>
                            <select name="edit_bpjs" class="form-control" id="edit_bpjs">
                                <option>-- Pilih BPJS --</option>
                                <option value="y">Ya</option>
                                <option value="n">Tidak</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">Status</label>
                            <select name="edit_status" class="form-control" id="edit_status">
                                <option>-- Pilih Status --</option>
                                <option value="y">Aktif</option>
                                <option value="n">Tidak Aktif</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="form-label">No. Rekening</label>
                            <input type="text" class="form-control" name="edit_rekening" id="edit_rekening" placeholder="No.Rekening">
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-soft-primary btn-sm">Submit</button>
                <button type="button" class="btn btn-soft-secondary btn-sm" data-bs-dismiss="modal">Close</button>
            </div>
            </form>
        </div>
    </div>
</div>
