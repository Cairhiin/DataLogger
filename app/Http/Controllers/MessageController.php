<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use Illuminate\Http\Request;
use App\Utilities\Encryption;
use App\Utilities\FileModel;
use App\Utilities\ReadLogsFromFile;

class MessageController extends Controller
{
    const PAGINATE = 15;

    public function store(Request $request)
    {
        $enc_key = "";

        // Check if the user is allowed to create new log entries
        if ($request->user()->tokenCan('message:create')) {
            $enc_key = $request->user()->encryption_key;
        } else {
            return ["Status" => "Error", "Message" => "Not allowed to create new message events!"];
        }

        if ($enc_key == '' || $enc_key == null) {
            return ["Status" => "Error", "Message" => "No encryption key set!"];
        }

        // Validate the request before encrypting it
        $validated = $request->validate([
            'app_id' => 'required',
            'route' => 'required',
            'event_type' => 'required',
            'user_email' => 'required|email',
            'ip' => 'required'
        ]);

        $message = [
            'model' => $request->model,
            'app_id' => Encryption::encryptUsingKey($enc_key, $request->app_id),
            'event_type' => $request->event_type,
            'route' => $request->route,
            'user_email' => Encryption::encryptUsingKey($enc_key, $request->user_email),
            'ip_address' => Encryption::encryptUsingKey($enc_key, $request->ip)
        ];

        // Serialize the log data and publish it on the RabbitMQ stream
        $mqService = new \App\Services\RabbitMQService();
        $mqService->publish(serialize($message), 'url');
    }

    public function index(Request $request)
    {
        $user = Auth()->user();

        $attributes = [
            "app_id",
            "ip_address",
            "event_type",
            "model",
            "route",
            "user_email"
        ];

        $encrypted = [
            "app_id",
            "user_email"
        ];

        $data = new FileModel(file(storage_path() . '/logs/user-data.log'), $attributes, $encrypted, $user->role != "Member", $user);
        $data = $data->all()->orderBy('app_id', 'DESC')->filterBy('route', 'delete.user')->paginate(15);

        return Inertia::render('Event/Messages', [
            'messages' => $data["messages"],
            'links' => $data["links"]
        ]);
    }
}
