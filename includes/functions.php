<?php
/**
 * CamsTube Helper Functions
 */

require_once __DIR__ . '/config.php';

/**
 * Get visitor's real IP address
 */
function getClientIp(): string {
    $headers = ['HTTP_CF_CONNECTING_IP', 'HTTP_X_FORWARDED_FOR', 'HTTP_X_REAL_IP', 'REMOTE_ADDR'];
    foreach ($headers as $header) {
        if (!empty($_SERVER[$header])) {
            $ip = trim(explode(',', $_SERVER[$header])[0]);
            if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE)) {
                return $ip;
            }
        }
    }
    return $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
}

/**
 * Initialize cache directory
 */
function initCache(): void {
    if (!is_dir(CACHE_DIR)) {
        @mkdir(CACHE_DIR, 0755, true);
    }
}

/**
 * Get cached data or null if expired/missing
 */
function cacheGet(string $key): ?array {
    initCache();
    $file = CACHE_DIR . md5($key) . '.json';
    if (!file_exists($file)) return null;

    $data = json_decode(file_get_contents($file), true);
    if (!$data || !isset($data['expires']) || time() > $data['expires']) {
        @unlink($file);
        return null;
    }
    return $data['payload'];
}

/**
 * Store data in cache
 */
function cacheSet(string $key, array $data): void {
    initCache();
    $file = CACHE_DIR . md5($key) . '.json';
    $payload = json_encode([
        'expires' => time() + CACHE_TTL,
        'payload' => $data
    ]);
    @file_put_contents($file, $payload, LOCK_EX);
}

/**
 * Fetch video list from API
 */
function fetchVideos(array $params = []): array {
    $defaults = [
        'psid' => API_PSID,
        'pstool' => API_PSTOOL_LIST,
        'accessKey' => API_ACCESS_KEY,
        'ms_notrack' => 1,
        'program' => 'vpapi',
        'campaign_id' => '',
        'type' => '',
        'site' => API_SITE,
        'sexualOrientation' => 'straight',
        'forcedPerformers' => '',
        'limit' => DEFAULT_LIMIT,
        'primaryColor' => '#' . API_PRIMARY_COLOR,
        'labelColor' => '#' . API_LABEL_COLOR,
        'clientIp' => getClientIp(),
    ];

    $query = array_merge($defaults, $params);
    $cacheKey = 'list_' . md5(serialize($query));

    $cached = cacheGet($cacheKey);
    if ($cached !== null) return $cached;

    $url = API_BASE_URL . '/list?' . http_build_query($query);
    $response = apiRequest($url);

    if ($response && isset($response['data']['videos'])) {
        $videos = $response['data']['videos'];
        // Fix protocol-relative URLs
        $videos = fixVideoUrls($videos);
        cacheSet($cacheKey, $videos);
        return $videos;
    }

    return [];
}

/**
 * Fetch video details from API
 */
function fetchVideoDetails(string $videoId): ?array {
    $cacheKey = 'details_' . $videoId;
    $cached = cacheGet($cacheKey);
    if ($cached !== null) return $cached;

    $params = [
        'psid' => API_PSID,
        'pstool' => API_PSTOOL_DETAILS,
        'accessKey' => API_ACCESS_KEY,
        'ms_notrack' => 1,
        'campaign_id' => '',
        'site' => 'jsm',
        'clientIp' => getClientIp(),
        'primaryColor' => API_PRIMARY_COLOR,
        'labelColor' => API_LABEL_COLOR,
        'psprogram' => 'VPAPI',
    ];

    $url = API_BASE_URL . '/details/' . urlencode($videoId) . '/?' . http_build_query($params);
    $response = apiRequest($url);

    if ($response && isset($response['data'])) {
        $video = $response['data'];
        $video = fixSingleVideoUrls($video);
        cacheSet($cacheKey, $video);
        return $video;
    }

    return null;
}

/**
 * Make API request with cURL
 */
function apiRequest(string $url): ?array {
    $ch = curl_init();
    curl_setopt_array($ch, [
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT => 15,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTPHEADER => [
            'Accept: application/json',
            'User-Agent: CamsTube/1.0'
        ],
        CURLOPT_SSL_VERIFYPEER => true,
    ]);

    $result = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($httpCode === 200 && $result) {
        return json_decode($result, true);
    }

    return null;
}

