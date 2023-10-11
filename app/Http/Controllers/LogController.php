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
        $enc_key = '';

        // Check if the user is allowed to create new log entries
        if ($request->user()->tokenCan('log:create')) {
            $enc_key = $request->user()->encryption_key;
        } else {
            return ["Status" => "Error", "Message" => "Not allowed to create new log events!"];
        }

        if ($enc_key == '') {
            return ["Status" => "Error", "Message" => "No encryption key set!"];
        }

        // Validate the request before encrypting it
        $validated = $request->validate([
            'original_data' => 'required',
            'new_data' => 'required',
            'user_email' => 'required|email',
            'model' => 'required',
            'route' => 'required',
            'event_type' => 'required',
            'ip' => 'required'
        ]);

        $log = [
            'model' => $request->model,
            'original_data' => Encryption::encryptUsingKey($enc_key, serialize($request->original_data)),
            'new_data' => Encryption::encryptUsingKey($enc_key, serialize($request->new_data)),
            'user_email' => Encryption::encryptUsingKey($enc_key, $request->user_email),
            'event_type' => $request->event_type,
            'route' => $request->route,
            'ip_address' => Encryption::encryptUsingKey($enc_key, $request->ip)
        ];

        // Serialize the log data and publish it on the RabbitMQ stream
        $mqService = new \App\Services\RabbitMQService();
        $mqService->publish(serialize($log));
    }

    public function index(Request $request)
    {
        $logs = LogEntry::query()
            ->when($request->model, function ($query) use ($request) {
                return $query->where('model', '=', $request->model);
            })
            ->when($request->route, function ($query) use ($request) {
                return $query->where('route', '=', $request->route);
            })
            ->when($request->event, function ($query) use ($request) {
                return $query->where('event_type', '=', $request->event);
            })
            ->orderBy('created_at', 'DESC')
            ->paginate(15);

        $enc_key = User::findOrFail(Auth()->id())->encryption_key;
        if (!$enc_key || $enc_key == "") {
            return Inertia::render('Error', [
                'error' => ["status" => "400 Bad Request", "message" => [
                    "header" => "You do not have an encryption key!",
                    "info" => "Please contact an administrator for more information."
                ]],
            ]);
        }

        foreach ($logs as $log) {
            $log->user_email = Encryption::decryptUsingKey($enc_key, $log->user_email);
            $log->ip_address = Encryption::decryptUsingKey($enc_key, $log->ip_address);
        }

        return Inertia::render('Event/Logs', [
            'logs' => $logs,
        ]);
    }

    public function show(Request $request)
    {
        $enc_key = User::findOrFail(Auth()->id())->encryption_key;

        if (!$enc_key || $enc_key == "") {
            return Inertia::render('Error', [
                'error' => ["status" => "400 Bad Request", "message" => [
                    "header" => "You do not have an encryption key!",
                    "info" => "Please contact an administrator for more information."
                ]],
            ]);
        }

        $log = LogEntry::findOrFail($request->id);
        $log->original_data = unserialize(Encryption::decryptUsingKey($enc_key, $log->original_data));
        $log->new_data = unserialize(Encryption::decryptUsingKey($enc_key, $log->new_data));

        return $log;
    }
}
