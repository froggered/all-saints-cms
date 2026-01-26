<?php
require_once 'includes/functions.php';

$page_title = 'Home';
$recurring = get_recurring_services();
$locations = get_locations();
$settings = get_settings();
// Get special events for homepage (excludes recurring services)
$upcoming_events = get_upcoming_special_events(3);
$recent_news = get_recent_news(4);
$featured_news = array_filter($recent_news, fn($n) => $n['featured'] ?? false);
$featured_news = !empty($featured_news) ? array_values($featured_news)[0] : ($recent_news[0] ?? null);

require_once 'includes/header.php';
?>

<!-- Hero Section -->
<section class="relative min-h-screen flex items-center justify-center bg-navy pt-20">
  <!-- Background Image -->
  <div class="absolute inset-0">
    <img src="/images/pantocrator-dome.jpg" alt="" class="w-full h-full object-cover opacity-100" />
    <div class="absolute inset-0" style="background: linear-gradient(135deg, rgb(60 26 26 / 73%) 0%, rgb(14 17 45 / 59%) 100%);"></div>
  </div>

  <!-- Content -->
  <div class="relative z-10 text-center text-white max-w-3xl px-6 py-12">
    <!-- Orthodox Cross SVG -->
    <div class="w-24 h-32 mx-auto mb-8" style="filter: drop-shadow(0 0 20px rgba(201, 162, 39, 0.6));">
      <svg class="w-full h-full" viewBox="0 0 80 120" xmlns="http://www.w3.org/2000/svg">
        <line x1="30" y1="15" x2="50" y2="15" stroke="#c9a227" stroke-width="4" stroke-linecap="round" />
        <line x1="40" y1="5" x2="40" y2="115" stroke="#c9a227" stroke-width="4" stroke-linecap="round" />
        <line x1="15" y1="40" x2="65" y2="40" stroke="#c9a227" stroke-width="4" stroke-linecap="round" />
        <line x1="22" y1="90" x2="58" y2="80" stroke="#c9a227" stroke-width="4" stroke-linecap="round" />
      </svg>
    </div>

    <h1 class="text-4xl md:text-5xl lg:text-6xl font-bold tracking-wide mb-4 font-heading uppercase">
      All Saints of Lincolnshire
    </h1>

    <p class="font-heading text-sm md:text-base uppercase tracking-[0.2em] text-gold mb-6">
      Orthodox Christian Parish · Antiochian Archdiocese
    </p>

    <p class="text-lg text-white/85 mb-8 max-w-2xl mx-auto">
      A congregation devoted to mission and evangelism in the heart of Lincoln.
      We welcome all who seek to discover the ancient faith of Orthodox Christianity.
    </p>

    <div class="flex flex-col sm:flex-row gap-4 justify-center">
      <a href="/visit" class="btn btn-primary">Plan Your Visit</a>
      <a href="<?= e($settings['youtube'] ?? '#') ?>" class="btn btn-secondary" target="_blank">Watch Live</a>
    </div>
  </div>

  <!-- Scroll Indicator -->
  <div class="hidden sm:flex absolute bottom-8 left-1/2 -translate-x-1/2 text-white/50 text-xs font-heading uppercase tracking-widest flex-col items-center gap-2">
    <span>Scroll</span>
    <div class="w-px h-10 bg-gradient-to-b from-white/50 to-transparent animate-pulse"></div>
  </div>
</section>

