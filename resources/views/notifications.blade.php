@extends('include.app')
@section('script')
<script src="{{ asset('assets/script/notifications.js') }}"></script>
@endsection
@section('content')

<div class="mb-2">
</div>

<div class="card">
    <div class="card-header d-flex align-items-center border-bottom">
        <h4 class="card-title mb-0 header-title">
            {{ __('Notifications')}}
        </h4>
        <a data-bs-toggle="modal" data-bs-target="#addNotificationModal" class="btn btn-dark ms-auto">{{ __('New Notification')}}</a>
    </a>
    </div>
    <div class="card-body">
        <span class="fs-6">*This is a common notification which will pushed to all users and can see in the app.</span>
        <div class="table-responsive mt-2">
            <table id="notificationTable" class="table table-centered table-hover w-100 dt-responsive nowrap mt-3" >
                <thead class="table-light">
                    <tr>
                        <th>{{ __('Notification')}}</th>
                        <th>{{ __('Date')}}</th>
                        <th style="width: 200px;" class="text-end">{{ __('Action')}}</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
</div>

{{-- Add Modal --}}
<div id="addNotificationModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="standard-modalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="standard-modalLabel">{{ __('Add Notification')}}</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-hidden="true"></button>
            </div>
            <form id="addAdminNotificationForm" method="POST">
                <div class="modal-body">
                    <div class="mb-2">
                        <label for="image" class="form-label">{{ __('Image')}}</label>
                        <input class="form-control" type="file" accept="image/*" min="1" id="image" name="image">
                    </div>
                    <div class="my-2">
                        <label for="title" class="form-label">{{ __('Title')}}</label>
                        <input class="form-control" type="text" id="title" name="title" required>
                    </div>
                    <div class="mb-2">
                        <label for="description" class="form-label">{{ __('Description')}}</label>
                        <textarea class="form-control" id="description" name="description" required></textarea>
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
{{-- Edit Modal --}}
<div id="editNotificationModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="standard-modalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="standard-modalLabel">{{ __('Edit Notification')}}</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-hidden="true"></button>
            </div>
            <form id="editAdminNotificationForm" method="POST">
                <input type="hidden" name="id" id="editId">
                <div class="modal-body">
                    <div class="mb-2">
                        <label for="image" class="form-label">{{ __('Image')}}</label>
                        <input class="form-control" type="file" accept="image/*" min="1" name="image">
                    </div>
                    <div class="my-2">
                        <label for="title" class="form-label">{{ __('Title')}}</label>
                        <input class="form-control" type="text" id="edit_title" name="title" required>
                    </div>
                    <div class="mb-2">
                        <label for="description" class="form-label">{{ __('Description')}}</label>
                        <textarea rows="10" class="form-control" id="edit_description" name="description" required></textarea>
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
