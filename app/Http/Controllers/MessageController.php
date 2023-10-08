<?php

namespace App\Http\Controllers;

use App\Models\LogEntry;
use Illuminate\Http\Request;

class MessageController extends Controller
{
    public function create(Request $request)
    {
        if ($request->user()->tokenCan('log:create')) {
            LogEntry::create([
                'original_data' => serialize($request->originalData),
                'new_data' => serialize($request->newData),
                'user_email' => $request->userEmail,
                'event_type' => $request->eventType,
                'route' => $request->route,
                'ip_address' => $request->ip
            ]);

            return $request->user();
        }

        return ["Status" => "Unauthorized", "Message" => "Not allowed to create new log events!"];
    }
}
