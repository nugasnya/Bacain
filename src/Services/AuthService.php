<?php

declare(strict_types=1);

namespace Bacain\Services;

use Bacain\Exceptions\BacainException;
use Bacain\Models\Member;
use Bacain\Repositories\MemberRepository;

class AuthService
{
    private MemberRepository $memberRepository;
    private string $sessionKey = 'bacain_member_id';

    public function __construct(MemberRepository $memberRepository)
    {
        $this->memberRepository = $memberRepository;
        if (session_status() === PHP_SESSION_NONE && !headers_sent()) {
            session_start();
        }
    }

    /**
     * Registrasi akun member baru dengan password ter-hash.
     */
    public function register(string $name, string $email, string $password): Member
    {
        $name = trim($name);
        $email = strtolower(trim($email));

        if ($name === '' || $email === '' || $password === '') {
            throw new BacainException("Nama, email, dan password wajib diisi.");
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new BacainException("Format email tidak valid.");
        }

        if (strlen($password) < 6) {
            throw new BacainException("Password minimal terdiri dari 6 karakter.");
        }

        if ($this->memberRepository->findByEmail($email) !== null) {
            throw new BacainException("Email sudah terdaftar.");
        }

        $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
        $id = 'MBR-' . bin2hex(random_bytes(4));

        $member = new Member($id, $name, $email, $hashedPassword);
        $this->memberRepository->save($member);

        return $member;
    }

    /**
     * Autentikasi login member dan menyimpan ID ke sesi.
     */
    public function login(string $email, string $password): Member
    {
        $member = $this->memberRepository->findByEmail($email);

        if ($member === null || !$member->verifyPassword($password)) {
            throw new BacainException("Email atau password salah.");
        }

        $_SESSION[$this->sessionKey] = $member->getId();
        return $member;
    }

    /**
     * Menghapus sesi login saat ini.
     */
    public function logout(): void
    {
        unset($_SESSION[$this->sessionKey]);
        if (session_status() === PHP_SESSION_ACTIVE) {
            session_regenerate_id(true);
        }
    }

    /**
     * Mengecek apakah pengguna sedang login.
     */
    public function isAuthenticated(): bool
    {
        return isset($_SESSION[$this->sessionKey]) && $this->getCurrentUser() !== null;
    }

    /**
     * Mengambil object Member yang sedang login saat ini.
     */
    public function getCurrentUser(): ?Member
    {
        $memberId = $_SESSION[$this->sessionKey] ?? null;
        if (!is_string($memberId) || $memberId === '') {
            return null;
        }

        return $this->memberRepository->findById($memberId);
    }
}

