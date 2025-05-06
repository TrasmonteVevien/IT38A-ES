<?php
session_start();
include 'db.php';
$job_seeker_id = $_SESSION['job_seeker_id'] ?? 1; // Example user ID

// Fetch job applications for the job seeker
$applications = $conn->query("
SELECT j.job_title, a.application_date, s.schedule_date
FROM applications a
JOIN jobs j ON a.job_id = j.id
LEFT JOIN schedules s ON a.id = s.application_id
WHERE a.user_id = $job_seeker_id");

// Handle credential update
if (isset($_POST['update_credentials'])) {
    $username = $_POST['username'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $phone = $_POST['phone'];
    $email = $_POST['email'];
    $conn->query("UPDATE users SET username='$username', password='$password', phone='$phone', email='$email' WHERE id=$job_seeker_id");
    echo "<p>Credentials updated successfully! Please log in again.</p>";
}

// Handle resume upload
if (isset($_POST['upload_resume'])) {
    $resume = $_FILES['resume']['name'];
    if ($resume) {
        move_uploaded_file($_FILES['resume']['tmp_name'], "uploads/$resume");
        $conn->query("UPDATE users SET resume='$resume' WHERE id=$job_seeker_id");
        echo "<p>Resume uploaded successfully!</p>";
    }
}

// Fetch user data
$user = $conn->query("SELECT * FROM users WHERE id = $job_seeker_id")->fetch_assoc();
?>

<!DOCTYPE html>
<html>
<head>
  <title>Job Seeker Dashboard</title>
  <link rel="stylesheet" href="css/style.css">
</head>
<body>
<header><h1>Job Seeker Dashboard</h1></header>
<div class="container">
  <!-- Edit Credentials -->
  <div class="card">
    <h2>Edit Credentials</h2>
    <form method="POST">
      <label>Username</label><input name="username" value="<?= $user['username'] ?>"><br>
      <label>Password</label><input name="password" type="password"><br>
      <label>Phone</label><input name="phone" value="<?= $user['phone'] ?>"><br>
      <label>Email</label><input name="email" value="<?= $user['email'] ?>"><br>
      <button class="button" type="submit" name="update_credentials">Update Credentials</button>
    </form>
  </div>

  <!-- Upload Resume -->
  <div class="card">
    <h2>Upload Resume</h2>
    <form method="POST" enctype="multipart/form-data">
      <label>Upload Resume</label><input type="file" name="resume" required><br>
      <button class="button" type="submit" name="upload_resume">Upload</button>
    </form>
  </div>

  <!-- Job Applications and Interview Schedules -->
  <div class="card">
    <h2>My Applications</h2>
    <table border ="1" cellpadding="10" cellspacing="0">
      <tr style="color: #00bfff;">
        <th>Job Title</th>
        <th>Application Date</th>
        <th>Interview Schedule</th>
      </tr>
      <?php while ($row = $applications->fetch_assoc()): ?>
        <tr>
          <td><?= $row['job_title'] ?></td>
          <td><?= $row['application_date'] ?></td>
          <td><?= $row['schedule_date'] ? $row['schedule_date'] : 'Pending' ?></td>
        </tr>
      <?php endwhile; ?>
    </table>
  </div>

  <!-- Logout -->
  <div class="card">
    <form method="POST" action="logout.php">
      <button class="button" type="submit">Logout</button>
    </form>
  </div>
</div>
</body>
</html>
