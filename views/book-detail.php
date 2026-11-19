<?php

declare(strict_types=1);

$pageTitle = $book ? $book->getJudul() : 'Buku Tidak Ditemukan';
require_once __DIR__ . '/layout/header.php';
?>

<div class="book-detail-page container">
    <?php if ($book === null): ?>
        <div class="empty-state-box">
            <div class="empty-icon">❌</div>
            <h2>Buku Tidak Ditemukan</h2>
            <p>Buku dengan ID tersebut tidak tersedia atau telah dihapus dari sistem.</p>
            <a href="?page=catalog" class="btn btn-primary">Kembali ke Katalog</a>
        </div>
    <?php else: ?>
        <?php
        $isNovel = $book instanceof \Bacain\Models\Novel;
        $isKomik = $book instanceof \Bacain\Models\Komik;
        $isEBook = $book instanceof \Bacain\Models\EBook;

        if ($isNovel) {
            $typeLabel = 'Novel';
            $typeClass = 'type-novel';
            $metaDetails = [
                'Jumlah Halaman' => $book->getJumlahHalaman() . ' Halaman',
                'Format File' => $book->getFormatFile(),
                'Ukuran File' => $book->getUkuranFile() . ' MB',
                'Skema Tarif' => 'Dasar + (Halaman × Rp 20)',
            ];
        } elseif ($isKomik) {
            $typeLabel = 'Komik';
            $typeClass = 'type-komik';
            $metaDetails = [
                'Jumlah Chapter' => $book->getJumlahChapter() . ' Bab / Chapter',
                'Warna' => $book->isBerwarna() ? 'Full Color (Berwarna)' : 'Hitam Putih (B&W)',
                'Skema Tarif' => 'Dasar + (Chapter × Rp ' . ($book->isBerwarna() ? '750' : '500') . ')',
            ];
        } elseif ($isEBook) {
            $typeLabel = 'E-Book';
            $typeClass = 'type-ebook';
            $metaDetails = [
                'Format File' => $book->getFormatFile(),
                'Ukuran Dokumen' => $book->getUkuranFile() . ' MB',
                'Skema Tarif' => 'Dasar + (Ukuran MB × Rp 50)',
            ];
        } else {
            $typeLabel = 'Media Umum';
            $typeClass = 'type-default';
            $metaDetails = [
                'Format' => 'Dokumen Digital',
                'Skema Tarif' => 'Tarif Dasar Harian',
            ];
        }

        $defaultDays = 7;
        $estimatedInitialCost = $book->hitungHargaSewa($defaultDays);
        ?>

        <!-- Breadcrumb -->
        <nav class="breadcrumb" aria-label="Jejak navigasi">
            <a href="?page=home">Beranda</a>
            <span class="sep">/</span>
            <a href="?page=catalog">Katalog</a>
            <span class="sep">/</span>
            <span class="current"><?= htmlspecialchars($book->getJudul()) ?></span>
        </nav>

        <div class="detail-layout">
            <!-- Left Column: Visual Cover Card -->
            <div class="detail-visual">
                <div class="detail-cover-card <?= $typeClass ?>">
                    <span class="book-type-badge <?= $typeClass ?>"><?= htmlspecialchars($typeLabel) ?></span>
                    <div class="detail-cover-body">
                        <span class="detail-cover-icon"><?= $isKomik ? '🎨' : ($isNovel ? '📖' : '💾') ?></span>
                        <h2 class="detail-cover-title"><?= htmlspecialchars($book->getJudul()) ?></h2>
                        <p class="detail-cover-author"><?= htmlspecialchars($book->getPenulis()) ?></p>
                    </div>
                </div>

                <div class="stock-status-box">
                    <span class="stock-status-label">Ketersediaan Stok:</span>
                    <?php if ($book->getStok() > 0): ?>
                        <span class="badge-stock-in">Tersedia (<?= $book->getStok() ?> unit)</span>
                    <?php else: ?>
                        <span class="badge-stock-out">Stok Habis</span>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Right Column: Information & Rental Form -->
            <div class="detail-info">
                <div class="detail-header">
                    <span class="category-pill <?= $typeClass ?>"><?= htmlspecialchars($typeLabel) ?> Digital</span>
                    <h1 class="detail-title"><?= htmlspecialchars($book->getJudul()) ?></h1>
                    <p class="detail-author">Karya: <strong><?= htmlspecialchars($book->getPenulis()) ?></strong></p>
                </div>

                <div class="detail-meta-grid">
                    <?php foreach ($metaDetails as $label => $val): ?>
                        <div class="meta-box">
                            <span class="meta-label"><?= htmlspecialchars($label) ?></span>
                            <span class="meta-val"><?= htmlspecialchars((string) $val) ?></span>
                        </div>
                    <?php endforeach; ?>
                </div>

                <!-- Rental Box Calculator -->
                <div class="rental-calculator-card" 
                     id="rental-calculator"
                     data-book-id="<?= htmlspecialchars((string) $book->getId()) ?>"
                     data-base-price="<?= $book->getHargaDasar() ?>"
                     data-book-type="<?= $isNovel ? 'novel' : ($isKomik ? 'komik' : ($isEBook ? 'ebook' : 'default')) ?>"
                     data-pages="<?= $isNovel ? $book->getJumlahHalaman() : 0 ?>"
                     data-chapters="<?= $isKomik ? $book->getJumlahChapter() : 0 ?>"
                     data-is-color="<?= ($isKomik && $book->isBerwarna()) ? '1' : '0' ?>"
                     data-size-mb="<?= ($book instanceof \Bacain\Models\EBook) ? $book->getUkuranFile() : 0 ?>"
                >
                    <h3 class="calculator-title">Form Penyewaan Buku</h3>
                    <p class="calculator-desc">Pilih durasi sewa yang Anda inginkan. Total biaya akan dihitung secara transparan dan otomatis.</p>

                    <form action="?page=rent" method="POST" id="form-rental" class="rental-form">
                        <input type="hidden" name="book_id" value="<?= htmlspecialchars((string) $book->getId()) ?>">

                        <div class="form-group">
                            <label for="rent-days" class="form-label">Durasi Sewa (Hari):</label>
                            <div class="duration-controls">
                                <div class="duration-presets">
                                    <button type="button" class="btn-preset" data-days="1">1 Hari</button>
                                    <button type="button" class="btn-preset" data-days="3">3 Hari</button>
                                    <button type="button" class="btn-preset active" data-days="7">7 Hari</button>
                                    <button type="button" class="btn-preset" data-days="14">14 Hari</button>
                                    <button type="button" class="btn-preset" data-days="30">30 Hari</button>
                                </div>
                                <div class="duration-input-wrapper">
                                    <input 
                                        type="number" 
                                        id="rent-days" 
                                        name="days" 
                                        class="form-control duration-number" 
                                        value="<?= $defaultDays ?>" 
                                        min="1" 
                                        max="90" 
                                        required
                                    >
                                    <span class="input-suffix">Hari</span>
                                </div>
                            </div>
                        </div>

                        <!-- Calculation breakdown -->
                        <div class="price-breakdown">
                            <div class="breakdown-row">
                                <span>Tarif Dasar Harian:</span>
                                <span>Rp <?= number_format($book->getHargaDasar(), 0, ',', '.') ?> × <span id="calc-days-label"><?= $defaultDays ?></span> hari</span>
                            </div>
                            <div class="breakdown-row">
                                <span>Komponen Khusus (<?= htmlspecialchars($typeLabel) ?>):</span>
                                <span id="calc-special-label">Dihitung otomatis</span>
                            </div>
                            <div class="breakdown-row total-row">
                                <span class="total-label">Total Biaya Sewa:</span>
                                <span class="total-value" id="calc-total-display">Rp <?= number_format($estimatedInitialCost, 0, ',', '.') ?></span>
                            </div>
                        </div>

                        <div class="rental-action">
                            <?php if ($book->getStok() <= 0): ?>
                                <button type="button" class="btn btn-secondary btn-block btn-lg" disabled>
                                    Stok Habis (Tidak Dapat Disewa)
                                </button>
                            <?php elseif (empty($_SESSION['bacain_member_id'])): ?>
                                <a href="?page=login" class="btn btn-primary btn-block btn-lg">
                                    Masuk untuk Menyewa Buku
                                </a>
                                <p class="action-hint text-center">Belum punya akun? <a href="?page=register">Daftar sekarang</a></p>
                            <?php else: ?>
                                <button type="submit" class="btn btn-primary btn-block btn-lg" id="btn-submit-rent">
                                    Konfirmasi & Sewa Sekarang
                                </button>
                            <?php endif; ?>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>

<?php require_once __DIR__ . '/layout/footer.php'; ?>
