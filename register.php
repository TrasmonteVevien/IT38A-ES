<?php

require_once "config.php";


$username = $password = $confirm_password = "";
$username_err = $password_err = $confirm_password_err = "";


if ($_SERVER["REQUEST_METHOD"] == "POST") {

   
    if (empty(trim($_POST["username"]))) {
        $username_err = "Please enter a username.";
    } elseif (!preg_match('/^[a-zA-Z0-9_]+$/', trim($_POST["username"]))) {
        $username_err = "Username can only contain letters, numbers, and underscores.";
    } else {
  
        $sql = "SELECT id FROM users WHERE username = :username";
        
        if ($stmt = $pdo->prepare($sql)) {
          
            $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);
            
     
            $param_username = trim($_POST["username"]);
            
          
            if ($stmt->execute()) {
                if ($stmt->rowCount() == 1) {
                    $username_err = "This username is already taken.";
                } else {
                    $username = trim($_POST["username"]);
                }
            } else {
                echo "Oops! Something went wrong. Please try again later.";
            }

      
            unset($stmt);
        }
    }


    if (empty(trim($_POST["password"]))) {
        $password_err = "Please enter a password.";     
    } elseif (strlen(trim($_POST["password"])) < 6) {
        $password_err = "Password must have at least 6 characters.";
    } else {
        $password = trim($_POST["password"]);
    }


    if (empty(trim($_POST["confirm_password"]))) {
        $confirm_password_err = "Please confirm password.";     
    } else {
        $confirm_password = trim($_POST["confirm_password"]);
        if (empty($password_err) && ($password != $confirm_password)) {
            $confirm_password_err = "Passwords did not match.";
        }
    }

    if (empty($username_err) && empty($password_err) && empty($confirm_password_err)) {
        
   
        $sql = "INSERT INTO users (username, password) VALUES (:username, :password)";
         
        if ($stmt = $pdo->prepare($sql)) {
          
            $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);
            $stmt->bindParam(":password", $param_password, PDO::PARAM_STR);
            

            $param_username = $username;
            $param_password = password_hash($password, PASSWORD_DEFAULT); 
            
   
            if ($stmt->execute()) {
               
                header("location: index.php");
            } else {
                echo "Oops! Something went wrong. Please try again later.";
            }

           
            unset($stmt);
        }
    }


    unset($pdo);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up</title>
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
            padding: 10px 10px;
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
        <h2>Sign Up</h2>
        <p>Please fill this form to create an account.</p>
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
            <div class="form-group">
                <label>Username</label>
                <input type="text" name="username" class="form-control <?php echo (!empty($username_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $username; ?>" aria-label="Username">
                <span class="invalid-feedback"><?php echo $username_err; ?></span>
            </div>    
            <div class="form-group">
                <label>Password</label>
                <input type="password" name="password" class="form-control <?php echo (!empty($password_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $password; ?>" aria-label="Password">
                <span class="invalid-feedback"><?php echo $password_err; ?></span>
                <small>Password must be at least 6 characters.</small>
            </div>
            <div class="form-group">
                <label>Confirm Password</label>
                <input type="password" name="confirm_password" class="form-control <?php echo (!empty($confirm_password_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $confirm_password; ?>" aria-label="Confirm Password">
                <span class="invalid-feedback"><?php echo $confirm_password_err; ?></span>
            </div>
            <div class="form-group">
                <input type="submit" class="btn btn-primary" value="Submit">
            </div>
            <p>Already have an account? <a href="index.php">Login here...</a>.</p>
        </form>
    </div>    
</body>
</html>
