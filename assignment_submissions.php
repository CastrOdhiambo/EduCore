<?php
session_start();
require_once 'includes/config.php';
require_once 'includes/auth.php';

if ($_SESSION['user_role'] !== 'teacher') {
  echo "Access denied.";
  exit();
}

// Fetch all submitted assignments
$stmt = $pdo->query("SELECT s.*, u.name AS student_name, a.title AS assignment_title, a.subject
                     FROM submissions s
                     JOIN users u ON s.student_id = u.id
                     JOIN assignments a ON s.assignment_id = a.id
                     ORDER BY s.submitted_at DESC");
$submissions = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>All Assignment Submissions | EduCore</title>
  <link rel="stylesheet" href="assets/style.css">
  <style>
    body {
      background: #e3f2fd;
      font-family: 'Segoe UI', sans-serif;
    }
    .container {
      max-width: 1100px;
      margin: 2rem auto;
      background: #ffffff;
      padding: 2rem;
      border-radius: 12px;
      box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    }
    h2 {
      text-align: center;
      color: #1565c0;
    }
    .submission {
      border-left: 6px solid #42a5f5;
      background: #f1f8ff;
      margin-bottom: 1rem;
      padding: 1rem;
      border-radius: 8px;
    }
    .submission h4 {
      margin: 0;
      color: #0d47a1;
    }
    .submission small {
      color: #666;
    }
    .submission a {
      display: inline-block;
      margin-top: 0.5rem;
      color: #1e88e5;
      text-decoration: none;
    }
    .submission .marks {
      font-weight: bold;
      color: #2e7d32;
    }
    .submission .feedback {
      background: #e8f5e9;
      padding: 0.4rem;
      margin-top: 0.5rem;
      border-left: 4px solid #66bb6a;
      border-radius: 4px;
    }
    .back {
      text-align: center;
      margin-top: 2rem;
    }
    .back a {
      color: #1565c0;
      text-decoration: none;
    }
  </style>
</head>
<body>
<div class="container">
  <h2>📂 All Assignment Submissions</h2>

  <?php if (count($submissions) === 0): ?>
    <p>No student submissions found.</p>
  <?php else: ?>
    <?php foreach ($submissions as $sub): ?>
      <div class="submission">
        <h4><?php echo htmlspecialchars($sub['assignment_title']); ?> (<?php echo htmlspecialchars($sub['subject']); ?>)</h4>
        <small>👤 Student: <?php echo htmlspecialchars($sub['student_name']); ?> | Submitted: <?php echo date('M d, Y - H:i', strtotime($sub['submitted_at'])); ?></small><br>
        <a href="<?php echo htmlspecialchars($sub['file_path']); ?>" target="_blank">📄 View Submission</a><br>

        <?php if ($sub['marks'] !== null): ?>
          <div class="marks">✅ Marks: <?php echo $sub['marks']; ?>/100</div>
          <?php if (!empty($sub['feedback'])): ?>
            <div class="feedback">
              📝 Feedback:<br>
              <?php echo nl2br(htmlspecialchars($sub['feedback'])); ?>
            </div>
          <?php endif; ?>
        <?php else: ?>
          <div style="color: #ef6c00;">🚫 Not yet marked</div>
        <?php endif; ?>
      </div>
    <?php endforeach; ?>
  <?php endif; ?>

  <div class="back">
    <a href="dashboard.php">← Back to Dashboard</a>
  </div>
</div>
</body>
</html>
