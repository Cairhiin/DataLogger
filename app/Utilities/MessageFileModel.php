<?php

namespace App\Utilities;

use App\Models\Role;

class MessageFileModel extends FileModel
{
    function __construct($file)
    {
        parent::__construct($file, self::$identifier, self::$attributes, self::$encrypted, self::$access);
    }

    static public $attributes = [
        "id",
        "app_id",
        "ip_address",
        "route",
        "user_email",
        "user",
        "name"
    ];

    static public $encrypted = [
        "app_id",
        "ip_address"
    ];

    static public $access = [
        "Super Admin",
        "Admin"
    ];

    static public $identifier = "user_email";
}
