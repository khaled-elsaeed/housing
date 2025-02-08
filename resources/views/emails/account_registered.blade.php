<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta content="width=device-width, initial-scale=1" name="viewport">
    <meta name="x-apple-disable-message-reformatting">
    <meta content="IE=edge" http-equiv="X-UA-Compatible">
    <meta content="telephone=no" name="format-detection">
    <title>تأكيد التسجيل - بوابة الإسكان الجامعي</title>
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
        .services-box {
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
        .centered-image {
            display: block;
            margin: 0 auto;
            width: 70%;
        }
        @media only screen and (max-width: 600px) {
            .container { margin: 0; border-radius: 0; }
            .content { padding: 20px; }
            .button { width: 100%; box-sizing: border-box; }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <img class="logo" src="https://es.nmu.edu.eg/housing/images/logo.png" alt="شعار بوابة الإسكان">
            <h1 style="margin:0;font-size:24px;">مرحباً بك في نظام الإسكان الجامعي</h1>
        </div>
        
        <div class="content">
            <img src="https://es.nmu.edu.eg/housing/images/email/house_2.svg" alt="house" title="house" class="centered-image">

            <h2 style="color:#1e293b;margin-top:0;">مرحباً {{ $user->first_name_ar .' '.$user->last_name_ar }}،</h2>
            <p>نرحب بك في نظام الإسكان الجامعي لجامعة المنصورة الجديدة. تم تفعيل حسابك بنجاح ويمكنك الآن الاستفادة من جميع خدماتنا.</p>

            <div class="services-box">
                <h3 style="color:#8C2F39;margin-top:0;">الخدمات المتاحة</h3>
                <p>يمكنك الاستفادة من الخدمات التالية:</p>
                <ul style="padding-right: 20px;">
                    <li>حجز سكن لفترة طويلة (فصل دراسي كامل)</li>
                    <li>حجز سكن لفترة قصيرة (أسبوعي أو شهري)</li>
                    <li>إدارة الحجوزات وتتبع حالتها</li>
                    <li>رفع إيصالات الدفع وإدارة المدفوعات</li>
                    <li>تقديم طلبات الدعم والمساعدة</li>
                </ul>
            </div>

            <h3 style="color:#8C2F39;">كيفية استخدام النظام</h3>
            <div class="steps">
                <div class="step">
                    <span class="step-number">1</span>
                    <strong>تسجيل الدخول</strong>
                    <p>قم بتسجيل الدخول باستخدام بريدك الإلكتروني وكلمة المرور</p>
                </div>
                <div class="step">
                    <span class="step-number">2</span>
                    <strong>اختيار نوع الحجز</strong>
                    <p>اختر بين الحجز لفترة طويلة (فصل دراسي) أو فترة قصيرة</p>
                </div>
                <div class="step">
                    <span class="step-number">3</span>
                    <strong>إتمام الحجز والدفع</strong>
                    <p>اختر الغرفة المناسبة وقم بإتمام عملية الدفع</p>
                </div>
                <div class="step">
                    <span class="step-number">4</span>
                    <strong>إدارة حجزك</strong>
                    <p>تابع حالة حجزك وقم بإدارة جميع التفاصيل من لوحة التحكم</p>
                </div>
            </div>

            <div class="info-box">
                <strong>معلومات مهمة:</strong>
                <p style="margin:5px 0 0;">
                    - احتفظ ببيانات تسجيل الدخول الخاصة بك<br>
                    - يجب إتمام عملية الدفع خلال 48 ساعة من وقت الحجز
                </p>
            </div>

            <center>
                <a href="https://es.nmu.edu.eg/housing/" class="button">
                    الدخول إلى حسابك
                </a>
            </center>

            <p>إذا واجهتك أي مشكلة أو كان لديك استفسار، يرجى التواصل مع فريق الدعم الفني.</p>
        </div>

        <div class="footer">
            <p>&copy; {{ date('Y') }} بوابة الإسكان الجامعي - جامعة المنصورة الجديدة. جميع الحقوق محفوظة.</p>
        </div>
    </div>
</body>
</html>