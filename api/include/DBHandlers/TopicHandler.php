<?php 

namespace BorumForum\DBHandlers;

use VarunS\BorumSleep\DBHandlers\UserKnownHandler;

class TopicHandler extends UserKnownHandler implements Deleteable {
    public function __construct($userApiKey) {
        parent::__construct($userApiKey);
    }

    public function delete($id) {

    }

    public function follow($id) {

    }

    public function ignore($id) {

    }
}

?>