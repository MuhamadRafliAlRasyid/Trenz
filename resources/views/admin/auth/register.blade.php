<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Register Admin</title>
</head>

<body>
    <form method="POST" action="{{ route('admin.register.submit') }}">
        @csrf
        <input type="text" name="name" placeholder="Name" required autofocus>
        <input type="email" name="email" placeholder="Email" required>
        <input type="password" name="password" placeholder="Password" required>
        <input type="password" name="password_confirmation" placeholder="Confirm Password" required>
        <button type="submit">Register</button>
    </form>
</body>

</html>
