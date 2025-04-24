<?php
session_start();
 

if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: index.php");
    exit;
}
?>
 
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body {
            font: 14px sans-serif;
            text-align: center;
            background-color: #f4f6f9;
            padding-top: 50px;
        }
        h1 {
            color: #2c3e50;
        }
        .container {
            max-width: 800px;
            margin: 0 auto;
        }
        .btn {
            font-size: 16px;
            padding: 10px 20px;
            margin: 10px;
        }
        .footer {
            position: fixed;
            bottom: 0;
            width: 100%;
            background-color: #2c3e50;
            color: white;
            text-align: center;
            padding: 10px;
        }
        @media (max-width: 768px) {
            .btn {
                width: 100%;
                margin: 5px 0;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Hello, <b><?php echo htmlspecialchars($_SESSION["username"]); ?></b>! Welcome!.</h1>
        <p>
            <a href="reset-password.php" class="btn btn-warning btn-lg">Reset Your Password</a>
            <a href="logout.php" class="btn btn-danger btn-lg">Sign Out</a>
        </p>
    </div>
    <footer class="footer">
        <p>&copy; <?php echo date("Y"); ?> Your Website. All rights reserved.</p>
    </footer>
</body>
</html>
