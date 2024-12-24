@extends('admin.master', ['menu' => 'ieo', 'sub_menu' => 'ieo_list'])
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
                        <form action="{{ route('adminIeoSaveProcess') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <input type="hidden" class="form-control" name="ieo_id" value="{{ $item->id }}" >
                            <div class="row">
                                <div class="col-12 col-sm-6 col-md-4">
                                    <div class="form-group">
                                        <label for="name">{{ __('IEO Name') }} <span class="required-file">*</span></label>
                                        <input type="text" id="name" name="name" value="{{ $item->name }}" class="form-control">
                                        <pre class="text-danger">{{ $errors->first('name') }}</pre>
                                    </div>
                                </div>

                                <div class="col-12 col-sm-6 col-md-4">
                                    <div class="form-group">
                                        <label for="value">{{ __('IEO Value') }} <span class="required-file">*</span></label>
                                        <input type="number" id="value" name="value" value="{{ $item->value }}" class="form-control">
                                        <pre class="text-danger">{{ $errors->first('value') }}</pre>
                                    </div>
                                </div>

                                <div class="col-12 col-sm-6 col-md-4">
                                    <div class="form-group">
                                        <label for="symbol">{{ __('IEO Symbol') }} <span class="required-file">*</span></label>
                                        <input type="text" id="symbol" name="symbol" value="{{ $item->symbol }}" class="form-control">
                                        <pre class="text-danger">{{ $errors->first('symbol') }}</pre>
                                    </div>
                                </div>

                                <div class="col-12 col-sm-6 col-md-4">
                                    <div class="form-group">
                                        <label for="total_supply">{{ __('Total Supply') }} <span class="required-file">*</span></label>
                                        <input type="number" id="total_supply" name="total_supply" value="{{ $item->total_supply }}" class="form-control">
                                        <pre class="text-danger">{{ $errors->first('total_supply') }}</pre>
                                    </div>
                                </div>

                                <div class="col-12 col-sm-6 col-md-4">
                                    <div class="form-group">
                                        <label for="max_rate">{{ __('IEO Max Rate') }}</label>
                                        <input type="text" id="max_rate" name="max_rate" value="{{ $item->max_rate }}" class="form-control">
                                        <pre class="text-danger">{{ $errors->first('max_rate') }}</pre>
                                    </div>
                                </div>

                                <div class="col-12 col-sm-6 col-md-4">
                                    <div class="form-group">
                                        <label for="start_date">{{ __('Start Date') }} <span class="required-file">*</span></label>
                                        <input type="date" id="start_date" name="start_date" value="{{ $item->start_date->format('Y-m-d') }}" class="form-control">
                                        <pre class="text-danger">{{ $errors->first('start_date') }}</pre>
                                    </div>
                                </div>

                                <div class="col-12 col-sm-6 col-md-4">
                                    <div class="form-group">
                                        <label for="end_date">{{ __('End Date') }} <span class="required-file">*</span></label>
                                        <input type="date" id="end_date" name="end_date" value="{{ $item->end_date->format('Y-m-d') }}" class="form-control">
                                        <pre class="text-danger">{{ $errors->first('end_date') }}</pre>
                                    </div>
                                </div>

                            </div>
                            <div class="col-md-2">
                                <button type="submit" class="btn theme-btn">{{ __('Update') }}</button>
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
