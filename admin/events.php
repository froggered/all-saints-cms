<?php
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/../includes/functions.php';

require_login();

$message = '';
$error = '';
$events = load_data('events');

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'save') {
        $id = $_POST['id'] ?? null;
        $item = [
            'title' => trim($_POST['title'] ?? ''),
            'slug' => generate_slug($_POST['title'] ?? ''),
            'date' => $_POST['date'] ?? date('Y-m-d'),
            'time' => trim($_POST['time'] ?? ''),
            'description' => trim($_POST['description'] ?? ''),
            'featured' => isset($_POST['featured']),
            'category' => $_POST['category'] ?? 'service'
        ];

        if (empty($item['title'])) {
            $error = 'Title is required';
        } else {
            if ($id) {
                foreach ($events as &$e) {
                    if ($e['id'] == $id) {
                        $item['id'] = (int)$id;
                        $e = $item;
                        break;
                    }
                }
                $message = 'Event updated successfully';
            } else {
                $maxId = 0;
                foreach ($events as $e) {
                    if (($e['id'] ?? 0) > $maxId) $maxId = $e['id'];
                }
                $item['id'] = $maxId + 1;
                $events[] = $item;
                $message = 'Event created successfully';
            }
            save_data('events', $events);
            $events = load_data('events');
        }
    }

    if ($action === 'delete' && isset($_POST['id'])) {
        $id = (int)$_POST['id'];
        $events = array_filter($events, fn($e) => ($e['id'] ?? 0) != $id);
        $events = array_values($events);
        save_data('events', $events);
        $message = 'Event deleted successfully';
    }
}

$editing = null;
if (isset($_GET['edit'])) {
    foreach ($events as $e) {
        if (($e['id'] ?? 0) == $_GET['edit']) {
            $editing = $e;
            break;
        }
    }
}

