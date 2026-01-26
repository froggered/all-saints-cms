<?php
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/../includes/functions.php';

require_login();

$locations = get_locations();
$message = '';
$error = '';
$event = null;
$is_new = true;
$is_recurring_instance = false;
$date = $_GET['date'] ?? date('Y-m-d');

// Load existing event if ID provided
if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $events = get_calendar_events();

    // First check stored events
    foreach ($events as $e) {
        if ($e['id'] === $id) {
            $event = $e;
            $is_new = false;
            break;
        }
    }

    // If not found in stored, it might be a generated recurring event
    if (!$event) {
        $day_events = get_events_for_date($date);
        foreach ($day_events as $e) {
            if ($e['id'] === $id) {
                $event = $e;
                $is_new = false;
                $is_recurring_instance = !empty($e['recurring_id']) && !($e['is_modified'] ?? false);
                break;
            }
        }
    }
}

// Get all events for the date (for display)
$date_events = get_events_for_date($date);

// Handle delete
if (isset($_GET['delete']) && $event) {
    $id = $_GET['delete'];
    if ($event['recurring_id'] && !($event['is_special'] ?? false)) {
        // Cancel recurring instance
        cancel_event_instance($event['date'], $event['recurring_id']);
    } else {
        // Delete special event
        delete_calendar_event($id);
    }
    $return_date = date('Y-m', strtotime($date));
    header("Location: /admin/calendar.php?year=" . date('Y', strtotime($date)) . "&month=" . date('n', strtotime($date)) . "&deleted=1");
    exit;
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $time = trim($_POST['time'] ?? '');
    $location_id = trim($_POST['location_id'] ?? 'temple');
    $description = trim($_POST['description'] ?? '');
    $event_date = $_POST['date'] ?? $date;
    $is_special = isset($_POST['is_special']);
    $edit_id = $_POST['edit_id'] ?? '';
    $recurring_id = $_POST['recurring_id'] ?? null;

    if (empty($name) || empty($time)) {
        $error = 'Name and time are required.';
    } else {
        $event_data = [
            'id' => $edit_id ?: $event_date . '-' . generate_slug($name) . '-' . time(),
            'date' => $event_date,
            'time' => $time,
            'name' => $name,
            'location_id' => $location_id,
            'description' => $description,
            'recurring_id' => $recurring_id ?: null,
            'is_modified' => !empty($recurring_id),
            'is_cancelled' => false,
            'is_special' => $is_special && empty($recurring_id)
        ];

        save_calendar_event($event_data);

        header("Location: /admin/calendar.php?year=" . date('Y', strtotime($event_date)) . "&month=" . date('n', strtotime($event_date)) . "&saved=1");
        exit;
    }
}

