<?php

declare(strict_types=1);

namespace Bacain\Models;

use Bacain\Exceptions\BacainException;

/**
 * Class User
 * Class dasar pengguna sistem Bacain.
 * Menerapkan prinsip Encapsulation dengan properti private serta getter dan setter.
 * 
 * @package Bacain\Models
 */
class User
{
    /**
     * @var int|string Identifier unik pengguna
     */
    private int|string $id;

    /**
     * @var string Nama lengkap pengguna
     */
    private string $nama;

    /**
     * @var string Alamat email pengguna
     */
    private string $email;

    /**
     * @var string Hash password untuk keamanan autentikasi
     */
    private string $passwordHash;

    /**
     * User constructor.
     *
     * @param int|string $id
     * @param string $nama
     * @param string $email
     * @param string $passwordHash
     */
    public function __construct(
        int|string $id,
        string $nama,
        string $email,
        string $passwordHash
    ) {
        $this->id = $id;
        $this->nama = $nama;
        $this->setEmail($email);
        $this->passwordHash = $passwordHash;
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
     * Getter nama
     *
     * @return string
     */
    public function getNama(): string
    {
        return $this->nama;
    }

    /**
     * Setter nama
     *
     * @param string $nama
     * @return void
     */
    public function setNama(string $nama): void
    {
        $this->nama = trim($nama);
    }

    /**
     * Getter email
     *
     * @return string
     */
    public function getEmail(): string
    {
        return $this->email;
    }

    /**
     * Setter email
     *
     * @param string $email
     * @return void
     * @throws BacainException Jika format email tidak valid
     */
    public function setEmail(string $email): void
    {
        $email = trim($email);
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new BacainException("Format email '{$email}' tidak valid.");
        }
        $this->email = $email;
    }

    /**
     * Getter passwordHash
     *
     * @return string
     */
    public function getPasswordHash(): string
    {
        return $this->passwordHash;
    }

    /**
     * Setter passwordHash
     *
     * @param string $passwordHash
     * @return void
     */
    public function setPasswordHash(string $passwordHash): void
    {
        $this->passwordHash = $passwordHash;
    }

    /**
     * Mengatur password baru dari plain text dengan melakukan hashing otomatis.
     *
     * @param string $plainPassword Password mentah
     * @return void
     */
    public function setPassword(string $plainPassword): void
    {
        $this->passwordHash = password_hash($plainPassword, PASSWORD_DEFAULT);
    }

    /**
     * Memverifikasi apakah password plain cocok dengan hash yang tersimpan.
     *
     * @param string $plainPassword Password mentah untuk dicocokkan
     * @return bool True jika cocok
     */
    public function verifyPassword(string $plainPassword): bool
    {
        return password_verify($plainPassword, $this->passwordHash);
    }

    public function getName(): string
    {
        return $this->getNama();
    }

    public function getPassword(): string
    {
        return $this->getPasswordHash();
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'nama' => $this->nama,
            'email' => $this->email,
            'passwordHash' => $this->passwordHash,
        ];
    }
}
