<?php 

require __DIR__ . "/../vendor/autoload.php";

use BorumForum\DBHandlers\UserHandler;
use VarunS\BorumSleep\Helpers;
use VarunS\BorumSleep\SimpleRest;

$headers = apache_request_headers();
SimpleRest::handleHeaderValidation($headers, "authorization");
$userApiKey = Helpers::parseAuthorizationHeader($headers["authorization"]);

$userHandler = new UserHandler($userApiKey);

switch ($_SERVER["REQUEST_METHOD"]) {
    case "POST":
        $response = $userHandler->createNewUser($_POST["email"], $_POST["password"]);
        SimpleRest::setHttpHeaders($response["statusCode"]);
        echo json_encode($response);
    break;
    case "PUT":
        $_PUT=[];
        parse_str(file_get_contents('php://input'), $_PUT);
        $response = $userHandler->updateSignIn($_PUT["new_password"]);
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