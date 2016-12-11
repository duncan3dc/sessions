<?php

namespace duncan3dc\SessionsTest;

require __DIR__ . "/../vendor/autoload.php";

const SERVER_PORT = 5355;

# HHVM no longer has a built in webserver, so don't run these tests
if (isset($_ENV["TRAVIS_PHP_VERSION"]) && $_ENV["TRAVIS_PHP_VERSION"] === "hhvm") {
    return;
}

# Start the internal web server for cookie based tests
exec("php -S localhost:" . SERVER_PORT . " -t " . __DIR__ . "/web >/dev/null 2>&1 & echo $!", $output, $status);
$pid = (int) $output[0];

# Ensure the internal web server is killed when the tests end
register_shutdown_function(function () use ($pid) {
    exec("kill {$pid}");
});
