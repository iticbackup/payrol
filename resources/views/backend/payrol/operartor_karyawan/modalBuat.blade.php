<div class="modal fade modalBuat" id="exampleModalCenter" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title m-0" id="exampleModalCenterTitle">Buat Data Karyawan Operator</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="form-simpan" method="post">
                @csrf
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label class="form-label">NIK</label>
                            <input type="text" class="form-control buat_nik_karyawan" name="nik" placeholder="NIK Karyawan">
                        </div>
                    </div>
                    <div class="col-md-8">
                        <div class="mb-3">
                            <label class="form-label">Nama Karyawan</label>
                            <input type="text" class="form-control buat_nama_karyawan" name="nama_karyawan" placeholder="Nama Karyawan" readonly>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label class="form-label">Posisi</label>
                            <select name="jenis_operator_id" class="form-control posisi">
                                <option>-- Pilih Posisi --</option>
                                @foreach ($jenis_operators as $jenis_operator)
                                    <option value="{{ $jenis_operator->id }}">{{ $jenis_operator->jenis_operator }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label class="form-label">Posisi Pengerjaan</label>
                            <select name="jenis_operator_detail_id" class="form-control posisi_pengerjaan">
                                <option>-- Pilih Posisi Pengerjaan --</option>
                                {{-- @foreach ($jenis_operators as $jenis_operator)
                                    <option value="{{ $jenis_operator->id }}">{{ $jenis_operator->jenis_operator }}</option>
                                @endforeach --}}
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label class="form-label">Posisi Jenis Pengerjaan</label>
                            <select name="jenis_operator_detail_pekerjaan_id" class="form-control posisi_pekerjaan">
                                <option>-- Pilih Posisi Jenis Pengerjaan --</option>
                                {{-- @foreach ($jenis_operators as $jenis_operator)
                                    <option value="{{ $jenis_operator->id }}">{{ $jenis_operator->jenis_operator }}</option>
                                @endforeach --}}
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label class="form-label">JHT</label>
                            <select name="jht" class="form-control" id="">
                                <option>-- Pilih JHT --</option>
                                <option value="Y">Ya</option>
                                <option value="T">Tidak</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label class="form-label">BPJS</label>
                            <select name="bpjs" class="form-control" id="">
                                <option>-- Pilih BPJS --</option>
                                <option value="Y">Ya</option>
                                <option value="T">Tidak</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label class="form-label">Training</label>
                            <select name="training" class="form-control" id="">
                                <option>-- Pilih Training --</option>
                                <option value="Y">Ya</option>
                                <option value="T">Tidak</option>
                            </select>
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
