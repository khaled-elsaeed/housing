@extends('layouts.student') 
@section('title', __('Student Dashboard')) 
@section('content')
<div class="container-fluid py-4">
   <!-- Welcome Section -->
   <div class="row mb-4">
      <div class="col-12">
         <div class="card border-0 bg-white text-primary overflow-hidden position-relative">
            <div class="card-body d-flex align-items-center p-3 p-md-4">
               <div class="d-flex align-items-center w-100">
                  <div class="flex-shrink-0 me-3 me-md-4">
                     <div class="bg-white bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center" style="width: 60px; height: 60px;">
                        <i class="fa fa-user-circle fa-2x text-primary"></i>
                     </div>
                  </div>
                  <div class="flex-grow-1">
                     <h1 class="h4 mb-1 fw-bold">{{ __('Welcome') }}, {{ $user->name}}</h1>
                     <p class="text-primary-50 mb-0 d-none d-md-block">{{ __('Manage your housing and stay updated with your recent activities') }}</p>
                  </div>
                  <div class="flex-shrink-0 d-none d-md-block">
                     <div class="text-primary-50 text-end">
                        <small>{{ date('F d, Y') }}</small>
                     </div>
                  </div>
               </div>
            </div>
         </div>
      </div>
   </div>
   <!-- Dashboard Content -->
   <div class="row">
      <!-- Reservation Overview -->
      <div class="col-lg-8">
         @if($reservation)
         <div class="card border-0 mb-4">
            <div class="card-header bg-white border-0 py-3">
               <h4 class="card-title mb-0 fs-5 fw-semibold">
                  <i class="fa fa-hotel text-primary me-2"></i>{{ __('Active Reservation') }}
               </h4>
            </div>
            <div class="card-body">
               <div class="row g-3">
                  <!-- Reservation Details Cards -->
                  <div class="col-md-4 border">
                     <div class="d-flex align-items-center bg-gradient-light p-3 rounded hover-scale">
                        <div class="me-3">
                           <i class="fa fa-building fa-2x text-primary"></i>
                        </div>
                        <div>
                           <h6 class="text-muted mb-1 fs-6">{{ __('Building') }}</h6>
                           <p class="h5 mb-0 fw-bold">{{ $reservation->room->apartment->building->number }}</p>
                        </div>
                     </div>
                  </div>
                  <div class="col-md-4">
                     <div class="d-flex align-items-center bg-gradient-light p-3 rounded hover-scale">
                        <div class="me-3">
                           <i class="fa fa-home fa-2x text-primary"></i>
                        </div>
                        <div>
                           <h6 class="text-muted mb-1 fs-6">{{ __('Apartment') }}</h6>
                           <p class="h5 mb-0 fw-bold">{{ $reservation->room->apartment->number }}</p>
                        </div>
                     </div>
                  </div>
                  <div class="col-md-4">
                     <div class="d-flex align-items-center bg-gradient-light p-3 rounded hover-scale">
                        <div class="me-3">
                           <i class="fa fa-bed fa-2x text-primary"></i>
                        </div>
                        <div>
                           <h6 class="text-muted mb-1 fs-6">{{ __('Room') }}</h6>
                           <p class="h5 mb-0 fw-bold">{{ $reservation->room->number }}</p>
                        </div>
                     </div>
                  </div>
               </div>
            </div>
         </div>
         @endif
         <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-0 py-3">
               <h4 class="card-title mb-0"><i class="fa fa-history text-primary me-2"></i>{{ __('Recent Activities') }}</h4>
            </div>
            <div class="card-body p-0">
               @if($activities->isNotEmpty())
               <ul class="list-group list-group-flush">
               @foreach($activities as $activity)
               <li class="list-group-item d-flex justify-content-between align-items-center">
                  <div class="d-flex align-items-center">
                     <div class="me-3">
                           @if($activity->activity_type === 'update_profile')
                              <i class="fa fa-check-circle text-success"></i>
                           @elseif($activity->activity_type === 'update_profile_picture')
                              <i class="fa fa-check-circle text-success"></i>
                           @elseif($activity->activity_type === 'delete_profile_picture')
                              <i class="fa fa-check-circle text-success"></i>
                           @elseif($activity->activity_type === 'Reservation Created')
                              <i class="fa fa-check-circle text-success"></i>
                           @elseif($activity->activity_type === 'Invoice Upload')
                              <i class="fa fa-upload text-primary"></i>
                           @elseif($activity->activity_type === 'Room Assignment')
                              <i class="fa fa-building text-info"></i>
                           @elseif($activity->activity_type === 'Term Registration')
                              <i class="fa fa-calendar text-warning"></i>
                           @else
                              <i class="fa fa-circle text-secondary"></i>
                           @endif

                        </div>
                        <div>
                           <h6 class="mb-1">{{ __($activity->activity_type) }}</h6>
                           <small class="text-muted">{{ __($activity->description) }}</small>
                        </div>
                     </div>
                     <span class="text-muted small">{{ $activity->created_at->diffForHumans() }}</span>
                  </li>
                  @endforeach
               </ul>
               @else
               <div class="p-3 text-center text-muted">
                  {{ __('No recent activities found.') }}
               </div>
               @endif
            </div>
         </div>
      </div>
      <!-- Reservation Details and Quick Actions -->
      <div class="col-lg-4 mt-4 mt-md-0">
    @if($reservation)
        <div class="row">
            <div class="col-12">
                <div class="card border-0">
                    <div class="card-header bg-white border-0 py-3">
                        <h4 class="card-title mb-0 fs-5 fw-semibold">
                            <i class="fa fa-users text-primary me-2"></i>{{ __('Apartment Roommates') }}
                        </h4>
                    </div>
                    <div class="card-body">
                        @if(isset($roommates) && $roommates->count() > 0)
                            <div class="row g-3">
                            @foreach($roommates as $roommate)
    <div class="col-md-6 mb-4">
        <div class="card border-0 shadow-sm h-100 hover-scale transition-all">
            <div class="card-body p-4">
                <!-- Profile Icon -->
                <div class="d-flex justify-content-center mb-3">
                    <div class="bg-white rounded-circle d-flex align-items-center justify-content-center shadow-sm border" style="width: 40px; height: 40px;">
                        <i class="fa fa-user text-primary fs-3"></i>
                    </div>
                </div>

                <!-- Roommate Info -->
                <div class="text-center">
                    <h6 class="mb-2 fw-bold text-dark">
                        {{ optional($roommate->user)->name ?? __('Unknown') }}
                    </h6>

                    <!-- Room Number -->
                    <p class="text-muted mb-2 small">
                        <i class="fa fa-bed me-1"></i> {{ __('Room') }} {{ optional($roommate->room)->number ?? 'N/A' }}
                    </p>

                    <!-- Phone Number -->
                    <p class="text-muted mb-2 small">
                        <i class="fa fa-phone me-1"></i> {{ __('Phone') }} {{ optional($roommate->user->student)->phone ?? 'N/A' }}
                    </p>

                    <!-- faculty (Optional) -->
                    <p class="text-muted mb-0 small">
    <i class="fa fa-university me-1"></i> 
    {{ optional($roommate->user->student->faculty)->name ?? 'N/A' }}
