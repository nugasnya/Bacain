<?php

declare(strict_types=1);

namespace Bacain\Repositories;

use Bacain\Exceptions\BacainException;
use Bacain\Models\EBook;
use Bacain\Models\Komik;
use Bacain\Models\Media;
use Bacain\Models\Novel;

class BookRepository extends JsonRepository
{
    public function __construct(string $filePath = __DIR__ . '/../../data/books.json')
    {
        parent::__construct($filePath);
    }

    /**
     * @return Media[]
     */
    public function findAll(): array
    {
        $records = $this->readData();
        return array_map(fn(array $row): Media => $this->mapToEntity($row), $records);
    }

    public function findById(string $id): ?Media
    {
        foreach ($this->findAll() as $book) {
            if ((string) $book->getId() === $id) {
                return $book;
            }
        }
        return null;
    }

    /**
     * Mengubah array dari JSON menjadi instance EBook, Novel, atau Komik.
     *
     * @param array<string, mixed> $data
     */
    protected function mapToEntity(array $data): Media
    {
        $type = strtolower((string) ($data['type'] ?? $data['tipe'] ?? $data['kategori'] ?? ''));

        return match ($type) {
            'ebook' => EBook::fromArray($data),
            'novel' => Novel::fromArray($data),
            'komik' => Komik::fromArray($data),
            default => throw new BacainException("Tipe buku tidak dikenali: {$type}"),
        };
    }
}

