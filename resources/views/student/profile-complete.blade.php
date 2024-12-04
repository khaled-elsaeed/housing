<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
    <title>تحديث بيانات السكن - جامعة المنصورة الجديدة</title>
    <!-- Favicon -->
    <link rel="shortcut icon" href="{{ asset('images/logo.png') }}">
    <!-- Start CSS -->
    <link href="{{ asset('css/bootstrap.min.css') }}" rel="stylesheet" type="text/css">
    <link href="{{ asset('css/icons.css') }}" rel="stylesheet" type="text/css">
    <link href="{{ asset('css/flag-icon.min.css') }}" rel="stylesheet" type="text/css">
    <link href="{{ asset('css/style.css') }}" rel="stylesheet" type="text/css">
    <link rel="stylesheet" href="{{ asset('css/resident-profile-complete.css') }}">
</head>

<body class="vertical-layout">
    <div class="full-page-container">
        <div class="loader2">
            <div class="loader-logo-container">
                <img src="{{ asset('images/logo.png') }}" class="loader-logo" alt="Loading...">
            </div>
            <div class="loading-text">جاري التحميل...</div>
        </div>
    </div>


    <!-- Start Containerbar -->
    <div id="containerbar">
        <!-- Start Rightbar -->
        <div class="rightbar">
            <!-- Start Topbar Mobile -->
            <x-mobile-topbar />
            <!-- Start Topbar -->
            <div class="topbar">
                <!-- Start row -->
                <div class="row align-items-center">
                    <!-- Start col -->
                    <div class="col-md-12 d-flex justify-content-center justify-content-md-between align-items-center">
                        <!-- Logo section -->
                        <div class="logo-section d-none d-md-block">
                            <a href="{{ route('login') }}" class="logo logo-large">
                                <img src="{{ asset('images/logo2.png') }}" alt="logo">
                            </a>
                        </div>
                        <!-- Infobar section (Settings, Notifications, Language, Profile) -->
                        <div class="infobar d-flex align-items-center">
                            <ul class="list-inline mb-0 d-flex align-items-center m-0">
                                <li class="list-inline-item">
                                    <div class="notifybar">
                                        <div class="dropdown">
                                            <a class="dropdown-toggle infobar-icon" href="#" role="button"
                                                id="notificationLink" data-bs-toggle="dropdown" aria-expanded="false">
                                                <img src="{{ asset('images/svg-icon/notifications.svg') }}"
                                                    class="img-fluid" alt="notifications">
                                                <span class="live-icon"></span>
                                            </a>
                                            <div class="dropdown-menu dropdown-menu-end"
                                                aria-labelledby="notificationLink">
                                                <div class="notification-dropdown-title">
                                                    <h4>الإشعارات</h4>
                                                </div>
                                                <ul class="list-unstyled">
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                </li>
                                <li class="list-inline-item">
                                    <div class="profilebar">
                                        <div class="dropdown">
                                            <a class="dropdown-toggle" href="#" role="button" id="profileLink"
                                                data-bs-toggle="dropdown" aria-expanded="false">
                                                <img src="{{ asset('images/svg-icon/male.svg') }}" class="img-fluid"
                                                    alt="profile">
                                                <span class="feather icon-chevron-down live-icon"></span>
                                            </a>
                                            <div class="dropdown-menu dropdown-menu-end" aria-labelledby="profileLink">
                                                <div class="dropdown-item">
                                                    <div class="profilename">
                                                        <h5>{{ auth()->user()->username_ar }}</h5>
                                                    </div>
                                                </div>
                                                <div class="userbox">
                                                    <ul class="list-unstyled mb-0">
                                                        <li class="d-flex p-2 mt-1 dropdown-item">
                                                            <a href="{{ route('logout') }}" class="profile-icon"
                                                                onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                                                <img src="{{ asset('images/svg-icon/logout.svg') }}"
                                                                    class="img-fluid" alt="logout">تسجيل الخروج
                                                            </a>
                                                            <form id="logout-form" action="{{ route('logout') }}"
                                                                method="POST" style="display: none;">
                                                                @csrf
                                                            </form>
                                                        </li>
                                                    </ul>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </li>
                            </ul>
                        </div>
                    </div>
                    <!-- End col -->
                </div>
                <!-- End row -->
            </div>
            <!-- End Topbar -->
            <!-- Start Contentbar -->
            <div class="contentbar">
                <div class="row">
                    <div class="col-md-12">
                        <div class="card shadow-sm">
                            <div class="card-body">
                                <!-- Progress Bar -->
                                <div class="progress">
                                    <div class="progress-bar progress-bar-striped" id="progressBar" role="progressbar"
                                        style="width: 0%;" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100">0%
                                    </div>
                                </div>
                                <!-- Form and Sidebar Container -->
                                <div class="form-and-sidebar">
                                    <!-- Sidebar with Image and Slogan -->
                                    <div class="sidebar">
                                        <div class="logo-container">
                                            <img src="{{ asset('images/logo.png') }}" alt="Logo"
                                                class="slogan-logo">
                                        </div>
                                        <div class="slogan">سكن جامعة المنصورة الجديدة</div>
                                    </div>
                                    <!-- Multi-step Form -->
                                    <form id="multiStepForm" class="form-container" action="#" method="POST">
                                        @csrf
                                        @if ($errors->any())
                                            <div class="alert alert-danger">
                                                <ul>
                                                    @foreach ($errors->all() as $error)
                                                        <li>{{ $error }}</li>
                                                    @endforeach
                                                </ul>
                                            </div>
                                        @endif
                                        @if (session('success'))
                                            <div class="alert alert-success">
                                                {{ session('success') }}
                                            </div>
                                        @endif
                                        <!-- Step 1: Personal Information -->
                                        <!-- Step 1: Personal Information -->
                                        <div class="step active">
                                            <div class="step-title">الخطوة 1: المعلومات الشخصية</div>
                                            <div class="container">
                                                <div class="row mb-3">
                                                    <!-- English Name -->
                                                    <div class="col-md-6 form-group">
                                                        <label for="name_en" class="form-label">
                                                            الاسم الكامل (إنجليزي) <span class="text-danger">*</span>
                                                        </label>
                                                        <input type="text" class="form-control" id="name_en"
                                                            name="name_en"
                                                            value="{{ old('name_en', $archivedData ? $archivedData->name_en : '') }}"
                                                            readonly>
                                                    </div>
                                                    <!-- Arabic Name -->
                                                    <div class="col-md-6 form-group">
                                                        <label for="name_ar" class="form-label">
                                                            الاسم الكامل (عربي) <span class="text-danger">*</span>
                                                        </label>
                                                        <input type="text" class="form-control" id="name_ar"
                                                            name="name_ar"
                                                            value="{{ old('name_ar', $archivedData ? $archivedData->name_ar : '') }}"
                                                            readonly>
                                                    </div>
                                                </div>
                                                <!-- National ID -->
                                                <div class="mb-3 form-group">
                                                    <label for="national_id" class="form-label">
                                                        الرقم القومي <span class="text-danger">*</span>
                                                    </label>
                                                    <input type="text" class="form-control" id="national_id"
                                                        name="national_id"
                                                        value="{{ old('national_id', $archivedData ? $archivedData->national_id : '') }}"
                                                        readonly>
                                                </div>



                                                <div class="row mb-3">
                                                    <!-- Date of Birth -->
                                                    <div class="col-md-6 form-group">
                                                        <label for="date_of_birth" class="form-label">
                                                            تاريخ الميلاد <span class="text-danger">*</span>
                                                        </label>
                                                        <input type="date" class="form-control" id="date_of_birth"
                                                            name="date_of_birth" value="" readonly>

                                                    </div>
                                                    <!-- Gender -->
                                                    <div class="col-md-6 form-group">
                                                        <label for="gender" class="form-label">
                                                            الجنس <span class="text-danger">*</span>
                                                        </label>
                                                        <input type="text" class="form-control" id="gender"
                                                            name="gender"
                                                            value="{{ old('gender', $archivedData ? $archivedData->gender : '') }}"
                                                            readonly>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Step 2: Contact Details -->
                                        <div class="step">
                                            <div class="step-title">الخطوة 2: تفاصيل الاتصال</div>
                                                <div class="container">
                                                    <div class="row mb-3">
                                                        <!-- Governorate -->
                                                        <div class="col-md-6 form-group">
                                                            <label for="governorate" class="form-label">
                                                                المحافظة <span class="text-danger">*</span>
                                                            </label>

                                                            @if ($archivedData && $archivedData->govern)
                                                                <!-- If archived data has a governorate, populate it in a readonly input field -->
                                                                <input type="text" class="form-control"
                                                                    id="governorate" name="governorate"
                                                                    value="{{ old('governorate', $archivedData->govern) }}"
                                                                    readonly>
                                                            @else
                                                                <!-- If no archived data is found, show a select dropdown for user to choose -->
                                                                <select class="form-select" id="governorate"
                                                                    name="governorate" required>
                                                                    <option value="">حدد الخيار</option>
                                                                    @foreach ($governorates as $governorate)
                                                                        <option value="{{ $governorate->name_ar }}"
                                                                            {{ old('governorate') == $governorate->name_ar ? 'selected' : '' }}>
                                                                            {{ $governorate->name_ar }}
                                                                        </option>
                                                                    @endforeach
                                                                </select>

                                                            @endif
                                                        </div>


                                                        <div class="col-md-6 form-group">
                                                            <label for="governorate" class="form-label">
                                                                المحافظة <span class="text-danger">*</span>
                                                            </label>
                                                            <select class="form-select" id="governorate"
                                                                name="governorate" required>
                                                                <option value="" disabled selected>حدد المحافظة
                                                                </option>
                                                                @foreach ($governorates as $governorate)
                                                                    <option value="{{ $governorate->id }}"
                                                                        {{ old('governorate', $archivedData->governorate_id ?? '') == $governorate->id ? 'selected' : '' }}>
                                                                        {{ $governorate->name_ar }}
                                                                    </option>
                                                                @endforeach
                                                            </select>
                                                        </div>

                                                        <div class="col-md-6 form-group">
                                                            <label for="city" class="form-label">
                                                                المدينة <span class="text-danger">*</span>
                                                            </label>
                                                            @if ($archivedData && $archivedData->city)
                                                                <input type="text" class="form-control" id="city"
                                                                    name="city"
                                                                    value="{{ old('city', $archivedData->city) }}"
                                                                    readonly>
                                                            @else
                                                                <select class="form-select" id="city" name="city"
                                                                    required>
                                                                    <option value="" disabled selected>اختر المدينة
                                                                    </option>
                                                                    <!-- City options will be populated dynamically based on governorate -->
                                                                </select>
                                                            @endif
                                                        </div>




                                                        <div class="row mb-3">
                                                            <!-- Street -->
                                                            <div class="col-md-6 form-group">
                                                                <label for="street" class="form-label">
                                                                    الشارع <span class="text-danger">*</span>
                                                                </label>
                                                                <input type="text" class="form-control" id="street"
                                                                    name="street"
                                                                    value="{{ old('street', $archivedData ? $archivedData->street : '') }}"
                                                                    @if (isset($archivedData) && $archivedData->street !== null) readonly @endif
                                                                    required>
                                                            </div>

                                                            <!-- Phone -->
                                                            <div class="col-md-6 form-group">
                                                                <label for="phone" class="form-label">
                                                                    رقم الهاتف <span class="text-danger">*</span>
                                                                </label>
                                                                <input type="tel" class="form-control" id="phone"
                                                                    name="phone"
                                                                    value="{{ old('phone', $archivedData ? $archivedData->mobile : '') }}"
                                                                    @if (isset($archivedData) && $archivedData->mobile !== null) readonly @endif
                                                                    required>
                                                            </div>
                                                        </div>

                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                            <!-- Step 3: Parent Information -->
                                            <div class="step" id="step3">
                                                <div class="step-title">الخطوة 3: معلومات ولي الأمر</div>
                                                    <div class="container">
                                                        <div class="row">
                                                            <!-- Parent Relation -->
                                                            <div class="col-md-6 form-group">
                                                                <label for="parent_relation">العلاقة بولي الأمر <span
                                                                        class="text-danger">*</span></label>
                                                                <select class="form-control" id="parent_relation"
                                                                    name="parent_relation" required>
                                                                    <option value="">حدد الخيار</option>
                                                                    <option value="father">أب</option>
                                                                    <option value="mother">أم</option>
                                                                </select>
                                                            </div>

                                                            <!-- Parent Name -->
                                                            <div class="col-md-6 form-group">
                                                                <label for="parent_name">الاسم الكامل لولي الأمر <span
                                                                        class="text-danger">*</span></label>
                                                                <input type="text" class="form-control"
                                                                    id="parent_name" name="parent_name"
                                                                    value="{{ old('parent_name', $archivedData ? $archivedData->parent_name : '') }}"
                                                                    @if (isset($archivedData) && $archivedData->parent_name !== null) readonly @endif
                                                                    required>
                                                            </div>
                                                        </div>

                                                        <div class="row">
                                                            <!-- Parent Phone -->
                                                            <div class="col-md-6 form-group">
                                                                <label for="parent_phone">رقم هاتف ولي الأمر <span
                                                                        class="text-danger">*</span></label>
                                                                <input type="tel" class="form-control"
                                                                    id="parent_phone" name="parent_phone"
                                                                    value="{{ old('parent_phone', $archivedData ? $archivedData->parent_mobile : '') }}"
                                                                    @if (isset($archivedData) && $archivedData->parent_mobile !== null) readonly @endif
                                                                    required>
                                                            </div>

                                                            <!-- Parent Email -->
                                                            <div class="col-md-6 form-group">
                                                                <label for="parent_email">البريد الإلكتروني لولي الأمر
                                                                    <span class="text-danger">*</span></label>
                                                                <input type="email" class="form-control"
                                                                    id="parent_email" name="parent_email"
                                                                    value="{{ old('parent_email', $archivedData ? $archivedData->parent_email : '') }}"
                                                                    @if (isset($archivedData) && $archivedData->parent_email !== null) readonly @endif
                                                                    required>
                                                            </div>
                                                        </div>

                                                        <div class="row">
                                                            <!-- Abroad Country Location -->
                                                            <div class="col-md-6 form-group">
                                                                <label for="parent_is_abroad">موقع ولي الأمر <span
                                                                        class="text-danger">*</span></label>
                                                                @if (isset($archivedData) && $archivedData->parent_is_abroad !== null)
                                                                    <!-- For existing archived data: Display as readonly -->
                                                                    <input type="text" class="form-control"
                                                                        id="parent_is_abroad_display"
                                                                        value="{{ $archivedData->parent_is_abroad == '1' ? 'خارج البلاد' : 'داخل البلاد' }}"
                                                                        readonly>
                                                                    <input type="hidden" id="parent_is_abroad"
                                                                        name="parent_is_abroad"
                                                                        value="{{ $archivedData->parent_is_abroad }}">
                                                                @else
                                                                    <!-- For new data: Allow selecting location -->
                                                                    <select class="form-control" id="parent_is_abroad"
                                                                        name="parent_is_abroad" required>
                                                                        <option value="">حدد الخيار</option>
                                                                        <option value="1"
                                                                            {{ old('parent_is_abroad') == '1' ? 'selected' : '' }}>
                                                                            خارج البلاد</option>
                                                                        <option value="0"
                                                                            {{ old('parent_is_abroad') == '0' ? 'selected' : '' }}>
                                                                            داخل البلاد</option>
                                                                    </select>
                                                                @endif
                                                            </div>

                                                            <!-- Abroad Country (only for non-newcomers and when abroad) -->
                                                            <div class="col-md-6 form-group @if (isset($archivedData) && $archivedData->parent_is_abroad == '1') d-block @else d-none @endif"
                                                                id="abroad_country_display">
                                                                <label for="abroad_country">البلد الذي يعيش فيه ولي
                                                                    الأمر</label>
                                                                @if (isset($archivedData) && $archivedData->parent_is_abroad == '1')
                                                                    <!-- Display country selection for non-newcomers (if abroad) -->
                                                                    <select class="form-control" id="abroad_country"
                                                                        name="abroad_country" required>
                                                                        <option value="">حدد الخيار</option>
                                                                        @foreach ($countries as $country)
                                                                            <option value="{{ $country->code }}"
                                                                                {{ old('abroad_country', $archivedData->parent_abroad_country) == $country->code ? 'selected' : '' }}>
                                                                                {{ $country->name_ar }}
                                                                                ({{ $country->name_en }})
                                                                            </option>
                                                                        @endforeach
                                                                    </select>
                                                                @endif
                                                            </div>

                                                            <div class="row">
                                                                <!-- Living With -->
                                                                <div class="col-md-6 form-group d-none"
                                                                    id="living_with_display">
                                                                    <label for="living_with">هل تعيش مع ولي الأمر؟ <span
                                                                            class="text-danger">*</span></label>
                                                                    <select class="form-control" id="living_with"
                                                                        name="parent_living_with" required>
                                                                        <option value="">حدد الخيار</option>
                                                                        <option value="1">نعم</option>
                                                                        <option value="0">لا</option>
                                                                    </select>
                                                                </div>

                                                                <!-- Parent Governorate -->
                                                                <div class="col-md-6 form-group d-none"
                                                                    id="parent_governorate_display">
                                                                    <label for="parent_governorate">محافظة ولي الأمر <span
                                                                            class="text-danger">*</span></label>
                                                                    <select class="form-control" id="parent_governorate"
                                                                        name="parent_governorate" required>
                                                                        <!-- Options for parent governorate will be populated here -->
                                                                    </select>
                                                                </div>

                                                                <!-- Parent City -->
                                                                <div class="col-md-6 form-group d-none"
                                                                    id="parent_city_display">
                                                                    <label for="parent_city">مدينة ولي الأمر <span
                                                                            class="text-danger">*</span></label>
                                                                    <select class="form-control" id="parent_city"
                                                                        name="parent_city" required>
                                                                        <!-- Options for parent city will be populated here -->
                                                                    </select>
                                                                </div>
                                                            </div>

                                                        </div>

                                                    </div>
                                                </div>
                                            </div>



                                                        <!-- Step 4: Emergency Contact Information -->
                                                        <div class="step hidden" id="step4">
                                                            <div class="step-title">الخطوة 4: معلومات الاتصال في حالات
                                                                الطوارئ</div>
                                                            <div class="container">
                                                                <div class="row">
                                                                    <div class="col-md-6 form-group">
                                                                        <label for="emergency_relation">العلاقة بجهة
                                                                            الطوارئ <span
                                                                                class="text-danger">*</span></label>
                                                                        <select class="form-control"
                                                                            id="emergency_relation"
                                                                            name="emergency_relation" required>
                                                                            <option value="">حدد الخيار</option>
                                                                            <option value="spouse">زوج/زوجة</option>
                                                                            <option value="grandparent">جد/جدة</option>
                                                                            <option value="uncle">عم/خال</option>
                                                                            <option value="aunt">عمة/خالة</option>
                                                                            <option value="cousin">ابن/بنت عم</option>
                                                                            <option value="nephew">ابن أخ/ابنة أخ
                                                                            </option>
                                                                            <option value="niece">ابنة أخ/ابن أخت
                                                                            </option>
                                                                            <option value="other">آخر</option>
                                                                        </select>
                                                                    </div>
                                                                    <div class="col-md-6 form-group">
                                                                        <label for="emergency_name">الاسم الكامل لجهة
                                                                            الطوارئ <span
                                                                                class="text-danger">*</span></label>
                                                                        <input type="text" class="form-control"
                                                                            id="emergency_name" name="emergency_name"
                                                                            required>
                                                                    </div>
                                                                </div>
                                                                <div class="row">
                                                                    <div class="col-md-6 form-group">
                                                                        <label for="emergency_phone">رقم هاتف جهة
                                                                            الطوارئ <span
                                                                                class="text-danger">*</span></label>
                                                                        <input type="tel" class="form-control"
                                                                            id="emergency_phone"
                                                                            name="emergency_phone" required>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <!-- Step 5: College Information -->
                                                        <div class="step">
                                                            <div class="step-title">الخطوة 5: معلومات الكلية</div>
                                                            <div class="container">
                                                                <div class="row">
                                                                    <!-- College Details -->
                                                                    <div class="col-md-6 form-group">
                                                                        <label for="faculty">الكلية <span
                                                                                class="text-danger">*</span></label>
                                                                        <input type="text" class="form-control"
                                                                            id="faculty" name="faculty"
                                                                            value="{{ old('faculty', $archivedData ? $archivedData->faculty : '') }}"
                                                                            readonly>
                                                                    </div>
                                                                    <!-- For newcomers, show score, percentage, and certificate details -->
                                                                    <div class="col-md-6 form-group">
                                                                        <label for="score">المعدل التراكمي <span
                                                                                class="text-danger">*</span></label>
                                                                        @if (isset($archivedData) && $archivedData->score !== null)
                                                                            <!-- Display score as readonly if it exists -->
                                                                            <input type="text" class="form-control"
                                                                                id="score" name="score"
                                                                                value="{{ old('score', $archivedData->score) }}"
                                                                                readonly>
                                                                        @else
                                                                            <!-- Allow user to enter the score if it doesn't exist -->
                                                                            <input type="text" class="form-control"
                                                                                id="score" name="score"
                                                                                value="{{ old('score') }}"
                                                                                placeholder="أدخل المعدل التراكمي هنا"
                                                                                required>
                                                                        @endif
                                                                    </div>

                                                                    @if ($archivedData && $archivedData->is_new_comer == 1)

                                                                        <div class="col-md-6 form-group">
                                                                            <label for="percentage">النسبة المئوية
                                                                                <span
                                                                                    class="text-danger">*</span></label>
                                                                            <input type="text" class="form-control"
                                                                                id="percentage" name="percentage"
                                                                                value="{{ old('percentage', $archivedData ? $archivedData->percent : '') }}"
                                                                                readonly>
                                                                        </div>

                                                                        <!-- Certificate Details for Newcomers -->
                                                                        <div class="col-md-6 form-group">
                                                                            <label for="cert_type">نوع الشهادة <span
                                                                                    class="text-danger">*</span></label>
                                                                            <input type="text" class="form-control"
                                                                                id="cert_type" name="cert_type"
                                                                                value="{{ old('cert_type', $archivedData ? $archivedData->cert_type : '') }}"
                                                                                readonly>
                                                                        </div>
                                                                        <div class="col-md-6 form-group">
                                                                            <label for="cert_country">بلد الشهادة <span
                                                                                    class="text-danger">*</span></label>
                                                                            <input type="text" class="form-control"
                                                                                id="cert_country" name="cert_country"
                                                                                value="{{ old('cert_country', $archivedData ? $archivedData->cert_country : '') }}"
                                                                                readonly>
                                                                        </div>
                                                                    @else
                                                                        <!-- For non-newcomers, show program and academic level -->
                                                                        <div class="col-md-6 form-group">
                                                                            <label for="program">البرنامج الدراسي
                                                                                <span
                                                                                    class="text-danger">*</span></label>
                                                                            <select class="form-select" id="program"
                                                                                name="program" required>
                                                                                <option value="">حدد البرنامج
                                                                                    الدراسي</option>

                                                                                @foreach ($programs as $program)
                                                                                    <option
                                                                                        value="{{ $program->name_en }}"
                                                                                        {{ old('program', $archivedData->program ?? '') == $program->name_en ? 'selected' : '' }}>
                                                                                        {{ $program->name_ar }}
                                                                                    </option>
                                                                                @endforeach
                                                                            </select>
                                                                        </div>

                                                                        <div class="col-md-6 form-group">
                                                                            <label for="academic_level">المستوى
                                                                                الأكاديمي <span
                                                                                    class="text-danger">*</span></label>
                                                                            <select class="form-select"
                                                                                id="academic_level"
                                                                                name="academic_level" required>
                                                                                <option value="">حدد المستوى
                                                                                    الأكاديمي</option>
                                                                                <option value="1"
                                                                                    {{ old('academic_level') == '1' ? 'selected' : '' }}>
                                                                                    المستوى الأول</option>
                                                                                <option value="2"
                                                                                    {{ old('academic_level') == '2' ? 'selected' : '' }}>
                                                                                    المستوى الثاني</option>
                                                                                <option value="3"
                                                                                    {{ old('academic_level') == '3' ? 'selected' : '' }}>
                                                                                    المستوى الثالث</option>
                                                                                <option value="4"
                                                                                    {{ old('academic_level') == '4' ? 'selected' : '' }}>
                                                                                    المستوى الرابع</option>
                                                                                <option value="5"
                                                                                    {{ old('academic_level') == '5' ? 'selected' : '' }}>
                                                                                    المستوى الخامس</option>
                                                                            </select>
                                                                        </div>
                                                                    @endif
                                                                </div>

                                                                <div class="row">
                                                                    <!-- Conditionally remove university_id and academic_email for newcomers -->
                                                                    <!-- For non-newcomers, show university_id and academic_email -->
                                                                    <div class="col-md-6 form-group">
                                                                        <label for="university_id">رقم الاكاديمي <span
                                                                                class="text-danger">*</span></label>
                                                                        <input type="text" class="form-control"
                                                                            id="university_id" name="university_id"
                                                                            value="{{ old('university_id', $archivedData ? $archivedData->university_id : '') }}"
                                                                            readonly>
                                                                    </div>
                                                                    <div class="col-md-6 form-group">
                                                                        <label for="academic_email">البريد الإلكتروني
                                                                            الأكاديمي <span
                                                                                class="text-danger">*</span></label>
                                                                        <input type="email" class="form-control"
                                                                            id="academic_email" name="academic_email"
                                                                            value="{{ old('academic_email', $archivedData ? $archivedData->academic_email : '') }}"
                                                                            readonly>
                                                                    </div>

                                                                </div>
                                                            </div>
                                                        </div>


                                                        <!-- Step 6: Sibling Information -->
                                                        <div class="step">
                                                            <div class="step-title">الخطوة 6: معلومات الإخوة</div>
                                                            <div class="container">
                                                                <div class="row">
                                                                    <!-- Sibling presence question -->
                                                                    <div class="col-md-6 form-group">
                                                                        <label for="has_sibling">هل لديك أي إخوة في
                                                                            السكن؟ <span
                                                                                class="text-danger">*</span></label>

                                                                        <!-- Check if archivedData exists and use that value if available -->
                                                                        @if ($archivedData && $archivedData->has_sibling !== null)
    <!-- Display value from archived data -->
    <input type="text" class="form-control"
           id="has_sibling_display"
           value="{{ $archivedData->has_sibling == 1 ? 'نعم' : 'لا' }}"
           readonly>
    <input type="hidden" id="has_sibling" value="{{ $archivedData->has_sibling }}">
