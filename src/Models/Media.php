<?php

declare(strict_types=1);

namespace Bacain\Models;

use Bacain\Exceptions\BacainException;
use Bacain\Interfaces\Rentable;

/**
 * Class Media
 * Abstract class induk untuk seluruh tipe media bacaan di Bacain.
 * Menerapkan prinsip Abstraction, Encapsulation, dan mengimplementasikan Rentable.
 * 
 * @package Bacain\Models
 */
abstract class Media implements Rentable
{
    /**
     * @var int|string Identifier unik media
     */
    private int|string $id;

    /**
     * @var string Judul media
     */
    private string $judul;

    /**
     * @var string Nama penulis/pengarang
     */
    private string $penulis;

    /**
     * @var float Harga dasar sewa
     */
    private float $hargaDasar;

    /**
     * @var int Jumlah stok tersedia
     */
    private int $stok;

    /**
     * Media constructor.
     *
     * @param int|string $id
     * @param string $judul
     * @param string $penulis
     * @param float $hargaDasar
     * @param int $stok
     */
    public function __construct(
        int|string $id,
        string $judul,
        string $penulis,
        float $hargaDasar,
        int $stok
    ) {
        $this->id = $id;
        $this->judul = $judul;
        $this->penulis = $penulis;
        $this->setHargaDasar($hargaDasar);
        $this->setStok($stok);
    }

    /**
     * Getter id
     *
     * @return int|string
     */
    public function getId(): int|string
    {
        return $this->id;
    }

    /**
     * Setter id
     *
     * @param int|string $id
     * @return void
     */
    public function setId(int|string $id): void
    {
        $this->id = $id;
    }

    /**
     * Getter judul
     *
     * @return string
     */
    public function getJudul(): string
    {
        return $this->judul;
    }

    /**
     * Setter judul
     *
     * @param string $judul
     * @return void
     */
    public function setJudul(string $judul): void
    {
        $this->judul = trim($judul);
    }

    /**
     * Getter penulis
     *
     * @return string
     */
    public function getPenulis(): string
    {
        return $this->penulis;
    }

    /**
     * Setter penulis
     *
     * @param string $penulis
     * @return void
     */
    public function setPenulis(string $penulis): void
    {
        $this->penulis = trim($penulis);
    }

    /**
     * Getter hargaDasar
     *
     * @return float
     */
    public function getHargaDasar(): float
    {
        return $this->hargaDasar;
    }

    /**
     * Setter hargaDasar
     *
     * @param float $hargaDasar
     * @return void
     * @throws BacainException Jika harga dasar bernilai negatif
     */
    public function setHargaDasar(float $hargaDasar): void
    {
        if ($hargaDasar < 0) {
            throw new BacainException("Harga dasar tidak boleh bernilai negatif.");
        }
        $this->hargaDasar = $hargaDasar;
    }

    /**
     * Getter stok
     *
     * @return int
     */
    public function getStok(): int
    {
        return $this->stok;
    }

    /**
     * Setter stok
     *
     * @param int $stok
     * @return void
     * @throws BacainException Jika stok bernilai negatif
     */
    public function setStok(int $stok): void
    {
        if ($stok < 0) {
            throw new BacainException("Stok tidak boleh bernilai negatif.");
        }
        $this->stok = $stok;
    }

    /**
     * Memeriksa apakah media masih memiliki stok tersedia untuk disewa.
     *
     * @return bool
     */
    public function isTersedia(): bool
    {
        return $this->stok > 0;
    }

    /**
     * Implementasi method dari interface Rentable.
     * Mengurangi stok jika media tersedia.
     *
     * @return bool
     * @throws BacainException Jika stok habis
     */
    public function sewa(): bool
    {
        if (!$this->isTersedia()) {
            throw new BacainException("Stok untuk media '{$this->judul}' habis. Tidak dapat disewa.");
        }

        $this->stok--;
        return true;
    }

    /**
     * Implementasi method dari interface Rentable.
     * Mengembalikan media dan menambah jumlah stok.
     *
     * @return bool
     */
    public function kembalikan(): bool
    {
        $this->stok++;
        return true;
    }

    /**
     * Method abstrak untuk menghitung harga sewa (polymorphism).
     * Setiap subclass Media wajib memberikan implementasi perhitungannya masing-masing.
     *
     * @param int $durasiHari Durasi sewa dalam satuan hari (default: 1)
     * @return float Total harga sewa
     */
    abstract public function hitungHargaSewa(int $durasiHari = 1): float;
}
