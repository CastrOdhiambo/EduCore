<?php
session_start();
require_once 'includes/config.php';
require_once 'includes/auth.php';

$role = $_SESSION['role'] ?? null;
if (!$role || ($role !== 'admin' && $role !== 'teacher')) {
  die("Access denied. Only teachers or admins can access this page.");
}

$students = $pdo->query("SELECT id, name FROM users WHERE role = 'student'")->fetchAll();
$success = $error = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $studentId = $_POST['student_id'] ?? null;
  $subject = trim($_POST['subject']);
  $score = (int)$_POST['score'];

  if (!$studentId || !$subject || $score === null) {
    $error = "❌ All fields are required.";
  } else {
    $check = $pdo->prepare("SELECT * FROM results WHERE user_id = ? AND subject = ?");
    $check->execute([$studentId, $subject]);
    if ($check->rowCount() > 0) {
      $update = $pdo->prepare("UPDATE results SET score = ? WHERE user_id = ? AND subject = ?");
      $update->execute([$score, $studentId, $subject]);
      $success = "✅ Result updated successfully.";
    } else {
      $insert = $pdo->prepare("INSERT INTO results (user_id, subject, score) VALUES (?, ?, ?)");
      $insert->execute([$studentId, $subject, $score]);
      $success = "✅ Result added successfully.";
    }
  }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Enter Results | EduCore</title>
  <link rel="stylesheet" href="assets/style.css">
  <style>
    body {
      background: #f0f4f8;
      font-family: 'Segoe UI', sans-serif;
    }
    .container {
      max-width: 700px;
      margin: 2rem auto;
      background: #fff;
      padding: 2rem;
      border-radius: 8px;
      box-shadow: 0 2px 6px rgba(0,0,0,0.08);
    }
    h2 {
      text-align: center;
      color: #2c3e50;
    }
    form input, form select, form button {
      width: 100%;
      padding: 0.7rem;
      margin-bottom: 1rem;
      border: 1px solid #ccc;
      border-radius: 6px;
    }
    .success, .error {
      padding: 0.8rem;
      border-radius: 6px;
      margin-bottom: 1rem;
    }
    .success { background: #e8f5e9; color: #2e7d32; }
    .error { background: #ffebee; color: #c62828; }
    .back {
      text-align: center;
      margin-top: 1rem;
    }
    .back a {
      color: #1565c0;
      text-decoration: none;
    }
  </style>
</head>
<body>
<div class="container">
  <h2>📋 Enter/Update Student Results</h2>
  <?php if ($success) echo "<div class='success'>$success</div>"; ?>
  <?php if ($error) echo "<div class='error'>$error</div>"; ?>
  <form method="POST">
    <select name="student_id" required>
      <option value="">-- Select Student --</option>
      <?php foreach ($students as $stu): ?>
        <option value="<?php echo $stu['id']; ?>"><?php echo htmlspecialchars($stu['name']); ?></option>
      <?php endforeach; ?>
    </select>
    <input type="text" name="subject" placeholder="Subject" required>
    <input type="number" name="score" min="0" max="100" placeholder="Score" required>
    <button type="submit">✅ Save Result</button>
  </form>
  <div class="back">
    <a href="dashboard.php">← Back to Dashboard</a>
  </div>
</div>
</body>
</html>