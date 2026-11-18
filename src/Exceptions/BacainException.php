<?php

declare(strict_types=1);

namespace Bacain\Exceptions;

use Exception;
use Throwable;

/**
 * Class BacainException
 * Exception khusus untuk sistem Bacain, misalnya ketika stok habis atau batas sewa tercapai.
 * 
 * @package Bacain\Exceptions
 */
class BacainException extends Exception
{
    /**
     * BacainException constructor.
     *
     * @param string $message Pesan error exception
     * @param int $code Kode error
     * @param Throwable|null $previous Exception sebelumnya jika ada
     */
    public function __construct(string $message = "", int $code = 0, ?Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
