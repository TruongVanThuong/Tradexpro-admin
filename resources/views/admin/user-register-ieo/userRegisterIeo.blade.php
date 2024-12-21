@extends('admin.master', ['menu' => 'User Register Ieo', 'sub_menu' => 'user_register_ieo_list'])
@section('title', isset($title) ? $title : '')
@section('style')
@endsection
@section('content')
    <!-- breadcrumb -->
    <div class="custom-breadcrumb">
        <div class="row">
            <div class="col-12">
                <ul>
                    <li>{{ __('User Registered Ieo') }}</li>
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
                </div>
                <div class="table-area">
                    <div class="">
                        <table id="table" class="table table-borderless custom-table display text-lg-center" width="100%">
                            <thead>
                                <tr>
                                    <th scope="col">{{ __('User Name') }}</th>
                                    <th scope="col">{{ __('IEO Name') }}</th>
                                    <th scope="col">{{ __('Rating win') }}</th>
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
        $(document).ready(function() {
            $('#table').DataTable({
                processing: true,
                serverSide: true,
                ajax: '{{ route('adminUserRegisteredIeoList') }}',
                columns: [
                    { "data": "user_name", "name": "users.last_name", "orderable": true },
                    { "data": "ieo_name", "name": "ieo.name", "orderable": true },
                    { "data": "rating_win", "name": "user_registered_ieo.rating_win", "orderable": true },
                    { "data": "actions", "orderable": false }
                ],
                language: {
                    processing: "Loading...",
                    paginate: {
                        next: '<i class="fa fa-angle-double-right" aria-hidden="true"></i>',
                        previous: '<i class="fa fa-angle-double-left" aria-hidden="true"></i>'
                    }
                },
                order: [[0, 'asc']],
            });
        });
    </script>
@endsection
