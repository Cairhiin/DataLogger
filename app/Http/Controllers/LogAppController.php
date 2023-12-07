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
    public function index(Request $request)
    {
        $logs = LogEntry::select('app_id')->distinct()->get();

        $enc_key = $this->getEncryptionKey($request);

        foreach ($logs as $log) {
            $log->app_id = Encryption::decryptUsingKey($enc_key, $log->app_id);
        }

        return $logs->map->only(['app_id']);
    }
}
