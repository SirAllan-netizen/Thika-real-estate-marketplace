<?php
include 'db_connect.php'; // Ensure your database connection file is included
?>

<!DOCTYPE html>
<html>
<head>
    <title>Upgrade to Premium</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f0f0f0;
        }

        .container {
            max-width: 600px;
            margin: 50px auto;
            padding: 20px;
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        h1 {
            text-align: center;
            color: #4CAF50;
        }

        label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }

        input[type="text"] {
            width: 100%;
            padding: 10px;
            margin-bottom: 20px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }

        button {
            width: 100%;
            padding: 10px;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }

        button:hover {
            background-color: #45a049;
        }

        #response {
            margin-top: 20px;
            padding: 10px;
            background-color: #e7f3fe;
            border: 1px solid #b3d7ff;
            border-radius: 4px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Upgrade to Premium</h1>
        <form id="paymentForm" method="POST" action="stk_push.php">
            <label for="phone_number">Enter your Phone Number (Format: 07XXXXXXXX):</label>
            <input type="text" name="phone_number" id="phone_number" required placeholder="07XXXXXXXX">

            <button type="submit">Upgrade to Premium</button>
        </form>

        <div id="response"></div>
    </div>

    <script>
        const form = document.getElementById('paymentForm');
        const responseDiv = document.getElementById('response');

        form.onsubmit = async (e) => {
            e.preventDefault();

            const formData = new FormData(form);

            try {
                const response = await fetch('stk_push.php', {
                    method: 'POST',
                    body: formData
                });

                const result = await response.json();
                if (result.errorMessage) {
                    responseDiv.innerHTML = `<p style="color: red;">Error: ${result.errorMessage}</p>`;
                } else if (result.CustomerMessage) {
                    responseDiv.innerHTML = `<p style="color: green;">${result.CustomerMessage}</p>`;
                } else {
                    responseDiv.innerHTML = `<p style="color: orange;">An unknown error occurred. Please try again.</p>`;
                }
            } catch (error) {
                responseDiv.innerHTML = `<p style="color: red;">Failed to connect to the server. Please try again later.</p>`;
            }
        };
    </script>
</body>
</html>
