@extends('layouts.staff')

@section('title', trans('Maintenance'))

@section('links')
    <!-- DataTables CSS -->
    <link href="{{ asset('plugins/datatables/dataTables.bootstrap4.min.css') }}?v={{ config('app.version') }}" rel="stylesheet" type="text/css">
    <link href="{{ asset('plugins/datatables/buttons.bootstrap4.min.css') }}?v={{ config('app.version') }}" rel="stylesheet" type="text/css">
    <link href="{{ asset('plugins/datatables/responsive.bootstrap4.min.css') }}?v={{ config('app.version') }}" rel="stylesheet" type="text/css">
    <link href="{{ asset('css/custom-datatable.css') }}?v={{ config('app.version') }}" rel="stylesheet" type="text/css">

    <style>
        .loading {
            pointer-events: none;
        }
    </style>
@endsection

@section('content')
    <!-- Table Section -->
    <div class="d-flex flex-column mb-3">
        <h2 class="page-title text-primary mb-2">{{ trans('Maintenance Requests') }}</h2>
    </div>

    <div class="collapse" id="collapseExample">
        <div class="search-filter-container card card-body">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-center">
                <div class="search-container d-flex align-items-center mb-3 mb-md-0">
                    <div class="search-icon-container">
                        <i class="fa fa-search search-icon"></i>
                    </div>
                    <input type="search" class="form-control search-input" id="searchBox" placeholder="{{ trans('Search for Requests') }}">
                </div>
                <div class="d-flex flex-column flex-md-row filters-container">
                    <select id="statusFilter" class="form-select mb-2 mb-md-0">
                        <option value="">{{ trans('Filter by Status') }}</option>
                        <option value="pending">{{ trans('Pending') }}</option>
                        <option value="completed">{{ trans('Completed') }}</option>
                        <option value="rejected">{{ trans('Rejected') }}</option>
                    </select>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-12">
            <div class="card m-b-30 table-card">
                <div class="card-body table-container">
                    <div class="table-responsive">
                        <table id="default-datatable" class="display table table-bordered">
                            <thead>
                                <tr>
                                    <th>{{ trans('Resident Name') }}</th>
                                    <th>{{ trans('Resident Location') }}</th>
                                    <th>{{ trans('Resident Phone') }}</th>
                                    <th>{{ trans('Category') }}</th>
                                    <th>{{ trans('Problems') }}</th>
                                    <th>{{ trans('Status') }}</th>
                                    <th>{{ trans('Created At') }}</th>
                                    <th>{{ trans('Has Photos') }}</th>
                                    <th>{{ trans('Photos') }}</th>
                                    <th>{{ trans('Actions') }}</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

   
@endsection

@section('scripts')
    <script src="{{ asset('plugins/datatables/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('plugins/datatables/dataTables.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('plugins/datatables/dataTables.responsive.min.js') }}"></script>
    <script src="{{ asset('plugins/datatables/responsive.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('js/pages/staff-maintenance.js') }}"></script>
    <script>
        window.routes = {
            fetchRequests: "{{ route('staff.maintenance.fetch') }}",
            acceptRequest: "{{ route('staff.maintenance.accept', ':id') }}",
            completeRequest: "{{ route('staff.maintenance.complete', ':id') }}",
        };
    </script>
@endsection