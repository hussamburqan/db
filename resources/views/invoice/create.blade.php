<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>إنشاء فاتورة جديدة</title>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Cairo', sans-serif;
            background-color: #f8f9fe;
            color: #2c3e50;
            line-height: 1.6;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .card {
            background-color: #ffffff;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            padding: 20px;
        }
        h1 {
            text-align: center;
            color: #2c3e50;
        }
        form {
            display: grid;
            gap: 20px;
        }
        label {
            font-weight: bold;
            display: block;
            margin-bottom: 5px;
        }
        input, select, textarea {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 16px;
        }
        button {
            background-color: #3498db;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
            font-weight: bold;
        }
        button:hover {
            background-color: #2980b9;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="card">
            <h1>إنشاء فاتورة جديدة</h1>
            <form id="invoiceForm">
                <div>
                    <label for="amount">المبلغ</label>
                    <input type="number" id="amount" name="amount" required>
                </div>
                <div>
                    <label for="payment_method">طريقة الدفع</label>
                    <select id="payment_method" name="payment_method" required>
                        <option value="">اختر طريقة الدفع</option>
                        <option value="cash">نقدي</option>
                        <option value="credit_card">بطاقة ائتمان</option>
                        <option value="debit_card">بطاقة خصم</option>
                        <option value="insurance">تأمين</option>
                    </select>
                </div>
                <div>
                    <label for="payment_status">حالة الدفع</label>
                    <select id="payment_status" name="payment_status" required>
                        <option value="">اختر حالة الدفع</option>
                        <option value="pending">معلق</option>
                        <option value="paid">مدفوع</option>
                        <option value="cancelled">ملغي</option>
                        <option value="refunded">مسترجع</option>
                    </select>
                </div>
                <div>
                    <label for="notes">ملاحظات</label>
                    <textarea id="notes" name="notes"></textarea>
                </div>
                <div>
                    <label for="patient_archive_id">معرف أرشيف المريض</label>
                    <input type="number" id="patient_archive_id" name="patient_archive_id" required>
                </div>
                <div>
                    <label for="nclinic_id">معرف العيادة</label>
                    <input type="number" id="nclinic_id" name="nclinic_id" required>
                </div>
                <button type="submit">إنشاء الفاتورة</button>
            </form>
        </div>
    </div>

    <script>
        document.getElementById('invoiceForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            const invoiceData = Object.fromEntries(formData.entries());
            
            console.log('Invoice Data:', invoiceData);
            alert('تم إنشاء الفاتورة بنجاح!');
        });
    </script>
</body>
</html>