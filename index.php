<?php

session_start();


if (isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true) {
    header("location: welcome.php");
    exit;
}


require_once "config.php";


if (!$pdo) {
    die("Database connection failed!");
}


$username = $password = "";
$username_err = $password_err = $login_err = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    if (empty(trim($_POST["username"]))) {
        $username_err = "Please enter username.";
    } else {
        $username = trim($_POST["username"]);
    }

  
    if (empty(trim($_POST["password"]))) {
        $password_err = "Please enter your password.";
    } else {
        $password = trim($_POST["password"]);
    }

   
    if (empty($username_err) && empty($password_err)) {

        $sql = "SELECT id, username, password FROM users WHERE username = :username";

        if ($stmt = $pdo->prepare($sql)) {
        
            $stmt->bindParam(":username", $username, PDO::PARAM_STR);

     
            if ($stmt->execute()) {
            
                if ($stmt->rowCount() == 1) {
                    $row = $stmt->fetch(PDO::FETCH_ASSOC);
                    $id = $row["id"];
                    $username = $row["username"];
                    $hashed_password = $row["password"];

            
                    if (password_verify($password, $hashed_password)) {
                     
                        $_SESSION["loggedin"] = true;
                        $_SESSION["id"] = $id;
                        $_SESSION["username"] = $username;

                  
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

 
        unset($stmt);
    }

    unset($pdo);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f4f7fc;
            color: #333;
            margin: 0;
            padding: 0;
        }
        .wrapper {
            width: 100%;
            max-width: 400px;
            padding: 20px;
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            margin: 50px auto;
        }
        h2 {
            color: #4e73df;
            text-align: center;
            margin-bottom: 20px;
        }
        p {
            text-align: center;
            font-size: 16px;
        }
        .form-group {
            margin-bottom: 20px;
        }
        .form-control {
            border-radius: 5px;
            border: 1px solid #ccc;
            padding: 10px;
            font-size: 14px;
            transition: all 0.3s ease;
        }
        .form-control:focus {
            border-color: #4e73df;
            box-shadow: 0 0 8px rgba(78, 115, 223, 0.5);
        }
        .btn-primary {
            background-color: #4e73df;
            border-color: #4e73df;
            padding: 10px 20px;
            font-size: 16px;
            width: 100%;
            border-radius: 5px;
            transition: all 0.3s ease;
        }
        .btn-primary:hover {
            background-color: #2e59d9;
            border-color: #2e59d9;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }
        .alert {
            margin-bottom: 20px;
            text-align: center;
        }
        .invalid-feedback {
            font-size: 14px;
            color: #e74a3b;
        }
        a {
            color: #4e73df;
            text-decoration: none;
        }
        a:hover {
            text-decoration: underline;
        }

        @media (max-width: 768px) {
            .wrapper {
                margin: 20px;
                padding: 15px;
            }
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
                <label for="username">Username</label>
                <input type="text" name="username" id="username" class="form-control <?php echo (!empty($username_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $username; ?>" aria-label="Username">
                <span class="invalid-feedback"><?php echo $username_err; ?></span>
            </div>    
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" name="password" id="password" class="form-control <?php echo (!empty($password_err)) ? 'is-invalid' : ''; ?>" aria-label="Password">
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
