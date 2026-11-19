<?php

declare(strict_types=1);

namespace Bacain\Models;

use Bacain\Exceptions\BacainException;

/**
 * Class Member
 * Turunan dari User yang merepresentasikan anggota perpustakaan digital Bacain.
 * Menambahkan atribut status keanggotaan, batas kuota sewa, dan daftar transaksi sewa aktif.
 * 
 * @package Bacain\Models
 */
class Member extends User
{
    /**
     * @var string Status keanggotaan (misal: 'Standard', 'Premium', 'VIP')
     */
    private string $statusKeanggotaan;

    /**
     * @var int Batas maksimum media yang dapat disewa secara bersamaan
     */
    private int $batasSewa;

    /**
     * @var array<int, Rental> Daftar sewa aktif milik member
     */
    private array $daftarSewaAktif = [];

    /**
     * Member constructor.
     *
     * @param int|string $id
     * @param string $nama
     * @param string $email
     * @param string $passwordHash
     * @param string $statusKeanggotaan
     * @param int $batasSewa
     * @param array $daftarSewaAktif
     */
    public function __construct(
        int|string $id,
        string $nama,
        string $email,
        string $passwordHash,
        string $statusKeanggotaan = 'Standard',
        int $batasSewa = 3,
        array $daftarSewaAktif = []
    ) {
        parent::__construct($id, $nama, $email, $passwordHash);
        $this->statusKeanggotaan = $statusKeanggotaan;
        $this->setBatasSewa($batasSewa);
        $this->daftarSewaAktif = $daftarSewaAktif;
    }

    /**
     * Getter statusKeanggotaan
     *
     * @return string
     */
    public function getStatusKeanggotaan(): string
    {
        return $this->statusKeanggotaan;
    }

    /**
     * Setter statusKeanggotaan
     *
     * @param string $statusKeanggotaan
     * @return void
     */
    public function setStatusKeanggotaan(string $statusKeanggotaan): void
    {
        $this->statusKeanggotaan = trim($statusKeanggotaan);
    }

    /**
     * Getter batasSewa
     *
     * @return int
     */
    public function getBatasSewa(): int
    {
        return $this->batasSewa;
    }

    /**
     * Setter batasSewa
     *
     * @param int $batasSewa
     * @return void
     * @throws BacainException Jika batas sewa bernilai negatif
     */
    public function setBatasSewa(int $batasSewa): void
    {
        if ($batasSewa < 0) {
            throw new BacainException("Batas sewa tidak boleh bernilai negatif.");
        }
        $this->batasSewa = $batasSewa;
    }

    /**
     * Getter daftarSewaAktif
     *
     * @return array
     */
    public function getDaftarSewaAktif(): array
    {
        return $this->daftarSewaAktif;
    }

    /**
     * Setter daftarSewaAktif
     *
     * @param array $daftarSewaAktif
     * @return void
     */
    public function setDaftarSewaAktif(array $daftarSewaAktif): void
    {
        $this->daftarSewaAktif = $daftarSewaAktif;
    }

    /**
     * Memeriksa apakah member masih memiliki kuota untuk menyewa media.
     *
     * @return bool
     */
    public function bisaSewa(): bool
    {
        return count($this->daftarSewaAktif) < $this->batasSewa;
    }

    /**
     * Mendapatkan jumlah sewa aktif saat ini.
     *
     * @return int
     */
    public function getJumlahSewaAktif(): int
    {
        return count($this->daftarSewaAktif);
    }

    /**
     * Menambahkan transaksi rental baru ke dalam daftar sewa aktif member.
     *
     * @param Rental $rental
     * @return void
     * @throws BacainException Jika kuota batas sewa telah tercapai
     */
    public function tambahSewa(Rental $rental): void
    {
        if (!$this->bisaSewa()) {
            throw new BacainException(
                "Batas sewa tercapai! Member '{$this->getNama()}' telah mencapai batas maksimal ({$this->batasSewa} media aktif)."
            );
        }

        $this->daftarSewaAktif[] = $rental;
    }

    /**
     * Menghapus transaksi rental dari daftar sewa aktif (misal saat buku dikembalikan).
     *
     * @param Rental|int|string $rental Objek Rental atau ID rental yang akan dihapus
     * @return bool True jika berhasil ditemukan dan dihapus
     */
    public function hapusSewa(Rental|int|string $rental): bool
    {
        $targetId = ($rental instanceof Rental) ? $rental->getId() : $rental;

        foreach ($this->daftarSewaAktif as $key => $item) {
            $itemId = ($item instanceof Rental) ? $item->getId() : $item;
            if ($itemId === $targetId || $item === $rental) {
                unset($this->daftarSewaAktif[$key]);
                $this->daftarSewaAktif = array_values($this->daftarSewaAktif);
                return true;
            }
        }

        return false;
    }

    public function getMaxRentLimit(): int
    {
        return $this->getBatasSewa();
    }

    public function getName(): string
    {
        return $this->getNama();
    }

    public function toArray(): array
    {
        return array_merge(parent::toArray(), [
            'name' => $this->getNama(),
            'password' => $this->getPasswordHash(),
            'statusKeanggotaan' => $this->statusKeanggotaan,
            'batasSewa' => $this->batasSewa,
            'maxRentLimit' => $this->batasSewa,
            'daftarSewaAktif' => array_map(
                fn(Rental $rental): array => $rental->toArray(),
                $this->daftarSewaAktif
            ),
        ]);
    }

    public static function fromArray(array $data): Member
    {
        return new Member(
            $data['id'],
            $data['name'] ?? $data['nama'] ?? '',
            $data['email'] ?? '',
            $data['passwordHash'] ?? $data['password'] ?? '',
            $data['statusKeanggotaan'] ?? $data['status_keanggotaan'] ?? $data['status'] ?? 'Standard',
            (int) ($data['batasSewa'] ?? $data['batas_sewa'] ?? $data['maxRentLimit'] ?? 3),
            (array) ($data['daftarSewaAktif'] ?? [])
        );
    }
}