$page_title = $event ? 'Edit Event' : 'Add Event';
$formatted_date = date('l, j F Y', strtotime($date));
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= e($page_title) ?> | All Saints Admin</title>
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

    .container { max-width: 900px; margin: 0 auto; padding: 24px; }

    .breadcrumb {
      margin-bottom: 16px;
    }
    .breadcrumb a {
      color: #666;
      text-decoration: none;
    }
    .breadcrumb a:hover { color: #c9a227; }

    .page-title {
      font-family: 'Oswald', sans-serif;
      font-size: 1.75rem;
      text-transform: uppercase;
      color: #1a1f3c;
      margin-bottom: 8px;
    }
    .page-subtitle {
      color: #666;
      margin-bottom: 24px;
    }

    .message { padding: 12px 16px; border-radius: 8px; margin-bottom: 20px; }
    .message.success { background: #d4edda; color: #155724; }
    .message.error { background: #f8d7da; color: #721c24; }
    .message.info { background: #cce5ff; color: #004085; }

    .grid { display: grid; grid-template-columns: 1fr 300px; gap: 24px; }
    @media (max-width: 800px) { .grid { grid-template-columns: 1fr; } }

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
      padding: 12px 24px;
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

    .event-list { margin-top: 16px; }
    .event-item {
      padding: 12px;
      border-bottom: 1px solid #f0f0f0;
      display: flex;
      justify-content: space-between;
      align-items: center;
    }
    .event-item:last-child { border-bottom: none; }
    .event-time {
      font-weight: 600;
      color: #c9a227;
      font-size: 0.9rem;
    }
    .event-name { color: #333; }
    .event-location {
      font-size: 0.8rem;
      color: #666;
    }

    .badge {
      display: inline-block;
      padding: 2px 8px;
      border-radius: 4px;
      font-size: 0.7rem;
      font-weight: 600;
    }
    .badge-recurring { background: #3498db; color: white; }
    .badge-special { background: #c9a227; color: white; }
    .badge-modified { background: #9b59b6; color: white; }
  </style>
</head>
<body>
  <header class="header">
    <a href="/admin/dashboard.php" class="header-logo"><span>&#9766;</span> All Saints Admin</a>
    <nav>
      <a href="/admin/dashboard.php">Dashboard</a>
      <a href="/admin/calendar.php?year=<?= date('Y', strtotime($date)) ?>&month=<?= date('n', strtotime($date)) ?>">Calendar</a>
      <a href="/" target="_blank">View Site</a>
    </nav>
  </header>

  <div class="container">
    <div class="breadcrumb">
      <a href="/admin/calendar.php?year=<?= date('Y', strtotime($date)) ?>&month=<?= date('n', strtotime($date)) ?>">&larr; Back to Calendar</a>
    </div>

    <h1 class="page-title"><?= e($page_title) ?></h1>
    <p class="page-subtitle"><?= e($formatted_date) ?></p>

    <?php if ($error): ?>
      <div class="message error"><?= e($error) ?></div>
    <?php endif; ?>

    <?php if ($is_recurring_instance && $event): ?>
      <div class="message info">
        This is a recurring service. Editing will create a modification for this date only.
        <a href="/admin/recurring.php?edit=<?= e($event['recurring_id']) ?>">Edit the recurring template instead</a>.
      </div>
    <?php endif; ?>

    <div class="grid">
      <div class="card">
        <h2><?= $event ? 'Edit Event' : 'New Event' ?></h2>
        <form method="POST">
          <?php if ($event): ?>
            <input type="hidden" name="edit_id" value="<?= e($event['id']) ?>">
            <?php if (!empty($event['recurring_id'])): ?>
              <input type="hidden" name="recurring_id" value="<?= e($event['recurring_id']) ?>">
            <?php endif; ?>
          <?php endif; ?>

          <div class="form-row">
            <div class="form-group">
              <label for="date">Date</label>
              <input type="date" id="date" name="date" value="<?= e($event['date'] ?? $date) ?>" required>
            </div>
            <div class="form-group">
              <label for="time">Time</label>
              <input type="time" id="time" name="time" value="<?= e($event['time'] ?? '10:00') ?>" required>
            </div>
          </div>

          <div class="form-group">
            <label for="name">Event Name</label>
            <input type="text" id="name" name="name" value="<?= e($event['name'] ?? '') ?>" required placeholder="e.g. Divine Liturgy, Vespers, Special Event">
          </div>

          <div class="form-group">
            <label for="location_id">Location</label>
            <select id="location_id" name="location_id">
              <?php foreach ($locations as $loc): ?>
                <option value="<?= e($loc['id']) ?>" <?= ($event['location_id'] ?? 'temple') === $loc['id'] ? 'selected' : '' ?>><?= e($loc['name']) ?></option>
              <?php endforeach; ?>
            </select>
          </div>

          <div class="form-group">
            <label for="description">Description</label>
            <textarea id="description" name="description" rows="3" placeholder="Additional details about this event..."><?= e($event['description'] ?? '') ?></textarea>
          </div>

          <?php if (!$event || empty($event['recurring_id'])): ?>
          <div class="form-group">
            <label class="checkbox-group">
              <input type="checkbox" name="is_special" <?= ($event['is_special'] ?? false) ? 'checked' : '' ?>>
              Special Event (feast day, one-off event)
            </label>
          </div>
          <?php endif; ?>

          <div style="display: flex; gap: 12px; margin-top: 24px;">
            <button type="submit" class="btn btn-primary">Save Event</button>
            <a href="/admin/calendar.php?year=<?= date('Y', strtotime($date)) ?>&month=<?= date('n', strtotime($date)) ?>" class="btn btn-secondary">Cancel</a>
            <?php if ($event && !$is_new): ?>
              <a href="?id=<?= e($event['id']) ?>&date=<?= e($date) ?>&delete=<?= e($event['id']) ?>" class="btn btn-danger" onclick="return confirm('<?= $event['recurring_id'] ? 'Cancel this instance?' : 'Delete this event?' ?>')">
                <?= $event['recurring_id'] ? 'Cancel Instance' : 'Delete' ?>
              </a>
            <?php endif; ?>
          </div>
        </form>
      </div>

      <div class="card">
        <h2>Events on this Day</h2>
        <?php if (empty($date_events)): ?>
          <p style="color: #666;">No events scheduled.</p>
        <?php else: ?>
          <div class="event-list">
            <?php foreach ($date_events as $de):
              $loc = get_location($de['location_id']);
              $badge_class = 'badge-recurring';
              if ($de['is_special'] ?? false) $badge_class = 'badge-special';
              elseif ($de['is_modified'] ?? false) $badge_class = 'badge-modified';
            ?>
              <a href="?id=<?= e($de['id']) ?>&date=<?= e($date) ?>" class="event-item" style="text-decoration: none; color: inherit;">
                <div>
                  <div class="event-time"><?= e(format_time($de['time'])) ?></div>
                  <div class="event-name"><?= e($de['name']) ?></div>
                  <div class="event-location"><?= e($loc['short'] ?? '') ?></div>
                </div>
                <span class="badge <?= $badge_class ?>"><?= ($de['is_special'] ?? false) ? 'Special' : (($de['is_modified'] ?? false) ? 'Modified' : 'Recurring') ?></span>
              </a>
            <?php endforeach; ?>
          </div>
        <?php endif; ?>
      </div>
    </div>
  </div>
</body>
</html>
