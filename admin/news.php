<?php
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/../includes/functions.php';

require_login();

$message = '';
$error = '';

// Load existing data
$news = load_data('news');

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'save') {
        $id = $_POST['id'] ?? null;
        $item = [
            'title' => trim($_POST['title'] ?? ''),
            'slug' => generate_slug($_POST['title'] ?? ''),
            'date' => $_POST['date'] ?? date('Y-m-d'),
            'featured' => isset($_POST['featured']),
            'author' => trim($_POST['author'] ?? ''),
            'excerpt' => trim($_POST['excerpt'] ?? ''),
            'body' => trim($_POST['body'] ?? '')
        ];

        if (empty($item['title'])) {
            $error = 'Title is required';
        } else {
            if ($id) {
                // Update existing
                foreach ($news as &$n) {
                    if ($n['id'] == $id) {
                        $item['id'] = (int)$id;
                        $n = $item;
                        break;
                    }
                }
                $message = 'News updated successfully';
            } else {
                // Add new
                $maxId = 0;
                foreach ($news as $n) {
                    if (($n['id'] ?? 0) > $maxId) $maxId = $n['id'];
                }
                $item['id'] = $maxId + 1;
                $news[] = $item;
                $message = 'News created successfully';
            }
            save_data('news', $news);
            $news = load_data('news'); // Reload
        }
    }

    if ($action === 'delete' && isset($_POST['id'])) {
        $id = (int)$_POST['id'];
        $news = array_filter($news, fn($n) => ($n['id'] ?? 0) != $id);
        $news = array_values($news);
        save_data('news', $news);
        $message = 'News deleted successfully';
    }
}

// Get item to edit
$editing = null;
if (isset($_GET['edit'])) {
    foreach ($news as $n) {
        if (($n['id'] ?? 0) == $_GET['edit']) {
            $editing = $n;
            break;
        }
    }
}

