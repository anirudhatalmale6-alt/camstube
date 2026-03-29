<?php
require_once __DIR__ . '/functions.php';

$pageTitle = $pageTitle ?? SITE_NAME . ' - ' . SITE_TAGLINE;
$pageDescription = $pageDescription ?? SITE_DESCRIPTION;
$canonicalUrl = $canonicalUrl ?? SITE_URL;
$currentNav = $currentNav ?? 'home';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($pageTitle) ?></title>
    <meta name="description" content="<?= e($pageDescription) ?>">
    <link rel="canonical" href="<?= e($canonicalUrl) ?>">
    <meta name="robots" content="index, follow">
    <meta property="og:title" content="<?= e($pageTitle) ?>">
    <meta property="og:description" content="<?= e($pageDescription) ?>">
    <meta property="og:type" content="website">
    <meta property="og:url" content="<?= e($canonicalUrl) ?>">
    <link rel="stylesheet" href="<?= SITE_URL ?>/assets/css/style.css">
    <?= $headExtra ?? '' ?>
</head>
<body>

<!-- HilltopAds Popunder Placeholder -->
<!-- <script>
    // HilltopAds: Insert popunder ad code here
    // Example: var _pop = {"zoneId": "YOUR_ZONE_ID"};
</script> -->

<header class="site-header">
    <!-- HilltopAds 728x90 Header Leaderboard -->
    <div class="ad-spot ad-leaderboard">
        <div class="ad-placeholder">
            <!-- HilltopAds: Insert 728x90 leaderboard ad code here -->
            <span>Ad 728x90</span>
        </div>
    </div>

    <nav class="navbar">
        <div class="container nav-container">
            <a href="<?= SITE_URL ?>/" class="logo">
                <span class="logo-icon">&#9654;</span>
                <span class="logo-text"><?= SITE_NAME ?></span>
            </a>

            <div class="nav-links">
                <a href="<?= SITE_URL ?>/" class="nav-link<?= $currentNav === 'home' ? ' active' : '' ?>">Home</a>
                <a href="<?= categoryUrl('girls') ?>" class="nav-link<?= $currentNav === 'girls' ? ' active' : '' ?>">Girls</a>
                <a href="<?= categoryUrl('guys') ?>" class="nav-link<?= $currentNav === 'guys' ? ' active' : '' ?>">Guys</a>
                <a href="<?= categoryUrl('trans') ?>" class="nav-link<?= $currentNav === 'trans' ? ' active' : '' ?>">Trans</a>
                <a href="<?= searchUrl() ?>" class="nav-link<?= $currentNav === 'search' ? ' active' : '' ?>">Search</a>
            </div>

            <button class="mobile-menu-btn" onclick="document.querySelector('.nav-links').classList.toggle('open')" aria-label="Menu">
                <span></span><span></span><span></span>
            </button>
        </div>
    </nav>
</header>

<main class="site-main">
    <div class="container">
