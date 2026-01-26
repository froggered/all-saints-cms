<?php
require_once 'includes/functions.php';

// Get article by slug or ID
$slug = $_GET['slug'] ?? '';
$id = $_GET['id'] ?? '';

$news = load_data('news');
$article = null;

foreach ($news as $post) {
    if (($slug && ($post['slug'] ?? '') === $slug) || ($id && ($post['id'] ?? '') == $id)) {
        $article = $post;
        break;
    }
}

// 404 if not found
if (!$article) {
    http_response_code(404);
    $page_title = 'Article Not Found';
    require_once 'includes/header.php';
    echo '<section class="py-20 bg-white"><div class="max-w-4xl mx-auto px-6 text-center">';
    echo '<h1 class="font-heading text-3xl text-navy mb-4">Article Not Found</h1>';
    echo '<p class="text-slate mb-8">The article you\'re looking for doesn\'t exist.</p>';
    echo '<a href="/news" class="btn btn-primary">Back to News</a>';
    echo '</div></section>';
    require_once 'includes/footer.php';
    exit;
}

$page_title = $article['title'];
$page_description = $article['excerpt'] ?? '';
require_once 'includes/header.php';
?>

<!-- Article Header -->
<section class="bg-navy text-white py-16">
  <div class="max-w-4xl mx-auto px-6">
    <a href="/news" class="inline-flex items-center gap-2 text-white/70 hover:text-gold mb-6 text-sm">
      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
      Back to News
    </a>
    <div class="flex items-center gap-3 mb-4">
      <span class="px-3 py-1 rounded-full text-xs font-heading uppercase tracking-wide <?= ($article['featured'] ?? false) ? 'bg-gold text-navy' : 'bg-white/20 text-white' ?>">
        <?= ($article['featured'] ?? false) ? 'Newsletter' : 'News' ?>
      </span>
      <span class="text-white/70 text-sm"><?= format_date($article['date']) ?></span>
      <?php if (!empty($article['author'])): ?>
      <span class="text-white/70 text-sm">by <?= e($article['author']) ?></span>
      <?php endif; ?>
    </div>
    <h1 class="text-3xl md:text-4xl font-heading uppercase tracking-wide"><?= e($article['title']) ?></h1>
  </div>
</section>

<!-- Header Image -->
<?php if (!empty($article['image'])): ?>
<div class="max-w-5xl mx-auto px-6 -mt-8">
  <picture>
    <?php if (!empty($article['image']['webp'])): ?>
    <source srcset="<?= e($article['image']['webp']) ?>" type="image/webp">
    <?php endif; ?>
    <img src="<?= e($article['image']['path']) ?>" alt="<?= e($article['title']) ?>" class="w-full h-64 md:h-96 object-cover rounded-xl shadow-lg" loading="lazy">
  </picture>
</div>
<?php endif; ?>

<!-- Article Content -->
<section class="py-12 bg-white">
  <div class="max-w-4xl mx-auto px-6">
    <?php if (!empty($article['body'])): ?>
    <div class="prose prose-lg max-w-none text-charcoal whitespace-pre-line mb-8">
