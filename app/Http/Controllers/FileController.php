<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use Illuminate\Http\Request;
use App\Utilities\Encryption;
use App\Utilities\MessageFileModel;

class FileController extends Controller
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
        $data = [];
        $dir = storage_path('logs/');
        $logFiles = getLogFiles();

        if ($request->file && file_exists($dir . $request->file)) {
            $data = new MessageFileModel(file($dir . $request->file));
        } else if (!empty(getLogFiles())) {
            $data = new MessageFileModel(file($logFiles[0]["name"]));
        }

        if (!empty($data)) {
            $data = $data->all()->paginate(self::PAGINATE);
        }

        return Inertia::render('Event/Messages', [
            'messages' => !empty($data) ? $data["messages"] : $data,
            'links' => !empty($data) ? $data["links"] : $data,
            'files' => $logFiles
        ]);
    }

    function delete(Request $request)
    {
        $user = Auth()->user();
        $dir = storage_path('logs/');

        if (!Auth()->check() || $user->role->name !== "Super Admin") {
            return ["Status" => "Error", "Message" => "Unauthorized"];
        }

        if (!file_exists($dir . "/backups/" . $request->name)) {
            return ["Status" => "Error", "Message" => "File doesn't exist!"];
        }

        unlink($dir . $request->name);
        return getLogFiles();
    }

    function copy(Request $request)
    {
        $user = Auth()->user();
        $dir = storage_path('logs/');

        if (!Auth()->check() || $user->role->name !== "Super Admin") {
            return ["Status" => "Error", "Message" => "Unauthorized"];
        }

        if (file_exists($dir . "/backups/" . $request->name)) {
            return ["Status" => "Error", "Message" => "File is already backed up!"];
        }

        copy($dir . $request->name, $dir . "/backups/" . $request->name);

        return getLogFiles();
    }
}
