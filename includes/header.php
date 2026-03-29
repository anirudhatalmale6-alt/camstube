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

<!-- HilltopAds Popunder -->
<script type="text/javascript" src="https://questionablenet.com/act/files/tag.min.js?z=8499811" data-cfasync="false" async></script>

<header class="site-header">
    <!-- HilltopAds Header Slide -->
    <div class="ad-spot ad-leaderboard">
        <script type="text/javascript" src="https://hurtfulcell.com/act/files/tag.min.js?z=8499817" data-cfasync="false" async></script>
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
