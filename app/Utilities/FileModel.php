<?php

namespace App\Utilities;

use Illuminate\Http\Client\Request;

class FileModel
{
    private array $attributes;
    private array $encrypted;
    private $file;
    private array $results;
    private $hasAccess;

    function __construct($file, $attributes = [], $encrypted = [])
    {
        $this->attributes = $attributes;
        $this->encrypted = $encrypted;
        $this->hasAccess = request()->user()->role != "Member";
        $this->file = $file;
    }

    public function all()
    {
        foreach ($this->file as $line_num => $line) {
            $obj = new \stdClass;

            // Remove the first part of the string to get the logged data
            $s = explode(": ", $line);
            $s = $s[1];
            $content = unserialize(trim($s));

            // Get the timestamp
            $date = substr(explode(']', $line)[0], 1);

            // Skip this line if the user is not an admin and the email doesn't match
            if (!$this->hasAccess && Encryption::decryptUsingKey(request()->user()->encryption_key, $content["user_email"]) != request()->user()->email) {
                continue;
            }

            // Decrypt App identifier and IP and add an id and created_at field
            foreach ($this->attributes as $attribute) {
                if (array_key_exists($attribute, $content)) {
                    if (in_array($attribute, $this->encrypted)) {
                        $obj->$attribute = Encryption::decryptUsingKey(request()->user()->encryption_key, $content[$attribute]);
                    } else {
                        $obj->$attribute = $content[$attribute];
                    }
                }
            }

            $obj->created_at = $date;
            $obj->id = $line_num;

            $this->results[] = $obj;
        }

        return $this;
    }

    public function numberOfRecords()
    {
        return count($this->file);
    }

    public function paginate($perPage = 15)
    {
        $links = [];
        $links[] = array("url" => (request()->page == null || request()->page == 1) ? null : '/event/messages?page=' . request()->page - 1, "label" => "previous");

        for ($page = 1; $page <= $this->getNumberOfPages($perPage); $page++) {
            $links[] = array("url" => '/event/messages?page=' . $page, "label" => $page);
        }

        $links[] = array("url" => request()->page >= $this->getNumberOfPages($perPage) ? null : '/event/messages?page=' . request()->page + 1, "label" => "next");

        return ["links" => $links, "messages" => array_slice($this->results, (request()->page - 1) * $perPage, $perPage)];
    }

    private function getNumberOfPages($perPage)
    {
        return ceil($this->numberOfRecords() / $perPage);
    }

    public function getResults()
    {
        return $this->results;
    }

    public function orderBy($attribute, $direction)
    {
        switch ($attribute) {
            case 'date':
                if ($direction = 'DESC') {
                    $this->results = array_reverse($this->results);
                }
                break;

            default:
                $this->results = array_reverse($this->results);
                break;
        }

        return $this;
    }
}
