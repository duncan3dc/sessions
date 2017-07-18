<?php

require __DIR__ . "/bootstrap.php";

$session->set($_GET["key"], $_GET["value"]);

echo $session->getId();
