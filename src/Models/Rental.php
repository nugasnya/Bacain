<?php

declare(strict_types=1);

namespace Bacain\Models;

use Bacain\Exceptions\BacainException;
use DateTime;
use Exception;

/**
 * Class Rental
 * Merepresentasikan satu transaksi penyewaan media oleh member di Bacain.
 * Mengaitkan member, buku (Media), tanggal sewa, tanggal jatuh tempo, dan status transaksi.
 * 
 * @package Bacain\Models
 */
class Rental
{
    /**
     * @var int|string Identifier unik transaksi rental
     */
    private int|string $id;

    /**
     * @var Member Anggota yang melakukan penyewaan
     */
    private Member $member;

    /**
     * @var Media Media / Buku bacaan yang disewa
     */
    private Media $buku;

    /**
     * @var string Tanggal mulai sewa (format: Y-m-d)
     */
    private string $tanggalSewa;

    /**
     * @var string Tanggal jatuh tempo pengembalian (format: Y-m-d)
     */
    private string $tanggalJatuhTempo;

    /**
     * @var string Status penyewaan ('Aktif', 'Selesai', 'Terlambat')
     */
    private string $status;

    /**
     * @var float Total biaya sewa transaksi ini
     */
    private float $totalBiaya;

    /**
     * Rental constructor.
     *
     * @param int|string $id
     * @param Member $member
     * @param Media $buku
     * @param string $tanggalSewa
     * @param string $tanggalJatuhTempo
     * @param string $status
     * @param float|null $totalBiaya
     */
    public function __construct(
        int|string $id,
        Member $member,
        Media $buku,
        string $tanggalSewa = '',
        string $tanggalJatuhTempo = '',
        string $status = 'Aktif',
        ?float $totalBiaya = null
    ) {
        $this->id = $id;
        $this->member = $member;
        $this->buku = $buku;
        $this->status = $status;

        $this->tanggalSewa = $tanggalSewa !== '' ? $tanggalSewa : date('Y-m-d');
        if ($tanggalJatuhTempo !== '') {
            $this->tanggalJatuhTempo = $tanggalJatuhTempo;
        } else {
            // Default durasi pinjam adalah 7 hari
            $this->tanggalJatuhTempo = date('Y-m-d', (int) strtotime($this->tanggalSewa . ' + 7 days'));
        }

        if ($totalBiaya !== null) {
            $this->totalBiaya = $totalBiaya;
        } else {
            $this->totalBiaya = $this->hitungTotalBiaya();
        }
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
     * Getter member
     *
     * @return Member
     */
    public function getMember(): Member
    {
        return $this->member;
    }

    /**
     * Setter member
     *
     * @param Member $member
     * @return void
     */
    public function setMember(Member $member): void
    {
        $this->member = $member;
    }

    /**
     * Getter buku (Media)
     *
     * @return Media
     */
    public function getBuku(): Media
    {
        return $this->buku;
    }

    /**
     * Setter buku (Media)
     *
     * @param Media $buku
     * @return void
     */
    public function setBuku(Media $buku): void
    {
        $this->buku = $buku;
    }

    /**
     * Alias getter untuk getBuku()
     *
     * @return Media
     */
    public function getMedia(): Media
    {
        return $this->getBuku();
    }

    /**
     * Alias setter untuk setBuku()
     *
     * @param Media $media
     * @return void
     */
    public function setMedia(Media $media): void
    {
        $this->setBuku($media);
    }

    /**
     * Getter tanggalSewa
     *
     * @return string
     */
    public function getTanggalSewa(): string
    {
        return $this->tanggalSewa;
    }

    /**
     * Setter tanggalSewa
     *
     * @param string $tanggalSewa
     * @return void
     */
    public function setTanggalSewa(string $tanggalSewa): void
    {
        $this->tanggalSewa = $tanggalSewa;
    }

    /**
     * Getter tanggalJatuhTempo
     *
     * @return string
     */
    public function getTanggalJatuhTempo(): string
    {
        return $this->tanggalJatuhTempo;
    }

    /**
     * Setter tanggalJatuhTempo
     *
     * @param string $tanggalJatuhTempo
     * @return void
     */
    public function setTanggalJatuhTempo(string $tanggalJatuhTempo): void
    {
        $this->tanggalJatuhTempo = $tanggalJatuhTempo;
    }

    /**
     * Getter status
     *
     * @return string
     */
    public function getStatus(): string
    {
        return $this->status;
    }

    /**
     * Setter status
     *
     * @param string $status
     * @return void
     */
    public function setStatus(string $status): void
    {
        $this->status = trim($status);
    }

    /**
     * Getter totalBiaya
     *
     * @return float
     */
    public function getTotalBiaya(): float
    {
        return $this->totalBiaya;
    }

    /**
     * Setter totalBiaya
     *
     * @param float $totalBiaya
     * @return void
     * @throws BacainException Jika total biaya bernilai negatif
     */
    public function setTotalBiaya(float $totalBiaya): void
    {
        if ($totalBiaya < 0) {
            throw new BacainException("Total biaya sewa tidak boleh bernilai negatif.");
        }
        $this->totalBiaya = $totalBiaya;
    }

    /**
     * Menghitung durasi sewa dalam hari berdasarkan selisih tanggalSewa dan tanggalJatuhTempo.
     *
     * @return int Jumlah hari (minimal 1)
     */
    public function hitungDurasiHari(): int
    {
        try {
            $tglAwal = new DateTime($this->tanggalSewa);
            $tglAkhir = new DateTime($this->tanggalJatuhTempo);
            $diff = $tglAwal->diff($tglAkhir);
            $hari = (int) $diff->days;

            return max(1, $hari);
        } catch (Exception $e) {
            return 1;
        }
    }

    /**
     * Menghitung ulang total biaya berdasarkan durasi sewa dan metode hitungHargaSewa media (Polymorphism).
     *
     * @return float Total biaya sewa
     */
    public function hitungTotalBiaya(): float
    {
        $durasiHari = $this->hitungDurasiHari();
        $this->totalBiaya = $this->buku->hitungHargaSewa($durasiHari);

        return $this->totalBiaya;
    }

    /**
     * Menjalankan proses sewa:
     * 1. Memeriksa ketersediaan kuota sewa member.
     * 2. Mengurangi stok media (method sewa() pada Rentable).
     * 3. Mendaftarkan transaksi ini ke daftar sewa aktif milik member.
     *
     * @return bool True jika berhasil
     * @throws BacainException Jika stok habis atau batas sewa member tercapai
     */
    public function prosesSewa(): bool
    {
        if (!$this->member->bisaSewa()) {
            throw new BacainException(
                "Batas sewa tercapai! Member '{$this->member->getNama()}' tidak dapat menyewa media lagi."
            );
        }

        // Potong stok media (akan melempar BacainException jika stok <= 0)
        $this->buku->sewa();

        // Daftarkan ke member
        $this->member->tambahSewa($this);

        $this->status = 'Aktif';
        return true;
    }

    /**
     * Menjalankan proses pengembalian media:
     * 1. Menambah kembali stok media (method kembalikan() pada Rentable).
     * 2. Menghapus transaksi ini dari daftar sewa aktif member.
     * 3. Mengubah status transaksi menjadi 'Selesai'.
     *
     * @return bool True jika berhasil
     */
    public function prosesKembalikan(): bool
    {
        // Kembalikan stok media
        $this->buku->kembalikan();

        // Hapus dari daftar sewa aktif member
        $this->member->hapusSewa($this);

        $this->status = 'Selesai';
        return true;
    }

    /**
     * Memeriksa keterlambatan pengembalian berdasarkan tanggal saat ini.
     * Jika terlambat dan masih aktif, ubah status menjadi 'Terlambat'.
     *
     * @param string|null $tanggalSekarang Tanggal acuan (default hari ini)
     * @return bool True jika terlambat
     */
    public function cekKeterlambatan(?string $tanggalSekarang = null): bool
    {
        if ($this->status === 'Selesai') {
            return false;
        }

        $sekarang = $tanggalSekarang ?? date('Y-m-d');
        if (strtotime($sekarang) > strtotime($this->tanggalJatuhTempo)) {
            $this->status = 'Terlambat';
            return true;
        }

        return false;
    }

    public function getMemberId(): int|string
    {
        return $this->member->getId();
    }

    public function getBookId(): int|string
    {
        return $this->buku->getId();
    }

    public function getRentalDate(): string
    {
        return $this->getTanggalSewa();
    }

    public function getDueDate(): string
    {
        return $this->getTanggalJatuhTempo();
    }

    public function getTotalPrice(): float
    {
        return $this->getTotalBiaya();
    }

    public function markAsReturned(?string $returnDate = null): void
    {
        $this->prosesKembalikan();
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'memberId' => $this->member->getId(),
            'member_id' => $this->member->getId(),
            'bookId' => $this->buku->getId(),
            'book_id' => $this->buku->getId(),
            'tanggalSewa' => $this->tanggalSewa,
            'tanggal_sewa' => $this->tanggalSewa,
            'rentalDate' => $this->tanggalSewa,
            'tanggalJatuhTempo' => $this->tanggalJatuhTempo,
            'tanggal_jatuh_tempo' => $this->tanggalJatuhTempo,
            'dueDate' => $this->tanggalJatuhTempo,
            'status' => $this->status,
            'totalBiaya' => $this->totalBiaya,
            'harga_total' => $this->totalBiaya,
            'totalPrice' => $this->totalBiaya,
            'returnDate' => null,
        ];
    }
}