</p>

                </div>
            </div>            
        </div>
    </div>
@endforeach
                            </div>
                        @else
                            <div class="text-center text-muted py-3">
                                <i class="fa fa-info-circle mb-2 d-block fs-4"></i>
                                {{ __('No roommates found in your apartment.') }}
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Quick Actions -->
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white border-0 py-3">
            <h4 class="card-title mb-0"><i class="fa fa-bolt text-primary me-2"></i>{{ __('Quick Actions') }}</h4>
        </div>
        <div class="card-body">
            <div class="d-grid gap-3">
                <button class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#addReservationModal">
                    <i class="fa fa-plus-circle me-2"></i>{{ __('New Reservation') }}
                </button>
                <button class="btn btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#guideModal">
                    <i class="fa fa-file-text-o me-2"></i>{{ __('View Guide') }}
                </button>
                <button class="btn btn-outline-info" data-bs-toggle="modal" data-bs-target="#helpSupportModal">
                    <i class="fa fa-support me-2"></i>{{ __('Help & Support') }}
                </button>
            </div>
        </div>
    </div>
</div>
   </div>
</div>
<!-- Add Reservation Modal -->
<div class="modal fade" id="addReservationModal" tabindex="-1" aria-labelledby="addReservationModalLabel">
   <div class="modal-dialog">
      <div class="modal-content">
         <div class="modal-header">
            <h5 class="modal-title" id="addReservationModalLabel">{{ __('Add New Reservation') }}</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="{{ __('Close') }}"></button>
         </div>
         <div class="modal-body">
            <form id="addReservationForm" method="POST" action="{{ route('student.reservation.store') }}">
               @csrf
               <!-- Reservation Period Type -->
               <div class="mb-3">
                  <label for="reservationType" class="form-label">{{ __('Reservation Period Type') }}</label>
                  <select name="reservation_period_type" id="reservationType" class="form-select" required>
                     <option value="" disabled selected>{{ __('Select Reservation Period Type') }}</option>
                     <option value="long">{{ __('Long Period') }}</option>
                     <option value="short">{{ __('Short Period') }}</option>
                  </select>
               </div>

               <!-- Long Term Details (Academic Terms) -->
               <div id="longPeriodDetails" class="mb-3 d-none">
                  <label for="reservationTerm" class="form-label">{{ __('Select Academic Term') }}</label>
                  <select name="reservation_academic_term_id" id="reservationTerm" class="form-select" required>
                     <option value="" disabled selected>{{ __('Select Term') }}</option>
                     @foreach($availableTerms as $term)
                     <option value="{{ $term->id }}">
                        {{ __($term->semester) }}
                        ({{ __($term->name) }}  {{ app()->getLocale() == 'ar' ? arabicNumbers($term->academic_year) : $term->academic_year }})
                     </option>
                     @endforeach
                  </select>

                  <!-- Option to Stay in Old Room -->
                  @if($user->lastReservation())
                  <div id="oldRoomOption" class="mb-3 mt-3">
                     <div class="alert alert-info d-flex align-items-center">
                        <i class="fa fa-info-circle me-2"></i>
                        <span>{{ __('You had a reservation in the previous academic term. Would you like to stay in your old room?') }}</span>
                     </div>
                     <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="stayInOldRoom" name="stay_in_last_old_room">
                        <label class="form-check-label d-flex align-items-center" for="stayInOldRoom">
                           <span class="me-2">{{ __('Yes, I want to stay in my old room') }}</span>
                           <input type="hidden" name="old_room_id" value="{{ $user->lastReservation()->room->id }}">
                           <span class="badge bg-secondary d-flex align-items-center">
                              <i class="fa fa-bed me-1"></i>
                              <span>{{ __('Room') }}: {{ $user->lastReservation()->room->number }}</span>
                           </span>
                           <span class="badge bg-secondary ms-2 d-flex align-items-center">
                              <i class="fa fa-home me-1"></i>
                              <span>{{ __('Apartment') }}: {{ $user->lastReservation()->room->apartment->number }}</span>
                           </span>
                           <span class="badge bg-secondary ms-2 d-flex align-items-center">
                              <i class="fa fa-hotel me-1"></i>
                              <span>{{ __('Building') }}: {{ $user->lastReservation()->room->apartment->building->number }}</span>
                           </span>
                        </label>
                     </div>
                  </div>
                  @endif

                  <!-- Sibling Option (Only shows if user has eligible siblings) -->
                  @if($sibling)
                  <div id="siblingOption" class="mb-3 d-none">
                     <div class="alert alert-info">
                        <i class="fa fa-info-circle me-2"></i>
                        {{ __('We detected that you have a sibling of the same gender in the system. Would you like to share a double room?') }}
                     </div>
                     <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="shareWithSibling" name="share_with_sibling">
                        <input type="hidden" name="sibling_id" value="{{ $sibling->id }}">
                        <label class="form-check-label" for="shareWithSibling">
                           {{ __('Yes, I want to share a double room with my sibling') }}
                           <strong>({{ $sibling->name }})</strong>
                        </label>
                     </div>
                  </div>
                  @endif
               </div>

               <!-- Short Term Details (Day/Week/Month) -->
               <div id="shortPeriodDetails" class="d-none">
                  <label for="shortPeriodDuration" class="form-label">{{ __('Select Duration') }}</label>
                  <select name="short_period_duration" id="shortPeriodDuration" class="form-select" required>
                     <option value="" disabled selected>{{ __('Select Duration') }}</option>
                     <option value="day">{{ __('Day') }}</option>
                     <option value="week">{{ __('Week') }}</option>
                     <option value="month">{{ __('Month') }}</option>
                  </select>
                  <!-- Dates for short term -->
                  <div class="row mt-3">
                     <div class="col-md-6 mb-3">
                        <label for="startDate" class="form-label">{{ __('Start Date') }}</label>
                        <input type="date" class="form-control" id="startDate" name="start_date" required />
                     </div>
                     <div class="col-md-6 mb-3" id="endDateContainer" style="display: none;">
                        <label for="endDate" class="form-label">{{ __('End Date') }}</label>
                        <input type="date" class="form-control" id="endDate" name="end_date" readonly />
                     </div>
                  </div>
               </div>

               <!-- Submit Button -->
               <div class="mt-3">
                  <button type="submit" class="btn btn-primary w-100" id="submitReservation" disabled>
                     <span class="spinner-border spinner-border-sm d-none me-2" role="status" aria-hidden="true" id="submitSpinner"></span>
                     <span id="submitText">{{ __('Add Reservation') }}</span>
                  </button>
               </div>
            </form>
         </div>
      </div>
   </div>
