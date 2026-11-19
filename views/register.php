<?php

declare(strict_types=1);

$pageTitle = 'Daftar Member Baru';
require_once __DIR__ . '/layout/header.php';
?>

<div class="auth-page container">
    <div class="auth-card">
        <div class="auth-header">
            <div class="auth-icon-badge">✨</div>
            <h1 class="auth-title">Daftar Akun Member</h1>
            <p class="auth-subtitle">Bergabunglah dan nikmati pinjaman buku digital hingga 3 buku secara serentak.</p>
        </div>

        <?php if (!empty($error)): ?>
            <div class="alert alert-danger" role="alert">
                <span class="alert-icon">⚠️</span>
                <span class="alert-text"><?= htmlspecialchars($error) ?></span>
            </div>
        <?php endif; ?>

        <form action="?page=register" method="POST" id="form-register" class="auth-form" novalidate>
            <div class="form-group">
                <label for="reg-name" class="form-label">Nama Lengkap</label>
                <div class="input-with-icon">
                    <span class="input-icon">👤</span>
                    <input 
                        type="text" 
                        name="name" 
                        id="reg-name" 
                        class="form-control" 
                        placeholder="Contoh: Muhammad Syakhish" 
                        required
                        autocomplete="name"
                    >
                </div>
                <div class="form-error-msg" id="reg-name-error"></div>
            </div>

            <div class="form-group">
                <label for="reg-email" class="form-label">Alamat Email</label>
                <div class="input-with-icon">
                    <span class="input-icon">✉️</span>
                    <input 
                        type="email" 
                        name="email" 
                        id="reg-email" 
                        class="form-control" 
                        placeholder="nama@email.com" 
                        required
                        autocomplete="email"
                    >
                </div>
                <div class="form-error-msg" id="reg-email-error"></div>
            </div>

            <div class="form-group">
                <label for="reg-password" class="form-label">Kata Sandi (Password)</label>
                <div class="input-with-icon">
                    <span class="input-icon">🔒</span>
                    <input 
                        type="password" 
                        name="password" 
                        id="reg-password" 
                        class="form-control" 
                        placeholder="Minimal 6 karakter..." 
                        required
                        minlength="6"
                        autocomplete="new-password"
                    >
                    <button type="button" class="btn-toggle-password" id="toggle-reg-password" title="Lihat password">👁️</button>
                </div>
                <small class="form-hint">Minimal terdiri dari 6 karakter.</small>
                <div class="form-error-msg" id="reg-password-error"></div>
            </div>

            <div class="form-group">
                <label for="reg-confirm-password" class="form-label">Konfirmasi Kata Sandi</label>
                <div class="input-with-icon">
                    <span class="input-icon">🔒</span>
                    <input 
                        type="password" 
                        id="reg-confirm-password" 
                        class="form-control" 
                        placeholder="Ulangi kata sandi di atas..." 
                        required
                        autocomplete="new-password"
                    >
                </div>
                <div class="form-error-msg" id="reg-confirm-password-error"></div>
            </div>

            <button type="submit" class="btn btn-primary btn-block btn-lg" id="btn-submit-register">
                Daftar Jadi Member
            </button>
        </form>

        <div class="auth-footer">
            <p>Sudah memiliki akun? <a href="?page=login" class="link-bold">Masuk di sini</a></p>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/layout/footer.php'; ?>
