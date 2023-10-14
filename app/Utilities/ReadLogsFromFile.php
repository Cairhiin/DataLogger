<?php

namespace App\Utilities;

class ReadLogsFromFile
{
    const PAGINATE = 15;

    /**
     * Reads messages from a log file and paginates the data.
     * Returns an array of results and links.
     *
     * @param Int $page
     * @param Object $user
     * @return Array ["links" => $links, "messages" => messages]
     */
    public static function formatAndPaginate($page, $user)
    {
        $enc_key = $user->encryption_key;
        $hasAccess = $user->role->name == "Super Admin" ? true : false;
        $page = ($page == 0 || $page == "undefined") ? 1 : $page;
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

        // Reverse the logfile to get newest data first
        $data = [];
        foreach ($logFile as $line) {
            $data[] = $line;
        }
        $logFile = array_reverse($data);

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

        // Reverse the array to get newest messages first
        return ["links" => $links, "messages" => $messageCollection];
    }
}