<!-- Service Times & Location Section -->
<section class="py-20 bg-white">
  <div class="max-w-7xl mx-auto px-6">
    <div class="section-header">
      <h2 class="text-navy">Join Us for Worship</h2>
      <div class="section-divider"></div>
      <p class="text-slate max-w-xl mx-auto">
        Experience the beauty of Orthodox Christian worship. All are welcome.
      </p>
    </div>

    <div class="grid md:grid-cols-2 gap-8">
      <!-- Service Times -->
      <div class="bg-off-white rounded-xl p-8">
        <h3 class="text-2xl text-navy mb-6 font-heading uppercase tracking-wide">Weekly Services</h3>
        <div class="space-y-4">
          <?php
          // Show all church services on homepage (services only, at The Church)
          $main_services = array_filter($recurring, fn($s) =>
            ($s['active'] ?? true) &&
            ($s['type'] ?? 'service') === 'service' &&
            $s['location_id'] === 'temple'
          );
          // Sort by day of week then time
          usort($main_services, function($a, $b) {
            if ($a['day_of_week'] !== $b['day_of_week']) {
              // Put Sunday (0) last, so order is Wed, Thu, Sat, Sun
              $a_day = $a['day_of_week'] === 0 ? 7 : $a['day_of_week'];
              $b_day = $b['day_of_week'] === 0 ? 7 : $b['day_of_week'];
              return $a_day - $b_day;
            }
            return strcmp($a['time'], $b['time']);
          });
          foreach ($main_services as $service):
          ?>
          <div class="flex justify-between items-center pb-4 border-b border-charcoal/10 last:border-0">
            <div>
              <p class="font-semibold text-charcoal"><?= e(get_day_name($service['day_of_week'])) ?></p>
              <p class="text-sm text-slate"><?= e($service['name']) ?></p>
            </div>
            <span class="font-heading text-navy"><?= e(format_time($service['time'])) ?></span>
          </div>
          <?php endforeach; ?>
        </div>
        <a href="/services" class="text-gold text-sm hover:underline mt-4 inline-block">View full schedule</a>
      </div>

      <!-- Location with Map -->
      <div class="bg-navy text-white rounded-xl overflow-hidden">
        <div class="aspect-video">
          <iframe
            src="https://maps.google.com/maps?q=St+Matthias+Church+Centre,+Burton+Road,+Lincoln,+LN1+3TX&t=&z=15&ie=UTF8&iwloc=&output=embed"
            width="100%"
            height="100%"
            style="border:0;"
            allowfullscreen=""
            loading="lazy"
            referrerpolicy="no-referrer-when-downgrade">
          </iframe>
        </div>
        <div class="p-6">
          <h3 class="text-lg mb-2 font-heading uppercase tracking-wide text-gold">Saint Matthias Church Centre</h3>
          <p class="text-white/70 text-sm mb-4"><?= e($settings['address']['street'] ?? 'Burton Road') ?>, <?= e($settings['address']['city'] ?? 'Lincoln') ?> <?= e($settings['address']['postcode'] ?? 'LN1 3TX') ?></p>
          <a href="<?= e($settings['googleMapsUrl'] ?? '#') ?>" class="btn btn-primary text-sm" target="_blank">
            Get Directions
          </a>
        </div>
      </div>
    </div>
  </div>
</section>

<?php if (!empty($upcoming_events)): ?>
<!-- Upcoming Events Section -->
<section class="py-20 bg-off-white">
  <div class="max-w-7xl mx-auto px-6">
    <div class="section-header">
      <h2 class="text-navy">Upcoming Events</h2>
      <div class="section-divider"></div>
      <p class="text-slate max-w-xl mx-auto">
        Special services, feast days, and community gatherings
      </p>
    </div>

    <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-6">
      <?php
        $months = ['January','February','March','April','May','June','July','August','September','October','November','December'];
        $weekdays = ['Sunday','Monday','Tuesday','Wednesday','Thursday','Friday','Saturday'];
        $colors = ['bg-burgundy', 'bg-navy', 'bg-burgundy'];
        foreach ($upcoming_events as $index => $event):
        $date = strtotime($event['date']);
        $loc = get_location($event['location_id']);
        $bg_color = $colors[$index % count($colors)];
      ?>
      <div class="bg-white rounded-xl overflow-hidden shadow-sm hover:shadow-md transition-shadow">
        <div class="<?= $bg_color ?> text-white p-4 text-center">
          <span class="block text-gold font-heading uppercase text-sm tracking-wide"><?= $months[date('n', $date) - 1] ?></span>
          <span class="block text-4xl font-heading"><?= date('d', $date) ?></span>
          <span class="block text-white/60 text-sm"><?= $weekdays[date('w', $date)] ?></span>
        </div>
        <div class="p-6">
          <h3 class="font-heading uppercase tracking-wide text-lg text-navy mb-2"><?= e($event['name']) ?></h3>
          <p class="text-gold text-sm font-medium mb-1"><?= e(format_time($event['time'])) ?></p>
          <p class="text-slate text-xs mb-3"><?= e($loc['short'] ?? '') ?></p>
          <?php if (!empty($event['description'])): ?>
          <p class="text-slate text-sm"><?= e($event['description']) ?></p>
          <?php endif; ?>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
    <div class="text-center mt-8">
      <a href="/calendar" class="btn btn-secondary">View Full Calendar</a>
    </div>
  </div>
