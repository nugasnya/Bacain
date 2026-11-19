<?php

declare(strict_types=1);

namespace Bacain\Models;

use Bacain\Exceptions\BacainException;

/**
 * Class Novel
 * Turunan dari EBook / Media yang merepresentasikan buku novel.
 * Memiliki atribut jumlah halaman dan mengimplementasikan perhitungan harga sewa berbasis jumlah halaman.
 * 
 * @package Bacain\Models
 */
class Novel extends EBook
{
    /**
     * @var int Jumlah halaman buku novel
     */
    private int $jumlahHalaman;

    /**
     * Novel constructor.
     * Fleksibel mendukung urutan parameter ($jumlahHalaman lebih dahulu atau format/ukuran file lebih dahulu).
     *
     * @param int|string $id
     * @param string $judul
     * @param string $penulis
     * @param float $hargaDasar
     * @param int $stok
     * @param mixed $param6 int jumlahHalaman atau string formatFile
     * @param mixed $param7 string formatFile atau float ukuranFile
     * @param mixed $param8 float ukuranFile atau int jumlahHalaman
     */
    public function __construct(
        int|string $id,
        string $judul,
        string $penulis,
        float $hargaDasar,
        int $stok,
        mixed $param6 = 0,
        mixed $param7 = 'PDF',
        mixed $param8 = 0.0
    ) {
        $formatFile = 'PDF';
        $ukuranFile = 0.0;
        $jumlahHalaman = 0;

        if (is_string($param6) && !is_numeric($param6)) {
            // Urutan: formatFile, ukuranFile, jumlahHalaman
            $formatFile = $param6;
            $ukuranFile = is_numeric($param7) ? (float)$param7 : 0.0;
            $jumlahHalaman = is_numeric($param8) ? (int)$param8 : 0;
        } else {
            // Urutan: jumlahHalaman, formatFile, ukuranFile
            $jumlahHalaman = is_numeric($param6) ? (int)$param6 : 0;
            $formatFile = is_string($param7) ? $param7 : 'PDF';
            $ukuranFile = is_numeric($param8) ? (float)$param8 : 0.0;
        }

        parent::__construct($id, $judul, $penulis, $hargaDasar, $stok, $formatFile, $ukuranFile);
        $this->setJumlahHalaman($jumlahHalaman);
    }

    /**
     * Getter jumlahHalaman
     *
     * @return int
     */
    public function getJumlahHalaman(): int
    {
        return $this->jumlahHalaman;
    }

    /**
     * Setter jumlahHalaman
     *
     * @param int $jumlahHalaman
     * @return void
     * @throws BacainException Jika jumlah halaman bernilai negatif
     */
    public function setJumlahHalaman(int $jumlahHalaman): void
    {
        if ($jumlahHalaman < 0) {
            throw new BacainException("Jumlah halaman tidak boleh bernilai negatif.");
        }
        $this->jumlahHalaman = $jumlahHalaman;
    }

    /**
     * Implementasi hitungHargaSewa untuk Novel (Polymorphism).
     * Harga sewa dihitung berdasarkan jumlah halaman:
     * (hargaDasar * durasiHari) + (jumlahHalaman * tarif per halaman).
     *
     * @param int $durasiHari Durasi sewa dalam hari (minimal 1 hari)
     * @return float Total harga sewa
     */
    public function hitungHargaSewa(int $durasiHari = 1): float
    {
        $durasi = max(1, $durasiHari);
        $tarifPerHalaman = 20.0; // Biaya Rp 20 per halaman
        $biayaHalaman = $this->jumlahHalaman * $tarifPerHalaman;
        $total = ($this->getHargaDasar() * $durasi) + $biayaHalaman;

        return (float) round($total, 2);
    }

    public function toArray(): array
    {
        return array_merge(parent::toArray(), [
            'type' => 'novel',
            'jumlahHalaman' => $this->jumlahHalaman,
        ]);
    }

    public static function fromArray(array $data): Novel
    {
        return new Novel(
            $data['id'],
            $data['title'] ?? $data['judul'] ?? '',
            $data['author'] ?? $data['penulis'] ?? '',
            (float) ($data['rentalPrice'] ?? $data['hargaDasar'] ?? $data['hargaSewa'] ?? $data['harga_sewa'] ?? 0),
            (int) ($data['stock'] ?? $data['stok'] ?? 0),
            (int) ($data['pages'] ?? $data['jumlahHalaman'] ?? $data['halaman'] ?? 0),
            $data['fileFormat'] ?? $data['formatFile'] ?? $data['format'] ?? 'PDF',
            (float) ($data['fileSizeMb'] ?? $data['ukuranFile'] ?? $data['ukuran_mb'] ?? 0)
        );
    }
}
