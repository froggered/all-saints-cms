  </main>

  <?php $footer_settings = get_settings(); ?>
  <!-- Footer -->
  <footer class="bg-navy text-white py-16">
    <div class="max-w-7xl mx-auto px-6">
      <div class="grid md:grid-cols-4 gap-12 mb-12">
        <!-- Brand -->
        <div class="md:col-span-1">
          <a href="/" class="flex items-center gap-2 text-white font-heading text-xl font-semibold uppercase tracking-wide mb-4">
            <span class="text-gold text-2xl">&#9766;</span>
            All Saints
          </a>
          <p class="text-white/70 text-sm">
            Orthodox Christian Parish in Lincoln, UK.<br>
            Antiochian Archdiocese of the British Isles and Ireland.
          </p>
        </div>

        <!-- Quick Links -->
        <div>
          <h4 class="font-heading text-sm uppercase tracking-wide text-gold mb-4">Quick Links</h4>
          <ul class="space-y-2 text-sm">
            <li><a href="/about.php" class="text-white/70 hover:text-gold transition-colors">About Us</a></li>
            <li><a href="/discover.php" class="text-white/70 hover:text-gold transition-colors">Discover Orthodoxy</a></li>
            <li><a href="/services.php" class="text-white/70 hover:text-gold transition-colors">Service Times</a></li>
            <li><a href="/news.php" class="text-white/70 hover:text-gold transition-colors">News & Updates</a></li>
            <li><a href="/contact.php" class="text-white/70 hover:text-gold transition-colors">Contact</a></li>
          </ul>
        </div>

        <!-- Contact -->
        <div>
          <h4 class="font-heading text-sm uppercase tracking-wide text-gold mb-4">Contact</h4>
          <ul class="space-y-2 text-sm text-white/70">
            <li>Saint Matthias Church Centre</li>
            <li>Burton Road, Lincoln LN1 3TX</li>
            <li class="pt-2">
              <a href="tel:07969332740" class="hover:text-gold transition-colors">07969 332740</a>
            </li>
            <li>
              <a href="mailto:Fr.Philip@gmx.com" class="hover:text-gold transition-colors">Fr.Philip@gmx.com</a>
            </li>
          </ul>
        </div>

        <!-- Social -->
        <div>
          <h4 class="font-heading text-sm uppercase tracking-wide text-gold mb-4">Follow Us</h4>
          <div class="flex flex-wrap gap-3">
            <?php if (!empty($footer_settings['facebook'])): ?>
            <a href="<?= e($footer_settings['facebook']) ?>" class="flex items-center gap-2 px-3 py-2 bg-white/10 rounded hover:bg-gold transition-colors" target="_blank" rel="noopener">
              <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
              <span class="text-xs">Facebook</span>
            </a>
            <?php endif; ?>
            <?php if (!empty($footer_settings['youtube'])): ?>
            <a href="<?= e($footer_settings['youtube']) ?>" class="flex items-center gap-2 px-3 py-2 bg-white/10 rounded hover:bg-gold transition-colors" target="_blank" rel="noopener">
              <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M23.498 6.186a3.016 3.016 0 0 0-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 0 0 .502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 0 0 2.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 0 0 2.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z"/></svg>
              <span class="text-xs">YouTube</span>
            </a>
            <?php endif; ?>
            <?php if (!empty($footer_settings['twitter'])): ?>
            <a href="<?= e($footer_settings['twitter']) ?>" class="flex items-center gap-2 px-3 py-2 bg-white/10 rounded hover:bg-gold transition-colors" target="_blank" rel="noopener">
              <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/></svg>
              <span class="text-xs">Twitter</span>
            </a>
            <?php endif; ?>
          </div>
        </div>
      </div>

      <div class="border-t border-white/10 pt-8 text-center text-sm text-white/50">
        <p>&copy; <?= date('Y') ?> All Saints of Lincolnshire. All rights reserved.</p>
      </div>
    </div>
  </footer>

  <!-- Mobile Menu Script -->
  <script>
    const menuBtn = document.getElementById('mobile-menu-btn');
    const mobileMenu = document.getElementById('mobile-menu');

    menuBtn?.addEventListener('click', () => {
      mobileMenu?.classList.toggle('hidden');
    });
  </script>
</body>
</html>
