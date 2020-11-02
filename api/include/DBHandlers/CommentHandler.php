<?php 

namespace BorumForum\DBHandlers;

use VarunS\BorumSleep\DBHandlers\DBHandler;

class CommentHandler extends DBHandler implements Deleteable {
    function __construct($userApiKey) {
        parent::__construct($userApiKey);
    }

    public function delete($id) {

    }

    public function insert($body) {

    }

    public function edit($id, $newBody) {

    }
}

?>