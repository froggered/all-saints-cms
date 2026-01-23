<?php
require_once 'includes/functions.php';
$page_title = 'Discover Orthodoxy';
require_once 'includes/header.php';
?>

<!-- Page Header -->
<section class="bg-navy text-white py-20">
  <div class="max-w-4xl mx-auto px-6 text-center">
    <h1 class="text-4xl md:text-5xl font-heading uppercase tracking-wide mb-4">Discover Orthodoxy</h1>
    <div class="w-16 h-1 bg-gold mx-auto mb-6"></div>
    <p class="text-white/70 text-lg">Explore the ancient Christian faith</p>
  </div>
</section>

<section class="py-20 bg-white">
  <div class="max-w-4xl mx-auto px-6">
    <div class="grid sm:grid-cols-2 gap-6 mb-16">
      <a href="/visit.php" class="block p-8 bg-off-white rounded-xl border border-black/5 hover:shadow-lg hover:-translate-y-1 transition-all duration-200">
        <h3 class="font-heading text-2xl uppercase tracking-wide text-navy mb-3">Plan Your Visit</h3>
        <p class="text-slate">What to expect when visiting an Orthodox church for the first time.</p>
        <span class="inline-block mt-4 text-gold font-heading text-sm uppercase tracking-wide">Learn More →</span>
      </a>
      <a href="/about.php" class="block p-8 bg-off-white rounded-xl border border-black/5 hover:shadow-lg hover:-translate-y-1 transition-all duration-200">
        <h3 class="font-heading text-2xl uppercase tracking-wide text-navy mb-3">About Our Parish</h3>
        <p class="text-slate">Learn about All Saints of Lincolnshire and our community.</p>
        <span class="inline-block mt-4 text-gold font-heading text-sm uppercase tracking-wide">Learn More →</span>
      </a>
    </div>

    <div class="prose prose-lg max-w-none">
      <h2 class="text-3xl text-navy mb-6 font-heading uppercase tracking-wide">What is Orthodox Christianity?</h2>
      <p class="text-charcoal text-lg mb-6">
        Orthodox Christianity is the original Christian Church, founded by Jesus Christ and His Apostles nearly 2,000 years ago.
        The word "Orthodox" comes from the Greek words <em>orthos</em> (right, true) and <em>doxa</em> (belief, glory),
        meaning "right belief" or "right worship."
      </p>
      <p class="text-charcoal mb-6">
        We have preserved the faith, worship, and spiritual life handed down from the Apostles through an unbroken succession
        of bishops and saints. The Orthodox Church is the second-largest Christian communion in the world, with over 300 million
        faithful worldwide.
      </p>

      <h3 class="text-2xl text-navy mt-12 mb-6 font-heading uppercase tracking-wide">Key Beliefs</h3>
      <ul class="text-charcoal space-y-3 mb-8">
        <li><strong>The Holy Trinity:</strong> One God in three Persons — Father, Son, and Holy Spirit.</li>
        <li><strong>Jesus Christ:</strong> Fully God and fully man, who died and rose again for our salvation.</li>
        <li><strong>The Church:</strong> The living Body of Christ, guided by the Holy Spirit through Sacred Scripture and Holy Tradition.</li>
        <li><strong>The Sacraments:</strong> Baptism, Chrismation, the Eucharist, Confession, Marriage, Holy Orders, and Anointing of the Sick.</li>
        <li><strong>The Saints:</strong> Those who have lived lives of holiness and now intercede for us in heaven.</li>
        <li><strong>Icons:</strong> Sacred images that connect us to Christ, the Theotokos, and the saints.</li>
      </ul>

      <div class="bg-off-white rounded-xl p-8 mt-12">
        <h3 class="text-xl text-navy mb-4 font-heading uppercase tracking-wide">Interested in Learning More?</h3>
        <p class="text-charcoal mb-4">
          We offer catechumen classes for those interested in exploring the Orthodox faith more deeply.
          These classes cover the history, theology, and spiritual practices of the Orthodox Church.
        </p>
        <p class="text-charcoal">
          Please <a href="/contact.php" class="text-gold hover:underline">contact Fr. Philip</a> to learn more about becoming a catechumen.
        </p>
      </div>
    </div>
  </div>
</section>

<?php require_once 'includes/footer.php'; ?>
