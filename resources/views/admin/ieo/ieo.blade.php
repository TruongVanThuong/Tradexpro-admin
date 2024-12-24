@extends('admin.master', ['menu' => 'ieo', 'sub_menu' => 'ieo_list'])
@section('title', isset($title) ? $title : '')
@section('style')
@endsection
@section('content')
    <!-- breadcrumb -->
    <div class="custom-breadcrumb">
        <div class="row">
            <div class="col-12">
                <ul>
                    <li>{{ __('IEO') }}</li>
                    <li class="active-item">{{ $title }}</li>
                </ul>
            </div>
        </div>
    </div>
    <!-- /breadcrumb -->

    <!-- User Management -->
    <div class="user-management pt-4">
        <div class="row">
            <div class="col-12">
                <div class="header-bar">
                    <div class="table-title">
                        <h3>{{ $title }}</h3>
                    </div>
                    <div class="right d-flex align-items-center">
                        <div class="add-btn-new mb-2 ml-2">
                            <a href="{{route('adminAddIeo')}}">{{__('Add New IEO')}}</a>
                        </div>
                    </div>
                </div>
                <div class="table-area">
                    <div class="">
                        <table id="table" class="table table-borderless custom-table display text-lg-center" width="100%">
                            <thead>
                                <tr>
                                    <th scope="col">{{ __('Ieo Icon') }}</th>
                                    <th scope="col">{{ __('Ieo Name') }}</th>
                                    <th scope="col">{{ __('Ieo Value (USDT)') }}</th>
                                    <th scope="col" class="all">{{ __('Ieo Symbol') }}</th>
                                    <th scope="col">{{ __('Total Supply') }}</th>
                                    <th scope="col">{{ __('Max Win Rate (%)') }}</th>
                                    <th scope="col" class="all">{{ __('Start Date') }}</th>
                                    <th scope="col">{{ __('End Date') }}</th>
                                    <th scope="col" class="all text-left">{{ __('Actions') }}</th>
                                </tr>
                            </thead>
                            <tbody>

                            </tbody>
                        </table>
                    </div>
                </div>


            </div>
        </div>
    </div>
    <!-- /User Management -->
@endsection

@section('script')
    <script>
        (function($) {
            "use strict";
            $('#table').DataTable({
                processing: true,
                serverSide: true,
                ajax: '{{ route('adminIeoList') }}', // Đảm bảo route đúng
                columns: [
                    { "data": "ieo_icon", "orderable": false, "searchable": false },
                    { "data": "name", "orderable": true },
                    { "data": "value", "orderable": true },
                    { "data": "symbol", "orderable": true },
                    { "data": "total_supply", "orderable": false },
                    { "data": "max_rate", "orderable": true },
                    { "data": "start_date", "orderable": true },
                    { "data": "end_date", "orderable": true },
                    { "data": "actions", "orderable": false }
                ],
                language: {
                    paginate: {
                        next: '<i class="fa fa-angle-double-right" aria-hidden="true"></i>',
                        previous: '<i class="fa fa-angle-double-left" aria-hidden="true"></i>'
                    }
                },
                order: [[5, 'desc']],
            });
        })(jQuery);
    </script>
@endsection
