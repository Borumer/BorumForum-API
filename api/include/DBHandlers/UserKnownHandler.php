<?php 

namespace BorumForum\DBHandlers;

use VarunS\PHPSleep\DBHandlers\UserKnownHandler as PHPSleepUserKnownHandler;

/**
 * Base class in namespace for DBHandlers that require the user's api key
 */
class UserKnownHandler extends PHPSleepUserKnownHandler {
    function __construct($userApiKey) {
        parent::__construct($userApiKey, $_ENV["DB_USERNAME"], $_ENV["DB_PASSWORD"], $_ENV["DB_HOST"], $_ENV["DB_NAME"]);
    }
}

?>