<?php
require_once 'includes/functions.php';
$page_title = 'News & Updates';
$posts = get_all_news();
require_once 'includes/header.php';
?>

<!-- Page Header -->
<section class="bg-navy text-white py-20">
  <div class="max-w-4xl mx-auto px-6 text-center">
    <h1 class="text-4xl md:text-5xl font-heading uppercase tracking-wide mb-4">News & Updates</h1>
    <div class="w-16 h-1 bg-gold mx-auto mb-6"></div>
    <p class="text-white/70 text-lg">The latest from All Saints of Lincolnshire</p>
  </div>
</section>

<section class="py-20 bg-white">
  <div class="max-w-4xl mx-auto px-6">
    <div class="space-y-8">
      <?php if (empty($posts)): ?>
      <p class="text-center text-slate py-12">No news posts yet. Check back soon!</p>
      <?php else: ?>
        <?php foreach ($posts as $post): ?>
        <article class="p-6 bg-off-white rounded-xl border border-black/5 hover:shadow-lg transition-shadow">
          <div class="flex items-center gap-3 mb-3">
            <span class="px-3 py-1 rounded-full text-xs font-heading uppercase tracking-wide <?= ($post['featured'] ?? false) ? 'bg-gold text-navy' : 'bg-navy text-white' ?>">
              <?= ($post['featured'] ?? false) ? 'Newsletter' : 'News' ?>
            </span>
            <span class="text-slate text-sm"><?= format_date($post['date']) ?></span>
          </div>
          <h2 class="font-heading text-xl uppercase tracking-wide text-navy mb-3"><?= e($post['title']) ?></h2>
          <p class="text-charcoal mb-4"><?= e($post['excerpt']) ?></p>
          <?php if (!empty($post['body'])): ?>
          <details class="mt-4">
            <summary class="text-gold font-heading text-sm uppercase tracking-wide cursor-pointer hover:underline">Read More →</summary>
            <div class="mt-4 pt-4 border-t border-black/10 text-charcoal whitespace-pre-line"><?= e($post['body']) ?></div>
          </details>
          <?php endif; ?>
        </article>
        <?php endforeach; ?>
      <?php endif; ?>
    </div>

    <div class="mt-12 p-6 bg-navy text-white rounded-xl text-center">
      <h3 class="font-heading text-lg uppercase tracking-wide mb-3">Subscribe to Our Newsletter</h3>
      <p class="text-white/70 text-sm mb-4">Receive Fr. Philip's monthly newsletter and parish updates directly in your inbox.</p>
      <p class="text-gold text-sm italic">Newsletter signup coming soon</p>
    </div>
  </div>
</section>

<?php require_once 'includes/footer.php'; ?>
