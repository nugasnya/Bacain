<?php

declare(strict_types=1);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$isLoggedIn = !empty($_SESSION['bacain_member_id']);
$currentMember = null;

if ($isLoggedIn) {
    if (isset($member) && $member instanceof \Bacain\Models\Member) {
        $currentMember = $member;
    } else {
        try {
            $memberRepo = new \Bacain\Repositories\MemberRepository();
            $currentMember = $memberRepo->findById((string) $_SESSION['bacain_member_id']);
        } catch (\Throwable $th) {
            $currentMember = null;
        }
    }
}

$currentPage = (string) ($_GET['page'] ?? 'home');
$flashSuccess = $_SESSION['flash_success'] ?? null;
$flashError = $_SESSION['flash_error'] ?? null;
unset($_SESSION['flash_success'], $_SESSION['flash_error']);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($pageTitle) ? htmlspecialchars($pageTitle) . ' - ' : '' ?>Bacain | Perpustakaan Digital & Rental Buku</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <header class="site-header">
        <nav class="navbar container">
            <a href="?page=home" class="brand-logo" aria-label="Bacain Beranda">
                <span class="logo-icon">📖</span>
                <span class="logo-text">Bacain<span class="logo-dot">.</span></span>
            </a>

            <button class="nav-toggle" id="nav-toggle" aria-label="Toggle navigasi" aria-expanded="false">
                <span class="hamburger-bar"></span>
                <span class="hamburger-bar"></span>
                <span class="hamburger-bar"></span>
            </button>

            <div class="nav-menu" id="nav-menu">
                <ul class="nav-links">
                    <li>
                        <a href="?page=home" class="nav-link <?= $currentPage === 'home' ? 'active' : '' ?>">Beranda</a>
                    </li>
                    <li>
                        <a href="?page=catalog" class="nav-link <?= in_array($currentPage, ['catalog', 'book-detail', 'detail'], true) ? 'active' : '' ?>">Katalog Buku</a>
                    </li>
                    <?php if ($isLoggedIn): ?>
                        <li>
                            <a href="?page=my-rentals" class="nav-link <?= in_array($currentPage, ['my-rentals', 'rentals'], true) ? 'active' : '' ?>">
                                Sewa Saya
                            </a>
                        </li>
                    <?php endif; ?>
                </ul>

                <div class="nav-auth">
                    <?php if ($isLoggedIn && $currentMember !== null): ?>
                        <div class="user-profile-badge">
                            <span class="user-avatar"><?= htmlspecialchars(strtoupper(substr($currentMember->getNama(), 0, 1))) ?></span>
                            <div class="user-info">
                                <span class="user-name"><?= htmlspecialchars($currentMember->getNama()) ?></span>
                                <span class="user-tier"><?= htmlspecialchars($currentMember->getStatusKeanggotaan()) ?></span>
                            </div>
                        </div>
                        <a href="?page=logout" class="btn btn-outline btn-sm">Keluar</a>
                    <?php else: ?>
                        <a href="?page=login" class="btn btn-ghost btn-sm <?= $currentPage === 'login' ? 'active' : '' ?>">Masuk</a>
                        <a href="?page=register" class="btn btn-primary btn-sm <?= $currentPage === 'register' ? 'active' : '' ?>">Daftar</a>
                    <?php endif; ?>
                </div>
            </div>
        </nav>
    </header>

    <div class="flash-messages container">
        <?php if ($flashSuccess): ?>
            <div class="alert alert-success" role="alert">
                <span class="alert-icon">✓</span>
                <span class="alert-text"><?= htmlspecialchars($flashSuccess) ?></span>
                <button type="button" class="alert-close" aria-label="Tutup notifikasi">&times;</button>
            </div>
        <?php endif; ?>

        <?php if ($flashError): ?>
            <div class="alert alert-danger" role="alert">
                <span class="alert-icon">⚠️</span>
                <span class="alert-text"><?= htmlspecialchars($flashError) ?></span>
                <button type="button" class="alert-close" aria-label="Tutup notifikasi">&times;</button>
            </div>
        <?php endif; ?>
    </div>

    <main class="main-content">
