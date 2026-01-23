<?php
require_once 'includes/functions.php';
$page_title = 'Contact';
$settings = get_settings();
require_once 'includes/header.php';

$googleMapsUrl = $settings['googleMapsUrl'] ?? 'https://www.google.com/maps/dir/?api=1&destination=Saint+Matthias+Church+Centre+Burton+Road+Lincoln+LN1+3TX';
$googleMapsEmbed = 'https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d2404.5!2d-0.5467!3d53.2344!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x0%3A0x0!2sSaint%20Matthias%20Church%20Centre!5e0!3m2!1sen!2suk!4v1234567890';
?>

<!-- Page Header -->
<section class="bg-navy text-white py-20">
  <div class="max-w-4xl mx-auto px-6 text-center">
    <h1 class="text-4xl md:text-5xl font-heading uppercase tracking-wide mb-4">Contact Us</h1>
    <div class="w-16 h-1 bg-gold mx-auto mb-6"></div>
    <p class="text-white/70 text-lg">Get in touch or find directions to our church</p>
  </div>
</section>

<section class="py-20 bg-white">
  <div class="max-w-6xl mx-auto px-6">
    <div class="grid lg:grid-cols-2 gap-12">
      <!-- Contact Info -->
      <div>
        <h2 class="text-3xl text-navy mb-8 font-heading uppercase tracking-wide">Get in Touch</h2>

        <div class="space-y-8">
          <!-- Fr Philip -->
          <div class="bg-off-white rounded-xl p-6">
            <h3 class="font-heading text-lg uppercase tracking-wide text-gold mb-3">Parish Priest</h3>
            <p class="text-charcoal text-lg font-semibold">Archimandrite Philip Hall</p>
            <p class="text-slate text-sm mb-4">Protosyngellos of the Antiochian Archdiocese</p>
            <div class="space-y-2 mb-4">
              <a href="tel:07969332740" class="flex items-center gap-2 text-charcoal hover:text-gold transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                07969 332740
              </a>
              <a href="mailto:Fr.Philip@gmx.com" class="flex items-center gap-2 text-charcoal hover:text-gold transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                Fr.Philip@gmx.com
              </a>
            </div>
            <div class="flex gap-3 pt-2 border-t border-charcoal/10">
              <?php if (!empty($settings['facebook'])): ?>
              <a href="<?= e($settings['facebook']) ?>" class="text-slate hover:text-gold transition-colors" target="_blank" rel="noopener" title="Facebook">
                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
              </a>
              <?php endif; ?>
              <?php if (!empty($settings['youtube'])): ?>
              <a href="<?= e($settings['youtube']) ?>" class="text-slate hover:text-gold transition-colors" target="_blank" rel="noopener" title="YouTube">
                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M23.498 6.186a3.016 3.016 0 0 0-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 0 0 .502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 0 0 2.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 0 0 2.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z"/></svg>
              </a>
              <?php endif; ?>
              <?php if (!empty($settings['twitter'])): ?>
              <a href="<?= e($settings['twitter']) ?>" class="text-slate hover:text-gold transition-colors" target="_blank" rel="noopener" title="Twitter">
                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/></svg>
              </a>
              <?php endif; ?>
            </div>
          </div>

          <!-- Deacon Panagiotis -->
          <div class="bg-off-white rounded-xl p-6">
            <h3 class="font-heading text-lg uppercase tracking-wide text-gold mb-3">Deacon</h3>
            <p class="text-charcoal text-lg font-semibold">Deacon Panagiotis</p>
            <p class="text-slate text-sm mb-4">Monastery Deacon</p>
            <div class="space-y-2">
              <a href="tel:07826322064" class="flex items-center gap-2 text-charcoal hover:text-gold transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                07826 322064
              </a>
            </div>
          </div>

          <!-- Address -->
          <div>
            <h3 class="font-heading text-lg uppercase tracking-wide text-gold mb-3">Address</h3>
            <address class="text-charcoal not-italic text-lg">
              Saint Matthias Church Centre<br>
              Burton Road<br>
              Lincoln, LN1 3TX
            </address>
            <div class="mt-4">
              <a href="<?= e($googleMapsUrl) ?>" class="btn btn-primary" target="_blank" rel="noopener">
                Get Directions
              </a>
            </div>
          </div>
        </div>
      </div>

      <!-- Map -->
      <div>
        <h2 class="text-3xl text-navy mb-8 font-heading uppercase tracking-wide">Find Us</h2>
        <div class="bg-off-white rounded-xl overflow-hidden shadow-lg">
          <div class="aspect-[4/3] bg-navy/10">
            <iframe
              src="https://maps.google.com/maps?q=St+Matthias+Church+Centre,+Burton+Road,+Lincoln,+LN1+3TX&t=&z=15&ie=UTF8&iwloc=&output=embed"
              width="100%"
              height="100%"
              style="border:0; min-height: 350px;"
              allowfullscreen=""
              loading="lazy"
              referrerpolicy="no-referrer-when-downgrade">
            </iframe>
          </div>
          <div class="p-6">
            <p class="text-charcoal mb-4">
              <strong>Saint Matthias Church Centre</strong> is located on Burton Road in Lincoln,
              near the junction with Yarborough Road.
            </p>
            <p class="text-slate text-sm">
              <strong>Parking:</strong> Free parking is available on site and on surrounding streets.
            </p>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<?php require_once 'includes/footer.php'; ?>
