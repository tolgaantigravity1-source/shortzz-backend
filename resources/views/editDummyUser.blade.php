@extends('include.app')
@section('script')
<script src="{{ asset('assets/script/editDummyUser.js') }}"></script>
@endsection
@section('content')

@php
use App\Models\Constants;
use App\Models\GlobalFunction;

@endphp


<div class="card">
    <div class="card-header d-flex align-items-center border-bottom">
        <h4 class="card-title mb-0 header-title">
            {{ __('Edit Dummy User')}}
        </h4>
    </a>
    </div>
    <div class="card-body">
        <form class="" id="editDummyUserForm" method="POST">
            <input type="hidden" name="id" value="{{$user->id}}">
                <td class="p-0 border-0">
                {!!GlobalFunction::createUserDetailsColumn($user->id)!!}
                </td>
            <div class="row mt-3">
                <div class="col-md-3 mb-3">
                    <div class="mb-0 bg-secondary-lighten border p-2 rounded-3">
                        <label for="profile_photo" class="form-label">{{ __('Profile Photo')}}</label>
                        <input type="file" id="profile_photo" name="profile_photo" class="form-control">
                    </div>
                </div>
                <div class="col-md-3 mb-3">
                    <div class="mb-0 bg-secondary-lighten border p-2 rounded-3">
                        <label for="fullname" class="form-label">{{ __('Fullname')}}</label>
                        <input value="{{$user->fullname}}" type="text" id="fullname" name="fullname" class="form-control" required>
                    </div>
                </div>
                <div class="col-md-3 mb-3">
                    <div class="mb-0 bg-secondary-lighten border p-2 rounded-3">
                        <label for="username" class="form-label">{{ __('Username')}}</label>
                        <input type="text" value="{{$user->username}}" id="username" name="username" class="form-control" required>
                    </div>
                </div>
                <div class="col-md-3 mb-3">
                    <div class="mb-0 bg-secondary-lighten border p-2 rounded-3">
                        <label for="password" class="form-label">{{ __('Password')}}</label>
                        <input type="text" value="{{$user->password}}" id="password" name="password" class="form-control" required>
                    </div>
                </div>
                <div class="col-md-3 mb-3">
                    <div class="mb-0 bg-secondary-lighten border p-2 rounded-3">
                        <label for="bio" class="form-label">{{ __('Bio')}}</label>
                        <textarea type="text"  id="bio" name="bio" class="form-control" required>{{$user->bio}}</textarea>
                    </div>
                </div>
                <div class="col-md-3 mb-3">
                    <div class="mb-0 bg-secondary-lighten border p-2 rounded-3">
                        <label for="" class="form-label">{{ __('Verified')}}</label>
                        <div class="mb-0">
                            <input name="is_verify" type="checkbox" id="switchIsVerify" {{$user->is_verify == 1? 'checked' : ''}} data-switch="primary"/>
                            <label for="switchIsVerify" ></label>
                        </div>
                    </div>
                </div>
            </div>

            <button type="submit" class="btn btn-primary">{{__('Submit')}}</button>
        </form>
    </div>
</div>

@endsection
