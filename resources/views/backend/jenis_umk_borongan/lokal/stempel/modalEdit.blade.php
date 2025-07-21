<div class="modal fade modalEditStempel" id="exampleModalCenterEditStempel" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title m-0" id="exampleModalCenterTitle">Edit Data UMK Borongan Stempel</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="form-update-stempel" method="post">
                @csrf
                <input type="hidden" name="edit_stempel_id" id="edit_stempel_id">
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label class="form-label">Jenis Produk</label>
                            <input type="text" class="form-control" name="edit_stempel_jenis_produk" id="edit_stempel_jenis_produk" placeholder="Jenis Produk">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label class="form-label">Nominal UMK</label>
                            <input type="text" class="form-control" name="edit_stempel_nominal_umk" id="edit_stempel_nominal_umk" placeholder="Nominal UMK">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label class="form-label">Target Pengerjaan</label>
                            <input type="number" class="form-control" name="edit_stempel_target_pengerjaan" id="edit_stempel_target_pengerjaan" placeholder="Target Pengerjaan">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label class="form-label">Tahun Aktif</label>
                            <input type="number" class="form-control" name="edit_stempel_tahun_aktif" min="0" id="edit_stempel_tahun_aktif" placeholder="Tahun Aktif">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label class="form-label">Status</label>
                            <select name="edit_stempel_status" class="form-control" id="edit_stempel_status">
                                <option>-- Pilih Status --</option>
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
