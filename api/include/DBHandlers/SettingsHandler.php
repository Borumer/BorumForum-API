<?php 

namespace BorumForum\DBHandlers;

class SettingsHandler extends UserKnownHandler {
    function __construct($userApiKey) {
        parent::__construct($userApiKey);    
    }

    /**
     * Updates a user's sign in
     * @param string $newPassword The new password
     * @return Array statusCode 200 if everything ran without error, 500 otherwise
     */
    public function updateSignIn($newPassword) {
        $sanitizedNewPassword = mysqli_real_escape_string($this->conn, trim($newPassword));
        $this->executeQuery("
        UPDATE users SET pass = SHA2('$sanitizedNewPassword', 512) 
        WHERE id = " . $this->userId . " LIMIT 1
        ");

        return [
            "statusCode" => 200
        ];
    }

    public function toggleDarkMode($newValue) {

    }
}

?>