<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>User Login</title>
  <style>
    body {
      margin: 0;
      padding: 0;
      font-family: "Inter", sans-serif;
      background: #f4f6f9;
      display: flex;
      justify-content: center;
      align-items: center;
      height: 100vh; /* Full screen height */
    }

    .login-box {
      width: 400px;
      background: #fff;
      padding: 40px;
      border-radius: 12px;
      box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15);
      text-align: center;
    }

    .login-box h1 {
      margin-bottom: 25px;
      font-size: 26px;
      color: #333;
    }

    .login-box label {
      display: block;
      text-align: left;
      margin-bottom: 6px;
      font-weight: 600;
      color: #444;
      font-size: 14px;
    }

    .login-box input {
      width: 100%;
      padding: 12px;
      margin-bottom: 18px;
      border: 1px solid #ddd;
      border-radius: 8px;
      outline: none;
      font-size: 14px;
      transition: 0.3s;
    }

    .login-box input:focus {
      border-color: #4a90e2;
      box-shadow: 0 0 6px rgba(74, 144, 226, 0.4);
    }

    .login-box button {
      width: 100%;
      padding: 12px;
      background: #4a90e2;
      border: none;
      border-radius: 8px;
      color: white;
      font-size: 16px;
      cursor: pointer;
      transition: 0.3s;
    }

    .login-box button:hover {
      background: #357abd;
    }

    .login-box .extra {
      margin-top: 15px;
      font-size: 14px;
      color: #666;
    }

    .login-box .extra a {
      color: #4a90e2;
      text-decoration: none;
      font-weight: bold;
    }

    .login-box .extra a:hover {
      text-decoration: underline;
    }
  </style>
</head>

<body>
  <div class="login-box">
    <h1>User Sign In</h1>
   <form action="{{ route('app.login.store') }}" method="POST">
    @csrf
    <!-- Email -->
    <label for="email">Email</label>
    <input type="email" id="email" name="email" placeholder="Enter your email" required>

    <!-- Password -->
    <label for="password">Password</label>
    <input type="password" id="password" name="password" placeholder="Enter your password" required>

    <button type="submit">Log In</button>
</form>

  </div>
</body>

</html>
