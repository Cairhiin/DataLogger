<?php

namespace App\Http\Controllers;

use App\Models\LogEntry;
use App\Models\User;
use Inertia\Inertia;


class DashboardController extends Controller
{
    public function index()
    {
        $users = User::latest()->take(10)->get();
        foreach ($users as $user) {
            $user->role->name;
        }

        $logs = LogEntry::latest()->take(10)->get();
        return Inertia::render('Dashboard', [
            'users' => $users,
            'logs' => $logs
        ]);
    }
}
