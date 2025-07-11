<?php
session_start();
require_once 'includes/config.php';
require_once 'includes/auth.php';

$name = $_SESSION['user_name'] ?? 'Educator';
$role = $_SESSION['user_role'] ?? 'guest';
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Dashboard | EduCore</title>
  <link rel="stylesheet" href="assets/style.css">
  <style>
    body {
      background: #f0f4f8;
      font-family: 'Segoe UI', sans-serif;
    }
    .dashboard {
      max-width: 1080px;
      margin: 2rem auto;
      padding: 2rem;
      background: #ffffff;
      border-radius: 12px;
      box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    }
    h2 {
      text-align: center;
      color: #0d47a1;
      font-size: 2rem;
      margin-bottom: 1.5rem;
    }
    .welcome {
      text-align: center;
      margin-bottom: 2rem;
      font-size: 1.2rem;
      color: #333;
    }
    .card-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
      gap: 1.5rem;
    }
    .card {
      background: #e3f2fd;
      border-left: 6px solid #42a5f5;
      padding: 1.4rem;
      border-radius: 10px;
      text-align: center;
      transition: 0.3s;
      text-decoration: none;
      color: #0d47a1;
      box-shadow: 0 2px 6px rgba(0,0,0,0.08);
    }
    .card:hover {
      transform: translateY(-5px);
      background: #bbdefb;
    }
    .card span {
      font-size: 2rem;
      display: block;
      margin-bottom: 0.5rem;
    }
    .section-title {
      font-size: 1.4rem;
      color: #1565c0;
      margin: 2rem 0 1rem;
      border-bottom: 2px solid #90caf9;
      padding-bottom: 0.3rem;
    }
  </style>
</head>
<body>
  <div class="dashboard">
    <h2>📊 EduCore Dashboard</h2>
    <div class="welcome">
      Welcome, <strong><?php echo htmlspecialchars($name); ?></strong> 👋<br>
      Role: <strong><?php echo ucfirst($role); ?></strong>
    </div>

    <?php if ($role === 'student'): ?>
      <div class="section-title">🎓 Student Tools</div>
      <div class="card-grid">
        <a href="resources.php" class="card"><span>📚</span>View Resources</a>
        <a href="submit_assignment.php" class="card"><span>📝</span>Submit Assignment</a>
        <a href="view_results.php" class="card"><span>📈</span>View Results</a>
        <a href="quiz.php" class="card"><span>🧠</span>Take Quiz</a>
        <a href="quiz_history.php" class="card"><span>📜</span>Quiz History</a>
        <a href="profile.php" class="card"><span>👤</span>My Profile</a>
        <a href="logout.php" class="card"><span>🚪</span>Logout</a>
      </div>
    <?php elseif ($role === 'teacher' || $role === 'admin'): ?>
      <div class="section-title">👨‍🏫 Teacher/Admin Tools</div>
      <div class="card-grid">
        <a href="upload.php" class="card"><span>📤</span>Upload Resources</a>
        <a href="create_assignment.php" class="card"><span>🗂️</span>Create Assignment</a>
        <a href="mark_assignments.php" class="card"><span>✅</span>Mark Assignments</a>
        <a href="admin_enter_results.php" class="card"><span>📋</span>Enter Results</a>
        <a href="create_quiz.php" class="card"><span>➕</span>Create Quiz</a>
        <a href="quiz_history.php" class="card"><span>📜</span>Quiz Submissions</a>
        <a href="assignment_submissions.php" class="card"><span>📜</span>Assignment Submissions</a>
        <a href="profile.php" class="card"><span>👤</span>My Profile</a>
        <a href="logout.php" class="card"><span>🚪</span>Logout</a>
      </div>
    <?php else: ?>
      <div class="welcome">Access Denied. Please log in with the correct role.</div>
    <?php endif; ?>
  </div>
</body>
</html>
