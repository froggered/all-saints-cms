<?php
require_once 'includes/functions.php';
$page_title = 'About';
$clergy = get_clergy();
require_once 'includes/header.php';

$sections = [
  ['title' => 'Our Clergy', 'href' => '#clergy', 'description' => 'Meet the priests and deacons serving All Saints of Lincolnshire.'],
  ['title' => 'Ministries', 'href' => '/ministries.php', 'description' => 'Education, outreach, and service to our community and beyond.'],
  ['title' => 'Our History', 'href' => '/history.php', 'description' => 'From a mission in Louth to a thriving parish in Lincoln.'],
  ['title' => 'Contact', 'href' => '/contact.php', 'description' => 'Get in touch with us or find directions to our church.'],
];
?>

<!-- Page Header -->
<section class="bg-navy text-white py-20">
  <div class="max-w-4xl mx-auto px-6 text-center">
    <h1 class="text-4xl md:text-5xl font-heading uppercase tracking-wide mb-4">About Us</h1>
    <div class="w-16 h-1 bg-gold mx-auto mb-6"></div>
    <p class="text-white/70 text-lg">Learn more about All Saints of Lincolnshire</p>
  </div>
</section>

<!-- Quick Links -->
<section class="py-16 bg-white">
  <div class="max-w-4xl mx-auto px-6">
    <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-6">
      <?php foreach ($sections as $section): ?>
      <a href="<?= e($section['href']) ?>" class="block p-6 bg-off-white rounded-xl border border-black/5 hover:shadow-lg hover:-translate-y-1 transition-all duration-200">
        <h3 class="font-heading text-xl uppercase tracking-wide text-navy mb-2"><?= e($section['title']) ?></h3>
        <p class="text-slate text-sm"><?= e($section['description']) ?></p>
        <span class="inline-block mt-4 text-gold font-heading text-sm uppercase tracking-wide">Learn More →</span>
      </a>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<!-- Clergy Section -->
<section id="clergy" class="py-20 bg-off-white">
  <div class="max-w-6xl mx-auto px-6">
    <div class="section-header">
      <h2 class="text-navy">Our Clergy</h2>
      <div class="section-divider"></div>
      <p class="text-slate max-w-xl mx-auto">Meet the priests and deacons serving our parish</p>
    </div>

    <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
      <?php foreach ($clergy as $index => $person): ?>
      <div class="bg-white rounded-xl overflow-hidden shadow-sm hover:shadow-lg transition-shadow cursor-pointer" onclick="openClergyModal(<?= $index ?>)">
        <div class="aspect-[4/3] bg-navy/10">
          <img src="<?= e($person['photo']) ?>" alt="<?= e($person['title']) ?>" class="w-full h-full object-cover" onerror="this.src='data:image/svg+xml,<svg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 400 300%22><rect fill=%22%231a1f3c%22 width=%22400%22 height=%22300%22/><text x=%22200%22 y=%22150%22 fill=%22%23c9a227%22 font-size=%2280%22 text-anchor=%22middle%22 dominant-baseline=%22middle%22>☦</text></svg>'">
        </div>
        <div class="p-6">
          <h3 class="font-heading text-xl uppercase tracking-wide text-navy mb-1"><?= e($person['title']) ?></h3>
          <p class="text-gold font-medium text-sm mb-4"><?= e($person['role']) ?></p>
          <p class="text-slate text-sm line-clamp-3"><?= e(substr($person['bio'] ?? '', 0, 150)) ?>...</p>
          <span class="inline-block mt-4 text-gold font-heading text-sm uppercase tracking-wide hover:underline">Read More →</span>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<!-- Clergy Modal -->
<div id="clergy-modal" class="fixed inset-0 z-50 hidden" onclick="closeClergyModal(event)">
  <div class="absolute inset-0 bg-black/60 backdrop-blur-sm"></div>
  <div class="absolute inset-4 md:inset-8 lg:inset-16 flex items-center justify-center">
    <div class="bg-white rounded-2xl max-w-2xl w-full max-h-full overflow-auto shadow-2xl" onclick="event.stopPropagation()">
      <div class="relative">
        <button onclick="closeClergyModal()" class="absolute top-4 right-4 z-10 w-10 h-10 bg-white/90 rounded-full flex items-center justify-center text-navy hover:bg-gold hover:text-white transition-colors shadow-lg">
          <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
        </button>
        <div id="modal-photo" class="aspect-[16/9] bg-navy/10">
          <img src="" alt="" class="w-full h-full object-cover">
        </div>
      </div>
      <div class="p-8">
        <h3 id="modal-title" class="font-heading text-2xl uppercase tracking-wide text-navy mb-1"></h3>
        <p id="modal-role" class="text-gold font-medium mb-6"></p>
        <div id="modal-bio" class="text-charcoal leading-relaxed whitespace-pre-line"></div>
      </div>
    </div>
  </div>
</div>

<script>
const clergyData = <?= json_encode($clergy) ?>;

function openClergyModal(index) {
  const person = clergyData[index];
  const modal = document.getElementById('clergy-modal');
  const photo = document.querySelector('#modal-photo img');

  photo.src = person.photo;
  photo.alt = person.title;
  photo.onerror = function() {
    this.src = 'data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 400 225"><rect fill="#1a1f3c" width="400" height="225"/><text x="200" y="112" fill="#c9a227" font-size="60" text-anchor="middle" dominant-baseline="middle">☦</text></svg>';
  };
  document.getElementById('modal-title').textContent = person.title;
  document.getElementById('modal-role').textContent = person.role;
  document.getElementById('modal-bio').textContent = person.bio || '';

  modal.classList.remove('hidden');
  document.body.style.overflow = 'hidden';
}

function closeClergyModal(event) {
  if (event && event.target !== event.currentTarget) return;
  const modal = document.getElementById('clergy-modal');
  modal.classList.add('hidden');
  document.body.style.overflow = '';
}

document.addEventListener('keydown', (e) => {
  if (e.key === 'Escape') closeClergyModal();
});
</script>

<!-- Ministries & History Section -->
<section class="py-20 bg-white">
  <div class="max-w-6xl mx-auto px-6">
    <div class="grid md:grid-cols-2 gap-8">
      <!-- Ministries Excerpt -->
      <div class="bg-off-white rounded-xl p-8">
        <h3 class="text-2xl text-navy mb-4 font-heading uppercase tracking-wide">Our Ministries</h3>
        <p class="text-charcoal mb-4">
          All Saints of Lincolnshire is active in education, outreach, and service. From catechumen classes
          to food banks, prison ministry to university chaplaincy, we seek to serve God and neighbour.
        </p>
        <p class="text-slate text-sm mb-6">
          Catechumenate Classes, E-Quip Sessions, Food Bank, Homeless Outreach, Prison Ministry, University Ministry, and more.
        </p>
        <a href="/ministries" class="btn btn-primary">View All Ministries</a>
      </div>

      <!-- History Excerpt -->
      <div class="bg-navy text-white rounded-xl p-8">
        <h3 class="text-2xl mb-4 font-heading uppercase tracking-wide text-gold">Our History</h3>
        <p class="text-white/90 mb-4">
          All Saints of Lincolnshire grew as a mission from Louth parish. By 2005 it had grown to a point
          where it needed to meet weekly, and Fr. Philip moved from Louth to Lincoln to serve the growing community.
        </p>
        <p class="text-white/70 text-sm mb-6">
          Since the start, we have always had catechumens—most of whom have never attended a church before.
        </p>
        <a href="/history" class="btn btn-primary">Read Full History</a>
      </div>
    </div>
  </div>
</section>

<?php require_once 'includes/footer.php'; ?>
