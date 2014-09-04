<?php

require_once 'vendor/autoload.php';

require_once 'include/config.php';

require_once 'controller/LoginController.php';
require_once 'util/HmacHashCalculator.php';
require_once 'util/PasswordUtil.php';
require_once 'util/EntityManagerFactory.php';
require_once 'util/PasswordUtil.php';
require_once 'util/JsonMessage.php';
require_once 'model/User.php';
require_once 'model/SessionToken.php';
require_once 'exception/BadRequestException.php';
require_once 'exception/UnauthorizedException.php';

use Tonic\Response;

define("TOKEN_BYTE_SIZE", 512);

// TODO introduce setupHeaders(boolean useCors) method
header('Content-Type: application/json');
header("Access-Control-Allow-Origin: *");
header('Access-Control-Allow-Credentials: true');
header("Access-Control-Max-Age: 86400");
header('Access-Control-Request-Method: *');
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Authorization, Content-Type, If-Match, If-Modified-Since, If-None-Match, If-Unmodified-Since, X-Requested-With, X-MICRO-TIME, X-SESSION-TOKEN, X-HMAC-HASH, X-URL");

// TODO proper bypass of HTTP OPTIONS method for CORS
if ($_SERVER['REQUEST_METHOD'] == "OPTIONS") {
    exit(0);
}

try {

    // TODO check HMAC hash for login
//    $hmacHashCalculator = new HmacHashCalculator();
//    $hmacHashCalculator->checkHmacHash();

    $jsonData = file_get_contents('php://input');
    $requestBody = json_decode($jsonData);

    $login = new LoginController();

    // check HTTP request body parameters
    $login->checkRequestParameters($requestBody);
    // get username from json body
    $username = $requestBody->username;
    $password = $requestBody->password;

    $user = $login->checkUser($username, $password);

    // create and save a new SessionToken and return it
    $sessionToken = $login->createAndSaveSessionToken($user, $password);
    $response = new Response(Response::OK, json_encode($sessionToken));
} catch (BadRequestException $e) {
    // TODO use exception handling instead of else
    // not all parameters have been sent within html body
    $response = new Response(Response::FORBIDDEN, json_encode(new JsonMessage("Unauthorized: Missing at least one
    parameter in HTML Body")));
} catch (UnauthorizedException $e) {
    $response = new Response(Response::FORBIDDEN, json_encode(new JsonMessage("Unauthorized: Invalid Username /
    Password")));
}

$response->output();

?>
