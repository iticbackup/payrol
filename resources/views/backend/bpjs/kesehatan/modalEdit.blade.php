<div class="modal fade modalEdit" id="exampleModalCenter" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title m-0" id="exampleModalCenterTitle">Edit Data BPJS Kesehatan</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="form-update" method="post" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="edit_id" id="edit_id">
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <div class="mb-3">
                            <label class="form-label">Keterangan</label>
                            <input type="text" class="form-control" name="edit_keterangan" id="edit_keterangan" placeholder="Keterangan">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Nominal</label>
                            <input type="text" class="form-control" name="edit_nominal" id="edit_nominal" placeholder="Nominal">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Tahun Aktif</label>
                            <input type="text" class="form-control" name="edit_tahun" id="edit_tahun" placeholder="Tahun">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Status</label>
                            <select name="edit_status" class="form-control" id="edit_status">
                                <option value="">-- Pilih Status --</option>
                                <option value="y">Ya</option>
                                <option value="n">Tidak</option>
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
