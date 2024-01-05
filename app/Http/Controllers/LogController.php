<?php

namespace App\Http\Controllers;

use App\Exceptions\CustomException;
use Throwable;
use Inertia\Inertia;
use App\Models\LogEntry;
use Illuminate\Http\Request;
use App\Utilities\Encryption;
use Illuminate\Support\Carbon;

class LogController extends Controller
{
    public function store(Request $request)
    {
        $enc_key = '';

        // Check if the user is allowed to create new log entries
        if (!$request->user()->tokenCan('log:create')) {
            return ["Status" => "Error", "Message" => "Not allowed to create new log events!"];
        }

        // Validate the request before encrypting it
        $validated = $request->validate([
            'original_data' => 'string|nullable',
            'new_data' => 'string|nullable',
            'ip' => 'string|required',
        ]);

        $enc_key = $this->getEncryptionKey($request);

        $log = [
            'model' => $request->model ?? 'Unspecified',
            'original_data' => $request->original_data ? Encryption::encryptUsingKey($enc_key, $request->original_data) : null,
            'new_data' => $request->new_data ? Encryption::encryptUsingKey($enc_key, $request->new_data) : null,
            'app_id' => $request->app_id ? Encryption::encryptUsingKey($enc_key, $request->app_id) : Encryption::encryptUsingKey($enc_key, 'Default'),
            'event_type' => $request->event_type ?? 'create',
            'route' => $request->route ?? 'dashboard',
            'ip_address' => Encryption::encryptUsingKey($enc_key, $request->ip),
            'user_id' => $request->user()->id,
            'date' => $request->date ?? date("Y-m-d H:i:s"),
            'remote_user_id' => $request->user ?? 1
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
                return $query->where('user_id', $user->id);
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
            ->orderBy('date', 'DESC')
            ->paginate(15);

        $enc_key = $this->getEncryptionKey($request);

        foreach ($logs as $log) {
            $log->app_id = Encryption::decryptUsingKey($enc_key, $log->app_id);
            $log->ip_address = Encryption::decryptUsingKey($enc_key, $log->ip_address);
            $log->user;
        }

        return Inertia::render('Event/Logs', [
            'logs' => $logs,
        ]);
    }

    public function show(Request $request)
    {
        try {
            $log = LogEntry::findOrFail($request->id);
            $enc_key = $this->getEncryptionKey($request);

            $log->original_data = Encryption::decryptUsingKey($enc_key, $log->original_data);
            $log->new_data = Encryption::decryptUsingKey($enc_key, $log->new_data);

            $log->user;
        } catch (CustomException $e) {
            return  $e->render("There was an error retrieving the requested resource!");
        }

        return $log;
    }

    public function destroy(Request $request)
    {
        if ($request->user()->tokenCan('log:delete')) {
            try {
                $log = LogEntry::findOrFail($request->id);
                $log->forceDelete();
            } catch (CustomException $e) {
                throw new CustomException("There was an error retrieving the requested resource!");
            }
        } else {
            throw new CustomException("You are not authorized to make this request!");
        }

        return $request->id;
    }

    public function indexByDateRange(Request $request)
    {
        $from = date($request->from);
        $to = date($request->to);
        $user = Auth()->user();

        try {
            $logs = LogEntry::query()
                ->whereBetween('date', [$from, $to])
                ->when($user->role->name == "member", function ($query) use ($user) {
                    // If the user is just a member only show results that are theirs
                    return $query->where('user_id', $user->id);
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
                ->orderBy('date', 'DESC')
                ->paginate(15);

            $enc_key = $this->getEncryptionKey($request);

            foreach ($logs as $log) {
                $log->app_id = Encryption::decryptUsingKey($enc_key, $log->app_id);
                $log->ip_address = Encryption::decryptUsingKey($enc_key, $log->ip_address);
                $log->user;
            }
        } catch (CustomException $e) {
            throw new CustomException("The requested resource was not found!");
        }

        return $logs;
    }
}
