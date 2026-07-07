@extends('include.app')
@section('script')
<script src="{{ asset('assets/script/postDetails.js') }}"></script>

<script>
    var swiper = new Swiper(".mySwiper", {
     navigation: {
       nextEl: ".swiper-button-next",
       prevEl: ".swiper-button-prev",
     },
   });
</script>
@endsection
@section('content')

@php
use App\Models\Constants;
use App\Models\GlobalFunction;

@endphp

<div class="row">
    <input type="hidden" name="postId" id="postId" value="{{$post->id}}">
    {{-- Post Card --}}
    <div class=" col-lg-4">
        <div class="card">
            <div class="card-header d-flex align-items-center border-bottom">
                <div>
                    <h4 class="header-title mb-0 d-flex align-items-center">
                        {{ __('Post Details')}}
                        <span class="ms-2">
                            {!!$postType!!}
                        </span>
                    </h4>
                    <span class="fs-6">{{__('Post ID')}} : {{$post->id}}</span>
                </div>
                {{-- Date --}}
                <p class="text-secondary fs-6 ms-auto">{{GlobalFunction::formateDatabaseTime($post->created_at)}}</p>
            </div>
            <div class="card-body">
                <table class="table mb-0">
                    <td class="p-0 border-0">
                    {!!GlobalFunction::createUserDetailsColumn($postUser->id)!!}
                    </td>
                </table>
                <div class="row mt-3">
                    <div class="col">
                        @if ($post->post_type !=  Constants::postTypeText)
                            <div class="postSpace bg-dark d-flex justify-content-center align-items-center rounded ">
                                @if ($post->post_type == Constants::postTypeReel || $post->post_type == Constants::postTypeVideo)
                                {{-- Reel & Video --}}
                                <video rel="" id="video" width="450" height="450" controls>
                                    <source src="{{$baseUrl.$post->video}}" type="video/mp4">
                                    Your browser does not support the video tag.
                                </video>
                                @elseif ($post->post_type == Constants::postTypeImage)
                                {{-- Image --}}
                                <div class="swiper mySwiper postImageSwiper w-100 h-100">
                                    <div class="swiper-wrapper" id="imgPostSwiperWrapper">
                                        {{-- Insert images here --}}
                                        @foreach ($post->images as $image)
                                            <div class="swiper-slide p-0">
                                                <img src="{{$baseUrl.$image->image}}" alt="" class="img-fluid ">
                                            </div>
                                        @endforeach
                                    </div>
                                    <div class="swiper-button-next"></div>
                                    <div class="swiper-button-prev"></div>
                                </div>
                                @endif

                            </div>
                        @endif
                        <div class="mt-2">
                            {!!$formattedDesc!!}
                        </div>
                        <div class="mt-3">
                            {!!$states!!}
                        </div>

                    </div>
                </div>
            </div>
        </div>
        {{-- Music --}}
        @if ($music != null)
            <div class="card">
                <div class="card-header d-flex align-items-center border-bottom">
                    <div>
                        <h4 class="header-title mb-0 d-flex align-items-center">
                            {{ __('Music Details')}}
                        </h4>
                        <span class="fs-6">{{__('Music ID')}} : {{$music->id}}</span>
                    </div>
                </div>
                <div class="card-body">
                    <div class="text-center">
                        <img class="rounded-circle shadow border" style="width: 120px; height: 120px; object-fit: cover;" src="{{$baseUrl.$music->image}}" alt="">
                        <div class="mt-2">
                            <audio controls><source src="{{$baseUrl.$music->sound}}"></audio>
                        </div>
                        <h4 class="mb-0">{{$music->title}}</h4>
                        <span class="fs-6">{{$music->artist}}</span>
                        <div class="mt-2">
                            <span class="fs-6 header-title mt-3">{{__('Added By')}}</span>
                        </div>
                        @if ($music->added_by == 0)
                        <table class="table mb-0">
                            <td class="p-0 border-0">
                            {!!GlobalFunction::createUserDetailsColumn($music->user_id)!!}
                            </td>
                        </table>
                        @else
                        <span class='badge badge-info-lighten fs-5 header-title'>{{__('Admin')}}</span>
                        @endif
                    </div>

                </div>
            </div>
        @endif
    </div>
    {{-- Comments Card --}}
    <div class=" col-lg-8">
        <div class="card">
            <div class="card-header d-flex align-items-center border-bottom">
                <div>
                    <h4 class="header-title mb-0 d-flex align-items-center">
                        {{ __('Comments')}}
                    </h4>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table id="commentsTable" class="table table-centered table-hover w-100 dt-responsive nowrap mt-3">
                        <thead class="table-light">
                            <tr>
                                <th>{{ __('Comment')}}</th>
                                <th>{{ __('User')}}</th>
                                <th>{{ __('Created Date')}}</th>
                                <th style="width: 200px;" class="text-end">{{ __('Action')}}</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
        <div class="card">
            <div class="card-header d-flex align-items-center border-bottom">
                <div>
                    <h4 class="header-title mb-0 d-flex align-items-center">
                        {{ __('Comment Replies')}}
                    </h4>
                </div>
            </div>
            <div class="card-body">
                <span>*Click on replies <i class='uil-comments text-info px-1 border rounded-2'></i> button on comments table and replies will list here..</span>
                <div class="table-responsive mt-2">
                    <table id="commentRepliesTable" class="table table-centered table-hover w-100 dt-responsive nowrap mt-3">
                        <thead class="table-light">
                            <tr>
                                <th>{{ __('Reply')}}</th>
                                <th>{{ __('User')}}</th>
                                <th>{{ __('Created Date')}}</th>
                                <th style="width: 200px;" class="text-end">{{ __('Action')}}</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>


@endsection
