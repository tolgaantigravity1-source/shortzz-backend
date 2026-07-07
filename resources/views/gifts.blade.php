@extends('include.app')
@section('script')
<script src="{{ asset('assets/script/gifts.js') }}"></script>

<script>

</script>
@endsection
@section('content')

<div class="card">
    <div class="card-header d-flex align-items-center border-bottom">
        <h4 class="card-title mb-0 header-title">
            {{ __('Gifts')}}
        </h4>
        <a data-bs-toggle="modal" data-bs-target="#addGiftModal" class="btn btn-dark ms-auto">{{ __('Add Gift')}}</a>
    </div>
    <div class="card-body">
        <div id="gift-list" class="row">
            {{-- Gifts --}}
            @foreach ($gifts as $gift)
            <div class="col-md-2">
                <div class="card gift-card text-center">
                    <div class="card-body">
                    <div class="gift-img">
                        <img src="{{$baseUrl}}{{$gift->image}}" alt="" class="img-fluid">
                    </div>
                    <h5> {{$gift->coin_price}} {{__('Coins')}} </h5>
                    <div class="gift-card-action">
                        <div class='d-flex justify-content-center align-items-center'>
                            <a href="#" data-gifturl="{{$baseUrl}}{{$gift->image}}" data-coinprice="{{$gift->coin_price}}" rel="{{$gift->id}}" class="action-btn edit d-flex align-items-center justify-content-center btn border rounded-2 text-success ms-1">
                                <i class="uil-pen"></i>
                            </a>
                            <a href="#" rel="{{$gift->id}}" class="action-btn delete d-flex align-items-center justify-content-center btn border rounded-2 text-danger ms-1">
                                <i class='uil-trash-alt'></i>
                            </a>
                        </div>
                    </div>
                    </div>
                </div>
            </div>
            @endforeach

        </div>
    </div>
</div>
{{-- Add Modal --}}
<div id="addGiftModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="standard-modalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="standard-modalLabel">{{ __('Add Gift')}}</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-hidden="true"></button>
            </div>
            <form id="addGiftForm" method="POST">
                <div class="modal-body">
                    <img id="imgAddGiftPreview" src="{{ url('assets/img/placeholder.png')}}" alt="" class="rounded" height="100" width="100">
                    <div class="my-3">
                        <label for="image" class="form-label">{{ __('Image')}}</label>
                        <input id="inputAddGiftImage" class="form-control" type="file" accept="image/*" id="image" name="image" required>
                    </div>

                    <div class="mb-3">
                        <label for="coin_price" class="form-label">{{ __('Coin Price')}}</label>
                        <input class="form-control" type="number" min="1" id="coin_price" name="coin_price" required>
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
<div id="editGiftModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="standard-modalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="standard-modalLabel">{{ __('Edit Gift')}}</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-hidden="true"></button>
            </div>
            <form id="editGiftForm" method="POST">
                <input type="hidden" name="id" id="editGiftId">
                <div class="modal-body">
                    <img id="imgEditGiftPreview" src="{{ url('assets/img/placeholder.png')}}" alt="" class="rounded" height="100" width="100">
                    <div class="my-3">
                        <label for="image" class="form-label">{{ __('Image')}} (Select To Edit Only)</label>
                        <input id="inputEditGiftImage" class="form-control" type="file" accept="image/*" id="image" name="image">
                    </div>

                    <div class="mb-3">
                        <label for="editGiftCoinPrice" class="form-label">{{ __('Coin Price')}}</label>
                        <input id="editGiftCoinPrice" class="form-control" type="number" min="1" id="editGiftCoinPrice" name="coin_price" required>
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
