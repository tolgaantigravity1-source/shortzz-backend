@extends('include.app')
@section('script')
<script src="{{ asset('assets/script/hashtags.js') }}"></script>
@endsection
@section('content')

<div class="mb-2">
</div>

<div class="card">
    <div class="card-header d-flex align-items-center border-bottom">
        <h4 class="card-title mb-0 header-title">
            {{ __('Hashtags')}}
        </h4>
        <a data-bs-toggle="modal" data-bs-target="#addHashtagModal" class="btn btn-dark ms-auto">{{ __('Add Hashtag')}}</a>
    </a>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table id="hashtagsTable" class="table table-centered table-hover w-100 dt-responsive nowrap mt-3" >
                <thead class="table-light">
                    <tr>
                        <th>{{ __('Hashtag')}}</th>
                        <th>{{ __('Post Count')}}</th>
                        <th style="width: 200px;" class="text-end">{{ __('Action')}}</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
</div>

{{-- Add Modal --}}
<div id="addHashtagModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="standard-modalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="standard-modalLabel">{{ __('Add Hashtag')}}</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-hidden="true"></button>
            </div>
            <form id="addHashtagForm" method="POST">
                <div class="modal-body">

                    <div class="mb-3">
                        <label for="hashtag" class="form-label">{{ __('Hashtag')}}</label>
                        <div class="input-group flex-nowrap">
                            <span class="input-group-text" id="basic-addon1">#</span>
                            <input type="text" id="hashtag" name="hashtag" class="form-control" placeholder="Hashtag" aria-label="Hashtag" aria-describedby="basic-addon1"  pattern="[^@#]*"  required>
                        </div>
                        <span class="fs-6">* Do not include (#,@) symbol</span>
                    </div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">{{ __('Close')}}</button>
                    <button type="submit" class="btn btn-primary">
                        <span class="spinner-border spinner-border-sm me-1 spinner hide" role="status" aria-hidden="true"></span>
                        {{ __('Save')}}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection
