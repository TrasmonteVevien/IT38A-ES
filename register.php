<?php
require_once "config.php";

$username = $password = $confirm_password = $name = $birthday = $address = $contact_email = $role = $company_name = "";
$username_err = $password_err = $confirm_password_err = $name_err = $birthday_err = $address_err = $contact_email_err = $role_err = $company_name_err = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validate username
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

    // Validate password
    if (empty(trim($_POST["password"]))) {
        $password_err = "Please enter a password.";
    } elseif (strlen(trim($_POST["password"])) < 6) {
        $password_err = "Password must have at least 6 characters.";
    } else {
        $password = trim($_POST["password"]);
    }

    // Confirm password
    if (empty(trim($_POST["confirm_password"]))) {
        $confirm_password_err = "Please confirm password.";
    } else {
        $confirm_password = trim($_POST["confirm_password"]);
        if (empty($password_err) && ($password != $confirm_password)) {
            $confirm_password_err = "Password did not match.";
        }
    }

    // Other inputs
    $name = trim($_POST["name"]);
    $birthday = trim($_POST["birthday"]);
    $address = trim($_POST["address"]);
    $contact_email = trim($_POST["contact_email"]);
    $role = $_POST["role"];
    $company_name = ($role == "employer") ? trim($_POST["company_name"]) : null;

    // Validate others
    if (empty($name)) $name_err = "Please enter your name.";
    if (empty($birthday)) $birthday_err = "Please enter your birthday.";
    if (empty($address)) $address_err = "Please enter your address.";
    if (empty($contact_email) || !filter_var($contact_email, FILTER_VALIDATE_EMAIL)) {
        $contact_email_err = "Please enter a valid email address.";
    }
    if (empty($role)) $role_err = "Please select a role.";
    if ($role == "employer" && empty($company_name)) {
        $company_name_err = "Please enter your company name.";
    }

    // Insert data
    if (empty($username_err) && empty($password_err) && empty($confirm_password_err) &&
        empty($name_err) && empty($birthday_err) && empty($address_err) &&
        empty($contact_email_err) && empty($role_err) && empty($company_name_err)) {

        $sql = "INSERT INTO users (username, password, name, birthday, address, contact_email, role, company_name) 
                VALUES (:username, :password, :name, :birthday, :address, :contact_email, :role, :company_name)";

        if ($stmt = $pdo->prepare($sql)) {
            $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);
            $stmt->bindParam(":password", $param_password, PDO::PARAM_STR);
            $stmt->bindParam(":name", $param_name, PDO::PARAM_STR);
            $stmt->bindParam(":birthday", $param_birthday, PDO::PARAM_STR);
            $stmt->bindParam(":address", $param_address, PDO::PARAM_STR);
            $stmt->bindParam(":contact_email", $param_contact_email, PDO::PARAM_STR);
            $stmt->bindParam(":role", $param_role, PDO::PARAM_STR);
            $stmt->bindParam(":company_name", $param_company_name, PDO::PARAM_STR);

            $param_username = $username;
            $param_password = password_hash($password, PASSWORD_DEFAULT);
            $param_name = $name;
            $param_birthday = $birthday;
            $param_address = $address;
            $param_contact_email = $contact_email;
            $param_role = $role;
            $param_company_name = $company_name;

            if ($stmt->execute()) {
                header("Location: " . ($role == "job_seeker" ? "job_seeker.php" : "employer.php"));
                exit();
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
    <title>Sign Up</title>
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
    </style>
<form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
    <!-- Username -->
    <div class="form-group">
        <label>Username</label>
        <input type="text" name="username" class="form-control <?php echo (!empty($username_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $username; ?>">
        <span class="invalid-feedback"><?php echo $username_err; ?></span>
    </div>

    <!-- Password -->
    <div class="form-group">
        <label>Password</label>
        <input type="password" name="password" class="form-control <?php echo (!empty($password_err)) ? 'is-invalid' : ''; ?>">
        <span class="invalid-feedback"><?php echo $password_err; ?></span>
    </div>

    <!-- Confirm Password -->
    <div class="form-group">
        <label>Confirm Password</label>
        <input type="password" name="confirm_password" class="form-control <?php echo (!empty($confirm_password_err)) ? 'is-invalid' : ''; ?>">
        <span class="invalid-feedback"><?php echo $confirm_password_err; ?></span>
    </div>

    <!-- Name -->
    <div class="form-group">
        <label>Name</label>
        <input type="text" name="name" class="form-control" value="<?php echo $name; ?>">
    </div>

    <!-- Birthday -->
    <div class="form-group">
        <label>Birthday</label>
        <input type="date" name="birthday" class="form-control" value="<?php echo $birthday; ?>">
    </div>

    <!-- Address -->
    <div class="form-group">
        <label>Address</label>
        <input type="text" name="address" class="form-control" value="<?php echo $address; ?>">
    </div>

    <!-- Contact Email -->
    <div class="form-group">
        <label>Contact Email</label>
        <input type="email" name="contact_email" class="form-control" value="<?php echo $contact_email; ?>">
    </div>

    <!-- Role -->
    <div class="form-group">
        <label>Role</label>
        <select name="role" class="form-control" id="roleSelect">
            <option value="job_seeker" <?php echo ($role == "job_seeker") ? 'selected' : ''; ?>>Job Seeker</option>
            <option value="employer" <?php echo ($role == "employer") ? 'selected' : ''; ?>>Employer</option>
        </select>
    </div>

    <!-- Company Name (conditional) -->
    <div class="form-group" id="company-name-container" style="display: <?php echo ($role == 'employer') ? 'block' : 'none'; ?>;">
        <label>Company Name</label>
        <input type="text" name="company_name" class="form-control" value="<?php echo $company_name; ?>">
    </div>

    <!-- Submit -->
    <div class="form-group">
        <input type="submit" class="btn btn-primary" value="Submit">
    </div>
    <p>Already have an account? <a href="index.php">Login here...</a></p>
</form>

<script>
    document.getElementById("roleSelect").addEventListener("change", function () {
        const companyField = document.getElementById("company-name-container");
        companyField.style.display = this.value === "employer" ? "block" : "none";
    });
</script>