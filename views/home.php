<?php

declare(strict_types=1);

$pageTitle = 'Beranda';
require_once __DIR__ . '/layout/header.php';
?>

<!-- Hero Section -->
<section class="hero-section">
    <div class="container hero-container">
        <div class="hero-content">
            <span class="hero-pill">🚀 Platform Perpustakaan Digital Interaktif</span>
            <h1 class="hero-title">
                Sewa & Baca Koleksi <span class="text-gradient">Novel, Komik, dan E-Book</span> Terbaik
            </h1>
            <p class="hero-desc">
                Nikmati kemudahan mengakses ribuan lembar bacaan berkualitas secara fleksibel. Hitung biaya sewa transparan sesuai format bacaan dan mulai membaca sekarang juga.
            </p>
            <div class="hero-actions">
                <a href="?page=catalog" class="btn btn-primary btn-lg">
                    <span>Jelajahi Katalog</span>
                    <span class="btn-arrow">→</span>
                </a>
                <?php if (empty($_SESSION['bacain_member_id'])): ?>
                    <a href="?page=register" class="btn btn-outline btn-lg">Daftar Jadi Member</a>
                <?php else: ?>
                    <a href="?page=my-rentals" class="btn btn-outline btn-lg">Lihat Sewa Saya</a>
                <?php endif; ?>
            </div>
            <div class="hero-stats">
                <div class="stat-item">
                    <span class="stat-number">3+</span>
                    <span class="stat-label">Tipe Media (Novel, Komik, E-Book)</span>
                </div>
                <div class="stat-divider"></div>
                <div class="stat-item">
                    <span class="stat-number">100%</span>
                    <span class="stat-label">Sistem OOP Polymorphic</span>
                </div>
                <div class="stat-divider"></div>
                <div class="stat-item">
                    <span class="stat-number">24/7</span>
                    <span class="stat-label">Akses Digital Cepat</span>
                </div>
            </div>
        </div>

        <div class="hero-graphic">
            <div class="hero-card-stack">
                <div class="floating-badge badge-top">⭐ Koleksi Terpopuler</div>
                <div class="hero-book-preview novel-theme">
                    <div class="preview-tag">Novel</div>
                    <h3>Laskar Pelangi</h3>
                    <p class="author">Andrea Hirata</p>
                    <div class="preview-meta">534 Halaman • Tarif Halaman</div>
                </div>
                <div class="hero-book-preview komik-theme">
                    <div class="preview-tag">Komik</div>
                    <h3>Tahilalats</h3>
                    <p class="author">Nurfadli Mursyid</p>
                    <div class="preview-meta">18 Chapter • Full Color</div>
                </div>
                <div class="floating-badge badge-bottom">⚡ Sewa Instan</div>
            </div>
        </div>
    </div>
</section>

<!-- Feature Section -->
<section class="features-section" id="fitur">
    <div class="container">
        <div class="section-header text-center">
            <h2 class="section-title">Kategori Bacaan & Sistem Tarif Unik</h2>
            <p class="section-subtitle">
                Setiap kategori media memiliki mekanisme perhitungan tarif khusus sesuai prinsip polimorfisme OOP.
            </p>
        </div>

        <div class="features-grid">
            <div class="feature-card">
                <div class="feature-icon feature-novel">📚</div>
                <h3 class="feature-title">Novel</h3>
                <p class="feature-desc">
                    Membaca karya sastra dan fiksi panjang. Biaya sewa dihitung berdasarkan jumlah halaman (<span class="fw-bold">Rp 20/halaman</span>) ditambah tarif harian.
                </p>
                <a href="?page=catalog&category=novel" class="feature-link">Lihat Novel →</a>
            </div>

            <div class="feature-card">
                <div class="feature-icon feature-komik">🎨</div>
                <h3 class="feature-title">Komik & Manga</h3>
                <p class="feature-desc">
                    Nikmati visual cerita bergambar. Tarif sewa dinamis dihitung per chapter (<span class="fw-bold">Rp 500 - Rp 750/chapter</span>) dengan dukungan full-color.
                </p>
                <a href="?page=catalog&category=komik" class="feature-link">Lihat Komik →</a>
            </div>

            <div class="feature-card">
                <div class="feature-icon feature-ebook">💻</div>
                <h3 class="feature-title">E-Book Digital</h3>
                <p class="feature-desc">
                    Buku teknologi, sains, dan pengembangan diri dalam format EPUB / PDF. Ditambah biaya cloud storage efisien (<span class="fw-bold">Rp 50/MB</span>).
                </p>
                <a href="?page=catalog&category=ebook" class="feature-link">Lihat E-Book →</a>
            </div>
        </div>
    </div>
