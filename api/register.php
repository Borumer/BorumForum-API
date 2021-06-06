<?php 

require __DIR__ . "/../vendor/autoload.php";

use BorumForum\DBHandlers\SettingsHandler;
use BorumForum\DBHandlers\UserHandler;
use BorumForum\DBHandlers\UserKnownHandler;
use VarunS\PHPSleep\SimpleRest;

header('Access-Control-Allow-Methods: POST, PUT, OPTIONS');
header('Access-Control-Allow-Origin: ' . $_SERVER['HTTP_ORIGIN']);

switch ($_SERVER["REQUEST_METHOD"]) {
    case "POST":
        $handler = new UserHandler();
        $response = $handler->createNewUser($_POST["first_name"], $_POST["last_name"], $_POST["email"], $_POST["password"]);
        SimpleRest::setHttpHeaders($response["statusCode"]);
        echo json_encode($response);
    break;
    case "DELETE":
        $headers = apache_request_headers();
        SimpleRest::handleHeaderValidation($headers, "authorization");
        $handler = new SettingsHandler(SimpleRest::parseAuthorizationHeader($headers["authorization"]));
        
        $response = $handler->deleteAccount();

        SimpleRest::setHttpHeaders($response["statusCode"]);
        echo json_encode($response);
    break;
}

?>