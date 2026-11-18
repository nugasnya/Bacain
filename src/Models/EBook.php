<?php

declare(strict_types=1);

namespace Bacain\Models;

use Bacain\Exceptions\BacainException;

/**
 * Class EBook
 * Turunan dari Media yang merepresentasikan buku elektronik (digital).
 * Menambahkan atribut format file dan ukuran file, serta mengimplementasikan perhitungan harga sewa sendiri.
 * 
 * @package Bacain\Models
 */
class EBook extends Media
{
    /**
     * @var string Format file dokumen digital (misal: 'PDF', 'EPUB', 'MOBI')
     */
    private string $formatFile;

    /**
     * @var float Ukuran file dalam Megabytes (MB)
     */
    private float $ukuranFile;

    /**
     * EBook constructor.
     *
     * @param int|string $id
     * @param string $judul
     * @param string $penulis
     * @param float $hargaDasar
     * @param int $stok
     * @param string $formatFile
     * @param float $ukuranFile
     */
    public function __construct(
        int|string $id,
        string $judul,
        string $penulis,
        float $hargaDasar,
        int $stok,
        string $formatFile = 'PDF',
        float $ukuranFile = 0.0
    ) {
        parent::__construct($id, $judul, $penulis, $hargaDasar, $stok);
        $this->formatFile = $formatFile;
        $this->setUkuranFile($ukuranFile);
    }

    /**
     * Getter formatFile
     *
     * @return string
     */
    public function getFormatFile(): string
    {
        return $this->formatFile;
    }

    /**
     * Setter formatFile
     *
     * @param string $formatFile
     * @return void
     */
    public function setFormatFile(string $formatFile): void
    {
        $this->formatFile = strtoupper(trim($formatFile));
    }

    /**
     * Getter ukuranFile
     *
     * @return float
     */
    public function getUkuranFile(): float
    {
        return $this->ukuranFile;
    }

    /**
     * Setter ukuranFile
     *
     * @param float $ukuranFile
     * @return void
     * @throws BacainException Jika ukuran file bernilai negatif
     */
    public function setUkuranFile(float $ukuranFile): void
    {
        if ($ukuranFile < 0) {
            throw new BacainException("Ukuran file tidak boleh bernilai negatif.");
        }
        $this->ukuranFile = $ukuranFile;
    }

    /**
     * Implementasi hitungHargaSewa untuk EBook.
     * Harga sewa dihitung dari (hargaDasar * durasiHari) ditambah biaya lisensi/cloud storage
     * berbasis ukuran file (Rp 50 per MB).
     *
     * @param int $durasiHari Durasi sewa dalam hari (minimal 1 hari)
     * @return float Total harga sewa
     */
    public function hitungHargaSewa(int $durasiHari = 1): float
    {
        $durasi = max(1, $durasiHari);
        $biayaCloud = $this->ukuranFile * 50.0;
        $total = ($this->getHargaDasar() * $durasi) + $biayaCloud;

        return (float) round($total, 2);
    }
}

// Menjaga kompatibilitas penamaan class jika dipanggil sebagai Ebook
if (!class_exists('Bacain\Models\Ebook', false)) {
    class_alias(EBook::class, 'Bacain\Models\Ebook');
}