</section>
<?php endif; ?>

<!-- Welcome Section -->
<section class="py-20 bg-white">
  <div class="max-w-7xl mx-auto px-6">
    <div class="grid lg:grid-cols-2 gap-16 items-center">
      <div>
        <h2 class="text-4xl text-navy mb-6">New to Orthodoxy?</h2>
        <p class="text-charcoal mb-4 text-lg">
          Orthodox Christianity is the ancient faith passed down from the Apostles,
          preserved unchanged for two thousand years. At All Saints, we welcome seekers
          from all backgrounds — whether you've never been to church or are exploring
          Orthodoxy after years in another tradition.
        </p>
        <p class="text-charcoal mb-8">
          Our community is devoted to mission and evangelism. We offer catechumen classes,
          Bible studies, and a warm welcome to all who wish to discover the fullness of
          the Christian faith.
        </p>
        <div class="flex flex-wrap gap-4">
          <a href="/discover" class="btn btn-primary">Discover Orthodoxy</a>
          <a href="/visit" class="btn btn-secondary">What to Expect</a>
        </div>
      </div>

      <div class="relative">
        <div class="grid grid-cols-2 gap-4">
          <img
            src="/images/church_exterior.jpg"
            alt="Saint Matthias Church Centre exterior"
            class="w-full rounded-xl aspect-[3/4] object-cover shadow-lg"
            loading="lazy"
          />
          <img
            src="/images/allsaints_icon.jpg"
            alt="All Saints Orthodox Icon"
            class="w-full rounded-xl aspect-[3/4] object-cover shadow-lg"
            loading="lazy"
          />
        </div>
        <div class="absolute -bottom-6 -right-6 bg-gold text-navy p-6 rounded-lg shadow-xl max-w-xs">
          <blockquote class="font-accent italic text-lg mb-2">"Come and see"</blockquote>
          <cite class="font-heading text-sm uppercase tracking-wide">— John 1:46</cite>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- News Section -->
<section class="py-20 bg-navy text-white">
  <div class="max-w-7xl mx-auto px-6">
    <div class="section-header">
      <h2 class="text-white">News & Updates</h2>
      <div class="section-divider"></div>
      <p class="text-white/70 max-w-xl mx-auto">
        The latest from All Saints of Lincolnshire
      </p>
    </div>

    <div class="grid lg:grid-cols-3 gap-8">
      <!-- Featured Newsletter -->
      <?php if ($featured_news): ?>
      <div class="lg:col-span-2 bg-white/5 border border-white/10 rounded-xl p-8">
        <span class="font-heading text-xs uppercase tracking-widest text-gold mb-3 block">
          <?= $featured_news['featured'] ? 'Newsletter' : 'News' ?> · <?= format_date($featured_news['date']) ?>
        </span>
        <h3 class="text-2xl font-heading uppercase tracking-wide mb-4">
          <?= e($featured_news['title']) ?>
        </h3>
        <p class="text-white/80 mb-6">
          <?= e(truncate_text($featured_news['body'] ?? '', 200)) ?>
        </p>
        <a href="/news" class="btn btn-secondary border-white text-white hover:bg-white/10">
          Read More
        </a>
      </div>
      <?php endif; ?>

      <!-- Recent Posts -->
      <div>
        <h4 class="font-heading text-sm uppercase tracking-wide text-gold mb-6">Recent Posts</h4>
        <div class="space-y-4">
          <?php foreach (array_slice($recent_news, 0, 4) as $post): ?>
          <div class="pb-4 border-b border-white/10 last:border-0">
            <h5 class="font-heading text-sm uppercase tracking-wide mb-1"><?= e($post['title']) ?></h5>
            <span class="text-xs text-white/50"><?= format_date($post['date']) ?></span>
          </div>
          <?php endforeach; ?>
        </div>
      </div>
    </div>
  </div>
</section>

<?php require_once 'includes/footer.php'; ?>
