<?php

declare(strict_types=1);
?>
    </main>

    <footer class="site-footer">
        <div class="footer-top container">
            <div class="footer-brand">
                <a href="?page=home" class="brand-logo footer-logo">
                    <span class="logo-icon">📖</span>
                    <span class="logo-text">Bacain<span class="logo-dot">.</span></span>
                </a>
                <p class="footer-tagline">
                    Platform perpustakaan digital interaktif untuk eksplorasi dan rental Novel, Komik, serta E-Book pilihan dengan tarif fleksibel dan akses mudah.
                </p>
                <div class="footer-badges">
                    <span class="badge-pill">Novel</span>
                    <span class="badge-pill">Komik</span>
                    <span class="badge-pill">E-Book</span>
                </div>
            </div>

            <div class="footer-col">
                <h4 class="footer-heading">Navigasi Cepat</h4>
                <ul class="footer-links">
                    <li><a href="?page=home">Beranda</a></li>
                    <li><a href="?page=catalog">Katalog Lengkap</a></li>
                    <li><a href="?page=catalog&category=novel">Kategori Novel</a></li>
                    <li><a href="?page=catalog&category=komik">Kategori Komik</a></li>
                    <li><a href="?page=catalog&category=ebook">Kategori E-Book</a></li>
                </ul>
            </div>

            <div class="footer-col">
                <h4 class="footer-heading">Keanggotaan</h4>
                <ul class="footer-links">
                    <?php if (!empty($_SESSION['bacain_member_id'])): ?>
                        <li><a href="?page=my-rentals">Sewa Saya</a></li>
                        <li><a href="?page=logout">Keluar Akun</a></li>
                    <?php else: ?>
                        <li><a href="?page=login">Masuk Member</a></li>
                        <li><a href="?page=register">Daftar Akun Baru</a></li>
                    <?php endif; ?>
                    <li><a href="?page=home#fitur">Aturan & Tarif Sewa</a></li>
                </ul>
            </div>

            <div class="footer-col">
                <h4 class="footer-heading">Teknologi & Tim</h4>
                <p class="footer-text">
                    Dibangun dengan arsitektur PHP 8 OOP (Penerapan Abstraction, Inheritance, Polymorphism, & Encapsulation) untuk tugas UTS Pemrograman Web.
                </p>
                <p class="footer-text team-badge">
                    Kelompok: <strong>Bimo, Farhan, Rofi, Syakhish</strong>
                </p>
            </div>
        </div>

        <div class="footer-bottom">
            <div class="container footer-bottom-inner">
                <p>&copy; <?= date('Y') ?> <strong>Bacain</strong>. Hak Cipta Dilindungi Undang-Undang.</p>
                <p class="footer-credit">Didesain & Dikembangkan oleh <strong>Syakhish</strong> (Front-end)</p>
            </div>
        </div>
    </footer>

    <script src="js/validation.js"></script>
    <script src="js/app.js"></script>
</body>
</html>
