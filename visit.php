<?php
require_once 'includes/functions.php';
$page_title = 'Plan Your Visit';
$settings = get_settings();
$services = get_services();
require_once 'includes/header.php';
?>

<!-- Page Header -->
<section class="bg-navy text-white py-20">
  <div class="max-w-4xl mx-auto px-6 text-center">
    <h1 class="text-4xl md:text-5xl font-heading uppercase tracking-wide mb-4">Plan Your Visit</h1>
    <div class="w-16 h-1 bg-gold mx-auto mb-6"></div>
    <p class="text-white/70 text-lg">What to expect when visiting All Saints</p>
  </div>
</section>

<section class="py-20 bg-white">
  <div class="max-w-4xl mx-auto px-6">
    <div class="prose prose-lg max-w-none">
      <p class="text-charcoal text-xl mb-8">
        Whether you're Orthodox and new to the area, or exploring Orthodoxy for the first time, we warmly welcome you to All Saints of Lincolnshire.
      </p>

      <h2 class="text-2xl text-navy mt-12 mb-6 font-heading uppercase tracking-wide">When to Visit</h2>
      <p class="text-charcoal mb-6">
        The best service to attend for your first visit is the <strong>Sunday Divine Liturgy at 10:00 AM</strong>.
        This is our main weekly service and gives the fullest experience of Orthodox worship.
      </p>

      <div class="bg-off-white rounded-xl p-6 mb-8">
        <h3 class="font-heading text-lg uppercase tracking-wide text-navy mb-4">Weekly Services</h3>
        <div class="space-y-2">
          <?php foreach ($services as $service): ?>
          <div class="flex justify-between">
            <div>
              <span class="text-charcoal font-semibold"><?= e($service['day']) ?></span>
              <span class="text-slate text-sm ml-2"><?= e($service['name']) ?></span>
            </div>
            <span class="font-semibold"><?= e($service['time']) ?></span>
          </div>
          <?php endforeach; ?>
        </div>
      </div>

      <h2 class="text-2xl text-navy mt-12 mb-6 font-heading uppercase tracking-wide">What to Expect</h2>
      <ul class="text-charcoal space-y-4 mb-8">
        <li><strong>Length:</strong> The Divine Liturgy lasts approximately 1.5 to 2 hours. Feel free to arrive a few minutes early.</li>
        <li><strong>Standing:</strong> Orthodox Christians traditionally stand during services, though seating is available for those who need it.</li>
        <li><strong>Participation:</strong> As a visitor, you're welcome to observe quietly. The congregation will sing hymns and make responses throughout the service.</li>
        <li><strong>Communion:</strong> Holy Communion is reserved for baptised and chrismated Orthodox Christians who have prepared through fasting and confession. All are welcome to receive blessed bread (antidoron) at the end of the service.</li>
        <li><strong>Icons:</strong> You'll notice icons throughout the church. Orthodox Christians venerate (kiss) icons as a sign of respect. As a visitor, you may observe or participate as you feel comfortable.</li>
      </ul>

      <h2 class="text-2xl text-navy mt-12 mb-6 font-heading uppercase tracking-wide">What to Wear</h2>
      <p class="text-charcoal mb-6">
        There is no strict dress code, but modest dress is appreciated. Many men wear collared shirts and trousers;
        women often wear dresses or skirts, though trousers are perfectly acceptable. Head coverings for women are
        traditional but not required.
      </p>

      <h2 class="text-2xl text-navy mt-12 mb-6 font-heading uppercase tracking-wide">Location & Directions</h2>
      <div class="bg-navy text-white rounded-xl p-8 mb-8">
        <h3 class="text-xl font-heading uppercase tracking-wide text-gold mb-4">Saint Matthias Church Centre</h3>
        <p class="text-white/80 mb-2"><?= e($settings['address']['street'] ?? 'Burton Road') ?></p>
        <p class="text-white/80 mb-6"><?= e($settings['address']['city'] ?? 'Lincoln') ?>, <?= e($settings['address']['postcode'] ?? 'LN1 3TX') ?></p>
        <a href="<?= e($settings['googleMapsUrl'] ?? 'https://www.google.com/maps/dir/?api=1&destination=Saint+Matthias+Church+Centre+Burton+Road+Lincoln+LN1+3TX') ?>"
           class="btn btn-primary" target="_blank" rel="noopener">
          Get Directions
        </a>
      </div>

      <h2 class="text-2xl text-navy mt-12 mb-6 font-heading uppercase tracking-wide">Questions?</h2>
      <p class="text-charcoal">
        If you have any questions before your visit, please don't hesitate to <a href="/contact.php" class="text-gold hover:underline">contact Fr. Philip</a>.
        We look forward to welcoming you!
      </p>
    </div>
  </div>
</section>

<?php require_once 'includes/footer.php'; ?>
