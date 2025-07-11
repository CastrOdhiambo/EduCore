<?php
session_start();
require_once 'includes/config.php';
require_once 'includes/auth.php';

$userId = $_SESSION['user_id'];

// Fetch current user info
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$userId]);
$user = $stmt->fetch();

$success = $error = "";

// Handle profile update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $name = trim($_POST['name']);
  $class = trim($_POST['class']);
  $gender = $_POST['gender'];

  // Optional password change
  $newPassword = $_POST['new_password'];
  if (!empty($newPassword)) {
    $hashed = password_hash($newPassword, PASSWORD_DEFAULT);
    $stmt = $pdo->prepare("UPDATE users SET name = ?, class = ?, gender = ?, password = ? WHERE id = ?");
    $stmt->execute([$name, $class, $gender, $hashed, $userId]);
  } else {
    $stmt = $pdo->prepare("UPDATE users SET name = ?, class = ?, gender = ? WHERE id = ?");
    $stmt->execute([$name, $class, $gender, $userId]);
  }

  $success = "✅ Profile updated successfully.";
  $_SESSION['user_name'] = $name; // update session name

  // Refresh user data
  $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
  $stmt->execute([$userId]);
  $user = $stmt->fetch();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>My Profile | EduCore</title>
  <link rel="stylesheet" href="assets/style.css">
  <style>
    .profile-container {
      max-width: 600px;
      margin: 2rem auto;
      background: #e3f2fd;
      padding: 2rem;
      border-radius: 10px;
      box-shadow: 0 2px 6px rgba(0,0,0,0.08);
    }
    .profile-container h2 {
      text-align: center;
      color: #0277bd;
    }
    form input, form select, form button {
      width: 100%;
      margin-bottom: 1rem;
      padding: 0.6rem;
    }
    .success {
      background: #e8f5e9;
      padding: 0.6rem;
      border: 1px solid #43a047;
      color: #2e7d32;
      border-radius: 5px;
      margin-bottom: 1rem;
    }
    .error {
      background: #ffebee;
      padding: 0.6rem;
      border: 1px solid #e53935;
      color: #c62828;
      border-radius: 5px;
      margin-bottom: 1rem;
    }
    .back {
      text-align: center;
    }
    .back a {
      text-decoration: none;
      color: #01579b;
    }
  </style>
</head>
<body>

<div class="profile-container">
  <h2>👤 My Profile</h2>
  <?php if ($success) echo "<div class='success'>$success</div>"; ?>
  <?php if ($error) echo "<div class='error'>$error</div>"; ?>

  <form method="POST">
    <input type="text" name="name" value="<?php echo htmlspecialchars($user['name']); ?>" placeholder="Full Name" required>
    <input type="text" name="class" value="<?php echo htmlspecialchars($user['class']); ?>" placeholder="Class">
    <select name="gender" required>
      <option value="Male" <?php if ($user['gender'] === 'Male') echo 'selected'; ?>>Male</option>
      <option value="Female" <?php if ($user['gender'] === 'Female') echo 'selected'; ?>>Female</option>
    </select>
    <input type="password" name="new_password" placeholder="New Password (leave blank to keep current)">
    <button type="submit">Update Profile</button>
  </form>

  <div class="back">
    <a href="dashboard.php">← Back to Dashboard</a>
  </div>
</div>

</body>
</html>