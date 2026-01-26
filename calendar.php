<?php
require_once 'includes/functions.php';
$page_title = 'Calendar';
$settings = get_settings();

// Get current month/year from query or default to current
$year = isset($_GET['year']) ? (int)$_GET['year'] : (int)date('Y');
$month = isset($_GET['month']) ? (int)$_GET['month'] : (int)date('n');

// Validate
if ($month < 1) { $month = 12; $year--; }
if ($month > 12) { $month = 1; $year++; }

$locations = get_locations();
$month_events = get_calendar_month($year, $month);

// Group events by date
$events_by_date = [];
foreach ($month_events as $event) {
    $date = $event['date'];
    if (!isset($events_by_date[$date])) {
        $events_by_date[$date] = [];
    }
    $events_by_date[$date][] = $event;
}

// Calendar data
$first_day = mktime(0, 0, 0, $month, 1, $year);
$days_in_month = (int)date('t', $first_day);
$start_weekday = (int)date('w', $first_day);
$month_name = date('F', $first_day);

$prev_month = $month - 1;
$prev_year = $year;
if ($prev_month < 1) { $prev_month = 12; $prev_year--; }

$next_month = $month + 1;
$next_year = $year;
if ($next_month > 12) { $next_month = 1; $next_year++; }

// Get upcoming events for sidebar
$upcoming = get_upcoming_calendar_events(5);

require_once 'includes/header.php';
?>

<!-- Page Header -->
<section class="bg-navy text-white py-20">
  <div class="max-w-4xl mx-auto px-6 text-center">
    <h1 class="text-4xl md:text-5xl font-heading uppercase tracking-wide mb-4">Calendar</h1>
    <div class="w-16 h-1 bg-gold mx-auto mb-6"></div>
    <p class="text-white/70 text-lg">Services, events, and feast days</p>
  </div>
</section>

