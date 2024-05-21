<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);
    $gender = $_POST['gender'];

    $conn = new mysqli("http://php-database.cxeppltorunv.us-east-1.rds.amazonaws.com/", "admin", "cloudcomputer", "php-database");

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $check_stmt = $conn->prepare("SELECT username FROM users WHERE username = ?");
    $check_stmt->bind_param("s", $username);
    $check_stmt->execute();
    $check_stmt->store_result();
    if ($check_stmt->num_rows > 0) {
        echo "Error: Username already exists";
        $check_stmt->close();
        $conn->close();
        exit;
    }
    $check_stmt->close();

    $stmt = $conn->prepare("INSERT INTO users (username, password, gender) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $username, $password, $gender);

    if ($stmt->execute()) {
        session_start();
        $_SESSION['user_id'] = $conn->insert_id;
        header("Location: dashboard.php");
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Register</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
        }
        .container {
            width: 300px;
            margin: 0 auto;
            padding: 20px;
            background-color: #fff;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            margin-top: 50px;
        }
        h2 {
            text-align: center;
        }
        label {
            font-weight: bold;
        }
        input[type="text"],
        input[type="password"],
        input[type="submit"],
        select {
            width: 100%;
            padding: 10px;
            margin-bottom: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
            box-sizing: border-box;
        }
        input[type="submit"] {
            background-color: #4CAF50;
            color: white;
            border: none;
            cursor: pointer;
        }
        input[type="submit"]:hover {
            background-color: #45a049;
        }
        .success {
            color: green;
            text-align: center;
            margin-top: 10px;
        }
        .error {
            color: red;
            text-align: center;
            margin-top: 10px;
        }
    </style>
    <script>
        function validateForm() {
            var password = document.getElementById("password").value;
            var confirmPassword = document.getElementById("confirm_password").value;

            var passwordRegex = /^(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/;

            if (password !== confirmPassword) {
                alert("Password and Confirm Password do not match");
                return false;
            }

            if (!passwordRegex.test(password)) {
                alert("Password must contain one capital letter, one number, one symbol, and be at least 8 characters long");
                return false;
            }

            return true;
        }
    </script>
</head>
<body>
    <div class="container">
        <h2>Register</h2>
        <form method="post" action="register.php" onsubmit="return validateForm()">
            <label for="username">Username:</label><br>
            <input type="text" id="username" name="username" required><br>
            <label for="password">Password:</label><br>
            <input type="password" id="password" name="password" required><br>
            <label for="confirm_password">Confirm Password:</label><br>
            <input type="password" id="confirm_password" name="confirm_password" required><br>
            <label for="gender">Gender:</label><br>
            <select id="gender" name="gender">
                <option value="male">Male</option>
                <option value="female">Female</option>
            </select><br>
            <input type="submit" value="Register">
        </form>
    </div>
</body>
</html>
