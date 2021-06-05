<?php 

namespace BorumForum\DBHandlers;

class SettingsHandler {

    private UserKnownHandler $dbChecker;

    function __construct($userApiKey) {
        $this->dbChecker = new UserKnownHandler($userApiKey);    
    }

    /**
     * Updates a user's sign in
     * @param string $newPassword The new password
     * @return Array Output with statusCode 201 if everything ran without error, 500 otherwise
     */
    public function updateSignIn($newPassword) {

        $sanitizedNewPassword = mysqli_real_escape_string($this->conn, trim($newPassword));
        $this->dbChecker->executeQuery("
        UPDATE users SET pass = SHA2('$sanitizedNewPassword', 512) 
        WHERE id = " . $this->userId . " LIMIT 1
        ");
        
        if ($this->dbChecker->lastQueryWasSuccessful()) {
            return [
                "statusCode" => 201
            ];
        } else if ($this->dbChecker->lastQueryAffectedNoRows()) {
            return [
                "statusCode" => 400,
                "error" => [
                    "message" => "That password is the same as the old password"
                ]
            ];
        } else {
            return [
                "statusCode" => 500,
                "error" => [
                    "message" => "A system error occurred"
                ]
            ];
        }

    }

    public function toggleDarkMode($newValue) {
        
    }
}

?>