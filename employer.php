<?php
session_start();
include 'db.php';
$employer_id = $_SESSION['employer_id'] ?? 1; // Example employer ID

// Fetch posted jobs
$jobs = $conn->query("SELECT * FROM jobs WHERE employer_id = $employer_id");

// Fetch candidates who applied to posted jobs
$candidates = $conn->query("
SELECT u.username, u.resume, j.job_title, a.application_date, a.application_id
FROM applications a
JOIN users u ON a.user_id = u.id
JOIN jobs j ON a.job_id = j.id
WHERE j.employer_id = $employer_id");

// Handle job posting
if (isset($_POST['post_job'])) {
    $job_title = $_POST['job_title'];
    $description = $_POST['description'];
    $location = $_POST['location'];
    $conn->query("INSERT INTO jobs (job_title, description, location, employer_id) 
    VALUES ('$job_title', '$description', '$location', $employer_id)");
    echo "<p>Job posted successfully!</p>";
}

// Handle interview scheduling
if (isset($_POST['schedule_interview'])) {
    $application_id = $_POST['application_id'];
    $schedule_date = $_POST['schedule_date'];
    $conn->query("INSERT INTO schedules (application_id, schedule_date, status) 
    VALUES ($application_id, '$schedule_date', 'Scheduled')");
    echo "<p>Interview scheduled successfully!</p>";
}

// Handle employer info update
if (isset($_POST['update_info'])) {
    $company_name = $_POST['company_name'];
    $uname = $_POST['username'];
    $pass = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $phone = $_POST['phone'];
    $email = $_POST['email'];
    $conn->query("UPDATE employers SET company_name='$company_name', username='$uname', password='$pass', phone='$phone', email='$email' WHERE id=$employer_id");
    echo "<p>Employer info updated successfully!</p>";
}

// Fetch employer info
$employer = $conn->query("SELECT * FROM employers WHERE id = $employer_id")->fetch_assoc();
?>

<!DOCTYPE html>
<html>
<head>
  <title>Employer Dashboard</title>
  <link rel="stylesheet" href="css/style.css">
</head>
<body>
<header><h1>Employer Dashboard</h1></header>
<div class="container">
  <!-- Edit Employer Info -->
  <div class="card">
    <h2>Edit Employer Info</h2>
    <form method="POST">
      <label>Company Name</label><input name="company_name" value="<?= $employer['company_name'] ?>"><br>
      <label>Username</label><input name="username" value="<?= $employer['username'] ?>"><br>
      <label>Password</label><input name="password" type="password"><br>
      <label>Phone</label><input name="phone" value="<?= $employer['phone'] ?>"><br>
      <label>Email</label><input name="email" value="<?= $employer['email'] ?>"><br>
      <button class="button" type="submit" name="update_info">Update Info</button>
    </form>
  </div>

  <!-- Post Job -->
  <div class="card">
    <h2>Post a Job</h2>
    <form method="POST">
      <label>Job Title</label><input name="job_title" required><br>
      <label>Job Description</label><textarea name="description" required></textarea><br>
      <label>Location (Bukidnon/Manolo)</label><input name="location" required><br>
      <button class="button" type="submit" name="post_job">Post Job</button>
    </form>
  </div>

  <!-- View Candidates -->
  <div class="card">
    <h2>View Candidates</h2>
    <table border="1" cellpadding="10" cellspacing="0">
      <tr style="color: #00bfff;">
        <th>Candidate</th>
        <th>Resume</th>
        <th>Applied Job</th>
        <th>Application Date</th>
        <th>Schedule Interview</th>
      </tr>
      <?php while ($row = $candidates->fetch_assoc()): ?>
        <tr>
          <td><?= $row['username'] ?></td>
          <td><a href="uploads/<?= $row['resume'] ?>" target="_blank">View Resume</a></td>
          <td><?= $row['job_title'] ?></td>
          <td><?= $row['application_date'] ?></td>
          <td>
            <form method="POST">
              <input type="hidden" name="application_id" value="<?= $
