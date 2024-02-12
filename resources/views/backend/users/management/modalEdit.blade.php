<div class="modal fade modalEdit" id="exampleModalCenter" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title m-0" id="exampleModalCenterTitle">Edit User Management</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="form-update" method="post">
                @csrf
                <input type="hidden" name="edit_id" id="edit_id">
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label" for="exampleInputPassword1">User</label>
                    <select class="form-control" name="edit_user_id" id="edit_user_id">
                        <option>-- Pilih User --</option>
                        @foreach ($users as $user)
                            <option value="{{ $user->id }}">{{ $user->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label">Akses</label>
                    <div class="checkbox checkbox-success">
                        <input id="checkbox5" type="checkbox" name="edit_c" value="Y" class="edit_c">
                        <label for="checkbox5">
                            Create
                        </label>
                        <input id="checkbox6" type="checkbox" name="edit_r" value="Y" class="edit_r">
                        <label for="checkbox6">
                            Read
                        </label>
                        <input id="checkbox7" type="checkbox" name="edit_u" value="Y" class="edit_u">
                        <label for="checkbox7">
                            Update
                        </label>
                        <input id="checkbox8" type="checkbox" name="edit_d" value="Y" class="edit_d">
                        <label for="checkbox8">
                            Delete
                        </label>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-soft-primary btn-sm">Update</button>
                <button type="button" class="btn btn-soft-secondary btn-sm" data-bs-dismiss="modal">Close</button>
            </div>
            </form>
        </div>
    </div>
</div>