<?php
session_start();
require_once 'includes/config.php';
require_once 'includes/auth.php';

if ($_SESSION['user_role'] !== 'teacher') {
  echo "Access denied.";
  exit();
}

$success = $error = "";

// Handle marking form
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $submissionId = $_POST['submission_id'];
  $marks = (int)$_POST['marks'];
  $feedback = trim($_POST['feedback']);

  $stmt = $pdo->prepare("UPDATE submissions SET marks = ?, feedback = ?, marked_at = NOW() WHERE id = ?");
  if ($stmt->execute([$marks, $feedback, $submissionId])) {
    $success = "✅ Assignment marked successfully.";
  } else {
    $error = "❌ Failed to mark assignment.";
  }
}

// Fetch unmarked submissions
$stmt = $pdo->query("SELECT s.id AS submission_id, s.file_path, s.submitted_at, u.name AS student_name, a.title, a.subject
                     FROM submissions s
                     JOIN users u ON s.student_id = u.id
                     JOIN assignments a ON s.assignment_id = a.id
                     WHERE s.marks IS NULL
                     ORDER BY s.submitted_at DESC");
$submissions = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Mark Assignments | EduCore</title>
  <link rel="stylesheet" href="assets/style.css">
  <style>
    body {
      background: #fff3e0;
      font-family: 'Segoe UI', sans-serif;
    }
    .container {
      max-width: 1000px;
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
    .submission-card {
      background: #fbe9e7;
      padding: 1rem;
      border-left: 6px solid #ff5722;
      margin-bottom: 1.5rem;
      border-radius: 6px;
    }
    .submission-card h4 {
      margin: 0;
      color: #d84315;
    }
    .submission-card small {
      color: #777;
    }
    form textarea, form input[type=number], form button {
      width: 100%;
      margin-top: 0.5rem;
      margin-bottom: 1rem;
      padding: 0.6rem;
    }
    .success { background: #e8f5e9; color: #2e7d32; padding: 0.7rem; border-radius: 5px; margin-bottom: 1rem; }
    .error { background: #ffebee; color: #c62828; padding: 0.7rem; border-radius: 5px; margin-bottom: 1rem; }
    .back { text-align: center; margin-top: 1rem; }
    .back a { text-decoration: none; color: #e65100; }
  </style>
</head>
<body>
<div class="container">
  <h2>📝 Mark Student Assignments</h2>
  <?php if ($success) echo "<div class='success'>$success</div>"; ?>
  <?php if ($error) echo "<div class='error'>$error</div>"; ?>

  <?php if (count($submissions) === 0): ?>
    <p>No unmarked submissions available.</p>
  <?php else: ?>
    <?php foreach ($submissions as $s): ?>
      <div class="submission-card">
        <h4><?php echo htmlspecialchars($s['title']); ?> (<?php echo htmlspecialchars($s['subject']); ?>)</h4>
        <small>👤 Student: <?php echo htmlspecialchars($s['student_name']); ?> | Submitted on: <?php echo date('M d, Y', strtotime($s['submitted_at'])); ?></small><br>
        <a href="<?php echo $s['file_path']; ?>" target="_blank">📄 View Submission</a>

        <form method="POST">
          <input type="hidden" name="submission_id" value="<?php echo $s['submission_id']; ?>">
          <label for="marks">Score (out of 100):</label>
          <input type="number" name="marks" max="100" required>
          <label for="feedback">Feedback:</label>
          <textarea name="feedback" rows="3" placeholder="Enter feedback..." required></textarea>
          <button type="submit">✅ Submit Marks</button>
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
