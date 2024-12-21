@extends('layouts.student')

@section('title', __('pages.student.home.title'))

@section('content')
<div class="container my-1">
    <!-- Welcome Section -->
    <div class="row">
        <div class="col-12">
            <div class="p-4 bg-primary text-white rounded shadow-sm text-center">
                <h1 class="mb-3 text-white"><i class="fa fa-user-circle"></i> {{ $user->getUsernameEnAttribute() }}</h1>
                <p class="lead text-white">{{ __('pages.student.home.welcome_message') }}</p>
            </div>
        </div>
    </div>

    <!-- Reservation Details Section -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card border-secondary shadow">
                <div class="card-header bg-secondary text-white text-center">
                    <h4 class="mb-0 text-white"><i class="fa fa-hotel"></i> {{ __('pages.student.home.active_reservation') }}</h4>
                </div>
                <div class="card-body">
                    @if($Reservation)
                        <!-- Cards for Each Detail -->
                        <div class="row text-center">
                            <!-- Building Number Card -->
                            <div class="col-md-4 mb-3">
                                <div class="card border-primary h-100">
                                    <div class="card-body">
                                        <i class="fa fa-building fa-2x text-primary"></i>
                                        <h5 class="card-title mt-3">{{ __('pages.student.home.building') }}</h5>
                                        <p class="card-text">{{ $Reservation->room->apartment->building->number }}</p>
                                    </div>
                                </div>
                            </div>

                            <!-- Apartment Number Card -->
                            <div class="col-md-4 mb-3">
                                <div class="card border-primary h-100">
                                    <div class="card-body">
                                        <i class="fa fa-home fa-2x text-primary"></i>
                                        <h5 class="card-title mt-3">{{ __('pages.student.home.apartment') }}</h5>
                                        <p class="card-text">{{ $Reservation->room->apartment->number }}</p>
                                    </div>
                                </div>
                            </div>

                            <!-- Room Number Card -->
                            <div class="col-md-4 mb-3">
                                <div class="card border-primary h-100">
                                    <div class="card-body">
                                        <i class="fa fa-bed fa-2x text-primary"></i>
                                        <h5 class="card-title mt-3">{{ __('pages.student.home.room') }}</h5>
                                        <p class="card-text">{{ $Reservation->room->number }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Additional Details -->
                        <div class="row text-center">
                            <!-- Status Card -->
                            <div class="col-md-4 mb-3">
                                <div class="card border-primary h-100">
                                    <div class="card-body">
                                        <i class="fa fa-check-circle fa-2x text-success"></i>
                                        <h5 class="card-title mt-3">{{ __('pages.student.home.status') }}</h5>
                                        <span class="badge badge-{{ $Reservation->status === 'pending' ? 'warning' : ($Reservation->status === 'confirmed' ? 'success' : 'danger') }}">
                                            {{ ucfirst($Reservation->status) }}
                                        </span>
                                    </div>
                                </div>
                            </div>

                            <!-- Term Card -->
                            <div class="col-md-4 mb-3">
                                <div class="card border-primary h-100">
                                    <div class="card-body">
                                        <i class="fa fa-calendar fa-2x text-secondary"></i>
                                        <h5 class="card-title mt-3">{{ __('pages.student.home.term') }}</h5>
                                        <p class="card-text">
                                            {{ ucfirst(str_replace('_', ' ', $Reservation->term)) }}
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <!-- Year Card -->
                            <div class="col-md-4 mb-3">
                                <div class="card border-primary h-100">
                                    <div class="card-body">
                                        <i class="fa fa-calendar-check-o fa-2x text-secondary"></i>
                                        <h5 class="card-title mt-3">{{ __('pages.student.home.year') }}</h5>
                                        <p class="card-text">{{ $Reservation->year }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                    @else
                        <div class="text-center py-5">
                            <h5 class="text-muted"><i class="fa fa-info-circle"></i> {{ __('pages.student.home.no_active_reservation') }}</h5>
                            <a href="#" class="btn btn-success btn-lg mt-4">
                                <i class="fa fa-plus-circle"></i> {{ __('pages.student.home.create_new_reservation') }}
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
@endsection
