<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Welcome to TaskHive')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * { box-sizing: border-box; }
        body {
            font-family: 'Inter', sans-serif;
            background: #f6f5f7;
            display: flex;
            justify-content: center;
            align-items: center;
            flex-direction: column;
            height: 100vh;
            margin: 0;
            overflow: hidden;
        }

        h1 { font-weight: 700; margin: 0 0 20px; }
        p { font-size: 14px; font-weight: 300; line-height: 20px; letter-spacing: 0.5px; margin: 20px 0 30px; }
        
        .btn-custom {
            border-radius: 20px;
            border: 1px solid #1670d0;
            background-color: #1670d0;
            color: #FFFFFF;
            font-size: 12px;
            font-weight: bold;
            padding: 12px 45px;
            letter-spacing: 1px;
            text-transform: uppercase;
            transition: transform 80ms ease-in;
            cursor: pointer;
        }
        .btn-custom:active { transform: scale(0.95); }
        .btn-custom:focus { outline: none; }
        .btn-custom.ghost { background-color: transparent; border-color: #FFFFFF; }

        form {
            background-color: #ffffff;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-direction: column;
            padding: 0 50px;
            height: 100%;
            text-align: center;
        }

        .form-control { background-color: #eee; border: none; padding: 12px 15px; margin: 8px 0; border-radius: 8px; }
        .form-control:focus { background-color: #e2e2e2; box-shadow: none; }

        .auth-container {
            background-color: #fff;
            border-radius: 0;
            position: relative;
            overflow: hidden;
            width: 100vw;
            height: 100vh;
        }

        .form-container {
            position: absolute;
            top: 0;
            height: 100%;
            transition: all 0.6s ease-in-out;
        }

        .sign-in-container {
            left: 0;
            width: 50%;
            z-index: 2;
        }
        .auth-container.right-panel-active .sign-in-container {
            transform: translateX(100%);
        }

        .sign-up-container {
            left: 0;
            width: 50%;
            opacity: 0;
            z-index: 1;
        }
        .auth-container.right-panel-active .sign-up-container {
            transform: translateX(100%);
            opacity: 1;
            z-index: 5;
            animation: show 0.6s;
        }

        @keyframes show {
            0%, 49.99% { opacity: 0; z-index: 1; }
            50%, 100% { opacity: 1; z-index: 5; }
        }

        .overlay-container {
            position: absolute;
            top: 0;
            left: 50%;
            width: 50%;
            height: 100%;
            overflow: hidden;
            transition: transform 0.6s ease-in-out;
            z-index: 100;
        }
        .auth-container.right-panel-active .overlay-container {
            transform: translateX(-100%);
        }

        .overlay {
            background: linear-gradient(to right, rgba(1, 38, 54, 0.8), rgba(1, 13, 24, 0.8)), url('images/bg-1.png') no-repeat center;
            background-size: cover;
            color: #FFFFFF;
            position: relative;
            left: -100%;
            height: 100%;
            width: 200%;
            transform: translateX(0);
            transition: transform 0.6s ease-in-out;
        }
        .auth-container.right-panel-active .overlay {
            transform: translateX(50%);
        }

        .overlay-panel {
            position: absolute;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-direction: column;
            padding: 0 40px;
            text-align: center;
            top: 0;
            height: 100%;
            width: 50%;
            transform: translateX(0);
            transition: transform 0.6s ease-in-out;
        }

        .overlay-left { transform: translateX(-20%); }
        .auth-container.right-panel-active .overlay-left { transform: translateX(0); }
        .overlay-right { right: 0; transform: translateX(0); }
        .auth-container.right-panel-active .overlay-right { transform: translateX(20%); }

        .role-selector { display: flex; gap: 10px; width: 100%; margin: 10px 0; }
        .role-selector .form-check { background: #eee; padding: 10px; border-radius: 8px; flex: 1; text-align: center; cursor: pointer;}
        
        @media (max-width: 768px) {
            .auth-container { display: flex; flex-direction: column; overflow-y: auto; }
            .form-container, .overlay-container { position: static; width: 100%; height: auto; }
            .overlay-container { display: none; }
            .sign-up-container { opacity: 1; z-index: 1; transform: none; padding: 40px 0; border-bottom: 2px solid #eee;}
            .sign-in-container { transform: none; padding: 40px 0;}
            form { padding: 0 20px; }
        }
    </style>
</head>
<body>

    @if($errors->any())
        <div class="position-absolute top-0 start-50 translate-middle-x mt-3 w-50" style="z-index: 9999;">
            <div class="alert alert-danger alert-dismissible fade show shadow" role="alert">
                <ul class="mb-0 ps-3 text-start">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        </div>
    @endif

    @yield('content')

    @stack('scripts')
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>