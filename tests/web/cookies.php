<?php

use duncan3dc\Sessions\SessionInstance;
use duncan3dc\Sessions\Cookie;

require __DIR__ . "/../../vendor/autoload.php";

$cookie = new Cookie;

if (isset($_GET["lifetime"])) {
    $cookie = $cookie->withLifetime($_GET["lifetime"]);
}

if (isset($_GET["path"])) {
    $cookie = $cookie->withPath($_GET["path"]);
}

if (isset($_GET["domain"])) {
    $cookie = $cookie->withDomain($_GET["domain"]);
}

if (isset($_GET["secure"])) {
    $cookie = $cookie->withSecure($_GET["secure"]);
}

if (isset($_GET["httponly"])) {
    $cookie = $cookie->withHttpOnly($_GET["httponly"]);
}

$session = new SessionInstance("web", $cookie);

$session->set("test", "ok");
