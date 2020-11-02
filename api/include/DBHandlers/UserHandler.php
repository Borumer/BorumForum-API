<?php 

namespace BorumForum\DBHandlers;

class UserHandler extends UserNotKnownHandler {
    function __construct() {
        parent::__construct();
    }

    public function createNewUser() {
        return [
            "statusCode" => 200
        ];
    }

    public function getUser($email, $password) {
        return [
            "statusCode" => 200
        ];
    }
}

?>