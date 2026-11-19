<?php

declare(strict_types=1);

namespace Bacain\Models;

use Bacain\Exceptions\BacainException;

/**
 * Class Komik
 * Turunan langsung dari Media yang merepresentasikan buku komik / manga / manhwa.
 * Mengimplementasikan perhitungan harga sewa berbasis jumlah chapter sebagai bukti polymorphism.
 * 
 * @package Bacain\Models
 */
class Komik extends Media
{
    /**
     * @var int Jumlah chapter / bab dalam komik
     */
    private int $jumlahChapter;

    /**
     * @var bool Status apakah komik full-color / berwarna
     */
    private bool $isBerwarna;

    /**
     * Komik constructor.
     *
     * @param int|string $id
     * @param string $judul
     * @param string $penulis
     * @param float $hargaDasar
     * @param int $stok
     * @param int $jumlahChapter
     * @param bool $isBerwarna
     */
    public function __construct(
        int|string $id,
        string $judul,
        string $penulis,
        float $hargaDasar,
        int $stok,
        int $jumlahChapter = 1,
        bool $isBerwarna = false
    ) {
        parent::__construct($id, $judul, $penulis, $hargaDasar, $stok);
        $this->setJumlahChapter($jumlahChapter);
        $this->isBerwarna = $isBerwarna;
    }

    /**
     * Getter jumlahChapter
     *
     * @return int
     */
    public function getJumlahChapter(): int
    {
        return $this->jumlahChapter;
    }

    /**
     * Setter jumlahChapter
     *
     * @param int $jumlahChapter
     * @return void
     * @throws BacainException Jika jumlah chapter bernilai negatif atau 0
     */
    public function setJumlahChapter(int $jumlahChapter): void
    {
        if ($jumlahChapter < 0) {
            throw new BacainException("Jumlah chapter tidak boleh bernilai negatif.");
        }
        $this->jumlahChapter = $jumlahChapter;
    }

    /**
     * Getter isBerwarna
     *
     * @return bool
     */
    public function isBerwarna(): bool
    {
        return $this->isBerwarna;
    }

    /**
     * Setter isBerwarna
     *
     * @param bool $isBerwarna
     * @return void
     */
    public function setIsBerwarna(bool $isBerwarna): void
    {
        $this->isBerwarna = $isBerwarna;
    }

    /**
     * Implementasi hitungHargaSewa untuk Komik (Bukti Polymorphism).
     * Harga sewa dihitung per chapter:
     * (hargaDasar * durasiHari) + (jumlahChapter * tarif per chapter).
     *
     * @param int $durasiHari Durasi sewa dalam hari (minimal 1 hari)
     * @return float Total harga sewa
     */
    public function hitungHargaSewa(int $durasiHari = 1): float
    {
        $durasi = max(1, $durasiHari);
        $tarifPerChapter = $this->isBerwarna ? 750.0 : 500.0;
        $biayaChapter = $this->jumlahChapter * $tarifPerChapter;
        $total = ($this->getHargaDasar() * $durasi) + $biayaChapter;

        return (float) round($total, 2);
    }

    public function toArray(): array
    {
        return array_merge(parent::toArray(), [
            'type' => 'komik',
            'jumlahChapter' => $this->jumlahChapter,
            'isBerwarna' => $this->isBerwarna,
        ]);
    }

    public static function fromArray(array $data): Komik
    {
        return new Komik(
            $data['id'],
            $data['title'] ?? $data['judul'] ?? '',
            $data['author'] ?? $data['penulis'] ?? '',
            (float) ($data['rentalPrice'] ?? $data['hargaDasar'] ?? $data['hargaSewa'] ?? $data['harga_sewa'] ?? 0),
            (int) ($data['stock'] ?? $data['stok'] ?? 0),
            (int) ($data['jumlahChapter'] ?? $data['chapter'] ?? $data['volume'] ?? 1),
            (bool) ($data['isBerwarna'] ?? $data['berwarna'] ?? false)
        );
    }
}
