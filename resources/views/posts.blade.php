@extends('include.app')
@section('script')
<script src="{{ asset('assets/script/posts.js') }}"></script>
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

<div class="card">
    <div class="card-header d-flex align-items-center border-bottom">
        <h4 class="card-title mb-0 header-title">
            {{ __('Posts')}}
        </h4>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-sm-3 mb-2 mb-sm-0 ">
                <div class="nav nav-pills" id="v-pills-tab" role="tablist" aria-orientation="vertical">

                    {{-- All --}}
                    <a class="nav-link active show" id="v-pills-allPost-tab" data-bs-toggle="pill" href="#v-pills-allPost" role="tab" aria-controls="v-pills-allPost"
                    aria-selected="false">
                    <span class="d-md-block">{{__('All')}}</span>
                     </a>

                     {{-- Reels --}}
                    <a class="nav-link " id="v-pills-reels-tab" data-bs-toggle="pill" href="#v-pills-reels" role="tab" aria-controls="v-pills-reels"
                        aria-selected="true">
                        <span class="d-md-block">{{__('Reels')}}</span>
                    </a>

                     {{-- Video --}}
                    <a class="nav-link " id="v-pills-videos-tab" data-bs-toggle="pill" href="#v-pills-videos" role="tab" aria-controls="v-pills-videos"
                        aria-selected="true">
                        <span class="d-md-block">{{__('Videos')}}</span>
                    </a>

                     {{-- Image --}}
                    <a class="nav-link " id="v-pills-image-tab" data-bs-toggle="pill" href="#v-pills-image" role="tab" aria-controls="v-pills-image"
                        aria-selected="true">
                        <span class="d-md-block">{{__('Image')}}</span>
                    </a>

                     {{-- Text --}}
                    <a class="nav-link " id="v-pills-text-tab" data-bs-toggle="pill" href="#v-pills-text" role="tab" aria-controls="v-pills-text"
                        aria-selected="true">
                        <span class="d-md-block">{{__('Text')}}</span>
                    </a>

                </div>
            </div> <!-- end col-->

            <div class="col-sm-12">
                <div class="tab-content mt-3" id="v-pills-tabContent">
                        {{-- All Posts --}}
                        <div class="tab-pane fade active show" id="v-pills-allPost" role="tabpanel" aria-labelledby="v-pills-allPost-tab">
                            <div class="table-responsive">
                                <table id="allPostsTable" class="table table-centered table-hover w-100 dt-responsive nowrap mt-3">
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
                    {{-- Categories --}}
                    <div class="tab-pane fade " id="v-pills-reels" role="tabpanel" aria-labelledby="v-pills-reels-tab">
                        <div class="table-responsive">
                            <table id="reelPostsTable" class="table table-centered table-hover w-100 dt-responsive nowrap mt-3">
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
                    {{-- Video --}}
                    <div class="tab-pane fade " id="v-pills-videos" role="tabpanel" aria-labelledby="v-pills-videos-tab">
                        <div class="table-responsive">
                            <table id="videoPostsTable" class="table table-centered table-hover w-100 dt-responsive nowrap mt-3">
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
                    {{-- Image --}}
                    <div class="tab-pane fade " id="v-pills-image" role="tabpanel" aria-labelledby="v-pills-image-tab">
                        <div class="table-responsive">
                            <table id="imagePostsTable" class="table table-centered table-hover w-100 dt-responsive nowrap mt-3">
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
                    {{-- Text --}}
                    <div class="tab-pane fade " id="v-pills-text" role="tabpanel" aria-labelledby="v-pills-text-tab">
                        <div class="table-responsive">
                            <table id="textPostsTable" class="table table-centered table-hover w-100 dt-responsive nowrap mt-3">
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

                </div> <!-- end tab-content-->
            </div> <!-- end col-->
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
