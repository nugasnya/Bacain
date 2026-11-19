<?php

declare(strict_types=1);

require_once __DIR__ . '/../src/autoload.php';

use Bacain\Controllers\AuthController;
use Bacain\Controllers\CatalogController;
use Bacain\Controllers\RentalController;
use Bacain\Repositories\BookRepository;
use Bacain\Repositories\MemberRepository;
use Bacain\Repositories\RentalRepository;
use Bacain\Services\AuthService;
use Bacain\Services\CatalogService;
use Bacain\Services\RentalService;

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$bookRepository = new BookRepository();
$memberRepository = new MemberRepository();
$rentalRepository = new RentalRepository(
    __DIR__ . '/../data/rentals.json',
    $memberRepository,
    $bookRepository
);

$authService = new AuthService($memberRepository);
$catalogService = new CatalogService($bookRepository);
$rentalService = new RentalService(
    $bookRepository,
    $memberRepository,
    $rentalRepository
);

$authController = new AuthController($authService);
$catalogController = new CatalogController($catalogService);
$rentalController = new RentalController($rentalService, $authService);

$page = (string) ($_GET['page'] ?? 'home');
$method = strtoupper($_SERVER['REQUEST_METHOD'] ?? 'GET');

try {
    if ($method === 'POST') {
        match ($page) {
            'login' => $authController->login(),
            'register' => $authController->register(),
            'rent' => $rentalController->rent(),
            'return' => $rentalController->returnRental(),
            default => throw new \Bacain\Exceptions\BacainException('Aksi tidak dikenali.'),
        };
    } else {
        match ($page) {
            'home' => $catalogController->home(),
            'catalog' => $catalogController->catalog(),
            'book-detail', 'detail' => $catalogController->detail(),
            'login' => $authController->showLogin(),
            'register' => $authController->showRegister(),
            'logout' => $authController->logout(),
            'my-rentals', 'rentals' => $rentalController->myRentals(),
            default => (function (): void {
                http_response_code(404);
                echo 'Halaman tidak ditemukan.';
            })(),
        };
    }
} catch (\Bacain\Exceptions\BacainException $exception) {
    $_SESSION['flash_error'] = $exception->getMessage();
    header('Location: ?page=home');
    exit;
}
