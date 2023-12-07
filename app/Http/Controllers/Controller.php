<?php

namespace App\Http\Controllers;

use Throwable;
use App\Models\User;
use Illuminate\Http\Request;
use App\Utilities\Encryption;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;

    function getEncryptionKey(Request $request)
    {
        try {
            $enc_key = User::findOrFail(Auth()->id())->encryption_key;
        } catch (Throwable $e) {
            return [
                'error' => ["status" => "404 Not Found", "message" => [
                    "header" => "The requested resources was not found!",
                    "info" => "There appears to be a problem with your user account."
                ]],
            ];
        }

        if ($enc_key == '' || $enc_key == null) {
            $enc_key = Encryption::createPersonalKey();
            $user = $request->user();
            $user->encryption_key = $enc_key;
            $user->save();
        }

        return $enc_key;
    }
}
