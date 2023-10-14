<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use Illuminate\Http\Request;
use App\Utilities\Encryption;

class MessageController extends Controller
{
    const PAGINATE = 15;

    public function store(Request $request)
    {
        $enc_key = "";

        // Check if the user is allowed to create new log entries
        if ($request->user()->tokenCan('message:create')) {
            $enc_key = $request->user()->encryption_key;
        } else {
            return ["Status" => "Error", "Message" => "Not allowed to create new message events!"];
        }

        if ($enc_key == '' || $enc_key == null) {
            return ["Status" => "Error", "Message" => "No encryption key set!"];
        }

        // Validate the request before encrypting it
        $validated = $request->validate([
            'app_id' => 'required',
            'route' => 'required',
            'event_type' => 'required',
            'user_email' => 'required|email',
            'ip' => 'required'
        ]);

        $message = [
            'model' => $request->model,
            'app_id' => Encryption::encryptUsingKey($enc_key, $request->app_id),
            'event_type' => $request->event_type,
            'route' => $request->route,
            'user_email' => Encryption::encryptUsingKey($enc_key, $request->user_email),
            'ip_address' => Encryption::encryptUsingKey($enc_key, $request->ip)
        ];

        // Serialize the log data and publish it on the RabbitMQ stream
        $mqService = new \App\Services\RabbitMQService();
        $mqService->publish(serialize($message), 'url');
    }

    public function index(Request $request)
    {
        $user = Auth()->user();
        $enc_key = $user->encryption_key;
        $hasAccess = $user->role->name == "Super Admin" ? true : false;
        $page = ($request->page == 0 || $request->page == "undefined") ? 1 : $request->page;
        $lineStart = ($page - 1) * self::PAGINATE;

        $logFile = file(storage_path() . '/logs/user-data.log');
        $messageCollection = [];
        $links = [];
        $numberOfLinks = 1;
        $numberOfResults = 0;
        $prev = $page == 1 ? null : '/event/messages?page=' . $page - 1;

        // Set the prev labelled link
        $links[] = array("url" => $prev, "label" => "previous");
        $links[] = array("url" => "/event/messages?page=" . 1, "label" => 1);

        foreach ($logFile as $line_num => $line) {
            if (($line_num + 1) % 15 == 0) {
                $numberOfLinks++;
                $links[] = array("url" => "/event/messages?page=" . $numberOfLinks, "label" => $numberOfLinks);
            }

            // Remove the first part of the string to get the logged data
            $s = explode(": ", $line);
            $s = $s[1];
            $content = unserialize(trim($s));

            // Get the timestamp
            $date = substr(explode(']', $line)[0], 1);

            // Skip this line if the user is not an admin and the email doesn't match
            if (!$hasAccess && Encryption::decryptUsingKey($enc_key, $content["user_email"]) != $user->email) {
                continue;
            }

            // Increase number of results for pagination
            $numberOfResults++;

            // If we are outside the paginated results we skip to next iteration
            if ($line_num < $lineStart || $line_num >= $lineStart + self::PAGINATE) {
                continue;
            }

            // Remove the email from the data that is being sent to the frontend for privacy reasons
            unset($content['user_email']);

            // Decrypt App identifier and IP and add an id and created_at field
            $content["app_id"] = Encryption::decryptUsingKey($enc_key, $content["app_id"]);
            $content["ip_address"] = Encryption::decryptUsingKey($enc_key, $content["ip_address"]);
            $content["created_at"] = $date;
            $content["id"] = $line_num;

            $messageCollection[] = $content;
        }

        // Set the next labelled link
        $next = $page  * 15 >= $numberOfResults ? null : '/event/messages?page=' . $page + 1;
        $links[] = array("url" => $next, "label" => "next");

        return Inertia::render('Event/Messages', [
            'messages' => $messageCollection,
            'links' => $links
        ]);
    }
}
