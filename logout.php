<?php
session_start();
session_unset();
session_destroy();
?>

<!DOCTYPE html>
<html>
<head>
  <title>Logged Out</title>
  <style>
    body {
      background-color: #000;
      color: #fff;
      font-family: Arial, sans-serif;
      display: flex;
      flex-direction: column;
      justify-content: center;
      align-items: center;
      height: 100vh;
      margin: 0;
    }

    h1 {
      color: #00bfff;
    }

    .button {
      background-color: #00bfff;
      color: #000;
      padding: 10px 20px;
      margin: 10px;
      border: none;
      border-radius: 5px;
      font-weight: bold;
      text-decoration: none;
      cursor: pointer;
    }

    .button:hover {
      background-color: #009fd4;
    }
  </style>
</head>
<body>
  <h1>You have been logged out.</h1>
  <a href="index.php" class="button">Login Again</a>
  <button class="button" onclick="exitSite()">Exit</button>

  <script>
    function exitSite() {
      if (confirm('Are you sure you want to close the tab?')) {
        window.open('', '_self', '');
        window.close(); // This only works if opened via JS
        alert("You can now close this tab.");
      }
    }
  </script>
</body>
</html>
