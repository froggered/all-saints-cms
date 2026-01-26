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
            'body' => trim($_POST['body'] ?? ''),
            'attachment' => null,
            'image' => null,
            'gallery' => []
        ];

        // Keep existing files if editing
        if ($id) {
            foreach ($news as $n) {
                if ($n['id'] == $id) {
                    $item['attachment'] = $n['attachment'] ?? null;
                    $item['image'] = $n['image'] ?? null;
                    $item['gallery'] = $n['gallery'] ?? [];
                    break;
                }
            }
        }

        // Handle document upload
        if (!empty($_FILES['attachment']['name'])) {
            $allowed_extensions = ['pdf', 'doc', 'docx'];
            $file = $_FILES['attachment'];
            $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

            if ($file['error'] === UPLOAD_ERR_OK) {
                if (in_array($ext, $allowed_extensions)) {
                    $upload_dir = __DIR__ . '/../uploads/newsletters/';
                    $filename = date('Y-m-d') . '-' . generate_slug($item['title']) . '.' . $ext;
                    $filepath = $upload_dir . $filename;

                    if (move_uploaded_file($file['tmp_name'], $filepath)) {
                        $item['attachment'] = [
                            'filename' => $filename,
                            'original_name' => $file['name'],
                            'path' => '/uploads/newsletters/' . $filename,
                            'type' => $ext,
                            'size' => $file['size']
                        ];
                    } else {
                        $error = 'Failed to upload document.';
                    }
                } else {
                    $error = 'Invalid document type. Only PDF, DOC, and DOCX are allowed.';
                }
            } elseif ($file['error'] !== UPLOAD_ERR_NO_FILE) {
                $error = 'Document upload error: ' . $file['error'];
            }
        }

        // Handle image upload
        if (!empty($_FILES['image']['name'])) {
            $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
            $file = $_FILES['image'];
            $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

            if ($file['error'] === UPLOAD_ERR_OK) {
                if (in_array($ext, $allowed_extensions)) {
                    $upload_dir = __DIR__ . '/../uploads/news-images/';
                    $filename = date('Y-m-d') . '-' . generate_slug($item['title']) . '-' . time() . '.' . $ext;
                    $filepath = $upload_dir . $filename;

                    if (move_uploaded_file($file['tmp_name'], $filepath)) {
                        // Delete old image if replacing
                        if ($item['image'] && !empty($item['image']['path'])) {
                            $old_path = __DIR__ . '/..' . $item['image']['path'];
                            if (file_exists($old_path)) {
                                unlink($old_path);
                            }
                            // Also delete old WebP version
                            $old_webp = preg_replace('/\.(jpg|jpeg|png|gif)$/i', '.webp', $old_path);
                            if (file_exists($old_webp)) {
                                unlink($old_webp);
                            }
                        }
                        // Convert to WebP
                        $webp_path = convert_to_webp($filepath);
                        $item['image'] = [
                            'filename' => $filename,
                            'original_name' => $file['name'],
                            'path' => '/uploads/news-images/' . $filename,
                            'type' => $ext,
                            'size' => $file['size'],
                            'webp' => $webp_path ? '/uploads/news-images/' . basename($webp_path) : null
                        ];
                    } else {
                        $error = 'Failed to upload image.';
                    }
                } else {
                    $error = 'Invalid image type. Only JPG, PNG, GIF, and WebP are allowed.';
                }
            } elseif ($file['error'] !== UPLOAD_ERR_NO_FILE) {
                $error = 'Image upload error: ' . $file['error'];
            }
        }

        // Handle attachment removal
        if (isset($_POST['remove_attachment']) && $_POST['remove_attachment'] === '1') {
            if ($item['attachment'] && !empty($item['attachment']['path'])) {
                $filepath = __DIR__ . '/..' . $item['attachment']['path'];
                if (file_exists($filepath)) {
                    unlink($filepath);
                }
            }
            $item['attachment'] = null;
        }

        // Handle image removal
        if (isset($_POST['remove_image']) && $_POST['remove_image'] === '1') {
            if ($item['image'] && !empty($item['image']['path'])) {
                $filepath = __DIR__ . '/..' . $item['image']['path'];
                if (file_exists($filepath)) {
                    unlink($filepath);
                }
            }
            $item['image'] = null;
        }

        // Handle gallery uploads (multiple files)
        if (!empty($_FILES['gallery']['name'][0])) {
            $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
            $upload_dir = __DIR__ . '/../uploads/gallery/';

            foreach ($_FILES['gallery']['name'] as $key => $name) {
                if ($_FILES['gallery']['error'][$key] === UPLOAD_ERR_OK) {
                    $ext = strtolower(pathinfo($name, PATHINFO_EXTENSION));
                    if (in_array($ext, $allowed_extensions)) {
                        $filename = date('Y-m-d') . '-' . generate_slug($item['title']) . '-' . uniqid() . '.' . $ext;
                        $filepath = $upload_dir . $filename;

                        if (move_uploaded_file($_FILES['gallery']['tmp_name'][$key], $filepath)) {
                            // Convert to WebP
                            $webp_path = convert_to_webp($filepath);
                            $item['gallery'][] = [
                                'filename' => $filename,
                                'original_name' => $name,
                                'path' => '/uploads/gallery/' . $filename,
                                'type' => $ext,
                                'size' => $_FILES['gallery']['size'][$key],
                                'webp' => $webp_path ? '/uploads/gallery/' . basename($webp_path) : null
                            ];
                        }
                    }
                }
            }
        }

        // Handle gallery image removal
        if (!empty($_POST['remove_gallery'])) {
            $to_remove = $_POST['remove_gallery'];
            $new_gallery = [];
            foreach ($item['gallery'] as $img) {
                if (in_array($img['filename'], $to_remove)) {
                    $filepath = __DIR__ . '/..' . $img['path'];
                    if (file_exists($filepath)) {
                        unlink($filepath);
                    }
                    // Also delete WebP version
                    if (!empty($img['webp'])) {
                        $webp_filepath = __DIR__ . '/..' . $img['webp'];
                        if (file_exists($webp_filepath)) {
                            unlink($webp_filepath);
                        }
                    }
                } else {
                    $new_gallery[] = $img;
                }
            }
            $item['gallery'] = $new_gallery;
        }

        if (empty($item['title'])) {
            $error = 'Title is required';
        } elseif (!$error) {
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
        // Delete associated files
        foreach ($news as $n) {
            if (($n['id'] ?? 0) == $id) {
                // Delete attachment
                if (!empty($n['attachment']['path'])) {
                    $filepath = __DIR__ . '/..' . $n['attachment']['path'];
                    if (file_exists($filepath)) {
                        unlink($filepath);
                    }
                }
                // Delete header image
                if (!empty($n['image']['path'])) {
                    $filepath = __DIR__ . '/..' . $n['image']['path'];
                    if (file_exists($filepath)) {
                        unlink($filepath);
                    }
                }
                // Delete gallery images
                if (!empty($n['gallery'])) {
                    foreach ($n['gallery'] as $img) {
                        if (!empty($img['path'])) {
                            $filepath = __DIR__ . '/..' . $img['path'];
                            if (file_exists($filepath)) {
                                unlink($filepath);
                            }
                        }
                    }
                }
            }
        }
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

    .current-file {
      display: flex;
      align-items: center;
      gap: 8px;
      padding: 12px;
      background: #f8f9fa;
      border-radius: 8px;
      margin-bottom: 12px;
    }
    .current-file .file-icon { font-size: 1.5rem; }
    .current-file a { color: #1a1f3c; font-weight: 500; }
    .current-file a:hover { color: #c9a227; }
    .current-file .file-size { color: #666; font-size: 0.85rem; }
    .current-file .remove-file {
      margin-left: auto;
      display: flex;
      align-items: center;
      gap: 4px;
      color: #dc3545;
      font-size: 0.85rem;
      cursor: pointer;
    }
    .current-file .remove-file input { width: auto; }

    .form-group input[type="file"] {
      padding: 10px;
      border: 2px dashed #e0e0e0;
      border-radius: 8px;
      background: #fafafa;
      cursor: pointer;
    }
    .form-group input[type="file"]:hover {
      border-color: #c9a227;
    }
    .form-group .hint {
      font-size: 0.85rem;
      color: #666;
      margin-top: 6px;
    }

    .item-info .attachment-badge {
      display: inline-flex;
      align-items: center;
      gap: 4px;
      background: #e8f4fd;
      color: #1a5276;
      padding: 2px 8px;
      border-radius: 4px;
      font-size: 0.75rem;
      margin-left: 8px;
    }
    .item-info .image-badge {
      display: inline-flex;
      align-items: center;
      gap: 4px;
      background: #d4edda;
      color: #155724;
      padding: 2px 8px;
      border-radius: 4px;
      font-size: 0.75rem;
      margin-left: 8px;
    }

    .current-image {
      display: flex;
      gap: 16px;
      padding: 12px;
      background: #f8f9fa;
      border-radius: 8px;
      margin-bottom: 12px;
      align-items: flex-start;
    }
    .current-image img {
      width: 120px;
      height: 80px;
      object-fit: cover;
      border-radius: 6px;
      border: 2px solid #e0e0e0;
    }
    .current-image .image-info {
      display: flex;
      flex-direction: column;
      gap: 4px;
      font-size: 0.9rem;
    }
    .current-image .image-info .file-size {
      color: #666;
      font-size: 0.85rem;
    }

    .gallery-grid {
      display: grid;
      grid-template-columns: repeat(auto-fill, minmax(100px, 1fr));
      gap: 12px;
      margin-bottom: 12px;
    }
    .gallery-item {
      position: relative;
      border-radius: 8px;
      overflow: hidden;
      border: 2px solid #e0e0e0;
    }
    .gallery-item img {
      width: 100%;
      height: 80px;
      object-fit: cover;
      display: block;
    }
    .gallery-remove {
      position: absolute;
      bottom: 0;
      left: 0;
      right: 0;
      background: rgba(220, 53, 69, 0.9);
      color: white;
      font-size: 0.7rem;
      padding: 4px;
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 4px;
      cursor: pointer;
      opacity: 0;
      transition: opacity 0.2s;
    }
    .gallery-item:hover .gallery-remove { opacity: 1; }
    .gallery-remove input { width: auto; margin: 0; }

    .item-info .gallery-badge {
      display: inline-flex;
      align-items: center;
      gap: 4px;
      background: #ffeeba;
      color: #856404;
      padding: 2px 8px;
      border-radius: 4px;
      font-size: 0.75rem;
      margin-left: 8px;
    }
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
    <?php if ($message): ?>
    <div class="message success"><?= e($message) ?></div>
    <?php endif; ?>
    <?php if ($error): ?>
    <div class="message error"><?= e($error) ?></div>
    <?php endif; ?>

    <!-- Edit/Create Form -->
    <div class="form-card">
      <h3><?= $editing ? 'Edit News' : 'Add News' ?></h3>
      <form method="POST" enctype="multipart/form-data">
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
          <label for="body">Content</label>
          <textarea id="body" name="body" rows="6"><?= e($editing['body'] ?? '') ?></textarea>
        </div>

        <div class="form-group">
          <label for="image">Header Image (JPG, PNG, GIF, WebP)</label>
          <?php if (!empty($editing['image'])): ?>
          <div class="current-image">
            <img src="<?= e($editing['image']['path']) ?>" alt="Current image">
            <div class="image-info">
              <span><?= e($editing['image']['original_name']) ?></span>
              <span class="file-size">(<?= number_format($editing['image']['size'] / 1024, 1) ?> KB)</span>
              <label class="remove-file">
                <input type="checkbox" name="remove_image" value="1"> Remove
              </label>
            </div>
          </div>
          <?php endif; ?>
          <input type="file" id="image" name="image" accept=".jpg,.jpeg,.png,.gif,.webp">
          <p class="hint">Main image shown at the top of the article.</p>
        </div>

        <div class="form-group">
          <label>Image Gallery</label>
          <?php if (!empty($editing['gallery'])): ?>
          <div class="gallery-grid">
            <?php foreach ($editing['gallery'] as $img): ?>
            <div class="gallery-item">
              <img src="<?= e($img['path']) ?>" alt="">
              <label class="gallery-remove">
                <input type="checkbox" name="remove_gallery[]" value="<?= e($img['filename']) ?>">
                <span>Remove</span>
              </label>
            </div>
            <?php endforeach; ?>
          </div>
          <?php endif; ?>
          <input type="file" id="gallery" name="gallery[]" accept=".jpg,.jpeg,.png,.gif,.webp" multiple>
          <p class="hint">Upload multiple images for a gallery. Hold Ctrl/Cmd to select multiple files.</p>
        </div>

        <div class="form-group">
          <label for="attachment">Document (PDF, DOC, DOCX)</label>
          <?php if (!empty($editing['attachment'])): ?>
          <div class="current-file">
            <span class="file-icon">📄</span>
            <a href="<?= e($editing['attachment']['path']) ?>" target="_blank"><?= e($editing['attachment']['original_name']) ?></a>
            <span class="file-size">(<?= number_format($editing['attachment']['size'] / 1024, 1) ?> KB)</span>
            <label class="remove-file">
              <input type="checkbox" name="remove_attachment" value="1"> Remove
            </label>
          </div>
          <?php endif; ?>
          <input type="file" id="attachment" name="attachment" accept=".pdf,.doc,.docx">
          <p class="hint">Upload a newsletter PDF or document for download.</p>
        </div>

        <div class="form-group">
          <label class="checkbox-group">
            <input type="checkbox" name="featured" <?= ($editing['featured'] ?? false) ? 'checked' : '' ?>>
            Newsletter (unchecked = News)
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
              <span class="badge">Newsletter</span>
              <?php endif; ?>
              <?php if (!empty($item['image'])): ?>
              <span class="image-badge">🖼️ Header</span>
              <?php endif; ?>
              <?php if (!empty($item['gallery'])): ?>
              <span class="gallery-badge">📷 <?= count($item['gallery']) ?> photos</span>
              <?php endif; ?>
              <?php if (!empty($item['attachment'])): ?>
              <span class="attachment-badge">📄 <?= strtoupper($item['attachment']['type'] ?? 'PDF') ?></span>
              <?php endif; ?>
            </h3>
            <p><?= format_date($item['date'] ?? '') ?> <?= $item['author'] ? '· ' . e($item['author']) : '' ?></p>
          </div>
          <div class="item-actions">
            <?php if (!empty($item['attachment'])): ?>
            <a href="<?= e($item['attachment']['path']) ?>" class="btn btn-secondary" target="_blank">View PDF</a>
            <?php endif; ?>
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
