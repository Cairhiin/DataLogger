<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class FileController extends Controller
{
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
