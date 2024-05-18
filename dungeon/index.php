<?php
require('resources/ErrorHandler.php');
require('resources/Controller.php');
require('resources/Dungeon.php');
require('resources/Queue.php');


session_start();

set_error_handler("ErrorHandler::handleError");
set_exception_handler("ErrorHandler::handleException");

header("Content-type: application/json");

if (!isset($_SESSION['controller'])) {
    $_SESSION['controller'] = new Controller();
}

$controller = $_SESSION['controller'];

$parts = explode('/', $_SERVER['REQUEST_URI']);

$controller->proccesRequest($_SERVER['REQUEST_METHOD'], $parts[2] ?? null);