</div>

<!-- Guide Modal -->
<div class="modal fade" id="guideModal" tabindex="-1" aria-labelledby="guideModalLabel">
   <div class="modal-dialog modal-lg">
      <div class="modal-content">
         <div class="modal-header">
            <h5 class="modal-title" id="guideModalLabel">{{ __('Housing System Guide') }}</h5>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="{{ __('Close') }}"></button>
         </div>
         <div class="modal-body">
            <h6 class="card-subtitle mb-4">{{ __('Welcome to NMU Housing System. Here\'s everything you need to know about managing your housing.') }}</h6>
            <div class="accordion" id="housingGuideAccordion">
               <!-- Long Period Reservation Guide -->
               <div class="card">
                  <div class="card-header" id="longPeriodGuide">
                     <h2 class="mb-0">
                        <button class="btn btn-link" type="button" data-bs-toggle="collapse" 
                           data-bs-target="#collapseLongPeriod" aria-expanded="true" 
                           aria-controls="collapseLongPeriod">
                        <i class="feather icon-calendar me-2"></i>{{ __('Long Period Reservation') }}
                        </button>
                     </h2>
                  </div>
                  <div id="collapseLongPeriod" class="collapse show" 
                     aria-labelledby="longPeriodGuide" 
                     data-parent="#housingGuideAccordion">
                     <div class="card-body">
                        <ol class="list-group list-group-numbered">
                           <li class="list-group-item">{{ __('Click "New Reservation" on your dashboard') }}</li>
                           <li class="list-group-item">{{ __('Select "Long Period" (e.g., academic term, fall/spring)') }}</li>
                           <li class="list-group-item">{{ __('Choose an available period (e.g., Spring Term 2024)') }}</li>
                           <li class="list-group-item">{{ __('If you stayed with us last term, you’ll keep your old room') }}</li>
                           <li class="list-group-item">{{ __('If new, choose your room type (single or double)') }}</li>
                           <li class="list-group-item">{{ __('If you have a same-gender sibling, you can share a double room') }}</li>
                           <li class="list-group-item">{{ __('Submit your reservation and wait for housing approval') }}</li>
                        </ol>
                     </div>
                  </div>
               </div>
               <!-- Short Period Reservation Guide -->
               <div class="card">
                  <div class="card-header" id="shortPeriodGuide">
                     <h2 class="mb-0">
                        <button class="btn btn-link collapsed" type="button" 
                           data-bs-toggle="collapse" data-bs-target="#collapseShortPeriod" 
                           aria-expanded="false" aria-controls="collapseShortPeriod">
                        <i class="feather icon-clock me-2"></i>{{ __('Short Period Reservation') }}
                        </button>
                     </h2>
                  </div>
                  <div id="collapseShortPeriod" class="collapse" 
                     aria-labelledby="shortPeriodGuide" 
                     data-parent="#housingGuideAccordion">
                     <div class="card-body">
                     <ol class="list-group list-group-numbered">
                        <li class="list-group-item">{{ __('Click "New Reservation" on your dashboard') }}</li>
                        <li class="list-group-item">{{ __('Select "Short Period" (day, week, or month)') }}</li>
                        <li class="list-group-item">{{ __('Choose your stay period') }}</li>
                        <li class="list-group-item">{{ __('Submit and await approval') }}</li>
                    </ol>

                     </div>
                  </div>
               </div>
               <!-- Activate/Finalize Reservation Guide -->
               <div class="card">
                  <div class="card-header" id="activateReservationGuide">
                     <h2 class="mb-0">
                        <button class="btn btn-link collapsed" type="button" 
                           data-bs-toggle="collapse" data-bs-target="#collapseActivateReservation" 
                           aria-expanded="false" aria-controls="collapseActivateReservation">
                        <i class="feather icon-check-circle me-2"></i>{{ __('Activate/Finalize Reservation') }}
                        </button>
                     </h2>
                  </div>
                  <div id="collapseActivateReservation" class="collapse" 
                     aria-labelledby="activateReservationGuide" 
                     data-parent="#housingGuideAccordion">
                     <div class="card-body">
                        <ol class="list-group list-group-numbered">
                           <li class="list-group-item">{{ __('Go to "My Profile" and click on "Payments"') }}</li>
                           <li class="list-group-item">{{ __('If your reservation is accepted, the payment option will appear') }}</li>
                           <li class="list-group-item">{{ __('Click "Pay Now" and Upload the invoice image') }}</li>
                           <li class="list-group-item">{{ __('Wait for housing management to confirm the invoice') }}</li>
                           <li class="list-group-item">{{ __('Once confirmed, your reservation will be activated') }}</li>
                        </ol>
                     </div>
                  </div>
               </div>
               <!-- Housing Rules & Regulations -->
               <div class="card">
                  <div class="card-header" id="rulesGuide">
                     <h2 class="mb-0">
                        <button class="btn btn-link collapsed" type="button" 
                           data-bs-toggle="collapse" data-bs-target="#collapseRules" 
                           aria-expanded="false" aria-controls="collapseRules">
                        <i class="feather icon-book me-2"></i>{{ __('Housing Rules & Regulations') }}
                        </button>
                     </h2>
                  </div>
                  <div id="collapseRules" class="collapse" 
                     aria-labelledby="rulesGuide" 
                     data-parent="#housingGuideAccordion">
                     <div class="card-body">
                        <div class="alert alert-info">
                           {{ __('All residents must follow these rules to maintain their housing eligibility.') }}
                        </div>
                        <ul class="list-group">
                           <li class="list-group-item">{{ __('Quiet hours: 8:00 PM - 11:00 AM') }}</li>
                           <li class="list-group-item">{{ __('Keep rooms and common areas clean') }}</li>
                           <li class="list-group-item">{{ __('Report maintenance issues immediately') }}</li>
                           <li class="list-group-item">{{ __('No smoking in buildings') }}</li>
                           <li class="list-group-item">{{ __('Respect other residents and staff') }}</li>
                        </ul>
                     </div>
                  </div>
               </div>
            </div>
         </div>
      </div>
   </div>
