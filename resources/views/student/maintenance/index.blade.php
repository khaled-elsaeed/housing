@extends('layouts.student')

@section('title', __('Maintenance Requests'))

@section('links')
<link href="{{ asset('plugins/datatables/dataTables.bootstrap4.min.css') }}?v={{ config('app.version') }}" rel="stylesheet" type="text/css" />
<link href="{{ asset('plugins/datatables/buttons.bootstrap4.min.css') }}?v={{ config('app.version') }}" rel="stylesheet" type="text/css" />
<link href="{{ asset('plugins/datatables/responsive.bootstrap4.min.css') }}?v={{ config('app.version') }}" rel="stylesheet" type="text/css" />
<link href="{{ asset('css/custom-datatable.css') }}?v={{ config('app.version') }}" rel="stylesheet" type="text/css" />

<style>
    .problem-checkbox {
        border: 1px solid #dee2e6;
        padding: 15px;
        border-radius: 8px;
        margin-bottom: 10px;
        transition: background-color 0.3s ease;
    }
    .problem-checkbox:hover {
        background-color: #f8f9fa;
    }
    .photo-preview-item {
        position: relative;
        margin-right: 10px;
        margin-bottom: 10px;
    }
    .photo-preview-item img {
        height: 100px;
        width: 100px;
        object-fit: cover;
        border-radius: 8px;
    }
    .photo-preview-item button {
        position: absolute;
        top: 0;
        right: 0;
        padding: 0 5px;
    }
    .custom-file-label::after {
        content: "{{ __('Browse') }}";
    }
</style>
@endsection

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <!-- Maintenance Requests Table -->
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h5 class="card-title mb-0">
                        <i class="fa fa-tools me-2"></i> {{ __('Your Maintenance Requests') }}
                    </h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="default-datatable" class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>@lang('ID')</th>
                                    <th>@lang('Category')</th>
                                    <th>@lang('Status')</th>
                                    <th>@lang('Created At')</th>
                                    <th>@lang('Actions')</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Data will be loaded via DataTables -->
                            </tbody>
                        </table>
                    </div>
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
<script>
    $(document).ready(function () {
        // Initialize DataTable
        const table = $('#default-datatable').DataTable({
            processing: true,
            serverSide: true,
            responsive: true,
            ajax: {
                url: "{{ route('student.maintenance.requests') }}", // Route to fetch user's requests
                type: 'GET',
            },
            columns: [
                { data: 'id', name: 'id' },
                { data: 'category', name: 'category' },
                {
                    data: 'status',
                    name: 'status',
                    render: function (data) {
                        let badgeClass = '';
                        switch (data) {
                            case 'pending':
                                badgeClass = 'badge bg-warning text-dark';
                                break;
                            case 'assigned':
                                badgeClass = 'badge bg-primary';
                                break;
                            case 'in_progress':
                                badgeClass = 'badge bg-info';
                                break;
                            case 'completed':
                                badgeClass = 'badge bg-success';
                                break;
                            default:
                                badgeClass = 'badge bg-secondary';
                                break;
                        }
                        return `<span class="${badgeClass}">${data.replace('_', ' ')}</span>`;
                    }
                },
                {
                    data: 'created_at',
                    name: 'created_at',
                    render: function (data) {
                        return new Date(data).toLocaleString();
                    }
                },
                {
                    data: null,
                    render: function (data) {
                        return `
                            <a href="{{ route('student.maintenance.show', '') }}/${data.id}" class="btn btn-sm btn-primary">
                                <i class="fa fa-eye"></i> {{ __('View') }}
                            </a>
                        `;
                    }
                }
            ],
            language: {
                url: "{{ app()->getLocale() === 'ar' ? 'https://cdn.datatables.net/plug-ins/1.13.6/i18n/Arabic.json' : '' }}"
            }
        });
    });
</script>
@endsection