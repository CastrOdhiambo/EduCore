<?php
session_start();
require_once 'includes/config.php';
require_once 'includes/auth.php';

// Only students can access
if ($_SESSION['user_role'] !== 'student') {
  echo "Access denied.";
  exit();
}

$studentId = $_SESSION['user_id'];
$success = $error = "";

// Handle submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $assignment_id = $_POST['assignment_id'];
  $file_path = "";

  if (!empty($_FILES['submission']['name'])) {
    $targetDir = "submissions/";
    $fileName = time() . "_" . basename($_FILES['submission']['name']);
    $file_path = $targetDir . $fileName;
    move_uploaded_file($_FILES['submission']['tmp_name'], $file_path);

    $stmt = $pdo->prepare("INSERT INTO submissions (assignment_id, student_id, file_path, submitted_at) VALUES (?, ?, ?, NOW())");
    $stmt->execute([$assignment_id, $studentId, $file_path]);
    $success = "✅ Assignment submitted successfully.";
  } else {
    $error = "⚠️ Please attach your assignment file.";
  }
}

// Fetch all active assignments
$stmt = $pdo->query("SELECT * FROM assignments ORDER BY due_date DESC");
$assignments = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Submit Assignment | EduCore</title>
  <link rel="stylesheet" href="assets/style.css">
  <style>
    body { background: #f0f4c3; font-family: 'Segoe UI', sans-serif; }
    .container { max-width: 900px; margin: 2rem auto; background: #fff; padding: 2rem; border-radius: 12px; box-shadow: 0 4px 10px rgba(0,0,0,0.1); }
    h2 { text-align: center; color: #827717; }
    .assignment-card { padding: 1rem; border-left: 6px solid #c0ca33; margin-bottom: 1.5rem; border-radius: 6px; background: #fafafa; }
    .assignment-card h4 { margin: 0; color: #827717; }
    .assignment-card small { color: #555; }
    form input[type=file], form select, form button {
      width: 100%;
      padding: 0.6rem;
      margin-top: 0.5rem;
      margin-bottom: 1rem;
    }
    .success { background: #e8f5e9; color: #2e7d32; padding: 0.7rem; border-radius: 5px; margin-bottom: 1rem; }
    .error { background: #ffebee; color: #c62828; padding: 0.7rem; border-radius: 5px; margin-bottom: 1rem; }
    .back { text-align: center; margin-top: 1rem; }
    .back a { text-decoration: none; color: #827717; }
  </style>
</head>
<body>
<div class="container">
  <h2>📝 Submit Assignment</h2>
  <?php if ($success) echo "<div class='success'>$success</div>"; ?>
  <?php if ($error) echo "<div class='error'>$error</div>"; ?>

  <?php if (count($assignments) === 0): ?>
    <p>No assignments available.</p>
  <?php else: ?>
    <?php foreach ($assignments as $a): ?>
      <div class="assignment-card">
        <h4><?php echo htmlspecialchars($a['title']); ?> (<?php echo htmlspecialchars($a['subject']); ?>)</h4>
        <small>Due: <?php echo date('M d, Y', strtotime($a['due_date'])); ?></small><br>
        <?php if (!empty($a['description'])): ?>
          <p><?php echo nl2br(htmlspecialchars($a['description'])); ?></p>
        <?php endif; ?>
        <?php if (!empty($a['file_path'])): ?>
          <a href="<?php echo $a['file_path']; ?>" download>📄 Download Assignment</a>
        <?php endif; ?>
        <form method="POST" enctype="multipart/form-data">
          <input type="hidden" name="assignment_id" value="<?php echo $a['id']; ?>">
          <label>📤 Upload Your Work</label>
          <input type="file" name="submission" required>
          <button type="submit">Submit</button>
        </form>
      </div>
    <?php endforeach; ?>
  <?php endif; ?>

  <div class="back">
    <a href="dashboard.php">← Back to Dashboard</a>
  </div>
</div>
</body>
</html>
