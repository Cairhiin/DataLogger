<?php

namespace App\Http\Controllers;

use App\Models\User;
use Inertia\Inertia;
use App\Models\LogEntry;
use App\Utilities\Encryption;
use Illuminate\Support\Carbon;
use App\Utilities\MessageFileModel;
use Illuminate\Http\Request;


class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $messages = [];
        $messagesDate = now();

        $logFiles = getMessageLogFiles();
        if (!empty($logFiles)) {
            $data = new MessageFileModel($logFiles[0]["name"]);
            $messages = $data->all()->getRecords(5);

            $enc_key = $this->getEncryptionKey($request);

            foreach ($messages as $message) {
                $message->app_id = Encryption::decryptUsingKey($enc_key, $message->app_id);
                $message->ip_address = Encryption::decryptUsingKey($enc_key, $message->ip_address);
            }

            $messagesDate = date("d-m-Y", filemtime($logFiles[0]["name"]));
        }

        $users = User::latest()->take(5)->get();
        foreach ($users as $user) {
            $user->role->name;
        }

        $logs = LogEntry::latest()->take(5)->get();

        return Inertia::render('Dashboard', [
            'users' => $users,
            'logs' => $logs,
            'numberOfLogs' => $this->numberOfLogs(),
            'messages' => $messages,
            'numberOfMessages' => $this->numberOfMessages(),
            'messageDate' => $messagesDate
        ]);
    }

    public function numberOfLogs()
    {
        $logs = LogEntry::whereYear('date', Carbon::now()->year)
            ->whereMonth('date', Carbon::now()->month)
            ->get();

        return $logs->count();
    }

    public function numberOfMessages()
    {
        $logFiles = getMessageLogFiles();
        $numberOfmessages = 0;

        foreach ($logFiles as $logFile) {
            $data = new MessageFileModel($logFile["name"]);
            $numberOfmessages += $data->numberOfRecords();
        }

        return $numberOfmessages;
    }
}
