    </div><!-- /.container -->
</main>

<!-- HilltopAds Footer Banner -->
<div class="ad-spot ad-footer-banner">
    <script type="text/javascript" src="https://hurtfulcell.com/act/files/tag.min.js?z=8499817" data-cfasync="false" async></script>
</div>

<footer class="site-footer">
    <div class="container">
        <div class="footer-grid">
            <div class="footer-col">
                <h4 class="footer-title"><?= SITE_NAME ?></h4>
                <p class="footer-text">Free cam model videos from top LiveJasmin performers. Updated daily with the hottest HD cam recordings.</p>
            </div>

            <div class="footer-col">
                <h4 class="footer-title">Categories</h4>
                <ul class="footer-links">
                    <li><a href="<?= categoryUrl('girls') ?>">Girls</a></li>
                    <li><a href="<?= categoryUrl('guys') ?>">Guys</a></li>
                    <li><a href="<?= categoryUrl('trans') ?>">Trans</a></li>
                    <li><a href="<?= categoryUrl('couples') ?>">Couples</a></li>
                </ul>
            </div>

            <div class="footer-col">
                <h4 class="footer-title">Our Network</h4>
                <ul class="footer-links">
                    <?php foreach ($NETWORK_SITES as $site): ?>
                    <li><a href="<?= e($site['url']) ?>" target="_blank" rel="noopener"><?= e($site['name']) ?></a></li>
                    <?php endforeach; ?>
                </ul>
            </div>

            <div class="footer-col">
                <h4 class="footer-title">Info</h4>
                <ul class="footer-links">
                    <li><a href="<?= SITE_URL ?>/search.php">Search</a></li>
                    <li><a href="<?= SITE_URL ?>/sitemap.xml">Sitemap</a></li>
                </ul>
            </div>
        </div>

        <div class="footer-bottom">
            <div class="age-warning">
                <span class="age-badge">18+</span>
                <p>WARNING: This website contains adult content. You must be at least 18 years old to enter. By accessing this site, you confirm that you are of legal age in your jurisdiction.</p>
            </div>
            <div class="footer-disclaimer">
                <p>&copy; <?= date('Y') ?> <?= SITE_NAME ?>. All rights reserved. All videos are provided by third-party services. We do not host any content on our servers.</p>
                <p>All models appearing on this website are 18 years or older.</p>
            </div>
        </div>
    </div>
</footer>

<script src="<?= SITE_URL ?>/assets/js/main.js"></script>
</body>
</html>
