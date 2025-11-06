<div class="modal fade modalBuat" id="exampleModalCenterBuat" tabindex="-1" role="dialog"
    aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title m-0" id="exampleModalCenterTitle">Buat Data UMK Borongan Lokal</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="form-simpan" method="post">
                @csrf
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label class="form-label">Jenis Produk</label>
                                <input type="text" class="form-control" name="jenis_produk"
                                    placeholder="Jenis Produk">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="mb-3">
                                <div class="form-label fw-bold">Packing</div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <label class="form-label">UMK Packing</label>
                                        <input type="text" class="form-control" name="umk_packing"
                                            placeholder="UMK Packing">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Target Packing</label>
                                        <input type="text" class="form-control" name="target_packing"
                                            placeholder="Target Packing">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Bandrol</label>
                                <div class="row">
                                    <div class="col-md-6">
                                        <label class="form-label">UMK Bandrol</label>
                                        <input type="text" class="form-control" name="umk_bandrol"
                                            placeholder="UMK Bandrol">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Target bandrol</label>
                                        <input type="text" class="form-control" name="target_bandrol"
                                            placeholder="Target Bandrol">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Inner</label>
                                <div class="row">
                                    <div class="col-md-6">
                                        <label class="form-label">UMK Inner</label>
                                        <input type="text" class="form-control" name="umk_inner" placeholder="UMK Inner">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Target Inner</label>
                                        <input type="text" class="form-control" name="target_inner"
                                            placeholder="Target Inner">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Outer</label>
                                <div class="row">
                                    <div class="col-md-6">
                                        <label class="form-label">UMK Outer</label>
                                        <input type="text" class="form-control" name="umk_outer" placeholder="UMK Outer">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Target Outer</label>
                                        <input type="text" class="form-control" name="target_outer"
                                            placeholder="Target Outer">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Tahun Aktif</label>
                                <input type="text" class="form-control" name="tahun_aktif" placeholder="Tahun Aktif">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Status</label>
                                <select name="status" class="form-control" id="">
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
