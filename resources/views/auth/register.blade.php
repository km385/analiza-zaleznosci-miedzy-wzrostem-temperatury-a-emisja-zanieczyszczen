<!DOCTYPE html>
<html>
<head>
    <title>Register</title>
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
            width: 400px; 
            max-width: 90%;
            padding: 20px;
            border: 1px solid #ccc;
            border-radius: 8px;
            background-color: #fff;
            box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.1);
            display: flex; 
            flex-direction: column; 
            gap: 10px; 
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

        input[type="text"],
        input[type="email"],
        input[type="password"] {
            width: 100%;
            padding: 8px;
            border: 1px solid #ccc;
            border-radius: 4px;
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
        <h2>Register</h2>

        <form id="registerForm" method="POST" action="{{ route('register') }}">
            @csrf

            <div>
                <label for="name">Name:</label>
                <input type="text" id="name" name="name" required autofocus>
            </div>

            <div>
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" required>
            </div>

            <div>
                <label for="password">Password:</label>
                <input type="password" id="password" name="password" required>
            </div>

            <div>
                <button type="submit">Register</button>
            </div>
        </form>
    </div>

    <script>
        document.getElementById('registerForm').addEventListener('submit', function(event) {
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
                if (data.redirect) {
                    window.location.href = data.redirect;
                } else {
                    
                    console.log(data.message);
                }
            })
            .catch(function(error) {

                console.log('An error occurred:', error);
            });
        });
    </script>
</body>
</html>
