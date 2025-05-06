To match the UI color and add the requested features to your login page, we will:

1. **Match the color theme**: We'll keep the same dark background and light text as the sign-up page for consistency.
2. **Terms and Agreement Modal**: We'll add a modal that shows the terms and conditions when the user clicks on a link.
3. **Email and Password Login**: We'll implement a Gmail-based login feature with basic validation.
4. **Security Features**: We'll add basic security features such as checking if the email format is correct and if the password is secure.

Here's the updated code for your `login.php`:

### Updated `login.php`

```php
<?php
// Include config file
require_once "config.php";

// Define variables and initialize with empty values
$email = $password = "";
$email_err = $password_err = "";

// Processing form data when form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Validate email
    if (empty(trim($_POST["email"]))) {
        $email_err = "Please enter an email.";
    } elseif (!filter_var(trim($_POST["email"]), FILTER_VALIDATE_EMAIL)) {
        $email_err = "Please enter a valid email address.";
    } else {
        $email = trim($_POST["email"]);
    }

    // Validate password
    if (empty(trim($_POST["password"]))) {
        $password_err = "Please enter a password.";
    } elseif (strlen(trim($_POST["password"])) < 6) {
        $password_err = "Password must have at least 6 characters.";
    } else {
        $password = trim($_POST["password"]);
    }

    // Check input errors before validating the credentials
    if (empty($email_err) && empty($password_err)) {

        // Prepare a select statement
        $sql = "SELECT id, username, password, role FROM users WHERE email = :email";

        if ($stmt = $pdo->prepare($sql)) {
            // Bind variables to the prepared statement as parameters
            $stmt->bindParam(":email", $param_email, PDO::PARAM_STR);

            // Set parameters
            $param_email = $email;

            // Attempt to execute the prepared statement
            if ($stmt->execute()) {
                if ($stmt->rowCount() == 1) {
                    if ($row = $stmt->fetch()) {
                        $id = $row["id"];
                        $username = $row["username"];
                        $hashed_password = $row["password"];
                        $role = $row["role"];

                        // Verify the password
                        if (password_verify($password, $hashed_password)) {
                            // Password is correct, so start a new session
                            session_start();

                            // Store data in session variables
                            $_SESSION["id"] = $id;
                            $_SESSION["username"] = $username;
                            $_SESSION["role"] = $role;

                            // Redirect user to the appropriate dashboard based on role
                            if ($role == "job_seeker") {
                                header("location: job_seeker.php");
                            } else {
                                header("location: employer.php");
                            }
                            exit();
                        } else {
                            $password_err = "The password you entered was not valid.";
                        }
                    }
                } else {
                    $email_err = "No account found with that email.";
                }
            } else {
                echo "Oops! Something went wrong. Please try again later.";
            }

            // Close statement
            unset($stmt);
        }
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
        background-color: #000;
        color: #fff;
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
        padding: 10px 10px;
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

    .modal-content {
        background-color: #fff;
        color: #000;
    }
    </style>
</head>
<body>
    <div class="wrapper">
        <h2>Login</h2>
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
            <div class="form-group">
                <label>Email</label>
                <input type="email" name="email" class="form-control <?php echo (!empty($email_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $email; ?>">
                <span class="invalid-feedback"><?php echo $email_err; ?></span>
            </div>    
            <div class="form-group">
                <label>Password</label>
                <input type="password" name="password" class="form-control <?php echo (!empty($password_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $password; ?>">
                <span class="invalid-feedback"><?php echo $password_err; ?></span>
            </div>
            <div class="form-group">
                <input type="submit" class="btn btn-primary" value="Login">
            </div>
            <p>Don't have an account? <a href="register.php">Sign up here...</a>.</p>
            <p><a href="#" data-toggle="modal" data-target="#termsModal">Terms and Conditions</a></p>
        </form>
    </div>

    <!-- Terms and Conditions Modal -->
    <div class="modal fade" id="termsModal" tabindex="-1" role="dialog" aria-labelledby="termsModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="termsModalLabel">Terms and Conditions</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p>By using this website, you agree to the terms and conditions...</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Include Bootstrap JS -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
```

### Key Updates:

1. **UI Colors**: The UI color is consistent with your sign-up page.
2. **Terms and Conditions Modal**: A modal appears when the user clicks on the "Terms and Conditions" link.
3. **Email and Password Validation**: The email is validated for correct format, and password length is checked to be at least 6 characters.
4. **Security Features**: The password entered is validated against the stored hash using `password_verify`.

### Next Steps:

* You can customize the terms and conditions inside the modal body.
* Adjust the redirection paths (`job_seeker_dashboard.php` and `employer_dashboard.php`) as needed based on your file structure.
