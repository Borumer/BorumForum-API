<?php 

namespace BorumForum\DBHandlers;

use VarunS\BorumSleep\DBHandlers\UserKnownHandler as BorumSleepUserKnownHandler;

/**
 * Base class in namespace for DBHandlers that require the user's api key
 */
class UserKnownHandler extends BorumSleepUserKnownHandler {
    function __construct($userApiKey) {
        parent::__construct($userApiKey, $_ENV["DB_USERNAME"], $_ENV["DB_PASSWORD"], $_ENV["DB_HOST"], $_ENV["DB_NAME"]);
    }
}

?>