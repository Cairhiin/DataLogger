<?php

namespace App\Http\Controllers;

use App\Models\User;
use Inertia\Inertia;
use App\Models\LogEntry;
use Illuminate\Http\Request;
use App\Utilities\Encryption;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

class LogController extends Controller
{
    public function store(Request $request)
    {
        echo "email: " . $request->user_email;
        $user = User::where('email', $request->user_email)->get();
        $enc_key = '';

        if (!$user->isEmpty()) {
            $enc_key = $user->first()->encryption_key;
        } else {
            return ["Status" => "Error", "Message" => "Invalid email address!"];
        }

        if ($enc_key == '') {
            return ["Status" => "Error", "Message" => "No encryption key set!"];
        }


        $validated = $request->validate([
            'original_data' => 'required',
            'new_data' => 'required',
            'user_email' => 'required|email',
            'model' => 'required',
            'route' => 'required',
            'event_type' => 'required',
            'ip' => 'required'
        ]);

        if ($request->user()->tokenCan('log:create')) {
            $log = [
                'model' => $request->model,
                'original_data' => Encryption::encryptUsingKey($enc_key, serialize($request->original_data)),
                'new_data' => Encryption::encryptUsingKey($enc_key, serialize($request->new_data)),
                'user_email' => Encryption::encryptUsingKey($enc_key, $request->user_email),
                'event_type' => $request->event_type,
                'route' => $request->route,
                'ip_address' => Encryption::encryptUsingKey($enc_key, $request->ip)
            ];

            $mqService = new \App\Services\RabbitMQService();
            $mqService->publish(serialize($log));
        } else {
            return ["Status" => "Error", "Message" => "Not allowed to create new log events!"];
        }
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

        if ($enc_key == '') {
            return ["Status" => "Error", "Message" => "No encryption key set!"];
        }

        $log = LogEntry::findOrFail($request->id);
        $log->original_data = unserialize(Encryption::decryptUsingKey($enc_key, $log->original_data));
        $log->new_data = unserialize(Encryption::decryptUsingKey($enc_key, $log->new_data));

        return $log;
    }

    public function testRabbitMQ(Request $request)
    {
        $data = [
            'ip_address' => '127.0.0.1',
            'route' => 'profile.update',
            'new_data' => 'Kirkkokatu',
            'original_data' => 'Kymenlaaksonkatu',
            'user_email' => 'info@worthIT-it.com',
            'event_type' => 'update',
            'model' => 'Address'
        ];
    }
}
