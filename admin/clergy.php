<?php
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/../includes/functions.php';

require_login();

$message = '';
$error = '';
$clergy = load_data('clergy');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'save') {
        $id = $_POST['id'] ?? null;
        $item = [
            'title' => trim($_POST['title'] ?? ''),
            'role' => trim($_POST['role'] ?? ''),
            'photo' => trim($_POST['photo'] ?? ''),
            'order' => (int)($_POST['order'] ?? 10),
            'active' => isset($_POST['active']),
            'bio' => trim($_POST['bio'] ?? '')
        ];

        if (empty($item['title'])) {
            $error = 'Name is required';
        } else {
            if ($id) {
                foreach ($clergy as &$c) {
                    if ($c['id'] == $id) {
                        $item['id'] = (int)$id;
                        $c = $item;
                        break;
                    }
                }
                $message = 'Profile updated';
            } else {
                $maxId = 0;
                foreach ($clergy as $c) {
                    if (($c['id'] ?? 0) > $maxId) $maxId = $c['id'];
                }
                $item['id'] = $maxId + 1;
                $clergy[] = $item;
                $message = 'Profile added';
            }
            save_data('clergy', $clergy);
            $clergy = load_data('clergy');
        }
    }

    if ($action === 'delete' && isset($_POST['id'])) {
        $id = (int)$_POST['id'];
        $clergy = array_filter($clergy, fn($c) => ($c['id'] ?? 0) != $id);
        $clergy = array_values($clergy);
        save_data('clergy', $clergy);
        $message = 'Profile deleted';
    }
}

$editing = null;
if (isset($_GET['edit'])) {
    foreach ($clergy as $c) {
        if (($c['id'] ?? 0) == $_GET['edit']) {
            $editing = $c;
            break;
        }
    }
}

