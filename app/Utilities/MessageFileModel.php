<?php

namespace App\Utilities;

class MessageFileModel extends FileModel
{
    function __construct($file)
    {
        parent::__construct($file, self::$attributes, self::$encrypted);
    }

    static public $attributes = [
        "app_id",
        "ip_address",
        "event_type",
        "model",
        "route",
        "user_email"
    ];

    static public $encrypted = [
        "app_id",
        "user_email",
        "ip_address"
    ];
}
