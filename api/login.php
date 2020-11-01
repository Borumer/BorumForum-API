<?php 

require __DIR__ . "/../vendor/autoload.php";

use BorumForum\DBHandlers\UserHandler;
use VarunS\BorumSleep\Helpers;
use VarunS\BorumSleep\SimpleRest;

header('Access-Control-Allow-Methods: POST, PUT');

switch ($_SERVER["REQUEST_METHOD"]) {
    case "POST":
        $response = $userHandler->getUser($_POST["email"], $_POST["password"]);
        SimpleRest::setHttpHeaders($response["statusCode"]);
        echo json_encode($response);
    break;
    case "PUT":
        $headers = apache_request_headers();
        SimpleRest::handleHeaderValidation($headers, "authorization");
        $userApiKey = Helpers::parseAuthorizationHeader($headers["authorization"]);

        $userHandler = new UserHandler($userApiKey);
        parse_str(file_get_contents('php://input'), $GLOBALS["_{PUT}"]);
        $response = $userHandler->updateSignIn($GLOBALS["_{PUT}"]["new_password"]);
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