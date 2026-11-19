<?php

declare(strict_types=1);

namespace Bacain\Services;

use Bacain\Exceptions\BacainException;
use Bacain\Interfaces\Rentable;
use Bacain\Models\Rental;
use Bacain\Repositories\BookRepository;
use Bacain\Repositories\MemberRepository;
use Bacain\Repositories\RentalRepository;

class RentalService
{
    private BookRepository $bookRepository;
    private MemberRepository $memberRepository;
    private RentalRepository $rentalRepository;
    private int $maxActiveRentals;

    public function __construct(
        BookRepository $bookRepository,
        MemberRepository $memberRepository,
        RentalRepository $rentalRepository,
        int $maxActiveRentals = 3
    ) {
        $this->bookRepository = $bookRepository;
        $this->memberRepository = $memberRepository;
        $this->rentalRepository = $rentalRepository;
        $this->maxActiveRentals = $maxActiveRentals;
    }

    /**
     * Menghitung total biaya sewa berdasarkan durasi hari.
     */
    public function calculatePrice(string $bookId, int $days): float
    {
        if ($days <= 0) {
            throw new BacainException("Durasi sewa minimal 1 hari.");
        }

        $book = $this->bookRepository->findById($bookId);
        if ($book === null) {
            throw new BacainException("Buku tidak ditemukan.");
        }

        return $book instanceof Rentable
            ? $book->hitungHargaSewa($days)
            : $book->getHargaDasar() * $days;
    }

    /**
     * Membuat transaksi sewa baru setelah memvalidasi stok dan batas pinjam.
     */
    public function createRental(string $memberId, string $bookId, int $days): Rental
    {
        if ($days <= 0) {
            throw new BacainException("Durasi sewa minimal 1 hari.");
        }

        $member = $this->memberRepository->findById($memberId);
        if ($member === null) {
            throw new BacainException("Member tidak ditemukan.");
        }

        $book = $this->bookRepository->findById($bookId);
        if ($book === null) {
            throw new BacainException("Buku tidak ditemukan.");
        }

        // 1. Cek ketersediaan stok buku
        if ($book->getStok() <= 0) {
            throw new BacainException("Stok buku '{$book->getJudul()}' sedang habis.");
        }

        // 2. Cek batas maksimal sewa aktif member
        $activeRentals = $this->rentalRepository->findActiveByMemberId($memberId);
        $limit = method_exists($member, 'getBatasSewa')
            ? $member->getBatasSewa()
            : $this->maxActiveRentals;

        if (count($activeRentals) >= $limit) {
            throw new BacainException("Anda telah mencapai batas maksimal sewa aktif ({$limit} buku).");
        }

        // 3. Hitung harga sewa
        $totalPrice = $this->calculatePrice($bookId, $days);

        // 4. Kurangi stok buku dan simpan perubahan
        $book->sewa();
        $this->bookRepository->save($book);

        // 5. Buat dan simpan record Rental
        $rentalId = 'RNT-' . bin2hex(random_bytes(4));
        $rentalDate = date('Y-m-d');
        $dueDate = date('Y-m-d', strtotime("+{$days} days"));

        $rental = new Rental(
            $rentalId,
            $member,
            $book,
            $rentalDate,
            $dueDate,
            'active',
            $totalPrice
        );

        $this->rentalRepository->save($rental);

        return $rental;
    }

    /**
     * Memproses pengembalian buku dan mengembalikan stok buku.
     */
    public function returnRental(string $rentalId): Rental
    {
        $rental = $this->rentalRepository->findById($rentalId);
        if ($rental === null) {
            throw new BacainException("Data penyewaan tidak ditemukan.");
        }

        if (strtolower($rental->getStatus()) === 'returned' || strtolower($rental->getStatus()) === 'dikembalikan') {
            throw new BacainException("Buku pada transaksi ini sudah dikembalikan sebelumnya.");
        }

        // Tandai sewa selesai dan kembalikan stok melalui model domain.
        $rental->prosesKembalikan();
        $book = $rental->getBuku();
        $this->bookRepository->save($book);

        $this->rentalRepository->save($rental);

        return $rental;
    }

    /**
     * Mengambil riwayat penyewaan milik member tertentu.
     *
     * @return Rental[]
     */
    public function getMemberRentalHistory(string $memberId): array
    {
        return $this->rentalRepository->findByMemberId($memberId);
    }
}

