<?php

namespace App\Exceptions;

use Exception;

class CustomException extends Exception
{
    public function render()
    {
        return response()->json([
            [
                'error' => true,
                "status" => "401 Unauthorized",
                "message" => $this->getMessage()
            ]
        ]);
    }
}
