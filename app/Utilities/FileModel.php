<?php

namespace App\Utilities;

use App\Models\User;

class FileModel
{
    private array $attributes;
    private array $encrypted;
    private $file;
    private string $filename;
    private array $results;
    private string $filter;
    private string $filterValue;
    private string $customerIdentifier;
    private $hasAccess;

    function __construct($file, $identifier, $attributes = [], $encrypted = [], $access = [])
    {
        $this->attributes = $attributes;
        $this->encrypted = $encrypted;
        $this->customerIdentifier = $identifier;
        $this->hasAccess = empty($access) || in_array(request()->user()->role->name, $access);
        $this->file = file($file);
        $this->filename = basename($file);
        $this->filter = "";
        $this->filterValue = "";
        $this->results = [];
    }

    public function get($id)
    {
        $results = array_filter($this->results, function ($result) use ($id) {
            return $id == $result->id;
        });

        $result = new \stdClass;

        if (!empty($results)) {
            $result = reset($results);
        }

        return $result;
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
        if (!$this->hasAccess && array_key_exists($this->customerIdentifier, $content) && request()->user()->id != $content[$this->customerIdentifier]) {
            return;
        } else {
            $content["user"] = User::find($content["user_id"]);
        }

        if (!array_key_exists("remote_user_id", $content)) {
            $content["remote_user_id"] = User::find($content["user_id"])->id;
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

        // add date field
        $obj->created_at = $date;

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

    public function getRecords($amount = null)
    {
        return $amount ? array_slice($this->results, 0, $amount) : $this->results;
    }

    public function numberOfRecords()
    {
        return count($this->results);
    }

    public function paginate($perPage = 15)
    {
        $links = [];
        if ($this->filter !== '') {
            $links[] = array("url" => (request()->page == null || request()->page == 1) ?
                null : '?page=' . request()->page - 1 . '&' . $this->filter . '&file=' . $this->filename, "label" => "previous");
        } else {
            $links[] = array("url" => (request()->page == null || request()->page == 1) ? null : '?page=' . request()->page - 1 . '&file=' . $this->filename, "label" => "previous");
        }

        for ($page = 1; $page <= $this->getNumberOfPages($perPage); $page++) {
            if ($this->filter !== '') {
                $links[] = array("url" => '?page=' . $page . '&' . $this->filter . '=' . $this->filterValue . '&file=' . $this->filename, "label" => $page);
            } else {
                $links[] = array("url" => '?page=' . $page . '&file=' . $this->filename, "label" => $page);
            }
        }

        if ($this->filter !== '') {
            $links[] = array("url" => request()->page >= $this->getNumberOfPages($perPage) ? null :
                '?page=' . request()->page + 1 . '&' . $this->filter . '=' . $this->filterValue . '&file=' . $this->filename, "label" => "next");
        } else {
            $links[] = array("url" => request()->page >= $this->getNumberOfPages($perPage) ? null : '?page=' . request()->page + 1 . '&file=' . $this->filename, "label" => "next");
        }

        return ["links" => $links, "messages" => array_slice($this->results, (request()->page - 1) * $perPage, $perPage)];
    }

    private function getNumberOfPages($perPage)
    {
        return ceil($this->numberOfRecords() / $perPage);
    }

    public function orderBy($attribute, $direction)
    {
        usort($this->results, function ($a, $b) use ($attribute, $direction) {
            if (strtoupper($direction) == 'ASC') {
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

        $this->filter = $attribute;
        $this->filterValue = $value;
        return $this;
    }

    public function getUniqueValuesPerAttribute($array, $attribute)
    {
        $values = [];
        foreach ($array as $obj) {
            $values[] = $obj->$attribute;
        }

        return array_values(array_unique($values));
    }

    public function getUniqueValues()
    {
        foreach ($this->attributes as $attribute) {
            $uniqueValues[$attribute] = $this->getUniqueValuesPerAttribute($this->results, $attribute);
        }

        return $uniqueValues;
    }

    public function getUniqueResults($attribute)
    {
        $this->results = array_filter($this->results, function ($result) use ($attribute) {
            return $result[$attribute];
        });

        return $this;
    }
}
