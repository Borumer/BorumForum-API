<?php 

namespace BorumForum\DBHandlers;
use VarunS\BorumSleep\DBHandlers\UserKnownHandler;

class UserHandler extends UserKnownHandler {
    function __construct($userApiKey) {
        parent::__construct($userApiKey, $_ENV["DB_USERNAME"], $_ENV["DB_PASSWORD"], $_ENV["DB_HOST"], $_ENV["DB_NAME"]);    
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

    /**
     * Updates a user's sign in
     * @param string $newPassword The new password
     * @return Array statusCode 200 if everything ran without error, 500 otherwise
     */
    public function updateSignIn($newPassword) {
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