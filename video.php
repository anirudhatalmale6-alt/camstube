<?php
/**
 * CamsTube - Video Detail Page
 * Displays embedded video player, info, model card, and related videos
 */

require_once __DIR__ . '/includes/functions.php';

$videoId = $_GET['id'] ?? '';
if (empty($videoId)) {
    header('Location: ' . SITE_URL . '/');
    exit;
}

// Fetch video details
$video = fetchVideoDetails($videoId);
if (!$video) {
    http_response_code(404);
    $pageTitle = 'Video Not Found - ' . SITE_NAME;
    $pageDescription = 'The requested video could not be found.';
    $canonicalUrl = SITE_URL . '/video.php?id=' . urlencode($videoId);
    $currentNav = '';
    require_once __DIR__ . '/includes/header.php';
    echo '<div class="no-results"><h2>Video Not Found</h2><p>This video may have been removed or is temporarily unavailable.</p><p><a href="' . SITE_URL . '/">Back to Homepage</a></p></div>';
    require_once __DIR__ . '/includes/footer.php';
    exit;
}

$videoTitle = $video['title'] ?? 'Untitled Video';
$videoSlug = slugify($videoTitle);
$uploaderName = $video['uploader'] ?? 'Unknown Model';
$duration = formatDuration(intval($video['duration'] ?? 0));
$quality = $video['quality'] ?? '';
$isHd = !empty($video['isHd']);
$tags = $video['tags'] ?? [];
$profileImage = $video['profileImage'] ?? '';
$targetUrl = $video['targetUrl'] ?? '';
$coverImage = $video['coverImage'] ?? $video['thumbImage'] ?? '';

// Page meta
$pageTitle = e($videoTitle) . ' - ' . SITE_NAME;
$pageDescription = 'Watch ' . $videoTitle . ' by ' . $uploaderName . '. Free HD cam video on ' . SITE_NAME . '.';
$canonicalUrl = SITE_URL . '/video.php?id=' . urlencode($videoId) . '&slug=' . $videoSlug;
$currentNav = '';

// Schema markup for head
$headExtra = videoSchemaMarkup($video);

require_once __DIR__ . '/includes/header.php';
?>

<!-- HilltopAds: Above Player -->
<div class="ad-spot ad-video-top">
    <script type="text/javascript" src="https://hurtfulcell.com/act/files/tag.min.js?z=8499817" data-cfasync="false" async></script>
</div>

<div class="content-layout">
    <div class="content-main">
        <!-- Video Player -->
        <div class="video-player-wrap">
            <div class="player-container">
                <div id="camstube-player" style="position:absolute;top:0;left:0;width:100%;height:100%;">
                    <?php if (!empty($video['playerEmbedScript'])): ?>
                        <?php
                        // Embed script inside the container div - replace {CONTAINER} with our div ID
                        $embedScript = $video['playerEmbedScript'];
                        $embedScript = str_replace('{CONTAINER}', 'camstube-player', $embedScript);
                        $embedScript = str_replace('src="//', 'src="https://', $embedScript);
                        echo $embedScript;
                        ?>
                    <?php elseif (!empty($video['playerEmbedUrl'])): ?>
                        <?php
                        $embedUrl = $video['playerEmbedUrl'];
                        if (strpos($embedUrl, '//') === 0) $embedUrl = 'https:' . $embedUrl;
                        $embedUrl = str_replace('{CONTAINER}', 'camstube-player', $embedUrl);
                        ?>
                        <iframe src="<?= e($embedUrl) ?>" width="100%" height="100%" frameborder="0" allowfullscreen allow="autoplay; encrypted-media" style="width:100%;height:100%;border:none;"></iframe>
                    <?php else: ?>
                        <!-- Fallback: cover image with link -->
                        <a href="<?= e($targetUrl) ?>" target="_blank" rel="noopener" style="display:block;width:100%;height:100%;background:url(<?= e($coverImage) ?>) center/cover no-repeat;"></a>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- HilltopAds: Below Player -->
        <div class="ad-spot ad-video-bottom">
            <script type="text/javascript" src="https://hurtfulcell.com/act/files/tag.min.js?z=8499817" data-cfasync="false" async></script>
        </div>

        <!-- Video Info -->
        <div class="video-detail-info">
            <h1 class="video-detail-title"><?= e($videoTitle) ?></h1>

            <div class="video-meta">
                <div class="meta-item">
                    Duration: <span class="meta-value"><?= $duration ?></span>
                </div>
                <?php if ($quality): ?>
                <div class="meta-item">
                    Quality: <span class="meta-value"><?= e($quality) ?></span>
                </div>
                <?php endif; ?>
                <?php if ($isHd): ?>
                <div class="meta-item">
                    <span class="meta-value" style="color: var(--gold);">&#9733; HD</span>
                </div>
                <?php endif; ?>
            </div>

            <?php if (!empty($tags)): ?>
            <div class="video-tags">
                <?php foreach ($tags as $tag): ?>
                <a href="<?= tagUrl($tag) ?>" class="tag-pill"><?= e($tag) ?></a>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>

            <!-- Model Card -->
            <div class="model-card">
                <?php if ($profileImage): ?>
                <img src="<?= e($profileImage) ?>" alt="<?= e($uploaderName) ?>" class="model-avatar" loading="lazy">
                <?php endif; ?>
                <div class="model-info">
                    <h3><?= e($uploaderName) ?></h3>
                    <?php if ($targetUrl): ?>
                    <a href="<?= e($targetUrl) ?>" target="_blank" rel="noopener" class="model-link">Watch Live on LiveJasmin &#8594;</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Related Videos -->
        <div class="related-section">
            <h2 class="section-title">Related Videos</h2>
            <?php
            // Fetch related videos - try matching first tag
            $relatedParams = ['limit' => 8];
            if (!empty($tags)) {
                $relatedParams['forcedPerformers'] = '';
            }
            $relatedVideos = fetchVideos($relatedParams);
            // Remove current video from related
            $relatedVideos = array_filter($relatedVideos, function($v) use ($videoId) {
                return ($v['id'] ?? '') !== $videoId;
            });
            $relatedVideos = array_slice($relatedVideos, 0, 8);
            ?>

            <?php if (!empty($relatedVideos)): ?>
            <div class="video-grid">
                <?php foreach ($relatedVideos as $rel): ?>
                    <?= renderVideoCard($rel) ?>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <?= renderSidebar() ?>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
