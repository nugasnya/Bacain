<?php

declare(strict_types=1);

namespace Bacain\Controllers;

use Bacain\Services\CatalogService;

class CatalogController
{
    public function __construct(private CatalogService $catalogService)
    {
    }

    public function home(): void
    {
        $books = $this->catalogService->getAllBooks();
        $this->render('home', [
            'books' => $books,
            'popularBooks' => array_slice($books, 0, 4),
        ]);
    }

    public function catalog(): void
    {
        $keyword = trim((string) ($_GET['q'] ?? ''));
        $category = trim((string) ($_GET['category'] ?? 'all'));

        $books = $keyword !== ''
            ? $this->catalogService->searchBooks($keyword)
            : $this->catalogService->filterByCategory($category);

        $this->render('catalog', [
            'books' => $books,
            'keyword' => $keyword,
            'category' => $category,
        ]);
    }

    public function detail(): void
    {
        $bookId = (string) ($_GET['id'] ?? '');
        $book = $bookId !== '' ? $this->catalogService->getBookById($bookId) : null;

        if ($book === null) {
            http_response_code(404);
            $this->render('book-detail', ['book' => null]);
            return;
        }

        $this->render('book-detail', ['book' => $book]);
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
