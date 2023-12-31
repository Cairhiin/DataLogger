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
        // Validate the request before encrypting it
        $validated = $request->validate([
            'app_id' => 'required',
            'route' => 'required',
            'ip' => 'required'
        ]);

        $enc_key = $this->getEncryptionKey($request);

        $message = [
            'app_id' => Encryption::encryptUsingKey($enc_key, $request->app_id),
            'route' => $request->route,
            'ip_address' => Encryption::encryptUsingKey($enc_key, $request->ip),
            'user_id' => $request->user()->id
        ];

        // Serialize the log data and publish it on the RabbitMQ stream
        $mqService = new \App\Services\RabbitMQService();
        $mqService->publish(serialize($message), 'url');
    }

    public function index(Request $request)
    {
        $data = [];
        $unique = [];
        $dir = storage_path('logs/');
        $logFiles = getMessageLogFiles();

        if ($request->file && file_exists($dir . $request->file)) {
            $data = new MessageFileModel($dir . $request->file);
        } else if (!empty($logFiles)) {
            $data = new MessageFileModel($logFiles[0]["name"]);
        }

        if (!empty($data)) {
            $unique = $data->all()->getUniqueValues();
            if ($request->model) {
                $data = $data->all()->filterBy('model', $request->model)->paginate(self::PAGINATE);
            } else if ($request->app) {
                $data = $data->all()->filterBy('app_id', $request->app)->paginate(self::PAGINATE);
            } else if ($request->route) {
                $data = $data->all()->filterBy('route', $request->route)->paginate(self::PAGINATE);
            } else {
                $data = $data->all()->paginate(self::PAGINATE);
            }
        }

        return Inertia::render('Event/Messages', [
            'messages' => !empty($data) ? $data["messages"] : $data,
            'links' => !empty($data) ? $data["links"] : $data,
            'files' => $logFiles,
            'uniqueValues' => $unique,
            'url' => $request->file,
        ]);
    }

    function delete(Request $request)
    {
        $user = Auth()->user();
        $dir = storage_path('logs/');
        $logFiles = getMessageLogFiles();

        if (!Auth()->check() || $user->role->name !== "Super Admin") {
            return ["Status" => "Error", "Message" => "Unauthorized"];
        }

        if (!file_exists($dir . "/backups/" . $request->name)) {
            return ["Status" => "Error", "Message" => "File doesn't exist!"];
        }

        unlink($dir . $request->name);
        return $logFiles;
    }

    function copy(Request $request)
    {
        $user = Auth()->user();
        $dir = storage_path('logs/');
        $logFiles = getMessageLogFiles();

        if (!Auth()->check() || $user->role->name !== "Super Admin") {
            return ["Status" => "Error", "Message" => "Unauthorized"];
        }

        if (file_exists($dir . "/backups/" . $request->name)) {
            return ["Status" => "Error", "Message" => "File is already backed up!"];
        }

        copy($dir . $request->name, $dir . "/backups/" . $request->name);

        return $logFiles;
    }
}
