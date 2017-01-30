<?php

namespace duncan3dc\Sessions;

require __DIR__ . "/../../vendor/autoload.php";

if (isset($_GET["session_name"])) {
    $name = $_GET["session_name"];
} else {
    $name = "web";
}

$session = new SessionInstance($name);
