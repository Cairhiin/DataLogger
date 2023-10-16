<?php

namespace App\Http\Controllers;

use App\Models\LogEntry;
use Illuminate\Http\Request;

class LogModelController extends Controller
{
    public function index()
    {
        $logs = LogEntry::select('model')->distinct()->get();
        return $logs->map->only(['model']);
    }
}
