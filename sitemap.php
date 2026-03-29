<?php
/**
 * CamsTube - Dynamic XML Sitemap
 * Generates sitemap from cached/live API data
 */

require_once __DIR__ . '/includes/functions.php';

header('Content-Type: application/xml; charset=utf-8');

$baseUrl = SITE_URL;
$today = date('Y-m-d');

echo '<?xml version="1.0" encoding="UTF-8"?>';
?>

<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
    <!-- Homepage -->
    <url>
        <loc><?= e($baseUrl) ?>/</loc>
        <lastmod><?= $today ?></lastmod>
        <changefreq>daily</changefreq>
        <priority>1.0</priority>
    </url>

    <!-- Category Pages -->
    <url>
        <loc><?= e($baseUrl) ?>/category.php?cat=girls</loc>
        <lastmod><?= $today ?></lastmod>
        <changefreq>daily</changefreq>
        <priority>0.8</priority>
    </url>
    <url>
        <loc><?= e($baseUrl) ?>/category.php?cat=guys</loc>
        <lastmod><?= $today ?></lastmod>
        <changefreq>daily</changefreq>
        <priority>0.8</priority>
    </url>
    <url>
        <loc><?= e($baseUrl) ?>/category.php?cat=trans</loc>
        <lastmod><?= $today ?></lastmod>
        <changefreq>daily</changefreq>
        <priority>0.8</priority>
    </url>
    <url>
        <loc><?= e($baseUrl) ?>/category.php?cat=couples</loc>
        <lastmod><?= $today ?></lastmod>
        <changefreq>daily</changefreq>
        <priority>0.8</priority>
    </url>

    <!-- Search Page -->
    <url>
        <loc><?= e($baseUrl) ?>/search.php</loc>
        <lastmod><?= $today ?></lastmod>
        <changefreq>weekly</changefreq>
        <priority>0.6</priority>
    </url>

    <!-- Popular Tag Pages -->
<?php
$tags = ['blonde', 'brunette', 'asian', 'latina', 'ebony', 'milf', 'teen', 'mature', 'solo', 'lesbian', 'fetish', 'anal'];
foreach ($tags as $tag):
?>
    <url>
        <loc><?= e($baseUrl) ?>/category.php?tag=<?= urlencode($tag) ?></loc>
        <lastmod><?= $today ?></lastmod>
        <changefreq>daily</changefreq>
        <priority>0.7</priority>
    </url>
<?php endforeach; ?>

    <!-- Video Pages (from recent API fetch) -->
<?php
$videos = fetchVideos(['limit' => 50]);
foreach ($videos as $video):
    $videoUrl = $baseUrl . '/video.php?id=' . urlencode($video['id']) . '&slug=' . slugify($video['title'] ?? 'video');
?>
    <url>
        <loc><?= e($videoUrl) ?></loc>
        <lastmod><?= $today ?></lastmod>
        <changefreq>weekly</changefreq>
        <priority>0.6</priority>
    </url>
<?php endforeach; ?>
</urlset>
