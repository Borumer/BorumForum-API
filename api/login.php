<?php 

require __DIR__ . "/../vendor/autoload.php";

use BorumForum\DBHandlers\SettingsHandler;
use BorumForum\DBHandlers\UserHandler;
use VarunS\BorumSleep\SimpleRest;

header('Access-Control-Allow-Methods: POST, PUT');

switch ($_SERVER["REQUEST_METHOD"]) {
    case "POST":
        $userHandler = new UserHandler($userApiKey);
        $response = $userHandler->getUser($_POST["email"], $_POST["password"]);
        SimpleRest::setHttpHeaders($response["statusCode"]);
        echo json_encode($response);
    break;
    case "PUT":
        $headers = apache_request_headers();
        SimpleRest::handleHeaderValidation($headers, "authorization");
        $userApiKey = SimpleRest::parseAuthorizationHeader($headers["authorization"]);

        $settingsHandler = new SettingsHandler($userApiKey);
        parse_str(file_get_contents('php://input'), $GLOBALS["_{PUT}"]);
        $response = $settingsHandler->updateSignIn($GLOBALS["_{PUT}"]["new_password"]);
        SimpleRest::setHttpHeaders($response["statusCode"]);
        echo json_encode($response);
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