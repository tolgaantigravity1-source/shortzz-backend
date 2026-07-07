@extends('include.app')
@section('script')
<script src="{{ asset('assets/script/music.js') }}"></script>
@endsection
@section('content')

<div class="card">
    <div class="card-header d-flex align-items-center border-bottom">
        <h4 class="card-title mb-0 header-title">
            {{ __('Music')}}
        </h4>
        <a data-bs-toggle="modal" data-bs-target="#addMusicCategoryModal" class="btn btn-dark ms-auto">{{ __('Add Category')}}</a>
        <a data-bs-toggle="modal" data-bs-target="#addMusicModal" class="btn btn-dark ms-2">{{ __('Add Music')}}</a>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-sm-3 mb-2 mb-sm-0 ">
                <div class="nav nav-pills" id="v-pills-tab" role="tablist" aria-orientation="vertical">
                    <a class="nav-link active show" id="v-pills-music-tab" data-bs-toggle="pill" href="#v-pills-music" role="tab" aria-controls="v-pills-music"
                    aria-selected="false">
                    <span class="d-md-block">{{__('Music')}}</span>
                     </a>
                    <a class="nav-link " id="v-pills-category-tab" data-bs-toggle="pill" href="#v-pills-category" role="tab" aria-controls="v-pills-category"
                        aria-selected="true">
                        <span class="d-md-block">{{__('Categories')}}</span>
                    </a>

                </div>
            </div> <!-- end col-->

            <div class="col-sm-12">
                <div class="tab-content mt-3" id="v-pills-tabContent">
                    {{-- Categories --}}
                    <div class="tab-pane fade " id="v-pills-category" role="tabpanel" aria-labelledby="v-pills-category-tab">
                        <div class="table-responsive">
                            <table class="table table-centered table-hover w-100 dt-responsive nowrap mt-3" id="musicCategoryTable">
                                <thead class="table-light">
                                    <tr>
                                        <th>{{ __('Name')}}</th>
                                        <th>{{ __('Music Count')}}</th>
                                        <th style="width: 200px;" class="text-end">{{ __('Action')}}</th>
                                    </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                    {{-- Musics --}}
                    <div class="tab-pane fade active show" id="v-pills-music" role="tabpanel" aria-labelledby="v-pills-music-tab">
                        <div class="table-responsive">
                            <table id="musicTable" class="table table-centered table-hover w-100 dt-responsive nowrap mt-3">
                                <thead class="table-light">
                                    <tr>
                                        <th>{{ __('Image')}}</th>
                                        <th>{{ __('Music')}}</th>
                                        <th>{{ __('Title')}}</th>
                                        <th>{{ __('Category')}}</th>
                                        <th>{{ __('Duration')}}</th>
                                        <th>{{ __('Artist')}}</th>
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

