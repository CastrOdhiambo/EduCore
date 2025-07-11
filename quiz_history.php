<?php
session_start();
require_once 'includes/config.php';
require_once 'includes/auth.php';

// Make sure only students access this page
if ($_SESSION['user_role'] !== 'student') {
  header('Location: dashboard.php');
  exit();
}

$userId = $_SESSION['user_id'];

// Fetch quiz results
$stmt = $pdo->prepare("
  SELECT q.title, q.subject, qr.score, qr.total, qr.submitted_at
  FROM quiz_results qr
  JOIN quizzes q ON qr.quiz_id = q.id
  WHERE qr.user_id = ?
  ORDER BY qr.submitted_at DESC
");
$stmt->execute([$userId]);
$results = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>My Quiz History | EduCore</title>
  <link rel="stylesheet" href="assets/style.css">
  <style>
    body {
      font-family: 'Segoe UI', sans-serif;
      background: #e8f5e9;
      margin: 0;
      padding: 0;
    }
    .container {
      max-width: 900px;
      margin: 2rem auto;
      background: #ffffff;
      padding: 2rem;
      border-radius: 10px;
      box-shadow: 0 4px 10px rgba(0,0,0,0.1);
    }
    h2 {
      text-align: center;
      color: #2e7d32;
    }
    .quiz-card {
      border-left: 6px solid #66bb6a;
      padding: 1rem;
      margin-bottom: 1rem;
      border-radius: 6px;
      background: #f1f8e9;
    }
    .quiz-card h4 {
      margin: 0;
      color: #33691e;
    }
    .quiz-card small {
      color: #757575;
    }
    .score {
      font-weight: bold;
      color: #2e7d32;
      font-size: 1.1rem;
    }
    .back {
      text-align: center;
      margin-top: 2rem;
    }
    .back a {
      text-decoration: none;
      color: #388e3c;
      font-weight: bold;
    }
  </style>
</head>
<body>

<div class="container">
  <h2>📜 My Quiz History</h2>

  <?php if (count($results) === 0): ?>
    <p>No quiz attempts yet.</p>
  <?php else: ?>
    <?php foreach ($results as $quiz): ?>
      <div class="quiz-card">
        <h4><?php echo htmlspecialchars($quiz['title']); ?> (<?php echo htmlspecialchars($quiz['subject']); ?>)</h4>
        <small>Attempted on: <?php echo date('M d, Y h:i A', strtotime($quiz['submitted_at'])); ?></small><br>
        <span class="score">Score: <?php echo $quiz['score']; ?>/<?php echo $quiz['total']; ?></span>
      </div>
    <?php endforeach; ?>
  <?php endif; ?>

  <div class="back">
    <a href="dashboard.php">← Back to Dashboard</a>
  </div>
</div>

</body>
</html>