</section>

<!-- Popular Books Section -->
<section class="popular-section">
    <div class="container">
        <div class="section-header flex-between">
            <div>
                <h2 class="section-title">Buku Populer Saat Ini</h2>
                <p class="section-subtitle">Koleksi pilihan yang paling sering disewa oleh pembaca setia kami.</p>
            </div>
            <a href="?page=catalog" class="btn btn-outline">Lihat Semua Katalog →</a>
        </div>

        <div class="book-grid">
            <?php if (empty($popularBooks)): ?>
                <div class="empty-state">
                    <p>Belum ada buku tersedia di katalog.</p>
                </div>
            <?php else: ?>
                <?php foreach ($popularBooks as $book): ?>
                    <?php
                    $isNovel = $book instanceof \Bacain\Models\Novel;
                    $isKomik = $book instanceof \Bacain\Models\Komik;
                    $isEBook = $book instanceof \Bacain\Models\EBook;

                    if ($isNovel) {
                        $typeLabel = 'Novel';
                        $typeClass = 'type-novel';
                        $metaInfo = $book->getJumlahHalaman() . ' Halaman';
                    } elseif ($isKomik) {
                        $typeLabel = 'Komik';
                        $typeClass = 'type-komik';
                        $metaInfo = $book->getJumlahChapter() . ' Ch' . ($book->isBerwarna() ? ' • Berwarna' : '');
                    } elseif ($isEBook) {
                        $typeLabel = 'E-Book';
                        $typeClass = 'type-ebook';
                        $metaInfo = $book->getFormatFile() . ' • ' . $book->getUkuranFile() . ' MB';
                    } else {
                        $typeLabel = 'Buku';
                        $typeClass = 'type-default';
                        $metaInfo = 'Media';
                    }
                    ?>
                    <article class="book-card">
                        <div class="book-cover-wrapper <?= $typeClass ?>">
                            <div class="book-cover-pattern"></div>
                            <span class="book-type-badge <?= $typeClass ?>"><?= htmlspecialchars($typeLabel) ?></span>
                            <div class="book-cover-center">
                                <span class="book-cover-icon"><?= $isKomik ? '🎨' : ($isNovel ? '📖' : '💾') ?></span>
                                <h4 class="book-cover-title"><?= htmlspecialchars($book->getJudul()) ?></h4>
                            </div>
                        </div>

                        <div class="book-details">
                            <div class="book-header-info">
                                <h3 class="book-title" title="<?= htmlspecialchars($book->getJudul()) ?>">
                                    <a href="?page=book-detail&id=<?= urlencode((string) $book->getId()) ?>">
                                        <?= htmlspecialchars($book->getJudul()) ?>
                                    </a>
                                </h3>
                                <p class="book-author">Oleh <span><?= htmlspecialchars($book->getPenulis()) ?></span></p>
                            </div>

                            <div class="book-meta-tags">
                                <span class="meta-tag"><?= htmlspecialchars($metaInfo) ?></span>
                                <?php if ($book->getStok() > 0): ?>
                                    <span class="stock-badge stock-available">Stok: <?= $book->getStok() ?></span>
                                <?php else: ?>
                                    <span class="stock-badge stock-empty">Stok Habis</span>
                                <?php endif; ?>
                            </div>

                            <div class="book-footer">
                                <div class="book-price">
                                    <span class="price-label">Mulai dari</span>
                                    <span class="price-value">Rp <?= number_format($book->hitungHargaSewa(1), 0, ',', '.') ?><small>/hr</small></span>
                                </div>
                                <a href="?page=book-detail&id=<?= urlencode((string) $book->getId()) ?>" class="btn btn-sm btn-primary">
                                    Detail & Sewa
                                </a>
                            </div>
                        </div>
                    </article>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</section>

<!-- Call to Action Banner -->
<section class="cta-section">
    <div class="container">
        <div class="cta-box">
            <div class="cta-text">
                <h2>Siap Menemukan Bacaan Berikutnya?</h2>
                <p>Daftar sekarang dan nikmati kuota sewa hingga 3 buku secara bersamaan!</p>
            </div>
            <div class="cta-btn-wrap">
                <a href="?page=register" class="btn btn-light btn-lg">Daftar Akun Gratis</a>
            </div>
        </div>
    </div>
</section>

<?php require_once __DIR__ . '/layout/footer.php'; ?>
