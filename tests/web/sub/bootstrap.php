<?php

namespace duncan3dc\Sessions;

require __DIR__ . "/../../../vendor/autoload.php";

session_set_cookie_params(3600, '/sub', 'localhost', false, true);
$session = new SessionInstance("web");
