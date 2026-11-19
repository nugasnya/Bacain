<?php

declare(strict_types=1);

namespace Bacain\Controllers;

use Bacain\Exceptions\BacainException;
use Bacain\Services\AuthService;

class AuthController
{
    public function __construct(private AuthService $authService)
    {
    }

    public function showLogin(): void
    {
        $this->render('login', [
            'error' => $_SESSION['flash_error'] ?? null,
        ]);
        unset($_SESSION['flash_error'], $_SESSION['flash_success']);
    }

    public function showRegister(): void
    {
        $this->render('register', [
            'error' => $_SESSION['flash_error'] ?? null,
        ]);
        unset($_SESSION['flash_error'], $_SESSION['flash_success']);
    }

    public function login(): void
    {
        try {
            $this->authService->login(
                (string) ($_POST['email'] ?? ''),
                (string) ($_POST['password'] ?? '')
            );
            $_SESSION['flash_success'] = 'Berhasil masuk.';
            header('Location: ?page=catalog');
        } catch (BacainException $exception) {
            $_SESSION['flash_error'] = $exception->getMessage();
            header('Location: ?page=login');
        }
        exit;
    }

    public function register(): void
    {
        try {
            $this->authService->register(
                (string) ($_POST['name'] ?? ''),
                (string) ($_POST['email'] ?? ''),
                (string) ($_POST['password'] ?? '')
            );
            $_SESSION['flash_success'] = 'Pendaftaran berhasil. Silakan masuk.';
            header('Location: ?page=login');
        } catch (BacainException $exception) {
            $_SESSION['flash_error'] = $exception->getMessage();
            header('Location: ?page=register');
        }
        exit;
    }

    public function logout(): void
    {
        $this->authService->logout();
        $_SESSION['flash_success'] = 'Anda telah keluar.';
        header('Location: ?page=home');
        exit;
    }

    private function render(string $view, array $data = []): void
    {
        extract($data);
        $viewFile = __DIR__ . '/../../views/' . $view . '.php';
        if (is_file($viewFile)) {
            require $viewFile;
            return;
        }
        http_response_code(404);
        echo 'View belum tersedia: ' . htmlspecialchars($view);
    }
}
