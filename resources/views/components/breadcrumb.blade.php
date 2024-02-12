<div class="row">
    <div class="col-sm-12">
        <div class="page-title-box">
            <div class="row">
                <div class="col">
                    <h4 class="page-title">{{ $title }}</h4>
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="javascript:void(0);">{{ $li_1 }}</a></li>
                        @if(isset($li_2))
                            <li class="breadcrumb-item"><a href="javascript:void(0);">{{ $li_2 }}</a></li>
                        @endif
                        @if(isset($li_3))
                            <li class="breadcrumb-item active">{{ $li_3 }}</li>
                        @endif
                    </ol>
                </div><!--end col-->
                {{-- <div class="col-auto align-self-center">
                    <a href="#" class="btn btn-sm btn-outline-primary" id="Dash_Date">
                        <span class="ay-name" id="Day_Name">Today:</span>&nbsp;
                        <span class="" id="Select_date">Jan 11</span>
                        <i data-feather="calendar" class="align-self-center icon-xs ms-1"></i>
                    </a>
                    <a href="#" class="btn btn-sm btn-outline-primary">
                        <i data-feather="download" class="align-self-center icon-xs"></i>
                    </a>
                </div> --}}
            </div>
        </div>
    </div>
</div>

