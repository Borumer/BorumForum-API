<?php 

require __DIR__ . "/../vendor/autoload.php";

use BorumForum\DBHandlers\QuestionHandler;
use VarunS\BorumSleep\SimpleRest;

header('Access-Control-Allow-Methods: POST, PUT, DELETE');

$headers = apache_request_headers();
$userApiKey = SimpleRest::parseAuthorizationHeader($headers["authorization"]);
$handler = new QuestionHandler($userApiKey);

switch ($_SERVER["REQUEST_METHOD"]) {
    case "GET":
        SimpleRest::setHttpHeaders(501);
    break;
    case "POST":
        $response = $handler->create($_POST);
        SimpleRest::setHttpHeaders($response["statusCode"]);
        echo json_encode($response);
    break;
    case "DELETE":
        $response = $handler->delete($GLOBALS["_{DELETE}"]);
        SimpleRest::setHttpHeaders($response["statusCode"]);
        echo json_encode($response);
    break;
    case "PUT":
        SimpleRest::setHttpHeaders(501);
    break;
    default: 
        SimpleRest::setHttpHeaders(405);
        echo json_encode([
            "statusCode" => 405,
            "error" => [
                "message" => "Invalid request method sent for this endpoint"
            ]
        ]);
}

?>