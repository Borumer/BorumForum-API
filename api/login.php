<?php 

require __DIR__ . "/../vendor/autoload.php";

use BorumForum\DBHandlers\SettingsHandler;
use BorumForum\DBHandlers\UserHandler;
use BorumForum\DBHandlers\UserKnownHandler;
use VarunS\PHPSleep\SimpleRest;
use Dotenv\Dotenv;

$dotenv = Dotenv::createImmutable(__DIR__, "/../.env");
$dotenv->safeLoad();

header('Access-Control-Allow-Methods: POST, PUT, DELETE, OPTIONS');

if (!isset($_SERVER["HTTP_ORIGIN"]))
    $_SERVER["HTTP_ORIGIN"] = "localhost";

header('Access-Control-Allow-Origin: ' . $_SERVER['HTTP_ORIGIN']);

header('Access-Control-Allow-Headers: content-type, authorization');

switch ($_SERVER["REQUEST_METHOD"]) {
    case "GET":
        $headers = apache_request_headers();
        $userApiKey = SimpleRest::parseAuthorizationHeader($headers["authorization"]);
        $userHandler = new SettingsHandler($userApiKey);
        $response = $userHandler->getActivatedApps();
        SimpleRest::setHttpHeaders($response["statusCode"]);
        echo json_encode($response);
        break;
    case "POST":
        $userHandler = new UserHandler();
        $response = $userHandler->getUser($_POST["email"], $_POST["password"]);
        SimpleRest::setHttpHeaders($response["statusCode"]);
        echo json_encode($response);
    break;
    case "PUT":
        $headers = apache_request_headers();
        SimpleRest::handleHeaderValidation($headers, "authorization");
        $userApiKey = SimpleRest::parseAuthorizationHeader($headers["authorization"]);

        $settingsHandler = new SettingsHandler($userApiKey);
        parse_str(file_get_contents('php://input'), $put);

        if (isset($put["old_password"]) && isset($put["new_password"])) {
            $response = $settingsHandler->changePasswordWithOldPassword($GLOBALS["_{PUT}"]["old_password"], $GLOBALS["_{PUT}"]["new_password"]);
        } else if (isset($put["code"]) && isset($put["email"]) && isset($put["new_password"])) {
            $response = $settingsHandler->changePasswordWithCode($put["new_password"], $put["code"], $put["email"]);
        }
        SimpleRest::setHttpHeaders($response["statusCode"]);
        echo json_encode($response);
    break;
    case "DELETE":
        // $delete = [];
        // parse_str(file_get_contents('php://input'), $delete);

        // $userHandler = new UserHandler();
        // $response = $userHandler->sendResetPasswordEmail($delete["email"]);
        $response = [
            "statusCode" => 302,
            "error" => [
                "message" => "Moved Temporarily. Use POST https://forum.borumtech.com/reset_password instead"
            ]
        ];
        SimpleRest::setHttpHeaders($response["statusCode"]);
        echo json_encode($response);
    case "OPTIONS":
    break;
    default:
        SimpleRest::setHttpHeaders(405);
        echo json_encode([
            "statusCode" => 405,
            "error" => [
                "message" => "Invalid request method sent"
            ]
        ]);
    break;
}

?>