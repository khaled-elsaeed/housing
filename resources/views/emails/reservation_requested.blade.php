<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta content="width=device-width, initial-scale=1" name="viewport">
    <meta name="x-apple-disable-message-reformatting">
    <meta content="IE=edge" http-equiv="X-UA-Compatible">
    <meta content="telephone=no" name="format-detection">
    <title>طلب حجز السكن الجامعي - بوابة الإسكان الجامعي</title>
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
        .reservation-details {
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
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <img class="logo" src="https://es.nmu.edu.eg/housing/images/logo.png" alt="شعار بوابة الإسكان">
            <h1 style="margin:0;font-size:24px;">طلب حجز السكن الجامعي</h1>
        </div>
        
        <div class="content">
            <h2 style="color:#1e293b;margin-top:0;">مرحباً {{ $user->name }}،</h2>
            <p>شكراً لاختيارك السكن الجامعي بجامعة المنصورة الجديدة. لقد تم استلام طلب حجزك بنجاح وهو الآن قيد المراجعة من قبل ادارة السكن.</p>

            <div class="reservation-details">
                <h3 style="color:#8C2F39;margin-top:0;">تفاصيل الطلب</h3>
                <p>
                نوع الغرفة: {{ $reservationRequest->room->type }}<br>
                
                @if($reservationRequest->period_type == 'long')
                    المدة: فصل دراسي كامل 
                    @if($reservationRequest->academicTerm->semester == 'second')
                        (فصل الدراسي الثاني)
                    @else
                        (فصل الدراسي الأول)
                    @endif
                    <br>
                    تاريخ البداية: {{ $reservationRequest->academicTerm->start_date }}<br>
                @else
                    @php
                        $start_date = \Carbon\Carbon::parse($reservationRequest->start_date);
                        $end_date = \Carbon\Carbon::parse($reservationRequest->end_date);
                        $diffInDays = $start_date->diffInDays($end_date);
                        
                        if ($diffInDays >= 30) {
                            $duration = floor($diffInDays / 30) . ' شهر' . (($diffInDays / 30) > 1 ? ' أشهر' : '');
                        } elseif ($diffInDays >= 7) {
                            $duration = floor($diffInDays / 7) . ' أسبوع' . (($diffInDays / 7) > 1 ? ' أسابيع' : '');
                        } else {
                            $duration = $diffInDays . ' يوم' . ($diffInDays > 1 ? ' أيام' : '');
                        }
                    @endphp
                    المدة: {{ $duration }}<br>
                    تاريخ البداية: {{ $reservationRequest->start_date }}<br>
                    تاريخ النهاية: {{ $reservationRequest->end_date }}<br>
                @endif
                </p>
            </div>

            <div class="info-box">
                <strong>ملاحظة هامة:</strong>
                <p style="margin:5px 0 0;">يرجى الانتظار حتى يتم مراجعة طلبك من قبل الفريق المختص. سيتم إعلامك بالنتيجة في أقرب وقت ممكن.</p>
            </div>

            <p>في حال وجود أي استفسارات، يرجى عدم التردد في التواصل مع فريق الدعم.</p>
        </div>

        <div class="footer">
            <p>&copy; {{ date('Y') }} بوابة الإسكان الجامعي - جامعة المنصورة الجديدة. جميع الحقوق محفوظة.</p>
        </div>
    </div>
</body>
</html>