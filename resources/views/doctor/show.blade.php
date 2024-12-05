<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>معلومات الطبيب</title>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Cairo', sans-serif;
            background-color: #1e3c72;
            color: #2c3e50;
            line-height: 1.6;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
        }
        .card {
            background-color: #ffffff;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            padding: 20px;
            margin-bottom: 20px;
        }
        .doctor-header {
            text-align: center;
            margin-bottom: 20px;
        }
        .doctor-avatar {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            object-fit: cover;
            margin-bottom: 10px;
        }
        .doctor-name {
            font-size: 24px;
            font-weight: bold;
            margin: 0;
        }
        .doctor-specialization {
            color: #7f8c8d;
            margin: 5px 0;
        }
        .info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
        }
        .info-item h3 {
            font-weight: 600;
            margin-bottom: 5px;
        }
        .info-item p {
            margin: 0;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="card">
            <div class="doctor-header">
                <img src="{{ asset('storage/' . $doctor->photo) }}" alt="صورة {{$doctor->user->name}}" class="doctor-avatar">
                <h1 class="doctor-name">{{$doctor->user->name}}</h1>
                <p class="doctor-specialization">{{$doctor->specialization}}</p>
            </div>
            <div class="info-grid">
                <div class="info-item">
                    <h3>البريد الإلكتروني</h3>
                    <p>{{$doctor->user->email}}</p>
                </div>
                <div class="info-item">
                    <h3>التعليم</h3>
                    <p>{{$doctor->education}}</p>
                </div>
                <div class="info-item">
                    <h3>سنوات الخبرة</h3>
                    <p>{{$doctor->experience_years}} سنة</p>
                </div>
                <div class="info-item">
                    <h3>نبذة</h3>
                    <p>{{$doctor->bio}}</p>
                </div>
                <div class="info-item">
    <h3>ساعات العمل</h3>
    <p>من {{ \Carbon\Carbon::parse($doctor->start_work_time)->format('H:i') }} إلى {{ \Carbon\Carbon::parse($doctor->end_work_time)->format('H:i') }}</p>
</div>

                <div class="info-item">
                    <h3>مدة الحجز الافتراضية</h3>
                    <p>{{$doctor->default_time_reservations}} دقيقة</p>
                </div>
            </div>
        </div>
    </div>
</body>
</html>