usort($clergy, fn($a, $b) => ($a['order'] ?? 99) - ($b['order'] ?? 99));
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Clergy | All Saints Admin</title>
  <link rel="icon" type="image/svg+xml" href="/favicon.svg">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Oswald:wght@400;500;600;700&display=swap" rel="stylesheet">
  <style>
    * { box-sizing: border-box; margin: 0; padding: 0; }
    body { font-family: 'Inter', sans-serif; background: #f5f5f5; min-height: 100vh; }
    .header { background: #1a1f3c; color: white; padding: 16px 24px; display: flex; justify-content: space-between; align-items: center; }
    .header-logo { font-family: 'Oswald', sans-serif; font-size: 1.25rem; text-transform: uppercase; letter-spacing: 0.05em; display: flex; align-items: center; gap: 10px; color: white; text-decoration: none; }
    .header-logo:hover { color: white; }
    .header-logo span { color: #c9a227; }
    .header nav { display: flex; gap: 12px; }
    .header a { color: white; text-decoration: none; padding: 8px 16px; background: rgba(255,255,255,0.1); border-radius: 6px; }
    .header a:hover { background: rgba(255,255,255,0.2); }
    .container { max-width: 900px; margin: 0 auto; padding: 24px; }
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
    .form-group input, .form-group textarea { width: 100%; padding: 12px 16px; border: 2px solid #e0e0e0; border-radius: 8px; font-size: 1rem; font-family: inherit; }
    .form-group input:focus, .form-group textarea:focus { outline: none; border-color: #c9a227; }
    .form-group textarea { min-height: 150px; resize: vertical; }
    .form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; }
    .checkbox-group { display: flex; align-items: center; gap: 8px; }
    .checkbox-group input { width: 18px; height: 18px; }
    .form-actions { display: flex; gap: 12px; margin-top: 24px; }
    .page-header { margin-bottom: 24px; }
    .page-header h2 { font-family: 'Oswald', sans-serif; font-size: 1.5rem; text-transform: uppercase; color: #1a1f3c; }
    .item-list { list-style: none; }
    .item { padding: 16px 20px; border-bottom: 1px solid #eee; display: flex; justify-content: space-between; align-items: center; }
    .item:last-child { border-bottom: none; }
    .item-info { display: flex; align-items: center; gap: 16px; }
    .item-info img { width: 50px; height: 50px; border-radius: 50%; object-fit: cover; background: #eee; }
    .item-info h3 { font-size: 1rem; color: #1a1f3c; margin-bottom: 4px; }
    .item-info p { font-size: 0.85rem; color: #666; }
    .item-info .badge { display: inline-block; background: #6c757d; color: white; padding: 2px 8px; border-radius: 4px; font-size: 0.75rem; margin-left: 8px; }
    .item-info .badge.active { background: #28a745; }
    .item-actions { display: flex; gap: 8px; }
    .item-actions button, .item-actions a { padding: 6px 12px; font-size: 0.85rem; }
    .empty-state { text-align: center; padding: 40px; color: #666; }
  </style>
</head>
<body>
  <header class="header">
    <a href="/admin/dashboard.php" class="header-logo"><span>&#9766;</span> All Saints Admin</a>
    <nav>
      <a href="/admin/dashboard.php">Dashboard</a>
      <a href="/" target="_blank">View Site</a>
    </nav>
  </header>

  <div class="container">
    <?php if ($message): ?><div class="message success"><?= e($message) ?></div><?php endif; ?>
    <?php if ($error): ?><div class="message error"><?= e($error) ?></div><?php endif; ?>

    <div class="form-card">
      <h3><?= $editing ? 'Edit Profile' : 'Add Clergy' ?></h3>
      <form method="POST">
        <input type="hidden" name="action" value="save">
        <?php if ($editing): ?><input type="hidden" name="id" value="<?= e($editing['id']) ?>"><?php endif; ?>

        <div class="form-row">
          <div class="form-group">
            <label for="title">Name</label>
            <input type="text" id="title" name="title" value="<?= e($editing['title'] ?? '') ?>" required>
          </div>
          <div class="form-group">
            <label for="role">Role</label>
            <input type="text" id="role" name="role" value="<?= e($editing['role'] ?? '') ?>" placeholder="e.g. Parish Priest">
          </div>
        </div>

        <div class="form-row">
          <div class="form-group">
            <label for="photo">Photo URL</label>
            <input type="text" id="photo" name="photo" value="<?= e($editing['photo'] ?? '') ?>" placeholder="/images/clergy-name.jpg">
          </div>
          <div class="form-group">
            <label for="order">Display Order</label>
            <input type="number" id="order" name="order" value="<?= e($editing['order'] ?? 10) ?>" min="1">
          </div>
        </div>

        <div class="form-group">
          <label for="bio">Biography</label>
          <textarea id="bio" name="bio" rows="6"><?= e($editing['bio'] ?? '') ?></textarea>
        </div>

        <div class="form-group">
          <label class="checkbox-group">
            <input type="checkbox" name="active" <?= ($editing['active'] ?? true) ? 'checked' : '' ?>>
            Active (show on website)
          </label>
        </div>

        <div class="form-actions">
          <button type="submit" class="btn">Save</button>
          <?php if ($editing): ?><a href="/admin/clergy.php" class="btn btn-secondary">Cancel</a><?php endif; ?>
        </div>
      </form>
    </div>

    <div class="page-header"><h2>All Clergy</h2></div>

    <div class="card">
      <?php if (empty($clergy)): ?>
      <div class="empty-state"><p>No clergy profiles yet.</p></div>
      <?php else: ?>
      <ul class="item-list">
        <?php foreach ($clergy as $item): ?>
        <li class="item">
          <div class="item-info">
            <img src="<?= e($item['photo'] ?? '') ?>" alt="" onerror="this.src='data:image/svg+xml,<svg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 100 100%22><rect fill=%22%23eee%22 width=%22100%22 height=%22100%22/></svg>'">
            <div>
              <h3>
                <?= e($item['title']) ?>
                <span class="badge <?= ($item['active'] ?? true) ? 'active' : '' ?>">
                  <?= ($item['active'] ?? true) ? 'Active' : 'Inactive' ?>
                </span>
              </h3>
              <p><?= e($item['role'] ?? '') ?></p>
            </div>
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
