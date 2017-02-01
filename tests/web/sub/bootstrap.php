<?php

namespace duncan3dc\Sessions;

require __DIR__ . "/../../../vendor/autoload.php";

session_set_cookie_params(3600, '/sub', 'localhost', false, true);

session_save_path('/tmp/duncan3dc-sessions');
if (!file_exists(session_save_path())) {
    mkdir(session_save_path());
}

$session = new SessionInstance("web");
