@extends('include.app')
@section('script')
<script src="{{ asset('assets/script/dashboard.js') }}"></script>
 {{-- <script src="assets/js/pages/demo.apex-area.js"></script> --}}
@endsection
@section('content')

<div class="col-12">
    <div class="card">
        <div class="card-header border-bottom d-flex align-items-center">
            <h4 class="header-title me-auto">{{__('Analytics')}}</h4>
            <select class="picker me-1" name="month" id="months">
                <option value="01">January</option>
                <option value="02">February</option>
                <option value="03">March</option>
                <option value="04">April</option>
                <option value="05">May</option>
                <option value="06">June</option>
                <option value="07">July</option>
                <option value="08">August</option>
                <option value="09">September</option>
                <option value="10">October</option>
                <option value="11">November</option>
                <option value="12">December</option>
            </select>
            <select class="picker" name="year" id="years">
                <option value="2024">2024</option>
                <option value="2025">2025</option>
                <option value="2026">2026</option>
                <option value="2027">2027</option>
            </select>
        </div>
        <div class="card-body">

            <div dir="ltr">
                <div id="chart-dashboard" class="apex-charts" data-colors="#B754F9,#0acf97"></div>
            </div>
        </div>
        <!-- end card body-->
    </div>
    <!-- end card -->
</div>

<div class="row">
    <div class="col-md-3">
        <div class="card">
            <div class="card-body">
                <div class="tilebox-one">
                    <i class='uil uil-users-alt float-end'></i>
                </div>
                <h5 class="text-uppercase mt-0 d-flex align-items-center ">
                    {{__('Total')}} {{__('Regisreted Users')}}
                    <a href="{{route('users')}}" class="d-flex align-items-center justify-content-center fs-4 ms-1 bg-primary-lighten badge text-primary rounded-circle w-h-30">
                        <i class="uil-link-h"></i>
                    </a>
                </h5>
                <h2 class="my-2" id="active-users-count">{{$userTotal}}</h2>
                    <span class="text-muted badge border">
                        <span class="text-danger fw-medium me-1 fs-6 ">{{$userFreezed}}</span>
                        <span class="text-nowrap fw-medium fs-6">{{__('Freezed')}}</span>
                    </span>
                    <span class="text-muted badge border">
                        <span class="text-success fw-medium me-1 fs-6 ">{{$userModerator}}</span>
                        <span class="text-nowrap fw-medium fs-6">{{__('Moderators')}}</span>
                    </span>
                    <span class="text-muted badge border">
                        <span class="text-success fw-medium me-1 fs-6 ">{{$userDummy}}</span>
                        <span class="text-nowrap fw-medium fs-6">{{__('Dummy')}}</span>
                    </span>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card">
            <div class="card-body">
                <div class="tilebox-one">
                    <i class='uil-shutter float-end'></i>
                </div>
                <h5 class="text-uppercase mt-0 d-flex align-items-center ">
                   {{__('Posts')}}
                    <a href="{{route('posts')}}" class="d-flex align-items-center justify-content-center fs-4 ms-1 bg-primary-lighten badge text-primary rounded-circle w-h-30">
                        <i class="uil-link-h"></i>
                    </a>
                </h5>
                <h2 class="my-2" id="active-users-count">{{$postsTotal}}</h2>
                    <span class="text-muted badge border">
                        <span class="text-success fw-medium me-1 fs-6 ">{{$postsReels}}</span>
                        <span class="text-nowrap fw-medium fs-6">{{__('Reels')}}</span>
                    </span>
                    <span class="text-muted badge border">
                        <span class="text-success fw-medium me-1 fs-6 ">{{$postsVideos}}</span>
                        <span class="text-nowrap fw-medium fs-6">{{__('Videos')}}</span>
                    </span>
                    <span class="text-muted badge border">
                        <span class="text-success fw-medium me-1 fs-6 ">{{$postsImages}}</span>
                        <span class="text-nowrap fw-medium fs-6">{{__('Image')}}</span>
                    </span>
                    <span class="text-muted badge border">
                        <span class="text-success fw-medium me-1 fs-6 ">{{$postsText}}</span>
                        <span class="text-nowrap fw-medium fs-6">{{__('Text')}}</span>
                    </span>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card">
            <div class="card-body">
                <div class="tilebox-one">
                    <i class='uil-exclamation-triangle float-end'></i>
                </div>
                <h5 class="text-uppercase mt-0 d-flex align-items-center ">
                   {{__('Reports')}}
                    <a href="{{route('reports')}}" class="d-flex align-items-center justify-content-center fs-4 ms-1 bg-primary-lighten badge text-primary rounded-circle w-h-30">
                        <i class="uil-link-h"></i>
                    </a>
                </h5>
                <h2 class="my-2" id="active-users-count">{{$reportsPost + $reportsUser}}</h2>
                    <span class="text-muted badge border">
                        <span class="text-success fw-medium me-1 fs-6 ">{{$reportsUser}}</span>
                        <span class="text-nowrap fw-medium fs-6">{{__('User Reports')}}</span>
                    </span>
                    <span class="text-muted badge border">
                        <span class="text-success fw-medium me-1 fs-6 ">{{$reportsPost}}</span>
                        <span class="text-nowrap fw-medium fs-6">{{__('Post Reports')}}</span>
                    </span>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card">
            <div class="card-body">
                <div class="tilebox-one">
                    <i class='uil-money-withdraw float-end'></i>
                </div>
                <h5 class="text-uppercase mt-0 d-flex align-items-center ">
                   {{__('Withdrawals')}}
                    <a href="{{route('withdrawals')}}" class="d-flex align-items-center justify-content-center fs-4 ms-1 bg-primary-lighten badge text-primary rounded-circle w-h-30">
                        <i class="uil-link-h"></i>
                    </a>
                </h5>
                <h2 class="my-2" id="active-users-count">{{$withdrawalPending + $withdrawalCompleted + $withdrawalRejected}}</h2>
                    <span class="text-muted badge border">
                        <span class="text-success fw-medium me-1 fs-6 ">{{$withdrawalPending}}</span>
                        <span class="text-nowrap fw-medium fs-6">{{__('Pending')}}</span>
                    </span>
                    <span class="text-muted badge border">
                        <span class="text-success fw-medium me-1 fs-6 ">{{$withdrawalCompleted}}</span>
                        <span class="text-nowrap fw-medium fs-6">{{__('Completed')}}</span>
                    </span>
                    <span class="text-muted badge border">
                        <span class="text-success fw-medium me-1 fs-6 ">{{$withdrawalRejected}}</span>
                        <span class="text-nowrap fw-medium fs-6">{{__('Rejected')}}</span>
                    </span>
            </div>
        </div>
    </div>
</div>


@endsection
