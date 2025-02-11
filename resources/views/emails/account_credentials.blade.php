<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta content="width=device-width, initial-scale=1" name="viewport">
    <title>تفاصيل الحساب - بوابة الإسكان الجامعي</title>
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
        .credentials-box {
            background: #f0f4f8;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            padding: 20px;
            margin: 20px 0;
        }
        .credentials-item {
            margin: 10px 0;
            padding: 10px;
            background: #ffffff;
            border-radius: 8px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .copy-link {
            color: #8C2F39;
            text-decoration: underline;
            cursor: pointer;
            font-size: 12px;
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
        .footer {
            background: #f8fafc;
            padding: 20px;
            text-align: center;
            font-size: 14px;
            color: #64748b;
            border-top: 1px solid #e5e7eb;
        }
        .warning {
            background: #fff5f5;
            border-right: 4px solid #e53e3e;
            padding: 15px;
            border-radius: 8px;
            margin: 20px 0;
            color: #742a2a;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <img class="logo" src="https://es.nmu.edu.eg/housing/images/logo.png" alt="شعار بوابة الإسكان">
            <h1 style="margin:0;font-size:24px;">تفاصيل الحساب</h1>
        </div>
        
        <div class="content">
            <h2 style="color:#234E70;margin-top:0;">مرحباً {{ $user->first_name_ar .' '.$user->last_name_ar }}،</h2>
            
            <p>يسرنا تزويدك بمعلومات تسجيل الدخول الرسمية للوصول إلى بوابة الإسكان الجامعي:</p>
            
            <div class="credentials-box">
                <div class="credentials-item">
                    <div>
                        <span style="color:#234E70;" >اسم المستخدم:</span>
                        <strong id="username">{{ $user->email}}</strong>
                    </div>
                </div>
                <div class="credentials-item">
                    <div>
                        <span style="color:#234E70;">كلمة المرور:</span>
                        <strong id="password">{{ $password }}</strong>
                    </div>
                </div>
            </div>

            <center>
                <a href="https://es.nmu.edu.eg/housing/" class="button">
                    تسجيل الدخول
                </a>
            </center>
        </div>

        <div class="footer">
            <p>&copy; {{ date('Y') }} بوابة الإسكان الجامعي - جامعة المنصورة الجديدة. جميع الحقوق محفوظة.</p>
        </div>
    </div>
</body>
</html>