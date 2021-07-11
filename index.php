<?php

chdir(__DIR__);

if (php_sapi_name() !== 'cli-server') {
    die('this is only for the php development server');
}

// Serve as asset file from filesystem if non-PHP file and not root
if (preg_match('/\.(?:png|jpg|jpeg|gif|css|html|json|md|txt)$/', $_SERVER["REQUEST_URI"])) {
    return false;    // serve the requested resource as-is.
}

function rewrite($url, $file) {
    if (str_contains($_SERVER["REQUEST_URI"], $url)) {
        include __DIR__ . DIRECTORY_SEPARATOR . $file;
        exit();
    }
}

rewrite("/login", "api/login.php");
rewrite("/register", "api/register.php");
rewrite("/question", "api/question.php");

include __DIR__ . DIRECTORY_SEPARATOR . "routes.html";

?>