<section class="py-12 bg-white">
  <div class="max-w-7xl mx-auto px-6">
    <!-- Calendar Navigation -->
    <div class="flex items-center justify-between mb-8">
      <div class="flex gap-2">
        <a href="?year=<?= $prev_year ?>&month=<?= $prev_month ?>" class="px-4 py-2 border-2 border-charcoal/20 rounded-lg hover:border-gold transition-colors">&larr; Previous</a>
        <a href="?year=<?= date('Y') ?>&month=<?= date('n') ?>" class="px-4 py-2 border-2 border-charcoal/20 rounded-lg hover:border-gold transition-colors">Today</a>
        <a href="?year=<?= $next_year ?>&month=<?= $next_month ?>" class="px-4 py-2 border-2 border-charcoal/20 rounded-lg hover:border-gold transition-colors">Next &rarr;</a>
      </div>
      <h2 class="text-3xl font-heading uppercase tracking-wide text-navy"><?= $month_name ?> <?= $year ?></h2>
      <div></div>
    </div>

    <div class="grid lg:grid-cols-4 gap-8">
      <!-- Calendar Grid -->
      <div class="lg:col-span-3">
        <div class="bg-off-white rounded-xl overflow-hidden shadow-sm">
          <!-- Header -->
          <div class="grid grid-cols-7 bg-navy text-white">
            <?php foreach (['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'] as $day): ?>
              <div class="py-3 text-center font-heading uppercase text-sm tracking-wide"><?= $day ?></div>
            <?php endforeach; ?>
          </div>

          <!-- Days -->
          <div class="grid grid-cols-7">
            <?php
            // Empty cells before first day
            for ($i = 0; $i < $start_weekday; $i++):
            ?>
              <div class="min-h-[100px] border border-charcoal/5 bg-charcoal/5"></div>
            <?php endfor; ?>

            <?php
            $today = date('Y-m-d');
            for ($day = 1; $day <= $days_in_month; $day++):
              $date = sprintf('%04d-%02d-%02d', $year, $month, $day);
              $day_events = $events_by_date[$date] ?? [];
              $is_today = ($date === $today);
            ?>
              <div class="min-h-[100px] border border-charcoal/5 p-2 <?= $is_today ? 'bg-gold/10' : 'bg-white' ?>">
                <div class="font-semibold text-sm mb-1 <?= $is_today ? 'text-gold' : 'text-charcoal' ?>"><?= $day ?></div>
                <div class="space-y-1">
                  <?php
                  $shown = 0;
                  foreach ($day_events as $event):
                    if ($shown >= 3) break;
                    $shown++;
                    // Color by type and location (Church = purple, Monastery = navy)
                    $bg_class = 'bg-purple-600'; // default: church service
                    if ($event['is_special'] ?? false) {
                        $bg_class = 'bg-gold';
                    } elseif (($event['type'] ?? 'service') === 'misc') {
                        $bg_class = 'bg-slate';
                    } elseif (($event['location_id'] ?? 'temple') === 'jasmin') {
                        $bg_class = 'bg-navy';
                    }
                    $loc = get_location($event['location_id']);
                  ?>
                    <button
                      class="event-btn text-[10px] px-1 py-0.5 rounded <?= $bg_class ?> text-white truncate w-full text-left cursor-pointer hover:opacity-80"
                      data-name="<?= e($event['name']) ?>"
                      data-time="<?= e(format_time($event['time'])) ?>"
                      data-date="<?= e(date('l, j F Y', strtotime($event['date']))) ?>"
                      data-location="<?= e($loc['short'] ?? '') ?>"
                      data-address="<?= e($loc['address'] ?? '') ?>"
                      data-description="<?= e($event['description'] ?? '') ?>"
                    >
                      <?= e(format_time($event['time'])) ?> <?= e($event['name']) ?>
                    </button>
                  <?php endforeach; ?>
                  <?php if (count($day_events) > 3):
                    // Encode all events for this day as JSON for the modal
                    $day_events_json = [];
                    foreach ($day_events as $de) {
                        $de_loc = get_location($de['location_id']);
                        $day_events_json[] = [
                            'name' => $de['name'],
                            'time' => format_time($de['time']),
                            'date' => date('l, j F Y', strtotime($de['date'])),
                            'location' => $de_loc['short'] ?? '',
                            'address' => $de_loc['address'] ?? '',
                            'description' => $de['description'] ?? '',
                            'is_special' => $de['is_special'] ?? false,
                            'location_id' => $de['location_id'] ?? 'temple',
                            'type' => $de['type'] ?? 'service'
                        ];
                    }
                  ?>
                    <button class="text-[10px] text-slate hover:text-gold more-btn" data-date="<?= e(date('l, j F Y', strtotime($date))) ?>" data-events='<?= e(json_encode($day_events_json)) ?>'>+<?= count($day_events) - 3 ?> more</button>
                  <?php endif; ?>
                </div>
              </div>
            <?php endfor; ?>

            <?php
            // Empty cells after last day
            $total_cells = $start_weekday + $days_in_month;
            $remaining = (7 - ($total_cells % 7)) % 7;
            for ($i = 0; $i < $remaining; $i++):
            ?>
              <div class="min-h-[100px] border border-charcoal/5 bg-charcoal/5"></div>
            <?php endfor; ?>
          </div>
        </div>

        <!-- Legend -->
        <div class="flex flex-wrap gap-4 mt-4 text-sm">
          <div class="flex items-center gap-2">
            <div class="w-4 h-4 rounded bg-purple-600"></div>
            <span>The Church</span>
          </div>
          <div class="flex items-center gap-2">
            <div class="w-4 h-4 rounded bg-navy"></div>
            <span>The Monastery</span>
          </div>
          <div class="flex items-center gap-2">
            <div class="w-4 h-4 rounded bg-slate"></div>
            <span>Activities</span>
          </div>
          <div class="flex items-center gap-2">
            <div class="w-4 h-4 rounded bg-gold"></div>
            <span>Special Event</span>
          </div>
        </div>
      </div>

      <!-- Sidebar: Upcoming Events -->
      <div>
        <div class="bg-navy rounded-xl p-6 text-white">
          <h3 class="font-heading uppercase tracking-wide text-gold mb-4">Upcoming Events</h3>
          <div class="space-y-4">
            <?php foreach ($upcoming as $event):
              $loc = get_location($event['location_id']);
              $event_date = strtotime($event['date']);
            ?>
              <div class="pb-4 border-b border-white/10 last:border-0 last:pb-0">
                <div class="text-gold text-xs font-heading uppercase"><?= date('D, j M', $event_date) ?></div>
                <div class="font-semibold"><?= e($event['name']) ?></div>
                <div class="text-white/60 text-sm"><?= e(format_time($event['time'])) ?> &middot; <?= e($loc['short'] ?? '') ?></div>
              </div>
            <?php endforeach; ?>
          </div>
        </div>

        <!-- Locations -->
        <div class="mt-6 bg-off-white rounded-xl p-6">
          <h3 class="font-heading uppercase tracking-wide text-navy mb-4">Locations</h3>
          <div class="space-y-4 text-sm">
            <?php foreach ($locations as $loc): if ($loc['id'] === 'other') continue; ?>
              <div>
                <div class="font-semibold text-charcoal"><?= e($loc['short']) ?></div>
                <div class="text-slate"><?= e($loc['address']) ?></div>
              </div>
            <?php endforeach; ?>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- Event Detail Modal -->
<div id="event-modal" class="fixed inset-0 bg-black/50 z-50 hidden items-center justify-center p-4">
  <div class="bg-white rounded-xl max-w-md w-full shadow-2xl">
    <div class="bg-navy text-white p-6 rounded-t-xl">
      <h3 id="modal-name" class="font-heading uppercase tracking-wide text-xl"></h3>
      <p id="modal-date" class="text-white/70 text-sm mt-1"></p>
    </div>
    <div class="p-6">
      <div class="flex items-center gap-3 mb-4">
        <div class="w-10 h-10 rounded-full bg-gold/20 flex items-center justify-center">
          <svg class="w-5 h-5 text-gold" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
          </svg>
        </div>
        <div>
          <p class="font-semibold text-charcoal" id="modal-time"></p>
          <p class="text-sm text-slate" id="modal-location"></p>
        </div>
      </div>
      <div id="modal-address" class="text-sm text-slate mb-4"></div>
      <div id="modal-description" class="text-charcoal"></div>
    </div>
    <div class="border-t border-charcoal/10 p-4">
      <button id="modal-close" class="w-full py-2 px-4 bg-charcoal/10 hover:bg-charcoal/20 rounded-lg font-heading uppercase text-sm tracking-wide transition-colors">
        Close
      </button>
    </div>
  </div>
