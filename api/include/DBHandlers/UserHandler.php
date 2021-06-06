<?php

namespace BorumForum\DBHandlers;

use PHPMailer\PHPMailer\PHPMailer;

class UserHandler extends UserNotKnownHandler
{
  function __construct()
  {
    parent::__construct();
  }

  /**
   * Creates a new user if the email doesn't already exist
   * @param string $unsafeFirstName The raw given first name of the user
   * @param string $unsafeLastName The raw given last name of the user
   * @param string $unsafeEmail The raw given email of the user
   * @param string $unsafePassword The plaintext password of the new user
   */
  public function createNewUser($unsafeFirstName, $unsafeLastName, $unsafeEmail, $unsafePassword)
  {
    $registrationQuery = "
        INSERT INTO users 
        (first_name, last_name, email, pass, api_key, registration_date) 
        VALUES (?, ?, ?, SHA2(?, 512), ?, NOW())";

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

    $registration_preparation = $this->conn->prepare($registrationQuery);
    $registration_preparation->bind_param("sssss", $firstName, $lastName, $email, $password, $apiKey);
    $registration_preparation->execute();

    if (mysqli_affected_rows($this->conn) == 1) {
      $response = $this->getUser($unsafeEmail, $unsafePassword);

      $registration_preparation->close();

      if ($response["statusCode"] != 401) {
        return $response;
      }
    }

    return [
      "statusCode" => 500,
      "error" => [
        "message" => "A server error occurred",
        "query" => $registrationQuery
      ]
    ];
  }

  /**
   * Gets all the account information for a Borum user
   * @param string $email The user's email
   * @param string $password The user's password
   * @return (string|int)[] An associative array of the id, api key, email, first name last name, and username
   */
  public function getUser($email, $password)
  {
    $sanitizedEmail = $this->sanitizeParam($email);
    $sanitizedPassword = $this->sanitizeParam($password);

    $userData = $this->executeQuery("SELECT id, api_key, email, first_name, last_name, username FROM users WHERE email = '$sanitizedEmail' AND pass = SHA2('$sanitizedPassword', 512) LIMIT 1");

    if (!!!$userData || !$userData instanceof \mysqli_result) {
      return [
        "statusCode" => 500,
        "error" => [
          "message" => "A server error occurred"
        ]
      ];
    }

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

  /**
   * DONE Send email with link to Borum Sphere change password page 
   * DONE with user api key in url - for verifying the user with the email is changing the password
   * TODO with reset password activated code in url - for expiring after 24 hours
   */
  public function sendResetPasswordEmail($email)
  {
    $userQuery = $this->executeQuery("SELECT first_name, last_name, api_key FROM users WHERE email = '$email'");
    $userData = mysqli_fetch_array($userQuery);
    $name = "{$userData[0]} {$userData[1]}";

    $resetPasswordLink = "https://accounts.borumtech.com/change_password?key={$userData[2]}";
    try {
        $this->sendEmail("Reset your Password", "Hello " . $name . ", <br>You requested to reset your password. We've got your covered. Go to the link below to change your password.<br><a href='$resetPasswordLink'>$resetPasswordLink</a><p>For security, this request was received from a Windows, Android, Linux, iOS, Mac, or ChromeOS device using Google Chrome, Firefox, Safari, or Edge. If you did not request a password reset, please ignore this email or <a href='mailto:support@borumtech.com'>contact support</a> if you have questions.</p>
        Thanks, Borum Support", $email);

        return [
          "statusCode" => 202
        ];
      } catch (\Throwable $e) {
        return [
          "statusCode" => 500,
          "error" => [
            "message" => "A server error occurred"
          ]
        ];
      }
  }


  /**
   * @param string $subject The subject of the email
   * @param string $body The body of the email
   * @param string $email The recipient of the email
   * @return bool Whether the email was successfully sent
   */
  function sendEmail($subject, $body, $email = 'admin@borumtech.com') : bool {  
    // Instantiation and passing `true` enables exceptions
    $mail = new PHPMailer(true);
  
    try {
        //Server settings
        $mail->SMTPDebug = 3;                      // Verbose debug output: 1 for no 2,3,4 for yes
        $mail->isMail();                                            // Send using SMTP
        $mail->Port       = 25;                                    // TCP port to connect to
  
        //Recipients
        $mail->setFrom('admin@borumtech.com', 'The Borum Team');
        $mail->addAddress($email);     // Add a recipient
  
        // Content
        $mail->isHTML(true);                                  // Set email format to HTML
        $mail->Subject = $subject;
        $mail->Body    = $body;
        $mail->AltBody = strip_tags($body);
        $mail->send();
        return true;
    } catch (\Exception $e) {
        return false;
    }
  }
}
