<?php 

namespace BorumForum\DBHandlers;
use VarunS\BorumSleep\DBHandlers\UserKnownHandler;

class UserHandler extends UserKnownHandler {
    function __construct($userApiKey) {
        parent::__construct($userApiKey);    
    }

    function createNewUser($email, $password) {
        return [
            "statusCode" => 200
        ];
    }

    /**
     * @param string $newPassword The new password
     */
    function updateSignIn($newPassword) {
        $sanitizedNewPassword = mysqli_real_escape_string($this->conn, trim($newPassword));
        $this->executeQuery("
        UPDATE users SET password = SHA2('$sanitizedNewPassword', 512) 
        WHERE id = " . $this->userId . "  
        LIMIT 1
        ");

        return [
            "statusCode" => 200
        ];
    }
}

?>