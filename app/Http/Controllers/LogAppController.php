<?php

namespace App\Http\Controllers;

use App\Models\User;
use Inertia\Inertia;
use App\Models\LogEntry;
use Illuminate\Http\Request;
use App\Utilities\Encryption;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class LogAppController extends Controller
{
    public function index()
    {
        $logs = LogEntry::select('app_id')->distinct()->get();

        try {
            $enc_key = User::findOrFail(Auth()->id())->encryption_key;
        } catch (ModelNotFoundException $e) {
            return Inertia::render('Error', [
                'error' => ["status" => "404 Not Found", "message" => [
                    "header" => "The requested resources was not found!",
                    "info" => "There appears to be a problem with your user account."
                ]],
            ]);
        }

        if (!$enc_key || $enc_key == "") {
            return Inertia::render('Error', [
                'error' => ["status" => "400 Bad Request", "message" => [
                    "header" => "You do not have an encryption key!",
                    "info" => "Please contact an administrator for more information."
                ]],
            ]);
        }

        foreach ($logs as $log) {
            $log->app_id = Encryption::decryptUsingKey($enc_key, $log->app_id);
        }

        return $logs->map->only(['app_id']);
    }
}
