<?php

namespace App\Utilities;

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

    public function get($amount)
    {
        $this->results = array_slice($this->results, 0, $amount - 1);

        return $this;
    }

    private function formatMessage($message)
    {
        // Remove the first part of the string to get the logged data
        $s = explode("###", $message);
        $s = $s[1];

        // Get the timestamp
        $date = substr(explode(']', $message)[0], 1);

        return ["date" => $date, "message" => json_decode(trim($s), true)];
    }

    private function assignAttributes($data, $index)
    {
        $obj = new \stdClass;
        $content = $data["message"];
        $date = $data["date"];

        // Skip this data if the user is not an admin and the email doesn't match
        if (!$this->hasAccess && Encryption::decryptUsingKey(request()->user()->encryption_key, $content["user_email"]) != request()->user()->email) {
            return;
        }

        // Assign the attributes to the result object and decrypt those that need decrypting
        foreach ($this->attributes as $attribute) {
            if (array_key_exists($attribute, $content)) {
                if (in_array($attribute, $this->encrypted)) {
                    $obj->$attribute = Encryption::decryptUsingKey(request()->user()->encryption_key, $content[$attribute]);
                } else {
                    $obj->$attribute = $content[$attribute];
                }
            }
        }

        // add an id and created_at field
        $obj->created_at = $date;
        $obj->id = $index;

        $this->results[] = $obj;
    }

    public function all()
    {
        $this->results = [];
        foreach ($this->file as $line_num => $line) {
            $data = $this->formatMessage($line);
            $this->assignAttributes($data, $line_num);
        }

        $this->results = array_reverse($this->results);
        return $this;
    }

    public function numberOfRecords()
    {
        return !empty($this->results) ? count($this->results) : count($this->file);
    }

    public function paginate($perPage = 15)
    {
        $links = [];
        $links[] = array("url" => (request()->page == null || request()->page == 1) ? null : '?page=' . request()->page - 1, "label" => "previous");

        for ($page = 1; $page <= $this->getNumberOfPages($perPage); $page++) {
            $links[] = array("url" => '?page=' . $page, "label" => $page);
        }

        $links[] = array("url" => request()->page >= $this->getNumberOfPages($perPage) ? null : '?page=' . request()->page + 1, "label" => "next");

        return ["links" => $links, "messages" => array_slice($this->results, (request()->page - 1) * $perPage, $perPage)];
    }

    private function getNumberOfPages($perPage)
    {
        return ceil($this->numberOfRecords() / $perPage);
    }

    public function orderBy($attribute, $direction)
    {
        usort($this->results, function ($a, $b) use ($attribute, $direction) {
            if ($direction == 'ASC') {
                return strcmp($a->$attribute, $b->$attribute);
            } else {
                return strcmp($b->$attribute, $a->$attribute);
            }
        });

        return $this;
    }

    public function filterBy($attribute, $value)
    {
        $this->results = array_filter($this->results, function ($result) use ($attribute, $value) {
            return $result->$attribute == $value;
        });

        return $this;
    }
}
