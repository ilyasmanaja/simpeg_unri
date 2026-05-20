<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - SIMPEG Universitas Riau</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.10.5/font/bootstrap-icons.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <style>
        :root {
            --unri-red: #b91c1c;
            --unri-dark-red: #7f1d1d;
            --bg-gradient: linear-gradient(135deg, #f3f4f6 0%, #e5e7eb 100%);
        }
        
        body {
            font-family: 'Poppins', sans-serif;
            background: var(--bg-gradient);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .login-container {
            max-width: 450px;
            width: 100%;
            padding: 20px;
        }

        .login-card {
            border: none;
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
            overflow: hidden;
            background: #ffffff;
        }

        .login-header {
            background: linear-gradient(135deg, var(--unri-red), var(--unri-dark-red));
            padding: 40px 30px;
            text-align: center;
            color: #ffffff;
        }

        .login-header i {
            font-size: 3rem;
            margin-bottom: 10px;
            display: inline-block;
        }

        .login-header h4 {
            font-weight: 700;
            letter-spacing: 0.5px;
            margin-bottom: 5px;
        }

        .login-header p {
            font-size: 0.85rem;
            opacity: 0.85;
            margin-bottom: 0;
        }

        .form-label-custom {
            font-size: 0.85rem;
            font-weight: 600;
            color: #4b5563;
            margin-bottom: 6px;
        }

        .input-group-text {
            background-color: #f9fafb;
            border-right: none;
            color: #9ca3af;
        }

        .form-control {
            border-left: none;
            padding: 10px 12px;
            font-size: 0.9rem;
            background-color: #f9fafb;
        }

        .form-control:focus {
            background-color: #ffffff;
            border-color: #dee2e6;
            box-shadow: none;
        }

        .input-group:focus-within .input-group-text {
            color: var(--unri-red);
            background-color: #ffffff;
        }

        .btn-login {
            background: linear-gradient(135deg, var(--unri-red), var(--unri-dark-red));
            border: none;
            border-radius: 10px;
            padding: 12px;
            font-weight: 600;
            font-size: 0.95rem;
            transition: all 0.3s ease;
        }

        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(185, 28, 28, 0.3);
        }

        .alert {
            border-radius: 10px;
            font-size: 0.85rem;
        }
    </style>
</head>
<body>

    <div class="login-container">
        <div class="card login-card">
            
            {{-- Header Card --}}
            <div class="login-header">
                <i class="bi bi-shield-lock-fill"></i>
                <h4>SIMPEG UNRI</h4>
                <p>Sistem Informasi Kepegawaian - Fakultas Teknik</p>
            </div>

            <div class="card-body p-4 p-md-5">
                
                {{-- Notifikasi Sukses Logout --}}
                @if(session('success'))
                    <div class="alert alert-success d-flex align-items-center py-2 mb-4" role="alert">
                        <i class="bi bi-check-circle-fill me-2"></i>
                        <div>{{ session('success') }}</div>
                    </div>
                @endif

                {{-- Notifikasi Error Gagal Login --}}
                @if($errors->any())
                    <div class="alert alert-danger py-2 mb-4" role="alert">
                        <ul class="mb-0 ps-3">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                {{-- Form Login --}}
                <form method="POST" action="{{ route('login') }}">
                    @csrf
                    
                    {{-- Input Email --}}
                    <div class="mb-3">
                        <label class="form-label-custom">Alamat Email</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-envelope-fill"></i></span>
                            <input type="email" name="email" class="form-control" value="{{ old('email') }}" placeholder="nama@unri.ac.id" required autofocus>
                        </div>
                    </div>

                    {{-- Input Password --}}
                    <div class="mb-4">
                        <label class="form-label-custom">Password</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-lock-fill"></i></span>
                            <input type="password" name="password" class="form-control" placeholder="••••••••" required>
                        </div>
                    </div>

                    {{-- Tombol Submit --}}
                    <button type="submit" class="btn btn-login btn-primary w-100 text-white">
                        Masuk ke Sistem <i class="bi bi-box-arrow-in-right ms-1"></i>
                    </button>
                </form>
                
            </div>
        </div>
    </div>

</body>
</html>