<?php
require_once 'includes/functions.php';
$page_title = 'Ministries';
require_once 'includes/header.php';

$ministries = [
  [
    'title' => 'Catechumenate Classes',
    'description' => 'Instruction for those preparing to enter the Orthodox Church. Our catechumenate program provides thorough grounding in Orthodox faith and practice.',
    'icon' => '📖',
  ],
  [
    'title' => 'E-Quip Sessions',
    'description' => 'A diploma course for Orthodox Christians who want to deepen their knowledge of the Faith and Life of the Church. No prior knowledge assumed—open to all members seeking to be better equipped to serve God.',
    'icon' => '🎓',
  ],
  [
    'title' => 'Food Bank',
    'description' => 'Serving those in need in our local community through regular food distribution and support.',
    'icon' => '🍞',
  ],
  [
    'title' => 'Homeless Outreach',
    'description' => 'Reaching out to those without shelter in Lincoln, offering practical help and the love of Christ.',
    'icon' => '🤝',
  ],
  [
    'title' => 'Prison Ministry',
    'description' => 'Bringing the Gospel to those who are incarcerated, offering hope and spiritual support.',
    'icon' => '⛓️',
  ],
  [
    'title' => 'University Ministry',
    'description' => 'Daily services at Lincoln University during term time, serving students and staff.',
    'icon' => '🏛️',
  ],
  [
    'title' => 'Ministry Preparation',
    'description' => 'Programs for those discerning a call to ordained ministry, including Axios meetings and formation.',
    'icon' => '☦',
  ],
  [
    'title' => 'Parish Library',
    'description' => 'A growing collection of books and resources for Christian education and spiritual reading.',
    'icon' => '📚',
  ],
  [
    'title' => 'Bible Study',
    'description' => 'Weekday livestreamed Bible study sessions led by Fr. Philip, exploring Scripture together.',
    'icon' => '✝️',
  ],
  [
    'title' => 'Livestreaming',
    'description' => 'Regular livestreamed services for those unable to attend in person, extending our parish beyond Lincoln.',
    'icon' => '📺',
  ],
];
?>

<!-- Page Header -->
<section class="bg-navy text-white py-20">
  <div class="max-w-4xl mx-auto px-6 text-center">
    <h1 class="text-4xl md:text-5xl font-heading uppercase tracking-wide mb-4">Ministries</h1>
    <div class="w-16 h-1 bg-gold mx-auto mb-6"></div>
    <p class="text-white/70 text-lg">Serving God and neighbour through faith in action</p>
  </div>
</section>

<section class="py-20 bg-white">
  <div class="max-w-6xl mx-auto px-6">
    <div class="text-center mb-16">
      <h2 class="text-3xl text-navy mb-6 font-heading uppercase tracking-wide">Our Ministries</h2>
      <p class="text-charcoal max-w-2xl mx-auto">
        All Saints of Lincolnshire is active in education, outreach, and service.
        These ministries reflect our commitment to sharing the Orthodox faith and
        serving those in need.
      </p>
    </div>

    <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-6 mb-16">
      <?php foreach ($ministries as $ministry): ?>
      <div class="p-6 bg-off-white rounded-xl border border-black/5 hover:shadow-lg hover:-translate-y-1 transition-all duration-200">
        <span class="text-3xl mb-3 block"><?= $ministry['icon'] ?></span>
        <h3 class="font-heading text-lg uppercase tracking-wide text-navy mb-2">
          <?= e($ministry['title']) ?>
        </h3>
        <p class="text-slate text-sm"><?= e($ministry['description']) ?></p>
      </div>
      <?php endforeach; ?>
    </div>

    <div class="p-8 bg-navy text-white rounded-xl">
      <h3 class="font-heading text-xl uppercase tracking-wide text-gold mb-4">
        Get Involved
      </h3>
      <p class="text-white/80 mb-6">
        If you'd like to participate in any of our ministries or learn more about
        how you can serve, please speak with Fr. Philip or contact us.
      </p>
      <a href="/contact.php" class="btn btn-primary">
        Contact Us
      </a>
    </div>
  </div>
</section>

<?php require_once 'includes/footer.php'; ?>
