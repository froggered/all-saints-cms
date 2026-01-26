<?php
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/../includes/functions.php';

require_login();

$services = load_data('recurring_services');
$locations = get_locations();
$days = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];

$message = '';
$error = '';
$editing = null;

// Handle delete
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $services = array_filter($services, fn($s) => $s['id'] !== $id);
    save_data('recurring_services', array_values($services));
    header('Location: /admin/recurring.php?deleted=1');
    exit;
}

// Handle edit
if (isset($_GET['edit'])) {
    $id = $_GET['edit'];
    foreach ($services as $s) {
        if ($s['id'] === $id) {
            $editing = $s;
            break;
        }
    }
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $day_of_week = (int)($_POST['day_of_week'] ?? 0);
    $time = trim($_POST['time'] ?? '');
    $location_id = trim($_POST['location_id'] ?? 'temple');
    $description = trim($_POST['description'] ?? '');
    $active = isset($_POST['active']);
    $order = (int)($_POST['order'] ?? 99);
    $type = trim($_POST['type'] ?? 'service');
    $edit_id = $_POST['edit_id'] ?? '';

    if (empty($name) || empty($time)) {
        $error = 'Name and time are required.';
    } else {
        if ($edit_id) {
            // Update existing
            foreach ($services as $i => $s) {
                if ($s['id'] === $edit_id) {
                    $services[$i]['name'] = $name;
                    $services[$i]['day_of_week'] = $day_of_week;
                    $services[$i]['time'] = $time;
                    $services[$i]['location_id'] = $location_id;
                    $services[$i]['description'] = $description;
                    $services[$i]['active'] = $active;
                    $services[$i]['order'] = $order;
                    $services[$i]['type'] = $type;
                    break;
                }
            }
            $message = 'Service updated successfully.';
        } else {
            // Create new
            $id = strtolower($days[$day_of_week]) . '-' . generate_slug($name);
            // Ensure unique ID
            $base_id = $id;
            $counter = 1;
            while (array_filter($services, fn($s) => $s['id'] === $id)) {
                $id = $base_id . '-' . $counter++;
            }

            $services[] = [
                'id' => $id,
                'name' => $name,
                'day_of_week' => $day_of_week,
                'time' => $time,
                'location_id' => $location_id,
                'description' => $description,
                'active' => $active,
                'order' => $order,
                'type' => $type
            ];
            $message = 'Service created successfully.';
        }

        usort($services, fn($a, $b) => ($a['order'] ?? 99) - ($b['order'] ?? 99));
        save_data('recurring_services', $services);

        header('Location: /admin/recurring.php?saved=1');
        exit;
    }
}

