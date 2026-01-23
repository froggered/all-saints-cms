<?php
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/../includes/functions.php';

require_login();

$message = '';
$error = '';
$services = load_data('services');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'save') {
        $id = $_POST['id'] ?? null;
        $item = [
            'name' => trim($_POST['name'] ?? ''),
            'day' => $_POST['day'] ?? 'Sunday',
            'time' => trim($_POST['time'] ?? ''),
            'order' => (int)($_POST['order'] ?? 1)
        ];

        if (empty($item['name'])) {
            $error = 'Service name is required';
        } else {
            if ($id) {
                foreach ($services as &$s) {
                    if ($s['id'] == $id) {
                        $item['id'] = (int)$id;
                        $s = $item;
                        break;
                    }
                }
                $message = 'Service updated';
            } else {
                $maxId = 0;
                foreach ($services as $s) {
                    if (($s['id'] ?? 0) > $maxId) $maxId = $s['id'];
                }
                $item['id'] = $maxId + 1;
                $services[] = $item;
                $message = 'Service added';
            }
            save_data('services', $services);
            $services = load_data('services');
        }
    }

    if ($action === 'delete' && isset($_POST['id'])) {
        $id = (int)$_POST['id'];
        $services = array_filter($services, fn($s) => ($s['id'] ?? 0) != $id);
        $services = array_values($services);
        save_data('services', $services);
        $message = 'Service deleted';
    }
}

$editing = null;
if (isset($_GET['edit'])) {
    foreach ($services as $s) {
        if (($s['id'] ?? 0) == $_GET['edit']) {
            $editing = $s;
            break;
        }
    }
}

usort($services, fn($a, $b) => ($a['order'] ?? 99) - ($b['order'] ?? 99));
$days = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Services | All Saints Admin</title>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Oswald:wght@400;500;600;700&display=swap" rel="stylesheet">
  <style>
    * { box-sizing: border-box; margin: 0; padding: 0; }
    body { font-family: 'Inter', sans-serif; background: #f5f5f5; min-height: 100vh; }
    .header { background: #1a1f3c; color: white; padding: 16px 24px; display: flex; justify-content: space-between; align-items: center; }
    .header h1 { font-family: 'Oswald', sans-serif; font-size: 1.25rem; text-transform: uppercase; }
    .header a { color: white; text-decoration: none; padding: 8px 16px; background: rgba(255,255,255,0.1); border-radius: 6px; }
    .container { max-width: 800px; margin: 0 auto; padding: 24px; }
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
    .form-group input, .form-group select { width: 100%; padding: 12px 16px; border: 2px solid #e0e0e0; border-radius: 8px; font-size: 1rem; }
    .form-group input:focus, .form-group select:focus { outline: none; border-color: #c9a227; }
    .form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; }
    .form-actions { display: flex; gap: 12px; margin-top: 24px; }
    .page-header { margin-bottom: 24px; }
    .page-header h2 { font-family: 'Oswald', sans-serif; font-size: 1.5rem; text-transform: uppercase; color: #1a1f3c; }
    .item-list { list-style: none; }
    .item { padding: 16px 20px; border-bottom: 1px solid #eee; display: flex; justify-content: space-between; align-items: center; }
    .item:last-child { border-bottom: none; }
    .item-info h3 { font-size: 1rem; color: #1a1f3c; margin-bottom: 4px; }
    .item-info p { font-size: 0.85rem; color: #666; }
    .item-actions { display: flex; gap: 8px; }
    .item-actions button, .item-actions a { padding: 6px 12px; font-size: 0.85rem; }
    .empty-state { text-align: center; padding: 40px; color: #666; }
  </style>
</head>
<body>
  <header class="header">
    <h1>Service Schedule</h1>
    <a href="/admin/dashboard.php">← Back to Dashboard</a>
  </header>

  <div class="container">
    <?php if ($message): ?><div class="message success"><?= e($message) ?></div><?php endif; ?>
    <?php if ($error): ?><div class="message error"><?= e($error) ?></div><?php endif; ?>

    <div class="form-card">
      <h3><?= $editing ? 'Edit Service' : 'Add Service' ?></h3>
      <form method="POST">
        <input type="hidden" name="action" value="save">
        <?php if ($editing): ?><input type="hidden" name="id" value="<?= e($editing['id']) ?>"><?php endif; ?>

        <div class="form-group">
          <label for="name">Service Name</label>
          <input type="text" id="name" name="name" value="<?= e($editing['name'] ?? '') ?>" required placeholder="e.g. Divine Liturgy">
        </div>

        <div class="form-row">
          <div class="form-group">
            <label for="day">Day</label>
            <select id="day" name="day">
              <?php foreach ($days as $day): ?>
              <option value="<?= $day ?>" <?= ($editing['day'] ?? '') === $day ? 'selected' : '' ?>><?= $day ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="form-group">
            <label for="time">Time</label>
            <input type="text" id="time" name="time" value="<?= e($editing['time'] ?? '') ?>" placeholder="e.g. 10:00 AM">
          </div>
        </div>

        <div class="form-group">
          <label for="order">Display Order</label>
          <input type="number" id="order" name="order" value="<?= e($editing['order'] ?? 1) ?>" min="1">
        </div>

        <div class="form-actions">
          <button type="submit" class="btn">Save</button>
          <?php if ($editing): ?><a href="/admin/services.php" class="btn btn-secondary">Cancel</a><?php endif; ?>
        </div>
      </form>
    </div>

    <div class="page-header"><h2>Weekly Services</h2></div>

    <div class="card">
      <?php if (empty($services)): ?>
      <div class="empty-state"><p>No services yet.</p></div>
      <?php else: ?>
      <ul class="item-list">
        <?php foreach ($services as $item): ?>
        <li class="item">
          <div class="item-info">
            <h3><?= e($item['name']) ?></h3>
            <p><?= e($item['day']) ?> · <?= e($item['time']) ?></p>
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
