<?php

declare(strict_types=1);

namespace Bacain\Services;

use Bacain\Models\EBook;
use Bacain\Models\Komik;
use Bacain\Models\Media;
use Bacain\Models\Novel;
use Bacain\Repositories\BookRepository;

class CatalogService
{
    private BookRepository $bookRepository;

    public function __construct(BookRepository $bookRepository)
    {
        $this->bookRepository = $bookRepository;
    }

    /**
     * Mengambil seluruh daftar buku di katalog.
     *
     * @return Media[]
     */
    public function getAllBooks(): array
    {
        return $this->bookRepository->findAll();
    }

    /**
     * Mengambil detail satu buku berdasarkan ID.
     */
    public function getBookById(string $id): ?Media
    {
        return $this->bookRepository->findById($id);
    }

    /**
     * Mencari buku berdasarkan kecocokan judul atau penulis.
     *
     * @return Media[]
     */
    public function searchBooks(string $keyword): array
    {
        $keyword = strtolower(trim($keyword));
        if ($keyword === '') {
            return $this->getAllBooks();
        }

        return array_values(array_filter(
            $this->getAllBooks(),
            function (Media $book) use ($keyword): bool {
                $titleMatch = str_contains(strtolower($book->getTitle()), $keyword);
                $authorMatch = str_contains(strtolower($book->getAuthor()), $keyword);
                return $titleMatch || $authorMatch;
            }
        ));
    }

    /**
     * Memfilter buku berdasarkan kategori (ebook, novel, komik).
     *
     * @return Media[]
     */
    public function filterByCategory(string $category): array
    {
        $normalized = strtolower(trim($category));
        if ($normalized === '' || $normalized === 'all' || $normalized === 'semua') {
            return $this->getAllBooks();
        }

        return array_values(array_filter(
            $this->getAllBooks(),
            function (Media $book) use ($normalized): bool {
                return match ($normalized) {
                    'ebook' => $book instanceof EBook,
                    'novel' => $book instanceof Novel,
                    'komik' => $book instanceof Komik,
                    default => false,
                };
            }
        ));
    }
}

