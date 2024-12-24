@extends('admin.master',['menu'=>'coin', 'sub_menu'=>'coin_pair'])
@section('title', isset($title) ? $title : '')
@section('style')
@endsection
@section('content')
    <!-- breadcrumb -->
    <div class="custom-breadcrumb">
        <div class="row">
            <div class="col-md-9">
                <ul>
                    <li class="active-item">{{ $title }}</li>
                </ul>
            </div>
        </div>
    </div>
    <!-- /breadcrumb -->
    <div class="user-management">
        <div class="row">
            <div class="col-12">
                <div class="profile-info-form">
                    <div>
                        <form action="{{route('coinPairFutureSettingUpdate')}}" method="POST">
                            @csrf
                            <input type="hidden" name="id" value="{{encrypt($coin_pair_details->id)}}">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <div class="controls">
                                            <div class="form-label">{{__('Current Market Price')}} : {{ $coin_pair_details->price }}</div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <div class="controls">
                                            <div class="form-label">{{__('Trade Coin')}}</div>
                                            <input type="text" class="form-control"
                                                value="{{isset($coin_pair_details->child_coin) ? check_default_coin_type($coin_pair_details->child_coin->coin_type) : ''}}"
                                                readonly>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <div class="controls">
                                            <div class="form-label">{{__('Base Coin')}}</div>
                                            <input type="text" class="form-control"
                                                value="{{isset($coin_pair_details->parent_coin) ? check_default_coin_type($coin_pair_details->parent_coin->coin_type) : ''}}"
                                                readonly>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12 mb-3 mt-3">
                                    <h4 class="text-white">{{__('Custom Trade Bot Setting')}}</h4>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <div class="controls">
                                            <div class="form-label">{{__('Bot Operation')}}</div>
                                            <select class="form-control" name="bot_operation" id="">
                                                @foreach (bot_operation() as $key => $val)
                                                    <option @if (isset($coin_pair_details) && $coin_pair_details->bot_operation == $key) selected @endif
                                                        value="{{ $key }}">{{ $val }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <div class="controls">
                                            <div class="form-label">{{__('Bot Price Range Percentage')}} ( %)</div>
                                            <input type="text" class="form-control" name="bot_percentage"
                                                value="{{$coin_pair_details->bot_percentage}}">
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <div class="controls">
                                            <div class="form-label">{{__('Upper Threshold')}}</div>
                                            <input type="text" class="form-control" name="upper_threshold"
                                                value="{{$coin_pair_details->upper_threshold}}">
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <div class="controls">
                                            <div class="form-label">{{__('Lower Threshold')}}</div>
                                            <input type="text" class="form-control" name="lower_threshold"
                                                value="{{$coin_pair_details->lower_threshold}}">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div id="add-rows-container">
                                @if(isset($coin_pair_operations) && count($coin_pair_operations) > 0)
                                    @foreach($coin_pair_operations as $setting)
                                        <input type="hidden" name="operation_id[]" value="{{ $setting->id }}">
                                        <div class="row mb-2 pb-2">
                                            <div class="col-md-12 border-top"></div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <div class="controls">
                                                        <div class="form-label">{{__('Bot Operation')}}</div>
                                                        <select class="form-control" name="bot_operation_cpo[]">
                                                            @foreach (bot_operation() as $key => $val)
                                                                <option value="{{ $key }}" @if($setting->bot_operation == $key) selected @endif>
                                                                    {{ $val }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <div class="controls">
                                                        <div class="form-label">{{__('Running Time Start')}}</div>
                                                        <input type="text" class="form-control" name="running_time_start[]"
                                                            value="{{ $setting->running_time_start }}" required>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <div class="controls">
                                                        <div class="form-label">{{__('Running Time Close')}}</div>
                                                        <input type="text" class="form-control" name="running_time_close[]"
                                                            value="{{ $setting->running_time_close }}" required>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <div class="controls">
                                                        <div class="form-label">{{__('Upper Threshold')}}</div>
                                                        <input type="number" class="form-control" name="upper_threshold_cpo[]"
                                                            value="{{ $setting->upper_threshold }}" step="0.01" min="0" required>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <div class="controls">
                                                        <div class="form-label">{{__('Lower Threshold')}}</div>
                                                        <input type="number" class="form-control" name="lower_threshold_cpo[]"
                                                            value="{{ $setting->lower_threshold }}" step="0.01" min="0" required>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-4" style="margin-top: 31px;">
                                                <button type="button" class="btn btn-outline-danger delete-row-btn" style="height: 38px;"><i class="fa fa-trash"></i></button>
                                            </div>
                                        </div>
                                    @endforeach
                                @endif
                            </div>

                            <div class="row">
                                <div class="col-md-2">
                                    <button type="button" id="add-row-btn" class="btn btn-outline-primary"><i class="fa fa-plus"></i></button>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-12 mb-3 mt-3">
                                    <h4 class="text-white">{{__('Future Trade Setting')}}</h4>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <div class="controls">
                                            <div class="form-label">{{__('Minimum Amount For Future Trade')}}</div>
                                            <input type="text" class="form-control" name="minimum_amount_future"
                                                value="{{$coin_pair_details->minimum_amount_future}}">
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <div class="controls">
                                            <div class="form-label">{{__('Maintenance Margin Rate')}} ({{__('Percentage')}} %)</div>
                                            <input type="text" class="form-control" name="maintenance_margin_rate"
                                                value="{{$coin_pair_details->maintenance_margin_rate}}">
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <div class="controls">
                                            <div class="form-label">{{__('Funding Fee')}} ({{__('Percentage')}} %)</div>
                                            <input type="text" class="form-control" name="leverage_fee"
                                                value="{{$coin_pair_details->leverage_fee}}">
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <div class="controls">
                                            <div class="form-label">{{__('Maximum Leverage')}}</div>
                                            <input type="text" class="form-control" name="max_leverage"
                                                value="{{$coin_pair_details->max_leverage ? $coin_pair_details->max_leverage : 10}}">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-2">
                                    <button type="submit" class="btn theme-btn">{{ __('Submit')}}</button>
                                </div>
                            </div>
                        </form>



                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')

<script>
    $(document).ready(function () {
        $('#add-row-btn').on('click', function () {
            let newRow = `
                <div class="row mb-2 pb-2">
                    // <input type="hidden" name="operation_id[]" value="">
                    <div class="col-md-12 border-top"></div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <div class="controls">
                                <div class="form-label">{{__('Bot Operation')}}</div>
                                <select class="form-control" name="bot_operation_cpo[]" id="">
                                    @foreach (bot_operation() as $key => $val)
                                        <option value="{{ $key }}">{{ $val }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="form-group">
                            <div class="controls">
                                <div class="form-label">{{__('Running Time Start')}}</div>
                                <input type="date" class="form-control" name="running_time_start[]" requied>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="form-group">
                            <div class="controls">
                                <div class="form-label">{{__('Running Time Close')}}</div>
                                <input type="date" class="form-control" name="running_time_close[]" required >
                            </div>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="form-group">
                            <div class="controls">
                                <div class="form-label">{{__('Upper Threshold')}}</div>
                                <input type="number" class="form-control" name="upper_threshold_cpo[]" value="" step="0.01" min="0" required>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="form-group">
                            <div class="controls">
                                <div class="form-label">{{__('Lower Threshold')}}</div>
                                <input type="number" class="form-control" name="lower_threshold_cpo[]" value="" step="0.01" min="0" required>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4" style="margin-top: 31px;">
                        <button type="button" class="btn btn-outline-danger delete-row-btn" style="height: 38px;"><i class="fa fa-trash"></i></button>
                    </div>
                </div>
            `;

            $('#add-rows-container').append(newRow);
        });

        $(document).on('click', '.delete-row-btn', function () {
            $(this).closest('.row').remove();
        });

    });
</script>


@endsection
