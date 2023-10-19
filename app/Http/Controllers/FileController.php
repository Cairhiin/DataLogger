<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class FileController extends Controller
{
    function delete()
    {
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

        $logFiles = [];
        $dir = storage_path('logs/');

        foreach (glob($dir . '[user-data-]*.log') as $filename) {
            $strippedFileName = explode('/', $filename)[1];
            if (file_exists($dir . "/backups/" . $strippedFileName)) {
                $fileData = ["name" => $filename, "backup" => true];
            } else {
                $fileData = ["name" => $filename, "backup" => false];
            }
            $logFiles[] = $fileData;
        }

        // Sort by newest logfile first
        usort(
            $logFiles,
            function ($file1, $file2) {
                return filemtime($file1["name"]) < filemtime($file2["name"]);
            }
        );

        return $logFiles;
    }
}