/**
 * Fix protocol-relative URLs in video array
 */
function fixVideoUrls(array $videos): array {
    foreach ($videos as &$video) {
        $video = fixSingleVideoUrls($video);
    }
    return $videos;
}

/**
 * Fix protocol-relative URLs in a single video
 */
function fixSingleVideoUrls(array $video): array {
    $urlFields = ['profileImage', 'thumbImage', 'targetUrl', 'detailsUrl', 'coverImage', 'uploaderLink', 'playerEmbedUrl'];
    foreach ($urlFields as $field) {
        if (isset($video[$field]) && strpos($video[$field], '//') === 0) {
            $video[$field] = 'https:' . $video[$field];
        }
    }

    if (isset($video['previewImages']) && is_array($video['previewImages'])) {
        foreach ($video['previewImages'] as &$img) {
            if (strpos($img, '//') === 0) {
                $img = 'https:' . $img;
            }
        }
    }

    // Fix playerEmbedScript URLs
    if (isset($video['playerEmbedScript'])) {
        $video['playerEmbedScript'] = str_replace('src="//', 'src="https://', $video['playerEmbedScript']);
    }

    return $video;
}

/**
 * Format duration from seconds to MM:SS or HH:MM:SS
 */
function formatDuration(int $seconds): string {
    if ($seconds >= 3600) {
        return sprintf('%d:%02d:%02d', floor($seconds / 3600), floor(($seconds % 3600) / 60), $seconds % 60);
    }
    return sprintf('%d:%02d', floor($seconds / 60), $seconds % 60);
}

/**
 * Truncate text to specified length
 */
function truncate(string $text, int $length = 50): string {
    if (mb_strlen($text) <= $length) return $text;
    return mb_substr($text, 0, $length) . '...';
}

/**
 * Generate SEO-friendly slug
 */
function slugify(string $text): string {
    $text = strtolower(trim($text));
    $text = preg_replace('/[^a-z0-9\s-]/', '', $text);
    $text = preg_replace('/[\s-]+/', '-', $text);
    return trim($text, '-');
}

/**
 * Build internal URL for a video
 */
function videoUrl(array $video): string {
    $slug = slugify($video['title'] ?? 'video');
    return SITE_URL . '/video.php?id=' . urlencode($video['id']) . '&slug=' . $slug;
}

/**
 * Build internal URL for a category
 */
function categoryUrl(string $category, int $page = 1): string {
    $url = SITE_URL . '/category.php?cat=' . urlencode($category);
    if ($page > 1) $url .= '&page=' . $page;
    return $url;
}

/**
 * Build internal URL for a tag
 */
function tagUrl(string $tag): string {
    return SITE_URL . '/category.php?tag=' . urlencode($tag);
}

/**
 * Build internal URL for search
 */
function searchUrl(string $query = ''): string {
    $url = SITE_URL . '/search.php';
    if ($query) $url .= '?q=' . urlencode($query);
    return $url;
}

/**
 * Sanitize output for HTML
 */
function e(string $str): string {
    return htmlspecialchars($str, ENT_QUOTES, 'UTF-8');
}

/**
 * Get current page number from query string
 */
function getCurrentPage(): int {
    return max(1, intval($_GET['page'] ?? 1));
}

/**
 * Render a video card HTML
 */
function renderVideoCard(array $video): string {
    $thumb = e($video['thumbImage'] ?? '');
    $title = e($video['title'] ?? 'Untitled Video');
    $duration = formatDuration(intval($video['duration'] ?? 0));
    $isHd = !empty($video['isHd']);
    $uploader = e($video['uploader'] ?? 'Unknown');
    $url = videoUrl($video);
    $previewImages = $video['previewImages'] ?? [];
    $previewJson = e(json_encode($previewImages));

    $hdBadge = $isHd ? '<span class="badge badge-hd">HD</span>' : '';

    return <<<HTML
    <div class="video-card" data-previews="{$previewJson}">
        <a href="{$url}" class="video-thumb-link">
            <div class="video-thumb">
                <img src="{$thumb}" alt="{$title}" loading="lazy" class="thumb-img">
                <span class="badge badge-duration">{$duration}</span>
                {$hdBadge}
                <div class="thumb-overlay"><span class="play-icon">&#9654;</span></div>
            </div>
        </a>
        <div class="video-info">
            <a href="{$url}" class="video-title" title="{$title}">{$title}</a>
            <span class="video-model">{$uploader}</span>
        </div>
    </div>
HTML;
}

