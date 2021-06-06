<?php 

namespace BorumForum\DBHandlers;

class SettingsHandler {

    private UserKnownHandler $dbChecker;

    function __construct($userApiKey) {
        $this->dbChecker = new UserKnownHandler($userApiKey);    
    }

    /**
     * Updates a user's sign in
     * @param string $oldPassword The old password
     * @param string $newPassword The new password
     * @return Array Output with statusCode 201 if everything ran without error, 500 otherwise
     */
    public function updateSignIn($oldPassword, $newPassword) {
        $sanitizedOldPassword = $this->dbChecker->sanitizeParam($oldPassword);
        $sanitizedNewPassword = $this->dbChecker->sanitizeParam($newPassword);

        $comparePasswords = $this->dbChecker->executeQuery("SELECT SHA2('$sanitizedOldPassword', 512), pass FROM users WHERE id = " . $this->dbChecker->userId);

        $comparePasswords = mysqli_fetch_array($comparePasswords);
        $oldPasswordIsCorrect = $comparePasswords[0] == $comparePasswords[1];

        if ($oldPasswordIsCorrect) {
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
        } else {
            return [
                "statusCode" => 403,
                "error" => [
                    "message" => "The current password is not correct. If you do not remember your current password, log out and click 'Forgot Password'"
                ]
            ];
        }
    }

    public function toggleDarkMode($newValue) {
        
    }
}

?>