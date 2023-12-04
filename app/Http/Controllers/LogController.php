<?php

namespace App\Http\Controllers;

use App\Models\User;
use Inertia\Inertia;
use App\Models\LogEntry;
use Illuminate\Http\Request;
use App\Utilities\Encryption;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class LogController extends Controller
{
    public function store(Request $request)
    {
        $enc_key = '';

        // Check if the user is allowed to create new log entries
        if ($request->user()->tokenCan('log:create')) {
            $enc_key = $request->user()->encryption_key;
        } else {
            return ["Status" => "Error", "Message" => "Not allowed to create new log events!"];
        }

        if ($enc_key == '') {
            return ["Status" => "Error", "Message" => "No encryption key set!"];
        }

        // Validate the request before encrypting it
        $validated = $request->validate([
            'original_data' => 'required',
            'new_data' => 'required',
            'app_id' => 'required',
            'model' => 'required',
            'route' => 'required',
            'event_type' => 'required',
            'user_email' => 'required|email',
            'ip' => 'required'
        ]);

        $log = [
            'model' => $request->model,
            'original_data' => Encryption::encryptUsingKey($enc_key, serialize($request->original_data)),
            'new_data' => Encryption::encryptUsingKey($enc_key, serialize($request->new_data)),
            'app_id' => Encryption::encryptUsingKey($enc_key, $request->app_id),
            'event_type' => $request->event_type,
            'route' => $request->route,
            'user_email' => Encryption::encryptUsingKey($enc_key, $request->user_email),
            'ip_address' => Encryption::encryptUsingKey($enc_key, $request->ip),
            'user' => $request->user()->id
        ];

        // Serialize the log data and publish it on the RabbitMQ stream
        $mqService = new \App\Services\RabbitMQService();
        $mqService->publish(serialize($log), 'log');
    }

    public function index(Request $request)
    {
        $user = Auth()->user();
        $logs = LogEntry::query()
            ->when($user->role->name == "member", function ($query) use ($user) {
                // If the user is just a member only show results that are theirs
                return $query->where('user_email', Encryption::encryptUsingKey($user->encryption_key, $user->email));
            })
            ->when($request->model, function ($query) use ($request) {
                return $query->where('model', '=', $request->model);
            })
            ->when($request->route, function ($query) use ($request) {
                return $query->where('route', '=', $request->route);
            })
            ->when($request->event, function ($query) use ($request) {
                return $query->where('event_type', '=', $request->event);
            })
            ->when($request->app, function ($query) use ($request, $user) {
                return $query->where('app_id', '=', Encryption::encryptUsingKey($user->encryption_key, $request->app));
            })
            ->orderBy('created_at', 'DESC')
            ->paginate(15);

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
            $log->ip_address = Encryption::decryptUsingKey($enc_key, $log->ip_address);
        }

        return Inertia::render('Event/Logs', [
            'logs' => $logs,
        ]);
    }

    public function show(Request $request)
    {
        try {
            $enc_key = User::findOrFail(Auth()->id())->encryption_key;
        } catch (ModelNotFoundException $e) {
            return [
                'error' => ["status" => "404 Not Found", "message" => [
                    "header" => "The requested resources was not found!",
                    "info" => "There appears to be a problem with your user account."
                ]],
            ];
        }

        if (!$enc_key || $enc_key == "") {
            return [
                'error' => ["status" => "400 Bad Request", "message" => [
                    "header" => "You do not have an encryption key!",
                    "info" => "Please contact an administrator for more information."
                ]]
            ];
        }

        try {
            $log = LogEntry::findOrFail($request->id);
        } catch (ModelNotFoundException $e) {
            return  [
                'error' => ["status" => "404 Not Found", "message" => [
                    "header" => "The requested resources was not found!",
                    "info" => "There appears to be a problem finding the requested resource."
                ]]
            ];
        }

        $log->original_data = unserialize(Encryption::decryptUsingKey($enc_key, $log->original_data));
        $log->new_data = unserialize(Encryption::decryptUsingKey($enc_key, $log->new_data));

        return $log;
    }

    public function destroy(Request $request)
    {
        if ($request->user()->tokenCan('log:delete')) {
            try {
                $log = LogEntry::findOrFail($request->id);
                $log->forceDelete();
            } catch (ModelNotFoundException $e) {
                return  [
                    'error' => ["status" => "404 Not Found", "message" => [
                        "header" => "The requested resources was not found!",
                        "info" => "There appears to be a problem finding the requested resource."
                    ]]
                ];
            }
        } else {
            return  [
                'error' => ["status" => "401 Unauthorized", "message" => [
                    "header" => "You do not have permission to delete log entries!",
                    "info" => "Please contact an administrator for more information."
                ]]
            ];
        }

        return $request->id;
    }
}