</div>

<!-- Help & Support Modal -->
<div class="modal fade" id="helpSupportModal" tabindex="-1" aria-labelledby="helpSupportModalLabel">
   <div class="modal-dialog modal-lg">
      <div class="modal-content">
         <div class="modal-header">
            <h5 class="modal-title" id="helpSupportModalLabel">{{ __('Help & Support') }}</h5>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="{{ __('Close') }}"></button>
         </div>
         <div class="modal-body">
            <h6 class="card-subtitle mb-4">{{ __('Need assistance? Here’s how we can help.') }}</h6>
            <div class="accordion" id="helpSupportAccordion">
               <!-- Contact Information -->
               <div class="card">
                  <div class="card-header" id="contactInfoHeader">
                     <h2 class="mb-0">
                        <button class="btn btn-link" type="button" data-bs-toggle="collapse" 
                           data-bs-target="#collapseContactInfo" aria-expanded="true" 
                           aria-controls="collapseContactInfo">
                        <i class="feather icon-phone-call me-2"></i>{{ __('Contact Information') }}
                        </button>
                     </h2>
                  </div>
                  <div id="collapseContactInfo" class="collapse show" 
                     aria-labelledby="contactInfoHeader" 
                     data-parent="#helpSupportAccordion">
                     <div class="card-body">
                        <div class="row">
                           <!-- Female Housing Contacts -->
                           <div class="col-md-6">
                              <h6 class="fw-bold">{{ __('Female Housing Contacts:') }}</h6>
                              <ul class="list-group mb-3">
                                 <li class="list-group-item">
                                    <strong>{{ __('Shaima Abdel-Mordi:') }}</strong><br>
                                    {{ __('Role: Housing Manager') }}<br>
                                 </li>
                                 <li class="list-group-item">
                                    <strong>{{ __('Hend Sabry:') }}</strong><br>
                                    {{ __('Role: Supervisor-female') }}<br>
                                    
                                 </li>
                                 <li class="list-group-item">
                                    <strong>{{ __('Nagwa Ebrahim:') }}</strong><br>
                                    {{ __('Role: Supervisor-female') }}<br>
                                    
                                 </li>
                              </ul>
                           </div>
                           <!-- Male Housing Contacts -->
                           <div class="col-md-6">
                              <h6 class="fw-bold">{{ __('Male Housing Contacts:') }}</h6>
                              <ul class="list-group mb-3">
                                 <li class="list-group-item">
                                    <strong>{{ __('Mohamed Douad:') }}</strong><br>
                                    {{ __('Role: Supervisor-male') }}<br>
                                 </li>
                                 <li class="list-group-item">
                                    <strong>{{ __('Ismail:') }}</strong><br>
                                    {{ __('Role: Supervisor-male') }}<br>
                                 </li>
                                 <li class="list-group-item">
                                    <strong>{{ __('Mohammed Al Rahmani:') }}</strong><br>
                                    {{ __('Role: Supervisor-male') }}<br>
                                 </li>
                              </ul>
                           </div>
                        </div>
                     </div>
                  </div>
               </div>
               <!-- IT Support -->
               <div class="card">
                  <div class="card-header" id="itSupportHeader">
                     <h2 class="mb-0">
                        <button class="btn btn-link collapsed" type="button" 
                           data-bs-toggle="collapse" data-bs-target="#collapseITSupport" 
                           aria-expanded="false" aria-controls="collapseITSupport">
                        <i class="feather icon-monitor me-2"></i>{{ __('IT Support') }}
                        </button>
                     </h2>
                  </div>
                  <div id="collapseITSupport" class="collapse" 
                     aria-labelledby="itSupportHeader" 
                     data-parent="#helpSupportAccordion">
                     <div class="card-body">
                        <h6 class="fw-bold">{{ __('IT Department (Available from 9 AM to 4 PM (IT Office))') }}</h6>
                        <ul class="list-group mb-3">
                           <li class="list-group-item">
                              <strong>{{ __('Ahmed Elemam') }}</strong><br>
                           </li>
                           <li class="list-group-item">
                              <strong>{{ __('Khaled Zahran') }}</strong><br>
                           </li>
                        </ul>
                     </div>
                  </div>
               </div>
            </div>
         </div>
      </div>
   </div>
