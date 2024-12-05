<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تسجيل حساب جديد للعيادة</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }
        .container {
            background-color: rgba(255, 255, 255, 0.9);
            border-radius: 15px;
            box-shadow: 0 8px 32px rgba(31, 38, 135, 0.37);
            backdrop-filter: blur(4px);
            border: 1px solid rgba(255, 255, 255, 0.18);
            padding: 2rem;
            width: 100%;
            max-width: 500px;
            margin: 2rem auto;
        }
        .logo {
            display: block;
            margin: 0 auto 1rem;
            width: 120px;
            height: 120px;
            padding: 1px;
        }
        h2 {
            color: #1e3c72;
            text-align: center;
            margin-bottom: 1.5rem;
            font-size: 1.8rem;
        }
        .form-group {
            margin-bottom: 1.5rem;
        }
        label {
            display: block;
            margin-bottom: 0.5rem;
            color: #1e3c72;
            font-weight: bold;
        }
        input, select, textarea {
            width: 93%;
            padding: 0.75rem;
            border: 2px solid #1e3c72;
            border-radius: 8px;
            font-size: 1rem;
            transition: border-color 0.3s, box-shadow 0.3s;
        }
        input:focus, select:focus, textarea:focus {
            outline: none;
            border-color: #2a5298;
            box-shadow: 0 0 0 3px rgba(42, 82, 152, 0.2);
        }
        .btn {
            background-color: #1e3c72;
            color: white;
            border: none;
            padding: 0.75rem;
            width: 100%;
            font-size: 1rem;
            border-radius: 8px;
            cursor: pointer;
            transition: background-color 0.3s, transform 0.1s;
        }
        .btn:hover {
            background-color: #2a5298;
        }
        .btn:active {
            transform: scale(0.98);
        }
        .alert {
            padding: 0.75rem;
            margin-bottom: 1rem;
            border-radius: 8px;
            font-weight: bold;
        }
        .alert-success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        .alert-danger {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        .error-message {
            color: #dc3545;
            font-size: 0.875rem;
            margin-top: 0.25rem;
        }
        .login-link {
            text-align: center;
            margin-top: 1.5rem;
            color: #2a5298;
        }
        .login-link a {
            color: #000000;
            text-decoration: none;
            font-weight: bold;
            transition: color 0.3s;
        }
        .login-link a:hover {
            color: #2a5298;
        }
        .section-title {
            color: #1e3c72;
            margin-top: 2rem;
            margin-bottom: 1rem;
            font-size: 1.4rem;
            border-bottom: 2px solid #1e3c72;
            padding-bottom: 0.5rem;
        }
    </style>
</head>
<body>
    <div class="container">
        <img src="{{ asset('images/Logo.png') }}" alt="شعار العيادة" class="logo">
        <h2>تسجيل حساب جديد للعيادة</h2>

        <?php if(session('success')): ?>
            <div class="alert alert-success">
                <?php echo e(session('success')); ?>
            </div>
        <?php endif; ?>

        <?php if($errors->any()): ?>
            <div class="alert alert-danger">
                <ul style="list-style: none; padding: 0; margin: 0;">
                    <?php foreach($errors->all() as $error): ?>
                        <li><?php echo e($error); ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <form method="POST" action="<?php echo e(route('clinic.register')); ?>" enctype="multipart/form-data">
            <?php echo csrf_field(); ?>
            
            <h3 class="section-title">معلومات المستخدم</h3>
            
            <div class="form-group">
                <label for="name">الاسم الكامل</label>
                <input type="text" id="name" name="name" value="<?php echo e(old('name')); ?>" required>
            </div>

            <div class="form-group">
                <label for="email">البريد الإلكتروني</label>
                <input type="email" id="email" name="email" value="<?php echo e(old('email')); ?>" required>
            </div>

            <div class="form-group">
                <label for="password">كلمة المرور</label>
                <input type="password" id="password" name="password" required>
            </div>

            <div class="form-group">
                <label for="password_confirmation">تأكيد كلمة المرور</label>
                <input type="password" id="password_confirmation" name="password_confirmation" required>
            </div>

            <div class="form-group">
                <label for="address">العنوان</label>
                <input type="text" id="address" name="address" value="<?php echo e(old('address')); ?>" required>
            </div>

            <div class="form-group">
                <label for="age">العمر</label>
                <input type="number" id="age" name="age" value="<?php echo e(old('age')); ?>" required>
            </div>

            <div class="form-group">
                <label for="gender">الجنس</label>
                <select id="gender" name="gender" required>
                    <option value="">اختر الجنس</option>
                    <option value="male" <?php echo e(old('gender') == 'male' ? 'selected' : ''); ?>>ذكر</option>
                    <option value="female" <?php echo e(old('gender') == 'female' ? 'selected' : ''); ?>>أنثى</option>
                </select>
            </div>

            <div class="form-group">
                <label for="phone">رقم الهاتف</label>
                <input type="tel" id="phone" name="phone" value="<?php echo e(old('phone')); ?>" required>
            </div>

            <h3 class="section-title">معلومات العيادة</h3>

            <div class="form-group">
                <label for="location">موقع العيادة</label>
                <input type="text" id="location" name="location" value="<?php echo e(old('location')); ?>" required>
            </div>

            <div class="form-group">
                <label for="photo">صورة العيادة</label>
                <input type="file" id="photo" name="photo" required>
            </div>

            <div class="form-group">
                <label for="cover_photo">صورة الغلاف</label>
                <input type="file" id="cover_photo" name="cover_photo" required>
            </div>

            <div class="form-group">
                <label for="description">وصف العيادة</label>
                <textarea id="description" name="description" required><?php echo e(old('description')); ?></textarea>
            </div>

            <div class="form-group">
                <label for="opening_time">وقت الفتح</label>
                <input type="time" id="opening_time" name="opening_time" value="<?php echo e(old('opening_time')); ?>" required>
            </div>

            <div class="form-group">
                <label for="closing_time">وقت الإغلاق</label>
                <input type="time" id="closing_time" name="closing_time" value="<?php echo e(old('closing_time')); ?>" required>
            </div>

            <div class="form-group">
                <label for="major_id">التخصص</label>
                <select id="major_id" name="major_id" required>
                    <option value="">اختر التخصص</option>
                    <?php foreach ($majors as $major): ?>
                        <option value="<?php echo e($major->id); ?>" 
                            <?php echo e(old('major_id') == $major->id ? 'selected' : ''); ?>>
                            <?php echo e($major->name); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <button type="submit" class="btn">تسجيل العيادة</button>
        </form>

        <div class="login-link">
            <p>لديك حساب بالفعل؟ <a href="<?php echo e(route('clinic.login')); ?>">تسجيل الدخول</a></p>
        </div>
    </div>
</body>
</html>