/**
 * Render pagination HTML
 */
function renderPagination(int $currentPage, int $totalItems, int $perPage, string $baseUrl): string {
    $totalPages = max(1, ceil($totalItems / $perPage));
    if ($totalPages <= 1) return '';

    $separator = strpos($baseUrl, '?') !== false ? '&' : '?';
    $html = '<div class="pagination">';

    if ($currentPage > 1) {
        $html .= '<a href="' . $baseUrl . $separator . 'page=' . ($currentPage - 1) . '" class="page-link">&laquo; Prev</a>';
    }

    $start = max(1, $currentPage - 3);
    $end = min($totalPages, $currentPage + 3);

    if ($start > 1) {
        $html .= '<a href="' . $baseUrl . $separator . 'page=1" class="page-link">1</a>';
        if ($start > 2) $html .= '<span class="page-dots">...</span>';
    }

    for ($i = $start; $i <= $end; $i++) {
        $active = $i === $currentPage ? ' active' : '';
        $html .= '<a href="' . $baseUrl . $separator . 'page=' . $i . '" class="page-link' . $active . '">' . $i . '</a>';
    }

    if ($end < $totalPages) {
        if ($end < $totalPages - 1) $html .= '<span class="page-dots">...</span>';
        $html .= '<a href="' . $baseUrl . $separator . 'page=' . $totalPages . '" class="page-link">' . $totalPages . '</a>';
    }

    if ($currentPage < $totalPages) {
        $html .= '<a href="' . $baseUrl . $separator . 'page=' . ($currentPage + 1) . '" class="page-link">Next &raquo;</a>';
    }

    $html .= '</div>';
    return $html;
}

/**
 * Render sidebar with ads and network links
 */
function renderSidebar(): string {
    global $NETWORK_SITES;

    $networkLinks = '';
    foreach ($NETWORK_SITES as $site) {
        $networkLinks .= '<a href="' . e($site['url']) . '" target="_blank" rel="noopener" class="network-link">'
            . '<span class="network-name">' . e($site['name']) . '</span>'
            . '<span class="network-desc">' . e($site['desc']) . '</span>'
            . '</a>';
    }

    return <<<HTML
    <aside class="sidebar">
        <!-- HilltopAds Sidebar -->
        <div class="ad-spot ad-300x250">
            <script type="text/javascript" src="https://hurtfulcell.com/act/files/tag.min.js?z=8499817" data-cfasync="false" async></script>
        </div>

        <!-- PornMayer Network Banner -->
        <div class="network-banner">
            <a href="https://pornmayer.com" target="_blank" rel="noopener">
                <div class="promo-banner">
                    <span class="promo-title">PornMayer</span>
                    <span class="promo-sub">Visit Our Premium Network</span>
                </div>
            </a>
        </div>

        <!-- From Our Network -->
        <div class="sidebar-section">
            <h3 class="sidebar-title">From Our Network</h3>
            <div class="network-links">
                {$networkLinks}
            </div>
        </div>

        <!-- HilltopAds Sidebar #2 -->
        <div class="ad-spot ad-300x250">
            <script type="text/javascript" src="https://hurtfulcell.com/act/files/tag.min.js?z=8499817" data-cfasync="false" async></script>
        </div>
    </aside>
HTML;
}

/**
 * Generate Schema.org VideoObject JSON-LD
 */
function videoSchemaMarkup(array $video): string {
    $schema = [
        '@context' => 'https://schema.org',
        '@type' => 'VideoObject',
        'name' => $video['title'] ?? '',
        'description' => ($video['title'] ?? '') . ' - Free cam video from ' . ($video['uploader'] ?? 'cam model'),
        'thumbnailUrl' => $video['thumbImage'] ?? $video['coverImage'] ?? '',
        'duration' => 'PT' . intval($video['duration'] ?? 0) . 'S',
        'uploadDate' => date('Y-m-d'),
        'contentUrl' => videoUrl($video),
    ];

    if (!empty($video['uploader'])) {
        $schema['author'] = [
            '@type' => 'Person',
            'name' => $video['uploader'],
        ];
    }

    return '<script type="application/ld+json">' . json_encode($schema, JSON_UNESCAPED_SLASHES) . '</script>';
}
