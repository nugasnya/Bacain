<?php

declare(strict_types=1);

namespace Bacain\Interfaces;

/**
 * Interface Rentable
 * Kontrak interface untuk media atau item yang dapat disewa dan dikembalikan.
 * 
 * @package Bacain\Interfaces
 */
interface Rentable
{
    /**
     * Memproses penyewaan media (mengurangi stok dan validasi ketersediaan).
     *
     * @return bool True jika proses sewa berhasil
     * @throws \Bacain\Exceptions\BacainException Jika stok habis atau tidak dapat disewa
     */
    public function sewa(): bool;

    /**
     * Memproses pengembalian media (menambah stok kembali).
     *
     * @return bool True jika proses pengembalian berhasil
     */
    public function kembalikan(): bool;
}
