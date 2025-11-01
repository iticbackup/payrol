<div class="modal fade modalEdit" id="exampleModalCenter" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title m-0" id="exampleModalCenterTitle">Edit Data Cut Off</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="form-update" method="post">
                @csrf
                @php
                    $currentYear = date('Y');
                    $startYear = $currentYear - 5;
                    $endYear = $currentYear + 5;

                @endphp
                <input type="hidden" name="id" id="edit_id">
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-3">
                        <div class="mb-3">
                            <label class="form-label">Periode</label>
                            <input type="text" name="periode" class="form-control" placeholder="Periode" id="edit_periode">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="mb-3">
                            <label class="form-label">Pertanggal</label>
                            <select name="tanggal" class="form-control" id="edit_tanggal">
                                <option value="">-- Pilih Pertanggal --</option>
                                @for ($i = 1; $i <= 31; $i++)
                                    <option value="{{ $i }}">{{ $i }}</option>
                                @endfor
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="mb-3">
                            <label class="form-label">Tahun</label>
                            <select name="tahun" class="form-control" id="edit_tahun">
                                <option value="">-- Pilih Periode --</option>
                                @for ($i = $startYear; $i <= $endYear; $i++)
                                    <option value="{{ $i }}">{{ $i }}</option>
                                @endfor
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="mb-3">
                            <label class="form-label">Status</label>
                            <select name="status" class="form-control" id="edit_status">
                                <option value="">-- Pilih Status --</option>
                                <option value="Y">Aktif</option>
                                <option value="T">Tidak Aktif</option>
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