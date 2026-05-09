<?php

declare(strict_types=1);

namespace App\Core;

class Response
{
    public static function success(array $data = [], int $status = 200): void
    {
        http_response_code($status);
        echo json_encode([
            'success' => true,
            'data' => $data,
        ], JSON_UNESCAPED_UNICODE);
        exit;
    }

    public static function error(string $message, int $status = 400): void
    {
        http_response_code($status);
        echo json_encode([
            'success' => false,
            'message' => $message,
        ], JSON_UNESCAPED_UNICODE);
        exit;
    }
}
