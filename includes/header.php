<?php
$page_title = $page_title ?? 'Home';
$page_description = $page_description ?? 'Orthodox Christian Parish in Lincoln, UK. Part of the Antiochian Archdiocese.';
$site_title = $page_title . ' | All Saints of Lincolnshire';
$header_settings = get_settings();
$is_live = is_youtube_live();
$youtube_url = $header_settings['youtube'] ?? '#';
$youtube_live_url = !empty($youtube_url) ? rtrim($youtube_url, '/') . '/live' : '#';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="description" content="<?= e($page_description) ?>">
  <title><?= e($site_title) ?></title>
  <link rel="icon" type="image/svg+xml" href="/favicon.svg">

  <!-- Fonts -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Oswald:wght@400;500;600;700&family=Playfair+Display:ital,wght@0,400;0,700;1,400&display=swap" rel="stylesheet">

  <!-- Tailwind CSS CDN -->
  <script src="https://cdn.tailwindcss.com"></script>
  <script>
    tailwind.config = {
      theme: {
        extend: {
          colors: {
            navy: '#1a1f3c',
            charcoal: '#2d2d2d',
            'off-white': '#f8f6f3',
            gold: {
              DEFAULT: '#c9a227',
              light: '#d4af37',
            },
            burgundy: '#722f37',
            slate: '#64748b',
            ivory: '#fffef9',
            'live-red': '#dc2626',
          },
          fontFamily: {
            heading: ['Oswald', 'sans-serif'],
            body: ['Inter', 'sans-serif'],
            accent: ['Playfair Display', 'serif'],
          },
        },
      },
    }
  </script>

  <style>
    body {
      font-family: 'Inter', sans-serif;
      background-color: #f8f6f3;
      color: #2d2d2d;
    }
    h1, h2, h3, h4, h5, h6 {
      font-family: 'Oswald', sans-serif;
      text-transform: uppercase;
      letter-spacing: 0.05em;
    }
    .font-heading { font-family: 'Oswald', sans-serif; }
    .font-body { font-family: 'Inter', sans-serif; }
    .font-accent { font-family: 'Playfair Display', serif; }

    .btn {
      display: inline-flex;
      align-items: center;
      justify-content: center;
      padding: 0.75rem 1.5rem;
      border-radius: 0.25rem;
      font-family: 'Oswald', sans-serif;
      font-weight: 500;
      font-size: 0.875rem;
      text-transform: uppercase;
      letter-spacing: 0.05em;
      transition: all 0.2s;
      text-decoration: none;
    }
    .btn-primary {
      background-color: #c9a227;
      color: #1a1f3c;
    }
    .btn-primary:hover {
      background-color: #d4af37;
    }
    .btn-secondary {
      background-color: transparent;
      color: #c9a227;
      border: 2px solid #c9a227;
    }
    .btn-secondary:hover {
      background-color: rgba(201, 162, 39, 0.1);
    }
    .btn-live {
      background-color: #dc2626;
      color: white;
    }

    .section-header {
      text-align: center;
      margin-bottom: 3rem;
    }
    .section-header h2 {
      font-size: 2.25rem;
      margin-bottom: 0.5rem;
    }
    .section-divider {
      width: 4rem;
      height: 0.25rem;
      background-color: #c9a227;
      margin: 1rem auto;
    }
  </style>
</head>
<body>
  <!-- Navigation -->
  <nav class="fixed top-0 left-0 right-0 z-50 bg-navy">
    <div class="max-w-7xl mx-auto px-6 py-4 flex justify-between items-center">
      <a href="/" class="flex items-center gap-2 text-white font-heading text-xl font-semibold uppercase tracking-wide">
        <span class="text-gold text-2xl">&#9766;</span>
        All Saints
      </a>

      <!-- Desktop Navigation -->
      <ul class="hidden md:flex gap-8">
        <li><a href="/about" class="text-white/85 font-heading text-sm uppercase tracking-wide hover:text-gold transition-colors">About</a></li>
        <li><a href="/discover" class="text-white/85 font-heading text-sm uppercase tracking-wide hover:text-gold transition-colors">Discover</a></li>
        <li><a href="/services" class="text-white/85 font-heading text-sm uppercase tracking-wide hover:text-gold transition-colors">Services</a></li>
        <li><a href="/news" class="text-white/85 font-heading text-sm uppercase tracking-wide hover:text-gold transition-colors">News</a></li>
        <li><a href="/support" class="text-white/85 font-heading text-sm uppercase tracking-wide hover:text-gold transition-colors">Support</a></li>
        <li><a href="/contact" class="text-white/85 font-heading text-sm uppercase tracking-wide hover:text-gold transition-colors">Contact</a></li>
      </ul>

      <div class="flex items-center gap-4">
        <!-- Live/Watch Button -->
        <?php if ($is_live): ?>
        <a href="<?= e($youtube_live_url) ?>" target="_blank" rel="noopener" class="hidden sm:flex btn btn-live gap-2 py-2 px-4 text-xs">
          <span class="w-2 h-2 bg-white rounded-full animate-pulse"></span>
          Live
        </a>
        <?php else: ?>
        <a href="<?= e($youtube_url) ?>" target="_blank" rel="noopener" class="hidden sm:flex btn btn-secondary gap-2 py-2 px-4 text-xs">
          <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M23.498 6.186a3.016 3.016 0 0 0-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 0 0 .502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 0 0 2.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 0 0 2.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z"/></svg>
          Watch
        </a>
        <?php endif; ?>

        <!-- Mobile Menu Button -->
        <button id="mobile-menu-btn" class="md:hidden text-white p-2" aria-label="Toggle menu">
          <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
          </svg>
        </button>
      </div>
    </div>

    <!-- Mobile Navigation -->
    <div id="mobile-menu" class="hidden md:hidden bg-navy border-t border-white/10">
      <ul class="px-6 py-4 space-y-4">
        <li><a href="/about" class="block text-white/85 font-heading text-sm uppercase tracking-wide hover:text-gold transition-colors">About</a></li>
        <li><a href="/discover" class="block text-white/85 font-heading text-sm uppercase tracking-wide hover:text-gold transition-colors">Discover</a></li>
        <li><a href="/services" class="block text-white/85 font-heading text-sm uppercase tracking-wide hover:text-gold transition-colors">Services</a></li>
        <li><a href="/news" class="block text-white/85 font-heading text-sm uppercase tracking-wide hover:text-gold transition-colors">News</a></li>
        <li><a href="/support" class="block text-white/85 font-heading text-sm uppercase tracking-wide hover:text-gold transition-colors">Support</a></li>
        <li><a href="/contact" class="block text-white/85 font-heading text-sm uppercase tracking-wide hover:text-gold transition-colors">Contact</a></li>
      </ul>
    </div>
  </nav>

  <main class="pt-16">
