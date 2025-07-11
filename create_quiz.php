<?php
session_start();
require_once 'includes/config.php';
require_once 'includes/auth.php';

// Only teachers allowed
if ($_SESSION['user_role'] !== 'teacher') {
  die("Access denied.");
}

// Step 1: Handle initial quiz setup (title + subject)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['create_quiz'])) {
  $title = trim($_POST['title']);
  $subject = trim($_POST['subject']);

  if ($title && $subject) {
    $stmt = $pdo->prepare("INSERT INTO quizzes (title, subject, created_by) VALUES (?, ?, ?)");
    $stmt->execute([$title, $subject, $_SESSION['user_id']]);
    $quizId = $pdo->lastInsertId();
    $_SESSION['current_quiz_id'] = $quizId;
    $_SESSION['current_quiz_title'] = $title;
    $_SESSION['current_quiz_subject'] = $subject;
  }
}

// Step 2: Handle question addition
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_question'])) {
  $quizId = $_SESSION['current_quiz_id'] ?? null;
  $q = trim($_POST['question']);
  $a = trim($_POST['option_a']);
  $b = trim($_POST['option_b']);
  $c = trim($_POST['option_c']);
  $d = trim($_POST['option_d']);
  $correct = $_POST['correct'];

  if ($quizId && $q && $a && $b && $c && $d && $correct) {
    $stmt = $pdo->prepare("INSERT INTO quiz_questions 
      (quiz_id, question, option_a, option_b, option_c, option_d, correct_answer) 
      VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->execute([$quizId, $q, $a, $b, $c, $d, $correct]);
    $success = "✅ Question added.";
  } else {
    $error = "⚠️ Please fill all fields.";
  }
}

// Step 3: Reset session if needed
if (isset($_GET['reset'])) {
  unset($_SESSION['current_quiz_id'], $_SESSION['current_quiz_title'], $_SESSION['current_quiz_subject']);
  header('Location: create_quiz.php');
  exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Create Quiz | EduCore</title>
  <link rel="stylesheet" href="assets/style.css">
  <style>
    body {
      background: #e3f2fd;
      font-family: 'Segoe UI', sans-serif;
    }
    .container {
      max-width: 700px;
      margin: 2rem auto;
      background: #ffffff;
      padding: 2rem;
      border-radius: 12px;
      box-shadow: 0 3px 10px rgba(0,0,0,0.1);
    }
    h2 {
      text-align: center;
      color: #0277bd;
    }
    input, select, textarea, button {
      width: 100%;
      margin-bottom: 1rem;
      padding: 0.6rem;
    }
    .success {
      background: #e8f5e9;
      color: #2e7d32;
      padding: 0.6rem;
      border-left: 5px solid #43a047;
      margin-bottom: 1rem;
    }
    .error {
      background: #ffebee;
      color: #c62828;
      padding: 0.6rem;
      border-left: 5px solid #e53935;
      margin-bottom: 1rem;
    }
    .top-right {
      text-align: right;
      margin-bottom: 1rem;
    }
    .back a {
      text-decoration: none;
      color: #01579b;
    }
  </style>
</head>
<body>

<div class="container">
  <div class="top-right">
    <a href="?reset">🔄 Start New Quiz</a>
  </div>

  <?php if (!isset($_SESSION['current_quiz_id'])): ?>
    <h2>📘 Create New Quiz</h2>
    <form method="POST">
      <input type="text" name="title" placeholder="Quiz Title" required>
      <input type="text" name="subject" placeholder="Subject" required>
      <button type="submit" name="create_quiz">Start Adding Questions</button>
    </form>

  <?php else: ?>
    <h2>🧠 Add Questions to Quiz: <?= htmlspecialchars($_SESSION['current_quiz_title']); ?> (<?= htmlspecialchars($_SESSION['current_quiz_subject']); ?>)</h2>

    <?php if (!empty($success)) echo "<div class='success'>$success</div>"; ?>
    <?php if (!empty($error)) echo "<div class='error'>$error</div>"; ?>

    <form method="POST">
      <textarea name="question" rows="3" placeholder="Enter question..." required></textarea>
      <input type="text" name="option_a" placeholder="Option A" required>
      <input type="text" name="option_b" placeholder="Option B" required>
      <input type="text" name="option_c" placeholder="Option C" required>
      <input type="text" name="option_d" placeholder="Option D" required>
      <select name="correct" required>
        <option value="">-- Select Correct Option --</option>
        <option value="A">A</option>
        <option value="B">B</option>
        <option value="C">C</option>
        <option value="D">D</option>
      </select>
      <button type="submit" name="add_question">➕ Add Question</button>
    </form>
  <?php endif; ?>

  <div class="back">
    <a href="dashboard.php">← Back to Dashboard</a>
  </div>
</div>

</body>
</html>