// Sort by date desc
usort($news, fn($a, $b) => strtotime($b['date'] ?? 0) - strtotime($a['date'] ?? 0));
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>News | All Saints Admin</title>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Oswald:wght@400;500;600;700&display=swap" rel="stylesheet">
  <style>
    * { box-sizing: border-box; margin: 0; padding: 0; }
    body { font-family: 'Inter', sans-serif; background: #f5f5f5; min-height: 100vh; }

    .header {
      background: #1a1f3c;
      color: white;
      padding: 16px 24px;
      display: flex;
      justify-content: space-between;
      align-items: center;
    }
    .header h1 {
      font-family: 'Oswald', sans-serif;
      font-size: 1.25rem;
      text-transform: uppercase;
      letter-spacing: 0.05em;
    }
    .header a {
      color: white;
      text-decoration: none;
      padding: 8px 16px;
      background: rgba(255,255,255,0.1);
      border-radius: 6px;
      font-size: 0.9rem;
    }
    .header a:hover { background: rgba(255,255,255,0.2); }

    .container {
      max-width: 1000px;
      margin: 0 auto;
      padding: 24px;
    }

    .page-header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 24px;
    }
    .page-header h2 {
      font-family: 'Oswald', sans-serif;
      font-size: 1.5rem;
      text-transform: uppercase;
      letter-spacing: 0.05em;
      color: #1a1f3c;
    }

    .btn {
      padding: 10px 20px;
      background: #1a1f3c;
      color: white;
      border: none;
      border-radius: 6px;
      font-family: 'Oswald', sans-serif;
      text-transform: uppercase;
      letter-spacing: 0.05em;
      cursor: pointer;
      text-decoration: none;
      font-size: 0.9rem;
    }
    .btn:hover { background: #2d2d4a; }
    .btn-secondary {
      background: white;
      color: #1a1f3c;
      border: 2px solid #e0e0e0;
    }
    .btn-secondary:hover { border-color: #c9a227; }
    .btn-danger { background: #dc3545; }
    .btn-danger:hover { background: #c82333; }

    .message {
      padding: 12px 16px;
      border-radius: 8px;
      margin-bottom: 20px;
    }
    .message.success { background: #d4edda; color: #155724; }
    .message.error { background: #f8d7da; color: #721c24; }

    .card {
      background: white;
      border-radius: 12px;
      box-shadow: 0 2px 8px rgba(0,0,0,0.08);
      overflow: hidden;
    }

    .item-list { list-style: none; }
    .item {
      padding: 16px 20px;
      border-bottom: 1px solid #eee;
      display: flex;
      justify-content: space-between;
      align-items: center;
    }
    .item:last-child { border-bottom: none; }
    .item-info h3 {
      font-size: 1rem;
      color: #1a1f3c;
      margin-bottom: 4px;
    }
    .item-info p {
      font-size: 0.85rem;
      color: #666;
    }
    .item-info .badge {
      display: inline-block;
      background: #c9a227;
      color: white;
      padding: 2px 8px;
      border-radius: 4px;
      font-size: 0.75rem;
      margin-left: 8px;
    }
    .item-actions {
      display: flex;
      gap: 8px;
    }
    .item-actions button, .item-actions a {
      padding: 6px 12px;
      font-size: 0.85rem;
    }

    /* Form Styles */
    .form-card {
      background: white;
      border-radius: 12px;
      padding: 24px;
      box-shadow: 0 2px 8px rgba(0,0,0,0.08);
      margin-bottom: 24px;
    }
    .form-card h3 {
      font-family: 'Oswald', sans-serif;
      font-size: 1.25rem;
      text-transform: uppercase;
      letter-spacing: 0.05em;
      color: #1a1f3c;
      margin-bottom: 20px;
    }
    .form-group {
      margin-bottom: 20px;
    }
    .form-group label {
      display: block;
      font-weight: 500;
      margin-bottom: 8px;
      color: #333;
    }
    .form-group input[type="text"],
    .form-group input[type="date"],
    .form-group textarea {
      width: 100%;
      padding: 12px 16px;
      border: 2px solid #e0e0e0;
      border-radius: 8px;
      font-size: 1rem;
      font-family: inherit;
    }
    .form-group input:focus,
    .form-group textarea:focus {
      outline: none;
      border-color: #c9a227;
    }
    .form-group textarea {
      min-height: 150px;
      resize: vertical;
    }
    .form-row {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 20px;
    }
    .checkbox-group {
      display: flex;
      align-items: center;
      gap: 8px;
    }
    .checkbox-group input {
      width: 18px;
      height: 18px;
    }
    .form-actions {
      display: flex;
      gap: 12px;
      margin-top: 24px;
    }

    .empty-state {
      text-align: center;
      padding: 40px;
      color: #666;
    }
  </style>
</head>
<body>
  <header class="header">
    <h1>News & Updates</h1>
    <a href="/admin/dashboard.php">← Back to Dashboard</a>
  </header>

  <div class="container">
    <?php if ($message): ?>
    <div class="message success"><?= e($message) ?></div>
    <?php endif; ?>
    <?php if ($error): ?>
    <div class="message error"><?= e($error) ?></div>
    <?php endif; ?>

    <!-- Edit/Create Form -->
    <div class="form-card">
      <h3><?= $editing ? 'Edit News' : 'Add News' ?></h3>
      <form method="POST">
        <input type="hidden" name="action" value="save">
        <?php if ($editing): ?>
        <input type="hidden" name="id" value="<?= e($editing['id']) ?>">
        <?php endif; ?>

        <div class="form-group">
          <label for="title">Title</label>
          <input type="text" id="title" name="title" value="<?= e($editing['title'] ?? '') ?>" required>
        </div>

        <div class="form-row">
          <div class="form-group">
            <label for="date">Date</label>
            <input type="date" id="date" name="date" value="<?= e($editing['date'] ?? date('Y-m-d')) ?>">
          </div>
          <div class="form-group">
            <label for="author">Author</label>
            <input type="text" id="author" name="author" value="<?= e($editing['author'] ?? '') ?>">
          </div>
        </div>

        <div class="form-group">
          <label for="excerpt">Excerpt</label>
          <textarea id="excerpt" name="excerpt" rows="2"><?= e($editing['excerpt'] ?? '') ?></textarea>
        </div>

        <div class="form-group">
          <label for="body">Content</label>
          <textarea id="body" name="body" rows="6"><?= e($editing['body'] ?? '') ?></textarea>
        </div>

        <div class="form-group">
          <label class="checkbox-group">
            <input type="checkbox" name="featured" <?= ($editing['featured'] ?? false) ? 'checked' : '' ?>>
            Featured post
          </label>
        </div>

        <div class="form-actions">
          <button type="submit" class="btn">Save</button>
          <?php if ($editing): ?>
          <a href="/admin/news.php" class="btn btn-secondary">Cancel</a>
          <?php endif; ?>
        </div>
      </form>
    </div>

    <!-- Items List -->
    <div class="page-header">
      <h2>All News</h2>
    </div>

    <div class="card">
      <?php if (empty($news)): ?>
      <div class="empty-state">
        <p>No news items yet. Add your first one above!</p>
      </div>
      <?php else: ?>
      <ul class="item-list">
        <?php foreach ($news as $item): ?>
        <li class="item">
          <div class="item-info">
            <h3>
              <?= e($item['title']) ?>
              <?php if ($item['featured'] ?? false): ?>
              <span class="badge">Featured</span>
              <?php endif; ?>
            </h3>
            <p><?= format_date($item['date'] ?? '') ?> <?= $item['author'] ? '· ' . e($item['author']) : '' ?></p>
          </div>
          <div class="item-actions">
            <a href="?edit=<?= $item['id'] ?>" class="btn btn-secondary">Edit</a>
            <form method="POST" style="display:inline;" onsubmit="return confirm('Delete this item?')">
              <input type="hidden" name="action" value="delete">
              <input type="hidden" name="id" value="<?= $item['id'] ?>">
              <button type="submit" class="btn btn-danger">Delete</button>
            </form>
          </div>
        </li>
        <?php endforeach; ?>
      </ul>
      <?php endif; ?>
    </div>
  </div>
</body>
</html>
