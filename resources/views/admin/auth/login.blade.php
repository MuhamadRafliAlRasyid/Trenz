<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Login Admin</title>
</head>

<body>
    <form method="POST" action="{{ route('admin.login.submit') }}">
        @csrf
        <input type="email" name="email" placeholder="Email" required autofocus>
        <input type="password" name="password" placeholder="Password" required>
        <label>
            <input type="checkbox" name="remember"> Remember Me
        </label>
        <button type="submit">Login</button>
    </form>
</body>

</html>
