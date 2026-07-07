@extends('include.app')
@section('script')
<script src="{{ asset('assets/script/viewUserDetails.js') }}"></script>
@endsection
@section('content')

@php
use App\Models\GlobalFunction;
@endphp

<style>
 a {
        color: #B754F9
    }
</style>
<input type="hidden" name="user_id" id="user_id" value="{{$user->id}}">

<div class="row">
    <div class="col-lg-4">
        <div class="card">
            <div class="card-header d-flex align-items-center border-bottom">
                <div class="w-100">
                    <h4 class="card-title header-title mb-1">
                        {{ __('User Details')}}
                    </h4>
                    <div class="d-flex justify-content-between ">
                        <div>
                            <h4 class="card-title mb-1 hashtag">
                                {{$user->username}}
                                @if ($user->is_verify == 1)
                                <img src="{{asset('assets/img/ic_verify.png')}}" alt="profile" class="rounded-circle object-fit-cover" width="18px">
                                @endif
                                {!!GlobalFunction::createUserTypeBadge($user->id)!!}
                            </h4>
                        </div>
                        <div>
                            <a href='{{route('editUser',$user->id)}}' class='action-btn edit d-flex align-items-center justify-content-center btn border rounded-2 text-info ms-1'>
                                <i class='uil-pen'></i>
                            </a>
                        </div>
                    </div>

                    <span class="fs-6">{{__('User ID')}} : {{$user->id}}</span>
                </div>
            </div>
            <div class="text-center">
                <div class="card-body">

                        <img src="{{$user->profile_photo == null ? url('assets/img/placeholder.png') : $baseUrl.$user->profile_photo}}" class="rounded-circle avatar-lg img-thumbnail object-fit-cover" alt="profile-image">
                        <div class="mt-2">
                            <h4 class="mb-0  d-inline">{{$user->username}}
                                @if ($user->is_verify == 1)
                                <img src="{{asset('assets/img/ic_verify.png')}}" alt="profile" class="rounded-circle object-fit-cover" width="18px">
                                @endif
                            </h4>
                        </div>
                        <p class="text-muted font-14 mb-0">{{$user->fullname}}</p>
                        <span class='badge badge-primary-lighten fs-6 mt-1'>{{__('Level')}} : {{$user->levelNumber}}</span>

                        <div class="d-flex aligh-items-center justify-content-center mt-3">
                            {{-- likes --}}
                            <div style="width: 100px" class="text-center">
                                <h5 class="m-0">{{GlobalFunction::formatNumber($user->total_post_likes_count)}}</h5>
                                <span class="fs-6">{{__('Likes')}}</span>
                            </div>
                            {{-- Folowers --}}
                            <div style="width: 100px" class="text-center">
                                <h5 class="m-0">{{GlobalFunction::formatNumber($user->follower_count)}}</h5>
                                <span class="fs-6">{{__('Followers')}}</span>
                            </div>
                            {{-- Following --}}
                            <div style="width: 100px" class="text-center">
                                <h5 class="m-0">{{GlobalFunction::formatNumber($user->following_count)}}</h5>
                                <span class="fs-6">{{__('Following')}}</span>
                            </div>
                        </div>

                        <div class="text-center">
                            {{-- <h4 class="font-13 text-uppercase mb-0">{{__('Bio')}}</h4> --}}
                            <p class="text-muted font-13 mb-3 mt-3">
                               {{$user->bio}}
                            </p>
                        </div>
                        {{-- Links --}}
                        @if ($user->links && $user->links->isNotEmpty())
                        <hr>
                         <h4 class="font-13 text-uppercase mb-0">{{__('Links')}}</h4>
                        <div id="link-space" class="mt-2">
                           @foreach ($user->links as $link)
                           <div class="link-item border px-4 py-2 mt-1 rounded-pill d-flex align-items-center">
                                <div class="d-grid text-start">
                                    <div class="text-body text-dark fw-semibold">{{$link->title}}</div>
                                    <a class="fs-6" target="_blank" href="{{$link->url}}">{{$link->url}}</a>
                                </div>
                                <a href='#'
                                    rel='{{$link->id}}'
                                    class='ms-auto action-btn delete-link d-flex align-items-center justify-content-center btn border rounded-2 text-danger ms-1'>
                                        <i class='uil-trash-alt'></i>
                                </a>
                            </div>
                           @endforeach
                        </div>
                        @endif
                </div>
            </div>
        </div>
        {{-- Wallet Card --}}
        <div class="card">
            <div class="card-header d-flex align-items-center border-bottom">
                <h4 class="card-title header-title">
                    {{ __('Wallet')}}
                </h4>
                <a data-bs-toggle="modal" data-bs-target="#addCoinsModal" class="btn btn-dark ms-auto">{{ __('Add Coins')}}</a>
            </div>
            <div class="text-center">
                <div class="card-body">
                    <div>
                        <h1 class="m-0 text-primary">{{GlobalFunction::formatNumber($user->coin_wallet)}}</h1>
                        <span class="fs-6">{{__('Balance')}}</span>
                    </div>
                    <div class="d-flex aligh-items-center justify-content-center mt-3">
                        {{-- likes --}}
                        <div style="width: 100px" class="text-center">
                            <h4 class="m-0 text-primary">{{GlobalFunction::formatNumber($user->coin_collected_lifetime)}}</h4>
                            <span class="fs-6">{{__('Collected')}}*</span>
                        </div>
                        {{-- Folowers --}}
                        <div style="width: 100px" class="text-center">
                            <h4 class="m-0 text-primary">{{GlobalFunction::formatNumber($user->coin_gifted_lifetime)}}</h4>
                            <span class="fs-6">{{__('Gifted')}}*</span>
                        </div>
                        {{-- Following --}}
                        <div style="width: 100px" class="text-center">
                            <h4 class="m-0 text-primary">{{GlobalFunction::formatNumber($user->coin_purchased_lifetime)}}</h4>
                            <span class="fs-6">{{__('Purchased')}}*</span>
                        </div>
                    </div>
                    <p class="font-12 mt-2 mb-0">{{__('*lifetime')}}</p>
                </div>

            </div>
        </div>
        {{-- Other details card --}}
        <div class="card">
            <div class="card-header d-flex align-items-center border-bottom">
                <h4 class="card-title header-title">
                    {{ __('Other Details')}}
                </h4>
            </div>
            <div class="text-center">
                <div class="card-body">
                        <div class="text-start">
                            <div class="row">
                                {{-- Freeze Switch --}}
                                <div class="col-3 text-end">
                                    <p class="text-muted mb-2 font-13 "><strong>{{__('Freeze')}} :</strong></p>
                                </div>
                                <div class="mb-2 col-9">
                                    <input name="is_freez" type="checkbox" id="switchFreezeStatus" {{$user->is_freez == 1? 'checked' : ''}} data-switch="primary"/>
                                    <label for="switchFreezeStatus" ></label>
                                </div>
                                {{-- Moderator Switch --}}
                                <div class="col-3 text-end">
                                    <p class="text-muted mb-2 font-13 "><strong>{{__('Moderator')}} :</strong></p>
                                </div>
                                <div class="mb-2 col-9">
                                    <input name="is_moderator" type="checkbox" id="switchModeratorStatus" {{$user->is_moderator == 1? 'checked' : ''}} data-switch="primary"/>
                                    <label for="switchModeratorStatus" ></label>
                                </div>
                                <div class="col-3 text-end">
                                    <p class="text-muted mb-2 font-13 "><strong>{{__('Identity')}} :</strong></p>
                                </div>
                                <p class="text-muted mb-2 font-13 col-9">{{$user->identity}}</p>
                                <div class="col-3 text-end">
                                    <p class="text-muted mb-2 font-13 "><strong>{{__('Email')}} :</strong></p>
                                </div>
                                <p class="text-muted mb-2 font-13 col-9">{{$user->user_email}}</p>
                                <div class="col-3 text-end">
                                    <p class="text-muted mb-2 font-13 "><strong>{{__('Phone')}} :</strong></p>
                                </div>
                                <div class="col-9">
                                    @if ($user->user_mobile_no != null)
                                    <p class="text-muted mb-2 font-13">+{{$user->mobile_country_code.' '.$user->user_mobile_no}}</p>
                                    @endif
                                </div>

                            </div>
                            <hr>
                            {{-- Device Details --}}
                            <div class="row">
                                 <div class="col-3 text-end">
                                    <p class="text-muted mb-2 font-13 "><strong>{{__('Last Opened')}} :</strong></p>
                                </div>
                                <p class="text-muted mb-2 font-13 col-9">{{$user->app_last_used_at}}</p>
                                 <div class="col-3 text-end">
                                    <p class="text-muted mb-2 font-13 "><strong>{{__('Log-in')}} :</strong></p>
                                </div>
                                <p class="text-muted mb-2 font-13 col-9">{{$user->login_method}}</p>
                                 <div class="col-3 text-end">
                                    <p class="text-muted mb-2 font-13 "><strong>{{__('Device OS')}} :</strong></p>
                                </div>
                                <p class="text-muted mb-2 font-13 col-9">{{$user->device_type == 1 ? 'Android' : 'iOS'}}</p>
                                 <div class="col-3 text-end">
                                    <p class="text-muted mb-2 font-13 "><strong>{{__('Device Token')}} :</strong></p>
                                </div>
                                <p class="text-muted mb-2 font-13 col-9">{{$user->device_token}}</p>
                            </div>
                            {{-- Location Details --}}
                            <hr>
                            <div class="row">
                                 <div class="col-3 text-end">
                                    <p class="text-muted mb-2 font-13 "><strong>{{__('Country')}} :</strong></p>
                                </div>
                                <p class="text-muted mb-2 font-13 col-9">({{$user->countryCode}}) {{$user->country}}</p>
                                 <div class="col-3 text-end">
                                    <p class="text-muted mb-2 font-13 "><strong>{{__('Region')}} :</strong></p>
                                </div>
                                <p class="text-muted mb-2 font-13 col-9">({{$user->region}}) {{$user->regionName}}</p>
                                 <div class="col-3 text-end">
                                    <p class="text-muted mb-2 font-13 "><strong>{{__('City')}} :</strong></p>
                                </div>
                                <p class="text-muted mb-2 font-13 col-9">{{$user->city}}</p>
                                 <div class="col-3 text-end">
                                    <p class="text-muted mb-2 font-13 "><strong>{{__('Latitude')}} :</strong></p>
                                </div>
                                <p class="text-muted mb-2 font-13 col-9">{{$user->lat}}</p>
                                 <div class="col-3 text-end">
                                    <p class="text-muted mb-2 font-13 "><strong>{{__('Longitude')}} :</strong></p>
                                </div>
                                <p class="text-muted mb-2 font-13 col-9">{{$user->lon}}</p>
                            </div>
                            {{-- Settings --}}
                            <hr>
                            <div class="row">
                                 <div class="col-3 text-end">
                                    <p class="text-muted mb-2 font-13 "><strong>{{__('Who Can View Post')}} :</strong></p>
                                </div>
                                <p class="text-muted mb-2 font-13 col-9">{{$user->who_can_view_post == 0 ? 'Everyone' : 'Followers Only'}}</p>
                                 <div class="col-3 text-end">
                                    <p class="text-muted mb-2 font-13 "><strong>{{__('Show Following')}} :</strong></p>
                                </div>
                                <p class="text-muted mb-2 font-13 col-9">{{$user->show_my_following == 1 ? 'Yes' : 'No'}}</p>
                                 <div class="col-3 text-end">
                                    <p class="text-muted mb-2 font-13 "><strong>{{__('Receive Message')}} :</strong></p>
                                </div>
                                <p class="text-muted mb-2 font-13 col-9">{{$user->receive_message == 1 ? 'Yes' : 'No'}}</p>
                            </div>
                        </div>

                </div>
            </div>
        </div>
         {{-- Notification Card --}}
         <div class="card">
            <div class="card-header d-flex align-items-center border-bottom">
                <h4 class="card-title header-title">
                    {{ __('Notifications')}}
                </h4>
            </div>
            <div class="text-center">
                <div class="card-body">
                    <div class="text-start">
                        <div class="row">
                            {{-- ********* --}}
                            <div class="col-3 text-end">
                                <p class="text-muted mb-2 font-13 "><strong>{{__('Post Likes')}} :</strong></p>
                            </div>
                            <p class="text-muted mb-2 font-13 col-9">{{$user->notify_post_like == 1 ? 'Yes' : 'No'}}</p>
                            {{-- ********* --}}
                            <div class="col-3 text-end">
                                <p class="text-muted mb-2 font-13 "><strong>{{__('Post Comments')}} :</strong></p>
                            </div>
                            <p class="text-muted mb-2 font-13 col-9">{{$user->notify_post_comment == 1 ? 'Yes' : 'No'}}</p>
                            {{-- ********* --}}
                            <div class="col-3 text-end">
                                <p class="text-muted mb-2 font-13 "><strong>{{__('Follow')}} :</strong></p>
                            </div>
                            <p class="text-muted mb-2 font-13 col-9">{{$user->notify_follow == 1 ? 'Yes' : 'No'}}</p>
                            {{-- ********* --}}
                            <div class="col-3 text-end">
                                <p class="text-muted mb-2 font-13 "><strong>{{__('Mentions')}} :</strong></p>
                            </div>
                            <p class="text-muted mb-2 font-13 col-9">{{$user->notify_mention == 1 ? 'Yes' : 'No'}}</p>
                            {{-- ********* --}}
                            <div class="col-3 text-end">
                                <p class="text-muted mb-2 font-13 "><strong>{{__('Gifts Received')}} :</strong></p>
                            </div>
                            <p class="text-muted mb-2 font-13 col-9">{{$user->notify_gift_received == 1 ? 'Yes' : 'No'}}</p>
                            {{-- ********* --}}
                            <div class="col-3 text-end">
                                <p class="text-muted mb-2 font-13 "><strong>{{__('Chat Message')}} :</strong></p>
                            </div>
                            <p class="text-muted mb-2 font-13 col-9">{{$user->notify_chat == 1 ? 'Yes' : 'No'}}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    {{-- posts/stories etc.. Card --}}
    <div class="col-lg-8">
        {{-- Posts --}}
        <div class="card">
            <div class="card-header d-flex align-items-center border-bottom">
                    <h4 class="card-title header-title">
                        <div class="nav nav-pills" id="v-pills-tab" role="tablist" aria-orientation="vertical">
                            <a class="nav-link active show" id="v-pills-posts-tab" data-bs-toggle="pill" href="#v-pills-posts" role="tab" aria-controls="v-pills-posts"
                            aria-selected="false">
                            <span class="d-md-block">{{__('Posts')}}</span>
                             </a>
                            <a class="nav-link " id="v-pills-stories-tab" data-bs-toggle="pill" href="#v-pills-stories" role="tab" aria-controls="v-pills-stories"
                                aria-selected="true">
                                <span class="d-md-block">{{__('Stories')}}</span>
                            </a>
                        </div>
                    </h4>
            </div>
            <div class="card-body">

                <div class="row">

                    <div class="col-sm-12">
                        <div class="tab-content mt-3" id="v-pills-tabContent">
                            {{-- Posts --}}
                            <div class="tab-pane fade active show" id="v-pills-posts" role="tabpanel" aria-labelledby="v-pills-posts-tab">
                                <div class="table-responsive">
                                    <table id="userPostsTable" class="table table-centered table-hover w-100 dt-responsive nowrap mt-3">
                                        <thead class="table-light">
                                            <tr>
                                                <th>{{ __('Content')}}</th>
                                                <th>{{ __('Type')}}</th>
                                                <th class="w-100">{{ __('Description & Stats')}}</th>
                                                <th>{{ __('Created Date')}}</th>
                                                <th style="width: 200px;" class="text-end">{{ __('Action')}}</th>
                                            </tr>
                                        </thead>
                                    </table>
                                </div>
                            </div>
                              {{-- Stories --}}
                              <div class="tab-pane fade " id="v-pills-stories" role="tabpanel" aria-labelledby="v-pills-stories-tab">
                                <div class="table-responsive">
                                    <table class="table table-centered table-hover w-100 dt-responsive nowrap mt-3" id="userStoriesTable">
                                        <thead class="table-light">
                                            <tr>
                                                <th>{{ __('Story')}}</th>
                                                <th>{{ __('Type')}}</th>
                                                <th>{{ __('Created At')}}</th>
                                                <th style="width: 200px;" class="text-end">{{ __('Action')}}</th>
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
    </div>
