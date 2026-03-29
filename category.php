<?php
/**
 * CamsTube - Category / Tag Page
 * Filters videos by sexual orientation category or tag
 */

require_once __DIR__ . '/includes/functions.php';

$cat = isset($_GET['cat']) ? strtolower(trim($_GET['cat'])) : '';
$tag = isset($_GET['tag']) ? trim($_GET['tag']) : '';
$page = getCurrentPage();

// Determine what we're filtering
$apiParams = ['limit' => DEFAULT_LIMIT];
$pageLabel = 'Videos';
$pageDesc = 'Browse cam model videos';

if ($cat && isset($CATEGORIES[$cat])) {
    $catInfo = $CATEGORIES[$cat];
    $apiParams['sexualOrientation'] = $catInfo['orientation'];
    if (isset($catInfo['type'])) {
        $apiParams['type'] = $catInfo['type'];
    }
    $pageLabel = $catInfo['label'] . ' Cam Videos';
    $pageDesc = 'Browse free ' . strtolower($catInfo['label']) . ' cam model videos in HD quality.';
    $currentNav = $cat;
} elseif ($tag) {
    // The API doesn't have a direct tag filter, but we can try forcedPerformers or just show all and note the tag
    $pageLabel = ucfirst($tag) . ' Videos';
    $pageDesc = 'Watch free ' . $tag . ' cam videos from top models.';
    $currentNav = '';
} else {
    header('Location: ' . SITE_URL . '/');
    exit;
}

// Fetch videos
$videos = fetchVideos($apiParams);

// If filtering by tag, do client-side filtering
if ($tag && !empty($videos)) {
    $tagLower = strtolower($tag);
    $filtered = array_filter($videos, function($v) use ($tagLower) {
        if (isset($v['tags']) && is_array($v['tags'])) {
            foreach ($v['tags'] as $t) {
                if (stripos($t, $tagLower) !== false) return true;
            }
        }
        if (isset($v['title']) && stripos($v['title'], $tagLower) !== false) return true;
        return false;
    });
    // If filtered results are too few, show all (API may not support tag filtering)
    if (count($filtered) >= 2) {
        $videos = array_values($filtered);
    }
}

$pageTitle = $pageLabel . ' - ' . SITE_NAME;
$pageDescription = $pageDesc;
$canonicalUrl = SITE_URL . '/category.php?' . ($cat ? 'cat=' . urlencode($cat) : 'tag=' . urlencode($tag));

require_once __DIR__ . '/includes/header.php';
?>

<!-- Page Header -->
<div class="page-header">
    <h1><?= e($pageLabel) ?></h1>
    <p><?= e($pageDesc) ?></p>
</div>

<?php if ($cat): ?>
<!-- Category Tabs -->
<div class="category-tabs">
    <a href="<?= SITE_URL ?>/" class="cat-tab">All</a>
    <?php foreach ($CATEGORIES as $key => $catItem): ?>
    <a href="<?= categoryUrl($key) ?>" class="cat-tab<?= $cat === $key ? ' active' : '' ?>"><?= $catItem['icon'] ?> <?= e($catItem['label']) ?></a>
    <?php endforeach; ?>
</div>
<?php endif; ?>

<!-- Content Layout: Main + Sidebar -->
<div class="content-layout">
    <div class="content-main">
        <?php if (empty($videos)): ?>
        <div class="no-results">
            <h2>No Videos Found</h2>
            <p>Try a different category or tag, or <a href="<?= SITE_URL ?>/">browse all videos</a>.</p>
        </div>
        <?php else: ?>
        <div class="video-grid">
            <?php foreach ($videos as $index => $video): ?>
                <?= renderVideoCard($video) ?>

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
        $totalEstimate = count($videos) >= DEFAULT_LIMIT ? DEFAULT_LIMIT * 10 : count($videos);
        $baseUrl = SITE_URL . '/category.php?' . ($cat ? 'cat=' . urlencode($cat) : 'tag=' . urlencode($tag));
        echo renderPagination($page, $totalEstimate, DEFAULT_LIMIT, $baseUrl);
        ?>
        <?php endif; ?>

        <!-- Related Tags -->
        <?php if ($tag): ?>
        <div style="margin-top: 20px;">
            <h3 class="section-title">Popular Tags</h3>
            <div class="tag-cloud">
                <?php foreach ($POPULAR_TAGS as $pt): ?>
                <a href="<?= tagUrl($pt) ?>" class="tag-pill<?= strtolower($pt) === strtolower($tag) ? ' active' : '' ?>"><?= e($pt) ?></a>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>
    </div>

    <?= renderSidebar() ?>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
