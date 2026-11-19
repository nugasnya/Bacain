<?php

declare(strict_types=1);

$pageTitle = 'Masuk Member';
require_once __DIR__ . '/layout/header.php';
?>

<div class="auth-page container">
    <div class="auth-card">
        <div class="auth-header">
            <div class="auth-icon-badge">🔐</div>
            <h1 class="auth-title">Masuk ke Bacain</h1>
            <p class="auth-subtitle">Akses akun member Anda untuk mulai menyewa buku pilihan.</p>
        </div>

        <?php if (!empty($error)): ?>
            <div class="alert alert-danger" role="alert">
                <span class="alert-icon">⚠️</span>
                <span class="alert-text"><?= htmlspecialchars($error) ?></span>
            </div>
        <?php endif; ?>

        <form action="?page=login" method="POST" id="form-login" class="auth-form" novalidate>
            <div class="form-group">
                <label for="login-email" class="form-label">Alamat Email</label>
                <div class="input-with-icon">
                    <span class="input-icon">✉️</span>
                    <input 
                        type="email" 
                        name="email" 
                        id="login-email" 
                        class="form-control" 
                        placeholder="nama@email.com" 
                        required
                        autocomplete="email"
                    >
                </div>
                <div class="form-error-msg" id="login-email-error"></div>
            </div>

            <div class="form-group">
                <div class="flex-between">
                    <label for="login-password" class="form-label">Kata Sandi (Password)</label>
                </div>
                <div class="input-with-icon">
                    <span class="input-icon">🔒</span>
                    <input 
                        type="password" 
                        name="password" 
                        id="login-password" 
                        class="form-control" 
                        placeholder="Masukkan kata sandi..." 
                        required
                        autocomplete="current-password"
                    >
                    <button type="button" class="btn-toggle-password" id="toggle-password" title="Lihat password">👁️</button>
                </div>
                <div class="form-error-msg" id="login-password-error"></div>
            </div>

            <button type="submit" class="btn btn-primary btn-block btn-lg" id="btn-submit-login">
                Masuk Sekarang
            </button>
        </form>

        <div class="auth-footer">
            <p>Belum memiliki akun member? <a href="?page=register" class="link-bold">Daftar Akun Baru</a></p>
        </div>

        <div class="auth-demo-hint">
            <strong>💡 Akun Pengujian:</strong>
            <p>Email: <code>syakish@bacain.test</code> atau <code>rofi@bacain.test</code></p>
            <p>Password default demo: <code>password</code> atau <code>123456</code></p>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/layout/footer.php'; ?>
