<div class="modal fade modalBuat" id="exampleModalCenter" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title m-0" id="exampleModalCenterTitle">Close Periode</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            {{-- <form id="form-simpan" method="post">
                @csrf --}}
            <div class="modal-body">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th class="text-center">Kode Pengerjaan</th>
                            <th class="text-center">Periode Pengerjaan</th>
                            <th class="text-center">Status</th>
                        </tr>
                    </thead>
                    <tbody id="data_periode"></tbody>
                </table>
                {{-- <div class="mb-3">
                    <label class="form-label">Username</label>
                    <input type="text" name="username" class="form-control" placeholder="Username">
                </div>
                <div class="mb-3">
                    <label class="form-label">Nama</label>
                    <input type="text" name="name" class="form-control" placeholder="Nama">
                </div> --}}
            </div>
            <div class="modal-footer">
                <button onclick="periode_submit()" class="btn btn-soft-primary btn-sm">Submit</button>
                <button type="button" class="btn btn-soft-secondary btn-sm" data-bs-dismiss="modal">Close</button>
            </div>
            {{-- </form> --}}
        </div>
    </div>
</div>