</div>

{{-- Add Coins Modal --}}
<div id="addCoinsModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="standard-modalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="standard-modalLabel">{{ __('Add Coins')}}</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-hidden="true"></button>
            </div>
            <form id="addCoinsForm" method="POST">
                <input type="hidden" name="user_id" value="{{$user->id}}">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="coins" class="form-label">{{ __('Coins')}}</label>
                        <input class="form-control" type="number" min="1" id="coins" name="coins" required>
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

{{-- Video/Reel Post Modal --}}
<div id="videoPostModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="standard-modalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="standard-modalLabel">{{ __('View Content')}}</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-hidden="true"></button>
            </div>
            <div class="modal-body">
                <div class="postDescription mb-2" id="videoDescription"></div>
                <video rel="" id="video" width="450" height="450" controls>
                    <source src="" type="video/mp4">
                    Your browser does not support the video tag.
                </video>
            </div>
        </div>
    </div>
</div>
{{-- Video Story Modal --}}
<div id="videoStoryModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="standard-modalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="standard-modalLabel">{{ __('View Story')}}</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-hidden="true"></button>
            </div>
            <div class="modal-body text-center">
                <video rel="" id="videoStory" width="450" height="450" controls>
                    <source src="" type="video/mp4">
                    Your browser does not support the video tag.
                </video>
            </div>
        </div>
    </div>
