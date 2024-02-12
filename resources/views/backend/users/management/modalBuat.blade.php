<div class="modal fade modalBuat" id="exampleModalCenter" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title m-0" id="exampleModalCenterTitle">Buat User Management</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="form-simpan" method="post">
                @csrf
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label" for="exampleInputPassword1">User</label>
                    <select class="form-control" name="user_id">
                        <option>-- Pilih User --</option>
                        @foreach ($users as $user)
                            <option value="{{ $user->id }}">{{ $user->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="row">
                    <div class="col-md-3">
                        <div class="card">
                            <div class="card-body">
                                <div class="pricingTable1 text-center">
                                    <h6 class="pt-3 pb-2 m-0">Administrator</h6>
                                    <hr class="hr-dashed">
                                    <ul class="list-unstyled text-left py-3 border-0 mb-0">
                                        <li><i class="dripicons-checkmark"></i> Create</li>
                                        <li><i class="dripicons-checkmark"></i> Read</li>
                                        <li><i class="dripicons-checkmark"></i> Update</li>
                                        <li><i class="dripicons-checkmark"></i> Delete</li>
                                    </ul>
                                    <input type="radio" class="btn-check" name="options_akses" id="administrator" value="administrator" autocomplete="off">
                                    <label class="btn btn-outline-success btn-sm" for="administrator">Check</label>
                                    {{-- <a href="#" class="btn btn-dark py-2 px-5 font-16"><span>Sign up</span></a> --}}
                                </div>
                            </div>
                        </div> 
                    </div>
                    <div class="col-md-3">
                        <div class="card">
                            <div class="card-body">
                                <div class="pricingTable1 text-center">
                                    <h6 class="pt-3 pb-2 m-0">Admin</h6>
                                    <hr class="hr-dashed">
                                    <ul class="list-unstyled text-left py-3 border-0 mb-0">
                                        <li><i class="dripicons-checkmark"></i> Create</li>
                                        <li><i class="dripicons-checkmark"></i> Read</li>
                                        <li><i class="dripicons-checkmark"></i> Update</li>
                                        <li><i class="dripicons-cross"></i> Delete</li>
                                    </ul>
                                    <input type="radio" class="btn-check" name="options_akses" id="admin" value="admin" autocomplete="off">
                                    <label class="btn btn-outline-success btn-sm" for="admin">Check</label>
                                    {{-- <a href="#" class="btn btn-dark py-2 px-5 font-16"><span>Sign up</span></a> --}}
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card">
                            <div class="card-body">
                                <div class="pricingTable1 text-center">
                                    <h6 class="pt-3 pb-2 m-0">User</h6>
                                    <hr class="hr-dashed">
                                    <ul class="list-unstyled text-left py-3 border-0 mb-0">
                                        <li><i class="dripicons-cross"></i> Create</li>
                                        <li><i class="dripicons-checkmark"></i> Read</li>
                                        <li><i class="dripicons-cross"></i> Update</li>
                                        <li><i class="dripicons-cross"></i> Delete</li>
                                    </ul>
                                    <input type="radio" class="btn-check" name="options_akses" id="user" value="user" autocomplete="off">
                                    <label class="btn btn-outline-success btn-sm" for="user">Check</label>
                                    {{-- <a href="#" class="btn btn-dark py-2 px-5 font-16"><span>Sign up</span></a> --}}
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card">
                            <div class="card-body">
                                <div class="pricingTable1 text-center">
                                    <h6 class="pt-3 pb-2 m-0">Custom</h6>
                                    <hr class="hr-dashed">
                                    <div class="checkbox checkbox-success">
                                        <input id="checkbox1" type="checkbox" name="c" value="Y">
                                        <label for="checkbox1">
                                            Create
                                        </label>
                                        <input id="checkbox2" type="checkbox" name="r" value="Y">
                                        <label for="checkbox2">
                                            Read
                                        </label>
                                        <input id="checkbox3" type="checkbox" name="u" value="Y">
                                        <label for="checkbox3">
                                            Update
                                        </label>
                                        <input id="checkbox4" type="checkbox" name="d" value="Y">
                                        <label for="checkbox4">
                                            Delete
                                        </label>
                                    </div>
                                    <input type="radio" class="btn-check" name="options_akses" id="custom" value="custom" autocomplete="off">
                                    <label class="btn btn-outline-success btn-sm" for="custom">Check</label>
                                    {{-- <a href="#" class="btn btn-dark py-2 px-5 font-16"><span>Sign up</span></a> --}}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                {{-- <div class="mb-3">
                    <label class="form-label">Akses</label>
                    <div class="checkbox checkbox-success">
                        <input id="checkbox1" type="checkbox" name="c" value="Y">
                        <label for="checkbox1">
                            Create
                        </label>
                        <input id="checkbox2" type="checkbox" name="r" value="Y">
                        <label for="checkbox2">
                            Read
                        </label>
                        <input id="checkbox3" type="checkbox" name="u" value="Y">
                        <label for="checkbox3">
                            Update
                        </label>
                        <input id="checkbox4" type="checkbox" name="d" value="Y">
                        <label for="checkbox4">
                            Delete
                        </label>
                    </div>
                </div> --}}
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-soft-primary btn-sm">Submit</button>
                <button type="button" class="btn btn-soft-secondary btn-sm" data-bs-dismiss="modal">Close</button>
            </div>
            </form>
        </div>
    </div>
</div>