<?php

declare(strict_types=1);

namespace Bacain\Repositories;

use Bacain\Models\Rental;
use Bacain\Exceptions\BacainException;

class RentalRepository extends JsonRepository
{
    private ?MemberRepository $memberRepository;
    private ?BookRepository $bookRepository;

    public function __construct(
        string $filePath = __DIR__ . '/../../data/rentals.json',
        ?MemberRepository $memberRepository = null,
        ?BookRepository $bookRepository = null
    )
    {
        parent::__construct($filePath);
        $this->memberRepository = $memberRepository ?? new MemberRepository();
        $this->bookRepository = $bookRepository ?? new BookRepository();
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
        foreach ($this->findAll() as $rental) {
            if ((string) $rental->getId() === $id) {
                return $rental;
            }
        }
        return null;
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
            fn(Rental $rental): bool => (string) $rental->getMemberId() === (string) $memberId
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
                || strtolower($rental->getStatus()) === 'terlambat'
        ));
    }

    /**
     * @param array<string, mixed> $data
     */
    protected function mapToEntity(array $data): Rental
    {
        $memberId = (string) ($data['memberId'] ?? $data['member_id'] ?? '');
        $bookId = (string) ($data['bookId'] ?? $data['book_id'] ?? '');

        $member = $this->memberRepository?->findById($memberId);
        $book = $this->bookRepository?->findById($bookId);

        if ($member === null || $book === null) {
            throw new BacainException("Data member atau buku pada transaksi '{$data['id']}' tidak ditemukan.");
        }

        $rentalDate = $data['tanggalSewa'] ?? $data['tanggal_sewa'] ?? $data['rentalDate'] ?? date('Y-m-d');
        $dueDate = $data['tanggalJatuhTempo'] ?? $data['tanggal_jatuh_tempo'] ?? $data['dueDate'] ?? date('Y-m-d', strtotime('+7 days'));
        $status = $data['status'] ?? 'active';
        $totalPrice = (float) ($data['totalBiaya'] ?? $data['harga_total'] ?? $data['totalPrice'] ?? 0);

        return new Rental(
            $data['id'],
            $member,
            $book,
            $rentalDate,
            $dueDate,
            $status,
            $totalPrice
        );
    }
}

