@extends('layouts.admin')
@section('title', __('pages.admin.resident.add-resident.title'))
@section('links')
<!-- Include necessary CSS files -->
<link href="{{ asset('plugins/datatables/dataTables.bootstrap4.min.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ asset('css/custom-form.css') }}" rel="stylesheet" type="text/css" />
@endsection

@section('content')
<div class="container-fluid">
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">{{ __('Add Resident') }}</h3>
        </div>
        <div class="card-body">
            <form method="POST" action="#" enctype="multipart/form-data">
                @csrf
                
                <!-- Personal Information Section -->
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="name_en">Name (English)</label>
                            <input type="text" class="form-control border-secondary" id="name_en" name="name_en" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="name_ar">Name (Arabic)</label>
                            <input type="text" class="form-control border-secondary" id="name_ar" name="name_ar" required>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="national_id">National ID</label>
                            <input type="text" class="form-control border-secondary" id="national_id" name="national_id" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="birthdate">Birthdate</label>
                            <input type="date" class="form-control border-secondary" id="birthdate" name="birthdate" required>
                        </div>
                    </div>
                    
                </div>

                <div class="row">
                <div class="col-md-6">
                        <div class="form-group">
                            <label for="mobile">Mobile Number</label>
                            <input type="text" class="form-control border-secondary" id="mobile" name="mobile" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="gender">Gender</label>
                            <select class="form-control border-secondary" id="gender" name="gender" required>
                                <option value="male">Male</option>
                                <option value="female">Female</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Address Information -->
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="governorate_id">Governorate</label>
                            <select class="form-control border-secondary" id="governorate_id" name="governorate_id">
                                @foreach ($governorates as $governorate)
                                    <option value="{{ $governorate->id }}">{{ $governorate->name_en }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="city_id">City</label>
                            <select class="form-control border-secondary" id="city_id" name="city_id" disabled>
                               <option value="">Select City</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label for="street">Street</label>
                            <input type="text" class="form-control border-secondary" id="street" name="street">
                        </div>
                    </div>
                </div>

                <!-- Academic Information -->
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="faculty_id">Faculty</label>
                            <select class="form-control border-secondary" id="faculty_id" name="faculty_id">
                                @foreach ($faculties as $faculty)
                                    <option value="{{ $faculty->id }}">{{ $faculty->name_en }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="program_id">Program</label>
                            <select class="form-control border-secondary" id="program_id" name="program_id" disabled>
                            <option value="">Select Program</option>

                            </select>
                        </div>
                    </div>
                </div>


                <div class="row mt-4">
                    <div class="col-md-12 text-center">
                        <button type="submit" class="btn btn-primary">Save Resident</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="{{ asset('js/pages/add-residents.js') }}"></script>
<script>
    const cities = @json($cities);
    console.log(cities);
    const programs = @json($programs);
    console.log(programs);
    window.routes = {
      exportExcel: "{{ route('admin.residents.export-excel') }}",
      getResidentMoreDetails: "{{ route('admin.residents.more-details', ':id') }}",
      fetchResidents: "{{ route('admin.residents.fetch') }}",
      getSummary: "{{ route('admin.residents.get-summary') }}"
   };
</script>
@endsection