<?php

namespace App\Http\Controllers;

use App\Models\User;
use Inertia\Inertia;
use App\Models\LogEntry;
use Illuminate\Support\Carbon;
use App\Utilities\MessageFileModel;


class DashboardController extends Controller
{
    public function index()
    {
        $messages = [];
        $messagesDate = now();
        $logFiles = getMessageLogFiles();
        if (!empty($logFiles)) {
            $data = new MessageFileModel($logFiles[0]["name"]);
            $messages = $data->all()->getRecords(5);
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
