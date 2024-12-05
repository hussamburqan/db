<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>لوحة تحكم العيادة</title>
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
            background-color: var(--secondary-color);
            color: var(--text-primary);
            line-height: 1.6;
        }

        .navbar {
            background: linear-gradient(135deg, var(--primary-color) 0%, #357abd 100%);
            color: white;
            padding: 1rem;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            position: sticky;
            top: 0;
            z-index: 1000;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }

        .flex-between {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .btn {
            padding: 8px 16px;
            border-radius: 4px;
            border: none;
            cursor: pointer;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .btn-logout {
            background: white;
            color: var(--primary-color);
        }

        .btn-success {
            background: var(--success);
            color: white;
        }

        .btn-danger {
            background: var(--danger);
            color: white;
        }

        .btn-info {
            background: var(--primary-color);
            color: white;
            text-decoration: none;
            display: inline-block;
        }

        .btn:hover {
            opacity: 0.9;
            transform: translateY(-2px);
        }

        .grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 20px;
            margin-top: 20px;
        }

        .card {
            background: white;
            border-radius: 8px;
            padding: 20px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            transition: all 0.3s ease;
        }

        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 6px 12px rgba(0,0,0,0.15);
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 15px;
        }

        .stat-item {
            text-align: center;
        }

        .number {
            font-size: 2.5em;
            font-weight: bold;
            color: var(--primary-color);
            line-height: 1;
        }

        .label {
            color: var(--text-secondary);
            font-size: 0.9em;
            margin-top: 5px;
        }

        .appointments-table {
            width: 100%;
            border-collapse: collapse;
        }

        .appointments-table th,
        .appointments-table td {
            padding: 12px;
            text-align: right;
            border-bottom: 1px solid #eee;
        }

        .appointments-table th {
            background-color: #f8f9fa;
            font-weight: 600;
        }

        .appointments-table tr:hover {
            background-color: #f8f9fe;
        }

        .doctor-card {
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 20px;
            text-align: center;
            transition: all 0.3s ease;
        }

        .doctor-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 6px 12px rgba(0,0,0,0.1);
        }

        .doctor-img {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            object-fit: cover;
            border: 4px solid var(--primary-color);
            margin-bottom: 10px;
        }
        .logo {
            display: block;
            margin: 0 auto 1rem;
            width: 120px;
            height: 120px;
            padding: 1px;
        }
        .status-badge {
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 0.85em;
        }

        .status-confirmed {
            background: #d4edda;
            color: #155724;
        }

        .status-pending {
            background: #fff3cd;
            color: #856404;
        }

        @media (max-width: 768px) {
            .stats-grid {
                grid-template-columns: 1fr;
            }
            
            .grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <nav class="navbar">
        <div class="container flex-between">
        <img src="{{ asset('images/Logo.png') }}" alt="شعار العيادة" class="logo">

            <h2>لوحة تحكم العيادة</h2>
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="btn btn-logout">تسجيل الخروج</button>
            </form>
        </div>
    </nav>

    <div class="container">
        <div class="card">
            <h3>مرحباً {{ $clinic->user->name }}</h3>
            <p>آخر تسجيل دخول: {{ auth()->user()->last_login ?? 'لا يوجد' }}</p>
        </div>

        <div class="grid">
            <div class="card">
                <h3>معلومات العيادة</h3>
                <p><strong>الموقع:</strong> {{ $clinic->location }}</p>
                <p><strong>ساعات العمل:</strong> {{ \Carbon\Carbon::parse($clinic->opening_time)->format('H:i') }} - {{ \Carbon\Carbon::parse($clinic->closing_time)->format('H:i') }}</p>
                <p><strong>الوصف:</strong> {{ $clinic->description }}</p>
            </div>

            <div class="card">
                <h3>إحصائيات سريعة</h3>
                <div class="stats-grid">
                    <div class="stat-item">
                        <span class="number">{{ $pendingReservations->count() }}</span>
                        <span class="label">مواعيد معلقة</span>
                    </div>
                    <div class="stat-item">
                        <span class="number">{{ $todayReservations->count() }}</span>
                        <span class="label">مواعيد اليوم</span>
                    </div>
                    <div class="stat-item">
                        <span class="number">{{ $doctors->count() }}</span>
                        <span class="label">الأطباء</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid">
        <div class="card">
    <h3>المواعيد المعلقة</h3>
    @if($pendingReservations->isEmpty())
        <p>لا توجد مواعيد معلقة</p>
    @else
        @foreach($pendingReservations as $reservation)
            <div class="appointment-item">
                <h4>{{ $reservation->patient->user->name }}</h4>
                <p>{{ $reservation->date }} - {{ \Carbon\Carbon::parse($reservation->time)->format('H:i') }}</p>
                <p>مع الدكتور: {{ $reservation->doctor->user->name }}</p>
                <p>السبب: {{ $reservation->reason_for_visit }}</p>
                <p>الملاحظات: {{ $reservation->notes }}</p>
                <div class="actions">
                    <form action="{{ route('reservations.confirm', $reservation) }}" method="POST" style="display: inline;">
                        @csrf
                        <button type="submit" class="btn btn-success">قبول</button>
                    </form>
                    <form action="{{ route('reservations.reject', $reservation) }}" method="POST" style="display: inline;">
                        @csrf
                        <button type="submit" class="btn btn-danger">رفض</button>
                    </form>
                </div>
            </div>
        @endforeach
    @endif
</div>

            <div class="card">
                <h3>مواعيد اليوم</h3>
                @if($todayReservations->isEmpty())
                    <p>لا توجد مواعيد لهذا اليوم</p>
                @else
                    <div style="overflow-x: auto;">
                        <table class="appointments-table">
                            <thead>
                                <tr>
                                    <th>الوقت</th>
                                    <th>المريض</th>
                                    <th>الطبيب</th>
                                    <th>السبب</th>
                                    <th>الحالة</th>
                                    <th>الإجراءات</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($todayReservations as $reservation)
                                    <tr>
                                        <td>{{ \Carbon\Carbon::parse($reservation->time)->format('H:i') }}</td>
                                        <td>{{ $reservation->patient->user->name }}</td>
                                        <td>{{ $reservation->doctor->user->name }}</td>
                                        <td>{{ $reservation->reason_for_visit }}</td>
                                        <td>
                                            <span class="status-badge {{ $reservation->status == 'confirmed' ? 'status-confirmed' : 'status-pending' }}">
                                                {{ $reservation->status }}
                                            </span>
                                        </td>
                                        <td>
                                            <button class="btn btn-info create-invoice" data-reservation-id="{{ $reservation->id }}">إنشاء فاتورة</button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>

        <div class="card">
            <h3>الأطباء</h3>
            <div class="grid">
                @foreach($doctors as $doctor)
                    <div class="doctor-card">
                        <img src="{{ asset('storage/' . $doctor->photo) }}" alt="صورة {{ $doctor->user->name }}" class="doctor-img">
                        <h4>{{ $doctor->user->name }}</h4>
                        <p>{{ $doctor->specialization }}</p>
                        <p>{{ \Carbon\Carbon::parse($doctor->start_work_time)->format('H:i') }} - {{ \Carbon\Carbon::parse($doctor->end_work_time)->format('H:i') }}</p>
                        <a href="{{ route('doctors.show', $doctor) }}" class="btn btn-info">عرض التفاصيل</a>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
    $(document).ready(function() {
        $('.create-invoice').on('click', function() {
            var reservationId = $(this).data('reservation-id');
            var modal = `
    <div class="modal" id="invoiceModal">
        <div class="modal-content">
            <h2>إنشاء فاتورة</h2>
            <form id="invoiceForm">
                @csrf
                <input type="hidden" name="reservation_id" value="${reservationId}">
                <div>
                    <label for="amount">المبلغ:</label>
                    <input type="number" id="amount" name="amount" required>
                </div>
                <div>
                    <label for="payment_method">طريقة الدفع:</label>
                    <select name="payment_method" required>
                        <option value="cash">نقدي</option>
                        <option value="credit_card">بطاقة ائتمان</option>
                        <option value="debit_card">بطاقة خصم</option>
                        <option value="insurance">تأمين</option>
                    </select>
                </div>
                <div>
                    <label for="payment_status">حالة الدفع:</label>
                    <select name="payment_status" required>
                        <option value="pending">معلق</option>
                        <option value="paid">مدفوع</option>
                    </select>
                </div>
                <div>
                    <label for="notes">ملاحظات:</label>
                    <textarea name="notes"></textarea>
                </div>
                <div>
                    <button type="submit" class="btn btn-success">إنشاء الفاتورة</button>
                    <button type="button" class="btn btn-danger" id="closeModal">إلغاء</button>
                </div>
            </form>
        </div>
    </div>
`;
    
            $('body').append(modal);
            $('#invoiceModal').show();
        });

        $(document).on('click', '#closeModal', function() {
            $('#invoiceModal').remove();
        });

        $(document).on('submit', '#invoiceForm', function(e) {
            e.preventDefault();
            var formData = $(this).serialize();
            $.ajax({
                url: '{{ route("invoices.store") }}',
                method: 'POST',
                data: formData,
                success: function(response) {
                    alert('تم إنشاء الفاتورة بنجاح');
                    $('#invoiceModal').remove();
                },
                error: function(xhr) {
                    alert('حدث خطأ أثناء إنشاء الفاتورة');
                }
            });
        });
    });
    </script>
</body>
</html>

