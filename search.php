<?php
/**
 * CamsTube - Search Results Page
 * Search by performer name or tags
 */

require_once __DIR__ . '/includes/functions.php';

$query = isset($_GET['q']) ? trim($_GET['q']) : '';
$page = getCurrentPage();
$currentNav = 'search';

$videos = [];
$searched = false;

if ($query !== '') {
    $searched = true;
    // Fetch videos and filter client-side by performer name / tags / title
    $apiParams = ['limit' => 100]; // Fetch more to filter from
    $allVideos = fetchVideos($apiParams);

    $queryLower = strtolower($query);
    $queryWords = preg_split('/\s+/', $queryLower);

    $videos = array_filter($allVideos, function($v) use ($queryLower, $queryWords) {
        $searchable = strtolower(
            ($v['title'] ?? '') . ' ' .
            ($v['uploader'] ?? '') . ' ' .
            implode(' ', $v['tags'] ?? [])
        );

        // Match if all query words appear somewhere
        foreach ($queryWords as $word) {
            if (strpos($searchable, $word) !== false) return true;
        }
        return false;
    });

    $videos = array_values($videos);
}

$pageTitle = $query ? 'Search: ' . e($query) . ' - ' . SITE_NAME : 'Search - ' . SITE_NAME;
$pageDescription = $query ? 'Search results for "' . $query . '" on ' . SITE_NAME : 'Search for cam model videos on ' . SITE_NAME;
$canonicalUrl = SITE_URL . '/search.php' . ($query ? '?q=' . urlencode($query) : '');

require_once __DIR__ . '/includes/header.php';
?>

<!-- Search Form -->
<div class="search-page-form">
    <div class="page-header">
        <h1>Search Videos</h1>
        <p>Find cam model videos by performer name, tags, or keywords</p>
    </div>
    <form action="<?= SITE_URL ?>/search.php" method="get" class="search-form">
        <input type="text" name="q" class="search-input" placeholder="Search models, tags, keywords..." value="<?= e($query) ?>" autocomplete="off">
        <button type="submit" class="search-btn">Search</button>
    </form>
</div>

<!-- Content Layout: Main + Sidebar -->
<div class="content-layout">
    <div class="content-main">
        <?php if ($searched): ?>
            <?php if (empty($videos)): ?>
            <div class="no-results">
                <h2>No results for "<?= e($query) ?>"</h2>
                <p>Try different keywords or <a href="<?= SITE_URL ?>/">browse all videos</a>.</p>
            </div>

            <!-- Suggest Popular Tags -->
            <div style="margin-top: 20px;">
                <h3 class="section-title">Try These Tags</h3>
                <div class="tag-cloud">
                    <?php foreach ($POPULAR_TAGS as $tag): ?>
                    <a href="<?= tagUrl($tag) ?>" class="tag-pill"><?= e($tag) ?></a>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php else: ?>
            <h2 class="section-title">Results for "<?= e($query) ?>" (<?= count($videos) ?>)</h2>
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
            <?php endif; ?>
        <?php else: ?>
            <!-- Show popular tags when no search yet -->
            <h2 class="section-title">Popular Tags</h2>
            <div class="tag-cloud">
                <?php foreach ($POPULAR_TAGS as $tag): ?>
                <a href="<?= tagUrl($tag) ?>" class="tag-pill"><?= e($tag) ?></a>
                <?php endforeach; ?>
            </div>

            <!-- Show some videos as suggestions -->
            <?php
            $suggestedVideos = fetchVideos(['limit' => 12]);
            if (!empty($suggestedVideos)):
            ?>
            <h2 class="section-title" style="margin-top: 24px;">Suggested Videos</h2>
            <div class="video-grid">
                <?php foreach ($suggestedVideos as $video): ?>
                    <?= renderVideoCard($video) ?>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>
        <?php endif; ?>
    </div>

    <?= renderSidebar() ?>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
