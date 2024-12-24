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
                        {{ Form::open(['route' => 'adminSaveIeo', 'files' => true]) }}
                        <div class="row">

                            <div class="col-12 col-sm-6 col-md-4">
                                <div class="form-group">
                                    <div class="controls">
                                        <div class="form-label">{{ __('IEO Name') }} <span class="required-file">*</span></div>
                                        <input type="text" class="form-control" name="name"
                                            value="{{ old('name') }}">
                                        <pre class="text-danger">{{ $errors->first('name') }}</pre>
                                    </div>
                                </div>
                            </div>

                            <div class="col-12 col-sm-6 col-md-4">
                                <div class="form-group">
                                    <div class="controls">
                                        <div class="form-label">{{ __('IEO Value') }} <span class="required-file">*</span></div>
                                        <input type="text" class="form-control" name="value"
                                            value="{{ old('value') }}">
                                        <pre class="text-danger">{{ $errors->first('value') }}</pre>
                                    </div>
                                </div>
                            </div>

                            <div class="col-12 col-sm-6 col-md-4">
                                <div class="form-group">
                                    <div class="controls">
                                        <div class="form-label">{{ __('IEO Symbol') }} <span class="required-file">*</span></div>
                                        <input type="text" class="form-control" name="symbol"
                                            value="{{ old('symbol') }}">
                                        <pre class="text-danger">{{ $errors->first('symbol') }}</pre>
                                    </div>
                                </div>
                            </div>

                            <div class="col-12 col-sm-6 col-md-4">
                                <div class="form-group">
                                    <div class="controls">
                                        <div class="form-label">{{ __('IEO Total Supply') }} <span class="required-file">*</span></div>
                                        <input type="text" class="form-control" name="total_supply"
                                            value="{{ old('total_supply') }}">
                                        <pre class="text-danger">{{ $errors->first('total_supply') }}</pre>
                                    </div>
                                </div>
                            </div>

                            <div class="col-12 col-sm-6 col-md-4">
                                <div class="form-group">
                                    <div class="controls">
                                        <div class="form-label">{{ __('IEO Max Rate') }}</div>
                                        <input type="text" class="form-control" name="max_rate"
                                            value="{{ old('max_rate') }}" placeholder="5">
                                        <pre class="text-danger">{{ $errors->first('max_rate') }}</pre>
                                    </div>
                                </div>
                            </div>

                            <div class="col-12 col-sm-6 col-md-4">
                                <div class="form-group">
                                    <div class="controls">
                                        <div class="form-label">{{ __('IEO Start End') }} <span class="required-file">*</span></div>
                                        <input type="date" class="form-control" id="start_date" name="start_date"
                                            value="{{ old('start_date') }}">
                                        <pre class="text-danger">{{ $errors->first('start_date') }}</pre>
                                    </div>
                                </div>
                            </div>

                            <div class="col-12 col-sm-6 col-md-4">
                                <div class="form-group">
                                    <div class="controls">
                                        <div class="form-label">{{ __('IEO End Date') }} <span class="required-file">*</span></div>
                                        <input type="date" class="form-control" id="end_date" name="end_date"
                                            value="{{ old('end_date') }}">
                                        <pre class="text-danger">{{ $errors->first('end_date') }}</pre>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-2">
                            <button type="submit" class="btn theme-btn">{{ $button_title }}</button>
                        </div>

                        {{ Form::close() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- /User Management -->
@endsection
@section('script')
    <script>
        "use strict";
        let currency = 1;

        function add_coin_ui_change() { // name="coin_type"
            let coin_type_input = document.getElementById("coin_type_input");
            let coin_type_select = document.getElementById("coin_type_select");


            if (currency == 1) {
                $("#coin_type_input_").attr("name", "coin_type");
                $("#coin_type_select_").attr("name", "");

                coin_type_select.classList.add("d-none");
                coin_type_input.classList.remove("d-none");
                $("#coin_api").show()
                $("#coin_rate_api").show()
            }

            if (currency == 2) {
                $("#coin_type_input_").attr("name", "")
                $("#coin_type_select_").attr("name", "coin_type")

                coin_type_select.classList.remove("d-none");
                coin_type_input.classList.add("d-none");

                $("#coin_api").hide();
                $("#coin_rate_api").hide();
            }
        }

        function currency_change(event) {
            currency = event.target.value;
            add_coin_ui_change();
        }
        add_coin_ui_change();
        $("#currency_type").on("change", currency_change);
        $("#coin_type_select_").on("change", (e) => {
            let rate = $("#coin_type_select_").find(':selected').data("price")
            $('input[name="coin_price"]').val((1 / rate).toFixed(8));
        });

        $( function() {
            $( "#datepicker" ).datepicker({
                dateFormat: "dd-mm-yy"
                ,	duration: "fast"
            });
        } );
    </script>
@endsection