<?= e($article['body']) ?>
    </div>
    <?php endif; ?>

    <!-- Download Attachment -->
    <?php if (!empty($article['attachment'])): ?>
    <div class="bg-off-white rounded-xl p-6 mb-8">
      <h3 class="font-heading text-lg uppercase tracking-wide text-navy mb-3">Download</h3>
      <a href="<?= e($article['attachment']['path']) ?>" target="_blank" class="inline-flex items-center gap-3 px-6 py-3 bg-navy text-white rounded-lg hover:bg-charcoal transition-colors">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
        <div>
          <div class="font-heading uppercase tracking-wide"><?= e($article['attachment']['original_name']) ?></div>
          <div class="text-white/70 text-sm"><?= strtoupper($article['attachment']['type'] ?? 'PDF') ?> - <?= number_format(($article['attachment']['size'] ?? 0) / 1024, 0) ?> KB</div>
        </div>
      </a>
    </div>
    <?php endif; ?>

    <!-- Image Gallery -->
    <?php if (!empty($article['gallery'])): ?>
    <div class="mb-8">
      <h3 class="font-heading text-lg uppercase tracking-wide text-navy mb-4">Photo Gallery</h3>
      <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
        <?php foreach ($article['gallery'] as $index => $img): ?>
        <button type="button" onclick="openLightbox(<?= $index ?>)" class="block group text-left">
          <picture>
            <?php if (!empty($img['webp'])): ?>
            <source srcset="<?= e($img['webp']) ?>" type="image/webp">
            <?php endif; ?>
            <img src="<?= e($img['path']) ?>" alt="" class="w-full h-40 object-cover rounded-lg shadow-md group-hover:shadow-xl transition-shadow cursor-pointer" loading="lazy">
          </picture>
        </button>
        <?php endforeach; ?>
      </div>
    </div>

    <!-- Lightbox Modal -->
    <div id="lightbox" class="fixed inset-0 bg-black/95 z-50 hidden items-center justify-center" onclick="closeLightbox(event)">
      <button onclick="closeLightbox()" class="absolute top-4 right-4 text-white/70 hover:text-white text-4xl font-light z-10">&times;</button>
      <button onclick="prevImage(event)" class="absolute left-4 top-1/2 -translate-y-1/2 text-white/70 hover:text-white text-5xl font-light z-10 p-4">&#8249;</button>
      <button onclick="nextImage(event)" class="absolute right-4 top-1/2 -translate-y-1/2 text-white/70 hover:text-white text-5xl font-light z-10 p-4">&#8250;</button>
      <div class="max-w-5xl max-h-[90vh] px-16">
        <img id="lightbox-img" src="" alt="" class="max-w-full max-h-[85vh] object-contain mx-auto">
        <div id="lightbox-counter" class="text-white/70 text-center mt-4 font-heading"></div>
      </div>
    </div>

    <script>
      const galleryImages = <?= json_encode(array_map(fn($img) => [
          'src' => $img['path'],
          'webp' => $img['webp'] ?? null
      ], $article['gallery'])) ?>;
      let currentIndex = 0;

      function openLightbox(index) {
        currentIndex = index;
        updateLightbox();
        document.getElementById('lightbox').classList.remove('hidden');
        document.getElementById('lightbox').classList.add('flex');
        document.body.style.overflow = 'hidden';
      }

      function closeLightbox(event) {
        if (event && event.target !== event.currentTarget) return;
        document.getElementById('lightbox').classList.add('hidden');
        document.getElementById('lightbox').classList.remove('flex');
        document.body.style.overflow = '';
      }

      function prevImage(event) {
        event.stopPropagation();
        currentIndex = (currentIndex - 1 + galleryImages.length) % galleryImages.length;
        updateLightbox();
      }

      function nextImage(event) {
        event.stopPropagation();
        currentIndex = (currentIndex + 1) % galleryImages.length;
        updateLightbox();
      }

      function updateLightbox() {
        const img = galleryImages[currentIndex];
        // Use WebP if available and browser supports it
        const src = (img.webp && document.createElement('canvas').toDataURL('image/webp').indexOf('data:image/webp') === 0) ? img.webp : img.src;
        document.getElementById('lightbox-img').src = src;
        document.getElementById('lightbox-counter').textContent = (currentIndex + 1) + ' / ' + galleryImages.length;
      }

      // Keyboard navigation
      document.addEventListener('keydown', function(e) {
        const lightbox = document.getElementById('lightbox');
        if (lightbox.classList.contains('hidden')) return;
        if (e.key === 'Escape') closeLightbox();
        if (e.key === 'ArrowLeft') { e.preventDefault(); prevImage(e); }
        if (e.key === 'ArrowRight') { e.preventDefault(); nextImage(e); }
      });
    </script>
    <?php endif; ?>

    <!-- Share / Back -->
    <div class="border-t border-black/10 pt-8 mt-8 flex flex-wrap gap-4 justify-between items-center">
      <a href="/news" class="text-gold font-heading text-sm uppercase tracking-wide hover:underline">
        ← Back to All News
      </a>
    </div>
  </div>
</section>

<?php require_once 'includes/footer.php'; ?>
