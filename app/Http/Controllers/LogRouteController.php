<?php

namespace App\Http\Controllers;

use App\Models\LogEntry;
use Illuminate\Http\Request;

class LogRouteController extends Controller
{
    public function index()
    {
        $logs = LogEntry::select('route')->distinct()->get();
        return $logs->map->only(['route']);
    }
}
