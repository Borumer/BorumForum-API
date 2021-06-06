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
      $this->sendEmail("Reset your Password", `<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
      <html xmlns="http://www.w3.org/1999/xhtml">
        <head>
          <meta name="viewport" content="width=device-width, initial-scale=1.0" />
          <meta name="x-apple-disable-message-reformatting" />
          <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
          <meta name="color-scheme" content="light dark" />
          <meta name="supported-color-schemes" content="light dark" />
          <title></title>
          <style type="text/css" rel="stylesheet" media="all">
          /* Base ------------------------------ */
          
          @import url("https://fonts.googleapis.com/css?family=Nunito+Sans:400,700&display=swap");
          body {
            width: 100% !important;
            height: 100%;
            margin: 0;
            -webkit-text-size-adjust: none;
          }
          
          a {
            color: #3869D4;
          }
          
          a img {
            border: none;
          }
          
          td {
            word-break: break-word;
          }
          
          .preheader {
            display: none !important;
            visibility: hidden;
            mso-hide: all;
            font-size: 1px;
            line-height: 1px;
            max-height: 0;
            max-width: 0;
            opacity: 0;
            overflow: hidden;
          }
          /* Type ------------------------------ */
          
          body,
          td,
          th {
            font-family: "Nunito Sans", Helvetica, Arial, sans-serif;
          }
          
          h1 {
            margin-top: 0;
            color: #333333;
            font-size: 22px;
            font-weight: bold;
            text-align: left;
          }
          
          h2 {
            margin-top: 0;
            color: #333333;
            font-size: 16px;
            font-weight: bold;
            text-align: left;
          }
          
          h3 {
            margin-top: 0;
            color: #333333;
            font-size: 14px;
            font-weight: bold;
            text-align: left;
          }
          
          td,
          th {
            font-size: 16px;
          }
          
          p,
          ul,
          ol,
          blockquote {
            margin: .4em 0 1.1875em;
            font-size: 16px;
            line-height: 1.625;
          }
          
          p.sub {
            font-size: 13px;
          }
          /* Utilities ------------------------------ */
          
          .align-right {
            text-align: right;
          }
          
          .align-left {
            text-align: left;
          }
          
          .align-center {
            text-align: center;
          }
          /* Buttons ------------------------------ */
          
          .button {
            background-color: #3869D4;
            border-top: 10px solid #3869D4;
            border-right: 18px solid #3869D4;
            border-bottom: 10px solid #3869D4;
            border-left: 18px solid #3869D4;
            display: inline-block;
            color: #FFF;
            text-decoration: none;
            border-radius: 3px;
            box-shadow: 0 2px 3px rgba(0, 0, 0, 0.16);
            -webkit-text-size-adjust: none;
            box-sizing: border-box;
          }
          
          .button--green {
            background-color: #22BC66;
            border-top: 10px solid #22BC66;
            border-right: 18px solid #22BC66;
            border-bottom: 10px solid #22BC66;
            border-left: 18px solid #22BC66;
          }
          
          .button--red {
            background-color: #FF6136;
            border-top: 10px solid #FF6136;
            border-right: 18px solid #FF6136;
            border-bottom: 10px solid #FF6136;
            border-left: 18px solid #FF6136;
          }
          
          @media only screen and (max-width: 500px) {
            .button {
              width: 100% !important;
              text-align: center !important;
            }
          }
          /* Attribute list ------------------------------ */
          
          .attributes {
            margin: 0 0 21px;
          }
          
          .attributes_content {
            background-color: #F4F4F7;
            padding: 16px;
          }
          
          .attributes_item {
            padding: 0;
          }
          /* Related Items ------------------------------ */
          
          .related {
            width: 100%;
            margin: 0;
            padding: 25px 0 0 0;
            -premailer-width: 100%;
            -premailer-cellpadding: 0;
            -premailer-cellspacing: 0;
          }
          
          .related_item {
            padding: 10px 0;
            color: #CBCCCF;
            font-size: 15px;
            line-height: 18px;
          }
          
          .related_item-title {
            display: block;
            margin: .5em 0 0;
          }
          
          .related_item-thumb {
            display: block;
            padding-bottom: 10px;
          }
          
          .related_heading {
            border-top: 1px solid #CBCCCF;
            text-align: center;
            padding: 25px 0 10px;
          }
          /* Discount Code ------------------------------ */
          
          .discount {
            width: 100%;
            margin: 0;
            padding: 24px;
            -premailer-width: 100%;
            -premailer-cellpadding: 0;
            -premailer-cellspacing: 0;
            background-color: #F4F4F7;
            border: 2px dashed #CBCCCF;
          }
          
          .discount_heading {
            text-align: center;
          }
          
          .discount_body {
            text-align: center;
            font-size: 15px;
          }
          /* Social Icons ------------------------------ */
          
          .social {
            width: auto;
          }
          
          .social td {
            padding: 0;
            width: auto;
          }
          
          .social_icon {
            height: 20px;
            margin: 0 8px 10px 8px;
            padding: 0;
          }
          /* Data table ------------------------------ */
          
          .purchase {
            width: 100%;
            margin: 0;
            padding: 35px 0;
            -premailer-width: 100%;
            -premailer-cellpadding: 0;
            -premailer-cellspacing: 0;
          }
          
          .purchase_content {
            width: 100%;
            margin: 0;
            padding: 25px 0 0 0;
            -premailer-width: 100%;
            -premailer-cellpadding: 0;
            -premailer-cellspacing: 0;
          }
          
          .purchase_item {
            padding: 10px 0;
            color: #51545E;
            font-size: 15px;
            line-height: 18px;
          }
          
          .purchase_heading {
            padding-bottom: 8px;
            border-bottom: 1px solid #EAEAEC;
          }
          
          .purchase_heading p {
            margin: 0;
            color: #85878E;
            font-size: 12px;
          }
          
          .purchase_footer {
            padding-top: 15px;
            border-top: 1px solid #EAEAEC;
          }
          
          .purchase_total {
            margin: 0;
            text-align: right;
            font-weight: bold;
            color: #333333;
          }
          
          .purchase_total--label {
            padding: 0 15px 0 0;
          }
          
          body {
            background-color: #FFF;
            color: #333;
          }
          
          p {
            color: #333;
          }
          
          .email-wrapper {
            width: 100%;
            margin: 0;
            padding: 0;
            -premailer-width: 100%;
            -premailer-cellpadding: 0;
            -premailer-cellspacing: 0;
          }
          
          .email-content {
            width: 100%;
            margin: 0;
            padding: 0;
            -premailer-width: 100%;
            -premailer-cellpadding: 0;
            -premailer-cellspacing: 0;
          }
          /* Masthead ----------------------- */
          
          .email-masthead {
            padding: 25px 0;
            text-align: center;
          }
          
          .email-masthead_logo {
            width: 94px;
          }
          
          .email-masthead_name {
            font-size: 16px;
            font-weight: bold;
            color: #A8AAAF;
            text-decoration: none;
            text-shadow: 0 1px 0 white;
          }
          /* Body ------------------------------ */
          
          .email-body {
            width: 100%;
            margin: 0;
            padding: 0;
            -premailer-width: 100%;
            -premailer-cellpadding: 0;
            -premailer-cellspacing: 0;
          }
          
          .email-body_inner {
            width: 570px;
            margin: 0 auto;
            padding: 0;
            -premailer-width: 570px;
            -premailer-cellpadding: 0;
            -premailer-cellspacing: 0;
          }
          
          .email-footer {
            width: 570px;
            margin: 0 auto;
            padding: 0;
            -premailer-width: 570px;
            -premailer-cellpadding: 0;
            -premailer-cellspacing: 0;
            text-align: center;
          }
          
          .email-footer p {
            color: #A8AAAF;
          }
          
          .body-action {
            width: 100%;
            margin: 30px auto;
            padding: 0;
            -premailer-width: 100%;
            -premailer-cellpadding: 0;
            -premailer-cellspacing: 0;
            text-align: center;
          }
          
          .body-sub {
            margin-top: 25px;
            padding-top: 25px;
            border-top: 1px solid #EAEAEC;
          }
          
          .content-cell {
            padding: 35px;
          }
          /*Media Queries ------------------------------ */
          
          @media only screen and (max-width: 600px) {
            .email-body_inner,
            .email-footer {
              width: 100% !important;
            }
          }
          
          @media (prefers-color-scheme: dark) {
            body {
              background-color: #333333 !important;
              color: #FFF !important;
            }
            p,
            ul,
            ol,
            blockquote,
            h1,
            h2,
            h3,
            span,
            .purchase_item {
              color: #FFF !important;
            }
            .attributes_content,
            .discount {
              background-color: #222 !important;
            }
            .email-masthead_name {
              text-shadow: none !important;
            }
          }
          
          :root {
            color-scheme: light dark;
            supported-color-schemes: light dark;
          }
          </style>
          <!--[if mso]>
          <style type="text/css">
            .f-fallback  {
              font-family: Arial, sans-serif;
            }
          </style>
        <![endif]-->
        </head>
        <body>
          <span class="preheader">Use this link to reset your password. The link is only valid for 24 hours.</span>
          <table class="email-wrapper" width="100%" cellpadding="0" cellspacing="0" role="presentation">
            <tr>
              <td align="center">
                <table class="email-content" width="100%" cellpadding="0" cellspacing="0" role="presentation">
                  <tr>
                    <td class="email-masthead">
                      <a href="https://borumtech.com" class="f-fallback email-masthead_name">
                      Borum Sphere
                    </a>
                    </td>
                  </tr>
                  <!-- Email Body -->
                  <tr>
                    <td class="email-body" width="570" cellpadding="0" cellspacing="0">
                      <table class="email-body_inner" align="center" width="570" cellpadding="0" cellspacing="0" role="presentation">
                        <!-- Body content -->
                        <tr>
                          <td class="content-cell">
                            <div class="f-fallback">
                              <h1>Hi ` . $name . `,</h1>
                              <p>You recently requested to reset your password for your Borum Sphere account. Use the button below to reset it. <strong>This password reset is only valid for the next 24 hours.</strong></p>
                              <!-- Action -->
                              <table class="body-action" align="center" width="100%" cellpadding="0" cellspacing="0" role="presentation">
                                <tr>
                                  <td align="center">
                                    <!-- Border based button
                 https://litmus.com/blog/a-guide-to-bulletproof-buttons-in-email-design -->
                                    <table width="100%" border="0" cellspacing="0" cellpadding="0" role="presentation">
                                      <tr>
                                        <td align="center">
                                          <a href="` . $resetPasswordLink . `" class="f-fallback button button--green" target="_blank">Reset your password</a>
                                        </td>
                                      </tr>
                                    </table>
                                  </td>
                                </tr>
                              </table>
                              <p>For security, this request was received from a Windows, Android, Linux, iOS, Mac, or ChromeOS device using Google Chrome, Firefox, Safari, or Edge. If you did not request a password reset, please ignore this email or <a href="mailto:support@borumtech.com">contact support</a> if you have questions.</p>
                              <p>Thanks,
                                <br>The Borum Team</p>
                              <!-- Sub copy -->
                              <table class="body-sub" role="presentation">
                                <tr>
                                  <td>
                                    <p class="f-fallback sub">If you’re having trouble with the button above, copy and paste the URL below into your web browser.</p>
                                    <p class="f-fallback sub">` . $resetPasswordLink . `</p>
                                  </td>
                                </tr>
                              </table>
                            </div>
                          </td>
                        </tr>
                      </table>
                    </td>
                  </tr>
                  <tr>
                    <td>
                      <table class="email-footer" align="center" width="570" cellpadding="0" cellspacing="0" role="presentation">
                        <tr>
                          <td class="content-cell" align="center">
                            <p class="f-fallback sub align-center">&copy; 2021 Borum Sphere. All rights reserved.</p>
                            <!-- <p class="f-fallback sub align-center">
                              Borum Tech
                              <br>1234 Street Rd.
                              <br>Mountain View, California
                            </p> -->
                          </td>
                        </tr>
                      </table>
                    </td>
                  </tr>
                </table>
              </td>
            </tr>
          </table>
        </body>
      </html>`, $email);

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
        // Server settings
        $mail->isSMTP();
        $mail->Host = 'localhost';
        $mail->SMTPAuth = false;
        $mail->SMTPAutoTLS = false; 
        $mail->Port = 25; 

        // Recipients
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
