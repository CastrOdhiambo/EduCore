<?php
// includes/auth.php
if (session_status() === PHP_SESSION_NONE) {
  session_start();
}

// Redirect if user is not logged in
if (!isset($_SESSION['user_id'])) {
  header('Location: login.php');
  exit();
}

// Optionally block users with missing roles
if (!isset($_SESSION['user_role'])) {
  echo "❌ User role not set. Contact admin.";
  exit();
}