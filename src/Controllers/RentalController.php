<?php

declare(strict_types=1);

namespace Bacain\Controllers;

use Bacain\Exceptions\BacainException;
use Bacain\Services\AuthService;
use Bacain\Services\RentalService;

class RentalController
{
    public function __construct(
        private RentalService $rentalService,
        private AuthService $authService
    ) {
    }

    public function myRentals(): void
    {
        $member = $this->authService->getCurrentUser();
        if ($member === null) {
            $_SESSION['flash_error'] = 'Silakan masuk untuk melihat sewa.';
            header('Location: ?page=login');
            exit;
        }

        $rentals = $this->rentalService->getMemberRentalHistory(
            (string) $member->getId()
        );

        extract(['rentals' => $rentals, 'member' => $member]);
        $viewFile = __DIR__ . '/../../views/my-rentals.php';
        if (is_file($viewFile)) {
            require $viewFile;
            return;
        }
        http_response_code(404);
        echo 'View belum tersedia: my-rentals';
    }

    public function rent(): void
    {
        $member = $this->authService->getCurrentUser();
        if ($member === null) {
            $_SESSION['flash_error'] = 'Silakan masuk sebelum menyewa.';
            header('Location: ?page=login');
            exit;
        }

        try {
            $this->rentalService->createRental(
                (string) $member->getId(),
                (string) ($_POST['book_id'] ?? $_GET['book_id'] ?? ''),
                (int) ($_POST['days'] ?? $_GET['days'] ?? 7)
            );
            $_SESSION['flash_success'] = 'Buku berhasil disewa.';
            header('Location: ?page=my-rentals');
        } catch (BacainException $exception) {
            $_SESSION['flash_error'] = $exception->getMessage();
            header('Location: ?page=catalog');
        }
        exit;
    }

    public function returnRental(): void
    {
        $member = $this->authService->getCurrentUser();
        if ($member === null) {
            $_SESSION['flash_error'] = 'Silakan masuk sebelum mengembalikan buku.';
            header('Location: ?page=login');
            exit;
        }

        try {
            $this->rentalService->returnRental(
                (string) ($_POST['rental_id'] ?? $_GET['rental_id'] ?? '')
            );
            $_SESSION['flash_success'] = 'Buku berhasil dikembalikan.';
        } catch (BacainException $exception) {
            $_SESSION['flash_error'] = $exception->getMessage();
        }

        header('Location: ?page=my-rentals');
        exit;
    }
}
