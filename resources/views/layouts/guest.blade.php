<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'MyBlog') }}</title>

        
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">

        <style>
            body {
                background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
                min-height: 100vh;
                font-family: 'Figtree', sans-serif;
            }
            .auth-card {
                background: #1e293b;
                border: 1px solid rgba(255, 255, 255, 0.1);
                border-radius: 16px;
                box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.5);
            }
            .form-control {
                background-color: #0f172a;
                border: 1px solid #334155;
                color: #f8fafc;
            }
            .form-control:focus {
                background-color: #0f172a;
                border-color: #3b82f6;
                color: #f8fafc;
                box-shadow: 0 0 0 0.25rem rgba(59, 130, 246, 0.25);
            }
            .form-control::placeholder {
                color: #64748b;
            }
        </style>
    </head>
    <body class="d-flex align-items-center justify-content-center py-5">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-12 col-sm-10 col-md-8 col-lg-5">

                    
                    <div class="text-center mb-4">
                        <a href="/" class="text-decoration-none d-inline-flex align-items-center gap-2">
                            <div class="bg-primary text-white rounded-3 d-flex align-items-center justify-content-center shadow" style="width: 45px; height: 45px;">
                                <i class="bi bi-feather fs-4"></i>
                            </div>
                            <span class="fw-bold fs-3 text-white">My<span class="text-primary">Blog</span></span>
                        </a>
                    </div>

                    
                    <div class="auth-card p-4 p-sm-5 text-white">
                        {{ $slot }}
                    </div>

                </div>
            </div>
        </div>

        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    </body>
</html>