@extends('include.app')
@section('script')
<script src="{{ asset('assets/script/withdrawals.js') }}"></script>
@endsection
@section('content')

<div class="card">
    <div class="card-header d-flex align-items-center border-bottom">
        <h4 class="card-title mb-0 header-title">
            {{ __('Withdrawals')}}
        </h4>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-sm-3 mb-2 mb-sm-0 ">
                <div class="nav nav-pills" id="v-pills-tab" role="tablist" aria-orientation="vertical">
                    <a class="nav-link active show" id="v-pills-pending-tab" data-bs-toggle="pill" href="#v-pills-pending" role="tab" aria-controls="v-pills-pending"
                    aria-selected="false">
                    <span class="d-md-block">{{__('Pending')}}</span>
                     </a>
                    <a class="nav-link " id="v-pills-completed-tab" data-bs-toggle="pill" href="#v-pills-completed" role="tab" aria-controls="v-pills-completed"
                        aria-selected="true">
                        <span class="d-md-block">{{__('Completed')}}</span>
                    </a>
                    <a class="nav-link " id="v-pills-rejected-tab" data-bs-toggle="pill" href="#v-pills-rejected" role="tab" aria-controls="v-pills-rejected"
                        aria-selected="true">
                        <span class="d-md-block">{{__('Rejected')}}</span>
                    </a>

                </div>
            </div> <!-- end col-->

            <div class="col-sm-12">
                <div class="tab-content mt-3" id="v-pills-tabContent">
                        {{-- Pending --}}
                        <div class="tab-pane fade active show" id="v-pills-pending" role="tabpanel" aria-labelledby="v-pills-pending-tab">
                            <div class="table-responsive">
                                <table class="table table-centered table-hover w-100 dt-responsive nowrap mt-3" id="pendingWithdrawalsTable">
                                    <thead class="table-light">
                                        <tr>
                                            <th>{{ __('Request Number')}}</th>
                                            <th>{{ __('User')}}</th>
                                            <th>{{ __('Coin Details')}}</th>
                                            <th>{{ __('Payment Details')}}</th>
                                            <th>{{ __('Created Date')}}</th>
                                            <th style="width: 200px;" class="text-end">{{ __('Action')}}</th>
                                        </tr>
                                    </thead>
                                </table>
                            </div>
                        </div>
                    {{-- Completed --}}
                    <div class="tab-pane fade " id="v-pills-completed" role="tabpanel" aria-labelledby="v-pills-completed-tab">
                        <div class="table-responsive">
                            <table class="table table-centered table-hover w-100 dt-responsive nowrap mt-3" id="completedWithdrawalsTable">
                                <thead class="table-light">
                                    <tr>
                                        <th>{{ __('Request Number')}}</th>
                                        <th>{{ __('User')}}</th>
                                        <th>{{ __('Coin Details')}}</th>
                                        <th>{{ __('Payment Details')}}</th>
                                        <th>{{ __('Created Date')}}</th>
                                    </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                    {{-- Rejected --}}
                    <div class="tab-pane fade " id="v-pills-rejected" role="tabpanel" aria-labelledby="v-pills-rejected-tab">
                        <div class="table-responsive">
                            <table class="table table-centered table-hover w-100 dt-responsive nowrap mt-3" id="rejectedWithdrawalsTable">
                                <thead class="table-light">
                                    <tr>
                                        <th>{{ __('Request Number')}}</th>
                                        <th>{{ __('User')}}</th>
                                        <th>{{ __('Coin Details')}}</th>
                                        <th>{{ __('Payment Details')}}</th>
                                        <th>{{ __('Created Date')}}</th>
                                    </tr>
                                </thead>
                            </table>
                        </div>
                    </div>

                </div> <!-- end tab-content-->
            </div> <!-- end col-->
        </div>
    </div>
</div>



@endsection
