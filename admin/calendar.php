<?php
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/../includes/functions.php';

require_login();

// Get current month/year from query or default to current
$year = isset($_GET['year']) ? (int)$_GET['year'] : (int)date('Y');
$month = isset($_GET['month']) ? (int)$_GET['month'] : (int)date('n');

// Validate
if ($month < 1) { $month = 12; $year--; }
if ($month > 12) { $month = 1; $year++; }

$locations = get_locations();
$month_events = get_calendar_month($year, $month);

// Group events by date
$events_by_date = [];
foreach ($month_events as $event) {
    $date = $event['date'];
    if (!isset($events_by_date[$date])) {
        $events_by_date[$date] = [];
    }
    $events_by_date[$date][] = $event;
}

// Calendar data
$first_day = mktime(0, 0, 0, $month, 1, $year);
$days_in_month = (int)date('t', $first_day);
$start_weekday = (int)date('w', $first_day);
$month_name = date('F', $first_day);

$prev_month = $month - 1;
$prev_year = $year;
if ($prev_month < 1) { $prev_month = 12; $prev_year--; }

$next_month = $month + 1;
$next_year = $year;
if ($next_month > 12) { $next_month = 1; $next_year++; }

$message = '';
if (isset($_GET['saved'])) $message = 'Event saved successfully.';
if (isset($_GET['deleted'])) $message = 'Event deleted.';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Calendar | All Saints Admin</title>
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

    .container { max-width: 1400px; margin: 0 auto; padding: 24px; }

    .page-header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 24px;
      flex-wrap: wrap;
      gap: 16px;
    }
    .page-title {
      font-family: 'Oswald', sans-serif;
      font-size: 1.75rem;
      text-transform: uppercase;
      color: #1a1f3c;
    }
    .page-actions { display: flex; gap: 12px; }

    .message { padding: 12px 16px; border-radius: 8px; margin-bottom: 20px; background: #d4edda; color: #155724; }

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
    .btn-secondary { background: #1a1f3c; color: white; }
    .btn-secondary:hover { background: #2d2d4a; }

    .calendar-nav {
      display: flex;
      align-items: center;
      justify-content: space-between;
      background: white;
      padding: 16px 24px;
      border-radius: 12px 12px 0 0;
      border-bottom: 2px solid #f0f0f0;
    }
    .calendar-nav h2 {
      font-family: 'Oswald', sans-serif;
      font-size: 1.5rem;
      text-transform: uppercase;
      color: #1a1f3c;
    }
    .nav-buttons { display: flex; gap: 8px; }
    .nav-btn {
      padding: 8px 16px;
      border: 2px solid #e0e0e0;
      background: white;
      border-radius: 6px;
      cursor: pointer;
      font-weight: 600;
      text-decoration: none;
      color: #333;
    }
    .nav-btn:hover { border-color: #c9a227; }

    .calendar-grid {
      background: white;
      border-radius: 0 0 12px 12px;
      overflow: hidden;
    }
    .calendar-header {
      display: grid;
      grid-template-columns: repeat(7, 1fr);
      background: #1a1f3c;
      color: white;
    }
    .calendar-header div {
      padding: 12px;
      text-align: center;
      font-family: 'Oswald', sans-serif;
      text-transform: uppercase;
      font-size: 0.85rem;
    }
    .calendar-body {
      display: grid;
      grid-template-columns: repeat(7, 1fr);
    }
    .calendar-day {
      min-height: 120px;
      border: 1px solid #f0f0f0;
      padding: 8px;
      position: relative;
    }
    .calendar-day.empty { background: #fafafa; }
    .calendar-day.today { background: #fffde7; }
    .day-number {
      font-weight: 600;
      color: #333;
      margin-bottom: 6px;
    }
    .day-events {
      display: flex;
      flex-direction: column;
      gap: 4px;
    }
    .event-item {
      font-size: 0.7rem;
      padding: 3px 6px;
      border-radius: 4px;
      cursor: pointer;
      text-decoration: none;
      color: white;
      display: block;
      white-space: nowrap;
      overflow: hidden;
      text-overflow: ellipsis;
    }
    .event-item.church { background: #7c3aed; }
    .event-item.monastery { background: #1a1f3c; }
    .event-item.misc { background: #64748b; }
    .event-item.special { background: #c9a227; }
    .event-item.cancelled { background: #e74c3c; text-decoration: line-through; }
    .event-item:hover { opacity: 0.9; }

    .add-event-btn {
      position: absolute;
      top: 4px;
      right: 4px;
      width: 20px;
      height: 20px;
      border-radius: 50%;
      background: #e0e0e0;
      border: none;
      cursor: pointer;
      font-size: 14px;
      line-height: 1;
      color: #666;
      opacity: 0;
      transition: opacity 0.2s;
    }
    .calendar-day:hover .add-event-btn { opacity: 1; }
    .add-event-btn:hover { background: #c9a227; color: white; }

    .more-events {
      font-size: 0.7rem;
      color: #666;
      cursor: pointer;
    }
    .more-events:hover { color: #c9a227; }

    .legend {
      display: flex;
      gap: 16px;
      margin-top: 16px;
      flex-wrap: wrap;
    }
    .legend-item {
      display: flex;
      align-items: center;
      gap: 6px;
      font-size: 0.85rem;
    }
    .legend-color {
      width: 16px;
      height: 16px;
      border-radius: 4px;
    }
  </style>
</head>
<body>
  <header class="header">
    <a href="/admin/dashboard.php" class="header-logo"><span>&#9766;</span> All Saints Admin</a>
    <nav>
      <a href="/admin/dashboard.php">Dashboard</a>
      <a href="/admin/recurring.php">Recurring</a>
      <a href="/" target="_blank">View Site</a>
    </nav>
  </header>

  <div class="container">
    <div class="page-header">
      <h1 class="page-title">Calendar</h1>
      <div class="page-actions">
        <a href="/admin/recurring.php" class="btn btn-secondary">Manage Recurring Services</a>
        <a href="/admin/calendar-event.php?date=<?= date('Y-m-d') ?>" class="btn btn-primary">+ Add Event</a>
      </div>
    </div>

    <?php if ($message): ?>
      <div class="message"><?= e($message) ?></div>
    <?php endif; ?>

    <div class="calendar-nav">
      <div class="nav-buttons">
        <a href="?year=<?= $prev_year ?>&month=<?= $prev_month ?>" class="nav-btn">&larr; Prev</a>
        <a href="?year=<?= date('Y') ?>&month=<?= date('n') ?>" class="nav-btn">Today</a>
        <a href="?year=<?= $next_year ?>&month=<?= $next_month ?>" class="nav-btn">Next &rarr;</a>
      </div>
      <h2><?= $month_name ?> <?= $year ?></h2>
      <div></div>
    </div>

    <div class="calendar-grid">
      <div class="calendar-header">
        <div>Sun</div>
        <div>Mon</div>
        <div>Tue</div>
        <div>Wed</div>
        <div>Thu</div>
        <div>Fri</div>
        <div>Sat</div>
      </div>
      <div class="calendar-body">
        <?php
        // Empty cells before first day
        for ($i = 0; $i < $start_weekday; $i++):
        ?>
          <div class="calendar-day empty"></div>
        <?php endfor; ?>

        <?php
        // Days of month
        $today = date('Y-m-d');
        for ($day = 1; $day <= $days_in_month; $day++):
          $date = sprintf('%04d-%02d-%02d', $year, $month, $day);
          $day_events = $events_by_date[$date] ?? [];
          $is_today = ($date === $today);
        ?>
          <div class="calendar-day <?= $is_today ? 'today' : '' ?>">
            <div class="day-number"><?= $day ?></div>
            <a href="/admin/calendar-event.php?date=<?= $date ?>" class="add-event-btn" title="Add event">+</a>
            <div class="day-events">
              <?php
              $shown = 0;
              foreach ($day_events as $event):
                if ($shown >= 4) break;
                $shown++;
                // Color by type and location (Church = purple, Monastery = navy)
                $class = 'church'; // default
                if ($event['is_cancelled'] ?? false) {
                    $class = 'cancelled';
                } elseif ($event['is_special'] ?? false) {
                    $class = 'special';
                } elseif (($event['type'] ?? 'service') === 'misc') {
                    $class = 'misc';
                } elseif (($event['location_id'] ?? 'temple') === 'jasmin') {
                    $class = 'monastery';
                }
              ?>
                <a href="/admin/calendar-event.php?id=<?= urlencode($event['id']) ?>&date=<?= $date ?>" class="event-item <?= $class ?>" title="<?= e($event['name']) ?> - <?= e(format_time($event['time'])) ?>">
                  <?= e(format_time($event['time'])) ?> <?= e($event['name']) ?>
                </a>
              <?php endforeach; ?>
              <?php if (count($day_events) > 4): ?>
                <a href="/admin/calendar-event.php?date=<?= $date ?>" class="more-events">+<?= count($day_events) - 4 ?> more</a>
              <?php endif; ?>
            </div>
          </div>
        <?php endfor; ?>

        <?php
        // Empty cells after last day
        $total_cells = $start_weekday + $days_in_month;
        $remaining = (7 - ($total_cells % 7)) % 7;
        for ($i = 0; $i < $remaining; $i++):
        ?>
          <div class="calendar-day empty"></div>
        <?php endfor; ?>
      </div>
    </div>

    <div class="legend">
      <div class="legend-item">
        <div class="legend-color" style="background: #7c3aed;"></div>
        <span>The Church</span>
      </div>
      <div class="legend-item">
        <div class="legend-color" style="background: #1a1f3c;"></div>
        <span>The Monastery</span>
      </div>
      <div class="legend-item">
        <div class="legend-color" style="background: #64748b;"></div>
        <span>Activities</span>
      </div>
      <div class="legend-item">
        <div class="legend-color" style="background: #c9a227;"></div>
        <span>Special Event</span>
      </div>
      <div class="legend-item">
        <div class="legend-color" style="background: #e74c3c;"></div>
        <span>Cancelled</span>
      </div>
    </div>
  </div>
</body>
</html>
