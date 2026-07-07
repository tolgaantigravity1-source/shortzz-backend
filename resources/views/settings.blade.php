@extends('include.app')
@section('script')
    <script src="{{ asset('assets/script/settings.js') }}"></script>
    <!-- Quill Editor js -->
    <script src="{{ asset('assets/vendor/quill/quill.js') }}"></script>
    <script>
        // Add new SHA field
        $("#addSha").on("click", function() {
            let field = `
            <div class="input-group mb-2 sha-field">
                <input type="text" class="form-control sha-input" name="sha_256[]" placeholder="Enter SHA 256">
                <button type="button" class="btn btn-danger remove-sha">-</button>
            </div>`;
            $("#shaContainer").append(field);
        });

        // Remove SHA field
        $(document).on("click", ".remove-sha", function() {
            $(this).closest(".sha-field").remove();
        });

        $(document).ready(function() {
            $("#checkValidationOfApple").on("click", function() {
                let baseUrl = "https://app-site-association.cdn-apple.com/a/v1/baseUrl";

                let appUrl = "{{ config('app.url') }}";
                // Remove trailing slash
                let domainOnly = appUrl.replace(/^https?:\/\//, '').replace(/\/$/, '');

                let newUrl = baseUrl.replace("baseUrl", domainOnly);

                window.open(newUrl, "_blank");
            });

            $("#checkValidationOfAndroid").on("click", function() {
                let baseUrl =
                    "https://digitalassetlinks.googleapis.com/v1/statements:list?source.web.site=baseUrl&relation=delegate_permission/common.handle_all_urls";

                let appUrl = "{{ config('app.url') }}";
                // Remove trailing slash
                let cleanUrl = appUrl.replace(/\/$/, '');

                let newUrl = baseUrl.replace("baseUrl", cleanUrl);

                window.open(newUrl, "_blank");
            });
        });
    </script>
@endsection

@section('content')
    <div class="row">
        <div class="col-sm-2 mb-2 mb-sm-0">
            <div class="card">
                <div class="card-body p-2">
                    <div class="nav flex-column nav-pills" id="v-pills-tab" role="tablist" aria-orientation="vertical">
                        <a class="main-nav-link nav-link first-nav-link" id="v-pills-appSettings-tab" data-bs-toggle="pill"
                            href="#v-pills-appSettings" role="tab" aria-controls="v-pills-password"
                            aria-selected="false">
                            <i class="mdi mdi-settings-outline d-md-none d-block"></i>
                            <span class="d-none d-md-block">{{ __('App Settings') }}</span>
                        </a>
                        <a class="main-nav-link nav-link" id="v-pills-limits-tab" data-bs-toggle="pill"
                            href="#v-pills-limits" role="tab" aria-controls="v-pills-limits" aria-selected="false">
                            <i class="mdi mdi-settings-outline d-md-none d-block"></i>
                            <span class="d-none d-md-block">{{ __('Limits') }}</span>
                        </a>
                        <a class="main-nav-link nav-link" id="v-pills-livestream-tab" data-bs-toggle="pill"
                            href="#v-pills-livestream" role="tab" aria-controls="v-pills-livestream"
                            aria-selected="false">
                            <i class="mdi mdi-settings-outline d-md-none d-block"></i>
                            <span class="d-none d-md-block">{{ __('Livestream') }}</span>
                        </a>
                        <a class="main-nav-link nav-link" id="v-pills-gif-tab" data-bs-toggle="pill" href="#v-pills-gif"
                            role="tab" aria-controls="v-pills-gif" aria-selected="false">
                            <i class="mdi mdi-settings-outline d-md-none d-block"></i>
                            <span class="d-none d-md-block">{{ __('GIPHY') }}</span>
                        </a>
                        <a class="main-nav-link nav-link" id="v-pills-sightEngine-tab" data-bs-toggle="pill"
                            href="#v-pills-sightEngine" role="tab" aria-controls="v-pills-sightEngine"
                            aria-selected="false">
                            <i class="mdi mdi-settings-outline d-md-none d-block"></i>
                            <span class="d-none d-md-block">{{ __('SightEngine') }}</span>
                        </a>
                        <a class="main-nav-link nav-link" id="v-pills-admob-tab" data-bs-toggle="pill" href="#v-pills-admob"
                            role="tab" aria-controls="v-pills-admob" aria-selected="false">
                            <i class="mdi mdi-settings-outline d-md-none d-block"></i>
                            <span class="d-none d-md-block">{{ __('Admob') }}</span>
                        </a>
                        <a class="main-nav-link nav-link" id="v-pills-onBoarding-tab" data-bs-toggle="pill"
                            href="#v-pills-onBoarding" role="tab" aria-controls="v-pills-onBoarding"
                            aria-selected="false">
                            <i class="mdi mdi-settings-outline d-md-none d-block"></i>
                            <span class="d-none d-md-block">{{ __('Onboarding') }}</span>
                        </a>
                        <a class="main-nav-link nav-link" id="v-pills-userLevels-tab" data-bs-toggle="pill"
                            href="#v-pills-userLevels" role="tab" aria-controls="v-pills-userLevels"
                            aria-selected="false">
                            <i class="mdi mdi-settings-outline d-md-none d-block"></i>
                            <span class="d-none d-md-block">{{ __('User Levels') }}</span>
                        </a>
                        <a class="main-nav-link nav-link" id="v-pills-reportReasons-tab" data-bs-toggle="pill"
                            href="#v-pills-reportReasons" role="tab" aria-controls="v-pills-reportReasons"
                            aria-selected="false">
                            <i class="mdi mdi-settings-outline d-md-none d-block"></i>
                            <span class="d-none d-md-block">{{ __('Report Reasons') }}</span>
                        </a>
                        <a class="main-nav-link nav-link" id="v-pills-withdrawalGateways-tab" data-bs-toggle="pill"
                            href="#v-pills-withdrawalGateways" role="tab" aria-controls="v-pills-withdrawalGateways"
                            aria-selected="false">
                            <i class="mdi mdi-settings-outline d-md-none d-block"></i>
                            <span class="d-none d-md-block">{{ __('Withdrawal Gateways') }}</span>
                        </a>
                        <a class="main-nav-link nav-link" id="v-pills-deepar-tab" data-bs-toggle="pill"
                            href="#v-pills-deepar" role="tab" aria-controls="v-pills-deepar" aria-selected="false">
                            <i class="mdi mdi-settings-outline d-md-none d-block"></i>
                            <span class="d-none d-md-block">{{ __('DeepAR Settings') }}</span>
                        </a>
                        <a class="main-nav-link nav-link" id="v-pills-deeplinking-tab" data-bs-toggle="pill"
                            href="#v-pills-deeplinking" role="tab" aria-controls="v-pills-deeplinking"
                            aria-selected="false">
                            <i class="mdi mdi-settings-outline d-md-none d-block"></i>
                            <span class="d-none d-md-block">{{ __('Deeplink Settings') }}</span>
                        </a>
                        <hr>
                        <a class="main-nav-link nav-link" id="v-pills-privacy-policy-tab" data-bs-toggle="pill"
                            href="#v-pills-privacy-policy" role="tab" aria-controls="v-pills-privacy-policy"
                            aria-selected="false">
                            <i class="mdi mdi-settings-outline d-md-none d-block"></i>
                            <span class="d-none d-md-block">{{ __('Privacy Policy') }}</span>
                        </a>
                        <a class="main-nav-link nav-link" id="v-pills-terms-tab" data-bs-toggle="pill"
                            href="#v-pills-terms" role="tab" aria-controls="v-pills-terms" aria-selected="false">
                            <i class="mdi mdi-settings-outline d-md-none d-block"></i>
                            <span class="d-none d-md-block">{{ __('Terms Of Uses') }}</span>
                        </a>
                        <hr>
                        <a class="main-nav-link nav-link " id="v-pills-setting-tab" data-bs-toggle="pill"
                            href="#v-pills-setting" role="tab" aria-controls="v-pills-setting" aria-selected="true">
                            <i class="mdi mdi-home-variant d-md-none d-block"></i>
                            <span class="d-none d-md-block">{{ __('Admin Settings') }}</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-10">
            <div class="tab-content" id="v-pills-tabContent">
                {{-- Admin Settings --}}
                <div class="tab-pane fade " id="v-pills-setting" role="tabpanel" aria-labelledby="v-pills-setting-tab">
                    {{-- 1st card --}}
                    <div class="card">
                        <div class="card-header border-bottom">
                            <h4 class="m-0 header-title">{{ __('Admin Settings') }}</h4>
                        </div>
                        <div class="card-body">
                            <form id="brandSettingForm" method="POST">
                                <div class="row">
                                    <div class="col-md-3 mb-3">
                                        <div class="mb-0 bg-secondary-lighten border p-2 rounded-3">
                                            <label for="title" class="form-label">{{ __('Title') }}</label>
                                            <input type="text" class="form-control" id="app_name" name="app_name"
                                                placeholder="Enter title" value="{{ $setting->app_name }}">
                                        </div>
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <div class="mb-0 bg-secondary-lighten border p-2 rounded-3">
                                            <label for="favicon" class="form-label">{{ __('Favicon') }}</label>
                                            <input type="file" id="favicon" name="favicon" class="form-control">
                                            <img class="mt-2" width="80"
                                                src="{{ asset('assets/img/favicon.png') }}" alt="">
                                        </div>
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <div class="mb-0 bg-secondary-lighten border p-2 rounded-3">
                                            <label for="logo_dark" class="form-label">{{ __('Logo (Dark)') }}</label>
                                            <input type="file" id="logo_dark" name="logo_dark" class="form-control">
                                            <img class="mt-2" width="80"
                                                src="{{ asset('assets/img/logo-dark.png') }}" alt="">
                                        </div>
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <div class="mb-0 bg-secondary-lighten border p-2 rounded-3">
                                            <label for="logo_light" class="form-label">{{ __('Logo (Light)') }}</label>
                                            <input type="file" id="logo_light" name="logo_light"
                                                class="form-control">
                                            <img class="mt-2" width="80" src="{{ asset('assets/img/logo.png') }}"
                                                alt="">
                                        </div>
                                    </div>
                                </div>
                                <hr>
                                <button type="submit" class="btn btn-primary">{{ __('Save') }}</button>
                            </form>
                        </div>
                    </div>
                    {{-- Password --}}
                    @if ($userType == 1)
                        <div class="card">
                            <div class="card-header border-bottom">
                                <h4 class="m-0 header-title">{{ __('Password') }}</h4>
                            </div>
                            <div class="card-body">
                                <form id="changePasswordForm" method="POST">
                                    <input type="hidden" name="user_type" value="{{ $userType }}">
                                    <div class="row mb-3">
                                        <div class="col-md-3 mb-3">
                                            <div class="mb-0 bg-secondary-lighten border p-2 rounded-3">
                                                <label for="password" class="form-label">{{ __('Old Password') }}</label>
                                                <div class="input-group input-group-merge">
                                                    <input type="password" id="password" name="old_password"
                                                        class="form-control" placeholder="Enter your password">
                                                    <div class="input-group-text" data-password="false">
                                                        <span class="password-eye"></span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-3 mb-3">
                                            <div class="mb-0 bg-secondary-lighten border p-2 rounded-3">
                                                <label for="password" class="form-label">{{ __('New Password') }}</label>
                                                <div class="input-group input-group-merge">
                                                    <input type="password" id="new_password" name="new_password"
                                                        class="form-control" placeholder="Enter your password">
                                                    <div class="input-group-text" data-password="false">
                                                        <span class="password-eye"></span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <button type="submit" class="btn btn-primary">{{ __('Save') }}</button>
                                </form>
                            </div>
                        </div>
                    @endif

                </div>
                {{-- App Settings --}}
                <div class="tab-pane fade first-tab-pane" id="v-pills-appSettings" role="tabpanel"
                    aria-labelledby="v-pills-password-tab">
                    {{-- 1st card --}}
                    <div class="card">
                        <div class="card-header border-bottom">
                            <h4 class="m-0 header-title">{{ __('App Settings') }}</h4>
                        </div>
                        <div class="card-body">
                            <span class="fs-6">*Make sure to set coin value according to your currency.</span><br>
                            <span class="fs-6">*Users can use withdrawal functions only if it the switch is on
                                below.</span>
                            <form class="mt-2" id="basicSettingForm" method="POST">
                                <div class="row">
                                    <div class="col-md-4 mb-3">
                                        <div class="mb-0 bg-secondary-lighten border p-2 rounded-3">
                                            <label for="currency" class="form-label">{{ __('Currency') }}</label>
                                            <input type="text" class="form-control" id="currency" name="currency"
                                                value="{{ $setting->currency }}">
                                        </div>
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <div class="mb-0 bg-secondary-lighten border p-2 rounded-3">
                                            <label for="coin_value" class="form-label">1 {{ __('Coin Value') }}</label>
                                            <input type="number" step="any" class="form-control" id="coin_value"
                                                name="coin_value" value="{{ $setting->coin_value }}">
                                        </div>
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <div class="mb-0 bg-secondary-lighten border p-2 rounded-3">
                                            <label for="min_redeem_coins"
                                                class="form-label">{{ __('Min. Coins To Withdraw') }}</label>
                                            <input type="number" min="1" step="1" class="form-control"
                                                id="min_redeem_coins" name="min_redeem_coins"
                                                value="{{ $setting->min_redeem_coins }}">
                                        </div>
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <div class="mb-0 bg-secondary-lighten border p-2 rounded-3">
                                            <label for="help_mail" class="form-label">{{ __('Help Email') }}</label>
                                            <input type="email" class="form-control" id="help_mail" name="help_mail"
                                                value="{{ $setting->help_mail }}">
                                        </div>
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <div class="mb-0 bg-secondary-lighten border p-2 rounded-3">
                                            <label for=""
                                                class="form-label">{{ __('Compress Post/Story Videos') }}</label>
                                            <div class="mb-0">
                                                <input name="is_compress" type="checkbox" id="switchCompressVideosStatus"
                                                    {{ $setting->is_compress == 1 ? 'checked' : '' }}
                                                    data-switch="primary" />
                                                <label for="switchCompressVideosStatus"></label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <div class="mb-0 bg-secondary-lighten border p-2 rounded-3">
                                            <label for=""
                                                class="form-label">{{ __('Allow Withdrawal Of Coins') }}</label>
                                            <div class="mb-0">
                                                <input name="is_withdrawal_on" type="checkbox" id="switchWithdrawal"
                                                    {{ $setting->is_withdrawal_on == 1 ? 'checked' : '' }}
                                                    data-switch="primary" />
                                                <label for="switchWithdrawal"></label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                {{-- Watermark --}}
                                <h5>{{ __('REWARD SETTINGS') }}</h5>
                                <hr>
                                <span class="fs-6">*Users will get the following number of coins as a bonus when they
                                    register, if the switch below is turned on.</span><br>
                                <div class="row mt-2">
                                    <div class="col-md-4 mb-3">
                                        <div class="mb-0 bg-secondary-lighten border p-2 rounded-3">
                                            <label for=""
                                                class="form-label">{{ __('Registration Bonus Status') }}</label>
                                            <div class="mb-0">
                                                <input name="registration_bonus_status" type="checkbox"
                                                    id="switcRegistrationBonusStatus"
                                                    {{ $setting->registration_bonus_status == 1 ? 'checked' : '' }}
                                                    data-switch="primary" />
                                                <label for="switcRegistrationBonusStatus"></label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <div class="mb-0 bg-secondary-lighten border p-2 rounded-3">
                                            <label for="registration_bonus_amount"
                                                class="form-label">{{ __('Registration Bonus Amount (Coins)') }}</label>
                                            <input type="number" min="1" step="1" class="form-control"
                                                id="registration_bonus_amount" name="registration_bonus_amount"
                                                value="{{ $setting->registration_bonus_amount }}">
                                        </div>
                                    </div>
                                </div>
                                {{-- Watermark --}}
                                <h5>{{ __('WATERMARK SETTINGS') }}</h5>
                                <hr>
                                <div class="row">
                                    <div class="col-md-4 mb-3">
                                        <div class="mb-0 bg-secondary-lighten border p-2 rounded-3">
                                            <label for="" class="form-label">{{ __('Watermark Videos') }}</label>
                                            <div class="mb-0">
                                                <input name="watermark_status" type="checkbox" id="switchWatermarkStatus"
                                                    {{ $setting->watermark_status == 1 ? 'checked' : '' }}
                                                    data-switch="primary" />
                                                <label for="switchWatermarkStatus"></label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <div class="mb-0 bg-secondary-lighten border p-2 rounded-3">
                                            <label for="watermark_image"
                                                class="form-label">{{ __('Watermark Image') }}</label>
                                            <input type="file" id="watermark_image" name="watermark_image"
                                                class="form-control">
                                            <img class="mt-2" width="80"
                                                src="{{ $baseUrl }}{{ $setting->watermark_image }}" alt="">
                                        </div>
                                    </div>
                                </div>


                                <button type="submit" class="btn btn-primary">{{ __('Save') }}</button>
                            </form>

                        </div>
                    </div>

                </div>
                {{-- Admob --}}
                <div class="tab-pane fade" id="v-pills-admob" role="tabpanel" aria-labelledby="v-pills-admob-tab">
                    <div class="card">
                        <div class="card-header border-bottom">
                            <h4 class="m-0 header-title">{{ __('Admob') }}</h4>
                        </div>
                        <div class="card-body">
                            <form id="admobForm" method="POST">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="card">
                                            <div class="card-body">
                                                <div class="d-flex align-items-center">
                                                    <h4 class="mt-0 d-inline">{{ __('Android') }}</h4>
                                                    <!-- Admob Android Switch-->
                                                    <div class="d-inline ms-2 mb-0">
                                                        <input type="checkbox" id="switchAdmobAndroidStatus"
                                                            {{ $setting->admob_android_status == 1 ? 'checked' : '' }}
                                                            data-switch="primary" />
                                                        <label for="switchAdmobAndroidStatus"></label>
                                                    </div>
                                                </div>
                                                <hr>
                                                <div class="mb-3">
                                                    <label for="admob_banner"
                                                        class="form-label">{{ __('Banner Ad Unit') }}</label>
                                                    <input class="form-control" type="text" name="admob_banner"
                                                        placeholder="Enter Ad Unit" required=""
                                                        value="{{ $setting->admob_banner }}">
                                                </div>
                                                <div class="mb-3">
                                                    <label for="admob_int"
                                                        class="form-label">{{ __('Interstitial Ad Unit') }}</label>
                                                    <input class="form-control" type="text" name="admob_int"
                                                        placeholder="Enter Ad Unit" required=""
                                                        value="{{ $setting->admob_int }}">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="card">
                                            <div class="card-body">
                                                <div class="d-flex align-items-center">
                                                    <h4 class="mt-0 d-inline">{{ __('iOS') }}</h4>
                                                    <!-- Admob iOS Switch-->
                                                    <div class="d-inline ms-2 mb-0">
                                                        <input type="checkbox" id="switchAdmobiOSStatus"
                                                            {{ $setting->admob_ios_status == 1 ? 'checked' : '' }}
                                                            data-switch="primary" />
                                                        <label for="switchAdmobiOSStatus"></label>
                                                    </div>
                                                </div>
                                                <hr>
                                                <div class="mb-3">
                                                    <label for="admob_banner_ios"
                                                        class="form-label">{{ __('Banner Ad Unit') }}</label>
                                                    <input class="form-control" type="text" name="admob_banner_ios"
                                                        placeholder="Enter ID" required=""
                                                        value="{{ $setting->admob_banner_ios }}">
                                                </div>
                                                <div class="mb-3">
                                                    <label for="admob_int_ios"
                                                        class="form-label">{{ __('Interstitial Ad Unit') }}</label>
                                                    <input class="form-control" type="text" name="admob_int_ios"
                                                        placeholder="Enter ID" required=""
                                                        value="{{ $setting->admob_int_ios }}">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <button type="submit" class="btn btn-primary">{{ __('Save') }}</button>
                            </form>
                        </div>
                    </div>
                </div>
                {{-- Limits --}}
                <div class="tab-pane fade" id="v-pills-limits" role="tabpanel" aria-labelledby="v-pills-limits-tab">
                    <div class="card">
                        <div class="card-header border-bottom">
                            <h4 class="m-0 header-title">{{ __('Limits') }}</h4>
                        </div>
                        <div class="card-body">
                            <form id="limitSettingForm" method="POST">
                                <div class="row">
                                    <div class="col-md-4 mb-3">
                                        <div class="mb-0 bg-secondary-lighten border p-2 rounded-3">
                                            <label for="max_upload_daily"
                                                class="form-label">{{ __('Max. Post Upload/Day') }}</label>
                                            <input type="number" min="1" class="form-control"
                                                id="max_upload_daily" name="max_upload_daily"
                                                value="{{ $setting->max_upload_daily }}">
                                        </div>
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <div class="mb-0 bg-secondary-lighten border p-2 rounded-3">
                                            <label for="max_story_daily"
                                                class="form-label">{{ __('Max. Stories/Day') }}</label>
                                            <input type="number" min="1" class="form-control"
                                                id="max_story_daily" name="max_story_daily"
                                                value="{{ $setting->max_story_daily }}">
                                        </div>
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <div class="mb-0 bg-secondary-lighten border p-2 rounded-3">
                                            <label for="max_comment_daily"
                                                class="form-label">{{ __('Max. Comments/Day') }}</label>
                                            <input type="number" min="1" class="form-control"
                                                id="max_comment_daily" name="max_comment_daily"
                                                value="{{ $setting->max_comment_daily }}">
                                        </div>
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <div class="mb-0 bg-secondary-lighten border p-2 rounded-3">
                                            <label for="max_comment_reply_daily"
                                                class="form-label">{{ __('Max. Comment Reply/Day') }}</label>
                                            <input type="number" min="1" class="form-control"
                                                id="max_comment_reply_daily" name="max_comment_reply_daily"
                                                value="{{ $setting->max_comment_reply_daily }}">
                                        </div>
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <div class="mb-0 bg-secondary-lighten border p-2 rounded-3">
                                            <label for="max_post_pins"
                                                class="form-label">{{ __('Max. Post Pins') }}</label>
                                            <input type="number" min="1" class="form-control" id="max_post_pins"
                                                name="max_post_pins" value="{{ $setting->max_post_pins }}">
                                        </div>
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <div class="mb-0 bg-secondary-lighten border p-2 rounded-3">
                                            <label for="max_comment_pins"
                                                class="form-label">{{ __('Max. Comment Pins') }}</label>
                                            <input type="number" min="1" class="form-control"
                                                id="max_comment_pins" name="max_comment_pins"
                                                value="{{ $setting->max_comment_pins }}">
                                        </div>
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <div class="mb-0 bg-secondary-lighten border p-2 rounded-3">
                                            <label for="max_images_per_post"
                                                class="form-label">{{ __('Max. Images Per Post') }}</label>
                                            <input type="number" min="1" class="form-control"
                                                id="max_images_per_post" name="max_images_per_post"
                                                value="{{ $setting->max_images_per_post }}">
                                        </div>
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <div class="mb-0 bg-secondary-lighten border p-2 rounded-3">
                                            <label for="max_user_links"
                                                class="form-label">{{ __('Max. User Links') }}</label>
                                            <input type="number" min="1" class="form-control"
                                                id="max_user_links" name="max_user_links"
                                                value="{{ $setting->max_user_links }}">
                                        </div>
                                    </div>
                                </div>
                                <button type="submit" class="btn btn-primary">{{ __('Save') }}</button>
                            </form>
                        </div>
                    </div>
                </div>
                {{-- Livestream --}}
                <div class="tab-pane fade" id="v-pills-livestream" role="tabpanel"
                    aria-labelledby="v-pills-livestream-tab">
                    <div class="card">
                        <div class="card-header border-bottom">
                            <h4 class="m-0 header-title">{{ __('Livestream') }}</h4>
                        </div>
                        <div class="card-body">
                            <span class="fs-6">* Set 0 as a value either in Timeout Minutes or Min. Viewers required to
                                stop Livestream Timeout function.</span><br>
                            <span class="fs-6">* If you turn ON dummy live streams, It will display dummy lives on the
                                app. In order to show dummy lives, There must be dummy live videos added in the list.</span>
                            <form class="mt-2" id="livestreamSettingForm" method="POST">
                                <div class="row">
                                    <div class="col-md-4 mb-3">
                                        <div class="mb-0 bg-secondary-lighten border p-2 rounded-3">
                                            <label for="min_followers_for_live"
                                                class="form-label">{{ __('Min. Followers needed to go Live') }}</label>
                                            <input type="number" step="1" class="form-control"
                                                id="min_followers_for_live" name="min_followers_for_live"
                                                value="{{ $setting->min_followers_for_live }}">
                                        </div>
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <div class="mb-0 bg-secondary-lighten border p-2 rounded-3">
                                            <label for="live_min_viewers"
                                                class="form-label">{{ __('Min. Viewers Required to continue live') }}</label>
                                            <input type="number" step="1" class="form-control"
                                                id="live_min_viewers" name="live_min_viewers"
                                                value="{{ $setting->live_min_viewers }}">
                                        </div>
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <div class="mb-0 bg-secondary-lighten border p-2 rounded-3">
                                            <label for="live_timeout"
                                                class="form-label">{{ __('Time Out Minutes (if not get min. viewers)') }}</label>
                                            <input type="number" step="1" class="form-control" id="live_timeout"
                                                name="live_timeout" value="{{ $setting->live_timeout }}">
                                        </div>
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <div class="mb-0 bg-secondary-lighten border p-2 rounded-3">
                                            <label for="" class="form-label">{{ __('PK Battle') }}</label>
                                            <div class="mb-0">
                                                <input name="live_battle" type="checkbox" id="switchPKBattle"
                                                    {{ $setting->live_battle == 1 ? 'checked' : '' }}
                                                    data-switch="primary" />
                                                <label for="switchPKBattle"></label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <div class="mb-0 bg-secondary-lighten border p-2 rounded-3">
                                            <label for=""
                                                class="form-label">{{ __('Dummy Live Streams') }}</label>
                                            <div class="mb-0">
                                                <input name="live_dummy_show" type="checkbox" id="switchDummyLiveShow"
                                                    {{ $setting->live_dummy_show == 1 ? 'checked' : '' }}
                                                    data-switch="primary" />
                                                <label for="switchDummyLiveShow"></label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                {{-- Watermark --}}
                                <h5>{{ __('ZEGO CLOUD SETTINGS') }}</h5>
                                <hr>
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <div class="mb-0 bg-secondary-lighten border p-2 rounded-3">
                                            <label for="zego_app_id"
                                                class="form-label">{{ __('Zego Cloud App ID') }}</label>
                                            <input type="text" class="form-control" id="zego_app_id"
                                                name="zego_app_id"
                                                value="{{ $userType == 0 ? '---------' : $setting->zego_app_id }}">
                                        </div>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <div class="mb-0 bg-secondary-lighten border p-2 rounded-3">
                                            <label for="zego_app_sign"
                                                class="form-label">{{ __('Zego Cloud App Sign') }}</label>
                                            <input type="text" class="form-control" id="zego_app_sign"
                                                name="zego_app_sign"
                                                value="{{ $userType == 0 ? '---------' : $setting->zego_app_sign }}">
                                        </div>
                                    </div>
                                </div>
                                <button type="submit" class="btn btn-primary">{{ __('Save') }}</button>
                            </form>
                        </div>
                    </div>
                </div>
                {{-- GIF --}}
                <div class="tab-pane fade" id="v-pills-gif" role="tabpanel" aria-labelledby="v-pills-gif-tab">
                    <div class="card">
                        <div class="card-header border-bottom">
                            <h4 class="m-0 header-title">{{ __('GIPHY') }}</h4>
                        </div>
                        <div class="card-body">
                            <span class="fs-6">*If you turn this On, Users will have GIF options in Chat &
                                Comment.</span><br>
                            <span class="fs-6">*Make sure you have added correct GIPHY keys properly.</span>
                            <form class="mt-2" id="gifSettingForm" method="POST">
                                <div class="row">
                                    <div class="col-md-4 mb-3">
                                        <div class="mb-0 bg-secondary-lighten border p-2 rounded-3">
                                            <label for="" class="form-label">{{ __('GIF Supported') }}</label>
                                            <div class="mb-0">
                                                <input name="gif_support" type="checkbox" id="switchGifSupport"
                                                    {{ $setting->gif_support == 1 ? 'checked' : '' }}
                                                    data-switch="primary" />
                                                <label for="switchGifSupport"></label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <div class="mb-0 bg-secondary-lighten border p-2 rounded-3">
                                            <label for="giphy_key" class="form-label">{{ __('GIPHY API Key') }}</label>
                                            <input type="text" class="form-control" id="giphy_key" name="giphy_key"
                                                value="{{ $userType == 0 ? '---------' : $setting->giphy_key }}">
                                        </div>
                                    </div>
                                </div>
                                <button type="submit" class="btn btn-primary">{{ __('Save') }}</button>
                            </form>
                        </div>
                    </div>
                </div>
                {{-- SightEngine --}}
                <div class="tab-pane fade" id="v-pills-sightEngine" role="tabpanel"
                    aria-labelledby="v-pills-sightEngine-tab">
                    <div class="card">
                        <div class="card-header border-bottom">
                            <h4 class="m-0 header-title">{{ __('SightEngine') }}</h4>
                        </div>
                        <div class="card-body">
                            <form id="contentModerationSettingForm" method="POST">
                                <div class="row">
                                    <div class="col-md-4 mb-3">
                                        <div class="mb-0 bg-secondary-lighten border p-2 rounded-3">
                                            <label for=""
                                                class="form-label">{{ __('Content Moderation') }}</label>
                                            <div class="mb-0">
                                                <input name="is_content_moderation" type="checkbox"
                                                    id="switchContentModeration"
                                                    {{ $setting->is_content_moderation == 1 ? 'checked' : '' }}
                                                    data-switch="primary" />
                                                <label for="switchContentModeration"></label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <div class="mb-0 bg-secondary-lighten border p-2 rounded-3">
                                            <label for="sight_engine_api_user"
                                                class="form-label">{{ __('API User') }}</label>
                                            <input type="text" class="form-control" id="sight_engine_api_user"
                                                name="sight_engine_api_user"
                                                value="{{ $userType == 0 ? '---------' : $setting->sight_engine_api_user }}">
                                        </div>
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <div class="mb-0 bg-secondary-lighten border p-2 rounded-3">
                                            <label for="sight_engine_api_secret"
                                                class="form-label">{{ __('API Secret') }}</label>
                                            <input type="text" class="form-control" id="sight_engine_api_secret"
                                                name="sight_engine_api_secret"
                                                value="{{ $userType == 0 ? '---------' : $setting->sight_engine_api_secret }} ">
                                        </div>
                                    </div>

                                    <div class="col-md-4 mb-3">
                                        <div class="mb-0 bg-secondary-lighten border p-2 rounded-3">
                                            <label for="sight_engine_image_workflow_id"
                                                class="form-label">{{ __('Image Workflow ID') }}</label>
                                            <input type="text" class="form-control"
                                                id="sight_engine_image_workflow_id" name="sight_engine_image_workflow_id"
                                                value="{{ $userType == 0 ? '---------' : $setting->sight_engine_image_workflow_id }}">
                                        </div>
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <div class="mb-0 bg-secondary-lighten border p-2 rounded-3">
                                            <label for="sight_engine_video_workflow_id"
                                                class="form-label">{{ __('Video Workflow ID') }}</label>
                                            <input type="text" class="form-control"
                                                id="sight_engine_video_workflow_id" name="sight_engine_video_workflow_id"
                                                value="{{ $userType == 0 ? '---------' : $setting->sight_engine_video_workflow_id }}">
                                        </div>
                                    </div>

                                </div>
                                <button type="submit" class="btn btn-primary">{{ __('Save') }}</button>
                            </form>
                        </div>
                    </div>
                </div>
                {{-- Onboarding --}}
                <div class="tab-pane fade" id="v-pills-onBoarding" role="tabpanel"
                    aria-labelledby="v-pills-onBoarding-tab">
                    <div class="card">
                        <div class="card-header d-flex align-items-center border-bottom">
                            <h4 class="m-0 header-title">{{ __('Onboarding') }}</h4>
                            <a data-bs-toggle="modal" data-bs-target="#addOnBoardingScreenModal"
                                class="btn btn-dark ms-auto">{{ __('Add Onboarding') }}</a>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table id="onboardingScreenTable"
                                    class="table table-centered table-hover w-100 dt-responsive nowrap mt-3">
                                    <thead class="table-light">
                                        <tr>
                                            <th> {{ __('Sortable') }}</th>
                                            <th> {{ __('Position') }}</th>
                                            <th>{{ __('Image') }}</th>
                                            <th>{{ __('Details') }}</th>
                                            <th style="width: 200px;" class="text-end">{{ __('Action') }}</th>
                                        </tr>
                                    </thead>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                {{-- user Levels --}}
                <div class="tab-pane fade" id="v-pills-userLevels" role="tabpanel"
                    aria-labelledby="v-pills-userLevels-tab">
                    <div class="card">
                        <div class="card-header d-flex align-items-center border-bottom">
                            <h4 class="m-0 header-title">{{ __('User Levels') }}</h4>
                            <a data-bs-toggle="modal" data-bs-target="#addUserLevelModal"
                                class="btn btn-dark ms-auto">{{ __('Add User Level') }}</a>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive smallSearchBar">
                                <table id="userLevelTable"
                                    class="table table-centered table-hover w-100 dt-responsive nowrap mt-3">
                                    <thead class="table-light">
                                        <tr>
                                            <th>{{ __('Level') }}</th>
                                            <th>{{ __('Coins Collection') }}</th>
                                            <th style="width: 200px;" class="text-end">{{ __('Action') }}</th>
                                        </tr>
                                    </thead>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                {{-- Report Reasons --}}
                <div class="tab-pane fade" id="v-pills-reportReasons" role="tabpanel"
                    aria-labelledby="v-pills-reportReasons-tab">
                    <div class="card">
                        <div class="card-header d-flex align-items-center border-bottom">
                            <h4 class="m-0 header-title">{{ __('Report Reasons') }}</h4>
                            <a data-bs-toggle="modal" data-bs-target="#addReportReasonModal"
                                class="btn btn-dark ms-auto">{{ __('Add Report Reason') }}</a>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive smallSearchBar">
                                <table id="reportReasonsTable"
                                    class="table table-centered table-hover w-100 dt-responsive nowrap mt-3">
                                    <thead class="table-light">
                                        <tr>
                                            <th>{{ __('Title') }}</th>
                                            <th style="width: 200px;" class="text-end">{{ __('Action') }}</th>
                                        </tr>
                                    </thead>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                {{-- Withdrawal Gateways --}}
                <div class="tab-pane fade" id="v-pills-withdrawalGateways" role="tabpanel"
                    aria-labelledby="v-pills-withdrawalGateways-tab">
                    <div class="card">
                        <div class="card-header d-flex align-items-center border-bottom">
                            <h4 class="m-0 header-title">{{ __('Withdrawal Gateways') }}</h4>
                            <a data-bs-toggle="modal" data-bs-target="#addWithdrawalGatewayModal"
                                class="btn btn-dark ms-auto">{{ __('Add Withdrawal Gateways') }}</a>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive smallSearchBar">
                                <table id="withdrawalGatewayTable"
                                    class="table table-centered table-hover w-100 dt-responsive nowrap mt-3">
                                    <thead class="table-light">
                                        <tr>
                                            <th>{{ __('Title') }}</th>
                                            <th style="width: 200px;" class="text-end">{{ __('Action') }}</th>
                                        </tr>
                                    </thead>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                {{-- DeepAR Settings --}}
                <div class="tab-pane fade" id="v-pills-deepar" role="tabpanel" aria-labelledby="v-pills-deepar-tab">
                    <div class="card">
                        <div class="card-header d-flex align-items-center border-bottom">
                            <h4 class="m-0 header-title">{{ __('DeepAR Settings') }}</h4>
                        </div>
                        <div class="card-body">
                            <form class="mt-2" id="deepARSettingsForm" method="POST">
                                <div class="row">
                                    <div class="col-md-4 mb-3">
                                        <div class="mb-0 bg-secondary-lighten border p-2 rounded-3">
                                            <label for=""
                                                class="form-label">{{ __('Use DeepAR Camera (Off=Simple Camera)') }}</label>
                                            <div class="mb-0">
                                                <input name="is_deepAR" type="checkbox" id="switchDeepARCamera"
                                                    {{ $setting->is_deepAR == 1 ? 'checked' : '' }}
                                                    data-switch="primary" />
                                                <label for="switchDeepARCamera"></label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <div class="mb-0 bg-secondary-lighten border p-2 rounded-3">
                                            <label for="deepar_android_key"
                                                class="form-label">{{ __('DeepAR Android Key') }}</label>
                                            <input type="text" class="form-control" id="deepar_android_key"
                                                name="deepar_android_key"
                                                value="{{ $userType == 0 ? '---------' : $setting->deepar_android_key }}">
                                        </div>
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <div class="mb-0 bg-secondary-lighten border p-2 rounded-3">
                                            <label for="deepar_iOS_key"
                                                class="form-label">{{ __('DeepAR iOS Key') }}</label>
                                            <input type="text" class="form-control" id="deepar_iOS_key"
                                                name="deepar_iOS_key"
                                                value="{{ $userType == 0 ? '---------' : $setting->deepar_iOS_key }}">
                                        </div>
                                    </div>
                                </div>
                                <button type="submit" class="btn btn-primary">{{ __('Save') }}</button>
                            </form>
                        </div>
                    </div>
                    <div class="card">
                        <div class="card-header d-flex align-items-center border-bottom">
                            <h4 class="m-0 header-title">{{ __('DeepAR Filters') }}</h4>
                            <a data-bs-toggle="modal" data-bs-target="#addDeepARFilterModal"
                                class="btn btn-dark ms-auto">{{ __('Add Filter') }}</a>
                        </div>
                        <div class="card-body">
                            <table id="deepARFiltersTable"
                                class="table table-centered table-hover w-100 dt-responsive nowrap mt-3">
                                <thead class="table-light">
                                    <tr>
                                        <th>{{ __('Image') }}</th>
                                        <th>{{ __('Title') }}</th>
                                        <th>{{ __('File') }}</th>
                                        <th style="width: 200px;" class="text-end">{{ __('Action') }}</th>
                                    </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                </div>
                {{-- Deeplinking --}}
                <div class="tab-pane fade" id="v-pills-deeplinking" role="tabpanel"
                    aria-labelledby="v-pills-deeplinking-tab">
                    <div class="card">
                        <div class="card-header border-bottom">
                            <h5 class="m-0">{{ __('Deep Linking') }}</h5>
                        </div>
                        <div class="card-body">
                            <form id="deepLinkingForm" method="POST">
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="mb-1">
                                            <label for="uri_scheme" class="form-label">{{ __('URI Schema') }} <button
                                                    type="button" class="btn btn-secondary p-0 tooltip-icon"
                                                    data-bs-trigger="focus" data-bs-toggle="popover"
                                                    data-bs-title="How to make a Scheme"
                                                    data-bs-content="Use your app name in lowercase with no spaces or special characters (e.g., shortzz, cinereel, myapp2025).">
                                                    ?
                                                </button></label>
                                            <input type="text" class="form-control" id="uri_scheme" name="uri_scheme"
                                                value="{{ $setting->uri_scheme }}">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="mb-1">
                                            <label for="play_store_download_link"
                                                class="form-label">{{ __('Play Store Download Link') }}</label>
                                            <input type="text" class="form-control" id="play_store_download_link"
                                                name="play_store_download_link"
                                                value="{{ $setting->play_store_download_link }}">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="mb-1">
                                            <label for="app_store_download_link"
                                                class="form-label">{{ __('App Store Download Link') }}</label>
                                            <input type="text" class="form-control" id="app_store_download_link"
                                                name="app_store_download_link"
                                                value="{{ $setting->app_store_download_link }}">
                                        </div>
                                    </div>
                                </div>
                                <hr>
                                <button type="submit" class="btn btn-primary">
                                    <span class="spinner-border spinner-border-sm me-1 spinner hide" role="status"
                                        aria-hidden="true"></span>
                                    {{ __('Save') }}
                                </button>
                            </form>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header border-bottom">
                                    <h5 class="m-0">{{ __('Android') }}</h5>
                                </div>
                                <div class="card-body">
                                    <form id="androidDeepLinkingForm" method="POST">
                                        <div class="row">
                                            <div class="mb-3">
                                                <label for="package_name"
                                                    class="form-label">{{ __('Package Name') }} <a href="https://docs.retrytech.com/find_bundle_id_android" target="_blank" type="button" class="btn btn-secondary p-0 tooltip-icon">
                                                    ?
                                                </a></label>
                                                <input type="text" class="form-control" id="package_name"
                                                    name="package_name" value="{{ $packageName }}">
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label">{{ __('SHA 256 Keys') }} <a href="https://docs.retrytech.com/how_to_get_sha1_key" target="_blank" type="button" class="btn btn-secondary p-0 tooltip-icon">
                                                    ?
                                                </a></label>
                                                <div id="shaContainer">
                                                    @if (!empty($sha256))
                                                        @foreach (explode(',', $sha256) as $sha)
                                                            <div class="input-group mb-2 sha-field">
                                                                <input type="text" class="form-control sha-input"
                                                                    name="sha_256[]" value="{{ trim($sha) }}">
                                                                <button type="button"
                                                                    class="btn btn-danger remove-sha">-</button>
                                                            </div>
                                                        @endforeach
                                                    @else
                                                        <div class="input-group mb-2 sha-field">
                                                            <input type="text" class="form-control sha-input"
                                                                name="sha_256[]" placeholder="Enter SHA 256">
                                                            <button type="button"
                                                                class="btn btn-danger remove-sha">-</button>
                                                        </div>
                                                    @endif
                                                </div>
                                                <button type="button" class="btn btn-sm btn-success mt-1"
                                                    id="addSha">+ Add SHA</button>
                                            </div>
                                        </div>
                                        <hr>
                                        <button type="submit" class="btn btn-primary">
                                            <span class="spinner-border spinner-border-sm me-1 spinner hide"
                                                role="status" aria-hidden="true"></span>
                                            {{ __('Save') }}
                                        </button>
                                        <button type="button" id="checkValidationOfAndroid" class="btn btn-success">
                                            {{ __('Check Validation') }}
                                        </button>

                                    </form>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header border-bottom">
                                    <h5 class="m-0">{{ __('iOS') }}</h5>
                                </div>
                                <div class="card-body">
                                    <form id="iOSDeepLinkingForm" method="POST">
                                        <div class="row">
                                            <div class="mb-3">
                                                <label for="package_name_ios"
                                                    class="form-label">{{ __('Bundle Id / Package Name') }} <a href="https://docs.retrytech.com/find_bundle_id_ios" target="_blank" type="button" class="btn btn-secondary p-0 tooltip-icon">
                                                    ?
                                                </a></label>
                                                <input type="text" class="form-control" id="package_name_ios"
                                                    name="package_name" value="{{ $iosPackageName }}" required>
                                            </div>
                                            <div class="mb-3">
                                                <label for="team_id" class="form-label">{{ __('Team Id') }} <a href="https://docs.retrytech.com/find_team_id" target="_blank" type="button" class="btn btn-secondary p-0 tooltip-icon">
                                                    ?
                                                </a></label>
                                                <input type="text" class="form-control" id="team_id" name="team_id"
                                                    value="{{ $iosTeamId }}" required>
                                            </div>
                                        </div>
                                        <hr>
                                        <button type="submit" class="btn btn-primary">
                                            <span class="spinner-border spinner-border-sm me-1 spinner hide"
                                                role="status" aria-hidden="true"></span>
                                            {{ __('Save') }}
                                        </button>

                                        <button type="button" id="checkValidationOfApple" class="btn btn-success">
                                            {{ __('Check Validation') }}
                                        </button>

                                        <hr>

                                    </form>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
                {{-- Privacy Policy --}}
                <div class="tab-pane fade" id="v-pills-privacy-policy" role="tabpanel"
                    aria-labelledby="v-pills-privacy-policy-tab">
                    <div class="card">
                        <div class="card-header border-bottom d-flex align-items-center">
                            <h4 class="m-0 header-title">{{ __('Privacy Policy') }}</h4>
                            <a href="{{ url('privacy_policy') }}" target="_blank"
                                class="btn btn-primary rounded-5 ms-2">{{ __('View') }}</a>
                        </div>
                        <div class="card-body">
                            <form id="privacyPolicyForm" method="POST">
                                <div id="privacyEditor">{!! $setting->privacy_policy !!}</div>
                                <br>
                                <button type="submit" class="btn btn-primary">{{ __('Save') }}</button>
                            </form>
                        </div>
                    </div>
                </div>
                {{-- Terms Of Use --}}
                <div class="tab-pane fade" id="v-pills-terms" role="tabpanel" aria-labelledby="v-pills-terms-tab">
                    <div class="card">
                        <div class="card-header border-bottom d-flex align-items-center">
                            <h4 class="m-0 header-title">{{ __('Terms Of Uses') }}</h4>
                            <a href="{{ url('terms_of_uses') }}" target="_blank"
                                class="btn btn-primary rounded-5 ms-2">{{ __('View') }}</a>
                        </div>
                        <div class="card-body">
                            <form id="termsOfUsesForm" method="POST">
                                <div id="termsOfUsesEditor">{!! $setting->terms_of_uses !!}</div>
                                <br>
                                <button type="submit" class="btn btn-primary">{{ __('Save') }}</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Add DeepAR Filter Modal --}}
    <div id="addDeepARFilterModal" class="modal fade" tabindex="-1" role="dialog"
        aria-labelledby="standard-modalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="standard-modalLabel">{{ __('Add DeepAR Filter') }}</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-hidden="true"></button>
                </div>
                <form id="addDeepARFilterForm" method="POST">
                    <div class="modal-body">
                        <img id="imgDeepARFilterPreview" src="{{ url('assets/img/placeholder.png') }}"
                            alt="" class="rounded" height="100" width="100">
                        <div class="my-2">
                            <label for="image" class="form-label">{{ __('Image') }}</label>
                            <input id="inputaddDeepARFilterImage" class="form-control" type="file"
                                accept="image/*" id="image" name="image" required>
                        </div>
                        <div class="my-2">
                            <label for="title" class="form-label">{{ __('Title') }}</label>
                            <input class="form-control" type="text" id="title" name="title" required>
                        </div>
                        <div class="my-2">
                            <label for="filter_file" class="form-label">{{ __('Filter File') }}</label>
                            <input class="form-control" type="file" id="filter_file" name="filter_file" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light"
                            data-bs-dismiss="modal">{{ __('Close') }}</button>
                        <button type="submit" class="btn btn-primary">
                            <span class="spinner-border spinner-border-sm me-1 spinner hide" role="status"
                                aria-hidden="true"></span>
                            {{ __('Save') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    {{-- Edit DeepAR Filter Modal --}}
    <div id="editDeepARFilterModal" class="modal fade" tabindex="-1" role="dialog"
        aria-labelledby="standard-modalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="standard-modalLabel">{{ __('Edit DeepAR Filter') }}</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-hidden="true"></button>
                </div>
                <form id="editDeepARFilterForm" method="POST">
                    <input type="hidden" name="id" id="editDeepARFilterId">
                    <div class="modal-body">
                        <img id="imgEditDeepARFilterPreview" src="{{ url('assets/img/placeholder.png') }}"
                            alt="" class="rounded" height="100" width="100">
                        <div class="my-2">
                            <label for="image" class="form-label">{{ __('Image') }}</label>
                            <input id="inputeditDeepARFilterImage" class="form-control" type="file"
                                accept="image/*" id="image" name="image">
                        </div>
                        <div class="my-2">
                            <label for="editDeepARFilterTitle" class="form-label">{{ __('Title') }}</label>
                            <input class="form-control" type="text" id="editDeepARFilterTitle" name="title"
                                required>
                        </div>
                        <div class="my-2">
                            <label for="edit_filter_file" class="form-label">{{ __('Filter File') }}</label>
                            <input class="form-control" type="file" id="edit_filter_file" name="filter_file">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light"
                            data-bs-dismiss="modal">{{ __('Close') }}</button>
                        <button type="submit" class="btn btn-primary">
                            <span class="spinner-border spinner-border-sm me-1 spinner hide" role="status"
                                aria-hidden="true"></span>
                            {{ __('Save') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Edit User Level --}}
    <div id="editUserLevelModal" class="modal fade" tabindex="-1" role="dialog"
        aria-labelledby="standard-modalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="standard-modalLabel">{{ __('Edit User Level') }}</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-hidden="true"></button>
                </div>
                <form id="editUserLevelForm" method="POST">
                    <input type="hidden" name="id" id="editUserLevelId">
                    <div class="modal-body">
                        <div class="mb-2">
                            <label for="edit_level" class="form-label">{{ __('Level') }}</label>
                            <input class="form-control" type="text" id="edit_level" name="level" required
                                disabled>
                        </div>
                        <div class="mb-2">
                            <label for="edit_coins_collection" class="form-label">{{ __('Coins Collection') }}</label>
                            <input class="form-control" type="text" id="edit_coins_collection"
                                name="coins_collection" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light"
                            data-bs-dismiss="modal">{{ __('Close') }}</button>
                        <button type="submit" class="btn btn-primary">
                            <span class="spinner-border spinner-border-sm me-1 spinner hide" role="status"
                                aria-hidden="true"></span>
                            {{ __('Submit') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    {{-- Edit Withdrawal Gateway --}}
    <div id="editWithdrawalGatewayModal" class="modal fade" tabindex="-1" role="dialog"
        aria-labelledby="standard-modalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="standard-modalLabel">{{ __('Edit Withdrawal Gateway') }}</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-hidden="true"></button>
                </div>
                <form id="editWithdrawalGatewayForm" method="POST">
                    <input type="hidden" name="id" id="editWithdrawalGatewayId">
                    <div class="modal-body">
                        <div class="mb-2">
                            <label for="title" class="form-label">{{ __('Title') }}</label>
                            <input id="editWithdrawalGatewayTitle" class="form-control" type="text"
                                id="title" name="title" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light"
                            data-bs-dismiss="modal">{{ __('Close') }}</button>
                        <button type="submit" class="btn btn-primary">
                            <span class="spinner-border spinner-border-sm me-1 spinner hide" role="status"
                                aria-hidden="true"></span>
                            {{ __('Submit') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    {{-- Edit Withdrawal Gateway --}}
    <div id="editWithdrawalGatewayModal" class="modal fade" tabindex="-1" role="dialog"
        aria-labelledby="standard-modalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="standard-modalLabel">{{ __('Edit Withdrawal Gateway') }}</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-hidden="true"></button>
                </div>
                <form id="editWithdrawalGatewayForm" method="POST">
                    <input type="hidden" name="id" id="editWithdrawalGatewayId">
                    <div class="modal-body">
                        <div class="mb-2">
                            <label for="title" class="form-label">{{ __('Title') }}</label>
                            <input id="editWithdrawalGatewayTitle" class="form-control" type="text"
                                id="title" name="title" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light"
                            data-bs-dismiss="modal">{{ __('Close') }}</button>
                        <button type="submit" class="btn btn-primary">
                            <span class="spinner-border spinner-border-sm me-1 spinner hide" role="status"
                                aria-hidden="true"></span>
                            {{ __('Submit') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    {{-- Edit Report Reason --}}
    <div id="editReportReasonModal" class="modal fade" tabindex="-1" role="dialog"
        aria-labelledby="standard-modalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="standard-modalLabel">{{ __('Edit Report Reason') }}</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-hidden="true"></button>
                </div>
                <form id="editReportReasonForm" method="POST">
                    <input type="hidden" name="id" id="editReportReasonId">
                    <div class="modal-body">
                        <div class="mb-2">
                            <label for="title" class="form-label">{{ __('Title') }}</label>
                            <input id="editReportReasonTitle" class="form-control" type="text" id="title"
                                name="title" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light"
                            data-bs-dismiss="modal">{{ __('Close') }}</button>
                        <button type="submit" class="btn btn-primary">
                            <span class="spinner-border spinner-border-sm me-1 spinner hide" role="status"
                                aria-hidden="true"></span>
                            {{ __('Submit') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    {{-- Add User Level --}}
    <div id="addUserLevelModal" class="modal fade" tabindex="-1" role="dialog"
        aria-labelledby="standard-modalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="standard-modalLabel">{{ __('Add User Level') }}</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-hidden="true"></button>
                </div>
                <form id="addUserLevelForm" method="POST">
                    <div class="modal-body">
                        <div class="mb-2">
                            <label for="level" class="form-label">{{ __('Level') }}</label>
                            <input class="form-control" type="text" id="level" name="level" required>
                        </div>
                        <div class="mb-2">
                            <label for="coins_collection" class="form-label">{{ __('Coins Collection') }}</label>
                            <input class="form-control" type="text" id="coins_collection" name="coins_collection"
                                required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light"
                            data-bs-dismiss="modal">{{ __('Close') }}</button>
                        <button type="submit" class="btn btn-primary">
                            <span class="spinner-border spinner-border-sm me-1 spinner hide" role="status"
                                aria-hidden="true"></span>
                            {{ __('Submit') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    {{-- Add Withdrawal Gateways --}}
    <div id="addWithdrawalGatewayModal" class="modal fade" tabindex="-1" role="dialog"
        aria-labelledby="standard-modalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="standard-modalLabel">{{ __('Add Withdrawal Gateway') }}</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-hidden="true"></button>
                </div>
                <form id="addWithdrawalGatewayForm" method="POST">
                    <div class="modal-body">
                        <div class="mb-2">
                            <label for="title" class="form-label">{{ __('Title') }}</label>
                            <input class="form-control" type="text" id="title" name="title" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light"
                            data-bs-dismiss="modal">{{ __('Close') }}</button>
                        <button type="submit" class="btn btn-primary">
                            <span class="spinner-border spinner-border-sm me-1 spinner hide" role="status"
                                aria-hidden="true"></span>
                            {{ __('Submit') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    {{-- Add Report Reason --}}
    <div id="addReportReasonModal" class="modal fade" tabindex="-1" role="dialog"
        aria-labelledby="standard-modalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="standard-modalLabel">{{ __('Add Report Reason') }}</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-hidden="true"></button>
                </div>
                <form id="addReportReasonForm" method="POST">
                    <div class="modal-body">
                        <div class="mb-2">
                            <label for="title" class="form-label">{{ __('Title') }}</label>
                            <input class="form-control" type="text" id="title" name="title" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light"
                            data-bs-dismiss="modal">{{ __('Close') }}</button>
                        <button type="submit" class="btn btn-primary">
                            <span class="spinner-border spinner-border-sm me-1 spinner hide" role="status"
                                aria-hidden="true"></span>
                            {{ __('Submit') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    {{-- Edit Onboarding Screen --}}
    <div id="editOnBoardingScreenModal" class="modal fade" tabindex="-1" role="dialog"
        aria-labelledby="standard-modalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="standard-modalLabel">{{ __('Add Onboarding Screen') }}</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-hidden="true"></button>
                </div>
                <form id="editOnBoardingScreenForm" method="POST">
                    <input type="hidden" name="id" id="editOnboardingScreenId">
                    <div class="modal-body">
                        <img id="imgEditOnBoradingPreview" src="{{ url('assets/img/placeholder.png') }}"
                            alt="" class="rounded" width="200">
                        <div class="my-2">
                            <label for="image" class="form-label">{{ __('Image') }}</label>
                            <input id="inputEditOnboardingImage" class="form-control" type="file"
                                accept="image/*" id="image" name="image">
                        </div>
                        <div class="mb-2">
                            <label for="title" class="form-label">{{ __('Title') }}</label>
                            <input id="editOnboardingTitle" class="form-control" type="text" id="title"
                                name="title" required>
                        </div>
                        <div class="mb-2">
                            <label for="description" class="form-label">{{ __('Description') }}</label>
                            <textarea id="editOnboardingDesc" class="form-control" id="description" name="description" required></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light"
                            data-bs-dismiss="modal">{{ __('Close') }}</button>
                        <button type="submit" class="btn btn-primary">
                            <span class="spinner-border spinner-border-sm me-1 spinner hide" role="status"
                                aria-hidden="true"></span>
                            {{ __('Submit') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    {{-- Add Onboarding Screen --}}
    <div id="addOnBoardingScreenModal" class="modal fade" tabindex="-1" role="dialog"
        aria-labelledby="standard-modalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="standard-modalLabel">{{ __('Add Onboarding Screen') }}</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-hidden="true"></button>
                </div>
                <form id="addOnBoardingScreenForm" method="POST">
                    <div class="modal-body">
                        <img id="imgAddOnBoradingPreview" src="{{ url('assets/img/placeholder.png') }}"
                            alt="" class="rounded" width="200">
                        <div class="my-2">
                            <label for="image" class="form-label">{{ __('Image') }}</label>
                            <input id="inputAddOnboardingImage" class="form-control" type="file" accept="image/*"
                                id="image" name="image" required>
                        </div>
                        <div class="mb-2">
                            <label for="title" class="form-label">{{ __('Title') }}</label>
                            <input class="form-control" type="text" id="title" name="title" required>
                        </div>
                        <div class="mb-2">
                            <label for="description" class="form-label">{{ __('Description') }}</label>
                            <textarea class="form-control" id="description" name="description" required></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light"
                            data-bs-dismiss="modal">{{ __('Close') }}</button>
                        <button type="submit" class="btn btn-primary">
                            <span class="spinner-border spinner-border-sm me-1 spinner hide" role="status"
                                aria-hidden="true"></span>
                            {{ __('Submit') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
