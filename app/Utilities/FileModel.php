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
            $obj->id = $line_num;

            $this->results[] = $obj;
        }

        return $this;
    }

    public function numberOfRecords()
    {
        return $this->results ? count($this->results) : count($this->file);
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

    public function orderBy($attribute, $direction)
    {
        if ($attribute == 'date' && $direction == 'DESC') {
            $this->results = array_reverse($this->results);
        } else {
            usort($this->results, function ($a, $b) use ($attribute, $direction) {
                if ($direction == 'ASC') {
                    return strcmp($a->$attribute, $b->$attribute);
                } else {
                    return strcmp($b->$attribute, $a->$attribute);
                }
            });
        }

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
