<?php 

require __DIR__ . "/../vendor/autoload.php";

use BorumForum\DBHandlers\UserHandler;
use VarunS\BorumSleep\SimpleRest;

header('Access-Control-Allow-Methods: POST, PUT');

switch ($_SERVER["REQUEST_METHOD"]) {
    case "POST":
        $handler = new UserHandler();
        $response = $handler->createNewUser();
        SimpleRest::setHttpHeaders($response["statusCode"]);
        echo json_encode($response);
    break;
}

?>