@else
    <!-- Display selection options if no archived data -->
    <select class="form-control" id="has_sibling" name="has_sibling" required>
        <option value="">حدد الخيار</option>
        <option value="1" {{ old('has_sibling') == '1' ? 'selected' : '' }}>نعم</option>
        <option value="0" {{ old('has_sibling') == '0' ? 'selected' : '' }}>لا</option>
    </select>
@endif

                                                                    </div>
                                                                </div>

                                                                @if ($archivedData && $archivedData->has_sibling == 1)
                                                                    <!-- For newcomers, show sibling-related fields -->
                                                                    <div class="row sibling-info">
                                                                        <div class="col-md-6 form-group">
                                                                            <label for="sibling_name">اسم الأخ/الأخت
                                                                                <span
                                                                                    class="text-danger">*</span></label>
                                                                            <input type="text" class="form-control"
                                                                                id="sibling_name" name="sibling_name"
                                                                                value="{{ old('sibling_name', $archivedData ? $archivedData->brother_name : '') }}"
                                                                                required>
                                                                        </div>
                                                                        <div class="col-md-6 form-group">
                                                                            <label for="sibling_gender">جنس الأخ/الأخت
                                                                                <span
                                                                                    class="text-danger">*</span></label>
                                                                            <select class="form-control"
                                                                                id="sibling_gender"
                                                                                name="sibling_gender" required>
                                                                                <option value="">حدد الخيار
                                                                                </option>
                                                                                <option value="brother"
                                                                                    {{ old('sibling_gender') == 'brother' ? 'selected' : '' }}>
                                                                                    أخ</option>
                                                                                <option value="sister"
                                                                                    {{ old('sibling_gender') == 'sister' ? 'selected' : '' }}>
                                                                                    أخت</option>
                                                                            </select>
                                                                        </div>
                                                                        <div class="col-md-6 form-group">
                                                                            <label for="sibling_faculty">كلية
                                                                                الأخ/الأخت <span
                                                                                    class="text-danger">*</span></label>
                                                                            @if (isset($archivedData) && $archivedData->sibling_faculty)
                                                                                <input type="text"
                                                                                    class="form-control"
                                                                                    id="sibling_faculty"
                                                                                    name="sibling_faculty"
                                                                                    value="{{ old('sibling_faculty', $archivedData->sibling_faculty) }}"
                                                                                    readonly>
                                                                            @else
                                                                                <select class="form-control"
                                                                                    id="sibling_faculty"
                                                                                    name="sibling_faculty" required>
                                                                                    <option value="">حدد الكلية
                                                                                    </option>
                                                                                    @foreach ($faculties as $faculty)
                                                                                        <option
                                                                                            value="{{ $faculty->id }}"
                                                                                            {{ old('sibling_faculty') == $faculty->id ? 'selected' : '' }}>
                                                                                            {{ $faculty->name_ar }}
                                                                                            <!-- or use name_en if you prefer -->
                                                                                        </option>
                                                                                    @endforeach
                                                                                </select>
                                                                            @endif
                                                                        </div>
                                                                    </div>

                                                                    <div class="row sibling-info">
                                                                        <div class="col-md-6 form-group">
                                                                            <label for="sibling_national_id">رقم الهوية
                                                                                الوطنية للأخ/الأخت</label>
                                                                            <input type="text" class="form-control"
                                                                                id="sibling_national_id"
                                                                                name="sibling_national_id"
                                                                                value="{{ old('sibling_national_id', $archivedData ? $archivedData->sibling_national_id : '') }}">
                                                                        </div>
                                                                        <div class="col-md-6 form-group">
                                                                            <label for="sibling_share_room">هل يشترك
                                                                                الأخ/الأخت في الغرفة؟</label>
                                                                            <select class="form-control"
                                                                                id="sibling_share_room"
                                                                                name="sibling_share_room">
                                                                                <option value="" disabled>حدد
                                                                                    الخيار</option>
                                                                                <option value="1"
                                                                                    {{ old('sibling_share_room', $archivedData ? $archivedData->sibling_share_room : '') == '1' ? 'selected' : '' }}>
                                                                                    نعم</option>
                                                                                <option value="0"
                                                                                    {{ old('sibling_share_room', $archivedData ? $archivedData->sibling_share_room : '') == '0' ? 'selected' : '' }}>
                                                                                    لا</option>
                                                                            </select>
                                                                        </div>
                                                                    </div>
                                                                @elseif (!$archivedData)
                                                                    <!-- If there's no archived data, show empty form for the user to fill out -->
                                                                    <div class="row sibling-info">
                                                                        <div class="col-md-6 form-group">
                                                                            <label for="sibling_name">اسم الأخ/الأخت
                                                                                <span
                                                                                    class="text-danger">*</span></label>
                                                                            <input type="text" class="form-control"
                                                                                id="sibling_name" name="sibling_name"
                                                                                value="{{ old('sibling_name') }}"
                                                                                required>
                                                                        </div>
                                                                        <div class="col-md-6 form-group">
                                                                            <label for="sibling_gender">جنس الأخ/الأخت
                                                                                <span
                                                                                    class="text-danger">*</span></label>
                                                                            <select class="form-control"
                                                                                id="sibling_gender"
                                                                                name="sibling_gender" required>
                                                                                <option value="">حدد الخيار
                                                                                </option>
                                                                                <option value="brother"
                                                                                    {{ old('sibling_gender') == 'brother' ? 'selected' : '' }}>
                                                                                    أخ</option>
                                                                                <option value="sister"
                                                                                    {{ old('sibling_gender') == 'sister' ? 'selected' : '' }}>
                                                                                    أخت</option>
                                                                            </select>
                                                                        </div>
                                                                        <div class="col-md-6 form-group">
                                                                            <label for="sibling_faculty">كلية
                                                                                الأخ/الأخت <span
                                                                                    class="text-danger">*</span></label>
                                                                            <select class="form-control"
                                                                                id="sibling_faculty"
                                                                                name="sibling_faculty" required>
                                                                                <option value="" disabled
                                                                                    selected>اختر الكلية</option>
                                                                                @foreach ($faculties as $faculty)
                                                                                    <option
                                                                                        value="{{ $faculty->id }}"
                                                                                        {{ old('sibling_faculty') == $faculty->id ? 'selected' : '' }}>
                                                                                        {{ $faculty->name_ar }}
                                                                                        <!-- or use name_en if you prefer -->
                                                                                    </option>
                                                                                @endforeach
                                                                            </select>
                                                                        </div>
                                                                    </div>
                                                                @endif
                                                            </div>
                                                        </div>










                                                        <!-- Hidden input to determine if the user is a newcomer -->


                                                        <div class="d-flex justify-content-between">
                                                            <button type="button" id="prevBtn"
                                                                class="btn btn-secondary">السابق</button>
                                                            <div>
                                                                <button type="button" id="nextBtn"
                                                                    class="btn btn-primary">التالي</button>
                                                                <!-- Submit button -->
                                                                <button type="submit" id="submitBtn"
                                                                    class="btn btn-primary" style="display: none;">
                                                                    <span id="submitBtnText">إرسال</span>
                                                                    <span id="submitLoader"
                                                                        class="spinner-border spinner-border-sm"
                                                                        role="status" aria-hidden="true"
                                                                        style="display: none;"></span>
                                                                </button>
                                                            </div>
                                                        </div>

                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- End Contentbar -->
        </div>
        <!-- End Rightbar -->
    </div>
    <!-- jQuery and Validation Plugin -->
    <script src="{{ asset('js/jquery.min.js') }}"></script>
    <script sfrc="{{ asset('js/popper.min.js') }}"></script>
    <script src="{{ asset('js/bootstrap.bundle.js') }}"></script>
    <script src="{{ asset('js/modernizr.min.js') }}"></script>
    <script src="{{ asset('js/detect.js') }}"></script>
    <script src="{{ asset('js/jquery.slimscroll.js') }}"></script>
    <script src="{{ asset('js/vertical-menu.js') }}"></script>
    <!-- Core js -->
    <script src="{{ asset('js/core.js') }}"></script>
    <!-- <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script> -->
    <script src="https://cdn.jsdelivr.net/npm/jquery-validation@1.19.5/dist/jquery.validate.min.js"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const livingWith = document.getElementById('living_with');
            const parentGovernorateDisplay = document.getElementById('parent_governorate_display');
            const parentCityDisplay = document.getElementById('parent_city_display');
            const parentGovernorate = document.getElementById('parent_governorate');
            const parentCity = document.getElementById('parent_city');

            // Handle the 'Living With' selection
            if (livingWith) {
                livingWith.addEventListener('change', function() {
                    // Show or hide governorate and city based on "Living With" selection
                    if (livingWith.value == '0') { // "لا" - Not living with the parent
                        parentGovernorateDisplay.classList.remove('d-none');
                    } else {
                        parentGovernorateDisplay.classList.add('d-none');
                        parentCityDisplay.classList.add('d-none');
                    }
                });

                // Initial state based on existing selection
                if (livingWith.value == '0') {
                    parentGovernorateDisplay.classList.remove('d-none');
                }
            }

            // Fetch and populate cities when a governorate is selected
            if (parentGovernorate) {
                parentGovernorate.addEventListener('change', function() {
                    const governorateId = parentGovernorate.value;

                    // Clear the city dropdown before populating new values
                    parentCity.innerHTML = '<option value="">حدد المدينة</option>';

                    if (governorateId) {
                        // Make an AJAX call to fetch cities based on governorate ID
                        fetch("{{ route('get-cities', ['governorateId' => '__governorateId__']) }}"
                                .replace('__governorateId__', governorateId))
                            .then(response => response.json())
                            .then(response => response.json())
                            .then(data => {
                                data.cities.forEach(function(city) {
                                    const option = document.createElement("option");
                                    option.value = city.id;
                                    option.textContent = city.name_ar + ' (' + city.name_en +
                                        ')';
                                    parentCity.appendChild(option);
                                });

                                // Show the city dropdown after governorate selection
                                parentCityDisplay.classList.remove('d-none');
                            });
                    } else {
                        // Hide city dropdown if no governorate selected
                        parentCityDisplay.classList.add('d-none');
                    }
                });
            }
        });
    </script>


    <script>
        // Script to toggle the display of fields based on parent_is_abroad selection
        document.addEventListener("DOMContentLoaded", function() {
            const parentIsAbroad = document.getElementById('parent_is_abroad');
            const abroadCountryDisplay = document.getElementById('abroad_country_display');

            if (parentIsAbroad) {
                parentIsAbroad.addEventListener('change', function() {
                    if (parentIsAbroad.value == '1') {
                        abroadCountryDisplay.classList.remove('d-none');
                    } else {
                        abroadCountryDisplay.classList.add('d-none');
                    }
                });

                // Initially trigger to show/hide abroad country field
                if (parentIsAbroad.value == '1') {
                    abroadCountryDisplay.classList.remove('d-none');
                } else {
                    abroadCountryDisplay.classList.add('d-none');
                }
            }
        });
    </script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const governorateSelect = document.getElementById('governorate');
            const citySelect = document.getElementById('city');

            // Function to populate city options based on selected governorate
            function populateCities(governorateId) {
                // Clear existing options
                citySelect.innerHTML = '<option value="" disabled selected>اختر المدينة</option>';

                // If no governorate is selected, disable city dropdown
                if (!governorateId) {
                    citySelect.disabled = true;
                    return;
                }

                // Enable city dropdown and fetch cities for the selected governorate
                citySelect.disabled = false;

                // Fetch cities based on selected governorate using AJAX
                fetch("{{ route('get-cities', ['governorateId' => '__governorateId__']) }}".replace(
                        '__governorateId__', governorateId))
                    .then(response => response.json())
                    .then(data => {
                        // Populate the city dropdown with options
                        data.forEach(city => {
                            const option = document.createElement('option');
                            option.value = city.id;
                            option.textContent = city.name_ar;
                            citySelect.appendChild(option);
                        });

                        // Optionally select the city if data exists in old or archived data
                        const selectedCityId = "{{ old('city', $archivedData->city_id ?? '') }}";
                        if (selectedCityId) {
                            citySelect.value = selectedCityId;
                        }
                    });
            }

            // Check if there’s an existing selected governorate on page load
            const selectedGovernorate = governorateSelect.value;
            if (selectedGovernorate) {
                populateCities(selectedGovernorate);
            }

            // Add event listener to fetch cities when governorate is changed
            governorateSelect.addEventListener('change', function() {
                populateCities(this.value);
            });
        });
    </script>

    <script>
        $(document).ready(function() {
            // Initial setup
            var currentStep = 0;
            var steps = $(".step");
            var progressBar = $("#progressBar");
            var totalSteps = steps.length;
            const loader = document.querySelector('.loader2');
            const loaderBackground = document.querySelector('.full-page-container')

            async function initialize() {
                try {
                    // Show loader
                    loader.style.display = 'block';

                    // Wait for data to be fetched
                    // await fetchGovernorates();
                    // await fetchCountries();

                    // Hide loader once data is fetched successfully
                    loader.style.display = 'none';
                    loaderBackground.style.display = 'none';
                } catch (error) {
                    console.error('Failed to fetch data:', error);
                    loader.style.display = 'none';
                    loaderBackground.style.display = 'none';

                    window.location.reload();

                }
            }

            // Start the initialization process
            initialize();

            // Show a specific step in the multi-step form
            function showStep(stepIndex) {
                // Ensure stepIndex is within bounds

                if (stepIndex >= 0 && stepIndex < totalSteps) {
                    steps.removeClass("active");
                    steps.eq(stepIndex).addClass("active");
                    $("#prevBtn").toggle(stepIndex > 0);
                    $("#nextBtn").toggle(stepIndex < totalSteps - 1);
                    $("#submitBtn").toggle(stepIndex === totalSteps - 1);
                    updateProgressBar(stepIndex);
                }
            }

            // Update the progress bar based on the current step
            function updateProgressBar(stepIndex) {
                var percent = ((stepIndex + 1) / totalSteps) * 100;
                progressBar.css("width", percent + "%").text(Math.round(percent) + "%");
                progressBar.attr("aria-valuenow", percent);
            }

            // Function to determine the next step to show
            function getNextStep(stepIndex) {
                var nextStep = stepIndex + 1;
                // Skip hidden steps
                while (nextStep < totalSteps && steps.eq(nextStep).hasClass("hidden")) {
                    nextStep++;
                }
                return nextStep;
            }

            // Function to determine the previous step to show
            function getPreviousStep(stepIndex) {
                var prevStep = stepIndex - 1;
                // Skip hidden steps
                while (prevStep >= 0 && steps.eq(prevStep).hasClass("hidden")) {
                    prevStep--;
                }
                return prevStep;
            }

            // Navigation button handlers
            $("#nextBtn").click(function() {
                if ($("#multiStepForm").valid()) {
                    var nextStepIndex = getNextStep(currentStep);
                    if (nextStepIndex < totalSteps) {
                        currentStep = nextStepIndex;
                        showStep(currentStep);
                    }
                }
            });

            $("#prevBtn").click(function() {
                var prevStepIndex = getPreviousStep(currentStep);
                if (prevStepIndex >= 0) {
                    currentStep = prevStepIndex;
                    showStep(currentStep);
                }
            });

            // Update the total steps count and adjust navigation based on parent location
            // Define the function to update steps based on parent location
            function updateStepsBasedOnParentLocation() {
                var parentLocation = $("#parent_is_abroad").val();

                if (parentLocation === "1") {
                    // Include all steps
                    totalSteps = steps.length;
                    $("#step4").removeClass("hidden"); // Show step 4
                } else {
                    // Exclude step 4 (emergency contact)
                    $("#step4").addClass("hidden"); // Hide step 4
                }

                // Adjust the view to show the current step
                showStep(currentStep);
            }

            // Bind the function to the change event of #parent_is_abroad
            $("#parent_is_abroad").change(updateStepsBasedOnParentLocation);


            updateStepsBasedOnParentLocation();

            function updateStep7BasedOnNewcomer() {

                var isNewcomer = $("#is_new_comer").val();

                if (isNewcomer === "1") {
                    // Skip step 7 for newcomers
                    totalSteps--;

                    $("#step7").addClass("hidden"); // Hide step 7
                } else {
                    // Show step 7 for non-newcomers
                    $("#step7").removeClass("hidden"); // Show step 7
                }


            }

            // Bind the function to the change event of #is_new_comer
            $("#is_new_comer").change(updateStep7BasedOnNewcomer);

            // Call the function initially to set the correct view based on the current value
            updateStep7BasedOnNewcomer();


            // Function to update the display based on the parent_is_abroad value
            function updateDisplayBasedOnLocation() {
                var value = document.getElementById('parent_is_abroad').value;
                console.log("hereeee", value, typeof(value));
                if (value === "1") {
                    document.getElementById('abroad_country_display').classList.remove('d-none');
                    document.getElementById('living_with_display').classList.add('d-none');
                    document.getElementById('parent_governorate_display').classList.add('d-none');
                    document.getElementById('parent_city_display').classList.add('d-none');
                } else if (value === "0") {
                    document.getElementById('abroad_country_display').classList.add('d-none');
                    document.getElementById('living_with_display').classList.remove('d-none');
                    document.getElementById('parent_governorate_display').classList.remove('d-none');
                    document.getElementById('parent_city_display').classList.remove('d-none');
                }
            }

            // Attach event listener to the dropdown
            document.getElementById('parent_is_abroad').addEventListener('change', function() {
                updateDisplayBasedOnLocation();
            });

            // Initialize display based on the current value of the dropdown on page load
            updateDisplayBasedOnLocation();

            document.getElementById('living_with').addEventListener('change', function() {
                var val = this.value; // Corrected from this.val to this.value
                if (val === '0') {
                    document.getElementById('parent_governorate_display').classList.remove('d-none');
                    document.getElementById('parent_city_display').classList.remove('d-none');
                } else {
                    document.getElementById('parent_governorate_display').classList.add('d-none');
                    document.getElementById('parent_city_display').classList.add('d-none');
                }
            });

            // Function to show or hide sibling information
            function toggleSiblingInfo() {
                var hasSiblingSelect = document.getElementById('has_sibling');
                var siblingInfoSections = document.querySelectorAll('.sibling-info');

                if (hasSiblingSelect.value == '1') {
                    siblingInfoSections.forEach(function(section) {
                        section.style.display = 'flex'; // Show sibling info sections
                    });
                } else {
                    siblingInfoSections.forEach(function(section) {
                        section.style.display = 'none'; // Hide sibling info sections
                    });
                }
            }

            // Add event listener to the select element
            var hasSiblingSelect = document.getElementById('has_sibling');
            hasSiblingSelect.addEventListener('change', toggleSiblingInfo);
            toggleSiblingInfo();
          

            // Attach the function to the change event of the select element



            async function fetchCountries() {
                try {
                    // Fetch from the API route
                    const response = await fetch('/housing/public/api/countries');
                    if (!response.ok) {
                        throw new Error('Network response was not ok');
                    }
                    const data = await response.json();
                    const countries = data.countries; // Adjust based on your JSON structure
                    populateCountries(countries);
                } catch (error) {
                    console.error('Error fetching countries:', error);
                }
            }

            function populateCountries(countries) {
                // Map the countries data to use only Arabic names
                const countryOptions = countries.map(country => ({
                    name: country.nameAr, // Use Arabic names only
                    value: country.code
                }));


                // Populate the select elements with the Arabic country options
                populateSelect('abroad_country', countryOptions, 'name', 'value', "اختر بلد");
            }

            // Helper function to populate a select element
            function populateSelect(selectId, options, textKey, valueKey, placeholder) {
                const $select = $(`#${selectId}`);
                $select.empty().append(`<option value="">${placeholder}</option>`);
                options.forEach(option => {
                    $select.append(`<option value="${option[valueKey]}">${option[textKey]}</option>`);
                });
            }



            // Custom validation methods
            $.validator.addMethod("isEnglish", function(value, element) {
                return this.optional(element) || /^[a-zA-Z\s]+$/.test(value);
            }, "يرجى إدخال اسم باللغة الإنجليزية فقط.");

            $.validator.addMethod("isArabic", function(value, element) {
                return this.optional(element) || /^[\u0600-\u06FF\s]+$/.test(value);
            }, "يرجى إدخال اسم باللغة العربية فقط.");

            $.validator.addMethod("minThreeParts", function(value, element) {
                return this.optional(element) || value.split(/\s+/).length >= 3;
            }, "يرجى إدخال اسم يحتوي على ثلاثة أجزاء على الأقل.");

            $.validator.addMethod('validate_national_id', function(value, element) {
                return /^[0-9]{14}$/.test(value);
            }, 'يرجى إدخال رقم قومي صحيح');

            // Validator for Egyptian phone numbers
            $.validator.addMethod('validate_egyptian_phone', function(value, element) {
                return /^01[0125][0-9]{8}$/.test(value);
            }, 'يرجى إدخال رقم هاتف مصري صحيح');

            // Validator for email addresses, matching common email patterns
            $.validator.addMethod('validate_email', function(value, element) {
                return /^[A-Z0-9._%+-]+@[A-Z0-9.-]+\.[A-Z]{2,}$/i.test(value);
            }, 'يرجى إدخال بريد إلكتروني صحيح');

            // Form validation rules and messages
            $("#multiStepForm").validate({
                rules: {

                    national_id: {
                        required: true,
                        validate_national_id: true
                    },

                    governorate: {
                        required: true
                    },
                    gender: {
                        required: true
                    },
                    date_of_birth: {
                        required: true
                    },
                    city: {
                        required: true
                    },
                    street: {
                        required: true
                    },
                    phone: {
                        required: true,
                        validate_egyptian_phone: true
                    },
                    parent_relation: {
                        required: true
                    },
                    parent_name: {
                        required: true,
                        minThreeParts: true
                    },
                    parent_phone: {
                        required: true
                    },
                    parent_email: {
                        required: true,
                        validate_email: true
                    },
                    parent_is_abroad: {
                        required: true
                    },
                    abroad_country: {
                        required: function() {
                            return $("#parent_is_abroad").val() === "abroad";
                        }
                    },
                    living_with: {
                        required: true
                    },
                    parent_governorate: {
                        required: true
                    },
                    parent_city: {
                        required: true
                    },
                    emergency_relation: {
                        required: true
                    },
                    emergency_name: {
                        required: true,
                        minThreeParts: true

                    },
                    emergency_phone: {
                        required: true,
                        validate_egyptian_phone: true
                    },
                    faculty: {
                        required: true
                    },
                    program: {
                        required: true
                    },
                    academic_year: {
                        required: true
                    },
                    cert_type: {
                        required: true
                    },
                    cert_country: {
                        required: true
                    },
                    cert_score: {
                        required: true,
                        digits: true
                    },
                    gpa: {
                        required: true,
                        number: true
                    },
                    score: {
                        required: true,
                    },
                    university_id: {
                        required: true
                    },
                    has_sibling: {
                        required: true
                    },
                    sibling_name: {
                        required: function() {
                            return $("#has_sibling").val() === "1";
                        },
                        minThreeParts: true
                    },
                    sibling_gender: {
                        required: function() {
                            return $("#has_sibling").val() === "1";
                        }
                    },
                    sibling_faculty: {
                        required: function() {
                            return $("#has_sibling").val() === "1";
                        }
                    },
                    sibling_national_id: {
                        required: function() {
                            return $("#has_sibling").val() === "1";
                        }
                    },
                    sibling_share_room: {
                        required: function() {
                            return $("#has_sibling").val() === "1";
                        }
                    },
                    previous_resident: {
                        required: true
                    },
                    stay_in_old_room: {
                        required: function() {
                            return $("#previous_resident").val() === "1";
                        }
                    }
                },
                messages: {

                    national_id: {
                        required: "يرجى إدخال الرقم القومي.",
                        validate_national_id: "يرجى إدخال رقم قومي صحيح."
                    },

                    governorate: {
                        required: "يرجى اختيار المحافظة"
                    },
                    gender: {
                        required: "يرجى كتابة الرقم القومي بشكل صحيح"
                    },
                    date_of_birth: {
                        required: "يرجى كتابة الرقم القومي بشكل صحيح"
                    },
                    city: {
                        required: "يرجى إدخال المدينة"
                    },
                    street: {
                        required: "يرجى إدخال الشارع"
                    },
                    phone: {
                        required: "يرجى إدخال رقم الهاتف",
                        validate_egyptian_phone: "يرجى إدخال رقم هاتف مصري صحيح"
                    },

                    parent_relation: {
                        required: "يرجى تحديد العلاقة بولي الأمر"
                    },
                    parent_name: {
                        required: "يرجى إدخال الاسم الكامل لولي الأمر",
                        minThreeParts: "يجب أن يحتوي الأسم على ثلاثة أجزاء على الأقل."

                    },
                    parent_phone: {
                        required: "يرجى إدخال رقم هاتف ولي الأمر",
                        validate_egyptian_phone: "يرجى إدخال رقم هاتف مصري صحيح"
                    },
                    parent_email: {
                        required: "يرجى إدخال البريد الإلكتروني لولي الأمر",
                        validate_email: "يرجى إدخال بريد إلكتروني صحيح"
                    },
                    parent_is_abroad: {
                        required: "يرجى تحديد موقع ولي الأمر"
                    },
                    abroad_country: {
                        required: "يرجى إدخال البلد الذي يعيش فيه ولي الأمر"
                    },
                    living_with: {
                        required: "يرجى تحديد إذا كنت تعيش مع ولي الأمر"
                    },
                    parent_governorate: {
                        required: "يرجى إدخال محافظة ولي الأمر"
                    },
                    parent_city: {
                        required: "يرجى إدخال مدينة ولي الأمر"
                    },
                    emergency_relation: {
                        required: "يرجى تحديد العلاقة بجهة الطوارئ"
                    },
                    emergency_name: {
                        required: "يرجى إدخال الاسم الكامل لجهة الطوارئ",
                        minThreeParts: "يجب أن يحتوي الأسم على ثلاثة أجزاء على الأقل."

                    },
                    emergency_phone: {
                        required: "يرجى إدخال رقم هاتف جهة الطوارئ",
                        validate_egyptian_phone: "يرجى إدخال رقم هاتف مصري صحيح"
                    },
                    faculty: {
                        required: "يرجى إدخال الكلية"
                    },
                    program: {
                        required: "يرجى إدخال البرنامج الدراسي"
                    },
                    academic_year: {
                        required: "يرجى إدخال السنة الدراسية"
                    },
                    cert_type: {
                        required: "يرجى تحديد نوع الشهادة"
                    },
                    cert_country: {
                        required: "يرجى تحديد بلد الشهادة"
                    },
                    cert_score: {
                        required: "يرجى إدخال درجات الثانوية العامة",
                        digits: "يرجى إدخال درجة صحيحة"
                    },
                    gpa: {
                        required: "يرجى إدخال معدل التقدير",
                        number: "يرجى إدخال معدل تراكمي صحيح"
                    },
                    score: {
                        required: "يرجى إدخال الدرجات",
                        digits: "يرجى إدخال درجة صحيحة"
                    },
                    university_id: {
                        required: "يرجى إدخال رقم الجامعة"
                    },
                    has_sibling: {
                        required: "يرجى تحديد إذا كان لديك أي إخوة في السكن"
                    },
                    sibling_name: {
                        required: "يرجى إدخال اسم الأخ/الأخت",
                        minThreeParts: "يجب أن يحتوي الأسم على ثلاثة أجزاء على الأقل."
                    },
                    sibling_gender: {
                        required: "يرجى اختيار جنس الأخ/الأخت"
                    },
                    sibling_faculty: {
                        required: "يرجى إدخال كلية الأخ/الأخت"
                    },
                    sibling_national_id: {
                        required: "يرجى إدخال رقم قومي للأخ/الأخت"
                    },
                    sibling_share_room: {
                        required: "يرجى تحديد إذا كنت تشارك الغرفة مع الأخ/الأخت"
                    },
                    previous_resident: {
                        required: "يرجى تحديد إذا كنت قد أقمت في السكن سابقاً"
                    },
                    stay_in_old_room: {
                        required: "يرجى تحديد إذا كنت ترغب في الإقامة في غرفتك القديمة"
                    }
                },
                errorPlacement: function(error, element) {
                    if (element.attr("name") === "gender" || element.attr("name") ===
                        "parent_relation" || element.attr("name") === "living_with" || element.attr(
                            "name") === "previous_resident") {
                        error.appendTo(element.closest(".form-group"));
                    } else {
                        error.insertAfter(element);
                    }
                }
            });

            // Initialize the form by showing the first step
            showStep(currentStep);

            // Event handler for national_id input field
            $('#national_id').on('input', function() {

                const national_id = $(this).val();


                // Manually validate the national_id field
                const form = $("#multiStepForm");
                form.validate();

                if (form.validate().element('#national_id')) {

                    populateGenderAndBirthDate(national_id);
                } else {

                    // Clear fields if the national ID is invalid
                    $('#date_of_birth').val('');
                    $('#gender').val('');
                }
            });

            // Populate fields based on the national ID
            function populateGenderAndBirthDate(nationalId) {
                const currentLang = 'ar'; // Modify this line based on your language detection method



                if (/^[0-9]{14}$/.test(nationalId)) {
                    const birthCenturyCode = nationalId.charAt(0);
                    const birthDate = nationalId.substring(1, 7);
                    const genderCode = nationalId.charAt(12);





                    // Parse date of birth
                    const yearPrefix = birthCenturyCode === '2' ? '19' : '20';
                    const year = `${yearPrefix}${birthDate.substring(0, 2)}`;
                    const month = birthDate.substring(2, 4);
                    const day = birthDate.substring(4, 6);
                    const birthDateFormatted = `${year}-${month}-${day}`;



                    // Determine gender based on current language
                    let gender;
                    if (currentLang === 'ar') {
                        gender = (genderCode % 2 === 0) ? 'أنثى' : 'ذكر'; // Arabic for Female and Male
                    } else {
                        gender = (genderCode % 2 === 0) ? 'Female' : 'Male';
                    }



                    // Populate fields
                    $('#date_of_birth').val(birthDateFormatted);
                    $('#gender').val(gender);
                } else {

                    // Clear fields if the national ID is not valid
                    $('#date_of_birth').val('');
                    $('#gender').val('');
                }
            }
            const nationalId = $('#national_id').val();
            populateGenderAndBirthDate(nationalId)

            // fetch governorates and cities 

            let governorates = {};
            let parentGovernorates = {};

            async function fetchGovernorates() {
                try {
                    const response = await fetch(
                    '/housing/public/api/governorates'); // Adjust the route as needed
                    if (!response.ok) {
                        throw new Error('Network response was not ok');
                    }
                    const data = await response.json();
                    // Debug: Check the fetched data

                    governorates = data.governorates; // Assign fetched data to the governorates variable
                    parentGovernorates = data
                    .governorates; // Assign fetched data to the parentGovernorates variable

                    // Debug: Check assigned data



                    // Populate dropdowns
                    populateGovernorates(governorates, 'governorate');
                    populateGovernorates(parentGovernorates, 'parent_governorate');

                    // Set up event listeners
                    setupEventListeners();
                } catch (error) {
                    console.error('Error fetching governorates:', error);
                }
            }

            function populateGovernorates(governorates, selectId) {
                const selectElement = document.getElementById(selectId);
                if (!selectElement) {
                    console.error(`Element with id "${selectId}" not found.`);
                    return;
                }

                selectElement.innerHTML =
                '<option value="" disabled selected>اختر المحافظة</option>'; // Reset options

                // Debug: Check the governorates being processed

                for (const [key, value] of Object.entries(governorates)) {
                    // Debug: Check each option being added
                    const option = document.createElement('option');
                    option.value = key; // Use the English name key for value
                    option.textContent = value.ar; // Display Arabic name
                    selectElement.appendChild(option);
                }
            }

            function populateCities(cities, selectId) {
                const selectElement = document.getElementById(selectId);
                if (!selectElement) {
                    console.error(`Element with id "${selectId}" not found.`);
                    return;
                }

                selectElement.innerHTML =
                '<option value="" disabled selected>اختر المدينة</option>'; // Reset options

                // Debug: Check the cities being processed

                for (const [key, value] of Object.entries(cities)) {
                    // Debug: Check each option being added
                    const option = document.createElement('option');
                    option.value = key; // Use the English name key for value
                    option.textContent = value.ar; // Display Arabic name
                    selectElement.appendChild(option);
                }
                selectElement.removeAttribute('disabled')
            }

            function setupEventListeners() {
                // Governorate dropdown
                const governorateSelect = document.getElementById('governorate');
                governorateSelect.addEventListener('change', (e) => {
                    const selectedGovernorateKey = e.target.value;
                    const selectedGovernorate = governorates[selectedGovernorateKey];
                    if (selectedGovernorate) {
                        document.getElementById('selectedGovernorateAr').value = selectedGovernorate
                        .ar; // Set hidden input value
                        populateCities(selectedGovernorate.cities || {}, 'city');
                    } else {
                        document.getElementById('selectedGovernorateAr').value =
                        ''; // Clear hidden input value
                        populateCities({}, 'city'); // Clear city options
                    }
                });

                // Parent Governorate dropdown
                const parentGovernorateSelect = document.getElementById('parent_governorate');
                parentGovernorateSelect.addEventListener('change', (e) => {
                    const selectedGovernorateKey = e.target.value;
                    const selectedGovernorate = parentGovernorates[selectedGovernorateKey];
                    if (selectedGovernorate) {
                        document.getElementById('parentSelectedGovernorateAr').value = selectedGovernorate
                            .ar; // Set hidden input value
                        populateCities(selectedGovernorate.cities || {}, 'parent_city');
                    } else {
                        document.getElementById('parentSelectedGovernorateAr').value =
                        ''; // Clear hidden input value
                        populateCities({}, 'parent_city'); // Clear city options
                    }
                });

                // City dropdowns
                const citySelect = document.getElementById('city');
                citySelect.addEventListener('change', (e) => {
                    const selectedCityKey = e.target.value;
                    const selectedCity = governorates[document.getElementById('governorate').value]?.cities[
                        selectedCityKey];
                    document.getElementById('selectedCityAr').value = selectedCity ? selectedCity.ar :
                    ''; // Set hidden input value
                });

                const parentCitySelect = document.getElementById('parent_city');
                parentCitySelect.addEventListener('change', (e) => {
                    const selectedCityKey = e.target.value;
                    const selectedCity = parentGovernorates[document.getElementById('parent_governorate')
                        .value]?.cities[selectedCityKey];
                    document.getElementById('parentSelectedCityAr').value = selectedCity ? selectedCity.ar :
                        ''; // Set hidden input value
                });
            }

            const submitBtn = document.getElementById('submitBtn');
            const submitBtnText = document.getElementById('submitBtnText');
            const submitLoader = document.getElementById('submitLoader');
            submitBtn.addEventListener('click', function() {
                // Show loader and hide button text when submitting
                submitBtnText.style.display = 'none';
                submitLoader.style.display = 'inline-block';
            });
        });
    </script>
</body>

</html>
