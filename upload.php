<?php
session_start();
require_once 'includes/config.php';

// Restrict access to only teachers
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'teacher') {
  header('Location: login.php');
  exit();
}

// File upload handling
$message = "";
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_FILES['file'])) {
  $title    = trim($_POST['title']);
  $subject  = trim($_POST['subject']);
  $category = trim($_POST['category']);
  $userId   = $_SESSION['user_id'];

  $allowedTypes = ['pdf', 'doc', 'docx', 'jpg', 'png', 'jpeg'];
  $fileName = $_FILES['file']['name'];
  $fileTmp  = $_FILES['file']['tmp_name'];
  $fileSize = $_FILES['file']['size'];
  $fileExt  = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

  if (!in_array($fileExt, $allowedTypes)) {
    $message = "<div class='error'>❌ Invalid file type.</div>";
  } elseif ($fileSize > 5 * 1024 * 1024) { // 5MB max
    $message = "<div class='error'>❌ File too large. Max 5MB.</div>";
  } else {
    $uploadDir = "assets/uploads/";
    $newFileName = time() . '_' . basename($fileName);
    $filePath = $uploadDir . $newFileName;

    if (move_uploaded_file($fileTmp, $filePath)) {
      $stmt = $pdo->prepare("INSERT INTO resources (title, subject, category, file_url, uploaded_by) VALUES (?, ?, ?, ?, ?)");
      $stmt->execute([$title, $subject, $category, $filePath, $userId]);
      $message = "<div class='success'>✅ File uploaded successfully!</div>";
    } else {
      $message = "<div class='error'>❌ Failed to upload file.</div>";
    }
  }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Upload Material | EduCore</title>
  <link rel="stylesheet" href="assets/style.css">
  <style>
    .upload-box {
      max-width: 500px;
      margin: 2rem auto;
      background: #ffffff;
      padding: 2rem;
      border-radius: 10px;
      box-shadow: 0 0 15px rgba(0,0,0,0.1);
    }
    .upload-box input,
    .upload-box select,
    .upload-box button {
      width: 100%;
      margin-bottom: 1rem;
    }
    .success {
      background: #e8f5e9;
      color: #2e7d32;
      padding: 1rem;
      border-radius: 5px;
      border: 1px solid #43a047;
      margin-top: 1rem;
    }
    .error {
      background: #ffebee;
      color: #c62828;
      padding: 1rem;
      border-radius: 5px;
      border: 1px solid #e53935;
      margin-top: 1rem;
    }
    .back {
      text-align: center;
      margin-top: 1rem;
    }
    .back a {
      text-decoration: none;
      color: #00695c;
    }
  </style>
</head>
<body>

<div class="upload-box">
  <h2>📁 Upload Study Material</h2>
  <form action="" method="POST" enctype="multipart/form-data">
    <input type="text" name="title" placeholder="Material Title" required>
    <input type="text" name="subject" placeholder="Subject (e.g. Mathematics)" required>
    <select name="category" required>
      <option value="Notes">Notes</option>
      <option value="Exams">Exams</option>
      <option value="Assignments">Assignments</option>
    </select>
    <input type="file" name="file" required>
    <button type="submit">Upload</button>
  </form>
  <?php echo $message; ?>
  <div class="back">
    <a href="dashboard.php">← Back to Dashboard</a>
  </div>
</div>

</body>
</html>