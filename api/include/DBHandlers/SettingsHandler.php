<?php

namespace BorumForum\DBHandlers;

class SettingsHandler
{

    private UserKnownHandler $dbChecker;

    function __construct($userApiKey)
    {
        $this->dbChecker = new UserKnownHandler($userApiKey);
    }

    public function getActivatedApps() {
        $jot = $this->dbChecker->executeQuery("SELECT borum_user_id FROM Jottings.users WHERE borum_user_id = " . $this->dbChecker->userId);
        
        return [
            "statusCode" => 200,
            "data" => [
                "jot" => mysqli_num_rows($jot) >= 1
            ]
        ];
    }

    /**
     * Updates a user's sign in
     * @param string $oldPassword The old password
     * @param string $newPassword The new password
     * @return Array Output with statusCode 201 if everything ran without error, 500 otherwise
     */
    public function changePasswordWithOldPassword($oldPassword, $newPassword)
    {
        $sanitizedOldPassword = $this->dbChecker->sanitizeParam($oldPassword);
        $sanitizedNewPassword = $this->dbChecker->sanitizeParam($newPassword);

        $comparePasswords = $this->dbChecker->executeQuery("SELECT SHA2('$sanitizedOldPassword', 512), pass FROM users WHERE id = " . $this->dbChecker->userId);

        $comparePasswords = mysqli_fetch_array($comparePasswords);
        $oldPasswordIsCorrect = $comparePasswords[0] == $comparePasswords[1];

        if ($oldPasswordIsCorrect) {
            $this->updateSignIn($sanitizedNewPassword);
        } else {
            return [
                "statusCode" => 403,
                "error" => [
                    "message" => "The current password is not correct. If you do not remember your current password, log out and click 'Forgot Password'"
                ]
            ];
        }
    }

    public function changePasswordWithCode($newPassword, $activationCode, $email) {
        $sanitizedNewPassword = $this->dbChecker->sanitizeParam($newPassword);
        
        if ($email != $this->dbChecker->userArray["email"]) {
            return [
                "statusCode" => 401,
                "error" => [
                    "message" => "The email and key do not match"
                ]
            ];
        }

        $passwordReset = $this->dbChecker->executeQuery("SELECT * FROM `password-resets` WHERE code = \"$activationCode\" AND email = \"$email\" LIMIT 1");
        $passwordResetSuccess = mysqli_num_rows($passwordReset) == 1;

        if ($passwordResetSuccess)
            return $this->updateSignIn($sanitizedNewPassword);        

        return [
            "statusCode" => 500,
            "error" => [
                "message" => "A server error occurred and the password could not be changed"
            ]
        ];
    }

    private function updateSignIn($sanitizedNewPassword) {
        $query = "
        UPDATE users SET pass = SHA2('$sanitizedNewPassword', 512) 
        WHERE id = " . $this->dbChecker->userId . " LIMIT 1
        ";

        $this->dbChecker->executeQuery($query);

        if ($this->dbChecker->lastQueryWasSuccessful()) {
            return [
                "statusCode" => 204
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

    public function deleteAccount()
    {
        $this->dbChecker->beginTransaction();

        // Remove Borum Jot data
        $this->dbChecker->executeQuery("DELETE FROM Jottings.users WHERE user_id = " . $this->dbChecker->userId);
        $this->dbChecker->executeQuery("DELETE FROM Jottings.notes WHERE user_id = " . $this->dbChecker->userId);
        $this->dbChecker->executeQuery("DELETE FROM Jottings.tasks WHERE user_id = " . $this->dbChecker->userId);

        // Remove Borum account data
        $this->dbChecker->executeQuery("DELETE FROM firstborumdatabase.users WHERE id = " . $this->dbChecker->userId);
    }

    public function toggleDarkMode($newValue)
    {
    }
}
