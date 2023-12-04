<?php
if (!function_exists('getMessageLogFiles')) {
    function getMessageLogFiles()
    {
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
