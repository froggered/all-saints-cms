<?php
require_once 'includes/functions.php';
$page_title = 'Services';
$recurring = get_recurring_services();
$locations = get_locations();
$upcoming_events = get_upcoming_special_events(6);
require_once 'includes/header.php';

// Separate services, events, and misc by type
$services_by_location = [];
$weekly_events = [];
$weekly_misc = [];
foreach ($recurring as $item) {
    if (!($item['active'] ?? true)) continue;
    $type = $item['type'] ?? 'service';
    if ($type === 'event') {
        $weekly_events[] = $item;
    } elseif ($type === 'misc') {
        $weekly_misc[] = $item;
    } else {
        $loc_id = $item['location_id'];
        if (!isset($services_by_location[$loc_id])) {
            $services_by_location[$loc_id] = [];
        }
        $services_by_location[$loc_id][] = $item;
    }
}

// Sort services chronologically within each location (by day, then by time)
foreach ($services_by_location as $loc_id => &$services) {
    usort($services, function($a, $b) {
        if ($a['day_of_week'] !== $b['day_of_week']) {
            return $a['day_of_week'] - $b['day_of_week'];
        }
        return strcmp($a['time'], $b['time']);
    });
}
unset($services);

// Order locations: church first, then monastery
$location_order = ['temple', 'jasmin', 'other'];
uksort($services_by_location, function($a, $b) use ($location_order) {
    return array_search($a, $location_order) - array_search($b, $location_order);
});

$months = ['January','February','March','April','May','June','July','August','September','October','November','December'];
$weekdays = ['Sunday','Monday','Tuesday','Wednesday','Thursday','Friday','Saturday'];
?>

<!-- Page Header -->
<section class="bg-navy text-white py-20">
  <div class="max-w-4xl mx-auto px-6 text-center">
    <h1 class="text-4xl md:text-5xl font-heading uppercase tracking-wide mb-4">Services</h1>
    <div class="w-16 h-1 bg-gold mx-auto mb-6"></div>
    <p class="text-white/70 text-lg">Join us for Orthodox Christian worship throughout the week</p>
  </div>
</section>

<section class="py-20 bg-white">
  <div class="max-w-7xl mx-auto px-6">
    <div class="grid lg:grid-cols-3 gap-12">
      <!-- Service Times by Location -->
      <div class="lg:col-span-1">
        <?php foreach ($services_by_location as $loc_id => $services):
          $loc = get_location($loc_id);
        ?>
        <div class="bg-off-white rounded-xl p-8 mb-6">
          <h3 class="text-xl text-navy mb-2 font-heading uppercase tracking-wide"><?= e($loc['short'] ?? $loc['name']) ?></h3>
          <p class="text-slate text-sm mb-6"><?= e($loc['address']) ?></p>
          <div class="space-y-3">
            <?php foreach ($services as $service): ?>
            <div class="flex justify-between items-center pb-3 border-b border-charcoal/10 last:border-0 last:pb-0">
              <div>
                <p class="font-semibold text-charcoal"><?= e(get_day_name($service['day_of_week'])) ?></p>
                <p class="text-sm text-slate"><?= e($service['name']) ?></p>
              </div>
              <span class="font-heading text-navy"><?= e(format_time($service['time'])) ?></span>
            </div>
            <?php endforeach; ?>
          </div>
        </div>
        <?php endforeach; ?>

        <?php if (!empty($weekly_misc)): ?>
        <div class="bg-slate/5 border border-slate/20 rounded-xl p-6 mb-6">
          <h3 class="text-lg text-navy mb-4 font-heading uppercase tracking-wide">Weekly Activities</h3>
          <div class="space-y-3">
            <?php foreach ($weekly_misc as $misc): ?>
            <div class="pb-3 border-b border-charcoal/10 last:border-0 last:pb-0">
              <div class="flex justify-between items-center">
                <p class="font-semibold text-charcoal"><?= e(get_day_name($misc['day_of_week'])) ?></p>
                <span class="font-heading text-navy text-sm"><?= e(format_time($misc['time'])) ?></span>
              </div>
              <p class="text-sm text-slate"><?= e($misc['name']) ?></p>
              <?php if (!empty($misc['description'])): ?>
              <p class="text-xs text-slate/70 mt-1"><?= e($misc['description']) ?></p>
              <?php endif; ?>
            </div>
            <?php endforeach; ?>
          </div>
        </div>
        <?php endif; ?>

        <div class="p-6 bg-off-white rounded-xl">
          <h3 class="font-heading text-lg uppercase tracking-wide text-navy mb-4">About Our Services</h3>
          <p class="text-slate text-sm mb-4">
            The Divine Liturgy is the central worship service of the Orthodox Church,
            celebrated on Saturdays and Sundays. Vespers is the evening prayer service
            that begins the liturgical day.
          </p>
          <p class="text-slate text-sm">
            All are welcome to attend. If this is your first visit, please see our
            <a href="/visit" class="text-gold hover:underline">guide for visitors</a>.
          </p>
        </div>
      </div>

      <!-- Events & Calendar -->
      <div class="lg:col-span-2" id="calendar">
        <?php if (!empty($weekly_events)): ?>
        <div class="mb-12">
          <h2 class="text-3xl text-navy font-heading uppercase tracking-wide mb-8">Weekly Events</h2>
          <div class="grid sm:grid-cols-2 gap-6">
            <?php foreach ($weekly_events as $event):
              $loc = get_location($event['location_id']);
            ?>
            <div class="bg-gold/10 border border-gold/30 rounded-xl overflow-hidden shadow-sm hover:shadow-md transition-shadow">
              <div class="bg-gold text-navy p-4 text-center">
                <span class="block font-heading uppercase text-sm tracking-wide"><?= e(get_day_name($event['day_of_week'])) ?></span>
                <span class="block text-2xl font-heading"><?= e(format_time($event['time'])) ?></span>
              </div>
              <div class="p-6">
                <h3 class="font-heading uppercase tracking-wide text-lg text-navy mb-2"><?= e($event['name']) ?></h3>
                <p class="text-slate text-xs mb-3"><?= e($loc['short'] ?? '') ?></p>
                <?php if (!empty($event['description'])): ?>
                <p class="text-slate text-sm"><?= e($event['description']) ?></p>
                <?php endif; ?>
              </div>
            </div>
            <?php endforeach; ?>
          </div>
        </div>
        <?php endif; ?>

        <div class="flex justify-between items-center mb-8">
          <h2 class="text-3xl text-navy font-heading uppercase tracking-wide">Upcoming Events</h2>
          <a href="/calendar" class="btn btn-secondary text-sm">View Full Calendar</a>
        </div>

        <?php if (!empty($upcoming_events)): ?>
        <div class="grid sm:grid-cols-2 gap-6">
          <?php
          $colors = ['bg-burgundy', 'bg-navy'];
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
        <?php else: ?>
        <p class="text-slate">No upcoming events at this time.</p>
        <?php endif; ?>

        <div class="mt-12 p-6 bg-burgundy/10 border border-burgundy/20 rounded-xl">
          <h3 class="font-heading text-lg uppercase tracking-wide text-burgundy mb-2">Special Services</h3>
          <p class="text-charcoal text-sm">
            During feast days and special occasions, service times may vary. Check the
            <a href="/calendar" class="text-gold hover:underline">full calendar</a> or contact Fr. Philip for the latest schedule.
          </p>
        </div>
      </div>
    </div>
  </div>
</section>

<?php require_once 'includes/footer.php'; ?>
