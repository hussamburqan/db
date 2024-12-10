<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تفاصيل الطبيب</title>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary-color: #1e4c72;
            --secondary-color: #1e3c72;
            --text-primary: #2c3e50;
            --text-secondary: #7f8c8d;
            --success: #27ae60;
            --danger: #e74c3c;
        }

        body {
            font-family: 'Cairo', sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f8f9fa;
            color: var(--text-primary);
            line-height: 1.6;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }

        .doctor-profile {
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            padding: 30px;
            margin-bottom: 30px;
        }

        .profile-header {
            display: flex;
            align-items: center;
            margin-bottom: 30px;
        }

        .doctor-image {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            object-fit: cover;
            margin-left: 30px;
            border: 4px solid var(--primary-color);
        }

        .doctor-info h1 {
            color: var(--primary-color);
            margin: 0 0 10px 0;
        }

        .info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-top: 20px;
        }

        .info-item {
            padding: 15px;
            background: #f8f9fa;
            border-radius: 8px;
        }

        .info-item h3 {
            color: var(--primary-color);
            margin: 0 0 10px 0;
            font-size: 1.1em;
        }

        .info-item p {
            margin: 0;
            color: var(--text-secondary);
        }

        .schedule {
            margin-top: 30px;
            padding: 20px;
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .schedule h2 {
            color: var(--primary-color);
            margin-top: 0;
        }

        .btn {
            padding: 8px 16px;
            border-radius: 4px;
            border: none;
            cursor: pointer;
            font-weight: 600;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-block;
        }

        .btn-primary {
            background: var(--primary-color);
            color: white;
        }

        .btn-danger {
            background: var(--danger);
            color: white;
        }

        .actions {
            margin-top: 20px;
            display: flex;
            gap: 10px;
        }

        @media (max-width: 768px) {
            .profile-header {
                flex-direction: column;
                text-align: center;
            }

            .doctor-image {
                margin: 0 0 20px 0;
            }

            .info-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="doctor-profile">
            <div class="profile-header">
                <img src="{{ asset('storage/' . $doctor->photo) }}" alt="{{ $doctor->user->name }}" class="doctor-image">
                <div class="doctor-info">
                    <h1>{{ $doctor->user->name }}</h1>
                    <p>{{ $doctor->specialization }}</p>
                    <p>{{ $doctor->major->name }}</p>
                </div>
            </div>

            <div class="info-grid">
                <div class="info-item">
                    <h3>معلومات الاتصال</h3>
                    <p><strong>البريد الإلكتروني:</strong> {{ $doctor->user->email }}</p>
                    <p><strong>رقم الهاتف:</strong> {{ $doctor->user->phone }}</p>
                </div>

                <div class="info-item">
                    <h3>معلومات شخصية</h3>
                    <p><strong>العمر:</strong> {{ $doctor->user->age }} سنة</p>
                    <p><strong>الجنس:</strong> {{ $doctor->user->gender == 'male' ? 'ذكر' : 'أنثى' }}</p>
                    <p><strong>العنوان:</strong> {{ $doctor->user->address }}</p>
                </div>

                <div class="info-item">
                    <h3>معلومات العمل</h3>
                    <p><strong>رقم الهوية:</strong> {{ $doctor->national_id }}</p>
                    <p><strong>سعر الكشف:</strong> {{ $doctor->price }} ريال</p>
                    <p><strong>مدة الكشف:</strong> {{ $doctor->default_time_reservations }} دقيقة</p>
                </div>
            </div>

            <div class="schedule">
                <h2>جدول العمل</h2>
                <div class="info-grid">
                    <div class="info-item">
                        <p><strong>ساعات العمل:</strong></p>
                        <p>من {{ \Carbon\Carbon::parse($doctor->start_work_time)->format('H:i') }} إلى {{ \Carbon\Carbon::parse($doctor->end_work_time)->format('H:i') }}</p>
                    </div>

                    <div class="info-item">
                        <p><strong>مواعيد اليوم:</strong></p>
                        <p>{{ $todayAppointments->count() }} موعد</p>
                    </div>
                </div>
            </div>

            <div class="actions">
                <a href="{{ route('doctors.edit', $doctor->id) }}" class="btn btn-primary">تعديل البيانات</a>
                <form action="{{ route('doctors.destroy', $doctor->id) }}" method="POST" style="display: inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger" onclick="return confirm('هل أنت متأكد من حذف هذا الطبيب؟')">
                        حذف الطبيب
                    </button>
                </form>
            </div>
        </div>

        @if($todayAppointments->isNotEmpty())
        <div class="schedule">
            <h2>مواعيد اليوم</h2>
            <div style="overflow-x: auto;">
                <table style="width: 100%; border-collapse: collapse;">
                    <thead>
                        <tr>
                            <th style="padding: 12px; text-align: right; border-bottom: 2px solid #eee;">الوقت</th>
                            <th style="padding: 12px; text-align: right; border-bottom: 2px solid #eee;">اسم المريض</th>
                            <th style="padding: 12px; text-align: right; border-bottom: 2px solid #eee;">سبب الزيارة</th>
                            <th style="padding: 12px; text-align: right; border-bottom: 2px solid #eee;">الحالة</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($todayAppointments as $appointment)
                        <tr>
                            <td style="padding: 12px; border-bottom: 1px solid #eee;">
                                {{ \Carbon\Carbon::parse($appointment->time)->format('H:i') }}
                            </td>
                            <td style="padding: 12px; border-bottom: 1px solid #eee;">
                                {{ $appointment->patient->user->name }}
                            </td>
                            <td style="padding: 12px; border-bottom: 1px solid #eee;">
                                {{ $appointment->reason_for_visit }}
                            </td>
                            <td style="padding: 12px; border-bottom: 1px solid #eee;">
                                <span class="status-badge {{ $appointment->status == 'confirmed' ? 'status-confirmed' : 'status-pending' }}">
                                    {{ $appointment->status }}
                                </span>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @endif
    </div>
</body>
</html>