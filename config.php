<?php

$cookieParams = session_get_cookie_params();
session_set_cookie_params([
    'lifetime' => $cookieParams["lifetime"],
    'path' => '/',
    'domain' => $_SERVER['HTTP_HOST'],
    'secure' => true,  // Set to true if using HTTPS
    'httponly' => true,
    'samesite' => 'Lax'
]);
?>