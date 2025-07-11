<?php
session_start();
require_once 'includes/config.php';
require_once 'includes/auth.php';

// Only students allowed
if ($_SESSION['user_role'] !== 'student') {
  header('Location: dashboard.php');
  exit();
}

$userId = $_SESSION['user_id'];
$selectedQuizId = $_GET['quiz_id'] ?? null;
$submitted = false;
$score = 0;
$total = 0;
$quizTitle = "";
$questions = [];

if ($selectedQuizId) {
  // Fetch quiz title
  $stmt = $pdo->prepare("SELECT * FROM quizzes WHERE id = ?");
  $stmt->execute([$selectedQuizId]);
  $quiz = $stmt->fetch();

  if ($quiz) {
    $quizTitle = $quiz['title'];

    // Fetch questions
    $stmt = $pdo->prepare("SELECT * FROM quiz_questions WHERE quiz_id = ?");
    $stmt->execute([$selectedQuizId]);
    $questions = $stmt->fetchAll();
  }

  // Handle submission
  if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $submitted = true;
    foreach ($questions as $q) {
      $qid = $q['id'];
      $userAnswer = $_POST['answer_' . $qid] ?? '';
      if (strtoupper($userAnswer) === strtoupper($q['correct_answer'])) {
        $score++;
      }
      $total++;
    }

    // Save to quiz_results
    $stmt = $pdo->prepare("INSERT INTO quiz_results (user_id, quiz_id, score, total) VALUES (?, ?, ?, ?)");
    $stmt->execute([$userId, $selectedQuizId, $score, $total]);
  }
}

// Fetch list of all quizzes
$allQuizzes = $pdo->query("SELECT * FROM quizzes ORDER BY created_at DESC")->fetchAll();

// Fetch quiz history
$stmt = $pdo->prepare("
  SELECT q.title, qr.score, qr.total, qr.submitted_at 
  FROM quiz_results qr
  JOIN quizzes q ON qr.quiz_id = q.id
  WHERE qr.user_id = ?
  ORDER BY qr.submitted_at DESC
");
$stmt->execute([$userId]);
$history = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Take Quiz | EduCore</title>
  <link rel="stylesheet" href="assets/style.css">
  <style>
    body { background: #fffde7; font-family: 'Segoe UI', sans-serif; }
    .container { max-width: 900px; margin: 2rem auto; background: #ffffff; padding: 2rem; border-radius: 10px; box-shadow: 0 4px 10px rgba(0,0,0,0.1); }
    h2 { text-align: center; color: #f57f17; }
    select, button, input[type=radio] { margin: 0.5rem 0; }
    .quiz-card { background: #fff8e1; padding: 1rem; margin-bottom: 1rem; border-left: 6px solid #fbc02d; border-radius: 6px; }
    .question { font-weight: bold; }
    .result-box { background: #e8f5e9; border: 1px solid #43a047; padding: 1rem; border-radius: 6px; color: #2e7d32; }
    .history-card { background: #f3e5f5; border-left: 6px solid #8e24aa; margin: 1rem 0; padding: 1rem; border-radius: 6px; }
  </style>
</head>
<body>
<div class="container">
  <h2>🧠 Interactive Quizzes</h2>

  <!-- Select Quiz -->
  <form method="GET">
    <label for="quiz_id">Select a Quiz:</label><br>
    <select name="quiz_id" id="quiz_id" required>
      <option value="">-- Choose a quiz --</option>
      <?php foreach ($allQuizzes as $q): ?>
        <option value="<?= $q['id'] ?>" <?= ($selectedQuizId == $q['id']) ? 'selected' : '' ?>>
          <?= htmlspecialchars($q['title']) ?> (<?= $q['subject'] ?>)
        </option>
      <?php endforeach; ?>
    </select>
    <button type="submit">🎯 Load Quiz</button>
  </form>

  <?php if ($selectedQuizId && $quizTitle): ?>
    <hr>
    <h3><?= htmlspecialchars($quizTitle) ?></h3>

    <?php if ($submitted): ?>
      <div class="result-box">
        ✅ You scored <strong><?= $score ?>/<?= $total ?></strong> on this quiz.
      </div>
    <?php endif; ?>

    <form method="POST">
      <?php if (count($questions) > 0): ?>
        <?php foreach ($questions as $q): ?>
          <div class="quiz-card">
            <div class="question"><?= htmlspecialchars($q['question']) ?></div>
            <label><input type="radio" name="answer_<?= $q['id'] ?>" value="A" required> A. <?= htmlspecialchars($q['option_a']) ?></label><br>
            <label><input type="radio" name="answer_<?= $q['id'] ?>" value="B"> B. <?= htmlspecialchars($q['option_b']) ?></label><br>
            <label><input type="radio" name="answer_<?= $q['id'] ?>" value="C"> C. <?= htmlspecialchars($q['option_c']) ?></label><br>
            <label><input type="radio" name="answer_<?= $q['id'] ?>" value="D"> D. <?= htmlspecialchars($q['option_d']) ?></label>
          </div>
        <?php endforeach; ?>
        <button type="submit">Submit Quiz</button>
      <?php else: ?>
        <p>No questions found for this quiz.</p>
      <?php endif; ?>
    </form>
  <?php endif; ?>

  <hr>
  <h3>📜 Quiz History</h3>
  <?php if (count($history) === 0): ?>
    <p>No quiz attempts yet.</p>
  <?php else: ?>
    <?php foreach ($history as $h): ?>
      <div class="history-card">
        <strong><?= htmlspecialchars($h['title']) ?></strong><br>
        Score: <?= $h['score'] ?>/<?= $h['total'] ?><br>
        Attempted: <?= date('M d, Y h:i A', strtotime($h['submitted_at'])) ?>
      </div>
    <?php endforeach; ?>
  <?php endif; ?>

  <div class="back" style="text-align:center; margin-top: 2rem;">
    <a href="dashboard.php">← Back to Dashboard</a>
  </div>
</div>
</body>
</html>