usort($events, fn($a, $b) => strtotime($a['date'] ?? 0) - strtotime($b['date'] ?? 0));
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Events | All Saints Admin</title>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Oswald:wght@400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="/admin/style.css">
  <style>
    * { box-sizing: border-box; margin: 0; padding: 0; }
    body { font-family: 'Inter', sans-serif; background: #f5f5f5; min-height: 100vh; }
    .header { background: #1a1f3c; color: white; padding: 16px 24px; display: flex; justify-content: space-between; align-items: center; }
    .header h1 { font-family: 'Oswald', sans-serif; font-size: 1.25rem; text-transform: uppercase; letter-spacing: 0.05em; }
    .header a { color: white; text-decoration: none; padding: 8px 16px; background: rgba(255,255,255,0.1); border-radius: 6px; }
    .container { max-width: 1000px; margin: 0 auto; padding: 24px; }
    .btn { padding: 10px 20px; background: #1a1f3c; color: white; border: none; border-radius: 6px; font-family: 'Oswald', sans-serif; text-transform: uppercase; cursor: pointer; text-decoration: none; font-size: 0.9rem; }
    .btn:hover { background: #2d2d4a; }
    .btn-secondary { background: white; color: #1a1f3c; border: 2px solid #e0e0e0; }
    .btn-danger { background: #dc3545; }
    .message { padding: 12px 16px; border-radius: 8px; margin-bottom: 20px; }
    .message.success { background: #d4edda; color: #155724; }
    .message.error { background: #f8d7da; color: #721c24; }
    .card { background: white; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.08); overflow: hidden; }
    .form-card { background: white; border-radius: 12px; padding: 24px; box-shadow: 0 2px 8px rgba(0,0,0,0.08); margin-bottom: 24px; }
    .form-card h3 { font-family: 'Oswald', sans-serif; font-size: 1.25rem; text-transform: uppercase; color: #1a1f3c; margin-bottom: 20px; }
    .form-group { margin-bottom: 20px; }
    .form-group label { display: block; font-weight: 500; margin-bottom: 8px; color: #333; }
    .form-group input, .form-group textarea, .form-group select { width: 100%; padding: 12px 16px; border: 2px solid #e0e0e0; border-radius: 8px; font-size: 1rem; font-family: inherit; }
    .form-group input:focus, .form-group textarea:focus, .form-group select:focus { outline: none; border-color: #c9a227; }
    .form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; }
    .checkbox-group { display: flex; align-items: center; gap: 8px; }
    .checkbox-group input { width: 18px; height: 18px; }
    .form-actions { display: flex; gap: 12px; margin-top: 24px; }
    .page-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px; }
    .page-header h2 { font-family: 'Oswald', sans-serif; font-size: 1.5rem; text-transform: uppercase; color: #1a1f3c; }
    .item-list { list-style: none; }
    .item { padding: 16px 20px; border-bottom: 1px solid #eee; display: flex; justify-content: space-between; align-items: center; }
    .item:last-child { border-bottom: none; }
    .item-info h3 { font-size: 1rem; color: #1a1f3c; margin-bottom: 4px; }
    .item-info p { font-size: 0.85rem; color: #666; }
    .item-info .badge { display: inline-block; background: #c9a227; color: white; padding: 2px 8px; border-radius: 4px; font-size: 0.75rem; margin-left: 8px; }
    .item-actions { display: flex; gap: 8px; }
    .item-actions button, .item-actions a { padding: 6px 12px; font-size: 0.85rem; }
    .empty-state { text-align: center; padding: 40px; color: #666; }
  </style>
</head>
<body>
  <header class="header">
    <h1>Events</h1>
    <a href="/admin/dashboard.php">← Back to Dashboard</a>
  </header>

  <div class="container">
    <?php if ($message): ?><div class="message success"><?= e($message) ?></div><?php endif; ?>
    <?php if ($error): ?><div class="message error"><?= e($error) ?></div><?php endif; ?>

    <div class="form-card">
      <h3><?= $editing ? 'Edit Event' : 'Add Event' ?></h3>
      <form method="POST">
        <input type="hidden" name="action" value="save">
        <?php if ($editing): ?><input type="hidden" name="id" value="<?= e($editing['id']) ?>"><?php endif; ?>

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
            <label for="time">Time</label>
            <input type="text" id="time" name="time" value="<?= e($editing['time'] ?? '') ?>" placeholder="e.g. Divine Liturgy - 10:00 AM">
          </div>
        </div>

        <div class="form-group">
          <label for="description">Description</label>
          <textarea id="description" name="description" rows="3"><?= e($editing['description'] ?? '') ?></textarea>
        </div>

        <div class="form-row">
          <div class="form-group">
            <label for="category">Category</label>
            <select id="category" name="category">
              <option value="feast" <?= ($editing['category'] ?? '') === 'feast' ? 'selected' : '' ?>>Feast Day</option>
              <option value="class" <?= ($editing['category'] ?? '') === 'class' ? 'selected' : '' ?>>Class/Study</option>
              <option value="community" <?= ($editing['category'] ?? '') === 'community' ? 'selected' : '' ?>>Community</option>
              <option value="service" <?= ($editing['category'] ?? '') === 'service' ? 'selected' : '' ?>>Special Service</option>
            </select>
          </div>
          <div class="form-group">
            <label class="checkbox-group" style="margin-top: 32px;">
              <input type="checkbox" name="featured" <?= ($editing['featured'] ?? false) ? 'checked' : '' ?>>
              Featured event
            </label>
          </div>
        </div>

        <div class="form-actions">
          <button type="submit" class="btn">Save</button>
          <?php if ($editing): ?><a href="/admin/events.php" class="btn btn-secondary">Cancel</a><?php endif; ?>
        </div>
      </form>
    </div>

    <div class="page-header"><h2>All Events</h2></div>

    <div class="card">
      <?php if (empty($events)): ?>
      <div class="empty-state"><p>No events yet.</p></div>
      <?php else: ?>
      <ul class="item-list">
        <?php foreach ($events as $item): ?>
        <li class="item">
          <div class="item-info">
            <h3><?= e($item['title']) ?><?php if ($item['featured'] ?? false): ?><span class="badge">Featured</span><?php endif; ?></h3>
            <p><?= format_date($item['date'] ?? '') ?> · <?= e($item['time'] ?? '') ?></p>
          </div>
          <div class="item-actions">
            <a href="?edit=<?= $item['id'] ?>" class="btn btn-secondary">Edit</a>
            <form method="POST" style="display:inline;" onsubmit="return confirm('Delete?')">
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
