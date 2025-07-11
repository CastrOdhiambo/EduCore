<?php
session_start();
require_once 'includes/config.php';
require_once 'includes/auth.php';

// Only students can access this page
if ($_SESSION['user_role'] !== 'student') {
  header('Location: dashboard.php');
  exit();
}

$studentId = $_SESSION['user_id'];
$stmt = $pdo->prepare("SELECT * FROM submissions WHERE student_id = ? AND marks IS NOT NULL ORDER BY marked_at DESC");
$stmt->execute([$studentId]);
$results = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>My Assignment Results | EduCore</title>
  <link rel="stylesheet" href="assets/style.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <style>
    body {
      background: #ede7f6;
      font-family: 'Segoe UI', sans-serif;
      margin: 0;
      padding: 0;
    }
    .container {
      max-width: 1000px;
      margin: 2rem auto;
      background: #ffffff;
      padding: 2rem;
      border-radius: 12px;
      box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    }
    h2 {
      text-align: center;
      color: #6a1b9a;
    }
    .result-card {
      background: #f8f5fc;
      padding: 1.5rem;
      border-left: 6px solid #8e24aa;
      margin-bottom: 1.5rem;
      border-radius: 8px;
      transition: transform 0.2s;
    }
    .result-card:hover {
      transform: translateY(-3px);
      box-shadow: 0 2px 10px rgba(0,0,0,0.08);
    }
    .result-card h4 {
      margin: 0;
      font-size: 1.2rem;
      color: #4a148c;
    }
    .result-card small {
      color: #777;
    }
    .score {
      font-size: 1.1rem;
      font-weight: bold;
      margin-top: 0.5rem;
      color: #1b5e20;
    }
    .feedback {
      background: #ede7f6;
      padding: 0.75rem;
      margin-top: 1rem;
      border-left: 4px solid #7e57c2;
      border-radius: 6px;
      color: #4a148c;
    }
    .icon-label {
      font-weight: bold;
      color: #6a1b9a;
    }
    .back {
      text-align: center;
      margin-top: 2rem;
    }
    .back a {
      color: #4a148c;
      text-decoration: none;
    }
    @media (max-width: 600px) {
      .container {
        padding: 1rem;
      }
      .result-card {
        padding: 1rem;
      }
    }
  </style>
</head>
<body>

<div class="container">
  <h2>📊 My Assignment Results</h2>
  <p style="text-align:center; color:#555">Here are all your marked assignments with feedback.</p>

  <?php if (count($results) === 0): ?>
    <p style="text-align:center;">No graded results available yet. Please check back later.</p>
  <?php else: ?>
    <?php foreach ($results as $res): ?>
      <div class="result-card">
        <h4><i class="fas fa-file-alt"></i> <?php echo htmlspecialchars($res['title']); ?> <small>(<?php echo htmlspecialchars($res['subject']); ?>)</small></h4>
        <small><i class="fas fa-calendar-check"></i> Marked on: <?php echo date('M d, Y', strtotime($res['marked_at'])); ?></small><br>
        <span class="score"><i class="fas fa-star"></i> Score: <?php echo $res['marks']; ?>/100</span>

        <?php if (!empty($res['feedback'])): ?>
          <div class="feedback">
            📝 <strong>Teacher Feedback:</strong><br>
            <?php echo nl2br(htmlspecialchars($res['feedback'])); ?>
          </div>
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