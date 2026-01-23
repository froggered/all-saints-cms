<?php
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/../includes/functions.php';

require_login();

// Handle logout
if (isset($_GET['logout'])) {
    logout();
}

$news_count = count(load_data('news'));
$events_count = count(load_data('events'));
$clergy_count = count(load_data('clergy'));
$services_count = count(load_data('services'));
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Dashboard | All Saints Admin</title>
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
      display: flex;
      align-items: center;
      gap: 10px;
    }
    .header h1 span { color: #c9a227; }
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
      max-width: 1200px;
      margin: 0 auto;
      padding: 24px;
    }

    .welcome {
      margin-bottom: 32px;
    }
    .welcome h2 {
      font-family: 'Oswald', sans-serif;
      font-size: 1.75rem;
      text-transform: uppercase;
      letter-spacing: 0.05em;
      color: #1a1f3c;
      margin-bottom: 8px;
    }
    .welcome p { color: #666; }

    .grid {
      display: grid;
      grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
      gap: 20px;
    }

    .card {
      background: white;
      border-radius: 12px;
      padding: 24px;
      box-shadow: 0 2px 8px rgba(0,0,0,0.08);
      text-decoration: none;
      color: inherit;
      transition: transform 0.2s, box-shadow 0.2s;
      display: block;
    }
    .card:hover {
      transform: translateY(-2px);
      box-shadow: 0 4px 16px rgba(0,0,0,0.12);
    }
    .card .icon {
      font-size: 2.5rem;
      margin-bottom: 16px;
    }
    .card h3 {
      font-family: 'Oswald', sans-serif;
      font-size: 1.25rem;
      text-transform: uppercase;
      letter-spacing: 0.05em;
      color: #1a1f3c;
      margin-bottom: 8px;
    }
    .card p {
      color: #666;
      font-size: 0.9rem;
      margin-bottom: 12px;
    }
    .card .count {
      font-size: 0.85rem;
      color: #c9a227;
      font-weight: 600;
    }

    .quick-links {
      margin-top: 32px;
      padding-top: 32px;
      border-top: 1px solid #e0e0e0;
    }
    .quick-links h3 {
      font-family: 'Oswald', sans-serif;
      font-size: 1rem;
      text-transform: uppercase;
      letter-spacing: 0.05em;
      color: #1a1f3c;
      margin-bottom: 16px;
    }
    .quick-links a {
      display: inline-block;
      padding: 10px 20px;
      background: #1a1f3c;
      color: white;
      text-decoration: none;
      border-radius: 6px;
      font-size: 0.9rem;
      margin-right: 10px;
      margin-bottom: 10px;
    }
    .quick-links a:hover { background: #2d2d4a; }
    .quick-links a.secondary {
      background: white;
      color: #1a1f3c;
      border: 2px solid #e0e0e0;
    }
    .quick-links a.secondary:hover {
      border-color: #c9a227;
    }
  </style>
</head>
<body>
  <header class="header">
    <h1><span>&#9766;</span> All Saints Admin</h1>
    <a href="?logout=1">Sign Out</a>
  </header>

  <div class="container">
    <div class="welcome">
      <h2>Dashboard</h2>
      <p>Manage your website content</p>
    </div>

    <div class="grid">
      <a href="/admin/news.php" class="card">
        <div class="icon">📰</div>
        <h3>News & Updates</h3>
        <p>Manage parish news and announcements</p>
        <span class="count"><?= $news_count ?> items</span>
      </a>

      <a href="/admin/events.php" class="card">
        <div class="icon">📅</div>
        <h3>Events</h3>
        <p>Manage upcoming events and feast days</p>
        <span class="count"><?= $events_count ?> items</span>
      </a>

      <a href="/admin/clergy.php" class="card">
        <div class="icon">⛪</div>
        <h3>Clergy</h3>
        <p>Manage clergy profiles</p>
        <span class="count"><?= $clergy_count ?> profiles</span>
      </a>

      <a href="/admin/services.php" class="card">
        <div class="icon">🕯️</div>
        <h3>Service Schedule</h3>
        <p>Update weekly service times</p>
        <span class="count"><?= $services_count ?> services</span>
      </a>
    </div>

    <div class="quick-links">
      <h3>Quick Links</h3>
      <a href="/" target="_blank">View Website</a>
      <a href="/admin/settings.php" class="secondary">Site Settings</a>
    </div>
  </div>
</body>
</html>