{{-- Edit Music Modal --}}
<div id="editMusicModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="standard-modalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="standard-modalLabel">{{ __('Edit Music')}}</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-hidden="true"></button>
            </div>
            <form id="editMusicForm" method="POST">
                <input id="editMusicId" type="hidden" name="id">
                <div class="modal-body">
                    <div class="mb-2 d-flex align-items-center">
                        <img id="imgEditMusicPreview" src="" alt="" class="rounded" height="100" width="100">
                        <audio id="audioEditMusicPreview" class="ms-2" controls="">
                            <source src="" type="audio/mp3">
                        </audio>
                    </div>
                    <div class="mb-2">
                        <label for="image" class="form-label">{{ __('Image (Optional)')}}</label>
                        <input id="inputEditMusicImage" class="form-control" type="file" accept="image/*" name="image" >
                    </div>
                    <div class="mb-2">
                        <label for="sound" class="form-label">{{ __('Select Music (Optional)')}}</label>
                        <input id="inputEditMusicFile" class="form-control" type="file" accept="audio/*" id="editMusicSound" name="sound">
                    </div>
                    <div class="mb-2">
                        <label for="title" class="form-label">{{ __('Title')}}</label>
                        <input class="form-control" type="text" id="editMusicTitle" name="title" required>
                    </div>
                    <div class="mb-2">
                        <label for="title" class="form-label">{{ __('Category')}}</label>
                        <select name="category_id" id="editMusicCategory" class="form-control select2" data-toggle="select2" required>
                            <option selected disabled>{{ __('Select Category')}}</option>
                            @foreach($categories as $category)
                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="row">
                        <div class="mb-2 col-6">
                            <label for="duration" class="form-label">{{ __('Duration')}}</label>
                            <input class="form-control" type="text" id="editMusicDuration" name="duration" required>
                        </div>
                        <div class="mb-2 col-6">
                            <label for="artist" class="form-label">{{ __('Artist')}}</label>
                            <input class="form-control" type="text" id="editMusicArtist" name="artist" required>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">{{ __('Close')}}</button>
                    <button type="submit" class="btn btn-primary">
                        <span class="spinner-border spinner-border-sm me-1 spinner hide" role="status" aria-hidden="true"></span>
                        {{ __('Submit')}}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
{{-- Add Music Modal --}}
<div id="addMusicModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="standard-modalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="standard-modalLabel">{{ __('Add Music')}}</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-hidden="true"></button>
            </div>
            <form id="addMusicForm" method="POST">
                <div class="modal-body">
                    <div class="mb-2 d-flex align-items-center">
                        <img id="imgAddMusicPreview" src="{{ url('assets/img/placeholder.png')}}" alt="" class="rounded" height="100" width="100">
                        <audio id="audioAddMusicPreview" class="ms-2" controls="">
                            <source src="" type="audio/mp3">
                        </audio>
                    </div>
                    <div class="mb-2">
                        <label for="image" class="form-label">{{ __('Image')}}</label>
                        <input id="inputAddMusicImage" class="form-control" type="file" accept="image/*" id="image" name="image" required>
                    </div>
                    <div class="mb-2">
                        <label for="sound" class="form-label">{{ __('Select Music')}}</label>
                        <input id="inputAddMusicFile" class="form-control" type="file" accept="audio/*" id="sound" name="sound" required>
                    </div>
                    <div class="mb-2">
                        <label for="title" class="form-label">{{ __('Title')}}</label>
                        <input class="form-control" type="text" id="title" name="title" required>
                    </div>
                    <div class="mb-2">
                        <label for="title" class="form-label">{{ __('Category')}}</label>
                        <select name="category_id" id="" class="form-control select2 remove-searchbar" data-toggle="select2" required>
                            <option selected disabled>{{ __('Select Category')}}</option>
                            @foreach($categories as $category)
                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="row">
                        <div class="mb-2 col-6">
                            <label for="duration" class="form-label">{{ __('Duration')}}</label>
                            <input class="form-control" type="text" id="duration" name="duration" required>
                        </div>
                        <div class="mb-2 col-6">
                            <label for="artist" class="form-label">{{ __('Artist')}}</label>
                            <input class="form-control" type="text" id="artist" name="artist" required>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">{{ __('Close')}}</button>
                    <button type="submit" class="btn btn-primary">
                        <span class="spinner-border spinner-border-sm me-1 spinner hide" role="status" aria-hidden="true"></span>
                        {{ __('Submit')}}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
{{-- Add Category Modal --}}
<div id="addMusicCategoryModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="standard-modalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="standard-modalLabel">{{ __('Add Category')}}</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-hidden="true"></button>
            </div>
            <form id="addMusicCategoryForm" method="POST">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="name" class="form-label">{{ __('Name')}}</label>
                        <input class="form-control" type="text" id="name" name="name" required="">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">{{ __('Close')}}</button>
                    <button type="submit" class="btn btn-primary">
                        <span class="spinner-border spinner-border-sm me-1 spinner hide" role="status" aria-hidden="true"></span>
                        {{ __('Submit')}}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
{{-- Edit Category Modal --}}
<div id="editMusicCategoryModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="standard-modalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="standard-modalLabel">{{ __('Edit Category')}}</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-hidden="true"></button>
            </div>
            <form id="editMusicCategoryForm" method="POST">
                <input type="hidden" name="id" id="editMusicCatId">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="name" class="form-label">{{ __('Name')}}</label>
                        <input class="form-control" type="text" id="editMusicCatName" name="name" required="">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">{{ __('Close')}}</button>
                    <button type="submit" class="btn btn-primary">
                        <span class="spinner-border spinner-border-sm me-1 spinner hide" role="status" aria-hidden="true"></span>
                        {{ __('Submit')}}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection
