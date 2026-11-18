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
        $item = parent::findById($id);
        return $item instanceof Media ? $item : null;
    }

    /**
     * Mengubah array dari JSON menjadi instance EBook, Novel, atau Komik.
     *
     * @param array<string, mixed> $data
     */
    protected function mapToEntity(array $data): Media
    {
        $type = strtolower((string) ($data['type'] ?? $data['kategori'] ?? ''));

        return match ($type) {
            'ebook' => method_exists(EBook::class, 'fromArray')
                ? EBook::fromArray($data)
                : new EBook(
                    $data['id'],
                    $data['title'] ?? $data['judul'] ?? '',
                    $data['author'] ?? $data['penulis'] ?? '',
                    (float) ($data['rentalPrice'] ?? $data['hargaSewa'] ?? 0),
                    (int) ($data['stock'] ?? $data['stok'] ?? 0),
                    $data['fileFormat'] ?? 'PDF',
                    (float) ($data['fileSizeMb'] ?? 0)
                ),
            'novel' => method_exists(Novel::class, 'fromArray')
                ? Novel::fromArray($data)
                : new Novel(
                    $data['id'],
                    $data['title'] ?? $data['judul'] ?? '',
                    $data['author'] ?? $data['penulis'] ?? '',
                    (float) ($data['rentalPrice'] ?? $data['hargaSewa'] ?? 0),
                    (int) ($data['stock'] ?? $data['stok'] ?? 0),
                    $data['genre'] ?? 'Umum',
                    (int) ($data['pages'] ?? $data['halaman'] ?? 0)
                ),
            'komik' => method_exists(Komik::class, 'fromArray')
                ? Komik::fromArray($data)
                : new Komik(
                    $data['id'],
                    $data['title'] ?? $data['judul'] ?? '',
                    $data['author'] ?? $data['penulis'] ?? '',
                    (float) ($data['rentalPrice'] ?? $data['hargaSewa'] ?? 0),
                    (int) ($data['stock'] ?? $data['stok'] ?? 0),
                    (int) ($data['volume'] ?? 1),
                    $data['illustrator'] ?? ''
                ),
            default => throw new BacainException("Tipe buku tidak dikenali: {$type}"),
        };
    }
}

