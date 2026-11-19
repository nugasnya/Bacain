<?php

declare(strict_types=1);

$pageTitle = 'Katalog Buku';
$keyword = $keyword ?? '';
$category = $category ?? 'all';

require_once __DIR__ . '/layout/header.php';
?>

<div class="catalog-page container">
    <div class="catalog-header">
        <h1 class="page-heading">Katalog Bacaan Digital</h1>
        <p class="page-subheading">Temukan koleksi Novel sastra, Komik berwarna, dan E-Book terkini.</p>
    </div>

    <!-- Filter & Search Controls -->
    <div class="catalog-controls">
        <form action="" method="GET" class="search-form" id="search-form">
            <input type="hidden" name="page" value="catalog">
            <input type="hidden" name="category" value="<?= htmlspecialchars($category) ?>" id="search-category-input">
            
            <div class="search-input-group">
                <span class="search-icon">🔍</span>
                <input 
                    type="text" 
                    name="q" 
                    id="search-input" 
                    class="form-control search-input" 
                    placeholder="Cari judul buku atau penulis..." 
                    value="<?= htmlspecialchars($keyword) ?>"
                    autocomplete="off"
                >
                <?php if ($keyword !== ''): ?>
                    <a href="?page=catalog&category=<?= urlencode($category) ?>" class="search-clear-btn" title="Hapus pencarian">&times;</a>
                <?php endif; ?>
                <button type="submit" class="btn btn-primary search-btn">Cari</button>
            </div>
        </form>

        <div class="category-tabs" id="category-tabs">
            <a href="?page=catalog&category=all<?= $keyword !== '' ? '&q=' . urlencode($keyword) : '' ?>" 
               class="category-tab <?= ($category === 'all' || $category === '') ? 'active' : '' ?>"
               data-category="all">
                Semua Koleksi
            </a>
            <a href="?page=catalog&category=novel<?= $keyword !== '' ? '&q=' . urlencode($keyword) : '' ?>" 
               class="category-tab <?= $category === 'novel' ? 'active' : '' ?>"
               data-category="novel">
                📚 Novel
            </a>
            <a href="?page=catalog&category=komik<?= $keyword !== '' ? '&q=' . urlencode($keyword) : '' ?>" 
               class="category-tab <?= $category === 'komik' ? 'active' : '' ?>"
               data-category="komik">
                🎨 Komik
            </a>
            <a href="?page=catalog&category=ebook<?= $keyword !== '' ? '&q=' . urlencode($keyword) : '' ?>" 
               class="category-tab <?= $category === 'ebook' ? 'active' : '' ?>"
               data-category="ebook">
                💻 E-Book
            </a>
        </div>
    </div>

    <!-- Results Status -->
    <div class="catalog-status">
        <span class="results-count">
            Menampilkan <strong><?= count($books) ?></strong> buku
            <?php if ($keyword !== ''): ?>
                untuk pencarian "<mark><?= htmlspecialchars($keyword) ?></mark>"
            <?php endif; ?>
            <?php if ($category !== 'all' && $category !== ''): ?>
                dalam kategori <span class="badge-category-filter"><?= htmlspecialchars(ucfirst($category)) ?></span>
            <?php endif; ?>
        </span>
    </div>

    <!-- Book Grid -->
    <?php if (empty($books)): ?>
        <div class="empty-state-box">
            <div class="empty-icon">📭</div>
            <h3>Buku Tidak Ditemukan</h3>
            <p>Maaf, tidak ada buku yang cocok dengan kriteria pencarian atau filter Anda.</p>
            <a href="?page=catalog" class="btn btn-outline">Reset Filter & Pencarian</a>
        </div>
    <?php else: ?>
        <div class="book-grid" id="catalog-book-grid">
            <?php foreach ($books as $book): ?>
                <?php
                $isNovel = $book instanceof \Bacain\Models\Novel;
                $isKomik = $book instanceof \Bacain\Models\Komik;
                $isEBook = $book instanceof \Bacain\Models\EBook;

                if ($isNovel) {
                    $typeLabel = 'Novel';
                    $typeClass = 'type-novel';
                    $metaInfo = $book->getJumlahHalaman() . ' Halaman';
                    $dataCategory = 'novel';
                } elseif ($isKomik) {
                    $typeLabel = 'Komik';
                    $typeClass = 'type-komik';
                    $metaInfo = $book->getJumlahChapter() . ' Ch' . ($book->isBerwarna() ? ' • Berwarna' : '');
                    $dataCategory = 'komik';
                } elseif ($isEBook) {
                    $typeLabel = 'E-Book';
                    $typeClass = 'type-ebook';
                    $metaInfo = $book->getFormatFile() . ' • ' . $book->getUkuranFile() . ' MB';
                    $dataCategory = 'ebook';
                } else {
                    $typeLabel = 'Buku';
                    $typeClass = 'type-default';
                    $metaInfo = 'Media';
                    $dataCategory = 'all';
                }
                ?>
                <article class="book-card" data-title="<?= htmlspecialchars(strtolower($book->getJudul())) ?>" data-author="<?= htmlspecialchars(strtolower($book->getPenulis())) ?>" data-category="<?= $dataCategory ?>">
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
        </div>
    <?php endif; ?>
</div>

<?php require_once __DIR__ . '/layout/footer.php'; ?>
