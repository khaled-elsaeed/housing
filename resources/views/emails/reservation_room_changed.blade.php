<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta content="width=device-width, initial-scale=1" name="viewport">
    <meta name="x-apple-disable-message-reformatting">
    <meta content="IE=edge" http-equiv="X-UA-Compatible">
    <meta content="telephone=no" name="format-detection">
    <title>تغيير تفاصيل الغرفة - بوابة الإسكان الجامعي</title>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Cairo', Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f8fafc;
            direction: rtl;
            line-height: 1.6;
        }
        .container {
            max-width: 600px;
            margin: 20px auto;
            background: #ffffff;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
        }
        .header {
            background-color: #8C2F39;
            background-image: linear-gradient(135deg, #8C2F39 0%, #a13742 100%);
            padding: 30px;
            text-align: center;
            color: #ffffff;
        }
        .logo {
            width: 90px;
            height: auto;
            margin-bottom: 15px;
        }
        .content {
            padding: 40px 30px;
        }
        .room-details, .reservation-details {
            background: #f8fafc;
            border-radius: 12px;
            padding: 20px;
            margin: 20px 0;
        }
        .info-box {
            border-right: 4px solid #8C2F39;
            padding: 15px;
            background: #fff5f5;
            border-radius: 8px;
            margin: 20px 0;
        }
        .footer {
            background: #f8fafc;
            padding: 20px;
            text-align: center;
            font-size: 14px;
            color: #64748b;
            border-top: 1px solid #e5e7eb;
        }
        @media only screen and (max-width: 600px) {
            .container { margin: 0; border-radius: 0; }
            .content { padding: 20px; }
        }
        .centered-image {
            display: block;
            margin: 0 auto;
            width: 70%;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <img class="logo" src="https://es.nmu.edu.eg/housing/images/logo.png" alt="شعار بوابة الإسكان">
            <h1 style="margin:0;font-size:24px;">تغيير تفاصيل الغرفة</h1>
        </div>
        
        <div class="content">

            <img src="https://es.nmu.edu.eg/housing/images/email/bedroom.svg" alt="house" title="house" class="centered-image">

            <h2 style="color:#1e293b;margin-top:0;">مرحباً {{ $user->first_name_ar .' '.$user->last_name_ar }}،</h2>
            <p>نود إعلامك بأنه تم تغيير تفاصيل الغرفة الخاصة بك في السكن الجامعي بجامعة المنصورة الجديدة. يرجى الاطلاع على التفاصيل الجديدة أدناه:</p>

            <!-- New Room Details -->
            <div class="room-details">
                <h3 style="color:#8C2F39;margin-top:0;">تفاصيل الغرفة الجديدة</h3>
                <p>
                    <strong>رقم الغرفة:</strong> {{ $room->number }}<br>
                    <strong>الشقة:</strong> {{ $room->apartment->number }}<br>
                    <strong>المبنى:</strong> {{ $room->apartment->building->number }}<br>
                    @php
                        $roomTypes = [
                            'single' => 'فردي',
                            'double' => 'مزدوج',
                            'shared' => 'مشترك',
                        ];

                        $semesterNames = [
                            'first' => 'الفصل الدراسي الأول',
                            'second' => 'الفصل الدراسي الثاني',
                            'fall' => 'الخريف',
                            'spring' => 'الربيع',
                        ];
                    @endphp

                    <strong>النوع:</strong>
                    {{ $roomTypes[$room->type] ?? $room->type }}
                    <br>
                </p>
            </div>

            <!-- Reservation Details -->
            <div class="reservation-details">
                <h3 style="color:#8C2F39;margin-top:0;">تفاصيل الحجز</h3>
                <p>
                    @if($room->reservation->period_type == 'long')
                    <strong>المدة:</strong> فصل دراسي كامل<br>
            <strong>الفترة الدراسية:</strong> 
            {{ $semesterNames[$room->reservation->academicTerm->semester] ?? $room->reservation->academicTerm->semester }}
            {{ $room->reservation->academicTerm->academic_year }}
            ({{ $semesterNames[$room->reservation->academicTerm->name] ?? $room->reservation->academicTerm->name }})<br>
                    @else
                        @php
                            $start_date = \Carbon\Carbon::parse($room->reservation->start_date);
                            $end_date = \Carbon\Carbon::parse($room->reservation->end_date);
                            $diffInDays = $start_date->diffInDays($end_date);
                            
                            if ($diffInDays >= 30) {
                                $duration = floor($diffInDays / 30) . ' شهر' . (($diffInDays / 30) > 1 ? ' أشهر' : '');
                            } elseif ($diffInDays >= 7) {
                                $duration = floor($diffInDays / 7) . ' أسبوع' . (($diffInDays / 7) > 1 ? ' أسابيع' : '');
                            } else {
                                $duration = $diffInDays . ' يوم' . ($diffInDays > 1 ? ' أيام' : '');
                            }
                        @endphp
                        <strong>المدة:</strong> {{ $duration }}<br>
                        <strong>تاريخ البداية:</strong> {{ $room->reservation->start_date }}<br>
                        <strong>تاريخ النهاية:</strong> {{ $room->reservation->end_date }}<br>
                    @endif
                </p>
            </div>

            <!-- Important Note -->
            <div class="info-box">
                <strong>ملاحظة هامة:</strong>
                <p style="margin:5px 0 0;">يرجى التأكد من تفاصيل الغرفة الجديدة والحجز. في حال وجود أي استفسارات أو مشاكل، يرجى التواصل مع فريق الدعم على الفور.</p>
            </div>

            <p>شكراً لتفهمك، ونتطلع لتوفير إقامة مريحة لك في السكن الجامعي.</p>
        </div>

        <div class="footer">
            <p>&copy; {{ date('Y') }} بوابة الإسكان الجامعي - جامعة المنصورة الجديدة. جميع الحقوق محفوظة.</p>
        </div>
    </div>
</body>
</html>