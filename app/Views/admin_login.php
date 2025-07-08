<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            min-height: 100vh;
            background: linear-gradient(135deg, #e0e7ff 0%, #f8fafc 100%);
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
        }
        .container.d-flex {
            min-height: 100vh;
            padding: 0;
        }
        .card.shadow.p-4 {
            min-width: 350px;
            max-width: 380px;
            border-radius: 1.5rem;
            box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.15);
            background: rgba(255,255,255,0.95);
            border: 1px solid #e0e7ff;
        }
        .btn-primary {
            background: linear-gradient(90deg, #6366f1 0%, #60a5fa 100%);
            border: none;
            font-weight: 600;
            letter-spacing: 0.5px;
            box-shadow: 0 2px 8px rgba(99,102,241,0.08);
            transition: background 0.2s;
        }
        .btn-primary:hover {
            background: linear-gradient(90deg, #60a5fa 0%, #6366f1 100%);
        }
        .logo-img {
            max-width: 120px;
            max-height: 80px;
            margin-bottom: 0.5rem;
        }
        @media (max-width: 500px) {
            .card.shadow.p-4 {
                min-width: 100% !important;
                max-width: 100vw;
                padding: 1.2rem !important;
                border-radius: 0.8rem;
            }
            .container.d-flex {
                padding: 0;
            }
        }
        @media (max-width: 350px) {
            .card.shadow.p-4 {
                padding: 0.5rem !important;
            }
            h3.mb-3 {
                font-size: 1.1rem;
            }
        }
    </style>
</head>
<body class="bg-light">
    <div class="container d-flex justify-content-center align-items-center" style="min-height: 100vh;">
        <div class="card shadow p-4" style="min-width: 350px;">
            <div class="text-center mb-3">
                <img src="/smec_logo.png" alt="SMEC Logo" class="logo-img">
            </div>
            <h3 class="mb-3 text-center">Admin Login</h3>
            <?php if (isset($error)): ?>
                <div class="alert alert-danger"><?= esc($error) ?></div>
            <?php endif; ?>
            <form method="post" action="<?= site_url('/admin_login') ?>">
                <div class="mb-3">
                    <label for="username" class="form-label">Username</label>
                    <input type="text" class="form-control" id="username" name="username" required autofocus>
                </div>
                <div class="mb-3">
                    <label for="password" class="form-label">Password</label>
                    <input type="password" class="form-control" id="password" name="password" required>
                </div>
                <button type="submit" class="btn btn-primary w-100">Login</button>
            </form>
        </div>
    </div>
    <footer class="text-center mt-4 mb-2 text-muted" style="font-size: 0.95rem;">
        &copy; <span id="copyright-year"></span> All rights reserved SMEC
    </footer>
    <script>
        document.getElementById('copyright-year').textContent = new Date().getFullYear();
    </script>
</body>
</html> 