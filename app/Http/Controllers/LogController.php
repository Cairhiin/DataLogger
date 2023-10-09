<?php

namespace App\Http\Controllers;

use App\Models\User;
use Inertia\Inertia;
use App\Models\LogEntry;
use Illuminate\Http\Request;
use App\Utilities\Encryption;

class LogController extends Controller
{
    public function store(Request $request)
    {
        $user = User::where('email', $request->userEmail)->get();
        $enc_key = '';

        if (!$user->isEmpty()) {
            $enc_key = $user->first()->encryption_key;
        } else {
            return ["Status" => "Error", "Message" => "Invalid email address!"];
        }

        if ($enc_key == '') {
            return ["Status" => "Error", "Message" => "No encryption key set!"];
        }

        if ($request->user()->tokenCan('log:create')) {
            LogEntry::create([
                'model' => $request->model,
                'original_data' => Encryption::encryptUsingKey($enc_key, serialize($request->originalData)),
                'new_data' => Encryption::encryptUsingKey($enc_key, serialize($request->newData)),
                'user_email' => Encryption::encryptUsingKey($enc_key, $request->userEmail),
                'event_type' => $request->eventType,
                'route' => $request->route,
                'ip_address' => Encryption::encryptUsingKey($enc_key, $request->ip)
            ]);

            return ["Status" => "Success", "Message" => "Entered new log event!"];
        }

        return ["Status" => "Error", "Message" => "Not allowed to create new log events!"];
    }

    public function index()
    {
        $logs = LogEntry::orderBy('created_at', 'DESC')->paginate(5);
        $enc_key = User::findOrFail(Auth()->id())->encryption_key;

        foreach ($logs as $log) {
            $log->original_data = unserialize(Encryption::decryptUsingKey($enc_key, $log->original_data));
            $log->new_data = unserialize(Encryption::decryptUsingKey($enc_key, $log->new_data));
            $log->user_email = Encryption::decryptUsingKey($enc_key, $log->user_email);
            $log->ip_address = Encryption::decryptUsingKey($enc_key, $log->ip_address);
        }

        return Inertia::render('Event/Logs', [
            'logs' => $logs,
        ]);
    }
}
