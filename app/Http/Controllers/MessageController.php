<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use App\Utilities\MessageFileModel;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class MessageController extends Controller
{
    public function show(Request $request)
    {
        $dir = storage_path('logs/');
        $logFiles = getMessageLogFiles();

        if ($request->name != null && file_exists($dir . $request->name)) {
            $data = new MessageFileModel($dir . $request->name);
        } else if (!empty($logFiles)) {
            $data = new MessageFileModel($logFiles[0]["name"]);
        }

        if ($data == null || empty($data)) {
            return  [
                'error' => ["status" => "404 Not Found", "message" => [
                    "header" => "The requested resources was not found!",
                    "info" => "There appears to be a problem finding the requested resource."
                ]]
            ];
        }

        $data = $data->all()->get($request->id);
        return $data;
    }
}
