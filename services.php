<?php
require_once 'includes/functions.php';
$page_title = 'Services & Calendar';
$services = get_services();
$upcoming_events = get_upcoming_events(6);
require_once 'includes/header.php';

$months = ['January','February','March','April','May','June','July','August','September','October','November','December'];
$weekdays = ['Sunday','Monday','Tuesday','Wednesday','Thursday','Friday','Saturday'];
?>

<!-- Page Header -->
<section class="bg-navy text-white py-20">
  <div class="max-w-4xl mx-auto px-6 text-center">
    <h1 class="text-4xl md:text-5xl font-heading uppercase tracking-wide mb-4">Services & Calendar</h1>
    <div class="w-16 h-1 bg-gold mx-auto mb-6"></div>
    <p class="text-white/70 text-lg">Join us for Orthodox Christian worship throughout the week</p>
  </div>
</section>

<section class="py-20 bg-white">
  <div class="max-w-7xl mx-auto px-6">
    <div class="grid lg:grid-cols-3 gap-12">
      <!-- Service Times -->
      <div class="lg:col-span-1">
        <div class="bg-off-white rounded-xl p-8">
          <h3 class="text-2xl text-navy mb-6 font-heading uppercase tracking-wide">Weekly Services</h3>
          <div class="space-y-4">
            <?php foreach ($services as $service): ?>
            <div class="flex justify-between items-center pb-4 border-b border-charcoal/10 last:border-0">
              <div>
                <p class="font-semibold text-charcoal"><?= e($service['day']) ?></p>
                <p class="text-sm text-slate"><?= e($service['name']) ?></p>
              </div>
              <span class="font-heading text-navy"><?= e($service['time']) ?></span>
            </div>
            <?php endforeach; ?>
          </div>
        </div>

        <div class="mt-8 p-6 bg-off-white rounded-xl">
          <h3 class="font-heading text-lg uppercase tracking-wide text-navy mb-4">About Our Services</h3>
          <p class="text-slate text-sm mb-4">
            The Divine Liturgy is the central worship service of the Orthodox Church,
            celebrated on Saturdays and Sundays. Vespers is the evening prayer service
            that begins the liturgical day.
          </p>
          <p class="text-slate text-sm">
            All are welcome to attend. If this is your first visit, please see our
            <a href="/visit.php" class="text-gold hover:underline">guide for visitors</a>.
          </p>
        </div>
      </div>

      <!-- Calendar -->
      <div class="lg:col-span-2" id="calendar">
        <h2 class="text-3xl text-navy mb-8 font-heading uppercase tracking-wide">Upcoming Events</h2>

        <?php if (!empty($upcoming_events)): ?>
        <div class="grid sm:grid-cols-2 gap-6">
          <?php foreach ($upcoming_events as $event):
            $date = strtotime($event['date']);
          ?>
          <div class="bg-off-white rounded-xl overflow-hidden shadow-sm hover:shadow-md transition-shadow">
            <div class="bg-navy text-white p-4 text-center">
              <span class="block text-gold font-heading uppercase text-sm tracking-wide"><?= $months[date('n', $date) - 1] ?></span>
              <span class="block text-4xl font-heading"><?= date('d', $date) ?></span>
              <span class="block text-white/60 text-sm"><?= $weekdays[date('w', $date)] ?></span>
            </div>
            <div class="p-6">
              <h3 class="font-heading uppercase tracking-wide text-lg text-navy mb-2"><?= e($event['title']) ?></h3>
              <p class="text-gold text-sm font-medium mb-3"><?= e($event['time']) ?></p>
              <p class="text-slate text-sm"><?= e($event['description']) ?></p>
            </div>
          </div>
          <?php endforeach; ?>
        </div>
        <?php else: ?>
        <p class="text-slate">No upcoming events at this time.</p>
        <?php endif; ?>

        <div class="mt-12 p-6 bg-burgundy/10 border border-burgundy/20 rounded-xl">
          <h3 class="font-heading text-lg uppercase tracking-wide text-burgundy mb-2">Special Services</h3>
          <p class="text-charcoal text-sm">
            During feast days and special occasions, service times may vary. Please check
            this calendar regularly or contact Fr. Philip for the latest schedule.
          </p>
        </div>
      </div>
    </div>
  </div>
</section>

<?php require_once 'includes/footer.php'; ?>
