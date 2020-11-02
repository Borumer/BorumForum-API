<?php 

namespace BorumForum\DBHandlers;

use VarunS\BorumSleep\DBHandlers\DBHandler as BorumSleepDBHandler;

/**
 * Base class in namespace for DBHandlers where the user is not known
 */
class UserNotKnownHandler extends BorumSleepDBHandler {
    function __construct() {
        parent::__construct($_ENV["DB_USERNAME"], $_ENV["DB_PASSWORD"], $_ENV["DB_HOST"], $_ENV["DB_NAME"]);
    }
}

?>