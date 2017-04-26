<?php

namespace duncan3dc\Sessions;

require __DIR__ . "/../../vendor/autoload.php";

if (isset($_GET["session_name"])) {
    $name = $_GET["session_name"];
} else {
    $name = "web";
}

session_save_path('/tmp/duncan3dc-sessions');
if (!file_exists(session_save_path())) {
    mkdir(session_save_path());
}

$session = new SessionInstance($name);
