@extends('include.app')
@section('script')
<script src="{{ asset('assets/script/hashtagDetails.js') }}"></script>
<script src="{{ asset('assets/script/posts.js') }}"></script>

<script>

</script>
@endsection
@section('content')

<div class="card">
    <input type="hidden" id="hashtag" value="{{$hashtag->hashtag}}">
    <div class="card-header border-bottom">
        <h4 class="card-title  header-title mb-1">
            {{ __('Hashtag Details')}}
        </h4>
        <h3 class="card-title mb-1 hashtag">
            #{{$hashtag->hashtag}}
        </h3>
        <div>
            <span class="fs-6">{{__('Total Posts')}} : {{$hashtag->post_count}}</span>
        </div>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="table-responsive">
                <table id="hashtagPostsTable" class="table table-centered table-hover w-100 dt-responsive nowrap mt-3">
                    <thead class="table-light">
                        <tr>
                            <th>{{ __('Content')}}</th>
                            <th>{{ __('Type')}}</th>
                            <th>{{ __('User')}}</th>
                            <th>{{ __('Description & Stats')}}</th>
                            <th>{{ __('Created Date')}}</th>
                            <th style="width: 200px;" class="text-end">{{ __('Action')}}</th>
                        </tr>
                    </thead>
                </table>
            </div>
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
