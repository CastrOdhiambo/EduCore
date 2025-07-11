<?php
session_start();
require_once 'includes/config.php';
require_once 'includes/auth.php';

// Fetch all resources
$category = $_GET['category'] ?? '';
$search = $_GET['search'] ?? '';

$query = "SELECT * FROM resources WHERE 1";
$params = [];

if ($category) {
  $query .= " AND category = ?";
  $params[] = $category;
}

if ($search) {
  $query .= " AND (title LIKE ? OR description LIKE ?)";
  $params[] = "%$search%";
  $params[] = "%$search%";
}

$query .= " ORDER BY uploaded_at DESC";
$stmt = $pdo->prepare($query);
$stmt->execute($params);
$resources = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Resources | EduCore</title>
  <link rel="stylesheet" href="assets/style.css">
  <style>
    body {
      background: #f4f6f8;
      font-family: 'Segoe UI', sans-serif;
    }
    .container {
      max-width: 1000px;
      margin: 2rem auto;
      background: #ffffff;
      padding: 2rem;
      border-radius: 10px;
      box-shadow: 0 4px 10px rgba(0,0,0,0.05);
    }
    h2 {
      text-align: center;
      color: #2c3e50;
    }
    .filters {
      display: flex;
      justify-content: space-between;
      gap: 1rem;
      margin-bottom: 1rem;
    }
    .filters input, .filters select {
      padding: 0.5rem;
      width: 100%;
    }
    .resource-list {
      display: grid;
      grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
      gap: 1.5rem;
    }
    .card {
      background: #ecf0f1;
      padding: 1rem;
      border-radius: 8px;
      box-shadow: 0 2px 6px rgba(0,0,0,0.08);
      transition: 0.3s;
    }
    .card:hover {
      background: #d0ece7;
    }
    .card h4 {
      margin-top: 0;
      color: #2980b9;
    }
    .card p {
      font-size: 0.9rem;
      color: #555;
    }
    .card a.download {
      display: inline-block;
      margin-top: 0.5rem;
      text-decoration: none;
      background: #3498db;
      color: #fff;
      padding: 0.5rem 1rem;
      border-radius: 5px;
    }
    .card a.download:hover {
      background: #2e86c1;
    }
  </style>
</head>
<body>
<div class="container">
  <h2>📚 Educational Resources</h2>

  <form method="GET" class="filters">
    <select name="category">
      <option value="">All Categories</option>
      <option value="Notes" <?php if($category==='Notes') echo 'selected'; ?>>Notes</option>
      <option value="Assignments" <?php if($category==='Assignments') echo 'selected'; ?>>Assignments</option>
      <option value="Exams" <?php if($category==='Exams') echo 'selected'; ?>>Exams</option>
    </select>
    <input type="text" name="search" value="<?php echo htmlspecialchars($search); ?>" placeholder="Search by title or keyword...">
    <button type="submit">🔍 Filter</button>
  </form>

  <div class="resource-list">
    <?php if (count($resources) > 0): ?>
      <?php foreach ($resources as $res): ?>
        <div class="card">
          <h4><?php echo htmlspecialchars($res['title']); ?></h4>
          <p><?php echo htmlspecialchars($res['description']); ?></p>
          <p><strong>Category:</strong> <?php echo htmlspecialchars($res['category']); ?></p>
          <p><strong>Uploaded:</strong> <?php echo date('d M Y', strtotime($res['uploaded_at'])); ?></p>
          <a href="uploads/<?php echo htmlspecialchars($res['file_path']); ?>" class="download" download>⬇ Download</a>
        </div>
      <?php endforeach; ?>
    <?php else: ?>
      <p>No resources found matching your filter.</p>
    <?php endif; ?>
  </div>
</div>
</body>
</html>
