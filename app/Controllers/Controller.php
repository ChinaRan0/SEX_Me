<?php

namespace App\Controllers;

use App\Helpers\Response;

/**
 * Base Controller
 */
abstract class Controller
{
    /**
     * Get JSON input from request body
     */
    protected function getInput(): array
    {
        $input = file_get_contents('php://input');
        $data = json_decode($input, true);
        return $data ?? [];
    }

    /**
     * Get query parameter
     */
    protected function getQuery(string $key, $default = null)
    {
        return $_GET[$key] ?? $default;
    }

    /**
     * Get path parameter
     */
    protected function getPathParam(int $index, $default = null)
    {
        global $pathParams;
        return $pathParams[$index] ?? $default;
    }
}
