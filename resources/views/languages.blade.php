@extends('include.app')
@section('script')
<script src="{{ asset('assets/script/languages.js') }}"></script>

<script>

</script>
@endsection
@section('content')

<div class="card">
    <div class="card-header d-flex align-items-center border-bottom">
        <h4 class="card-title mb-0 header-title">
            {{ __('App Languages')}}
        </h4>
        <a data-bs-toggle="modal" data-bs-target="#addLanguageModal" class="btn btn-dark ms-auto">{{ __('Add Language')}}</a>
    </div>
    <div class="card-body">
        <span class="fs-6">*Please refer documentation to learn how to add new language to the app.</span>
        <div class="table-responsive">
            <table id="languageTable" class="table table-centered table-hover w-100 dt-responsive nowrap mt-3" >
                <thead class="table-light">
                    <tr>
                        <th>{{ __('Title')}}</th>
                        <th>{{ __('Code')}}</th>
                        <th>{{ __('Localized Title')}}</th>
                        <th>{{ __('Status')}}</th>
                        <th>{{ __('Default')}}</th>
                        <th>{{ __('CSV File')}}</th>
                        <th style="width: 200px;" class="text-end">{{ __('Action')}}</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
</div>
{{-- Add Language Modal --}}
<div id="addLanguageModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="standard-modalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="standard-modalLabel">{{ __('Add Language')}}</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-hidden="true"></button>
            </div>
            <form id="addLanguageForm" method="POST">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="code" class="form-label">{{ __('Code')}} (Ex. fr)</label>
                        <input class="form-control" type="text" id="code" name="code" required>
                    </div>
                    <div class="mb-3">
                        <label for="title" class="form-label">{{ __('Title')}} (Ex. French)</label>
                        <input class="form-control" type="text" id="title" name="title" required>
                    </div>
                    <div class="mb-3">
                        <label for="localized_title" class="form-label">{{ __('Localized Title')}} (Ex. Français)</label>
                        <input class="form-control" type="text" id="localized_title" name="localized_title" required>
                    </div>
                    <div class="mb-3">
                        <label for="csv_file" class="form-label">{{ __('CSV File')}}</label>
                        <input class="form-control" type="file" id="csv_file" name="csv_file" required>
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
{{-- Edit Language --}}
<div id="editLanguageModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="standard-modalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="standard-modalLabel">{{ __('Edit Language')}}</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-hidden="true"></button>
            </div>
            <form id="editLanguageForm" method="POST">
                <div class="modal-body">
                    <input type="hidden" id="language_id" name="language_id">
                    <div class="mb-3">
                        <label for="code" class="form-label">{{ __('Code')}} (Ex. fr)</label>
                        <input class="form-control" type="text" id="edit_code" name="code" required>
                    </div>
                    <div class="mb-3">
                        <label for="title" class="form-label">{{ __('Title')}} (Eg: French)</label>
                        <input class="form-control" type="text" id="edit_title" name="title" required>
                    </div>
                    <div class="mb-3">
                        <label for="localized_title" class="form-label">{{ __('Localized Title')}} (Eg: Français)</label>
                        <input class="form-control" type="text" id="edit_localized_title" name="localized_title" required>
                    </div>
                    <div class="mb-3">
                        <label for="csv_file" class="form-label">{{ __('CSV File')}}</label>
                        <input class="form-control" type="file" id="csv_file" name="csv_file">
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
