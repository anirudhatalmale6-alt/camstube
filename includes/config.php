<?php
/**
 * CamsTube Configuration
 */

// Site Settings
define('SITE_NAME', 'CamsTube');
define('SITE_URL', (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http') . '://' . ($_SERVER['HTTP_HOST'] ?? 'localhost'));
define('SITE_TAGLINE', 'Free Cam Videos');
define('SITE_DESCRIPTION', 'Watch free cam model videos from top performers. HD quality cam recordings updated daily.');

// AWEmpire API Settings
define('API_BASE_URL', 'https://atwmcd.com/api/video-promotion/v1');
define('API_PSID', '6camgirl');
define('API_PSTOOL_LIST', '421_1');
define('API_PSTOOL_DETAILS', '421_2');
define('API_ACCESS_KEY', '818113f58d0e4a74c21b9c204e464e94');
define('API_SITE', 'jasmin');
define('API_PRIMARY_COLOR', '8AC437');
define('API_LABEL_COLOR', '212121');
define('DEFAULT_LIMIT', 24);

// Cache Settings
define('CACHE_DIR', '/tmp/camstube_cache/');
define('CACHE_TTL', 1800); // 30 minutes in seconds

// Design Colors
define('COLOR_BG', '#0d0d0d');
define('COLOR_CARD', '#1a1a1a');
define('COLOR_ACCENT', '#e91e63');

// PornMayer Network Sites
$NETWORK_SITES = [
    ['name' => 'PornMayer', 'url' => 'https://pornmayer.com', 'desc' => 'Premium Porn Hub'],
    ['name' => 'Ebony Femdom Tube', 'url' => 'https://ebonyfemdomtube.com', 'desc' => 'Ebony Femdom Videos'],
    ['name' => 'Asian Femdom Tube', 'url' => 'https://asianfemdomtube.com', 'desc' => 'Asian Femdom Videos'],
    ['name' => 'Shemale TubeX', 'url' => 'https://shemaletubex.com', 'desc' => 'Shemale Videos'],
    ['name' => 'Blue Banana Tube', 'url' => 'https://bluebananatube.com', 'desc' => 'Blue Banana Videos'],
    ['name' => 'Fetish TubeX', 'url' => 'https://fetishtubex.com', 'desc' => 'Fetish Videos'],
    ['name' => 'Fetish Mega Store', 'url' => 'https://fetishmegastore.com', 'desc' => 'Fetish Mega Collection'],
];

// Category Mapping
$CATEGORIES = [
    'girls' => ['label' => 'Girls', 'orientation' => 'straight', 'icon' => '♀'],
    'guys' => ['label' => 'Guys', 'orientation' => 'gay', 'icon' => '♂'],
    'trans' => ['label' => 'Trans', 'orientation' => 'transgender', 'icon' => '⚧'],
    'couples' => ['label' => 'Couples', 'orientation' => 'straight', 'type' => 'couples', 'icon' => '♥'],
];

// Popular Tags
$POPULAR_TAGS = [
    'blonde', 'brunette', 'redhead', 'asian', 'latina', 'ebony',
    'milf', 'teen', 'mature', 'bbw', 'petite', 'curvy',
    'anal', 'solo', 'lesbian', 'fetish', 'bdsm', 'squirt',
    'big tits', 'small tits', 'big ass', 'stockings', 'lingerie', 'cosplay',
    'creampie', 'dildo', 'vibrator', 'oil', 'massage', 'striptease'
];
