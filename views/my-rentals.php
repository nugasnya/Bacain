<?php

declare(strict_types=1);

$pageTitle = 'Sewa Saya';
require_once __DIR__ . '/layout/header.php';

$activeRentals = array_values(array_filter($rentals, function (\Bacain\Models\Rental $r): bool {
    return $r->getStatus() === 'Aktif' || (method_exists($r, 'isAktif') && $r->isAktif());
}));

$historyRentals = array_values(array_filter($rentals, function (\Bacain\Models\Rental $r): bool {
    return $r->getStatus() !== 'Aktif' && (!method_exists($r, 'isAktif') || !$r->isAktif());
}));

$batasSewa = $member->getBatasSewa();
$jumlahAktif = count($activeRentals);
$sisaKuota = max(0, $batasSewa - $jumlahAktif);
?>

<div class="rentals-page container">
    <!-- Member Header Profile Card -->
    <div class="member-profile-card">
        <div class="profile-info-wrap">
            <div class="profile-avatar-large">
                <?= htmlspecialchars(strtoupper(substr($member->getNama(), 0, 1))) ?>
            </div>
            <div class="profile-details">
                <div class="profile-name-row">
                    <h1 class="profile-name"><?= htmlspecialchars($member->getNama()) ?></h1>
                    <span class="badge-tier tier-<?= strtolower($member->getStatusKeanggotaan()) ?>">
                        <?= htmlspecialchars(ucfirst($member->getStatusKeanggotaan())) ?> Member
                    </span>
                </div>
                <p class="profile-email">✉️ <?= htmlspecialchars($member->getEmail()) ?></p>
            </div>
        </div>

        <div class="quota-stats">
            <div class="quota-box">
                <span class="quota-label">Sewa Aktif</span>
                <span class="quota-val <?= $jumlahAktif >= $batasSewa ? 'text-danger' : 'text-primary' ?>"><?= $jumlahAktif ?></span>
            </div>
            <div class="quota-divider"></div>
            <div class="quota-box">
                <span class="quota-label">Batas Kuota</span>
                <span class="quota-val"><?= $batasSewa ?> Buku</span>
            </div>
            <div class="quota-divider"></div>
            <div class="quota-box">
                <span class="quota-label">Sisa Kuota</span>
                <span class="quota-val <?= $sisaKuota === 0 ? 'text-danger' : 'text-success' ?>"><?= $sisaKuota ?></span>
            </div>
        </div>
    </div>

    <!-- Active Rentals Section -->
    <section class="rentals-section">
        <div class="section-header flex-between">
            <div>
                <h2 class="section-title">Buku Sedang Disewa (Aktif)</h2>
                <p class="section-subtitle">Daftar buku yang saat ini sedang Anda pinjam dan masa aktif sewanya.</p>
            </div>
            <a href="?page=catalog" class="btn btn-primary btn-sm">+ Sewa Buku Baru</a>
        </div>

        <?php if (empty($activeRentals)): ?>
            <div class="empty-state-card">
                <div class="empty-icon">📖</div>
                <h3>Tidak Ada Sewa Aktif</h3>
                <p>Saat ini Anda tidak memiliki buku yang sedang disewa. Yuk pilih buku menarik dari katalog!</p>
                <a href="?page=catalog" class="btn btn-outline">Jelajahi Katalog</a>
            </div>
        <?php else: ?>
            <div class="rental-grid">
                <?php foreach ($activeRentals as $rental): ?>
                    <?php
                    $book = $rental->getBuku();
                    $isLate = method_exists($rental, 'isTerlambat') ? $rental->isTerlambat() : false;
                    $today = new DateTime();
                    $dueDate = new DateTime($rental->getTanggalJatuhTempo());
                    $interval = $today->diff($dueDate);
                    $daysRemaining = (int) $interval->format('%r%a');

                    $isNovel = $book instanceof \Bacain\Models\Novel;
                    $isKomik = $book instanceof \Bacain\Models\Komik;
                    $typeClass = $isNovel ? 'type-novel' : ($isKomik ? 'type-komik' : 'type-ebook');
                    $typeLabel = $isNovel ? 'Novel' : ($isKomik ? 'Komik' : 'E-Book');
                    ?>
                    <article class="rental-card <?= $isLate ? 'rental-card-overdue' : '' ?>">
                        <div class="rental-card-header">
                            <span class="book-type-badge <?= $typeClass ?>"><?= htmlspecialchars($typeLabel) ?></span>
                            <?php if ($isLate || $daysRemaining < 0): ?>
                                <span class="badge-status-overdue">Terlambat</span>
                            <?php elseif ($daysRemaining <= 2): ?>
                                <span class="badge-status-warning">Sisa <?= $daysRemaining ?> Hari</span>
                            <?php else: ?>
                                <span class="badge-status-active">Aktif (Sisa <?= $daysRemaining ?> Hari)</span>
                            <?php endif; ?>
                        </div>

                        <div class="rental-card-body">
                            <h3 class="rental-book-title">
                                <a href="?page=book-detail&id=<?= urlencode((string) $book->getId()) ?>">
                                    <?= htmlspecialchars($book->getJudul()) ?>
                                </a>
                            </h3>
                            <p class="rental-book-author">Penulis: <?= htmlspecialchars($book->getPenulis()) ?></p>

                            <div class="rental-dates">
                                <div class="date-item">
                                    <span class="date-label">Tgl Sewa:</span>
                                    <span class="date-val"><?= htmlspecialchars($rental->getTanggalSewa()) ?></span>
                                </div>
                                <div class="date-item">
                                    <span class="date-label">Jatuh Tempo:</span>
                                    <span class="date-val <?= ($isLate || $daysRemaining < 0) ? 'text-danger fw-bold' : '' ?>">
                                        <?= htmlspecialchars($rental->getTanggalJatuhTempo()) ?>
                                    </span>
                                </div>
                            </div>

                            <div class="rental-cost-row">
                                <span class="cost-label">Total Biaya Sewa:</span>
                                <span class="cost-val">Rp <?= number_format($rental->getTotalBiaya(), 0, ',', '.') ?></span>
                            </div>
                        </div>

                        <div class="rental-card-footer">
                            <form action="?page=return" method="POST" class="form-return-rental" onsubmit="return confirm('Apakah Anda yakin ingin mengembalikan buku <?= htmlspecialchars(addslashes($book->getJudul())) ?>?');">
                                <input type="hidden" name="rental_id" value="<?= htmlspecialchars((string) $rental->getId()) ?>">
                                <button type="submit" class="btn btn-outline-primary btn-block btn-return">
                                    Kembalikan Buku
                                </button>
                            </form>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </section>

    <!-- Rental History Section -->
    <section class="history-section">
        <h2 class="section-title">Riwayat Pengembalian</h2>
        <p class="section-subtitle">Daftar buku yang telah selesai Anda sewa sebelumnya.</p>

        <?php if (empty($historyRentals)): ?>
            <p class="text-muted empty-history">Belum ada riwayat buku yang dikembalikan.</p>
        <?php else: ?>
            <div class="history-table-wrapper">
                <table class="history-table">
                    <thead>
                        <tr>
                            <th>ID Sewa</th>
                            <th>Judul Buku</th>
                            <th>Tanggal Pinjam</th>
                            <th>Jatuh Tempo</th>
                            <th>Total Biaya</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($historyRentals as $hRental): ?>
                            <tr>
                                <td><code><?= htmlspecialchars((string) $hRental->getId()) ?></code></td>
                                <td class="fw-bold"><?= htmlspecialchars($hRental->getBuku()->getJudul()) ?></td>
                                <td><?= htmlspecialchars($hRental->getTanggalSewa()) ?></td>
                                <td><?= htmlspecialchars($hRental->getTanggalJatuhTempo()) ?></td>
                                <td>Rp <?= number_format($hRental->getTotalBiaya(), 0, ',', '.') ?></td>
                                <td>
                                    <span class="badge-status-completed">Selesai Dikembalikan</span>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </section>
</div>

<?php require_once __DIR__ . '/layout/footer.php'; ?>