</div>

@endsection 
@section('scripts')
<script>
   document.addEventListener("DOMContentLoaded", function () {
       const form = document.getElementById("addReservationForm");
       const reservationType = document.getElementById("reservationType");
       const shortPeriodDetails = document.getElementById("shortPeriodDetails");
       const longPeriodDetails = document.getElementById("longPeriodDetails");
       const submitReservation = document.getElementById("submitReservation");
       const startDate = document.getElementById("startDate");
       const endDate = document.getElementById("endDate");
       const endDateContainer = document.getElementById("endDateContainer");
       const reservationTerm = document.getElementById("reservationTerm");
       const shortPeriodDuration = document.getElementById("shortPeriodDuration");
   
       // Handle the Reservation Period Type change (Long Term / Short Term)
       reservationType.addEventListener("change", function () {
           shortPeriodDetails.classList.add("d-none");
           longPeriodDetails.classList.add("d-none");
           submitReservation.disabled = true;
   
           if (this.value === "") {
               return;
           }
   
           if (this.value === "long") {
               longPeriodDetails.classList.remove("d-none");
               shortPeriodDetails.classList.add("d-none");
               shortPeriodDuration.removeAttribute("required");
               startDate.removeAttribute("required");
               // Optionally populate long-term terms dynamically
               reservationTerm.value = ""; // Reset term selection if switching
           } else if (this.value === "short") {
               shortPeriodDetails.classList.remove("d-none");
               longPeriodDetails.classList.add("d-none");
               shortPeriodDuration.setAttribute("required", "required");
               startDate.setAttribute("required", "required");
               reservationTerm.removeAttribute("required");
               // Show or hide the end date field based on short term duration
               endDateContainer.style.display = shortPeriodDuration.value === "day" ? "none" : "block";
           }
       });
       startDate.addEventListener("change", () => {
   const startDateValue = startDate.value;
   const startDateObject = new Date(startDateValue);
   
   const shortPeriodType = shortPeriodDuration.value;
   
   if (shortPeriodType === 'week') {
      const endDateObject = new Date(startDateObject);
      endDateObject.setDate(startDateObject.getDate() + 6);
      
      const endDateFormatted = endDateObject.toISOString().split('T')[0];
      endDate.value = endDateFormatted;
   } else if (shortPeriodType === 'month') {
      const endDateObject = new Date(startDateObject);
      endDateObject.setMonth(startDateObject.getMonth() + 1);
      endDateObject.setDate(0); // Last day of the month
      
      const endDateFormatted = endDateObject.toISOString().split('T')[0];
      endDate.value = endDateFormatted;
   }
   });
   
       // Handle the short-term duration selection (Day/Week/Month)
       shortPeriodDuration.addEventListener("change", function () {
           endDateContainer.style.display = this.value === "day" ? "none" : "block";
           if (this.value === "day") {
               endDate.removeAttribute("required");
           } else {
               endDate.setAttribute("required", "required");
           }
           
           // Set min date to today for start date
           const today = new Date().toISOString().split('T')[0];
           startDate.setAttribute('min', today);
           
           updateSubmitButton();
       });
   
       // Validate Dates
       function validateDates() {
           if (reservationType.value === "short") {
               const today = new Date();
               today.setHours(0, 0, 0, 0);
               
               if (shortPeriodDuration.value === "day") {
                   const selectedDate = new Date(startDate.value);
                   return selectedDate >= today;
               }
   
               if (startDate.value && endDate.value) {
                   const start = new Date(startDate.value);
                   const end = new Date(endDate.value);
                   return start >= today && start < end;
               }
               return false;
           }
           return true;
       }
   
       // Enable submit button when all required fields are filled
       function updateSubmitButton() {
           const islongPeriod = reservationType.value === "long";
           const isshortPeriod = reservationType.value === "short";
   
           let isValid = false;
   
           if (islongPeriod) {
               isValid = reservationTerm.value !== "";
           } else if (isshortPeriod) {
               if (shortPeriodDuration.value === "day") {
                   isValid = startDate.value !== "";
               } else if (shortPeriodDuration.value) {
                   isValid = startDate.value !== "" && endDate.value !== "" && validateDates();
               }
           }
   
           submitReservation.disabled = !isValid;
       }
   
       // Add event listeners for form validation
       const formInputs = form.querySelectorAll("select, input");
       formInputs.forEach((input) => {
           input.addEventListener("change", updateSubmitButton);
           input.addEventListener("input", updateSubmitButton);
       });
   
       startDate.addEventListener("change", function () {
           // Prevent selection of past dates
           const today = new Date();
           today.setHours(0, 0, 0, 0);
           const selectedDate = new Date(this.value);
           
           if (selectedDate < today) {
               swal({
                   title: "{{ __('Invalid Date') }}",
                   text: "{{ __('Please select a present or future date') }}",
                   type: "warning",
               });
               this.value = '';
               updateSubmitButton();
               return;
           }
   
           // Update end date based on duration type
           if (shortPeriodDuration.value !== "day") {
               const startDateValue = this.value;
               const startDateObject = new Date(startDateValue);
               
               if (shortPeriodDuration.value === 'week') {
                   const endDateObject = new Date(startDateObject);
                   endDateObject.setDate(startDateObject.getDate() + 6);
                   endDate.value = endDateObject.toISOString().split('T')[0];
               } else if (shortPeriodDuration.value === 'month') {
                   const endDateObject = new Date(startDateObject);
                   endDateObject.setMonth(startDateObject.getMonth() + 1);
                   endDateObject.setDate(0);
                   endDate.value = endDateObject.toISOString().split('T')[0];
               }
           }
           
           updateSubmitButton();
       });
   
       endDate.addEventListener("change", function () {
           if (startDate.value) {
               validateDates();
           }
           updateSubmitButton();
       });
   
       // Submit Form Handling (for demonstration)
       form.addEventListener("submit", async function (e) {
           e.preventDefault();
   
           if (!form.checkValidity()) {
               e.stopPropagation();
               return;
           }
   
           const submitBtn = document.getElementById("submitReservation");
           const spinner = document.getElementById("submitSpinner");
           const submitText = document.getElementById("submitText");
   
           // Disable button and show loading state
           submitBtn.disabled = true;
           spinner.classList.remove("d-none");
           submitText.textContent = '{{ __('Processing...') }}';
   
           try {
               const formData = new FormData(form);
               const response = await fetch(form.action, {
                   method: "POST",
                   body: formData,
                   headers: {
                       "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content,
                       Accept: "application/json",
                   },
               });
   
               const data = await response.json();
   
               if (response.ok) {
                   await swal({
                       title: '{{ __('Success') }}',
                       text: data.message || '{{ __('Reservation has been added successfully!') }}',
                       type: "success",
                   });
   
                   // Reset form and close modal
                   form.reset();
                   $("#addReservationModal").modal("hide");
   
                   // Optionally reload the page or update the UI
                   window.location.reload();
               } else {
                   throw new Error(data.message || '{{ __('Something went wrong!') }}');
               }
           } catch (error) {
               await swal({
                   title: '{{ __('Error') }}',
                   text: error.message,
                   type: "error",
               });
           } finally {
               // Reset button state
               submitBtn.disabled = false;
               spinner.classList.add("d-none");
               submitText.textContent = '{{ __('Add Reservation') }}';
           }
       });
   });
</script>
@endsection