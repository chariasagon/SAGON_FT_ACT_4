<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f4f7fc;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            color: #333;
        }

        .login-container {
            background-color: #fff;
            padding: 40px;
            width: 100%;
            max-width: 400px;
            border-radius: 10px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            text-align: center;
        }

        .logo {
            width: 80px;
            height: auto;
            margin-bottom: 15px;
        }

        h2 {
            margin-bottom: 20px;
            font-size: 28px;
            color: #4e73df;
        }

        input[type="email"], input[type="password"] {
            width: 100%;
            padding: 12px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 16px;
            box-sizing: border-box;
            transition: border 0.3s;
        }

        input:focus {
            border-color: #4e73df;
            outline: none;
        }

        button {
            width: 100%;
            padding: 12px;
            background-color: #4e73df;
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        button:hover {
            background-color: #2e59d9;
        }

        .social-btn {
            display: block;
            width: 100%;
            padding: 12px;
            margin-top: 10px;
            border-radius: 5px;
            text-align: center;
            font-size: 16px;
            text-decoration: none;
            color: white;
            text-transform: uppercase;
            transition: opacity 0.3s;
        }

        .google-btn { background-color: #db4437; }
        .facebook-btn { background-color: #3b5998; }
        .social-btn:hover { opacity: 0.85; }
        .error-message {
            margin-bottom: 10px;
            font-size: 14px;
            display: block;
            color: red;
        }

        .success-message {
            margin-bottom: 10px;
            font-size: 14px;
            display: block;
            color: green;
        }

        .register-link {
            margin-top: 10px;
            font-size: 14px;
        }

        @media (max-width: 500px) {
            .login-container { padding: 20px; }
            h2 { font-size: 24px; }
        }
    </style>
</head>
<body>

    <div class="login-container">
        <!-- Logo -->
        <img src="{{ asset('a.png') }}" alt="Logo" class="logo">

        <h2>Login</h2>

        <!-- Response Message -->
        <p id="responseMessage" class="error-message"></p>

        <!-- Login Form -->
        <form id="loginForm" autocomplete="off">
            <input type="email" id="email" name="email" placeholder="Email" required autocomplete="off">
            <input type="password" id="password" name="password" placeholder="Password" required autocomplete="new-password">
            <button type="submit">Login</button>
        </form>

        <div style="margin-top: 15px;">
            <p>OR</p>
        </div>

        <!-- Google Login -->
        <a href="{{ route('social.redirect', ['provider' => 'google']) }}" class="social-btn google-btn">Login with Google</a>

        <!-- Facebook Login -->
        <a href="{{ route('social.redirect', ['provider' => 'facebook']) }}" class="social-btn facebook-btn">Login with Facebook</a>

        <p class="register-link">
            Don't have an account? <a href="{{ route('register') }}">Register here</a>
        </p>
    </div>

    <script>
        $(document).ready(function() {
            // AJAX login form submission
            $('#loginForm').submit(function(e) {
                e.preventDefault();

                // Get the email and password
                const email = $('#email').val();
                const password = $('#password').val();

                $.ajax({
                    url: "{{ route('ajax.login') }}",
                    type: "POST",
                    data: {
                        email: email,
                        password: password,
                        _token: "{{ csrf_token() }}" // Ensure CSRF token is included
                    },
                    success: function(response) {
                        // Display success message and redirect
                        $('#responseMessage').text(response.message).removeClass('error-message').addClass('success-message');

                        if (response.redirect) {
                            setTimeout(() => { window.location.href = response.redirect; }, 1000); // Redirect after 1 second
                        }
                    },
                    error: function(xhr) {
                        // Handle error response
                        let errorMessage = "Something went wrong!";
                        if (xhr.responseJSON && xhr.responseJSON.error) {
                            errorMessage = xhr.responseJSON.error;
                        }
                        $('#responseMessage').text(errorMessage).removeClass('success-message').addClass('error-message');
                    }
                });
            });
        });
    </script>

</body>
</html>
