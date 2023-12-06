<?php

namespace App\Utilities;

use App\Models\Role;
use App\Models\User;

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
        "user_id",
        "user"
    ];

    // Ecnrypted fields
    static public $encrypted = [
        "app_id",
        "ip_address"
    ];

    // Super Admin access overwrites the customer id
    static public $access = [
        "Super Admin",
        "Admin"
    ];

    // Customer id to identify which messages belong to a certain customer
    static public $identifier = "user_id";
}
