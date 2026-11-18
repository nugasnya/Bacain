<?php

declare(strict_types=1);

namespace Bacain\Repositories;

use Bacain\Models\Rental;

class RentalRepository extends JsonRepository
{
    public function __construct(string $filePath = __DIR__ . '/../../data/rentals.json')
    {
        parent::__construct($filePath);
    }

    /**
     * @return Rental[]
     */
    public function findAll(): array
    {
        $records = $this->readData();
        return array_map(fn(array $row): Rental => $this->mapToEntity($row), $records);
    }

    public function findById(string $id): ?Rental
    {
        $item = parent::findById($id);
        return $item instanceof Rental ? $item : null;
    }

    /**
     * Mengambil semua riwayat sewa milik member tertentu.
     *
     * @return Rental[]
     */
    public function findByMemberId(string $memberId): array
    {
        return array_values(array_filter(
            $this->findAll(),
            fn(Rental $rental): bool => $rental->getMemberId() === $memberId
        ));
    }

    /**
     * Mengambil daftar sewa yang masih aktif (belum dikembalikan) milik member tertentu.
     *
     * @return Rental[]
     */
    public function findActiveByMemberId(string $memberId): array
    {
        return array_values(array_filter(
            $this->findByMemberId($memberId),
            fn(Rental $rental): bool => strtolower($rental->getStatus()) === 'active'
                || strtolower($rental->getStatus()) === 'dipinjam'
        ));
    }

    /**
     * @param array<string, mixed> $data
     */
    protected function mapToEntity(array $data): Rental
    {
        if (method_exists(Rental::class, 'fromArray')) {
            return Rental::fromArray($data);
        }

        return new Rental(
            $data['id'],
            $data['memberId'],
            $data['bookId'],
            $data['rentalDate'] ?? date('Y-m-d'),
            $data['dueDate'] ?? date('Y-m-d', strtotime('+7 days')),
            (float) ($data['totalPrice'] ?? 0),
            $data['status'] ?? 'active',
            $data['returnDate'] ?? null
        );
    }
}

