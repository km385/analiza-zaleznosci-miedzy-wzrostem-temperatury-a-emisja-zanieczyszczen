<!DOCTYPE html>
<html>
<head>
    <title>Login</title>
    <style>
        body {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            background-color: #f2f2f2;
            font-family: Arial, sans-serif;
        }

        .container {
            width: 400px; /* Adjust the width as desired */
            max-width: 90%;
            padding: 20px;
            border: 1px solid #ccc;
            border-radius: 8px;
            background-color: #fff;
            box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.1);
            display: flex; /* Added */
            flex-direction: column; /* Added */
            gap: 10px; /* Added */
            padding-right: 35px;
        }

        h2 {
            text-align: center;
            margin-bottom: 20px;
            color: #333;
        }

        label {
            display: block;
            margin-bottom: 8px;
            font-weight: bold;
            color: #555;
        }

        input[type="email"],
        input[type="password"] {
            width: 100%;
            padding: 8px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }

        .error-message {
            color: #ff0000;
            font-size: 14px;
            margin-top: 5px;
            display: none;
        }

        button[type="submit"] {
            padding: 8px 16px;
            background-color: #4CAF50;
            color: #fff;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }

        button[type="submit"]:hover {
            background-color: #45a049;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Login</h2>

        <form id="loginForm" method="POST" action="{{ route('login') }}">
            @csrf

            <div>
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" required autofocus>
            </div>

            <div>
                <label for="password">Password:</label>
                <input type="password" id="password" name="password" required>
            </div>

            <div>
                <button type="submit">Login</button>
            </div>

            <div class="error-message" id="errorMessage"></div>
        </form>
    </div>

    <script>
        document.getElementById('loginForm').addEventListener('submit', function(event) {
            event.preventDefault(); 

            var form = event.target;
            var url = form.action;
            var method = form.method;
            var data = new FormData(form);

            fetch(url, {
                method: method,
                body: data
            })
            .then(function(response) {
                return response.json();
            })
            .then(function(data) {
                var errorMessage = document.getElementById('errorMessage');
                
                if (data.redirect) {
                    window.location.href = data.redirect;
                } else {
                    
                    errorMessage.textContent = data.message;
                    errorMessage.style.display = 'block';
                }
            })
            .catch(function(error) {
                
                console.log('An error occurred:', error);
            });
        });
    </script>
</body>
</html>
