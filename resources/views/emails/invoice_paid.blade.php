<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta content="width=device-width, initial-scale=1" name="viewport">
    <meta name="x-apple-disable-message-reformatting">
    <meta content="IE=edge" http-equiv="X-UA-Compatible">
    <meta content="telephone=no" name="format-detection">
    <title>تأكيد استلام الدفع - بوابة الإسكان الجامعي</title>
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
        .invoice->reservation-details {
            background: #f8fafc;
            border-radius: 12px;
            padding: 20px;
            margin: 20px 0;
        }
        .steps {
            margin: 30px 0;
            padding: 0;
        }
        .step {
            background: #fff;
            border: 1px solid #e5e7eb;
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 15px;
            position: relative;
        }
        .step-number {
            background: #8C2F39;
            color: white;
            width: 28px;
            height: 28px;
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            margin-left: 10px;
            font-weight: bold;
        }
        .button {
            display: inline-block;
            padding: 16px 32px;
            background: #8C2F39;
            color: #ffffff !important;
            text-decoration: none;
            border-radius: 8px;
            font-weight: 600;
            text-align: center;
            margin: 20px 0;
            transition: all 0.3s ease;
        }
        .button:hover {
            background: #752731;
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
            .button { width: 100%; box-sizing: border-box; }
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
            <h1 style="margin:0;font-size:24px;">تأكيد استلام الدفع</h1>
        </div>
        
        <div class="content">
            <img src="https://es.nmu.edu.eg/housing/images/email/invoice.svg" alt="Invoice" title="Invoice" class="centered-image">

            <h2 style="color:#1e293b;margin-top:0;">مرحباً {{ $user->first_name_ar .' '.$user->last_name_ar }}،</h2>
            @if($invoice->reservation->status == 'upcoming' || $invoice->reservation->status == 'pending')
             <p>تم استلام الدفع الخاص بحجزك في السكن الجامعي بجامعة المنصورة الجديدة بنجاح. يرجى الانتظار حتى يتم مراجعة الدفع من قبل إدارة السكن وتأكيد الحجز.</p>
            @elseif($invoice->reservation->status == 'completed')
            <p>تم استلام الدفع الخاص بحجزك في السكن الجامعي بجامعة المنصورة الجديدة بنجاح. يرجى الانتظار حتى يتم مراجعة الدفع من قبل إدارة السكن.</p>
            @endif
            <div class="invoice->reservation-details">
                <h3 style="color:#8C2F39;margin-top:0;">تفاصيل الحجز</h3>
                <p>
                    نوع الغرفة: {{ $invoice->reservation->room->type == 'single' ? 'مفردة' : 'مزدوجة' }}<br>
                
                    @if($invoice->reservation->period_type == 'long')
                        المدة: فصل دراسي كامل 
                        @if($invoice->reservation->academicTerm->semester == 'second')
                            (الفصل الدراسي الثاني)
                        @else
                            (الفصل الدراسي الأول)
                        @endif
                        <br>
                        @if($invoice->reservation->status == "planned")
                        تاريخ البداية: {{ $invoice->reservation->academicTerm->start_date }}<br>
                        @endif
                    @else
                        @php
                            $start_date = \Carbon\Carbon::parse($invoice->reservation->start_date);
                            $end_date = \Carbon\Carbon::parse($invoice->reservation->end_date);
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
                        تاريخ البداية: {{ $invoice->reservation->start_date }}<br>
                        تاريخ النهاية: {{ $invoice->reservation->end_date }}<br>
                    @endif
                
                    التكلفة الإجمالية: {{ $invoice->reservation->invoice->totalAmount() }} جنيه مصري
                </p>
            </div>

            <div class="info-box">
                <strong>ملاحظة هامة:</strong>
                <p style="margin:5px 0 0;">سيتم إعلامك عبر البريد الإلكتروني بمجرد مراجعة عملية الدفع من قبل إدارة السكن.</p>
            </div>

            <center>
                <a href="https://es.nmu.edu.eg/housing/" class="button">
                    الانتقال إلى بوابة السكن الجامعي
                </a>
            </center>

            <p>إذا كان لديك أي استفسارات، لا تتردد في التواصل مع فريق الدعم.</p>
        </div>

        <div class="footer">
            <p>&copy; {{ date('Y') }} بوابة الإسكان الجامعي - جامعة المنصورة الجديدة. جميع الحقوق محفوظة.</p>
        </div>
    </div>
</body>
</html>