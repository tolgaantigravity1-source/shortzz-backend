@extends('include.app')
@section('script')
<script src="{{ asset('assets/script/languages.js') }}"></script>
@endsection
@section('content')


<div class="card">
    <div class="card-body">
        <div class="mb-2">
            <div class="d-flex align-items-center justify-content-between">
                <h4 class="card-title mb-0">
                    {{ __('Add String')}}
                </h4>
            </div>
        </div>
        <hr>
        <form id="addStringForm" method="POST">
            <div class="modal-body">
                    <div class="border-left">
                        <div class="row">
                            @foreach ($languages as $language)
                            <div class="col-lg-4 col-md-4">
                                <div class="mb-3">
                                    <label for="value_{{ $language->id }}" class="form-label ">{{ $language->title}} <span class="fs-6 {{ $language->is_default == 1 ? 'text-danger' : 'text-secondary text-muted' }}"> ({{ $language->is_default == 1 ? __('Required') : __('Optional') }}) </span> </label>
                                    <input class="form-control string-input" type="text" id="{{ $language->id }}" name="{{ $language->code }}" {{ $language->is_default == 1 ? 'required' : '' }}>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                <hr>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary" id="keyBtn">
                        <span class="spinner-border spinner-border-sm me-1 spinner hide" role="status" aria-hidden="true"></span>
                        {{ __('Save')}}
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>


@endsection