</div>

{{-- Image Story Modal --}}
<div id="imageStoryModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="standard-modalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="standard-modalLabel">{{ __('View Story')}}</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-hidden="true"></button>
            </div>
            <div class="modal-body text-center">
                <div class="swiper mySwiper storyImageSwiper">
                    <div class="swiper-wrapper" id="imgStorySwiperWrapper">
                        {{-- Insert images here --}}

                    </div>
                    <div class="swiper-button-next"></div>
                    <div class="swiper-button-prev"></div>
                  </div>
            </div>
        </div>
    </div>
</div>
{{-- Image Post Modal --}}
<div id="imagePostModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="standard-modalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="standard-modalLabel">{{ __('View Content')}}</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-hidden="true"></button>
            </div>
            <div class="modal-body">
                <div class="postDescription mb-2" id="imageDescription"></div>
                <div class="swiper mySwiper postImageSwiper">
                    <div class="swiper-wrapper" id="imgPostSwiperWrapper">
                        {{-- Insert images here --}}

                    </div>
                    <div class="swiper-button-next"></div>
                    <div class="swiper-button-prev"></div>
                  </div>
            </div>
        </div>
    </div>
</div>
{{-- Text Post Modal --}}
<div id="textPostModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="standard-modalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="standard-modalLabel">{{ __('View Content')}}</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-hidden="true"></button>
            </div>
            <div class="modal-body">
                <div class="postDescription mb-2" id="textDescription"></div>
            </div>
        </div>
    </div>
</div>

@endsection
