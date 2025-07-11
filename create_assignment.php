<?php
session_start();
require_once 'includes/config.php';
require_once 'includes/auth.php';

// Restrict to teachers
if ($_SESSION['user_role'] !== 'teacher' && $_SESSION['user_role'] !== 'admin') {
  echo "Access denied.";
  exit();
}

$success = $error = "";

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $title = trim($_POST['title']);
  $subject = trim($_POST['subject']);
  $description = trim($_POST['description']);
  $due_date = $_POST['due_date'];
  $created_by = $_SESSION['user_id'];

  // Handle file upload (optional)
  $file_path = "";
  if (!empty($_FILES['attachment']['name'])) {
    $targetDir = "uploads/";
    $fileName = basename($_FILES["attachment"]["name"]);
    $file_path = $targetDir . time() . "_" . $fileName;
    move_uploaded_file($_FILES["attachment"]["tmp_name"], $file_path);
  }

  if ($title && $subject && $due_date) {
    $stmt = $pdo->prepare("INSERT INTO assignments (title, subject, description, due_date, file_path, created_by) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->execute([$title, $subject, $description, $due_date, $file_path, $created_by]);
    $success = "✅ Assignment published successfully.";
  } else {
    $error = "⚠️ All required fields must be filled.";
  }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Create Assignment | EduCore</title>
  <link rel="stylesheet" href="assets/style.css">
  <style>
    body {
      background: #fff3e0;
      font-family: 'Segoe UI', sans-serif;
    }
    .container {
      max-width: 700px;
      margin: 2rem auto;
      background: #ffffff;
      padding: 2rem;
      border-radius: 12px;
      box-shadow: 0 4px 10px rgba(0,0,0,0.1);
    }
    h2 {
      text-align: center;
      color: #e65100;
    }
    input, textarea, select, button {
      width: 100%;
      padding: 0.6rem;
      margin-bottom: 1rem;
      border: 1px solid #ccc;
      border-radius: 6px;
    }
    .success {
      background: #e8f5e9;
      color: #2e7d32;
      padding: 0.7rem;
      border-radius: 5px;
      margin-bottom: 1rem;
    }
    .error {
      background: #ffebee;
      color: #c62828;
      padding: 0.7rem;
      border-radius: 5px;
      margin-bottom: 1rem;
    }
    .back {
      text-align: center;
    }
    .back a {
      color: #e65100;
      text-decoration: none;
    }
  </style>
</head>
<body>
  <div class="container">
    <h2>📌 Create Assignment</h2>
    <?php if ($success) echo "<div class='success'>$success</div>"; ?>
    <?php if ($error) echo "<div class='error'>$error</div>"; ?>

    <form method="POST" enctype="multipart/form-data">
      <input type="text" name="title" placeholder="Assignment Title" required>
      <input type="text" name="subject" placeholder="Subject" required>
      <textarea name="description" rows="4" placeholder="Description (optional)"></textarea>
      <input type="date" name="due_date" required>
      <label>Attach File (optional)</label>
      <input type="file" name="attachment">
      <button type="submit">📤 Publish Assignment</button>
    </form>

    <div class="back">
      <a href="dashboard.php">← Back to Dashboard</a>
    </div>
  </div>
</body>
</html>
