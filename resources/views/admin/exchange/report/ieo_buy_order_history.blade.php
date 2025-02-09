@extends('admin.master', ['menu' => 'trade', 'sub_menu' => $sub_menu])

@section('title', $title ?? '')

@section('style')
<!-- Add custom styles if necessary -->
@endsection

@section('content')
<!-- Breadcrumb Section -->
<div class="custom-breadcrumb">
    <div class="row">
        <div class="col-12">
            <ul>
                <li>{{ __('Order') }}</li>
                <li class="active-item">{{ $title }}</li>
            </ul>
        </div>
    </div>
</div>
<!-- /Breadcrumb Section -->

<!-- User Management Section -->
<div class="user-management pt-4">
    <div class="row">
        <div class="col-12">
            <div class="table-area">
                <!-- Export Form -->
                <div class="export-form mb-4">
                    <form id="withdrawal_form" class="row" action="{{ route('adminAllIEOBuyOrderHistoryExport') }}" method="get">
                        @csrf
                        <div class="col-3 form-group">
                            <label for="from_date">{{ __('From Date') }}</label>
                            <input type="hidden" name="type" value="withdrawal" />
                            <input type="date" name="from_date" id="from_date" class="form-control" />
                        </div>
                        <div class="col-3 form-group">
                            <label for="to_date">{{ __('To Date') }}</label>
                            <input type="date" name="to_date" id="to_date" class="form-control" />
                        </div>
                        <div class="col-3 form-group">
                            <label for="export_to">{{ __('Export') }}</label>
                            <select name="export_to" id="export_to" class="selectpicker" data-style="form-control" data-width="100%" title="{{ __('Select a file type') }}">
                                <option value=".csv">CSV</option>
                                <option value=".xlsx">XLSX</option>
                            </select>
                        </div>
                        <div class="col-3 form-group">
                            <label for="export_button">&nbsp;</label>
                            <input id="export_button" class="form-control btn btn-primary" type="submit" value="{{ __('Export') }}" />
                        </div>
                    </form>
                </div>
                <!-- /Export Form -->

                <br>
                <div class="row">
                    <div class="form-group col-md-4">
                        <label for="#">{{ __('Filter By User') }}</label>
                        <select name="export_to" class="selectpicker" data-style="form-control" data-width="100%" onchange="filterByUser(this.value)">
                            <option>__ Select User __</option>
                            @foreach (getAllUser() as $user)
                                <option value="{{ $user->id }}">{{$user->email . " - " . $user->first_name . " " . $user->last_name}}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group col-md-4">
                        <label for="#">{{ __('Filter By Type History') }}</label>
                        <select name="type_history" class="selectpicker" data-style="form-control" data-width="100%" onchange="filterByTypeHistory(this.value)">
                            <option value="ieo">IEO Transaction History</option>
                            <option value="trade">Transaction History</option>
                            <option value="fiat">Fiat To Crypto Deposit History</option>
                        </select>
                    </div>
                </div>

                <!-- Data Table -->
                    <table id="table" class="table table-borderless custom-table display text-lg-center" width="100%">
                        <thead id="table-header"></thead>
                        <tbody></tbody>
                    </table>
                <!-- /Data Table -->
            </div>
        </div>
    </div>
</div>
<!-- /User Management Section -->
@endsection

@section('script')
<script>
    var TransactionReportTable = null;
    let selectedUserId = null;
    let selectedTypeHistory = 'ieo';

    function filterByUser(userId) {
        selectedUserId = userId;
        reloadTable();
    }

    function filterByTypeHistory(type_history) {
        selectedTypeHistory = type_history;
        reloadTable();
    }

    function getColumns() {
        if (selectedTypeHistory == 'ieo') {
            return [
                { data: 'ieo_name', name: 'ieo.name' },
                { data: 'quantity', name: 'user_registered_ieo.quantity' },
                { data: 'ieo_value', name: 'ieo.value' },
                { data: 'totalAmount', name: 'totalAmount', "orderable": true },
                { data: 'created_at', name: 'user_registered_ieo.created_at', render: function(data) { return data ? new Date(data).toLocaleString() : ''; } },
            ];
        } else if (selectedTypeHistory == 'trade') {
            return [
                { data: 'transaction_id', name: 'trade.transaction_id' },
                { data: 'base_coin', name: 'trade.base_coin' },
                { data: 'trade_coin', name: 'trade.trade_coin' },
                { data: 'amount', name: 'trade.amount' },
                { data: 'price', name: 'trade.price' },
                { data: 'total', name: 'trade.total' },
                { data: 'fees', name: 'trade.fees' },
                { data: 'time', name: 'trade.time', render: function(data) { return data ? new Date(data).toLocaleString() : ''; } },
            ];
        } else if (selectedTypeHistory == 'fiat') {
            return [
                { data: 'currency_amount', name: 'currency_amount' },
                { data: 'coin_amount', name: 'coin_amount' },
                { data: 'id', name: 'id' },
                { data: 'rate', name: 'rate' },
                { data: 'status', name: 'status', orderable: false, searchable: false },
                { data: 'created_at', name: 'created_at' },
            ];
        }
    }

    function getHeaderContent() {
        if (selectedTypeHistory == 'ieo') {
            return `
            <tr>
                <th>{{ __('IEO Name') }}</th>
                <th>{{ __('Quantity') }}</th>
                <th>{{ __('Value') }}</th>
                <th>{{ __('Total Amount') }}</th>
                <th>{{ __('Created At') }}</th>
            </tr>`;
        } else if (selectedTypeHistory == 'trade') {
            return `
            <tr>
                <th>{{ __('Transaction Id') }}</th>
                <th>{{ __('Base Coin') }}</th>
                <th>{{ __('Trade Coin') }}</th>
                <th>{{ __('Amount') }}</th>
                <th>{{ __('Price') }}</th>
                <th>{{ __('Total') }}</th>
                <th>{{ __('Fees') }}</th>
                <th>{{ __('Created At') }}</th>
            </tr>`;
        } else if (selectedTypeHistory == 'fiat') {
            return `
            <tr>
                <th>{{ __('Currency Amount') }}</th>
                <th>{{ __('Coin Amount') }}</th>
                <th>{{ __('Transaction id') }}</th>
                <th>{{ __('Rate') }}</th>
                <th>{{ __('Status') }}</th>
                <th>{{ __('Date') }}</th>
            </tr>`;
        }
    }

    function reloadTable() {
        if (TransactionReportTable) {
            TransactionReportTable.destroy();
        }

        $('#table').empty();
        $('#table').html('<thead><tr>' + getHeaderContent() + '</tr></thead><tbody></tbody>');

        TransactionReportTable = $('#table').DataTable({
            processing: true,
            serverSide: true,
            pageLength: 25,
            responsive: true,
            ajax: {
                url: '{{ route('adminAllIEOBuyOrderHistory') }}',
                data: function(d) {
                    d.user_id = selectedUserId;
                    d.type_history = selectedTypeHistory;
                }
            },
            order: [[0, 'desc']],
            columns: getColumns(),
            language: {
                paginate: {
                    next: '<i class="fa fa-angle-double-right" aria-hidden="true"></i>',
                    previous: '<i class="fa fa-angle-double-left" aria-hidden="true"></i>'
                }
            }
        });
    }

    // Initialize everything when document is ready
    $(document).ready(function() {
        // Initialize selectpicker
        $('.selectpicker').selectpicker();

        // Initialize the table
        reloadTable();

        // Add event listeners for selectpicker changes
        $('.selectpicker').on('changed.bs.select', function() {
            $(this).selectpicker('refresh');
        });
    });
</script>


@endsection
