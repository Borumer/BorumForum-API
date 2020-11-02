<?php 

namespace BorumForum\DBHandlers;

use VarunS\BorumSleep\DBHandlers\UserKnownHandler;

class PostHandler extends UserKnownHandler {
    public function __construct($userApiKey) {
        parent::__construct($userApiKey);
    }
}

?>