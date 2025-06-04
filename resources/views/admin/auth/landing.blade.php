<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Landing Page</title>
    <style>
        body {
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            font-family: Arial, sans-serif;
            background-color: #f5f5f5;
        }

        .logo {
            max-width: 300px;
            margin-bottom: 30px;
        }

        .btn-login {
            padding: 12px 24px;
            background-color: #3490dc;
            color: white;
            border: none;
            border-radius: 6px;
            font-size: 18px;
            cursor: pointer;
            text-decoration: none;
            transition: background-color 0.3s ease;
        }

        .btn-login:hover {
            background-color: #2779bd;
        }
    </style>
</head>

<body>
    <img src="{{ asset('img/logo.png') }}" alt="Logo" class="logo" />
    <a href="{{ route('admin.login.form') }}" class="btn-login">Masuk</a>
</body>

</html>
