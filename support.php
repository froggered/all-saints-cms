<?php
require_once 'includes/functions.php';
$page_title = 'Support Our Parish';
$settings = get_settings();
require_once 'includes/header.php';
?>

<!-- Page Header -->
<section class="bg-navy text-white py-20">
  <div class="max-w-4xl mx-auto px-6 text-center">
    <h1 class="text-4xl md:text-5xl font-heading uppercase tracking-wide mb-4">Support Our Parish</h1>
    <div class="w-16 h-1 bg-gold mx-auto mb-6"></div>
    <p class="text-white/70 text-lg">Help us continue our mission in Lincolnshire</p>
  </div>
</section>

<section class="py-20 bg-white">
  <div class="max-w-4xl mx-auto px-6">
    <div class="prose prose-lg max-w-none">
      <p class="text-charcoal text-xl mb-8">
        All Saints of Lincolnshire relies on the generous support of our community to continue our mission
        of worship, education, and outreach. Your donations help us maintain our services, support those in need,
        and share the Orthodox faith with all who seek it.
      </p>

      <h2 class="text-2xl text-navy mt-12 mb-6 font-heading uppercase tracking-wide">Ways to Give</h2>

      <div class="grid md:grid-cols-2 gap-6 mb-12">
        <div class="bg-off-white rounded-xl p-6">
          <h3 class="font-heading text-xl uppercase tracking-wide text-navy mb-4">Bank Transfer</h3>
          <p class="text-slate text-sm mb-4">Make a direct transfer to our parish account:</p>
          <div class="space-y-2 text-charcoal">
            <p><strong>Account Name:</strong> The Orthodox Community of All Saints of Lincolnshire</p>
            <p><strong>Sort Code:</strong> 20-34-60</p>
            <p><strong>Account Number:</strong> 70353175</p>
          </div>
          <p class="text-slate text-xs mt-4">33-35 High Street, Grantham, NG31 6PH</p>
        </div>

        <div class="bg-off-white rounded-xl p-6">
          <h3 class="font-heading text-xl uppercase tracking-wide text-navy mb-4">In Person</h3>
          <p class="text-slate text-sm mb-4">
            Donations can be made in person at any service. Collection baskets are passed during the Divine Liturgy,
            or you may speak with Fr. Philip directly.
          </p>
        </div>
      </div>

      <h2 class="text-2xl text-navy mt-12 mb-6 font-heading uppercase tracking-wide">Other Ways to Help</h2>

      <div class="space-y-6 mb-12">
        <div class="flex gap-4">
          <div class="text-gold text-2xl">🙏</div>
          <div>
            <h3 class="font-heading text-lg uppercase tracking-wide text-navy mb-2">Pray for Us</h3>
            <p class="text-charcoal">Please remember our parish in your prayers. Pray for our clergy, our congregation, those preparing for baptism, and for the growth of the Orthodox faith in Lincolnshire. Your prayers are a precious gift to our community.</p>
          </div>
        </div>

        <div class="flex gap-4">
          <div class="text-gold text-2xl">🤝</div>
          <div>
            <h3 class="font-heading text-lg uppercase tracking-wide text-navy mb-2">Volunteer</h3>
            <p class="text-charcoal">Help with cleaning, coffee hour, choir, or other parish activities. Contact Fr. Philip to get involved.</p>
          </div>
        </div>
      </div>

      <div class="bg-gold/10 border border-gold/30 rounded-xl p-8">
        <h3 class="text-xl text-navy mb-4 font-heading uppercase tracking-wide">Thank You</h3>
        <p class="text-charcoal">
          Your generosity makes our parish's ministry possible. Whether through financial support, volunteering,
          or simply praying for our community, you are helping to build up the Body of Christ in Lincolnshire.
        </p>
      </div>
    </div>
  </div>
</section>

<?php require_once 'includes/footer.php'; ?>
