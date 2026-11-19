<?php

declare(strict_types=1);

namespace Bacain\Repositories;

use Bacain\Exceptions\BacainException;

abstract class JsonRepository
{
    protected string $filePath;

    public function __construct(string $filePath)
    {
        $this->filePath = $filePath;
        $this->ensureFileExists();
    }

    private function ensureFileExists(): void
    {
        $directory = dirname($this->filePath);
        if (!is_dir($directory)) {
            mkdir($directory, 0777, true);
        }

        if (!file_exists($this->filePath)) {
            file_put_contents($this->filePath, json_encode([], JSON_PRETTY_PRINT));
        }
    }

    /**
     * Membaca seluruh data mentah dari file JSON.
     *
     * @return array<int, array<string, mixed>>
     */
    protected function readData(): array
    {
        $content = file_get_contents($this->filePath);
        if ($content === false || trim($content) === '') {
            return [];
        }

        $decoded = json_decode($content, true);
        if (!is_array($decoded)) {
            throw new BacainException("Format JSON tidak valid pada file: {$this->filePath}");
        }

        return $decoded;
    }

    /**
     * Menyimpan seluruh array data ke file JSON.
     *
     * @param array<int, array<string, mixed>> $data
     */
    protected function writeData(array $data): void
    {
        $encoded = json_encode(array_values($data), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        if ($encoded === false) {
            throw new BacainException("Gagal mengenkode data ke format JSON.");
        }

        file_put_contents($this->filePath, $encoded, LOCK_EX);
    }

    /**
     * Mengambil semua item (dapat di-override oleh class turunan untuk mapping ke object).
     *
     * @return array<int, mixed>
     */
    public function findAll(): array
    {
        return $this->readData();
    }

    /**
     * Mencari item berdasarkan ID.
     *
     * @return mixed|null
     */
    public function findById(string $id)
    {
        foreach ($this->findAll() as $item) {
            $itemId = is_object($item) && method_exists($item, 'getId')
                ? $item->getId()
                : ($item['id'] ?? null);

            if ($itemId !== null && (string) $itemId === $id) {
                return $item;
            }
        }

        return null;
    }

    /**
     * Menyimpan (insert atau update) satu entitas ke file JSON.
     *
     * @param mixed $entity
     */
    public function save($entity): void
    {
        $records = $this->readData();
        $dataArray = is_object($entity) && method_exists($entity, 'toArray')
            ? $entity->toArray()
            : (array) $entity;

        $id = $dataArray['id'] ?? null;
        if ($id === null) {
            throw new BacainException("Entitas harus memiliki atribut 'id' untuk disimpan.");
        }

        $updated = false;
        foreach ($records as $index => $record) {
            if (isset($record['id']) && (string) $record['id'] === (string) $id) {
                $records[$index] = $dataArray;
                $updated = true;
                break;
            }
        }

        if (!$updated) {
            $records[] = $dataArray;
        }

        $this->writeData($records);
    }

    /**
     * Menghapus entitas berdasarkan ID.
     */
    public function delete(string $id): bool
    {
        $records = $this->readData();
        $initialCount = count($records);

        $filtered = array_filter(
            $records,
            fn(array $record): bool =>
                !isset($record['id']) || (string) $record['id'] !== (string) $id
        );

        if (count($filtered) === $initialCount) {
            return false;
        }

        $this->writeData($filtered);
        return true;
    }
}

