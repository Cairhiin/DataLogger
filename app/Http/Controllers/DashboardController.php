<?php

namespace App\Http\Controllers;

use App\Models\User;
use Inertia\Inertia;
use App\Models\LogEntry;
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
            $messagesDate = date("d-m-Y", filemtime($logFiles[0]["name"]));
        }

        if ($this->roleAuthentication()) {
            $users = User::latest()->take(5)->get();
            foreach ($users as $user) {
                $user->role->name;
            }
        } else {
            $users = null;
        }

        $logs = LogEntry::latest()->take(5)->get();

        return Inertia::render('Dashboard', [
            'users' => $users,
            'numberOfUsers' => $this->numberOfUsers(),
            'logs' => $logs,
            'numberOfLogs' => $this->numberOfLogs(),
            'messages' => $messages,
            'numberOfMessages' => $this->numberOfMessages(),
            'messageDate' => $messagesDate
        ]);
    }

    private function numberOfLogs()
    {
        $logs = LogEntry::whereYear('date', Carbon::now()->year)
            ->whereMonth('date', Carbon::now()->month)
            ->WhereDay('date', Carbon::now()->day)
            ->get();

        return $logs->count();
    }

    private function numberOfMessages()
    {
        $logFiles = getMessageLogFiles();
        $numberOfmessages = 0;

        foreach ($logFiles as $logFile) {
            $data = new MessageFileModel($logFile["name"]);
            $numberOfmessages += $data->numberOfRecords();
        }

        return $numberOfmessages;
    }

    private function numberOfUsers()
    {
        if ($this->roleAuthentication()) {
            $users = User::get();
            return $users->count();
        }

        return 0;
    }
}
