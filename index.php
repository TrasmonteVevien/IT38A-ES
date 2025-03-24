<?php
// Initialize the session
session_start();

// Check if the user is already logged in, redirect if true
if (isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true) {
    header("location: welcome.php");
    exit;
}

// Include config file
require_once "config.php";

// Check if database connection is working
if (!$pdo) {
    die("Database connection failed!");
}

// Define variables and initialize with empty values
$username = $password = "";
$username_err = $password_err = $login_err = "";

// Processing form data when form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validate username
    if (empty(trim($_POST["username"]))) {
        $username_err = "Please enter username.";
    } else {
        $username = trim($_POST["username"]);
    }

    // Validate password
    if (empty(trim($_POST["password"]))) {
        $password_err = "Please enter your password.";
    } else {
        $password = trim($_POST["password"]);
    }

    // Check if there are no errors before querying the database
    if (empty($username_err) && empty($password_err)) {
        // Prepare SQL statement
        $sql = "SELECT id, username, password FROM users WHERE username = :username";

        if ($stmt = $pdo->prepare($sql)) {
            // Bind parameters
            $stmt->bindParam(":username", $username, PDO::PARAM_STR);

            // Execute statement
            if ($stmt->execute()) {
                // Check if username exists
                if ($stmt->rowCount() == 1) {
                    $row = $stmt->fetch(PDO::FETCH_ASSOC);
                    $id = $row["id"];
                    $username = $row["username"];
                    $hashed_password = $row["password"];

                    // Verify password
                    if (password_verify($password, $hashed_password)) {
                        // Password is correct, start session
                        $_SESSION["loggedin"] = true;
                        $_SESSION["id"] = $id;
                        $_SESSION["username"] = $username;

                        // Redirect to welcome page
                        header("location: welcome.php");
                        exit;
                    } else {
                        $login_err = "Invalid username or password.";
                    }
                } else {
                    $login_err = "Invalid username or password.";
                }
            } else {
                echo "Oops! Something went wrong. Please try again later.";
            }
        } else {
            echo "Database query preparation failed.";
        }

        // Close statement
        unset($stmt);
    }

    // Close connection
    unset($pdo);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
    body {
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        background-color: #f0f0f0;
        color: #000;
        margin: 0;
        padding: 0;
    }
    .wrapper {
        width: 100%;
        max-width: 400px;
        padding: 20px;
        background-color: #fff;
        border-radius: 8px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        margin: 50px auto;
    }
    h2 {
        color: #000;
        text-align: center;
        margin-bottom: 20px;
    }
    p {
        text-align: center;
        font-size: 16px;
        color: #222;
    }
    .form-group {
        margin-bottom: 20px;
    }
    .form-control {
        border-radius: 5px;
        border: 1px solid #666;
        padding: 10px;
        font-size: 14px;
        color: #000;
        background-color: #fff;
        transition: all 0.3s ease;
    }
    .form-control:focus {
        border-color: #000;
        box-shadow: 0 0 8px rgba(0, 0, 0, 0.3);
    }
    .btn-primary {
        background-color: #000;
        border-color: #000;
        color: #fff;
        padding: 10px 20px;
        font-size: 16px;
        width: 100%;
        border-radius: 5px;
        transition: all 0.3s ease;
    }
    .btn-primary:hover {
        background-color: #333;
        border-color: #333;
    }
    .alert {
        margin-bottom: 20px;
        text-align: center;
        background-color: #eee;
        color: #000;
    }
    .invalid-feedback {
        font-size: 14px;
        color: #a00;
    }
    a {
        color: #000;
        text-decoration: underline;
    }
    a:hover {
        text-decoration: none;
    }
</style>

</head>
<body>
    <div class="wrapper">
        <h2>Login</h2>
        <p>Fill in your credentials to login.</p>

        <?php 
        if (!empty($login_err)) {
            echo '<div class="alert alert-danger">' . $login_err . '</div>';
        }        
        ?>

        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
            <div class="form-group">
                <label>Username</label>
                <input type="text" name="username" class="form-control <?php echo (!empty($username_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $username; ?>">
                <span class="invalid-feedback"><?php echo $username_err; ?></span>
            </div>    
            <div class="form-group">
                <label>Password</label>
                <input type="password" name="password" class="form-control <?php echo (!empty($password_err)) ? 'is-invalid' : ''; ?>">
                <span class="invalid-feedback"><?php echo $password_err; ?></span>
            </div>
            <div class="form-group">
                <input type="submit" class="btn btn-primary" value="Login">
            </div>
            <p>Don't have an account? <a href="register.php">Sign up now.</a></p>
        </form>
    </div>
</body>
</html>
