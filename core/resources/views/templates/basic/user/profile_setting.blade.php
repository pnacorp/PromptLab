@extends($activeTemplate . 'layouts.master')
@section('content')
    <form method="post" enctype="multipart/form-data" autocomplete="off">
        @csrf
        <div class="row gy-4">
            <div class="col-12">
                <div class="seller-details setting-prfile">
                    <div class="seller-details__thumb upload">
                        <img src="{{ getImage(getFilePath('userCover') . '/' . $user->coverImage, '', cover: true) }}" alt="image" class="fit-image">
                    </div>
                    <div class="seller-wrapper">
                        <div class="container p-0">
                            <div class="seller-profile position-relative">
                                <div class="avatar-edit seller-cover-photo">
                                    <input class="profilePicUpload" id="profilePicUpload1" name="coverImage" type="file" accept = ".png, .jpg, .jpeg">
                                    <label class="btn mb-0" for="profilePicUpload1"><i class="la la-camera"></i></label>
                                </div>
                                <div class="seller-profile__thumb position-relative">
                                    <img src="{{ getImage(getFilePath('userProfile') . '/' . $user->image, '', avatar: true) }}" alt="image" class="fit-image">
                                    <div class="avatar-edit profile-pic">
                                        <input class="profilePicUpload" id="profilePicUpload2" name="image" type="file" accept = ".png, .jpg, .jpeg">
                                        <label class="profile-uploader-icon mb-0" for="profilePicUpload2"><i class="la la-camera"></i></label>
                                    </div>
                                </div>

                                <div class="public-view-btn">
                                    <a class="btn btn-outline--base btn--sm" href="{{ route('seller.profile', auth()->user()->username) }}">
                                        <i class="las la-eye"></i> @lang('Public View')
                                    </a>
                                </div>
                            </div>
                            <div class="seller-details__content">
                                <div>
                                    <span class="seller-details__name mb-0">{{ $user->fullname }}</span>
                                    <div class="user-data d-flex flex-wrap gap-4">
                                        <span> <i class="la la-user"></i> {{ $user->username }}</span>
                                        <span><i class="la la-envelope"></i> {{ $user->email }}</span>
                                        <span><i class="la la-phone"></i> {{ $user->mobile }}</span>
                                        <span><i class="la la-globe"></i> {{ $user->country_name }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-12">
                <div class="custom--card card">
                    <div class="card-body">
                        <h5 class="mb-3">@lang('Personal Information')</h5>
                        <div class="row">
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label class="form--label">@lang('First name')</label>
                                    <input type="text" class="form--control" name="firstname" value="{{ $user->firstname }}" required>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label class="form--label">@lang('Last name')</label>
                                    <input type="text" class="form--control" name="lastname" value="{{ $user->lastname }}" required>
                                </div>
                            </div>
                            <div class="col-sm-12">
                                <div class="form-group">
                                    <label class="form--label">@lang('Enter Your Bio')</label>
                                    <textarea class="form--control text--overflow" name="description" placeholder="Please type your bio here...">{{ @$user->description }}</textarea>
                                </div>
                            </div>

                        </div>

                    </div>
                </div>
            </div>

            <div class="col-12">
                <div class="custom--card card">
                    <div class="card-body">
                        <h5 class="mb-3">@lang('Contact Information')</h5>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="form--label">@lang('State')</label>
                                    <input type="text" class="form--control" name="state" value="{{ @$user->state }}">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="form--label">@lang('City')</label>
                                    <input type="text" class="form--control" name="city" value="{{ @$user->city }}">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="form--label">@lang('Postcode')</label>
                                    <input type="number" class="form--control" name="zip" value="{{ @$user->zip }}">
                                </div>
                            </div>
                            <div class="col-sm-12">
                                <div class="form-group">
                                    <label class="form--label">@lang('Address')</label>
                                    <input class="form--control" name="address" value="{{ @$user->address }}">
                                </div>
                            </div>

                        </div>

                    </div>
                </div>
            </div>

            <div class="col-sm-12">
                <button type="submit" class="btn btn--base w-100">@lang('Save Changes')</button>
            </div>
        </div>
    </form>
@endsection

@push('script')
    <script>
        'use strict';

        (function($) {
            function previewImage(input, target) {
                if (input.files && input.files[0]) {
                    var reader = new FileReader();
                    reader.onload = function(e) {
                        $(target).attr('src', e.target.result);
                    }
                    reader.readAsDataURL(input.files[0]);
                }
            }

            $("#profilePicUpload1").on('change', function() {
                previewImage(this, '.seller-details__thumb img');
            });

            $("#profilePicUpload2").on('change', function() {
                previewImage(this, '.seller-profile__thumb img');
            });

        })(jQuery)
    </script>
@endpush

@push('style')
    <style>
        .public-view-btn {
            position: absolute;
            right: 10px;
            bottom: 0;
        }

        @media(max-width: 575px) {
            .public-view-btn {
                right: -10px;
            }
        }
    </style>
@endpush
