@extends('layouts.backend.app')
@section('content')
    @component('components.breadcrumb')
        @slot('li_1')
            Dashboard
        @endslot
        @slot('li_3')
            @yield('title')
        @endslot
        @slot('title')
            @yield('title')
        @endslot
    @endcomponent
    <div class="row">
        <div class="col-md-12">
            <form action="{{ route('test_import_excel_borongan_simpan') }}" method="post" enctype="multipart/form-data">
                @csrf
                <div class="mb-3">
                    <a href="{{ route('test_export_excel_borongan_download') }}" class="btn btn-success">Download Template</a>
                </div>
                <div class="mb-3">
                    <label for="">Upload File Pengerjaan Borongan</label>
                    <input type="file" name="upload_file_pengerjaan_borongan" class="form-control" id="">
                </div>
                <button type="submit" class="btn btn-success">Submit</button>
            </form>
        </div>
    </div>
@endsection
@section('script')
    
@endsection