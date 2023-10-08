<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use App\Models\LoggedMessage;
use App\Utilities\Encryption;

class LogController extends Controller
{
    public function create(Request $request)
    {
        $user = User::where('email', $request->userEmail)->get();
        $enc_key = '';

        if (!$user->isEmpty()) {
            $enc_key = $user->first()->encryption_key;
        }

        if ($request->user()->tokenCan('log:create')) {
            LoggedMessage::create([
                'original_data' => Encryption::encrypt_using_key($enc_key, serialize($request->originalData)),
                'new_data' => Encryption::encrypt_using_key($enc_key, serialize($request->newData)),
                'user_email' => Encryption::encrypt_using_key($enc_key, $request->userEmail),
                'event_type' => $request->eventType,
                'route' => $request->route,
                'ip_address' => $request->ip
            ]);

            return $request->user();
        }

        return ["Status" => "Unauthorized", "Message" => "Not allowed to create new log events!"];
    }
}
