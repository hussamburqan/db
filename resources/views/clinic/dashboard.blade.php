<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">
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

/* General Styles */
body {
   font-family: 'Cairo', sans-serif;
   margin: 0;
   padding: 0;
   background-color: #f8f9fa;
   color: var(--text-primary);
   line-height: 1.6;
}

/* Layout */
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

/* Navigation */
.navbar {
   background: linear-gradient(135deg, var(--primary-color) 0%, #357abd 100%);
   color: white;
   padding: 1rem;
   box-shadow: 0 2px 4px rgba(0,0,0,0.1);
   position: sticky;
   top: 0;
   z-index: 1000;
}

.logo {
   object-fit: cover;
   display: block;
   margin: 0 0 1rem;
   width: 70px;
   height: 70px;
   padding: 1px;
}

/* Grid Layout */
.grid {
   display: grid;
   grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
   gap: 20px;
   margin-top: 20px;
}

/* Cards */
.card {
   background: white;
   border-radius: 8px;
   padding: 20px;
   box-shadow: 0 2px 4px rgba(0,0,0,0.1);
   margin-bottom: 20px;
}

/* Buttons */
.btn {
   padding: 8px 16px;
   border-radius: 4px;
   border: none;
   cursor: pointer;
   font-weight: 600;
   transition: all 0.3s ease;
}

.btn-logout { background: var(--danger); color: white; }
.btn-success { background: var(--success); color: white; }
.btn-danger { background: var(--danger); color: white; }
.btn-info { background: var(--primary-color); color: white; }

/* Stats */
.stats-grid {
   display: grid;
   grid-template-columns: repeat(3, 1fr);
   gap: 15px;
}

.stat-item {
   text-align: center;
   padding: 15px;
   background: white;
   border-radius: 8px;
   box-shadow: 0 2px 4px rgba(0,0,0,0.05);
}

.number {
   font-size: 2.5em;
   font-weight: bold;
   color: var(--primary-color);
   line-height: 1;
}

.label {
   color: var(--text-secondary);
   margin-top: 5px;
}

/* Tables */
.appointments-table {
   width: 100%;
   border-collapse: collapse;
   margin-top: 15px;
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

/* Status Badges */
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

/* Doctor Card */
.doctor-card {
   display: flex;
   flex-direction: column;
   align-items: center;
   padding: 20px;
   text-align: center;
   border-radius: 8px;
   background: white;
   box-shadow: 0 2px 4px rgba(0,0,0,0.1);
   transition: all 0.3s ease;
}

.doctor-card:hover {
   transform: translateY(-5px);
   box-shadow: 0 4px 8px rgba(0,0,0,0.2);
}

.doctor-img {
   width: 120px;
   height: 120px;
   border-radius: 50%;
   object-fit: cover;
   border: 4px solid var(--primary-color);
   margin-bottom: 15px;
}

.doctor-actions {
   margin-top: 15px;
}

/* Forms */
.form-group {
   margin-bottom: 1rem;
}

.form-control {
   width: 100%;
   padding: 0.5rem;
   border: 1px solid #ddd;
   border-radius: 4px;
   font-size: 1rem;
}

.form-control:focus {
   border-color: var(--primary-color);
   outline: none;
}

label {
   display: block;
   margin-bottom: 0.5rem;
   font-weight: 600;
}

select.form-control {
   height: 38px;
}

/* Modal */
.modal-overlay {
   display: none;
   position: fixed;
   top: 0;
   left: 0;
   right: 0;
   bottom: 0;
   background-color: rgba(0, 0, 0, 0.5);
   z-index: 1000;
   justify-content: center;
   align-items: center;
}

.modal {
   background-color: white;
   padding: 20px;
   border-radius: 8px;
   width: 90%;
   max-width: 500px;
}

/* Responsive */
@media (max-width: 768px) {
   .stats-grid, .grid {
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
            <form action="{{ route('logout') }}" method="POST" style="margin: 0;">
                @csrf
                <button type="submit" class="btn btn-logout">تسجيل الخروج</button>
            </form>
        </div>
    </nav>

    <div class="container">
        <div class="card">
            <h3>مرحباً {{ auth()->user()->name }}</h3>
        </div>

        <div class="card">
            <h3>إحصائيات سريعة</h3>
            <div class="stats-grid">
                <div class="stat-item">
                    <div class="number">{{ $pendingReservations->count() }}</div>
                    <div class="label">مواعيد معلقة</div>
                </div>
                <div class="stat-item">
                    <div class="number">{{ $todayReservations->count() }}</div>
                    <div class="label">مواعيد اليوم</div>
                </div>
                <div class="stat-item">
                    <div class="number">{{ $doctors->count() }}</div>
                    <div class="label">الأطباء</div>
                </div>
            </div>
        </div>

        <div class="card">
            <h3>المواعيد المعلقة</h3>
            @if($pendingReservations->isEmpty())
                <p>لا توجد مواعيد معلقة</p>
            @else
                <div class="appointments-table">
                    <table style="width: 100%;">
                        <thead>
                            <tr>
                                <th>المريض</th>
                                <th>الموعد</th>
                                <th>الطبيب</th>
                                <th>السبب</th>
                                <th>الإجراءات</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($pendingReservations as $reservation)
                                <tr>
                                    <td>{{ $reservation->patient->user->name }}</td>
                                    <td>{{ \Carbon\Carbon::parse($reservation->date)->format('Y-m-d') }} {{ \Carbon\Carbon::parse($reservation->time)->format('H:i') }}</td>
                                    <td>{{ $reservation->doctor->user->name }}</td>
                                    <td>{{ $reservation->reason_for_visit }}</td>
                                    <td>
                                        <form action="{{ route('reservations.confirm', $reservation->id) }}" method="POST" style="display: inline;">
                                            @csrf
                                            <button type="submit" class="btn btn-success">قبول</button>
                                        </form>
                                        <form action="{{ route('reservations.reject', $reservation->id) }}" method="POST" style="display: inline;">
                                            @csrf
                                            <button type="submit" class="btn btn-danger">رفض</button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>

        <div class="card">
    <h3>مواعيد اليوم</h3>
    @if($todayReservations->where('status', 'accepted')->isEmpty())
        <p>لا توجد مواعيد مقبولة لهذا اليوم</p>
    @else
        <div class="appointments-table">
            <table style="width: 100%;">
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
                    @foreach($todayReservations->where('status', 'accepted') as $reservation)
                        <tr>
                            <td>{{ \Carbon\Carbon::parse($reservation->time)->format('H:i') }}</td>
                            <td>{{ $reservation->patient->user->name }}</td>
                            <td>{{ $reservation->doctor->user->name }}</td>
                            <td>{{ $reservation->reason_for_visit }}</td>
                            <td>
                                <div style="display: flex; gap: 5px; align-items: center;">
                                    <span class="status-badge status-confirmed">
                                        {{ $reservation->status }}
                                    </span>
                                    <button class="btn btn-info change-status" 
                                            data-reservation-id="{{ $reservation->id }}"
                                            data-current-status="{{ $reservation->status }}">
                                        تغيير الحالة
                                    </button>
                                </div>
                            </td>
                            <td>
                                @if(!$reservation->invoice)
                                    <button class="btn btn-info create-invoice"
                                            data-reservation-id="{{ $reservation->id }}"
                                            data-clinic-id="{{ $reservation->nclinic_id }}">
                                        إنشاء فاتورة
                                    </button>
                                @else
                                    <span class="badge bg-success">تم إنشاء الفاتورة</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>

<div class="card">
    <h3>الأطباء</h3>
    <div class="grid">
        @foreach($doctors as $doctor)
            <div class="doctor-card">
                <img src="{{ asset('storage/' . $doctor->photo) }}" 
                     alt="صورة {{ $doctor->user->name }}" 
                     class="doctor-img">
                <h4>{{ $doctor->user->name }}</h4>
                <p>{{ $doctor->specialization }}</p>
                <p>
                    {{ \Carbon\Carbon::parse($doctor->start_work_time)->format('H:i') }} - 
                    {{ \Carbon\Carbon::parse($doctor->end_work_time)->format('H:i') }}
                </p>
                <div class="doctor-actions">
                    <a href="{{ route('doctors.show', $doctor->id) }}" class="btn btn-info">
                        عرض التفاصيل
                    </a>
                </div>
            </div>
        @endforeach
    </div>
</div>

<div class="modal-overlay" id="statusModal">
    <div class="modal">
        <div class="modal-header">
            <h3>تغيير حالة الموعد</h3>
            <button type="button" class="close-modal" onclick="closeStatusModal()">&times;</button>
        </div>
        <form id="statusForm">
            @csrf
            <input type="hidden" id="statusReservationId" name="reservation_id">
            
            <div class="form-group">
                <label for="status">الحالة</label>
                <select id="status" name="status" class="form-control" required>
                    <option value="pending">معلق</option>
                    <option value="confirmed">مؤكد</option>
                    <option value="cancelled">ملغي</option>
                </select>
            </div>

            <div class="modal-footer">
                <button type="submit" class="btn btn-success">تحديث الحالة</button>
                <button type="button" class="btn btn-secondary" onclick="closeStatusModal()">إلغاء</button>
            </div>
        </form>
    </div>
</div>

<div class="modal-overlay" id="invoiceModal">
        <div class="modal">
            <div class="modal-header">
                <h3>إنشاء فاتورة جديدة</h3>
                <button type="button" class="close-modal" onclick="closeModal()">&times;</button>
            </div>
            <form id="invoiceForm">
                @csrf
                <input type="hidden" id="reservationId" name="reservation_id">
                <input type="hidden" id="clinicId" name="nclinic_id">

                <div class="form-group">
                    <label for="amount">المبلغ</label>
                    <input type="number" 
                           id="amount" 
                           name="amount" 
                           class="form-control" 
                           step="0.01" 
                           min="0" 
                           required>
                </div>

                <div class="form-group">
                    <label for="payment_method">طريقة الدفع</label>
                    <select id="payment_method" 
                            name="payment_method" 
                            class="form-control" 
                            required>
                        <option value="">اختر طريقة الدفع</option>
                        <option value="cash">نقدي</option>
                        <option value="credit_card">بطاقة ائتمان</option>
                        <option value="debit_card">بطاقة خصم</option>
                        <option value="insurance">تأمين</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="payment_status">حالة الدفع</label>
                    <select id="payment_status" 
                            name="payment_status" 
                            class="form-control" 
                            required>
                        <option value="">اختر حالة الدفع</option>
                        <option value="pending">معلق</option>
                        <option value="paid">مدفوع</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="notes">ملاحظات</label>
                    <textarea id="notes" 
                              name="notes" 
                              class="form-control" 
                              rows="3"></textarea>
                </div>

                <div class="modal-footer">
                    <button type="submit" class="btn btn-success">إنشاء الفاتورة</button>
                    <button type="button" 
                            class="btn btn-secondary" 
                            onclick="closeModal()">إلغاء</button>
                </div>
            </form>
        </div>
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
    const statusButtons = document.querySelectorAll('.change-status');
    statusButtons.forEach(button => {
        button.addEventListener('click', function() {
            const reservationId = this.dataset.reservationId;
            const currentStatus = this.dataset.currentStatus;
            openStatusModal(reservationId, currentStatus);
        });
    });

    document.getElementById('statusForm').addEventListener('submit', handleStatusSubmit);
});

function openStatusModal(reservationId, currentStatus) {
    document.getElementById('statusReservationId').value = reservationId;
    document.getElementById('status').value = currentStatus;
    document.getElementById('statusModal').style.display = 'flex';
    document.body.style.overflow = 'hidden';
}

function closeStatusModal() {
    document.getElementById('statusModal').style.display = 'none';
    document.body.style.overflow = 'auto';
    document.getElementById('statusForm').reset();
}

async function handleStatusSubmit(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    const reservationId = document.getElementById('statusReservationId').value;
    
    try {
        const response = await fetch(`/api/reservations/${reservationId}/status`, {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({
                status: formData.get('status')
            })
        });

        const result = await response.json();
        
        if (!response.ok) {
            throw new Error(result.message || 'حدث خطأ أثناء تحديث الحالة');
        }

        if (result.status) {
            alert('تم تحديث الحالة بنجاح');
            closeStatusModal();
            window.location.reload();
        }
    } catch (error) {
        console.error('Error:', error);
        alert(error.message);
    }
}

        document.addEventListener('DOMContentLoaded', function() {
            const invoiceButtons = document.querySelectorAll('.create-invoice');
            
            invoiceButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const reservationId = this.dataset.reservationId;
                    const clinicId = this.dataset.clinicId;
                    openModal(reservationId, clinicId);
                });
            });

            document.getElementById('invoiceForm').addEventListener('submit', handleSubmit);
        });

        function openModal(reservationId, clinicId) {
            document.getElementById('reservationId').value = reservationId;
            document.getElementById('clinicId').value = clinicId;
            document.getElementById('invoiceModal').style.display = 'flex';
            document.body.style.overflow = 'hidden';
        }

        function closeModal() {
            document.getElementById('invoiceModal').style.display = 'none';
            document.body.style.overflow = 'auto';
            document.getElementById('invoiceForm').reset();
        }

        document.getElementById('invoiceModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeModal();
            }
        });

        async function handleSubmit(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            const data = Object.fromEntries(formData.entries());
            
            try {
                const response = await fetch('/api/invoices', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify(data)
                });

                const result = await response.json();
                
                if (!response.ok) {
                    throw new Error(result.message || 'حدث خطأ أثناء إنشاء الفاتورة');
                }

                if (result.status) {
                    alert('تم إنشاء الفاتورة بنجاح');
                    closeModal();
                    window.location.reload();
                }
            } catch (error) {
                console.error('Error:', error);
                alert(error.message);
            }
        }
    </script>
</body>
</html>
