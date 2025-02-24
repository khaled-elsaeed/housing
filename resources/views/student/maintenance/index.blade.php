@extends('layouts.student')

@section('title', __('Maintenance Requests'))

@section('content')
<div class="row px-0">
    <div class="col-12 px-0">
        <div class="card border-0 shadow-sm rounded-3">
        <div class="card-header d-flex justify-content-between align-items-center bg-primary">
            <h5 class="m-0 text-white"><i class="fa fa-tools me-2"></i>{{ __('Your Maintenance Requests') }}</h5>
            <a href="{{ route('student.maintenance.create') }}" class="btn btn-sm btn-secondary">
                <i class="fa fa-plus me-1"></i> {{ __('New Request') }}
            </a>
        </div>
        
        <div class="card-body rounded-3 ">
            @if ($requests->isEmpty())
                <div class="text-center py-4">
                    <img src="{{ asset('images/maintenance/empty-state.svg') }}" alt="{{ __('No requests') }}" class="img-fluid mb-3" style="max-height: 120px">
                    <h6 class="text-muted">{{ __('No maintenance requests found') }}</h6>
                    <p class="small text-muted mb-3">{{ __('Need something fixed? Create your first maintenance request.') }}</p>
                    <a href="{{ route('student.maintenance.create') }}" class="btn btn-primary btn-sm">
                        <i class="fa fa-plus-circle me-1"></i> {{ __('Create Request') }}
                    </a>
                </div>
            @else
                <div class="row g-3">
                    @foreach ($requests as $request)
                        <div class="col-md-6 col-lg-4">
                            <div class="card h-100 border shadow-sm rounded-3">
                                <div class="card-header bg-transparent py-2 d-flex justify-content-between align-items-center">
                                    <span class="fw-bold small">{{ __('Request') }} #{{ $request->id }}</span>
                                    <span class="badge 
                                        @if ($request->status === 'pending') bg-warning text-dark
                                        @elseif ($request->status === 'accepted') bg-info
                                        @elseif ($request->status === 'rejected') bg-danger
                                        @elseif ($request->status === 'assigned') bg-primary
                                        @elseif ($request->status === 'in_progress') bg-info
                                        @elseif ($request->status === 'completed') bg-success
                                        @else bg-secondary @endif 
                                        rounded-pill">
                                        {{ __($request->status) }}
                                    </span>
                                </div>
                                
                                <div class="card-body p-3">
                                    <div class="d-flex align-items-center mb-2">
                                        <i class="fa 
                                            @if ($request->category->name === 'Plumbing') fa-faucet
                                            @elseif ($request->category->name === 'Electrical') fa-bolt
                                            @elseif ($request->category->name === 'Furniture') fa-chair
                                            @elseif ($request->category->name === 'Appliance') fa-blender
                                            @else fa-wrench @endif 
                                            text-primary me-2"></i>
                                        <span class="text-capitalize small fw-medium">{{ __($request->category->name ?? 'N/A') }}</span>
                                    </div>

                                    <!-- Description -->
                                    <p class="small fw-bold mb-1">{{ __('Description') }}:</p>
                                    <p class="small mb-2 text-muted">{{ Str::limit($request->description, 80) }}</p>

                                    <!-- Reject Reason (if rejected) -->
                                    @if($request->status === 'rejected' && $request->reject_reason)
                                        <div class="alert alert-danger small mb-3">
                                            <i class="fa fa-exclamation-circle me-2"></i>
                                            <strong>{{ __('Reject Reason') }}:</strong> {{ $request->reject_reason }}
                                        </div>
                                    @endif

                                    <!-- Problems -->
                                    @if($request->problems->count() > 0)
                                        <div class="list-group-item border-0 px-0 py-1 d-flex">
                                            <i class="fa fa-exclamation-triangle text-secondary me-2 mt-1"></i>
                                            <div>
                                                @if($request->problems->count() <= 2)
                                                    @foreach($request->problems as $problem)
                                                        <span class="badge bg-light text-dark me-1 mb-1">{{ __($problem->name) }}</span>
                                                    @endforeach
                                                @else
                                                    @foreach($request->problems->take(1) as $problem)
                                                        <span class="badge bg-light text-dark me-1 mb-1">{{ __($problem->name) }}</span>
                                                    @endforeach
                                                    <span class="badge bg-secondary text-white" data-bs-toggle="tooltip" 
                                                          title="{{ $request->problems->skip(1)->pluck('name')->join(', ') }}">
                                                        +{{ $request->problems->count() - 1 }}
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                    @endif

                                    <!-- Attachments -->
                                    @if($request->media->count() > 0)
                                        <div class="list-group-item border-0 px-0 py-1">
                                            <i class="fa fa-paperclip text-secondary me-2"></i>
                                            <span>{{ __('Attachments') }} ({{ $request->media->count() }}):</span>
                                            <div class="mt-2">
                                                @foreach ($request->media as $media)
                                                    @if(in_array(pathinfo($media->path, PATHINFO_EXTENSION), ['jpg', 'jpeg', 'png']))
                                                        <a href="{{ asset($media->path) }}" target="_blank">
                                                            <img src="{{ asset($media->path) }}" alt="Attachment" class="img-thumbnail me-1" style="max-width: 80px; max-height: 80px;">
                                                        </a>
                                                    @else
                                                        <a href="{{ asset($media->path) }}" target="_blank" class="d-block text-truncate small">
                                                            <i class="fa fa-file me-1"></i> {{ basename($media->file_path) }}
                                                        </a>
                                                    @endif
                                                @endforeach
                                            </div>
                                        </div>
                                    @endif
                                </div>
                                
                                <!-- Card Footer -->
                                <div class="card-footer bg-transparent py-2 d-flex justify-content-between align-items-center">
                                    <!-- Created Date -->
                                    <small class="text-muted d-flex align-items-center gap-2">
                                        <i class="fa fa-calendar"></i>
                                        <span>{{ $request->created_at->format('M d, Y') }}</span>
                                    </small>

                                    <!-- Accepted or Rejected Date -->
                                    @if($request->accepted_at)
                                        <small class="text-info d-flex align-items-center gap-2">
                                            <i class="fa fa-check-circle"></i>
                                            <span>{{ $request->accepted_at->format('M d, Y') }}</span>
                                        </small>
                                    @elseif($request->rejected_at)
                                        <small class="text-danger d-flex align-items-center gap-2">
                                            <i class="fa fa-times-circle"></i>
                                            <span>{{ $request->rejected_at->format('M d, Y') }}</span>
                                        </small>
                                    @endif

                                    <!-- Completed Date -->
                                    @if($request->completed_at)
                                        <small class="text-success d-flex align-items-center gap-2">
                                            <i class="fa fa-check-circle"></i>
                                            <span>{{ $request->completed_at->format('M d, Y') }}</span>
                                        </small>
                                    @endif

                                    <!-- Assigned Technician -->
                                    @if($request->isAssigned())
                                        <div class="d-flex align-items-center gap-2">
                                            <i class="fa fa-user-cog text-secondary"></i>
                                            <span class="text-muted small">{{ __('Assigned') }}: {{ $request->assignedTo->name ?? 'N/A' }}</span>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
                
                <!-- Pagination -->
                <div class="d-flex justify-content-center mt-4">
                    {{ $requests->links('pagination::bootstrap-5') }}
                </div>
            @endif
        </div>
    </div>
    </div>
    
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl)
        });
    });
</script>
@endpush

@push('styles')
<style>
    .card {
        transition: transform 0.2s ease;
    }
    .card:hover {
        transform: translateY(-3px);
        box-shadow: 0 5px 15px rgba(0,0,0,0.08) !important;
    }
</style>
@endpush