<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تسجيل الدخول للعيادة</title>
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
            max-width: 400px;
        }
        .logo {
            object-fit: cover;

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
        input {
            width: 93%;
            padding: 0.75rem;
            border: 2px solid #1e3c72;
            border-radius: 8px;
            font-size: 1rem;
            transition: border-color 0.3s, box-shadow 0.3s;
        }
        input:focus {
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
        .register-link {
            text-align: center;
            margin-top: 1.5rem;
            color: #2a5298;
        }
        .register-link a {
            color: #000000;
            text-decoration: none;
            font-weight: bold;
            transition: color 0.3s;
        }
        .register-link a:hover {
            color: #2a5298;
        }
    </style>
</head>
<body>
    <div class="container">
    <img src="{{ asset('images/Logo.png') }}" alt="شعار العيادة" class="logo">
        <h2>تسجيل الدخول للعيادة</h2>

        <?php if(session('success')): ?>
            <div class="alert alert-success">
                <?php echo e(session('success')); ?>
            </div>
        <?php endif; ?>

        <?php if(session('error')): ?>
            <div class="alert alert-danger">
                <?php echo e(session('error')); ?>
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

        <form method="POST" action="<?php echo e(route('clinic.login')); ?>">
            <?php echo csrf_field(); ?>
            <div class="form-group">
                <label for="email">البريد الإلكتروني</label>
                <input type="email" 
                       class="form-control <?php echo e($errors->has('email') ? 'is-invalid' : ''); ?>" 
                       id="email" 
                       name="email" 
                       value="<?php echo e(old('email')); ?>"
                       required>
                <?php if($errors->has('email')): ?>
                    <div class="error-message"><?php echo e($errors->first('email')); ?></div>
                <?php endif; ?>
            </div>

            <div class="form-group">
                <label for="password">كلمة المرور</label>
                <input type="password" 
                       class="form-control <?php echo e($errors->has('password') ? 'is-invalid' : ''); ?>" 
                       id="password" 
                       name="password" 
                       required>
                <?php if($errors->has('password')): ?>
                    <div class="error-message"><?php echo e($errors->first('password')); ?></div>
                <?php endif; ?>
            </div>
            <div class="register-link">
        <p>ليس لديك حساب؟ <a href="<?php echo e(route('clinic.register')); ?>">سجل الآن</a></p>
    </div>
            <button type="submit" class="btn">تسجيل الدخول</button>
        </form>
    </div>

</body>
</html>