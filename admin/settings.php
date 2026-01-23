<?php
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/../includes/functions.php';

require_login();

$settings = get_settings();
$message = '';
$error = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $settings['churchName'] = trim($_POST['churchName'] ?? '');
    $settings['email'] = trim($_POST['email'] ?? '');
    $settings['phone'] = trim($_POST['phone'] ?? '');
    $settings['address'] = [
        'street' => trim($_POST['street'] ?? ''),
        'city' => trim($_POST['city'] ?? ''),
        'postcode' => trim($_POST['postcode'] ?? '')
    ];
    $settings['youtube'] = trim($_POST['youtube'] ?? '');
    $settings['facebook'] = trim($_POST['facebook'] ?? '');
    $settings['googleMapsUrl'] = trim($_POST['googleMapsUrl'] ?? '');

    if (save_data('settings', $settings)) {
        $message = 'Settings saved successfully.';
    } else {
        $error = 'Failed to save settings.';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Site Settings | All Saints Admin</title>
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

    .container {
      max-width: 800px;
      margin: 0 auto;
      padding: 24px;
    }

    .page-title {
      font-family: 'Oswald', sans-serif;
      font-size: 1.75rem;
      text-transform: uppercase;
      letter-spacing: 0.05em;
      color: #1a1f3c;
      margin-bottom: 24px;
    }

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
      padding: 24px;
      box-shadow: 0 2px 8px rgba(0,0,0,0.08);
      margin-bottom: 24px;
    }

    .card h2 {
      font-family: 'Oswald', sans-serif;
      font-size: 1.1rem;
      text-transform: uppercase;
      letter-spacing: 0.05em;
      color: #1a1f3c;
      margin-bottom: 16px;
      padding-bottom: 12px;
      border-bottom: 2px solid #f0f0f0;
    }

    .form-group {
      margin-bottom: 16px;
    }
    .form-group label {
      display: block;
      font-weight: 500;
      margin-bottom: 6px;
      color: #333;
    }
    .form-group input,
    .form-group textarea {
      width: 100%;
      padding: 10px 14px;
      border: 2px solid #e0e0e0;
      border-radius: 8px;
      font-size: 1rem;
      font-family: inherit;
      transition: border-color 0.2s;
    }
    .form-group input:focus,
    .form-group textarea:focus {
      outline: none;
      border-color: #c9a227;
    }
    .form-group .hint {
      font-size: 0.85rem;
      color: #666;
      margin-top: 4px;
    }

    .form-row {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 16px;
    }
    @media (max-width: 600px) {
      .form-row { grid-template-columns: 1fr; }
    }

    .btn {
      display: inline-block;
      padding: 12px 24px;
      border: none;
      border-radius: 8px;
      font-size: 1rem;
      font-weight: 600;
      cursor: pointer;
      text-decoration: none;
      transition: transform 0.1s, box-shadow 0.2s;
    }
    .btn:active { transform: scale(0.98); }
    .btn-primary {
      background: #c9a227;
      color: white;
    }
    .btn-primary:hover { background: #b8931f; }
  </style>
</head>
<body>
  <header class="header">
    <h1><span>&#9766;</span> All Saints Admin</h1>
    <nav>
      <a href="/admin/dashboard.php">Dashboard</a>
      <a href="/" target="_blank">View Site</a>
    </nav>
  </header>

  <div class="container">
    <h1 class="page-title">Site Settings</h1>

    <?php if ($message): ?>
      <div class="message success"><?= e($message) ?></div>
    <?php endif; ?>

    <?php if ($error): ?>
      <div class="message error"><?= e($error) ?></div>
    <?php endif; ?>

    <form method="POST">
      <div class="card">
        <h2>General Information</h2>
        <div class="form-group">
          <label for="churchName">Church Name</label>
          <input type="text" id="churchName" name="churchName" value="<?= e($settings['churchName'] ?? '') ?>">
        </div>
        <div class="form-row">
          <div class="form-group">
            <label for="email">Email Address</label>
            <input type="email" id="email" name="email" value="<?= e($settings['email'] ?? '') ?>">
          </div>
          <div class="form-group">
            <label for="phone">Phone Number</label>
            <input type="tel" id="phone" name="phone" value="<?= e($settings['phone'] ?? '') ?>" placeholder="Optional">
          </div>
        </div>
      </div>

      <div class="card">
        <h2>Address</h2>
        <div class="form-group">
          <label for="street">Street Address</label>
          <input type="text" id="street" name="street" value="<?= e($settings['address']['street'] ?? '') ?>">
        </div>
        <div class="form-row">
          <div class="form-group">
            <label for="city">City</label>
            <input type="text" id="city" name="city" value="<?= e($settings['address']['city'] ?? '') ?>">
          </div>
          <div class="form-group">
            <label for="postcode">Postcode</label>
            <input type="text" id="postcode" name="postcode" value="<?= e($settings['address']['postcode'] ?? '') ?>">
          </div>
        </div>
      </div>

      <div class="card">
        <h2>Social Media & Links</h2>
        <div class="form-group">
          <label for="youtube">YouTube Channel URL</label>
          <input type="url" id="youtube" name="youtube" value="<?= e($settings['youtube'] ?? '') ?>" placeholder="https://www.youtube.com/@...">
        </div>
        <div class="form-group">
          <label for="facebook">Facebook Page URL</label>
          <input type="url" id="facebook" name="facebook" value="<?= e($settings['facebook'] ?? '') ?>" placeholder="https://www.facebook.com/...">
        </div>
        <div class="form-group">
          <label for="googleMapsUrl">Google Maps Directions URL</label>
          <input type="url" id="googleMapsUrl" name="googleMapsUrl" value="<?= e($settings['googleMapsUrl'] ?? '') ?>">
          <p class="hint">The URL for the "Get Directions" button on the Contact page.</p>
        </div>
      </div>

      <button type="submit" class="btn btn-primary">Save Settings</button>
    </form>
  </div>
</body>
</html>
