<?php
require_once 'includes/config.php';
session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Register | EduCore</title>
  <link rel="stylesheet" href="assets/style.css">
  <style>
    .message {
      max-width: 400px;
      margin: 2rem auto;
      padding: 1.5rem;
      border-radius: 8px;
      text-align: center;
    }
    .success {
      background: #e0f2f1;
      color: #00695c;
      border: 1px solid #00796b;
    }
    .error {
      background: #ffebee;
      color: #c62828;
      border: 1px solid #d32f2f;
    }
  </style>
</head>
<body>

<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $name     = trim($_POST['name']);
  $email    = trim($_POST['email']);
  $password = $_POST['password'];
  $role     = $_POST['role'];
  $class    = $_POST['class'];
  $gender   = $_POST['gender'];

  if (empty($name) || empty($email) || empty($password) || empty($role)) {
    echo '<div class="message error">Please fill in all required fields.</div>';
    exit;
  }

  $hashed = password_hash($password, PASSWORD_DEFAULT);

  $stmt = $pdo->prepare("INSERT INTO users (name, email, password, role, class, gender)
                         VALUES (?, ?, ?, ?, ?, ?)");

  try {
    $stmt->execute([$name, $email, $hashed, $role, $class, $gender]);

    // Retrieve user and start session
    $userId = $pdo->lastInsertId();
    $_SESSION['user_id']   = $userId;
    $_SESSION['user_name'] = $name;
    $_SESSION['user_role'] = $role;

    echo '<div class="message success">✅ Registration successful! Redirecting to your dashboard...</div>';

    echo '<script>
      setTimeout(() => {
        window.location.href = "dashboard.php";
      }, 2000);
    </script>';

  } catch (PDOException $e) {
    if ($e->errorInfo[1] == 1062) {
      echo '<div class="message error">❌ This email is already registered.</div>';
    } else {
      echo '<div class="message error">❌ Error: ' . $e->getMessage() . '</div>';
    }
  }
} else {
  echo '<div class="message error">⚠️ Invalid request method.</div>';
}
?>

</body>
</html>
