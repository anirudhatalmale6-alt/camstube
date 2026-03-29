<?php
/**
 * CamsTube - Homepage
 * Displays hero, category tabs, tag cloud, and video grid with sidebar
 */

$pageTitle = 'CamsTube - Free Cam Videos | HD Cam Model Recordings';
$pageDescription = 'Watch free HD cam model videos from top LiveJasmin performers. Browse girls, guys, trans and couples cam recordings updated daily.';
$canonicalUrl = SITE_URL ?? '';
$currentNav = 'home';

require_once __DIR__ . '/includes/header.php';

// Get active category from query
$activeCategory = isset($_GET['cat']) ? strtolower(trim($_GET['cat'])) : '';
$page = getCurrentPage();

// Build API params based on category
$apiParams = ['limit' => DEFAULT_LIMIT];
if ($activeCategory && isset($CATEGORIES[$activeCategory])) {
    $cat = $CATEGORIES[$activeCategory];
    $apiParams['sexualOrientation'] = $cat['orientation'];
    if (isset($cat['type'])) {
        $apiParams['type'] = $cat['type'];
    }
}

// Fetch videos
$videos = fetchVideos($apiParams);
?>

<!-- Hero Section -->
<section class="hero">
    <h1><span class="accent">CamsTube</span> - Free Cam Videos</h1>
    <p>Watch HD cam model recordings from top performers, updated daily</p>
    <form action="<?= SITE_URL ?>/search.php" method="get" class="search-form">
        <input type="text" name="q" class="search-input" placeholder="Search models, tags..." autocomplete="off">
        <button type="submit" class="search-btn">Search</button>
    </form>
</section>

<!-- Category Tabs -->
<div class="category-tabs">
    <a href="<?= SITE_URL ?>/" class="cat-tab<?= empty($activeCategory) ? ' active' : '' ?>">All</a>
    <?php foreach ($CATEGORIES as $key => $cat): ?>
    <a href="<?= categoryUrl($key) ?>" class="cat-tab<?= $activeCategory === $key ? ' active' : '' ?>"><?= $cat['icon'] ?> <?= e($cat['label']) ?></a>
    <?php endforeach; ?>
</div>

<!-- Tag Cloud -->
<div class="tag-cloud">
    <?php foreach ($POPULAR_TAGS as $tag): ?>
    <a href="<?= tagUrl($tag) ?>" class="tag-pill"><?= e($tag) ?></a>
    <?php endforeach; ?>
</div>

<!-- Content Layout: Main + Sidebar -->
<div class="content-layout">
    <div class="content-main">
        <h2 class="section-title"><?= $activeCategory ? e($CATEGORIES[$activeCategory]['label'] ?? 'Videos') : 'Latest Videos' ?></h2>

        <?php if (empty($videos)): ?>
        <div class="no-results">
            <h2>No Videos Found</h2>
            <p>Try a different category or check back later.</p>
        </div>
        <?php else: ?>
        <div class="video-grid">
            <?php foreach ($videos as $index => $video): ?>
                <?php echo renderVideoCard($video); ?>

                <?php // Insert in-feed ad after every 8th video ?>
                <?php if (($index + 1) % 8 === 0 && $index + 1 < count($videos)): ?>
                <div class="ad-infeed-row">
                    <div class="ad-spot ad-infeed">
                        <script type="text/javascript" src="https://hurtfulcell.com/act/files/tag.min.js?z=8499817" data-cfasync="false" async></script>
                    </div>
                </div>
                <?php endif; ?>
            <?php endforeach; ?>
        </div>

        <?php
        // Pagination (estimate: API usually returns exactly the limit, so assume more pages exist)
        $totalEstimate = count($videos) >= DEFAULT_LIMIT ? DEFAULT_LIMIT * 10 : count($videos);
        $baseUrl = SITE_URL . '/' . ($activeCategory ? '?cat=' . urlencode($activeCategory) : '');
        echo renderPagination($page, $totalEstimate, DEFAULT_LIMIT, $baseUrl);
        ?>
        <?php endif; ?>
    </div>

    <?= renderSidebar() ?>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