</div>

<!-- Day Events Modal (for "+X more") -->
<div id="day-modal" class="fixed inset-0 bg-black/50 z-50 hidden items-center justify-center p-4">
  <div class="bg-white rounded-xl max-w-lg w-full shadow-2xl max-h-[80vh] flex flex-col">
    <div class="bg-navy text-white p-6 rounded-t-xl">
      <h3 class="font-heading uppercase tracking-wide text-xl">All Events</h3>
      <p id="day-modal-date" class="text-white/70 text-sm mt-1"></p>
    </div>
    <div class="p-4 overflow-y-auto flex-1">
      <div id="day-modal-events" class="space-y-3"></div>
    </div>
    <div class="border-t border-charcoal/10 p-4">
      <button id="day-modal-close" class="w-full py-2 px-4 bg-charcoal/10 hover:bg-charcoal/20 rounded-lg font-heading uppercase text-sm tracking-wide transition-colors">
        Close
      </button>
    </div>
  </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
  const modal = document.getElementById('event-modal');
  const modalName = document.getElementById('modal-name');
  const modalDate = document.getElementById('modal-date');
  const modalTime = document.getElementById('modal-time');
  const modalLocation = document.getElementById('modal-location');
  const modalAddress = document.getElementById('modal-address');
  const modalDescription = document.getElementById('modal-description');
  const modalClose = document.getElementById('modal-close');

  const dayModal = document.getElementById('day-modal');
  const dayModalDate = document.getElementById('day-modal-date');
  const dayModalEvents = document.getElementById('day-modal-events');
  const dayModalClose = document.getElementById('day-modal-close');

  function getEventColor(event) {
    if (event.is_special) return 'bg-gold';
    if (event.type === 'misc') return 'bg-slate';
    if (event.location_id === 'jasmin') return 'bg-navy';
    return 'bg-purple-600';
  }

  function showEventModal(event) {
    modalName.textContent = event.name;
    modalDate.textContent = event.date;
    modalTime.textContent = event.time;
    modalLocation.textContent = event.location;
    modalAddress.textContent = event.address;
    modalDescription.textContent = event.description || 'No additional details.';
    modal.classList.remove('hidden');
    modal.classList.add('flex');
  }

  function closeAllModals() {
    modal.classList.add('hidden');
    modal.classList.remove('flex');
    dayModal.classList.add('hidden');
    dayModal.classList.remove('flex');
  }

  // Open modal when clicking an event
  document.querySelectorAll('.event-btn').forEach(btn => {
    btn.addEventListener('click', function() {
      showEventModal({
        name: this.dataset.name,
        date: this.dataset.date,
        time: this.dataset.time,
        location: this.dataset.location,
        address: this.dataset.address,
        description: this.dataset.description
      });
    });
  });

  // Open day modal when clicking "+X more"
  document.querySelectorAll('.more-btn').forEach(btn => {
    btn.addEventListener('click', function() {
      const events = JSON.parse(this.dataset.events);
      const date = this.dataset.date;

      dayModalDate.textContent = date;
      dayModalEvents.innerHTML = '';

      events.forEach(event => {
        const colorClass = getEventColor(event);
        const div = document.createElement('div');
        div.className = 'p-3 rounded-lg border border-charcoal/10 hover:bg-charcoal/5 cursor-pointer transition-colors';
        div.innerHTML = `
          <div class="flex items-center gap-3">
            <div class="w-2 h-8 rounded ${colorClass}"></div>
            <div class="flex-1">
              <p class="font-semibold text-charcoal">${event.name}</p>
              <p class="text-sm text-slate">${event.time} · ${event.location}</p>
            </div>
          </div>
        `;
        div.addEventListener('click', function() {
          dayModal.classList.add('hidden');
          dayModal.classList.remove('flex');
          showEventModal(event);
        });
        dayModalEvents.appendChild(div);
      });

      dayModal.classList.remove('hidden');
      dayModal.classList.add('flex');
    });
  });

  // Close modals
  modalClose.addEventListener('click', closeAllModals);
  dayModalClose.addEventListener('click', closeAllModals);

  // Close on backdrop click
  modal.addEventListener('click', function(e) {
    if (e.target === modal) closeAllModals();
  });
  dayModal.addEventListener('click', function(e) {
    if (e.target === dayModal) closeAllModals();
  });

  // Close on escape key
  document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') closeAllModals();
  });
});
</script>

<?php require_once 'includes/footer.php'; ?>