if (isset($_GET['saved'])) $message = 'Changes saved successfully.';
if (isset($_GET['deleted'])) $message = 'Service deleted.';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Recurring Services | All Saints Admin</title>
  <link rel="icon" type="image/svg+xml" href="/favicon.svg">
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
    .header-logo {
      font-family: 'Oswald', sans-serif;
      font-size: 1.25rem;
      text-transform: uppercase;
      letter-spacing: 0.05em;
      display: flex;
      align-items: center;
      gap: 10px;
      color: white;
      text-decoration: none;
      background: none;
      padding: 0;
    }
    .header-logo:hover { color: white; background: none; }
    .header-logo span { color: #c9a227; }
    .header nav { display: flex; gap: 12px; }
    .header a {
      color: white;
      text-decoration: none;
      padding: 8px 16px;
      background: rgba(255,255,255,0.1);
      border-radius: 6px;
      font-size: 0.9rem;
    }
    .header a:hover { background: rgba(255,255,255,0.2); }

    .container { max-width: 1200px; margin: 0 auto; padding: 24px; }

    .page-header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 24px;
    }
    .page-title {
      font-family: 'Oswald', sans-serif;
      font-size: 1.75rem;
      text-transform: uppercase;
      color: #1a1f3c;
    }

    .message { padding: 12px 16px; border-radius: 8px; margin-bottom: 20px; }
    .message.success { background: #d4edda; color: #155724; }
    .message.error { background: #f8d7da; color: #721c24; }

    .grid { display: grid; grid-template-columns: 1fr 400px; gap: 24px; }
    @media (max-width: 900px) { .grid { grid-template-columns: 1fr; } }

    .card {
      background: white;
      border-radius: 12px;
      padding: 24px;
      box-shadow: 0 2px 8px rgba(0,0,0,0.08);
    }
    .card h2 {
      font-family: 'Oswald', sans-serif;
      font-size: 1.1rem;
      text-transform: uppercase;
      color: #1a1f3c;
      margin-bottom: 16px;
      padding-bottom: 12px;
      border-bottom: 2px solid #f0f0f0;
    }

    .form-group { margin-bottom: 16px; }
    .form-group label {
      display: block;
      font-weight: 500;
      margin-bottom: 6px;
      color: #333;
    }
    .form-group input, .form-group select, .form-group textarea {
      width: 100%;
      padding: 10px 14px;
      border: 2px solid #e0e0e0;
      border-radius: 8px;
      font-size: 1rem;
      font-family: inherit;
    }
    .form-group input:focus, .form-group select:focus, .form-group textarea:focus {
      outline: none;
      border-color: #c9a227;
    }
    .form-row {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 16px;
    }

    .checkbox-group {
      display: flex;
      align-items: center;
      gap: 8px;
    }
    .checkbox-group input { width: auto; }

    .btn {
      display: inline-block;
      padding: 10px 20px;
      border: none;
      border-radius: 8px;
      font-size: 0.9rem;
      font-weight: 600;
      cursor: pointer;
      text-decoration: none;
    }
    .btn-primary { background: #c9a227; color: white; }
    .btn-primary:hover { background: #b8931f; }
    .btn-secondary { background: #e0e0e0; color: #333; }
    .btn-danger { background: #dc3545; color: white; }
    .btn-sm { padding: 6px 12px; font-size: 0.8rem; }

    table { width: 100%; border-collapse: collapse; }
    th, td { padding: 12px; text-align: left; border-bottom: 1px solid #f0f0f0; }
    th {
      font-family: 'Oswald', sans-serif;
      text-transform: uppercase;
      font-size: 0.8rem;
      color: #666;
    }
    tr:hover { background: #fafafa; }

    .badge {
      display: inline-block;
      padding: 4px 8px;
      border-radius: 4px;
      font-size: 0.75rem;
      font-weight: 600;
    }
    .badge-success { background: #d4edda; color: #155724; }
    .badge-secondary { background: #e0e0e0; color: #666; }
    .badge-location { background: #e8f4fd; color: #1a5276; }
    .badge-service { background: #3498db; color: white; }
    .badge-event { background: #c9a227; color: white; }
    .badge-misc { background: #95a5a6; color: white; }

    .actions { display: flex; gap: 8px; }
  </style>
</head>
<body>
  <header class="header">
    <a href="/admin/dashboard.php" class="header-logo"><span>&#9766;</span> All Saints Admin</a>
    <nav>
      <a href="/admin/dashboard.php">Dashboard</a>
      <a href="/admin/calendar.php">Calendar</a>
      <a href="/" target="_blank">View Site</a>
    </nav>
  </header>

  <div class="container">
    <div class="page-header">
      <h1 class="page-title">Recurring Services</h1>
    </div>

    <?php if ($message): ?>
      <div class="message success"><?= e($message) ?></div>
    <?php endif; ?>
    <?php if ($error): ?>
      <div class="message error"><?= e($error) ?></div>
    <?php endif; ?>

    <div class="grid">
      <!-- List -->
      <div class="card">
        <h2>Weekly Service Templates</h2>
        <table>
          <thead>
            <tr>
              <th>Day</th>
              <th>Name</th>
              <th>Time</th>
              <th>Location</th>
              <th>Type</th>
              <th>Status</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($services as $service):
              $loc = get_location($service['location_id']);
            ?>
            <tr>
              <td><?= e($days[$service['day_of_week']]) ?></td>
              <td><strong><?= e($service['name']) ?></strong></td>
              <td><?= e(format_time($service['time'])) ?></td>
              <td><span class="badge badge-location"><?= e($loc['short'] ?? $service['location_id']) ?></span></td>
              <td>
                <?php
                $type = $service['type'] ?? 'service';
                if ($type === 'service'): ?>
                  <span class="badge badge-service">Service</span>
                <?php elseif ($type === 'event'): ?>
                  <span class="badge badge-event">Event</span>
                <?php else: ?>
                  <span class="badge badge-misc">Misc</span>
                <?php endif; ?>
              </td>
              <td>
                <?php if ($service['active'] ?? true): ?>
                  <span class="badge badge-success">Active</span>
                <?php else: ?>
                  <span class="badge badge-secondary">Inactive</span>
                <?php endif; ?>
              </td>
              <td class="actions">
                <a href="?edit=<?= e($service['id']) ?>" class="btn btn-secondary btn-sm">Edit</a>
                <a href="?delete=<?= e($service['id']) ?>" class="btn btn-danger btn-sm" onclick="return confirm('Delete this template?')">Delete</a>
              </td>
            </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>

      <!-- Form -->
      <div class="card">
        <h2><?= $editing ? 'Edit Service' : 'Add New Service' ?></h2>
        <form method="POST">
          <?php if ($editing): ?>
            <input type="hidden" name="edit_id" value="<?= e($editing['id']) ?>">
          <?php endif; ?>

          <div class="form-group">
            <label for="name">Name</label>
            <input type="text" id="name" name="name" value="<?= e($editing['name'] ?? '') ?>" required>
          </div>

          <div class="form-group">
            <label for="type">Type</label>
            <select id="type" name="type" required>
              <option value="service" <?= ($editing['type'] ?? 'service') === 'service' ? 'selected' : '' ?>>Service (worship)</option>
              <option value="event" <?= ($editing['type'] ?? 'service') === 'event' ? 'selected' : '' ?>>Event (special gatherings)</option>
              <option value="misc" <?= ($editing['type'] ?? 'service') === 'misc' ? 'selected' : '' ?>>Misc (cleaning, classes, etc.)</option>
            </select>
          </div>

          <div class="form-row">
            <div class="form-group">
              <label for="day_of_week">Day of Week</label>
              <select id="day_of_week" name="day_of_week" required>
                <?php foreach ($days as $i => $day): ?>
                  <option value="<?= $i ?>" <?= ($editing['day_of_week'] ?? 0) === $i ? 'selected' : '' ?>><?= $day ?></option>
                <?php endforeach; ?>
              </select>
            </div>

            <div class="form-group">
              <label for="time">Time</label>
              <input type="time" id="time" name="time" value="<?= e($editing['time'] ?? '10:00') ?>" required>
            </div>
          </div>

          <div class="form-row">
            <div class="form-group">
              <label for="location_id">Location</label>
              <select id="location_id" name="location_id" required>
                <?php foreach ($locations as $loc): ?>
                  <option value="<?= e($loc['id']) ?>" <?= ($editing['location_id'] ?? 'temple') === $loc['id'] ? 'selected' : '' ?>><?= e($loc['name']) ?></option>
                <?php endforeach; ?>
              </select>
            </div>

            <div class="form-group">
              <label for="order">Display Order</label>
              <input type="number" id="order" name="order" value="<?= e($editing['order'] ?? 99) ?>" min="1">
            </div>
          </div>

          <div class="form-group">
            <label for="description">Description (optional)</label>
            <textarea id="description" name="description" rows="2"><?= e($editing['description'] ?? '') ?></textarea>
          </div>

          <div class="form-group">
            <label class="checkbox-group">
              <input type="checkbox" name="active" <?= ($editing['active'] ?? true) ? 'checked' : '' ?>>
              Active (appears on calendar)
            </label>
          </div>

          <div style="display: flex; gap: 12px;">
            <button type="submit" class="btn btn-primary"><?= $editing ? 'Update Service' : 'Add Service' ?></button>
            <?php if ($editing): ?>
              <a href="/admin/recurring.php" class="btn btn-secondary">Cancel</a>
            <?php endif; ?>
          </div>
        </form>
      </div>
    </div>
  </div>
</body>
</html>
