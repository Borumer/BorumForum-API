<?php 

namespace BorumForum\DBHandlers;

class UserHandler extends UserNotKnownHandler {
    function __construct() {
        parent::__construct();
    }

    /**
     * Creates a new user if the email doesn't already exist
     * @param string $unsafeFirstName The raw given first name of the user
     * @param string $unsafeLastName The raw given last name of the user
     * @param string $unsafeEmail The raw given email of the user
     * @param string $unsafePassword The plaintext password of the new user
     */
    public function createNewUser($unsafeFirstName, $unsafeLastName, $unsafeEmail, $unsafePassword) {
        $firstName = $this->sanitizeParam($unsafeFirstName);
        $lastName = $this->sanitizeParam($unsafeLastName);
        $email = $this->sanitizeParam($unsafeEmail);
        $password = $this->sanitizeParam($unsafePassword);
        
        $emailExists = $this->executeQuery("SELECT email FROM users WHERE email = '$email'");
        $emailExists = mysqli_num_rows($emailExists) == 1;

        if ($emailExists) {
            return [
                "statusCode" => 409,
                "error" => [
                    "message" => "A user with that email already exists on Borum"
                ]
            ];
        }

        $apiKey = $this->generateApiKey();
        if ($this->userExists($apiKey)) {
            $apiKey = $this->generateApiKey();
        }

        $registrationQuery = "
        INSERT INTO users 
        (first_name, last_name, email, password, api_key, registration_date) 
        VALUES ('$firstName', '$lastName', '$email', SHA2('$password', 512), $apiKey, NOW())";
        
        $this->executeQuery($registrationQuery);

        if (mysqli_affected_rows($this->conn) == 1) {
            return [
                "statusCode" => 200,
                "data" => [
                    "first_name" => $firstName,
                    "last_name" => $lastName,
                    "email" => $email,
                    "api_key" => $apiKey
                ]
            ];
        } else {
            return [
                "statusCode" => 500,
                "error" => [
                    "message" => "A server error occurred",
                    "query" => $registrationQuery
                ]
            ];
        }
    }

    /**
     * Gets all the account information for a Borum user
     * @param string $email The user's email
     * @param string $password The user's password
     * @return (string|int)[] An associative array of the id, api key, email, first name last name, and username
     */
    public function getUser($email, $password) {
        $sanitizedEmail = $this->sanitizeParam($email);
        $sanitizedPassword = $this->sanitizeParam($password);

        $userData = $this->executeQuery("SELECT id, api_key, email, first_name, last_name, username FROM users WHERE email = '$sanitizedEmail' AND pass = SHA2('$sanitizedPassword', 512) LIMIT 1");
        
        if (mysqli_num_rows($userData) == 1) {
            $userData = mysqli_fetch_array($userData, MYSQLI_ASSOC);
        
            return [
                "statusCode" => 200,
                "data" => $userData
            ];
        } else {
            return [
                "statusCode" => 401,
                "error" => [
                    "message" => "Invalid credentials"
                ]
            ];
        }
    }

    private function sanitizeParam(string $param) : string {
        return mysqli_real_escape_string($this->conn, trim($param));
    }
}
