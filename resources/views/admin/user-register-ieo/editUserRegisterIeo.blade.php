@extends('admin.master', ['menu' => 'User Register Ieo', 'sub_menu' => 'user_register_ieo_list'])
@section('title', isset($title) ? $title : '')
@section('style')
@endsection
@section('content')
    <!-- breadcrumb -->
    <div class="custom-breadcrumb">
        <div class="row">
            <div class="col-md-9">
                <ul>
                    <li>{{ __('IEO') }}</li>
                    <li class="active-item">{{ $title }}</li>
                </ul>
            </div>
        </div>
    </div>
    <!-- /breadcrumb -->

    <!-- User Management -->
    <div class="user-management">
        <div class="row">
            <div class="col-12">
                <div class="profile-info-form">
                    <div>
                        <form action="{{ route('adminUserRegisteredIeoSaveProcess') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <input type="hidden" class="form-control" name="ieo_id" value="{{ $item->id }}" >
                            <div class="row">

                                <div class="col-12 col-sm-6 col-md-4">
                                    <div class="form-group">
                                        <label for="name">{{ __('User Name') }}</label>
                                        <input type="text" id="user_name" name="user_name" value="{{ $item->user_name }}" class="form-control" disabled>
                                    </div>
                                </div>

                                <div class="col-12 col-sm-6 col-md-4">
                                    <div class="form-group">
                                        <label for="ieo_name">{{ __('IEO Name') }}</label>
                                        <input type="text" id="ieo_name" name="ieo_name" value="{{ $item->ieo_name }}" class="form-control" disabled>
                                    </div>
                                </div>

                                <div class="col-12 col-sm-6 col-md-4">
                                    <div class="form-group">
                                        <label for="rating_win">{{ __('Rating win') }} <span class="required-file">*</span></label>
                                        <input type="text" id="rating_win" name="rating_win" value="{{ $item->rating_win }}" class="form-control">
                                    </div>
                                </div>

                            </div>

                            <div class="row">
                                <div class="col-md-2">
                                    <button type="submit" class="btn theme-btn">{{ $button_title }}</button>
                                </div>
                            </div>

                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- /User Management -->

@endsection

@section('script')
@endsection
